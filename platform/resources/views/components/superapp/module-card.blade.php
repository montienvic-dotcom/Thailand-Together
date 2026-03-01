@props(['module', 'color' => '#6C757D', 'icon' => 'cube'])

<div class="group bg-white rounded-xl border border-gray-200 p-5 hover:shadow-md hover:border-gray-300 transition-all cursor-default">
    <div class="flex items-start gap-4">
        <div class="flex-shrink-0 w-11 h-11 rounded-xl flex items-center justify-center" style="background-color: {{ $color }}15">
            <x-icon :name="$icon" class="w-5 h-5" style="color: {{ $color }}" />
        </div>
        <div class="flex-1 min-w-0">
            <h4 class="text-sm font-semibold text-gray-900 group-hover:text-gray-700">{{ $module->name }}</h4>
            @if($module->description ?? null)
                <p class="mt-1 text-xs text-gray-500 line-clamp-2">{{ $module->description }}</p>
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
