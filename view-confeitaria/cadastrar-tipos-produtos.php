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

include_once '../controller/controller-tipo-produto.php';
$tipoProdutoController = new ControllerTipoProduto();

if (isset($_GET['action']) && $_GET['action'] == 'fetch_data') {
    header('Content-Type: application/json');

    if (isset($_POST['pesq']) && !empty($_POST['pesq'])) {
        $tiposProdutos = $tipoProdutoController->pesquisaTipoProduto();
    } else {
        $tiposProdutos = $tipoProdutoController->viewTipoProduto();
    }

    echo json_encode($tiposProdutos);
    exit;
}

if (isset($_GET['id'])) {
    header('Content-Type: application/json');
    echo json_encode($tipoProdutoController->deleteTipoProduto($_GET['id']));
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
                        <li><a href="meus-produtos.php">Produtos</a></li>
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
            <img src="../assets/img-site/logo.png" alt="">
        </div>
        <div class="form">
            <form method="post">
                <div class="form-header">
                    <div class="title">
                        <h1>Tipo de Produto</h1>
                    </div>
                </div>
                <div class="input-group">
                    <div class="input-box">
                        <label for="descTipoProduto">Tipo de Produto*</label>
                        <textarea id="descricao" name="descricao" placeholder="Descreva o Tipo de Produto:"
                            maxlength="150" oninput="updateCharCount()" required></textarea>
                        <div id="charCount">150 caracteres restantes</div>
                    </div>
                </div>

                <div class="continue-button">
                    <button type="submit" id="submit" name="submit">Cadastrar</button>
                </div>
            </form>
            <script src="assets/js/valida-regras.js"></script>
        </div>
    </div>

    <?php
    if (isset($_POST['submit'])) {
        if ($tipoProdutoController->verificaTipoProduto()) {
            echo "
                <script>
                    Swal.fire({
                    title: 'Erro ao cadastrar Tipo de Produto!',
                    text: 'Esse Tipo já está cadastrado!',
                    icon: 'error',
                    confirmButtonText: 'OK'
                    });
                </script>";
        } else {
            if ($tipoProdutoController->addTipoProduto()) {
                echo "
                    <script>
                        Swal.fire({
                        title: 'Tipo cadastrado com sucesso!',
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

        <h2>Sua lista de Tipos de Produtos</h2>

        <form id="search-form" method="post">
            <div class="pesquisa">
                <label for="pesq">Buscar Tipo de produto</label>
                <div class="input-wrapper">
                    <input id="pesq" type="text" name="pesq" placeholder="Digite o nome do tipo de produto/ex: bolo"
                        required>
                    <button type="submit" name="pesquisa">Pesquisar</button>
                </div>
            </div>
        </form>
        <br>
        <div class="tabela-scroll">
            <table id="minhaTabela">
                <thead>
                    <tr>
                        <th>
                            <center>Descrição</center>
                        </th>
                        <th>
                            <center>Excluir</center>
                        </th>
                        <th>
                            <center>Editar</center>
                        </th>
                    </tr>
                </thead>
                <tbody id="tiposProdutos">
                </tbody>
            </table>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            function loadData(query = '') {
                $.ajax({
                    url: 'cadastrar-tipos-produtos.php?action=fetch_data',
                    type: 'POST',
                    data: {
                        pesq: query
                    },
                    dataType: 'json',
                    success: function(data) {
                        var tableBody = $('#tiposProdutos');
                        tableBody.empty();
                        if (data.length === 0) {
                            tableBody.append('<tr><td colspan="3">Você não tem nenhum tipo de produto no momento</td></tr>');
                        } else {
                            data.forEach(function(tipo_produto) {
                                var row = $('<tr></tr>');
                                row.append('<td>' + tipo_produto.desc_tipo_produto + '</td>');
                                row.append('<td><button onclick="confirmarExclusao(' + tipo_produto.id_tipo_produto + ')">Excluir</button></td>');
                                row.append('<td><button onclick="confirmarEdicao(' + tipo_produto.id_tipo_produto + ',\'' + tipo_produto.desc_tipo_produto + '\')">Editar</button></td>');
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

        function confirmarExclusao(idTipoProduto) {
            Swal.fire({
                title: 'Tem certeza que deseja excluir?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sim, excluir!',
                cancelButtonText: 'Não, cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: 'cadastrar-tipos-produtos.php?id=' + idTipoProduto,
                        type: 'POST',
                        data: {
                            idTipoProduto: idTipoProduto
                        },
                        success: function(response) {
                            Swal.fire(
                                'Excluído!',
                                'O Tipo de Produto foi excluído.',
                                'success',
                            ).then(() => {
                                window.location.href = 'cadastrar-tipos-produtos.php';
                            });
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            Swal.fire(
                                'Erro!',
                                'Ocorreu um erro ao excluir o tipo de produto.',
                                'error'
                            );
                        }
                    });
                }
            });
        }

        function confirmarEdicao(idTipoProduto, descTipoProduto) {
            Swal.fire({
                title: 'Tem certeza que deseja editar?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sim, editar!',
                cancelButtonText: 'Não, cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    const url = `editar-tipo-produto.php?id=${idTipoProduto}&desc=${descTipoProduto}`;
                    window.location.href = url;
                }
            });
        }
    </script>

</body>

</html>