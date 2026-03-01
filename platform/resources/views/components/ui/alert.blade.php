@props(['type' => 'info', 'dismissible' => false])

@php
    $styles = [
        'info' => 'bg-blue-50 text-blue-800 border-blue-200',
        'success' => 'bg-green-50 text-green-800 border-green-200',
        'warning' => 'bg-yellow-50 text-yellow-800 border-yellow-200',
        'error' => 'bg-red-50 text-red-800 border-red-200',
    ];
    $style = $styles[$type] ?? $styles['info'];
@endphp

<div x-data="{ show: true }" x-show="show" {{ $attributes->merge(['class' => 'rounded-lg border p-4 ' . $style]) }}>
    <div class="flex items-start">
        <div class="flex-1">{{ $slot }}</div>
        @if($dismissible)
            <button @click="show = false" class="ml-3 flex-shrink-0 opacity-50 hover:opacity-100">
                <x-icon name="x-mark" class="w-4 h-4" />
            </button>
        @endif
    </div>
</div>
