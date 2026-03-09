<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MerchantDemoSeeder extends Seeder
{
    /**
     * Seed 5-10 demo merchants per place with tier ranking.
     * Pattern: MERCH_{place_code}_{nn}
     */
    public function run(): void
    {
        $clusterId = DB::table('clusters')->where('code', 'PTY')->value('id');
        $places = DB::table('place')->where('cluster_id', $clusterId)->get();

        $tiers = ['XL', 'E', 'M', 'S'];
        $tierPriority = ['XL' => 1, 'E' => 2, 'M' => 3, 'S' => 4];
        $categories = [
            'restaurant' => ['ร้านอาหาร', 'Restaurant'],
            'cafe'       => ['คาเฟ่', 'Cafe'],
            'market'     => ['ร้านค้า', 'Shop'],
            'mall'       => ['ร้านค้าในห้าง', 'Mall Shop'],
            'hotel'      => ['โรงแรม', 'Hotel'],
            'spa'        => ['สปา', 'Spa'],
            'show'       => ['โชว์', 'Show'],
            'entertainment' => ['สถานบันเทิง', 'Entertainment'],
            'wellness'   => ['สุขภาพ', 'Wellness'],
            'transport'  => ['ขนส่ง', 'Transport'],
            'tour'       => ['ทัวร์', 'Tour'],
            'attraction' => ['ท่องเที่ยว', 'Attraction'],
            'beach'      => ['ชายหาด', 'Beach'],
            'office'     => ['ออฟฟิศ', 'Office'],
            'venue'      => ['สถานที่จัดงาน', 'Venue'],
            'service'    => ['บริการ', 'Service'],
        ];

        foreach ($places as $place) {
            $merchantCount = rand(5, 10);
            $cat = $categories[$place->place_type] ?? ['ร้านค้า', 'Shop'];

            for ($i = 1; $i <= $merchantCount; $i++) {
                $tierIdx = min($i - 1, 3); // first merchant XL, then E, M, S...
                $tier = $tiers[$tierIdx % 4];
                $isPrimary = ($i === 1);
                $code = sprintf('MERCH_%s_%02d', str_replace('PLACE_', '', $place->place_code), $i);

                // Upsert merchant
                $existing = DB::table('merchant')->where('merchant_code', $code)->first();
                if (!$existing) {
                    $merchantId = DB::table('merchant')->insertGetId([
                        'merchant_code'    => $code,
                        'merchant_name_th' => sprintf('%s %s #%d', $cat[0], $place->place_name_th, $i),
                        'merchant_name_en' => sprintf('%s %s #%d', $cat[1], $place->place_name_en, $i),
                        'tier_code'        => $tier,
                        'category'         => $place->place_type,
                        'lat'              => $place->lat + (rand(-50, 50) / 10000),
                        'lng'              => $place->lng + (rand(-50, 50) / 10000),
                        'phone'            => '0' . rand(80, 99) . rand(1000000, 9999999),
                        'website'          => null,
                        'is_active'        => true,
                        'cluster_id'       => $clusterId,
                        'created_at'       => now(),
                        'updated_at'       => now(),
                    ]);
                } else {
                    $merchantId = $existing->id;
                }

                // Link merchant to place via place_merchant pivot
                DB::table('place_merchant')->updateOrInsert(
                    [
                        'place_id'    => $place->id,
                        'merchant_id' => $merchantId,
                    ],
                    [
                        'tier_code'  => $tier,
                        'is_primary' => $isPrimary,
                        'sort_order' => $i,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }
        }
    }
}
