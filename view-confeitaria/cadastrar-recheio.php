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

include_once '../controller/controller-recheio.php';
$recheioController = new ControllerRecheio();

if (isset($_GET['action']) && $_GET['action'] == 'fetch_data') {
    header('Content-Type: application/json');

    if (isset($_POST['pesq']) && !empty($_POST['pesq'])) {
        $recheios = $recheioController->pesquisaRecheio();
    } else {
        $recheios = $recheioController->viewRecheio($_SESSION['idConfeitaria']);
    }

    echo json_encode($recheios);
    exit;
}

if (isset($_GET['id'])) {
    header('Content-Type: application/json');
    echo json_encode($recheioController->deleteRecheio($_GET['id']));
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
    <title>Recheios</title>
</head>

<body>
    <div class="container">
        <header>
            <nav>
                <div class="nav-container">
                    <a href="dashboard.php">
                        <img id="logo" src="../assets/img-site/logo.png" alt="Logo Confeitaria">
                    </a>
                    <div class="greeting">
                        <?php
                        if (isset($_SESSION['nome'])) {
                            echo 'Olá, ' . $_SESSION['nome'];
                        } else {
                            echo "Olá, Visitante";
                        }
                        ?>
                    </div>

                    <i class="fas fa-bars btn-menumobile"></i>
                    <ul class="nav-links">
                        <ul>
                            <li><a href="cadastrar-produto.php">Produtos</a></li>
                            <li><a href="cadastrar-personalizado.php">Personalizados</a></li>
                            <li><a href="regras-confeitaria.php">Regras</a></li>
                            <li><a href="meus-produtos.php">Voltar </a></li>
                        </ul>
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
                        <h1>Recheios</h1>
                    </div>
                </div>
                <div class="input-group">
                    <div class="input-box">
                        <label for="descRecheio">Recheio*</label>
                        <textarea id="descricao" name="descricao" placeholder="Descreva o recheio:" maxlength="150"
                            oninput="updateCharCount()" required></textarea>
                        <div id="charCount">150 caracteres restantes</div>
                    </div>
                </div>
                <div class="input-box">
                    <label for="price">Valor por Cada 100g:*</label>
                    <input id="ValorGrama" type="text" name="ValorGrama" placeholder="R$ 0,00" required>
                </div>
                <div class="continue-button">
                    <button type="submit" id="submit" name="submit">Cadastrar</button>
                </div>
            </form>
            <script src="js/valida-regras.js"></script>
        </div>
    </div>

    <?php
    if (isset($_POST['submit'])) {
        if ($recheioController->verificaRecheio()) {
            echo "
                    <script>
                        Swal.fire({
                        title: 'Erro ao cadastrar recheio!',
                        text: 'Esse recheio já esta cadastrado!',
                        icon: 'error',
                        confirmButtonText: 'OK'
                        });
                    </script>";
        } else {
            if ($recheioController->addRecheio()) {
                echo "
                            <script>
                                Swal.fire({
                                title: 'Recheio cadastrado com sucesso!',
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
        <h2>Sua lista de Recheios</h2>

        <form id="search-form" method="post">
            <div class="pesquisa">
                <label for="pesq">Buscar Recheio</label>
                <div class="input-wrapper">
                    <input id="pesq" type="text" name="pesq" placeholder="Digite o nome do recheio/ex: brigadeiro"
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
                            <center>Valor Peso</center>
                        </th>
                        <th>
                            <center>Excluir</center>
                        </th>
                        <th>
                            <center>Editar</center>
                        </th>
                    </tr>
                </thead>
                <tbody id="recheios">
                </tbody>
            </table>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            function loadData(query = '') {
                $.ajax({
                    url: 'cadastrar-recheio.php?action=fetch_data',
                    type: 'POST',
                    data: { pesq: query },
                    dataType: 'json',
                    success: function (data) {
                        var tableBody = $('#recheios');
                        tableBody.empty();
                        if (data.length === 0) {
                            tableBody.append('<tr><td colspan="3">Você não tem nenhum recheio no momento</td></tr>');
                        } else {
                            data.forEach(function (recheio) {
                                var row = $('<tr></tr>');
                                row.append('<td>' + recheio.desc_recheio + '</td>');
                                row.append('<td>' + recheio.valor_por_peso + '</td>');
                                row.append('<td><button onclick="confirmarExclusao(' + recheio.id_recheio + ')">Excluir</button></td>');
                                row.append('<td><button onclick="confirmarEdicao(' + recheio.id_recheio + ',\'' + recheio.desc_recheio + '\',\'' + recheio.valor_por_peso + '\')">Editar</button></td>');
                                tableBody.append(row);
                            });
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        console.log('Erro: ' + textStatus + ' - ' + errorThrown);
                    }
                });
            }

            $('#search-form').on('submit', function (e) {
                e.preventDefault();
                var query = $('#pesq').val();
                loadData(query);
            });

            loadData();
        });

        function confirmarExclusao(idRecheio) {
            Swal.fire({
                title: 'Tem certeza que deseja excluir?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sim, excluir!',
                cancelButtonText: 'Não, cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: 'cadastrar-recheio.php?id=' + idRecheio,
                        type: 'POST',
                        data: { idRecheio: idRecheio },
                        success: function (response) {
                            Swal.fire(
                                'Excluído!',
                                'O recheio foi excluído.',
                                'success',
                            ).then(() => {
                                window.location.href = 'cadastrar-recheio.php';
                            });
                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            Swal.fire(
                                'Erro!',
                                'Ocorreu um erro ao excluir o recheio.',
                                'error'
                            );
                        }
                    });
                }
            });
        }

        function confirmarEdicao(idRecheio, descRecheio, valorPeso) {
            Swal.fire({
                title: 'Tem certeza que deseja editar?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sim, editar!',
                cancelButtonText: 'Não, cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    const url = `editar-recheio.php?id=${idRecheio}&desc=${descRecheio}&valor=${valorPeso}`;
                    window.location.href = url;
                }
            });
        }
    </script>

</body>

</html>