<?php
session_start();
if (!isset($_SESSION['idCliente'])) {
    echo "<script language='javascript' type='text/javascript'> window.location.href='clientes-root.php'</script>";
}

include_once '../controller/controller-cliente.php';
$clienteController = new ControllerCliente();
$cliente = $clienteController->viewCliente();

if (isset($_GET['action']) && $_GET['action'] == 'fetch_data') {
    header('Content-Type: application/json');
    echo json_encode($cliente);
    exit;
}
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
    <link rel="stylesheet" href="assets/css/style_home.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/menu.css">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"
        integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://unpkg.com/scrollreveal"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
    <title>Delicia online</title>

</head>

<body>
    <div class="container">
        <header>
            <nav>
                <div class="nav-container">
                    <a href="dashboard-cliente.php">
                        <img id="logo" src="assets/img/logo.png" alt="JobFinder">
                    </a>
                    <i class="fas fa-bars btn-menumobile"></i>
                    <ul>
                        <li><a href="clientes-root.php">Voltar </a></li>
                    </ul>
                </div>
            </nav>
        </header>
    </div>

    <div>
        <br><br><br><br><br><br>
    </div>

    <div class="form-container">
        <div class="form-image">
            <img src="assets/img/pessoa.webp" alt="">
        </div>
        <div class="form">
            <form id="form" method="post" onsubmit="return validaEditCliente()">
                <div class="form-header">
                    <div class="title">
                        <h1>Dados Pessoais</h1>
                    </div>
                </div>
                <div class="input-group">
                    <div class="input-box">
                        <label for="nome">Nome Completo*</label>
                        <input type="text" id="nomeCliente" name="nomeCliente" required>
                    </div>
                    <div class="input-box">
                        <label for="date">Data de Nascimento*</label>
                        <input type="date" id="nascCliente" name="nascCliente" required>
                        <span id="erroData" class="error"></span>
                    </div>
                    <div class="input-box">
                        <label for="cpf">CPF*</label>
                        <input type="text" id="cpfCliente" name="cpfCliente" required>
                        <span id="erroCpf" class="error"></span>
                    </div>
                </div>
                <div class="continue-button">
                    <button type="submit" id="submit" name="submit">Editar</button>
                </div>
            </form>
        </div>
    </div>
    </div>

    <?php
    if (isset($_POST['submit'])) {
        if ($clienteController->updateCliente()) {
            echo "
                <script>
                    Swal.fire({
                    title: 'Editado com sucesso!',
                    icon: 'success',
                    confirmButtonText: 'OK'
                    });
                </script>";
        }
    }
    ?>

    <div>
        <br><br>
    </div>

    <footer class="rodape">
        <p>© 2024 | Todos os direitos são de propriedade da FoxBitSystem</p>
    </footer>
    <script src="assets/js/valida-cliente-root.js"></script>

    <script>
        $(document).ready(function () {
            function loadData() {
                $.ajax({
                    url: 'editar-cliente-root.php?action=fetch_data',
                    type: 'GET',
                    dataType: 'json',
                    success: function (data) {
                        if (data.length > 0) {
                            var user = data[0];
                            $('#nomeCliente').val(user.nomeCliente);
                            $('#nascCliente').val(user.nascCliente);
                            $('#cpfCliente').val(user.cpfCliente);
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        console.log('Erro: ' + textStatus + ' - ' + errorThrown);
                    }
                });
            }

            loadData();
        });
    </script>
</body>

</html>