<?php
session_start();
if (!isset($_SESSION['idCliente'])) {
    echo "<script language='javascript' type='text/javascript'> window.location.href='clientes-root.php'</script>";
}

include_once '../controller/controller-cliente.php';
$clienteController = new controllerCliente();

if (isset($_POST['submit'])) {
    if ($clienteController->verificaEndereco()) {
        echo '<script>alert("Este endereço ja esta cadastrado!");</script>';
    } else {
        $clienteController->addEndereco();
    }
}

$enderecos = $clienteController->viewEndereco($_SESSION['idCliente']);
include_once '../view/delete.php';
$delete = new Delete();
$deleteEndereco = $delete->deletarEndereco();
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

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"
        integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://unpkg.com/scrollreveal"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
                        <li><a href="confeitarias-root.php">Voltar </a></li>
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
            <form id="form" method="post" onsubmit="return validaEndereco()">
                <div class="form-header">
                    <div class="title">
                        <h1>Endereço</h1>
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
                <div class="continue-button">
                    <button type="submit" id="submit" name="submit">Cadastrar</button>
                </div>
            </form>
            <script src="assets/js/valida-endereco-root.js"></script>
        </div>
    </div>

    </div>

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
            <tbody id="telefones">
                <?php if (empty($enderecos)): ?>
                    <tr>
                        <td colspan="5">Não existe nenhum cadastro no momento.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($enderecos as $e): ?>
                        <tr>
                            <td>
                                <?php echo $e['cepCliente']; ?>
                            </td>
                            <td>
                                <?php echo $e['logCliente']; ?>
                            </td>
                            <td>
                                <?php echo $e['numLocal']; ?>
                            </td>
                            <td>
                                <?php echo $e['bairroCliente']; ?>
                            </td>
                            <td>
                                <?php echo $e['cidadeCliente']; ?>
                            </td>
                            <td>
                                <?php echo $e['ufCliente']; ?>
                            </td>
                            <td>
                                <?php echo "<a href=\"$deleteEndereco?id={$e['idEnderecoCliente']}\" onClick=\"return confirm('Tem certeza que deseja excluir?' )\">Excluir</a>" ?>
                            </td>

                            <td>
                                <?php echo "<a href=\"editar-endereco-root.php?id={$e['idEnderecoCliente']}&cep={$e['cepCliente']}&log={$e['logCliente']}&numLocal={$e['numLocal']}&bairro={$e['bairroCliente']}&cidade={$e['cidadeCliente']}&uf={$e['ufCliente']}\" onClick=\"return confirm('Tem certeza que deseja editar?' )\">Editar</a>" ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

    </div>

    <footer class="rodape">
        <p>© 2024 | Todos os direitos são de propriedade da FoxBitSystem</p>
    </footer>

</body>

</html>