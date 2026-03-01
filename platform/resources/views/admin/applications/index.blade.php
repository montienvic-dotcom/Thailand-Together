@extends('layouts.admin')

@section('title', 'Applications')

@section('content')
    <x-admin.breadcrumb :items="[
        ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
        ['label' => 'Applications'],
    ]" />

    <x-ui.card title="Applications" class="mt-4">
        @forelse($apps ?? [] as $app)
            @if($loop->first)
                <x-ui.table>
                    <x-slot:head>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">App</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Code</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Source</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Modules</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-4 py-3"></th>
                    </x-slot:head>

                    @foreach($apps as $app)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-lg flex items-center justify-center" style="background-color: {{ $app->color ?? '#6C757D' }}20">
                                        <x-icon :name="$app->icon ?? 'cube'" class="w-5 h-5" style="color: {{ $app->color ?? '#6C757D' }}" />
                                    </div>
                                    <span class="text-sm font-medium text-gray-900">{{ $app->name }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500 font-mono">{{ $app->code }}</td>
                            <td class="px-4 py-3">
                                <x-ui.badge :color="$app->type === 'mobile' ? 'purple' : ($app->type === 'hybrid' ? 'teal' : 'blue')">
                                    {{ ucfirst($app->type) }}
                                </x-ui.badge>
                            </td>
                            <td class="px-4 py-3">
                                <x-ui.badge :color="$app->source === 'internal' ? 'green' : 'orange'">
                                    {{ ucfirst($app->source) }}
                                </x-ui.badge>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500">{{ $app->active_modules_count ?? 0 }}</td>
                            <td class="px-4 py-3">
                                <x-ui.badge color="green">Active</x-ui.badge>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <x-ui.button variant="ghost" size="sm" :href="route('admin.applications.show', $app->id)">
                                    View
                                </x-ui.button>
                            </td>
                        </tr>
                    @endforeach
                </x-ui.table>
            @endif
        @empty
            <x-ui.empty-state title="No applications found" description="Applications will appear here once they are created." icon="cube" />
        @endforelse
    </x-ui.card>
@endsection
