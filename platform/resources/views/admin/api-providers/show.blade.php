@extends('layouts.admin')

@section('title', $provider->name ?? 'API Provider')

@section('content')
    <x-admin.breadcrumb :items="[
        ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
        ['label' => 'API Providers', 'url' => route('admin.api-providers')],
        ['label' => $provider->name],
    ]" />

    <div x-data="providerDetail()" x-cloak>
        {{-- Provider header --}}
        <div class="mt-4 bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                <div>
                    <h2 class="text-xl font-bold text-gray-900">{{ $provider->name }}</h2>
                    <div class="mt-2 flex items-center gap-2 flex-wrap">
                        <x-ui.badge :color="match($provider->category) { 'payment' => 'green', 'sms' => 'blue', 'ai' => 'purple', default => 'gray' }">
                            {{ ucfirst(str_replace('_', ' ', $provider->category)) }}
                        </x-ui.badge>
                        <x-ui.badge :color="$provider->is_active ? 'green' : 'red'">
                            {{ $provider->is_active ? 'Active' : 'Inactive' }}
                        </x-ui.badge>
                        @if($provider->is_shared)
                            <x-ui.badge color="teal">Shared</x-ui.badge>
                        @endif
                    </div>
                    @if($provider->description)
                        <p class="mt-2 text-sm text-gray-500">{{ $provider->description }}</p>
                    @endif
                    <div class="mt-2 flex gap-4 text-xs text-gray-400">
                        @if($provider->base_url)
                            <span>API: {{ $provider->base_url }}</span>
                        @endif
                        @if($provider->docs_url)
                            <a href="{{ $provider->docs_url }}" target="_blank" class="text-blue-500 hover:underline">Documentation</a>
                        @endif
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <x-ui.button variant="outline" size="sm" @click="testConnection()" x-bind:disabled="testing">
                        <template x-if="testing"><x-icon name="arrow-path" class="w-4 h-4 animate-spin mr-1" /></template>
                        <template x-if="!testing"><x-icon name="signal" class="w-4 h-4 mr-1" /></template>
                        <span x-text="testing ? 'Testing...' : 'Test Connection'"></span>
                    </x-ui.button>
                    <x-ui.button variant="outline" size="sm" @click="$dispatch('open-modal-edit-provider')">
                        <x-icon name="pencil" class="w-4 h-4 mr-1" /> Edit
                    </x-ui.button>
                    <x-ui.button variant="outline" size="sm" @click="toggleActive()">
                        <span x-text="provActive ? 'Deactivate' : 'Activate'"></span>
                    </x-ui.button>
                </div>

                {{-- Health status indicator --}}
                <div x-show="healthStatus !== null" x-transition class="mt-3 flex items-center gap-2">
                    <div class="w-2.5 h-2.5 rounded-full" :class="healthStatus === 'ok' ? 'bg-green-400' : 'bg-red-400'"></div>
                    <span class="text-xs font-medium" :class="healthStatus === 'ok' ? 'text-green-600' : 'text-red-600'" x-text="healthMessage"></span>
                </div>
            </div>
        </div>

        {{-- Credentials --}}
        <div class="mt-6 flex items-center justify-between">
            <h3 class="text-base font-semibold text-gray-900">Credentials ({{ $provider->credentials->count() }})</h3>
            <x-ui.button variant="primary" size="sm" @click="$dispatch('open-modal-add-credential')">
                <x-icon name="plus" class="w-4 h-4 mr-1" /> Add Credential
            </x-ui.button>
        </div>

        @if($provider->credentials->isNotEmpty())
            <div class="mt-3 bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <x-ui.table>
                    <x-slot:head>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Environment</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Scope</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase hidden sm:table-cell">Created</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </x-slot:head>
                    @foreach($provider->credentials as $cred)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <x-ui.badge :color="$cred->environment === 'production' ? 'green' : 'orange'" size="sm">
                                    {{ ucfirst($cred->environment) }}
                                </x-ui.badge>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-700">
                                @if($cred->cluster)
                                    Cluster: {{ $cred->cluster->name }}
                                @elseif($cred->country)
                                    Country: {{ $cred->country->name }}
                                @else
                                    Global
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <x-ui.badge :color="$cred->is_active ? 'green' : 'red'" size="sm">
                                    {{ $cred->is_active ? 'Active' : 'Inactive' }}
                                </x-ui.badge>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500 hidden sm:table-cell">
                                {{ $cred->created_at?->format('M d, Y') }}
                            </td>
                            <td class="px-4 py-3 text-right">
                                <button @click="deleteCred({{ $cred->id }})" class="text-red-400 hover:text-red-600 p-1" title="Delete">
                                    <x-icon name="trash" class="w-4 h-4 inline" />
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </x-ui.table>
            </div>
        @else
            <x-ui.card class="mt-3">
                <x-ui.empty-state title="No credentials" description="Add credentials for this provider to start using the integration." icon="key" />
            </x-ui.card>
        @endif

        {{-- Edit Provider Modal --}}
        <x-ui.modal name="edit-provider" maxWidth="xl">
            <form @submit.prevent="saveProvider()">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900">Edit Provider</h3>
                </div>
                <div class="px-6 py-4 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                        <input type="text" x-model="editForm.name" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea x-model="editForm.description" rows="2" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm"></textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Base URL</label>
                            <input type="url" x-model="editForm.base_url" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Docs URL</label>
                            <input type="url" x-model="editForm.docs_url" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm" />
                        </div>
                    </div>
                </div>
                <div class="px-6 py-3 bg-gray-50 border-t border-gray-100 flex justify-end gap-2">
                    <x-ui.button variant="outline" size="sm" @click="$dispatch('close-modal-edit-provider')" type="button">Cancel</x-ui.button>
                    <x-ui.button variant="primary" size="sm" type="submit" x-bind:disabled="loading">Save</x-ui.button>
                </div>
            </form>
        </x-ui.modal>

        {{-- Add Credential Modal --}}
        <x-ui.modal name="add-credential" maxWidth="xl">
            <form @submit.prevent="addCredential()">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900">Add Credentials</h3>
                </div>
                <div class="px-6 py-4 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Environment *</label>
                        <select x-model="credForm.environment" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm">
                            <option value="sandbox">Sandbox</option>
                            <option value="production">Production</option>
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Country (optional)</label>
                            <select x-model="credForm.country_id" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm">
                                <option value="">Global</option>
                                @foreach($countries as $country)
                                    <option value="{{ $country->id }}">{{ $country->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Cluster (optional)</label>
                            <select x-model="credForm.cluster_id" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm">
                                <option value="">All clusters</option>
                                @foreach($clusters as $cluster)
                                    <option value="{{ $cluster->id }}">{{ $cluster->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">API Key</label>
                        <input type="text" x-model="credForm.api_key" placeholder="pk_live_..." class="w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm font-mono" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">API Secret</label>
                        <input type="password" x-model="credForm.api_secret" placeholder="sk_live_..." class="w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm font-mono" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Webhook Secret (optional)</label>
                        <input type="password" x-model="credForm.webhook_secret" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm font-mono" />
                    </div>
                </div>
                <div class="px-6 py-3 bg-gray-50 border-t border-gray-100 flex justify-end gap-2">
                    <x-ui.button variant="outline" size="sm" @click="$dispatch('close-modal-add-credential')" type="button">Cancel</x-ui.button>
                    <x-ui.button variant="primary" size="sm" type="submit" x-bind:disabled="loading">
                        <template x-if="loading"><x-icon name="arrow-path" class="w-4 h-4 animate-spin mr-1" /></template>
                        Save Credentials
                    </x-ui.button>
                </div>
            </form>
        </x-ui.modal>

        {{-- Toast --}}
        <div x-show="toast.show" x-transition class="fixed bottom-4 right-4 z-50 px-4 py-3 rounded-lg shadow-lg text-sm font-medium"
             :class="toast.type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'" x-text="toast.message"></div>
    </div>

    <script>
        function providerDetail() {
            return {
                loading: false,
                testing: false,
                healthStatus: null,
                healthMessage: '',
                provActive: {{ $provider->is_active ? 'true' : 'false' }},
                editForm: {
                    name: @json($provider->name),
                    description: @json($provider->description ?? ''),
                    base_url: @json($provider->base_url ?? ''),
                    docs_url: @json($provider->docs_url ?? ''),
                },
                credForm: {
                    environment: 'sandbox',
                    country_id: '',
                    cluster_id: '',
                    api_key: '',
                    api_secret: '',
                    webhook_secret: '',
                },
                toast: { show: false, message: '', type: 'success' },

                csrfToken() { return document.querySelector('meta[name="csrf-token"]').content; },
                showToast(message, type = 'success') {
                    this.toast = { show: true, message, type };
                    setTimeout(() => this.toast.show = false, 3000);
                },

                async saveProvider() {
                    this.loading = true;
                    try {
                        const res = await fetch('{{ route("admin.api-providers.update", $provider->id) }}', {
                            method: 'PUT',
                            headers: { 'X-CSRF-TOKEN': this.csrfToken(), 'Content-Type': 'application/json', 'Accept': 'application/json' },
                            body: JSON.stringify(this.editForm),
                        });
                        const data = await res.json();
                        this.showToast(data.message);
                        this.$dispatch('close-modal-edit-provider');
                        setTimeout(() => location.reload(), 800);
                    } catch (e) { this.showToast('Failed to save.', 'error'); }
                    this.loading = false;
                },

                async toggleActive() {
                    this.loading = true;
                    try {
                        const res = await fetch('{{ route("admin.api-providers.toggle", $provider->id) }}', {
                            method: 'PATCH',
                            headers: { 'X-CSRF-TOKEN': this.csrfToken(), 'Accept': 'application/json' },
                        });
                        const data = await res.json();
                        this.provActive = data.is_active;
                        this.showToast(data.message);
                    } catch (e) { this.showToast('Failed to toggle.', 'error'); }
                    this.loading = false;
                },

                async addCredential() {
                    this.loading = true;
                    const body = {
                        environment: this.credForm.environment,
                        country_id: this.credForm.country_id || null,
                        cluster_id: this.credForm.cluster_id || null,
                        credentials: {
                            api_key: this.credForm.api_key,
                            api_secret: this.credForm.api_secret,
                            webhook_secret: this.credForm.webhook_secret,
                        },
                    };
                    try {
                        const res = await fetch('{{ route("admin.api-credentials.store", $provider->id) }}', {
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': this.csrfToken(), 'Content-Type': 'application/json', 'Accept': 'application/json' },
                            body: JSON.stringify(body),
                        });
                        const data = await res.json();
                        if (!res.ok) { this.showToast(Object.values(data.errors || {}).flat().join(', '), 'error'); }
                        else { this.showToast(data.message); this.$dispatch('close-modal-add-credential'); setTimeout(() => location.reload(), 800); }
                    } catch (e) { this.showToast('Failed to save credentials.', 'error'); }
                    this.loading = false;
                },

                async testConnection() {
                    this.testing = true;
                    this.healthStatus = null;
                    try {
                        const res = await fetch('{{ route("admin.api-providers.health", $provider->id) }}', {
                            headers: { 'Accept': 'application/json' },
                        });
                        const data = await res.json();
                        this.healthStatus = data.status;
                        this.healthMessage = data.message;
                        this.showToast(data.message, data.status === 'ok' ? 'success' : 'error');
                    } catch (e) {
                        this.healthStatus = 'error';
                        this.healthMessage = 'Connection test failed.';
                        this.showToast('Connection test failed.', 'error');
                    }
                    this.testing = false;
                },

                async deleteCred(id) {
                    if (!confirm('Delete this credential?')) return;
                    try {
                        const res = await fetch('/admin/api-credentials/' + id, {
                            method: 'DELETE',
                            headers: { 'X-CSRF-TOKEN': this.csrfToken(), 'Accept': 'application/json' },
                        });
                        const data = await res.json();
                        this.showToast(data.message);
                        setTimeout(() => location.reload(), 800);
                    } catch (e) { this.showToast('Failed to delete.', 'error'); }
                },
            };
        }
    </script>
@endsection
