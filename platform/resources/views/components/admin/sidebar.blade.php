@php
    $navItems = [
        ['label' => 'Dashboard', 'route' => 'admin.dashboard', 'icon' => 'chart-bar'],
        ['label' => 'Applications', 'route' => 'admin.applications', 'icon' => 'cube'],
    ];
@endphp

<div class="flex flex-col h-full bg-white border-r border-gray-200">
    {{-- Logo --}}
    <div class="flex items-center gap-3 px-6 py-5 border-b border-gray-100">
        <div class="w-8 h-8 rounded-lg bg-(--color-primary) flex items-center justify-center">
            <x-icon name="globe" class="w-5 h-5 text-white" />
        </div>
        <div>
            <h1 class="text-sm font-bold text-gray-900">Thailand Together</h1>
            <p class="text-xs text-gray-500">Admin Panel</p>
        </div>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
        @foreach($navItems as $item)
            <a href="{{ route($item['route']) }}"
               @class([
                   'flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors',
                   'bg-(--color-primary)/10 text-(--color-primary)' => request()->routeIs($item['route'] . '*'),
                   'text-gray-600 hover:bg-gray-50 hover:text-gray-900' => !request()->routeIs($item['route'] . '*'),
               ])>
                <x-icon :name="$item['icon']" class="w-5 h-5" />
                {{ $item['label'] }}
            </a>
        @endforeach

        {{-- Permissions section --}}
        <div x-data="{ expanded: {{ request()->routeIs('admin.permissions*') ? 'true' : 'false' }} }">
            <button @click="expanded = !expanded"
                    @class([
                        'w-full flex items-center justify-between px-3 py-2 rounded-lg text-sm font-medium transition-colors',
                        'bg-(--color-primary)/10 text-(--color-primary)' => request()->routeIs('admin.permissions*'),
                        'text-gray-600 hover:bg-gray-50 hover:text-gray-900' => !request()->routeIs('admin.permissions*'),
                    ])>
                <span class="flex items-center gap-3">
                    <x-icon name="shield" class="w-5 h-5" />
                    Permissions
                </span>
                <x-icon name="chevron-down" class="w-4 h-4 transition-transform" ::class="expanded ? 'rotate-180' : ''" />
            </button>

            <div x-show="expanded" x-collapse class="mt-1 ml-8 space-y-1">
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

    {{-- Cluster info --}}
    @if(isset($currentCluster))
        <div class="px-4 py-3 border-t border-gray-100">
            <p class="text-xs text-gray-400">Current Cluster</p>
            <p class="text-sm font-medium text-gray-700">{{ $currentCluster->name }}</p>
            @if(isset($currentCountry))
                <p class="text-xs text-gray-500">{{ $currentCountry->name }}</p>
            @endif
        </div>
    @endif
</div>
