<header class="bg-white shadow-sm" x-data="{ mobileMenu: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            {{-- Logo --}}
            <a href="{{ route('superapp.landing') }}" class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-(--color-primary) flex items-center justify-center">
                    <x-icon name="globe" class="w-6 h-6 text-white" />
                </div>
                <span class="text-lg font-bold text-gray-900 hidden sm:block">Thailand Together</span>
            </a>

            {{-- Desktop nav --}}
            <div class="hidden md:flex items-center gap-4">
                @if(isset($currentCluster))
                    <x-superapp.cluster-switcher />
                @endif

                @auth
                    <x-superapp.user-menu />
                @else
                    <x-ui.button variant="primary" size="sm" :href="route('login')">Login</x-ui.button>
                @endauth
            </div>

            {{-- Mobile menu button --}}
            <button @click="mobileMenu = !mobileMenu" class="md:hidden p-2 text-gray-400 hover:text-gray-600">
                <x-icon x-show="!mobileMenu" name="bars-3" class="w-6 h-6" />
                <x-icon x-show="mobileMenu" x-cloak name="x-mark" class="w-6 h-6" />
            </button>
        </div>
    </div>

    {{-- Mobile menu --}}
    <div x-show="mobileMenu" x-cloak x-collapse class="md:hidden border-t border-gray-100">
        <div class="px-4 py-3 space-y-2">
            @if(isset($currentCluster))
                <div class="py-2 border-b border-gray-100">
                    <p class="text-xs text-gray-400">Current Cluster</p>
                    <p class="text-sm font-medium text-gray-700">{{ $currentCluster->name }}</p>
                </div>
            @endif

            @auth
                <div class="py-2 border-b border-gray-100">
                    <p class="text-sm font-medium text-gray-900">{{ auth()->user()->name ?? 'User' }}</p>
                    <p class="text-xs text-gray-500">{{ auth()->user()->email ?? '' }}</p>
                </div>
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-600 hover:bg-gray-50 rounded-lg">
                    <x-icon name="cog" class="w-4 h-4 text-gray-400" />
                    Admin Panel
                </a>
                <a href="{{ route('superapp.api-docs') }}" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-600 hover:bg-gray-50 rounded-lg">
                    <x-icon name="code-bracket" class="w-4 h-4 text-gray-400" />
                    API Reference
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left px-3 py-2 text-sm text-gray-600 hover:bg-gray-50 rounded-lg">
                        Logout
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}" class="block px-3 py-2 text-sm font-medium text-(--color-primary) hover:bg-gray-50 rounded-lg">
                    Login
                </a>
            @endauth
        </div>
    </div>
</header>
