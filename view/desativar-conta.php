<?php
session_start();
if (!isset($_SESSION['idUsuario'])) {
    session_destroy();
    echo "<script language='javascript' type='text/javascript'> window.location.href='index.php'</script>";
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/style-form-table.css">
    <link rel="stylesheet" href="../assets/css/style-menu.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://unpkg.com/scrollreveal"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
    <title>Desativar Conta</title>
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
                    <ul>
                        <?php if (isset($_SESSION['idCliente'])): ?>
                            <li><a href="../view-cliente/editar-cliente.php">Dados Pessoais</a></li>
                            <li><a href="../view-cliente/cadastrar-telefone-cliente.php">Telefone</a></li>
                            <li><a href="../view-cliente/editar-usuario-cliente.php">Senha</a></li>
                            <li><a href="../view-cliente/cadastrar-endereco.php">Endereço</a></li>
                            <li><a href="index.php">Voltar </a></li>
                        <?php elseif (isset($_SESSION['idConfeitaria'])): ?>
                            <li><a href="../view-confeitaria/editar-confeitaria.php">Dados da Confeitaria</a></li>
                            <li><a href="../view-confeitaria/cadastrar-telefone-confeitaria.php">Telefone</a></li>
                            <li><a href="../view-confeitaria/editar-usuario-confeitaria.php">Senha</a></li>
                            <li><a href="../view-confeitaria/dashboard-confeitaria.php">Voltar </a></li>
                        <?php endif; ?>
                        <li><a href="#" onclick="confirmarDesativarConta()">Desativar Conta</a></li>
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
            <form id="form" method="post" onsubmit="return validaSenha()">
                <div class="form-header">
                    <div class="title">
                        <h1>Insira a sua senha pra confirmar a desativação da conta!</h1>
                    </div>
                </div>
                <div class="input-box">
                    <div class="input-box">
                        <label for="senhaUsuario">Senha*</label>
                        <input type="password" id="senhaUsuario" name="senhaUsuario" minlength="8" maxlength="15"
                            required>
                    </div>

                    <div class="input-box">
                        <label for="confirmaSenha">Confirmar Senha*</label>
                        <input type="password" id="confirmaSenha" name="confirmaSenha" minlength="8" maxlength="15"
                            required>
                        <span id="erroSenha1" class="error"></span>
                    </div>
                </div>
                <div class="continue-button">
                    <button type="submit" id="submit" name="submit">Desativar Conta!</button>
                </div>
            </form>
        </div>
    </div>

    <?php
    include_once '../controller/controller-usuario.php';
    $usuarioController = new ControllerUsuario();

    if (isset($_POST['submit'])) {
        if ($usuarioController->ativaUsuario(0, $_SESSION['emailUsuario'])) {
            echo "
                <script>
                    Swal.fire({
                    title: 'Conta desativada com sucesso!',
                    text: 'Para reativa-la faça login novamente!',
                    icon: 'success',
                    confirmButtonText: 'OK',
                    didClose: () => {
                window.location.href = 'index.php';
            }
                    });
                </script>";
            session_destroy();
        } else {
            echo "
                <script>
                    Swal.fire({
                    title: 'Erro ao desativar a conta!',
                    text: 'A senha esta incorreta!',
                    icon: 'error',
                    confirmButtonText: 'OK'
                    });
                </script>";
        }
    }
    ?>
    <script src="assets/js/valida-senha.js"></script>
    <script src="assets/js/valida-enviar.js"></script>
</body>

</html>