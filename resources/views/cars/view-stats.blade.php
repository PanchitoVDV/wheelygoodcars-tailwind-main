<x-app-layout>
    <div class="py-12">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold">Weergavestatistieken</h1>
            <a href="{{ route('cars.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">
                Terug naar mijn auto's
            </a>
        </div>
        
        <div class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
            <div class="p-6 border-b border-gray-200">
                <div class="flex flex-col md:flex-row">
                    <div class="md:w-1/4 mb-4 md:mb-0">
                        @if($car->image)
                            <img src="{{ asset('storage/' . $car->image) }}" alt="{{ $car->brand }} {{ $car->model }}" class="w-full h-auto object-cover rounded">
                        @else
                            <div class="bg-gray-200 w-full h-48 flex items-center justify-center rounded">
                                <span class="text-gray-400">Geen foto</span>
                            </div>
                        @endif
                    </div>
                    <div class="md:w-3/4 md:pl-6">
                        <h2 class="text-2xl font-bold text-gray-800">{{ $car->brand }} {{ $car->model }}</h2>
                        <div class="mt-2 text-sm text-gray-600">Kenteken: {{ $car->license_plate }}</div>
                        <div class="mt-1 text-sm text-gray-600">Bouwjaar: {{ $car->production_year }}</div>
                        <div class="mt-1 text-sm text-gray-600">Kilometerstand: {{ number_format($car->mileage, 0, ',', '.') }} km</div>
                        <div class="mt-1 font-bold text-orange-500">Prijs: €{{ number_format($car->price, 2, ',', '.') }}</div>
                        
                        <div class="mt-4">
                            <div class="text-xl font-bold text-gray-800">Weergavestatistieken</div>
                            <div class="flex items-center mt-2">
                                <div class="text-3xl font-bold text-blue-600">{{ number_format($car->views, 0, ',', '.') }}</div>
                                <div class="ml-2 text-gray-600">totale weergaven</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="text-gray-500 text-sm mb-1">Populariteitsscore</div>
                <div class="flex items-end">
                    <div class="text-2xl font-bold text-blue-600">{{ $popularityScore }}</div>
                    <div class="ml-1 text-gray-500">/100</div>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                    <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $popularityScore }}%"></div>
                </div>
                <div class="text-xs text-gray-500 mt-2">
                    @if($popularityScore >= 80)
                        Zeer hoge interesse in jouw auto
                    @elseif($popularityScore >= 60)
                        Bovengemiddelde interesse
                    @elseif($popularityScore >= 40)
                        Gemiddelde interesse
                    @elseif($popularityScore >= 20)
                        Matige interesse
                    @else
                        Lage interesse
                    @endif
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="text-gray-500 text-sm mb-1">Vergelijking met soortgelijke auto's</div>
                <div class="flex items-end">
                    <div class="text-2xl font-bold {{ $car->views > $similarCarsAvgViews ? 'text-green-600' : 'text-red-600' }}">
                        {{ $car->views > $similarCarsAvgViews ? '+' : '' }}{{ round(($car->views / max(1, $similarCarsAvgViews) * 100) - 100) }}%
                    </div>
                </div>
                <div class="flex items-center justify-between mt-2">
                    <div class="text-xs text-gray-500">Gemiddeld: {{ $similarCarsAvgViews }} views</div>
                    <div class="text-xs text-gray-500">Jouw auto: {{ $car->views }} views</div>
                </div>
                <div class="text-xs text-gray-500 mt-2">
                    @if($car->views > $similarCarsAvgViews * 1.5)
                        Veel meer interesse dan vergelijkbare auto's
                    @elseif($car->views > $similarCarsAvgViews)
                        Meer interesse dan vergelijkbare auto's
                    @elseif($car->views >= $similarCarsAvgViews * 0.8)
                        Vergelijkbare interesse
                    @else
                        Minder interesse dan vergelijkbare auto's
                    @endif
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="text-gray-500 text-sm mb-1">Potentiële kopers</div>
                <div class="flex items-end">
                    <div class="text-2xl font-bold text-green-600">{{ $potentialBuyers }}</div>
                    <div class="ml-1 text-gray-500">bezoekers</div>
                </div>
                <div class="flex items-center mt-2">
                    <div class="text-xs text-gray-500">{{ $viewsPercentage }}% van alle bezoekers</div>
                </div>
                <div class="text-xs text-gray-500 mt-2">
                    @if($viewsPercentage >= 35)
                        Zeer hoge conversie
                    @elseif($viewsPercentage >= 30)
                        Goede conversie
                    @else
                        Normale conversie
                    @endif
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="text-gray-500 text-sm mb-1">Advies</div>
                @if($car->views < 20)
                    <div class="text-sm font-medium text-orange-600">Voeg meer details toe</div>
                    <div class="text-xs text-gray-500 mt-2">
                        Je auto heeft weinig weergaven. Voeg meer details en betere foto's toe om de aandacht te trekken.
                    </div>
                @elseif($popularityScore < 40)
                    <div class="text-sm font-medium text-orange-600">Overweeg prijsaanpassing</div>
                    <div class="text-xs text-gray-500 mt-2">
                        Je auto krijgt bekijks maar de interesse blijft achter. Overweeg je prijs aan te passen.
                    </div>
                @elseif($viewsPercentage < 25)
                    <div class="text-sm font-medium text-orange-600">Verbeter je advertentie</div>
                    <div class="text-xs text-gray-500 mt-2">
                        Je auto trekt aandacht, maar weinig serieuze kopers. Verbeter je beschrijving.
                    </div>
                @else
                    <div class="text-sm font-medium text-green-600">Goede advertentie</div>
                    <div class="text-xs text-gray-500 mt-2">
                        Je advertentie presteert goed. Blijf geduldig en houd contact met geïnteresseerden.
                    </div>
                @endif
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Weergaven per dag (laatste 7 dagen)</h3>
                <div class="h-64">
                    <canvas id="viewsChart"></canvas>
                </div>
            </div>
        </div>
        
        <div class="mt-8 text-center">
            <a href="{{ route('cars.index') }}" class="bg-orange-500 hover:bg-orange-600 text-white font-bold py-2 px-4 rounded">
                Terug naar mijn auto's
            </a>
        </div>
    </div>

    <!-- ChartJS -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('viewsChart').getContext('2d');
            const viewsChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: [
                        @foreach($dailyViews as $date => $views)
                            '{{ \Carbon\Carbon::parse($date)->format('d M') }}',
                        @endforeach
                    ],
                    datasets: [{
                        label: 'Aantal weergaven',
                        data: [
                            @foreach($dailyViews as $views)
                                {{ $views }},
                            @endforeach
                        ],
                        backgroundColor: 'rgba(59, 130, 246, 0.5)',
                        borderColor: 'rgba(59, 130, 246, 1)',
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
                                precision: 0
                            }
                        }
                    }
                }
            });
        });
    </script>
</x-app-layout> 