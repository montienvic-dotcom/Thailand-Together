<x-ui.dropdown align="right" width="48">
    <x-slot:trigger>
        <button class="flex items-center gap-2 text-sm text-gray-600 hover:text-gray-900">
            <div class="w-8 h-8 rounded-full bg-(--color-primary) flex items-center justify-center text-white text-xs font-bold">
                {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
            </div>
            <span class="hidden sm:block">{{ auth()->user()->name ?? 'User' }}</span>
            <x-icon name="chevron-down" class="w-4 h-4" />
        </button>
    </x-slot:trigger>

    <div class="px-4 py-2 border-b border-gray-100">
        <p class="text-sm font-medium text-gray-900">{{ auth()->user()->name ?? 'User' }}</p>
        <p class="text-xs text-gray-500">{{ auth()->user()->email ?? '' }}</p>
    </div>

    <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
        <span class="flex items-center gap-2">
            <x-icon name="cog" class="w-4 h-4 text-gray-400" />
            Admin Panel
        </span>
    </a>
    <a href="{{ route('superapp.api-docs') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
        <span class="flex items-center gap-2">
            <x-icon name="code-bracket" class="w-4 h-4 text-gray-400" />
            API Reference
        </span>
    </a>
    <a href="{{ route('superapp.guide') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 border-b border-gray-100">
        <span class="flex items-center gap-2">
            <x-icon name="book-open" class="w-4 h-4 text-gray-400" />
            User Guide
        </span>
    </a>

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
            Logout
        </button>
    </form>
</x-ui.dropdown>
