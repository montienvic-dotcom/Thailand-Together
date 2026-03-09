<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Seeds role_permission pivot table — maps which permissions each role has.
 *
 * Permission matrix:
 * - Global Admin: ALL permissions
 * - Country Admin: all except patches
 * - Cluster Admin: dashboard, users.view, applications, campaigns, analytics, menu, rewards
 * - App Admin: dashboard, users.view, applications, analytics
 * - Operator: dashboard, users.view, analytics
 * - Merchant: dashboard
 * - Tourist: (none — access via group_app_access)
 */
class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $roles = DB::table('roles')->pluck('id', 'slug');
        $permissions = DB::table('permissions')->pluck('id', 'slug');

        if ($roles->isEmpty() || $permissions->isEmpty()) {
            $this->command->warn('No roles or permissions found. Run PlatformSeeder first.');
            return;
        }

        $matrix = [
            'global-admin' => [
                'dashboard.view', 'users.manage', 'users.view',
                'roles.manage', 'permissions.manage', 'applications.manage',
                'clusters.manage', 'countries.manage', 'campaigns.manage',
                'analytics.view', 'api.manage', 'menu.manage',
                'rewards.manage', 'patches.apply',
            ],
            'country-admin' => [
                'dashboard.view', 'users.manage', 'users.view',
                'roles.manage', 'permissions.manage', 'applications.manage',
                'clusters.manage', 'campaigns.manage',
                'analytics.view', 'api.manage', 'menu.manage',
                'rewards.manage',
            ],
            'cluster-admin' => [
                'dashboard.view', 'users.manage', 'users.view',
                'applications.manage', 'campaigns.manage',
                'analytics.view', 'menu.manage', 'rewards.manage',
            ],
            'app-admin' => [
                'dashboard.view', 'users.view',
                'applications.manage', 'analytics.view',
            ],
            'operator' => [
                'dashboard.view', 'users.view', 'analytics.view',
            ],
            'merchant' => [
                'dashboard.view',
            ],
            'tourist' => [],
        ];

        foreach ($matrix as $roleSlug => $permSlugs) {
            $roleId = $roles[$roleSlug] ?? null;
            if (!$roleId) continue;

            foreach ($permSlugs as $permSlug) {
                $permId = $permissions[$permSlug] ?? null;
                if (!$permId) continue;

                DB::table('role_permission')->updateOrInsert(
                    ['role_id' => $roleId, 'permission_id' => $permId]
                );
            }
        }

        $this->command->info('Role-permission matrix seeded');
    }
}
