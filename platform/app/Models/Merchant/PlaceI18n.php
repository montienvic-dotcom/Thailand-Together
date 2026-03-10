<?php

namespace App\Models\Merchant;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlaceI18n extends Model
{
    protected $table = 'place_i18n';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'place_id',
        'lang',
        'place_name',
        'place_desc',
    ];

    public function place(): BelongsTo
    {
        return $this->belongsTo(Place::class, 'place_id', 'place_id');
    }
}
