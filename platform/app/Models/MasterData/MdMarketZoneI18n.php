<?php

namespace App\Models\MasterData;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MdMarketZoneI18n extends Model
{
    protected $table = 'md_market_zone_i18n';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'zone_code',
        'lang',
        'zone_name',
        'zone_desc',
    ];

    public function zone(): BelongsTo
    {
        return $this->belongsTo(MdMarketZone::class, 'zone_code', 'zone_code');
    }
}
