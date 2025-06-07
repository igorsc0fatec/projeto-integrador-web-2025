<?php
session_start();
if (!isset($_SESSION['idCliente'])) {
    session_destroy();
    echo "<script language='javascript' type='text/javascript'> window.location.href='login-cliente.php'</script>";
    exit();
} else if (isset($_SESSION['idTipoUsuario']) && $_SESSION['idTipoUsuario'] != 2) {
    echo "<script language='javascript' type='text/javascript'> window.location.href='login-cliente.php'</script>";
    exit();
}

require_once '../controller/controller-config.php';
$controllerConfig = new ControllerConfig();

$idUsuario = $_GET['u'];
$idConfeitaria = $_GET['c'];
$dadosConfeitaria = $controllerConfig->confeitaria->viewConfeitaria($idConfeitaria);

if (isset($_GET['action'])) {
    if ($_GET['action'] == 'fetch_mensagens') {
        $mensagens = $controllerConfig->chat->viewMensagem($_SESSION['idUsuario'], $idUsuario);
        header('Content-Type: application/json');
        echo json_encode($mensagens);
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controllerConfig->chat->addMensagem();
    header("Location: " . $_SERVER['REQUEST_URI']);
    exit();
}

$numProdutosCarrinho = 0;
if (isset($_SESSION['carrinho'])) {
    $numProdutosCarrinho = count($_SESSION['carrinho']);
}

$statusCliente = $controllerConfig->chat->online($idUsuario);

$controllerConfig->chat->marcarMensagensComoLidas($idUsuario, $_SESSION['idUsuario']);
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Chat</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@400;700&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../assets/css/style-menu.css">
    <link rel="stylesheet" href="../assets/css/style-chat.css">
</head>

<body>
    <div class="header">
        <div class="container">
            <header>
                <nav>
                    <div class="nav-container">
                        <a href="../view/index.php">
                            <img id="logo" src="../assets/img-site/logo.png" alt="Delicia Online">
                        </a>
                        <div class="greeting">
                            <?php
                            if (isset($_SESSION['nome'])) {
                                echo $_SESSION['nome'];
                            } else {
                                echo "Ola, Visitante";
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
                            <li><a href="pedir-suporte.php">Suporte</a></li>
                            <li><a href="../view/index.php">Voltar</a></li>
                        </ul>
                    </div>
                </nav>
            </header>
        </div>
    </div>

    <div class="chat-container">
        <div class="sidebar">
            <div class="profile-image">
                <img id="confeitaria-img"
                    src="../view-confeitaria/<?php echo $dadosConfeitaria[0]['img_confeitaria'] ?>"
                    alt="<?php echo $dadosConfeitaria[0]['nome_confeitaria'] ?>">
            </div>
            <h2 id="confeitaria-nome"><?php echo htmlspecialchars($dadosConfeitaria[0]['nome_confeitaria']); ?></h2>
            <p id="confeitaria-online-status">
                <?php
                if ($statusCliente == "online") {
                    echo '<span style="color: green;">● Online</span>';
                } else {
                    echo '<span style="color: gray;">○ ' . htmlspecialchars($statusCliente) . '</span>';
                }
                ?>
            </p>
        </div>

        <div class="chat-content">
            <div class="chat-header">
                Chat com <?php echo htmlspecialchars($dadosConfeitaria[0]['nome_confeitaria']); ?>
            </div>
            <div id="chat-messages" class="chat-messages" style="overflow-y: scroll; height: 400px;">
                <!-- Mensagens serão carregadas aqui via AJAX -->
            </div>
            <div class="chat-footer">
                <form method="POST" enctype="multipart/form-data" id="chat-form">
                    <input type="hidden" name="id" value="<?php echo $idUsuario; ?>">
                    <input type="text" placeholder="Digite sua mensagem" name="mensagem" id="mensagem" required>
                    <label for="img" class="upload-image-btn">
                        <i class="fas fa-image"></i>
                    </label>
                    <input type="file" name="img" id="img" accept="image/*" style="display: none;">
                    <button type="submit" name="enviar" id="send-button">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script>
        $(document).ready(function () {
            var isFirstLoad = true;

            function loadMensagens() {
                $.ajax({
                    url: 'chat-cliente.php?action=fetch_mensagens&u=<?php echo $idUsuario; ?>&c=<?php echo $idConfeitaria; ?>',
                    type: 'GET',
                    dataType: 'json',
                    success: function (data) {
                        var chatMessages = $('#chat-messages');
                        chatMessages.empty();

                        data.forEach(function (mensagem) {
                            var messageClass = mensagem.id_remetente == <?php echo $_SESSION['idUsuario']; ?> ? 'sent' : 'received';

                            var messageContent = mensagem.tipo === 'imagem'
                                ? `<img src="${mensagem.desc_mensagem}" alt="imagem" style="max-width: 200px; border-radius: 10px;">`
                                : `<p>${mensagem.desc_mensagem}</p>`;

                            var mensagemHtml = `
                        <div class="chat-message ${messageClass}">
                            <div class="chat-message-content">
                                ${messageContent}
                                <span style="font-size: 0.8em; color: gray;">
                                    ${formatDateTime(mensagem.hora_envio)}
                                </span>
                            </div>
                        </div>`;

                            chatMessages.append(mensagemHtml);
                        });

                        if (isFirstLoad || wasAtBottom) {
                            chatMessages.scrollTop(chatMessages[0].scrollHeight);
                            isFirstLoad = false;
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        console.log('Erro ao carregar mensagens: ' + textStatus + ' - ' + errorThrown);
                    }
                });
            }

            function formatDateTime(dateTimeString) {
                const date = new Date(dateTimeString);
                return date.toLocaleString('pt-BR');
            }

            // Carrega mensagens a cada 2 segundos
            loadMensagens();
            setInterval(loadMensagens, 2000);

            // Envio do formulário
            $('#chat-form').on('submit', function (e) {
                e.preventDefault();

                var mensagem = $('#mensagem').val().trim();
                var imagem = $('#img')[0].files.length > 0;

                // Só envia se houver mensagem ou imagem
                if (mensagem || imagem) {
                    var formData = new FormData(this);

                    $.ajax({
                        url: 'chat-cliente.php',
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function (response) {
                            $('#mensagem').val('');
                            $('#img').val(''); // Limpa o input de imagem
                            loadMensagens();
                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            console.log('Erro ao enviar mensagem: ' + textStatus + ' - ' + errorThrown);
                        }
                    });
                } else {
                    alert('Por favor, digite uma mensagem ou selecione uma imagem');
                }
            });

            // Atualiza automaticamente quando uma imagem é selecionada
            $('#img').change(function () {
                if (this.files.length > 0) {
                    // Adiciona um marcador indicando que é só imagem
                    $('#chat-form').append('<input type="hidden" name="only_image" value="1">');
                    $('#chat-form').submit();
                }
            });
        });
    </script>
</body>

</html>