class InvoiceDashboard {
            constructor() {
                this.currentPeriod = 7;
                this.initEventListeners();
                this.initCharts();
                this.loadData();
            }

            initEventListeners() {
                document.querySelectorAll('.period-btn').forEach(btn => {
                    btn.addEventListener('click', (e) => {
                        document.querySelectorAll('.period-btn').forEach(b => b.classList.remove('active'));
                        e.target.classList.add('active');
                        this.currentPeriod = parseInt(e.target.dataset.period);
                        this.updateData();
                    });
                });
            }

            initCharts() {
                // Gráfico de evolución de ingresos
                const revenueCtx = document.getElementById('revenueChart').getContext('2d');
                this.revenueChart = new Chart(revenueCtx, {
                    type: 'line',
                    data: {
                        labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul'],
                        datasets: [{
                            label: 'Ingresos (€)',
                            data: [1200, 1900, 1500, 2200, 2800, 2100, 2600],
                            borderColor: '#4facfe',
                            backgroundColor: 'rgba(79, 172, 254, 0.1)',
                            borderWidth: 3,
                            fill: true,
                            tension: 0.4,
                            pointBackgroundColor: '#4facfe',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            pointRadius: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: 'rgba(0,0,0,0.05)'
                                },
                                ticks: {
                                    callback: function(value) {
                                        return '€' + value;
                                    }
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });

                // Gráfico de distribución por categoría
                const categoryCtx = document.getElementById('categoryChart').getContext('2d');
                this.categoryChart = new Chart(categoryCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Servicios', 'Productos', 'Consultoría', 'Mantenimiento'],
                        datasets: [{
                            data: [45, 25, 20, 10],
                            backgroundColor: [
                                '#4facfe',
                                '#667eea',
                                '#f093fb',
                                '#4bc0c8'
                            ],
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    padding: 20,
                                    usePointStyle: true
                                }
                            }
                        }
                    }
                });
            }

            updateData() {
                // Simular cambio de datos según el período
                const periodData = {
                    7: {
                        revenue: '€3,240',
                        invoices: 23,
                        avgInvoice: '€140.87',
                        clients: 18
                    },
                    30: {
                        revenue: '€15,847',
                        invoices: 127,
                        avgInvoice: '€124.78',
                        clients: 42
                    },
                    90: {
                        revenue: '€45,230',
                        invoices: 342,
                        avgInvoice: '€132.25',
                        clients: 68
                    },
                    365: {
                        revenue: '€189,340',
                        invoices: 1456,
                        avgInvoice: '€130.01',
                        clients: 156
                    }
                };

                const data = periodData[this.currentPeriod];
                document.getElementById('totalRevenue').textContent = data.revenue;
                document.getElementById('totalInvoices').textContent = data.invoices;
                document.getElementById('avgInvoice').textContent = data.avgInvoice;
                document.getElementById('activeClients').textContent = data.clients;

                // Actualizar gráficos con animación
                this.updateCharts();
            }

            updateCharts() {
                // Generar datos aleatorios para simular diferentes períodos
                const newRevenueData = Array.from({length: 7}, () => 
                    Math.floor(Math.random() * 2000) + 1000
                );
                
                this.revenueChart.data.datasets[0].data = newRevenueData;
                this.revenueChart.update('active');

                // Actualizar gráfico de categorías
                const newCategoryData = [
                    Math.floor(Math.random() * 30) + 30,
                    Math.floor(Math.random() * 20) + 15,
                    Math.floor(Math.random() * 25) + 15,
                    Math.floor(Math.random() * 15) + 5
                ];
                
                this.categoryChart.data.datasets[0].data = newCategoryData;
                this.categoryChart.update('active');
            }

            loadData() {
                // Simular carga de datos
                console.log('Cargando datos del dashboard...');
            }
        }

        // Inicializar dashboard
        const dashboard = new InvoiceDashboard();