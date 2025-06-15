<?php
session_start();

if (!isset($_SESSION['idCliente'])) {
    session_destroy();
    echo "<script>window.location.href='login-cliente.php'</script>";
    exit;
} elseif (isset($_SESSION['idTipoUsuario']) && $_SESSION['idTipoUsuario'] != 2) {
    echo "<script>window.location.href='login-cliente.php'</script>";
    exit;
}

require_once '../controller/controller-config.php';
$configController = new ControllerConfig();

// Recupera parâmetros da URL
$idConfeitaria = $_GET['c'] ?? null;
$idPersonalizado = $_GET['p'] ?? null;

// Evita execução sem parâmetros obrigatórios
if (!$idConfeitaria || !$idPersonalizado) {
    echo "<script>
        Swal.fire({
            title: 'Erro!',
            text: 'Confeitaria ou Personalizado não informados.',
            icon: 'error',
            confirmButtonText: 'OK',
            didClose: () => {
                window.location.href = '../view/index.php';
            }
        });
    </script>";
    exit;
}

// Carrega os dados necessários
$coberturas = $configController->cobertura->viewCobertura($idConfeitaria);
$decoracoes = $configController->decoracao->viewDecoracao($idConfeitaria);
$formatos = $configController->formato->viewFormato($idConfeitaria);
$massas = $configController->massa->viewMassa($idConfeitaria);
$recheios = $configController->recheio->viewRecheio($idConfeitaria);
$personalizado = $configController->personalizado->viewDadosPersonalizado($idPersonalizado);

// Verifica se o personalizado existe
if (empty($personalizado)) {
    echo "<script>
        Swal.fire({
            title: 'Produto não encontrado!',
            icon: 'error',
            confirmButtonText: 'OK',
            didClose: () => {
                window.location.href = '../view/index.php';
            }
        });
    </script>";
    exit;
}

// Pega configurações ativas
foreach ($personalizado as $perso) {
    $coberturaAtiva = $perso['cobertura_ativa'];
    $decoracaoAtiva = $perso['decoracao_ativa'];
    $formatoAtiva = $perso['formato_ativa'];
    $massaAtiva = $perso['massa_ativa'];
    $recheioAtiva = $perso['recheio_ativa'];
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&family=Playfair+Display:wght@700&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style-menu.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delicia online - Personalizar</title>
    <style>
        :root {
            --primary: #2a2a2a;
            --secondary: #5a5a5a;
            --accent: #e74c3c;
            --light: #f8f8f8;
            --border: #e0e0e0;
            --white: #ffffff;
        }

        body {
            font-family: 'Montserrat', sans-serif;
            background-color: var(--light);
            color: var(--primary);
            margin: 0;
            padding: 0;
            line-height: 1.6;
        }

        .container {
            padding: 20px;
        }

        /* Container principal unificado */
        .customization-container {
            max-width: 1000px;
            margin: 30px auto;
            background: var(--white);
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.08);
        }

        /* Cabeçalho do produto - versão circular */
        .product-header {
            display: flex;
            gap: 50px;
            margin-bottom: 40px;
            align-items: center;
            flex-wrap: wrap;
        }

        .product-image {
            width: 250px;
            height: 250px;
            border-radius: 50%;
            overflow: hidden;
            border: 5px solid var(--light);
            box-shadow: 0 3px 15px rgba(0, 0, 0, 0.1);
            flex-shrink: 0;
        }

        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.4s ease;
        }

        .product-image:hover img {
            transform: scale(1.05);
        }

        .product-info {
            flex: 1;
            min-width: 300px;
        }

        .product-title {
            font-family: 'Playfair Display', serif;
            font-size: 2.2rem;
            color: var(--primary);
            margin-bottom: 15px;
            position: relative;
            display: inline-block;
        }

        .product-title:after {
            content: '';
            position: absolute;
            bottom: -8px;
            left: 0;
            width: 100%;
            height: 2px;
            background: linear-gradient(90deg, var(--accent), transparent);
        }

        .product-description {
            color: var(--secondary);
            font-size: 1rem;
            line-height: 1.7;
            margin-bottom: 20px;
        }

        .base-price {
            font-size: 1.2rem;
            color: var(--accent);
            font-weight: 600;
        }

        /* Formulário de personalização */
        .customization-form {
            margin-top: 40px;
            background: #fcfcfc;
            border-radius: 12px;
            padding: 40px;
            border: 1px solid var(--border);
        }

        .form-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.8rem;
            color: var(--primary);
            margin-bottom: 30px;
            text-align: center;
            position: relative;
            padding-bottom: 15px;
        }

        .form-title:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 2px;
            background: var(--accent);
        }

        .options-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }

        .option-group {
            margin-bottom: 15px;
        }

        .option-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            font-size: 0.9rem;
            color: var(--primary);
        }

        .option-select {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid var(--border);
            border-radius: 4px;
            font-size: 0.95rem;
            background-color: var(--white);
            transition: all 0.3s ease;
            appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 12px center;
            background-size: 16px;
        }

        .option-select:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 1px var(--accent);
        }

        .disabled-option {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid var(--border);
            border-radius: 4px;
            font-size: 0.95rem;
            background-color: #f9f9f9;
            color: #999;
        }

        .total-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid var(--border);
        }

        .total-label {
            font-weight: 600;
            font-size: 1rem;
        }

        .total-price {
            font-weight: 700;
            font-size: 1.3rem;
            color: var(--accent);
        }

        .submit-btn {
            background: var(--accent);
            color: var(--white);
            border: none;
            padding: 14px 30px;
            font-size: 1rem;
            font-weight: 600;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: block;
            width: 100%;
            max-width: 300px;
            margin: 40px auto 0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .submit-btn:hover {
            background: #c0392b;
            transform: translateY(-2px);
        }

        footer {
            background: var(--primary);
            color: var(--white);
            text-align: center;
            padding: 20px 0;
            margin-top: 60px;
            font-size: 0.85rem;
        }

        /* Responsivo */
        @media (max-width: 768px) {
            .customization-container {
                padding: 25px;
            }

            .product-header {
                flex-direction: column;
                text-align: center;
            }

            .product-image {
                width: 200px;
                height: 200px;
                margin-bottom: 20px;
            }

            .product-title:after {
                left: 50%;
                transform: translateX(-50%);
                width: 80%;
            }

            .customization-form {
                padding: 25px;
            }

            .options-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <header>
        <nav>
            <div class="nav-container">
                <a href="dashboard-cliente.php">
                    <img id="logo" src="../assets/img-site/logo.png" alt="Delicia Online" style="height: 40px;">
                </a>
                <div class="greeting">
                    <?php
                    if (isset($_SESSION['nome'])) {
                        echo $_SESSION['nome'];
                    }
                    ?>
                </div>
                <ul>
                    <li><a href="../view-cliente/meus-pedidos.php">Pedidos</a></li>
                    <li><a href="../view-cliente/meus-cupons.php">Cupons</a></li>
                    <li><a href="../view-cliente/meus-contatos.php">Conversas</a></li>
                    <li><a href="../view-cliente/editar-cliente.php">Meus Dados</a></li>
                    <li><a href="pedir-suporte.php">Suporte</a></li>
                    <li>
                        <form action="logout.php" method="POST">
                            <input type="hidden" name="id" value="<?php echo $_SESSION['idUsuario']; ?>">
                            <button type="submit" class="fa fa-logout logado"><i class="fa fa-sign-out"
                                    style="font-size:20px"></i></button>
                        </form>
                    </li>
                </ul>
            </div>
        </nav>
    </header>

    <br><br>
    <div class="container">
        <div class="customization-container">
            <!-- Cabeçalho do produto com foto circular -->
            <div class="product-header">
                <div class="product-image">
                    <?php foreach ($personalizado as $perso): ?>
                        <img src='../view-confeitaria/<?php echo $perso['img_personalizado'] ?>'
                            alt='<?php echo $perso['nome_personalizado'] ?>'>
                    <?php endforeach; ?>
                </div>

                <div class="product-info">
                    <?php foreach ($personalizado as $perso): ?>
                        <h1 class="product-title"><?php echo $perso['nome_personalizado'] ?></h1>
                        <p class="product-description"><?php echo $perso['desc_personalizado'] ?></p>
                        <div class="base-price">Personalize seu produto</div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Formulário de personalização -->
            <div class="customization-form">
                <h2 class="form-title">Personalize seu Pedido</h2>

                <form enctype="multipart/form-data" method="post" onsubmit="return validaProduto()">
                    <div class="options-grid">
                        <!-- Massa -->
                        <div class="option-group">
                            <label class="option-label" for="descMassa">Massa</label>
                            <?php if ($massaAtiva): ?>
                                <select id="descMassa" name="descMassa" class="option-select price-selector"
                                    data-target="massa-price">
                                    <option value="" selected disabled>Selecione uma massa</option>
                                    <?php foreach ($massas as $massa): ?>
                                        <option value="<?php echo $massa['id_massa']; ?>"
                                            data-price="<?php echo $massa['valor_por_peso']; ?>">
                                            <?php echo $massa['desc_massa'] ?> (R$
                                            <?php echo number_format($massa['valor_por_peso'], 2, ',', '.'); ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            <?php else: ?>
                                <div class="disabled-option">Indisponível</div>
                                <input type="hidden" name="descMassa" value="0" class="price-selector" data-price="0">
                            <?php endif; ?>
                        </div>

                        <!-- Recheio -->
                        <div class="option-group">
                            <label class="option-label" for="descRecheio">Recheio</label>
                            <?php if ($recheioAtiva): ?>
                                <select id="descRecheio" name="descRecheio" class="option-select price-selector"
                                    data-target="recheio-price">
                                    <option value="" selected disabled>Selecione um recheio</option>
                                    <?php foreach ($recheios as $recheio): ?>
                                        <option value="<?php echo $recheio['id_recheio']; ?>"
                                            data-price="<?php echo $recheio['valor_por_peso']; ?>">
                                            <?php echo $recheio['desc_recheio'] ?> (R$
                                            <?php echo number_format($recheio['valor_por_peso'], 2, ',', '.'); ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            <?php else: ?>
                                <div class="disabled-option">Indisponível</div>
                                <input type="hidden" name="descRecheio" value="0" class="price-selector" data-price="0">
                            <?php endif; ?>
                        </div>

                        <!-- Cobertura -->
                        <div class="option-group">
                            <label class="option-label" for="descCobertura">Cobertura</label>
                            <?php if ($coberturaAtiva): ?>
                                <select id="descCobertura" name="descCobertura" class="option-select price-selector"
                                    data-target="cobertura-price">
                                    <option value="" selected disabled>Selecione uma cobertura</option>
                                    <?php foreach ($coberturas as $cobertura): ?>
                                        <option value="<?php echo $cobertura['id_cobertura']; ?>"
                                            data-price="<?php echo $cobertura['valor_por_peso']; ?>">
                                            <?php echo $cobertura['desc_cobertura'] ?> (R$
                                            <?php echo number_format($cobertura['valor_por_peso'], 2, ',', '.'); ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            <?php else: ?>
                                <div class="disabled-option">Indisponível</div>
                                <input type="hidden" name="descCobertura" value="0" class="price-selector" data-price="0">
                            <?php endif; ?>
                        </div>

                        <!-- Formato -->
                        <div class="option-group">
                            <label class="option-label" for="descFormato">Formato</label>
                            <?php if ($formatoAtiva): ?>
                                <select id="descFormato" name="descFormato" class="option-select price-selector"
                                    data-target="formato-price">
                                    <option value="" selected disabled>Selecione um formato</option>
                                    <?php foreach ($formatos as $formato): ?>
                                        <option value="<?php echo $formato['id_formato']; ?>"
                                            data-price="<?php echo $formato['valor_por_peso']; ?>">
                                            <?php echo $formato['desc_formato'] ?> (R$
                                            <?php echo number_format($formato['valor_por_peso'], 2, ',', '.'); ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            <?php else: ?>
                                <div class="disabled-option">Indisponível</div>
                                <input type="hidden" name="descFormato" value="0" class="price-selector" data-price="0">
                            <?php endif; ?>
                        </div>

                        <!-- Decoração -->
                        <div class="option-group">
                            <label class="option-label" for="descDecoracao">Decoração</label>
                            <?php if ($decoracaoAtiva): ?>
                                <select id="descDecoracao" name="descDecoracao" class="option-select price-selector"
                                    data-target="decoracao-price">
                                    <option value="" selected disabled>Selecione uma decoração</option>
                                    <?php foreach ($decoracoes as $decoracao): ?>
                                        <option value="<?php echo $decoracao['id_decoracao']; ?>"
                                            data-price="<?php echo $decoracao['valor_por_peso']; ?>">
                                            <?php echo $decoracao['desc_decoracao'] ?> (R$
                                            <?php echo number_format($decoracao['valor_por_peso'], 2, ',', '.'); ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            <?php else: ?>
                                <div class="disabled-option">Indisponível</div>
                                <input type="hidden" name="descDecoracao" value="0" class="price-selector" data-price="0">
                            <?php endif; ?>
                        </div>

                        <div class="option-group">
                            <label class="option-label" for="peso">Peso do Produto (em gramas)</label>
                            <input type="number" id="peso" name="peso" class="option-select" placeholder="Ex: 1000"
                                min="50" step="50" required>
                        </div>

                    </div>

                    <div class="total-container">
                        <span class="total-label">Total estimado:</span>
                        <span class="total-price">R$ 0,00</span>
                    </div>
                    <span>*Valor aproximado, podendo sofrer alterações</span>

                    <input type="hidden" id="idPersonalizado" name="idPersonalizado"
                        value="<?php echo htmlspecialchars($_GET['p'] ?? ''); ?>">
                    <input type="hidden" id="totalPrice" name="totalPrice" value="0">

                    <button type="submit" class="submit-btn" id="submit" name="submit">CONFIRMAR PEDIDO</button>
                </form>
            </div>
        </div>
    </div>

    <?php
    if (isset($_POST['submit'])) {
        $idPedidoPersonalizado = $configController->pedidoPersonalizado->addPedidoPersonalizado();
        if ($idPedidoPersonalizado) {
            echo "
                <script>
                    Swal.fire({
                    title: 'Pedido feito com sucesso!',
                    icon: 'success',
                    confirmButtonText: 'OK',
                    didClose: () => {
                        window.location.href = 'visualizar-pedido-personalizado.php?i=$idPedidoPersonalizado';
                    }
                    });
                </script>";
        } else {
            echo "
                <script>
                    Swal.fire({
                    title: 'Erro ao fazer o pedido!',
                    icon: 'error',
                    confirmButtonText: 'OK'
                    });
                </script>";
        }
    }
    ?>
    <script>
        $(document).ready(function () {
            function formatPeso(peso) {
                return peso >= 1000
                    ? (peso / 1000).toFixed(2).replace('.', ',') + ' kg'
                    : peso + ' g';
            }

            function updateTotalPrice() {
                let peso = parseInt($('#peso').val()) || 0;
                let total = 0;

                if (peso < 50) {
                    $('.total-price').text('R$ 0,00');
                    $('#totalPrice').val(0);
                    return;
                }

                // Fator de conversão: divide por 100 porque os preços são por 100g
                let fator = peso / 100;

                $('.price-selector').each(function () {
                    const $this = $(this);
                    let price = 0;

                    if ($this.is('select')) {
                        const selectedOption = $this.find('option:selected');
                        price = parseFloat(selectedOption.data('price')) || 0;
                    } else {
                        price = parseFloat($this.data('price')) || 0;
                    }

                    total += price * fator;
                });

                // Atualiza o total na interface
                $('.total-price').text('R$ ' + total.toFixed(2).replace('.', ',') + ' (' + formatPeso(peso) + ')');
                $('#totalPrice').val(total.toFixed(2));
            }

            // Atualiza ao alterar qualquer select ou o peso
            $('.price-selector, #peso').on('change keyup', updateTotalPrice);

            updateTotalPrice(); // Inicializa com valor padrão
        });
    </script>
</body>

</html>