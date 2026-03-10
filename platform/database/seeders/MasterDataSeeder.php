<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Seeds all md_* master data tables from the production dump (u504097778_journey.sql).
 * This includes: md_country, md_market_zone, md_market_zone_i18n, md_partner_tier,
 * md_persona, md_persona_i18n, md_point_policy, md_tag, md_tag_i18n.
 */
class MasterDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedCountries();
        $this->seedMarketZones();
        $this->seedPartnerTiers();
        $this->seedPersonas();
        $this->seedPointPolicies();
        $this->seedTags();
    }

    private function seedCountries(): void
    {
        DB::table('md_country')->insertOrIgnore([
            ['country_code' => 'AU', 'country_name_th' => 'ออสเตรเลีย', 'country_name_en' => 'Australia', 'continent' => 'Oceania'],
            ['country_code' => 'CN', 'country_name_th' => 'จีน', 'country_name_en' => 'China', 'continent' => 'Asia'],
            ['country_code' => 'DE', 'country_name_th' => 'เยอรมนี', 'country_name_en' => 'Germany', 'continent' => 'Europe'],
            ['country_code' => 'IN', 'country_name_th' => 'อินเดีย', 'country_name_en' => 'India', 'continent' => 'Asia'],
            ['country_code' => 'JP', 'country_name_th' => 'ญี่ปุ่น', 'country_name_en' => 'Japan', 'continent' => 'Asia'],
            ['country_code' => 'KR', 'country_name_th' => 'เกาหลีใต้', 'country_name_en' => 'South Korea', 'continent' => 'Asia'],
            ['country_code' => 'MYSG', 'country_name_th' => 'มาเลเซีย/สิงคโปร์', 'country_name_en' => 'Malaysia/Singapore', 'continent' => 'Asia'],
            ['country_code' => 'RU', 'country_name_th' => 'รัสเซีย', 'country_name_en' => 'Russia', 'continent' => 'Europe'],
            ['country_code' => 'UK', 'country_name_th' => 'สหราชอาณาจักร', 'country_name_en' => 'United Kingdom', 'continent' => 'Europe'],
            ['country_code' => 'US', 'country_name_th' => 'สหรัฐอเมริกา', 'country_name_en' => 'United States', 'continent' => 'North America'],
        ]);
    }

    private function seedMarketZones(): void
    {
        DB::table('md_market_zone')->insertOrIgnore([
            ['zone_code' => 'ZONE_AFRICA', 'sort_order' => 110, 'is_active' => 1],
            ['zone_code' => 'ZONE_ARAB', 'sort_order' => 60, 'is_active' => 1],
            ['zone_code' => 'ZONE_ASEAN', 'sort_order' => 10, 'is_active' => 1],
            ['zone_code' => 'ZONE_CHINA', 'sort_order' => 20, 'is_active' => 1],
            ['zone_code' => 'ZONE_EU_E', 'sort_order' => 100, 'is_active' => 1],
            ['zone_code' => 'ZONE_EU_W', 'sort_order' => 90, 'is_active' => 1],
            ['zone_code' => 'ZONE_INDIA', 'sort_order' => 40, 'is_active' => 1],
            ['zone_code' => 'ZONE_JAPAN', 'sort_order' => 30, 'is_active' => 1],
            ['zone_code' => 'ZONE_NA', 'sort_order' => 80, 'is_active' => 1],
            ['zone_code' => 'ZONE_OCEANIA', 'sort_order' => 70, 'is_active' => 1],
            ['zone_code' => 'ZONE_SOUTH_AMERICA', 'sort_order' => 120, 'is_active' => 1],
            ['zone_code' => 'ZONE_SOUTH_ASIA', 'sort_order' => 50, 'is_active' => 1],
        ]);
    }

    private function seedPartnerTiers(): void
    {
        DB::table('md_partner_tier')->insertOrIgnore([
            ['tier_code' => 'E', 'tier_name_th' => 'Enterprise', 'tier_name_en' => 'Enterprise'],
            ['tier_code' => 'E/XL', 'tier_name_th' => 'Enterprise+XL (Mixed)', 'tier_name_en' => 'Enterprise+XL (Mixed)'],
            ['tier_code' => 'M', 'tier_name_th' => 'Medium', 'tier_name_en' => 'Medium'],
            ['tier_code' => 'S', 'tier_name_th' => 'Small/Micro', 'tier_name_en' => 'Small/Micro'],
            ['tier_code' => 'S/M', 'tier_name_th' => 'Small+Medium (Mixed)', 'tier_name_en' => 'Small+Medium (Mixed)'],
            ['tier_code' => 'XL', 'tier_name_th' => 'Extra Large', 'tier_name_en' => 'Extra Large'],
        ]);
    }

    private function seedPersonas(): void
    {
        DB::table('md_persona')->insertOrIgnore([
            ['persona_code' => 'P1', 'persona_name_th' => 'First-timer City Icons (มือใหม่/แลนด์มาร์ก)', 'persona_name_en' => 'First-timer City Icons'],
            ['persona_code' => 'P2', 'persona_name_th' => 'Family & Multi-gen (ครอบครัว/หลายวัย)', 'persona_name_en' => 'Family & Multi-gen'],
            ['persona_code' => 'P3', 'persona_name_th' => 'Couple/Honeymoon/Proposal (คู่รัก)', 'persona_name_en' => 'Couple/Honeymoon/Proposal'],
            ['persona_code' => 'P4', 'persona_name_th' => 'Party & Nightlife (สายปาร์ตี้)', 'persona_name_en' => 'Party & Nightlife'],
            ['persona_code' => 'P5', 'persona_name_th' => 'Premium/VIP/Yacht (พรีเมียม/VIP)', 'persona_name_en' => 'Premium/VIP/Yacht'],
            ['persona_code' => 'P6', 'persona_name_th' => 'Budget/Value Hunter (งบประหยัด)', 'persona_name_en' => 'Budget/Value Hunter'],
            ['persona_code' => 'P7', 'persona_name_th' => 'Wellness/Health (สุขภาพ/สปา)', 'persona_name_en' => 'Wellness/Health'],
            ['persona_code' => 'P8', 'persona_name_th' => 'MICE/Business/Workation (ธุรกิจ/ประชุม)', 'persona_name_en' => 'MICE/Business/Workation'],
            ['persona_code' => 'P9', 'persona_name_th' => 'Food-centric / Halal / Indian-friendly (สายกิน/ฮาลาล/อินเดีย)', 'persona_name_en' => 'Food-centric / Halal'],
            ['persona_code' => 'P10', 'persona_name_th' => 'Adventure/Island/Water Activities (ทะเล/กิจกรรม)', 'persona_name_en' => 'Adventure/Island/Water'],
        ]);
    }

    private function seedPointPolicies(): void
    {
        DB::table('md_point_policy')->insertOrIgnore([
            [
                'policy_code' => 'DEFAULT_2026',
                'normal_divisor' => 25,
                'goal_multiplier' => 1.300,
                'special_multiplier' => 2.000,
                'mission_checkin_normal' => 10,
                'mission_checkin_goal' => 20,
                'mission_checkin_special' => 50,
                'mission_review_normal' => 20,
                'mission_review_goal' => 40,
                'mission_review_special' => 80,
            ],
        ]);
    }

    private function seedTags(): void
    {
        $tags = [
            [1, 'TAG_FIRST_TIMER'], [2, 'TAG_FAMILY'], [3, 'TAG_COUPLE'],
            [4, 'TAG_NIGHTLIFE'], [5, 'TAG_VIP'], [6, 'TAG_BUDGET'],
            [7, 'TAG_WELLNESS'], [8, 'TAG_MICE'], [9, 'TAG_FOOD'],
            [10, 'TAG_ISLAND'], [11, 'TAG_SHOPPING'], [12, 'TAG_SAFE_RIDE'],
            [13, 'TAG_MIDWEEK'], [14, 'TAG_GREEN'], [15, 'TAG_PHOTO_SPOT'],
            [16, 'TAG_SEAFOOD'], [17, 'TAG_STREET_FOOD'], [18, 'TAG_CAFE'],
            [19, 'TAG_DESSERT'], [20, 'TAG_HALAL'], [21, 'TAG_INDIAN_FRIENDLY'],
            [22, 'TAG_SPA'], [23, 'TAG_MASSAGE'], [24, 'TAG_LUXURY_SPA'],
            [25, 'TAG_FITNESS'], [26, 'TAG_YOGA'], [27, 'TAG_HEALTH_CHECKUP'],
            [28, 'TAG_NIGHT_MARKET'], [29, 'TAG_HOTEL'], [30, 'TAG_WORKATION'],
            [31, 'TAG_COWORKING'], [32, 'TAG_STAYCATION'], [33, 'TAG_RESORT'],
            [34, 'TAG_AIRPORT_TRANSFER'], [35, 'TAG_PUBLIC_TRANSPORT'], [36, 'TAG_SCOOTER'],
            [37, 'TAG_ESIM'], [38, 'TAG_CITY_PASS'], [39, 'TAG_DIY_TICKETS'],
            [41, 'TAG_SHOW'], [42, 'TAG_CABARET'], [43, 'TAG_LIVE_MUSIC'],
            [44, 'TAG_NIGHT_PHOTO'], [45, 'TAG_EVENT'], [46, 'TAG_CONVENTION'],
            [47, 'TAG_GALA'], [48, 'TAG_FESTIVAL'], [49, 'TAG_CONCERT'],
            [50, 'TAG_WORKSHOP'], [51, 'TAG_NETWORKING'], [52, 'TAG_SPORTS_EVENT'],
            [54, 'TAG_DETOX'], [55, 'TAG_MEDITATION'], [56, 'TAG_BEACH'],
            [57, 'TAG_SNORKEL'], [58, 'TAG_WATER_ACTIVITY'], [59, 'TAG_ECO'],
            [60, 'TAG_LOW_CARBON'], [61, 'TAG_DAYTRIP'], [62, 'TAG_PRIVATE_VAN'],
            [63, 'TAG_FINE_DINING'], [64, 'TAG_MEDICAL'], [65, 'TAG_YACHT'],
            [66, 'TAG_CITY_HIGHLIGHTS'], [67, 'TAG_CULTURE'], [68, 'TAG_ROMANTIC'],
            [73, 'TAG_STREETFOOD'], [74, 'TAG_VIEWPOINT'], [75, 'TAG_KIDS'],
            [76, 'TAG_THEMEPARK'], [77, 'TAG_WATERPARK'], [78, 'TAG_ZOO'],
            [79, 'TAG_PHOTO'], [80, 'TAG_MARKET'], [187, 'TAG_GOLF'],
            [188, 'TAG_TEAMBUILD'], [189, 'TAG_HEALTHYFOOD'], [217, 'TAG_GARDEN'],
            [218, 'TAG_MUSEUM'], [219, 'TAG_NIGHTPHOTO'], [220, 'TAG_ADVENTURE'],
            [221, 'TAG_LUXURY'],
        ];

        $rows = array_map(fn ($t) => [
            'tag_id' => $t[0],
            'tag_code' => $t[1],
            'is_active' => 1,
        ], $tags);

        DB::table('md_tag')->insertOrIgnore($rows);
    }
}
