@extends('layouts.admin')

@section('title', 'Groups')

@section('content')
    <x-admin.breadcrumb :items="[
        ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
        ['label' => 'Permissions', 'url' => route('admin.permissions')],
        ['label' => 'Groups'],
    ]" />

    <x-ui.card title="Groups" class="mt-4">
        @forelse($groups ?? [] as $group)
            @if($loop->first)
                <x-ui.table>
                    <x-slot:head>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Scope</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Members</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    </x-slot:head>

                    @foreach($groups as $group)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $group->name }}</td>
                            <td class="px-4 py-3">
                                <x-ui.badge :color="$group->scope === 'global' ? 'purple' : 'blue'">
                                    {{ ucfirst($group->scope) }}
                                </x-ui.badge>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500">{{ $group->description ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-500">{{ $group->users_count ?? 0 }}</td>
                            <td class="px-4 py-3">
                                <x-ui.badge :color="($group->is_active ?? true) ? 'green' : 'gray'">
                                    {{ ($group->is_active ?? true) ? 'Active' : 'Inactive' }}
                                </x-ui.badge>
                            </td>
                        </tr>
                    @endforeach
                </x-ui.table>
            @endif
        @empty
            <x-ui.empty-state title="No groups found" description="Groups will appear here once they are created." icon="users" />
        @endforelse
    </x-ui.card>
@endsection
