<?php
require_once '../controller/controller-pedido.php';
require_once '../controller/controller-usuario.php';

$controllerPedido = new ControllerPedido();
$usuarioController = new ControllerUsuario();

session_start();
if (isset($_SESSION['emailUsuario'])) {
    if (isset($_SESSION['idTipoUsuario'])) {
    } else {
        $usuarioController->armazenaSessao(2, $_SESSION['emailUsuario']);
        $controllerPedido->gerarCupomSeNecessario($_SESSION['idUsuario']);
    }
}

// Inicialização segura do carrinho
if (!isset($_SESSION['carrinho']) || !is_array($_SESSION['carrinho'])) {
    $_SESSION['carrinho'] = [];
}

// Verificação adicional para garantir que é um array
if (!is_array($_SESSION['carrinho'])) {
    $_SESSION['carrinho'] = [];
}

$carrinho = $_SESSION['carrinho'];

// Processamento das ações
if (isset($_POST['remover'])) {
    $index = (int) $_POST['index']; // Convertendo para inteiro para segurança
    if (isset($_SESSION['carrinho'][$index])) {
        unset($_SESSION['carrinho'][$index]);
        $_SESSION['carrinho'] = array_values($_SESSION['carrinho']); // Reindexa o array
        $carrinho = $_SESSION['carrinho']; // Atualiza a variável local
    }
}

if (isset($_POST['atualizar'])) {
    $index = (int) $_POST['index'];
    $novaQuantidade = (int) $_POST['quantidade'];

    if ($novaQuantidade < 1)
        $novaQuantidade = 1;
    if ($novaQuantidade > 5)
        $novaQuantidade = 5;

    if (isset($_SESSION['carrinho'][$index])) {
        $_SESSION['carrinho'][$index]['quantidade'] = $novaQuantidade;

        // Força o recálculo do desconto
        if (isset($_SESSION['cupom_aplicado'])) {
            $totalProdutos = 0;
            foreach ($_SESSION['carrinho'] as $produto) {
                $totalProdutos += $produto['valorProduto'] * $produto['quantidade'];
            }

            if ($_SESSION['cupom_aplicado']['tipo'] == 'porcentagem') {
                $_SESSION['cupom_aplicado']['desconto'] =
                    $totalProdutos * ($_SESSION['cupom_aplicado']['valor'] / 100);
            } else {
                $_SESSION['cupom_aplicado']['desconto'] =
                    min($_SESSION['cupom_aplicado']['valor'], $totalProdutos);
            }
        }

        $carrinho = $_SESSION['carrinho'];
    }
}

if (isset($_POST['action']) && $_POST['action'] === 'validarCupom') {
    echo $controllerPedido->validarCupom($_POST['codigo']);
    exit();
}

// Cálculo dos totais
$totalProdutos = 0;
$frete = 0;
$quantidadeTotal = 0;

foreach ($carrinho as $produto) {
    if (!isset($produto['valorProduto'], $produto['quantidade'], $produto['frete'])) {
        continue;
    }

    $totalProdutos += (float) $produto['valorProduto'] * (int) $produto['quantidade'];
    $quantidadeTotal += (int) $produto['quantidade'];
    $frete += (float) $produto['frete'];
}

// Reaplicar o desconto se existir um cupom
if (isset($_SESSION['cupom_aplicado'])) {
    // Se for desconto percentual
    if (isset($_SESSION['cupom_aplicado']['porcen_desconto'])) {
        $porcentagem = $_SESSION['cupom_aplicado']['porcen_desconto'];
        $_SESSION['cupom_aplicado']['desconto'] = $totalProdutos * ($porcentagem / 100);
    }
    // Se for desconto fixo, mantém o valor original
    // (assumindo que já está armazenado em $_SESSION['cupom_aplicado']['desconto'])

    $total = max(0, ($totalProdutos - (float) $_SESSION['cupom_aplicado']['desconto']) + $frete);
} else {
    $total = $totalProdutos + $frete;
}

// Armazenamento dos dados do pedido - INCLUINDO id_cupom
$_SESSION['dados_pedido'] = [
    'frete' => $frete,
    'quantidadeTotal' => $quantidadeTotal,
    'valorTotal' => $totalProdutos + $frete,
    'valorTotalBruto' => $totalProdutos,
    'valorDesconto' => isset($_SESSION['cupom_aplicado']['desconto'])
        ? (float) $_SESSION['cupom_aplicado']['desconto']
        : 0,
    'id_cupom' => isset($_SESSION['cupom_aplicado']['id_cupom'])
        ? $_SESSION['cupom_aplicado']['id_cupom']
        : null
];

// Aplicação de desconto se existir
$total = $totalProdutos + $frete;
if (isset($_SESSION['cupom_aplicado']['desconto'])) {
    $total = max(0, ($totalProdutos - (float) $_SESSION['cupom_aplicado']['desconto']) + $frete);
}

$numProdutosCarrinho = count($carrinho);
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Carrinho de Compras</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style-menu.css">
    <link rel="stylesheet" href="../assets/css/style-carrinho.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>

    <div class="container">
        <header>
            <nav>
                <div class="nav-container">
                    <a href="../view/index.php">
                        <img id="logo" src="../assets/img-site/logo.png" alt="Logo">
                    </a>
                    <i class="fas fa-bars btn-menumobile"></i>
                    <ul>
                        <li style="position: relative;">
                            <a href="../view-cliente/carrinho.php">
                                <i class="fa fa-shopping-cart" style="font-size:20px"></i>
                                <?php if ($numProdutosCarrinho > 0): ?>
                                    <span class="cart-counter"><?php echo $numProdutosCarrinho; ?></span>
                                <?php endif; ?>
                            </a>
                        </li>
                        <li><a href="../view-cliente/meus-pedidos.php">Pedidos</a></li>
                        <li><a href="../view-cliente/meus-cupons.php">Cupons</a></li>
                        <li><a href="../view-cliente/meus-contatos.php">Conversas</a></li>
                        <li><a href="../view-cliente/editar-cliente.php">Meus Dados</a></li>
                        <li><a href="pedir-suporte.php">Suporte</a></li>
                    </ul>
                </div>
            </nav>
        </header>
    </div>

    <div class="container px-3 my-5 clearfix">
        <div class="card">
            <div class="card-header">
                <h2>Carrinho de Compras</h2>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered m-0">
                        <thead>
                            <tr>
                                <th class="text-center py-3 px-4" style="min-width: 400px;">Nome e Detalhes do Produto
                                </th>
                                <th class="text-right py-3 px-4" style="width: 100px;">Preço</th>
                                <th class="text-center py-3 px-4" style="width: 120px;">Quantidade</th>
                                <th class="text-right py-3 px-4" style="width: 100px;">Frete</th>
                                <th class="text-right py-3 px-4" style="width: 100px;">Excluir</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($carrinho)): ?>
                                <tr>
                                    <td colspan="5" class="text-center">Seu carrinho está vazio.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($carrinho as $index => $produto): ?>
                                    <?php if (!isset($produto['nomeProduto'], $produto['valorProduto'], $produto['quantidade'], $produto['frete'], $produto['imgProduto'], $produto['descProduto']))
                                        continue; ?>
                                    <tr>
                                        <td class="p-4">
                                            <div class="media align-items-center">
                                                <img src="../view-confeitaria/<?php echo htmlspecialchars($produto['imgProduto']); ?>"
                                                    class="d-block ui-w-40 ui-bordered mr-4"
                                                    alt="<?php echo htmlspecialchars($produto['nomeProduto']); ?>">
                                                <div class="media-body">
                                                    <a href="#" class="d-block text-dark">
                                                        <?php echo htmlspecialchars($produto['nomeProduto']); ?>
                                                    </a>
                                                    <small>
                                                        <span class="text-muted">Descrição: </span>
                                                        <?php echo htmlspecialchars($produto['descProduto']); ?>
                                                    </small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-right font-weight-semibold align-middle p-4">
                                            R$ <?php echo number_format($produto['valorProduto'], 2, ',', '.'); ?>
                                        </td>
                                        <td class="align-middle p-4">
                                            <form action="" method="post" style="display: flex; align-items: center;">
                                                <input type="hidden" name="index" value="<?php echo $index; ?>">
                                                <input type="number" name="quantidade"
                                                    value="<?php echo (int) $produto['quantidade']; ?>"
                                                    class="form-control form-control-quantity text-center" min="1" max="5">
                                                <button type="submit" name="atualizar"
                                                    class="btn btn-sm btn-outline-primary ml-2">Atualizar</button>
                                            </form>
                                        </td>
                                        <td class="text-right font-weight-semibold align-middle p-4">
                                            R$ <?php echo number_format($produto['frete'], 2, ',', '.'); ?>
                                        </td>
                                        <td class="text-center align-middle px-0">
                                            <form action="" method="post" class="remove-form">
                                                <input type="hidden" name="index" value="<?php echo $index; ?>">
                                                <button type="submit" name="remover"
                                                    class="close text-danger remove-button">&times;</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div class="d-flex flex-wrap justify-content-between align-items-center pb-4">
                    <div class="mt-4">
                        <label class="text-muted font-weight-normal">Código Promocional</label>
                        <input type="text" id="codigoCupom" placeholder="Digite aqui o cupom" class="form-control">
                        <button type="button" id="btnAplicarCupom" class="btn btn-sm btn-primary mt-2">Usar
                            Código</button>
                    </div>
                    <div class="d-flex">
                        <div class="text-right mt-4 mr-5">
                            <label class="text-muted font-weight-normal m-0">Desconto</label>
                            <div class="text-large"><strong>
                                    R$ <?php echo isset($_SESSION['cupom_aplicado']['desconto'])
                                        ? number_format($_SESSION['cupom_aplicado']['desconto'], 2, ',', '.')
                                        : '0,00'; ?>
                                </strong></div>
                        </div>
                        <div class="text-right mt-4">
                            <label class="text-muted font-weight-normal m-0">Preço Total</label>
                            <div class="text-large"><strong>
                                    R$ <?php echo number_format($total, 2, ',', '.'); ?>
                                </strong></div>
                        </div>
                    </div>
                </div>
                <div class="float-right">
                    <?php if (empty($carrinho)): ?>
                        <a href="../view/index.php" class="btn btn-lg btn-default md-btn-flat mt-2 mr-3 btn-link-style">
                            Voltar às compras
                        </a>
                    <?php else: ?>
                        <a href="dados-confeitaria.php?c=<?php echo $carrinho[0]['idConfeitaria']; ?>&u=<?php echo $carrinho[0]['idUsuario'] ?>"
                            class="btn btn-lg btn-default md-btn-flat mt-2 mr-3 btn-link-style">
                            Voltar às compras
                        </a>
                    <?php endif; ?>
                    <form action="" method="post" style="display: inline;">
                        <input type="hidden" name="quantidadeTotal" value="<?php echo $quantidadeTotal; ?>">
                        <input type="hidden" name="valorTotal"
                            value="<?php echo number_format($total, 2, ',', '.'); ?>">
                        <input type="hidden" name="frete" value="<?php echo number_format($frete, 2, ',', '.'); ?>">
                        <button type="submit" name="finalizar" class="btn btn-lg btn-primary mt-2">
                            Finalizar compra
                        </button>
                    </form>
                </div>
                <?php
                if (isset($_POST['finalizar'])) {
                    if (empty($_SESSION['carrinho'])) {
                        echo "<script>
                            Swal.fire({
                                title: 'Erro ao realizar compra!',
                                text: 'Você precisa de produtos no carrinho pra fazer uma compra!',
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        </script>";
                    } else if (!isset($_SESSION['emailUsuario'])) {
                        echo "<script>
                            Swal.fire({
                                title: 'Para continuar a compra faça login!',
                                icon: 'info',
                                confirmButtonText: 'OK',
                                didClose: () => {
                                    window.location.href = 'login-cliente.php';
                                }               
                            });
                        </script>";
                    } else {
                        // Armazena os dados do pedido
                        $_SESSION['dados_pedido'] = [
                            'frete' => $frete,
                            'quantidadeTotal' => $quantidadeTotal,
                            'valorTotal' => $total,
                            'valorTotalBruto' => $totalProdutos,
                            'valorDesconto' => isset($_SESSION['cupom_aplicado']['desconto'])
                                ? (float) $_SESSION['cupom_aplicado']['desconto']
                                : 0,
                            'id_cupom' => isset($_SESSION['cupom_aplicado']['id_cupom'])
                                ? $_SESSION['cupom_aplicado']['id_cupom']
                                : null
                        ];

                        // Remove o cupom da sessão após armazenar nos dados do pedido
                        if (isset($_SESSION['cupom_aplicado'])) {
                            unset($_SESSION['cupom_aplicado']);
                        }

                        echo "<script>
                            Swal.fire({
                                title: 'Escolha uma forma de pagamento para prosseguir com a compra!',
                                icon: 'info',
                                confirmButtonText: 'OK',
                                didClose: () => {
                                    window.location.href = 'forma-pagamento.php';
                                }               
                            });
                        </script>";
                    }
                }
                ?>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-1.10.2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.1/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        $(document).ready(function () {
            // Remove a verificação que desabilita o cupom inicialmente

            $("#btnAplicarCupom").click(function () {
                const codigoCupom = $("#codigoCupom").val().trim();

                if (!codigoCupom) {
                    Swal.fire({
                        title: 'Erro',
                        text: 'Por favor, insira um código de cupom.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                    return;
                }

                $.ajax({
                    url: '',
                    type: 'POST',
                    data: {
                        action: 'validarCupom',
                        codigo: codigoCupom
                    },
                    dataType: 'json',
                    success: function (response) {
                        if (response.status === "success") {
                            Swal.fire({
                                title: 'Sucesso!',
                                text: response.message,
                                icon: 'success',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                // Atualiza apenas os valores na página sem desabilitar o cupom
                                $(".text-large strong").eq(0).text("R$ " + parseFloat(response.desconto).toFixed(2).replace('.', ','));

                                const totalProdutos = <?php echo $totalProdutos; ?>;
                                const novoTotal = (totalProdutos - parseFloat(response.desconto)) + <?php echo $frete; ?>;
                                $(".text-large strong").eq(1).text("R$ " + novoTotal.toFixed(2).replace('.', ','));

                                // Recarrega a página para atualizar todos os valores
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                title: 'Erro',
                                text: response.message,
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        }
                    },
                    error: function () {
                        Swal.fire({
                            title: 'Erro',
                            text: 'Falha ao comunicar com o servidor.',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                });
            });
        });
    </script>
</body>

</html>