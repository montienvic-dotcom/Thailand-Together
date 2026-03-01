@props(['app', 'cluster' => null])

@php
    $href = $cluster ? route('superapp.app', ['cluster' => $cluster->slug, 'application' => $app->id]) : '#';
@endphp

<a href="{{ $href }}" class="group block bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md hover:border-gray-300 transition-all">
    <div class="p-6">
        <div class="flex items-start gap-4">
            <div class="flex-shrink-0 w-12 h-12 rounded-xl flex items-center justify-center" style="background-color: {{ $app->color ?? '#6C757D' }}20">
                <x-icon :name="$app->icon ?? 'cube'" class="w-6 h-6" style="color: {{ $app->color ?? '#6C757D' }}" />
            </div>
            <div class="flex-1 min-w-0">
                <h3 class="text-base font-semibold text-gray-900 group-hover:text-(--color-primary) transition-colors">
                    {{ $app->name }}
                </h3>
                @if($app->description ?? null)
                    <p class="mt-1 text-sm text-gray-500 line-clamp-2">{{ $app->description }}</p>
                @endif
                <div class="mt-3 flex items-center gap-2">
                    <x-ui.badge :color="$app->type === 'mobile' ? 'purple' : ($app->type === 'hybrid' ? 'teal' : 'blue')">
                        {{ ucfirst($app->type ?? 'web') }}
                    </x-ui.badge>
                    @if(isset($app->active_modules_count))
                        <span class="text-xs text-gray-400">{{ $app->active_modules_count }} modules</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</a>
