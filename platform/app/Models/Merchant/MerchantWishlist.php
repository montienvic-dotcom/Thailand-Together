<?php

namespace App\Models\Merchant;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MerchantWishlist extends Model
{
    protected $table = 'merchant_wishlist';

    protected $fillable = ['user_id', 'merchant_id'];

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class, 'merchant_id', 'merchant_id');
    }
}
