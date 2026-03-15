@php
    $navItems = [
        ['label' => 'Dashboard', 'route' => 'admin.dashboard', 'icon' => 'chart-bar'],
        ['label' => 'Applications', 'route' => 'admin.applications', 'icon' => 'cube'],
        ['label' => 'Users', 'route' => 'admin.users', 'icon' => 'users'],
        ['label' => 'Clusters', 'route' => 'admin.clusters', 'icon' => 'map'],
        ['label' => 'API Providers', 'route' => 'admin.api-providers', 'icon' => 'cloud'],
        ['label' => 'API Reference', 'route' => 'admin.api-reference', 'icon' => 'code-bracket'],
        ['label' => 'Roadmap', 'route' => 'admin.roadmap', 'icon' => 'clipboard-document-list'],
    ];
@endphp

<div class="flex flex-col h-full bg-white overflow-hidden">
    {{-- Logo --}}
    <div class="flex items-center gap-3 px-4 py-4 border-b border-gray-100 min-h-[57px]">
        <div class="w-8 h-8 rounded-lg bg-(--color-primary) flex items-center justify-center flex-shrink-0">
            <x-icon name="globe" class="w-5 h-5 text-white" />
        </div>
        <div class="overflow-hidden" x-show="!sidebarCollapsed" x-transition.opacity>
            <h1 class="text-sm font-bold text-gray-900 truncate">Thailand Together</h1>
            <p class="text-xs text-gray-500">Admin Panel</p>
        </div>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 px-2 py-3 space-y-0.5 overflow-y-auto">
        @foreach($navItems as $item)
            <a href="{{ route($item['route']) }}"
               @class([
                   'flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors',
                   'bg-(--color-primary)/10 text-(--color-primary)' => request()->routeIs($item['route'] . '*'),
                   'text-gray-600 hover:bg-gray-50 hover:text-gray-900' => !request()->routeIs($item['route'] . '*'),
               ])
               :class="sidebarCollapsed ? 'justify-center px-2' : ''"
               title="{{ $item['label'] }}">
                <x-icon :name="$item['icon']" class="w-5 h-5 flex-shrink-0" />
                <span x-show="!sidebarCollapsed" x-transition.opacity class="truncate">{{ $item['label'] }}</span>
            </a>
        @endforeach

        {{-- Permissions section --}}
        <div x-data="{ expanded: {{ request()->routeIs('admin.permissions*') ? 'true' : 'false' }} }">
            <button @click="sidebarCollapsed ? (window.location.href = '{{ route('admin.permissions') }}') : (expanded = !expanded)"
                    @class([
                        'w-full flex items-center justify-between px-3 py-2 rounded-lg text-sm font-medium transition-colors',
                        'bg-(--color-primary)/10 text-(--color-primary)' => request()->routeIs('admin.permissions*'),
                        'text-gray-600 hover:bg-gray-50 hover:text-gray-900' => !request()->routeIs('admin.permissions*'),
                    ])
                    :class="sidebarCollapsed ? 'justify-center px-2' : ''"
                    title="Permissions">
                <span class="flex items-center gap-3">
                    <x-icon name="shield" class="w-5 h-5 flex-shrink-0" />
                    <span x-show="!sidebarCollapsed" x-transition.opacity class="truncate">Permissions</span>
                </span>
                <x-icon x-show="!sidebarCollapsed" name="chevron-down" class="w-4 h-4 transition-transform flex-shrink-0" ::class="expanded ? 'rotate-180' : ''" />
            </button>

            <div x-show="expanded && !sidebarCollapsed" x-collapse class="mt-1 ml-8 space-y-0.5">
                <a href="{{ route('admin.permissions') }}"
                   @class([
                       'block px-3 py-1.5 rounded-lg text-sm transition-colors',
                       'text-(--color-primary) font-medium' => request()->routeIs('admin.permissions') && !request()->routeIs('admin.permissions.*'),
                       'text-gray-500 hover:text-gray-700' => !request()->routeIs('admin.permissions') || request()->routeIs('admin.permissions.*'),
                   ])>
                    Overview
                </a>
                <a href="{{ route('admin.permissions.users') }}"
                   @class([
                       'block px-3 py-1.5 rounded-lg text-sm transition-colors',
                       'text-(--color-primary) font-medium' => request()->routeIs('admin.permissions.users'),
                       'text-gray-500 hover:text-gray-700' => !request()->routeIs('admin.permissions.users'),
                   ])>
                    Users
                </a>
                <a href="{{ route('admin.permissions.groups') }}"
                   @class([
                       'block px-3 py-1.5 rounded-lg text-sm transition-colors',
                       'text-(--color-primary) font-medium' => request()->routeIs('admin.permissions.groups'),
                       'text-gray-500 hover:text-gray-700' => !request()->routeIs('admin.permissions.groups'),
                   ])>
                    Groups
                </a>
                <a href="{{ route('admin.permissions.roles') }}"
                   @class([
                       'block px-3 py-1.5 rounded-lg text-sm transition-colors',
                       'text-(--color-primary) font-medium' => request()->routeIs('admin.permissions.roles'),
                       'text-gray-500 hover:text-gray-700' => !request()->routeIs('admin.permissions.roles'),
                   ])>
                    Roles
                </a>
            </div>
        </div>
    </nav>

    {{-- Collapse toggle (desktop only) --}}
    <div class="hidden lg:block px-2 py-2 border-t border-gray-100">
        <button @click="toggleCollapse()"
                class="w-full flex items-center justify-center gap-2 px-3 py-2 rounded-lg text-sm text-gray-400 hover:bg-gray-50 hover:text-gray-600 transition-colors"
                title="Toggle sidebar">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                 class="w-5 h-5 transition-transform" :class="sidebarCollapsed ? 'rotate-180' : ''">
                <path stroke-linecap="round" stroke-linejoin="round" d="M18.75 19.5l-7.5-7.5 7.5-7.5m-6 15L5.25 12l7.5-7.5" />
            </svg>
            <span x-show="!sidebarCollapsed" x-transition.opacity class="truncate">Collapse</span>
        </button>
    </div>

    {{-- Cluster info --}}
    @if(isset($currentCluster))
        <div class="px-3 py-2 border-t border-gray-100" x-show="!sidebarCollapsed" x-transition.opacity>
            <p class="text-xs text-gray-400">Current Cluster</p>
            <p class="text-sm font-medium text-gray-700 truncate">{{ $currentCluster->name }}</p>
            @if(isset($currentCountry))
                <p class="text-xs text-gray-500">{{ $currentCountry->name }}</p>
            @endif
        </div>
        <div class="px-2 py-2 border-t border-gray-100" x-show="sidebarCollapsed" x-transition.opacity>
            <div class="w-8 h-8 mx-auto rounded-full bg-blue-100 flex items-center justify-center text-xs font-bold text-blue-600"
                 title="{{ $currentCluster->name }}">
                {{ strtoupper(substr($currentCluster->name, 0, 1)) }}
            </div>
        </div>
    @endif
</div>
