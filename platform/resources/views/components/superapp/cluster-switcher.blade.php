<x-ui.dropdown align="right" width="64">
    <x-slot:trigger>
        <button class="flex items-center gap-2 px-3 py-1.5 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
            <x-icon name="map" class="w-4 h-4" />
            {{ $currentCluster->name ?? 'Select Cluster' }}
            <x-icon name="chevron-down" class="w-3 h-3" />
        </button>
    </x-slot:trigger>

    <div class="px-3 py-2">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Switch Cluster</p>
    </div>
    @if(isset($currentCluster))
        <a href="{{ route('superapp.landing') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
            View All Clusters
        </a>
    @endif
</x-ui.dropdown>
