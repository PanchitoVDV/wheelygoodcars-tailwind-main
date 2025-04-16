<x-admin-layout>
    <x-slot name="title">Opvallende aanbieders</x-slot>
    
    <div class="bg-orange-50 border border-orange-200 text-orange-700 p-4 rounded mb-6">
        <p class="font-bold">Let op:</p>
        <p>Op deze pagina worden aanbieders getoond die mogelijk opvallend gedrag vertonen. De volgende criteria worden gehanteerd:</p>
        <ul class="list-disc pl-5 mt-2">
            <li>Geen telefoonnummer ingevuld</li>
            <li>Auto('s) met hoge leeftijd maar lage kilometerstand (mogelijk sjoemelen)</li>
            <li>Meer dan 3 auto's op dezelfde dag als verkocht aangemerkt, boven €10.000 (mogelijk witwassen)</li>
            <li>Alleen auto's met vraagprijs onder €1.000 (te mooi om waar te zijn)</li>
            <li>Auto's zonder tags</li>
            <li>Al een jaar geen nieuwe auto's aangeboden</li>
        </ul>
    </div>
    
    @if($suspiciousSellers->isEmpty())
        <div class="text-center p-8 bg-gray-50 rounded-lg">
            <p class="text-gray-500">Er zijn geen opvallende aanbieders gevonden.</p>
        </div>
    @else
        <div class="mb-6">
            <p class="text-gray-700">Gevonden opvallende aanbieders: {{ $suspiciousSellers->count() }}</p>
        </div>
        
        <div class="bg-white shadow overflow-hidden rounded-lg">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aanbieder</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statistieken</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Opvallende kenmerken</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acties</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($suspiciousSellers as $seller)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $seller->name }}</div>
                                        <div class="text-sm text-gray-500">ID: {{ $seller->id }}</div>
                                        <div class="text-sm text-gray-500">Lid sinds: {{ $seller->created_at->format('d-m-Y') }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $seller->email }}</div>
                                <div class="text-sm text-gray-500">
                                    @if(empty($seller->phone))
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            Geen telefoonnummer
                                        </span>
                                    @else
                                        {{ $seller->phone }}
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">Totaal auto's: {{ $seller->cars_count }}</div>
                                <div class="text-sm text-gray-500">Auto's zonder tags: {{ $seller->cars_without_tags_count }}</div>
                                <div class="text-sm text-gray-500">Auto's onder €1.000: {{ $seller->low_price_cars_count }}</div>
                                <div class="text-sm text-gray-500">Zelfde dag verkocht >€10.000: {{ $seller->high_price_same_day_sold_count }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <ul class="text-sm text-gray-900 list-disc pl-5">
                                    @php
                                        $oneYearAgo = now()->subYear();
                                        $hasRecentCars = $seller->cars->where('created_at', '>=', $oneYearAgo)->count() > 0;
                                        
                                        $hasSuspiciousCars = $seller->cars->contains(function($car) {
                                            $carAge = now()->year - ($car->production_year ?? now()->year);
                                            return $carAge > 10 && $car->mileage < 50000;
                                        });
                                        
                                        $allLowPriceCars = $seller->cars_count > 0 && $seller->cars_count === $seller->low_price_cars_count;
                                        $manySameDaySales = $seller->high_price_same_day_sold_count >= 3;
                                        $hasNoTags = $seller->cars_without_tags_count > 0;
                                        $hasPhoneNumber = !empty($seller->phone);
                                    @endphp
                                    
                                    @if(!$hasPhoneNumber)
                                        <li>Geen telefoonnummer ingevuld</li>
                                    @endif
                                    
                                    @if($hasSuspiciousCars)
                                        <li>Auto's met hoge leeftijd maar lage kilometerstand</li>
                                    @endif
                                    
                                    @if($manySameDaySales)
                                        <li>Meerdere dure auto's op dezelfde dag verkocht</li>
                                    @endif
                                    
                                    @if($allLowPriceCars)
                                        <li>Alleen goedkope auto's (onder €1.000)</li>
                                    @endif
                                    
                                    @if($hasNoTags)
                                        <li>Auto's zonder tags</li>
                                    @endif
                                    
                                    @if(!$hasRecentCars && $seller->cars_count > 0)
                                        <li>Al meer dan een jaar inactief</li>
                                    @endif
                                </ul>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <a href="#" class="text-blue-600 hover:text-blue-900 mr-3">Details</a>
                                <a href="#" class="text-red-600 hover:text-red-900">Blokkeren</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</x-admin-layout> 