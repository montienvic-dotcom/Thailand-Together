<?php

namespace App\Models\Global;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Country extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'code', 'code_alpha2', 'currency_code',
        'timezone', 'default_locale', 'supported_locales',
        'is_active', 'sort_order',
    ];

    protected $casts = [
        'supported_locales' => 'array',
        'is_active' => 'boolean',
    ];

    public function clusters(): HasMany
    {
        return $this->hasMany(Cluster::class);
    }

    public function activeClusters(): HasMany
    {
        return $this->clusters()->where('is_active', true);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
