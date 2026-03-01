<?php

namespace App\Models\Global;

use App\Models\App\Application;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MenuItem extends Model
{
    protected $fillable = [
        'label', 'icon', 'url', 'route_name', 'application_id',
        'parent_id', 'scope', 'country_id', 'cluster_id',
        'target', 'visibility', 'required_permissions',
        'is_active', 'sort_order',
    ];

    protected $casts = [
        'required_permissions' => 'array',
        'is_active' => 'boolean',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('sort_order');
    }

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function cluster(): BelongsTo
    {
        return $this->belongsTo(Cluster::class);
    }

    public function scopeTopLevel($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get menu items visible to a specific cluster context.
     */
    public function scopeForContext($query, ?int $countryId = null, ?int $clusterId = null)
    {
        return $query->where(function ($q) use ($countryId, $clusterId) {
            $q->where('scope', 'global');

            if ($countryId) {
                $q->orWhere(fn ($q2) => $q2->where('scope', 'country')->where('country_id', $countryId));
            }
            if ($clusterId) {
                $q->orWhere(fn ($q2) => $q2->where('scope', 'cluster')->where('cluster_id', $clusterId));
            }
        });
    }
}
