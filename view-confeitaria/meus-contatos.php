<?php
session_start();
if (!isset($_SESSION['idConfeitaria'])) {
    session_destroy();
    echo "<script language='javascript' type='text/javascript'> window.location.href='login-confeitaria.php'</script>";
}

include_once '../controller/controller-chat.php';
$controllerChat = new ControllerChat();

$conversasAtivas = $controllerChat->viewConversa($_SESSION['idUsuario']);
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
                        <a href="dashboard.php">
                            <img id="logo" src="../assets/img-site/logo.png" alt="JobFinder">
                        </a>
                        <div class="greeting">
                            <?php
                            if (isset($_SESSION['nome'])) {
                                echo $_SESSION['nome'];
                            } else {
                                echo "Ola, Visitante";
                            }
                            ?>
                        </div>

                        <i class="fas fa-bars btn-menumobile"></i>
                        <ul class="nav-links">
                            <li><a href="meus-produtos.php">Produtos</a></li>
                            <li><a href="meus-contatos.php">Conversas</a></li>
                            <li><a href="editar-confeitaria.php">Meus Dados</a></li>
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
    </div>

    <div>
        <br><br><br><br>
    </div>

    <div class="contacts-container">
        <h1>Meus Contatos</h1>
        <?php if (!empty($conversasAtivas)) { ?>
            <?php foreach ($conversasAtivas as $conversa) { ?>
                <div class="contact">
                    <img src="../assets/img-site/icon.png" alt="Foto de Perfil">
                    <a href="chat-confeitaria.php?u=<?php echo $conversa['id_usuario']; ?>&c=<?php echo $conversa['id_cliente']; ?>">
                        <?php echo htmlspecialchars($conversa['nome_cliente']); ?>
                    </a>
                </div>
            <?php } ?>
        <?php } else { ?>
            <p>Você não tem contatos ativos.</p>
        <?php } ?>
    </div>
</body>

</html>