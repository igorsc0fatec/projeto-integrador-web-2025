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

include_once '../controller/controller-cliente.php';
$clienteController = new controllerCliente();
$enderecos = $clienteController->viewEndereco($_SESSION['idCliente']);

if (isset($_GET['action']) && $_GET['action'] == 'fetch_data') {
    header('Content-Type: application/json');
    echo json_encode($enderecos);
    exit;
}

if (isset($_GET['id'])) {
    header('Content-Type: application/json');
    echo json_encode($clienteController->deleteEndereco($_GET['id']));
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
    <link rel="stylesheet" href="../assets/css/style-menu.css">
    <link rel="stylesheet" href="../assets/css/style-form-table.css">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"
        integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://unpkg.com/scrollreveal"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Endereços</title>

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
            <form id="form" method="post" onsubmit="return validaEndereco()">
                <div class="form-header">
                    <div class="title">
                        <h1>Endereços</h1>
                    </div>
                </div>
                <div class="input-group">
                    <div class="input-box">
                        <label for="CEP">CEP*</label>
                        <input id="cep" type="text" name="cep" placeholder="Digite seu cep" required>
                        <span id="erroCep1" class="error"></span>
                    </div>

                    <div class="input-box">
                        <label for="Logradouro">Logradouro</label>
                        <input id="logradouro" type="text" name="logradouro" readonly>
                    </div>

                    <div class="input-box">
                        <label for="nunlocal">Nº do Local*</label>
                        <input id="numLocal" type="text" name="numLocal" placeholder="Digite o Nº" required>
                    </div>

                    <div class="input-box">
                        <label for="bairro">Bairro</label>
                        <input id="bairro" type="text" name="bairro" readonly>
                    </div>

                    <div class="input-box">
                        <label for="cidade">Cidade</label>
                        <input id="cidade" type="text" name="cidade" readonly>
                    </div>

                    <div class="input-box">
                        <label for="uf">UF</label>
                        <input id="uf" type="text" name="uf" readonly>
                    </div>
                </div>

                <input id="latitude" type="hidden" name="latitude">
                    <input id="longitude" type="hidden" name="longitude">
                <div class="continue-button">
                    <button type="submit" id="submit" name="submit">Cadastrar</button>
                </div>
            </form>
            <script src="assets/js/valida-endereco-cliente.js"></script>
            <script src="assets/js/valida-enviar.js"></script>
        </div>
    </div>

    <?php
    if (isset($_POST['submit'])) {
        if ($clienteController->verificaEndereco()) {
            echo "
                <script>
                    Swal.fire({
                    title: 'Erro ao cadastrar endereço!',
                    text: 'Esse endereço ja esta cadastrado!',
                    icon: 'error',
                    confirmButtonText: 'OK'
                    });
                </script>";
        } else if ($clienteController->countEndereco()) {
            echo "
                <script>
                    Swal.fire({
                    title: 'Maximo de Endereços atingido!',
                    text: 'Você pode cadastrar no maximo 3 Endereços',
                    icon: 'info',
                    confirmButtonText: 'OK'
                    });
                </script>";
        } else {
            if ($clienteController->addEndereco($_SESSION['idCliente'])) {
                echo "
                    <script>
                        Swal.fire({
                        title: 'Endereço cadastrado com sucesso!',
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

        <h2>Seus Endereços</h2>
        <table id="minhaTabela">
            <thead>
                <tr>
                    <th>CEP</th>
                    <th>Logradouro</th>
                    <th>Nº</th>
                    <th>Bairro</th>
                    <th>Cidade</th>
                    <th>UF</th>
                    <th>Excluir</th>
                    <th>Editar</th>
                </tr>
            </thead>
            <tbody id="enderecos">
            </tbody>
        </table>
    </div>

    <script>
        $(document).ready(function () {
            function loadData() {
                $.ajax({
                    url: 'cadastrar-endereco.php?action=fetch_data',
                    type: 'GET',
                    dataType: 'json',
                    success: function (data) {
                        var tableBody = $('#enderecos');
                        if (data.length === 0) {
                            tableBody.append('<tr><td colspan="3">Você não tem nenhum endereço no momento</td></tr>');
                        } else {
                            data.forEach(function (endereco) {
                                var row = $('<tr></tr>');
                                row.append('<td>' + endereco.cep_cliente + '</td>');
                                row.append('<td>' + endereco.log_cliente + '</td>');
                                row.append('<td>' + endereco.num_local + '</td>');
                                row.append('<td>' + endereco.bairro_cliente + '</td>');
                                row.append('<td>' + endereco.cidade_cliente + '</td>');
                                row.append('<td>' + endereco.uf_cliente + '</td>');
                                row.append('<td><button onclick="confirmarExclusao(' + endereco.id_endereco_cliente + ')">Excluir</button></td>');
                                row.append('<td><button onclick="confirmarEdicao(' + endereco.id_endereco_cliente + ',\'' + endereco.cep_cliente + '\',\'' + endereco.log_cliente + '\',\'' + endereco.num_local +
                                    '\',\'' + endereco.bairro_cliente + '\',\'' + endereco.cidade_cliente + '\',\'' + endereco.uf_cliente + '\')">Editar</button></td>');
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

        function confirmarExclusao(idEnderecoCliente) {
            Swal.fire({
                title: 'Tem certeza que deseja excluir?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sim, excluir!',
                cancelButtonText: 'Não, cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: 'cadastrar-endereco.php?id=' + idEnderecoCliente,
                        type: 'POST',
                        data: { idEnderecoCliente: idEnderecoCliente },
                        success: function (response) {
                            Swal.fire(
                                'Excluído!',
                                'O endereço foi excluído.',
                                'success',
                            ).then(() => {
                                window.location.href = 'cadastrar-endereco.php';
                            });
                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            Swal.fire(
                                'Erro!',
                                'Ocorreu um erro ao excluir o endereco.',
                                'error'
                            );
                        }
                    });
                }
            });
        }

        function confirmarEdicao(idEnderecoCliente, cepCliente, logCliente, numLocal, bairroCliente, cidadeCliente, ufCliente) {
            Swal.fire({
                title: 'Tem certeza que deseja editar?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sim, editar!',
                cancelButtonText: 'Não, cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    const url = `editar-endereco.php?id=${idEnderecoCliente}&cep=${cepCliente}&log=${logCliente}&num=${numLocal}&bairro=${bairroCliente}&cidade=${cidadeCliente}&uf=${ufCliente}`;
                    window.location.href = url;
                }
            });
        }
    </script>

</body>

</html>