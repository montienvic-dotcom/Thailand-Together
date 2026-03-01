@extends('layouts.superapp')

@section('title', ($cluster->name ?? 'Cluster') . ' - Thailand Together')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Cluster header --}}
        <div class="mb-8">
            <div class="flex items-center gap-2 text-sm text-gray-500 mb-2">
                <a href="{{ route('superapp.landing') }}" class="hover:text-gray-700">Destinations</a>
                <x-icon name="chevron-right" class="w-4 h-4" />
                <span class="text-gray-900 font-medium">{{ $cluster->name }}</span>
            </div>
            <h1 class="text-3xl font-bold text-gray-900">{{ $cluster->name }}</h1>
            @if($cluster->description)
                <p class="mt-2 text-gray-500">{{ $cluster->description }}</p>
            @endif
            @if(isset($currentCountry))
                <x-ui.badge color="blue" class="mt-2">{{ $currentCountry->name }}</x-ui.badge>
            @endif
        </div>

        {{-- Apps grid --}}
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Available Services</h2>

        @forelse($apps ?? [] as $app)
            @if($loop->first)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @endif

            <x-superapp.app-card :app="$app" :cluster="$cluster" />

            @if($loop->last)
                </div>
            @endif
        @empty
            <x-ui.empty-state
                title="No services available"
                description="Services for this cluster will appear here once they are configured."
                icon="cube"
            />
        @endforelse
    </div>
@endsection
