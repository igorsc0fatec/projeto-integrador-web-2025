<?php
session_start();
if (!isset($_SESSION['idCliente'])) {
    session_destroy();
    echo "<script language='javascript' type='text/javascript'> window.location.href='login-cliente.php'</script>";
    exit();
} else if (isset($_SESSION['idTipoUsuario']) && $_SESSION['idTipoUsuario'] != 2) {
    echo "<script language='javascript' type='text/javascript'> window.location.href='login-cliente.php'</script>";
    exit();
}

require_once '../controller/controller-pedido.php';
$controllerPedido = new ControllerPedido();

$pedidosData = $controllerPedido->getPedidosByCliente($_SESSION['idCliente']);

$numProdutosCarrinho = 0;
if (isset($_SESSION['carrinho'])) {
    $numProdutosCarrinho = count($_SESSION['carrinho']);
}

if (isset($_POST['cancelar']) && isset($_POST['idPedido'])) {
    if ($controllerPedido->mudarStatusPedidos()) {
        echo "
            <script>
                Swal.fire({
                    title: 'Pedido Cancelado com Sucesso!',
                    icon: 'success',
                    confirmButtonText: 'OK'
                });
            </script>";
    } else {
        echo "
            <script>
                Swal.fire({
                    title: 'Erro ao cancelar pedido!',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            </script>";
    }
}

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="../assets/css/style-menu.css">
    <link rel="stylesheet" href="../assets/css/style-pedido.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Meus Pedidos - Delicia Online</title>
</head>

<style>
    /* Estilos apenas para a listagem de pedidos */
    :root {
        --primary: #ff6b6b;
        --secondary: #ff8e8e;
        --dark: #2b2d42;
        --light: #f8f9fa;
        --success: #4caf50;
        --warning: #ff9800;
        --danger: #f44336;
        --info: #2196f3;
        --gray: #6c757d;
        --light-gray: #e9ecef;
        --border-radius: 12px;
        --box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
        --transition: all 0.3s ease;
    }

    .container-body {
        padding: 40px 0;
        max-width: 1200px;
        margin: 0 auto;
    }

    .titulo {
        text-align: center;
        margin-bottom: 40px;
        color: var(--dark);
        font-weight: 600;
        position: relative;
        font-family: 'Poppins', sans-serif;
    }

    .titulo::after {
        content: '';
        display: block;
        width: 80px;
        height: 4px;
        background: var(--primary);
        margin: 10px auto 0;
        border-radius: 2px;
    }

    .lista-pedidos {
        display: grid;
        grid-template-columns: 1fr;
        gap: 25px;
        padding: 0 15px;
    }

    .sem-pedidos {
        text-align: center;
        padding: 40px;
        background: white;
        border-radius: var(--border-radius);
        box-shadow: var(--box-shadow);
        color: var(--gray);
        font-size: 18px;
        font-family: 'Poppins', sans-serif;
    }

    .pedido {
        background: white;
        border-radius: var(--border-radius);
        box-shadow: var(--box-shadow);
        overflow: hidden;
        transition: var(--transition);
        position: relative;
        font-family: 'Poppins', sans-serif;
    }

    .pedido:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 25px rgba(0, 0, 0, 0.1);
    }

    .pedido-header {
        display: flex;
        align-items: center;
        padding: 20px;
        border-bottom: 1px solid var(--light-gray);
        background: linear-gradient(135deg, #fff5f5 0%, #fff 100%);
    }

    .restaurante-imagem {
        width: 70px;
        height: 70px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid white;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
        margin-right: 20px;
    }

    .restaurante-info h3 {
        font-size: 18px;
        margin-bottom: 5px;
        color: var(--dark);
    }

    .restaurante-info p {
        font-size: 14px;
        color: var(--gray);
    }

    .detalhes-pedido {
        padding: 20px;
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 15px;
    }

    .detalhes-pedido p {
        margin-bottom: 0;
        font-size: 14px;
        display: flex;
        align-items: center;
    }

    .detalhes-pedido i {
        margin-right: 8px;
        color: var(--primary);
        width: 20px;
        text-align: center;
    }

    .detalhes-pedido strong {
        font-weight: 600;
        margin-right: 5px;
    }

    .status {
        display: inline-flex;
        align-items: center;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 500;
    }

    .status i {
        margin-right: 5px;
    }

    .pendente {
        background-color: #fff3cd;
        color: #856404;
    }

    .aceito {
        background-color: #d4edda;
        color: #155724;
    }

    .preparo {
        background-color: #cce5ff;
        color: #004085;
    }

    .transporte {
        background-color: #e2e3e5;
        color: #383d41;
    }

    .entregue {
        background-color: #d1ecf1;
        color: #0c5460;
    }

    .cancelado {
        background-color: #f8d7da;
        color: #721c24;
    }

    .botoes {
        display: flex;
        justify-content: flex-end;
        padding: 15px 20px;
        background: #f9f9f9;
        border-top: 1px solid var(--light-gray);
        flex-wrap: wrap;
        gap: 10px;
    }

    .botoes a, .botoes button {
        padding: 8px 15px;
        border-radius: var(--border-radius);
        font-size: 14px;
        font-weight: 500;
        text-decoration: none;
        transition: var(--transition);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-family: 'Poppins', sans-serif;
    }

    .botoes i {
        margin-right: 5px;
    }

    .detalhes {
        background: var(--dark);
        color: white;
        border: none;
    }

    .detalhes:hover {
        background: #1a1b2e;
        color: white;
    }

    .ajuda {
        background: var(--info);
        color: white;
        border: none;
    }

    .ajuda:hover {
        background: #0d8aee;
        color: white;
    }

    .btn-cancelar {
        background: var(--danger);
        color: white;
        border: none;
        cursor: pointer;
    }

    .btn-cancelar:hover {
        background: #d32f2f;
    }

    .order-badge {
        position: absolute;
        top: 15px;
        right: 15px;
        background: var(--primary);
        color: white;
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }

    @media (max-width: 768px) {
        .detalhes-pedido {
            grid-template-columns: 1fr;
        }

        .botoes {
            justify-content: center;
        }
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .pedido {
        animation: fadeIn 0.5s ease forwards;
    }

    .pedido:nth-child(1) { animation-delay: 0.1s; }
    .pedido:nth-child(2) { animation-delay: 0.2s; }
    .pedido:nth-child(3) { animation-delay: 0.3s; }
    .pedido:nth-child(4) { animation-delay: 0.4s; }
    .pedido:nth-child(5) { animation-delay: 0.5s; }
</style>

<body>
    <div class="container">
        <header>
            <nav>
                <div class="nav-container">
                    <a href="../view/index.php">
                        <img id="logo" src="../assets/img-site/logo.png" alt="JobFinder">
                    </a>
                    <div class="greeting">
                        <?php
                        if (isset($_SESSION['nome'])) {
                            echo 'Ola, ' . $_SESSION['nome'];
                        } else {
                            echo "Ola, Visitante";
                        }
                        ?>
                    </div>
                    <i class="fas fa-bars btn-menumobile"></i>
                    <ul class="nav-links">
                        <li style="position: relative;">
                            <a href="../view-cliente/carrinho.php">
                                <i class="fa fa-shopping-cart" style="font-size:20px"></i>
                                <?php if ($numProdutosCarrinho > 0): ?>
                                    <span class="cart-counter"><?php echo $numProdutosCarrinho; ?></span>
                                <?php endif; ?>
                            </a>
                        </li>
                        <li><a href="meus-pedidos-personalizados.php">Personalizados</a></li>
                        <li><a href="meus-cupons.php">Cupons</a></li>
                        <li><a href="meus-contatos.php">Conversas</a></li>
                        <li><a href="editar-cliente.php">Meus Dados</a></li>
                        <li><a href="pedir-suporte.php">Suporte</a></li>
                        <li>
                            <form action="logout.php" method="POST">
                                <input type="hidden" name="id" value="<?php echo $_SESSION['idUsuario']; ?>">
                                <button type="submit" class="fa fa-logout logado"><i class="fa fa-sign-out"
                                        style="font-size:20px"></i></button>
                            </form>
                        </li>
                    </ul>
                </div>
            </nav>
        </header>
    </div>

    
<div class="container-body">
    <h1 class="titulo">Meus Pedidos</h1>

    <div class="lista-pedidos">
        <?php if (empty($pedidosData)): ?>
            <div class="sem-pedidos">
                <i class="fas fa-box-open" style="font-size: 40px; margin-bottom: 15px; color: var(--gray);"></i>
                <p>Você ainda não fez nenhum pedido.</p>
                <a href="../view/index.php" style="display: inline-block; margin-top: 20px; background: var(--primary); color: white; padding: 10px 20px; border-radius: var(--border-radius); text-decoration: none;">Explorar Produtos</a>
            </div>
        <?php else: ?>
            <?php foreach ($pedidosData as $pedidoData):
                $pedido = $pedidoData['pedido'];
                $itens = $pedidoData['itens'];

                $dataPedido = new DateTime($pedido['data_pedido']);
                $dataFormatada = $dataPedido->format('d/m/Y \à\s H:i');

                $statusClass = '';
                $statusIcon = '';
                $statusText = ucfirst($pedido['status']);

                switch (strtolower($pedido['status'])) {
                    case 'pendente':
                        $statusClass = 'pendente';
                        $statusIcon = '<i class="fas fa-clock"></i>';
                        break;
                    case 'aceito':
                    case 'confirmado':
                        $statusClass = 'aceito';
                        $statusIcon = '<i class="fas fa-check-circle"></i>';
                        break;
                    case 'em preparo':
                        $statusClass = 'preparo';
                        $statusIcon = '<i class="fas fa-utensils"></i>';
                        break;
                    case 'a caminho':
                    case 'em transporte':
                        $statusClass = 'transporte';
                        $statusIcon = '<i class="fas fa-truck"></i>';
                        break;
                    case 'entregue':
                        $statusClass = 'entregue';
                        $statusIcon = '<i class="fas fa-check-double"></i>';
                        break;
                    case 'cancelado':
                        $statusClass = 'cancelado';
                        $statusIcon = '<i class="fas fa-times-circle"></i>';
                        break;
                    default:
                        $statusClass = 'pendente';
                        $statusIcon = '<i class="fas fa-question-circle"></i>';
                }
                ?>
                <div class="pedido">
                    <span class="order-badge">Pedido #<?= $pedido['id_pedido'] ?></span>
                    
                    <div class="pedido-header">
                        <img src="../view-confeitaria/<?= !empty($itens[0]['img_produto']) ? $itens[0]['img_produto'] : 'https://via.placeholder.com/80' ?>"
                            alt="<?= !empty($itens[0]['nome_produto']) ? $itens[0]['nome_produto'] : 'Produto' ?>"
                            class="restaurante-imagem">
                        <div class="restaurante-info">
                            <h3><?= $itens[0]['nome_confeitaria'] ?? 'Confeitaria' ?></h3>
                            <p><i class="fas fa-box-open"></i> <?= count($itens) ?> <?= count($itens) === 1 ? 'item' : 'itens' ?></p>
                        </div>
                    </div>
                    
                    <div class="detalhes-pedido">
                        <p><i class="fas fa-map-marker-alt"></i> <?= "{$pedido['log_cliente']}, {$pedido['num_local']} - {$pedido['bairro_cliente']}" ?></p>
                        <p><i class="fas fa-calendar-day"></i> <?= $dataFormatada ?></p>
                        <p><i class="fas fa-info-circle"></i> <strong>Status:</strong> <span class="status <?= $statusClass ?>"><?= $statusIcon ?> <?= $statusText ?></span></p>
                        <p><i class="fas fa-credit-card"></i> <strong>Pagamento:</strong> <?= htmlspecialchars($pedido['forma_pagamento']) ?></p>
                        <p><i class="fas fa-receipt"></i> <strong>Total:</strong> R$ <?= number_format($pedido['valor_total'], 2, ',', '.') ?></p>
                        <p><i class="fas fa-tag"></i> <strong>Desconto:</strong> R$ <?= number_format($pedido['desconto'], 2, ',', '.') ?></p>
                        <p><i class="fas fa-truck"></i> <strong>Frete:</strong> R$ <?= number_format($pedido['frete'], 2, ',', '.') ?></p>
                    </div>
                    
                    <div class="botoes">
                        <a href="visualizar-pedido.php?i=<?= $pedido['id_pedido'] ?>" class="detalhes">
                            <i class="fas fa-info-circle"></i> Detalhes
                        </a>
                        
                        <a href="pedir-suporte.php?pedido=<?= $pedido['id_pedido'] ?>" class="ajuda">
                            <i class="fas fa-question-circle"></i> Ajuda
                        </a>

                        <?php if ($pedido['status'] !== 'Cancelado pelo Cliente' && $pedido['status'] !== 'Entregue!'): ?>
                            <form action="" method="post" style="margin: 0;">
                                <input type="hidden" name="idPedido" value="<?php echo htmlspecialchars($pedido['id_pedido']); ?>">
                                <input type="hidden" name="novo_status" value="Cancelado pelo Cliente">
                                <button type="submit" name="cancelar" class="btn-cancelar">
                                    <i class="fas fa-times"></i> Cancelar
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!--<script>
    // SweetAlert for cancel order
    document.querySelectorAll('button[name="cancelar"]').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const form = this.closest('form');
            
            Swal.fire({
                title: 'Cancelar Pedido',
                text: 'Tem certeza que deseja cancelar este pedido?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sim, cancelar',
                cancelButtonText: 'Não'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
</script>-->

</body>

</html>