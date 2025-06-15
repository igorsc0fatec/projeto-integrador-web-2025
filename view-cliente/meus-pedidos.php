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
        echo 'true'; // ← importante para o JS saber que deu certo
    } else {
        echo 'false';
    }
    exit; // ← evita que o restante da página continue renderizando
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
    <title>Meus Pedidos - Delícia Online</title>
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
            max-height: 80vh;
            overflow-y: auto;
            padding-right: 10px;
        }

        .lista-pedidos::-webkit-scrollbar {
            width: 8px;
        }

        .lista-pedidos::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .lista-pedidos::-webkit-scrollbar-thumb {
            background: var(--primary);
            border-radius: 10px;
        }

        .lista-pedidos::-webkit-scrollbar-thumb:hover {
            background: #e05555;
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
            min-height: 500px;
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
            min-width: 0;
        }

        .restaurante-info h3 {
            font-size: 1.3rem;
            margin-bottom: 8px;
            color: var(--dark);
            display: flex;
            align-items: center;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
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
            word-break: break-word;
        }

        .detalhes-pedido i {
            margin-right: 12px;
            color: var(--primary);
            width: 20px;
            text-align: center;
            margin-top: 3px;
            flex-shrink: 0;
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
            white-space: nowrap;
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

        .botoes a,
        .botoes button {
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
            cursor: pointer;
            border: none;
        }

        .botoes i {
            margin-right: 8px;
        }

        .detalhes {
            background: var(--dark);
            color: white;
        }

        .detalhes:hover {
            background: #1a1b2e;
            color: white;
        }

        .ajuda {
            background: var(--info);
            color: white;
        }

        .ajuda:hover {
            background: #0d8aee;
            color: white;
        }

        .btn-cancelar {
            background: var(--danger);
            color: white;
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
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .pedido {
            animation: fadeIn 0.5s ease forwards;
        }

        .pedido:nth-child(1) {
            animation-delay: 0.1s;
        }

        .pedido:nth-child(2) {
            animation-delay: 0.2s;
        }

        .pedido:nth-child(3) {
            animation-delay: 0.3s;
        }

        .pedido:nth-child(4) {
            animation-delay: 0.4s;
        }

        .pedido:nth-child(5) {
            animation-delay: 0.5s;
        }
    </style>
</head>

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
                            echo $_SESSION['nome'];
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
                            <form action="../view/logout.php" method="POST">
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
                    <i class="fas fa-box-open"></i>
                    <p>Você ainda não fez nenhum pedido.</p>
                    <a href="../view/index.php" style="display: inline-block; margin-top: 25px; background: var(--primary); color: white; padding: 12px 25px; border-radius: var(--border-radius); text-decoration: none; font-weight: 500;">
                        Explorar Produtos
                    </a>
                </div>
            <?php else: ?>
                <?php foreach ($pedidosData as $pedidoData):
                    $pedido = $pedidoData['pedido'];
                    $itens = $pedidoData['itens'];

                    $dataPedido = new DateTime($pedido['data_pedido']);
                    $dataFormatada = $dataPedido->format('d/m/Y \à\s H:i');

                    $statusClass = '';
                    $statusIcon = '';
                    $statusText = ucfirst($pedido['tipo_status']);

                    switch (strtolower($pedido['tipo_status'])) {
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
                        case 'cancelado pelo cliente':
                            $statusClass = 'cancelado';
                            $statusIcon = '<i class="fas fa-times-circle"></i>';
                            break;
                        default:
                            $statusClass = 'pendente';
                            $statusIcon = '<i class="fas fa-question-circle"></i>';
                    }
                ?>
                    <div class="pedido">
                        <span class="order-badge">#<?= $pedido['id_pedido'] ?></span>

                        <div class="pedido-header">
                            <img src="../view-confeitaria/<?= !empty($itens[0]['img_produto']) ? $itens[0]['img_produto'] : 'https://via.placeholder.com/150?text=Produto' ?>"
                                alt="<?= !empty($itens[0]['nome_produto']) ? $itens[0]['nome_produto'] : 'Produto' ?>"
                                class="restaurante-imagem">
                            <div class="restaurante-info">
                                <h3><i class="fas fa-store"></i> <?= $itens[0]['nome_confeitaria'] ?? 'Confeitaria' ?></h3>
                                <p><i class="fas fa-box-open"></i> <?= count($itens) ?> <?= count($itens) === 1 ? 'item' : 'itens' ?></p>
                            </div>
                        </div>

                        <div class="detalhes-pedido">
                            <p><i class="fas fa-map-marker-alt"></i> <strong>Endereço:</strong> <?= "{$pedido['log_cliente']}, {$pedido['num_local']} - {$pedido['bairro_cliente']}" ?></p>
                            <p><i class="fas fa-calendar-day"></i> <strong>Data:</strong> <?= $dataFormatada ?></p>
                            <p><i class="fas fa-info-circle"></i> <strong>Status:</strong> <span class="status <?= $statusClass ?>"><?= $statusIcon ?> <?= $statusText ?></span></p>
                            <p><i class="fas fa-credit-card"></i> <strong>Pagamento:</strong> <?= htmlspecialchars($pedido['forma_pagamento']) ?></p>
                            <p><i class="fas fa-receipt"></i> <strong>Total:</strong> <span class="price-highlight">R$ <?= number_format($pedido['valor_total'], 2, ',', '.') ?></span></p>
                            <?php if ($pedido['desconto'] > 0): ?>
                                <p><i class="fas fa-tag"></i> <strong>Desconto:</strong> R$ <?= number_format($pedido['desconto'], 2, ',', '.') ?></p>
                            <?php endif; ?>
                            <p><i class="fas fa-truck"></i> <strong>Frete:</strong> R$ <?= number_format($pedido['frete'], 2, ',', '.') ?></p>
                        </div>

                        <div class="botoes">
                            <a href="visualizar-pedido.php?i=<?= $pedido['id_pedido'] ?>" class="detalhes">
                                <i class="fas fa-info-circle"></i> Detalhes
                            </a>

                            <a href="pedir-suporte.php?pedido=<?= $pedido['id_pedido'] ?>" class="ajuda">
                                <i class="fas fa-question-circle"></i> Ajuda
                            </a>

                            <?php if ($pedido['tipo_status'] !== 'Cancelado pelo Cliente!' && $pedido['tipo_status'] !== 'Entregue!'): ?>
                                <button type="button" onclick="confirmarCancelamento(<?= $pedido['id_pedido'] ?>)" class="btn-cancelar">
                                    <i class="fas fa-times"></i> Cancelar
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Função para confirmar cancelamento
        function confirmarCancelamento(idPedido) {
            Swal.fire({
                title: 'Cancelar Pedido',
                text: 'Tem certeza que deseja cancelar este pedido?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sim, cancelar',
                cancelButtonText: 'Não',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    const formData = new FormData();
                    formData.append('cancelar', true);
                    formData.append('idPedido', idPedido);
                    formData.append('novo_status', 5);

                    fetch('meus-pedidos.php', {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => response.text())
                        .then(data => {
                            // Verifica se retornou TRUE no PHP
                            if (data.includes('true')) {
                                Swal.fire({
                                    title: 'Pedido Cancelado com Sucesso!',
                                    icon: 'success',
                                    confirmButtonText: 'OK'
                                }).then(() => {
                                    window.location.reload();
                                });
                            } else {
                                Swal.fire({
                                    title: 'Erro!',
                                    text: 'Erro ao cancelar pedido!',
                                    icon: 'error',
                                    confirmButtonText: 'OK'
                                });
                            }
                        })
                        .catch(error => {
                            console.error('Erro:', error);
                            Swal.fire({
                                title: 'Erro!',
                                text: 'Ocorreu um erro ao tentar cancelar o pedido.',
                                icon: 'error'
                            });
                        });
                }
            });
        }
    </script>
</body>

</html>