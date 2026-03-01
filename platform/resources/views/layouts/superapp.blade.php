<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Thailand Together')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-gray-50">
    <div class="min-h-full flex flex-col">
        <x-superapp.header />

        <main class="flex-1">
            @if(session('success'))
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4">
                    <x-ui.alert type="success" :dismissible="true">{{ session('success') }}</x-ui.alert>
                </div>
            @endif
            @if(session('error'))
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4">
                    <x-ui.alert type="error" :dismissible="true">{{ session('error') }}</x-ui.alert>
                </div>
            @endif

            @yield('content')
        </main>

        <x-superapp.footer />
    </div>
</body>
</html>
