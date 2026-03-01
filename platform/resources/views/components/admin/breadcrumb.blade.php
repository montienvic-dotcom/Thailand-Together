@props(['items' => []])

<nav class="flex" aria-label="Breadcrumb">
    <ol class="flex items-center gap-2">
        @foreach($items as $i => $item)
            @if($i > 0)
                <li>
                    <x-icon name="chevron-right" class="w-4 h-4 text-gray-400" />
                </li>
            @endif
            <li>
                @if(isset($item['url']) && $i < count($items) - 1)
                    <a href="{{ $item['url'] }}" class="text-sm text-gray-500 hover:text-gray-700">{{ $item['label'] }}</a>
                @else
                    <span class="text-sm font-medium text-gray-900">{{ $item['label'] }}</span>
                @endif
            </li>
        @endforeach
    </ol>
</nav>
