<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Thailand Together - Global Tourism Platform</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-gray-50">
    {{-- Hero section --}}
    <div class="bg-gradient-to-br from-[var(--color-primary)] to-[var(--color-secondary)] text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-white/20 flex items-center justify-center">
                        <x-icon name="globe" class="w-6 h-6 text-white" />
                    </div>
                    <span class="text-lg font-bold">Thailand Together</span>
                </div>
                @auth
                    <div class="flex items-center gap-4">
                        <span class="text-sm text-white/80">{{ auth()->user()->name ?? 'User' }}</span>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="text-sm text-white/70 hover:text-white">Logout</button>
                        </form>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="text-sm font-medium text-white/90 hover:text-white px-4 py-2 bg-white/10 rounded-lg">
                        Login
                    </a>
                @endauth
            </div>

            <div class="py-16 text-center">
                <h1 class="text-4xl sm:text-5xl font-bold tracking-tight">
                    Discover Amazing Destinations
                </h1>
                <p class="mt-4 text-lg text-white/80 max-w-2xl mx-auto">
                    Your gateway to tourism experiences across Asia. Choose a destination to explore local services, activities, and more.
                </p>
            </div>
        </div>
    </div>

    {{-- Session alerts --}}
    @if(session('error'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-6">
            <x-ui.alert type="error" :dismissible="true">{{ session('error') }}</x-ui.alert>
        </div>
    @endif

    {{-- Clusters by country --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        @forelse($countries ?? [] as $country)
            <div class="mb-10">
                <div class="flex items-center gap-2 mb-4">
                    <h2 class="text-xl font-bold text-gray-900">{{ $country->name }}</h2>
                    <x-ui.badge color="blue">{{ $country->code_alpha2 ?? $country->code }}</x-ui.badge>
                </div>

                @if($country->activeClusters->isEmpty())
                    <p class="text-sm text-gray-500">No active clusters yet.</p>
                @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($country->activeClusters as $cluster)
                            <a href="{{ route('superapp.cluster', $cluster->slug) }}"
                               class="group bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md hover:border-(--color-primary) transition-all">
                                <div class="flex items-start gap-4">
                                    <div class="p-3 rounded-lg bg-(--color-primary)/10">
                                        <x-icon name="map" class="w-6 h-6 text-(--color-primary)" />
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900 group-hover:text-(--color-primary) transition-colors">
                                            {{ $cluster->name }}
                                        </h3>
                                        @if($cluster->description)
                                            <p class="mt-1 text-sm text-gray-500">{{ $cluster->description }}</p>
                                        @endif
                                        @if($cluster->launch_date)
                                            <p class="mt-2 text-xs text-gray-400">
                                                Launch: {{ $cluster->launch_date->format('M Y') }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        @empty
            <x-ui.empty-state
                title="No destinations available"
                description="Destinations will be listed here as they launch."
                icon="globe"
            />
        @endforelse
    </div>

    {{-- Footer --}}
    <footer class="border-t border-gray-200 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <p class="text-center text-sm text-gray-400">&copy; {{ date('Y') }} Thailand Together. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
