<?php
session_start();
if (!isset($_SESSION['emailUsuario'])) {
    session_destroy();
    echo "<script language='javascript' type='text/javascript'> window.location.href='../view/index.php'</script>";
}

include_once '../controller/controller-usuario.php';
$usuario = new ControllerUsuario();

$_SESSION['idTipoUsuario'] = "1";
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
    <link rel="stylesheet" href="assets/css/style_card.css">

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
                    <a href="dashboard-root.php">
                        <img id="logo" src="assets/img/logo.png" alt="JobFinder">
                    </a>
                    <i class="fas fa-bars btn-menumobile"></i>
                    <ul>
                        <li><a href="Clientes-root.php">Clientes</a></li>
                        <li><a href="Confeitarias-root.php">Confeitarias</a></li>
                        <li><a href="cadastrar-root.php">Novo Adm</a></li>
                    </ul>
                </div>
            </nav>
        </header>
    </div>

    <div class="ag-format-container">

        <div>
            <div class="ag-courses_box">
                <div class="ag-courses_item">
                    <a href="editar-cliente-root.php" class="ag-courses-item_link">
                        <div class="ag-courses-item_bg"></div>
                        <div class="ag-courses-item_title">
                            Dados Pessoais
                        </div>
                    </a>
                </div>

                <div class="ag-courses_item">
                    <a href="telefone-cliente-root.php" class="ag-courses-item_link">
                        <div class="ag-courses-item_bg"></div>
                        <div class="ag-courses-item_title">
                            Telefones
                        </div>
                    </a>
                </div>

                <div class="ag-courses_item">
                    <a href="enderecos-root.php" class="ag-courses-item_link">
                        <div class="ag-courses-item_bg"></div>
                        <div class="ag-courses-item_title">
                            Endereços
                        </div>
                    </a>
                </div>

            </div>
        </div>

        <form method="post">
            <div class="pesquisa">
                <label for="pesq">Id Usuario</label>
                <div class="input-wrapper">
                    <input id="pesq" type="text" name="pesq" placeholder="Digite o id do usuario aqui:" required>
                    <button type="submit" id="usuario" name="usuario">Buscar</button>
                </div>
            </div>
        </form>

        <?php
        if (isset($_POST['usuario'])) {
            if($usuario->buscaUsuario()){
                echo "
                <script>
                    Swal.fire({
                    title: 'Usuario armazenado com sucesso!',
                    icon: 'success',
                    confirmButtonText: 'OK'
                    });
                </script>";
            } else {
                echo "
                <script>
                    Swal.fire({
                    title: 'Erro ao armazenar o usuario!',
                    text: 'Usuario não encontrado!',
                    icon: 'error',
                    confirmButtonText: 'OK'
                    });
                </script>";
            }
        }
        ?>

        <br><br><br><br>

        <footer class="rodape">
            <p>© 2024 | Todos os direitos são de propriedade da FoxBitSystem</p>
        </footer>

</body>

</html>