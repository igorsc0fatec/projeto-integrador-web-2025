<?php
require_once '../controller/controller-usuario.php';
$usuarioController = new ControllerUsuario();

$numProdutosCarrinho = 0;
if (isset($_SESSION['carrinho'])) {
    $numProdutosCarrinho = count($_SESSION['carrinho']);
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

// Adicione isso junto com os outros actions no topo do arquivo
if (isset($_GET['action']) && $_GET['action'] == 'atualizar_senha') {
    if (isset($_POST['nova_senha'], $_POST['idUsuario'])) {
        $novaSenha = $_POST['nova_senha'];
        $idUsuario = $_POST['idUsuario'];
        
        if ($usuarioController->updateSenha($novaSenha, $idUsuario)) {
            echo 'success';
        } else {
            echo 'error';
        }
        exit;
    }
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
    <title>Recuperar Senha</title>
</head>

<body>
    <div class="container">
        <header>
            <nav>
                <div class="nav-container">
                    <a href="../view/index.php">
                        <img id="logo" src="../assets/img-site/logo.png" alt="JobFinder">
                    </a>
                    <i class="fas fa-bars btn-menumobile"></i>
                    <ul class="nav-links">
                        <li style="position: relative;">
                            <a href="../view-cliente/carrinho.php">
                                <i class="fa fa-shopping-cart" style="font-size:20px"></i>
                                <?php if ($numProdutosCarrinho > 0): ?>
                                    <span class="cart-counter"><?php echo $numProdutosCarrinho; ?></span>
                                <?php endif; ?>
                            </a>
                        </li>
                        <li><a href="../view-cliente/login-cliente.php">Sou Cliente</a></li>
                        <li><a href="../view-confeitaria/login-confeitaria.php">Sou Confeitaria</a></li>
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
            <img src="../assets/img-site/cadeado.png" alt="">
        </div>
        <div class="form">
            <form id="form-recuperar-senha" method="post">
                <div class="form-header">
                    <div class="title">
                        <h1>Recuperar Senha</h1>
                    </div>
                </div>

                <div class="input-box">
                    <label for="email">Email*</label>
                    <div class="dados">
                        <input type="email" id="emailUsuario" name="emailUsuario" required>
                    </div>
                </div>

                <div class="continue-button">
                    <button type="submit" id="enviar" name="enviar">Enviar</button>
                </div>
            </form>
        </div>
    </div>

    <?php
    function gerarScriptSenha($idUsuario)
    {

        return "
<script>
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
                    url: 'recuperar-senha.php?action=validar_codigo',
                    method: 'POST',
                    data: {
                        codigoDigitado: codigoDigitado,
                        idUsuario: " . $idUsuario . "
                    },
                    success: function(response) {
                        if (response === 'valido') {
                        // Código válido: mostra os campos de senha
                            Swal.fire({
                                title: 'Código válido!',
                                text: 'Agora defina sua nova senha.',
                                icon: 'success',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                    // Oculta o campo de email e botão Enviar
                                            $('#emailUsuario').closest('.input-box').hide();
                                            $('#enviar').hide();

                                    // Adiciona os campos de nova senha dinamicamente
                                            const form = $('#form-recuperar-senha');
                                            form.append(`
                                                <div class='input-box'>
                                                    <label for='nova_senha'>Nova Senha*</label>
                                                    <div class='dados'>
                                                        <input type='password' id='nova_senha' name='nova_senha' required>
                                                    </div>
                                                </div>
                                                <div class='input-box'>
                                                    <label for='confirmar_senha'>Confirmar Senha*</label>
                                                    <div class='dados'>
                                                        <input type='password' id='confirmar_senha' name='confirmar_senha' required>
                                                    </div>
                                                </div>
                                                    <input type='hidden' name='idUsuario' value='${idUsuario}'>
                                                <div class='continue-button'>
                                                    <button type='button' id='salvar-senha'>Salvar</button>
                                                </div>
                                        `);

                                            // Adiciona o evento para salvar a senha
                                                $('#salvar-senha').click(function() {
                                                    const novaSenha = $('#nova_senha').val();
                                                    const confirmarSenha = $('#confirmar_senha').val();

                                                    if (novaSenha !== confirmarSenha) {
                                                        Swal.fire('Erro', 'As senhas não coincidem!', 'error');
                                                        return;
                                                    }

                                                    // Envia a nova senha via AJAX
                                                    $.ajax({
                                                        url: 'recuperar-senha.php?action=atualizar_senha',
                                                        method: 'POST',
                                                    data: {
                                                        nova_senha: novaSenha,
                                                        idUsuario: " . $idUsuario . "
                                                    },
                                                    success: function(response) {
                                                    if (response === 'success') {
                                                        Swal.fire({
                                                            title: 'Sucesso!',
                                                            text: 'Senha alterada com sucesso.',
                                                            icon: 'success'
                                                        }).then(() => {
                                                            window.location.href = 'index.php';
                                                        });
                                                    } else {
                                                        Swal.fire('Erro', 'Erro ao atualizar a senha.', 'error');
                                                    }
                                                }
                                            });
                                        });
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
    ?>

    <?php
    if (isset($_POST['enviar'])) {
        $email = $_POST['emailUsuario'];
        $resultado = $usuarioController->identificaEmail($email);

        if ($resultado && isset($resultado[0]['id_usuario'])) {
            $idUsuario = $resultado[0]['id_usuario'];
            $codigo = $usuarioController->gerarCodigo($idUsuario);
            $usuarioController->enviaEmail($email, $codigo);
            echo gerarScriptSenha($idUsuario);
        } else {
            echo "
        <script>
            Swal.fire({
                title: 'Erro!',
                text: 'E-mail não encontrado.',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        </script>";
        }
    }
    ?>

</body>

</html>