<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Admin - {{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gray-100">
        <nav class="bg-black flex py-3">
            <div class="w-full mx-auto px-4 flex">
                <a class="text-lg text-white min-w-48" href="{{ route('home') }}"><strong class="text-orange-500 text-bold">Wheely</strong> good cars<strong class="text-orange-500 text-bold">!</strong></a>
                <div class="flex justify-between w-full" id="navbarNav">
                    <ul class="flex items-end">
                        <li class="mr-4"><a class="text-white hover:text-gray-300" href="{{ route('home') }}">Terug naar site</a></li>
                        <li class="mr-4"><a class="text-orange-400 hover:text-orange-300 font-bold" href="{{ route('admin.dashboard') }}">Admin Dashboard</a></li>
                        <li class="mr-4"><a class="text-orange-400 hover:text-orange-300" href="{{ route('admin.tags') }}">Tag Statistieken</a></li>
                        <li class="mr-4"><a class="text-orange-400 hover:text-orange-300" href="{{ route('admin.suspicious-sellers') }}">Opvallende Aanbieders</a></li>
                        <li class="mr-4"><a class="text-orange-400 hover:text-orange-300" href="{{ route('admin.realtime-dashboard') }}">Realtime Dashboard</a></li>
                    </ul>
                    <ul class="flex">
                        @auth
                            <li class="mr-4">
                                <form method="POST" action="{{ route('logout') }}" class="inline">
                                    @csrf
                                    <button type="submit" class="text-orange-500 hover:text-orange-400 bg-transparent border-0 p-0 cursor-pointer">
                                        Uitloggen
                                    </button>
                                </form>
                            </li>
                        @endauth
                    </ul>
                </div>
            </div>
        </nav>

        <div class="container mx-auto px-4 py-6">
            <div class="bg-white rounded-lg shadow-md p-6">
                <h1 class="text-2xl font-bold text-gray-800 mb-6">{{ $title ?? 'Beheerderspaneel' }}</h1>
                {{ $slot }}
            </div>
        </div>
        
        <footer class="bg-gray-800 text-white p-4 mt-8">
            <div class="container mx-auto">
                <p class="text-center">&copy; {{ date('Y') }} WeelyGoodCars - Admin Panel</p>
            </div>
        </footer>
    </body>
</html> 