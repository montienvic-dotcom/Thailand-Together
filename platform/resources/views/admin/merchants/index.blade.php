@extends('layouts.admin')

@section('title', 'Merchants')

@section('content')
    <x-admin.breadcrumb :items="[
        ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
        ['label' => 'Merchants'],
    ]" />

    <div x-data="merchantManager()" x-cloak>
        <div class="mt-4 flex items-center justify-between">
            <h2 class="text-lg font-bold text-gray-900">Merchants ({{ $merchants->total() }})</h2>
            <x-ui.button variant="primary" size="sm" @click="$dispatch('open-modal-create-merchant')">
                <x-icon name="plus" class="w-4 h-4 mr-1" /> Add Merchant
            </x-ui.button>
        </div>

        @if($merchants->isNotEmpty())
            <x-ui.card class="mt-3">
                <x-ui.table>
                    <x-slot:head>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Code</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase hidden sm:table-cell">Tier</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase hidden md:table-cell">Cluster</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </x-slot:head>
                    @foreach($merchants as $merchant)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm font-mono text-gray-700">{{ $merchant->merchant_code }}</td>
                            <td class="px-4 py-3">
                                <div class="text-sm font-medium text-gray-900">{{ $merchant->name_en }}</div>
                                @if($merchant->name_th)
                                    <div class="text-xs text-gray-400">{{ $merchant->name_th }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3 hidden sm:table-cell">
                                <x-ui.badge :color="match($merchant->default_tier_code ?? '') { 'platinum' => 'purple', 'gold' => 'orange', 'silver' => 'blue', default => 'gray' }" size="sm">
                                    {{ ucfirst($merchant->default_tier_code ?? 'none') }}
                                </x-ui.badge>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500 hidden md:table-cell">{{ $merchant->cluster_name ?? '-' }}</td>
                            <td class="px-4 py-3">
                                <x-ui.badge :color="match($merchant->status ?? '') { 'active' => 'green', 'pending' => 'orange', 'inactive' => 'red', default => 'gray' }" size="sm">
                                    {{ ucfirst($merchant->status ?? 'pending') }}
                                </x-ui.badge>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <button @click="toggleStatus('{{ $merchant->merchant_code }}')" class="text-gray-400 hover:text-orange-500 p-1" title="Toggle status">
                                    <x-icon name="arrow-path" class="w-4 h-4 inline" />
                                </button>
                                <button @click="deleteMerchant('{{ $merchant->merchant_code }}')" class="text-gray-400 hover:text-red-500 p-1" title="Delete">
                                    <x-icon name="trash" class="w-4 h-4 inline" />
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </x-ui.table>
            </x-ui.card>

            @if($merchants->hasPages())
                <div class="mt-4">{{ $merchants->links() }}</div>
            @endif
        @else
            <x-ui.card class="mt-3">
                <x-ui.empty-state title="No merchants" description="Add merchants to start managing your directory." icon="building-storefront" />
            </x-ui.card>
        @endif

        {{-- Create Merchant Modal --}}
        <x-ui.modal name="create-merchant" maxWidth="xl">
            <form @submit.prevent="createMerchant()">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900">Add Merchant</h3>
                </div>
                <div class="px-6 py-4 space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Merchant Code *</label>
                            <input type="text" x-model="form.merchant_code" required placeholder="PTY-001" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Cluster *</label>
                            <select x-model="form.cluster_id" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm">
                                <option value="">Select cluster</option>
                                @foreach($clusters as $cluster)
                                    <option value="{{ $cluster->id }}">{{ $cluster->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Name (EN) *</label>
                        <input type="text" x-model="form.name_en" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Name (TH)</label>
                        <input type="text" x-model="form.name_th" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm" />
                    </div>
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tier</label>
                            <select x-model="form.default_tier_code" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm">
                                <option value="">None</option>
                                <option value="silver">Silver</option>
                                <option value="gold">Gold</option>
                                <option value="platinum">Platinum</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select x-model="form.status" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm">
                                <option value="pending">Pending</option>
                                <option value="active">Active</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                            <input type="text" x-model="form.phone" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm" />
                        </div>
                    </div>
                </div>
                <div class="px-6 py-3 bg-gray-50 border-t border-gray-100 flex justify-end gap-2">
                    <x-ui.button variant="outline" size="sm" @click="$dispatch('close-modal-create-merchant')" type="button">Cancel</x-ui.button>
                    <x-ui.button variant="primary" size="sm" type="submit" x-bind:disabled="loading">Create</x-ui.button>
                </div>
            </form>
        </x-ui.modal>

        {{-- Toast --}}
        <div x-show="toast.show" x-transition class="fixed bottom-4 right-4 z-50 px-4 py-3 rounded-lg shadow-lg text-sm font-medium"
             :class="toast.type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'" x-text="toast.message"></div>
    </div>

    <script>
        function merchantManager() {
            return {
                loading: false,
                form: { merchant_code: '', name_en: '', name_th: '', cluster_id: '', default_tier_code: '', status: 'pending', phone: '' },
                toast: { show: false, message: '', type: 'success' },

                csrfToken() { return document.querySelector('meta[name="csrf-token"]').content; },
                showToast(message, type = 'success') {
                    this.toast = { show: true, message, type };
                    setTimeout(() => this.toast.show = false, 3000);
                },

                async createMerchant() {
                    this.loading = true;
                    try {
                        const res = await fetch('{{ route("admin.merchants.store") }}', {
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': this.csrfToken(), 'Content-Type': 'application/json', 'Accept': 'application/json' },
                            body: JSON.stringify(this.form),
                        });
                        const data = await res.json();
                        if (!res.ok) { this.showToast(Object.values(data.errors || {}).flat().join(', ') || data.message, 'error'); }
                        else { this.showToast(data.message); this.$dispatch('close-modal-create-merchant'); setTimeout(() => location.reload(), 800); }
                    } catch (e) { this.showToast('Failed to create merchant.', 'error'); }
                    this.loading = false;
                },

                async toggleStatus(code) {
                    try {
                        const res = await fetch('/admin/merchants/' + code + '/toggle', {
                            method: 'PATCH',
                            headers: { 'X-CSRF-TOKEN': this.csrfToken(), 'Accept': 'application/json' },
                        });
                        const data = await res.json();
                        this.showToast(data.message);
                        setTimeout(() => location.reload(), 800);
                    } catch (e) { this.showToast('Failed to toggle.', 'error'); }
                },

                async deleteMerchant(code) {
                    if (!confirm('Delete merchant ' + code + '?')) return;
                    try {
                        const res = await fetch('/admin/merchants/' + code, {
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
