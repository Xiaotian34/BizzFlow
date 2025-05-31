class InvoiceDashboard {
    constructor() {
        this.currentPeriod = 7;
        this.initEventListeners();
        //                this.initCharts();
        // this.loadData();
    }

    initEventListeners() {
        document.querySelectorAll('.period-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                this.currentPeriod = parseInt(e.target.dataset.period);
                window.location.search = '?controlador=documentos&action=estadisticas&period=' + this.currentPeriod;
            });
        });
    }

    // initCharts() {
    //     // Gráfico de evolución de ingresos
    //     const revenueCtx = document.getElementById('revenueChart');
    //     this.revenueChart = new Chart(revenueCtx, {
    //         type: 'line',
    //         data: {
    //             labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul'],
    //             datasets: [{
    //                 label: 'Ingresos (€)',
    //                 data: [1200, 1900, 1500, 2200, 2800, 2100, 2600],
    //                 borderColor: '#4facfe',
    //                 backgroundColor: 'rgba(79, 172, 254, 0.1)',
    //                 borderWidth: 3,
    //                 fill: true,
    //                 tension: 0.4,
    //                 pointBackgroundColor: '#4facfe',
    //                 pointBorderColor: '#fff',
    //                 pointBorderWidth: 2,
    //                 pointRadius: 6
    //             }]
    //         },
    //         options: {
    //             responsive: true,
    //             maintainAspectRatio: false,
    //             plugins: {
    //                 legend: {
    //                     display: false
    //                 }
    //             },
    //             scales: {
    //                 y: {
    //                     beginAtZero: true,
    //                     grid: {
    //                         color: 'rgba(0,0,0,0.05)'
    //                     },
    //                     ticks: {
    //                         callback: function(value) {
    //                             return '€' + value;
    //                         }
    //                     }
    //                 },
    //                 x: {
    //                     grid: {
    //                         display: false
    //                     }
    //                 }
    //             }
    //         }
    //     });

    //     // Gráfico de distribución por categoría
    //     const categoryCtx = document.getElementById('categoryChart').getContext('2d');
    //     this.categoryChart = new Chart(categoryCtx, {
    //         type: 'doughnut',
    //         data: {
    //             labels: ['Servicios', 'Productos', 'Consultoría', 'Mantenimiento'],
    //             datasets: [{
    //                 data: [45, 25, 20, 10],
    //                 backgroundColor: [
    //                     '#4facfe',
    //                     '#667eea',
    //                     '#f093fb',
    //                     '#4bc0c8'
    //                 ],
    //                 borderWidth: 0
    //             }]
    //         },
    //         options: {
    //             responsive: true,
    //             maintainAspectRatio: false,
    //             plugins: {
    //                 legend: {
    //                     position: 'bottom',
    //                     labels: {
    //                         padding: 20,
    //                         usePointStyle: true
    //                     }
    //                 }
    //             }
    //         }
    //     });
    // }

    // updateCharts() {
    //     // Generar datos aleatorios para simular diferentes períodos
    //     const newRevenueData = Array.from({length: 7}, () => 
    //         Math.floor(Math.random() * 2000) + 1000
    //     );

    //     this.revenueChart.data.datasets[0].data = newRevenueData;
    //     this.revenueChart.update('active');

    //     // Actualizar gráfico de categorías
    //     const newCategoryData = [
    //         Math.floor(Math.random() * 30) + 30,
    //         Math.floor(Math.random() * 20) + 15,
    //         Math.floor(Math.random() * 25) + 15,
    //         Math.floor(Math.random() * 15) + 5
    //     ];

    //     this.categoryChart.data.datasets[0].data = newCategoryData;
    //     this.categoryChart.update('active');
    // }

    // loadData() {
    //     // Simular carga de datos
    //     console.log('Cargando datos del dashboard...');
    // }
}

//dependiendo del período seleccionado, se activa el botón correspondiente
document.addEventListener('DOMContentLoaded', function() {
        // Obtener el parámetro 'period' de la URL
        const params = new URLSearchParams(window.location.search);
        const period = params.get('period') || '7';

        switch (period) {
            case '7':
                document.getElementById('1').classList.add('active');
                document.getElementById('2').classList.remove('active');
                document.getElementById('3').classList.remove('active');
                document.getElementById('4').classList.remove('active');
                break;
            case '30':
                document.getElementById('1').classList.remove('active');
                document.getElementById('2').classList.add('active');
                document.getElementById('3').classList.remove('active');
                document.getElementById('4').classList.remove('active');
                break;
            case '90':
                document.getElementById('1').classList.remove('active');
                document.getElementById('2').classList.remove('active');
                document.getElementById('3').classList.add('active');
                document.getElementById('4').classList.remove('active');
                break;
            case '365':
                document.getElementById('1').classList.remove('active');
                document.getElementById('2').classList.remove('active');
                document.getElementById('3').classList.remove('active');
                document.getElementById('4').classList.add('active');
                break;
            default:
                console.log('Período personalizado o no definido');
        }
    });

// Inicializar dashboard
const dashboard = new InvoiceDashboard();