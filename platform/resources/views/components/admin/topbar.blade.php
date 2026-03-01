<header class="sticky top-0 z-30 bg-white border-b border-gray-200">
    <div class="flex items-center justify-between h-16 px-6">
        {{-- Mobile menu button --}}
        <button @click="sidebarOpen = true" class="lg:hidden p-2 text-gray-400 hover:text-gray-600">
            <x-icon name="bars-3" class="w-6 h-6" />
        </button>

        {{-- Page title area --}}
        <div class="flex-1 lg:ml-0 ml-4">
            <h2 class="text-lg font-semibold text-gray-900">@yield('title', 'Dashboard')</h2>
        </div>

        {{-- Right side --}}
        <div class="flex items-center gap-4">
            @if(isset($currentCluster))
                <x-ui.badge color="blue" size="md">{{ $currentCluster->name }}</x-ui.badge>
            @endif

            {{-- User menu --}}
            <x-ui.dropdown align="right" width="48">
                <x-slot:trigger>
                    <button class="flex items-center gap-2 text-sm text-gray-600 hover:text-gray-900">
                        <div class="w-8 h-8 rounded-full bg-(--color-secondary) flex items-center justify-center text-white text-xs font-bold">
                            {{ auth()->check() ? strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) : 'G' }}
                        </div>
                        <x-icon name="chevron-down" class="w-4 h-4" />
                    </button>
                </x-slot:trigger>

                @auth
                    <div class="px-4 py-2 border-b border-gray-100">
                        <p class="text-sm font-medium text-gray-900">{{ auth()->user()->name ?? 'Admin' }}</p>
                        <p class="text-xs text-gray-500">{{ auth()->user()->email ?? '' }}</p>
                    </div>
                @endauth

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                        Logout
                    </button>
                </form>
            </x-ui.dropdown>
        </div>
    </div>
</header>
