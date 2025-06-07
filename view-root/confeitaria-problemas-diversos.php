<?php
require_once '../controller/controller-suporte.php';

$controller = new ControllerSuporte();

// Obtém lista de suportes (com filtro se existir)
$filtro = $_GET['search'] ?? '';
$suportes = $controller->listarSuportesConfeitariasCadastro($filtro);

// Processa ações
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $id_suporte = $_POST['id_suporte'] ?? null;
        
        if ($id_suporte) {
            if ($_POST['action'] === 'excluir') {
                $result = $controller->deletarSuporte($id_suporte);
                
                if ($result['success']) {
                    $mensagem = '<div class="alert alert-success">Chamado excluído com sucesso!</div>';
                    // Recarrega a lista
                    $suportes = $controller->listarSuportes($filtro);
                } else {
                    $mensagem = '<div class="alert alert-danger">Erro ao excluir: ' . htmlspecialchars($result['message']) . '</div>';
                }
            }
        }
    }
}

// Função auxiliar para mostrar status
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
    <title>Chamados de Suporte | Sistema Admin</title>
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

        body {
            background-color: #f5f7fa;
            color: var(--dark-color);
            line-height: 1.6;
        }

        .container-main {
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
        }

        .table {
            margin-bottom: 0;
        }

        .table th {
            background-color: #f8f9fa;
            color: var(--dark-color);
            font-weight: 600;
            padding: 1rem 1.25rem;
            border-bottom: 2px solid #dee2e6;
        }

        .table td {
            padding: 1rem 1.25rem;
            vertical-align: middle;
            border-bottom: 1px solid #e9ecef;
        }

        .table tr:hover {
            background-color: rgba(67, 97, 238, 0.05);
        }

        .badge {
            font-size: 0.8rem;
            font-weight: 500;
            padding: 0.4rem 0.75rem;
        }

        .badge-resolvido {
            background-color: #d1fae5;
            color: #166534;
        }

        .badge-pendente {
            background-color: #ffedd5;
            color: #9a3412;
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
        }

        .btn-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .btn-resolve {
            background-color: var(--success-color);
        }

        .btn-resolve:hover {
            background-color: #3aa8d8;
        }

        .btn-delete {
            background-color: var(--danger-color);
        }

        .btn-delete:hover {
            background-color: #e3116b;
        }

        .empty-state {
            padding: 3rem;
            text-align: center;
            color: var(--gray-color);
        }

        .empty-state-icon {
            font-size: 3rem;
            color: #adb5bd;
            margin-bottom: 1rem;
        }

        .descricao-resumida {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 300px;
        }

        .user-info {
            display: flex;
            flex-direction: column;
        }

        .user-name {
            font-weight: 600;
        }

        .user-email {
            font-size: 0.85rem;
            color: var(--gray-color);
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
    <div class="container-main">
        <div class="header">
            <h1 class="page-title">
                <i class="fas fa-headset"></i> Chamados de Suporte
            </h1>
            
            <form method="get" class="search-container">
                <input type="text" name="search" class="search-input" placeholder="Pesquisar chamados..." 
                       value="<?php echo htmlspecialchars($filtro); ?>">
                <button type="submit" class="search-button">
                    <i class="fas fa-search"></i>
                </button>
                <?php if ($filtro): ?>
                    <a href="usuarios.php" class="clear-button">
                        <i class="fas fa-times"></i>
                    </a>
                <?php endif; ?>
            </form>
        </div>
        
        <?php if (isset($mensagem)): ?>
            <div class="alert <?php echo strpos($mensagem, 'sucesso') !== false ? 'alert-success' : 'alert-danger'; ?>">
                <?php echo $mensagem; ?>
            </div>
        <?php endif; ?>
        
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Lista de Chamados</span>
                <span class="badge bg-primary">
                    <?php echo count($suportes); ?> <?php echo count($suportes) === 1 ? 'chamado' : 'chamados'; ?>
                </span>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Título</th>
                            <th>Descrição</th>
                            <th>Tipo</th>
                            <th>Solicitante</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($suportes)): ?>
                            <tr>
                                <td colspan="7" class="empty-state">
                                    <div class="empty-state-icon">
                                        <i class="far fa-folder-open"></i>
                                    </div>
                                    <p>Nenhum chamado encontrado</p>
                                    <?php if ($filtro): ?>
                                        <a href="usuarios.php" class="btn btn-sm btn-primary mt-2">
                                            Limpar filtros
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($suportes as $suporte): ?>
                                <tr>
                                    <td>#<?php echo $suporte['id_suporte']; ?></td>
                                    <td><?php echo htmlspecialchars($suporte['titulo_suporte']); ?></td>
                                    <td class="descricao-resumida" title="<?php echo htmlspecialchars($suporte['desc_suporte']); ?>">
                                        <?php echo htmlspecialchars($suporte['desc_suporte']); ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($suporte['tipo_suporte']); ?></td>
                                    <td>
                                        <div class="user-info">
                                            <span class="user-name"><?php echo htmlspecialchars($suporte['nome_usuario']); ?></span>
                                            <span class="user-email"><?php echo htmlspecialchars($suporte['email_usuario']); ?></span>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if ($suporte['resolvido']): ?>
                                            <span class="badge badge-resolvido">Resolvido</span>
                                        <?php else: ?>
                                            <span class="badge badge-pendente">Pendente</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <?php if (!$suporte['resolvido']): ?>
                                                <a href="resolver-chamado-confeitaria.php?id=<?php echo $suporte['id_suporte']; ?>" 
                                                   class="btn-action btn-resolve" title="Resolver">
                                                    <i class="fas fa-check"></i>
                                                </a>
                                            <?php endif; ?>
                                            <form method="post" onsubmit="return confirm('Tem certeza que deseja excluir este chamado?')">
                                                <input type="hidden" name="action" value="excluir">
                                                <input type="hidden" name="id_suporte" value="<?php echo $suporte['id_suporte']; ?>">
                                                <button type="submit" class="btn-action btn-delete" title="Excluir">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Tooltips para descrições longas
        document.addEventListener('DOMContentLoaded', function() {
            const descricoes = document.querySelectorAll('.descricao-resumida');
            
            descricoes.forEach(desc => {
                desc.addEventListener('mouseenter', function() {
                    const title = this.getAttribute('title');
                    if (title && title !== this.textContent.trim()) {
                        const tooltip = document.createElement('div');
                        tooltip.className = 'custom-tooltip';
                        tooltip.textContent = title;
                        document.body.appendChild(tooltip);
                        
                        const rect = this.getBoundingClientRect();
                        tooltip.style.position = 'absolute';
                        tooltip.style.left = `${rect.left}px`;
                        tooltip.style.top = `${rect.bottom + 5}px`;
                        tooltip.style.backgroundColor = 'rgba(0,0,0,0.9)';
                        tooltip.style.color = 'white';
                        tooltip.style.padding = '0.5rem 1rem';
                        tooltip.style.borderRadius = '4px';
                        tooltip.style.zIndex = '1000';
                        tooltip.style.maxWidth = '400px';
                        tooltip.style.fontSize = '0.9rem';
                        
                        this.dataset.tooltipId = 'tooltip-' + Date.now();
                    }
                });
                
                desc.addEventListener('mouseleave', function() {
                    const tooltips = document.querySelectorAll('.custom-tooltip');
                    tooltips.forEach(t => t.remove());
                });
            });
        });
    </script>
</body>
</html>