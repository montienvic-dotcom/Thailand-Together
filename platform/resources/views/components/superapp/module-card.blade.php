@props(['module'])

<div class="bg-white rounded-lg border border-gray-200 p-4 hover:shadow-sm transition-shadow">
    <div class="flex items-start gap-3">
        <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center">
            <x-icon name="cube" class="w-5 h-5 text-gray-500" />
        </div>
        <div class="flex-1 min-w-0">
            <h4 class="text-sm font-semibold text-gray-900">{{ $module->name }}</h4>
            @if($module->description ?? null)
                <p class="mt-0.5 text-xs text-gray-500 line-clamp-2">{{ $module->description }}</p>
            @endif
            <div class="mt-2 flex items-center gap-2">
                @if($module->is_premium ?? false)
                    <x-ui.badge color="orange">Premium</x-ui.badge>
                @endif
                <x-ui.badge color="green">Active</x-ui.badge>
            </div>
        </div>
    </div>
</div>
