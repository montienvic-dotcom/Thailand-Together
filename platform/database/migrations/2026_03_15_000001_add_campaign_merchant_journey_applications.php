<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $pattayaId = DB::table('clusters')->where('code', 'PTY')->value('id');
        $maxSort = (int) DB::table('applications')->max('sort_order');

        // ── New Applications ──
        $apps = [
            [
                'name' => 'Campaign Management',
                'slug' => 'campaign-management',
                'code' => 'CAMPAIGN_MGMT',
                'type' => 'web',
                'source' => 'internal',
                'icon' => 'megaphone',
                'color' => '#F59E0B',
                'sort_order' => $maxSort + 1,
                'description' => 'Create and manage marketing campaigns, promotions, coupon systems, and cross-cluster campaigns with analytics and A/B testing',
            ],
            [
                'name' => 'Journey Builder',
                'slug' => 'journey-builder',
                'code' => 'JOURNEY_BUILDER',
                'type' => 'hybrid',
                'source' => 'internal',
                'icon' => 'map-pin',
                'color' => '#10B981',
                'sort_order' => $maxSort + 2,
                'description' => 'Design curated travel journeys — multi-stop itineraries, persona-based recommendations, zone mapping, and Next5 AI suggestions',
            ],
            [
                'name' => 'Merchant Hub',
                'slug' => 'merchant-hub',
                'code' => 'MERCHANT_HUB',
                'type' => 'hybrid',
                'source' => 'internal',
                'icon' => 'building-storefront',
                'color' => '#8B5CF6',
                'sort_order' => $maxSort + 3,
                'description' => 'Merchant onboarding, profile management, tier system, check-in tracking, reviews, favorites, and bulk import tools',
            ],
        ];

        foreach ($apps as $app) {
            $appId = DB::table('applications')->insertGetId(array_merge($app, [
                'is_active' => true,
                'show_in_menu' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]));

            if ($pattayaId) {
                DB::table('cluster_application')->insert([
                    'cluster_id' => $pattayaId,
                    'application_id' => $appId,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // ── Modules per Application ──
        $appModules = [
            'CAMPAIGN_MGMT' => [
                ['name' => 'Campaign Dashboard', 'slug' => 'campaign-dashboard', 'code' => 'CAMPAIGN_DASH', 'description' => 'Overview of all campaigns — active, scheduled, completed — with key metrics and ROI tracking', 'sort_order' => 1],
                ['name' => 'Campaign Builder', 'slug' => 'campaign-builder', 'code' => 'CAMPAIGN_BUILD', 'description' => 'Create campaigns with targeting rules, date ranges, budgets, and A/B testing variants', 'sort_order' => 2],
                ['name' => 'Coupon & Voucher', 'slug' => 'coupon-voucher', 'code' => 'COUPON_VOUCHER', 'description' => 'Generate and manage discount codes, vouchers, flash deals — usage limits, expiry, and tracking', 'sort_order' => 3],
                ['name' => 'Push & Notification', 'slug' => 'push-notification', 'code' => 'PUSH_NOTIF', 'description' => 'Send targeted push notifications, in-app messages, SMS, and email campaigns', 'sort_order' => 4],
                ['name' => 'Audience Segmentation', 'slug' => 'audience-segment', 'code' => 'AUDIENCE_SEG', 'description' => 'Build audience segments by behavior, demographics, location, spending patterns, and persona', 'sort_order' => 5],
                ['name' => 'Cross-Cluster Campaigns', 'slug' => 'cross-cluster-campaign', 'code' => 'CROSS_CAMPAIGN', 'description' => 'Multi-cluster and multi-country campaigns — budget allocation, regional targeting, exchange promotions', 'sort_order' => 6],
                ['name' => 'Campaign Analytics', 'slug' => 'campaign-analytics', 'code' => 'CAMPAIGN_ANALYTICS', 'description' => 'Conversion funnels, redemption rates, revenue attribution, cohort analysis, and export reports', 'sort_order' => 7],
            ],
            'JOURNEY_BUILDER' => [
                ['name' => 'Journey Dashboard', 'slug' => 'journey-dashboard', 'code' => 'JOURNEY_DASH', 'description' => 'Overview of all journeys — active, draft, archived — with popularity and completion metrics', 'sort_order' => 1],
                ['name' => 'Journey Designer', 'slug' => 'journey-designer', 'code' => 'JOURNEY_DESIGN', 'description' => 'Visual journey builder — add stops, set durations, map routes, assign merchants, and preview', 'sort_order' => 2],
                ['name' => 'Place Management', 'slug' => 'place-management', 'code' => 'PLACE_MGMT', 'description' => 'Manage places/POIs — locations, categories, photos, operating hours, and geo-coordinates', 'sort_order' => 3],
                ['name' => 'Persona & Targeting', 'slug' => 'persona-targeting', 'code' => 'PERSONA_TARGET', 'description' => 'Define tourist personas, map journeys to personas, and set market/zone targeting rules', 'sort_order' => 4],
                ['name' => 'Zone Management', 'slug' => 'zone-management', 'code' => 'ZONE_MGMT', 'description' => 'Define geographic zones — draw boundaries, assign places, set zone-specific rules and pricing', 'sort_order' => 5],
                ['name' => 'Next5 Recommendation', 'slug' => 'next5-recommendation', 'code' => 'NEXT5_RECO', 'description' => 'AI-powered "What to do next" — real-time suggestions based on location, time, and preferences', 'sort_order' => 6],
                ['name' => 'Journey Analytics', 'slug' => 'journey-analytics', 'code' => 'JOURNEY_ANALYTICS', 'description' => 'Completion rates, popular stops, drop-off points, time-per-stop, and tourist flow analysis', 'sort_order' => 7],
                ['name' => 'I18n & Translations', 'slug' => 'journey-i18n', 'code' => 'JOURNEY_I18N', 'description' => 'Multi-language journey content — titles, descriptions, audio guides in TH/EN/ZH/JA/KO/RU', 'sort_order' => 8],
            ],
            'MERCHANT_HUB' => [
                ['name' => 'Merchant Dashboard', 'slug' => 'merchant-dashboard', 'code' => 'MERCHANT_DASH', 'description' => 'Merchant overview — profile completeness, check-ins, reviews, revenue, and tier status', 'sort_order' => 1],
                ['name' => 'Merchant Directory', 'slug' => 'merchant-directory', 'code' => 'MERCHANT_DIR', 'description' => 'Search and browse all merchants — filters by category, zone, tier, rating, and status', 'sort_order' => 2],
                ['name' => 'Merchant Onboarding', 'slug' => 'merchant-onboarding', 'code' => 'MERCHANT_ONBOARD', 'description' => 'New merchant registration flow — profile setup, document verification, contract signing', 'sort_order' => 3],
                ['name' => 'Tier & Rating', 'slug' => 'merchant-tier', 'code' => 'MERCHANT_TIER', 'description' => 'Merchant tier system (Bronze-Platinum) — criteria, auto-upgrade, benefits, and badge display', 'sort_order' => 4],
                ['name' => 'Check-in & Visits', 'slug' => 'merchant-checkin', 'code' => 'MERCHANT_CHECKIN', 'description' => 'QR/NFC check-in tracking, visit history, popular times, and foot traffic analytics', 'sort_order' => 5],
                ['name' => 'Reviews & Favorites', 'slug' => 'merchant-reviews', 'code' => 'MERCHANT_REVIEWS', 'description' => 'Customer reviews, star ratings, favorite/wishlist counts, and response management', 'sort_order' => 6],
                ['name' => 'Bulk Import', 'slug' => 'merchant-import', 'code' => 'MERCHANT_IMPORT', 'description' => 'CSV/Excel bulk import — validation, staging, conflict resolution, and batch processing', 'sort_order' => 7],
                ['name' => 'Merchant Analytics', 'slug' => 'merchant-analytics', 'code' => 'MERCHANT_ANALYTICS', 'description' => 'Performance reports, revenue trends, customer demographics, competitive benchmarking', 'sort_order' => 8],
            ],
        ];

        foreach ($appModules as $appCode => $modules) {
            $applicationId = DB::table('applications')->where('code', $appCode)->value('id');
            if (!$applicationId) {
                continue;
            }

            foreach ($modules as $module) {
                DB::table('modules')->insert(array_merge([
                    'application_id' => $applicationId,
                    'is_active' => true,
                    'is_premium' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ], $module));
            }
        }
    }

    public function down(): void
    {
        $codes = ['CAMPAIGN_MGMT', 'JOURNEY_BUILDER', 'MERCHANT_HUB'];
        $appIds = DB::table('applications')->whereIn('code', $codes)->pluck('id');

        DB::table('modules')->whereIn('application_id', $appIds)->delete();
        DB::table('cluster_application')->whereIn('application_id', $appIds)->delete();
        DB::table('applications')->whereIn('id', $appIds)->delete();
    }
};
