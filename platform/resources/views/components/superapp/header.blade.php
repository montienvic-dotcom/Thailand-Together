<header class="bg-white shadow-sm" x-data="{ mobileMenu: false }">
    {{-- Top bar --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-14">
            {{-- Logo --}}
            <a href="{{ route('superapp.landing') }}" class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-(--color-primary) flex items-center justify-center">
                    <x-icon name="globe" class="w-5 h-5 text-white" />
                </div>
                <span class="text-base font-bold text-gray-900 hidden sm:block">Thailand Together</span>
            </a>

            {{-- Desktop nav --}}
            <div class="hidden md:flex items-center gap-3">
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

    {{-- App Navigation Bar (dynamic based on cluster + permissions) --}}
    @if(isset($currentCluster) && isset($menuApps) && $menuApps->isNotEmpty())
        <div class="border-t border-gray-100 bg-gray-50/50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <nav class="flex items-center gap-1 overflow-x-auto scrollbar-none py-1.5 -mx-1" aria-label="App navigation">
                    @foreach($menuApps as $menuApp)
                        <a href="{{ route('superapp.app', [$currentCluster->slug, $menuApp->id]) }}"
                           @class([
                               'flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-medium whitespace-nowrap transition-colors flex-shrink-0',
                               'bg-(--color-primary)/10 text-(--color-primary)' => isset($activeApp) && $activeApp->id === $menuApp->id,
                               'text-gray-600 hover:bg-gray-100 hover:text-gray-900' => !isset($activeApp) || $activeApp->id !== $menuApp->id,
                           ])>
                            <div class="w-5 h-5 rounded flex items-center justify-center flex-shrink-0" style="background-color: {{ $menuApp->color ?? '#6C757D' }}20">
                                <x-icon :name="$menuApp->icon ?? 'cube'" class="w-3 h-3" style="color: {{ $menuApp->color ?? '#6C757D' }}" />
                            </div>
                            <span>{{ $menuApp->name }}</span>
                        </a>
                    @endforeach
                </nav>
            </div>
        </div>
    @endif

    {{-- Mobile menu --}}
    <div x-show="mobileMenu" x-cloak x-collapse class="md:hidden border-t border-gray-100">
        <div class="px-4 py-3 space-y-1">
            @if(isset($currentCluster))
                <div class="py-2 border-b border-gray-100 mb-2">
                    <p class="text-xs text-gray-400">Current Cluster</p>
                    <p class="text-sm font-medium text-gray-700">{{ $currentCluster->name }}</p>
                </div>

                {{-- Mobile app menu --}}
                @if(isset($menuApps) && $menuApps->isNotEmpty())
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide px-2 pt-1">Apps</p>
                    @foreach($menuApps as $menuApp)
                        <a href="{{ route('superapp.app', [$currentCluster->slug, $menuApp->id]) }}"
                           class="flex items-center gap-3 px-3 py-2 text-sm text-gray-600 hover:bg-gray-50 rounded-lg">
                            <div class="w-6 h-6 rounded flex items-center justify-center" style="background-color: {{ $menuApp->color ?? '#6C757D' }}20">
                                <x-icon :name="$menuApp->icon ?? 'cube'" class="w-3.5 h-3.5" style="color: {{ $menuApp->color ?? '#6C757D' }}" />
                            </div>
                            {{ $menuApp->name }}
                        </a>
                    @endforeach
                    <div class="border-b border-gray-100 my-2"></div>
                @endif
            @endif

            @auth
                <div class="py-2 border-b border-gray-100 mb-2">
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
                <a href="{{ route('superapp.guide') }}" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-600 hover:bg-gray-50 rounded-lg">
                    <x-icon name="book-open" class="w-4 h-4 text-gray-400" />
                    User Guide
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
