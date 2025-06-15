<?php
require_once '../controller/controller-confeitaria.php';
require_once '../controller/controller-usuario.php';
$confeitariaController = new ControllerConfeitaria();
$usuarioController = new ControllerUsuario();

$numProdutosCarrinho = 0;
if (isset($_SESSION['carrinho'])) {
    $numProdutosCarrinho = count($_SESSION['carrinho']);
}

if (isset($_GET['action']) && $_GET['action'] == 'validaEmail') {
    if (isset($_POST['emailUsuario'])) {
        $emailUsuario = $_POST['emailUsuario'];

        // Verifica se o email foi passado e ativa a conta
        if ($usuarioController->validaEmail($emailUsuario)) {
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
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/style-menu.css">
    <link rel="stylesheet" href="../assets/css/style-form.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
    <title>Nova Confeitaria</title>
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

    <div class="container-form">
        <div class="form-image">
            <img id="preview" src="../assets/img-site/empresa 2.png" alt="Imagem selecionada">
        </div>
        <div class="form">
            <form id="form" method="post" enctype="multipart/form-data" onsubmit="return validaConfeitaria()">
                <div class="form-header">
                    <div class="title">
                        <h1>Insira os dados da sua confeitaria</h1>
                    </div>
                </div>

                <div class="input-group">
                    <div class="input-box">
                        <label for="nomeFantasia">Nome da Confeitaria</label>
                        <input id="nomeConfeitaria" type="text" name="nomeConfeitaria"
                            placeholder="Digite seu nome Fantasia" required>
                    </div>

                    <div class="input-box">
                        <label for="email">Email</label>
                        <input id="emailUsuario" type="email" name="emailUsuario" placeholder="Digite seu E-mail"
                            required>
                    </div>

                    <div class="input-box">
                        <label for="CNPJ">CNPJ</label>
                        <input id="cnpjConfeitaria" type="text" name="cnpjConfeitaria" placeholder="Digite seu CNPJ"
                            required>
                        <span id="erroCnpj" class="error"></span>
                    </div>

                    <div class="input-box">
                        <label for="CEP">CEP</label>
                        <input type="text" id="cep" name="cep" placeholder="Digite seu cep" required>
                        <span id="erroCep1" class="error"></span>
                    </div>

                    <div class="input-box">
                        <label for="Logradouro">Logradouro</label>
                        <input id="logradouro" type="text" name="logradouro" readonly>
                    </div>

                    <div class="input-box">
                        <label for="nunlocal">Nº do Local</label>
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

                    <div class="input-box">
                        <label for="hora">Inicio</label>
                        <input id="hora_entrada" type="time" name="hora_entrada" required>
                    </div>

                    <div class="input-box">
                        <label for="hora">Termino</label>
                        <input id="hora_saida" type="time" name="hora_saida" required>
                    </div>

                    <div class="input-box">
                        <label for="password">Senha</label>
                        <input id="senhaUsuario" type="password" name="senhaUsuario" placeholder="Digite sua senha"
                            minlength="8" maxlength="15" required>
                    </div>

                    <div class="input-box">
                        <label for="confirmPassword">Confirme sua Senha</label>
                        <input id="confirmaSenha" type="password" name="confirmaSenha"
                            placeholder="Digite sua senha novamente" minlength="8" maxlength="15" required>
                        <span id="erroSenha1" class="error"></span>
                    </div>

                    <div class="input-box">
                        <label for="img" class="upload-button">Escolha uma imagem</label>
                        <input id="img" type="file" accept="image/*" name="img" required onchange="previewImage()">
                        <span id="erroImagem" class="error"></span>
                    </div>
                </div>

                <input id="latitude" type="hidden" name="latitude">
                    <input id="longitude" type="hidden" name="longitude">
                <input type="hidden" id="msg" name="msg" value="3">
                <div class="continue-button">
                    <button type="submit" id="cadastrar" name="cadastrar">Cadastrar</button>
                </div>
            </form>
            <script src="js/valida-confeitaria.js"></script>
        </div>
    </div>

    <?php
    function gerarScriptConfeitaria($emailUsuario, $idUsuario)
    {
        // Converte o email do usuário para JSON
        $emailUsuarioJson = json_encode($emailUsuario);

        // Converte o ID do usuário para JSON
        $idUsuarioJson = json_encode($idUsuario);

        return "
        <script>
            const emailUsuario = $emailUsuarioJson;
            const idUsuario = $idUsuarioJson;
    
            function verificarCodigoConfeitaria() {
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
                            url: 'cadastrar-confeitaria.php?action=validar_codigo',  // Ação para validar o código
                            method: 'POST',
                            data: {
                                codigoDigitado: codigoDigitado,
                                idUsuario: idUsuario
                            },
                            success: function (response) {
                                if (response === 'valido') {
                                    // Código válido, prossegue com a ativação da conta
                                    $.ajax({
                                        url: 'cadastrar-confeitaria.php?action=validaEmail',
                                        method: 'POST',
                                        data: {
                                            emailUsuario: emailUsuario
                                        },
                                        success: function (response) {
                                            if (response === 'ok') {
                                                Swal.fire({
                                                    title: 'Email validado com sucesso!',
                                                    icon: 'success',
                                                    confirmButtonText: 'OK'
                                                }).then(() => {
                                                    window.location.href = 'login-confeitaria.php'; // Redireciona para a página de login
                                                });
                                            } else {
                                                Swal.fire('Erro', 'Ocorreu um erro ao tentar ativar a conta.', 'error');
                                            }
                                        },
                                        error: function () {
                                            Swal.fire('Erro', 'Erro na requisição. Tente novamente.', 'error');
                                        }
                                    });
                                } else {
                                    // Código inválido ou expirado
                                    Swal.fire('Erro', 'Código inválido ou expirado. Tente novamente.', 'error').then(() => {
                                        verificarCodigoConfeitaria();  // Reexibe o prompt
                                    });
                                }
                            },
                            error: function () {
                                Swal.fire('Erro', 'Erro na requisição. Tente novamente.', 'error');
                            }
                        });
                    }
                });
            }
            // Inicia a verificação do código
            verificarCodigoConfeitaria();
        </script>";
    }
    ?>

    <?php
    if (isset($_POST['cadastrar'])) {
        if ($confeitariaController->verificaCNPJ()) {
            echo "
                <script>
                    Swal.fire({
                    title: 'Erro ao cadastrar usuario!',
                    text: 'Esse CNPJ já esta em uso!',
                    icon: 'error',
                    confirmButtonText: 'OK'
                    });
                </script>";
        } else if ($usuarioController->verificaEmail()) {
            echo "
                <script>
                    Swal.fire({
                    title: 'Erro ao cadastrar usuario!',
                    text: 'Esse Email já esta em uso!',
                    icon: 'error',
                    confirmButtonText: 'OK'
                    });
                </script>";
        } else {
            if ($usuarioController->addUsuario()) {
                if ($confeitariaController->addConfeitaria()) {
                    $idUsuario = $usuarioController->buscarUltimoUsuario();
                    $codigo = $usuarioController->gerarCodigo($idUsuario);
                    $usuarioController->enviaEmail($_POST['emailUsuario'], $codigo);
                    echo gerarScriptConfeitaria($_POST['emailUsuario'], $idUsuario);
                }
            }
        }
    }
    ?>
</body>

</html>