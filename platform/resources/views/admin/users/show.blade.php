@extends('layouts.admin')

@section('title', $user->name ?? 'User Detail')

@section('content')
    <x-admin.breadcrumb :items="[
        ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
        ['label' => 'Users', 'url' => route('admin.users')],
        ['label' => $user->name ?? 'User'],
    ]" />

    <div x-data="userDetail()" x-cloak>
        {{-- User header --}}
        <div class="mt-4 bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-full bg-orange-100 flex items-center justify-center text-orange-600 text-xl font-bold">
                        {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">{{ $user->name }}</h2>
                        <p class="text-sm text-gray-500">{{ $user->email }}</p>
                        <div class="mt-2 flex items-center gap-2 flex-wrap">
                            <x-ui.badge :color="($user->status ?? 'active') === 'active' ? 'green' : 'red'">
                                {{ ucfirst($user->status ?? 'active') }}
                            </x-ui.badge>
                            @if($user->phone)
                                <span class="text-xs text-gray-400">{{ $user->phone }}</span>
                            @endif
                            <span class="text-xs text-gray-400">Joined {{ $user->created_at?->format('M d, Y') }}</span>
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <x-ui.button variant="outline" size="sm" @click="$dispatch('open-modal-edit-user')">
                        <x-icon name="pencil" class="w-4 h-4 mr-1" /> Edit
                    </x-ui.button>
                    <x-ui.button variant="danger" size="sm" @click="deleteUser()">
                        <x-icon name="trash" class="w-4 h-4 mr-1" /> Delete
                    </x-ui.button>
                </div>
            </div>
        </div>

        {{-- Groups --}}
        <x-ui.card title="Groups" class="mt-6">
            <form @submit.prevent="updateGroups()">
                <div class="flex flex-wrap gap-2">
                    @foreach($groups as $group)
                        <label class="inline-flex items-center gap-1.5 px-3 py-2 bg-gray-50 rounded-lg border cursor-pointer hover:bg-gray-100 transition-colors">
                            <input type="checkbox" value="{{ $group->id }}" x-model="userGroups"
                                class="rounded border-gray-300 text-orange-500 focus:ring-orange-500" />
                            <span class="text-sm text-gray-700">{{ $group->name }}</span>
                            <span class="text-[10px] text-gray-400">({{ $group->scope }})</span>
                        </label>
                    @endforeach
                </div>
                <div class="mt-4 flex justify-end">
                    <x-ui.button variant="primary" size="sm" type="submit" x-bind:disabled="loading">Save Groups</x-ui.button>
                </div>
            </form>
        </x-ui.card>

        {{-- Roles --}}
        <x-ui.card title="Role Assignment" class="mt-4">
            <form @submit.prevent="updateRole()">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                        <select x-model="selectedRole" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm">
                            <option value="">No role</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}">{{ $role->name }} ({{ $role->level }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="mt-4 flex justify-end">
                    <x-ui.button variant="primary" size="sm" type="submit" x-bind:disabled="loading">Save Role</x-ui.button>
                </div>
            </form>
        </x-ui.card>

        {{-- App Access Matrix --}}
        <x-ui.card title="App Access per Cluster" class="mt-4">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Select Cluster</label>
                    <select x-model="selectedCluster" @change="loadAppAccess()" class="w-full sm:w-64 rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm">
                        <option value="">Choose a cluster...</option>
                        @foreach($clusters as $cluster)
                            <option value="{{ $cluster->id }}">{{ $cluster->name }}</option>
                        @endforeach
                    </select>
                </div>

                <template x-if="selectedCluster && clusterApps.length > 0">
                    <div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
                            <template x-for="app in clusterApps" :key="app.id">
                                <label class="inline-flex items-center gap-2 px-3 py-2 bg-gray-50 rounded-lg border cursor-pointer hover:bg-gray-100 transition-colors">
                                    <input type="checkbox" :value="app.id" :checked="appAccess[app.id] === true"
                                        @change="appAccess[app.id] = $event.target.checked"
                                        class="rounded border-gray-300 text-orange-500 focus:ring-orange-500" />
                                    <div class="flex items-center gap-1.5">
                                        <div class="w-5 h-5 rounded flex items-center justify-center flex-shrink-0" :style="'background-color: ' + (app.color || '#6C757D') + '20'">
                                            <span class="text-[10px]" :style="'color: ' + (app.color || '#6C757D')">&#9679;</span>
                                        </div>
                                        <span class="text-sm text-gray-700" x-text="app.name"></span>
                                    </div>
                                </label>
                            </template>
                        </div>
                        <div class="mt-4 flex justify-end">
                            <x-ui.button variant="primary" size="sm" @click="saveAppAccess()" x-bind:disabled="loading">Save App Access</x-ui.button>
                        </div>
                    </div>
                </template>

                <template x-if="selectedCluster && clusterApps.length === 0">
                    <p class="text-sm text-gray-400">No applications assigned to this cluster.</p>
                </template>
            </div>
        </x-ui.card>

        {{-- Edit User Modal --}}
        <x-ui.modal name="edit-user" maxWidth="lg">
            <form @submit.prevent="saveUser()">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900">Edit User</h3>
                </div>
                <div class="px-6 py-4 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                        <input type="text" x-model="editForm.name" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" x-model="editForm.email" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                        <input type="text" x-model="editForm.phone" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select x-model="editForm.status" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="suspended">Suspended</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">New Password (leave blank to keep current)</label>
                        <input type="password" x-model="editForm.password" minlength="8" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm" />
                    </div>
                </div>
                <div class="px-6 py-3 bg-gray-50 border-t border-gray-100 flex justify-end gap-2">
                    <x-ui.button variant="outline" size="sm" @click="$dispatch('close-modal-edit-user')" type="button">Cancel</x-ui.button>
                    <x-ui.button variant="primary" size="sm" type="submit" x-bind:disabled="loading">Save</x-ui.button>
                </div>
            </form>
        </x-ui.modal>

        {{-- Toast --}}
        <div x-show="toast.show" x-transition class="fixed bottom-4 right-4 z-50 px-4 py-3 rounded-lg shadow-lg text-sm font-medium"
             :class="toast.type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'" x-text="toast.message"></div>
    </div>

    <script>
        function userDetail() {
            return {
                loading: false,
                selectedCluster: '',
                clusterApps: [],
                appAccess: {},
                clustersData: @json($clusters->mapWithKeys(fn($c) => [$c->id => $c->applications->map(fn($a) => ['id' => $a->id, 'name' => $a->name, 'color' => $a->color])])),
                userGroups: @json($user->groups->pluck('id')->map(fn($id) => (string) $id)),
                selectedRole: @json((string) ($user->roles->first()?->id ?? '')),
                editForm: {
                    name: @json($user->name),
                    email: @json($user->email),
                    phone: @json($user->phone ?? ''),
                    status: @json($user->status ?? 'active'),
                    password: '',
                },
                toast: { show: false, message: '', type: 'success' },

                csrfToken() { return document.querySelector('meta[name="csrf-token"]').content; },
                showToast(message, type = 'success') {
                    this.toast = { show: true, message, type };
                    setTimeout(() => this.toast.show = false, 3000);
                },

                async saveUser() {
                    this.loading = true;
                    const body = { ...this.editForm };
                    if (!body.password) delete body.password;
                    try {
                        const res = await fetch('{{ route("admin.users.update", $user->id) }}', {
                            method: 'PUT',
                            headers: { 'X-CSRF-TOKEN': this.csrfToken(), 'Content-Type': 'application/json', 'Accept': 'application/json' },
                            body: JSON.stringify(body),
                        });
                        const data = await res.json();
                        if (!res.ok) { this.showToast(Object.values(data.errors || {}).flat().join(', '), 'error'); }
                        else { this.showToast(data.message); this.$dispatch('close-modal-edit-user'); setTimeout(() => location.reload(), 800); }
                    } catch (e) { this.showToast('Failed to save.', 'error'); }
                    this.loading = false;
                },

                async updateGroups() {
                    this.loading = true;
                    try {
                        const res = await fetch('{{ route("admin.users.update-groups", $user->id) }}', {
                            method: 'PUT',
                            headers: { 'X-CSRF-TOKEN': this.csrfToken(), 'Content-Type': 'application/json', 'Accept': 'application/json' },
                            body: JSON.stringify({ group_ids: this.userGroups.map(Number) }),
                        });
                        const data = await res.json();
                        this.showToast(data.message);
                    } catch (e) { this.showToast('Failed to update groups.', 'error'); }
                    this.loading = false;
                },

                async updateRole() {
                    this.loading = true;
                    try {
                        const res = await fetch('{{ route("admin.users.update-role", $user->id) }}', {
                            method: 'PUT',
                            headers: { 'X-CSRF-TOKEN': this.csrfToken(), 'Content-Type': 'application/json', 'Accept': 'application/json' },
                            body: JSON.stringify({ role_id: Number(this.selectedRole) }),
                        });
                        const data = await res.json();
                        this.showToast(data.message);
                    } catch (e) { this.showToast('Failed to update role.', 'error'); }
                    this.loading = false;
                },

                async loadAppAccess() {
                    if (!this.selectedCluster) { this.clusterApps = []; return; }
                    this.clusterApps = this.clustersData[this.selectedCluster] || [];
                    this.appAccess = {};
                    try {
                        const res = await fetch('/admin/users/{{ $user->id }}/app-access/' + this.selectedCluster, {
                            headers: { 'Accept': 'application/json' },
                        });
                        const data = await res.json();
                        this.appAccess = data.access || {};
                    } catch (e) { /* defaults to empty */ }
                },

                async saveAppAccess() {
                    this.loading = true;
                    const appAccessList = this.clusterApps.map(app => ({
                        app_id: app.id,
                        has_access: !!this.appAccess[app.id],
                    }));
                    try {
                        const res = await fetch('{{ route("admin.users.sync-app-access", $user->id) }}', {
                            method: 'PUT',
                            headers: { 'X-CSRF-TOKEN': this.csrfToken(), 'Content-Type': 'application/json', 'Accept': 'application/json' },
                            body: JSON.stringify({ cluster_id: Number(this.selectedCluster), app_access: appAccessList }),
                        });
                        const data = await res.json();
                        this.showToast(data.message);
                    } catch (e) { this.showToast('Failed to update app access.', 'error'); }
                    this.loading = false;
                },

                async deleteUser() {
                    if (!confirm('Are you sure you want to delete this user?')) return;
                    this.loading = true;
                    try {
                        const res = await fetch('{{ route("admin.users.destroy", $user->id) }}', {
                            method: 'DELETE',
                            headers: { 'X-CSRF-TOKEN': this.csrfToken(), 'Accept': 'application/json' },
                        });
                        const data = await res.json();
                        this.showToast(data.message);
                        setTimeout(() => window.location.href = '{{ route("admin.users") }}', 800);
                    } catch (e) { this.showToast('Failed to delete.', 'error'); }
                    this.loading = false;
                },
            };
        }
    </script>
@endsection
