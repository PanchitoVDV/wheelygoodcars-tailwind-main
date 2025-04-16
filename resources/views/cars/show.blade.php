<x-app-layout>
    <div class="py-12">
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="md:flex">
                <div class="md:w-1/2">
                    @if($car->image)
                        <img src="{{ asset('storage/' . $car->image) }}" alt="{{ $car->brand }} {{ $car->model }}" class="w-full h-96 object-cover">
                    @else
                        <div class="w-full h-96 bg-gray-200 flex items-center justify-center">
                            <span class="text-gray-400 text-lg">Geen foto beschikbaar</span>
                        </div>
                    @endif
                </div>
                <div class="md:w-1/2 p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h1 class="text-3xl font-bold text-gray-900">{{ $car->brand }} {{ $car->model }}</h1>
                    </div>
                    
                    <div class="text-2xl font-bold text-orange-500 mb-6">
                        €{{ number_format($car->price, 2, ',', '.') }}
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div>
                            <h2 class="text-sm text-gray-600">Bouwjaar</h2>
                            <p class="text-lg font-semibold">{{ $car->production_year }}</p>
                        </div>
                        <div>
                            <h2 class="text-sm text-gray-600">Kilometerstand</h2>
                            <p class="text-lg font-semibold">{{ number_format($car->mileage, 0, ',', '.') }} km</p>
                        </div>
                        <div>
                            <h2 class="text-sm text-gray-600">Kenteken</h2>
                            <p class="text-lg font-semibold">{{ $car->license_plate }}</p>
                        </div>
                        <div>
                            <h2 class="text-sm text-gray-600">Kleur</h2>
                            <p class="text-lg font-semibold">{{ $car->color }}</p>
                        </div>
                        @if($car->fuel_type)
                        <div>
                            <h2 class="text-sm text-gray-600">Brandstof</h2>
                            <p class="text-lg font-semibold">{{ $car->fuel_type }}</p>
                        </div>
                        @endif
                        @if($car->engine_capacity)
                        <div>
                            <h2 class="text-sm text-gray-600">Cilinderinhoud</h2>
                            <p class="text-lg font-semibold">{{ $car->engine_capacity }} cc</p>
                        </div>
                        @endif
                        @if($car->power_kw)
                        <div>
                            <h2 class="text-sm text-gray-600">Vermogen</h2>
                            <p class="text-lg font-semibold">{{ $car->power_kw }} kW ({{ round($car->power_kw * 1.36) }} pk)</p>
                        </div>
                        @endif
                        @if($car->doors)
                        <div>
                            <h2 class="text-sm text-gray-600">Aantal deuren</h2>
                            <p class="text-lg font-semibold">{{ $car->doors }}</p>
                        </div>
                        @endif
                        @if($car->seats)
                        <div>
                            <h2 class="text-sm text-gray-600">Aantal zitplaatsen</h2>
                            <p class="text-lg font-semibold">{{ $car->seats }}</p>
                        </div>
                        @endif
                        @if($car->weight)
                        <div>
                            <h2 class="text-sm text-gray-600">Gewicht</h2>
                            <p class="text-lg font-semibold">{{ number_format($car->weight, 0, ',', '.') }} kg</p>
                        </div>
                        @endif
                    </div>
                    
                    <div class="mt-8">
                        <a href="{{ route('cars.public') }}" class="inline-block bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-2 px-4 rounded mr-2">
                            Terug naar overzicht
                        </a>
                        <button class="bg-orange-500 hover:bg-orange-600 text-white font-semibold py-2 px-4 rounded">
                            Contact opnemen
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- View Toast Notification -->
    <div id="viewToast" class="hidden fixed bottom-4 right-4 bg-white shadow-lg rounded-lg p-4 max-w-md transform translate-y-12 opacity-0 transition-all duration-300 ease-in-out z-50">
        <div class="flex">
            <div class="bg-orange-100 p-2 rounded-full mr-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
            </div>
            <div>
                <div class="flex justify-between items-center mb-1">
                    <h3 class="font-bold text-gray-800">Populaire auto!</h3>
                    <button onclick="closeToast()" class="text-gray-400 hover:text-gray-500 focus:outline-none ml-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <p class="text-sm text-gray-600"><strong>{{ $dailyViews }} {{ $dailyViews == 1 ? 'klant heeft' : 'klanten hebben' }}</strong> deze auto vandaag bekeken.</p>
                <p class="text-sm text-gray-600 mt-1"><strong>{{ $potentialBuyers }} {{ $potentialBuyers == 1 ? 'persoon overweegt' : 'mensen overwegen' }}</strong> deze auto te kopen.</p>
                <p class="text-xs text-gray-500 mt-1">
                    @if($dailyViews > 15)
                        Deze auto is extreem populair! Wees er snel bij.
                    @elseif($dailyViews > 8)
                        Deze auto is erg in trek! Neem snel contact op.
                    @elseif($dailyViews > 3)
                        Deze auto trekt veel bekijks.
                    @else
                        Bekijk deze auto nu!
                    @endif
                </p>
            </div>
        </div>
    </div>

    <script>
        // Show toast notification after 10 seconds
        setTimeout(function() {
            const toast = document.getElementById('viewToast');
            toast.classList.remove('hidden');
            // Slight delay to ensure the transition works correctly
            setTimeout(() => {
                toast.classList.remove('translate-y-12', 'opacity-0');
            }, 50);
        }, 10000);
        
        function closeToast() {
            const toast = document.getElementById('viewToast');
            toast.classList.add('translate-y-12', 'opacity-0');
            setTimeout(() => {
                toast.classList.add('hidden');
            }, 300);
        }
    </script>
</x-app-layout> 