<?php

namespace App\Models\Merchant;

use App\Models\Traits\BelongsToCluster;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Place extends Model
{
    use BelongsToCluster;

    protected $table = 'place';
    protected $primaryKey = 'place_id';

    protected $fillable = [
        'place_code', 'place_name_th', 'place_name_en',
        'place_desc_th', 'place_desc_en', 'lat', 'lng',
        'place_type', 'is_active', 'cluster_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'lat' => 'decimal:7',
        'lng' => 'decimal:7',
    ];

    public function merchants(): BelongsToMany
    {
        return $this->belongsToMany(Merchant::class, 'place_merchant', 'place_id', 'merchant_id')
            ->withPivot(['is_primary', 'sort_order'])
            ->orderByPivot('is_primary', 'desc')
            ->orderByPivot('sort_order');
    }

    public function i18n(): HasMany
    {
        return $this->hasMany(PlaceI18n::class, 'place_id', 'place_id');
    }

    public function scopeByCode($query, string $code)
    {
        return $query->where('place_code', $code);
    }
}
