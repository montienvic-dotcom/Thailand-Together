<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') - Thailand Together</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* Admin layout — plain CSS, not dependent on Tailwind */
        html, body { height: 100%; margin: 0; overflow: hidden; background: #f9fafb; }
        .admin-shell { display: flex; height: 100%; overflow: hidden; }
        .admin-sidebar { flex-shrink: 0; height: 100%; overflow-y: auto; overflow-x: hidden;
            background: #fff; border-right: 1px solid #e5e7eb; transition: width 0.3s ease; }
        .admin-main { flex: 1 1 0%; min-width: 0; display: flex; flex-direction: column; height: 100%; overflow-y: auto; }
        .admin-topbar { position: sticky; top: 0; z-index: 20; background: #fff; border-bottom: 1px solid #e5e7eb; }
        .admin-body { flex: 1 1 0%; padding: 0.75rem; }
        @media (min-width: 640px) { .admin-body { padding: 1rem; } }
        @media (min-width: 1024px) { .admin-body { padding: 1.5rem; } }
        @media (max-width: 1023px) {
            .admin-sidebar { display: none; }
        }
    </style>
</head>
<body x-data="{
    sidebarOpen: false,
    sidebarCollapsed: localStorage.getItem('sidebarCollapsed') === 'true',
    toggleCollapse() {
        this.sidebarCollapsed = !this.sidebarCollapsed;
        localStorage.setItem('sidebarCollapsed', this.sidebarCollapsed);
    }
}">
    {{-- Mobile sidebar overlay --}}
    <div x-show="sidebarOpen" x-cloak class="fixed inset-0 z-40 lg:hidden">
        <div x-show="sidebarOpen"
             x-transition:enter="ease-in-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in-out duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-gray-600/75" @click="sidebarOpen = false"></div>
        <div x-show="sidebarOpen"
             x-transition:enter="transition ease-in-out duration-300" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0"
             x-transition:leave="transition ease-in-out duration-300" x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full"
             class="relative flex w-64 flex-col bg-white h-full">
            <button @click="sidebarOpen = false" class="absolute top-2 right-2 p-2 text-gray-400 hover:text-gray-600 z-10">
                <x-icon name="x-mark" class="w-5 h-5" />
            </button>
            <x-admin.sidebar />
        </div>
    </div>

    {{-- Main layout --}}
    <div class="admin-shell">
        {{-- Desktop sidebar --}}
        <div class="admin-sidebar" :style="'width: ' + (sidebarCollapsed ? '4rem' : '16rem')">
            <x-admin.sidebar />
        </div>

        {{-- Main content area (scrolls independently) --}}
        <div class="admin-main">
            <div class="admin-topbar">
                <x-admin.topbar />
            </div>

            <div class="admin-body">
                @if(isset($breadcrumb))
                    <div class="mb-3 sm:mb-4">{{ $breadcrumb }}</div>
                @endif

                @if(session('success'))
                    <div class="mb-3 sm:mb-4">
                        <x-ui.alert type="success" :dismissible="true">{{ session('success') }}</x-ui.alert>
                    </div>
                @endif
                @if(session('error'))
                    <div class="mb-3 sm:mb-4">
                        <x-ui.alert type="error" :dismissible="true">{{ session('error') }}</x-ui.alert>
                    </div>
                @endif

                @yield('content')
            </div>
        </div>
    </div>
</body>
</html>
