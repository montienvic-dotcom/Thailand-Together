<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlaceSeeder extends Seeder
{
    public function run(): void
    {
        $pattayaClusterId = DB::table('clusters')->where('code', 'PTY')->value('id');

        $places = [
            // ── Restaurants / Food ──
            ['place_code' => 'PLACE_TERMINAL21_PIER21', 'place_name_th' => 'Terminal 21 Pattaya – Pier 21 Food Court / Landmark Walk', 'place_name_en' => 'Terminal 21 Pattaya – Pier 21 Food Court / Landmark Walk', 'lat' => 12.9340, 'lng' => 100.8828, 'place_type' => 'mall'],
            ['place_code' => 'PLACE_THEPPRASIT_NIGHT_MARKET', 'place_name_th' => 'ตลาดเทพประสิทธิ์ไนท์มาร์เก็ต', 'place_name_en' => 'Thepprasit Night Market', 'lat' => 12.9010, 'lng' => 100.8850, 'place_type' => 'market'],
            ['place_code' => 'PLACE_CENTRAL_FESTIVAL', 'place_name_th' => 'เซ็นทรัลเฟสติวัล พัทยาบีช', 'place_name_en' => 'Central Festival Pattaya Beach', 'lat' => 12.9450, 'lng' => 100.8850, 'place_type' => 'mall'],
            ['place_code' => 'PLACE_LAN_PHO_NAKLUA', 'place_name_th' => 'ตลาดลานโพธิ์นาเกลือ', 'place_name_en' => 'Lan Pho Naklua Seafood Market', 'lat' => 12.9650, 'lng' => 100.8900, 'place_type' => 'market'],
            ['place_code' => 'PLACE_PUPEN_SEAFOOD', 'place_name_th' => 'ภูเพ็ญซีฟู้ด จอมเทียน', 'place_name_en' => 'Pupen Seafood (Jomtien)', 'lat' => 12.8860, 'lng' => 100.8720, 'place_type' => 'restaurant'],
            ['place_code' => 'PLACE_SKY_GALLERY', 'place_name_th' => 'The Sky Gallery Pattaya', 'place_name_en' => 'The Sky Gallery Pattaya', 'lat' => 12.9200, 'lng' => 100.8600, 'place_type' => 'restaurant'],
            ['place_code' => 'PLACE_CHOCOLATE_FACTORY', 'place_name_th' => 'The Chocolate Factory Pattaya', 'place_name_en' => 'The Chocolate Factory Pattaya', 'lat' => 12.8920, 'lng' => 100.8710, 'place_type' => 'restaurant'],
            ['place_code' => 'PLACE_GLASS_HOUSE', 'place_name_th' => 'The Glass House Pattaya', 'place_name_en' => 'The Glass House Pattaya (Beachfront)', 'lat' => 12.8850, 'lng' => 100.8680, 'place_type' => 'restaurant'],
            ['place_code' => 'PLACE_HORIZON_ROOFTOP', 'place_name_th' => 'Horizon Rooftop Restaurant & Bar (Hilton Pattaya)', 'place_name_en' => 'Horizon Rooftop Restaurant & Bar (Hilton Pattaya)', 'lat' => 12.9445, 'lng' => 100.8842, 'place_type' => 'restaurant'],
            ['place_code' => 'PLACE_TREE_TOWN', 'place_name_th' => 'Tree Town Pattaya', 'place_name_en' => 'Tree Town Pattaya (Eat drinks + Live Music)', 'lat' => 12.9300, 'lng' => 100.8780, 'place_type' => 'restaurant'],
            ['place_code' => 'PLACE_PATTAYA_NIGHT_BAZAAR', 'place_name_th' => 'พัทยาไนท์บาซาร์', 'place_name_en' => 'Pattaya Night Bazaar (Eat/Shop)', 'lat' => 12.9280, 'lng' => 100.8810, 'place_type' => 'market'],
            ['place_code' => 'PLACE_JOMTIEN_NIGHT_MARKET', 'place_name_th' => 'จอมเทียนไนท์มาร์เก็ต', 'place_name_en' => 'Jomtien Night Market', 'lat' => 12.8800, 'lng' => 100.8700, 'place_type' => 'market'],
            ['place_code' => 'PLACE_FLOATING_MARKET', 'place_name_th' => 'ตลาดน้ำ 4 ภาค พัทยา', 'place_name_en' => 'Pattaya Floating Market', 'lat' => 12.9010, 'lng' => 100.8540, 'place_type' => 'attraction'],
            ['place_code' => 'PLACE_ROYAL_GARDEN', 'place_name_th' => 'รอยัล การ์เด้น พลาซ่า', 'place_name_en' => 'Royal Garden Plaza Pattaya', 'lat' => 12.9380, 'lng' => 100.8840, 'place_type' => 'mall'],
            ['place_code' => 'PLACE_CENTRAL_MARINA', 'place_name_th' => 'เซ็นทรัล มารีน่า พัทยา', 'place_name_en' => 'Central Marina Pattaya', 'lat' => 12.9500, 'lng' => 100.8830, 'place_type' => 'mall'],
            ['place_code' => 'PLACE_LOCAL_BREAKFAST', 'place_name_th' => 'ร้านอาหารเช้าท้องถิ่น', 'place_name_en' => 'Local Breakfast Stall', 'lat' => 12.9350, 'lng' => 100.8790, 'place_type' => 'restaurant'],
            ['place_code' => 'PLACE_LOCAL_LUNCH_STREET', 'place_name_th' => 'ถนนข้าวกลางวัน', 'place_name_en' => 'Local Lunch Street (street food)', 'lat' => 12.9320, 'lng' => 100.8800, 'place_type' => 'restaurant'],
            ['place_code' => 'PLACE_CAFE_STOP', 'place_name_th' => 'ร้านกาแฟ & ขนม', 'place_name_en' => 'Cafe stop (coffee + dessert)', 'lat' => 12.9360, 'lng' => 100.8830, 'place_type' => 'cafe'],
            ['place_code' => 'PLACE_PARTNER_DINNER', 'place_name_th' => 'ร้านอาหารพาร์ทเนอร์', 'place_name_en' => 'Dinner (partner restaurant / seafood)', 'lat' => 12.9310, 'lng' => 100.8810, 'place_type' => 'restaurant'],
            ['place_code' => 'PLACE_PARTNER_MEAL', 'place_name_th' => 'ร้านอาหารโซนเซ็นทรัล', 'place_name_en' => 'Meal (Central Pattaya zone)', 'lat' => 12.9400, 'lng' => 100.8840, 'place_type' => 'restaurant'],
            // ── Entertainment ──
            ['place_code' => 'PLACE_TIFFANY_SHOW', 'place_name_th' => 'ทิฟฟานี่โชว์ พัทยา', 'place_name_en' => "Tiffany's Show Pattaya", 'lat' => 12.9530, 'lng' => 100.8900, 'place_type' => 'show'],
            ['place_code' => 'PLACE_ALCAZAR_SHOW', 'place_name_th' => 'อัลคาซาร์ คาบาเร่ต์โชว์', 'place_name_en' => 'Alcazar Cabaret Show', 'lat' => 12.9410, 'lng' => 100.8870, 'place_type' => 'show'],
            ['place_code' => 'PLACE_WALKING_STREET', 'place_name_th' => 'วอล์คกิ้งสตรีท พัทยา', 'place_name_en' => 'Walking Street Pattaya', 'lat' => 12.9260, 'lng' => 100.8710, 'place_type' => 'entertainment'],
            ['place_code' => 'PLACE_HARBOR_PATTAYA', 'place_name_th' => 'ฮาร์เบอร์ พัทยา', 'place_name_en' => 'Harbor Pattaya – Family Arcade / Indoor Fun', 'lat' => 12.9350, 'lng' => 100.8860, 'place_type' => 'entertainment'],
            ['place_code' => 'PLACE_BOWLING_ZONE', 'place_name_th' => 'โบว์ลิ่งโซน พัทยา', 'place_name_en' => 'Bowling Zone (Pattaya)', 'lat' => 12.9370, 'lng' => 100.8850, 'place_type' => 'entertainment'],
            // ── Spa / Wellness ──
            ['place_code' => 'PLACE_LETS_RELAX_SPA', 'place_name_th' => "เล็ทส์ รีแล็กซ์ สปา", 'place_name_en' => "Let's Relax Spa (Foot Massage 60 min.)", 'lat' => 12.9390, 'lng' => 100.8830, 'place_type' => 'spa'],
            ['place_code' => 'PLACE_HEALTH_LAND', 'place_name_th' => 'เฮลท์แลนด์ สปา & มาสซาจ', 'place_name_en' => 'Health Land Spa & Massage (Thai Massage 120 min.)', 'lat' => 12.9250, 'lng' => 100.8770, 'place_type' => 'spa'],
            ['place_code' => 'PLACE_LUXURY_SPA', 'place_name_th' => 'ลักซ์ชัวรี่สปา', 'place_name_en' => 'Luxury Spa Package (premium 120 min.)', 'lat' => 12.9420, 'lng' => 100.8840, 'place_type' => 'spa'],
            ['place_code' => 'PLACE_DETOX_JUICE', 'place_name_th' => 'ดีท็อกซ์จูซบาร์', 'place_name_en' => 'Detox Juice Bar', 'lat' => 12.9330, 'lng' => 100.8810, 'place_type' => 'cafe'],
            ['place_code' => 'PLACE_SUNRISE_YOGA', 'place_name_th' => 'โยคะริมหาด', 'place_name_en' => 'Sunrise Beach Yoga', 'lat' => 12.9280, 'lng' => 100.8650, 'place_type' => 'wellness'],
            ['place_code' => 'PLACE_HEALTHY_CAFE', 'place_name_th' => 'ร้านอาหารเพื่อสุขภาพ', 'place_name_en' => 'Healthy Cafe Meal (clean food)', 'lat' => 12.9340, 'lng' => 100.8820, 'place_type' => 'cafe'],
            ['place_code' => 'PLACE_WELLNESS_CLINIC', 'place_name_th' => 'คลินิกสุขภาพ', 'place_name_en' => 'Wellness Check-Up Clinic', 'lat' => 12.9400, 'lng' => 100.8860, 'place_type' => 'wellness'],
            ['place_code' => 'PLACE_MEDITATION_GARDEN', 'place_name_th' => 'สวนนั่งสมาธิ', 'place_name_en' => 'Meditation Garden', 'lat' => 12.9100, 'lng' => 100.8600, 'place_type' => 'wellness'],
            ['place_code' => 'PLACE_FITNESS_BOOTCAMP', 'place_name_th' => 'ฟิตเนสบูทแคมป์', 'place_name_en' => 'Fitness Bootcamp', 'lat' => 12.9270, 'lng' => 100.8660, 'place_type' => 'wellness'],
            ['place_code' => 'PLACE_ICE_BATH', 'place_name_th' => 'ไอซ์บาทรีคัฟเวอรี่', 'place_name_en' => 'Ice Bath Recovery', 'lat' => 12.9280, 'lng' => 100.8670, 'place_type' => 'wellness'],
            ['place_code' => 'PLACE_HOTEL_SPA', 'place_name_th' => 'สปาในโรงแรม', 'place_name_en' => 'Hotel Spa Package (90-120 mins)', 'lat' => 12.9430, 'lng' => 100.8840, 'place_type' => 'spa'],
            // ── Hotels ──
            ['place_code' => 'PLACE_MIDRANGE_HOTEL', 'place_name_th' => 'โรงแรมระดับกลาง Day-Use', 'place_name_en' => 'Mid-Range Hotel Day-Use (Pattaya)', 'lat' => 12.9380, 'lng' => 100.8830, 'place_type' => 'hotel'],
            ['place_code' => 'PLACE_BUDGET_HOTEL', 'place_name_th' => 'โรงแรมราคาประหยัด Day-Use', 'place_name_en' => 'Budget Hotel Day-Use (Pattaya)', 'lat' => 12.9350, 'lng' => 100.8810, 'place_type' => 'hotel'],
            ['place_code' => 'PLACE_LUXURY_HOTEL', 'place_name_th' => 'โรงแรมหรู Staycation', 'place_name_en' => 'Luxury Hotel Day-Use / Staycation (Pattaya)', 'lat' => 12.9440, 'lng' => 100.8840, 'place_type' => 'hotel'],
            ['place_code' => 'PLACE_FAMILY_RESORT', 'place_name_th' => 'แฟมิลี่รีสอร์ท', 'place_name_en' => 'Family Resort Day Pass (Pool + Kids Club)', 'lat' => 12.9200, 'lng' => 100.8750, 'place_type' => 'hotel'],
            ['place_code' => 'PLACE_COWORKING', 'place_name_th' => 'พาร์ทเนอร์ Co-Working Space', 'place_name_en' => 'Partner Co-Working Space (Pattaya)', 'lat' => 12.9390, 'lng' => 100.8830, 'place_type' => 'office'],
            // ── Transport / Tours ──
            ['place_code' => 'PLACE_VAN_GUIDE', 'place_name_th' => 'รถตู้+ไกด์เต็มวัน', 'place_name_en' => 'Van + full-day guide', 'lat' => 12.9340, 'lng' => 100.8800, 'place_type' => 'transport'],
            ['place_code' => 'PLACE_SUNSET_YACHT', 'place_name_th' => 'เรือยอชท์ชมพระอาทิตย์ตก', 'place_name_en' => 'Sunset Yacht Charter (per person)', 'lat' => 12.9260, 'lng' => 100.8640, 'place_type' => 'tour'],
            ['place_code' => 'PLACE_SAFE_RIDE', 'place_name_th' => 'Safe Ride กลับโรงแรม', 'place_name_en' => 'Safe Ride (Bolt/Grab) back to hotel', 'lat' => 12.9340, 'lng' => 100.8800, 'place_type' => 'transport'],
            ['place_code' => 'PLACE_BALI_HAI_PIER', 'place_name_th' => 'ท่าเรือบาลีฮาย', 'place_name_en' => 'Bali Hai Pier – to Koh Larn', 'lat' => 12.9230, 'lng' => 100.8630, 'place_type' => 'transport'],
            ['place_code' => 'PLACE_KOH_LARN', 'place_name_th' => 'เกาะล้าน จุดดำน้ำ', 'place_name_en' => 'Koh Larn Snorkeling Spot', 'lat' => 12.9170, 'lng' => 100.7850, 'place_type' => 'beach'],
            ['place_code' => 'PLACE_SCOOTER_RENTAL', 'place_name_th' => 'ร้านเช่ามอเตอร์ไซค์', 'place_name_en' => 'Licensed Scooter Rental Partner', 'lat' => 12.9350, 'lng' => 100.8800, 'place_type' => 'transport'],
            ['place_code' => 'PLACE_BIKE_ROUTE', 'place_name_th' => 'เส้นทางจักรยาน Low-Carbon', 'place_name_en' => 'Low-Carbon City Bike Route', 'lat' => 12.9300, 'lng' => 100.8750, 'place_type' => 'tour'],
            ['place_code' => 'PLACE_WATER_SPORTS', 'place_name_th' => 'โซนกีฬาทางน้ำ', 'place_name_en' => 'Pattaya Water Sports Zone', 'lat' => 12.9290, 'lng' => 100.8650, 'place_type' => 'tour'],
            ['place_code' => 'PLACE_FULL_DAY_YACHT', 'place_name_th' => 'เรือยอชท์เต็มวัน', 'place_name_en' => 'Full-Day Yacht Charter', 'lat' => 12.9260, 'lng' => 100.8640, 'place_type' => 'tour'],
            ['place_code' => 'PLACE_CITY_PASS', 'place_name_th' => 'พัทยา City Pass', 'place_name_en' => 'Pattaya City Pass / Tourist Pass', 'lat' => 12.9340, 'lng' => 100.8800, 'place_type' => 'service'],
            // ── Attractions ──
            ['place_code' => 'PLACE_NONG_NOOCH', 'place_name_th' => 'สวนนงนุช', 'place_name_en' => 'Nong Nooch Tropical Garden', 'lat' => 12.7650, 'lng' => 100.9350, 'place_type' => 'attraction'],
            ['place_code' => 'PLACE_SANCTUARY_TRUTH', 'place_name_th' => 'ปราสาทสัจธรรม', 'place_name_en' => 'The Sanctuary Of Truth', 'lat' => 12.9710, 'lng' => 100.8890, 'place_type' => 'attraction'],
            ['place_code' => 'PLACE_ART_IN_PARADISE', 'place_name_th' => 'Art In Paradise Pattaya', 'place_name_en' => 'Art In Paradise Pattaya', 'lat' => 12.9420, 'lng' => 100.8870, 'place_type' => 'attraction'],
            ['place_code' => 'PLACE_FROST_ICE', 'place_name_th' => 'Frost Magical Ice Of Siam', 'place_name_en' => 'Frost Magical Ice Of Siam', 'lat' => 12.9050, 'lng' => 100.8550, 'place_type' => 'attraction'],
            ['place_code' => 'PLACE_VIEWPOINT', 'place_name_th' => 'จุดชมวิว เขาพระตำหนัก', 'place_name_en' => 'Pattaya Viewpoint (Khao Phra Tamnak)', 'lat' => 12.9170, 'lng' => 100.8590, 'place_type' => 'attraction'],
            ['place_code' => 'PLACE_BEACH_ROAD', 'place_name_th' => 'ถนนพัทยาบีช', 'place_name_en' => 'Pattaya Beach Road Walk', 'lat' => 12.9380, 'lng' => 100.8720, 'place_type' => 'beach'],
            ['place_code' => 'PLACE_3MERMAIDS', 'place_name_th' => '3 Mermaids Cafe', 'place_name_en' => '3 Mermaids Cafe (Pratumnak)', 'lat' => 12.9150, 'lng' => 100.8600, 'place_type' => 'cafe'],
            ['place_code' => 'PLACE_SHELL_TANGKE', 'place_name_th' => 'เชลล์ ตังเก ซีฟู้ด', 'place_name_en' => 'Shell Tangke Seafood (Na Kluea)', 'lat' => 12.9600, 'lng' => 100.8880, 'place_type' => 'restaurant'],
            ['place_code' => 'PLACE_NAKLUA_SEAFOOD', 'place_name_th' => 'ร้านซีฟู้ดนาเกลือ', 'place_name_en' => 'Naklua Seafood', 'lat' => 12.9620, 'lng' => 100.8890, 'place_type' => 'restaurant'],
            // ── MICE / Events ──
            ['place_code' => 'PLACE_PEACH', 'place_name_th' => 'PEACH ศูนย์ประชุมพัทยา', 'place_name_en' => 'PEACH (Pattaya Exhibition And Convention Hall)', 'lat' => 12.9100, 'lng' => 100.8580, 'place_type' => 'venue'],
            ['place_code' => 'PLACE_ROYAL_CLIFF', 'place_name_th' => 'โรยัล คลิฟ โฮเทลส์', 'place_name_en' => 'Royal Cliff Hotels Group', 'lat' => 12.9100, 'lng' => 100.8570, 'place_type' => 'hotel'],
        ];

        foreach ($places as $place) {
            DB::table('place')->updateOrInsert(
                ['place_code' => $place['place_code']],
                array_merge($place, [
                    'is_active' => true,
                    'cluster_id' => $pattayaClusterId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
}
