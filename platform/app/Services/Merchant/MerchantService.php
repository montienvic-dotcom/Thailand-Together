<?php

namespace App\Services\Merchant;

use App\Models\Journey\Journey;
use App\Models\Merchant\Merchant;
use App\Models\Merchant\MerchantCheckin;
use App\Models\Merchant\MerchantFavorite;
use App\Models\Merchant\MerchantReview;
use App\Models\Merchant\MerchantWishlist;
use App\Models\Merchant\Place;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class MerchantService
{
    /**
     * Journey one-call: public (no user context).
     */
    public function journeyOneCallPublic(string $journeyCode): ?array
    {
        $journey = Journey::active()
            ->byCode($journeyCode)
            ->with([
                'i18n',
                'tags',
                'personas',
                'markets',
                'zones',
                'next5',
                'steps.place.merchants' => fn ($q) => $q->where('merchant.is_active', true),
            ])
            ->first();

        if (! $journey) {
            return null;
        }

        $merchants = $this->buildMerchantsJsonFromSteps($journey->steps);
        $stats = $this->computeMerchantStats($merchants);

        return [
            'journey_id' => $journey->journey_id,
            'journey_code' => $journey->journey_code,
            'journey_name_th' => $journey->journey_name_th,
            'journey_name_en' => $journey->journey_name_en,
            'group_size' => $journey->group_size,
            'gmv_per_person' => $journey->gmv_per_person,
            'gmv_per_group' => $journey->gmv_per_group,
            'tp_total_normal' => $journey->tp_total_normal,
            'tp_total_goal' => $journey->tp_total_goal,
            'tp_total_special' => $journey->tp_total_special,
            'total_minutes_sum' => $journey->total_minutes_sum,
            'journey_i18n_json' => $this->formatI18n($journey->i18n),
            'tags_json' => $journey->tags->map(fn ($t) => ['tag_code' => $t->tag_code]),
            'personas_json' => $journey->personas->map(fn ($p) => ['persona_code' => $p->persona_code]),
            'markets_json' => $journey->markets->map(fn ($m) => [
                'country_code' => $m->country_code,
                'fit_level' => $m->fit_level,
            ]),
            'zones_json' => $journey->zones->map(fn ($z) => [
                'zone_code' => $z->zone_code,
                'fit_level' => $z->fit_level,
            ]),
            'luxury_tone_json' => [
                'th' => ['tone' => $journey->luxury_tone_th],
                'en' => ['tone' => $journey->luxury_tone_en],
            ],
            'next5_json' => $journey->next5->map(fn ($n) => [
                'next_rank' => $n->next_rank,
                'next_journey_code' => $n->next_journey_code,
            ]),
            'merchants_json' => $merchants,
            ...$stats,
        ];
    }

    /**
     * Journey one-call: user context (includes user state per merchant).
     */
    public function journeyOneCallUser(string $journeyCode, int $userId): ?array
    {
        $base = $this->journeyOneCallPublic($journeyCode);
        if (! $base) {
            return null;
        }

        $merchantIds = collect($base['merchants_json'])->pluck('merchant_id')->unique()->values();

        $visitCounts = MerchantCheckin::where('user_id', $userId)
            ->whereIn('merchant_id', $merchantIds)
            ->selectRaw('merchant_id, COUNT(*) as cnt, MAX(created_at) as last_at')
            ->groupBy('merchant_id')
            ->get()
            ->keyBy('merchant_id');

        $favIds = MerchantFavorite::where('user_id', $userId)
            ->whereIn('merchant_id', $merchantIds)
            ->pluck('merchant_id')
            ->flip();

        $wishIds = MerchantWishlist::where('user_id', $userId)
            ->whereIn('merchant_id', $merchantIds)
            ->pluck('merchant_id')
            ->flip();

        $userReviews = MerchantReview::where('user_id', $userId)
            ->whereIn('merchant_id', $merchantIds)
            ->selectRaw('merchant_id, COUNT(*) as cnt, MAX(created_at) as last_at, MAX(rating) as last_rating')
            ->groupBy('merchant_id')
            ->get()
            ->keyBy('merchant_id');

        $merchantsWithState = collect($base['merchants_json'])->map(function ($m) use ($visitCounts, $favIds, $wishIds, $userReviews) {
            $mid = $m['merchant_id'];
            $vc = $visitCounts->get($mid);
            $rv = $userReviews->get($mid);

            $m['user_state'] = [
                'visit_count' => $vc?->cnt ?? 0,
                'last_checkin_at' => $vc?->last_at,
                'visited' => ($vc?->cnt ?? 0) > 0 ? 1 : 0,
                'is_favorite' => $favIds->has($mid) ? 1 : 0,
                'is_wishlist' => $wishIds->has($mid) ? 1 : 0,
                'review_count_by_user' => $rv?->cnt ?? 0,
                'last_review_at' => $rv?->last_at,
                'last_rating' => $rv?->last_rating,
            ];

            return $m;
        });

        $base['user_id'] = $userId;
        $base['merchants_json_user'] = $merchantsWithState;

        return $base;
    }

    /**
     * Public merchant search with filters.
     */
    public function searchPublic(array $filters): LengthAwarePaginator
    {
        $query = Merchant::active()
            ->join('place_merchant', 'merchant.merchant_id', '=', 'place_merchant.merchant_id')
            ->join('place', 'place.place_id', '=', 'place_merchant.place_id')
            ->leftJoin('journey_step', 'journey_step.place_id', '=', 'place.place_id')
            ->leftJoin('journey', 'journey.journey_id', '=', 'journey_step.journey_id')
            ->select([
                'merchant.merchant_id', 'merchant.merchant_code',
                'merchant.merchant_name_th', 'merchant.merchant_name_en',
                'merchant.default_tier_code as tier_code',
                'merchant.price_level', 'merchant.open_hours', 'merchant.service_tags',
                'place.place_code', 'place.place_name_th', 'place.place_name_en',
                'place_merchant.is_primary', 'place_merchant.sort_order',
                'journey.journey_code', 'journey_step.step_no',
            ])
            ->selectRaw('COALESCE((SELECT AVG(r.rating) FROM merchant_review r WHERE r.merchant_id = merchant.merchant_id AND r.status = "PUBLISHED" AND r.is_public = 1), 0) as avg_rating')
            ->selectRaw('(SELECT COUNT(*) FROM merchant_review r WHERE r.merchant_id = merchant.merchant_id AND r.status = "PUBLISHED" AND r.is_public = 1) as review_count');

        $this->applyPublicFilters($query, $filters);

        $query->orderByRaw('journey.journey_code, journey_step.step_no, place_merchant.is_primary DESC, place_merchant.sort_order');

        $limit = min((int) ($filters['limit'] ?? 50), 200);
        $page = max((int) ($filters['page'] ?? 1), 1);

        return $query->paginate($limit, ['*'], 'page', $page);
    }

    /**
     * User-context merchant search (adds user state).
     */
    public function searchUser(int $userId, array $filters): LengthAwarePaginator
    {
        $query = Merchant::active()
            ->join('place_merchant', 'merchant.merchant_id', '=', 'place_merchant.merchant_id')
            ->join('place', 'place.place_id', '=', 'place_merchant.place_id')
            ->leftJoin('journey_step', 'journey_step.place_id', '=', 'place.place_id')
            ->leftJoin('journey', 'journey.journey_id', '=', 'journey_step.journey_id')
            ->leftJoin('merchant_favorite as mf', function ($j) use ($userId) {
                $j->on('mf.merchant_id', '=', 'merchant.merchant_id')
                    ->where('mf.user_id', $userId);
            })
            ->leftJoin('merchant_wishlist as mw', function ($j) use ($userId) {
                $j->on('mw.merchant_id', '=', 'merchant.merchant_id')
                    ->where('mw.user_id', $userId);
            })
            ->select([
                'merchant.merchant_id', 'merchant.merchant_code',
                'merchant.merchant_name_th', 'merchant.merchant_name_en',
                'merchant.default_tier_code as tier_code',
                'merchant.price_level', 'merchant.open_hours', 'merchant.service_tags',
                'place.place_code', 'place.place_name_th',
                'place_merchant.is_primary', 'place_merchant.sort_order',
                'journey.journey_code', 'journey_step.step_no',
            ])
            ->selectRaw('IF(mf.id IS NOT NULL, 1, 0) as is_favorite')
            ->selectRaw('IF(mw.id IS NOT NULL, 1, 0) as is_wishlist')
            ->selectRaw('COALESCE((SELECT COUNT(*) FROM merchant_checkin mc WHERE mc.merchant_id = merchant.merchant_id AND mc.user_id = ?), 0) as visit_count', [$userId])
            ->selectRaw('IF((SELECT COUNT(*) FROM merchant_checkin mc WHERE mc.merchant_id = merchant.merchant_id AND mc.user_id = ?) > 0, 1, 0) as visited', [$userId])
            ->selectRaw('COALESCE((SELECT AVG(r.rating) FROM merchant_review r WHERE r.merchant_id = merchant.merchant_id AND r.status = "PUBLISHED" AND r.is_public = 1), 0) as avg_rating')
            ->selectRaw('(SELECT COUNT(*) FROM merchant_review r WHERE r.merchant_id = merchant.merchant_id AND r.status = "PUBLISHED" AND r.is_public = 1) as review_count')
            ->selectRaw('COALESCE((SELECT COUNT(*) FROM merchant_review r WHERE r.merchant_id = merchant.merchant_id AND r.user_id = ?), 0) as review_count_by_user', [$userId])
            ->selectRaw('(SELECT MAX(r.rating) FROM merchant_review r WHERE r.merchant_id = merchant.merchant_id AND r.user_id = ?) as last_rating_by_user', [$userId]);

        $this->applyPublicFilters($query, $filters);

        if (isset($filters['visited'])) {
            if ((int) $filters['visited'] === 1) {
                $query->whereRaw('(SELECT COUNT(*) FROM merchant_checkin mc WHERE mc.merchant_id = merchant.merchant_id AND mc.user_id = ?) > 0', [$userId]);
            } else {
                $query->whereRaw('(SELECT COUNT(*) FROM merchant_checkin mc WHERE mc.merchant_id = merchant.merchant_id AND mc.user_id = ?) = 0', [$userId]);
            }
        }
        if (isset($filters['is_favorite'])) {
            if ((int) $filters['is_favorite'] === 1) {
                $query->whereNotNull('mf.id');
            } else {
                $query->whereNull('mf.id');
            }
        }
        if (isset($filters['is_wishlist'])) {
            if ((int) $filters['is_wishlist'] === 1) {
                $query->whereNotNull('mw.id');
            } else {
                $query->whereNull('mw.id');
            }
        }

        $query->orderByRaw('journey.journey_code, journey_step.step_no, place_merchant.is_primary DESC, place_merchant.sort_order');

        $limit = min((int) ($filters['limit'] ?? 50), 200);
        $page = max((int) ($filters['page'] ?? 1), 1);

        return $query->paginate($limit, ['*'], 'page', $page);
    }

    /**
     * Merchants by place code.
     */
    public function merchantsByPlace(string $placeCode): Collection
    {
        $place = Place::byCode($placeCode)->first();
        if (! $place) {
            return collect();
        }

        return $place->merchants()
            ->where('merchant.is_active', true)
            ->get()
            ->map(fn ($m) => $this->formatMerchantRow($m, $place));
    }

    /**
     * Merchants by journey (normalized rows).
     */
    public function merchantsByJourney(string $journeyCode): Collection
    {
        $journey = Journey::active()->byCode($journeyCode)
            ->with(['steps.place.merchants' => fn ($q) => $q->where('merchant.is_active', true)])
            ->first();

        if (! $journey) {
            return collect();
        }

        return $this->buildMerchantsJsonFromSteps($journey->steps);
    }

    /**
     * Merchant stats per journey.
     */
    public function journeyMerchantStats(string $journeyCode): ?array
    {
        $merchants = $this->merchantsByJourney($journeyCode);
        if ($merchants->isEmpty()) {
            return null;
        }

        return $this->computeMerchantStats($merchants);
    }

    /**
     * Merchant reviews (public, published).
     */
    public function merchantReviews(int $merchantId, int $limit = 20, int $offset = 0): Collection
    {
        return MerchantReview::where('merchant_id', $merchantId)
            ->published()
            ->select(['review_id', 'user_id', 'merchant_id', 'place_id', 'journey_id', 'rating', 'title', 'review_text', 'created_at', 'updated_at'])
            ->orderByDesc('created_at')
            ->limit($limit)
            ->offset($offset)
            ->get();
    }

    /**
     * Check-in a user at a merchant.
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
     * Toggle favorite.
     */
    public function toggleFavorite(int $userId, int $merchantId, bool $isFavorite): bool
    {
        if ($isFavorite) {
            MerchantFavorite::firstOrCreate([
                'user_id' => $userId,
                'merchant_id' => $merchantId,
            ]);
        } else {
            MerchantFavorite::where('user_id', $userId)
                ->where('merchant_id', $merchantId)
                ->delete();
        }

        return $isFavorite;
    }

    /**
     * Toggle wishlist.
     */
    public function toggleWishlist(int $userId, int $merchantId, bool $isWishlist): bool
    {
        if ($isWishlist) {
            MerchantWishlist::firstOrCreate([
                'user_id' => $userId,
                'merchant_id' => $merchantId,
            ]);
        } else {
            MerchantWishlist::where('user_id', $userId)
                ->where('merchant_id', $merchantId)
                ->delete();
        }

        return $isWishlist;
    }

    /**
     * Create a review.
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

    // ─── Private helpers ───

    private function buildMerchantsJsonFromSteps(Collection $steps): Collection
    {
        $rows = collect();
        foreach ($steps as $step) {
            if (! $step->place) {
                continue;
            }
            foreach ($step->place->merchants as $merchant) {
                $rows->push([
                    'step_no' => $step->step_no,
                    'place_id' => $step->place->place_id,
                    'place_code' => $step->place->place_code,
                    'place_name_th' => $step->place->place_name_th,
                    'place_name_en' => $step->place->place_name_en,
                    'merchant_id' => $merchant->merchant_id,
                    'merchant_code' => $merchant->merchant_code,
                    'merchant_name_th' => $merchant->merchant_name_th,
                    'merchant_name_en' => $merchant->merchant_name_en,
                    'tier_code' => $merchant->default_tier_code,
                    'is_primary' => (int) $merchant->pivot->is_primary,
                    'sort_order' => $merchant->pivot->sort_order,
                    'open_hours' => $merchant->open_hours,
                    'service_tags' => $merchant->service_tags,
                    'avg_rating' => round((float) $merchant->publishedReviews()->avg('rating'), 2),
                    'review_count' => $merchant->publishedReviews()->count(),
                ]);
            }
        }

        return $rows;
    }

    private function computeMerchantStats(Collection $merchants): array
    {
        return [
            'merchant_rows' => $merchants->count(),
            'merchant_distinct_count' => $merchants->pluck('merchant_id')->unique()->count(),
            'place_with_merchant_count' => $merchants->pluck('place_id')->unique()->count(),
            'merchant_avg_rating' => $merchants->avg('avg_rating') ? round($merchants->avg('avg_rating'), 2) : 0,
            'merchant_primary_rows' => $merchants->where('is_primary', 1)->count(),
        ];
    }

    private function formatI18n(Collection $i18n): array
    {
        $result = [];
        foreach ($i18n as $row) {
            $result[$row->lang] = [
                'name' => $row->name,
                'description' => $row->description,
            ];
        }

        return $result;
    }

    private function formatMerchantRow($merchant, Place $place): array
    {
        return [
            'merchant_id' => $merchant->merchant_id,
            'merchant_code' => $merchant->merchant_code,
            'merchant_name_th' => $merchant->merchant_name_th,
            'merchant_name_en' => $merchant->merchant_name_en,
            'tier_code' => $merchant->default_tier_code,
            'is_primary' => (int) $merchant->pivot->is_primary,
            'sort_order' => $merchant->pivot->sort_order,
            'place_code' => $place->place_code,
            'open_hours' => $merchant->open_hours,
            'service_tags' => $merchant->service_tags,
        ];
    }

    private function applyPublicFilters(Builder $query, array $filters): void
    {
        if (! empty($filters['journey_code'])) {
            $query->where('journey.journey_code', $filters['journey_code']);
        }
        if (! empty($filters['place_code'])) {
            $query->where('place.place_code', $filters['place_code']);
        }
        if (! empty($filters['tier_code'])) {
            $query->where('merchant.default_tier_code', $filters['tier_code']);
        }
        if (! empty($filters['price_level'])) {
            $query->where('merchant.price_level', (int) $filters['price_level']);
        }
        if (! empty($filters['min_rating'])) {
            $query->havingRaw('avg_rating >= ?', [(float) $filters['min_rating']]);
        }
        if (! empty($filters['q'])) {
            $term = '%' . $filters['q'] . '%';
            $query->where(function ($q) use ($term) {
                $q->where('merchant.merchant_name_th', 'LIKE', $term)
                    ->orWhere('merchant.merchant_name_en', 'LIKE', $term)
                    ->orWhere('merchant.service_tags', 'LIKE', $term)
                    ->orWhere('place.place_name_th', 'LIKE', $term)
                    ->orWhere('place.place_name_en', 'LIKE', $term);
            });
        }
    }
}
