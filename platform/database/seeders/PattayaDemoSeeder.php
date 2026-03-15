<?php

namespace Database\Seeders;

use App\Models\Auth\User;
use App\Models\Global\Cluster;
use App\Models\Global\Country;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PattayaDemoSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding Pattaya demo data...');

        // ── Country & Cluster ──
        $thailand = Country::firstOrCreate(
            ['code' => 'THA'],
            ['name' => 'Thailand', 'code_alpha2' => 'TH', 'is_active' => true]
        );

        $pattaya = Cluster::firstOrCreate(
            ['code' => 'PTY'],
            [
                'name' => 'Pattaya',
                'slug' => 'pattaya',
                'country_id' => $thailand->id,
                'is_active' => true,
                'timezone' => 'Asia/Bangkok',
            ]
        );

        // ── Demo Users ──
        $this->seedUsers($pattaya);

        // ── Merchants ──
        $this->seedMerchants($pattaya);

        // ── Journeys ──
        $this->seedJourneys($pattaya);

        // ── Deals ──
        $this->seedDeals($pattaya);

        // ── Campaigns ──
        $this->seedCampaigns($pattaya, $thailand);

        $this->command->info('Pattaya demo data seeded successfully!');
    }

    private function seedUsers(Cluster $pattaya): void
    {
        $users = [
            ['name' => 'Admin Pattaya', 'email' => 'admin@pattaya.test', 'status' => 'active'],
            ['name' => 'Alex Tourist', 'email' => 'alex@tourist.test', 'status' => 'active'],
            ['name' => 'Sam Traveler', 'email' => 'sam@tourist.test', 'status' => 'active'],
            ['name' => 'Chris Explorer', 'email' => 'chris@tourist.test', 'status' => 'active'],
            ['name' => 'Merchant Spa', 'email' => 'spa@merchant.test', 'status' => 'active'],
            ['name' => 'Merchant Restaurant', 'email' => 'restaurant@merchant.test', 'status' => 'active'],
        ];

        foreach ($users as $userData) {
            User::firstOrCreate(
                ['email' => $userData['email']],
                array_merge($userData, [
                    'password' => Hash::make('password'),
                    'locale' => 'en',
                ])
            );
        }

        $this->command->info('  ✓ 6 demo users');
    }

    private function seedMerchants(Cluster $pattaya): void
    {
        $merchants = [
            [
                'merchant_code' => 'PTY-SPA-001',
                'merchant_name_en' => 'Oasis Spa Pattaya',
                'merchant_name_th' => 'โอเอซิส สปา พัทยา',
                'merchant_desc_en' => 'Premium Thai spa experience with traditional techniques',
                'default_tier_code' => 'E',
                'price_level' => 4,
                'lat' => 12.9236,
                'lng' => 100.8825,
                'phone' => '+66-38-123-456',
                'service_tags' => 'spa,massage,wellness,thai-massage',
                'open_hours' => '10:00-22:00',
            ],
            [
                'merchant_code' => 'PTY-REST-001',
                'merchant_name_en' => 'Walking Street Seafood',
                'merchant_name_th' => 'วอล์คกิ้งสตรีท ซีฟู้ด',
                'merchant_desc_en' => 'Fresh seafood with ocean view on Walking Street',
                'default_tier_code' => 'M',
                'price_level' => 3,
                'lat' => 12.9271,
                'lng' => 100.8718,
                'phone' => '+66-38-234-567',
                'service_tags' => 'restaurant,seafood,thai-food,ocean-view',
                'open_hours' => '11:00-23:00',
            ],
            [
                'merchant_code' => 'PTY-TOUR-001',
                'merchant_name_en' => 'Coral Island Adventures',
                'merchant_name_th' => 'เกาะล้าน แอดเวนเจอร์',
                'merchant_desc_en' => 'Speedboat tours to Koh Larn with snorkeling and beach activities',
                'default_tier_code' => 'XL',
                'price_level' => 3,
                'lat' => 12.9180,
                'lng' => 100.8850,
                'phone' => '+66-38-345-678',
                'service_tags' => 'tour,island,snorkeling,boat,beach',
                'open_hours' => '07:00-17:00',
            ],
            [
                'merchant_code' => 'PTY-HOTEL-001',
                'merchant_name_en' => 'Hilton Pattaya',
                'merchant_name_th' => 'ฮิลตัน พัทยา',
                'merchant_desc_en' => 'Luxury beachfront hotel in central Pattaya',
                'default_tier_code' => 'XL',
                'price_level' => 5,
                'lat' => 12.9363,
                'lng' => 100.8831,
                'phone' => '+66-38-253-000',
                'service_tags' => 'hotel,luxury,beach,pool,fitness',
                'open_hours' => '24/7',
            ],
            [
                'merchant_code' => 'PTY-SHOP-001',
                'merchant_name_en' => 'Central Festival Pattaya',
                'merchant_name_th' => 'เซ็นทรัล เฟสติวัล พัทยา',
                'merchant_desc_en' => 'Premium shopping mall with international brands',
                'default_tier_code' => 'E',
                'price_level' => 4,
                'lat' => 12.9340,
                'lng' => 100.8820,
                'phone' => '+66-38-999-999',
                'service_tags' => 'shopping,mall,fashion,food-court',
                'open_hours' => '10:30-22:00',
            ],
            [
                'merchant_code' => 'PTY-NONG-001',
                'merchant_name_en' => 'Nong Nooch Tropical Garden',
                'merchant_name_th' => 'สวนนงนุช',
                'merchant_desc_en' => 'World-famous tropical garden with cultural shows',
                'default_tier_code' => 'XL',
                'price_level' => 3,
                'lat' => 12.7654,
                'lng' => 100.9325,
                'phone' => '+66-38-429-321',
                'service_tags' => 'garden,culture,show,elephant,nature',
                'open_hours' => '08:00-18:00',
            ],
            [
                'merchant_code' => 'PTY-NIGHT-001',
                'merchant_name_en' => 'Alcazar Cabaret Show',
                'merchant_name_th' => 'อัลคาซ่าร์ คาบาเรต์โชว์',
                'merchant_desc_en' => 'World-renowned cabaret performance',
                'default_tier_code' => 'E',
                'price_level' => 3,
                'lat' => 12.9510,
                'lng' => 100.8890,
                'phone' => '+66-38-410-225',
                'service_tags' => 'show,entertainment,nightlife,cabaret',
                'open_hours' => '17:00-23:00',
            ],
            [
                'merchant_code' => 'PTY-WATER-001',
                'merchant_name_en' => 'Ramayana Water Park',
                'merchant_name_th' => 'รามายณะ วอเตอร์พาร์ค',
                'merchant_desc_en' => 'Largest water park in Thailand',
                'default_tier_code' => 'XL',
                'price_level' => 3,
                'lat' => 12.8456,
                'lng' => 100.9543,
                'phone' => '+66-33-005-929',
                'service_tags' => 'waterpark,family,adventure,slides',
                'open_hours' => '10:00-18:00',
            ],
        ];

        foreach ($merchants as $merchant) {
            DB::table('merchant')->updateOrInsert(
                ['merchant_code' => $merchant['merchant_code']],
                array_merge($merchant, [
                    'is_active' => true,
                    'cluster_id' => $pattaya->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }

        $this->command->info('  ✓ 8 demo merchants');
    }

    private function seedJourneys(Cluster $pattaya): void
    {
        $journeys = [
            [
                'journey_code' => 'J001',
                'journey_name_en' => 'Beach & Spa Day',
                'journey_name_th' => 'วันพักผ่อนชายหาด & สปา',
                'journey_group' => 'A',
                'group_size' => 2,
                'gmv_per_person' => 3500,
                'gmv_per_group' => 7000,
                'tp_total_normal' => 85,
                'total_minutes_sum' => 480,
                'status' => 'ACTIVE',
            ],
            [
                'journey_code' => 'J002',
                'journey_name_en' => 'Island Hopping Adventure',
                'journey_name_th' => 'ท่องเที่ยวเกาะ',
                'journey_group' => 'B',
                'group_size' => 4,
                'gmv_per_person' => 2800,
                'gmv_per_group' => 11200,
                'tp_total_normal' => 92,
                'total_minutes_sum' => 600,
                'status' => 'ACTIVE',
            ],
            [
                'journey_code' => 'J003',
                'journey_name_en' => 'Cultural Heritage Tour',
                'journey_name_th' => 'ทัวร์มรดกวัฒนธรรม',
                'journey_group' => 'C',
                'group_size' => 6,
                'gmv_per_person' => 2200,
                'gmv_per_group' => 13200,
                'tp_total_normal' => 78,
                'total_minutes_sum' => 540,
                'status' => 'ACTIVE',
            ],
            [
                'journey_code' => 'J004',
                'journey_name_en' => 'Nightlife & Entertainment',
                'journey_name_th' => 'ไลฟ์ไนท์ & ความบันเทิง',
                'journey_group' => 'D',
                'group_size' => 4,
                'gmv_per_person' => 4500,
                'gmv_per_group' => 18000,
                'tp_total_normal' => 88,
                'total_minutes_sum' => 360,
                'status' => 'ACTIVE',
            ],
            [
                'journey_code' => 'J005',
                'journey_name_en' => 'Family Fun Day',
                'journey_name_th' => 'วันครอบครัวสนุกสนาน',
                'journey_group' => 'E',
                'group_size' => 4,
                'gmv_per_person' => 1800,
                'gmv_per_group' => 7200,
                'tp_total_normal' => 95,
                'total_minutes_sum' => 720,
                'status' => 'ACTIVE',
            ],
            [
                'journey_code' => 'J006',
                'journey_name_en' => 'Luxury Pattaya Experience',
                'journey_name_th' => 'ประสบการณ์หรูหราพัทยา',
                'journey_group' => 'F',
                'group_size' => 2,
                'gmv_per_person' => 8500,
                'gmv_per_group' => 17000,
                'tp_total_normal' => 72,
                'total_minutes_sum' => 480,
                'status' => 'ACTIVE',
            ],
        ];

        foreach ($journeys as $journey) {
            DB::table('journey')->updateOrInsert(
                ['journey_code' => $journey['journey_code']],
                array_merge($journey, [
                    'cluster_id' => $pattaya->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }

        $this->command->info('  ✓ 6 demo journeys');
    }

    private function seedDeals(Cluster $pattaya): void
    {
        $deals = [
            [
                'title_en' => '50% Off Thai Massage',
                'title_th' => 'ลด 50% นวดแผนไทย',
                'description_en' => 'Get half price on traditional Thai massage at Oasis Spa',
                'category' => 'spa',
                'discount_value' => 50,
                'discount_type' => 'percentage',
                'max_redemptions' => 200,
                'priority' => 10,
            ],
            [
                'title_en' => 'Free Drink with Seafood Set',
                'title_th' => 'เครื่องดื่มฟรี เมื่อสั่งเซ็ทซีฟู้ด',
                'description_en' => 'Complimentary drink with any seafood set menu at Walking Street Seafood',
                'category' => 'restaurant',
                'discount_value' => 0,
                'discount_type' => 'freebie',
                'max_redemptions' => 500,
                'priority' => 8,
            ],
            [
                'title_en' => 'Coral Island Tour — 300 THB Off',
                'title_th' => 'ทัวร์เกาะล้าน ลด 300 บาท',
                'description_en' => 'Save 300 THB on Coral Island speedboat tour',
                'category' => 'tour',
                'discount_value' => 300,
                'discount_type' => 'fixed',
                'max_redemptions' => 100,
                'priority' => 9,
            ],
            [
                'title_en' => 'Ramayana Water Park 2-for-1',
                'title_th' => 'รามายณะ วอเตอร์พาร์ค ซื้อ 1 ได้ 2',
                'description_en' => 'Buy one ticket, get one free at Ramayana Water Park',
                'category' => 'attraction',
                'discount_value' => 50,
                'discount_type' => 'percentage',
                'max_redemptions' => 300,
                'priority' => 10,
            ],
            [
                'title_en' => 'Early Bird Alcazar Show',
                'title_th' => 'อัลคาซ่าร์ โชว์ ราคาพิเศษ',
                'description_en' => 'Book before 3PM for 20% off Alcazar Cabaret Show tickets',
                'category' => 'entertainment',
                'discount_value' => 20,
                'discount_type' => 'percentage',
                'max_redemptions' => 0,
                'priority' => 7,
            ],
        ];

        foreach ($deals as $deal) {
            DB::table('deals')->updateOrInsert(
                ['title_en' => $deal['title_en'], 'cluster_id' => $pattaya->id],
                array_merge($deal, [
                    'cluster_id' => $pattaya->id,
                    'is_active' => true,
                    'start_date' => now()->subDay(),
                    'end_date' => now()->addMonths(3),
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }

        $this->command->info('  ✓ 5 demo deals');
    }

    private function seedCampaigns(Cluster $pattaya, Country $thailand): void
    {
        DB::table('campaigns')->updateOrInsert(
            ['slug' => 'pattaya-welcome-2026'],
            [
                'name' => 'Welcome to Pattaya 2026',
                'slug' => 'pattaya-welcome-2026',
                'description' => 'Earn double reward points on your first 3 bookings in Pattaya',
                'scope' => 'cluster',
                'country_id' => $thailand->id,
                'cluster_id' => $pattaya->id,
                'type' => 'reward_bonus',
                'rules' => json_encode(['first_n_bookings' => 3, 'multiplier' => 2]),
                'rewards' => json_encode(['bonus_points' => 100]),
                'starts_at' => now()->startOfYear(),
                'ends_at' => now()->endOfYear(),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        DB::table('campaigns')->updateOrInsert(
            ['slug' => 'thailand-together-launch'],
            [
                'name' => 'Thailand Together Launch Promotion',
                'slug' => 'thailand-together-launch',
                'description' => 'Get 500 bonus points when you sign up and make your first booking',
                'scope' => 'global',
                'country_id' => null,
                'cluster_id' => null,
                'type' => 'reward_bonus',
                'rules' => json_encode(['first_booking' => true]),
                'rewards' => json_encode(['bonus_points' => 500]),
                'starts_at' => now()->subMonth(),
                'ends_at' => now()->addMonths(6),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        $this->command->info('  ✓ 2 demo campaigns');
    }
}
