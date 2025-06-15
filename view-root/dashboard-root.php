<?php
require_once '../controller/controller-usuario.php';
require_once '../controller/controller-adm.php';
require_once '../controller/controller-cliente.php';
require_once '../controller/controller-suporte.php';

$usuarioController = new ControllerUsuario();
$admController = new ControllerAdm();
$clienteController = new ControllerCliente();
$controller = new ControllerSuporte();

session_start();
if (isset($_SESSION['emailUsuario'])) {
    if (isset($_SESSION['idTipoUsuario'])) {
    } else {
        $usuarioController->armazenaSessao(1, $_SESSION['emailUsuario']);
    }
} else {
    session_destroy();
    echo "<script language='javascript' type='text/javascript'> window.location.href='../view/index.php'</script>";
}
?>


<?php

// Variáveis para o modal de resolução
$suporteParaResolver = null;
$mostrarModalResolver = false;

// Processar formulário Cadastrar ADM
if (isset($_POST['cadastrar'])) {
    if ($usuarioController->addUsuario() && $admController->addAdm()) {
        $message = 'Cadastro realizado com sucesso!';
    } else {
        $message = 'Erro ao cadastrar.';
    }

    echo "<script>
        alert('$message');
        window.location.href = 'dashboard-root.php?page=novos-adm';
    </script>";
    exit;
    
}

// Processar formulário Cadastrar Cliente
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cadastrarCliente'])) {
    try {
        if ($admController->cadastrarCliente($_POST)) {
            // Redireciona para a página de cadastro de cliente (subpágina 3)
            echo "<script>
                alert('Cliente cadastrado com sucesso!');
                window.location.href = 'dashboard-root.php?page=usuarios&subpage=3';
            </script>";
            exit;
        }
    } catch (Exception $e) {
        echo "<script>
            alert('Erro: " . addslashes($e->getMessage()) . "');
            window.location.href = 'dashboard-root.php?page=usuarios&subpage=3';
        </script>";
        exit;
    }
}

// Processar formulário Cadastrar Confeitaria
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cadastrarConfeitaria'])) {
    try {
        if ($admController->cadastrarConfeitaria($_POST)) {
            echo "<script>alert('Confeitaria cadastrada com sucesso!'); window.location.href = 'dashboard-root.php?page=usuarios&subpage=3';</script>";
        }
    } catch (Exception $e) {
        echo "<script>alert('Erro: " . addslashes($e->getMessage()) . "');</script>";
    }
}

// Processa ações (exclusão) ADM
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'excluir') {
    $id_adm = $_POST['id_adm'] ?? null;
    
    if ($id_adm) {
        // Chamar método para excluir adm e usuário
        $result = $admController->deleteAdmAndUsuario($id_adm);
        
        if ($result['success']) {
            // Redireciona para a página de lista de ADMs (subpágina 2)
            echo "<script>
                alert('Administrador e usuário associado excluídos com sucesso!');
                window.location.href = 'dashboard-root.php?page=novos-adm&subpage=2';
            </script>";
            exit;
        } else {
            $mensagem = '<div class="alert alert-danger">Erro ao excluir: ' . htmlspecialchars($result['message']) . '</div>';
            // Você pode decidir se quer redirecionar mesmo com erro ou mostrar a mensagem na mesma página
            echo "<script>
                alert('Erro ao excluir: " . addslashes($result['message']) . "');
                window.location.href = 'dashboard-root.php?page=novos-adm&subpage=2';
            </script>";
            exit;
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $id_suporte = $_POST['id_suporte'] ?? null;
        
        if ($id_suporte) {
            if ($_POST['action'] === 'abrir_resolver') {
                // Abre o modal com os dados do suporte
                $suporteParaResolver = $controller->obterSuportePorId($id_suporte);
                $mostrarModalResolver = true;
                
            } elseif ($_POST['action'] === 'resolver') {
                // Processa a resolução completa
                $solucao = $_POST['solucao'] ?? '';
                $novaSenha = $_POST['nova_senha'] ?? '';
                $enviarEmail = isset($_POST['enviar_email']);
                
                $result = $controller->marcarComoResolvido($id_suporte, $solucao, $novaSenha, $enviarEmail);
                
                if ($result['success']) {
                    $mensagem = '<div class="alert alert-success">Chamado resolvido com sucesso!</div>';
                    // Recarrega a lista
                    $suportes = $controller->listarSuportesClientesCadastro($filtro);
                } else {
                    $mensagem = '<div class="alert alert-danger">Erro ao resolver chamado: ' . htmlspecialchars($result['message']) . '</div>';
                }
            } elseif ($_POST['action'] === 'excluir') {
                $result = $controller->deletarSuporte($id_suporte);
                
                if ($result['success']) {
                    $mensagem = '<div class="alert alert-success">Chamado excluído com sucesso!</div>';
                    // Recarrega a lista
                    $suportes = $controller->listarSuportesClientesCadastro($filtro);
                } else {
                    $mensagem = '<div class="alert alert-danger">Erro ao excluir: ' . htmlspecialchars($result['message']) . '</div>';
                }
            }
        }
    }
}

function mostrarStatus($resolvido) {
    return $resolvido ? 
        '<span class="badge bg-success">Resolvido</span>' : 
        '<span class="badge bg-warning text-dark">Pendente</span>';
}

if (method_exists($controller, 'listarSuportesClientesCadastro')) {
    $filtro = isset($_GET['filtro']) ? $_GET['filtro'] : '';
    $suportes = $controller->listarSuportesClientesCadastro($filtro);
} else {
    die("Erro: Método 'listarSuportesClientesCadastro' não encontrado no controller.");
}

?>


<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Root</title>
    <link rel="stylesheet" href="../assets/css/style-dashboard-root.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <div class="dashboard">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-header">
                <h2><i class="fas fa-tachometer-alt"></i> Dashboard</h2>
            </div>
            <ul class="sidebar-nav">
                <li class="nav-item">
                    <a href="#" class="nav-link active" onclick="if(!isLoading) loadContent('visao-geral')">
                        <i class="fas fa-home"></i> Visão Geral
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link" onclick="if(!isLoading) loadContent('novos-adm')">
                        <i class="fas fa-user-plus"></i> Novos ADM
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link" onclick="if(!isLoading) loadContent('usuarios')">
                        <i class="fas fa-users"></i> Usuários
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link" onclick="if(!isLoading) loadContent('confeitarias')">
                        <i class="fas fa-store"></i> Confeitarias
                    </a>
                </li>
                <li class="nav-item">
                    <form action="../view/logout.php" method="POST">
                        <input type="hidden" name="id" value="<?php echo $_SESSION['idUsuario']; ?>">
                        <button type="submit" class="nav-link"><i class="fas fa-sign-out-alt"></i>Sair</button>
                    </form>
                </li>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Top Bar -->
            <div class="top-bar">
                <div class="greeting">
                    <?php echo 'Olá, ' . ($_SESSION['nome'] ?? 'Administrador'); ?>
                </div>
                <div class="search-bar">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Pesquisar..." id="global-search">
                </div>
            </div>

            <!-- Dynamic Content -->
            <div id="dynamic-content">

            <!-- Mensagens de sucesso/erro -->
            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="alert alert-success">
                    <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="alert alert-error">
                    <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
                </div>
            <?php endif; ?>

            
                <!-- Conteúdo será carregado aqui via AJAX -->
                <div class="loading-content">
                    <i class="fas fa-spinner fa-spin"></i>
                    <p>Carregando dashboard...</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Variáveis de controle de estado
        let isLoading = false;
        let loadingInterval;
        let currentPage = 'visao-geral';

        // Função para habilitar/desabilitar os botões do menu
        function toggleMenuButtons(disabled) {
            document.querySelectorAll('.sidebar-nav .nav-link').forEach(link => {
                if (link.getAttribute('href') !== '../view/logout.php') {
                    link.classList.toggle('disabled', disabled);
                    link.style.pointerEvents = disabled ? 'none' : 'auto';
                    link.style.opacity = disabled ? '0.6' : '1';
                }
            });
        }
        // Função para mostrar a tela de carregamento
        function showLoadingScreen(page) {
            document.getElementById('dynamic-content').innerHTML = `
            <div class="loading-content">
                <i class="fas fa-spinner fa-spin"></i>
                <p>Carregando ${page.replace('-', ' ')}...</p>
                <div class="progress-bar">
                    <div class="progress"></div>
                </div>
                <p class="loading-message">Por favor, aguarde</p>
            </div>
            `;

            // Anima a barra de progresso
            const progressBar = document.querySelector('.progress');
            let width = 0;
            progressBar.style.width = '0%';

            clearInterval(loadingInterval);
            loadingInterval = setInterval(() => {
                width += 1;
                progressBar.style.width = width + '%';
                if (width >= 100) clearInterval(loadingInterval);
            }, 100);
        }
        // Função para carregar o conteúdo da página
        function loadPageContent(page) {
            const fileMap = {
                'visao-geral': 'visao-geral.php',
                'novos-adm': 'novos-adm.php',
                'usuarios': 'usuarios.php',
                'confeitarias': 'confeitarias.php'
            };

            const file = fileMap[page] || 'visao-geral.php';

            fetch(file)
                .then(response => {
                    if (!response.ok) throw new Error('Erro ao carregar o conteúdo');
                    return response.text();
                })
                .then(data => {
                    const contentDiv = document.getElementById('dynamic-content');

                    // Adiciona uma classe ao container principal para controle
                    if (page === 'visao-geral') {
                        contentDiv.classList.remove('show-submenu');
                        contentDiv.innerHTML = data;
                    } else {
                        contentDiv.classList.add('show-submenu');
                        contentDiv.innerHTML = `
                            <div class="subpage-menu">
                                <ul class="subpage-nav" id="subpage-nav-${page}">
                                    <!-- Itens do menu serão adicionados dinamicamente -->
                                </ul>
                            </div>
                            <div class="subpage-content">
                                ${data}
                            </div>
                        `;

                        // Configura o menu específico
                        setupSubpageMenu(page);
                    }

                    if (page === 'visao-geral') initCharts();
                    currentPage = page;
                })
                .catch(error => {
                    console.error('Erro:', error);
                    showError(page);
                })
                .finally(() => {
                    isLoading = false;
                    toggleMenuButtons(false);
                });
        }
        // Configura o menu de subpáginas específico
        function setupSubpageMenu(page) {
            const subpageNav = document.getElementById(`subpage-nav-${page}`);

            // Define os menus para cada seção principal
            const submenus = {
                'usuarios': [
                    { icon: 'fas fa-tachometer-alt', title: 'Problemas Diversos', content: '' },
                    { icon: 'fa-chart-bar', title: 'Problemas Cadastrais ', content: '' },
                    { icon: 'fa-ban', title: 'Usuários Banidos', content: '' },
                    { icon: 'fa-plus-circle', title: 'Adicionar Novo Cliente', content: '' }
                ],
                'confeitarias': [
                    { icon: 'fa-store', title: 'Problemas Diversos', content: '' },
                    { icon: 'fa-check-circle', title: 'Problemas Cadastrais', content: '' },
                    { icon: 'fa-chart-pie', title: 'Pendente', content: '' },
                    { icon: 'fa-star', title: 'Adicionar Novo Confeitarias', content: '' }
                ],
                'novos-adm': [
                    { icon: 'fa-user-plus', title: 'Adicionar um novo administrador', content: '' },
                    { icon: 'fa-user-shield', title: 'Permissões', content: '' },
                    { icon: 'fa-list', title: 'Lista', content: '' }
                ]
            };

            // Adiciona os itens do menu
            if (submenus[page]) {
                submenus[page].forEach((item, index) => {
                    const li = document.createElement('li');
                    li.className = 'subpage-nav-item';
                    li.innerHTML = `
                        <a href="#" class="subpage-nav-link" 
                        onclick="loadSubpageContent('${page}', ${index})">
                            <i class="fas ${item.icon}"></i> ${item.title}
                        </a>
                    `;
                    subpageNav.appendChild(li);
                });

                // Carrega o conteúdo padrão (primeiro item)
                loadSubpageContent(page, 0);
            }
        }
        // Carrega o conteúdo de uma subpágina específica
        function loadSubpageContent(mainPage, subpageIndex) {
            const fileMap = {
                'usuarios': [
                    '../view-root/usuarios.php',
                    '../view-root/usuarios-problemas-diversos.php',
                    'Pagina 01',
                    '../view-root/cadastrar-Cliente.php'
                ],
                'confeitarias': [
                    '../view-root/confeitarias.php',
                    '../view-root/confeitaria-problemas-diversos.php',
                    'Pagina 01', 
                    '../view-root/cadastrar-Confeitaria.php'
                ],
                'novos-adm': [
                    '../view-root/novos-adm.php',
                    'Pagina 01',
                    '../view-root/lista-adm.php'
                ]
            };

            const file = fileMap[mainPage][subpageIndex];

            fetch(file)
                .then(response => response.text())
                .then(data => {
                    // Atualiza o menu ativo
                    const subpageNav = document.getElementById(`subpage-nav-${mainPage}`);
                    subpageNav.querySelectorAll('.subpage-nav-link').forEach((link, index) => {
                        link.classList.toggle('active', index === subpageIndex);
                    });

                    // Atualiza o conteúdo
                    document.querySelector('.subpage-content').innerHTML = data;
                    initCEPautocomplete();

                    // Inicializa gráficos se necessário
                    if (mainPage === 'usuarios' && subpageIndex === 1) {
                        initUserStatsChart();
                    }
                    
                })
                .catch(error => {
                    console.error('Erro ao carregar subpágina:', error);
                    document.querySelector('.subpage-content').innerHTML = `
                    <div class="error-message">
                        <i class="fas fa-exclamation-triangle"></i>
                        <p>Erro ao carregar o conteúdo</p>
                    </div>
                `;
                });
        }
        // Inicializa gráfico de estatísticas de usuários
        function initUserStatsChart() {
            const ctx = document.getElementById('userStatsChart');
            if (ctx) {
                new Chart(ctx.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun'],
                        datasets: [{
                            label: 'Novos Usuários',
                            data: [120, 190, 170, 210, 180, 200],
                            backgroundColor: 'rgba(78, 115, 223, 0.5)',
                            borderColor: 'rgba(78, 115, 223, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            }
        }
        // Função principal para carregar conteúdo
        function loadContent(page, isInitial = false) {
            if (isLoading || currentPage === page) return;

            isLoading = true;
            toggleMenuButtons(true);

            if (!isInitial) {
                showLoadingScreen(page);
                document.querySelectorAll('.nav-link').forEach(link => {
                    link.classList.remove('active');
                });
                event.target.classList.add('active');
            }

            // Timeout simulado de 10 segundos
            setTimeout(() => {
                loadPageContent(page);
            }, 1000);
        }
        // Função para mostrar gráficos
        function initCharts() {
            // Gráfico de Linha - Desempenho Mensal
            const lineCtx = document.getElementById('lineChart');
            if (lineCtx) {
                new Chart(lineCtx.getContext('2d'), {
                    type: 'line',
                    data: {
                        labels: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul'],
                        datasets: [{
                            label: 'Receita (R$)',
                            data: [12000, 15000, 18000, 21000, 19000, 23000, 24500],
                            borderColor: '#4e73df',
                            backgroundColor: 'rgba(78, 115, 223, 0.05)',
                            tension: 0.3,
                            fill: true
                        }, {
                            label: 'Despesas (R$)',
                            data: [8000, 8500, 9000, 9500, 10000, 10500, 11000],
                            borderColor: '#e74a3b',
                            backgroundColor: 'rgba(231, 74, 59, 0.05)',
                            tension: 0.3,
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'top',
                            },
                            tooltip: {
                                mode: 'index',
                                intersect: false,
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function (value) {
                                        return 'R$ ' + value.toLocaleString('pt-BR');
                                    }
                                }
                            }
                        }
                    }
                });
            }

            // Gráfico de Pizza - Distribuição de Tarefas
            const pieCtx = document.getElementById('pieChart');
                if (pieCtx) {
                    new Chart(pieCtx.getContext('2d'), {
                        type: 'pie',
                        data: {
                            labels: ['Concluídas', 'Em Progresso', 'Pendentes', 'Canceladas'],
                            datasets: [{
                                data: [35, 25, 20, 5],
                                backgroundColor: [
                                    '#1cc88a',
                                    '#36b9cc',
                                    '#f6c23e',
                                    '#e74a3b'
                                ],
                                hoverBackgroundColor: [
                                    '#17a673',
                                    '#2c9faf',
                                    '#dda20a',
                                    '#be2617'
                                ],
                                hoverBorderColor: "rgba(234, 236, 244, 1)",
                            }],
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function (context) {
                                            const label = context.label || '';
                                            const value = context.raw || 0;
                                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                            const percentage = Math.round((value / total) * 100);
                                            return `${label}: ${value} (${percentage}%)`;
                                        }
                                    }
                                }
                            }
                        }
                    });
            }
        }   
        
        function initCEPautocomplete() {
            const cepField = document.getElementById('cep');
            
            if (cepField && !cepField.hasAttribute('data-cep-initialized')) {
                // Máscara para CEP
                cepField.addEventListener('input', function(e) {
                    let value = e.target.value.replace(/\D/g, '');
                    if (value.length > 5) value = value.substring(0, 5) + '-' + value.substring(5);
                    e.target.value = value.substring(0, 9);
                });

                // Autopreenchimento
                cepField.addEventListener('blur', function(e) {
                    const cep = e.target.value.replace(/\D/g, '');
                    if (cep.length !== 8) return;

                    e.target.disabled = true;
                    
                    fetch(`https://viacep.com.br/ws/${cep}/json/`)
                        .then(response => response.json())
                        .then(data => {
                            if (!data.erro) {
                                document.getElementById('logradouro').value = data.logradouro || '';
                                document.getElementById('bairro').value = data.bairro || '';
                                document.getElementById('uf').value = data.uf || '';
                                document.getElementById('numLocal').focus();
                            }
                        })
                        .catch(error => console.error('Erro:', error))
                        .finally(() => e.target.disabled = false);
                });

                cepField.setAttribute('data-cep-initialized', 'true');
            }
        }
        // Função para mostrar erro
        function showError(page) {
            document.getElementById('dynamic-content').innerHTML = `
            <div class="error-message">
                <i class="fas fa-exclamation-triangle"></i>
                <p>Erro ao carregar o conteúdo. Por favor, tente novamente.</p>
                <button onclick="loadContent('${page}')">Tentar novamente</button>
            </div>
            `;
        }
        document.addEventListener('DOMContentLoaded', function () {
            // Obtém o parâmetro da URL
            const params = new URLSearchParams(window.location.search);
            const pageParam = params.get('page') || 'visao-geral';
            const subpageParam = parseInt(params.get('subpage')) || 0;

            // Mostra loading screen e desativa os botões do menu
            showLoadingScreen(pageParam);
            toggleMenuButtons(true);

            // Remove a classe 'active' de todos os itens do menu primeiro
            document.querySelectorAll('.sidebar-nav .nav-link').forEach(link => {
                link.classList.remove('active');
            });

            // Marca apenas o item do menu correspondente como ativo
            const navLink = document.querySelector(`.nav-link[onclick*="${pageParam}"]`);
            if (navLink) {
                navLink.classList.add('active');
            }

            // Configura o timeout para carregar o conteúdo inicial
            setTimeout(() => {
                loadPageContent(pageParam);
                initCEPautocomplete();
                // Se a página tiver subpágina e for válida
                const subpages = ['usuarios', 'confeitarias', 'novos-adm'];
                if (subpages.includes(pageParam) && subpageParam >= 0) {
                    setTimeout(() => {
                        loadSubpageContent(pageParam, subpageParam);
                    }, 1000); // espera o carregamento da página principal
                }
            }, 1000);


            // Adiciona evento de pesquisa global
            document.getElementById('global-search').addEventListener('keyup', function (e) {
                if (e.key === 'Enter' && !isLoading) {
                    const termo = this.value.trim();
                    if (termo) {
                        alert('Funcionalidade de pesquisa será implementada');
                    }
                }
            });
        });
        document.getElementById('formCadastro').addEventListener('submit', function (e) {
            e.preventDefault(); // impede o envio padrão

            const form = e.target;
            const formData = new FormData(form);

            fetch('cadastro.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                document.getElementById('mensagem').innerHTML = data;
                form.reset(); // limpa o formulário
            })
            .catch(error => {
                console.error('Erro:', error);
                document.getElementById('mensagem').innerHTML = '<p style="color: red;">Erro ao cadastrar.</p>';
            });
        });

        
    </script>
</body>

</html>