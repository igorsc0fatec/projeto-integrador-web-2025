<?php
session_start();
if (!isset($_SESSION['idCliente'])) {
    session_destroy();
    echo "<script language='javascript' type='text/javascript'> window.location.href='login-cliente.php'</script>";
}

require_once '../controller/controller-chat.php';
$controllerChat = new ControllerChat();

$conversasAtivas = $controllerChat->viewConversa($_SESSION['idUsuario']);

$numProdutosCarrinho = 0;
if (isset($_SESSION['carrinho'])) {
    $numProdutosCarrinho = count($_SESSION['carrinho']);
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Contatos</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../assets/css/style-menu.css">
    <link rel="stylesheet" href="../assets/css/style-contatos.css">
</head>

<body>
    <div class="header">
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
                            <li><a href="meus-pedidos.php">Pedidos</a></li>
                            <li><a href="meus-cupons.php">Cupons</a></li>
                            <li><a href="meus-contatos.php">Conversas</a></li>
                            <li><a href="editar-cliente.php">Meus Dados</a></li>
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
    </div>

    <div>
        <br><br><br><br>
    </div>

    <div class="contacts-container">
        <h1>Meus Contatos</h1>
        <?php
        if (!empty($conversasAtivas)) {
            foreach ($conversasAtivas as $conversa) {
        ?>
                <div class="contact">
                    <img src="../view-confeitaria/<?php echo $conversa['img_confeitaria']; ?>" alt="Foto de Perfil">
                    <a href="chat-cliente.php?u=<?php echo $conversa['id_usuario']; ?>&c=<?php echo $conversa['id_confeitaria']; ?>">
                        <?php echo htmlspecialchars($conversa['nome_confeitaria']); ?>
                    </a>
                </div>
        <?php
            }
        } else {
            echo "<p>Nenhuma conversa encontrada.</p>";
        }
        ?>
    </div>
</body>

</html>