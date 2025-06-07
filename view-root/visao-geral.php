<?php
session_start();
include_once '../controller/controller-usuario.php';
$usuarioController = new ControllerUsuario();

// Verifica se o usuário está logado como root (idTipoUsuario = 1)
if (!isset($_SESSION['idTipoUsuario']) || $_SESSION['idTipoUsuario'] != 1) {
    die("Acesso não autorizado");
}
?>
<!-- Conteúdo da Visão Geral -->
<div class="dashboard-overview">
    <!-- Cards -->
    <div class="cards-row">
        <div class="card primary">
            <div class="card-header">
                <span>Receita Total</span>
                <i class="fas fa-dollar-sign"></i>
            </div>
            <div class="card-body">R$ 24.500,00</div>
            <div class="card-footer">
                <i class="fas fa-arrow-up"></i> 12% desde o último mês
            </div>
        </div>
        
        <div class="card success">
            <div class="card-header">
                <span>Tarefas Concluídas</span>
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="card-body">24</div>
            <div class="card-footer">
                <i class="fas fa-arrow-up"></i> 4% desde a última semana
            </div>
        </div>
        
        <div class="card info">
            <div class="card-header">
                <span>Novos Clientes</span>
                <i class="fas fa-user-plus"></i>
            </div>
            <div class="card-body">8</div>
            <div class="card-footer">
                <i class="fas fa-arrow-down"></i> 2% desde ontem
            </div>
        </div>
        
        <div class="card warning">
            <div class="card-header">
                <span>Taxa de Permanência</span>
                <i class="fas fa-percentage"></i>
            </div>
            <div class="card-body">3.2%</div>
            <div class="card-footer">
                <i class="fas fa-arrow-up"></i> 0.8% desde ontem
            </div>
        </div>
    </div>
    
    <!-- Charts -->
    <div class="charts-row">
        <div class="chart-container">
            <div class="chart-header">
                Desempenho Mensal
            </div>
            <div class="chart-wrapper">
                <canvas id="lineChart"></canvas>
            </div>
        </div>
        
        <div class="chart-container">
            <div class="chart-header">
                Distribuição de Tarefas
            </div>
            <div class="chart-wrapper">
                <canvas id="pieChart"></canvas>
            </div>
        </div>
    </div>
    
    <!-- Projects Table -->
    <div class="projects-table">
        <div class="chart-header">
            Projetos Recentes
        </div>
        <table>
            <thead>
                <tr>
                    <th>Solicitação</th>
                    <th>Responsável</th>
                    <th>Data da Solicitação</th>
                    <th>Prazo</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Ajuste de CPF</td>
                    <td>João Silva</td>
                    <td>01/03/2024</td>
                    <td>15/04/2024</td>
                    <td><span class="status-badge status-in-progress">Em Progresso</span></td>
                </tr>
                <tr>
                    <td>Ajuste de CNPJ</td>
                    <td>Maria Souza</td>
                    <td>10/02/2024</td>
                    <td>30/03/2024</td>
                    <td><span class="status-badge status-completed">Concluído</span></td>
                </tr>
                <tr>
                    <td>Ajuste de CNPJ</td>
                    <td>Carlos Oliveira</td>
                    <td>20/03/2024</td>
                    <td>05/04/2024</td>
                    <td><span class="status-badge status-pending">Pendente</span></td>
                </tr>
                <tr>
                    <td>Ajuste de CPF</td>
                    <td>Ana Costa</td>
                    <td>05/03/2024</td>
                    <td>20/04/2024</td>
                    <td><span class="status-badge status-in-progress">Em Progresso</span></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<script>
// Inicializa os gráficos quando o conteúdo for carregado
document.addEventListener('DOMContentLoaded', function() {
    initCharts();
});

function initCharts() {
    // Gráfico de Linha - Desempenho Mensal
    const lineCtx = document.getElementById('lineChart').getContext('2d');
    const lineChart = new Chart(lineCtx, {
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
                        callback: function(value) {
                            return 'R$ ' + value.toLocaleString('pt-BR');
                        }
                    }
                }
            }
        }
    });

    // Gráfico de Pizza - Distribuição de Tarefas
    const pieCtx = document.getElementById('pieChart').getContext('2d');
    const pieChart = new Chart(pieCtx, {
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
                        label: function(context) {
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
</script>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Verifica se os elementos existem antes de tentar criar os gráficos
    const lineCanvas = document.getElementById('lineChart');
    const pieCanvas = document.getElementById('pieChart');
    
    if (!lineCanvas || !pieCanvas) {
        console.error('Elementos canvas não encontrados!');
        return;
    }
    
    try {
        initCharts();
    } catch (error) {
        console.error('Erro ao inicializar gráficos:', error);
    }
});

function initCharts() {
    // Gráfico de Linha - Desempenho Mensal
    const lineCtx = document.getElementById('lineChart').getContext('2d');
    new Chart(lineCtx, {
        // ... (seu código existente para o gráfico de linha)
    });

    // Gráfico de Pizza - Distribuição de Tarefas
    const pieCtx = document.getElementById('pieChart').getContext('2d');
    new Chart(pieCtx, {
        // ... (seu código existente para o gráfico de pizza)
    });
}
</script>
