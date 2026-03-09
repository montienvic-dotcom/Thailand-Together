<?php

namespace App\Http\Controllers\Api\Merchant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Merchant\CheckinRequest;
use App\Http\Requests\Merchant\FavoriteToggleRequest;
use App\Http\Requests\Merchant\MerchantSearchRequest;
use App\Http\Requests\Merchant\ReviewRequest;
use App\Http\Requests\Merchant\WishlistToggleRequest;
use App\Services\Merchant\MerchantService;
use Illuminate\Http\JsonResponse;

class MerchantController extends Controller
{
    public function __construct(
        private MerchantService $merchantService,
    ) {}

    /**
     * GET /api/merchants/search
     * Uses view: vw_merchant_search_public + vw_merchant_search_blob_public
     */
    public function search(MerchantSearchRequest $request): JsonResponse
    {
        $result = $this->merchantService->searchPublic($request->validated());

        return response()->json($result);
    }

    /**
     * GET /api/merchants/search/user
     * Uses view: vw_merchant_search_user
     */
    public function searchUser(MerchantSearchRequest $request): JsonResponse
    {
        $filters = $request->validated();
        $userId = (int) $filters['user_id'];

        $result = $this->merchantService->searchUser($userId, $filters);

        return response()->json($result);
    }

    /**
     * GET /api/places/{place_code}/merchants
     * Uses view: vw_merchant_search_public
     */
    public function byPlace(string $placeCode): JsonResponse
    {
        $merchants = $this->merchantService->merchantsByPlace($placeCode);

        return response()->json([
            'data' => $merchants,
            'meta' => ['total' => $merchants->count()],
        ]);
    }

    /**
     * GET /api/merchant/{merchant_id}/reviews
     */
    public function reviews(int $merchantId): JsonResponse
    {
        $limit = min((int) request('limit', 20), 100);
        $offset = max((int) request('offset', 0), 0);

        $reviews = $this->merchantService->merchantReviews($merchantId, $limit, $offset);

        return response()->json([
            'data' => $reviews,
            'meta' => ['limit' => $limit, 'offset' => $offset, 'count' => $reviews->count()],
        ]);
    }

    /**
     * POST /api/merchant/checkin
     */
    public function checkin(CheckinRequest $request): JsonResponse
    {
        $checkin = $this->merchantService->checkin($request->validated());

        return response()->json([
            'data' => $checkin,
            'message' => 'Check-in recorded successfully',
        ], 201);
    }

    /**
     * POST /api/merchant/favorite/toggle
     */
    public function favoriteToggle(FavoriteToggleRequest $request): JsonResponse
    {
        $data = $request->validated();
        $state = $this->merchantService->toggleFavorite(
            (int) $data['user_id'],
            (int) $data['merchant_id'],
            (bool) $data['is_favorite'],
        );

        return response()->json([
            'data' => [
                'user_id' => (int) $data['user_id'],
                'merchant_id' => (int) $data['merchant_id'],
                'is_favorite' => $state ? 1 : 0,
            ],
            'message' => $state ? 'Favorite added' : 'Favorite removed',
        ]);
    }

    /**
     * POST /api/merchant/wishlist/toggle
     */
    public function wishlistToggle(WishlistToggleRequest $request): JsonResponse
    {
        $data = $request->validated();
        $state = $this->merchantService->toggleWishlist(
            (int) $data['user_id'],
            (int) $data['merchant_id'],
            (bool) $data['is_wishlist'],
        );

        return response()->json([
            'data' => [
                'user_id' => (int) $data['user_id'],
                'merchant_id' => (int) $data['merchant_id'],
                'is_wishlist' => $state ? 1 : 0,
            ],
            'message' => $state ? 'Wishlist added' : 'Wishlist removed',
        ]);
    }

    /**
     * POST /api/merchant/review
     */
    public function createReview(ReviewRequest $request): JsonResponse
    {
        $review = $this->merchantService->createReview($request->validated());

        return response()->json([
            'data' => $review,
            'message' => 'Review created successfully',
        ], 201);
    }
}
