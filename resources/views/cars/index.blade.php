<x-app-layout>
    <div class="py-12">
        <h1 class="text-3xl font-bold mb-8">Mijn auto's</h1>
        
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif
        
        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif
        
        @if(count($cars) > 0)
            <div class="bg-white shadow-md rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Afbeelding
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Auto
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Kenteken
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Prijs
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Bouwjaar
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Km stand
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Weergaven
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Acties
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($cars as $car)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($car->image)
                                        <img src="{{ asset('storage/' . $car->image) }}" alt="{{ $car->brand }} {{ $car->model }}" class="h-16 w-20 object-cover rounded">
                                    @else
                                        <div class="h-16 w-20 bg-gray-200 flex items-center justify-center rounded">
                                            <span class="text-gray-400 text-xs">Geen foto</span>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $car->brand }}</div>
                                    <div class="text-sm text-gray-500">{{ $car->model }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $car->license_plate }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-bold text-orange-500">€{{ number_format($car->price, 2, ',', '.') }}</div>
                                    <div class="mt-2">
                                        <button 
                                            type="button" 
                                            onclick="showPriceUpdateDialog('{{ $car->id }}')"
                                            class="text-xs text-blue-700 hover:text-blue-900 underline"
                                        >
                                            Prijs aanpassen
                                        </button>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $car->production_year }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ number_format($car->mileage, 0, ',', '.') }} km</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex flex-col">
                                        <div class="text-sm text-gray-900 font-bold">{{ number_format($car->views, 0, ',', '.') }}</div>
                                        <div class="w-full bg-gray-200 rounded-full h-2 mt-1">
                                            @php
                                                $percentage = 0;
                                                if ($car->views > 0) {
                                                    if ($car->views < 50) {
                                                        $percentage = $car->views * 2;
                                                    } elseif ($car->views < 200) {
                                                        $percentage = 100 + (($car->views - 50) / 1.5);
                                                    } else {
                                                        $percentage = 200;
                                                    }
                                                }
                                                $percentage = min(100, $percentage);
                                            @endphp
                                            <div class="bg-orange-500 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                                        </div>
                                        <div class="text-xs text-gray-500 mt-1">
                                            @if($car->views >= 200)
                                                Zeer populair!
                                            @elseif($car->views >= 100)
                                                Populair
                                            @elseif($car->views >= 50)
                                                Goed bekeken
                                            @elseif($car->views >= 10)
                                                Enkele weergaven
                                            @else
                                                Weinig bekeken
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <a href="#" class="text-indigo-600 hover:text-indigo-900" title="Bekijken">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>
                                        <a href="{{ route('cars.view-stats', $car) }}" class="text-blue-600 hover:text-blue-900" title="Statistieken">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                            </svg>
                                        </a>
                                        @if($car->sold_at)
                                            <button type="button" 
                                                onclick="updateCarStatus('{{ $car->id }}', 'available')"
                                                class="text-green-600 hover:text-green-900" 
                                                title="Markeer als beschikbaar">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </button>
                                        @else
                                            <button type="button" 
                                                onclick="updateCarStatus('{{ $car->id }}', 'sold')"
                                                class="text-yellow-600 hover:text-yellow-900" 
                                                title="Markeer als verkocht">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </button>
                                        @endif
                                        <a href="#" class="text-indigo-600 hover:text-indigo-900" title="Bewerken">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>
                                        <a href="{{ route('cars.edit-tags', $car) }}" class="text-purple-600 hover:text-purple-900" title="Tags beheren">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                            </svg>
                                        </a>
                                        <button type="button" 
                                                onclick="document.getElementById('delete-car-{{ $car->id }}').classList.remove('hidden')"
                                                class="text-red-600 hover:text-red-900" 
                                                title="Verwijderen">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                        
                                        <!-- Verwijder bevestigingsdialoog -->
                                        <div id="delete-car-{{ $car->id }}" class="hidden fixed inset-0 flex items-center justify-center z-50 bg-black bg-opacity-50">
                                            <div class="bg-white p-6 rounded-lg shadow-lg max-w-md w-full">
                                                <h3 class="text-lg font-bold mb-4">Auto verwijderen</h3>
                                                <p class="mb-6">Weet je zeker dat je de {{ $car->brand }} {{ $car->model }} ({{ $car->license_plate }}) wilt verwijderen?</p>
                                                <div class="flex justify-end space-x-3">
                                                    <button type="button" 
                                                            onclick="document.getElementById('delete-car-{{ $car->id }}').classList.add('hidden')"
                                                            class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded">
                                                        Annuleren
                                                    </button>
                                                    <form action="{{ route('cars.destroy', $car) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded">
                                                            Verwijderen
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="bg-white rounded-lg shadow p-6 text-center">
                <p class="text-gray-600">Je hebt nog geen auto's toegevoegd.</p>
                <a href="{{ route('cars.create') }}" class="mt-4 inline-block bg-orange-500 hover:bg-orange-600 text-white font-bold py-2 px-4 rounded">
                    Auto toevoegen
                </a>
            </div>
        @endif
        
        <div class="mt-8 text-center">
            <a href="{{ route('cars.create') }}" class="bg-orange-500 hover:bg-orange-600 text-white font-bold py-2 px-4 rounded">
                Nieuwe auto toevoegen
            </a>
        </div>
        
        <!-- Prijs aanpassen dialoog -->
        <div id="price-update-dialog" class="hidden fixed inset-0 flex items-center justify-center z-50 bg-black bg-opacity-50">
            <div class="bg-white p-6 rounded-lg shadow-lg max-w-md w-full">
                <h3 class="text-lg font-bold mb-4">Prijs aanpassen</h3>
                <div class="mb-4">
                    <label for="new-price" class="block text-sm font-medium text-gray-700 mb-1">Nieuwe prijs (€)</label>
                    <input type="number" id="new-price" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-orange-500 focus:border-orange-500" step="0.01" min="0">
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" 
                            onclick="document.getElementById('price-update-dialog').classList.add('hidden')"
                            class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded">
                        Annuleren
                    </button>
                    <button type="button" 
                            onclick="submitPriceUpdate()"
                            class="bg-orange-500 hover:bg-orange-600 text-white font-bold py-2 px-4 rounded">
                        Bijwerken
                    </button>
                </div>
            </div>
        </div>
        
        <!-- JavaScript voor AJAX calls -->
        <script>
            let currentCarId = null;
            
            function showPriceUpdateDialog(carId) {
                currentCarId = carId;
                document.getElementById('price-update-dialog').classList.remove('hidden');
                document.getElementById('new-price').focus();
            }
            
            function submitPriceUpdate() {
                const newPrice = document.getElementById('new-price').value;
                
                if (!newPrice || isNaN(newPrice) || parseFloat(newPrice) < 0) {
                    alert('Voer een geldige prijs in');
                    return;
                }
                
                updateCarPrice(currentCarId, newPrice);
            }
            
            function updateCarStatus(carId, status) {
                const endpoint = status === 'sold' 
                    ? `/cars/${carId}/mark-as-sold` 
                    : `/cars/${carId}/mark-as-available`;
                
                fetch(endpoint, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Pagina herladen om de wijzigingen weer te geven
                        location.reload();
                    } else {
                        alert(data.message || 'Er is een fout opgetreden');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Er is een fout opgetreden bij het bijwerken van de status');
                });
            }
            
            function updateCarPrice(carId, newPrice) {
                fetch(`/cars/${carId}/update-price`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ price: newPrice })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Dialoog sluiten
                        document.getElementById('price-update-dialog').classList.add('hidden');
                        // Pagina herladen om de wijzigingen weer te geven
                        location.reload();
                    } else {
                        alert(data.message || 'Er is een fout opgetreden');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Er is een fout opgetreden bij het bijwerken van de prijs');
                });
            }
        </script>
    </div>
</x-app-layout> 