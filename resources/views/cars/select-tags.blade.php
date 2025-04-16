<x-app-layout>
    <div class="py-12">
        <h1 class="text-3xl font-bold mb-8">Auto toevoegen</h1>
        
        <div class="max-w-2xl mx-auto bg-white p-6 rounded-lg shadow-md">
            <x-progress-bar :currentStep="3" :totalSteps="3" />
            
            <h2 class="text-xl font-semibold mb-6">Stap 3: Tags selecteren</h2>
            
            <div class="mb-6 bg-orange-50 border border-orange-200 text-orange-700 p-4 rounded">
                <p>Selecteer tags die je auto het beste beschrijven. Tags helpen potentiële kopers de juiste auto te vinden.</p>
            </div>
            
            @error('general')
                <div class="mb-6 bg-red-50 border border-red-400 text-red-700 p-4 rounded">
                    <p>{{ $message }}</p>
                </div>
            @enderror
            
            <div class="mb-6 flex space-x-6">
                <div class="w-1/3">
                    @if(!empty($carData['image']))
                        <img src="{{ asset('storage/' . $carData['image']) }}" alt="{{ $carData['brand'] }} {{ $carData['model'] }}" class="w-full h-48 object-cover rounded-lg">
                    @else
                        <div class="w-full h-48 bg-gray-200 flex items-center justify-center rounded-lg">
                            <span class="text-gray-400 text-sm">Geen foto</span>
                        </div>
                    @endif
                </div>
                
                <div class="w-2/3">
                    <h3 class="text-lg font-semibold text-gray-800">{{ $carData['brand'] }} {{ $carData['model'] }}</h3>
                    <p class="text-sm text-gray-600">{{ $carData['production_year'] }} · {{ number_format($carData['mileage'], 0, ',', '.') }} km</p>
                    <p class="text-lg font-bold text-orange-500 mt-1">€{{ number_format($carData['price'], 2, ',', '.') }}</p>
                    
                    <div class="mt-4 grid grid-cols-2 gap-2 text-sm text-gray-600">
                        <div>
                            <span class="font-medium">Kenteken:</span> {{ $carData['license_plate'] }}
                        </div>
                        <div>
                            <span class="font-medium">Kleur:</span> {{ $carData['color'] }}
                        </div>
                        @if(!empty($carData['fuel_type']))
                        <div>
                            <span class="font-medium">Brandstof:</span> {{ $carData['fuel_type'] }}
                        </div>
                        @endif
                        @if(!empty($carData['engine_capacity']))
                        <div>
                            <span class="font-medium">Motor:</span> {{ $carData['engine_capacity'] }} cc
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <form action="{{ route('cars.finalize') }}" method="POST">
                @csrf
                
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-3">Selecteer tags voor je auto</h3>
                    
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                        @foreach($allTags as $tag)
                            <label class="flex items-start space-x-2 cursor-pointer group">
                                <input type="checkbox" name="tags[]" value="{{ $tag->id }}" 
                                        class="mt-1 rounded border-gray-300 text-orange-500 focus:border-orange-300 focus:ring focus:ring-orange-200 focus:ring-opacity-50">
                                <div>
                                    <span class="text-gray-700 group-hover:text-gray-900 font-medium">{{ $tag->name }}</span>
                                    <div class="inline-block ml-2 w-3 h-3 rounded-full" style="background-color: {{ $tag->color }}"></div>
                                </div>
                            </label>
                        @endforeach
                    </div>
                    
                    @error('tags')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="flex justify-between mt-8">
                    <a href="javascript:history.back()" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded">
                        Terug
                    </a>
                    <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white font-bold py-2 px-4 rounded">
                        Auto toevoegen
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout> 