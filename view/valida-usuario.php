<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title>Document</title>
</head>

<body>
    O seu email foi validado com sucesso!
    <br>
    <form action="" method="post">
        <button type="submit" id="logar" name="logar">Fazer Login</button>
    </form>

    <?php
    include_once '../controller/controller-usuario.php';
    $usuarioController = new ControllerUsuario();
    $email = $_GET['e'];

    if ($usuarioController->validaEmail($email)) {
        echo "
            <script>
                Swal.fire({
                title: 'Email validado com sucsso!',
                icon: 'success',
                confirmButtonText: 'OK'
                });
            </script>";
    }

    $u = $_GET['u'];

    if (isset($_POST['logar'])) {
        if ($u === 'cliente') {
            echo "<script language='javascript' type='text/javascript'> window.location.href='../view-cliente/login-cliente.php'</script>";
        } else if ($u === 'confeitaria') {
            echo "<script language='javascript' type='text/javascript'> window.location.href='../view-confeitaria/login-confeitaria.php'</script>";
        }
    }
    ?>
</body>

</html>