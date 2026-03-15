@extends('layouts.admin')

@section('title', 'Groups')

@section('content')
    <x-admin.breadcrumb :items="[
        ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
        ['label' => 'Permissions', 'url' => route('admin.permissions')],
        ['label' => 'Groups'],
    ]" />

    <div x-data="groupManager()" x-cloak>
        <div class="mt-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <h2 class="text-lg font-bold text-gray-900">Groups ({{ count($groups ?? []) }})</h2>
            <x-ui.button variant="primary" size="sm" @click="$dispatch('open-modal-create-group')">
                <x-icon name="plus" class="w-4 h-4 mr-1" /> Add Group
            </x-ui.button>
        </div>

        <x-ui.card class="mt-4">
            @forelse($groups ?? [] as $group)
                @if($loop->first)
                    <x-ui.table>
                        <x-slot:head>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Scope</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase hidden md:table-cell">Description</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Members</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-4 py-3"></th>
                        </x-slot:head>

                        @foreach($groups as $group)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $group->name }}</td>
                                <td class="px-4 py-3">
                                    @php
                                        $scopeColors = ['global' => 'purple', 'country' => 'blue', 'cluster' => 'green'];
                                    @endphp
                                    <x-ui.badge :color="$scopeColors[$group->scope] ?? 'gray'">
                                        {{ ucfirst($group->scope) }}
                                    </x-ui.badge>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-500 hidden md:table-cell">{{ $group->description ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-500">{{ $group->users_count ?? 0 }}</td>
                                <td class="px-4 py-3">
                                    <x-ui.badge :color="($group->is_active ?? true) ? 'green' : 'gray'">
                                        {{ ($group->is_active ?? true) ? 'Active' : 'Inactive' }}
                                    </x-ui.badge>
                                </td>
                                <td class="px-4 py-3 text-right flex items-center justify-end gap-1">
                                    <button @click="toggleGroup({{ $group->id }})" class="text-gray-400 hover:text-gray-600 p-1" title="Toggle active">
                                        <x-icon name="power" class="w-4 h-4" />
                                    </button>
                                    <button @click="deleteGroup({{ $group->id }}, {{ json_encode($group->name) }})" class="text-gray-400 hover:text-red-600 p-1" title="Delete">
                                        <x-icon name="trash" class="w-4 h-4" />
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </x-ui.table>
                @endif
            @empty
                <x-ui.empty-state title="No groups found" description="Create your first group to organize user permissions." icon="users" />
            @endforelse
        </x-ui.card>

        {{-- Create Group Modal --}}
        <x-ui.modal name="create-group" maxWidth="xl">
            <form @submit.prevent="createGroup()">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900">Create Group</h3>
                </div>
                <div class="px-6 py-4 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Name *</label>
                        <input type="text" x-model="form.name" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea x-model="form.description" rows="3" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Scope *</label>
                        <select x-model="form.scope" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm">
                            <option value="global">Global</option>
                            <option value="country">Country</option>
                            <option value="cluster">Cluster</option>
                        </select>
                    </div>
                    <div class="flex items-center gap-2">
                        <input type="checkbox" x-model="form.is_active" class="rounded border-gray-300 text-orange-500 focus:ring-orange-500" />
                        <label class="text-sm text-gray-700">Active</label>
                    </div>
                </div>
                <div class="px-6 py-3 bg-gray-50 border-t border-gray-100 flex justify-end gap-2">
                    <x-ui.button variant="outline" size="sm" @click="$dispatch('close-modal-create-group')" type="button">Cancel</x-ui.button>
                    <x-ui.button variant="primary" size="sm" type="submit" x-bind:disabled="loading">
                        <template x-if="loading"><x-icon name="arrow-path" class="w-4 h-4 animate-spin mr-1" /></template>
                        Create Group
                    </x-ui.button>
                </div>
            </form>
        </x-ui.modal>

        {{-- Toast --}}
        <div x-show="toast.show" x-transition class="fixed bottom-4 right-4 z-50 px-4 py-3 rounded-lg shadow-lg text-sm font-medium"
             :class="toast.type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'" x-text="toast.message"></div>
    </div>

    <script>
        function groupManager() {
            return {
                loading: false,
                form: { name: '', description: '', scope: 'cluster', is_active: true },
                toast: { show: false, message: '', type: 'success' },

                csrfToken() { return document.querySelector('meta[name="csrf-token"]').content; },
                showToast(message, type = 'success') {
                    this.toast = { show: true, message, type };
                    setTimeout(() => this.toast.show = false, 3000);
                },

                async toggleGroup(id) {
                    try {
                        const res = await fetch('/admin/permissions/groups/' + id + '/toggle', {
                            method: 'PATCH',
                            headers: { 'X-CSRF-TOKEN': this.csrfToken(), 'Accept': 'application/json' },
                        });
                        const data = await res.json();
                        this.showToast(data.message);
                        setTimeout(() => location.reload(), 800);
                    } catch (e) { this.showToast('Failed to toggle group.', 'error'); }
                },

                async deleteGroup(id, name) {
                    if (!confirm('Delete group "' + name + '"?')) return;
                    try {
                        const res = await fetch('/admin/permissions/groups/' + id, {
                            method: 'DELETE',
                            headers: { 'X-CSRF-TOKEN': this.csrfToken(), 'Accept': 'application/json' },
                        });
                        const data = await res.json();
                        this.showToast(data.message);
                        setTimeout(() => location.reload(), 800);
                    } catch (e) { this.showToast('Failed to delete group.', 'error'); }
                },

                async createGroup() {
                    this.loading = true;
                    try {
                        const res = await fetch('{{ route("admin.permissions.groups.store") }}', {
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': this.csrfToken(), 'Content-Type': 'application/json', 'Accept': 'application/json' },
                            body: JSON.stringify(this.form),
                        });
                        const data = await res.json();
                        if (!res.ok) { this.showToast(Object.values(data.errors || {}).flat().join(', '), 'error'); }
                        else { this.showToast(data.message); this.$dispatch('close-modal-create-group'); setTimeout(() => location.reload(), 800); }
                    } catch (e) { this.showToast('Failed to create group.', 'error'); }
                    this.loading = false;
                },
            };
        }
    </script>
@endsection
