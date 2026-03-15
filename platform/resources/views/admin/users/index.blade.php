@extends('layouts.admin')

@section('title', 'User Management')

@section('content')
    <x-admin.breadcrumb :items="[
        ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
        ['label' => 'Users'],
    ]" />

    <div x-data="userManager()" x-cloak>
        {{-- Header with actions --}}
        <div class="mt-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="text-lg font-bold text-gray-900">Users ({{ $users->total() }})</h2>
            </div>
            <x-ui.button variant="primary" size="sm" @click="$dispatch('open-modal-create-user')">
                <x-icon name="plus" class="w-4 h-4 mr-1" /> Add User
            </x-ui.button>
        </div>

        {{-- Search & Filter --}}
        <div class="mt-3 bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <form method="GET" action="{{ route('admin.users') }}" class="flex flex-col sm:flex-row gap-3">
                <div class="flex-1">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name or email..."
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm" />
                </div>
                <select name="status" class="rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
                </select>
                <x-ui.button variant="outline" size="sm" type="submit">
                    <x-icon name="magnifying-glass" class="w-4 h-4 mr-1" /> Search
                </x-ui.button>
                @if(request('search') || request('status'))
                    <a href="{{ route('admin.users') }}" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700">Clear</a>
                @endif
            </form>
        </div>

        {{-- Users Table --}}
        <x-ui.card class="mt-4">
            @forelse($users as $u)
                @if($loop->first)
                    <x-ui.table>
                        <x-slot:head>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase hidden sm:table-cell">Groups</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase hidden md:table-cell">Role</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </x-slot:head>

                        @foreach($users as $u)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-orange-100 flex items-center justify-center text-orange-600 text-xs font-bold flex-shrink-0">
                                            {{ strtoupper(substr($u->name ?? 'U', 0, 1)) }}
                                        </div>
                                        <div class="min-w-0">
                                            <a href="{{ route('admin.users.show', $u->id) }}" class="text-sm font-medium text-gray-900 hover:text-orange-600 truncate block">{{ $u->name ?? '-' }}</a>
                                            <span class="text-xs text-gray-500 truncate block">{{ $u->email }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 hidden sm:table-cell">
                                    <div class="flex flex-wrap gap-1">
                                        @forelse($u->groups as $group)
                                            <x-ui.badge color="blue" size="sm">{{ $group->name }}</x-ui.badge>
                                        @empty
                                            <span class="text-xs text-gray-400">-</span>
                                        @endforelse
                                    </div>
                                </td>
                                <td class="px-4 py-3 hidden md:table-cell">
                                    @forelse($u->roles as $role)
                                        <x-ui.badge color="purple" size="sm">{{ $role->name }}</x-ui.badge>
                                    @empty
                                        <span class="text-xs text-gray-400">-</span>
                                    @endforelse
                                </td>
                                <td class="px-4 py-3">
                                    <x-ui.badge :color="($u->status ?? 'active') === 'active' ? 'green' : (($u->status ?? '') === 'suspended' ? 'red' : 'gray')">
                                        {{ ucfirst($u->status ?? 'active') }}
                                    </x-ui.badge>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('admin.users.show', $u->id) }}" class="text-gray-400 hover:text-gray-600 p-1">
                                        <x-icon name="pencil" class="w-4 h-4 inline" />
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </x-ui.table>

                    @if($users->hasPages())
                        <div class="mt-4 px-4 pb-4">
                            {{ $users->links() }}
                        </div>
                    @endif
                @endif
            @empty
                <x-ui.empty-state title="No users found" description="Add your first user to get started." icon="users" />
            @endforelse
        </x-ui.card>

        {{-- Create User Modal --}}
        <x-ui.modal name="create-user" maxWidth="xl">
            <form @submit.prevent="createUser()">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900">Create User</h3>
                </div>
                <div class="px-6 py-4 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Name *</label>
                        <input type="text" x-model="form.name" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                        <input type="email" x-model="form.email" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Password *</label>
                        <input type="password" x-model="form.password" required minlength="8" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                        <input type="text" x-model="form.phone" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Groups</label>
                        <div class="flex flex-wrap gap-2 mt-1">
                            @foreach($groups as $group)
                                <label class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gray-50 rounded-lg border cursor-pointer hover:bg-gray-100">
                                    <input type="checkbox" value="{{ $group->id }}" x-model="form.group_ids" class="rounded border-gray-300 text-orange-500 focus:ring-orange-500" />
                                    <span class="text-sm text-gray-700">{{ $group->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                        <select x-model="form.role_id" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm">
                            <option value="">No role</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}">{{ $role->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="px-6 py-3 bg-gray-50 border-t border-gray-100 flex justify-end gap-2">
                    <x-ui.button variant="outline" size="sm" @click="$dispatch('close-modal-create-user')" type="button">Cancel</x-ui.button>
                    <x-ui.button variant="primary" size="sm" type="submit" x-bind:disabled="loading">
                        <template x-if="loading"><x-icon name="arrow-path" class="w-4 h-4 animate-spin mr-1" /></template>
                        Create User
                    </x-ui.button>
                </div>
            </form>
        </x-ui.modal>

        {{-- Toast --}}
        <div x-show="toast.show" x-transition class="fixed bottom-4 right-4 z-50 px-4 py-3 rounded-lg shadow-lg text-sm font-medium"
             :class="toast.type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'" x-text="toast.message"></div>
    </div>

    <script>
        function userManager() {
            return {
                loading: false,
                form: { name: '', email: '', password: '', phone: '', group_ids: [], role_id: '' },
                toast: { show: false, message: '', type: 'success' },

                csrfToken() { return document.querySelector('meta[name="csrf-token"]').content; },
                showToast(message, type = 'success') {
                    this.toast = { show: true, message, type };
                    setTimeout(() => this.toast.show = false, 3000);
                },

                async createUser() {
                    this.loading = true;
                    try {
                        const res = await fetch('{{ route("admin.users.store") }}', {
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': this.csrfToken(), 'Content-Type': 'application/json', 'Accept': 'application/json' },
                            body: JSON.stringify(this.form),
                        });
                        const data = await res.json();
                        if (!res.ok) { this.showToast(data.message || Object.values(data.errors || {}).flat().join(', '), 'error'); }
                        else { this.showToast(data.message); this.$dispatch('close-modal-create-user'); setTimeout(() => location.reload(), 800); }
                    } catch (e) { this.showToast('Failed to create user.', 'error'); }
                    this.loading = false;
                },
            };
        }
    </script>
@endsection
