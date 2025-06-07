<?php
require_once '../controller/controller-suporte.php';

$controller = new ControllerSuporte();

// Obtém o ID do suporte da URL
$id_suporte = $_GET['id'] ?? null;

if (!$id_suporte) {
    header("Location: usuarios.php");
    exit();
}

// Busca TODOS os suportes e filtra pelo ID
$todos_suportes = $controller->listarSuportes();
$suporte = null;

foreach ($todos_suportes as $item) {
    if ($item['id_suporte'] == $id_suporte) {
        $suporte = $item;
        break;
    }
}

if (!$suporte) {
    header("Location: usuarios.php");
    exit();
}

// Processa o formulário de resolução
$mensagem = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';
    $detalhes_resolucao = $_POST['detalhes_resolucao'] ?? '';
    
    if ($acao === 'resolver') {
        // Marca como resolvido
        $result = $controller->marcarComoResolvido($id_suporte);
        
        if ($result['success']) {
            // Envia e-mail conforme o tipo de suporte
            $assunto = "Seu chamado #{$id_suporte} foi resolvido";
            $mensagem_email = "";
            
            if (in_array($suporte['tipo_suporte'], ['Problemas com login', 'Atualizar dados pessoais'])) {
                // Caso seja sobre senha ou dados pessoais
                $nova_senha = bin2hex(random_bytes(4)); // Gera uma senha aleatória simples
                $mensagem_email = "Seu chamado foi resolvido! Sua nova senha é: $nova_senha\n\n";
                $mensagem_email .= "Por favor, altere esta senha após o primeiro login.\n\n";
            } else {
                // Outros tipos de chamados
                $mensagem_email = "Seu chamado foi resolvido!\n\n";
                $mensagem_email .= "Detalhes da resolução:\n";
                $mensagem_email .= $detalhes_resolucao . "\n\n";
            }
            
            $mensagem_email .= "Agradecemos pelo seu contato!\nEquipe de Suporte";
            
            // Aqui você implementaria o envio real de e-mail
            // mail($suporte['email_usuario'], $assunto, $mensagem_email);
            
            $mensagem = '<div class="alert alert-success">Chamado resolvido com sucesso! E-mail enviado ao solicitante.</div>';
        } else {
            $mensagem = '<div class="alert alert-danger">Erro ao resolver chamado: ' . htmlspecialchars($result['message']) . '</div>';
        }
    }
}

function mostrarStatus($resolvido) {
    return $resolvido ? 
        '<span class="badge bg-success">Resolvido</span>' : 
        '<span class="badge bg-warning text-dark">Pendente</span>';
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resolver Chamado #<?php echo $id_suporte; ?> | Sistema Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3f37c9;
            --success-color: #4cc9f0;
            --danger-color: #f72585;
            --warning-color: #f8961e;
            --light-color: #f8f9fa;
            --dark-color: #212529;
            --gray-color: #6c757d;
            --border-radius: 8px;
            --box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #f5f7fa;
            color: var(--dark-color);
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        h1 {
            color: var(--primary-color);
            font-size: 1.8rem;
            font-weight: 600;
        }

        .back-button {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--primary-color);
            text-decoration: none;
            margin-bottom: 1rem;
        }

        .back-button:hover {
            text-decoration: underline;
        }

        .alert {
            padding: 1rem;
            margin-bottom: 1.5rem;
            border-radius: var(--border-radius);
            font-size: 0.9rem;
        }

        .alert-success {
            background-color: #d1fae5;
            color: #065f46;
            border-left: 4px solid #10b981;
        }

        .alert-danger {
            background-color: #fee2e2;
            color: #b91c1c;
            border-left: 4px solid #ef4444;
        }

        .resolucao-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            margin-top: 1.5rem;
        }

        @media (max-width: 768px) {
            .resolucao-container {
                grid-template-columns: 1fr;
            }
        }

        .info-box {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            padding: 1.5rem;
        }

        .info-box h2 {
            font-size: 1.3rem;
            margin-bottom: 1rem;
            color: var(--primary-color);
            border-bottom: 1px solid #eee;
            padding-bottom: 0.5rem;
        }

        .info-item {
            margin-bottom: 1rem;
        }

        .info-label {
            font-weight: 600;
            color: var(--gray-color);
            display: block;
            margin-bottom: 0.25rem;
        }

        .info-value {
            padding: 0.5rem;
            background: #f8f9fa;
            border-radius: 4px;
            border-left: 3px solid var(--primary-color);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
        }

        .form-group textarea {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: var(--border-radius);
            min-height: 150px;
            resize: vertical;
        }

        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 1rem;
            margin-top: 1.5rem;
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: var(--border-radius);
            cursor: pointer;
            font-weight: 600;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-primary {
            background-color: var(--primary-color);
            color: white;
        }

        .btn-primary:hover {
            background-color: var(--secondary-color);
        }

        .btn-secondary {
            background-color: var(--gray-color);
            color: white;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
        }

        .status-badge {
            display: inline-block;
            padding: 0.35rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-badge.resolvido {
            background-color: #dcfce7;
            color: #166534;
        }

        .status-badge.pendente {
            background-color: #ffedd5;
            color: #9a3412;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="dashboard-root.php?page=confeitarias" class="back-button">
            <i class="fas fa-arrow-left"></i> Voltar para lista de chamados
        </a>
        
        <div class="header">
            <h1><i class="fas fa-headset"></i> Resolver Chamado #<?php echo $id_suporte; ?></h1>
        </div>
        
        <?php if ($mensagem): ?>
            <div class="alert <?php echo strpos($mensagem, 'sucesso') !== false ? 'alert-success' : 'alert-danger'; ?>">
                <?php echo $mensagem; ?>
            </div>
        <?php endif; ?>
        
        <div class="resolucao-container">
            <!-- Bloco A - Informações do chamado -->
            <div class="info-box">
                <h2><i class="fas fa-info-circle"></i> Informações do Chamado</h2>
                
                <div class="info-item">
                    <span class="info-label">ID:</span>
                    <div class="info-value">#<?php echo $suporte['id_suporte']; ?></div>
                </div>
                
                <div class="info-item">
                    <span class="info-label">Título:</span>
                    <div class="info-value"><?php echo htmlspecialchars($suporte['titulo_suporte']); ?></div>
                </div>
                
                <div class="info-item">
                    <span class="info-label">Descrição:</span>
                    <div class="info-value"><?php echo htmlspecialchars($suporte['desc_suporte']); ?></div>
                </div>
                
                <div class="info-item">
                    <span class="info-label">Tipo:</span>
                    <div class="info-value"><?php echo htmlspecialchars($suporte['tipo_suporte']); ?></div>
                </div>
                
                <div class="info-item">
                    <span class="info-label">Solicitante:</span>
                    <div class="info-value">
                        <strong><?php echo htmlspecialchars($suporte['nome_usuario']); ?></strong><br>
                        <?php echo htmlspecialchars($suporte['email_usuario']); ?>
                    </div>
                </div>
                
                <div class="info-item">
                    <span class="info-label">Status:</span>
                    <div class="info-value"><?php echo mostrarStatus($suporte['resolvido']); ?></div>
                </div>
            </div>
            
            <!-- Bloco B - Formulário de resolução -->
            <div class="info-box">
                <h2><i class="fas fa-check-circle"></i> Resolver Chamado</h2>
                
                <form method="post">
                    <div class="form-group">
                        <label for="detalhes_resolucao">Detalhes da Resolução:</label>
                        <textarea id="detalhes_resolucao" name="detalhes_resolucao" required
                                  placeholder="Descreva como o problema foi resolvido..."></textarea>
                    </div>
                    
                    <?php if (in_array($suporte['tipo_suporte'], ['Problemas com login', 'Atualizar dados pessoais'])): ?>
                        <div class="info-item" style="background: #fff3cd; padding: 1rem; border-radius: 4px;">
                            <p><i class="fas fa-info-circle"></i> <strong>Atenção:</strong> Este chamado é sobre credenciais de acesso. 
                            Ao confirmar a resolução, será gerada uma nova senha e enviada por e-mail ao usuário.</p>
                        </div>
                    <?php endif; ?>
                    
                    <div class="form-actions">
                        <a href="usuarios.php" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
                        <button type="submit" name="acao" value="resolver" class="btn btn-primary">
                            <i class="fas fa-check"></i> Confirmar Resolução
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>