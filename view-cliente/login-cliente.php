<?php
require_once '../controller/controller-usuario.php';
$usuarioController = new ControllerUsuario();
session_start();

if(isset($_SESSION['idCliente'])){
    echo "<script language='javascript' type='text/javascript'> window.location.href='../view/index.php'</script>";
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página de Login</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="../assets/css/style-login.css">
</head>

<body>
    <div class="container">
        <div class="image">
            <img src="../assets/img-site/fundo_login.jpg" alt="Imagem de fundo">
        </div>
        <div class="login-form">
            <h2>Bem Vindo</h2>
            <form method="post">
                <input type="email" name="emailUsuario" id="emailUsuario" placeholder="Email" required><br>
                <input type="password" name="senhaUsuario" id="senhaUsuario" placeholder="Senha" minlength="8"
                    maxlength="15" required><br>
                <a href="../view/recuperar-senha.php">Esqueceu a senha?</a>
                <div class="buttons-container">
                    <input type="submit" id="logar" name="logar" value="Login">
                </div>
            </form>
            <div class="back-button"><a href="../view/index.php">Voltar</a></div>
            <p>Ainda não possui um cadastro? <a href="cadastrar-cliente.php">Cadastre-se</a></p>
        </div>
    </div>

    <?php
    if (isset($_POST['logar'])) {
        if (isset($_SESSION['idConfeitaria'])) {
            session_unset();
        }
        $idTipoUsuario = $usuarioController->verificaLogin();

        if ($idTipoUsuario != 0) {
            $_SESSION['emailUsuario'] = $_POST['emailUsuario'];
        }

        if ($idTipoUsuario == 1) {
            echo "<script language='javascript' type='text/javascript'> window.location.href='../view-root/dashboard-root.php'</script>";
        } else if ($idTipoUsuario == 2) {
            if (isset($_SESSION['carrinho'])) {
                echo "<script language='javascript' type='text/javascript'> window.location.href='carrinho.php'</script>";
            } else {
                echo "<script language='javascript' type='text/javascript'> window.location.href='../view/index.php'</script>";
            }
        } else if ($idTipoUsuario == 4 || $idTipoUsuario != 1) {
            session_destroy();
            echo "
            <script>
                Swal.fire({
                title: 'Erro ao logar!',
                text: 'Usuario não Encontrado!',
                icon: 'error',
                confirmButtonText: 'OK'
                });
            </script>";
        }
    }
    ?>
</body>

</html>