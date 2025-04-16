<x-app-layout>
    <div class="py-12 text-center">
        <h1 class="text-5xl font-bold mb-6">Welkom bij <span class="text-orange-500">Wheely Good Cars</span>!</h1>
        <p class="text-xl mb-8 max-w-3xl mx-auto">Ontdek een breed aanbod van kwaliteitsvolle tweedehands auto's. Vind jouw nieuwe droomauto vandaag nog!</p>
        
        <div class="flex justify-center">
            <a href="{{ route('cars.public') }}" class="bg-orange-500 hover:bg-orange-600 text-white font-bold py-3 px-6 rounded-lg text-lg">
                Bekijk ons aanbod
            </a>
        </div>
        
        <div class="mt-16 grid grid-cols-1 md:grid-cols-3 gap-8 max-w-5xl mx-auto">
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="text-orange-500 mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold mb-2">Betrouwbaar</h3>
                <p class="text-gray-600">Alle auto's worden zorgvuldig gecontroleerd op kwaliteit en betrouwbaarheid.</p>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="text-orange-500 mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold mb-2">Scherp geprijsd</h3>
                <p class="text-gray-600">Wij bieden eerlijke prijzen zonder verborgen kosten of verrassingen.</p>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="text-orange-500 mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold mb-2">Persoonlijk advies</h3>
                <p class="text-gray-600">Onze experts staan voor je klaar om je te helpen bij het vinden van de perfecte auto.</p>
            </div>
        </div>
    </div>
</x-app-layout>
