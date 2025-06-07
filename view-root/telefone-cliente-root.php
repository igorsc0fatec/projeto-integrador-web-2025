<?php
session_start();
if (!isset($_SESSION['idCliente'])) {
    echo "<script language='javascript' type='text/javascript'> window.location.href='clientes-root.php'</script>";
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
            <form id="form" method="post" onsubmit="return validaTelefone()">
                <div class="form-header">
                    <div class="title">
                        <h1>Telefone</h1>
                    </div>
                </div>
                <div class="input-box">
                    <div class="input-box">
                        <label for="telefone">Telefone*</label>
                        <input id="telefone" type="text" name="telefone" placeholder="Digite seu telefone" required>
                    </div>

                    <div class="input-box">
                        <label for="telefone">Tipo do Telefone*</label>
                        <input id="tipoTelefone" type="text" name="tipoTelefone" placeholder="Digite o tipo do telefone"
                            required>
                    </div>

                </div>
                <div class="continue-button">
                    <button type="submit" id="submit" name="submit">Cadastrar</button>
                </div>
            </form>
        </div>
    </div>

    <?php
    include_once '../controller/controller-cliente.php';
    $clienteController = new ControllerCliente();

    if (isset($_POST['submit'])) {
        if ($clienteController->verificaTelefone()) {
            echo "
    <script>
        Swal.fire({
            title: 'Erro ao cadastrar telefone!',
            text: 'Esse telefone ja existe',
            icon: 'error',
            confirmButtonText: 'OK'
        });
    </script>";
        } else if (!$clienteController->verificaDDD()) {
            echo "
    <script>
        Swal.fire({
            title: 'Erro ao cadastrar telefone!',
            text: 'Esse DDD não existe',
            icon: 'error',
            confirmButtonText: 'OK'
        });
    </script>";
        } else {
            if ($clienteController->addTelefone()) {
                echo "
    <script>
        Swal.fire({
            title: 'Cadastrado com sucesso!',
            icon: 'success',
            confirmButtonText: 'OK'
        });
    </script>";
            }
        }
    }
    $telefones = $clienteController->viewTelefone();

    include_once '../view/delete.php';
    $delete = new Delete();
    $deleteTelefone = $delete->deletarTelCliente();
    ?>

    <div>
        <br><br>
    </div>

    <DIV>

        <h2>Seus Telefones</h2>
        <table id="minhaTabela">
            <thead>
                <tr>
                    <th>Telefone</th>
                    <th>Tipo</th>
                    <th>Excluir</th>
                </tr>
            </thead>
            <tbody id="telefones">
                <?php if (empty($telefones)): ?>
                    <tr>
                        <td colspan="5">Não existe nenhum cadastro no momento.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($telefones as $t): ?>
                        <tr>
                            <td>
                                <?php echo $t['numTelCliente']; ?>
                            </td>
                            <td>
                                <?php echo $t['tipoTelefone']; ?>
                            </td>
                            <td><?php echo "<a href=\"$deleteTelefone?id={$t['idTelCliente']}\" onClick=\"return confirm('Tem certeza que deseja excluir?' )\">Excluir</a>" ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <script src="assets/js/valida-telefone.js"></script>
    </DIV>

    <footer class="rodape">
        <p>© 2024 | Todos os direitos são de propriedade da FoxBitSystem</p>
    </footer>

</body>

</html>