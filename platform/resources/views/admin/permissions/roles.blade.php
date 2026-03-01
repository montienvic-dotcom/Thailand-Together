@extends('layouts.admin')

@section('title', 'Roles')

@section('content')
    <x-admin.breadcrumb :items="[
        ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
        ['label' => 'Permissions', 'url' => route('admin.permissions')],
        ['label' => 'Roles'],
    ]" />

    <x-ui.card title="Roles" class="mt-4">
        @forelse($roles ?? [] as $role)
            @if($loop->first)
                <x-ui.table>
                    <x-slot:head>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Level</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">System</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Users</th>
                    </x-slot:head>

                    @foreach($roles as $role)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $role->name }}</td>
                            <td class="px-4 py-3">
                                @php
                                    $levelColors = ['global' => 'purple', 'country' => 'blue', 'cluster' => 'green', 'app' => 'orange'];
                                @endphp
                                <x-ui.badge :color="$levelColors[$role->level] ?? 'gray'">
                                    {{ ucfirst($role->level) }}
                                </x-ui.badge>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500">{{ $role->description ?? '-' }}</td>
                            <td class="px-4 py-3">
                                @if($role->is_system)
                                    <x-ui.badge color="blue">System</x-ui.badge>
                                @else
                                    <span class="text-sm text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500">{{ $role->users_count ?? 0 }}</td>
                        </tr>
                    @endforeach
                </x-ui.table>
            @endif
        @empty
            <x-ui.empty-state title="No roles found" description="Roles will appear here once they are created." icon="shield" />
        @endforelse
    </x-ui.card>
@endsection
