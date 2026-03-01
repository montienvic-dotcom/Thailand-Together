<?php

namespace App\Models\Integration;

use App\Models\Global\Cluster;
use App\Models\Global\Country;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApiCredential extends Model
{
    protected $fillable = [
        'api_provider_id', 'country_id', 'cluster_id',
        'environment', 'credentials', 'config', 'is_active',
    ];

    protected $casts = [
        'credentials' => 'encrypted:array',
        'config' => 'array',
        'is_active' => 'boolean',
    ];

    protected $hidden = [
        'credentials',
    ];

    public function provider(): BelongsTo
    {
        return $this->belongsTo(ApiProvider::class, 'api_provider_id');
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function cluster(): BelongsTo
    {
        return $this->belongsTo(Cluster::class);
    }

    /**
     * Get a specific credential value (e.g., api_key, secret).
     */
    public function credential(string $key, $default = null): mixed
    {
        return data_get($this->credentials, $key, $default);
    }

    public function scopeProduction($query)
    {
        return $query->where('environment', 'production');
    }

    public function scopeSandbox($query)
    {
        return $query->where('environment', 'sandbox');
    }
}
