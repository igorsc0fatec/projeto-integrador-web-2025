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

require_once '../controller/controller-produto.php';
require_once '../controller/controller-tipo-produto.php';
$produtoController = new ControllerProduto();
$tipoProdutoController = new ControllerTipoProduto();

$tiposProdutos = $tipoProdutoController->viewTipoProduto();
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
    <title>Delicia online</title>

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
                        <?php
                        if (isset($_SESSION['nome'])) {
                            echo 'Ola, ' . $_SESSION['nome'];
                        } else {
                            echo "Ola, Visitante";
                        }
                        ?>
                    </div>

                    <i class="fas fa-bars btn-menumobile"></i>
                    <ul class="nav-links">
                        <li><a href="pedidos-personalizados.php">Personalizados</a></li>
                        <li><a href="cadastrar-tipos-produtos.php">Tipos de Produtos</a></li>
                        <li><a href="meus-contatos.php">Conversas</a></li>
                        <li><a href="editar-confeitaria.php">Meus Dados</a></li>
                        <li><a href="../view/pedir-suporte.php">Suporte</a></li>
                        <li>
                            <form action="../view/logout.php" method="POST">
                                <input type="hidden" name="id" value="<?php echo $_SESSION['idUsuario']; ?>">
                                <button type="submit" class="fa fa-logout logado"><i class="fa fa-sign-out"
                                        style="font-size:20px"></i></button>
                            </form>
                        </li>
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
            <img id="preview" src="../assets/img-site/logo.png" alt="Imagem selecionada"
                style="max-width: 100%; height: auto;">
        </div>
        <div class="form">
            <form enctype="multipart/form-data" method="post" onsubmit="return validaProduto()">
                <div class="form-header">
                    <div class="title">
                        <h1>Produtos</h1>
                    </div>
                </div>
                <div class="input-box">
                    <div class="input-box">
                        <label for="nome">Nome do Produto</label>
                        <input id="nomeProduto" type="text" name="nomeProduto" placeholder="Digite o Nome do Produto"
                            value="<?php echo $_GET['nome'] ?>" required>
                    </div>

                    <div class="input-box">
                        <label for="price">Valor do Produto:</label>
                        <input id="valorProduto" type="number" min=0.01 step="0.01" name="valorProduto"
                            placeholder="Digite a nova Valor do Produto:" value="<?php echo $_GET['valor'] ?>" required>
                    </div>

                    <div class="input-box">
                        <label for="frete">Frete:</label>
                        <input id="frete" type="number" min=0.01 step="0.01" maxlength="8" name="frete"
                            placeholder="Frete:" value="<?php echo $_GET['frete'] ?>" required>
                    </div>

                    <div class="input-box">
                        <label for="frete">Status do Produto:</label>
                        <select id="ativo" name="ativo">
                            <option value=1>Ativar</option>
                            <option value=0>Desativar</option>
                        </select>
                    </div>

                    <div class="input-box">
                        <label for="tipo">Tipo do Produto</label>
                        <select id="tiposProduto" name="tiposProduto">
                            <?php
                            if (!empty($tiposProdutos)) {
                                foreach ($tiposProdutos as $tipo) {
                                    ?>
                                    <option value="<?php echo $tipo['id_tipo_produto']; ?>">
                                        <?php echo $tipo['desc_tipo_produto'] ?>
                                    </option>
                                    <?php
                                }
                            } else {
                                echo '<option value="">Nenhum tipo cadastrado</option>';
                            }
                            ?>
                        </select>
                    </div>

                    <div class="input-box">
                        <label for="img" class="upload-button">Escolha uma imagem</label>
                        <input id="img" type="file" accept="image/*" name="img" required onchange="previewImage()">
                        <p>*A imagem deve conter no máximo 16mb</p>
                        <span id="erroImagem" class="error"></span>
                    </div>

                    <br>

                    <div class="input-box">
                        <label for="distanciaMaxima">Distância máxima de entrega (km)</label>
                        <div class="range-container">
                            <input type="range" id="distanciaMaxima" name="distanciaMaxima" min="0" max="150" value=<?php echo $_GET['limite'] ?>
                                step="1" oninput="updateDistanceValue(this.value)">
                            <span id="distanceValue">30 km</span>
                        </div>
                    </div>

                    <div class="input-box">
                        <label for="descProduto">Descrição do Produto:</label>
                        <textarea id="descProduto" name="descProduto" placeholder="Descreva sobre o produto:"
                            maxlength="150" oninput="updateCharCount()" required><?php echo $_GET['desc'] ?></textarea>
                        <div id="charCount">150 caracteres restantes</div>
                    </div>
                </div>
                <input id="id" type="hidden" name="id" value="<?php echo $_GET['id'] ?>">
                <div class="continue-button">
                    <button type="submit" id="submit" name="submit">Editar</button>
                </div>
            </form>

            <?php
            if (isset($_POST['submit'])) {
                if ($produtoController->verificaEditProduto()) {
                    if ($produtoController->updateProduto()) {
                        echo "
                        <script>
                            Swal.fire({
                            title: 'Produto editado com sucesso!',
                            icon: 'success',
                            confirmButtonText: 'OK',
                            didClose: () => {
                                window.location.href = 'cadastrar-produto.php';
                                }
                            });
                        </script>";
                    }
                } else if ($produtoController->verificaProduto()) {
                    echo "
                        <script>
                            Swal.fire({
                            title: 'Erro ao cadastrar o produto!',
                            text: 'Produto já cadastrado na plataforma!',
                            icon: 'error',
                            confirmButtonText: 'OK'
                            });
                        </script>";
                } else {
                    if ($produtoController->updateProduto()) {
                        echo "
                        <script>
                            Swal.fire({
                            title: 'Produto editado com sucesso!',
                            icon: 'success',
                            confirmButtonText: 'OK',
                            didClose: () => {
                                window.location.href = 'cadastrar-produto.php';
                                }
                            });
                        </script>";
                    }
                }
            }
            ?>
            <script src="js/valida-produto.js"></script>
            <script>
                function updateDistanceValue(value) {
                    document.getElementById('distanceValue').textContent = value + ' km';
                }
            </script>
        </div>
    </div>

</body>

</html>