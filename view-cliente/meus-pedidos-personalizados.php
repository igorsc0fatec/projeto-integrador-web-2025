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

require_once '../controller/controller-pedido-personalizado.php';
$controllerPedidoPersonalizado = new ControllerPedidoPersonalizado();

// Busca os pedidos personalizados
$pedidosPersonalizados = $controllerPedidoPersonalizado->getPedidosPersonalizadosByCliente($_SESSION['idCliente']);

$numProdutosCarrinho = 0;
if (isset($_SESSION['carrinho'])) {
    $numProdutosCarrinho = count($_SESSION['carrinho']);
}

if (isset($_POST['cancelar']) && isset($_POST['idPedido'])) {
    if ($controllerPedidoPersonalizado->mudarStatusPedidos()) {
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
    <title>Pedidos Personalizados - Delícia Online</title>
</head>
<style>
    /* Estilos apenas para a listagem de pedidos personalizados */
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
        padding: 40px 15px;
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
        font-size: 2rem;
    }

    .titulo::after {
        content: '';
        display: block;
        width: 80px;
        height: 4px;
        background: var(--primary);
        margin: 15px auto 0;
        border-radius: 2px;
    }

    .lista-pedidos {
        display: grid;
        grid-template-columns: 1fr;
        gap: 30px;
    }

    .sem-pedidos {
        text-align: center;
        padding: 50px 20px;
        background: white;
        border-radius: var(--border-radius);
        box-shadow: var(--box-shadow);
        color: var(--gray);
        font-size: 1.1rem;
        font-family: 'Poppins', sans-serif;
    }

    .sem-pedidos i {
        font-size: 3rem;
        color: var(--primary);
        margin-bottom: 20px;
        display: block;
    }

    .pedido {
        background: white;
        border-radius: var(--border-radius);
        box-shadow: var(--box-shadow);
        overflow: hidden;
        transition: var(--transition);
        position: relative;
        font-family: 'Poppins', sans-serif;
        border: 1px solid rgba(0, 0, 0, 0.05);
    }

    .pedido:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.12);
    }

    .pedido-header {
        display: flex;
        align-items: center;
        padding: 25px;
        background: linear-gradient(135deg, #fff9f9 0%, #fff 100%);
        border-bottom: 1px solid var(--light-gray);
        position: relative;
    }

    .order-badge {
        position: absolute;
        top: 20px;
        right: 20px;
        background: var(--primary);
        color: white;
        padding: 5px 15px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
        box-shadow: 0 3px 10px rgba(255, 107, 107, 0.3);
    }

    .restaurante-imagem {
        width: 90px;
        height: 90px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid white;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        margin-right: 25px;
    }

    .restaurante-info {
        flex: 1;
    }

    .restaurante-info h3 {
        font-size: 1.3rem;
        margin-bottom: 8px;
        color: var(--dark);
        display: flex;
        align-items: center;
    }

    .restaurante-info h3 i {
        margin-right: 10px;
        color: var(--primary);
    }

    .restaurante-info p {
        font-size: 1rem;
        color: var(--gray);
        font-weight: 500;
    }

    .detalhes-pedido {
        padding: 25px;
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 20px;
    }

    .detalhes-pedido p {
        margin-bottom: 0;
        font-size: 0.95rem;
        display: flex;
        align-items: flex-start;
        line-height: 1.6;
    }

    .detalhes-pedido i {
        margin-right: 12px;
        color: var(--primary);
        width: 20px;
        text-align: center;
        margin-top: 3px;
    }

    .detalhes-pedido strong {
        font-weight: 600;
        margin-right: 5px;
    }

    .status {
        display: inline-flex;
        align-items: center;
        padding: 6px 15px;
        border-radius: 20px;
        font-size: 0.9rem;
        font-weight: 500;
    }

    .status i {
        margin-right: 8px;
    }

    .pendente {
        background-color: rgba(255, 193, 7, 0.2);
        color: #ff9800;
    }

    .aceito {
        background-color: rgba(76, 175, 80, 0.2);
        color: var(--success);
    }

    .preparo {
        background-color: rgba(33, 150, 243, 0.2);
        color: var(--info);
    }

    .transporte {
        background-color: rgba(158, 158, 158, 0.2);
        color: #616161;
    }

    .entregue {
        background-color: rgba(0, 150, 136, 0.2);
        color: #00796b;
    }

    .cancelado {
        background-color: rgba(244, 67, 54, 0.2);
        color: var(--danger);
    }

    .botoes {
        display: flex;
        justify-content: flex-end;
        padding: 20px;
        background: #fafafa;
        border-top: 1px solid var(--light-gray);
        flex-wrap: wrap;
        gap: 15px;
    }

    .botoes a, .botoes button {
        padding: 10px 20px;
        border-radius: var(--border-radius);
        font-size: 0.95rem;
        font-weight: 500;
        text-decoration: none;
        transition: var(--transition);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-family: 'Poppins', sans-serif;
    }

    .botoes i {
        margin-right: 8px;
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
        transform: translateY(-2px);
    }

    .price-highlight {
        font-size: 1.2rem;
        font-weight: 700;
        color: var(--dark);
    }

    @media (max-width: 768px) {
        .detalhes-pedido {
            grid-template-columns: 1fr;
        }

        .botoes {
            justify-content: center;
        }

        .pedido-header {
            flex-direction: column;
            text-align: center;
            padding: 20px;
        }

        .restaurante-imagem {
            margin-right: 0;
            margin-bottom: 15px;
            width: 80px;
            height: 80px;
        }

        .order-badge {
            position: static;
            margin-top: 10px;
            display: inline-block;
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
                        <img id="logo" src="../assets/img-site/logo.png" alt="Delícia Online">
                    </a>
                    <div class="greeting">
                        <?php
                        if (isset($_SESSION['nome'])) {
                            echo 'Olá, ' . $_SESSION['nome'];
                        } else {
                            echo "Olá, Visitante";
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
                        <li><a href="meus-pedidos.php">Pedidos</a></li>
                        <li><a href="meus-cupons.php">Cupons</a></li>
                        <li><a href="#">Conversas</a></li>
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
    <h1 class="titulo">Pedidos Personalizados</h1>

    <div class="lista-pedidos">
        <?php if (empty($pedidosPersonalizados)): ?>
            <div class="sem-pedidos">
                <i class="fas fa-birthday-cake"></i>
                <p>Você ainda não fez nenhum pedido personalizado.</p>
                <a href="../view/index.php" style="display: inline-block; margin-top: 25px; background: var(--primary); color: white; padding: 12px 25px; border-radius: var(--border-radius); text-decoration: none; font-weight: 500;">
                    Criar Pedido Personalizado
                </a>
            </div>
        <?php else: ?>
            <?php foreach ($pedidosPersonalizados as $pedido):
                $dataHoraPedido = $pedido['data_pedido'] . ' ';
                $dataFormatada = date('d/m/Y \à\s H:i', strtotime($dataHoraPedido));

                $statusClass = '';
                $statusIcon = '';
                $statusText = $pedido['status'];

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
                    <span class="order-badge">#<?= $pedido['id_pedido_personalizado'] ?></span>
                    
                    <div class="pedido-header">
                        <img src="../view-confeitaria/<?= !empty($pedido['img_personalizado']) ? $pedido['img_personalizado'] : 'https://via.placeholder.com/150?text=Pedido+Personalizado' ?>"
                            alt="<?= !empty($pedido['nome_personalizado']) ? $pedido['nome_personalizado'] : 'Pedido Personalizado' ?>"
                            class="restaurante-imagem">
                        <div class="restaurante-info">
                            <h3><i class="fas fa-birthday-cake"></i> <?= $pedido['nome_personalizado'] ?></h3>
                            <p>Pedido criado especialmente para você</p>
                        </div>
                    </div>
                    
                    <div class="detalhes-pedido">
                        <p><i class="fas fa-info-circle"></i> <strong>Massa:</strong> <?= $pedido['desc_massa'] ?></p>
                        <p><i class="fas fa-info-circle"></i> <strong>Recheio:</strong> <?= $pedido['desc_recheio'] ?></p>
                        <p><i class="fas fa-info-circle"></i> <strong>Cobertura:</strong> <?= $pedido['desc_cobertura'] ?></p>
                        <p><i class="fas fa-info-circle"></i> <strong>Formato:</strong> <?= $pedido['desc_formato'] ?></p>
                        <p><i class="fas fa-info-circle"></i> <strong>Decoração:</strong> <?= $pedido['desc_decoracao'] ?></p>
                        <p><i class="fas fa-calendar-day"></i> <strong>Data:</strong> <?= $dataFormatada ?></p>
                        <p><i class="fas fa-info-circle"></i> <strong>Status:</strong> <span class="status <?= $statusClass ?>"><?= $statusIcon ?> <?= $statusText ?></span></p>
                        <p><i class="fas fa-receipt"></i> <strong>Total:</strong> <span class="price-highlight">R$ <?= number_format($pedido['valor_total'], 2, ',', '.') ?></span></p>
                        <?php if ($pedido['desconto'] > 0): ?>
                            <p><i class="fas fa-tag"></i> <strong>Desconto:</strong> R$ <?= number_format($pedido['desconto'], 2, ',', '.') ?></p>
                        <?php endif; ?>
                        <p><i class="fas fa-truck"></i> <strong>Frete:</strong> R$ <?= number_format($pedido['frete'], 2, ',', '.') ?></p>
                    </div>
                    
                    <div class="botoes">
                        <a href="visualizar-pedido-personalizado.php?i=<?= $pedido['id_pedido_personalizado'] ?>" class="detalhes">
                            <i class="fas fa-info-circle"></i> Detalhes
                        </a>
                        
                        <a href="pedir-suporte.php?pedido_personalizado=<?= $pedido['id_pedido_personalizado'] ?>" class="ajuda">
                            <i class="fas fa-question-circle"></i> Ajuda
                        </a>

                        <?php if ($pedido['status'] !== 'Cancelado pelo Cliente' && $pedido['status'] !== 'Entregue!'): ?>
                            <form action="" method="post" style="margin: 0;">
                                <input type="hidden" name="idPedido" value="<?php echo htmlspecialchars($pedido['id_pedido_personalizado']); ?>">
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
                title: 'Cancelar Pedido Personalizado',
                text: 'Tem certeza que deseja cancelar este pedido personalizado?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sim, cancelar',
                cancelButtonText: 'Manter pedido',
                reverseButtons: true
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