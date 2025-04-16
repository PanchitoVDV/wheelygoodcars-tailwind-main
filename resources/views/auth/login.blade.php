<x-app-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="flex flex-col justify-center items-center min-h-screen">
        <div class="bg-white shadow-md rounded-lg p-4 h-full" style="min-width: 50%;">
            <div class="p-4">
                <h5 class="text-lg font-semibold mb-4">Inloggen</h5>
                @include('layouts.error')
                <form action="{{ route('login.do') }}" method="POST">
                    @csrf
                    <div class="mb-4 flex items-center">
                        <label for="email" class="block text-sm font-medium text-gray-700 w-1/3">E-mailadres:</label>
                        <div class="w-2/3">
                        <input type="email" name="email" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-orange-500 focus:ring-orange-500" id="email" placeholder="name@example.com"></div>
                    </div>
                    <div class="mb-4 flex items-center">
                        <label for="password" class="block text-sm font-medium text-gray-700 w-1/3">Wachtwoord:</label>
                        <div class="w-2/3">
                        <input type="password" name="password" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-orange-500 focus:ring-orange-500" id="password"></div>
                    </div>
                    <div class="mb-4">
                        <input type="submit" class="mt-4 w-full inline-flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-orange-500 hover:bg-orange-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-400" value="Inloggen">
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
