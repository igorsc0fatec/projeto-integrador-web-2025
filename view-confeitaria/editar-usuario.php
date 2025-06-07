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

require_once '../controller/controller-usuario.php';
$usuarioController = new ControllerUsuario();
$usuario = $usuarioController->viewUsuario($_SESSION['idUsuario']);

if (isset($_GET['action']) && $_GET['action'] == 'fetch_data') {
    header('Content-Type: application/json');
    echo json_encode($usuario);
    exit;
}

if (isset($_GET['action']) && $_GET['action'] == 'update') {
    // Verifica se o e-mail foi passado corretamente pela requisição GET
    if (isset($_GET['emailUsuario'])) {
        $emailUsuario = $_GET['emailUsuario'];

        // Verifique se o e-mail foi passado e valide-o
        if ($usuarioController->updateEmail($emailUsuario)) {
            $response = 'ok';
        } else {
            $response = 'erro';
        }
    } else {
        $response = 'erro';
    }

    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

if (isset($_GET['action']) && $_GET['action'] == 'validar_codigo') {
    // Verifica se o código e o ID do usuário foram passados corretamente
    if (isset($_POST['codigoDigitado']) && isset($_POST['idUsuario'])) {
        $codigoDigitado = $_POST['codigoDigitado'];
        $idUsuario = $_POST['idUsuario'];

        // Chama o método validarCodigo do ControllerUsuario
        if ($usuarioController->validarCodigo($codigoDigitado, $idUsuario)) {
            $response = 'valido';
        } else {
            $response = 'invalido';
        }
    } else {
        $response = 'invalido';
    }

    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

if (isset($_GET['action']) && $_GET['action'] == 'update_senha') {
    if (isset($_POST['senhaUsuario'])) {
        $senhaUsuario = $_POST['senhaUsuario'];

        // Verifica se a senha foi passada e atualiza no banco de dados
        if ($usuarioController->updateSenha($senhaUsuario)) {
            $response = 'ok';
        } else {
            $response = 'erro';
        }
    } else {
        $response = 'erro';
    }

    header('Content-Type: application/json');
    echo json_encode($response);
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dados de Usuario</title>

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
            <img src="../assets/img-site/pessoa.webp" alt="">
        </div>
        <div class="form">

            <!-- Formulário de alteração de email -->
            <form id="form-email" method="post">
                <div class="form-header">
                    <div class="title">
                        <h1>Dados Pessoais</h1>
                    </div>
                </div>

                <div class="input-box">
                    <label for="email">Email*</label>
                </div>
                <div class="dados">
                    <input type="email" id="emailUsuario" name="emailUsuario" required>
                    <button type="submit" id="alterar-email" name="alterar-email">Alterar</button>
                </div>
            </form>

            <!-- Formulário de alteração de senha -->
            <form id="form-senha" method="post">
                <div class="input-box">
                    <label for="senha">Senha*</label>
                </div>
                <div class="dados">
                    <input type="password" id="senhaUsuario" name="senhaUsuario" placeholder="Digite sua senha"
                        minlength="8" maxlength="15" required>
                </div>

                <div class="input-box">
                    <label for="confirmaSenha">Confirma Senha*</label>
                </div>
                <div class="dados">
                    <input type="password" id="confirmaSenha" name="confirmaSenha"
                        placeholder="Digite sua senha novamente" minlength="8" maxlength="15" required>
                    <span id="erroSenha1" class="error"></span>
                    <button type="submit" id="alterar-senha" name="alterar-senha">Alterar</button>
                </div>
            </form>
        </div>
    </div>

    <?php

    function gerarScriptEmail($emailUsuario)
    {
        $emailUsuarioJson = json_encode($emailUsuario);

        return "
        <script>
            const emailUsuario = $emailUsuarioJson;

            function verificarCodigo() {
                Swal.fire({
                    title: 'Digite o código enviado para o seu e-mail',
                    input: 'text',
                    showCancelButton: true,
                    confirmButtonText: 'Verificar',
                    cancelButtonText: 'Cancelar',
                    inputValidator: (value) => {
                        if (!value) {
                            return 'Você deve digitar um código!';
                        }
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        const codigoDigitado = result.value;

                    // Faz a requisição AJAX para validar o código
                        $.ajax({
                            url: 'editar-usuario.php?action=validar_codigo',  // Ação para validar o código
                            method: 'POST',
                            data: {
                                codigoDigitado: codigoDigitado,
                                idUsuario: " . $_SESSION['idUsuario'] . "
                            },
                            success: function(response) {
                                if (response === 'valido') {
                                    console.log('Resposta do servidor:', response); // Log da resposta para debug
                                // Código válido, prossegue com a atualização do e-mail
                                    $.ajax({
                                        url: 'editar-usuario.php?action=update',
                                        method: 'GET',
                                        data: {
                                            updateUsuario: true,
                                            emailUsuario: emailUsuario
                                    },
                                    success: function(response) {
                                        if (response === 'ok') {
                                            Swal.fire({
                                                title: 'Dados alterados com sucesso!',
                                                icon: 'success',
                                                confirmButtonText: 'OK'
                                            }).then(() => {
                                                loadData();  // Atualiza os dados na página
                                            });
                                        } else {
                                            Swal.fire('DEU RUIM', 'Ocorreu um erro ao tentar atualizar os dados.', 'error');
                                        }
                                    },
                                    error: function() {
                                        Swal.fire('DEU RUIM', 'Erro na requisição. Tente novamente.', 'error');
                                    }
                                });
                            } else {
                                // Código inválido ou expirado
                                Swal.fire('DEU RUIM', 'Código inválido ou expirado. Tente novamente.', 'error').then(() => {
                                    verificarCodigo();  // Reexibe o prompt
                                });
                            }
                        },
                        error: function() {
                            Swal.fire('DEU RUIM', 'Erro na requisição. Tente novamente.', 'error');
                        }
                    });
                }
            });
        }

        // Inicia a verificação do código
        verificarCodigo();
    </script>";
    }

    function gerarScriptSenha($senhaUsuario)
    {
        $senhaUsuarioJson = json_encode($senhaUsuario);

        return "
    <script>
        const senhaUsuario = $senhaUsuarioJson;

        function verificarCodigoSenha() {
            Swal.fire({
                title: 'Digite o código enviado para o seu e-mail',
                input: 'text',
                showCancelButton: true,
                confirmButtonText: 'Verificar',
                cancelButtonText: 'Cancelar',
                inputValidator: (value) => {
                    if (!value) {
                        return 'Você deve digitar um código!';
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const codigoDigitado = result.value;

                    // Faz a requisição AJAX para validar o código
                    $.ajax({
                        url: 'editar-usuario.php?action=validar_codigo',
                        method: 'POST',
                        data: {
                            codigoDigitado: codigoDigitado,
                            idUsuario: " . $_SESSION['idUsuario'] . "
                        },
                        success: function(response) {
                            if (response === 'valido') {
                                // Código válido, prossegue com a atualização da senha
                                $.ajax({
                                    url: 'editar-usuario.php?action=update_senha',
                                    method: 'POST',
                                    data: {
                                        senhaUsuario: senhaUsuario
                                    },
                                    success: function(response) {
                                        if (response === 'ok') {
                                            Swal.fire({
                                                title: 'Senha alterada com sucesso!',
                                                icon: 'success',
                                                confirmButtonText: 'OK'
                                            }).then(() => {
                                                loadData();
                                            });
                                        } else {
                                            Swal.fire('Erro', 'Ocorreu um erro ao tentar atualizar a senha.', 'error');
                                        }
                                    },
                                    error: function() {
                                        Swal.fire('Erro', 'Erro na requisição. Tente novamente.', 'error');
                                    }
                                });
                            } else {
                                // Código inválido ou expirado
                                Swal.fire('Erro', 'Código inválido ou expirado. Tente novamente.', 'error').then(() => {
                                    verificarCodigoSenha();  // Reexibe o prompt
                                });
                            }
                        },
                        error: function() {
                            Swal.fire('Erro', 'Erro na requisição. Tente novamente.', 'error');
                        }
                    });
                }
            });
        }

        // Inicia a verificação do código
        verificarCodigoSenha();
    </script>";
    }

    if (isset($_POST['alterar-email'])) {
        if ($usuarioController->verificaEmail(true, $_SESSION['idUsuario'])) {
            echo "
                <script>
                    Swal.fire({
                        title: 'Erro ao cadastrar email!',
                        text: 'Esse email já existe!',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                </script>";
        } else {
            $codigo = $usuarioController->gerarCodigo();
            $usuarioController->enviaEmail($_POST['emailUsuario'], $codigo);
            echo gerarScriptEmail($_POST['emailUsuario']);
        }
    }

    if (isset($_POST['alterar-senha'])) {
        $novaSenha = $_POST['senhaUsuario']; // Captura a nova senha do formulário
    
        // Verifica se a senha e a confirmação de senha são iguais
        if ($_POST['senhaUsuario'] === $_POST['confirmaSenha']) {
            $codigo = $usuarioController->gerarCodigo();
            $emailUsuario = $_SESSION['emailUsuario'];
            $usuarioController->enviaEmail($emailUsuario, $codigo);
            echo gerarScriptSenha($novaSenha);
        } else {
            echo "
        <script>
            Swal.fire({
                title: 'Erro ao alterar senha!',
                text: 'As senhas não coincidem!',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        </script>";
        }
    }
    ?>

    <script>
        $(document).ready(function () {
            function loadData() {
                $.ajax({
                    url: 'editar-usuario.php?action=fetch_data',
                    type: 'GET',
                    dataType: 'json',
                    success: function (data) {
                        if (data.length > 0) {
                            var user = data[0];
                            $('#emailUsuario').val(user.email_usuario);
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