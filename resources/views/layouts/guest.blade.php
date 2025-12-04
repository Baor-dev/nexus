<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Nexus') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="[https://fonts.googleapis.com](https://fonts.googleapis.com)">
        <link href="[https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap](https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap)" rel="stylesheet">
        
        <!-- Icon -->
        <link rel="icon" href="{{ asset('images/logo.png') }}" type="image/png">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <style>
            body { font-family: 'Inter', sans-serif; }
        </style>
    </head>
    <body class="text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-[#DAE0E6]">
            <!-- Logo -->
            <div class="mb-6">
                <a href="/" class="flex flex-col items-center gap-2">
                    <img src="{{ asset('images/logo.png') }}" class="w-16 h-16 object-contain">
                    <span class="font-bold text-2xl text-gray-800 tracking-tight">Nexus</span>
                </a>
            </div>

            <!-- Card chứa Form -->
            <div class="w-full sm:max-w-md mt-6 px-6 py-8 bg-white shadow-md overflow-hidden sm:rounded-lg border border-gray-200">
                {{ $slot }}
            </div>
            
            <!-- Footer nhỏ -->
            <div class="mt-8 text-xs text-gray-500">
                &copy; {{ date('Y') }} Nexus Inc. All rights reserved.
            </div>
        </div>
    </body>
</html>
