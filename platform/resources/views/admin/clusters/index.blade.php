@extends('layouts.admin')

@section('title', 'Clusters & Countries')

@section('content')
    <x-admin.breadcrumb :items="[
        ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
        ['label' => 'Clusters & Countries'],
    ]" />

    <div x-data="clusterManager()" x-cloak>
        {{-- Header --}}
        <div class="mt-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <h2 class="text-lg font-bold text-gray-900">Clusters & Countries</h2>
            <div class="flex gap-2">
                <x-ui.button variant="outline" size="sm" @click="$dispatch('open-modal-create-country')">
                    <x-icon name="plus" class="w-4 h-4 mr-1" /> Add Country
                </x-ui.button>
                <x-ui.button variant="primary" size="sm" @click="$dispatch('open-modal-create-cluster')">
                    <x-icon name="plus" class="w-4 h-4 mr-1" /> Add Cluster
                </x-ui.button>
            </div>
        </div>

        {{-- Countries & Clusters --}}
        @forelse($countries as $country)
            <div class="mt-4">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-5">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-purple-100 flex items-center justify-center">
                                <x-icon name="globe" class="w-5 h-5 text-purple-600" />
                            </div>
                            <div>
                                <h3 class="text-base font-semibold text-gray-900">{{ $country->name }}</h3>
                                <div class="flex items-center gap-2 mt-0.5">
                                    <span class="text-xs text-gray-500 font-mono">{{ $country->code }}</span>
                                    @if($country->currency_code)
                                        <span class="text-xs text-gray-400">{{ $country->currency_code }}</span>
                                    @endif
                                    @if($country->timezone)
                                        <span class="text-xs text-gray-400">{{ $country->timezone }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <x-ui.badge :color="$country->is_active ? 'green' : 'red'" size="sm">
                                {{ $country->is_active ? 'Active' : 'Inactive' }}
                            </x-ui.badge>
                            <button @click="toggleCountry({{ $country->id }})" class="text-gray-400 hover:text-gray-600 p-1" title="Toggle active">
                                <x-icon name="power" class="w-4 h-4" />
                            </button>
                        </div>
                    </div>

                    {{-- Clusters within this country --}}
                    @if($country->clusters->isNotEmpty())
                        <div class="mt-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                            @foreach($country->clusters as $cluster)
                                <div class="border border-gray-200 rounded-lg p-3 hover:shadow-sm transition-shadow"
                                     :class="{ 'opacity-50': !clusterStates[{{ $cluster->id }}] }">
                                    <div class="flex items-start justify-between">
                                        <div class="min-w-0 flex-1">
                                            <a href="{{ route('admin.clusters.show', $cluster->id) }}"
                                               class="text-sm font-medium text-gray-900 hover:text-orange-600 block truncate">
                                                {{ $cluster->name }}
                                            </a>
                                            <span class="text-xs text-gray-500 font-mono">{{ $cluster->code }}</span>
                                        </div>
                                        <div class="flex items-center gap-1 flex-shrink-0">
                                            <button @click="toggleCluster({{ $cluster->id }})"
                                                    class="relative inline-flex h-5 w-9 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500"
                                                    :class="clusterStates[{{ $cluster->id }}] ? 'bg-green-500' : 'bg-gray-300'"
                                                    role="switch">
                                                <span class="pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                                                      :class="clusterStates[{{ $cluster->id }}] ? 'translate-x-4' : 'translate-x-0'"></span>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="mt-2 flex items-center gap-2 text-xs text-gray-400">
                                        @if($cluster->timezone)
                                            <span>{{ $cluster->timezone }}</span>
                                        @endif
                                        @if($cluster->launch_date)
                                            <span>Launch: {{ $cluster->launch_date->format('M Y') }}</span>
                                        @endif
                                        <span>{{ $cluster->applications_count ?? 0 }} apps</span>
                                    </div>
                                    @if($cluster->description)
                                        <p class="mt-1 text-xs text-gray-500 line-clamp-2">{{ $cluster->description }}</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="mt-3 text-sm text-gray-400">No clusters in this country yet.</p>
                    @endif
                </div>
            </div>
        @empty
            <x-ui.card class="mt-4">
                <x-ui.empty-state title="No countries" description="Add your first country to start organizing clusters." icon="globe" />
            </x-ui.card>
        @endforelse

        {{-- Create Country Modal --}}
        <x-ui.modal name="create-country" maxWidth="lg">
            <form @submit.prevent="createCountry()">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900">Add Country</h3>
                </div>
                <div class="px-6 py-4 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Country Name *</label>
                        <input type="text" x-model="countryForm.name" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm" />
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Code</label>
                            <input type="text" x-model="countryForm.code" placeholder="TH" maxlength="10" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm font-mono uppercase" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Alpha-2</label>
                            <input type="text" x-model="countryForm.code_alpha2" placeholder="TH" maxlength="2" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm font-mono uppercase" />
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Currency</label>
                            <input type="text" x-model="countryForm.currency_code" placeholder="THB" maxlength="10" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm font-mono uppercase" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Timezone</label>
                            <input type="text" x-model="countryForm.timezone" placeholder="Asia/Bangkok" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm" />
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Default Locale</label>
                        <input type="text" x-model="countryForm.default_locale" placeholder="th" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm" />
                    </div>
                </div>
                <div class="px-6 py-3 bg-gray-50 border-t border-gray-100 flex justify-end gap-2">
                    <x-ui.button variant="outline" size="sm" @click="$dispatch('close-modal-create-country')" type="button">Cancel</x-ui.button>
                    <x-ui.button variant="primary" size="sm" type="submit" x-bind:disabled="loading">
                        <template x-if="loading"><x-icon name="arrow-path" class="w-4 h-4 animate-spin mr-1" /></template>
                        Create Country
                    </x-ui.button>
                </div>
            </form>
        </x-ui.modal>

        {{-- Create Cluster Modal --}}
        <x-ui.modal name="create-cluster" maxWidth="lg">
            <form @submit.prevent="createCluster()">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900">Add Cluster</h3>
                </div>
                <div class="px-6 py-4 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Country *</label>
                        <select x-model="clusterForm.country_id" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm">
                            <option value="">Select country...</option>
                            @foreach($countries as $country)
                                <option value="{{ $country->id }}">{{ $country->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Cluster Name *</label>
                        <input type="text" x-model="clusterForm.name" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm" placeholder="e.g. Pattaya" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Code</label>
                        <input type="text" x-model="clusterForm.code" placeholder="AUTO_GENERATED" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm font-mono" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea x-model="clusterForm.description" rows="2" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm"></textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Timezone</label>
                            <input type="text" x-model="clusterForm.timezone" placeholder="Inherit from country" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Launch Date</label>
                            <input type="date" x-model="clusterForm.launch_date" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm" />
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">DB Connection</label>
                        <input type="text" x-model="clusterForm.database_connection" placeholder="mysql" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm font-mono" />
                    </div>
                </div>
                <div class="px-6 py-3 bg-gray-50 border-t border-gray-100 flex justify-end gap-2">
                    <x-ui.button variant="outline" size="sm" @click="$dispatch('close-modal-create-cluster')" type="button">Cancel</x-ui.button>
                    <x-ui.button variant="primary" size="sm" type="submit" x-bind:disabled="loading">
                        <template x-if="loading"><x-icon name="arrow-path" class="w-4 h-4 animate-spin mr-1" /></template>
                        Create Cluster
                    </x-ui.button>
                </div>
            </form>
        </x-ui.modal>

        {{-- Toast --}}
        <div x-show="toast.show" x-transition class="fixed bottom-4 right-4 z-50 px-4 py-3 rounded-lg shadow-lg text-sm font-medium"
             :class="toast.type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'" x-text="toast.message"></div>
    </div>

    <script>
        function clusterManager() {
            return {
                loading: false,
                clusterStates: {
                    @foreach($countries as $country)
                        @foreach($country->clusters as $cluster)
                            {{ $cluster->id }}: {{ $cluster->is_active ? 'true' : 'false' }},
                        @endforeach
                    @endforeach
                },
                countryForm: { name: '', code: '', code_alpha2: '', currency_code: '', timezone: '', default_locale: '' },
                clusterForm: { country_id: '', name: '', code: '', description: '', timezone: '', launch_date: '', database_connection: '' },
                toast: { show: false, message: '', type: 'success' },

                csrfToken() { return document.querySelector('meta[name="csrf-token"]').content; },
                showToast(message, type = 'success') {
                    this.toast = { show: true, message, type };
                    setTimeout(() => this.toast.show = false, 3000);
                },

                async createCountry() {
                    this.loading = true;
                    try {
                        const res = await fetch('{{ route("admin.countries.store") }}', {
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': this.csrfToken(), 'Content-Type': 'application/json', 'Accept': 'application/json' },
                            body: JSON.stringify(this.countryForm),
                        });
                        const data = await res.json();
                        if (!res.ok) { this.showToast(Object.values(data.errors || {}).flat().join(', '), 'error'); }
                        else { this.showToast(data.message); this.$dispatch('close-modal-create-country'); setTimeout(() => location.reload(), 800); }
                    } catch (e) { this.showToast('Failed to create country.', 'error'); }
                    this.loading = false;
                },

                async createCluster() {
                    this.loading = true;
                    try {
                        const res = await fetch('{{ route("admin.clusters.store") }}', {
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': this.csrfToken(), 'Content-Type': 'application/json', 'Accept': 'application/json' },
                            body: JSON.stringify(this.clusterForm),
                        });
                        const data = await res.json();
                        if (!res.ok) { this.showToast(Object.values(data.errors || {}).flat().join(', '), 'error'); }
                        else { this.showToast(data.message); this.$dispatch('close-modal-create-cluster'); setTimeout(() => location.reload(), 800); }
                    } catch (e) { this.showToast('Failed to create cluster.', 'error'); }
                    this.loading = false;
                },

                async toggleCluster(id) {
                    try {
                        const res = await fetch('/admin/clusters/' + id + '/toggle', {
                            method: 'PATCH',
                            headers: { 'X-CSRF-TOKEN': this.csrfToken(), 'Accept': 'application/json' },
                        });
                        const data = await res.json();
                        this.clusterStates[id] = data.is_active;
                        this.showToast(data.message);
                    } catch (e) { this.showToast('Failed to toggle.', 'error'); }
                },

                async toggleCountry(id) {
                    try {
                        const res = await fetch('/admin/countries/' + id + '/toggle', {
                            method: 'PATCH',
                            headers: { 'X-CSRF-TOKEN': this.csrfToken(), 'Accept': 'application/json' },
                        });
                        const data = await res.json();
                        this.showToast(data.message);
                        setTimeout(() => location.reload(), 800);
                    } catch (e) { this.showToast('Failed to toggle.', 'error'); }
                },
            };
        }
    </script>
@endsection
