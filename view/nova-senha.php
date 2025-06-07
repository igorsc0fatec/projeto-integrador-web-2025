<?php
require_once '../controller/controller-usuario.php';
$usuarioController = new ControllerUsuario();

$idUsuario = isset($_GET['idUsuario']) ? $_GET['idUsuario'] : null;

if (!$idUsuario) {
    header('Location: recuperar-senha.php');
    exit;
}

if (isset($_POST['novaSenha'])) {
    $novaSenha = $_POST['novaSenha'];
    $confirmarSenha = $_POST['confirmarSenha'];

    if ($novaSenha === $confirmarSenha) {
        if ($usuarioController->atualizarSenha($idUsuario, $novaSenha)) {
            echo "
            <script>
                Swal.fire({
                    title: 'Sucesso!',
                    text: 'Senha atualizada com sucesso.',
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then(() => {
                    window.location.href = 'login-cliente.php';
                });
            </script>";
        } else {
            echo "
            <script>
                Swal.fire({
                    title: 'Erro!',
                    text: 'Ocorreu um erro ao atualizar a senha.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            </script>";
        }
    } else {
        echo "
        <script>
            Swal.fire({
                title: 'Erro!',
                text: 'As senhas não coincidem.',
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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@400;700&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../assets/css/style-menu.css">
    <link rel="stylesheet" href="../assets/css/style-form-table.css">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"
        integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://unpkg.com/scrollreveal"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
    <title>Nova Senha</title>
</head>

<body>
    <div class="container">
        <header>
            <nav>
                <div class="nav-container">
                    <a href="../view/index.php">
                        <img id="logo" src="../assets/img-site/logo.png" alt="JobFinder">
                    </a>
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
                        <li><a href="../view-cliente/login-cliente.php">Sou Cliente</a></li>
                        <li><a href="../view-confeitaria/login-confeitaria.php">Sou Confeitaria</a></li>
                    </ul>
                </div>
            </nav>
        </header>
    </div>

    <div>
        <br><br>
    </div>

    <div class="form-container">
        <div class="form-image">
            <img src="../assets/img-site/cadeado.png" alt="">
        </div>
        <div class="form">
            <form id="form-nova-senha" method="post">
                <div class="form-header">
                    <div class="title">
                        <h1>Nova Senha</h1>
                    </div>
                </div>

                <div class="input-box">
                    <label for="novaSenha">Nova Senha*</label>
                    <div class="dados">
                        <input type="password" id="novaSenha" name="novaSenha" required>
                    </div>
                </div>

                <div class="input-box">
                    <label for="confirmarSenha">Confirmar Senha*</label>
                    <div class="dados">
                        <input type="password" id="confirmarSenha" name="confirmarSenha" required>
                    </div>
                </div>

                <div class="continue-button">
                    <button type="submit" id="enviar" name="enviar">Enviar</button>
                </div>
            </form>
        </div>
    </div>

</body>

</html>