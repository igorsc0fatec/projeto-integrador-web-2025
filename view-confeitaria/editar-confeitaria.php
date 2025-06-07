<?php
session_start();
if (!isset($_SESSION['idConfeitaria'])) {
    session_destroy();
    echo "<script language='javascript' type='text/javascript'> window.location.href='login-confeitaria.php'</script>";
} else if (isset($_SESSION['idTipoUsuario'])) {
    if ($_SESSION['idTipoUsuario'] != 3) {
        echo "<script language='javascript' type='text/javascript'> window.location.href='login-confeitaria.php'</script>";
    }
}

include_once '../controller/controller-confeitaria.php';
$confeitariaController = new ControllerConfeitaria();
$confeitaria = $confeitariaController->viewConfeitaria($_SESSION['idConfeitaria']);

if (isset($_GET['action']) && $_GET['action'] == 'fetch_data') {
    header('Content-Type: application/json');
    echo json_encode($confeitaria);
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"
        integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://unpkg.com/scrollreveal"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
    <title>Dados da Confeitaria</title>

</head>

<body>
    <div class="container">
        <header>
            <nav>
                <div class="nav-container">
                    <a href="dashboard.php">
                        <img id="logo" src="../assets/img-site/logo.png" alt="JobFinder">
                    </a>
                    <div class="greeting">
                        <?php echo $_SESSION['nome']; ?>
                    </div>
                    <i class="fas fa-bars btn-menumobile"></i>
                    <ul>
                        <li><a href="editar-confeitaria.php">Dados da Confeitaria</a></li>
                        <li><a href="cadastrar-telefone-confeitaria.php">Telefone</a></li>
                        <li><a href="editar-usuario.php">Senha</a></li>
                        <li><a href="#" onclick="confirmarDesativarConta()">Desativar Conta</a></li>
                        <li><a href="dashboard.php">Voltar </a></li>
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
            <img id="preview" src="../assets/img-site/empresa 2.png" alt="Imagem selecionada">
        </div>
        <div class="form">
            <form id="form" method="post" enctype="multipart/form-data" onsubmit="return validaEndereco()">
                <div class="form-header">
                    <div class="title">
                        <h1>Dados da Confeitaria</h1>
                    </div>
                </div>
                <div class="input-group">

                    <div class="input-box">
                        <label for="nome">Nome da Confeitaria*</label>
                        <input type="text" id="nomeConfeitaria" name="nomeConfeitaria" required>
                    </div>

                    <div class="input-box">
                        <label for="cnpj">CNPJ</label>
                        <input type="text" id="cnpjConfeitaria" name="cnpjConfeitaria" readonly>
                    </div>

                    <div class="input-box">
                        <label>Inicio*</label>
                        <input type="time" id="hora_entrada" name="hora_entrada" required>
                    </div>

                    <div class="input-box">
                        <label>Termino*</label>
                        <input type="time" id="hora_saida" name="hora_saida" required>
                    </div>

                    <div class="input-box">
                        <label>CEP*</label>
                        <input type="text" id="cep" name="cep" required>
                        <span id="erroCep1" class="error"></span>
                    </div>

                    <div class="input-box">
                        <label>Nº do local*</label>
                        <input type="text" id="numLocal" name="numLocal" required>
                    </div>

                    <div class="input-box">
                        <label>Logradouro</label>
                        <input type="text" id="logradouro" name="logradouro" readonly>
                    </div>

                    <div class="input-box">
                        <label>Bairro</label>
                        <input type="text" id="bairro" name="bairro" readonly>
                    </div>

                    <div class="input-box">
                        <label>Cidade</label>
                        <input type="text" id="cidade" name="cidade" readonly>
                    </div>

                    <div class="input-box">
                        <label for="tipo">UF</label>
                        <input type="text" id="uf" name="uf" readonly>
                    </div>

                    <div class="input-box">
                        <label for="img" class="upload-button">Escolha uma imagem</label>
                        <input id="img" type="file" accept="image/*" name="img" required onchange="previewImage()">
                        <p>*A imagem deve conter no máximo 16mb</p>
                        <span id="erroImagem" class="error"></span>
                    </div>
                    <input id="latitude" type="hidden" name="latitude">
                    <input id="longitude" type="hidden" name="longitude">
                </div>
                <div class="continue-button">
                    <button type="submit" id="submit" name="submit">Editar</button>
                </div>
            </form>

            <?php
            if (isset($_POST['submit'])) {
                if ($confeitariaController->updateConfeitaria()) {
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

        </div>
    </div>

    <script src="js/valida-edit-confeitaria.js"></script>
    <script src="js/valida-enviar.js"></script>
    <script>
        $(document).ready(function () {
            function loadData() {
                $.ajax({
                    url: 'editar-confeitaria.php?action=fetch_data',
                    type: 'GET',
                    dataType: 'json',
                    success: function (data) {
                        if (data.length > 0) {
                            var user = data[0];
                            $('#nomeConfeitaria').val(user.nome_confeitaria);
                            $('#cnpjConfeitaria').val(user.cnpj_confeitaria);
                            $('#cep').val(user.cep_confeitaria);
                            $('#hora_entrada').val(user.hora_entrada);
                            $('#hora_saida').val(user.hora_saida);
                            $('#logradouro').val(user.log_confeitaria);
                            $('#numLocal').val(user.num_local);
                            $('#bairro').val(user.bairro_confeitaria);
                            $('#cidade').val(user.cidade_confeitaria);
                            $('#uf').val(user.uf_confeitaria);
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