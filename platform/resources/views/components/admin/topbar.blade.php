<header class="sticky top-0 z-20 bg-white border-b border-gray-200">
    <div class="flex items-center justify-between h-12 sm:h-14 lg:h-16 px-3 sm:px-4 lg:px-6">
        {{-- Mobile menu button --}}
        <button @click="sidebarOpen = true" class="lg:hidden p-1.5 sm:p-2 text-gray-400 hover:text-gray-600">
            <x-icon name="bars-3" class="w-5 h-5 sm:w-6 sm:h-6" />
        </button>

        {{-- Page title area --}}
        <div class="flex-1 ml-2 sm:ml-4 lg:ml-0 min-w-0">
            <h2 class="text-sm sm:text-base lg:text-lg font-semibold text-gray-900 truncate">@yield('title', 'Dashboard')</h2>
        </div>

        {{-- Right side --}}
        <div class="flex items-center gap-2 sm:gap-3 lg:gap-4">
            @if(isset($currentCluster))
                <x-ui.badge color="blue" size="sm" class="hidden sm:inline-flex">{{ $currentCluster->name }}</x-ui.badge>
            @endif

            {{-- User menu --}}
            <x-ui.dropdown align="right" width="48">
                <x-slot:trigger>
                    <button class="flex items-center gap-1 sm:gap-2 text-sm text-gray-600 hover:text-gray-900">
                        <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-(--color-secondary) flex items-center justify-center text-white text-xs font-bold">
                            {{ auth()->check() ? strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) : 'G' }}
                        </div>
                        <x-icon name="chevron-down" class="w-3 h-3 sm:w-4 sm:h-4 hidden sm:block" />
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
