<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Add descriptions to all 10 apps and seed modules for the 5 apps
 * that were missing them (Hotel, Tour, Marketplace, Rewards, HelpDesk).
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── Update app descriptions ──
        $descriptions = [
            'APP_TOGETHER' => 'All-in-one mobile app for tourists — explore, book, navigate, and earn rewards in one place',
            'HOTEL_MGMT' => 'Complete hotel operations — room management, reservations, guest services, and revenue tracking',
            'TOUR_BOOKING' => 'Browse and book tours, activities, and experiences — day trips, island hopping, shows, and more',
            'MARKETPLACE' => 'Local shops, souvenirs, food delivery, and services — support local merchants while you travel',
            'REWARDS' => 'Earn and redeem points across all services — exclusive deals, tier benefits, and cross-cluster rewards',
            'HELPDESK' => '24/7 multilingual support — AI chatbot, live agents, ticket tracking, and emergency assistance',
            'CITY_DIGITAL_TWIN' => 'Interactive 3D city map with AR navigation, real-time data, and virtual tours of Pattaya',
            'SOCIAL_NETWORK' => 'Connect with fellow travelers — share experiences, join groups, find events, and make friends',
            'PARTNER_HUB' => 'Earn commissions by referring friends and merchants — influencer tools, tracking, and payouts',
            'UGC_AI_HUB' => 'AI-powered content creation — auto-translate, generate itineraries, and curate user reviews',
        ];

        foreach ($descriptions as $code => $description) {
            DB::table('applications')
                ->where('code', $code)
                ->whereNull('description')
                ->update(['description' => $description]);
        }

        // ── Seed modules for apps that have none ──
        $appModules = [
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
            if (! $applicationId) {
                continue;
            }

            // Only seed if this app has no modules yet
            $existingCount = DB::table('modules')->where('application_id', $applicationId)->count();
            if ($existingCount > 0) {
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
        // Remove modules for the 5 apps
        $appCodes = ['HOTEL_MGMT', 'TOUR_BOOKING', 'MARKETPLACE', 'REWARDS', 'HELPDESK'];
        foreach ($appCodes as $code) {
            $applicationId = DB::table('applications')->where('code', $code)->value('id');
            if ($applicationId) {
                DB::table('modules')->where('application_id', $applicationId)->delete();
            }
        }

        // Clear descriptions
        DB::table('applications')->update(['description' => null]);
    }
};
