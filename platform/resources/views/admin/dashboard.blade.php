@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
    {{-- Stat cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <x-ui.stat-card label="Applications" :value="$applications ?? 0" icon="cube" color="orange" />
        <x-ui.stat-card label="Active Users" :value="$total_users ?? 0" icon="users" color="blue" />
        <x-ui.stat-card label="Clusters" :value="$clusters ?? 0" icon="map" color="green" />
        <x-ui.stat-card label="Countries" :value="$countries ?? 0" icon="globe" color="purple" />
    </div>

    {{-- Quick links --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <x-ui.card title="Quick Actions">
            <div class="space-y-2">
                <a href="{{ route('admin.applications') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-gray-50 text-sm text-gray-700 transition-colors">
                    <x-icon name="cube" class="w-5 h-5 text-gray-400" />
                    <span>Manage Applications</span>
                    <x-icon name="chevron-right" class="w-4 h-4 text-gray-300 ml-auto" />
                </a>
                <a href="{{ route('admin.permissions') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-gray-50 text-sm text-gray-700 transition-colors">
                    <x-icon name="shield" class="w-5 h-5 text-gray-400" />
                    <span>Permission Management</span>
                    <x-icon name="chevron-right" class="w-4 h-4 text-gray-300 ml-auto" />
                </a>
                <a href="{{ route('admin.permissions.users') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-gray-50 text-sm text-gray-700 transition-colors">
                    <x-icon name="users" class="w-5 h-5 text-gray-400" />
                    <span>User Management</span>
                    <x-icon name="chevron-right" class="w-4 h-4 text-gray-300 ml-auto" />
                </a>
            </div>
        </x-ui.card>

        <x-ui.card title="System Info">
            <dl class="space-y-3">
                <div class="flex justify-between text-sm">
                    <dt class="text-gray-500">Admin Level</dt>
                    <dd>
                        <x-ui.badge :color="($admin_level ?? 'none') === 'global' ? 'purple' : (($admin_level ?? 'none') === 'country' ? 'blue' : 'green')">
                            {{ ucfirst($admin_level ?? 'none') }}
                        </x-ui.badge>
                    </dd>
                </div>
                <div class="flex justify-between text-sm">
                    <dt class="text-gray-500">API Providers</dt>
                    <dd class="font-medium text-gray-900">{{ $api_providers ?? 0 }}</dd>
                </div>
                <div class="flex justify-between text-sm">
                    <dt class="text-gray-500">Platform</dt>
                    <dd class="font-medium text-gray-900">Thailand Together v1.0</dd>
                </div>
            </dl>
        </x-ui.card>
    </div>
@endsection
