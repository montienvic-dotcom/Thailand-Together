<?php

namespace App\Http\Controllers\Api\Merchant;

use App\Http\Controllers\Controller;
use App\Services\Merchant\MerchantService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class JourneyController extends Controller
{
    public function __construct(
        private MerchantService $merchantService,
    ) {}

    /**
     * GET /api/journeys/{journey_code}/onecall/final
     * Uses view: vw_api_journey_onecall_with_merchants_stats_final (public)
     *            vw_api_journey_onecall_with_merchants_user (with ?user_id=)
     */
    public function oneCallFinal(Request $request, string $journeyCode): JsonResponse
    {
        $userId = $request->query('user_id');

        if ($userId) {
            $data = $this->merchantService->journeyOneCallUser($journeyCode, (int) $userId);
        } else {
            $data = $this->merchantService->journeyOneCallPublic($journeyCode);
        }

        if (! $data) {
            return response()->json(['error' => 'Journey not found'], 404);
        }

        return response()->json(['data' => $data]);
    }

    /**
     * GET /api/journeys/{journey_code}/merchants/rows
     * Uses view: vw_merchant_search_public
     */
    public function merchantRows(string $journeyCode): JsonResponse
    {
        $rows = $this->merchantService->merchantsByJourney($journeyCode);

        return response()->json([
            'data' => $rows,
            'meta' => ['total' => $rows->count()],
        ]);
    }

    /**
     * GET /api/journeys/{journey_code}/merchants
     * Uses view: vw_journey_place_merchant_json / vw_journey_merchant_json_user
     */
    public function journeyMerchants(Request $request, string $journeyCode): JsonResponse
    {
        $userId = $request->query('user_id');

        if ($userId) {
            $row = $this->merchantService->journeyMerchantsJsonUser($journeyCode, (int) $userId);
            if (! $row) {
                return response()->json(['error' => 'Journey not found'], 404);
            }

            $merchants = is_string($row->merchants_json) ? json_decode($row->merchants_json, true) : $row->merchants_json;

            return response()->json([
                'data' => [
                    'journey_code' => $journeyCode,
                    'user_id' => (int) $userId,
                    'merchants' => $merchants,
                ],
            ]);
        }

        $row = $this->merchantService->journeyMerchantsJson($journeyCode);
        if (! $row) {
            return response()->json(['error' => 'Journey not found'], 404);
        }

        $merchants = is_string($row->merchants_json) ? json_decode($row->merchants_json, true) : $row->merchants_json;

        return response()->json([
            'data' => [
                'journey_code' => $journeyCode,
                'merchants' => $merchants,
            ],
        ]);
    }

    /**
     * GET /api/journeys/{journey_code}/merchant-stats
     * Uses view: vw_journey_merchant_stats
     */
    public function merchantStats(string $journeyCode): JsonResponse
    {
        $stats = $this->merchantService->journeyMerchantStats($journeyCode);

        if (! $stats) {
            return response()->json(['error' => 'Journey not found'], 404);
        }

        return response()->json(['data' => $stats]);
    }
}
