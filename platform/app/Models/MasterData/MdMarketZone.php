<?php

namespace App\Models\MasterData;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MdMarketZone extends Model
{
    protected $table = 'md_market_zone';
    protected $primaryKey = 'zone_code';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'zone_code',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function i18n(): HasMany
    {
        return $this->hasMany(MdMarketZoneI18n::class, 'zone_code', 'zone_code');
    }
}
