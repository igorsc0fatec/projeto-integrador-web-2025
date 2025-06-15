<?php
require_once '../controller/controller-config.php';
$configController = new ControllerConfig();

session_start();

if (isset($_GET['action']) && $_GET['action'] == 'validaEmail') {
    if (isset($_POST['emailUsuario'])) {
        $emailUsuario = $_POST['emailUsuario'];

        if ($configController->usuario->validaEmail($emailUsuario)) {
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
    if (isset($_POST['codigoDigitado']) && isset($_POST['idUsuario'])) {
        $codigoDigitado = $_POST['codigoDigitado'];
        $idUsuario = $_POST['idUsuario'];

        if ($configController->usuario->validarCodigo($codigoDigitado, $idUsuario)) {
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

$numProdutosCarrinho = 0;
if (isset($_SESSION['carrinho'])) {
    $numProdutosCarrinho = count($_SESSION['carrinho']);
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastre-se</title>
    <link rel="stylesheet" href="../assets/css/style-form.css">
    <link rel="stylesheet" href="../assets/css/style-menu.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
    <title>Novo Cliente</title>
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
                        Faça o seu Cadastro!
                    </div>

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
            <img src="../assets/img-site/pessoa.webp" alt="">
        </div>
        <div class="form">
            <form id="form" method="post" onsubmit="return validaCliente()">
                <div class="form-header">
                    <div class="title">
                        <h1>Insira os seus dados</h1>
                    </div>
                </div>

                <div class="input-group">
                    <div class="input-box">
                        <label for="nome">Nome Completo</label>
                        <input id="nomeCliente" type="text" name="nomeCliente" placeholder="Digite seu nome" required>
                    </div>

                    <div class="input-box">
                        <label for="email">E-mail</label>
                        <input id="emailUsuario" type="email" name="emailUsuario" placeholder="Digite seu E-mail" required>
                    </div>

                    <div class="input-box">
                        <label for="date">Data de Nascimento</label>
                        <input id="nascCliente" type="date" name="nascCliente" required>
                        <span id="erroData" class="error"></span>
                    </div>

                    <div class="input-box">
                        <label for="cpf">CPF</label>
                        <input id="cpfCliente" type="text" name="cpfCliente" placeholder="Digite seu CPF" required>
                        <span id="erroCpf" class="error"></span>
                    </div>

                    <div class="input-box">
                        <label for="CEP">CEP</label>
                        <input type="text" id="cep" name="cep" placeholder="Digite seu CEP" required>
                        <span id="erroCep1" class="error"></span>
                    </div>

                    <div class="input-box">
                        <label for="logradouro">Logradouro</label>
                        <input id="logradouro" type="text" name="logradouro" readonly>
                    </div>

                    <div class="input-box">
                        <label for="nunlocal">Nº do Local</label>
                        <input id="numLocal" type="text" name="numLocal" required>
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
                        <label for="password">Senha</label>
                        <input id="senhaUsuario" type="password" name="senhaUsuario" placeholder="Digite sua senha" minlength="8" maxlength="15" required>
                    </div>

                    <div class="input-box">
                        <label for="confirmPassword">Confirme sua Senha</label>
                        <input id="confirmaSenha" type="password" name="confirmaSenha" placeholder="Digite sua senha novamente" minlength="8" maxlength="15" required>
                        <span id="erroSenha1" class="error"></span>
                    </div>

                    <input type="hidden" id="msg" name="msg" value="2">
                    <input id="latitude" type="hidden" name="latitude">
                    <input id="longitude" type="hidden" name="longitude">
                </div>

                <div class="continue-button">
                    <button type="submit" id="cadastrar" name="cadastrar">Cadastrar</button>
                </div>
            </form>

            <?php
            function gerarScriptCliente($emailUsuario, $idUsuario)
            {
                $emailUsuarioJson = json_encode($emailUsuario);
                $idUsuarioJson = json_encode($idUsuario);

                return "
                <script>
                    const emailUsuario = $emailUsuarioJson;
                    const idUsuario = $idUsuarioJson;

                    function verificarCodigoCliente() {
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

                                $.ajax({
                                    url: 'cadastrar-cliente.php?action=validar_codigo',
                                    method: 'POST',
                                    data: {
                                        codigoDigitado: codigoDigitado,
                                        idUsuario: idUsuario
                                    },
                                    success: function (response) {
                                        if (response === 'valido') {
                                            $.ajax({
                                                url: 'cadastrar-cliente.php?action=validaEmail',
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
                                                            window.location.href = 'login-cliente.php';
                                                        });
                                                    } else {
                                                        Swal.fire('Erro', 'Erro ao ativar a conta.', 'error');
                                                    }
                                                },
                                                error: function () {
                                                    Swal.fire('Erro', 'Erro na requisição.', 'error');
                                                }
                                            });
                                        } else {
                                            Swal.fire('Erro', 'Código inválido ou expirado.', 'error').then(() => {
                                                verificarCodigoCliente();
                                            });
                                        }
                                    },
                                    error: function () {
                                        Swal.fire('Erro', 'Erro na requisição.', 'error');
                                    }
                                });
                            }
                        });
                    }

                    verificarCodigoCliente();
                </script>";
            }

            if (isset($_POST['cadastrar'])) {
                if ($configController->cliente->verificaCPF()) {
                    echo "<script>
                            Swal.fire({
                                title: 'Erro ao cadastrar usuário!',
                                text: 'Esse CPF já está em uso!',
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                          </script>";
                } elseif ($configController->usuario->verificaEmail()) {
                    echo "<script>
                            Swal.fire({
                                title: 'Erro ao cadastrar usuário!',
                                text: 'Esse Email já está em uso!',
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                          </script>";
                } else {
                    if ($configController->usuario->addUsuario()) {
                        if ($configController->cliente->addCliente()) {
                            $idUsuario = $configController->usuario->buscarUltimoUsuario();
                            $codigo = $configController->usuario->gerarCodigo($idUsuario);
                            $configController->usuario->enviaEmail($_POST['emailUsuario'], $codigo);
                            echo gerarScriptCliente($_POST['emailUsuario'], $idUsuario);
                        }
                    }
                }
            }
            ?>
        </div>
    </div>

    <script src="js/valida-cliente.js"></script>
</body>

</html>