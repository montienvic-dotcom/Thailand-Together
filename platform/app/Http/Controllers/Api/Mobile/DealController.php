<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DealController extends Controller
{
    /**
     * GET /api/mobile/deals
     * Browse active deals/promotions in the tourist's cluster.
     */
    public function index(Request $request): JsonResponse
    {
        $clusterId = (int) $request->header('X-Cluster-Id', 1);
        $limit = min((int) $request->input('limit', 20), 100);
        $offset = max((int) $request->input('offset', 0), 0);

        $query = DB::table('deals')
            ->where('cluster_id', $clusterId)
            ->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('start_date')->orWhere('start_date', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', now());
            });

        if ($category = $request->input('category')) {
            $query->where('category', $category);
        }

        if ($merchantId = $request->input('merchant_id')) {
            $query->where('merchant_id', (int) $merchantId);
        }

        if ($q = $request->input('q')) {
            $term = '%' . $q . '%';
            $query->where(function ($qb) use ($term) {
                $qb->where('title_en', 'LIKE', $term)
                    ->orWhere('title_th', 'LIKE', $term)
                    ->orWhere('description_en', 'LIKE', $term);
            });
        }

        $total = (clone $query)->count();

        $deals = $query
            ->orderByDesc('priority')
            ->orderByDesc('created_at')
            ->limit($limit)
            ->offset($offset)
            ->get();

        return response()->json([
            'data' => $deals,
            'meta' => ['total' => $total, 'limit' => $limit, 'offset' => $offset],
        ]);
    }

    /**
     * GET /api/mobile/deals/{id}
     * Deal detail.
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $deal = DB::table('deals')
            ->where('id', $id)
            ->where('is_active', true)
            ->first();

        if (! $deal) {
            return response()->json(['error' => 'Deal not found'], 404);
        }

        // Check if user has redeemed this deal
        $user = $request->user();
        $redeemed = DB::table('deal_redemptions')
            ->where('deal_id', $id)
            ->where('user_id', $user->id)
            ->exists();

        return response()->json([
            'data' => [
                'deal' => $deal,
                'is_redeemed' => $redeemed,
            ],
        ]);
    }

    /**
     * POST /api/mobile/deals/{id}/redeem
     * Redeem a deal/promotion.
     */
    public function redeem(Request $request, int $id): JsonResponse
    {
        $deal = DB::table('deals')
            ->where('id', $id)
            ->where('is_active', true)
            ->first();

        if (! $deal) {
            return response()->json(['error' => 'Deal not found'], 404);
        }

        $user = $request->user();

        // Check if already redeemed
        $alreadyRedeemed = DB::table('deal_redemptions')
            ->where('deal_id', $id)
            ->where('user_id', $user->id)
            ->exists();

        if ($alreadyRedeemed) {
            return response()->json(['error' => 'Deal already redeemed'], 422);
        }

        // Check redemption limit
        if ($deal->max_redemptions > 0) {
            $totalRedemptions = DB::table('deal_redemptions')
                ->where('deal_id', $id)
                ->count();

            if ($totalRedemptions >= $deal->max_redemptions) {
                return response()->json(['error' => 'Deal redemption limit reached'], 422);
            }
        }

        DB::table('deal_redemptions')->insert([
            'deal_id' => $id,
            'user_id' => $user->id,
            'redeemed_at' => now(),
            'created_at' => now(),
        ]);

        return response()->json([
            'message' => 'Deal redeemed successfully',
            'data' => ['deal_id' => $id, 'redeemed_at' => now()->toIso8601String()],
        ], 201);
    }
}
