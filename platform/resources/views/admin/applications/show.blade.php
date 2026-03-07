@extends('layouts.admin')

@section('title', $app->name ?? 'Application')

@section('content')
    <x-admin.breadcrumb :items="[
        ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
        ['label' => 'Applications', 'url' => route('admin.applications')],
        ['label' => $app->name],
    ]" />

    <div x-data="appManager()" x-cloak>
        {{-- App header with actions --}}
        <div class="mt-4 bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-start justify-between">
                <div class="flex items-start gap-4">
                    <div class="w-14 h-14 rounded-xl flex items-center justify-center" style="background-color: {{ $app->color ?? '#6C757D' }}20">
                        <x-icon :name="$app->icon ?? 'cube'" class="w-8 h-8" style="color: {{ $app->color ?? '#6C757D' }}" />
                    </div>
                    <div class="flex-1">
                        <h2 class="text-xl font-bold text-gray-900">{{ $app->name }}</h2>
                        @if($app->description)
                            <p class="mt-1 text-sm text-gray-500">{{ $app->description }}</p>
                        @endif
                        <div class="mt-3 flex items-center gap-2 flex-wrap">
                            <x-ui.badge :color="$app->type === 'mobile' ? 'purple' : ($app->type === 'hybrid' ? 'teal' : 'blue')">
                                {{ ucfirst($app->type) }}
                            </x-ui.badge>
                            <x-ui.badge :color="$app->source === 'internal' ? 'green' : 'orange'">
                                {{ ucfirst($app->source) }}
                            </x-ui.badge>
                            <span class="text-xs text-gray-400 font-mono">{{ $app->code }}</span>
                            @if($app->base_url)
                                <span class="text-xs text-blue-500">{{ $app->base_url }}</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    {{-- Toggle app active --}}
                    <div class="flex items-center gap-2">
                        <span class="text-xs text-gray-500" x-text="appActive ? 'Active' : 'Inactive'"></span>
                        <button
                            @click="toggleApp()"
                            :disabled="loading"
                            class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500"
                            :class="appActive ? 'bg-green-500' : 'bg-gray-300'"
                            role="switch"
                            :aria-checked="appActive"
                        >
                            <span
                                class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                                :class="appActive ? 'translate-x-5' : 'translate-x-0'"
                            ></span>
                        </button>
                    </div>

                    {{-- Edit app button --}}
                    <x-ui.button variant="outline" size="sm" @click="$dispatch('open-modal-edit-app')">
                        <x-icon name="pencil" class="w-4 h-4 mr-1" />
                        Edit
                    </x-ui.button>
                </div>
            </div>
        </div>

        {{-- Cluster assignments --}}
        @if($app->clusters->isNotEmpty())
            <x-ui.card title="Cluster Assignments" class="mt-6">
                <div class="flex flex-wrap gap-3">
                    @foreach($app->clusters as $cluster)
                        <div class="flex items-center gap-2 px-3 py-2 bg-gray-50 rounded-lg border">
                            <span class="text-sm font-medium text-gray-700">{{ $cluster->name }}</span>
                            <x-ui.badge :color="$cluster->pivot->is_active ? 'green' : 'red'" size="sm">
                                {{ $cluster->pivot->is_active ? 'Active' : 'Inactive' }}
                            </x-ui.badge>
                        </div>
                    @endforeach
                </div>
            </x-ui.card>
        @endif

        {{-- Modules management --}}
        <div class="mt-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Modules ({{ $app->modules->count() }})</h3>
            </div>

            @forelse($app->modules as $module)
                @if($loop->first)
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        <x-ui.table>
                            <x-slot:head>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-8">#</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Module</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Code</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Premium</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Active</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </x-slot:head>
                @endif

                            <tr class="hover:bg-gray-50 transition-colors" :class="{ 'opacity-50': !modules[{{ $module->id }}]?.is_active }">
                                <td class="px-4 py-3 text-sm text-gray-400">{{ $loop->iteration }}</td>
                                <td class="px-4 py-3">
                                    <span class="text-sm font-medium text-gray-900">{{ $module->name }}</span>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-500 font-mono">{{ $module->code }}</td>
                                <td class="px-4 py-3 text-sm text-gray-500 max-w-xs truncate">{{ $module->description ?? '-' }}</td>
                                <td class="px-4 py-3 text-center">
                                    <button
                                        @click="togglePremium({{ $module->id }})"
                                        :disabled="loading"
                                        class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium transition-colors cursor-pointer"
                                        :class="modules[{{ $module->id }}]?.is_premium ? 'bg-orange-100 text-orange-700 hover:bg-orange-200' : 'bg-gray-100 text-gray-400 hover:bg-gray-200'"
                                    >
                                        <x-icon name="star" class="w-3 h-3" />
                                        <span x-text="modules[{{ $module->id }}]?.is_premium ? 'Premium' : 'Free'"></span>
                                    </button>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <button
                                        @click="toggleModule({{ $module->id }})"
                                        :disabled="loading"
                                        class="relative inline-flex h-5 w-9 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500"
                                        :class="modules[{{ $module->id }}]?.is_active ? 'bg-green-500' : 'bg-gray-300'"
                                        role="switch"
                                    >
                                        <span
                                            class="pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                                            :class="modules[{{ $module->id }}]?.is_active ? 'translate-x-4' : 'translate-x-0'"
                                        ></span>
                                    </button>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <button
                                        @click="openEditModule({{ $module->id }}, {{ json_encode($module->name) }}, {{ json_encode($module->description ?? '') }}, {{ json_encode($module->icon ?? '') }}, {{ json_encode($module->route_prefix ?? '') }})"
                                        class="text-gray-400 hover:text-gray-600 p-1"
                                        title="Edit module"
                                    >
                                        <x-icon name="pencil" class="w-4 h-4" />
                                    </button>
                                </td>
                            </tr>

                @if($loop->last)
                        </x-ui.table>
                    </div>
                @endif
            @empty
                <x-ui.card>
                    <x-ui.empty-state title="No modules" description="This application has no modules yet." icon="cube" />
                </x-ui.card>
            @endforelse
        </div>

        {{-- Edit App Modal --}}
        <x-ui.modal name="edit-app" maxWidth="xl">
            <form @submit.prevent="saveApp()">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900">Edit Application</h3>
                </div>
                <div class="px-6 py-4 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                        <input type="text" x-model="editApp.name" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea x-model="editApp.description" rows="3" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm"></textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Icon</label>
                            <input type="text" x-model="editApp.icon" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Color</label>
                            <div class="flex items-center gap-2">
                                <input type="color" x-model="editApp.color" class="h-9 w-12 rounded border-gray-300 cursor-pointer" />
                                <input type="text" x-model="editApp.color" class="flex-1 rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm font-mono" />
                            </div>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                            <select x-model="editApp.type" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm">
                                <option value="web">Web</option>
                                <option value="mobile">Mobile</option>
                                <option value="hybrid">Hybrid</option>
                                <option value="api">API</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Source</label>
                            <select x-model="editApp.source" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm">
                                <option value="internal">Internal</option>
                                <option value="external">External</option>
                                <option value="third-party">Third-party</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Base URL</label>
                        <input type="url" x-model="editApp.base_url" placeholder="https://..." class="w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm" />
                    </div>
                    <div class="flex items-center gap-2">
                        <input type="checkbox" x-model="editApp.show_in_menu" id="show_in_menu" class="rounded border-gray-300 text-orange-500 focus:ring-orange-500" />
                        <label for="show_in_menu" class="text-sm text-gray-700">Show in navigation menu</label>
                    </div>
                </div>
                <div class="px-6 py-3 bg-gray-50 border-t border-gray-100 flex justify-end gap-2">
                    <x-ui.button variant="outline" size="sm" @click="$dispatch('close-modal-edit-app')" type="button">Cancel</x-ui.button>
                    <x-ui.button variant="primary" size="sm" type="submit" x-bind:disabled="loading">
                        <template x-if="loading">
                            <x-icon name="arrow-path" class="w-4 h-4 animate-spin mr-1" />
                        </template>
                        Save Changes
                    </x-ui.button>
                </div>
            </form>
        </x-ui.modal>

        {{-- Edit Module Modal --}}
        <x-ui.modal name="edit-module" maxWidth="lg">
            <form @submit.prevent="saveModule()">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900">Edit Module</h3>
                </div>
                <div class="px-6 py-4 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                        <input type="text" x-model="editModule.name" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea x-model="editModule.description" rows="3" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm"></textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Icon</label>
                            <input type="text" x-model="editModule.icon" placeholder="Same as app icon" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Route Prefix</label>
                            <input type="text" x-model="editModule.route_prefix" placeholder="/module-path" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm" />
                        </div>
                    </div>
                </div>
                <div class="px-6 py-3 bg-gray-50 border-t border-gray-100 flex justify-end gap-2">
                    <x-ui.button variant="outline" size="sm" @click="$dispatch('close-modal-edit-module')" type="button">Cancel</x-ui.button>
                    <x-ui.button variant="primary" size="sm" type="submit" x-bind:disabled="loading">
                        <template x-if="loading">
                            <x-icon name="arrow-path" class="w-4 h-4 animate-spin mr-1" />
                        </template>
                        Save Changes
                    </x-ui.button>
                </div>
            </form>
        </x-ui.modal>

        {{-- Toast notification --}}
        <div x-show="toast.show"
             x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-2"
             class="fixed bottom-4 right-4 z-50 px-4 py-3 rounded-lg shadow-lg text-sm font-medium"
             :class="toast.type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'"
             x-text="toast.message">
        </div>
    </div>

    <script>
        function appManager() {
            return {
                loading: false,
                appActive: {{ $app->is_active ? 'true' : 'false' }},
                modules: {
                    @foreach($app->modules as $module)
                        {{ $module->id }}: { is_active: {{ $module->is_active ? 'true' : 'false' }}, is_premium: {{ $module->is_premium ? 'true' : 'false' }} },
                    @endforeach
                },
                editApp: {
                    name: @json($app->name),
                    description: @json($app->description ?? ''),
                    icon: @json($app->icon ?? ''),
                    color: @json($app->color ?? '#6C757D'),
                    type: @json($app->type ?? 'web'),
                    source: @json($app->source ?? 'internal'),
                    base_url: @json($app->base_url ?? ''),
                    show_in_menu: {{ $app->show_in_menu ? 'true' : 'false' }},
                },
                editModule: { id: null, name: '', description: '', icon: '', route_prefix: '' },
                toast: { show: false, message: '', type: 'success' },

                csrfToken() {
                    return document.querySelector('meta[name="csrf-token"]').content;
                },

                showToast(message, type = 'success') {
                    this.toast = { show: true, message, type };
                    setTimeout(() => this.toast.show = false, 3000);
                },

                async toggleApp() {
                    this.loading = true;
                    try {
                        const res = await fetch('{{ route('admin.applications.toggle', $app->id) }}', {
                            method: 'PATCH',
                            headers: { 'X-CSRF-TOKEN': this.csrfToken(), 'Accept': 'application/json' },
                        });
                        const data = await res.json();
                        this.appActive = data.is_active;
                        this.showToast(data.message);
                    } catch (e) {
                        this.showToast('Failed to toggle app.', 'error');
                    }
                    this.loading = false;
                },

                async toggleModule(id) {
                    this.loading = true;
                    try {
                        const res = await fetch('/admin/modules/' + id + '/toggle', {
                            method: 'PATCH',
                            headers: { 'X-CSRF-TOKEN': this.csrfToken(), 'Accept': 'application/json' },
                        });
                        const data = await res.json();
                        this.modules[id].is_active = data.is_active;
                        this.showToast(data.message);
                    } catch (e) {
                        this.showToast('Failed to toggle module.', 'error');
                    }
                    this.loading = false;
                },

                async togglePremium(id) {
                    this.loading = true;
                    try {
                        const res = await fetch('/admin/modules/' + id + '/toggle-premium', {
                            method: 'PATCH',
                            headers: { 'X-CSRF-TOKEN': this.csrfToken(), 'Accept': 'application/json' },
                        });
                        const data = await res.json();
                        this.modules[id].is_premium = data.is_premium;
                        this.showToast(data.message);
                    } catch (e) {
                        this.showToast('Failed to toggle premium.', 'error');
                    }
                    this.loading = false;
                },

                async saveApp() {
                    this.loading = true;
                    try {
                        const res = await fetch('{{ route('admin.applications.update', $app->id) }}', {
                            method: 'PUT',
                            headers: {
                                'X-CSRF-TOKEN': this.csrfToken(),
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify(this.editApp),
                        });
                        const data = await res.json();
                        this.showToast(data.message);
                        this.$dispatch('close-modal-edit-app');
                        setTimeout(() => location.reload(), 800);
                    } catch (e) {
                        this.showToast('Failed to save app.', 'error');
                    }
                    this.loading = false;
                },

                openEditModule(id, name, description, icon, routePrefix) {
                    this.editModule = { id, name, description, icon, route_prefix: routePrefix };
                    this.$dispatch('open-modal-edit-module');
                },

                async saveModule() {
                    this.loading = true;
                    try {
                        const res = await fetch('/admin/modules/' + this.editModule.id, {
                            method: 'PUT',
                            headers: {
                                'X-CSRF-TOKEN': this.csrfToken(),
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify(this.editModule),
                        });
                        const data = await res.json();
                        this.showToast(data.message);
                        this.$dispatch('close-modal-edit-module');
                        setTimeout(() => location.reload(), 800);
                    } catch (e) {
                        this.showToast('Failed to save module.', 'error');
                    }
                    this.loading = false;
                },
            };
        }
    </script>
@endsection
