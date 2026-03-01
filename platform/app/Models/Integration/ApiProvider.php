<?php

namespace App\Models\Integration;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ApiProvider extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'slug', 'category', 'description', 'base_url',
        'docs_url', 'adapter_class', 'is_active', 'is_shared',
        'supported_countries', 'default_config',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_shared' => 'boolean',
        'supported_countries' => 'array',
        'default_config' => 'array',
    ];

    public function credentials(): HasMany
    {
        return $this->hasMany(ApiCredential::class);
    }

    /**
     * Get credentials for a specific cluster & environment.
     * Falls back to country-level, then global credentials.
     */
    public function credentialsFor(?int $clusterId = null, ?int $countryId = null, string $environment = 'production'): ?ApiCredential
    {
        // Try cluster-specific first
        if ($clusterId) {
            $cred = $this->credentials()
                ->where('cluster_id', $clusterId)
                ->where('environment', $environment)
                ->where('is_active', true)
                ->first();
            if ($cred) return $cred;
        }

        // Then country-level
        if ($countryId) {
            $cred = $this->credentials()
                ->whereNull('cluster_id')
                ->where('country_id', $countryId)
                ->where('environment', $environment)
                ->where('is_active', true)
                ->first();
            if ($cred) return $cred;
        }

        // Then global (no country, no cluster)
        return $this->credentials()
            ->whereNull('cluster_id')
            ->whereNull('country_id')
            ->where('environment', $environment)
            ->where('is_active', true)
            ->first();
    }

    public function scopeCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeShared($query)
    {
        return $query->where('is_shared', true);
    }
}
