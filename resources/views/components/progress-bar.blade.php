@props(['currentStep' => 1, 'totalSteps' => 3])

<div class="mb-8">
    <div class="flex justify-between items-center mb-2">
        <span class="text-xs font-medium text-gray-500">Stap {{ $currentStep }} van {{ $totalSteps }}</span>
        <span class="text-xs font-medium text-gray-500">{{ round(($currentStep / $totalSteps) * 100) }}% voltooid</span>
    </div>
    
    <div class="w-full bg-gray-200 rounded-full h-2.5">
        <div class="bg-orange-500 h-2.5 rounded-full" style="width: {{ ($currentStep / $totalSteps) * 100 }}%"></div>
    </div>
    
    <div class="flex justify-between mt-2">
        @for($i = 1; $i <= $totalSteps; $i++)
            <div class="flex flex-col items-center">
                <div class="{{ $i <= $currentStep ? 'bg-orange-500 text-white' : 'bg-gray-200 text-gray-500' }} 
                           rounded-full flex items-center justify-center w-6 h-6 text-xs font-bold">
                    {{ $i }}
                </div>
                <span class="text-xs mt-1 {{ $i <= $currentStep ? 'text-orange-500 font-medium' : 'text-gray-500' }}">
                    @if($i == 1)
                        Kenteken
                    @elseif($i == 2)
                        Gegevens
                    @elseif($i == 3)
                        Tags
                    @endif
                </span>
            </div>
        @endfor
    </div>
</div> 