@extends('layouts.admin')

@section('title', 'API Providers')

@section('content')
    <x-admin.breadcrumb :items="[
        ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
        ['label' => 'API Providers'],
    ]" />

    <div x-data="providerManager()" x-cloak>
        <div class="mt-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <h2 class="text-lg font-bold text-gray-900">API Providers ({{ $providers->count() }})</h2>
            <x-ui.button variant="primary" size="sm" @click="$dispatch('open-modal-create-provider')">
                <x-icon name="plus" class="w-4 h-4 mr-1" /> Add Provider
            </x-ui.button>
        </div>

        {{-- Category filter tabs --}}
        @php
            $categories = $providers->pluck('category')->unique()->sort();
        @endphp
        <div class="mt-3 flex flex-wrap gap-2">
            <button @click="filterCat = ''" class="px-3 py-1.5 rounded-lg text-sm font-medium transition-colors"
                :class="filterCat === '' ? 'bg-orange-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'">All</button>
            @foreach($categories as $cat)
                <button @click="filterCat = '{{ $cat }}'" class="px-3 py-1.5 rounded-lg text-sm font-medium transition-colors"
                    :class="filterCat === '{{ $cat }}' ? 'bg-orange-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'">
                    {{ ucfirst(str_replace('_', ' ', $cat)) }}
                </button>
            @endforeach
        </div>

        {{-- Providers Grid --}}
        <div class="mt-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($providers as $prov)
                <div x-show="filterCat === '' || filterCat === '{{ $prov->category }}'"
                     class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 hover:shadow-md transition-shadow">
                    <div class="flex items-start justify-between">
                        <div class="min-w-0 flex-1">
                            <a href="{{ route('admin.api-providers.show', $prov->id) }}" class="text-sm font-semibold text-gray-900 hover:text-orange-600 block truncate">{{ $prov->name }}</a>
                            <x-ui.badge :color="match($prov->category) { 'payment' => 'green', 'sms' => 'blue', 'ai' => 'purple', default => 'gray' }" size="sm" class="mt-1">
                                {{ ucfirst(str_replace('_', ' ', $prov->category)) }}
                            </x-ui.badge>
                        </div>
                        <x-ui.badge :color="$prov->is_active ? 'green' : 'red'" size="sm">
                            {{ $prov->is_active ? 'Active' : 'Inactive' }}
                        </x-ui.badge>
                    </div>
                    @if($prov->description)
                        <p class="mt-2 text-xs text-gray-500 line-clamp-2">{{ $prov->description }}</p>
                    @endif
                    <div class="mt-3 flex items-center justify-between text-xs text-gray-400">
                        <span>{{ $prov->credentials_count }} credential(s)</span>
                        <a href="{{ route('admin.api-providers.show', $prov->id) }}" class="text-orange-500 hover:text-orange-600 font-medium">
                            Manage &rarr;
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        @if($providers->isEmpty())
            <x-ui.card class="mt-4">
                <x-ui.empty-state title="No API Providers" description="Register your first API provider." icon="cloud" />
            </x-ui.card>
        @endif

        {{-- Create Provider Modal --}}
        <x-ui.modal name="create-provider" maxWidth="xl">
            <form @submit.prevent="createProvider()">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900">Register API Provider</h3>
                </div>
                <div class="px-6 py-4 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Provider Name *</label>
                        <input type="text" x-model="form.name" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Category *</label>
                        <select x-model="form.category" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm">
                            <option value="">Select...</option>
                            <option value="payment">Payment Gateway</option>
                            <option value="sms">SMS Service</option>
                            <option value="ai">AI Service</option>
                            <option value="cloud_point">Cloud Point</option>
                            <option value="data_exchange">Data Exchange</option>
                            <option value="helpdesk">HelpDesk</option>
                            <option value="auth">Authorization</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea x-model="form.description" rows="2" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm"></textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Base URL</label>
                            <input type="url" x-model="form.base_url" placeholder="https://api.provider.com" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Docs URL</label>
                            <input type="url" x-model="form.docs_url" placeholder="https://docs.provider.com" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm" />
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Adapter Class</label>
                        <input type="text" x-model="form.adapter_class" placeholder="App\Services\ApiGateway\Adapters\..." class="w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm font-mono" />
                    </div>
                    <div class="flex items-center gap-2">
                        <input type="checkbox" x-model="form.is_shared" class="rounded border-gray-300 text-orange-500 focus:ring-orange-500" />
                        <label class="text-sm text-gray-700">Shared across all clusters</label>
                    </div>
                </div>
                <div class="px-6 py-3 bg-gray-50 border-t border-gray-100 flex justify-end gap-2">
                    <x-ui.button variant="outline" size="sm" @click="$dispatch('close-modal-create-provider')" type="button">Cancel</x-ui.button>
                    <x-ui.button variant="primary" size="sm" type="submit" x-bind:disabled="loading">
                        <template x-if="loading"><x-icon name="arrow-path" class="w-4 h-4 animate-spin mr-1" /></template>
                        Register Provider
                    </x-ui.button>
                </div>
            </form>
        </x-ui.modal>

        {{-- Toast --}}
        <div x-show="toast.show" x-transition class="fixed bottom-4 right-4 z-50 px-4 py-3 rounded-lg shadow-lg text-sm font-medium"
             :class="toast.type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'" x-text="toast.message"></div>
    </div>

    <script>
        function providerManager() {
            return {
                loading: false,
                filterCat: '',
                form: { name: '', category: '', description: '', base_url: '', docs_url: '', adapter_class: '', is_shared: true },
                toast: { show: false, message: '', type: 'success' },

                csrfToken() { return document.querySelector('meta[name="csrf-token"]').content; },
                showToast(message, type = 'success') {
                    this.toast = { show: true, message, type };
                    setTimeout(() => this.toast.show = false, 3000);
                },

                async createProvider() {
                    this.loading = true;
                    try {
                        const res = await fetch('{{ route("admin.api-providers.store") }}', {
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': this.csrfToken(), 'Content-Type': 'application/json', 'Accept': 'application/json' },
                            body: JSON.stringify(this.form),
                        });
                        const data = await res.json();
                        if (!res.ok) { this.showToast(Object.values(data.errors || {}).flat().join(', '), 'error'); }
                        else { this.showToast(data.message); this.$dispatch('close-modal-create-provider'); setTimeout(() => location.reload(), 800); }
                    } catch (e) { this.showToast('Failed to create provider.', 'error'); }
                    this.loading = false;
                },
            };
        }
    </script>
@endsection
