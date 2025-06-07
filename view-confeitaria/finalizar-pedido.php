<?php
session_start();
if (!isset($_SESSION['idConfeitaria'])) {
    session_destroy();
    echo "<script language='javascript' type='text/javascript'> window.location.href='login-confeitaria.php'</script>";
    exit;
} else if (isset($_SESSION['idTipoUsuario'])) {
    if ($_SESSION['idTipoUsuario'] != 3) {
        echo "<script language='javascript' type='text/javascript'> window.location.href='login-confeitaria.php'</script>";
        exit;
    }
}

$id = $_GET['i'];
include_once '../controller/controller-pedido-personalizado.php';
include_once '../controller/controller-pedido.php';
$controllerPedidoPersonalizado = new ControllerPedidoPersonalizado();
$controllerPedido = new ControllerPedido();

$formasPagamento = $controllerPedido->formaPagamento();
$dadosPersonalizado = $controllerPedidoPersonalizado->getPedidosPersonalizadosByIdPedido($id);
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@400;700&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../assets/css/style_home.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"
        integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://unpkg.com/scrollreveal"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delicia online</title>
</head>

<body>
    <div class="container">
        <header>
            <nav>
                <div class="nav-container">
                    <a href="dashboard-confeitaria.php">
                        <img id="logo" src="assets/img/logo.png" alt="JobFinder">
                    </a>
                    <i class="fas fa-bars btn-menumobile"></i>
                    <ul>
                        <li><a href="meus-produtos.php">Meus Produtos</a></li>
                        <li><a href="meus-contatos.php"><i class='fas fa-comments' style='font-size:20px'></i> Chat</a>
                        </li>
                        <li><a href="pedidos.php">Pedidos</a></li>
                        <li><a href="../view/pedir-suporte.php">Suporte</a></li>
                        <li><a href="editar-confeitaria.php">Meus Dados</a></li>
                    </ul>
                </div>
            </nav>
        </header>
    </div>

    <div>
        <br><br><br><br><br><br>
    </div>

    <?php foreach ($dadosPersonalizado as $dado) { ?>
        <div class="form-container">
            <div class="form-image">
                <img src="assets/img/logo.png" alt="">
            </div>
            <div class="form">
                <form action="#" method="Post">
                    <div class="form-header">
                        <div class="title">
                            <h1>Finalizar produto personalizado</h1>
                        </div>
                    </div>
                    <div class="input-group">
                        <div class="input-box">
                            <label for="name">Nome do Produto:</label>
                            <input id="name" type="text" name="name" value="<?php echo $dado['nomePersonalizado'] ?>"
                                disabled>
                        </div>
                    </div>

                    <div class="input-group">
                        <div class="input-box">
                            <label for="data">Data do Pedido:</label>
                            <input id="data" type="text" name="data"
                                value="<?php echo date('d/m/Y', strtotime($dado['dataPedido'])) ?>" disabled>
                        </div>

                        <div class="input-box">
                            <label for="hora">Hora do Pedido:</label>
                            <input id="hora" type="text" name="hora"
                                value="<?php echo date('H:i', strtotime($dado['horaPedido'])) ?>" disabled>
                        </div>

                        <div class="input-box">
                            <label for="valor">Valor:</label>
                            <input id="valor" type="number" min=0.01 step="0.01" maxlength="8" name="valor" required>
                        </div>

                        <div class="input-box">
                            <label for="desconto">Desconto:</label>
                            <input id="desconto" type="number" min=0.0 step="0.01" maxlength="8" name="desconto" required>
                        </div>

                        <div class="input-box">
                            <label for="frete">Frete:</label>
                            <input id="frete" type="number" min=0.0 step="0.01" maxlength="8" name="frete" required>
                        </div>

                        <div class="input-box">
                            <label for="FormaPagamento">Forma de pagamento:</label>
                            <select id="idForma" name="idForma">
                                <?php foreach ($formasPagamento as $fp): ?>
                                    <option value=<?php echo $fp['idFormaPagamento']; ?>><?php echo $fp['formaPagamento'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <h2>Detalhes do produto</h2>

                    <div class="input-group">
                        <div class="input-box">
                            <label for="Massa">Massa:</label>
                            <input id="massa" type="text" name="massa" value="<?php echo $dado['descMassa'] ?>" disabled>
                        </div>

                        <div class="input-box">
                            <label for="Recheio">Recheio:</label>
                            <input id="Recheio" type="text" name="Recheio" value="<?php echo $dado['descRecheio'] ?>"
                                disabled>
                        </div>

                        <div class="input-box">
                            <label for="Cobertura">Cobertura:</label>
                            <input id="Cobertura" type="text" name="Cobertura" value="<?php echo $dado['descCobertura'] ?>"
                                disabled>
                        </div>

                        <div class="input-box">
                            <label for="Formato">Formato:</label>
                            <input id="Formato" type="text" name="Formato" value="<?php echo $dado['descFormato'] ?>"
                                disabled>
                        </div>

                        <div class="input-box">
                            <label for="Descricao">Descrição:</label>
                            <textarea id="descricao" name="descricao"
                                disabled><?php echo $dado['descDecoracao'] ?></textarea>
                        </div>
                        <input id="id" type="hidden" name="id" value="<?php echo $id ?>">
                    </div>
                    <div class="continue-button">
                        <button type="submit" id="submit" name="submit">Finalizar Pedido</button>
                    </div>
                </form>
            </div>
        </div>
    <?php } ?>

    <?php
    if (isset($_POST['submit'])) {
        if ($controllerPedidoPersonalizado->updatePedidoPersonalizado()) {
            echo "
                <script>
                    Swal.fire({
                    title: 'Pedido finalizado com sucesso!',
                    icon: 'success',
                    confirmButtonText: 'OK',
                    didClose: () => {
                        window.location.href = 'pedidos-personalizados.php';
                    }
                    });
                </script>";
        }
    }
    ?>
    
</body>

</html>