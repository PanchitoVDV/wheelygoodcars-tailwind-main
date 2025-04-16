<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Realtime Dashboard - {{ config('app.name') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        body {
            background-color: #1a1a1a;
            color: #e0e0e0;
            overflow: hidden;
        }
        
        .dashboard-container {
            padding: 20px;
        }
        
        .card {
            background-color: #2a2a2a;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
            transition: all 0.3s ease;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.4);
        }
        
        .card-header {
            font-weight: 600;
            border-bottom: 1px solid #3a3a3a;
        }
        
        .stat-value {
            font-size: 2rem;
            font-weight: 700;
        }
        
        .progress-bar {
            height: 10px;
            border-radius: 5px;
            overflow: hidden;
        }
        
        .chart-container {
            position: relative;
            height: 250px;
        }
        
        .last-updated {
            position: fixed;
            bottom: 10px;
            right: 10px;
            background-color: rgba(42, 42, 42, 0.8);
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 0.8rem;
        }
        
        .fullscreen-button {
            position: fixed;
            top: 10px;
            right: 10px;
            background-color: rgba(42, 42, 42, 0.8);
            color: white;
            border: none;
            border-radius: 4px;
            padding: 5px 10px;
            cursor: pointer;
            z-index: 1000;
        }
    </style>
</head>
<body>
    <button id="fullscreenToggle" class="fullscreen-button">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M8 3H5a2 2 0 0 0-2 2v3m18 0V5a2 2 0 0 0-2-2h-3m0 18h3a2 2 0 0 0 2-2v-3M3 16v3a2 2 0 0 0 2 2h3"></path>
        </svg>
    </button>
    
    <div class="dashboard-container">
        <div class="mb-6 flex justify-between items-center">
            <h1 class="text-3xl font-bold text-white">WeelyGoodCars Realtime Dashboard</h1>
            <a href="{{ route('admin.dashboard') }}" class="text-blue-400 hover:text-blue-300">Terug naar admin</a>
        </div>
        
        <!-- Key Stats Row -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-6 mb-6">
            <div class="card p-4">
                <div class="text-gray-400 text-sm mb-1">Totaal auto's</div>
                <div class="stat-value text-blue-400" id="totalCars">0</div>
            </div>
            
            <div class="card p-4">
                <div class="text-gray-400 text-sm mb-1">Verkocht</div>
                <div class="stat-value text-green-400" id="soldCars">0</div>
            </div>
            
            <div class="card p-4">
                <div class="text-gray-400 text-sm mb-1">Vandaag toegevoegd</div>
                <div class="stat-value text-orange-400" id="carsAddedToday">0</div>
            </div>
            
            <div class="card p-4">
                <div class="text-gray-400 text-sm mb-1">Aantal aanbieders</div>
                <div class="stat-value text-purple-400" id="sellersCount">0</div>
            </div>
            
            <div class="card p-4">
                <div class="text-gray-400 text-sm mb-1">Views vandaag</div>
                <div class="stat-value text-yellow-400" id="viewsToday">0</div>
            </div>
            
            <div class="card p-4">
                <div class="text-gray-400 text-sm mb-1">Gemiddeld auto's/aanbieder</div>
                <div class="stat-value text-pink-400" id="averageCarsPerSeller">0</div>
            </div>
        </div>
        
        <!-- Progress Bar Section -->
        <div class="card p-4 mb-6">
            <div class="card-header pb-3 mb-4">Auto's beschikbaar vs. verkocht</div>
            <div class="mb-2 flex justify-between">
                <span id="availableCarsPercent">0%</span>
                <span id="soldCarsPercent">0%</span>
            </div>
            <div class="progress-bar bg-gray-700">
                <div id="availableProgressBar" class="h-full bg-blue-500" style="width: 0%"></div>
            </div>
            <div class="mt-2 flex justify-between text-sm text-gray-400">
                <span>Beschikbaar: <span id="availableCarsCount">0</span></span>
                <span>Verkocht: <span id="soldCarsCount">0</span></span>
            </div>
        </div>
        
        <!-- Charts Row -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <div class="card p-4">
                <div class="card-header pb-3 mb-4">Auto's per prijsklasse</div>
                <div class="chart-container">
                    <canvas id="priceRangeChart"></canvas>
                </div>
            </div>
            
            <div class="card p-4">
                <div class="card-header pb-3 mb-4">Top 5 automerken</div>
                <div class="chart-container">
                    <canvas id="brandChart"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Monthly Sales Chart -->
        <div class="card p-4">
            <div class="card-header pb-3 mb-4">Verkochte auto's per maand</div>
            <div class="chart-container">
                <canvas id="salesChart"></canvas>
            </div>
        </div>
    </div>
    
    <div class="last-updated text-gray-400">
        Laatst bijgewerkt: <span id="lastUpdated">-</span>
    </div>
    
    <script>
        // Chart Objects
        let priceRangeChart, brandChart, salesChart;
        let refreshInterval;
        
        // Toggle fullscreen
        document.getElementById('fullscreenToggle').addEventListener('click', () => {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen().catch(e => {
                    console.error(`Error attempting to enable fullscreen: ${e.message}`);
                });
            } else {
                if (document.exitFullscreen) {
                    document.exitFullscreen();
                }
            }
        });
        
        // Initialize charts
        function initCharts() {
            // Price Range Chart
            const priceRangeCtx = document.getElementById('priceRangeChart').getContext('2d');
            priceRangeChart = new Chart(priceRangeCtx, {
                type: 'bar',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'Aantal auto\'s',
                        data: [],
                        backgroundColor: [
                            'rgba(54, 162, 235, 0.6)',
                            'rgba(75, 192, 192, 0.6)',
                            'rgba(255, 206, 86, 0.6)',
                            'rgba(255, 159, 64, 0.6)',
                            'rgba(255, 99, 132, 0.6)'
                        ],
                        borderColor: [
                            'rgba(54, 162, 235, 1)',
                            'rgba(75, 192, 192, 1)',
                            'rgba(255, 206, 86, 1)',
                            'rgba(255, 159, 64, 1)',
                            'rgba(255, 99, 132, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                color: '#e0e0e0'
                            },
                            grid: {
                                color: 'rgba(255, 255, 255, 0.1)'
                            }
                        },
                        x: {
                            ticks: {
                                color: '#e0e0e0'
                            },
                            grid: {
                                display: false
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
            
            // Brand Chart
            const brandCtx = document.getElementById('brandChart').getContext('2d');
            brandChart = new Chart(brandCtx, {
                type: 'doughnut',
                data: {
                    labels: [],
                    datasets: [{
                        data: [],
                        backgroundColor: [
                            'rgba(54, 162, 235, 0.6)',
                            'rgba(75, 192, 192, 0.6)',
                            'rgba(255, 206, 86, 0.6)',
                            'rgba(255, 159, 64, 0.6)',
                            'rgba(255, 99, 132, 0.6)'
                        ],
                        borderColor: [
                            'rgba(54, 162, 235, 1)',
                            'rgba(75, 192, 192, 1)',
                            'rgba(255, 206, 86, 1)',
                            'rgba(255, 159, 64, 1)',
                            'rgba(255, 99, 132, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right',
                            labels: {
                                color: '#e0e0e0'
                            }
                        }
                    }
                }
            });
            
            // Sales Chart
            const salesCtx = document.getElementById('salesChart').getContext('2d');
            salesChart = new Chart(salesCtx, {
                type: 'line',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'Verkochte auto\'s',
                        data: [],
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 2,
                        tension: 0.3,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                color: '#e0e0e0'
                            },
                            grid: {
                                color: 'rgba(255, 255, 255, 0.1)'
                            }
                        },
                        x: {
                            ticks: {
                                color: '#e0e0e0'
                            },
                            grid: {
                                display: false
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            labels: {
                                color: '#e0e0e0'
                            }
                        }
                    }
                }
            });
        }
        
        // Update dashboard with new data
        function updateDashboard(data) {
            // Update simple stats
            document.getElementById('totalCars').textContent = data.totalCars;
            document.getElementById('soldCars').textContent = data.soldCars;
            document.getElementById('carsAddedToday').textContent = data.carsAddedToday;
            document.getElementById('sellersCount').textContent = data.sellersCount;
            document.getElementById('viewsToday').textContent = data.viewsToday;
            document.getElementById('averageCarsPerSeller').textContent = data.averageCarsPerSeller;
            document.getElementById('lastUpdated').textContent = data.timestamp;
            
            // Update progress bar
            const totalCars = data.totalCars;
            const soldCars = data.soldCars;
            const availableCars = data.availableCars;
            
            const soldPercent = totalCars > 0 ? Math.round((soldCars / totalCars) * 100) : 0;
            const availablePercent = 100 - soldPercent;
            
            document.getElementById('availableCarsPercent').textContent = `${availablePercent}%`;
            document.getElementById('soldCarsPercent').textContent = `${soldPercent}%`;
            document.getElementById('availableProgressBar').style.width = `${availablePercent}%`;
            document.getElementById('availableCarsCount').textContent = availableCars;
            document.getElementById('soldCarsCount').textContent = soldCars;
            
            // Update price range chart
            const priceRangeLabels = Object.keys(data.carsByPriceRange);
            const priceRangeData = Object.values(data.carsByPriceRange);
            
            priceRangeChart.data.labels = priceRangeLabels;
            priceRangeChart.data.datasets[0].data = priceRangeData;
            priceRangeChart.update();
            
            // Update brand chart
            const brandLabels = data.carsByBrand.map(item => item.brand);
            const brandData = data.carsByBrand.map(item => item.total);
            
            brandChart.data.labels = brandLabels;
            brandChart.data.datasets[0].data = brandData;
            brandChart.update();
            
            // Update sales chart
            const salesLabels = Object.keys(data.salesByMonth);
            const salesData = Object.values(data.salesByMonth);
            
            salesChart.data.labels = salesLabels;
            salesChart.data.datasets[0].data = salesData;
            salesChart.update();
        }
        
        // Fetch dashboard data from the server
        function fetchDashboardData() {
            fetch('{{ route('admin.dashboard-data') }}')
                .then(response => response.json())
                .then(data => {
                    updateDashboard(data);
                })
                .catch(error => {
                    console.error('Error fetching dashboard data:', error);
                });
        }
        
        // Initialize the dashboard
        document.addEventListener('DOMContentLoaded', function() {
            initCharts();
            fetchDashboardData();
            
            // Refresh data every 10 seconds
            refreshInterval = setInterval(fetchDashboardData, 10000);
            
            // Stop refreshing when page is not visible
            document.addEventListener('visibilitychange', function() {
                if (document.visibilityState === 'hidden') {
                    clearInterval(refreshInterval);
                } else {
                    fetchDashboardData();
                    refreshInterval = setInterval(fetchDashboardData, 10000);
                }
            });
        });
    </script>
</body>
</html> 