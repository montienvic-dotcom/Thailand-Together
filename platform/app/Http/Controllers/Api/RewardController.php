<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CrossCluster\RewardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RewardController extends Controller
{
    public function __construct(
        private RewardService $rewardService,
    ) {}

    /**
     * GET /api/rewards/balance
     * Get user's balance across all clusters.
     */
    public function balance(Request $request): JsonResponse
    {
        $balances = $this->rewardService->getTotalBalance($request->user()->id);

        return response()->json(['data' => $balances]);
    }

    /**
     * POST /api/rewards/earn
     */
    public function earn(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'cluster_id' => 'required|exists:clusters,id',
            'amount' => 'required|numeric|min:1',
            'reference_type' => 'required|string|in:checkin,review,booking,referral,campaign',
            'reference_id' => 'nullable|integer',
            'description' => 'nullable|string|max:500',
        ]);

        $success = $this->rewardService->earn(
            $request->user()->id,
            $validated['cluster_id'],
            $validated['amount'],
            $validated['reference_type'],
            $validated['reference_id'] ?? null,
            $validated['description'] ?? null
        );

        return $success
            ? response()->json(['message' => 'Points earned successfully.'])
            : response()->json(['message' => 'Failed to earn points.'], 422);
    }

    /**
     * POST /api/rewards/redeem
     */
    public function redeem(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'cluster_id' => 'required|exists:clusters,id',
            'amount' => 'required|numeric|min:1',
            'reference_type' => 'required|string|in:discount,voucher,upgrade,experience',
            'reference_id' => 'nullable|integer',
            'description' => 'nullable|string|max:500',
        ]);

        $success = $this->rewardService->redeem(
            $request->user()->id,
            $validated['cluster_id'],
            $validated['amount'],
            $validated['reference_type'],
            $validated['reference_id'] ?? null,
            $validated['description'] ?? null
        );

        return $success
            ? response()->json(['message' => 'Points redeemed successfully.'])
            : response()->json(['message' => 'Insufficient balance.'], 422);
    }

    /**
     * POST /api/rewards/transfer
     * Transfer points between clusters with exchange rate.
     */
    public function transfer(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'from_cluster_id' => 'required|exists:clusters,id',
            'to_cluster_id' => 'required|exists:clusters,id|different:from_cluster_id',
            'amount' => 'required|numeric|min:1',
        ]);

        $rate = $this->rewardService->getExchangeRate(
            $validated['from_cluster_id'],
            $validated['to_cluster_id']
        );

        if ($rate === null) {
            return response()->json(['message' => 'No exchange rate configured for these clusters.'], 422);
        }

        $success = $this->rewardService->transferBetweenClusters(
            $request->user()->id,
            $validated['from_cluster_id'],
            $validated['to_cluster_id'],
            $validated['amount']
        );

        return $success
            ? response()->json([
                'message' => 'Points transferred successfully.',
                'exchange_rate' => $rate,
                'converted_amount' => $validated['amount'] * $rate,
            ])
            : response()->json(['message' => 'Insufficient balance or transfer failed.'], 422);
    }

    /**
     * GET /api/rewards/exchange-rates
     * Get all active exchange rates.
     */
    public function exchangeRates(): JsonResponse
    {
        $rates = \Illuminate\Support\Facades\DB::table('reward_exchange_rates')
            ->where('is_active', true)
            ->join('clusters as from_c', 'reward_exchange_rates.from_cluster_id', '=', 'from_c.id')
            ->join('clusters as to_c', 'reward_exchange_rates.to_cluster_id', '=', 'to_c.id')
            ->select(
                'reward_exchange_rates.id',
                'from_c.name as from_cluster',
                'to_c.name as to_cluster',
                'reward_exchange_rates.rate',
                'reward_exchange_rates.from_cluster_id',
                'reward_exchange_rates.to_cluster_id'
            )
            ->get();

        return response()->json(['data' => $rates]);
    }
}
