<?php

namespace App\Services\Merchant;

use App\Models\Journey\Journey;
use App\Models\Merchant\MerchantCheckin;
use App\Models\Merchant\MerchantFavorite;
use App\Models\Merchant\MerchantReview;
use App\Models\Merchant\MerchantWishlist;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class MerchantService
{
    // ────────────────────────────────────────────────────────
    // Journey One-Call (View-based — realtime)
    // ────────────────────────────────────────────────────────

    /**
     * GET /api/journeys/{code}/onecall/final
     * Uses: vw_api_journey_onecall_with_merchants_stats_final
     */
    public function journeyOneCallPublic(string $journeyCode): ?object
    {
        $row = DB::table('vw_api_journey_onecall_with_merchants_stats_final')
            ->where('journey_code', $journeyCode)
            ->first();

        if (! $row) {
            return null;
        }

        // Decode merchants_json if it's a string
        if (is_string($row->merchants_json)) {
            $row->merchants_json = json_decode($row->merchants_json, true);
        }

        // Attach i18n, tags, personas, markets, zones, next5 from relational tables
        $journey = Journey::where('journey_code', $journeyCode)
            ->with(['i18n', 'tags', 'personas', 'markets', 'zones', 'next5'])
            ->first();

        if ($journey) {
            $row->journey_i18n_json = $this->formatI18n($journey->i18n);
            $row->tags_json = $journey->tags->map(fn ($t) => ['tag_code' => $t->tag_code])->values();
            $row->personas_json = $journey->personas->map(fn ($p) => ['persona_code' => $p->persona_code])->values();
            $row->markets_json = $journey->markets->map(fn ($m) => ['country_code' => $m->country_code, 'fit_level' => $m->fit_level])->values();
            $row->zones_json = $journey->zones->map(fn ($z) => ['zone_code' => $z->zone_code, 'fit_level' => $z->fit_level])->values();
            $row->luxury_tone_json = ['th' => ['tone' => $row->luxury_tone_th], 'en' => ['tone' => $row->luxury_tone_en]];
            $row->next5_json = $journey->next5->map(fn ($n) => ['next_rank' => $n->next_rank, 'next_journey_code' => $n->next_journey_code])->values();
        }

        return $row;
    }

    /**
     * GET /api/journeys/{code}/onecall/final?user_id=123
     * Uses: vw_api_journey_onecall_with_merchants_user
     */
    public function journeyOneCallUser(string $journeyCode, int $userId): ?object
    {
        $row = DB::table('vw_api_journey_onecall_with_merchants_user')
            ->where('journey_code', $journeyCode)
            ->where('user_id', $userId)
            ->first();

        if (! $row) {
            // Fallback: user has no interaction yet, return public data with empty user states
            $public = $this->journeyOneCallPublic($journeyCode);
            if (! $public) {
                return null;
            }
            $public->user_id = $userId;
            $public->merchants_json_user = $public->merchants_json;

            return $public;
        }

        if (is_string($row->merchants_json_user)) {
            $row->merchants_json_user = json_decode($row->merchants_json_user, true);
        }

        // Attach i18n etc.
        $journey = Journey::where('journey_code', $journeyCode)
            ->with(['i18n', 'tags', 'personas', 'markets', 'zones', 'next5'])
            ->first();

        if ($journey) {
            $row->journey_i18n_json = $this->formatI18n($journey->i18n);
            $row->tags_json = $journey->tags->map(fn ($t) => ['tag_code' => $t->tag_code])->values();
            $row->personas_json = $journey->personas->map(fn ($p) => ['persona_code' => $p->persona_code])->values();
            $row->markets_json = $journey->markets->map(fn ($m) => ['country_code' => $m->country_code, 'fit_level' => $m->fit_level])->values();
            $row->zones_json = $journey->zones->map(fn ($z) => ['zone_code' => $z->zone_code, 'fit_level' => $z->fit_level])->values();
            $row->luxury_tone_json = ['th' => ['tone' => $row->luxury_tone_th], 'en' => ['tone' => $row->luxury_tone_en]];
            $row->next5_json = $journey->next5->map(fn ($n) => ['next_rank' => $n->next_rank, 'next_journey_code' => $n->next_journey_code])->values();
        }

        return $row;
    }

    // ────────────────────────────────────────────────────────
    // Merchant Search (View-based)
    // ────────────────────────────────────────────────────────

    /**
     * GET /api/merchants/search
     * Uses: vw_merchant_search_public + vw_merchant_search_blob_public
     */
    public function searchPublic(array $filters): array
    {
        $query = DB::table('vw_merchant_search_public as p')
            ->leftJoin('vw_merchant_search_blob_public as b', function ($j) {
                $j->on('b.merchant_id', '=', 'p.merchant_id')
                    ->on('b.journey_id', '=', 'p.journey_code'); // journey_id matched via view
            });

        $this->applyPublicFilters($query, $filters, 'p');

        $query->orderByRaw('p.journey_code, p.step_no, p.is_primary DESC, p.sort_order');

        $limit = min((int) ($filters['limit'] ?? 50), 200);
        $offset = max((int) ($filters['offset'] ?? 0), 0);

        $total = (clone $query)->count();
        $rows = $query->select('p.*')->limit($limit)->offset($offset)->get();

        return [
            'data' => $rows,
            'meta' => ['total' => $total, 'limit' => $limit, 'offset' => $offset],
        ];
    }

    /**
     * GET /api/merchants/search/user
     * Uses: vw_merchant_search_user
     */
    public function searchUser(int $userId, array $filters): array
    {
        $query = DB::table('vw_merchant_search_user as p')
            ->where('p.user_id', $userId);

        $this->applyPublicFilters($query, $filters, 'p');

        if (isset($filters['visited'])) {
            $query->where('p.visited', (int) $filters['visited']);
        }
        if (isset($filters['is_favorite'])) {
            $query->where('p.is_favorite', (int) $filters['is_favorite']);
        }
        if (isset($filters['is_wishlist'])) {
            $query->where('p.is_wishlist', (int) $filters['is_wishlist']);
        }

        $query->orderByRaw('p.journey_code, p.step_no, p.is_primary DESC, p.sort_order');

        $limit = min((int) ($filters['limit'] ?? 50), 200);
        $offset = max((int) ($filters['offset'] ?? 0), 0);

        $total = (clone $query)->count();
        $rows = $query->select('p.*')->limit($limit)->offset($offset)->get();

        return [
            'data' => $rows,
            'meta' => ['total' => $total, 'limit' => $limit, 'offset' => $offset],
        ];
    }

    // ────────────────────────────────────────────────────────
    // Merchants by Place / Journey (View-based)
    // ────────────────────────────────────────────────────────

    /**
     * GET /api/places/{place_code}/merchants
     * Uses: vw_merchant_search_public
     */
    public function merchantsByPlace(string $placeCode): Collection
    {
        return DB::table('vw_merchant_search_public')
            ->where('place_code', $placeCode)
            ->orderByRaw('is_primary DESC, sort_order, merchant_code')
            ->get();
    }

    /**
     * GET /api/journeys/{code}/merchants/rows
     * Uses: vw_merchant_search_public
     */
    public function merchantsByJourney(string $journeyCode): Collection
    {
        return DB::table('vw_merchant_search_public')
            ->where('journey_code', $journeyCode)
            ->orderByRaw('step_no, is_primary DESC, sort_order, merchant_code')
            ->get();
    }

    /**
     * GET /api/journeys/{code}/merchants (JSON version)
     * Uses: vw_journey_place_merchant_json
     */
    public function journeyMerchantsJson(string $journeyCode): ?object
    {
        return DB::table('vw_journey_place_merchant_json')
            ->where('journey_code', $journeyCode)
            ->first();
    }

    /**
     * GET /api/journeys/{code}/merchants?user_id=123
     * Uses: vw_journey_merchant_json_user
     */
    public function journeyMerchantsJsonUser(string $journeyCode, int $userId): ?object
    {
        return DB::table('vw_journey_merchant_json_user')
            ->where('journey_code', $journeyCode)
            ->where('user_id', $userId)
            ->first();
    }

    /**
     * GET /api/journeys/{code}/merchant-stats
     * Uses: vw_journey_merchant_stats
     */
    public function journeyMerchantStats(string $journeyCode): ?object
    {
        return DB::table('vw_journey_merchant_stats')
            ->where('journey_code', $journeyCode)
            ->first();
    }

    // ────────────────────────────────────────────────────────
    // Reviews (direct table query)
    // ────────────────────────────────────────────────────────

    /**
     * GET /api/merchant/{id}/reviews
     */
    public function merchantReviews(int $merchantId, int $limit = 20, int $offset = 0): Collection
    {
        return DB::table('merchant_review')
            ->where('merchant_id', $merchantId)
            ->where('status', 'PUBLISHED')
            ->where('is_public', 1)
            ->select(['review_id', 'user_id', 'merchant_id', 'place_id', 'journey_id', 'rating', 'title', 'review_text', 'created_at', 'updated_at'])
            ->orderByDesc('created_at')
            ->limit($limit)
            ->offset($offset)
            ->get();
    }

    // ────────────────────────────────────────────────────────
    // User Actions (direct table mutations)
    // ────────────────────────────────────────────────────────

    /**
     * POST /api/merchant/checkin
     */
    public function checkin(array $data): MerchantCheckin
    {
        $journeyId = null;
        if (! empty($data['journey_code'])) {
            $journeyId = Journey::where('journey_code', $data['journey_code'])->value('journey_id');
        }

        return MerchantCheckin::create([
            'user_id' => $data['user_id'],
            'merchant_id' => $data['merchant_id'],
            'place_id' => $data['place_id'],
            'journey_id' => $journeyId,
            'checkin_method' => $data['checkin_method'] ?? 'QR',
            'note' => $data['note'] ?? null,
            'tp_awarded' => $data['tp_awarded'] ?? 0,
        ]);
    }

    /**
     * POST /api/merchant/favorite/toggle
     */
    public function toggleFavorite(int $userId, int $merchantId, bool $isFavorite): bool
    {
        if ($isFavorite) {
            MerchantFavorite::firstOrCreate(['user_id' => $userId, 'merchant_id' => $merchantId]);
        } else {
            MerchantFavorite::where('user_id', $userId)->where('merchant_id', $merchantId)->delete();
        }

        return $isFavorite;
    }

    /**
     * POST /api/merchant/wishlist/toggle
     */
    public function toggleWishlist(int $userId, int $merchantId, bool $isWishlist): bool
    {
        if ($isWishlist) {
            MerchantWishlist::firstOrCreate(['user_id' => $userId, 'merchant_id' => $merchantId]);
        } else {
            MerchantWishlist::where('user_id', $userId)->where('merchant_id', $merchantId)->delete();
        }

        return $isWishlist;
    }

    /**
     * POST /api/merchant/review
     */
    public function createReview(array $data): MerchantReview
    {
        $journeyId = null;
        if (! empty($data['journey_code'])) {
            $journeyId = Journey::where('journey_code', $data['journey_code'])->value('journey_id');
        }

        return MerchantReview::create([
            'user_id' => $data['user_id'],
            'merchant_id' => $data['merchant_id'],
            'place_id' => $data['place_id'] ?? null,
            'journey_id' => $journeyId,
            'rating' => $data['rating'],
            'title' => $data['title'] ?? null,
            'review_text' => $data['review_text'] ?? null,
        ]);
    }

    // ────────────────────────────────────────────────────────
    // Private helpers
    // ────────────────────────────────────────────────────────

    private function applyPublicFilters($query, array $filters, string $alias = 'p'): void
    {
        if (! empty($filters['journey_code'])) {
            $query->where("{$alias}.journey_code", $filters['journey_code']);
        }
        if (! empty($filters['place_code'])) {
            $query->where("{$alias}.place_code", $filters['place_code']);
        }
        if (! empty($filters['tier_code'])) {
            $query->where("{$alias}.tier_code", $filters['tier_code']);
        }
        if (! empty($filters['price_level'])) {
            $query->where("{$alias}.price_level", (int) $filters['price_level']);
        }
        if (! empty($filters['min_rating'])) {
            $query->where("{$alias}.avg_rating", '>=', (float) $filters['min_rating']);
        }
        if (! empty($filters['q'])) {
            $term = '%' . $filters['q'] . '%';
            $query->where(function ($q) use ($term, $alias) {
                $q->where("{$alias}.merchant_name_th", 'LIKE', $term)
                    ->orWhere("{$alias}.merchant_name_en", 'LIKE', $term)
                    ->orWhere("{$alias}.service_tags", 'LIKE', $term)
                    ->orWhere("{$alias}.place_name_th", 'LIKE', $term)
                    ->orWhere("{$alias}.place_name_en", 'LIKE', $term);
            });
        }
    }

    private function formatI18n(Collection $i18n): array
    {
        $result = [];
        foreach ($i18n as $row) {
            $result[$row->lang] = ['name' => $row->name, 'description' => $row->description];
        }

        return $result;
    }
}
