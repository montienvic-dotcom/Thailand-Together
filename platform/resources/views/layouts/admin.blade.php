<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') - Thailand Together</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full overflow-hidden" x-data="{
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

    {{-- Full-screen layout container --}}
    <div class="h-full flex flex-col lg:flex-row overflow-hidden">
        {{-- Desktop sidebar --}}
        <aside class="hidden lg:flex flex-shrink-0 transition-all duration-300 ease-in-out border-r border-gray-200 bg-white"
               :style="'width: ' + (sidebarCollapsed ? '4rem' : '16rem')">
            <div class="flex flex-col h-full w-full overflow-y-auto overflow-x-hidden">
                <x-admin.sidebar />
            </div>
        </aside>

        {{-- Main content area: this is the scroll container --}}
        <div class="flex-1 min-w-0 flex flex-col h-full overflow-y-auto" id="main-scroll">
            <x-admin.topbar />

            <main class="flex-1 p-3 sm:p-4 lg:p-6">
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
            </main>
        </div>
    </div>
</body>
</html>
