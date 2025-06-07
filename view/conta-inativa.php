<?php
include_once '../controller/controller-usuario.php';
$usuarioController = new ControllerUsuario();

session_start();

$email = $_GET['e'];
$tipo = $_GET['t'];

?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title>Reativação de Conta</title>
    <style>
        /* CSS */
        html,
        body {
            height: 100%;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #f0f0f0;
        }

        .container {
            text-align: center;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .container h2 {
            margin-bottom: 20px;
            color: #333333;
        }

        .container form {
            display: inline-block;
        }

        .container form button {
            padding: 10px 20px;
            font-size: 16px;
            background-color: #007bff;
            color: #ffffff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .container form button:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Reativação de Conta</h2>
        <p>Identificamos que a sua conta está desativada. Clique no botão abaixo para reativar a sua conta!</p>
        <form action="" method="post">
            <input type="hidden" name="emailUsuario" value="<?php echo $email ?>">
            <input type="hidden" name="tipo" value="<?php echo $tipo ?>">
            <button type="submit" name="submit">Ativar Conta</button>
        </form>
    </div>

    <?php
    if (isset($_POST['submit'])) {
        if ($usuarioController->ativaUsuario(1, $_POST['emailUsuario'])) {
            $_SESSION['emailUsuario'] = $_POST['emailUsuario'];
            if ($_POST['tipo'] == "2") {
                echo "
                <script>
                    Swal.fire({
                    title: 'Conta ativada com sucesso!',
                    icon: 'success',
                    confirmButtonText: 'OK',
                    didClose: () => {
                window.location.href = 'index.php';
            }
                    });
                </script>";
            } else if ($_POST['tipo'] == "3") {
                echo "
                <script>
                    Swal.fire({
                    title: 'Conta ativada com sucesso!',
                    icon: 'success',
                    confirmButtonText: 'OK',
                    didClose: () => {
                window.location.href = '../view-confeitaria/dashboard-confeitaria.php';
            }
                    });
                </script>";
            }
        }
    } else {
        session_destroy();
    }
    ?>
</body>

</html>