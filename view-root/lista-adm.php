<?php
require_once '../controller/controller-adm.php';

$controller = new ControllerAdm();

// Obtém lista de administradores (com filtro se existir)
$filtro = $_GET['search'] ?? '';
$adms = $controller->listarAdms($filtro);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'excluir') {
    $id_adm = $_POST['id_adm'] ?? null;
    
    if ($id_adm) {
        // Chamar método para excluir adm e usuário
        $result = $controller->deleteAdmAndUsuario($id_adm);
        
        if ($result['success']) {
            $mensagem = '<div class="alert alert-success">Administrador e usuário associado excluídos com sucesso!</div>';
        } else {
            $mensagem = '<div class="alert alert-danger">Erro ao excluir: ' . htmlspecialchars($result['message']) . '</div>';
        }
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
    <title>Lista de Administradores</title>
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
            max-width: 1400px;
            margin: 2rem auto;
            padding: 0 1.5rem;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            flex-wrap: wrap;
            gap: 1.5rem;
        }

        .page-title {
            color: var(--primary-color);
            font-size: 2rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .search-container {
            display: flex;
            gap: 0.75rem;
            width: 100%;
            max-width: 500px;
        }

        .search-input {
            flex: 1;
            padding: 0.75rem 1rem;
            border: 1px solid #dee2e6;
            border-radius: var(--border-radius);
            font-size: 0.95rem;
            transition: var(--transition);
        }

        .search-input:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(67, 97, 238, 0.25);
            outline: none;
        }

        .search-button {
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 0 1.25rem;
            border-radius: var(--border-radius);
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .search-button:hover {
            background: var(--secondary-color);
        }

        .clear-button {
            background: var(--danger-color);
            color: white;
            border: none;
            padding: 0 1rem;
            border-radius: var(--border-radius);
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .clear-button:hover {
            background: #d1145a;
        }

        .card {
            background: white;
            border: none;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            overflow: hidden;
            margin-bottom: 2rem;
        }

        .card-header {
            background-color: var(--primary-color);
            color: white;
            padding: 1.25rem 1.5rem;
            font-weight: 500;
        }

        .table-responsive {
            overflow-x: auto;
            margin-top: 1.5rem;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 0;
        }

        .table th {
            background-color: var(--primary-color);
            color: white;
            font-weight: 600;
            padding: 1rem 1.25rem;
            text-align: left;
        }

        .table td {
            padding: 1rem 1.25rem;
            vertical-align: middle;
            border-bottom: 1px solid #e9ecef;
        }

        .table tr:hover {
            background-color: rgba(67, 97, 238, 0.05);
        }

        .status-ativo { 
            color: green;
            font-weight: 500;
        }
        
        .status-inativo { 
            color: red;
            font-weight: 500;
        }

        .action-buttons {
            display: flex;
            gap: 0.75rem;
        }

        .btn-action {
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            border: none;
            color: white;
            transition: var(--transition);
            cursor: pointer;
        }

        .btn-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .btn-primary {
            background-color: var(--primary-color);
            border: none;
        }

        .btn-primary:hover {
            background-color: var(--secondary-color);
        }

        .btn-danger {
            background-color: var(--danger-color);
            border: none;
        }

        .btn-danger:hover {
            background-color: #e3116b;
        }

        .empty-state {
            padding: 3rem;
            text-align: center;
            color: var(--gray-color);
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

        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .search-container {
                max-width: 100%;
            }
            
            .action-buttons {
                flex-direction: column;
                gap: 0.5rem;
            }
            
            .btn-action {
                width: 32px;
                height: 32px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2 class="page-title">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M15 14s1 0 1-1-1-4-5-4-5 3-5 4 1 1 1 1h8zm-7.978-1A.261.261 0 0 1 7 12.996c.001-.264.167-1.03.76-1.72C8.312 10.629 9.282 10 11 10c1.717 0 2.687.63 3.24 1.276.593.69.758 1.457.76 1.72l-.008.002a.274.274 0 0 1-.014.002H7.022zM11 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4zm3-2a3 3 0 1 1-6 0 3 3 0 0 1 6 0zM6.936 9.28a5.88 5.88 0 0 0-1.23-.247A7.35 7.35 0 0 0 5 9c-4 0-5 3-5 4 0 .667.333 1 1 1h4.216A2.238 2.238 0 0 1 5 13c0-1.01.377-2.042 1.09-2.904.243-.294.526-.569.846-.816zM4.92 10A5.493 5.493 0 0 0 4 13H1c0-.26.164-1.03.76-1.724.545-.636 1.492-1.256 3.16-1.275zM1.5 5.5a3 3 0 1 1 6 0 3 3 0 0 1-6 0zm3-2a2 2 0 1 0 0 4 2 2 0 0 0 0 4z"/>
                </svg>
                Lista de Administradores
            </h2>
            
            <div class="search-container">
                <form method="get" style="width: 100%; display: flex; gap: 0.75rem;">
                    <input type="text" name="search" class="search-input" placeholder="Pesquisar..." value="<?php echo htmlspecialchars($filtro); ?>">
                    <button type="submit" class="search-button">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>
                        </svg>
                    </button>
                    
                    <?php if ($filtro): ?>
                        <a href="lista-adm.php" class="clear-button">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                            </svg>
                        </a>
                    <?php endif; ?>
                </form>
            </div>
        </div>
        
        <?php echo $mensagem ?? ''; ?>
        
        <div class="card">
            <div class="card-header">
                Administradores Cadastrados
            </div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Função</th>
                            <th>CPF</th>
                            <th>E-mail</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($adms)): ?>
                            <tr>
                                <td colspan="7" style="text-align: center; padding: 2rem;">Nenhum administrador encontrado</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($adms as $adm): ?>
                                <tr>
                                    <td><?php echo $adm['id_adm']; ?></td>
                                    <td><?php echo htmlspecialchars($adm['nome_adm']); ?></td>
                                    <td><?php echo htmlspecialchars($adm['funcao_adm']); ?></td>
                                    <td><?php echo formatarCPF($adm['cpf_adm']); ?></td>
                                    <td><?php echo htmlspecialchars($adm['email_usuario']); ?></td>
                                    <td>
                                        <span class="<?php echo $adm['ativo'] == 1 ? 'status-ativo' : 'status-inativo'; ?>">
                                            <?php echo $adm['ativo'] == 1 ? 'Ativo' : 'Inativo'; ?>
                                        </span>
                                    </td>
                                    <td class="action-buttons">
                                        <a href="adm-editar.php?id=<?php echo $adm['id_adm']; ?>" class="btn-action btn-primary" title="Editar">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M21.7 7.3l-5-5c-.4-.4-1-.4-1.4 0L3 14.6V21h6.4L21.7 8.7c.4-.4.4-1 0-1.4zM9.6 19H5v-4.6l9.3-9.3 4.6 4.6L9.6 19z"/>
                                            </svg>
                                        </a>

                                        <form method="post" style="display: inline;" onsubmit="return confirm('Tem certeza que deseja excluir este administrador?')">
                                            <input type="hidden" name="action" value="excluir">
                                            <input type="hidden" name="id_adm" value="<?php echo $adm['id_adm']; ?>">
                                            <button type="submit" class="btn-action btn-danger" title="Excluir">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M9 3V2h6v1h5v2H4V3h5zm1 5v10h2V8h-2zm4 0v10h2V8h-2zM6 8v12c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V8H6z"/>
                                                </svg>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>