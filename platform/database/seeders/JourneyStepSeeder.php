<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JourneyStepSeeder extends Seeder
{
    public function run(): void
    {
        // Map journey_code → [place_codes in order] from playbook highlights
        $journeySteps = [
            'A1'  => ['PLACE_LOCAL_BREAKFAST', 'PLACE_THEPPRASIT_NIGHT_MARKET', 'PLACE_CENTRAL_FESTIVAL'],
            'A2'  => ['PLACE_LAN_PHO_NAKLUA', 'PLACE_CAFE_STOP', 'PLACE_PUPEN_SEAFOOD'],
            'A3'  => ['PLACE_SKY_GALLERY', 'PLACE_CAFE_STOP', 'PLACE_PARTNER_DINNER'],
            'A4'  => ['PLACE_FLOATING_MARKET', 'PLACE_PARTNER_MEAL', 'PLACE_CENTRAL_FESTIVAL'],
            'A5'  => ['PLACE_LOCAL_BREAKFAST', 'PLACE_LOCAL_LUNCH_STREET', 'PLACE_CAFE_STOP'],
            'A6'  => ['PLACE_GLASS_HOUSE', 'PLACE_HORIZON_ROOFTOP', 'PLACE_CHOCOLATE_FACTORY'],
            'A7'  => ['PLACE_PARTNER_MEAL', 'PLACE_PARTNER_DINNER', 'PLACE_CHOCOLATE_FACTORY'],
            'A8'  => ['PLACE_CAFE_STOP', 'PLACE_CHOCOLATE_FACTORY', 'PLACE_ROYAL_GARDEN'],
            'A9'  => ['PLACE_THEPPRASIT_NIGHT_MARKET', 'PLACE_PATTAYA_NIGHT_BAZAAR', 'PLACE_TERMINAL21_PIER21'],
            'A10' => ['PLACE_TERMINAL21_PIER21', 'PLACE_CAFE_STOP', 'PLACE_PARTNER_DINNER'],

            'B1'  => ['PLACE_MIDRANGE_HOTEL', 'PLACE_CAFE_STOP', 'PLACE_COWORKING'],
            'B2'  => ['PLACE_FAMILY_RESORT', 'PLACE_PARTNER_MEAL', 'PLACE_CHOCOLATE_FACTORY'],
            'B3'  => ['PLACE_LUXURY_HOTEL', 'PLACE_HOTEL_SPA', 'PLACE_HORIZON_ROOFTOP'],
            'B4'  => ['PLACE_BUDGET_HOTEL', 'PLACE_TERMINAL21_PIER21', 'PLACE_THEPPRASIT_NIGHT_MARKET'],
            'B5'  => ['PLACE_MIDRANGE_HOTEL', 'PLACE_HOTEL_SPA', 'PLACE_HEALTHY_CAFE'],
            'B6'  => ['PLACE_MIDRANGE_HOTEL', 'PLACE_CENTRAL_FESTIVAL', 'PLACE_ROYAL_GARDEN'],
            'B7'  => ['PLACE_LUXURY_HOTEL', 'PLACE_HOTEL_SPA', 'PLACE_HORIZON_ROOFTOP'],
            'B8'  => ['PLACE_MIDRANGE_HOTEL', 'PLACE_CAFE_STOP', 'PLACE_COWORKING'],
            'B9'  => ['PLACE_BUDGET_HOTEL', 'PLACE_HARBOR_PATTAYA', 'PLACE_TERMINAL21_PIER21'],
            'B10' => ['PLACE_MIDRANGE_HOTEL', 'PLACE_CITY_PASS', 'PLACE_CENTRAL_FESTIVAL'],

            'C1'  => ['PLACE_PARTNER_MEAL', 'PLACE_TIFFANY_SHOW', 'PLACE_THEPPRASIT_NIGHT_MARKET'],
            'C2'  => ['PLACE_CAFE_STOP', 'PLACE_ALCAZAR_SHOW', 'PLACE_HORIZON_ROOFTOP'],
            'C3'  => ['PLACE_WALKING_STREET', 'PLACE_CHOCOLATE_FACTORY'],
            'C4'  => ['PLACE_TERMINAL21_PIER21', 'PLACE_TREE_TOWN', 'PLACE_PATTAYA_NIGHT_BAZAAR'],
            'C5'  => ['PLACE_HARBOR_PATTAYA', 'PLACE_BOWLING_ZONE', 'PLACE_TIFFANY_SHOW'],
            'C6'  => ['PLACE_LETS_RELAX_SPA', 'PLACE_PARTNER_MEAL', 'PLACE_TIFFANY_SHOW'],
            'C7'  => ['PLACE_BOWLING_ZONE', 'PLACE_HARBOR_PATTAYA', 'PLACE_JOMTIEN_NIGHT_MARKET'],
            'C8'  => ['PLACE_PATTAYA_NIGHT_BAZAAR', 'PLACE_THEPPRASIT_NIGHT_MARKET', 'PLACE_JOMTIEN_NIGHT_MARKET'],
            'C9'  => ['PLACE_SUNSET_YACHT', 'PLACE_SKY_GALLERY', 'PLACE_CHOCOLATE_FACTORY'],
            'C10' => ['PLACE_VAN_GUIDE', 'PLACE_NONG_NOOCH', 'PLACE_ALCAZAR_SHOW'],

            'D1'  => ['PLACE_SUNRISE_YOGA', 'PLACE_HEALTHY_CAFE', 'PLACE_LETS_RELAX_SPA'],
            'D2'  => ['PLACE_LUXURY_SPA', 'PLACE_DETOX_JUICE', 'PLACE_HORIZON_ROOFTOP'],
            'D3'  => ['PLACE_DETOX_JUICE', 'PLACE_SUNRISE_YOGA', 'PLACE_HEALTH_LAND'],
            'D4'  => ['PLACE_WELLNESS_CLINIC', 'PLACE_HEALTHY_CAFE', 'PLACE_LETS_RELAX_SPA'],
            'D5'  => ['PLACE_HARBOR_PATTAYA', 'PLACE_HEALTHY_CAFE', 'PLACE_THEPPRASIT_NIGHT_MARKET'],
            'D6'  => ['PLACE_CAFE_STOP', 'PLACE_LETS_RELAX_SPA', 'PLACE_DETOX_JUICE'],
            'D7'  => ['PLACE_LUXURY_SPA', 'PLACE_DETOX_JUICE', 'PLACE_HORIZON_ROOFTOP'],
            'D8'  => ['PLACE_MEDITATION_GARDEN', 'PLACE_HEALTHY_CAFE', 'PLACE_CHOCOLATE_FACTORY'],
            'D9'  => ['PLACE_FITNESS_BOOTCAMP', 'PLACE_ICE_BATH', 'PLACE_HEALTHY_CAFE'],
            'D10' => ['PLACE_LETS_RELAX_SPA', 'PLACE_PARTNER_MEAL', 'PLACE_TIFFANY_SHOW'],

            'E1'  => ['PLACE_THEPPRASIT_NIGHT_MARKET', 'PLACE_PARTNER_DINNER'],
            'E2'  => ['PLACE_BALI_HAI_PIER', 'PLACE_KOH_LARN', 'PLACE_NAKLUA_SEAFOOD'],
            'E3'  => ['PLACE_WATER_SPORTS', 'PLACE_PARTNER_MEAL'],
            'E4'  => ['PLACE_BIKE_ROUTE'],
            'E5'  => ['PLACE_BALI_HAI_PIER', 'PLACE_HARBOR_PATTAYA', 'PLACE_PARTNER_DINNER'],
            'E6'  => ['PLACE_VAN_GUIDE', 'PLACE_NONG_NOOCH', 'PLACE_PARTNER_MEAL'],
            'E7'  => ['PLACE_VAN_GUIDE', 'PLACE_PUPEN_SEAFOOD', 'PLACE_LAN_PHO_NAKLUA'],
            'E8'  => ['PLACE_SCOOTER_RENTAL', 'PLACE_CAFE_STOP', 'PLACE_TERMINAL21_PIER21'],
            'E9'  => ['PLACE_FULL_DAY_YACHT', 'PLACE_KOH_LARN', 'PLACE_NAKLUA_SEAFOOD'],
            'E10' => ['PLACE_BALI_HAI_PIER', 'PLACE_BIKE_ROUTE'],

            'F1'  => ['PLACE_PEACH', 'PLACE_ROYAL_CLIFF', 'PLACE_PARTNER_MEAL'],
            'F2'  => ['PLACE_CENTRAL_FESTIVAL', 'PLACE_PARTNER_MEAL', 'PLACE_TERMINAL21_PIER21'],
            'F3'  => ['PLACE_PARTNER_MEAL', 'PLACE_TERMINAL21_PIER21', 'PLACE_THEPPRASIT_NIGHT_MARKET'],
            'F4'  => ['PLACE_TERMINAL21_PIER21', 'PLACE_THEPPRASIT_NIGHT_MARKET', 'PLACE_CHOCOLATE_FACTORY'],
            'F5'  => ['PLACE_HEALTHY_CAFE', 'PLACE_LETS_RELAX_SPA', 'PLACE_PARTNER_MEAL'],
            'F6'  => ['PLACE_PARTNER_MEAL', 'PLACE_TIFFANY_SHOW', 'PLACE_CAFE_STOP'],
            'F7'  => ['PLACE_PEACH', 'PLACE_ROYAL_CLIFF', 'PLACE_PARTNER_MEAL'],
            'F8'  => ['PLACE_SKY_GALLERY', 'PLACE_PARTNER_DINNER'],
            'F9'  => ['PLACE_PARTNER_MEAL', 'PLACE_PARTNER_DINNER', 'PLACE_CAFE_STOP'],
            'F10' => ['PLACE_PEACH', 'PLACE_ROYAL_CLIFF', 'PLACE_PARTNER_DINNER'],

            'G1'  => ['PLACE_TIFFANY_SHOW', 'PLACE_BEACH_ROAD'],
            'G2'  => ['PLACE_TERMINAL21_PIER21', 'PLACE_CENTRAL_FESTIVAL', 'PLACE_3MERMAIDS'],
            'G3'  => ['PLACE_VIEWPOINT', 'PLACE_ART_IN_PARADISE', 'PLACE_ALCAZAR_SHOW'],
            'G4'  => ['PLACE_THEPPRASIT_NIGHT_MARKET', 'PLACE_CENTRAL_MARINA', 'PLACE_WALKING_STREET'],
            'G5'  => ['PLACE_TERMINAL21_PIER21', 'PLACE_TIFFANY_SHOW', 'PLACE_THEPPRASIT_NIGHT_MARKET'],
            'G6'  => ['PLACE_FROST_ICE', 'PLACE_3MERMAIDS', 'PLACE_ALCAZAR_SHOW'],
            'G7'  => ['PLACE_CENTRAL_FESTIVAL', 'PLACE_THEPPRASIT_NIGHT_MARKET', 'PLACE_WALKING_STREET'],
            'G8'  => ['PLACE_TERMINAL21_PIER21', 'PLACE_CENTRAL_FESTIVAL', 'PLACE_VIEWPOINT'],
            'G9'  => ['PLACE_CENTRAL_MARINA', 'PLACE_TERMINAL21_PIER21', 'PLACE_SHELL_TANGKE'],
            'G10' => ['PLACE_SANCTUARY_TRUTH', 'PLACE_FLOATING_MARKET', 'PLACE_3MERMAIDS'],

            'H1'  => ['PLACE_FLOATING_MARKET', 'PLACE_TERMINAL21_PIER21', 'PLACE_JOMTIEN_NIGHT_MARKET'],
            'H2'  => ['PLACE_NONG_NOOCH', 'PLACE_VIEWPOINT', 'PLACE_3MERMAIDS'],
            'H3'  => ['PLACE_ART_IN_PARADISE', 'PLACE_TERMINAL21_PIER21', 'PLACE_THEPPRASIT_NIGHT_MARKET'],
            'H4'  => ['PLACE_ART_IN_PARADISE', 'PLACE_3MERMAIDS', 'PLACE_JOMTIEN_NIGHT_MARKET'],
            'H5'  => ['PLACE_NONG_NOOCH', 'PLACE_3MERMAIDS', 'PLACE_TERMINAL21_PIER21'],
            'H6'  => ['PLACE_SANCTUARY_TRUTH', 'PLACE_CENTRAL_FESTIVAL', 'PLACE_TERMINAL21_PIER21'],
            'H7'  => ['PLACE_FROST_ICE', 'PLACE_3MERMAIDS', 'PLACE_TERMINAL21_PIER21'],
            'H8'  => ['PLACE_BEACH_ROAD', 'PLACE_3MERMAIDS', 'PLACE_JOMTIEN_NIGHT_MARKET'],
            'H9'  => ['PLACE_FLOATING_MARKET', 'PLACE_TERMINAL21_PIER21', 'PLACE_VIEWPOINT'],
            'H10' => ['PLACE_FLOATING_MARKET', 'PLACE_TERMINAL21_PIER21', 'PLACE_CENTRAL_FESTIVAL'],
        ];

        // Duration distribution per step (approximate split from total)
        foreach ($journeySteps as $journeyCode => $placeCodes) {
            $journey = DB::table('journey')->where('journey_code', $journeyCode)->first();
            if (!$journey) {
                continue;
            }

            $stepCount = count($placeCodes);
            $durationPerStep = $stepCount > 0 ? intval($journey->duration_min / $stepCount) : 0;
            $tpPerStep = $stepCount > 0 ? intval($journey->tp_normal / $stepCount) : 0;

            foreach ($placeCodes as $idx => $placeCode) {
                $placeId = DB::table('place')->where('place_code', $placeCode)->value('id');
                if (!$placeId) {
                    continue;
                }

                DB::table('journey_step')->updateOrInsert(
                    [
                        'journey_id' => $journey->id,
                        'step_no'    => $idx + 1,
                    ],
                    [
                        'place_id'     => $placeId,
                        'duration_min' => $durationPerStep,
                        'tp_normal'    => $tpPerStep,
                        'tp_goal'      => intval($journey->tp_goal / $stepCount),
                        'tp_special'   => intval($journey->tp_special / $stepCount),
                        'note'         => null,
                        'created_at'   => now(),
                        'updated_at'   => now(),
                    ]
                );
            }
        }
    }
}
