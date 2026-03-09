<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Seeds journey metadata tables:
 * - journey_i18n (multi-language names & descriptions)
 * - journey_tag (tags per journey)
 * - journey_persona (target personas per journey)
 * - journey_market (country fit per journey)
 * - journey_zone (zone fit per journey)
 * - journey_next5 (recommended next journeys)
 */
class JourneyMetaSeeder extends Seeder
{
    public function run(): void
    {
        $journeys = DB::table('journey')->get();

        if ($journeys->isEmpty()) {
            $this->command->warn('No journeys found. Run JourneySeeder first.');
            return;
        }

        // ── Tag mapping by group ──
        $groupTags = [
            'A' => ['food', 'restaurant', 'street-food', 'dining'],
            'B' => ['hotel', 'accommodation', 'stay', 'resort'],
            'C' => ['entertainment', 'nightlife', 'show', 'bar'],
            'D' => ['spa', 'wellness', 'health', 'massage'],
            'E' => ['transport', 'tour', 'travel', 'island'],
            'F' => ['event', 'mice', 'conference', 'exhibition'],
            'G' => ['shopping', 'souvenir', 'lifestyle', 'mall'],
            'H' => ['attraction', 'activity', 'family', 'sightseeing'],
        ];

        // Extra tags by specific journey tone/content
        $budgetTags = ['budget', 'value'];
        $premiumTags = ['premium', 'luxury'];
        $familyTags = ['family', 'kid-friendly'];
        $romanticTags = ['romantic', 'couple'];
        $photoTags = ['photogenic', 'instagram'];

        // ── Persona mapping by group ──
        $groupPersonas = [
            'A' => ['solo', 'couple', 'family', 'group'],
            'B' => ['solo', 'couple', 'family', 'business'],
            'C' => ['solo', 'couple', 'group'],
            'D' => ['solo', 'couple', 'family'],
            'E' => ['solo', 'couple', 'family', 'group'],
            'F' => ['business', 'group'],
            'G' => ['solo', 'couple', 'family', 'group'],
            'H' => ['family', 'couple', 'group'],
        ];

        // ── Market fit (country_code => default fit_level) ──
        $defaultMarkets = [
            'TH' => 5, 'CN' => 4, 'RU' => 4, 'KR' => 3,
            'JP' => 3, 'IN' => 3, 'US' => 2, 'GB' => 2,
            'DE' => 2, 'AU' => 2,
        ];

        // ── Zones in Pattaya ──
        $allZones = ['central-pattaya', 'south-pattaya', 'north-pattaya', 'jomtien', 'naklua', 'pratumnak', 'koh-larn'];

        // Zone mapping by group (primary zones)
        $groupZones = [
            'A' => ['central-pattaya' => 5, 'south-pattaya' => 4, 'naklua' => 4, 'jomtien' => 3],
            'B' => ['central-pattaya' => 5, 'jomtien' => 4, 'pratumnak' => 4, 'north-pattaya' => 3],
            'C' => ['south-pattaya' => 5, 'central-pattaya' => 4],
            'D' => ['central-pattaya' => 4, 'jomtien' => 4, 'pratumnak' => 4, 'north-pattaya' => 3],
            'E' => ['koh-larn' => 5, 'central-pattaya' => 4, 'jomtien' => 3, 'naklua' => 3],
            'F' => ['central-pattaya' => 5, 'north-pattaya' => 4, 'jomtien' => 3],
            'G' => ['central-pattaya' => 5, 'south-pattaya' => 4, 'north-pattaya' => 3],
            'H' => ['central-pattaya' => 4, 'jomtien' => 4, 'naklua' => 3, 'koh-larn' => 3, 'pratumnak' => 3],
        ];

        // ── Supported languages ──
        $languages = ['th', 'en', 'zh', 'ja', 'ko', 'ru'];

        // ── i18n name templates per language ──
        $langPrefix = [
            'zh' => ['A' => '美食路线', 'B' => '住宿之旅', 'C' => '娱乐夜生活', 'D' => '水疗养生', 'E' => '交通旅游', 'F' => '会展活动', 'G' => '购物生活', 'H' => '景点家庭'],
            'ja' => ['A' => 'グルメルート', 'B' => '宿泊の旅', 'C' => 'エンタメナイト', 'D' => 'スパウェルネス', 'E' => 'トランスポートツアー', 'F' => 'イベントMICE', 'G' => 'ショッピング', 'H' => 'アトラクション'],
            'ko' => ['A' => '맛집 루트', 'B' => '숙박 여행', 'C' => '엔터테인먼트', 'D' => '스파 웰니스', 'E' => '교통 투어', 'F' => '이벤트 MICE', 'G' => '쇼핑 라이프', 'H' => '관광 가족'],
            'ru' => ['A' => 'Гастро-маршрут', 'B' => 'Проживание', 'C' => 'Развлечения', 'D' => 'Спа и здоровье', 'E' => 'Транспорт и туры', 'F' => 'Мероприятия MICE', 'G' => 'Шопинг', 'H' => 'Достопримечательности'],
        ];

        $journeyList = $journeys->values()->toArray();
        $journeyCodes = collect($journeyList)->pluck('journey_code')->toArray();

        foreach ($journeyList as $journey) {
            $jId = $journey->journey_id ?? $journey->id;
            $code = $journey->journey_code;
            $group = $journey->group_code;
            $nameEn = $journey->journey_name_en;
            $nameTh = $journey->journey_name_th;
            $spend = $journey->est_spend_thb ?? 0;

            // ── 1. journey_i18n ──
            foreach ($languages as $lang) {
                $name = match ($lang) {
                    'th' => $nameTh,
                    'en' => $nameEn,
                    'zh' => ($langPrefix['zh'][$group] ?? '旅程') . ' ' . $code,
                    'ja' => ($langPrefix['ja'][$group] ?? 'ジャーニー') . ' ' . $code,
                    'ko' => ($langPrefix['ko'][$group] ?? '여행') . ' ' . $code,
                    'ru' => ($langPrefix['ru'][$group] ?? 'Маршрут') . ' ' . $code,
                    default => $nameEn,
                };

                DB::table('journey_i18n')->updateOrInsert(
                    ['journey_id' => $jId, 'lang' => $lang],
                    ['name' => $name, 'description' => null, 'created_at' => now(), 'updated_at' => now()]
                );
            }

            // ── 2. journey_tag ──
            $tags = $groupTags[$group] ?? ['general'];

            // Add extra tags based on spend/tone
            if ($spend <= 1000) {
                $tags = array_merge($tags, $budgetTags);
            }
            if ($spend >= 4000) {
                $tags = array_merge($tags, $premiumTags);
            }
            if (str_contains(strtolower($journey->tone ?? ''), 'family')) {
                $tags = array_merge($tags, $familyTags);
            }
            if (str_contains(strtolower($journey->tone ?? ''), 'romantic')) {
                $tags = array_merge($tags, $romanticTags);
            }
            if (str_contains(strtolower($journey->tone ?? ''), 'photo')) {
                $tags = array_merge($tags, $photoTags);
            }

            $tags = array_unique($tags);
            foreach ($tags as $tag) {
                DB::table('journey_tag')->updateOrInsert(
                    ['journey_id' => $jId, 'tag_code' => $tag]
                );
            }

            // ── 3. journey_persona ──
            $personas = $groupPersonas[$group] ?? ['solo', 'couple'];

            // Adjust based on content
            if (str_contains(strtolower($journey->tone ?? ''), 'professional')) {
                $personas[] = 'business';
            }
            $personas = array_unique($personas);

            foreach ($personas as $persona) {
                DB::table('journey_persona')->updateOrInsert(
                    ['journey_id' => $jId, 'persona_code' => $persona]
                );
            }

            // ── 4. journey_market ──
            foreach ($defaultMarkets as $countryCode => $baseFit) {
                // Adjust fit based on group
                $fit = $baseFit;
                if ($group === 'F' && in_array($countryCode, ['JP', 'KR', 'CN'])) {
                    $fit = min(5, $fit + 1); // MICE popular with Asian business travelers
                }
                if ($group === 'C' && in_array($countryCode, ['RU', 'IN'])) {
                    $fit = min(5, $fit + 1); // Nightlife popular
                }
                if ($group === 'D' && in_array($countryCode, ['JP', 'KR'])) {
                    $fit = min(5, $fit + 1); // Spa popular with JP/KR
                }

                DB::table('journey_market')->updateOrInsert(
                    ['journey_id' => $jId, 'country_code' => $countryCode],
                    ['fit_level' => $fit]
                );
            }

            // ── 5. journey_zone ──
            $zones = $groupZones[$group] ?? ['central-pattaya' => 3];

            foreach ($zones as $zone => $fit) {
                DB::table('journey_zone')->updateOrInsert(
                    ['journey_id' => $jId, 'zone_code' => $zone],
                    ['fit_level' => $fit]
                );
            }

            // ── 6. journey_next5 ──
            // Recommend journeys from same group first, then adjacent groups
            $sameGroup = collect($journeyList)
                ->where('group_code', $group)
                ->where('journey_code', '!=', $code)
                ->pluck('journey_code')
                ->shuffle()
                ->take(3)
                ->values();

            $adjacentGroups = [
                'A' => ['G', 'C'], 'B' => ['D', 'A'], 'C' => ['A', 'G'],
                'D' => ['B', 'A'], 'E' => ['A', 'H'], 'F' => ['B', 'G'],
                'G' => ['A', 'H'], 'H' => ['E', 'G'],
            ];

            $otherGroup = collect($journeyList)
                ->whereIn('group_code', $adjacentGroups[$group] ?? [])
                ->pluck('journey_code')
                ->shuffle()
                ->take(2)
                ->values();

            $nextCodes = $sameGroup->merge($otherGroup)->take(5)->values();

            foreach ($nextCodes as $rank => $nextCode) {
                DB::table('journey_next5')->updateOrInsert(
                    ['journey_id' => $jId, 'next_rank' => $rank + 1],
                    ['next_journey_code' => $nextCode]
                );
            }
        }

        $this->command->info('Journey metadata seeded: i18n, tags, personas, markets, zones, next5');
    }
}
