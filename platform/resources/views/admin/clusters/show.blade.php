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
            <h3 class="text-base font-semibold text-gray-900 mb-3">Assigned Applications ({{ $cluster->applications->count() }})</h3>
            @if($cluster->applications->isNotEmpty())
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <x-ui.table>
                        <x-slot:head>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Application</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase hidden sm:table-cell">Type</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
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
                                    <x-ui.badge :color="$app->pivot->is_active ? 'green' : 'red'" size="sm">
                                        {{ $app->pivot->is_active ? 'Active' : 'Inactive' }}
                                    </x-ui.badge>
                                </td>
                            </tr>
                        @endforeach
                    </x-ui.table>
                </div>
            @else
                <x-ui.card>
                    <x-ui.empty-state title="No applications assigned" description="Assign applications to this cluster from the Applications page." icon="cube" />
                </x-ui.card>
            @endif
        </div>

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
