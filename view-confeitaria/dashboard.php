<?php
session_start();
require_once '../controller/controller-config.php';
$configController = new ControllerConfig();

if (isset($_SESSION['emailUsuario'])) {
    if (isset($_SESSION['idTipoUsuario'])) {
        if ($_SESSION['idTipoUsuario'] != 3) {
            session_destroy();
            header("Location: login-confeitaria.php");
            exit();
        }
    } else {
        $configController->usuario->armazenaSessao(3, $_SESSION['emailUsuario']);
    }
} else {
    session_destroy();
    header("Location: login-confeitaria.php");
    exit();
}

$pedidos = $configController->pedido->getPedidosConfeitaria($_SESSION['idConfeitaria']);
$pedidosPersonalizados = $configController->pedidoPersonalizado->getPersonalizadosByConfeitaria();
$mensagens = $configController->chat->viewMensagemNaoLida($_SESSION['idUsuario']);

$periodo = isset($_GET['periodo']) ? $_GET['periodo'] : 'semana';

function getDadosGraficoPorPeriodo($periodo, $pedidos, $pedidosPersonalizados)
{
    $dados = [];

    // Junta tudo em um só array
    $todosPedidos = array_merge($pedidos, $pedidosPersonalizados);

    foreach ($todosPedidos as $pedido) {
        if (strtolower($pedido['status']) !== 'entregue!') {
            continue;
        }

        $data = strtotime($pedido['data_pedido']);
        $valor = floatval($pedido['valor_total']);

        switch ($periodo) {
            case 'semana':
                // Dia da semana (1 = segunda, ..., 7 = domingo)
                $dia = date('N', $data);
                $dados[$dia] = ($dados[$dia] ?? 0) + $valor;
                break;

            case 'mes':
                // Semana do mês (1 a 4+)
                $dia = (int) date('j', $data); // dia do mês
                $semana = ceil($dia / 7);
                $dados[$semana] = ($dados[$semana] ?? 0) + $valor;
                break;

            case 'ano':
                // Mês do ano (1 = Jan, ..., 12 = Dez)
                $mes = (int) date('n', $data);
                $dados[$mes] = ($dados[$mes] ?? 0) + $valor;
                break;
        }
    }

    // Garante que os dados estejam preenchidos com 0 caso não haja vendas
    if ($periodo === 'semana') {
        $labels = ['Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb', 'Dom'];
        $valores = [];
        for ($i = 1; $i <= 7; $i++) {
            $valores[] = $dados[$i] ?? 0;
        }
    } elseif ($periodo === 'mes') {
        $labels = ['Sem 1', 'Sem 2', 'Sem 3', 'Sem 4', 'Sem 5'];
        $valores = [];
        for ($i = 1; $i <= 5; $i++) {
            $valores[] = $dados[$i] ?? 0;
        }
    } else {
        $labels = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'];
        $valores = [];
        for ($i = 1; $i <= 12; $i++) {
            $valores[] = $dados[$i] ?? 0;
        }
    }

    return [
        'labels' => $labels,
        'valores' => $valores
    ];
}

// Dados genéricos para a dashboard
function getDadosGenericos($periodo, $pedidos, $pedidosPersonalizados, $mensagens)
{
    $pedidos_sem = [];
    $pedidos_mes = [];
    $pedidos_ano = [];

    $mensagensFormatadas = [];

    foreach ($mensagens as $m) {
        $mensagensFormatadas[] = [
            'remetente' => $m['nome_cliente'],
            'hora' => $m['hora_envio'],
            'mensagem' => $m['desc_mensagem'],
            'tipo' => $m['tipo'] // se quiser mostrar texto ou imagem
        ];
    }

    function dentroDoIntervalo($dataPedido, $dias)
    {
        $dataPedidoTime = strtotime($dataPedido);
        $hoje = strtotime('now');
        $diferenca = ($hoje - $dataPedidoTime) / (60 * 60 * 24); // em dias
        return $diferenca <= $dias;
    }

    // Formatar e distribuir os pedidos normais
    foreach ($pedidos as $pedido) {
        $pedidoFormatado = [
            'id' => $pedido['id_pedido'],
            'data' => date('Y-m-d H:i', strtotime($pedido['data_pedido'])),
            'valor' => $pedido['valor_total'],
            'status' => $pedido['status'],
            'tipo' => 'normal'
        ];

        if (dentroDoIntervalo($pedido['data_pedido'], 7)) {
            $pedidos_sem[] = $pedidoFormatado;
        }
        if (dentroDoIntervalo($pedido['data_pedido'], 30)) {
            $pedidos_mes[] = $pedidoFormatado;
        }
        if (dentroDoIntervalo($pedido['data_pedido'], 365)) {
            $pedidos_ano[] = $pedidoFormatado;
        }
    }

    // Formatar e distribuir os pedidos personalizados
    foreach ($pedidosPersonalizados as $pedido) {
        $pedidoFormatado = [
            'id' => $pedido['id_pedido_personalizado'],
            'data' => date('Y-m-d H:i', strtotime($pedido['data_pedido'])),
            'valor' => $pedido['valor_total'],
            'status' => $pedido['status'],
            'tipo' => 'personalizado'
        ];

        if (dentroDoIntervalo($pedido['data_pedido'], 7)) {
            $pedidos_sem[] = $pedidoFormatado;
        }
        if (dentroDoIntervalo($pedido['data_pedido'], 30)) {
            $pedidos_mes[] = $pedidoFormatado;
        }
        if (dentroDoIntervalo($pedido['data_pedido'], 365)) {
            $pedidos_ano[] = $pedidoFormatado;
        }
    }

    function getMaisVendidos($pedidosNormais, $pedidosPersonalizados)
    {
        $normais = [];
        $personalizados = [];

        $cancelados = ['Cancelado pelo Cliente', 'Cancelado pela Confeitaria'];

        // Processa pedidos normais
        foreach ($pedidosNormais as $pedido) {
            if (in_array($pedido['status'], $cancelados)) {
                continue;
            }

            $nome = $pedido['nome_produto'] ?? 'Produto sem nome';
            $desc = $pedido['desc_produto'] ?? '';
            $chave = $nome . '|' . $desc;

            if (!isset($normais[$chave])) {
                $normais[$chave] = [
                    'nome' => $nome,
                    'descricao' => $desc,
                    'vendas' => 0
                ];
            }

            $normais[$chave]['vendas'] += intval($pedido['quantidade']);
        }

        // Processa pedidos personalizados
        foreach ($pedidosPersonalizados as $pedido) {
            if (in_array($pedido['status'], $cancelados)) {
                continue;
            }

            $nome = $pedido['nome_personalizado'] ?? 'Personalizado sem nome';
            $desc = $pedido['desc_personalizado'] ?? '';
            $chave = $nome . '|' . $desc;

            if (!isset($personalizados[$chave])) {
                $personalizados[$chave] = [
                    'nome' => $nome,
                    'descricao' => $desc,
                    'vendas' => 0
                ];
            }

            $personalizados[$chave]['vendas'] += 1; // Sempre conta como 1
        }

        // Ordena por mais vendidos
        usort($normais, fn($a, $b) => $b['vendas'] <=> $a['vendas']);
        usort($personalizados, fn($a, $b) => $b['vendas'] <=> $a['vendas']);

        return [
            'normais' => array_slice(array_values($normais), 0, 3),
            'personalizados' => array_slice(array_values($personalizados), 0, 3)
        ];
    }


    $dados = [
        'semana' => [
            'pedidos' => $pedidos_sem,
            'produtos' => getMaisVendidos($pedidos, $pedidosPersonalizados),
            'mensagens' => $mensagensFormatadas,
            'stats' => [
                'total_pedidos' => count($pedidos_sem),
                'receita_total' => array_sum(array_map(
                    fn($p) => $p['status'] === 'Entregue!' ? $p['valor'] : 0,
                    $pedidos_sem
                )),
                'pedidos_pendentes' => count(array_filter($pedidos_sem, fn($p) => $p['status'] === 'Pedido Recebido!' || $p['status'] === 'Em Preparo!' || $p['status'] === 'Em Rota de Entrega!'))
            ]
        ],
        'mes' => [
            'pedidos' => $pedidos_mes,
            'produtos' => getMaisVendidos($pedidos, $pedidosPersonalizados),
            'mensagens' => $mensagensFormatadas,
            'stats' => [
                'total_pedidos' => count($pedidos_mes),
                'receita_total' => array_sum(array_map(
                    fn($p) => $p['status'] === 'Entregue!' ? $p['valor'] : 0,
                    $pedidos_mes
                )),

                'pedidos_pendentes' => count(array_filter($pedidos_mes, fn($p) => $p['status'] === 'Pedido Recebido!'))
            ]
        ],
        'ano' => [
            'pedidos' => $pedidos_ano,
            'produtos' => getMaisVendidos($pedidos, $pedidosPersonalizados),
            'mensagens' => $mensagensFormatadas,
            'stats' => [
                'total_pedidos' => count($pedidos_ano),
                'receita_total' => array_sum(array_map(
                    fn($p) => $p['status'] === 'Entregue!' ? $p['valor'] : 0,
                    $pedidos_ano
                )),
                'pedidos_pendentes' => count(array_filter($pedidos_ano, fn($p) => $p['status'] === 'Pedido Recebido!'))
            ]
        ]
    ];

    return $dados[$periodo];
}

$dados = getDadosGenericos($periodo, $pedidos, $pedidosPersonalizados, $mensagens);
$dadosGrafico = getDadosGraficoPorPeriodo($periodo, $pedidos, $pedidosPersonalizados);

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@400;700&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style-menu.css">
    <link rel="stylesheet" href="../assets/css/style-dashboard.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Confeitaria</title>
</head>

<body>
    <div class="container">
        <header>
            <nav>
                <div class="nav-container">
                    <a href="dashboard.php">
                        <img id="logo" src="../assets/img-site/logo.png" alt="Confeitaria">
                    </a>
                    <div class="greeting">
                        <?php echo isset($_SESSION['nome']) ? 'Olá, ' . $_SESSION['nome'] : 'Olá, Visitante'; ?>
                    </div>

                    <i class="fas fa-bars btn-menumobile"></i>
                    <ul class="nav-links">
                        <li><a href="meus-produtos.php">Produtos</a></li>
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

    <div class="dashboard-wrapper">
        <!-- Filtro lateral -->
        <div class="filter-sidebar">
            <div class="filter-header">Filtrar por Período</div>
            <a href="?periodo=semana" class="filter-option <?php echo $periodo == 'semana' ? 'active' : ''; ?>">
                <i class="fas fa-calendar-week"></i> Semana
            </a>
            <a href="?periodo=mes" class="filter-option <?php echo $periodo == 'mes' ? 'active' : ''; ?>">
                <i class="fas fa-calendar-alt"></i> Mês
            </a>
            <a href="?periodo=ano" class="filter-option <?php echo $periodo == 'ano' ? 'active' : ''; ?>">
                <i class="fas fa-calendar"></i> Ano
            </a>

            <div style="margin-top: 30px;">
                <div class="filter-header">Resumo</div>
                <div style="padding: 10px 15px; color: #555;">
                    <div style="margin-bottom: 10px;">
                        <small>Total de Pedidos</small>
                        <div style="font-weight: bold; font-size: 1.2rem;">
                            <?php echo $dados['stats']['total_pedidos']; ?>
                        </div>
                    </div>
                    <div style="margin-bottom: 10px;">
                        <small>Receita Total</small>
                        <div style="font-weight: bold; font-size: 1.2rem;">R$
                            <?php echo number_format($dados['stats']['receita_total'], 2, ',', '.'); ?>
                        </div>
                    </div>
                    <div>
                        <small>Pedidos Pendentes</small>
                        <div style="font-weight: bold; font-size: 1.2rem;">
                            <?php echo $dados['stats']['pedidos_pendentes']; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Conteúdo principal -->
        <div class="dashboard-content">
            <h1>Dashboard</h1>

            <div class="stats-grid">
                <div class="stat-card">
                    <i class="fas fa-shopping-bag"></i>
                    <div class="stat-card-content">
                        <h3>Total de Pedidos</h3>
                        <p><?php echo $dados['stats']['total_pedidos']; ?></p>
                    </div>
                </div>

                <div class="stat-card">
                    <i class="fas fa-money-bill-wave"></i>
                    <div class="stat-card-content">
                        <h3>Receita Total</h3>
                        <p>R$ <?php echo number_format($dados['stats']['receita_total'], 2, ',', '.'); ?></p>
                    </div>
                </div>

                <div class="stat-card">
                    <i class="fas fa-clock"></i>
                    <div class="stat-card-content">
                        <h3>Pedidos Pendentes</h3>
                        <p><?php echo $dados['stats']['pedidos_pendentes']; ?></p>
                    </div>
                </div>
            </div>

            <div class="chart-container">
                <canvas id="revenueChart"></canvas>
            </div>

            <div class="content-grid">
                <div class="orders-container">
                    <div class="section-header">
                        <h2 class="section-title">Últimos Pedidos</h2>
                        <a href="pedidos.php" class="btn">Ver Todos</a>
                    </div>

                    <?php if (!empty($dados['pedidos'])): ?>
                        <?php foreach ($dados['pedidos'] as $pedido): ?>
                            <div class="order-item">
                                <div class="order-info">
                                    <div class="order-id">Pedido #<?php echo $pedido['id']; ?>         <?php echo $pedido['tipo']; ?>
                                    </div>
                                    <div class="order-date"><?php echo date('d/m/Y H:i', strtotime($pedido['data'])); ?></div>
                                </div>
                                <div class="order-value">R$ <?php echo number_format($pedido['valor'], 2, ',', '.'); ?></div>
                                <div class="order-status status-<?php echo str_replace('_', '-', $pedido['status']); ?>">
                                    <?php
                                    $bloqueados = [
                                        'Entregue!',
                                        'Em rota de Entrega!',
                                        'Cancelado pelo Cliente',
                                        'Cancelado pela Confeitaria'
                                    ];
                                    echo ucfirst($pedido['status']);
                                    ?>
                                </div>
                                <?php if (!in_array($pedido['status'], $bloqueados)): ?>
                                    <form method="POST" action="">
                                        <input type="hidden" name="idPedido" value="<?php echo $pedido['id']; ?>">
                                        <button type="submit" class="btn">Iniciar Entrega</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        Nenhum pedido encontrado.
                    <?php endif; ?>
                </div>

                <div>
                    <div class="products-container">
                        <div class="section-header">
                            <h2 class="section-title">Produtos Mais Vendidos</h2>
                        </div>

                        <h3>Produtos Normais</h3>
                        <?php if (!empty($dados['produtos']['normais'])): ?>
                            <?php foreach ($dados['produtos']['normais'] as $produto): ?>
                                <div class="product-item">
                                    <div class="product-name"><?php echo $produto['nome']; ?></div>
                                    <div class="product-sales"><?php echo $produto['vendas']; ?> vendas</div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p>Nenhum produto normal encontrado.</p>
                        <?php endif; ?>

                        <h3 style="margin-top: 20px;">Produtos Personalizados</h3>
                        <?php if (!empty($dados['produtos']['personalizados'])): ?>
                            <?php foreach ($dados['produtos']['personalizados'] as $produto): ?>
                                <div class="product-item">
                                    <div class="product-name"><?php echo $produto['nome']; ?></div>
                                    <div class="product-sales"><?php echo $produto['vendas']; ?> vendas</div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p>Nenhum produto personalizado encontrado.</p>
                        <?php endif; ?>
                    </div>

                    <div class="messages-container" style="margin-top: 20px;">
                        <div class="section-header">
                            <h2 class="section-title">Últimas Mensagens</h2>
                            <a href="meus-contatos.php" class="btn">Ver Todas</a>
                        </div>

                        <?php if (!empty($dados['mensagens'])): ?>
                            <?php foreach ($dados['mensagens'] as $mensagem): ?>
                                <div class="message-item">
                                    <div class="message-header">
                                        <div class="message-sender"><?php echo $mensagem['remetente']; ?></div>
                                        <div class="message-time"><?php echo $mensagem['hora']; ?></div>
                                    </div>
                                    <div class="message-preview"><?php echo $mensagem['mensagem']; ?></div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p>Nenhuma mensagem encontrada.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Configuração do gráfico
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('revenueChart').getContext('2d');

            // Dados do gráfico baseados no período selecionado
            const chartData = {
                labels: <?php echo json_encode($dadosGrafico['labels']); ?>,
                datasets: [{
                    label: 'Receita (R$)',
                    data: <?php echo json_encode($dadosGrafico['valores']); ?>,
                    backgroundColor: 'rgba(255, 107, 107, 0.2)',
                    borderColor: 'rgba(255, 107, 107, 1)',
                    borderWidth: 2,
                    tension: 0.1,
                    fill: true
                }]
            };

            const chartOptions = {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function (value) {
                                return 'R$ ' + value.toLocaleString('pt-BR');
                            }
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                return 'Receita: R$ ' + context.raw.toLocaleString('pt-BR', { minimumFractionDigits: 2 });
                            }
                        }
                    }
                }
            };

            new Chart(ctx, {
                type: 'line',
                data: chartData,
                options: chartOptions
            });
        });
    </script>
</body>

</html>