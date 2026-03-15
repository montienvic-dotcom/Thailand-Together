@extends('layouts.admin')

@section('title', 'Journeys')

@section('content')
    <x-admin.breadcrumb :items="[
        ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
        ['label' => 'Journeys'],
    ]" />

    <div x-data="journeyManager()" x-cloak>
        <div class="mt-4 flex items-center justify-between">
            <h2 class="text-lg font-bold text-gray-900">Journeys ({{ $journeys->total() }})</h2>
            <x-ui.button variant="primary" size="sm" @click="$dispatch('open-modal-create-journey')">
                <x-icon name="plus" class="w-4 h-4 mr-1" /> Add Journey
            </x-ui.button>
        </div>

        @if($journeys->isNotEmpty())
            <x-ui.card class="mt-3">
                <x-ui.table>
                    <x-slot:head>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Code</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Title</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase hidden sm:table-cell">Group</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase hidden md:table-cell">Cluster</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </x-slot:head>
                    @foreach($journeys as $journey)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm font-mono text-gray-700">{{ $journey->journey_code }}</td>
                            <td class="px-4 py-3">
                                <div class="text-sm font-medium text-gray-900">{{ $journey->title_en }}</div>
                                @if($journey->title_th)
                                    <div class="text-xs text-gray-400">{{ $journey->title_th }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500 hidden sm:table-cell">{{ $journey->journey_group ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-500 hidden md:table-cell">{{ $journey->cluster_name ?? '-' }}</td>
                            <td class="px-4 py-3">
                                <x-ui.badge :color="match($journey->status) { 'active' => 'green', 'draft' => 'orange', 'archived' => 'gray', default => 'gray' }" size="sm">
                                    {{ ucfirst($journey->status ?? 'draft') }}
                                </x-ui.badge>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <button @click="toggleStatus('{{ $journey->journey_code }}')" class="text-gray-400 hover:text-orange-500 p-1" title="Toggle status">
                                    <x-icon name="arrow-path" class="w-4 h-4 inline" />
                                </button>
                                <button @click="deleteJourney('{{ $journey->journey_code }}')" class="text-gray-400 hover:text-red-500 p-1" title="Delete">
                                    <x-icon name="trash" class="w-4 h-4 inline" />
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </x-ui.table>
            </x-ui.card>

            @if($journeys->hasPages())
                <div class="mt-4">{{ $journeys->links() }}</div>
            @endif
        @else
            <x-ui.card class="mt-3">
                <x-ui.empty-state title="No journeys" description="Create your first journey to get started." icon="map-pin" />
            </x-ui.card>
        @endif

        {{-- Create Journey Modal --}}
        <x-ui.modal name="create-journey" maxWidth="xl">
            <form @submit.prevent="createJourney()">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900">Create Journey</h3>
                </div>
                <div class="px-6 py-4 space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Journey Code *</label>
                            <input type="text" x-model="form.journey_code" required placeholder="A1" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Group</label>
                            <input type="text" x-model="form.journey_group" placeholder="A" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm" />
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Title (EN) *</label>
                        <input type="text" x-model="form.title_en" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Title (TH)</label>
                        <input type="text" x-model="form.title_th" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm" />
                    </div>
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Duration (min)</label>
                            <input type="number" x-model="form.total_minutes_sum" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">GMV/Person</label>
                            <input type="number" step="0.01" x-model="form.gmv_per_person" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select x-model="form.status" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm">
                                <option value="draft">Draft</option>
                                <option value="active">Active</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="px-6 py-3 bg-gray-50 border-t border-gray-100 flex justify-end gap-2">
                    <x-ui.button variant="outline" size="sm" @click="$dispatch('close-modal-create-journey')" type="button">Cancel</x-ui.button>
                    <x-ui.button variant="primary" size="sm" type="submit" x-bind:disabled="loading">Create</x-ui.button>
                </div>
            </form>
        </x-ui.modal>

        {{-- Toast --}}
        <div x-show="toast.show" x-transition class="fixed bottom-4 right-4 z-50 px-4 py-3 rounded-lg shadow-lg text-sm font-medium"
             :class="toast.type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'" x-text="toast.message"></div>
    </div>

    <script>
        function journeyManager() {
            return {
                loading: false,
                form: { journey_code: '', title_en: '', title_th: '', journey_group: '', total_minutes_sum: '', gmv_per_person: '', status: 'draft' },
                toast: { show: false, message: '', type: 'success' },

                csrfToken() { return document.querySelector('meta[name="csrf-token"]').content; },
                showToast(message, type = 'success') {
                    this.toast = { show: true, message, type };
                    setTimeout(() => this.toast.show = false, 3000);
                },

                async createJourney() {
                    this.loading = true;
                    try {
                        const res = await fetch('{{ route("admin.journeys.store") }}', {
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': this.csrfToken(), 'Content-Type': 'application/json', 'Accept': 'application/json' },
                            body: JSON.stringify(this.form),
                        });
                        const data = await res.json();
                        if (!res.ok) { this.showToast(Object.values(data.errors || {}).flat().join(', ') || data.message, 'error'); }
                        else { this.showToast(data.message); this.$dispatch('close-modal-create-journey'); setTimeout(() => location.reload(), 800); }
                    } catch (e) { this.showToast('Failed to create journey.', 'error'); }
                    this.loading = false;
                },

                async toggleStatus(code) {
                    try {
                        const res = await fetch('/admin/journeys/' + code + '/toggle', {
                            method: 'PATCH',
                            headers: { 'X-CSRF-TOKEN': this.csrfToken(), 'Accept': 'application/json' },
                        });
                        const data = await res.json();
                        this.showToast(data.message);
                        setTimeout(() => location.reload(), 800);
                    } catch (e) { this.showToast('Failed to toggle.', 'error'); }
                },

                async deleteJourney(code) {
                    if (!confirm('Delete journey ' + code + '?')) return;
                    try {
                        const res = await fetch('/admin/journeys/' + code, {
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
