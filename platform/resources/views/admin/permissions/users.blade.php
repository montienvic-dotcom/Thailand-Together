@extends('layouts.admin')

@section('title', 'Users')

@section('content')
    <x-admin.breadcrumb :items="[
        ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
        ['label' => 'Permissions', 'url' => route('admin.permissions')],
        ['label' => 'Users'],
    ]" />

    <x-ui.card title="Users" class="mt-4">
        @forelse($users ?? [] as $user)
            @if($loop->first)
                <x-ui.table>
                    <x-slot:head>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Last Login</th>
                    </x-slot:head>

                    @foreach($users as $user)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-(--color-secondary) flex items-center justify-center text-white text-xs font-bold">
                                        {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                                    </div>
                                    <span class="text-sm font-medium text-gray-900">{{ $user->name ?? '-' }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500">{{ $user->email }}</td>
                            <td class="px-4 py-3">
                                <x-ui.badge :color="($user->status ?? 'active') === 'active' ? 'green' : 'gray'">
                                    {{ ucfirst($user->status ?? 'active') }}
                                </x-ui.badge>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500">
                                {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never' }}
                            </td>
                        </tr>
                    @endforeach
                </x-ui.table>

                @if(method_exists($users, 'links'))
                    <div class="mt-4">
                        {{ $users->links() }}
                    </div>
                @endif
            @endif
        @empty
            <x-ui.empty-state title="No users found" description="Users will appear here once they are registered." icon="users" />
        @endforelse
    </x-ui.card>
@endsection
