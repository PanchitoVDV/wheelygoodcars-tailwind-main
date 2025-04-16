@if(count($cars) > 0)
    <!-- Re-render the tag filters for AJAX updates -->
    <div id="tag-filters-container" class="hidden">
        <div class="flex flex-wrap gap-2">
            @foreach($tags as $tag)
                <label class="inline-flex items-center cursor-pointer">
                    <input 
                        type="checkbox" 
                        name="tags[]" 
                        value="{{ $tag->id }}" 
                        class="tag-filter hidden" 
                        {{ in_array($tag->id, $selectedTags) ? 'checked' : '' }}
                    >
                    <span class="px-3 py-1 rounded-full text-sm {{ in_array($tag->id, $selectedTags) 
                        ? 'bg-orange-500 text-white' 
                        : 'bg-gray-100 text-gray-800 hover:bg-gray-200' }}
                        transition-colors duration-200 ease-in-out">
                        {{ $tag->name }}
                    </span>
                </label>
            @endforeach
            
            @if(count($selectedTags) > 0)
                <button id="clear-filters-ajax" class="text-sm text-orange-600 hover:text-orange-800 ml-2">
                    Filters wissen
                </button>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 auto-rows-auto">
        @foreach($cars as $index => $car)
            @php
                $isFeatured = in_array($index, $featuredIndexes);
                $colSpan = $isFeatured ? 'sm:col-span-2' : '';
                $rowSpan = $isFeatured ? 'sm:row-span-2' : '';
                
                // Add variations for regular cards
                if (!$isFeatured) {
                    $rotationDegree = rand(-2, 2);
                    $rotation = "transform hover:rotate-{$rotationDegree} hover:scale-105";
                    $animation = rand(0, 1) ? 'float-animation' : '';
                } else {
                    $rotation = 'transform hover:scale-105';
                    $animation = 'hover:shadow-xl';
                }
                
                // Background patterns for featured cards
                if ($isFeatured) {
                    $patterns = ['pattern-dots', 'pattern-lines', 'pattern-grid'];
                    $pattern = $patterns[array_rand($patterns)];
                    $bgColor = "bg-orange-50 {$pattern}";
                } else {
                    $bgColor = 'bg-white';
                }
                
                $imageHeight = $isFeatured ? 'h-64 sm:h-80' : 'h-48';
                $fontSizeClass = $isFeatured ? 'text-2xl' : 'text-xl';
            @endphp

            <div class="{{ $colSpan }} {{ $rowSpan }}">
                <div class="{{ $bgColor }} shadow-md rounded-lg overflow-hidden hover:shadow-lg transition-all duration-500 {{ $rotation }} {{ $animation }} h-full">
                    <a href="{{ route('cars.show', $car) }}" class="block h-full flex flex-col">
                        @if($car->image)
                            <img src="{{ asset('storage/' . $car->image) }}" alt="{{ $car->brand }} {{ $car->model }}" class="w-full {{ $imageHeight }} object-cover">
                        @else
                            <div class="w-full {{ $imageHeight }} bg-gray-200 flex items-center justify-center">
                                <span class="text-gray-400">Geen foto beschikbaar</span>
                            </div>
                        @endif
                        
                        <div class="p-4 flex-grow">
                            <h2 class="{{ $fontSizeClass }} font-bold text-gray-900 mb-2">{{ $car->brand }} {{ $car->model }}</h2>
                            <div class="flex justify-between items-center">
                                <span class="text-lg font-bold text-orange-500">€{{ number_format($car->price, 2, ',', '.') }}</span>
                                <span class="text-sm text-gray-600">{{ $car->production_year }}</span>
                            </div>
                            <div class="mt-2 text-gray-600 text-sm">
                                <p>{{ number_format($car->mileage, 0, ',', '.') }} km</p>
                                @if($car->fuel_type)
                                    <p>{{ $car->fuel_type }}</p>
                                @endif
                            </div>
                            
                            <!-- Display tags -->
                            <div class="mt-4 flex flex-wrap gap-1">
                                <span class="inline-block bg-orange-100 text-orange-800 text-xs px-2 py-1 rounded-full">{{ $car->color }}</span>
                                
                                @foreach($car->tags as $tag)
                                    <span class="inline-block text-xs px-2 py-1 rounded-full" 
                                        style="background-color: {{ $tag->color }}22; color: {{ $tag->color }};">
                                        {{ $tag->name }}
                                    </span>
                                @endforeach
                                
                                @if($isFeatured)
                                    <span class="inline-block bg-orange-500 text-white text-xs px-2 py-1 rounded-full ml-2">Uitgelicht</span>
                                @endif
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        @endforeach
    </div>
    
    <!-- Pagination -->
    <div class="mt-8 flex justify-center">
        <div class="bg-white px-4 py-3 rounded-lg shadow-md">
            {{ $cars->appends(request()->query())->links() }}
        </div>
    </div>
    
    <script>
        // Update existing tag filters with the latest state when using AJAX
        document.addEventListener('DOMContentLoaded', function() {
            // Check if this is an AJAX response and update tag filters accordingly
            if (window.updateFiltersFromAjax) {
                const mainFiltersContainer = document.getElementById('tag-filters');
                const ajaxFiltersContainer = document.getElementById('tag-filters-container');
                
                if (mainFiltersContainer && ajaxFiltersContainer) {
                    mainFiltersContainer.innerHTML = ajaxFiltersContainer.firstElementChild.innerHTML;
                    
                    // Re-attach event listeners to newly inserted elements
                    const newTagFilters = mainFiltersContainer.querySelectorAll('.tag-filter');
                    newTagFilters.forEach(filter => {
                        filter.addEventListener('change', function() {
                            // Toggle active class on the parent label span
                            const labelSpan = this.nextElementSibling;
                            if (this.checked) {
                                labelSpan.classList.remove('bg-gray-100', 'text-gray-800');
                                labelSpan.classList.add('bg-orange-500', 'text-white');
                            } else {
                                labelSpan.classList.remove('bg-orange-500', 'text-white');
                                labelSpan.classList.add('bg-gray-100', 'text-gray-800');
                            }
                            
                            window.fetchResults();
                        });
                    });
                    
                    // Re-attach event listener to clear filters button
                    const clearFiltersButton = mainFiltersContainer.querySelector('#clear-filters-ajax');
                    if (clearFiltersButton) {
                        clearFiltersButton.id = 'clear-filters';
                        clearFiltersButton.addEventListener('click', function() {
                            newTagFilters.forEach(filter => {
                                filter.checked = false;
                                const labelSpan = filter.nextElementSibling;
                                labelSpan.classList.remove('bg-orange-500', 'text-white');
                                labelSpan.classList.add('bg-gray-100', 'text-gray-800');
                            });
                            
                            window.fetchResults();
                        });
                    }
                }
            }
        });
    </script>
@else
    <div class="bg-white rounded-lg shadow p-6 text-center">
        <p class="text-gray-600">Er zijn geen auto's gevonden die overeenkomen met je zoekopdracht.</p>
    </div>
@endif 