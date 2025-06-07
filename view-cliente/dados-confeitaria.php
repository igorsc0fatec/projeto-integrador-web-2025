<?php
session_start();
$idConfeitaria = $_GET['c'];
$idUsuario = $_GET['u'];

require_once '../controller/controller-config.php';
$configController = new ControllerConfig();
$confeitaria = $configController->confeitaria->viewPerfilConfeitaria($idConfeitaria);
$telefones = $configController->telefone->viewTelefone($idUsuario);

$numProdutosCarrinho = 0;
if (isset($_SESSION['carrinho'])) {
    $numProdutosCarrinho = count($_SESSION['carrinho']);
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $confeitaria[0]['nome_confeitaria']; ?> - Delivery</title>

    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Playfair+Display:wght@400;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- CSS -->
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css" />
    <style>
        :root {
            --ifood-red: #ea1d2c;
            --ifood-dark-red: #c2121f;
            --ifood-gray: #f7f7f7;
            --ifood-dark-gray: #3f3e3e;
            --ifood-text: #3e3e3e;
            --ifood-light-text: #717171;
            --ifood-border: #e4e4e4;
            --ifood-green: #50a773;
            --ifood-yellow: #f6d553;
            --shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            --shadow-hover: 0 4px 12px rgba(234, 29, 44, 0.15);

            /* Suas variáveis originais */
            --primary-color: #8381ac;
            --secondary-color: #726fa7;
            --hover-color: #5d5b99;
            --text-color: #fff;
            --border-color: #7070c24d;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            color: var(--ifood-text);
            background-color: var(--ifood-gray);
            line-height: 1.5;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            padding-top: 75px;
            /* Altura da navbar fixa */
        }

        /* NAVBAR ORIGINAL COM AJUSTES - COMEÇO */
        nav {
            position: fixed;
            top: 0;
            left: 0;
            height: 75px;
            width: 100%;
            z-index: 1000;
            background-color: var(--primary-color);
            border-bottom: 1px solid var(--border-color);
            box-shadow: var(--shadow);
        }

        .nav-container {
            max-width: 1200px;
            position: relative;
            margin-left: auto;
            margin-right: auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px;
        }

        /* Logo */
        #logo {
            width: 40px;
            height: 40px;
            margin: 15px 0;
            transition: transform 0.3s ease-in-out;
        }

        #logo:hover {
            transform: scale(1.1);
        }

        /* Saudação */
        .greeting {
            font-size: 14px;
            /* Tamanho reduzido */
            color: var(--text-color);
            margin-right: auto;
            padding-left: 20px;
        }

        .nav-container ul {
            list-style: none;
            display: flex;
            align-items: center;
            margin: 0;
            padding: 0;
        }

        .nav-container ul li {
            margin: 0 10px;
            /* Espaçamento reduzido */
        }

        .nav-container li a,
        .nav-container li form button {
            text-decoration: none;
            color: var(--text-color);
            padding: 8px 5px;
            /* Padding reduzido */
            border-radius: 5px;
            transition: background-color 0.3s ease-in-out, color 0.3s ease-in-out;
            background-color: transparent;
            border: none;
            cursor: pointer;
            font-size: 14px;
            /* Tamanho reduzido */
        }

        .nav-container li a:hover,
        .nav-container li form button:hover {
            background-color: var(--hover-color);
            color: var(--text-color);
        }

        /* Botão de menu para dispositivos móveis */
        .nav-container .btn-menumobile {
            display: none;
            background-color: var(--secondary-color);
            color: white;
            border: none;
            padding: 8px;
            /* Reduzido */
            border-radius: 5px;
            cursor: pointer;
            font-size: 20px;
            /* Reduzido */
            transition: background-color 0.3s ease-in-out;
        }

        .nav-container .btn-menumobile:hover {
            background-color: var(--hover-color);
        }

        /* Estilo para o menu responsivo */
        @media (max-width: 768px) {
            .nav-container ul {
                display: none;
                flex-direction: column;
                position: absolute;
                top: 75px;
                left: 0;
                width: 100%;
                background-color: var(--primary-color);
                text-align: center;
                box-shadow: var(--shadow);
                transition: opacity 0.3s ease-in-out, transform 0.3s ease-in-out;
                opacity: 0;
                transform: translateY(-10px);
            }

            .nav-container ul.active {
                display: flex;
                opacity: 1;
                transform: translateY(0);
            }

            .nav-container ul li {
                margin: 8px 0;
                /* Reduzido */
            }

            .nav-container .btn-menumobile {
                display: block;
            }
        }

        /* Barra de pesquisa */
        .search-container {
            display: flex;
            justify-content: flex-end;
            flex-grow: 1;
            margin: 0 15px;
            /* Reduzido */
            max-width: 400px;
            /* Reduzido */
        }

        .search-container form {
            display: flex;
            align-items: center;
            width: 100%;
            position: relative;
        }

        .search-input {
            padding: 8px 35px 8px 12px;
            /* Reduzido */
            border: 2px solid #ddd;
            border-radius: 25px;
            width: 100%;
            font-size: 14px;
            /* Reduzido */
            outline: none;
            transition: all 0.3s ease;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .search-input:focus {
            border-color: #6c5ce7;
            box-shadow: 0 2px 8px rgba(108, 92, 231, 0.3);
        }

        .search-button {
            position: absolute;
            right: 8px;
            /* Reduzido */
            background: none;
            border: none;
            color: #6c5ce7;
            font-size: 16px;
            /* Reduzido */
            cursor: pointer;
            transition: color 0.3s ease;
        }

        .search-button:hover {
            color: #5a4bcf;
        }

        .search-button i {
            font-size: 16px;
            /* Reduzido */
        }

        /* NAVBAR ORIGINAL COM AJUSTES - FIM */

        /* Restaurant Header */
        .restaurant-header {
            background: white;
            padding-bottom: 20px;
            border-bottom: 1px solid var(--ifood-border);
        }

        .restaurant-header-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .restaurant-cover {
            height: 200px;
            width: 100%;
            object-fit: cover;
            border-radius: 4px;
            margin-bottom: 20px;
        }

        .restaurant-info {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
            position: relative;
            /* Para posicionar o botão de chat */
        }

        .restaurant-logo {
            width: 80px;
            height: 80px;
            border-radius: 8px;
            object-fit: cover;
            border: 2px solid white;
            box-shadow: var(--shadow);
            margin-top: -40px;
            background: white;
        }

        .restaurant-details {
            flex: 1;
        }

        .restaurant-name-container {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 5px;
        }

        .restaurant-name {
            font-size: 24px;
            font-weight: 800;
            color: var(--ifood-dark-gray);
        }

        .chat-button-header {
            background: var(--ifood-red);
            color: white;
            border: none;
            border-radius: 4px;
            padding: 8px 15px;
            font-weight: 700;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 5px;
            text-decoration: none;
        }

        .chat-button-header:hover {
            background: var(--ifood-dark-red);
        }

        .restaurant-category {
            font-size: 14px;
            color: var(--ifood-light-text);
            margin-bottom: 15px;
        }

        /* Novo container para informações da empresa */
        .restaurant-info-container {
            background: white;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 30px;
            box-shadow: var(--shadow);
            border: 1px solid var(--ifood-border);
        }

        .restaurant-info-title {
            font-weight: 700;
            margin-bottom: 15px;
            font-size: 16px;
            color: var(--ifood-dark-gray);
        }

        .restaurant-contact {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .contact-item {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            font-size: 14px;
        }

        .contact-item i {
            color: var(--ifood-red);
            margin-top: 2px;
            min-width: 20px;
            text-align: center;
        }

        .address {
            line-height: 1.6;
        }

        .payment-methods {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 15px;
        }

        .payment-method {
            background: var(--ifood-gray);
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
        }

        .restaurant-meta {
            display: flex;
            gap: 15px;
            margin-bottom: 10px;
            /* Movido para cima das avaliações */
            flex-wrap: wrap;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 14px;
            color: var(--ifood-light-text);
        }

        .meta-item i {
            font-size: 16px;
        }

        .restaurant-rating {
            display: flex;
            align-items: center;
            gap: 5px;
            margin-bottom: 10px;
        }

        .rating-stars {
            color: var(--ifood-yellow);
            font-size: 14px;
        }

        .rating-value {
            font-weight: 700;
            font-size: 14px;
        }

        .rating-count {
            font-size: 13px;
            color: var(--ifood-light-text);
        }

        .delivery-info {
            background: white;
            border-radius: 8px;
            padding: 15px;
            margin-top: 15px;
            border: 1px solid var(--ifood-border);
        }

        .delivery-info-title {
            font-weight: 700;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .delivery-time {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .delivery-time-item {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 14px;
        }

        .delivery-time-item i {
            color: var(--ifood-red);
        }

        /* Main Content */
        .main-container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
        }

        /* Menu Categories */
        .menu-category {
            margin-bottom: 40px;
        }

        .category-title {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid var(--ifood-border);
            color: var(--ifood-dark-gray);
        }

        .category-description {
            font-size: 14px;
            color: var(--ifood-light-text);
            margin-bottom: 20px;
        }

        /* Menu Items */
        .menu-items {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }

        .menu-item {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: var(--shadow);
            transition: all 0.2s;
            border: 1px solid var(--ifood-border);
        }

        .menu-item:hover {
            box-shadow: var(--shadow-hover);
            transform: translateY(-2px);
        }

        .menu-item-image {
            height: 160px;
            width: 100%;
            object-fit: cover;
        }

        .menu-item-details {
            padding: 15px;
        }

        .menu-item-name {
            font-weight: 700;
            margin-bottom: 5px;
            font-size: 16px;
        }

        .menu-item-description {
            font-size: 14px;
            color: var(--ifood-light-text);
            margin-bottom: 10px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .menu-item-price {
            font-weight: 700;
            font-size: 18px;
            color: var(--ifood-dark-gray);
            margin-bottom: 15px;
        }

        .menu-item-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .add-to-cart {
            background: var(--secondary-color);
            /* Cor alterada para combinar com a navbar */
            color: white;
            border: none;
            border-radius: 4px;
            padding: 8px 15px;
            font-weight: 700;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .add-to-cart:hover {
            background: var(--hover-color);
            /* Cor de hover alterada */
        }

        .customize-btn {
            background: none;
            border: 1px solid var(--secondary-color);
            /* Cor alterada */
            color: var(--secondary-color);
            /* Cor alterada */
            border-radius: 4px;
            padding: 8px 15px;
            font-weight: 700;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .customize-btn:hover {
            background: rgba(114, 111, 167, 0.05);
            /* Cor alterada */
        }

        /* Floating Cart */
        .floating-cart {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: var(--secondary-color);
            /* Cor alterada */
            color: white;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 15px rgba(114, 111, 167, 0.3);
            /* Cor alterada */
            cursor: pointer;
            z-index: 100;
        }

        .floating-cart:hover {
            background: var(--hover-color);
            /* Cor de hover alterada */
        }

        .floating-cart-count {
            position: absolute;
            top: -5px;
            right: -5px;
            background: white;
            color: var(--secondary-color);
            /* Cor alterada */
            border-radius: 50%;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 12px;
            border: 2px solid var(--secondary-color);
            /* Cor alterada */
        }

        /* Responsive */
        @media (max-width: 992px) {
            .menu-items {
                grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            }
        }

        @media (max-width: 768px) {
            .restaurant-info {
                flex-direction: column;
            }

            .restaurant-logo {
                margin-top: 0;
                width: 60px;
                height: 60px;
            }

            .restaurant-name-container {
                flex-direction: column;
                gap: 10px;
            }

            .chat-button-header {
                align-self: flex-start;
            }

            .menu-items {
                grid-template-columns: 1fr;
            }
        }

        /* Footer */
        footer {
            text-align: center;
            padding: 20px;
            background-color: var(--primary-color);
            color: white;
            position: relative;
            margin-top: auto;
            width: 100%;
            box-shadow: var(--shadow);
        }

        main {
            flex: 1;
            margin-top: 10px;
            margin-bottom: 0;
        }
    </style>
</head>

<body>
    <!-- Navbar Original com Ajustes -->
    <nav>
        <div class="nav-container">
            <a href="../view/index.php">
                <img id="logo" src="../assets/img-site/logo.png" alt="Logo">
            </a>

            <span class="greeting">Bem-vindo(a)!</span>

            <div class="search-container">
                <form action="#" method="get">
                    <input type="text" class="search-input" placeholder="Pesquisar...">
                    <button type="submit" class="search-button">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
            </div>

            <i class="fas fa-bars btn-menumobile"></i>

            <ul>
                <li style="position: relative;">
                    <a href="../view-cliente/carrinho.php">
                        <i class="fa fa-shopping-cart"></i>
                        <?php if ($numProdutosCarrinho > 0): ?>
                            <span style="
                                position: absolute;
                                top: -8px;
                                right: -8px;
                                background: var(--ifood-red);
                                color: white;
                                border-radius: 50%;
                                width: 18px;
                                height: 18px;
                                display: flex;
                                align-items: center;
                                justify-content: center;
                                font-size: 11px;
                                font-weight: 700;
                            "><?php echo $numProdutosCarrinho; ?></span>
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
    <!-- Restaurant Header -->

    <div class="restaurant-header">
        <div class="restaurant-header-container">
            <img src='../view-confeitaria/<?php echo $confeitaria[0]['img_confeitaria']; ?>' class="restaurant-cover" alt='<?php echo $confeitaria[0]['nome_confeitaria']; ?>'>

            <div class="restaurant-info">
                <img src='../view-confeitaria/<?php echo $confeitaria[0]['img_confeitaria']; ?>' class="restaurant-logo" alt='<?php echo $confeitaria[0]['nome_confeitaria']; ?>'>

                <div class="restaurant-details">
                    <div class="restaurant-name-container">
                        <h1 class="restaurant-name"><?php echo $confeitaria[0]['nome_confeitaria']; ?></h1>
                        <a href="chat-cliente.php?u=<?php echo $confeitaria[0]['id_usuario']; ?>&c=<?php echo $confeitaria[0]['id_confeitaria']; ?>" class="chat-button-header">
                            <i class="fas fa-comment-dots"></i> Chat
                        </a>
                    </div>

                    <div class="restaurant-category">Confeitaria • Doces • Bolos</div>

                    <!-- Container com informações da empresa -->
                    <div class="restaurant-info-container">
                        <h3 class="restaurant-info-title">Informações da Confeitaria</h3>
                        <div class="restaurant-contact">
                            <div class="contact-item">
                                <i class="fas fa-map-marker-alt"></i>
                                <div class="address">
                                    <?php echo $confeitaria[0]['log_confeitaria'] . ', Nº ' . $confeitaria[0]['num_local'] . '<br>' . $confeitaria[0]['bairro_confeitaria'] . ', ' . $confeitaria[0]['cidade_confeitaria'] . ' - ' . $confeitaria[0]['uf_confeitaria'] . '<br>CEP: ' . $confeitaria[0]['cep_confeitaria']; ?>
                                </div>
                            </div>
                            <div class="contact-item">
                                <i class="fas fa-envelope"></i>
                                <span><?php echo $confeitaria[0]['email_usuario']; ?></span>
                            </div>
                            <?php foreach ($telefones as $fone) { ?>
                                <div class="contact-item">
                                    <?php if ($fone['tipo_telefone'] == 'Whats App'): ?>
                                        <i class="fab fa-whatsapp" style="color: #25D366;"></i>
                                    <?php elseif ($fone['tipo_telefone'] == 'Fixo'): ?>
                                        <i class="fas fa-phone-alt"></i>
                                    <?php else: ?>
                                        <i class="fas fa-mobile-alt"></i>
                                    <?php endif; ?>
                                    <span><?php echo $fone['num_telefone']; ?></span>
                                </div>
                            <?php } ?>
                        </div>

                        <h3 class="restaurant-info-title" style="margin-top: 20px;">Formas de pagamento</h3>
                        <div class="payment-methods">
                            <span class="payment-method">Cartão</span>
                            <span class="payment-method">Dinheiro</span>
                            <span class="payment-method">PIX</span>
                        </div>
                    </div>

                    <div class="restaurant-meta">
                        <div class="meta-item">
                            <i class="fas fa-clock"></i>
                            <span>30-40 min</span>
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-tag"></i>
                            <span>R$ 5,00</span>
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <span>1.5 km</span>
                        </div>
                    </div>

                    <div class="restaurant-rating">
                        <div class="rating-stars">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star-half-alt"></i>
                        </div>
                        <div class="rating-value">4.5</div>
                        <div class="rating-count">(150 avaliações)</div>
                    </div>
                </div>
            </div>

            <div class="delivery-info">
                <div class="delivery-info-title">
                    <i class="fas fa-motorcycle" style="color: var(--ifood-red);"></i>
                    <span>Informações de entrega</span>
                </div>
                <div class="delivery-time">
                    <div class="delivery-time-item">
                        <i class="fas fa-clock"></i>
                        <span>Entrega: 30-40 min</span>
                    </div>
                    <div class="delivery-time-item">
                        <i class="fas fa-tag"></i>
                        <span>Taxa de entrega: R$ 5,00</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-container">
        <!-- Content -->
        <main class="content">
            <?php
            $tiposProduto = $configController->personalizado->viewMostraTipoProdutos($idConfeitaria);
            if (isset($_SESSION['idCliente'])) {
                $idCliente = $_SESSION['idCliente'];
            } else {
                $idCliente = 0;
            }

            foreach ($tiposProduto as $tipoProduto) {
                $idTipoProduto = $tipoProduto['desc_tipo_produto'];
                $produtos = $configController->personalizado->viewPersonalizados($idTipoProduto, $idCliente, $idConfeitaria);
                $nomeCategoria = $tipoProduto['desc_tipo_produto'];
            ?>

                <section class="menu-category">
                    <h2 class="category-title"><?php echo $nomeCategoria; ?> Personalizáveis</h2>
                    <p class="category-description">Escolha entre nossas opções personalizáveis de <?php echo strtolower($nomeCategoria); ?></p>

                    <div class="menu-items">
                        <?php foreach ($produtos as $produto) { ?>
                            <div class="menu-item">
                                <a href="pedir-personalizado.php?p=<?php echo $produto['id_personalizado'] ?>&c=<?php echo $idConfeitaria ?>">
                                    <img src='../view-confeitaria/<?php echo $produto['img_personalizado']; ?>' class="menu-item-image" alt='<?php echo $produto['nome_personalizado']; ?>'>
                                </a>
                                <div class="menu-item-details">
                                    <h3 class="menu-item-name"><?php echo $produto['nome_personalizado']; ?></h3>
                                    <p class="menu-item-description"><?php echo $produto['desc_personalizado']; ?></p>
                                    <button class="customize-btn" onclick="window.location.href='pedir-personalizado.php?p=<?php echo $produto['id_personalizado'] ?>&c=<?php echo $idConfeitaria ?>'">Personalizar</button>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </section>
            <?php } ?>

            <?php
            $tiposProduto = $configController->produto->viewMostraTipoProdutos($idConfeitaria);

            foreach ($tiposProduto as $tipoProduto) {
                $idTipoProduto = $tipoProduto['id_tipo_produto'];
                $produtos = $configController->produto->viewProdutosConfeitaria($idTipoProduto, $idCliente, $idConfeitaria);
                $nomeCategoria = $tipoProduto['desc_tipo_produto'];
            ?>

                <section class="menu-category">
                    <h2 class="category-title"><?php echo $nomeCategoria; ?></h2>
                    <p class="category-description">Nossas deliciosas opções de <?php echo strtolower($nomeCategoria); ?></p>

                    <div class="menu-items">
                        <?php foreach ($produtos as $produto) { ?>
                            <div class="menu-item">
                                <img src='../view-confeitaria/<?php echo $produto['img_produto']; ?>' class="menu-item-image" alt='<?php echo $produto['nome_produto']; ?>'>
                                <div class="menu-item-details">
                                    <h3 class="menu-item-name"><?php echo $produto['nome_produto']; ?></h3>
                                    <p class="menu-item-description"><?php echo $produto['desc_produto']; ?></p>
                                    <p class="menu-item-price">R$ <?php echo number_format($produto['valor_produto'], 2, ',', '.'); ?></p>
                                    <div class="menu-item-actions">
                                        <form method="post" class="add-to-cart-form">
                                            <input type="hidden" name="id" value="<?php echo $produto['id_produto']; ?>">
                                            <button type="submit" name="carrinho" class="add-to-cart">
                                                <i class="fas fa-shopping-cart"></i> Adicionar
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </section>
            <?php } ?>
        </main>
    </div>

    <!-- Floating Cart -->
    <a href="../view-cliente/carrinho.php" class="floating-cart">
        <i class="fas fa-shopping-cart"></i>
        <?php if ($numProdutosCarrinho > 0): ?>
            <span class="floating-cart-count"><?php echo $numProdutosCarrinho; ?></span>
        <?php endif; ?>
    </a>

    <?php
    if (isset($_POST['carrinho'])) {
        $resposta = $configController->pedido->addCarrinho();
        if ($resposta == 'id_diferente') {
            echo "
                <script>
                    Swal.fire({
                        title: 'Ops!',
                        text: 'Você só pode pedir produtos de uma confeitaria por vez! Finalize seu pedido atual ou esvazie o carrinho.',
                        icon: 'info',
                        confirmButtonText: 'Entendi',
                        confirmButtonColor: '#ea1d2c'
                    });
                </script>";
        } else if ($resposta == 'add') {
            echo "
                <script>
                    Swal.fire({
                        title: 'Adicionado ao carrinho!',
                        icon: 'success',
                        confirmButtonText: 'Ver carrinho',
                        confirmButtonColor: '#ea1d2c',
                        showCancelButton: true,
                        cancelButtonText: 'Continuar comprando'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = 'carrinho.php';
                        }
                    });
                </script>";
        }
    }
    ?>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js"></script>
    <script>
        // Menu mobile toggle
        document.querySelector('.btn-menumobile').addEventListener('click', function() {
            document.querySelector('.nav-container ul').classList.toggle('active');
        });

        // Atualiza contador do carrinho flutuante
        function updateCartCount() {
            const count = <?php echo $numProdutosCarrinho; ?>;
            const floatingCount = document.querySelector('.floating-cart-count');
            const headerCount = document.querySelector('.cart-count');

            if (count > 0) {
                if (floatingCount) floatingCount.textContent = count;
                else {
                    const span = document.createElement('span');
                    span.className = 'floating-cart-count';
                    span.textContent = count;
                    document.querySelector('.floating-cart').appendChild(span);
                }
            } else {
                if (floatingCount) floatingCount.remove();
            }
        }

        // Adiciona animação ao adicionar no carrinho
        document.querySelectorAll('.add-to-cart-form').forEach(form => {
            form.addEventListener('submit', function(e) {
                const button = this.querySelector('.add-to-cart');
                button.innerHTML = '<i class="fas fa-check"></i> Adicionado';
                button.style.backgroundColor = '#50a773';

                setTimeout(() => {
                    button.innerHTML = '<i class="fas fa-shopping-cart"></i> Adicionar';
                    button.style.backgroundColor = '';
                }, 2000);

                // Atualiza contador após um breve delay para garantir que a session foi atualizada
                setTimeout(updateCartCount, 500);
            });
        });
    </script>
</body>

</html>