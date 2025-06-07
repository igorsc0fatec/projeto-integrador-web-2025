<?php
include_once '../controller/controller-usuario.php';
$usuarioController = new ControllerUsuario();

$email = $_GET['e'];
$tipo = $_GET['t'];

if (isset($_POST['submit'])) {
    $usuarioController->enviaEmail();
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reativação de Conta</title>
    <style>
        /* CSS */
        html, body {
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
        <h2>Usuario não verificado</h2>
        <p>Identificamos que o seu email ainda não foi verificado, clique no botão pra você recebr um link de verificação na sua conta de email!</p>
        <form action="" id="form" method="post">
            <input type="hidden" id="emailUsuario" name="emailUsuario" value="<?php echo $email ?>">
            <input type="hidden" id="msg" name="msg" value="<?php echo $tipo ?>">
            <button type="submit" id="submit" name="submit">Validar email !</button>
        </form> 
    </div>
</body>

</html>
