@props(['align' => 'right', 'width' => '48'])

@php
    $alignClasses = $align === 'left' ? 'left-0 origin-top-left' : 'right-0 origin-top-right';
    $widthClass = 'w-' . $width;
@endphp

<div x-data="{ open: false }" @click.away="open = false" class="relative" {{ $attributes }}>
    <div @click="open = !open">
        {{ $trigger }}
    </div>

    <div x-show="open"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         x-cloak
         class="absolute z-50 mt-2 {{ $widthClass }} {{ $alignClasses }} rounded-lg bg-white shadow-lg ring-1 ring-black/5 py-1">
        {{ $slot }}
    </div>
</div>
