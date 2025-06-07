<?php
require_once '../controller/controller-config.php';
$configController = new ControllerConfig();

$confeitarias = $configController->confeitaria->viewConfeitarias();

session_start();
if (isset($_SESSION['emailUsuario'])) {
  if (isset($_SESSION['idTipoUsuario'])) {
  } else {
    $configController->usuario->armazenaSessao(2, $_SESSION['emailUsuario']);
    $configController->pedido->gerarCupomSeNecessario($_SESSION['idUsuario']);
  }
}
// Verificar se o usuário está logado (supondo que a sessão tenha sido iniciada)
$usuarioLogado = isset($_SESSION['idCliente']) && $_SESSION['idCliente'] != '';

$numProdutosCarrinho = 0;
if (isset($_SESSION['carrinho'])) {
  $numProdutosCarrinho = count($_SESSION['carrinho']);
}

date_default_timezone_set('America/Sao_Paulo'); // Ajuste para seu fuso horário
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Delícias Online</title>

  <!-- Link de fontes e ícones -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@400;700&display=swap">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="stylesheet" href="../assets/css/style-menu.css">
  <link rel="stylesheet" href="../assets/css/style-index.css">

  <!-- Swiper e Slick Carousel -->
  <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.min.css">

  <!-- Sweetalert -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
  <script src="https://unpkg.com/scrollreveal"></script>
</head>

<body>
  <div class="container">
    <header>
      <nav>
        <div class="nav-container">
          <a href="index.php">
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

          <!-- Barra de pesquisa -->
          <div class="search-container">
            <form action="search.php" method="GET">
              <input type="text" name="query" placeholder="Pesquisar..." class="search-input" required>
              <button type="submit" class="search-button" aria-label="Pesquisar">
                <i class="fa fa-search"></i>
              </button>
            </form>
          </div>

          <ul class="nav-links">
            <li style="position: relative;">
              <a href="../view-cliente/carrinho.php">
                <i class="fa fa-shopping-cart" style="font-size:20px"></i>
                <?php if ($numProdutosCarrinho > 0): ?>
                  <span class="cart-counter"><?php echo $numProdutosCarrinho; ?></span>
                <?php endif; ?>
              </a>
            </li>

            <!-- Itens do menu alterados conforme o login -->
            <?php if ($usuarioLogado): ?>
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
            <?php else: ?>
              <li><a href="../view-cliente/login-cliente.php">Sou Cliente</a></li>
              <li><a href="../view-confeitaria/login-confeitaria.php">Sou Confeitaria</a></li>
            <?php endif; ?>
          </ul>
        </div>
      </nav>
    </header>
  </div>

  <div class="carousel">
    <input type="radio" name="carousel" id="slide1" checked>
    <input type="radio" name="carousel" id="slide2">
    <input type="radio" name="carousel" id="slide3">
    <div class="slides">
      <div class="slide">
        <img src="../assets/img-site/Oferta3.png" alt="Slide 1">
      </div>
      <div class="slide">
        <img src="../assets/img-site/Oferta2.png" alt="Slide 2">
      </div>
      <div class="slide">
        <img src="../assets/img-site/Oferta1.png" alt="Slide 3">
      </div>
    </div>
    <div class="navigation">
      <label for="slide1"></label>
      <label for="slide2"></label>
      <label for="slide3"></label>
    </div>
  </div>

  <h1>Confeitarias em destaque</h1>

  <div class="carousel-2">
    <?php foreach ($confeitarias as $confeitaria): ?>

      <?php
      $horaEntrada = date('H:i', strtotime($confeitaria['hora_entrada']));
      $horaSaida = date('H:i', strtotime($confeitaria['hora_saida']));
      $horaAgora = date('H:i');
      // Verifica se está dentro do horário de atendimento
      $estaAberto = ($horaAgora >= $horaEntrada && $horaAgora <= $horaSaida);
      ?>

      <div class="card">
        <a
          href="../view-cliente/dados-confeitaria.php?c=<?php echo $confeitaria['id_confeitaria'] ?>&u=<?php echo $confeitaria['id_usuario'] ?>">
          <div class="card-image">
            <?php
            echo "<img src='../view-confeitaria/" . $confeitaria['img_confeitaria'] . "' alt='" . $confeitaria['nome_confeitaria'] . "'>";
            ?>
          </div>
        </a>
        <div class="card-text">
          <h2 class="card-title"><?php echo $confeitaria['nome_confeitaria'] ?></h2>
          <div class="info">
            <div class="horario <?php echo $estaAberto ? '' : 'fechado'; ?>">
              <i class="fas fa-clock"></i>
              <span>Seg a Sex: <?php echo $horaEntrada ?> às <?php echo $horaSaida ?></span>
            </div>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
  <?php
  $tiposProduto = $configController->produto->viewMostraTipoGlobal();

  if (isset($_SESSION['idCliente'])) {
    $idCliente = $_SESSION['idCliente'];
  } else {
    $idCliente = 0;
  }

  foreach ($tiposProduto as $tipoProduto) {
    $desc = $tipoProduto['desc_tipo_produto'];
    $produtos = $configController->produto->viewProdutos($desc, $idCliente);
  ?>

    <div id="header">
      <h1>
        <?php
        echo $tipoProduto['desc_tipo_produto'];
        ?> em Destaque
      </h1>
    </div>

    <div class="carousel-2">
      <?php foreach ($produtos as $produto) { ?>
        <div class="card">
          <div class="card-image">
            <img src="../view-confeitaria/<?php echo $produto['img_produto']; ?>"
              alt="<?php echo $produto['nome_produto']; ?>">
          </div>
          <div class="card-text">
            <h2 class="card-title"><?php echo $produto['nome_produto'] ?></h2>
            <p class="card-body"><?php echo $produto['desc_produto'] ?></p>
            <div class="frete-container">

              <p class="frete-text">Frete: R$<?php echo $produto['frete'] ?><span class="frete-icon">🚚</span></p>
            </div>
            <div class="card-price">Preço: R$ <?php echo number_format($produto['valor_produto'], 2, ',', '.') ?></div>
          </div>
          <form class="card-form" method="post">
            <input type="hidden" name="id" value="<?php echo $produto['id_produto'] ?>">
            <button type="submit" name="carrinho" class="add-to-cart">
              <i class="fa fa-shopping-cart"></i> Adicionar ao carrinho
            </button>
          </form>
        </div>
      <?php } ?>
    </div>
  <?php } ?>

  <?php
  if (isset($_POST['carrinho'])) {
    $resposta = $configController->pedido->addCarrinho();
    if ($resposta == 'id_diferente') {
      echo "
                <script>
                    Swal.fire({
                    title: 'Erro ao adicionar Produto!',
                    text: 'Você só pode pedir produtos de uma confeitaria por vez!',
                    icon: 'info',
                    confirmButtonText: 'OK'
                    });
            </script>";
    } else if ($resposta == 'add') {
      echo "
                <script>
                    Swal.fire({
                    title: 'Produto adicionado com sucesso!',
                    icon: 'success',
                    confirmButtonText: 'OK',
                        didClose: () => {
                                window.location.href = '../view-cliente/carrinho.php';
                            }
                    });
            </script>";
    }
  }
  ?>

  <div id="container_ajuda">

    <h1>Ajuda</h1>
    <p>Se precisar de assistência, Aqui tem algumas perguntas frequentes.</p>
    <br>
    <p id="paragrafo1" onclick="mostrarParagrafo2()"> 1 - Este Site é seguro?</p>

    <p id="paragrafo2"><br>Todas as transações são criptografadas para garantir segurança.</p>
    <p>__________________________________________________________________________</p>
    <br>
    <p id="paragrafo1" onclick="mostrarParagrafo3()"> 2 - Como aplicar cupons de desconto?</p>

    <p id="paragrafo3"><br>Durante o checkout, há uma opção para inserir o código do cupom.</p>
    <p>__________________________________________________________________________</p>
    <br>
    <p id="paragrafo1" onclick="mostrarParagrafo4()"> 3 - Participação em promoções específicas?</p>

    <p id="paragrafo4"><br>Fique atento às nossas newsletters e redes sociais para informações sobre promoções
      especiais.</p>
    <p>__________________________________________________________________________</p>
  </div>

  <script>
    function mostrarParagrafo2() {
      var paragrafo2 = document.getElementById("paragrafo2");
      paragrafo2.style.display = "block";

      var tempoExibicao = 3000; // Defina o tempo em milissegundos (exemplo: 3000 para 3 segundos)

      // Use setTimeout para ocultar o parágrafo após o tempo especificado
      setTimeout(function() {
        paragrafo2.style.display = "none";
      }, tempoExibicao);
    }

    function mostrarParagrafo3() {
      var paragrafo2 = document.getElementById("paragrafo3");
      paragrafo2.style.display = "block";

      var tempoExibicao = 3000; // Defina o tempo em milissegundos (exemplo: 3000 para 3 segundos)

      // Use setTimeout para ocultar o parágrafo após o tempo especificado
      setTimeout(function() {
        paragrafo2.style.display = "none";
      }, tempoExibicao);
    }

    function mostrarParagrafo4() {
      var paragrafo2 = document.getElementById("paragrafo4");
      paragrafo2.style.display = "block";

      var tempoExibicao = 3000; // Defina o tempo em milissegundos (exemplo: 3000 para 3 segundos)

      // Use setTimeout para ocultar o parágrafo após o tempo especificado
      setTimeout(function() {
        paragrafo2.style.display = "none";
      }, tempoExibicao);
    }

    // Função dos banner
    function nextSlide() {
      var slides = document.querySelectorAll('.slides .slide');
      var radios = document.querySelectorAll('[name="carousel"]');
      var currentIndex;

      for (var i = 0; i < radios.length; i++) { // Encontrar o índice do slide atual
        if (radios[i].checked) {
          currentIndex = i;
          break;
        }
      }

      radios[currentIndex].checked = false; // Desmarcar o slide atual e marcar o próximo slide
      if (currentIndex < slides.length - 1) {
        radios[currentIndex + 1].checked = true;
      } else {
        radios[0].checked = true; // Voltar para o primeiro slide se chegarmos ao último
      }
    }

    setInterval(nextSlide, 3000); // Definir intervalo para mudar de slide a cada 3 segundos (3000 milissegundos)
  </script>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js"></script>

  <script>
    $(document).ready(function() {
      $('.carousel-2').slick({
        dots: true,
        infinite: true,
        speed: 300,
        slidesToShow: 3,
        slidesToScroll: 1,
        responsive: [{
            breakpoint: 1000,
            settings: {
              slidesToShow: 2,
              slidesToScroll: 1,
              infinite: true,
              dots: true
            }
          },
          {
            breakpoint: 400,
            settings: {
              slidesToShow: 1,
              slidesToScroll: 1
            }
          }
        ]
      });
    });

    document.addEventListener('DOMContentLoaded', function() {
      const menuMobile = document.querySelector('.btn-menumobile');
      const navLinks = document.querySelector('.nav-links');

      menuMobile.addEventListener('click', function() {
        navLinks.classList.toggle('active');
      });
    });
  </script>

</body>

</html>