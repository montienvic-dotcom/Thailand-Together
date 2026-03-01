@extends('layouts.admin')

@section('title', $app->name ?? 'Application')

@section('content')
    <x-admin.breadcrumb :items="[
        ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
        ['label' => 'Applications', 'url' => route('admin.applications')],
        ['label' => $app->name],
    ]" />

    {{-- App header --}}
    <div class="mt-4 bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-start gap-4">
            <div class="w-14 h-14 rounded-xl flex items-center justify-center" style="background-color: {{ $app->color ?? '#6C757D' }}20">
                <x-icon :name="$app->icon ?? 'cube'" class="w-8 h-8" style="color: {{ $app->color ?? '#6C757D' }}" />
            </div>
            <div class="flex-1">
                <h2 class="text-xl font-bold text-gray-900">{{ $app->name }}</h2>
                @if($app->description)
                    <p class="mt-1 text-sm text-gray-500">{{ $app->description }}</p>
                @endif
                <div class="mt-3 flex items-center gap-2">
                    <x-ui.badge :color="$app->type === 'mobile' ? 'purple' : ($app->type === 'hybrid' ? 'teal' : 'blue')">
                        {{ ucfirst($app->type) }}
                    </x-ui.badge>
                    <x-ui.badge :color="$app->source === 'internal' ? 'green' : 'orange'">
                        {{ ucfirst($app->source) }}
                    </x-ui.badge>
                    <span class="text-xs text-gray-400 font-mono">{{ $app->code }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Modules table --}}
    <x-ui.card title="Modules" class="mt-6">
        @forelse($app->activeModules ?? [] as $module)
            @if($loop->first)
                <x-ui.table>
                    <x-slot:head>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Code</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Premium</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    </x-slot:head>

                    @foreach($app->activeModules as $module)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $module->name }}</td>
                            <td class="px-4 py-3 text-sm text-gray-500 font-mono">{{ $module->code }}</td>
                            <td class="px-4 py-3 text-sm text-gray-500">{{ $module->description ?? '-' }}</td>
                            <td class="px-4 py-3">
                                @if($module->is_premium)
                                    <x-ui.badge color="orange">Premium</x-ui.badge>
                                @else
                                    <span class="text-sm text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <x-ui.badge color="green">Active</x-ui.badge>
                            </td>
                        </tr>
                    @endforeach
                </x-ui.table>
            @endif
        @empty
            <x-ui.empty-state title="No modules" description="This application has no active modules." icon="cube" />
        @endforelse
    </x-ui.card>
@endsection
