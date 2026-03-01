@props(['title', 'description' => null, 'icon' => null])

<div {{ $attributes->merge(['class' => 'text-center py-12']) }}>
    @if($icon)
        <div class="mx-auto mb-4 text-gray-300">
            <x-icon :name="$icon" class="w-12 h-12 mx-auto" />
        </div>
    @endif
    <h3 class="text-lg font-medium text-gray-900">{{ $title }}</h3>
    @if($description)
        <p class="mt-1 text-sm text-gray-500">{{ $description }}</p>
    @endif
    @if($slot->isNotEmpty())
        <div class="mt-4">{{ $slot }}</div>
    @endif
</div>
