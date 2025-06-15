<?php
require_once '../controller/controller-pedido.php';
$controllerPedido = new ControllerPedido();

session_start();
if (!isset($_SESSION['idCliente'])) {
    session_destroy();
    echo "<script language='javascript' type='text/javascript'> window.location.href='login-cliente.php'</script>";
} else if (isset($_SESSION['idTipoUsuario'])) {
    if ($_SESSION['idTipoUsuario'] != 2) {
        echo "<script language='javascript' type='text/javascript'> window.location.href='login-cliente.php'</script>";
    }
}

$idPedido = $_GET['i'];
$dadosPedido = $controllerPedido->viewPedido($idPedido);
$pedido = $dadosPedido['pedido'][0];
$itens = $dadosPedido['itens'];

// Formatar data
$dataPedido = date('d/m/Y - H:i', strtotime($pedido['data_pedido']));

// Calcular subtotal
$subtotal = 0;
foreach ($itens as $item) {
    $subtotal += $item['preco_unitario'] * $item['quantidade'];
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seu Pedido | Sua Loja</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style-visualizar-pdido.css">
</head>
<body>
    <div class="container">
        <div class="order-header">
            <div class="success-message">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                </svg>
                Pedido confirmado com sucesso!
            </div>
            <h1>Seu Pedido #<?php echo $pedido['id_pedido']; ?></h1>
            <p>Acompanhe os detalhes do seu pedido abaixo</p>
        </div>
        
        <div class="order-grid">
            <div class="order-main">
                <div class="order-section">
                    <h2 class="section-title">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
                            <line x1="3" y1="6" x2="21" y2="6"></line>
                            <path d="M16 10a4 4 0 0 1-8 0"></path>
                        </svg>
                        Detalhes do Pedido
                    </h2>
                    
                    <div class="order-info">
                        <div class="info-card">
                            <h3>Número do Pedido</h3>
                            <p>#<?php echo $pedido['id_pedido']; ?></p>
                        </div>
                        <div class="info-card">
                            <h3>Data do Pedido</h3>
                            <p><?php echo $dataPedido; ?></p>
                        </div>
                        <div class="info-card">
                            <h3>Status do Pedido</h3>
                            <p><?php echo ucfirst($pedido['tipo_status']); ?></p>
                        </div>
                        <div class="info-card">
                            <h3>Método de Pagamento</h3>
                            <p><?php echo $pedido['forma_pagamento']; ?></p>
                        </div>
                        <div class="info-card">
                            <h3>Confeitaria</h3>
                            <p><?php echo $item['nome_confeitaria']; ?></p>
                        </div>
                    </div>
                    
                    <div class="order-items">
                        <h3 class="section-title">Itens do Pedido</h3>
                        
                        <?php foreach ($itens as $item): ?>
                        <div class="order-item">
                            <div class="item-image">
                                <img src="../view-confeitaria/<?php echo $item['img_produto']; ?>" alt="<?php echo $item['nome_produto']; ?>">
                            </div>
                            <div class="item-details">
                                <h3><?php echo $item['nome_produto']; ?></h3>
                                <?php if (!empty($item['variacao'])): ?>
                                <p><?php echo $item['variacao']; ?></p>
                                <?php endif; ?>
                                <p>Quantidade: <?php echo $item['quantidade']; ?></p>
                            </div>
                            <div class="item-price">
                                R$ <?php echo number_format($item['preco_unitario'], 2, ',', '.'); ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <div class="order-section">
                    <h2 class="section-title">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                            <circle cx="12" cy="10" r="3"></circle>
                        </svg>
                        Endereço de Entrega
                    </h2>
                    
                    <div class="delivery-address">
                        <h3>Local</h3>
                        <p><?php echo $pedido['logradouro']; ?>, <?php echo $pedido['numero']; ?> <?php echo !empty($pedido['complemento']) ? '- ' . $pedido['complemento'] : ''; ?></p>
                        <p>Bairro <?php echo $pedido['bairro']; ?></p>
                        <p><?php echo $pedido['cidade']; ?> - <?php echo $pedido['estado']; ?></p>
                        <p>CEP: <?php echo $pedido['cep']; ?></p>
                    </div>
                    
                    <h3 class="section-title">Método de Entrega</h3>
                    <div class="payment-method">
                        <div class="icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="1" y="3" width="15" height="13"></rect>
                                <polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon>
                                <circle cx="5.5" cy="18.5" r="2.5"></circle>
                                <circle cx="18.5" cy="18.5" r="2.5"></circle>
                            </svg>
                        </div>
                        <div class="info">
                            <h3>Entrega Padrão</h3>
                            <p>Previsão de entrega: <?php 
                                $dataEntrega = date('d/m/Y', strtotime($pedido['data_pedido'] . ' + 5 days'));
                                echo $dataEntrega;
                            ?></p>
                        </div>
                    </div>
                </div>
                
                <div class="order-section">
                    <h2 class="section-title">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect>
                            <line x1="1" y1="10" x2="23" y2="10"></line>
                        </svg>
                        Método de Pagamento
                    </h2>
                    
                    <div class="payment-method">
                        <div class="icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect>
                                <line x1="1" y1="10" x2="23" y2="10"></line>
                            </svg>
                        </div>
                        <div class="info">
                            <h3><?php echo $pedido['forma_pagamento']; ?></h3>
                            <?php if ($pedido['forma_pagamento'] == 'Cartão de Crédito'): ?>
                            <p>•••• •••• •••• <?php echo substr($pedido['numero_cartao'], -4); ?></p>
                            <?php endif; ?>
                            <p>Valor total: R$ <?php echo number_format($pedido['valor_total'], 2, ',', '.'); ?></p>
                        </div>
                    </div>
                </div>
                
                <div class="order-section">
                    <h2 class="section-title">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                        Acompanhamento do Pedido
                    </h2>
                    
                    <div class="order-timeline">
                        <div class="timeline-item <?php echo in_array($pedido['tipo_status'], ['processando', 'confirmado', 'enviado', 'entregue']) ? 'completed' : ''; ?>">
                            <div class="timeline-content">
                                <h4>Pedido realizado</h4>
                                <p><?php echo $dataPedido; ?></p>
                            </div>
                        </div>
                        
                        <div class="timeline-item <?php echo in_array($pedido['tipo_status'], ['confirmado', 'enviado', 'entregue']) ? 'completed' : ($pedido['tipo_status'] == 'processando' ? 'active' : ''); ?>">
                            <div class="timeline-content">
                                <h4>Pagamento confirmado</h4>
                                <p><?php echo $pedido['tipo_status'] == 'processando' ? $dataPedido : ''; ?></p>
                            </div>
                        </div>
                        
                        <div class="timeline-item <?php echo in_array($pedido['tipo_status'], ['enviado', 'entregue']) ? 'completed' : ($pedido['tipo_status'] == 'confirmado' ? 'active' : ''); ?>">
                            <div class="timeline-content">
                                <h4>Preparando para envio</h4>
                                <p><?php echo $pedido['tipo_status'] == 'confirmado' ? date('d/m/Y - H:i', strtotime($pedido['data_pedido'] . ' + 1 day')) : 'Em breve'; ?></p>
                            </div>
                        </div>
                        
                        <div class="timeline-item <?php echo $pedido['tipo_status'] == 'entregue' ? 'completed' : ($pedido['tipo_status'] == 'enviado' ? 'active' : ''); ?>">
                            <div class="timeline-content">
                                <h4>Enviado</h4>
                                <p><?php echo $pedido['tipo_status'] == 'enviado' ? date('d/m/Y - H:i', strtotime($pedido['data_pedido'] . ' + 2 days')) : 'Em breve'; ?></p>
                            </div>
                        </div>
                        
                        <div class="timeline-item <?php echo $pedido['tipo_status'] == 'entregue' ? 'completed' : ''; ?>">
                            <div class="timeline-content">
                                <h4>Entregue</h4>
                                <p><?php echo $pedido['tipo_status'] == 'entregue' ? date('d/m/Y - H:i', strtotime($pedido['data_pedido'] . ' + 5 days')) : 'Em breve'; ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="order-summary">
                <div class="summary-section">
                    <h2 class="summary-title">Resumo do Pedido</h2>
                    
                    <div class="summary-item">
                        <span>Subtotal (<?php echo count($itens); ?> itens)</span>
                        <span>R$ <?php echo number_format($subtotal, 2, ',', '.'); ?></span>
                    </div>
                    
                    <div class="summary-item">
                        <span>Frete</span>
                        <span>R$ <?php echo number_format($pedido['valor_frete'], 2, ',', '.'); ?></span>
                    </div>
                    
                    <div class="summary-item">
                        <span>Desconto</span>
                        <span>- R$ <?php echo number_format($pedido['desconto'], 2, ',', '.'); ?></span>
                    </div>
                    
                    <div class="summary-item total">
                        <span>Total</span>
                        <span>R$ <?php echo number_format($pedido['valor_total'], 2, ',', '.'); ?></span>
                    </div>
                    
                    <div class="action-btns">
                        <a href="#" class="btn btn-primary">Realizar Pagamento</a>
                        <a href="../view/index.php" class="btn btn-outline">Voltar às Compras</a>
                    </div>
                </div>
                
                <div class="summary-section">
                    <h2 class="section-title">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                            <polyline points="22,6 12,13 2,6"></polyline>
                        </svg>
                        Precisa de ajuda?
                    </h2>
                    <p style="margin-bottom: 1rem;">Entre em contato conosco se tiver alguma dúvida sobre seu pedido.</p>
                    <a href="contato.php" class="btn btn-outline">Central de Ajuda</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>