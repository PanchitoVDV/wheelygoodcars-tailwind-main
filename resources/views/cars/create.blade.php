<x-app-layout>
    <div class="py-12">
        <h1 class="text-3xl font-bold mb-8">Auto toevoegen</h1>
        
        <div class="max-w-md mx-auto bg-white p-6 rounded-lg shadow-md">
            <x-progress-bar :currentStep="1" :totalSteps="3" />
            
            <h2 class="text-xl font-semibold mb-6">Stap 1: Kenteken invullen</h2>
            
            <form action="{{ route('cars.verify') }}" method="POST">
                @csrf
                
                <div class="mb-4">
                    <label for="license_plate" class="block text-sm font-medium text-gray-700 mb-1">Kenteken</label>
                    <input type="text" name="license_plate" id="license_plate" 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500"
                           placeholder="Bijv. AB-12-CD" required>
                    @error('license_plate')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="flex justify-end mt-6">
                    <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white font-bold py-2 px-4 rounded">
                        Controleren
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout> 