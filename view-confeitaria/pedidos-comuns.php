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

include_once '../controller/controller-pedido.php';
$controllerPedido = new ControllerPedido();
$idConfeitaria = $_SESSION['idConfeitaria'];

$termoPesquisa = '';
if (isset($_GET['termo'])) {
    $termoPesquisa = $_GET['termo'];
    $pedidos = $controllerPedido->buscarPedidosConfeitaria($idConfeitaria, $termoPesquisa);
} else {
    $pedidos = $controllerPedido->getPedidosConfeitaria($idConfeitaria);
}

$status = $controllerPedido->status();
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style-menu.css">
    <link rel="stylesheet" href="../assets/css/style-pedidos.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedidos - Delicia Online</title>
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

        <div class="header">
            <h1>Pedidos Recebidos</h1>
            <p>Acompanhe e gerencie todos os pedidos da sua confeitaria</p>
        </div>

        <div class="search-bar">
            <form action="" method="get">
                <input type="text" name="termo" placeholder="Buscar por ID do pedido ou status" value="<?php echo htmlspecialchars($termoPesquisa); ?>">
                <button type="submit"><i class="fas fa-search"></i> Buscar</button>
            </form>
        </div>

        <div class="pedidos-container">
            <?php if (empty($pedidos)): ?>
                <p class="empty-message">Não há pedidos no momento.</p>
            <?php else: ?>
                <?php
                $currentPedidoId = null;
                foreach ($pedidos as $pedido):
                    if ($pedido['id_pedido'] !== $currentPedidoId):
                        if ($currentPedidoId !== null): ?>
        </div> <!-- Fecha itens-container -->
        <div class="pedido-total">
            <div class="total-destaque">
                <p><span>Subtotal:</span> <span>R$ <?php echo number_format($totalItens, 2, ',', '.'); ?></span></p>
                <p><span>Frete:</span> <span>R$ <?php echo number_format($lastPedido['frete'], 2, ',', '.'); ?></span></p>
                <p><span>Desconto:</span> <span>R$ <?php echo number_format($lastPedido['desconto'], 2, ',', '.'); ?></span></p>
                <p><span>Total:</span> <span>R$ <?php echo number_format($lastPedido['valor_total'], 2, ',', '.'); ?></span></p>
            </div>
        </div>
    </div> <!-- Fecha pedido-content -->
    </div> <!-- Fecha pedido-card -->
<?php endif; ?>
<div class="pedido-card" data-status="<?php echo htmlspecialchars($pedido['tipo_status']); ?>">
    <div class="pedido-header">
        <h2>Pedido Nº <?php echo htmlspecialchars($pedido['id_pedido']); ?></h2>
        <div class="pedido-meta">
            <p><strong>Data:</strong> <?php echo date('d/m/Y H:i', strtotime($pedido['data_pedido'])); ?></p>
            <p><strong>Status:</strong> <?php echo htmlspecialchars($pedido['tipo_status']); ?></p>
            <p><strong>Cliente:</strong> <?php echo htmlspecialchars($pedido['nome_cliente']); ?></p>
            <p><strong>Pagamento:</strong> <?php echo htmlspecialchars($pedido['forma_pagamento']); ?></p>
        </div>
    </div>

    <form class="status-form" action="" method="post">
        <input type="hidden" name="idPedido" value="<?php echo htmlspecialchars($pedido['id_pedido']); ?>">
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
        <?php elseif ($pedido['tipo_status'] === 'Cancelado pelo Cliente!'): ?>
            <p style="color: red; font-weight: bold;">O Cliente Cancelou esse Pedido!</p>
        <?php endif; ?>
    </form>

    <div class="pedido-content">
        <div class="itens-container">
        <?php
                        $currentPedidoId = $pedido['id_pedido'];
                        $lastPedido = $pedido;
                        $totalItens = 0;
                    endif;

                    $valorItem = $pedido['valor_produto'] * $pedido['quantidade'];
                    $totalItens += $valorItem;
        ?>
        <div class="item">
            <div class="item-details">
                <p class="item-title"><?php echo htmlspecialchars($pedido['nome_produto']); ?></p>
                <p><?php echo htmlspecialchars($pedido['desc_produto']); ?></p>
                <p><strong>Quantidade:</strong> <?php echo htmlspecialchars($pedido['quantidade']); ?></p>
                <p class="item-total">Total: R$ <?php echo number_format($valorItem, 2, ',', '.'); ?></p>
                <?php if (!empty($pedido['desc_cupom'])): ?>
                    <p><strong>Cupom:</strong> <?php echo htmlspecialchars($pedido['desc_cupom']); ?> (<?php echo htmlspecialchars($pedido['porcen_desconto']); ?>%)</p>
                <?php endif; ?>
            </div>
            <div class="item-price">
                R$ <?php echo number_format($pedido['valor_produto'], 2, ',', '.'); ?> un
            </div>
        </div>
    <?php endforeach; ?>
        </div> <!-- Fecha itens-container -->

        <div class="pedido-total">
            <div class="total-destaque">
                <p><span>Subtotal:</span> <span>R$ <?php echo number_format($totalItens, 2, ',', '.'); ?></span></p>
                <p><span>Frete:</span> <span>R$ <?php echo number_format($lastPedido['frete'], 2, ',', '.'); ?></span></p>
                <p><span>Desconto:</span> <span>R$ <?php echo number_format($lastPedido['desconto'], 2, ',', '.'); ?></span></p>
                <p><span>Total:</span> <span>R$ <?php echo number_format($lastPedido['valor_total'], 2, ',', '.'); ?></span></p>
            </div>
        </div>
    </div> <!-- Fecha pedido-content -->
</div> <!-- Fecha pedido-card -->
<?php endif; ?>
</div> <!-- Fecha pedidos-container -->

<?php
if (isset($_POST['alterar_status'])) {
    if ($controllerPedido->mudarStatusPedidos()) {
        echo "<script>
                    Swal.fire({
                        title: 'Sucesso!',
                        text: 'Status do pedido atualizado com sucesso!',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    });
                </script>";
    } else {
        echo "<script>
                    Swal.fire({
                        title: 'Erro!',
                        text: 'Não foi possível alterar o status do pedido.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                </script>";
    }
}
?>
</div>

<script>
    // Menu mobile
    document.querySelector('.btn-menumobile').addEventListener('click', function() {
        document.querySelector('.nav-links').classList.toggle('active');
    });
</script>
</body>

</html>