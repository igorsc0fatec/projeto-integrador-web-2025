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

if (isset($_GET['action']) && $_GET['action'] == 'fetch_data') {
    header('Content-Type: application/json');

    if (isset($_POST['pesq']) && !empty($_POST['pesq'])) {
        $produtos = $produtoController->pesquisaProduto();
    } else {
        $produtos = $produtoController->viewProduto();
    }

    echo json_encode($produtos);
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
    <link rel="stylesheet" href="../assets/css/style-tabela.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
    <title>Produtos</title>
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
                            echo $_SESSION['nome'];
                        }
                        ?>
                    </div>

                    <i class="fas fa-bars btn-menumobile"></i>
                    <ul class="nav-links">
                        <li><a href="meus-produtos.php">Produtos</a></li>
                        <li><a href="pedidos.php">Pedidos</a></li>
                        <li><a href="meus-contatos.php">Conversas</a></li>
                        <li><a href="editar-confeitaria.php">Meus Dados</a></li>
                        <li><a href="../view/pedir-suporte.php">Suporte</a></li>
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
                        <label for="nome">Nome do Produto*</label>
                        <input id="nomeProduto" type="text" name="nomeProduto" maxlength="100"
                            placeholder="Nome do Produto:" required>
                    </div>

                    <div class="input-box">
                        <label for="price">Valor do Produto*</label>
                        <input id="valorProduto" name="valorProduto" type="text" placeholder="R$ 0,00" required>
                    </div>

                    <div class="input-box">
                        <label for="frete">Frete*</label>
                        <input id="frete" name="frete" type="text" placeholder="R$ 0,00" required>
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
                        <label for="img" class="upload-button">Escolha uma imagem*</label>
                        <input id="img" type="file" accept="image/*" name="img" required onchange="previewImage()">
                        <p>*A imagem deve conter no máximo 16mb</p>
                        <span id="erroImagem" class="error"></span>
                    </div>

                    <br>

                    <div class="input-box">
                        <label for="distanciaMaxima">Distância máxima de entrega (km)</label>
                        <div class="range-container">
                            <input type="range" id="distanciaMaxima" name="distanciaMaxima" min="0" max="150" value="30"
                                step="1" oninput="updateDistanceValue(this.value)">
                            <span id="distanceValue">30 km</span>
                        </div>
                    </div>

                    <div class="input-box">
                        <label for="descProduto">Descrição do Produto*</label>
                        <textarea id="descProduto" name="descProduto" placeholder="Descreva sobre o produto:"
                            maxlength="150" oninput="updateCharCount()" required></textarea>
                        <div id="charCount">150 caracteres restantes</div>
                    </div>

                </div>
                <div class="continue-button">
                    <button type="submit" id="submit" name="submit">Cadastrar</button>
                </div>
            </form>
            <script src="js/valida-produto.js"></script>
        </div>
    </div>

    <?php
    if (isset($_POST['submit'])) {
        if ($produtoController->verificaProduto()) {
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
            if ($produtoController->addProduto()) {
                echo "
                <script>
                    Swal.fire({
                    title: 'Produto cadastrado com sucesso!',
                    icon: 'success',
                    confirmButtonText: 'OK'
                    });
                </script>";
            }
        }
    }
    $produtos = $produtoController->viewProduto();
    ?>

    <div>
        <br><br>
    </div>

    <div>
        <!-- Substitua a seção de pesquisa por esta versão melhorada -->
        <div class="search-section">
            <h2>Seus Produtos</h2>

            <div class="search-container">
                <form id="search-form" method="post" class="modern-search-form">
                    <div class="search-box">
                        <input id="pesq" type="text" name="pesq" placeholder="Buscar produtos..." required>
                        <button type="submit" name="pesquisa" class="search-button">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="tabela-scroll">
            <table id="minhaTabela">
                <thead>
                    <tr>
                        <th style="background-color:rgb(0, 0, 0); color: white;">Imagem</th>
                        <th style="background-color:rgb(0, 0, 0); color: white;">Nome</th>
                        <th style="background-color:rgb(0, 0, 0); color: white;">Descrição</th>
                        <th style="background-color:rgb(0, 0, 0); color: white;">Tipo</th>
                        <th style="background-color:rgb(0, 0, 0); color: white;">Valor</th>
                        <th style="background-color:rgb(0, 0, 0); color: white;">Status</th>
                        <th style="background-color:rgb(0, 0, 0); color: white;">Frete</th>
                        <th style="background-color:rgb(0, 0, 0); color: white;">Distância Max</th>
                        <th style="background-color:rgb(0, 0, 0); color: white;">Editar</th>
                    </tr>
                </thead>
                <tbody id="produtos">
                </tbody>
            </table>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            function loadData(query = '') {
                $.ajax({
                    url: 'cadastrar-produto.php?action=fetch_data',
                    type: 'POST',
                    data: {
                        pesq: query
                    },
                    dataType: 'json',
                    success: function(data) {
                        var tableBody = $('#produtos');
                        tableBody.empty();
                        if (data.length === 0) {
                            tableBody.append('<tr><td colspan="8">Você não tem nenhum produto no momento</td></tr>');
                        } else {
                            data.forEach(function(produto) {
                                var row = $('<tr></tr>');
                                row.append('<td><img src="' + produto.img_produto + '" alt="' + produto.nome_produto + '" width="50"></td>');
                                row.append('<td>' + produto.nome_produto + '</td>');
                                row.append('<td>' + produto.desc_produto + '</td>');
                                row.append('<td>' + produto.desc_tipo_produto + '</td>');
                                row.append('<td>' + produto.valor_produto + '</td>');
                                if (produto.produto_ativo == 1) {
                                    row.append('<td>' + 'Ativado' + '</td>');
                                } else {
                                    row.append('<td>' + 'Desativado' + '</td>');
                                }
                                row.append('<td>' + produto.frete + '</td>');
                                row.append('<td>' + produto.limite_entrega + '</td>');
                                row.append('<td><button onclick="confirmarEdicao(' + produto.id_produto + ',\'' + produto.nome_produto + '\', \'' + produto.desc_produto + '\', \'' + produto.valor_produto + '\', \'' + produto.frete + '\', \'' + produto.limite_entrega + '\')">Editar</button></td>');
                                tableBody.append(row);
                            });
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.log('Erro: ' + textStatus + ' - ' + errorThrown);
                    }
                });
            }

            $('#search-form').on('submit', function(e) {
                e.preventDefault();
                var query = $('#pesq').val();
                loadData(query);
            });

            loadData();
        });

        function confirmarEdicao(idProduto, nomeProduto, descProduto, valorProduto, frete, limite_entrega) {
            Swal.fire({
                title: 'Tem certeza que deseja editar?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sim, editar!',
                cancelButtonText: 'Não, cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    const url = `editar-produto.php?id=${idProduto}&nome=${nomeProduto}&desc=${descProduto}&valor=${valorProduto}&frete=${frete}&limite=${limite_entrega}`;
                    window.location.href = url;
                }
            });
        }

        function updateDistanceValue(value) {
            document.getElementById('distanceValue').textContent = value + ' km';
        }
    </script>

</body>

</html>