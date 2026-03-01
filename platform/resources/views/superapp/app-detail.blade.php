@extends('layouts.superapp')

@section('title', ($app->name ?? 'App') . ' - Thailand Together')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Breadcrumb --}}
        <div class="flex items-center gap-2 text-sm text-gray-500 mb-6">
            <a href="{{ route('superapp.landing') }}" class="hover:text-gray-700">Destinations</a>
            <x-icon name="chevron-right" class="w-4 h-4" />
            <a href="{{ route('superapp.cluster', $cluster->slug) }}" class="hover:text-gray-700">{{ $cluster->name }}</a>
            <x-icon name="chevron-right" class="w-4 h-4" />
            <span class="text-gray-900 font-medium">{{ $app->name }}</span>
        </div>

        {{-- App header --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
            <div class="flex items-start gap-4">
                <div class="w-16 h-16 rounded-xl flex items-center justify-center" style="background-color: {{ $app->color ?? '#6C757D' }}20">
                    <x-icon :name="$app->icon ?? 'cube'" class="w-9 h-9" style="color: {{ $app->color ?? '#6C757D' }}" />
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $app->name }}</h1>
                    @if($app->description)
                        <p class="mt-1 text-gray-500">{{ $app->description }}</p>
                    @endif
                    <div class="mt-3 flex items-center gap-2">
                        <x-ui.badge :color="$app->type === 'mobile' ? 'purple' : ($app->type === 'hybrid' ? 'teal' : 'blue')">
                            {{ ucfirst($app->type ?? 'web') }}
                        </x-ui.badge>
                    </div>
                </div>
            </div>
        </div>

        {{-- Modules grid --}}
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Features</h2>

        @forelse($modules ?? [] as $module)
            @if($loop->first)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @endif

            <x-superapp.module-card :module="$module" />

            @if($loop->last)
                </div>
            @endif
        @empty
            <x-ui.empty-state
                title="No features available"
                description="Features for this app will appear here once they are enabled."
                icon="cube"
            />
        @endforelse
    </div>
@endsection
