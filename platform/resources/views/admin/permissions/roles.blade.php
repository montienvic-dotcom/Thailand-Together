@extends('layouts.admin')

@section('title', 'Roles')

@section('content')
    <x-admin.breadcrumb :items="[
        ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
        ['label' => 'Permissions', 'url' => route('admin.permissions')],
        ['label' => 'Roles'],
    ]" />

    <div x-data="roleManager()" x-cloak>
        <div class="mt-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <h2 class="text-lg font-bold text-gray-900">Roles ({{ count($roles ?? []) }})</h2>
            <x-ui.button variant="primary" size="sm" @click="$dispatch('open-modal-create-role')">
                <x-icon name="plus" class="w-4 h-4 mr-1" /> Add Role
            </x-ui.button>
        </div>

        <x-ui.card class="mt-4">
            @forelse($roles ?? [] as $role)
                @if($loop->first)
                    <x-ui.table>
                        <x-slot:head>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Level</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase hidden md:table-cell">Description</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">System</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Users</th>
                            <th class="px-4 py-3"></th>
                        </x-slot:head>

                        @foreach($roles as $role)
                            @php
                                $levelColors = ['global' => 'purple', 'country' => 'blue', 'cluster' => 'green', 'app' => 'orange'];
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $role->name }}</td>
                                <td class="px-4 py-3">
                                    <x-ui.badge :color="$levelColors[$role->level] ?? 'gray'">
                                        {{ ucfirst($role->level) }}
                                    </x-ui.badge>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-500 hidden md:table-cell">{{ $role->description ?? '-' }}</td>
                                <td class="px-4 py-3">
                                    @if($role->is_system)
                                        <x-ui.badge color="blue">System</x-ui.badge>
                                    @else
                                        <span class="text-sm text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-500">{{ $role->users_count ?? 0 }}</td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <x-ui.button variant="ghost" size="sm" @click="openManagePerms({{ $role->id }})" title="Manage Permissions">
                                            <x-icon name="key" class="w-4 h-4" />
                                        </x-ui.button>
                                        @if(!$role->is_system)
                                            <x-ui.button variant="ghost" size="sm" @click="openEditRole({{ $role->id }}, {{ json_encode($role->name) }}, {{ json_encode($role->description ?? '') }}, {{ json_encode($role->level) }})" title="Edit Role">
                                                <x-icon name="pencil" class="w-4 h-4" />
                                            </x-ui.button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </x-ui.table>
                @endif
            @empty
                <x-ui.empty-state title="No roles found" description="Create your first role to get started." icon="shield" />
            @endforelse
        </x-ui.card>

        {{-- Create Role Modal --}}
        <x-ui.modal name="create-role" maxWidth="lg">
            <form @submit.prevent="createRole()">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900">Create Role</h3>
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
                        <label class="block text-sm font-medium text-gray-700 mb-1">Level *</label>
                        <select x-model="form.level" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm">
                            <option value="global">Global</option>
                            <option value="country">Country</option>
                            <option value="cluster">Cluster</option>
                            <option value="app">App</option>
                        </select>
                    </div>
                </div>
                <div class="px-6 py-3 bg-gray-50 border-t border-gray-100 flex justify-end gap-2">
                    <x-ui.button variant="outline" size="sm" @click="$dispatch('close-modal-create-role')" type="button">Cancel</x-ui.button>
                    <x-ui.button variant="primary" size="sm" type="submit" x-bind:disabled="loading">
                        <template x-if="loading"><x-icon name="arrow-path" class="w-4 h-4 animate-spin mr-1" /></template>
                        Create Role
                    </x-ui.button>
                </div>
            </form>
        </x-ui.modal>

        {{-- Edit Role Modal --}}
        <x-ui.modal name="edit-role" maxWidth="lg">
            <form @submit.prevent="updateRole()">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900">Edit Role</h3>
                </div>
                <div class="px-6 py-4 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Name *</label>
                        <input type="text" x-model="editForm.name" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea x-model="editForm.description" rows="3" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Level *</label>
                        <select x-model="editForm.level" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm">
                            <option value="global">Global</option>
                            <option value="country">Country</option>
                            <option value="cluster">Cluster</option>
                            <option value="app">App</option>
                        </select>
                    </div>
                </div>
                <div class="px-6 py-3 bg-gray-50 border-t border-gray-100 flex justify-end gap-2">
                    <x-ui.button variant="outline" size="sm" @click="$dispatch('close-modal-edit-role')" type="button">Cancel</x-ui.button>
                    <x-ui.button variant="primary" size="sm" type="submit" x-bind:disabled="loading">
                        <template x-if="loading"><x-icon name="arrow-path" class="w-4 h-4 animate-spin mr-1" /></template>
                        Save Changes
                    </x-ui.button>
                </div>
            </form>
        </x-ui.modal>

        {{-- Manage Permissions Modal --}}
        <x-ui.modal name="manage-permissions" maxWidth="2xl">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="text-lg font-semibold text-gray-900">Manage Permissions</h3>
            </div>
            <div class="px-6 py-4">
                <template x-if="loadingPerms">
                    <div class="flex items-center justify-center py-8">
                        <x-icon name="arrow-path" class="w-6 h-6 animate-spin text-gray-400" />
                    </div>
                </template>
                <template x-if="!loadingPerms">
                    <div class="space-y-6 max-h-[60vh] overflow-y-auto">
                        @php
                            $grouped = ($permissions ?? collect())->groupBy('category');
                        @endphp
                        @foreach($grouped as $category => $perms)
                            <div>
                                <h4 class="text-sm font-semibold text-gray-900 mb-2 uppercase tracking-wide">{{ $category ?: 'General' }}</h4>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                    @foreach($perms as $perm)
                                        <label class="flex items-center gap-2 p-2 rounded-lg hover:bg-gray-50 cursor-pointer">
                                            <input type="checkbox"
                                                   value="{{ $perm->id }}"
                                                   :checked="selectedRolePerms.includes({{ $perm->id }})"
                                                   @change="togglePerm({{ $perm->id }})"
                                                   class="rounded border-gray-300 text-orange-500 focus:ring-orange-500" />
                                            <div>
                                                <span class="text-sm font-medium text-gray-700">{{ $perm->name }}</span>
                                                @if($perm->description)
                                                    <p class="text-xs text-gray-400">{{ $perm->description }}</p>
                                                @endif
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach

                        @if($grouped->isEmpty())
                            <x-ui.empty-state title="No permissions available" description="Create permissions first before assigning them to roles." icon="key" />
                        @endif
                    </div>
                </template>
            </div>
            <div class="px-6 py-3 bg-gray-50 border-t border-gray-100 flex justify-end gap-2">
                <x-ui.button variant="outline" size="sm" @click="$dispatch('close-modal-manage-permissions')" type="button">Cancel</x-ui.button>
                <x-ui.button variant="primary" size="sm" @click="syncPermissions()" x-bind:disabled="loading">
                    <template x-if="loading"><x-icon name="arrow-path" class="w-4 h-4 animate-spin mr-1" /></template>
                    Save Permissions
                </x-ui.button>
            </div>
        </x-ui.modal>

        {{-- Toast --}}
        <div x-show="toast.show" x-transition class="fixed bottom-4 right-4 z-50 px-4 py-3 rounded-lg shadow-lg text-sm font-medium"
             :class="toast.type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'" x-text="toast.message"></div>
    </div>

    <script>
        function roleManager() {
            return {
                loading: false,
                loadingPerms: false,
                form: { name: '', description: '', level: 'cluster' },
                editForm: { id: null, name: '', description: '', level: 'cluster' },
                selectedRolePerms: [],
                selectedRoleId: null,
                toast: { show: false, message: '', type: 'success' },

                csrfToken() { return document.querySelector('meta[name="csrf-token"]').content; },
                showToast(message, type = 'success') {
                    this.toast = { show: true, message, type };
                    setTimeout(() => this.toast.show = false, 3000);
                },

                async createRole() {
                    this.loading = true;
                    try {
                        const res = await fetch('{{ route("admin.permissions.roles.store") }}', {
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': this.csrfToken(), 'Content-Type': 'application/json', 'Accept': 'application/json' },
                            body: JSON.stringify(this.form),
                        });
                        const data = await res.json();
                        if (!res.ok) { this.showToast(Object.values(data.errors || {}).flat().join(', ') || data.message || 'Validation failed.', 'error'); }
                        else { this.showToast(data.message || 'Role created.'); this.$dispatch('close-modal-create-role'); setTimeout(() => location.reload(), 800); }
                    } catch (e) { this.showToast('Failed to create role.', 'error'); }
                    this.loading = false;
                },

                openEditRole(id, name, description, level) {
                    this.editForm = { id, name, description, level };
                    this.$dispatch('open-modal-edit-role');
                },

                async updateRole() {
                    this.loading = true;
                    try {
                        const res = await fetch('{{ url("admin/permissions/roles") }}/' + this.editForm.id, {
                            method: 'PUT',
                            headers: { 'X-CSRF-TOKEN': this.csrfToken(), 'Content-Type': 'application/json', 'Accept': 'application/json' },
                            body: JSON.stringify({ name: this.editForm.name, description: this.editForm.description, level: this.editForm.level }),
                        });
                        const data = await res.json();
                        if (!res.ok) { this.showToast(Object.values(data.errors || {}).flat().join(', ') || data.message || 'Validation failed.', 'error'); }
                        else { this.showToast(data.message || 'Role updated.'); this.$dispatch('close-modal-edit-role'); setTimeout(() => location.reload(), 800); }
                    } catch (e) { this.showToast('Failed to update role.', 'error'); }
                    this.loading = false;
                },

                async openManagePerms(roleId) {
                    this.selectedRoleId = roleId;
                    this.selectedRolePerms = [];
                    this.loadingPerms = true;
                    this.$dispatch('open-modal-manage-permissions');
                    try {
                        const res = await fetch('{{ url("admin/permissions/roles") }}/' + roleId + '/permissions', {
                            headers: { 'Accept': 'application/json' },
                        });
                        const data = await res.json();
                        this.selectedRolePerms = (data.permission_ids || []).map(id => Number(id));
                    } catch (e) { this.showToast('Failed to load permissions.', 'error'); }
                    this.loadingPerms = false;
                },

                togglePerm(permId) {
                    const idx = this.selectedRolePerms.indexOf(permId);
                    if (idx > -1) { this.selectedRolePerms.splice(idx, 1); }
                    else { this.selectedRolePerms.push(permId); }
                },

                async syncPermissions() {
                    this.loading = true;
                    try {
                        const res = await fetch('{{ url("admin/permissions/roles") }}/' + this.selectedRoleId + '/sync-permissions', {
                            method: 'PUT',
                            headers: { 'X-CSRF-TOKEN': this.csrfToken(), 'Content-Type': 'application/json', 'Accept': 'application/json' },
                            body: JSON.stringify({ permission_ids: this.selectedRolePerms }),
                        });
                        const data = await res.json();
                        if (!res.ok) { this.showToast(data.message || 'Failed to sync permissions.', 'error'); }
                        else { this.showToast(data.message || 'Permissions updated.'); this.$dispatch('close-modal-manage-permissions'); }
                    } catch (e) { this.showToast('Failed to sync permissions.', 'error'); }
                    this.loading = false;
                },
            };
        }
    </script>
@endsection
