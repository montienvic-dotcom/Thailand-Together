<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Login - Thailand Together</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full flex items-center justify-center p-4">
    <div class="w-full max-w-sm">
        {{-- Logo --}}
        <div class="text-center mb-8">
            <div class="mx-auto w-12 h-12 rounded-xl bg-(--color-primary) flex items-center justify-center mb-4">
                <x-icon name="globe" class="w-7 h-7 text-white" />
            </div>
            <h1 class="text-xl font-bold text-gray-900">Thailand Together</h1>
            <p class="text-sm text-gray-500 mt-1">Admin Panel Login</p>
        </div>

        {{-- Login form --}}
        <x-ui.card>
            <form method="POST" action="{{ route('admin.login.submit') }}" class="space-y-4">
                @csrf

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-(--color-primary) focus:ring-(--color-primary) text-sm px-3 py-2 border">
                    @error('email')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <input type="password" name="password" id="password" required
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-(--color-primary) focus:ring-(--color-primary) text-sm px-3 py-2 border">
                </div>

                <x-ui.button type="submit" variant="primary" class="w-full">Login</x-ui.button>
            </form>
        </x-ui.card>

        <p class="mt-4 text-center text-xs text-gray-400">
            <a href="{{ route('superapp.landing') }}" class="hover:text-gray-600">Back to main site</a>
        </p>
    </div>
</body>
</html>
