<?php

namespace App\Models\Merchant;

use App\Models\Journey\Journey;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MerchantCheckin extends Model
{
    protected $table = 'merchant_checkin';
    protected $primaryKey = 'checkin_id';

    protected $fillable = [
        'user_id', 'merchant_id', 'place_id', 'journey_id',
        'checkin_method', 'note', 'tp_awarded',
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
}
