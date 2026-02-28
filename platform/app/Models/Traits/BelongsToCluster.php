<?php

namespace App\Models\Traits;

use App\Models\Global\Cluster;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Trait for models that belong to a specific cluster.
 * Provides automatic scoping and cluster relationship.
 */
trait BelongsToCluster
{
    public function cluster(): BelongsTo
    {
        return $this->belongsTo(Cluster::class);
    }

    public function scopeForCluster(Builder $query, int|Cluster $cluster): Builder
    {
        $clusterId = $cluster instanceof Cluster ? $cluster->id : $cluster;
        return $query->where($this->getTable() . '.cluster_id', $clusterId);
    }

    public function scopeForCountry(Builder $query, int $countryId): Builder
    {
        return $query->whereHas('cluster', fn (Builder $q) => $q->where('country_id', $countryId));
    }
}
