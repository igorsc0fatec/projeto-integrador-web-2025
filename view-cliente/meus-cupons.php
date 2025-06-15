<?php
session_start();
if (!isset($_SESSION['idCliente'])) {
    session_destroy();
    echo "<script>window.location.href='login-cliente.php';</script>";
    exit;
}

include_once '../controller/controller-pedido.php';
$cupomController = new ControllerPedido();
$cupons = $cupomController->listarCupons($_SESSION['idUsuario']);

$numProdutosCarrinho = 0;
if (isset($_SESSION['carrinho'])) {
    $numProdutosCarrinho = count($_SESSION['carrinho']);
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meus Cupons</title>
    <link rel="stylesheet" href="../assets/css/style-menu.css">
    <link rel="stylesheet" href="../assets/css/style-cupons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
                        <?php
                        if (isset($_SESSION['nome'])) {
                            echo $_SESSION['nome'];
                        }
                        ?>
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
                        <li><a href="meus-pedidos.php">Pedidos</a></li>
                        <li><a href="meus-cupons.php">Cupons</a></li>
                        <li><a href="meus-contatos.php">Conversas</a></li>
                        <li><a href="editar-cliente.php">Meus Dados</a></li>
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

    <div class="container-body">
        <h1>Meus Cupons</h1>
        <ul class="cupom-list">
            <?php foreach ($cupons as $cupom): ?>
                <?php
                $dataCriacao = new DateTime($cupom['data_criacao']);
                $dataExpiracao = clone $dataCriacao;
                $dataExpiracao->modify('+7 days');
                $dataAtual = new DateTime();
                $diasRestantes = $dataAtual->diff($dataExpiracao)->days;
                $expirado = $dataAtual > $dataExpiracao;
                ?>
                <li class="cupom <?= $expirado ? 'expirado' : 'valido' ?>">
                    <div class="cupom-info">
                        <div class="titulo"><?= htmlspecialchars($cupom['titulo_cupom']) ?></div>
                        <div class="desconto"><?= htmlspecialchars($cupom['porcen_desconto']) ?>% OFF</div>
                        <div class="mensagem"><?= htmlspecialchars($cupom['mensagem']) ?></div>
                        <div class="validade">
                            <?= $expirado ? '<span class="expirado">Expirado</span>' : '<span class="valido">Expira em ' . $diasRestantes . ' dias</span>' ?>
                        </div>
                    </div>
                    <div class="cupom-code-container">
                        <button class="reveal-btn" <?= $expirado ? 'disabled' : '' ?>>
                            <i class="fas fa-gift"></i> Revelar Código
                        </button>
                        <div class="scratch-card" data-code="<?= htmlspecialchars($cupom['desc_cupom']) ?>">
                            <div class="scratch-overlay"></div>
                            <div class="cupom-code"><?= htmlspecialchars($cupom['desc_cupom']) ?></div>
                        </div>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <script>
        $(document).ready(function() {
            // Efeito de raspadinha
            $('.scratch-card').each(function() {
                let isDrawing = false;
                let lastPoint = null;
                const overlay = $(this).find('.scratch-overlay');
                const canvas = $('<canvas>').addClass('scratch-canvas')[0];
                const ctx = canvas.getContext('2d');

                // Configurar canvas
                $(this).prepend(canvas);
                $(canvas).css({
                    position: 'absolute',
                    top: 0,
                    left: 0,
                    width: '100%',
                    height: '100%',
                    zIndex: 2
                });

                // Ajustar tamanho do canvas
                function resizeCanvas() {
                    const container = $(this).parent();
                    canvas.width = container.width();
                    canvas.height = container.height();

                    // Preencher com a máscara
                    ctx.fillStyle = '#ccc';
                    ctx.fillRect(0, 0, canvas.width, canvas.height);
                }

                resizeCanvas.call(this);
                $(window).resize(resizeCanvas.bind(this));

                // Eventos para raspadinha
                $(canvas).on('mousedown touchstart', function(e) {
                    isDrawing = true;
                    lastPoint = getPosition(e, canvas);
                    e.preventDefault();
                });

                $(document).on('mousemove touchmove', function(e) {
                    if (!isDrawing) return;

                    const currentPoint = getPosition(e, canvas);
                    if (lastPoint) {
                        ctx.globalCompositeOperation = 'destination-out';
                        ctx.beginPath();
                        ctx.moveTo(lastPoint.x, lastPoint.y);
                        ctx.lineTo(currentPoint.x, currentPoint.y);
                        ctx.lineWidth = 20;
                        ctx.lineCap = 'round';
                        ctx.stroke();
                    }
                    lastPoint = currentPoint;

                    // Verificar se raspou o suficiente
                    checkIfRevealed(canvas, overlay);
                });

                $(document).on('mouseup touchend', function() {
                    isDrawing = false;
                    lastPoint = null;
                });

                function getPosition(e, canvas) {
                    const rect = canvas.getBoundingClientRect();
                    return {
                        x: (e.clientX || e.touches[0].clientX) - rect.left,
                        y: (e.clientY || e.touches[0].clientY) - rect.top
                    };
                }

                function checkIfRevealed(canvas, overlay) {
                    const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                    const pixels = imageData.data;
                    let transparentPixels = 0;

                    for (let i = 0; i < pixels.length; i += 4) {
                        if (pixels[i + 3] < 128) {
                            transparentPixels++;
                        }
                    }

                    const percentTransparent = (transparentPixels / (pixels.length / 4)) * 100;
                    if (percentTransparent > 30) { // 30% raspado
                        overlay.fadeOut(500);
                        $(canvas).off('mousedown touchstart');
                    }
                }
            });

            // Botão de revelar (alternativa à raspadinha)
            $('.reveal-btn').click(function() {
                const scratchCard = $(this).siblings('.scratch-card');
                scratchCard.find('.scratch-overlay').fadeOut(500);
                scratchCard.find('.scratch-canvas').remove();
            });
        });
    </script>
</body>

</html>