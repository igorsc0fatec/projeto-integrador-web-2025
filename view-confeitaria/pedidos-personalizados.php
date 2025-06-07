<?php
session_start();
if (!isset($_SESSION['idConfeitaria'])) {
    session_destroy();
    echo "<script language='javascript' type='text/javascript'> window.location.href='login-confeitaria.php'</script>";
} else if (isset($_SESSION['idTipoUsuario'])) {
    if ($_SESSION['idTipoUsuario'] != 3) {
        echo "<script language='javascript' type='text/javascript'> window.location.href='login-confeitaria.php'</script>";
    }
}

include_once '../controller/controller-pedido-personalizado.php';
$controllerPersonalizado = new ControllerPedidoPersonalizado();
$idConfeitaria = $_SESSION['idConfeitaria'];

$termoPesquisa = '';
if (isset($_GET['termo'])) {
    $termoPesquisa = $_GET['termo'];
    $pedidosPersonalizados = $controllerPersonalizado->buscarPedidosPersonalizados($idConfeitaria, $termoPesquisa);
} else {
    $pedidosPersonalizados = $controllerPersonalizado->getPersonalizadosByConfeitaria();
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="../assets/css/style-menu.css">
    <link rel="stylesheet" href="../assets/css/style-pedidos.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedidos Personalizados - Delícia Online</title>
</head>

<body>
    <div class="container">
        <header>
            <nav>
                <div class="nav-container">
                    <a href="dashboard.php">
                        <img id="logo" src="../assets/img-site/logo.png" alt="Confeitaria">
                    </a>
                    <div class="greeting">
                        <?php echo isset($_SESSION['nome']) ? 'Olá, ' . $_SESSION['nome'] : 'Olá, Visitante'; ?>
                    </div>

                    <i class="fas fa-bars btn-menumobile"></i>
                    <ul class="nav-links">
                        <li><a href="meus-produtos.php">Produtos</a></li>
                        <li><a href="meus-contatos.php">Conversas</a></li>
                        <li><a href="editar-confeitaria.php">Meus Dados</a></li>
                        <li><a href="pedidos.php">Pedidos</a></li>
                        <li><a href="../view/pedir-suporte.php">Suporte</a></li>
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

    <div class="container">
        <div class="header">
            <h1>Pedidos Personalizados Recebidos!</h1>
        </div>

        <div class="search-bar">
            <form action="" method="get">
                <input type="text" name="termo" placeholder="Buscar por ID do pedido ou status"
                    value="<?php echo htmlspecialchars($termoPesquisa); ?>">
                <button type="submit">Buscar</button>
            </form>
        </div>

        <?php if (empty($pedidosPersonalizados)): ?>
            <p>Não há pedidos personalizados no momento.</p>
        <?php else: ?>
            <?php foreach ($pedidosPersonalizados as $pedido): ?>
                <div class="pedido-info">
                    <h2>Pedido Personalizado Nº <?php echo htmlspecialchars($pedido['id_pedido_personalizado']); ?></h2>
                    <p><strong>Data do Pedido:</strong> <?php echo date('d/m/Y', strtotime($pedido['data_pedido'])); ?></p>

                    <p><strong>Status do Pedido:</strong></p>

                    <!-- NOVO SELECT AQUI -->
                    <form action="" method="post">
                        <input type="hidden" name="idPedido"
                            value="<?php echo htmlspecialchars($pedido['id_pedido_personalizado']); ?>">
                        <select name="novo_status" class="select-status" <?php echo ($pedido['status'] === 'Cancelado pelo Cliente' ||
                            $pedido['status'] === 'Entregue!') ? 'disabled' : ''; ?>>
                            <option value="Pedido Recebido!" <?php echo ($pedido['status'] === 'Pedido Recebido!') ? 'selected' : ''; ?>>Pedido Recebido!
                            </option>
                            <option value="Em Preparo!" <?php echo ($pedido['status'] === 'Em Preparo!') ? 'selected' : ''; ?>>Em
                                Preparo! </option>
                            <option value="Em Rota de Entrega!" <?php echo ($pedido['status'] === 'Em Rota de Entrega!') ? 'selected' : ''; ?>>Em Rota de Entrega!</option>
                            <option value="Entregue!" <?php echo ($pedido['status'] === 'Entregue!') ? 'selected' : ''; ?>>
                                Entregue!
                            </option>
                            <option value="Cancelado pela Confeitaria" <?php echo ($pedido['status'] === 'Cancelado pela Confeitaria') ? 'selected' : ''; ?>>Cancelado
                            </option>
                        </select>

                        <?php if ($pedido['status'] !== 'Cancelado pelo Cliente'): ?>
                            <button type="submit" name="alterar_status" class="btn-confirmar">Salvar</button>
                        <?php endif; ?>
                    </form>

                    <p><strong>Valor Total:</strong> R$ <?php echo number_format($pedido['valor_total'], 2, ',', '.'); ?></p>
                    <p><strong>Frete:</strong> R$ <?php echo number_format($pedido['frete'], 2, ',', '.'); ?></p>
                    <p><strong>Desconto:</strong> R$ <?php echo number_format($pedido['desconto'], 2, ',', '.'); ?></p>
                    <p><strong>Cliente:</strong> <?php echo htmlspecialchars($pedido['nome_cliente']); ?></p>

                    <h3>Detalhes do Pedido</h3>
                    <p><strong>Massa:</strong> <?php echo htmlspecialchars($pedido['desc_massa']); ?></p>
                    <p><strong>Recheio:</strong> <?php echo htmlspecialchars($pedido['desc_recheio']); ?></p>
                    <p><strong>Cobertura:</strong> <?php echo htmlspecialchars($pedido['desc_cobertura']); ?></p>
                    <p><strong>Formato:</strong> <?php echo htmlspecialchars($pedido['desc_formato']); ?></p>
                    <p><strong>Decoração:</strong> <?php echo htmlspecialchars($pedido['desc_decoracao']); ?></p>

                    <h3>Produto Personalizado</h3>
                    <p><strong>Nome:</strong> <?php echo htmlspecialchars($pedido['nome_personalizado']); ?></p>
                    <?php if (!empty($pedido['img_personalizado'])): ?>
                        <img src="../view-confeitaria/<?php echo htmlspecialchars($pedido['img_personalizado']); ?>"
                            alt="Imagem do Produto" style="width: 150px; height: auto;">
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <?php
        if (isset($_POST['alterar_status']) && isset($_POST['idPedido'])) {
            if ($controllerPersonalizado->mudarStatusPedidos()) {
                echo "
                    <script>
                        Swal.fire({
                        title: 'Status Alterado Com Sucesso!',
                        icon: 'success',
                        confirmButtonText: 'OK'
                        });
                    </script>";
            } else {
                echo "
                <script>
                        Swal.fire({
                        title: 'Erro ao alterar status!',
                        text: 'Status Cancelado pelo Cliente!',
                        icon: 'error',
                        confirmButtonText: 'OK'
                        });
                    </script>";
            }
        }
        ?>
    </div>
</body>

</html>