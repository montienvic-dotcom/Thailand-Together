<?php

namespace App\Models\Merchant;

use App\Models\Journey\Journey;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MerchantReview extends Model
{
    protected $table = 'merchant_review';
    protected $primaryKey = 'review_id';

    protected $fillable = [
        'user_id', 'merchant_id', 'place_id', 'journey_id',
        'rating', 'title', 'review_text', 'status', 'is_public',
    ];

    protected $casts = [
        'is_public' => 'boolean',
    ];

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class, 'merchant_id', 'merchant_id');
    }

    public function place(): BelongsTo
    {
        return $this->belongsTo(Place::class, 'place_id', 'place_id');
    }

    public function journey(): BelongsTo
    {
        return $this->belongsTo(Journey::class, 'journey_id', 'journey_id');
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'PUBLISHED')->where('is_public', true);
    }
}
