<x-admin-layout>
    <x-slot name="title">Tag Statistieken</x-slot>
    
    <div class="mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                <div class="text-green-500 text-2xl font-bold">{{ $totalCars }}</div>
                <div class="text-gray-500 text-sm">Totaal aantal auto's</div>
            </div>
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="text-blue-500 text-2xl font-bold">{{ $availableCars }}</div>
                <div class="text-gray-500 text-sm">Beschikbare auto's</div>
            </div>
            <div class="bg-orange-50 border border-orange-200 rounded-lg p-4">
                <div class="text-orange-500 text-2xl font-bold">{{ $soldCars }}</div>
                <div class="text-gray-500 text-sm">Verkochte auto's</div>
            </div>
        </div>
    </div>
    
    <div class="overflow-x-auto">
        <table class="min-w-full bg-white border border-gray-200 rounded-lg">
            <thead>
                <tr class="bg-gray-50">
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tag</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Totaal gebruikt</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Beschikbare auto's</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Verkochte auto's</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Distributie</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($tagStats as $tag)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="h-4 w-4 rounded-full mr-2" style="background-color: {{ $tag->color }}"></div>
                                <div class="font-medium text-gray-900">
                                    {{ $tag->name }}
                                    @if($tag->is_featured)
                                        <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            Featured
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <div class="text-sm text-gray-900">{{ $tag->total_usage }}</div>
                            <div class="text-xs text-gray-500">{{ $totalCars > 0 ? round(($tag->total_usage / $totalCars) * 100, 1) : 0 }}%</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <div class="text-sm text-gray-900">{{ $tag->available_count }}</div>
                            <div class="text-xs text-gray-500">{{ $availableCars > 0 ? round(($tag->available_count / $availableCars) * 100, 1) : 0 }}%</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <div class="text-sm text-gray-900">{{ $tag->sold_count }}</div>
                            <div class="text-xs text-gray-500">{{ $soldCars > 0 ? round(($tag->sold_count / $soldCars) * 100, 1) : 0 }}%</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="w-full bg-gray-200 rounded-full h-2.5">
                                @if($tag->total_usage > 0)
                                    <div class="bg-blue-500 h-2.5 rounded-full" style="width: {{ ($tag->available_count / $tag->total_usage) * 100 }}%"></div>
                                @endif
                            </div>
                            <div class="flex justify-between text-xs text-gray-500 mt-1">
                                <span>Beschikbaar</span>
                                <span>Verkocht</span>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    <div class="mt-8">
        <h3 class="text-lg font-semibold mb-4">Informatie</h3>
        <p class="text-gray-600 text-sm">
            Deze pagina toont statistieken over het gebruik van tags bij auto's in het systeem. De percentages geven aan welk aandeel 
            van auto's in elke categorie (totaal, beschikbaar, verkocht) deze tag heeft. De distributiebalk toont de verhouding 
            tussen beschikbare en verkochte auto's voor elke tag.
        </p>
        <p class="text-gray-600 text-sm mt-2">
            Door deze statistieken te analyseren, kunt u zien welke eigenschappen populair zijn bij verkochte auto's
            vergeleken met de beschikbare auto's. Dit kan helpen bij het bepalen van welke kenmerken belangrijk zijn voor kopers.
        </p>
    </div>
</x-admin-layout> 