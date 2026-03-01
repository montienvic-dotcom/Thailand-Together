@props(['label', 'value', 'icon' => null, 'color' => 'blue'])

@php
    $colorMap = [
        'blue' => 'bg-blue-50 text-blue-600',
        'green' => 'bg-green-50 text-green-600',
        'orange' => 'bg-orange-50 text-orange-600',
        'purple' => 'bg-purple-50 text-purple-600',
        'red' => 'bg-red-50 text-red-600',
        'teal' => 'bg-teal-50 text-teal-600',
    ];
    $iconColor = $colorMap[$color] ?? $colorMap['blue'];
@endphp

<div {{ $attributes->merge(['class' => 'bg-white rounded-xl shadow-sm border border-gray-200 p-6']) }}>
    <div class="flex items-center gap-4">
        @if($icon)
            <div class="flex-shrink-0 p-3 rounded-lg {{ $iconColor }}">
                <x-icon :name="$icon" class="w-6 h-6" />
            </div>
        @endif
        <div>
            <p class="text-sm font-medium text-gray-500">{{ $label }}</p>
            <p class="text-2xl font-bold text-gray-900">{{ $value }}</p>
        </div>
    </div>
</div>
