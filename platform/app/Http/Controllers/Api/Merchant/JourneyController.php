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
     *
     * Public one-call (no user_id) → full journey + merchants + stats.
     * With ?user_id=123 → adds user_state per merchant.
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
     *
     * Normalized merchant rows for a journey.
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
     *
     * Journey merchants with user context.
     */
    public function journeyMerchants(Request $request, string $journeyCode): JsonResponse
    {
        $userId = $request->query('user_id');
        $lang = $request->query('lang', 'th');

        if ($userId) {
            $data = $this->merchantService->journeyOneCallUser($journeyCode, (int) $userId);
            if (! $data) {
                return response()->json(['error' => 'Journey not found'], 404);
            }

            return response()->json([
                'data' => [
                    'journey_code' => $journeyCode,
                    'user_id' => (int) $userId,
                    'merchants' => $data['merchants_json_user'],
                ],
            ]);
        }

        $rows = $this->merchantService->merchantsByJourney($journeyCode);

        return response()->json([
            'data' => [
                'journey_code' => $journeyCode,
                'merchants' => $rows,
            ],
        ]);
    }

    /**
     * GET /api/journeys/{journey_code}/merchant-stats
     *
     * Summary KPIs per journey.
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
