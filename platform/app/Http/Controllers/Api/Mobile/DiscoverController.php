<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Services\CrossCluster\RecommendationService;
use App\Services\Merchant\MerchantService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DiscoverController extends Controller
{
    public function __construct(
        private MerchantService $merchantService,
        private RecommendationService $recommendationService,
    ) {}

    /**
     * GET /api/mobile/discover/home
     * Aggregated home screen data for the tourist app.
     */
    public function home(Request $request): JsonResponse
    {
        $clusterId = (int) $request->header('X-Cluster-Id', 1);
        $user = $request->user();

        // Featured journeys (top 6)
        $featuredJourneys = DB::table('journey')
            ->where('cluster_id', $clusterId)
            ->whereIn('status', ['active', 'ACTIVE'])
            ->select('journey_code', 'journey_name_en', 'journey_name_th', 'journey_group', 'total_minutes_sum', 'gmv_per_person')
            ->orderByDesc('tp_total_normal')
            ->limit(6)
            ->get();

        // Popular merchants (top 8)
        $popularMerchants = DB::table('merchant')
            ->where('cluster_id', $clusterId)
            ->where('is_active', true)
            ->select('merchant_id', 'merchant_code', 'merchant_name_en', 'merchant_name_th', 'default_tier_code', 'price_level', 'lat', 'lng')
            ->orderByDesc('merchant_id')
            ->limit(8)
            ->get();

        // Active deals count
        $dealsCount = DB::table('deals')
            ->where('cluster_id', $clusterId)
            ->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('start_date')->orWhere('start_date', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', now());
            })
            ->count();

        // Cross-cluster recommendations
        $recommendations = $this->recommendationService->getRecommendations(
            $user->id,
            $clusterId,
            3
        );

        return response()->json([
            'data' => [
                'featured_journeys' => $featuredJourneys,
                'popular_merchants' => $popularMerchants,
                'deals_count' => $dealsCount,
                'cross_cluster_recommendations' => $recommendations,
            ],
        ]);
    }

    /**
     * GET /api/mobile/discover/journeys
     * Browse journeys with filters for the mobile app.
     */
    public function journeys(Request $request): JsonResponse
    {
        $clusterId = (int) $request->header('X-Cluster-Id', 1);
        $limit = min((int) $request->input('limit', 20), 100);
        $offset = max((int) $request->input('offset', 0), 0);

        $query = DB::table('journey')
            ->where('cluster_id', $clusterId)
            ->whereIn('status', ['active', 'ACTIVE']);

        if ($q = $request->input('q')) {
            $term = '%' . $q . '%';
            $query->where(function ($qb) use ($term) {
                $qb->where('journey_name_en', 'LIKE', $term)
                    ->orWhere('journey_name_th', 'LIKE', $term)
                    ->orWhere('journey_code', 'LIKE', $term);
            });
        }

        if ($group = $request->input('group')) {
            $query->where('journey_group', $group);
        }

        if ($request->has('min_duration')) {
            $query->where('total_minutes_sum', '>=', (int) $request->input('min_duration'));
        }
        if ($request->has('max_duration')) {
            $query->where('total_minutes_sum', '<=', (int) $request->input('max_duration'));
        }

        if ($request->has('max_budget')) {
            $query->where('gmv_per_person', '<=', (float) $request->input('max_budget'));
        }

        $sortBy = $request->input('sort', 'popular');
        $query = match ($sortBy) {
            'newest' => $query->orderByDesc('created_at'),
            'duration' => $query->orderBy('total_minutes_sum'),
            'budget_low' => $query->orderBy('gmv_per_person'),
            'budget_high' => $query->orderByDesc('gmv_per_person'),
            default => $query->orderByDesc('tp_total_normal'), // popular
        };

        $total = (clone $query)->count();
        $journeys = $query
            ->select('journey_code', 'journey_name_en', 'journey_name_th', 'journey_group', 'total_minutes_sum', 'gmv_per_person', 'tp_total_normal', 'created_at')
            ->limit($limit)
            ->offset($offset)
            ->get();

        return response()->json([
            'data' => $journeys,
            'meta' => ['total' => $total, 'limit' => $limit, 'offset' => $offset],
        ]);
    }

    /**
     * GET /api/mobile/discover/journeys/{code}
     * Journey detail with merchants for mobile app.
     */
    public function journeyDetail(Request $request, string $journeyCode): JsonResponse
    {
        // Try view-based one-call first, fall back to basic query
        $journey = DB::table('journey')
            ->where('journey_code', $journeyCode)
            ->first();

        if (! $journey) {
            return response()->json(['error' => 'Journey not found'], 404);
        }

        return response()->json(['data' => $journey]);
    }

    /**
     * GET /api/mobile/discover/merchants
     * Browse merchants with filters for the mobile app.
     */
    public function merchants(Request $request): JsonResponse
    {
        $clusterId = (int) $request->header('X-Cluster-Id', 1);
        $limit = min((int) $request->input('limit', 20), 100);
        $offset = max((int) $request->input('offset', 0), 0);

        $query = DB::table('merchant')
            ->where('cluster_id', $clusterId)
            ->where('is_active', true);

        if ($q = $request->input('q')) {
            $term = '%' . $q . '%';
            $query->where(function ($qb) use ($term) {
                $qb->where('merchant_name_en', 'LIKE', $term)
                    ->orWhere('merchant_name_th', 'LIKE', $term)
                    ->orWhere('service_tags', 'LIKE', $term);
            });
        }

        if ($tier = $request->input('tier')) {
            $query->where('default_tier_code', $tier);
        }

        if ($priceLevel = $request->input('price_level')) {
            $query->where('price_level', (int) $priceLevel);
        }

        $sortBy = $request->input('sort', 'name');
        $query = match ($sortBy) {
            'price_low' => $query->orderBy('price_level'),
            'price_high' => $query->orderByDesc('price_level'),
            'newest' => $query->orderByDesc('created_at'),
            default => $query->orderBy('merchant_name_en'),
        };

        $total = (clone $query)->count();
        $merchants = $query
            ->select('merchant_id', 'merchant_code', 'merchant_name_en', 'merchant_name_th', 'default_tier_code', 'price_level', 'lat', 'lng', 'service_tags', 'phone')
            ->limit($limit)
            ->offset($offset)
            ->get();

        return response()->json([
            'data' => $merchants,
            'meta' => ['total' => $total, 'limit' => $limit, 'offset' => $offset],
        ]);
    }

    /**
     * GET /api/mobile/discover/merchants/{id}
     * Merchant detail page for mobile app.
     */
    public function merchantDetail(Request $request, int $merchantId): JsonResponse
    {
        $merchant = DB::table('merchant')
            ->where('merchant_id', $merchantId)
            ->first();

        if (! $merchant) {
            return response()->json(['error' => 'Merchant not found'], 404);
        }

        // Recent reviews
        $reviews = $this->merchantService->merchantReviews($merchantId, 5, 0);

        return response()->json([
            'data' => [
                'merchant' => $merchant,
                'recent_reviews' => $reviews,
            ],
        ]);
    }

    /**
     * GET /api/mobile/discover/merchants/nearby
     * Geo-search nearby merchants within radius.
     */
    public function nearbyMerchants(Request $request): JsonResponse
    {
        $request->validate([
            'lat' => 'required|numeric|between:-90,90',
            'lng' => 'required|numeric|between:-180,180',
            'radius' => 'sometimes|numeric|min:0.1|max:50', // km
        ]);

        $lat = (float) $request->input('lat');
        $lng = (float) $request->input('lng');
        $radius = (float) $request->input('radius', 5); // default 5km
        $limit = min((int) $request->input('limit', 20), 100);

        // Simple distance filter using bounding box + precise calculation
        $latDelta = $radius / 111.0; // ~111km per degree lat
        $lngDelta = $radius / (111.0 * cos(deg2rad($lat)));

        $merchants = DB::table('merchant')
            ->where('is_active', true)
            ->whereNotNull('lat')
            ->whereNotNull('lng')
            ->whereBetween('lat', [$lat - $latDelta, $lat + $latDelta])
            ->whereBetween('lng', [$lng - $lngDelta, $lng + $lngDelta])
            ->select('merchant_id', 'merchant_code', 'merchant_name_en', 'merchant_name_th', 'default_tier_code', 'price_level', 'lat', 'lng', 'service_tags')
            ->limit($limit)
            ->get();

        return response()->json([
            'data' => $merchants,
            'meta' => [
                'center' => ['lat' => $lat, 'lng' => $lng],
                'radius_km' => $radius,
                'count' => $merchants->count(),
            ],
        ]);
    }

    /**
     * GET /api/mobile/discover/recommendations
     * Personalized recommendations for the authenticated user.
     */
    public function recommendations(Request $request): JsonResponse
    {
        $user = $request->user();
        $clusterId = (int) $request->header('X-Cluster-Id', 1);
        $limit = min((int) $request->input('limit', 10), 50);

        // Cross-cluster recommendations
        $crossCluster = $this->recommendationService->getRecommendations(
            $user->id,
            $clusterId,
            $limit
        );

        // Popular journeys from other clusters
        $otherClusterJourneys = $this->recommendationService->getPopularJourneysFromOtherClusters(
            $clusterId,
            5
        );

        // Active campaigns
        $campaigns = $this->recommendationService->getActiveCampaigns($clusterId);

        return response()->json([
            'data' => [
                'cross_cluster' => $crossCluster,
                'explore_other_clusters' => $otherClusterJourneys,
                'campaigns' => $campaigns,
            ],
        ]);
    }
}
