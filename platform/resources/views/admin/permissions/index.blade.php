@extends('layouts.admin')

@section('title', 'Permissions')

@section('content')
    <x-admin.breadcrumb :items="[
        ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
        ['label' => 'Permissions'],
    ]" />

    <div class="mt-4 grid grid-cols-1 sm:grid-cols-3 gap-4">
        <a href="{{ route('admin.permissions.users') }}" class="group bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md hover:border-gray-300 transition-all">
            <div class="flex items-center gap-4">
                <div class="p-3 rounded-lg bg-blue-50 text-blue-600">
                    <x-icon name="users" class="w-6 h-6" />
                </div>
                <div>
                    <h3 class="text-base font-semibold text-gray-900 group-hover:text-(--color-primary)">Users</h3>
                    <p class="text-sm text-gray-500">Manage user access</p>
                </div>
            </div>
        </a>

        <a href="{{ route('admin.permissions.groups') }}" class="group bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md hover:border-gray-300 transition-all">
            <div class="flex items-center gap-4">
                <div class="p-3 rounded-lg bg-green-50 text-green-600">
                    <x-icon name="share-2" class="w-6 h-6" />
                </div>
                <div>
                    <h3 class="text-base font-semibold text-gray-900 group-hover:text-(--color-primary)">Groups</h3>
                    <p class="text-sm text-gray-500">Manage permission groups</p>
                </div>
            </div>
        </a>

        <a href="{{ route('admin.permissions.roles') }}" class="group bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md hover:border-gray-300 transition-all">
            <div class="flex items-center gap-4">
                <div class="p-3 rounded-lg bg-purple-50 text-purple-600">
                    <x-icon name="shield" class="w-6 h-6" />
                </div>
                <div>
                    <h3 class="text-base font-semibold text-gray-900 group-hover:text-(--color-primary)">Roles</h3>
                    <p class="text-sm text-gray-500">Manage system roles</p>
                </div>
            </div>
        </a>
    </div>
@endsection
