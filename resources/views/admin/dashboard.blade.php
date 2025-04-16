<x-admin-layout>
    <x-slot name="title">Dashboard</x-slot>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
            <div class="text-blue-500 text-4xl font-bold">{{ $usersCount }}</div>
            <div class="text-gray-500 mt-2">Gebruikers</div>
        </div>
        
        <div class="bg-green-50 border border-green-200 rounded-lg p-6">
            <div class="text-green-500 text-4xl font-bold">{{ $carsCount }}</div>
            <div class="text-gray-500 mt-2">Auto's</div>
        </div>
        
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6">
            <div class="text-yellow-500 text-4xl font-bold">{{ $soldCarsCount }}</div>
            <div class="text-gray-500 mt-2">Verkochte auto's</div>
        </div>
        
        <div class="bg-purple-50 border border-purple-200 rounded-lg p-6">
            <div class="text-purple-500 text-4xl font-bold">{{ $tagsCount }}</div>
            <div class="text-gray-500 mt-2">Tags</div>
        </div>
    </div>
    
    <h2 class="text-xl font-semibold mb-4">Beheer opties</h2>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <a href="{{ route('admin.tags') }}" class="bg-white hover:bg-gray-50 border border-gray-200 rounded-lg p-6 flex items-center space-x-4">
            <div class="bg-blue-100 text-blue-500 p-3 rounded-full">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                </svg>
            </div>
            <div>
                <h3 class="font-semibold text-lg">Tag statistieken</h3>
                <p class="text-gray-500">Bekijk het gebruik van tags bij verkochte en niet-verkochte auto's</p>
            </div>
        </a>
        
        <a href="{{ route('admin.suspicious-sellers') }}" class="bg-white hover:bg-gray-50 border border-gray-200 rounded-lg p-6 flex items-center space-x-4">
            <div class="bg-red-100 text-red-500 p-3 rounded-full">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <div>
                <h3 class="font-semibold text-lg">Opvallende aanbieders</h3>
                <p class="text-gray-500">Bekijk en controleer aanbieders met verdacht gedrag</p>
            </div>
        </a>
        
        <a href="{{ route('admin.realtime-dashboard') }}" class="bg-white hover:bg-gray-50 border border-gray-200 rounded-lg p-6 flex items-center space-x-4">
            <div class="bg-green-100 text-green-500 p-3 rounded-full">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
            </div>
            <div>
                <h3 class="font-semibold text-lg">Realtime Dashboard</h3>
                <p class="text-gray-500">Bekijk realtime statistieken en grafieken over het auto-aanbod</p>
            </div>
        </a>
    </div>
</x-admin-layout> 