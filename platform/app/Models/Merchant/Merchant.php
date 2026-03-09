<?php

namespace App\Models\Merchant;

use App\Models\Traits\BelongsToCluster;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Merchant extends Model
{
    use BelongsToCluster;

    protected $table = 'merchant';
    protected $primaryKey = 'merchant_id';

    protected $fillable = [
        'merchant_code', 'merchant_name_th', 'merchant_name_en',
        'merchant_desc_th', 'merchant_desc_en', 'default_tier_code',
        'is_active', 'phone', 'website', 'price_level',
        'lat', 'lng', 'open_hours', 'service_tags',
        'onsite_note', 'source_ref', 'cluster_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'lat' => 'decimal:7',
        'lng' => 'decimal:7',
    ];

    public function i18n(): HasMany
    {
        return $this->hasMany(MerchantI18n::class, 'merchant_id', 'merchant_id');
    }

    public function places(): BelongsToMany
    {
        return $this->belongsToMany(Place::class, 'place_merchant', 'merchant_id', 'place_id')
            ->withPivot(['is_primary', 'sort_order']);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(MerchantReview::class, 'merchant_id', 'merchant_id');
    }

    public function checkins(): HasMany
    {
        return $this->hasMany(MerchantCheckin::class, 'merchant_id', 'merchant_id');
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(MerchantFavorite::class, 'merchant_id', 'merchant_id');
    }

    public function wishlists(): HasMany
    {
        return $this->hasMany(MerchantWishlist::class, 'merchant_id', 'merchant_id');
    }

    public function publishedReviews(): HasMany
    {
        return $this->reviews()
            ->where('status', 'PUBLISHED')
            ->where('is_public', true);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCode($query, string $code)
    {
        return $query->where('merchant_code', $code);
    }
}
