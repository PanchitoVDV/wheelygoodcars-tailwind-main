<x-app-layout>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h1 class="text-2xl font-bold text-gray-800 mb-6">Tags beheren voor {{ $car->brand }} {{ $car->model }}</h1>
                    
                    <div class="flex mb-6">
                        <div class="mr-6">
                            @if($car->image)
                                <img src="{{ asset('storage/' . $car->image) }}" alt="{{ $car->brand }} {{ $car->model }}" class="w-40 h-40 object-cover rounded-lg">
                            @else
                                <div class="w-40 h-40 bg-gray-200 flex items-center justify-center rounded-lg">
                                    <span class="text-gray-400 text-sm">Geen foto</span>
                                </div>
                            @endif
                        </div>
                        <div>
                            <div class="text-lg font-semibold text-gray-800">{{ $car->brand }} {{ $car->model }}</div>
                            <div class="text-lg font-bold text-orange-500">€{{ number_format($car->price, 2, ',', '.') }}</div>
                            <div class="mt-2 text-sm text-gray-600">
                                <p>{{ $car->production_year }} · {{ number_format($car->mileage, 0, ',', '.') }} km</p>
                                <p>{{ $car->license_plate }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <form action="{{ route('cars.update-tags', $car) }}" method="POST">
                        @csrf
                        
                        <div class="mb-6">
                            <h2 class="text-lg font-semibold text-gray-800 mb-3">Selecteer tags voor uw auto</h2>
                            <p class="text-sm text-gray-600 mb-4">Tags helpen potentiële kopers de juiste auto te vinden. Kies tags die de kenmerken van uw auto het beste beschrijven.</p>
                            
                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                                @foreach($allTags as $tag)
                                    <label class="flex items-start space-x-2 cursor-pointer group">
                                        <input type="checkbox" name="tags[]" value="{{ $tag->id }}" 
                                               class="mt-1 rounded border-gray-300 text-orange-500 focus:border-orange-300 focus:ring focus:ring-orange-200 focus:ring-opacity-50"
                                               {{ in_array($tag->id, $selectedTagIds) ? 'checked' : '' }}>
                                        <div>
                                            <span class="text-gray-700 group-hover:text-gray-900 font-medium">{{ $tag->name }}</span>
                                            <div class="inline-block ml-2 w-3 h-3 rounded-full" style="background-color: {{ $tag->color }}"></div>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                            
                            @error('tags')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="flex justify-between">
                            <a href="{{ route('cars.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 rounded-md font-semibold text-gray-700 hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                                Terug
                            </a>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-orange-500 rounded-md font-semibold text-white hover:bg-orange-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                                Tags opslaan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 