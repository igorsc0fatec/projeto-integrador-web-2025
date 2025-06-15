<?php
require_once '../controller/controller-config.php';
$configController = new ControllerConfig();

session_start();
if (!isset($_SESSION['idCliente'])) {
    session_destroy();
    echo "<script language='javascript' type='text/javascript'> window.location.href='login-cliente.php'</script>";
    exit;
} else if (isset($_SESSION['idTipoUsuario'])) {
    if ($_SESSION['idTipoUsuario'] != 2) {
        echo "<script language='javascript' type='text/javascript'> window.location.href='login-cliente.php'</script>";
        exit;
    }
}

$idPedido = $_GET['i'] ?? null;
if (!$idPedido) {
    echo "<script language='javascript' type='text/javascript'> window.location.href='meus-pedidos.php'</script>";
    exit;
}

$pedido = $configController->pedidoPersonalizado->viewPedidoPersonalizado($idPedido);
$pedido = $pedido[0]; // Acessa o primeiro elemento do array

// Formatar data
$dataPedido = date('d/m/Y - H:i', strtotime($pedido['data_pedido']));

// Calcular subtotal (valor total + desconto - frete)
$subtotal = ($pedido['valor_total'] ?? 0) + ($pedido['desconto'] ?? 0) - ($pedido['frete'] ?? 0);

if (isset($_POST['acompanhar'])) {
    $c = $pedido['tc_id_confeitaria'];
    $u = $pedido['id_usuario'];
    $_POST['id'] = $u;
    $configController->chat->addMensagem();
    echo "<script language='javascript' type='text/javascript'> window.location.href='chat-cliente.php?u=$u&c=$c'</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seu Pedido Personalizado | Sua Loja</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style-visualizar-pdido.css">
</head>

<body>
    <div class="container">
        <div class="order-header">
            <div class="success-message">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                </svg>
                Pedido confirmado com sucesso!
            </div>
            <h1>Seu Pedido Personalizado #<?php echo htmlspecialchars($pedido['id_pedido_personalizado'] ?? ''); ?></h1>
            <p>Acompanhe os detalhes do seu pedido personalizado abaixo</p>
        </div>

        <div class="order-grid">
            <div class="order-main">
                <div class="order-section">
                    <h2 class="section-title">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
                            <line x1="3" y1="6" x2="21" y2="6"></line>
                            <path d="M16 10a4 4 0 0 1-8 0"></path>
                        </svg>
                        Detalhes do Pedido
                    </h2>

                    <div class="order-info">
                        <div class="info-card">
                            <h3>Número do Pedido</h3>
                            <p>#<?php echo htmlspecialchars($pedido['id_pedido_personalizado'] ?? 'N/A'); ?></p>
                        </div>
                        <div class="info-card">
                            <h3>Data do Pedido</h3>
                            <p><?php echo htmlspecialchars($dataPedido ?? 'N/A'); ?></p>
                        </div>
                        <div class="info-card">
                            <h3>Status do Pedido</h3>
                            <p><?php echo isset($pedido['status']) ? ucfirst($pedido['status']) : 'N/A'; ?></p>
                        </div>
                        <div class="info-card">
                            <h3>Método de Pagamento</h3>
                            <p><?php echo htmlspecialchars($pedido['forma_pagamento'] ?? 'N/A'); ?></p>
                        </div>
                        <div class="info-card">
                            <h3>Confeitaria</h3>
                            <p><?php echo $pedido['nome_confeitaria']; ?></p>
                        </div>
                    </div>

                    <div class="order-items">
                        <h3 class="section-title">Detalhes do Pedido Personalizado</h3>

                        <div class="order-item">
                            <?php if (!empty($pedido['img_personalizado'])): ?>
                                <div class="item-image">
                                    <img src="../view-confeitaria/<?php echo htmlspecialchars($pedido['img_personalizado']); ?>"
                                        alt="<?php echo htmlspecialchars($pedido['nome_personalizado'] ?? ''); ?>">
                                </div>
                            <?php endif; ?>
                            <div class="item-details">
                                <h3><?php echo htmlspecialchars($pedido['nome_personalizado'] ?? 'Produto Personalizado'); ?>
                                </h3>
                                <?php if (!empty($pedido['desc_personalizado'])): ?>
                                    <p><?php echo htmlspecialchars($pedido['desc_personalizado']); ?></p>
                                <?php endif; ?>
                                <div class="confeitaria-info"
                                    style="background: #f8f8f8; padding: 10px; border-radius: 8px; margin: 10px 0;">
                                    <h4 style="margin: 0 0 5px 0;">Confeitaria Responsável:</h4>
                                    <p style="margin: 5px 0; font-weight: 500;">
                                        <?php echo htmlspecialchars($pedido['nome_confeitaria'] ?? 'N/A'); ?>
                                    </p>
                                    <?php if (!empty($pedido['log_confeitaria'])): ?>
                                        <p style="margin: 5px 0;">
                                            Endereço: <?php echo htmlspecialchars($pedido['log_confeitaria']); ?>,
                                            <?php echo htmlspecialchars($pedido['num_local']); ?> -
                                            <?php echo htmlspecialchars($pedido['bairro_confeitaria']); ?>
                                        </p>
                                    <?php endif; ?>
                                </div>

                                <p>Peso: <?php echo htmlspecialchars($pedido['peso'] ?? '0'); ?> kg</p>

                                <div class="item-details">

                                    <?php if (!empty($pedido['massa_ativa'])): ?>
                                        <p>Massa:
                                            <?php echo htmlspecialchars($pedido['id_massa'] ?? 'Não especificado'); ?>
                                        </p>
                                    <?php endif; ?>

                                    <?php if (!empty($pedido['recheio_ativa'])): ?>
                                        <p>Recheio:
                                            <?php echo htmlspecialchars($pedido['id_recheio'] ?? 'Não especificado'); ?>
                                        </p>
                                    <?php endif; ?>

                                    <?php if (!empty($pedido['cobertura_ativa'])): ?>
                                        <p>Cobertura:
                                            <?php echo htmlspecialchars($pedido['id_cobertura'] ?? 'Não especificado'); ?>
                                        </p>
                                    <?php endif; ?>

                                    <?php if (!empty($pedido['decoracao_ativa'])): ?>
                                        <p>Decoração:
                                            <?php echo htmlspecialchars($pedido['id_decoracao'] ?? 'Não especificado'); ?>
                                        </p>
                                    <?php endif; ?>

                                    <?php if (!empty($pedido['formato_ativa'])): ?>
                                        <p>Formato:
                                            <?php echo htmlspecialchars($pedido['id_formato'] ?? 'Não especificado'); ?>
                                        </p>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="item-price">
                                R$
                                <?php echo isset($pedido['valor_total']) ? number_format($pedido['valor_total'], 2, ',', '.') : '0,00'; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="order-section">
                    <h2 class="section-title">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                            <circle cx="12" cy="10" r="3"></circle>
                        </svg>
                        Endereço da Confeitaria
                    </h2>

                    <div class="delivery-address">
                        <h3>Local</h3>
                        <p>Endereço <?php echo $pedido['log_confeitaria']; ?>, <?php echo $pedido['num_local']; ?>
                            <?php echo !empty($pedido['complemento']) ? '- ' . $pedido['complemento'] : ''; ?>
                        </p>
                        <p>Bairro <?php echo $pedido['bairro_confeitaria']; ?></p>
                        <p>Cidade <?php echo $pedido['cidade_confeitaria']; ?> - <?php echo $pedido['uf_confeitaria']; ?></p>
                        <p>CEP: <?php echo $pedido['cep_confeitaria']; ?></p>
                    </div>

                    Endereço de Entrega
                    </h2>

                    <div class="delivery-address">
                        <h3>Local</h3>
                        <p>Endereço <?php echo $pedido['log_cliente'] ?? 'N/A'; ?>, <?php echo $pedido['num_local'] ?? 'N/A'; ?>
                            <?php echo !empty($pedido['complemento']) ? '- ' . $pedido['complemento'] : ''; ?>
                        </p>
                        <p>Bairro <?php echo $pedido['bairro_cliente'] ?? 'N/A'; ?></p>
                        <p>Cidade <?php echo $pedido['cidade_cliente'] ?? 'N/A'; ?> - <?php echo $pedido['uf_cliente'] ?? 'N/A'; ?></p>
                        <p>CEP: <?php echo $pedido['cep_cliente'] ?? 'N/A'; ?></p>
                    </div>

                    <!--<h3 class="section-title">Método de Entrega</h3>
                    <div class="info">
                        <h3>Entrega Padrão</h3>
                        <p>Previsão de entrega:
                        </p>
                    </div>-->
                </div>

                <div class="order-section">
                    <h2 class="section-title">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect>
                            <line x1="1" y1="10" x2="23" y2="10"></line>
                        </svg>
                        Método de Pagamento
                    </h2>

                    <div class="payment-method">
                        <div class="icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round">
                                <rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect>
                                <line x1="1" y1="10" x2="23" y2="10"></line>
                            </svg>
                        </div>
                        <div class="info">
                            <h3><?php echo $pedido['forma_pagamento']; ?></h3>
                            <p>Valor total: R$ <?php echo number_format($pedido['valor_total'], 2, ',', '.'); ?></p>
                        </div>
                    </div>
                </div>

                <div class="order-section">
                    <h2 class="section-title">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                        Acompanhamento do Pedido
                    </h2>

                    <div class="order-timeline">
                        <div
                            class="timeline-item <?php echo in_array($pedido['tipo_status'], ['processando', 'confirmado', 'enviado', 'entregue']) ? 'completed' : ''; ?>">
                            <div class="timeline-content">
                                <h4>Pedido realizado</h4>
                                <p><?php echo $dataPedido; ?></p>
                            </div>
                        </div>

                        <div
                            class="timeline-item <?php echo in_array($pedido['tipo_status'], ['confirmado', 'enviado', 'entregue']) ? 'completed' : ($pedido['tipo_status'] == 'processando' ? 'active' : ''); ?>">
                            <div class="timeline-content">
                                <h4>Pagamento confirmado</h4>
                                <p><?php echo $pedido['tipo_status'] == 'processando' ? $dataPedido : ''; ?></p>
                            </div>
                        </div>

                        <div
                            class="timeline-item <?php echo in_array($pedido['tipo_status'], ['enviado', 'entregue']) ? 'completed' : ($pedido['tipo_status'] == 'confirmado' ? 'active' : ''); ?>">
                            <div class="timeline-content">
                                <h4>Preparando para envio</h4>
                                <p><?php echo $pedido['tipo_status'] == 'confirmado' ? date('d/m/Y - H:i', strtotime($pedido['data_pedido'] . ' + 1 day')) : 'Em breve'; ?>
                                </p>
                            </div>
                        </div>

                        <div
                            class="timeline-item <?php echo $pedido['tipo_status'] == 'entregue' ? 'completed' : ($pedido['tipo_status'] == 'enviado' ? 'active' : ''); ?>">
                            <div class="timeline-content">
                                <h4>Enviado</h4>
                                <p><?php echo $pedido['tipo_status'] == 'enviado' ? date('d/m/Y - H:i', strtotime($pedido['data_pedido'] . ' + 2 days')) : 'Em breve'; ?>
                                </p>
                            </div>
                        </div>

                        <div class="timeline-item <?php echo $pedido['tipo_status'] == 'entregue' ? 'completed' : ''; ?>">
                            <div class="timeline-content">
                                <h4>Entregue</h4>
                                <p><?php echo $pedido['tipo_status'] == 'entregue' ? date('d/m/Y - H:i', strtotime($pedido['data_pedido'] . ' + ' . $pedido['limite_entrega'] . ' days')) : 'Em breve'; ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="order-summary">
                <div class="summary-section">
                    <h2 class="summary-title">Resumo do Pedido</h2>

                    <div class="summary-item">
                        <span>Subtotal</span>
                        <span>R$ <?php echo number_format($subtotal, 2, ',', '.'); ?></span>
                    </div>

                    <div class="summary-item">
                        <span>Frete</span>
                        <span>R$
                            <?php echo isset($pedido['frete']) ? number_format($pedido['frete'], 2, ',', '.') : '0,00'; ?></span>
                    </div>

                    <div class="summary-item">
                        <span>Desconto</span>
                        <span>- R$
                            <?php echo isset($pedido['desconto']) ? number_format($pedido['desconto'], 2, ',', '.') : '0,00'; ?></span>
                    </div>

                    <div class="summary-item total">
                        <span>Total</span>
                        <span>R$
                            <?php echo isset($pedido['valor_total']) ? number_format($pedido['valor_total'], 2, ',', '.') : '0,00'; ?></span>
                    </div>

                    <div class="action-btns">
                        <?php
                        $mensagem = 'Olá, Gostaria de saber como está o andamento do meu pedido Nº ' . htmlspecialchars($pedido['id_pedido_personalizado']);
                        ?>
                        <div class="action-btns">
                            <form action="" method="POST">
                                <input type="hidden" name="mensagem" value="<?php echo $mensagem; ?>">
                                <button type="submit" name="acompanhar" id="acompanhar"
                                    class="btn btn-primary">Acompanhar Entrega</button>
                            </form>
                            <a href="../view/index.php" class="btn btn-outline">Voltar às Compras</a>
                        </div>
                    </div>
                </div>

                <div class="summary-section">
                    <h2 class="section-title">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z">
                            </path>
                            <polyline points="22,6 12,13 2,6"></polyline>
                        </svg>
                        Precisa de ajuda?
                    </h2>
                    <p style="margin-bottom: 1rem;">Entre em contato conosco se tiver alguma dúvida sobre seu pedido.
                    </p>
                    <a href="contato.php" class="btn btn-outline">Central de Ajuda</a>
                </div>
            </div>
        </div>
    </div>
</body>

</html>