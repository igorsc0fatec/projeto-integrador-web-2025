<?php
require_once '../controller/controller-adm.php';
require_once '../model/dao.php'; // Adicionar esta linha para ter acesso ao DAO

$controller = new ControllerAdm();
$dao = new DAO(); // Criar instância do DAO
$mensagem = '';

// Obter dados do administrador
$id_adm = $_GET['id'] ?? null;
$adm = null;

if ($id_adm) {
    $result = $controller->listarAdms();
    foreach ($result as $a) {
        if ($a['id_adm'] == $id_adm) {
            $adm = $a;
            break;
        }
    }
}

if (!$adm) {
    header('Location: lista-adm.php');
    exit();
}

// Processar formulário de edição
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $dados = [
            'id_adm' => $id_adm,
            'nome_adm' => $_POST['nome_adm'],
            'funcao_adm' => $_POST['funcao_adm'],
            'cpf_adm' => $_POST['cpf_adm'],
            'email_usuario' => $_POST['email_usuario'],
            'ativo' => isset($_POST['ativo']) ? 1 : 0
        ];

        // Atualizar administrador
        $sqlAdm = sprintf(
            "UPDATE tb_adm SET 
                nome_adm = '%s', 
                funcao_adm = '%s', 
                cpf_adm = '%s' 
            WHERE id_adm = %d",
            $dao->escape_string($dados['nome_adm']), // Usar $dao em vez de $controller
            $dao->escape_string($dados['funcao_adm']),
            $dao->escape_string($dados['cpf_adm']),
            $dados['id_adm']
        );

        $dao->execute($sqlAdm); // Usar $dao para executar

        // Atualizar usuário
        $sqlUsuario = sprintf(
            "UPDATE tb_usuario SET 
                email_usuario = '%s', 
                conta_ativa = %d 
            WHERE id_usuario = (SELECT id_usuario FROM tb_adm WHERE id_adm = %d)",
            $dao->escape_string($dados['email_usuario']),
            $dados['ativo'],
            $dados['id_adm']
        );

        $dao->execute($sqlUsuario);

        $mensagem = '<div class="alert alert-success">Administrador atualizado com sucesso!</div>';
        
        // Atualizar dados locais para exibir
        $adm['nome_adm'] = $dados['nome_adm'];
        $adm['funcao_adm'] = $dados['funcao_adm'];
        $adm['cpf_adm'] = $dados['cpf_adm'];
        $adm['email_usuario'] = $dados['email_usuario'];
        $adm['ativo'] = $dados['ativo'];

    } catch (Exception $e) {
        $mensagem = '<div class="alert alert-danger">Erro ao atualizar: ' . htmlspecialchars($e->getMessage()) . '</div>';
    }
}


// Função auxiliar para formatar CPF
function formatarCPF($cpf) {
    if (empty($cpf)) return '';
    $cpf = preg_replace('/[^0-9]/', '', $cpf);
    return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $cpf);
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Administrador</title>
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

        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .checkbox-group input {
            width: auto;
        }

        .status-ativo { 
            color: green;
            font-weight: 500;
        }
        
        .status-inativo { 
            color: red;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="dashboard-root.php?page=novos-adm&subpage=2.php" class="back-button">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z"/>
            </svg>
            Voltar para lista
        </a>

        <h1>Editar Administrador</h1>

        <?php echo $mensagem; ?>

        <div class="info-box">
            <form method="post">
                <div class="resolucao-container">
                    <div>
                        <div class="form-group">
                            <label for="nome_adm">Nome</label>
                            <input type="text" id="nome_adm" name="nome_adm" value="<?php echo htmlspecialchars($adm['nome_adm']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="funcao_adm">Função</label>
                            <input type="text" id="funcao_adm" name="funcao_adm" value="<?php echo htmlspecialchars($adm['funcao_adm']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="cpf_adm">CPF</label>
                            <input type="text" id="cpf_adm" name="cpf_adm" value="<?php echo formatarCPF($adm['cpf_adm']); ?>" required>
                        </div>
                    </div>

                    <div>
                        <div class="form-group">
                            <label for="email_usuario">E-mail</label>
                            <input type="email" id="email_usuario" name="email_usuario" value="<?php echo htmlspecialchars($adm['email_usuario']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label>Status</label>
                            <div class="checkbox-group">
                                <input type="checkbox" id="ativo" name="ativo" <?php echo $adm['ativo'] == 1 ? 'checked' : ''; ?>>
                                <label for="ativo">Ativo</label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>ID</label>
                            <div class="info-value"><?php echo $adm['id_adm']; ?></div>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <a href="lista-adm.php" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>