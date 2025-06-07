<?php
session_start();
if (!isset($_SESSION['idCliente'])) {
    session_destroy();
    echo "<script language='javascript' type='text/javascript'> window.location.href='login-cliente.php'</script>";
} else if (isset($_SESSION['idTipoUsuario'])) {
    if ($_SESSION['idTipoUsuario'] != 2) {
        echo "<script language='javascript' type='text/javascript'> window.location.href='login-cliente.php'</script>";
    }
}

require_once '../controller/controller-pedido.php';
require_once '../controller/controller-cliente.php';
$controllerPedido = new ControllerPedido();
$controllerCliente = new ControllerCliente();
$formasPagamento = $controllerPedido->formaPagamento();
$enderecos = $controllerCliente->viewEndereco($_SESSION['idCliente']);

if (!isset($_SESSION['carrinho']) || empty($_SESSION['carrinho'])) {
    echo "<script>window.location.href='carrinho.php';</script>";
    exit;
}

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Checkout</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"
        integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <link rel="stylesheet" href="../assets/css/style-menu.css">
    <link rel="stylesheet" href="../assets/css/style-pagamento.css">
</head>

<body>

    <div class="container" style="margin-bottom: 50px;">
        <header>
            <nav>
                <div class="nav-container">
                    <a href="dashboard-cliente.php">
                        <img id="logo" src="../assets/img-site/logo.png" alt="JobFinder">
                    </a>
                    <i class="fas fa-bars btn-menumobile"></i>
                    <ul>
                        <li><a href="../view-cliente/meus-pedidos.php">Pedidos</a></li>
                        <li><a href="../view-cliente/meus-cupons.php">Cupons</a></li>
                        <li><a href="../view-cliente/meus-contatos.php">Conversas</a></li>
                        <li><a href="../view-cliente/editar-cliente.php">Meus Dados</a></li>
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

    <!-- Formulário principal (invisível) que envolve todo o conteúdo -->
    <form id="formPedido" action="" method="post" style="display: contents;">
        <div class="checkout-container">

            <div class="left-panel">
                <section>
                    <h2 class="section-title">Forma de Pagamento</h2>
                    <div class="radio-group">
                        <?php foreach ($formasPagamento as $forma): ?>
                            <label>
                                <input type="radio" name="pagamento" value="<?php echo $forma['id_forma_pagamento']; ?>"
                                    <?php echo ($forma === reset($formasPagamento)); ?>>
                                <?php echo htmlspecialchars($forma['forma_pagamento']); ?>
                                <small style="color: gray;"> - <?php echo htmlspecialchars($forma['descricao']); ?></small>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </section>

                <section>
                    <h2 class="section-title">Endereço de Entrega</h2>
                    <div class="radio-group">
                        <?php foreach ($enderecos as $endereco): ?>
                            <label>
                                <input type="radio" name="endereco" value="<?php echo $endereco['id_endereco_cliente']; ?>"
                                    <?php echo ($endereco === reset($enderecos)); ?>>
                                <?php echo htmlspecialchars($endereco['log_cliente']) . ', Nº ' . $endereco['num_local'] . ', ' . $endereco['bairro_cliente'] . ' - ' . $endereco['cidade_cliente'] . '/' . $endereco['uf_cliente']; ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </section>

                <input type="hidden" name="cupom" value="<?php echo $_SESSION['dados_pedido']['id_cupom'] ?? '0,00'; ?>">
            </div>

            <div class="right-panel">
                <h2 class="section-title">Resumo da Compra</h2>

                <!-- Botão de submit estilizado -->
                <div class="submit-button" style="margin-bottom: 1.5rem;">
                    <button type="submit" name="confirmar" form="formPedido">Confirmar Pedido</button>
                </div>

                <!-- Totais -->
                <div class="total-info" style="margin-bottom: 2rem;">
                    <p><strong>Frete:</strong> R$ <?php echo $_SESSION['dados_pedido']['frete'] ?? '0,00'; ?></p>
                    <p><strong>Desconto:</strong> R$ <?php echo $_SESSION['dados_pedido']['valorDesconto'] ?? '0,00'; ?>
                    </p>
                    <p><strong>Total:</strong> R$ <?php echo $_SESSION['dados_pedido']['valorTotal'] ?? '0,00'; ?></p>
                </div>

                <!-- Produtos -->
                <?php foreach ($_SESSION['carrinho'] as $item): ?>
                    <?php if (is_array($item) && isset($item['imgProduto'])): ?>
                        <div class="order-item">
                            <img src="<?php echo '../view-confeitaria/' . htmlspecialchars($item['imgProduto']); ?>"
                                alt="Produto">
                            <div class="item-info">
                                <strong><?php echo htmlspecialchars($item['nomeProduto']); ?></strong><br>
                                <small><?php echo htmlspecialchars($item['descProduto']); ?></small><br>
                                <small>Qtd: <?php echo htmlspecialchars($item['quantidade']); ?></small>
                            </div>
                            <div class="item-price">
                                R$ <?php echo number_format($item['valorProduto'], 2, ',', '.'); ?>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
    </form>

    <?php
    if (isset($_POST['confirmar'])) {
        if (!isset($_POST['pagamento']) || !isset($_POST['endereco'])) {
            echo "<script>
                Swal.fire('Erro', 'Por favor, selecione uma forma de pagamento e um endereço', 'error');
            </script>";
        } else {
            $idPedido = $controllerPedido->addPedido();
            if (!empty($idPedido)) {
                if ($controllerPedido->addItensPedido($idPedido)) {
                    echo "
                    <script>
                        Swal.fire({
                            title: 'Pedido feito com sucesso!',
                            icon: 'success',
                            confirmButtonText: 'OK',
                            didClose: () => {
                                window.location.href = 'visualizar-pedido.php?i=$idPedido';
                            }               
                        });
                    </script>";
                }
            } else {
                echo "<script>
                    Swal.fire('Erro', 'Não foi possível processar o pedido', 'error');
                </script>";
            }
        }
    }
    ?>

    <script>
        $(document).ready(function () {
            $('#formPedido').submit(function (e) {
                // Verifica se uma forma de pagamento foi selecionada
                if (!$('input[name="pagamento"]:checked').val()) {
                    e.preventDefault();
                    Swal.fire('Erro', 'Selecione uma forma de pagamento', 'error');
                    return false;
                }

                // Verifica se um endereço foi selecionado
                if (!$('input[name="endereco"]:checked').val()) {
                    e.preventDefault();
                    Swal.fire('Erro', 'Selecione um endereço de entrega', 'error');
                    return false;
                }

                return true;
            });
        });
    </script>

</body>

</html>