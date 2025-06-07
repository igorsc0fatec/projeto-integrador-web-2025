<?php
session_start();
if (!isset($_SESSION['idCliente'])) {
    session_destroy();
    echo "<script language='javascript' type='text/javascript'> window.location.href='login-cliente.php'</script>";
} else if (isset($_SESSION['idTipoUsuario'])) {
    if ($_SESSION['idTipoUsuario'] != 2) {
        echo "<script language='javascript' type='text/javascript'> window.location.href='login-cliente.php'</script>";
    }
}

require_once '../controller/controller-config.php';
$configController = new ControllerConfig();
$telefones = $configController->telefone->viewTelefone($_SESSION['idUsuario']);
$tipoTelefone = $configController->telefone->viewTipoTelefone();

if (isset($_GET['action']) && $_GET['action'] == 'fetch_data') {
    header('Content-Type: application/json');
    echo json_encode($telefones);
    exit;
}

if (isset($_GET['id'])) {
    header('Content-Type: application/json');
    echo json_encode($configController->telefone->deleteTelefone($_GET['id']));
    exit;
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
    <script src="https://unpkg.com/scrollreveal"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
    <title>Telefones</title>

</head>

<body>
    <div class="container">
        <header>
            <nav>
                <div class="nav-container">
                    <a href="../view/index.php">
                        <img id="logo" src="../assets/img-site/logo.png" alt="JobFinder">
                    </a>
                    <div class="greeting">
                        <?php echo $_SESSION['nome']; ?>
                    </div>
                    <i class="fas fa-bars btn-menumobile"></i>
                    <ul>
                        <li><a href="editar-cliente.php">Dados Pessoais</a></li>
                        <li><a href="cadastrar-telefone-cliente.php">Telefone</a></li>
                        <li><a href="cadastrar-endereco.php">Endereço</a></li>
                        <li><a href="#" onclick="confirmarDesativarConta()">Desativar Conta</a></li>
                        <li><a href="../view/index.php">Voltar </a></li>
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
            <img src="../assets/img-site/pessoa.webp" alt="">
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
                        <select id="tipoTelefone" name="tipoTelefone">
                            <?php foreach ($tipoTelefone as $tipo) { ?>
                                <option value="<?php echo $tipo['id_tipo_telefone']; ?>">
                                    <?php echo $tipo['tipo_telefone'] ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                </div>

                <div class="continue-button">
                    <button type="submit" id="submit" name="submit">Cadastrar</button>
                </div>
            </form>
        </div>
    </div>

    <?php
    if (isset($_POST['submit'])) {
        if ($configController->telefone->verificaTelefone()) {
            echo "
                <script>
                    Swal.fire({
                    title: 'Erro ao cadastrar telefone!',
                    text: 'Esse telefone ja existe',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
                </script>";
        } else if (!$configController->telefone->verificaDDD()) {
            echo "
                <script>
                    Swal.fire({
                    title: 'Erro ao cadastrar telefone!',
                    text: 'Esse DDD não existe',
                    icon: 'error',
                    confirmButtonText: 'OK'
                    });
                </script>";
        } else if ($configController->telefone->countTelefone($_SESSION['idUsuario'])) {
            echo "
                <script>
                    Swal.fire({
                    title: 'Maximo de Telefones atingido!',
                    text: 'Você pode cadastrar no maximo 3 Telefones',
                    icon: 'info',
                    confirmButtonText: 'OK'
                    });
                </script>";
        } else {
            if ($configController->telefone->addTelefone($_SESSION['idUsuario'])) {
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
    ?>

    <div>
        <br><br>
    </div>

    <div>
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
            </tbody>
        </table>

        <script src="js/valida-telefone.js"></script>
        <script src="js/valida-enviar.js"></script>

        <script>
            $(document).ready(function () {
                function loadData() {
                    $.ajax({
                        url: 'cadastrar-telefone-cliente.php?action=fetch_data',
                        type: 'GET',
                        dataType: 'json',
                        success: function (data) {
                            var tableBody = $('#telefones');
                            if (data.length === 0) {
                                tableBody.append('<tr><td colspan="3">Você não tem nenhum telefone no momento</td></tr>');
                            } else {
                                data.forEach(function (telefone) {
                                    var row = $('<tr></tr>');
                                    row.append('<td>' + telefone.num_telefone + '</td>');
                                    row.append('<td>' + telefone.tipo_telefone + '</td>');
                                    row.append('<td><button onclick="confirmarExclusao(' + telefone.id_telefone + ')">Excluir</button></td>');
                                    tableBody.append(row);
                                });
                            }
                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            console.log('Erro: ' + textStatus + ' - ' + errorThrown);
                        }
                    });
                }

                loadData();
            });

            function confirmarExclusao(idTelefone) {
                Swal.fire({
                    title: 'Tem certeza que deseja excluir?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sim, excluir!',
                    cancelButtonText: 'Não, cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: 'cadastrar-telefone-cliente.php?id=' + idTelefone,
                            type: 'POST',
                            data: { idTelefone: idTelefone },
                            success: function (response) {
                                Swal.fire(
                                    'Excluído!',
                                    'O telefone foi excluído.',
                                    'success',
                                ).then(() => {
                                    window.location.href = 'cadastrar-telefone-cliente.php';
                                });
                            },
                            error: function (jqXHR, textStatus, errorThrown) {
                                Swal.fire(
                                    'Erro!',
                                    'Ocorreu um erro ao excluir o telefone.',
                                    'error'
                                );
                            }
                        });
                    }
                });
            }
        </script>
    </div>

</body>

</html>