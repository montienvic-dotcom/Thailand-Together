@extends('layouts.admin')

@section('title', 'Applications')

@section('content')
    <x-admin.breadcrumb :items="[
        ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
        ['label' => 'Applications'],
    ]" />

    <div x-data="appListManager()" x-cloak>
        <div class="mt-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <h2 class="text-lg font-bold text-gray-900">Applications ({{ $apps->count() }})</h2>
            <x-ui.button variant="primary" size="sm" @click="$dispatch('open-modal-create-app')">
                <x-icon name="plus" class="w-4 h-4 mr-1" /> Add Application
            </x-ui.button>
        </div>

        <x-ui.card class="mt-4">
            @forelse($apps ?? [] as $app)
                @if($loop->first)
                    <x-ui.table>
                        <x-slot:head>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">App</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase hidden sm:table-cell">Code</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase hidden md:table-cell">Type</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase hidden md:table-cell">Source</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Modules</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-4 py-3"></th>
                        </x-slot:head>

                        @foreach($apps as $app)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-lg flex items-center justify-center" style="background-color: {{ $app->color ?? '#6C757D' }}20">
                                            <x-icon :name="$app->icon ?? 'cube'" class="w-5 h-5" style="color: {{ $app->color ?? '#6C757D' }}" />
                                        </div>
                                        <span class="text-sm font-medium text-gray-900">{{ $app->name }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-500 font-mono hidden sm:table-cell">{{ $app->code }}</td>
                                <td class="px-4 py-3 hidden md:table-cell">
                                    <x-ui.badge :color="$app->type === 'mobile' ? 'purple' : ($app->type === 'hybrid' ? 'teal' : 'blue')">
                                        {{ ucfirst($app->type) }}
                                    </x-ui.badge>
                                </td>
                                <td class="px-4 py-3 hidden md:table-cell">
                                    <x-ui.badge :color="$app->source === 'internal' ? 'green' : 'orange'">
                                        {{ ucfirst($app->source) }}
                                    </x-ui.badge>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-500">{{ $app->active_modules_count ?? 0 }}</td>
                                <td class="px-4 py-3">
                                    <x-ui.badge :color="$app->is_active ? 'green' : 'red'">
                                        {{ $app->is_active ? 'Active' : 'Inactive' }}
                                    </x-ui.badge>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <x-ui.button variant="ghost" size="sm" :href="route('admin.applications.show', $app->id)">
                                        View
                                    </x-ui.button>
                                </td>
                            </tr>
                        @endforeach
                    </x-ui.table>
                @endif
            @empty
                <x-ui.empty-state title="No applications found" description="Create your first application to get started." icon="cube" />
            @endforelse
        </x-ui.card>

        {{-- Create Application Modal --}}
        <x-ui.modal name="create-app" maxWidth="xl">
            <form @submit.prevent="createApp()">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900">Create Application</h3>
                </div>
                <div class="px-6 py-4 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Name *</label>
                        <input type="text" x-model="form.name" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Code</label>
                        <input type="text" x-model="form.code" placeholder="AUTO_GENERATED" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm font-mono" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea x-model="form.description" rows="2" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm"></textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Type *</label>
                            <select x-model="form.type" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm">
                                <option value="web">Web</option>
                                <option value="mobile">Mobile</option>
                                <option value="hybrid">Hybrid</option>
                                <option value="api">API</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Source *</label>
                            <select x-model="form.source" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm">
                                <option value="internal">Internal</option>
                                <option value="external">External</option>
                                <option value="third-party">Third-Party</option>
                            </select>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Icon</label>
                            <input type="text" x-model="form.icon" placeholder="cube" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Color</label>
                            <input type="color" x-model="form.color" class="w-full h-[38px] rounded-lg border-gray-300 shadow-sm" />
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Base URL</label>
                        <input type="url" x-model="form.base_url" placeholder="https://..." class="w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm" />
                    </div>
                    <div class="flex items-center gap-2">
                        <input type="checkbox" x-model="form.show_in_menu" class="rounded border-gray-300 text-orange-500 focus:ring-orange-500" />
                        <label class="text-sm text-gray-700">Show in menu</label>
                    </div>
                </div>
                <div class="px-6 py-3 bg-gray-50 border-t border-gray-100 flex justify-end gap-2">
                    <x-ui.button variant="outline" size="sm" @click="$dispatch('close-modal-create-app')" type="button">Cancel</x-ui.button>
                    <x-ui.button variant="primary" size="sm" type="submit" x-bind:disabled="loading">
                        <template x-if="loading"><x-icon name="arrow-path" class="w-4 h-4 animate-spin mr-1" /></template>
                        Create Application
                    </x-ui.button>
                </div>
            </form>
        </x-ui.modal>

        {{-- Toast --}}
        <div x-show="toast.show" x-transition class="fixed bottom-4 right-4 z-50 px-4 py-3 rounded-lg shadow-lg text-sm font-medium"
             :class="toast.type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'" x-text="toast.message"></div>
    </div>

    <script>
        function appListManager() {
            return {
                loading: false,
                form: { name: '', code: '', description: '', type: 'web', source: 'internal', icon: 'cube', color: '#F97316', base_url: '', show_in_menu: true },
                toast: { show: false, message: '', type: 'success' },

                csrfToken() { return document.querySelector('meta[name="csrf-token"]').content; },
                showToast(message, type = 'success') {
                    this.toast = { show: true, message, type };
                    setTimeout(() => this.toast.show = false, 3000);
                },

                async createApp() {
                    this.loading = true;
                    try {
                        const res = await fetch('{{ route("admin.applications.store") }}', {
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': this.csrfToken(), 'Content-Type': 'application/json', 'Accept': 'application/json' },
                            body: JSON.stringify(this.form),
                        });
                        const data = await res.json();
                        if (!res.ok) { this.showToast(Object.values(data.errors || {}).flat().join(', '), 'error'); }
                        else { this.showToast(data.message); this.$dispatch('close-modal-create-app'); setTimeout(() => location.reload(), 800); }
                    } catch (e) { this.showToast('Failed to create application.', 'error'); }
                    this.loading = false;
                },
            };
        }
    </script>
@endsection
