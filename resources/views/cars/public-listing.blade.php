<x-app-layout>
    <div class="py-12">
        <h1 class="text-3xl font-bold mb-8">Beschikbare auto's</h1>
        
        <div class="flex flex-col md:flex-row gap-8 mb-8">
            <!-- Search Bar -->
            <div class="w-full md:w-1/2 lg:w-1/3">
                <div class="relative">
                    <input 
                        type="text" 
                        id="car-search" 
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 pl-10"
                        placeholder="Zoek op merk of model..."
                        value="{{ request('search') }}"
                    >
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>

                    <button 
                        id="clear-search" 
                        type="button" 
                        class="absolute inset-y-0 right-0 pr-3 flex items-center {{ request('search') ? '' : 'hidden' }}" 
                        title="Zoekopdracht wissen"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 hover:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>

                    <div id="search-loader" class="absolute inset-y-0 right-0 pr-3 flex items-center hidden">
                        <svg class="animate-spin h-5 w-5 text-orange-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                </div>
            </div>
            
            <!-- Tag Filters -->
            <div class="w-full md:w-1/2 lg:w-2/3">
                <div class="bg-white p-4 rounded-lg shadow-sm">
                    <h3 class="text-sm font-medium text-gray-700 mb-2">Filter op kenmerken:</h3>
                    <div class="flex flex-wrap gap-2" id="tag-filters">
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
                            <button id="clear-filters" class="text-sm text-orange-600 hover:text-orange-800 ml-2">
                                Filters wissen
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <style>
            /* Add playful animations */
            @keyframes float {
                0%, 100% { transform: translateY(0px); }
                50% { transform: translateY(-5px); }
            }
            .float-animation:hover {
                animation: float 2s ease-in-out infinite;
            }
            
            /* Random background patterns for featured cars */
            .pattern-dots {
                background-image: radial-gradient(#fd8e1e22 1px, transparent 1px);
                background-size: 10px 10px;
            }
            .pattern-lines {
                background-image: linear-gradient(to right, #fd8e1e11 1px, transparent 1px);
                background-size: 10px 10px;
            }
            .pattern-grid {
                background-image: linear-gradient(to right, #fd8e1e11 1px, transparent 1px),
                                  linear-gradient(to bottom, #fd8e1e11 1px, transparent 1px);
                background-size: 20px 20px;
            }
        </style>
        
        <!-- Car Grid Container - This will be updated by AJAX -->
        <div id="car-grid-container">
            @include('cars.partials.car-grid')
        </div>
    </div>
    
    <script>
        // Wait for the DOM to be fully loaded
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('car-search');
            const gridContainer = document.getElementById('car-grid-container');
            const searchLoader = document.getElementById('search-loader');
            const clearButton = document.getElementById('clear-search');
            const clearFiltersButton = document.getElementById('clear-filters');
            const tagFilters = document.querySelectorAll('.tag-filter');
            let searchTimer;
            
            // Add event listener for search input
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimer);
                const query = this.value.trim();
                
                // Show/hide clear button based on input
                if (query) {
                    clearButton.classList.remove('hidden');
                } else {
                    clearButton.classList.add('hidden');
                }
                
                // Show loading indicator
                searchLoader.classList.remove('hidden');
                clearButton.classList.add('hidden');
                
                // Debounce to prevent too many requests
                searchTimer = setTimeout(function() {
                    fetchResults();
                }, 300);
            });
            
            // Add event listener for clear button
            clearButton.addEventListener('click', function() {
                searchInput.value = '';
                clearButton.classList.add('hidden');
                fetchResults();
            });
            
            // Add event listeners for tag filters
            tagFilters.forEach(filter => {
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
                    
                    fetchResults();
                });
            });
            
            // Add event listener for clear filters button
            if (clearFiltersButton) {
                clearFiltersButton.addEventListener('click', function() {
                    tagFilters.forEach(filter => {
                        filter.checked = false;
                        const labelSpan = filter.nextElementSibling;
                        labelSpan.classList.remove('bg-orange-500', 'text-white');
                        labelSpan.classList.add('bg-gray-100', 'text-gray-800');
                    });
                    
                    fetchResults();
                });
            }
            
            // Make fetchResults function available globally for AJAX-loaded content
            window.fetchResults = fetchResults;
            
            // Set a flag to indicate we should update filters from AJAX
            window.updateFiltersFromAjax = true;
            
            function fetchResults() {
                // Show loading indicator
                searchLoader.classList.remove('hidden');
                
                // Get search query
                const query = searchInput.value.trim();
                
                // Get selected tag IDs
                const selectedTagIds = Array.from(tagFilters)
                    .filter(filter => filter.checked)
                    .map(filter => filter.value);
                
                // Construct the URL with search and tag parameters
                let url = `{{ route('cars.public') }}?search=${encodeURIComponent(query)}`;
                
                // Add tag filters to URL if there are any
                if (selectedTagIds.length > 0) {
                    selectedTagIds.forEach(tagId => {
                        url += `&tags[]=${tagId}`;
                    });
                }
                
                // Use fetch API to make the AJAX request
                fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.text())
                .then(html => {
                    // Update the grid container with new results
                    gridContainer.innerHTML = html;
                    
                    // Hide loading indicator
                    searchLoader.classList.add('hidden');
                    
                    // Show clear button if there's a query
                    if (query) {
                        clearButton.classList.remove('hidden');
                    }
                    
                    // Update browser URL without reloading the page
                    window.history.pushState({ path: url }, '', url);
                })
                .catch(error => {
                    console.error('Error fetching search results:', error);
                    searchLoader.classList.add('hidden');
                    if (query) {
                        clearButton.classList.remove('hidden');
                    }
                });
            }
        });
    </script>
</x-app-layout> 