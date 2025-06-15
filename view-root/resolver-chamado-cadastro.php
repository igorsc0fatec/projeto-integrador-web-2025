<?php
require_once '../controller/controller-suporte.php';
require_once '../model/dao.php'; // Adicione esta linha para acessar o DAO diretamente

$controller = new ControllerSuporte();
$dao = new DAO(); // Instância do DAO para operações no banco

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
            // Se for um chamado de cadastro/dados, atualiza os dados do cliente
            if (in_array($suporte['tipo_suporte'], ['Problemas com Dados', 'Problemas com Cadastro'])) {
                $nome = $_POST['nome'] ?? '';
                $email = $_POST['email'] ?? '';
                $cpf = $_POST['cpf'] ?? '';
                
                try {
                    // Primeiro, obtemos o ID do cliente
                    $sql_cliente = "SELECT c.id_cliente 
                                    FROM tb_cliente c
                                    JOIN tb_usuario u ON c.id_usuario = u.id_usuario
                                    WHERE u.email_usuario = '" . $dao->escape_string($suporte['email_usuario']) . "'";
                    $cliente = $dao->getData($sql_cliente);
                    
                    if ($cliente && count($cliente) > 0) {
                        $id_cliente = $cliente[0]['id_cliente'];
                        
                        // Atualiza os dados do cliente
                        $sql_update = "UPDATE tb_cliente SET 
                                      nome_cliente = '" . $dao->escape_string($nome) . "',
                                      cpf_cliente = '" . $dao->escape_string($cpf) . "'
                                      WHERE id_cliente = " . $id_cliente;
                                      
                        $dao->execute($sql_update);
                        
                        // Atualiza o email do usuário se foi alterado
                        if ($email != $suporte['email_usuario']) {
                            $sql_update_email = "UPDATE tb_usuario SET 
                                               email_usuario = '" . $dao->escape_string($email) . "'
                                               WHERE id_usuario = (SELECT id_usuario FROM tb_cliente WHERE id_cliente = " . $id_cliente . ")";
                            $dao->execute($sql_update_email);
                        }
                        
                        $mensagem = '<div class="alert alert-success">Chamado resolvido e dados do cliente atualizados com sucesso! E-mail enviado ao solicitante.</div>';
                    } else {
                        $mensagem = '<div class="alert alert-danger">Cliente não encontrado no sistema.</div>';
                    }
                } catch (Exception $e) {
                    $mensagem = '<div class="alert alert-danger">Erro ao atualizar dados do cliente: ' . htmlspecialchars($e->getMessage()) . '</div>';
                }
            }
            
            // Envia e-mail conforme o tipo de suporte
            $assunto = "Seu chamado #{$id_suporte} foi resolvido";
            $mensagem_email = "";
            
            if (in_array($suporte['tipo_suporte'], ['Problemas com Dados', 'Problemas com Cadastro'])) {
                // Caso seja sobre senha ou dados pessoais
                $nova_senha = bin2hex(random_bytes(4)); // Gera uma senha aleatória simples
                $mensagem_email = "Seu chamado foi resolvido! Sua nova senha é: $nova_senha\n\n";
                $mensagem_email .= "Por favor, altere esta senha após o primeiro login.\n\n";
                
                // Atualiza a senha no banco de dados
                try {
                    $senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);
                    $sql_update_senha = "UPDATE tb_usuario SET 
                                        senha_usuario = '" . $dao->escape_string($senha_hash) . "'
                                        WHERE id_usuario = (SELECT id_usuario FROM tb_cliente WHERE id_cliente = " . $id_cliente . ")";
                    $dao->execute($sql_update_senha);
                } catch (Exception $e) {
                    // Não interrompe o fluxo, apenas registra o erro
                    error_log("Erro ao atualizar senha: " . $e->getMessage());
                }
            } else {
                // Outros tipos de chamados
                $mensagem_email = "Seu chamado foi resolvido!\n\n";
                $mensagem_email .= "Detalhes da resolução:\n";
                $mensagem_email .= $detalhes_resolucao . "\n\n";
            }
            
            $mensagem_email .= "Agradecemos pelo seu contato!\nEquipe de Suporte";
            
            // Aqui você implementaria o envio real de e-mail
            // mail($suporte['email_usuario'], $assunto, $mensagem_email);
            
            if (empty($mensagem)) {
                $mensagem = '<div class="alert alert-success">Chamado resolvido com sucesso! E-mail enviado ao solicitante.</div>';
            }
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

        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: var(--border-radius);
            resize: vertical;
        }

        .form-group textarea {
            min-height: 150px;
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

        .dados-form {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: var(--border-radius);
            margin-top: 1rem;
        }

        .dados-form h3 {
            margin-bottom: 1rem;
            color: var(--primary-color);
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="dashboard-root.php?page=usuarios&subpage=1" class="back-button">
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
                    
                    <?php if (in_array($suporte['tipo_suporte'], ['Problemas com Dados', 'Problemas com Cadastro'])): ?>
                        <div class="dados-form">
                            <h3><i class="fas fa-user-edit"></i> Dados do Cliente para Atualização</h3>
                            
                            <div class="form-group">
                                <label for="nome">Nome Completo:</label>
                                <input type="text" id="nome" name="nome" value="<?php echo htmlspecialchars($suporte['nome_usuario']); ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="email">E-mail:</label>
                                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($suporte['email_usuario']); ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="cpf">CPF:</label>
                                <input type="text" id="cpf" name="cpf" placeholder="000.000.000-00" required>
                            </div>
                            
                            <div class="info-item" style="background: #fff3cd; padding: 1rem; border-radius: 4px;">
                                <p><i class="fas fa-info-circle"></i> <strong>Atenção:</strong> Verifique cuidadosamente os dados antes de confirmar a atualização.</p>
                            </div>
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

    <!-- Máscaras para os campos -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
    <script>
        $(document).ready(function(){
            $('#cpf').mask('000.000.000-00', {reverse: true});
            $('#telefone').mask('(00) 00000-0000');
        });
    </script>
</body>
</html>