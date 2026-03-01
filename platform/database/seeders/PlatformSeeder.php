<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

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
        // Skip if already seeded (safe for repeated deploys)
        if (DB::table('users')->count() > 0) {
            return;
        }

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

        // ── Applications (10 apps — all enabled in Pattaya) ──
        $apps = [
            ['name' => 'App Together', 'slug' => 'app-together', 'code' => 'APP_TOGETHER', 'type' => 'mobile', 'source' => 'internal', 'icon' => 'compass', 'color' => '#FF6B35', 'sort_order' => 1, 'description' => 'All-in-one mobile app for tourists — explore, book, navigate, and earn rewards in one place'],
            ['name' => 'Hotel Management', 'slug' => 'hotel-management', 'code' => 'HOTEL_MGMT', 'type' => 'web', 'source' => 'external', 'icon' => 'building', 'color' => '#004E89', 'sort_order' => 2, 'description' => 'Complete hotel operations — room management, reservations, guest services, and revenue tracking'],
            ['name' => 'Tour Booking', 'slug' => 'tour-booking', 'code' => 'TOUR_BOOKING', 'type' => 'hybrid', 'source' => 'external', 'icon' => 'map', 'color' => '#1A936F', 'sort_order' => 3, 'description' => 'Browse and book tours, activities, and experiences — day trips, island hopping, shows, and more'],
            ['name' => 'Marketplace', 'slug' => 'marketplace', 'code' => 'MARKETPLACE', 'type' => 'hybrid', 'source' => 'external', 'icon' => 'shopping-bag', 'color' => '#C14953', 'sort_order' => 4, 'description' => 'Local shops, souvenirs, food delivery, and services — support local merchants while you travel'],
            ['name' => 'Rewards Center', 'slug' => 'rewards-center', 'code' => 'REWARDS', 'type' => 'web', 'source' => 'internal', 'icon' => 'gift', 'color' => '#F4A261', 'sort_order' => 5, 'description' => 'Earn and redeem points across all services — exclusive deals, tier benefits, and cross-cluster rewards'],
            ['name' => 'HelpDesk', 'slug' => 'helpdesk', 'code' => 'HELPDESK', 'type' => 'web', 'source' => 'external', 'icon' => 'headphones', 'color' => '#6C757D', 'sort_order' => 6, 'description' => '24/7 multilingual support — AI chatbot, live agents, ticket tracking, and emergency assistance'],
            ['name' => 'City Location - Digital Twin', 'slug' => 'city-digital-twin', 'code' => 'CITY_DIGITAL_TWIN', 'type' => 'hybrid', 'source' => 'internal', 'icon' => 'globe', 'color' => '#2EC4B6', 'sort_order' => 7, 'description' => 'Interactive 3D city map with AR navigation, real-time data, and virtual tours of Pattaya'],
            ['name' => 'Social Network', 'slug' => 'social-network', 'code' => 'SOCIAL_NETWORK', 'type' => 'hybrid', 'source' => 'internal', 'icon' => 'users', 'color' => '#E71D36', 'sort_order' => 8, 'description' => 'Connect with fellow travelers — share experiences, join groups, find events, and make friends'],
            ['name' => 'Referral & Partner Hub', 'slug' => 'partner-hub', 'code' => 'PARTNER_HUB', 'type' => 'web', 'source' => 'internal', 'icon' => 'share-2', 'color' => '#8338EC', 'sort_order' => 9, 'description' => 'Earn commissions by referring friends and merchants — influencer tools, tracking, and payouts'],
            ['name' => 'UGC & AI Content Hub', 'slug' => 'ugc-ai-hub', 'code' => 'UGC_AI_HUB', 'type' => 'hybrid', 'source' => 'internal', 'icon' => 'edit-3', 'color' => '#3A86FF', 'sort_order' => 10, 'description' => 'AI-powered content creation — auto-translate, generate itineraries, and curate user reviews'],
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

        // ── Modules per Application ──
        $appModules = [
            'APP_TOGETHER' => [
                ['name' => 'Explore', 'slug' => 'explore', 'code' => 'EXPLORE', 'sort_order' => 1],
                ['name' => 'Booking', 'slug' => 'booking', 'code' => 'BOOKING', 'sort_order' => 2],
                ['name' => 'Map & Navigation', 'slug' => 'map', 'code' => 'MAP', 'sort_order' => 3],
                ['name' => 'Chat & Support', 'slug' => 'chat', 'code' => 'CHAT', 'sort_order' => 4],
                ['name' => 'My Rewards', 'slug' => 'rewards', 'code' => 'MY_REWARDS', 'sort_order' => 5],
                ['name' => 'My Profile', 'slug' => 'profile', 'code' => 'PROFILE', 'sort_order' => 6],
                ['name' => 'Deals & Promotions', 'slug' => 'deals', 'code' => 'DEALS', 'sort_order' => 7],
                ['name' => 'Reviews & Ratings', 'slug' => 'reviews', 'code' => 'REVIEWS', 'sort_order' => 8],
            ],
            'CITY_DIGITAL_TWIN' => [
                ['name' => '3D City Map', 'slug' => 'city-map-3d', 'code' => 'CITY_MAP_3D', 'description' => 'Digital Twin 3D city map', 'sort_order' => 1],
                ['name' => 'Points of Interest', 'slug' => 'poi', 'code' => 'POI', 'description' => 'Landmarks, shops, restaurants, attractions', 'sort_order' => 2],
                ['name' => 'AR Navigation', 'slug' => 'ar-nav', 'code' => 'AR_NAV', 'description' => 'Augmented reality walking navigation', 'sort_order' => 3],
                ['name' => 'Virtual Tour 360', 'slug' => 'virtual-tour', 'code' => 'VIRTUAL_TOUR', 'description' => 'Virtual 360-degree city exploration', 'sort_order' => 4],
                ['name' => 'Real-time City Data', 'slug' => 'city-realtime', 'code' => 'CITY_REALTIME', 'description' => 'Live city data: weather, traffic, crowd density', 'sort_order' => 5],
                ['name' => 'Smart Directory', 'slug' => 'smart-directory', 'code' => 'SMART_DIRECTORY', 'description' => 'Location and hours-based shop/service search', 'sort_order' => 6],
                ['name' => 'Route Planner', 'slug' => 'route-planner', 'code' => 'ROUTE_PLANNER', 'description' => 'Optimized sightseeing, dining, and shopping routes', 'sort_order' => 7],
            ],
            'SOCIAL_NETWORK' => [
                ['name' => 'Feed & Timeline', 'slug' => 'feed', 'code' => 'FEED', 'description' => 'Share travel experiences', 'sort_order' => 1],
                ['name' => 'Travel Stories', 'slug' => 'stories', 'code' => 'STORIES', 'description' => 'Travel stories with photos and videos', 'sort_order' => 2],
                ['name' => 'Friends & Followers', 'slug' => 'friends', 'code' => 'FRIENDS', 'description' => 'Connect with other travelers', 'sort_order' => 3],
                ['name' => 'Messaging', 'slug' => 'messaging', 'code' => 'MESSAGING', 'description' => 'Private and group chat', 'sort_order' => 4],
                ['name' => 'Groups & Communities', 'slug' => 'groups', 'code' => 'GROUPS', 'description' => 'Interest and destination-based groups', 'sort_order' => 5],
                ['name' => 'Events', 'slug' => 'events', 'code' => 'EVENTS', 'description' => 'Local events and traveler meetups', 'sort_order' => 6],
                ['name' => 'Check-in & Places', 'slug' => 'checkin', 'code' => 'CHECKIN', 'description' => 'Location check-ins, recommendations, point collection', 'sort_order' => 7],
            ],
            'PARTNER_HUB' => [
                ['name' => 'Referrer Program', 'slug' => 'referrer', 'code' => 'REFERRER', 'description' => 'Referral codes, tracking links, multi-tier rewards', 'sort_order' => 1],
                ['name' => 'Influencer Management', 'slug' => 'influencer', 'code' => 'INFLUENCER', 'description' => 'KOL/influencer profiles, campaigns, performance tracking', 'sort_order' => 2],
                ['name' => 'Content Builder', 'slug' => 'content-builder', 'code' => 'CONTENT_BUILDER', 'description' => 'Drag-and-drop content editor with templates', 'sort_order' => 3],
                ['name' => 'Merchant Inviter', 'slug' => 'merchant-inviter', 'code' => 'MERCHANT_INVITER', 'description' => 'Merchant invitation tracking and onboarding', 'sort_order' => 4],
                ['name' => 'Commission & Payout', 'slug' => 'commission', 'code' => 'COMMISSION', 'description' => 'Commission rules, payout schedules, wallet management', 'sort_order' => 5],
                ['name' => 'Tier & Incentives', 'slug' => 'tier-incentives', 'code' => 'TIER_INCENTIVES', 'description' => 'Partner tiers (Bronze-Platinum), auto-upgrade, leaderboard', 'sort_order' => 6],
                ['name' => 'Performance Analytics', 'slug' => 'partner-analytics', 'code' => 'PARTNER_ANALYTICS', 'description' => 'Conversion funnels, ROI analysis, channel heatmaps', 'sort_order' => 7],
            ],
            'UGC_AI_HUB' => [
                ['name' => 'UGC Feed', 'slug' => 'ugc-feed', 'code' => 'UGC_FEED', 'description' => 'User-generated reviews, photos, videos, and tips', 'sort_order' => 1],
                ['name' => 'AI Content Generator', 'slug' => 'ai-content-gen', 'code' => 'AI_CONTENT_GEN', 'description' => 'AI-powered descriptions, itineraries, and social posts', 'sort_order' => 2],
                ['name' => 'AI Resource Database', 'slug' => 'ai-resource-db', 'code' => 'AI_RESOURCE_DB', 'description' => 'AI-enriched venue database with auto-categorization', 'sort_order' => 3],
                ['name' => 'Content Moderation', 'slug' => 'content-mod', 'code' => 'CONTENT_MOD', 'description' => 'AI spam detection, sentiment analysis, review queue', 'sort_order' => 4],
                ['name' => 'Media Library', 'slug' => 'media-lib', 'code' => 'MEDIA_LIB', 'description' => 'Asset management with AI auto-tagging and CDN', 'sort_order' => 5],
                ['name' => 'Multi-language Engine', 'slug' => 'multi-lang', 'code' => 'MULTI_LANG', 'description' => 'AI auto-translation (TH/EN/ZH/JA/KO/RU) with TTS', 'sort_order' => 6],
                ['name' => 'Content Curation', 'slug' => 'content-curation', 'code' => 'CONTENT_CURATION', 'description' => 'AI quality scoring, editorial picks, auto-publishing', 'sort_order' => 7],
            ],
            'HOTEL_MGMT' => [
                ['name' => 'Room Management', 'slug' => 'room-mgmt', 'code' => 'ROOM_MGMT', 'description' => 'Room types, inventory, pricing, and availability calendar', 'sort_order' => 1],
                ['name' => 'Reservations', 'slug' => 'reservations', 'code' => 'RESERVATIONS', 'description' => 'Booking management, check-in/out, and guest records', 'sort_order' => 2],
                ['name' => 'Guest Services', 'slug' => 'guest-services', 'code' => 'GUEST_SERVICES', 'description' => 'Room service, housekeeping requests, and concierge', 'sort_order' => 3],
                ['name' => 'Channel Manager', 'slug' => 'channel-mgr', 'code' => 'CHANNEL_MGR', 'description' => 'Sync availability across OTAs (Agoda, Booking.com, etc.)', 'sort_order' => 4],
                ['name' => 'Revenue & Reports', 'slug' => 'revenue-reports', 'code' => 'REVENUE_REPORTS', 'description' => 'Occupancy rates, revenue analytics, and financial reports', 'sort_order' => 5],
                ['name' => 'Review Management', 'slug' => 'review-mgmt', 'code' => 'REVIEW_MGMT', 'description' => 'Monitor and respond to guest reviews across platforms', 'sort_order' => 6],
            ],
            'TOUR_BOOKING' => [
                ['name' => 'Tour Catalog', 'slug' => 'tour-catalog', 'code' => 'TOUR_CATALOG', 'description' => 'Browse tours, activities, day trips, and experiences', 'sort_order' => 1],
                ['name' => 'Booking Engine', 'slug' => 'booking-engine', 'code' => 'BOOKING_ENGINE', 'description' => 'Real-time availability, instant booking, and payment', 'sort_order' => 2],
                ['name' => 'Tour Operator Panel', 'slug' => 'operator-panel', 'code' => 'OPERATOR_PANEL', 'description' => 'Tour operators manage listings, schedules, and guides', 'sort_order' => 3],
                ['name' => 'Itinerary Builder', 'slug' => 'itinerary', 'code' => 'ITINERARY', 'description' => 'Create custom multi-day itineraries with AI suggestions', 'sort_order' => 4],
                ['name' => 'Transport & Transfers', 'slug' => 'transport', 'code' => 'TRANSPORT', 'description' => 'Airport transfers, car rentals, and local transport booking', 'sort_order' => 5],
                ['name' => 'Reviews & Ratings', 'slug' => 'tour-reviews', 'code' => 'TOUR_REVIEWS', 'description' => 'Verified reviews and ratings from past participants', 'sort_order' => 6],
            ],
            'MARKETPLACE' => [
                ['name' => 'Shop Directory', 'slug' => 'shop-directory', 'code' => 'SHOP_DIRECTORY', 'description' => 'Browse local shops, restaurants, and service providers', 'sort_order' => 1],
                ['name' => 'Product Listings', 'slug' => 'product-listings', 'code' => 'PRODUCT_LISTINGS', 'description' => 'Search and filter products, souvenirs, and local goods', 'sort_order' => 2],
                ['name' => 'Order & Delivery', 'slug' => 'order-delivery', 'code' => 'ORDER_DELIVERY', 'description' => 'Place orders with hotel delivery or pickup options', 'sort_order' => 3],
                ['name' => 'Merchant Dashboard', 'slug' => 'merchant-dash', 'code' => 'MERCHANT_DASH', 'description' => 'Merchants manage products, orders, and promotions', 'sort_order' => 4],
                ['name' => 'Deals & Coupons', 'slug' => 'deals-coupons', 'code' => 'DEALS_COUPONS', 'description' => 'Tourist-exclusive deals, flash sales, and discount codes', 'sort_order' => 5],
                ['name' => 'Food Delivery', 'slug' => 'food-delivery', 'code' => 'FOOD_DELIVERY', 'description' => 'Order from local restaurants with real-time tracking', 'sort_order' => 6],
            ],
            'REWARDS' => [
                ['name' => 'Points Dashboard', 'slug' => 'points-dashboard', 'code' => 'POINTS_DASH', 'description' => 'View balance, earning history, and point expiry dates', 'sort_order' => 1],
                ['name' => 'Earn Points', 'slug' => 'earn-points', 'code' => 'EARN_POINTS', 'description' => 'Earn from bookings, check-ins, reviews, and referrals', 'sort_order' => 2],
                ['name' => 'Redeem Rewards', 'slug' => 'redeem-rewards', 'code' => 'REDEEM_REWARDS', 'description' => 'Redeem for discounts, vouchers, upgrades, and experiences', 'sort_order' => 3],
                ['name' => 'Tier & Benefits', 'slug' => 'tier-benefits', 'code' => 'TIER_BENEFITS', 'description' => 'Bronze to Platinum tiers with exclusive perks at each level', 'sort_order' => 4],
                ['name' => 'Special Campaigns', 'slug' => 'campaigns', 'code' => 'CAMPAIGNS', 'description' => 'Limited-time bonus point events and seasonal promotions', 'sort_order' => 5],
                ['name' => 'Transfer & Exchange', 'slug' => 'point-transfer', 'code' => 'POINT_TRANSFER', 'description' => 'Transfer points to friends or exchange across clusters', 'sort_order' => 6],
            ],
            'HELPDESK' => [
                ['name' => 'AI Chatbot', 'slug' => 'ai-chatbot', 'code' => 'AI_CHATBOT', 'description' => 'Instant AI-powered answers in 6 languages, 24/7', 'sort_order' => 1],
                ['name' => 'Live Support', 'slug' => 'live-support', 'code' => 'LIVE_SUPPORT', 'description' => 'Connect with multilingual human agents for complex issues', 'sort_order' => 2],
                ['name' => 'Ticket System', 'slug' => 'ticket-system', 'code' => 'TICKET_SYSTEM', 'description' => 'Submit and track support tickets with SLA guarantees', 'sort_order' => 3],
                ['name' => 'Emergency Assist', 'slug' => 'emergency', 'code' => 'EMERGENCY', 'description' => 'One-tap emergency contacts: police, hospital, embassy', 'sort_order' => 4],
                ['name' => 'FAQ & Guides', 'slug' => 'faq-guides', 'code' => 'FAQ_GUIDES', 'description' => 'Travel guides, visa info, local tips, and how-to articles', 'sort_order' => 5],
                ['name' => 'Feedback & Surveys', 'slug' => 'feedback', 'code' => 'FEEDBACK', 'description' => 'Rate your experience and help us improve our services', 'sort_order' => 6],
            ],
        ];

        foreach ($appModules as $appCode => $modules) {
            $applicationId = DB::table('applications')->where('code', $appCode)->value('id');
            foreach ($modules as $module) {
                DB::table('modules')->insert(array_merge([
                    'application_id' => $applicationId,
                    'description' => null,
                    'is_active' => true,
                    'is_premium' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ], $module));
            }
        }

        // ── Default Menu Items ──
        $appTogetherId = DB::table('applications')->where('code', 'APP_TOGETHER')->value('id');
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

        // ── Default Admin User ──
        $adminId = DB::table('users')->insertGetId([
            'name' => 'Admin',
            'email' => 'admin@thailandtogether.com',
            'password' => Hash::make('password'),
            'status' => 'active',
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Assign Global Admin role
        $globalAdminRoleId = DB::table('roles')->where('slug', 'global-admin')->value('id');
        DB::table('role_user')->insert([
            'role_id' => $globalAdminRoleId,
            'user_id' => $adminId,
            'country_id' => null,
            'cluster_id' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // ── Demo Users ──
        $operatorId = DB::table('users')->insertGetId([
            'name' => 'Pattaya Operator',
            'email' => 'operator@thailandtogether.com',
            'password' => Hash::make('password'),
            'status' => 'active',
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $clusterAdminRoleId = DB::table('roles')->where('slug', 'cluster-admin')->value('id');
        DB::table('role_user')->insert([
            'role_id' => $clusterAdminRoleId,
            'user_id' => $operatorId,
            'country_id' => null,
            'cluster_id' => $pattayaId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $touristId = DB::table('users')->insertGetId([
            'name' => 'Demo Tourist',
            'email' => 'tourist@thailandtogether.com',
            'password' => Hash::make('password'),
            'status' => 'active',
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Add tourist to Tourists group
        $touristGroupId = DB::table('groups')->where('slug', 'tourists')->value('id');
        DB::table('group_user')->insert([
            'group_id' => $touristGroupId,
            'user_id' => $touristId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Give tourist group access to all apps in Pattaya
        $allAppIds = DB::table('applications')->where('is_active', true)->pluck('id');
        foreach ($allAppIds as $appIdForAccess) {
            DB::table('group_app_access')->insert([
                'group_id' => $touristGroupId,
                'application_id' => $appIdForAccess,
                'cluster_id' => $pattayaId,
                'has_access' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
