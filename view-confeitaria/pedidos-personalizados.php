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

$status = $controllerPersonalizado->status();
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
                        <?php echo $_SESSION['nome']; ?>
                    </div>

                    <i class="fas fa-bars btn-menumobile"></i>
                    <ul class="nav-links">
                        <li><a href="meus-produtos.php">Produtos</a></li>
                        <li><a href="pedidos.php">Pedidos</a></li>
                        <li><a href="meus-contatos.php">Conversas</a></li>
                        <li><a href="editar-confeitaria.php">Meus Dados</a></li>
                        <li><a href="../view/pedir-suporte.php">Suporte</a></li>
                        <li><a href="dashboard.php">Voltar</a></li>
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
                <div class="pedido-card" data-status="<?php echo htmlspecialchars($pedido['tipo_status']); ?>">
                    <div class="pedido-header">
                        <h2>Pedido Personalizado Nº <?php echo htmlspecialchars($pedido['id_pedido_personalizado']); ?></h2>
                        <div class="pedido-meta">
                            <p><strong>Data:</strong> <?php echo date('d/m/Y H:i', strtotime($pedido['data_pedido'])); ?></p>
                            <p><strong>Status:</strong> <?php echo htmlspecialchars($pedido['tipo_status']); ?></p>
                            <p><strong>Cliente:</strong> <?php echo htmlspecialchars($pedido['nome_cliente']); ?></p>
                        </div>
                    </div>

                    <div class="status-container" style="display: flex; align-items: center; gap: 20px;">

                        <form class="status-form" action="" method="post" style="display: flex; align-items: center; gap: 10px;">
                            <input type="hidden" name="idPedido" value="<?php echo htmlspecialchars($pedido['id_pedido_personalizado']); ?>">
                            <select name="novo_status" class="select-status" <?php echo ($pedido['tipo_status'] === 'Cancelado pelo Cliente!' || $pedido['tipo_status'] === 'Entregue!') ? 'disabled' : ''; ?>>
                                <?php foreach ($status as $st): ?>
                                    <option value="<?php echo $st['id_status']; ?>"
                                        <?php echo ($pedido['tipo_status'] === $st['tipo_status']) ? 'selected' : ''; ?>>
                                        <?php echo $st['tipo_status']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>

                            <?php if ($pedido['tipo_status'] !== 'Cancelado pelo Cliente!' && $pedido['tipo_status'] !== 'Entregue!'): ?>
                                <button type="submit" name="alterar_status" class="btn-confirmar"><i class="fas fa-save"></i> Salvar</button>
                                <a href="finalizar-pedido.php?id=<?php echo urlencode($pedido['id_pedido_personalizado']); ?>" class="btn-confirmar" style="text-decoration: none;"><i class="fas fa-check"></i> Finalizar</a>
                            <?php elseif ($pedido['tipo_status'] === 'Cancelado pelo Cliente!'): ?>
                                <p style="color: red; font-weight: bold;">O Cliente Cancelou esse Pedido!</p>
                            <?php endif; ?>
                        </form>
                    </div>

                    <div class="pedido-content">
                        <div class="itens-container">
                            <div class="item">
                                <div class="item-details">
                                    <p class="item-title"><?php echo htmlspecialchars($pedido['nome_personalizado']); ?></p>
                                    <p><strong>Massa:</strong> <?php echo htmlspecialchars($pedido['desc_massa']); ?></p>
                                    <p><strong>Recheio:</strong> <?php echo htmlspecialchars($pedido['desc_recheio']); ?></p>
                                    <p><strong>Cobertura:</strong> <?php echo htmlspecialchars($pedido['desc_cobertura']); ?></p>
                                    <p><strong>Formato:</strong> <?php echo htmlspecialchars($pedido['desc_formato']); ?></p>
                                    <p><strong>Decoração:</strong> <?php echo htmlspecialchars($pedido['desc_decoracao']); ?></p>
                                    <?php if (!empty($pedido['desc_cupom'])): ?>
                                        <p><strong>Cupom:</strong> <?php echo htmlspecialchars($pedido['desc_cupom']); ?> (<?php echo htmlspecialchars($pedido['porcen_desconto']); ?>%)</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="pedido-total">
                            <div class="total-destaque">
                                <p><span>Subtotal:</span> <span>R$ <?php echo number_format($pedido['valor_total'] - $pedido['frete'] + $pedido['desconto'], 2, ',', '.'); ?></span></p>
                                <p><span>Frete:</span> <span>R$ <?php echo number_format($pedido['frete'], 2, ',', '.'); ?></span></p>
                                <p><span>Desconto:</span> <span>R$ <?php echo number_format($pedido['desconto'], 2, ',', '.'); ?></span></p>
                                <p><span>Total:</span> <span>R$ <?php echo number_format($pedido['valor_total'], 2, ',', '.'); ?></span></p>
                            </div>
                        </div>
                    </div>
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
                        text: 'Pedido não foi Concluido!',
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