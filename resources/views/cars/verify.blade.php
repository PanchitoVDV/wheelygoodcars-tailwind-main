<x-app-layout>
    <div class="py-12">
        <h1 class="text-3xl font-bold mb-8">Auto toevoegen</h1>
        
        <div class="max-w-2xl mx-auto bg-white p-6 rounded-lg shadow-md">
            <x-progress-bar :currentStep="2" :totalSteps="3" />
            
            <h2 class="text-xl font-semibold mb-6">Stap 2: Gegevens controleren en prijs invullen</h2>
            
            <div class="mb-6 bg-orange-50 border border-orange-200 text-orange-700 p-4 rounded">
                <p>We hebben de volgende gegevens gevonden voor kenteken <strong>{{ $car['license_plate'] }}</strong>. Controleer of deze correct zijn en vul de prijs en kilometrage in.</p>
            </div>
            
            @error('general')
                <div class="mb-6 bg-red-50 border border-red-400 text-red-700 p-4 rounded">
                    <p>{{ $message }}</p>
                </div>
            @enderror
            
            @auth
                <div class="mb-6 bg-green-50 border border-green-200 text-green-700 p-4 rounded">
                    <p>Je bent ingelogd als: <strong>{{ Auth::user()->name }}</strong> (ID: {{ Auth::id() }})</p>
                </div>
            @else
                <div class="mb-6 bg-red-50 border border-red-400 text-red-700 p-4 rounded">
                    <p class="font-bold mb-2">Let op: Je bent niet ingelogd!</p>
                    <p>Je moet <a href="{{ route('login') }}" class="underline text-blue-600">inloggen</a> om een auto toe te kunnen voegen.</p>
                </div>
            @endauth
            
            <form action="{{ route('cars.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <!-- Forceer user_id zelfs als Auth::id() niet werkt -->
                <input type="hidden" name="user_id" value="{{ Auth::id() }}">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kenteken</label>
                        <input type="text" name="license_plate" value="{{ $car['license_plate'] }}" 
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm bg-gray-50" readonly>
                    </div>
                    
                    <div>
                        <label for="price" class="block text-sm font-medium text-gray-700 mb-1">Prijs (€) *</label>
                        <input type="number" name="price" id="price" step="0.01" min="0"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500"
                               required>
                        @error('price')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Merk</label>
                        <input type="text" name="brand" value="{{ $car['brand'] }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm bg-gray-50" readonly>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Model</label>
                        <input type="text" name="model" value="{{ $car['model'] }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm bg-gray-50" readonly>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Bouwjaar</label>
                        <input type="number" name="production_year" value="{{ $car['production_year'] }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm bg-gray-50" readonly>
                    </div>
                    
                    <div>
                        <label for="mileage" class="block text-sm font-medium text-gray-700 mb-1">Kilometrage *</label>
                        <input type="number" name="mileage" id="mileage" min="0"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500"
                               required>
                        @error('mileage')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kleur</label>
                        <input type="text" name="color" value="{{ $car['color'] }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm bg-gray-50" readonly>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Aantal zitplaatsen</label>
                        <input type="number" name="seats" value="{{ $car['seats'] }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm bg-gray-50" readonly>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Aantal deuren</label>
                        <input type="number" name="doors" value="{{ $car['doors'] }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm bg-gray-50" readonly>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Gewicht (kg)</label>
                        <input type="number" name="weight" value="{{ $car['weight'] }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm bg-gray-50" readonly>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Brandstoftype</label>
                        <input type="text" name="fuel_type" value="{{ $car['fuel_type'] }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm bg-gray-50" readonly>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Cilinderinhoud (cc)</label>
                        <input type="number" name="engine_capacity" value="{{ $car['engine_capacity'] }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm bg-gray-50" readonly>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Vermogen (kW)</label>
                        <input type="number" name="power_kw" value="{{ $car['power_kw'] }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm bg-gray-50" readonly>
                    </div>
                </div>
                
                <div class="mb-6">
                    <label for="image" class="block text-sm font-medium text-gray-700 mb-1">Foto (optioneel)</label>
                    <input type="file" name="image" id="image" accept="image/*"
                           class="mt-1 block w-full text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100">
                    @error('image')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="flex justify-between mt-8">
                    <a href="{{ route('cars.create') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded">
                        Terug
                    </a>
                    <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white font-bold py-2 px-4 rounded">
                        Volgende: Tags toevoegen
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout> 