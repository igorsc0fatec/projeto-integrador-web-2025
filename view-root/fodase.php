<?php
require_once '../controller/controller-suporte.php';

$controller = new ControllerSuporte();

// Obtém lista de suportes (com filtro se existir)
$filtro = $_GET['search'] ?? '';
$suportes = $controller->listarSuportesClientes($filtro);

// Processa ações
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $id_suporte = $_POST['id_suporte'] ?? null;
        
        if ($id_suporte) {
            if ($_POST['action'] === 'resolver') {
                $result = $controller->marcarComoResolvido($id_suporte);
                
                if ($result['success']) {
                    $mensagem = '<div class="suporte-alert suporte-alert-success">Chamado marcado como resolvido com sucesso!</div>';
                    // Recarrega a lista
                    $suportes = $controller->listarSuportes($filtro);
                } else {
                    $mensagem = '<div class="suporte-alert suporte-alert-danger">Erro ao marcar como resolvido: ' . htmlspecialchars($result['message']) . '</div>';
                }
            } elseif ($_POST['action'] === 'excluir') {
                $result = $controller->deletarSuporte($id_suporte);
                
                if ($result['success']) {
                    $mensagem = '<div class="suporte-alert suporte-alert-success">Chamado excluído com sucesso!</div>';
                    // Recarrega a lista
                    $suportes = $controller->listarSuportes($filtro);
                } else {
                    $mensagem = '<div class="suporte-alert suporte-alert-danger">Erro ao excluir: ' . htmlspecialchars($result['message']) . '</div>';
                }
            }
        }
    }
}

// Função auxiliar para mostrar status
function mostrarStatus($resolvido) {
    return $resolvido ? 
        '<span class="suporte-badge suporte-bg-success">Resolvido</span>' : 
        '<span class="suporte-badge suporte-bg-warning suporte-text-dark">Pendente</span>';
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
        .suporte-page {
            --suporte-primary-color: #4361ee;
            --suporte-secondary-color: #3f37c9;
            --suporte-success-color: #4cc9f0;
            --suporte-danger-color: #f72585;
            --suporte-warning-color: #f8961e;
            --suporte-light-color: #f8f9fa;
            --suporte-dark-color: #212529;
            --suporte-gray-color: #6c757d;
            --suporte-border-radius: 8px;
            --suporte-box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            --suporte-transition: all 0.3s ease;
        }

        .suporte-page * {
            box-sizing: border-box;
        }

        .suporte-page body {
            background-color: #f5f7fa;
            color: var(--suporte-dark-color);
            line-height: 1.6;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .suporte-page .suporte-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        .suporte-page .suporte-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .suporte-page .suporte-title {
            color: var(--suporte-primary-color);
            font-size: 1.8rem;
            font-weight: 600;
        }

        .suporte-page .suporte-search-box {
            display: flex;
            align-items: center;
            background: white;
            border-radius: var(--suporte-border-radius);
            box-shadow: var(--suporte-box-shadow);
            overflow: hidden;
            width: 100%;
            max-width: 400px;
        }

        .suporte-page .suporte-search-input {
            flex: 1;
            padding: 0.75rem 1rem;
            border: none;
            outline: none;
            font-size: 0.9rem;
        }

        .suporte-page .suporte-search-button {
            background: var(--suporte-primary-color);
            color: white;
            border: none;
            padding: 0 1rem;
            cursor: pointer;
            transition: var(--suporte-transition);
            height: 100%;
            display: flex;
            align-items: center;
        }

        .suporte-page .suporte-search-button:hover {
            background: var(--suporte-secondary-color);
        }

        .suporte-page .suporte-clear-button {
            background: var(--suporte-danger-color);
            color: white;
            border: none;
            padding: 0 1rem;
            cursor: pointer;
            transition: var(--suporte-transition);
            height: 100%;
            display: flex;
            align-items: center;
            margin-left: 0.5rem;
            border-radius: var(--suporte-border-radius);
        }

        .suporte-page .suporte-clear-button:hover {
            background: #d1145a;
        }

        .suporte-page .suporte-alert {
            padding: 1rem;
            margin-bottom: 1.5rem;
            border-radius: var(--suporte-border-radius);
            font-size: 0.9rem;
        }

        .suporte-page .suporte-alert-success {
            background-color: #d1fae5;
            color: #065f46;
            border-left: 4px solid #10b981;
        }

        .suporte-page .suporte-alert-danger {
            background-color: #fee2e2;
            color: #b91c1c;
            border-left: 4px solid #ef4444;
        }

        .suporte-page .suporte-table-container {
            background: white;
            border-radius: var(--suporte-border-radius);
            box-shadow: var(--suporte-box-shadow);
            overflow: hidden;
        }

        .suporte-page .suporte-table {
            width: 100%;
            border-collapse: collapse;
        }

        .suporte-page .suporte-table th,
        .suporte-page .suporte-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #e9ecef;
        }

        .suporte-page .suporte-table th {
            background-color: var(--suporte-primary-color);
            color: white;
            font-weight: 500;
            position: sticky;
            top: 0;
        }

        .suporte-page .suporte-table tr:hover {
            background-color: #f8f9fa;
        }

        .suporte-page .suporte-descricao-resumida {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 300px;
        }

        .suporte-page .suporte-badge {
            display: inline-block;
            padding: 0.35rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .suporte-page .suporte-bg-success {
            background-color: #dcfce7;
            color: #166534;
        }

        .suporte-page .suporte-bg-warning {
            background-color: #ffedd5;
            color: #9a3412;
        }

        .suporte-page .suporte-text-dark {
            color: var(--suporte-dark-color);
        }

        .suporte-page .suporte-action-buttons {
            display: flex;
            gap: 0.5rem;
        }

        .suporte-page .suporte-action-button {
            border: none;
            border-radius: var(--suporte-border-radius);
            padding: 0.5rem;
            cursor: pointer;
            transition: var(--suporte-transition);
            display: flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
        }

        .suporte-page .suporte-action-button-resolve {
            background-color: var(--suporte-success-color);
            color: white;
        }

        .suporte-page .suporte-action-button-delete {
            background-color: var(--suporte-danger-color);
            color: white;
        }

        .suporte-page .suporte-action-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .suporte-page .suporte-empty-state {
            text-align: center;
            padding: 3rem;
            color: var(--suporte-gray-color);
        }

        .suporte-page .suporte-empty-state i {
            font-size: 2rem;
            margin-bottom: 1rem;
            color: #adb5bd;
        }

        @media (max-width: 768px) {
            .suporte-page .suporte-header {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .suporte-page .suporte-search-box {
                max-width: 100%;
            }
            
            .suporte-page .suporte-table {
                display: block;
                overflow-x: auto;
            }
            
            .suporte-page .suporte-descricao-resumida {
                max-width: 200px;
            }
        }
    </style>
</head>
<body class="dashboard-container suporte-page">
    <div class="suporte-container">
        <div class="suporte-header">
            <h1 class="suporte-title"><i class="fas fa-headset"></i> Chamados de Suporte</h1>
            
            <form method="get" class="suporte-search-box">
                <input type="text" name="search" class="suporte-search-input" placeholder="Pesquisar chamados..." 
                       value="<?php echo htmlspecialchars($filtro); ?>">
                <button type="submit" class="suporte-search-button">
                    <i class="fas fa-search"></i>
                </button>
                <?php if ($filtro): ?>
                    <a href="lista-suporte.php" class="suporte-clear-button">
                        <i class="fas fa-times"></i>
                    </a>
                <?php endif; ?>
            </form>
        </div>
        
        <?php if (isset($mensagem)): ?>
            <div class="suporte-alert <?php echo strpos($mensagem, 'sucesso') !== false ? 'suporte-alert-success' : 'suporte-alert-danger'; ?>">
                <?php echo $mensagem; ?>
            </div>
        <?php endif; ?>
        
        <div class="suporte-table-container">
            <table class="suporte-table">
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
                            <td colspan="7" class="suporte-empty-state">
                                <i class="far fa-folder-open"></i>
                                <p>Nenhum chamado encontrado</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($suportes as $suporte): ?>
                            <tr>
                                <td>#<?php echo $suporte['id_suporte']; ?></td>
                                <td><?php echo htmlspecialchars($suporte['titulo_suporte']); ?></td>
                                <td class="suporte-descricao-resumida" title="<?php echo htmlspecialchars($suporte['desc_suporte']); ?>">
                                    <?php echo htmlspecialchars($suporte['desc_suporte']); ?>
                                </td>
                                <td><?php echo htmlspecialchars($suporte['tipo_suporte']); ?></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($suporte['nome_usuario']); ?></strong>
                                    <div class="suporte-email-small"><?php echo htmlspecialchars($suporte['email_usuario']); ?></div>
                                </td>
                                <td><?php echo mostrarStatus($suporte['resolvido']); ?></td>
                                <td>
                                    <div class="suporte-action-buttons">
                                        <?php if (!$suporte['resolvido']): ?>
                                            <form method="post" onsubmit="return confirm('Marcar este chamado como resolvido?')">
                                                <input type="hidden" name="action" value="resolver">
                                                <input type="hidden" name="id_suporte" value="<?php echo $suporte['id_suporte']; ?>">
                                                <button type="submit" class="suporte-action-button suporte-action-button-resolve" title="Resolver">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                        <form method="post" onsubmit="return confirm('Tem certeza que deseja excluir este chamado?')">
                                            <input type="hidden" name="action" value="excluir">
                                            <input type="hidden" name="id_suporte" value="<?php echo $suporte['id_suporte']; ?>">
                                            <button type="submit" class="suporte-action-button suporte-action-button-delete" title="Excluir">
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

    <script>
        // Adiciona tooltips dinâmicos
        document.addEventListener('DOMContentLoaded', function() {
            const descricoes = document.querySelectorAll('.suporte-descricao-resumida');
            
            descricoes.forEach(desc => {
                desc.addEventListener('mouseenter', function() {
                    const title = this.getAttribute('title');
                    if (title && title !== this.textContent.trim()) {
                        const tooltip = document.createElement('div');
                        tooltip.className = 'suporte-custom-tooltip';
                        tooltip.textContent = title;
                        document.body.appendChild(tooltip);
                        
                        const rect = this.getBoundingClientRect();
                        tooltip.style.position = 'absolute';
                        tooltip.style.left = `${rect.left}px`;
                        tooltip.style.top = `${rect.bottom + 5}px`;
                        tooltip.style.backgroundColor = 'rgba(0,0,0,0.8)';
                        tooltip.style.color = 'white';
                        tooltip.style.padding = '0.5rem 1rem';
                        tooltip.style.borderRadius = '4px';
                        tooltip.style.zIndex = '1000';
                        tooltip.style.maxWidth = '400px';
                        
                        this.dataset.tooltipId = 'tooltip-' + Date.now();
                    }
                });
                
                desc.addEventListener('mouseleave', function() {
                    const tooltips = document.querySelectorAll('.suporte-custom-tooltip');
                    tooltips.forEach(t => t.remove());
                });
            });
        });
    </script>
</body>
</html>