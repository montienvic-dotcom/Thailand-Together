<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Seeds the platform with initial data:
 * - Countries (Thailand, Vietnam)
 * - Clusters (Pattaya as Phase 1)
 * - System roles
 * - Default groups
 * - Default permissions
 * - Sample applications & modules
 */
class PlatformSeeder extends Seeder
{
    public function run(): void
    {
        // ── Countries ──
        $thailandId = DB::table('countries')->insertGetId([
            'name' => 'Thailand',
            'code' => 'THA',
            'code_alpha2' => 'TH',
            'currency_code' => 'THB',
            'timezone' => 'Asia/Bangkok',
            'default_locale' => 'th',
            'supported_locales' => json_encode(['th', 'en', 'zh', 'ja', 'ko', 'ru']),
            'is_active' => true,
            'sort_order' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $vietnamId = DB::table('countries')->insertGetId([
            'name' => 'Vietnam',
            'code' => 'VNM',
            'code_alpha2' => 'VN',
            'currency_code' => 'VND',
            'timezone' => 'Asia/Ho_Chi_Minh',
            'default_locale' => 'vi',
            'supported_locales' => json_encode(['vi', 'en', 'zh', 'ja', 'ko']),
            'is_active' => false, // Not launched yet
            'sort_order' => 2,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // ── Clusters ──
        $pattayaId = DB::table('clusters')->insertGetId([
            'country_id' => $thailandId,
            'name' => 'Pattaya',
            'slug' => 'pattaya',
            'code' => 'PTY',
            'description' => 'Phase 1 - Pattaya tourism cluster',
            'timezone' => 'Asia/Bangkok',
            'default_locale' => 'th',
            'is_active' => true,
            'launch_date' => '2026-03-01',
            'sort_order' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('clusters')->insert([
            'country_id' => $thailandId,
            'name' => 'Chiang Mai',
            'slug' => 'chiangmai',
            'code' => 'CNX',
            'description' => 'Future - Chiang Mai tourism cluster',
            'is_active' => false,
            'sort_order' => 2,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('clusters')->insert([
            'country_id' => $vietnamId,
            'name' => 'Danang',
            'slug' => 'danang',
            'code' => 'DAD',
            'description' => 'Future - Danang tourism cluster',
            'is_active' => false,
            'sort_order' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // ── System Roles ──
        $roles = [
            ['name' => 'Global Admin', 'slug' => 'global-admin', 'level' => 'global', 'is_system' => true, 'description' => 'Full access to all countries and clusters'],
            ['name' => 'Country Admin', 'slug' => 'country-admin', 'level' => 'country', 'is_system' => true, 'description' => 'Full access within a specific country'],
            ['name' => 'Cluster Admin', 'slug' => 'cluster-admin', 'level' => 'cluster', 'is_system' => true, 'description' => 'Full access within a specific cluster'],
            ['name' => 'App Admin', 'slug' => 'app-admin', 'level' => 'app', 'is_system' => true, 'description' => 'Admin of a specific application'],
            ['name' => 'Operator', 'slug' => 'operator', 'level' => 'cluster', 'is_system' => false, 'description' => 'Cluster operator with limited admin access'],
            ['name' => 'Merchant', 'slug' => 'merchant', 'level' => 'cluster', 'is_system' => false, 'description' => 'Business owner/merchant'],
            ['name' => 'Tourist', 'slug' => 'tourist', 'level' => 'cluster', 'is_system' => false, 'description' => 'Tourist/end user'],
        ];

        foreach ($roles as $role) {
            DB::table('roles')->insert(array_merge($role, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // ── Default Groups ──
        $groups = [
            ['name' => 'Operators', 'slug' => 'operators', 'scope' => 'global', 'description' => 'System operators and staff'],
            ['name' => 'Merchants', 'slug' => 'merchants', 'scope' => 'cluster', 'cluster_id' => $pattayaId, 'description' => 'Pattaya merchants and business owners'],
            ['name' => 'Tourists', 'slug' => 'tourists', 'scope' => 'global', 'description' => 'All registered tourists'],
            ['name' => 'VIP Members', 'slug' => 'vip-members', 'scope' => 'global', 'description' => 'VIP loyalty program members'],
        ];

        foreach ($groups as $group) {
            DB::table('groups')->insert(array_merge([
                'country_id' => null,
                'cluster_id' => null,
                'is_active' => true,
                'sort_order' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ], $group));
        }

        // ── Default Permissions ──
        $permissions = [
            ['name' => 'View Dashboard', 'slug' => 'dashboard.view', 'category' => 'dashboard'],
            ['name' => 'Manage Users', 'slug' => 'users.manage', 'category' => 'users'],
            ['name' => 'View Users', 'slug' => 'users.view', 'category' => 'users'],
            ['name' => 'Manage Roles', 'slug' => 'roles.manage', 'category' => 'roles'],
            ['name' => 'Manage Permissions', 'slug' => 'permissions.manage', 'category' => 'permissions'],
            ['name' => 'Manage Applications', 'slug' => 'applications.manage', 'category' => 'applications'],
            ['name' => 'Manage Clusters', 'slug' => 'clusters.manage', 'category' => 'clusters'],
            ['name' => 'Manage Countries', 'slug' => 'countries.manage', 'category' => 'countries'],
            ['name' => 'Manage Campaigns', 'slug' => 'campaigns.manage', 'category' => 'campaigns'],
            ['name' => 'View Analytics', 'slug' => 'analytics.view', 'category' => 'analytics'],
            ['name' => 'Manage API Integrations', 'slug' => 'api.manage', 'category' => 'api'],
            ['name' => 'Manage Menu', 'slug' => 'menu.manage', 'category' => 'menu'],
            ['name' => 'Manage Rewards', 'slug' => 'rewards.manage', 'category' => 'rewards'],
            ['name' => 'Apply Patches', 'slug' => 'patches.apply', 'category' => 'patches'],
        ];

        foreach ($permissions as $perm) {
            DB::table('permissions')->insert(array_merge($perm, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // ── Sample Applications ──
        $apps = [
            ['name' => 'App Together', 'slug' => 'app-together', 'code' => 'APP_TOGETHER', 'type' => 'mobile', 'source' => 'internal', 'icon' => 'compass', 'color' => '#FF6B35', 'sort_order' => 1],
            ['name' => 'Hotel Management', 'slug' => 'hotel-management', 'code' => 'HOTEL_MGMT', 'type' => 'web', 'source' => 'codecanyon', 'icon' => 'building', 'color' => '#004E89', 'sort_order' => 2],
            ['name' => 'Tour Booking', 'slug' => 'tour-booking', 'code' => 'TOUR_BOOKING', 'type' => 'hybrid', 'source' => 'codecanyon', 'icon' => 'map', 'color' => '#1A936F', 'sort_order' => 3],
            ['name' => 'Marketplace', 'slug' => 'marketplace', 'code' => 'MARKETPLACE', 'type' => 'hybrid', 'source' => 'codecanyon', 'icon' => 'shopping-bag', 'color' => '#C14953', 'sort_order' => 4],
            ['name' => 'Rewards Center', 'slug' => 'rewards-center', 'code' => 'REWARDS', 'type' => 'web', 'source' => 'internal', 'icon' => 'gift', 'color' => '#F4A261', 'sort_order' => 5],
            ['name' => 'HelpDesk', 'slug' => 'helpdesk', 'code' => 'HELPDESK', 'type' => 'web', 'source' => 'codecanyon', 'icon' => 'headphones', 'color' => '#6C757D', 'sort_order' => 6],
        ];

        foreach ($apps as $app) {
            $appId = DB::table('applications')->insertGetId(array_merge($app, [
                'is_active' => true,
                'show_in_menu' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]));

            // Enable app in Pattaya cluster
            DB::table('cluster_application')->insert([
                'cluster_id' => $pattayaId,
                'application_id' => $appId,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // ── Sample Modules for App Together ──
        $appTogetherId = DB::table('applications')->where('code', 'APP_TOGETHER')->value('id');
        $modules = [
            ['name' => 'Explore', 'slug' => 'explore', 'code' => 'EXPLORE', 'sort_order' => 1],
            ['name' => 'Booking', 'slug' => 'booking', 'code' => 'BOOKING', 'sort_order' => 2],
            ['name' => 'Map & Navigation', 'slug' => 'map', 'code' => 'MAP', 'sort_order' => 3],
            ['name' => 'Chat & Support', 'slug' => 'chat', 'code' => 'CHAT', 'sort_order' => 4],
            ['name' => 'My Rewards', 'slug' => 'rewards', 'code' => 'MY_REWARDS', 'sort_order' => 5],
            ['name' => 'My Profile', 'slug' => 'profile', 'code' => 'PROFILE', 'sort_order' => 6],
            ['name' => 'Deals & Promotions', 'slug' => 'deals', 'code' => 'DEALS', 'sort_order' => 7],
            ['name' => 'Reviews & Ratings', 'slug' => 'reviews', 'code' => 'REVIEWS', 'sort_order' => 8],
        ];

        foreach ($modules as $module) {
            DB::table('modules')->insert(array_merge($module, [
                'application_id' => $appTogetherId,
                'is_active' => true,
                'is_premium' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // ── Default Menu Items ──
        $menuItems = [
            ['label' => 'Home', 'icon' => 'home', 'url' => '/', 'scope' => 'global', 'visibility' => 'all', 'sort_order' => 1],
            ['label' => 'Explore', 'icon' => 'compass', 'url' => '/explore', 'scope' => 'global', 'visibility' => 'all', 'sort_order' => 2, 'application_id' => $appTogetherId],
            ['label' => 'Bookings', 'icon' => 'calendar', 'url' => '/bookings', 'scope' => 'global', 'visibility' => 'authenticated', 'sort_order' => 3],
            ['label' => 'Rewards', 'icon' => 'gift', 'url' => '/rewards', 'scope' => 'global', 'visibility' => 'authenticated', 'sort_order' => 4],
            ['label' => 'Admin', 'icon' => 'settings', 'url' => '/admin', 'scope' => 'global', 'visibility' => 'admin', 'sort_order' => 99],
        ];

        foreach ($menuItems as $item) {
            DB::table('menu_items')->insert(array_merge([
                'route_name' => null,
                'application_id' => null,
                'parent_id' => null,
                'country_id' => null,
                'cluster_id' => null,
                'target' => '_self',
                'required_permissions' => null,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ], $item));
        }

        // ── Default Reward Exchange Rates ──
        // (Will be populated when clusters go live)

        // ── Default API Providers ──
        $providers = [
            ['name' => 'Payment Gateway', 'slug' => 'payment-gateway', 'category' => 'payment', 'description' => 'Main payment processing service'],
            ['name' => 'SMS Provider', 'slug' => 'sms-provider', 'category' => 'sms', 'description' => 'SMS messaging service'],
            ['name' => 'AI Agent Service', 'slug' => 'ai-agent', 'category' => 'ai_agent', 'description' => 'AI chatbot, translation, TTS, call center', 'adapter_class' => \App\Services\ApiGateway\Adapters\AiAgentAdapter::class],
            ['name' => 'Cloud Point API', 'slug' => 'cloud-point', 'category' => 'cloud_point', 'description' => 'External loyalty point system'],
            ['name' => 'Data Exchange API', 'slug' => 'data-exchange', 'category' => 'data_exchange', 'description' => 'Data import/export/sync'],
            ['name' => 'HelpDesk API', 'slug' => 'helpdesk-api', 'category' => 'helpdesk', 'description' => 'Customer support ticketing'],
            ['name' => 'Translation API', 'slug' => 'translate-api', 'category' => 'translate', 'description' => 'Multi-language translation'],
            ['name' => 'Text-to-Speech API', 'slug' => 'tts-api', 'category' => 'tts', 'description' => 'Voice synthesis service'],
        ];

        foreach ($providers as $provider) {
            DB::table('api_providers')->insert(array_merge([
                'base_url' => null,
                'docs_url' => null,
                'adapter_class' => null,
                'is_active' => true,
                'is_shared' => true,
                'supported_countries' => null,
                'default_config' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ], $provider));
        }
    }
}
