@extends('layouts.admin')

@section('title', $cluster->name ?? 'Cluster')

@section('content')
    <x-admin.breadcrumb :items="[
        ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
        ['label' => 'Clusters', 'url' => route('admin.clusters')],
        ['label' => $cluster->name],
    ]" />

    <div x-data="clusterDetail()" x-cloak>
        {{-- Cluster header --}}
        <div class="mt-4 bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-xl bg-green-100 flex items-center justify-center flex-shrink-0">
                        <x-icon name="map" class="w-6 h-6 text-green-600" />
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">{{ $cluster->name }}</h2>
                        <div class="mt-1 flex items-center gap-2 flex-wrap">
                            <x-ui.badge color="purple">{{ $cluster->country->name ?? 'N/A' }}</x-ui.badge>
                            <span class="text-xs text-gray-500 font-mono">{{ $cluster->code }}</span>
                            @if($cluster->timezone)
                                <span class="text-xs text-gray-400">{{ $cluster->timezone }}</span>
                            @endif
                            @if($cluster->launch_date)
                                <span class="text-xs text-gray-400">Launch: {{ $cluster->launch_date->format('M d, Y') }}</span>
                            @endif
                        </div>
                        @if($cluster->description)
                            <p class="mt-2 text-sm text-gray-500">{{ $cluster->description }}</p>
                        @endif
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <div class="flex items-center gap-2">
                        <span class="text-xs text-gray-500" x-text="clusterActive ? 'Active' : 'Inactive'"></span>
                        <button @click="toggleCluster()"
                                class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500"
                                :class="clusterActive ? 'bg-green-500' : 'bg-gray-300'"
                                role="switch">
                            <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                                  :class="clusterActive ? 'translate-x-5' : 'translate-x-0'"></span>
                        </button>
                    </div>
                    <x-ui.button variant="outline" size="sm" @click="$dispatch('open-modal-edit-cluster')">
                        <x-icon name="pencil" class="w-4 h-4 mr-1" /> Edit
                    </x-ui.button>
                </div>
            </div>
        </div>

        {{-- Cluster Info Cards --}}
        <div class="mt-6 grid grid-cols-1 sm:grid-cols-3 gap-4">
            <x-ui.card>
                <div class="text-center">
                    <p class="text-2xl font-bold text-gray-900">{{ $cluster->applications->count() }}</p>
                    <p class="text-sm text-gray-500 mt-1">Applications</p>
                </div>
            </x-ui.card>
            <x-ui.card>
                <div class="text-center">
                    <p class="text-2xl font-bold text-gray-900">{{ $cluster->database_connection ?? 'mysql' }}</p>
                    <p class="text-sm text-gray-500 mt-1">DB Connection</p>
                </div>
            </x-ui.card>
            <x-ui.card>
                <div class="text-center">
                    <p class="text-2xl font-bold text-gray-900">{{ $cluster->default_locale ?? 'en' }}</p>
                    <p class="text-sm text-gray-500 mt-1">Default Locale</p>
                </div>
            </x-ui.card>
        </div>

        {{-- Assigned Applications --}}
        <div class="mt-6">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-base font-semibold text-gray-900">Assigned Applications ({{ $cluster->applications->count() }})</h3>
                <x-ui.button variant="primary" size="sm" @click="$dispatch('open-modal-assign-apps')">
                    <x-icon name="plus" class="w-4 h-4 mr-1" /> Assign Apps
                </x-ui.button>
            </div>
            @if($cluster->applications->isNotEmpty())
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <x-ui.table>
                        <x-slot:head>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Application</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase hidden sm:table-cell">Type</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </x-slot:head>
                        @foreach($cluster->applications as $app)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background-color: {{ $app->color ?? '#6C757D' }}20">
                                            <x-icon :name="$app->icon ?? 'cube'" class="w-4 h-4" style="color: {{ $app->color ?? '#6C757D' }}" />
                                        </div>
                                        <a href="{{ route('admin.applications.show', $app->id) }}" class="text-sm font-medium text-gray-900 hover:text-orange-600">{{ $app->name }}</a>
                                    </div>
                                </td>
                                <td class="px-4 py-3 hidden sm:table-cell">
                                    <x-ui.badge :color="$app->type === 'mobile' ? 'purple' : 'blue'" size="sm">{{ ucfirst($app->type) }}</x-ui.badge>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <button @click="toggleClusterApp({{ $app->id }})"
                                            class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium transition-colors cursor-pointer"
                                            :class="appStates[{{ $app->id }}] ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-red-100 text-red-700 hover:bg-red-200'">
                                        <span x-text="appStates[{{ $app->id }}] ? 'Active' : 'Inactive'"></span>
                                    </button>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('admin.applications.show', $app->id) }}" class="text-gray-400 hover:text-gray-600 p-1" title="View app">
                                        <x-icon name="arrow-top-right-on-square" class="w-4 h-4 inline" />
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </x-ui.table>
                </div>
            @else
                <x-ui.card>
                    <x-ui.empty-state title="No applications assigned" description="Click 'Assign Apps' to add applications to this cluster." icon="cube" />
                </x-ui.card>
            @endif
        </div>

        {{-- Assign Apps Modal --}}
        <x-ui.modal name="assign-apps" maxWidth="xl">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="text-lg font-semibold text-gray-900">Assign Applications to {{ $cluster->name }}</h3>
            </div>
            <div class="px-6 py-4 max-h-[60vh] overflow-y-auto">
                <div class="space-y-2">
                    @foreach($allApps as $app)
                        <label class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 cursor-pointer border border-gray-100">
                            <input type="checkbox" value="{{ $app->id }}" x-model="selectedApps"
                                   class="rounded border-gray-300 text-orange-500 focus:ring-orange-500" />
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0" style="background-color: {{ $app->color ?? '#6C757D' }}20">
                                <x-icon :name="$app->icon ?? 'cube'" class="w-4 h-4" style="color: {{ $app->color ?? '#6C757D' }}" />
                            </div>
                            <div class="flex-1 min-w-0">
                                <span class="text-sm font-medium text-gray-900">{{ $app->name }}</span>
                                <span class="text-xs text-gray-500 ml-2">{{ $app->code }}</span>
                            </div>
                            <x-ui.badge :color="$app->type === 'mobile' ? 'purple' : 'blue'" size="sm">{{ ucfirst($app->type) }}</x-ui.badge>
                        </label>
                    @endforeach
                </div>
            </div>
            <div class="px-6 py-3 bg-gray-50 border-t border-gray-100 flex justify-between items-center">
                <span class="text-xs text-gray-500" x-text="selectedApps.length + ' app(s) selected'"></span>
                <div class="flex gap-2">
                    <x-ui.button variant="outline" size="sm" @click="$dispatch('close-modal-assign-apps')">Cancel</x-ui.button>
                    <x-ui.button variant="primary" size="sm" @click="syncApps()" x-bind:disabled="loading">
                        <template x-if="loading"><x-icon name="arrow-path" class="w-4 h-4 animate-spin mr-1" /></template>
                        Save Assignments
                    </x-ui.button>
                </div>
            </div>
        </x-ui.modal>

        {{-- Edit Cluster Modal --}}
        <x-ui.modal name="edit-cluster" maxWidth="lg">
            <form @submit.prevent="saveCluster()">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900">Edit Cluster</h3>
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
                            <label class="block text-sm font-medium text-gray-700 mb-1">Timezone</label>
                            <input type="text" x-model="editForm.timezone" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Default Locale</label>
                            <input type="text" x-model="editForm.default_locale" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm" />
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">DB Connection</label>
                        <input type="text" x-model="editForm.database_connection" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm font-mono" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Launch Date</label>
                        <input type="date" x-model="editForm.launch_date" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm" />
                    </div>
                </div>
                <div class="px-6 py-3 bg-gray-50 border-t border-gray-100 flex justify-end gap-2">
                    <x-ui.button variant="outline" size="sm" @click="$dispatch('close-modal-edit-cluster')" type="button">Cancel</x-ui.button>
                    <x-ui.button variant="primary" size="sm" type="submit" x-bind:disabled="loading">Save</x-ui.button>
                </div>
            </form>
        </x-ui.modal>

        {{-- Toast --}}
        <div x-show="toast.show" x-transition class="fixed bottom-4 right-4 z-50 px-4 py-3 rounded-lg shadow-lg text-sm font-medium"
             :class="toast.type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'" x-text="toast.message"></div>
    </div>

    <script>
        function clusterDetail() {
            return {
                loading: false,
                clusterActive: {{ $cluster->is_active ? 'true' : 'false' }},
                selectedApps: @json($cluster->applications->pluck('id')->map(fn($id) => (string) $id)),
                appStates: {
                    @foreach($cluster->applications as $app)
                        {{ $app->id }}: {{ $app->pivot->is_active ? 'true' : 'false' }},
                    @endforeach
                },
                editForm: {
                    name: @json($cluster->name),
                    description: @json($cluster->description ?? ''),
                    timezone: @json($cluster->getRawOriginal('timezone') ?? ''),
                    default_locale: @json($cluster->default_locale ?? ''),
                    database_connection: @json($cluster->database_connection ?? ''),
                    launch_date: @json($cluster->launch_date?->format('Y-m-d') ?? ''),
                },
                toast: { show: false, message: '', type: 'success' },

                csrfToken() { return document.querySelector('meta[name="csrf-token"]').content; },
                showToast(message, type = 'success') {
                    this.toast = { show: true, message, type };
                    setTimeout(() => this.toast.show = false, 3000);
                },

                async syncApps() {
                    this.loading = true;
                    try {
                        const res = await fetch('{{ route("admin.clusters.sync-apps", $cluster->id) }}', {
                            method: 'PUT',
                            headers: { 'X-CSRF-TOKEN': this.csrfToken(), 'Content-Type': 'application/json', 'Accept': 'application/json' },
                            body: JSON.stringify({ application_ids: this.selectedApps.map(Number) }),
                        });
                        const data = await res.json();
                        this.showToast(data.message);
                        this.$dispatch('close-modal-assign-apps');
                        setTimeout(() => location.reload(), 800);
                    } catch (e) { this.showToast('Failed to sync apps.', 'error'); }
                    this.loading = false;
                },

                async toggleClusterApp(appId) {
                    try {
                        const res = await fetch('/admin/clusters/{{ $cluster->id }}/apps/' + appId + '/toggle', {
                            method: 'PATCH',
                            headers: { 'X-CSRF-TOKEN': this.csrfToken(), 'Accept': 'application/json' },
                        });
                        const data = await res.json();
                        this.appStates[appId] = data.is_active;
                        this.showToast(data.message);
                    } catch (e) { this.showToast('Failed to toggle.', 'error'); }
                },

                async toggleCluster() {
                    this.loading = true;
                    try {
                        const res = await fetch('{{ route("admin.clusters.toggle", $cluster->id) }}', {
                            method: 'PATCH',
                            headers: { 'X-CSRF-TOKEN': this.csrfToken(), 'Accept': 'application/json' },
                        });
                        const data = await res.json();
                        this.clusterActive = data.is_active;
                        this.showToast(data.message);
                    } catch (e) { this.showToast('Failed to toggle.', 'error'); }
                    this.loading = false;
                },

                async saveCluster() {
                    this.loading = true;
                    try {
                        const res = await fetch('{{ route("admin.clusters.update", $cluster->id) }}', {
                            method: 'PUT',
                            headers: { 'X-CSRF-TOKEN': this.csrfToken(), 'Content-Type': 'application/json', 'Accept': 'application/json' },
                            body: JSON.stringify(this.editForm),
                        });
                        const data = await res.json();
                        this.showToast(data.message);
                        this.$dispatch('close-modal-edit-cluster');
                        setTimeout(() => location.reload(), 800);
                    } catch (e) { this.showToast('Failed to save.', 'error'); }
                    this.loading = false;
                },
            };
        }
    </script>
@endsection
