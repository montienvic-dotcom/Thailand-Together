<?php

namespace App\Models\Global;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Campaign extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'slug', 'description', 'scope', 'country_id',
        'cluster_id', 'target_clusters', 'type', 'rules', 'rewards',
        'starts_at', 'ends_at', 'is_active',
    ];

    protected $casts = [
        'target_clusters' => 'array',
        'rules' => 'array',
        'rewards' => 'array',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function cluster(): BelongsTo
    {
        return $this->belongsTo(Cluster::class);
    }

    public function isRunning(): bool
    {
        if (!$this->is_active) return false;

        $now = now();
        if ($this->starts_at && $now->lt($this->starts_at)) return false;
        if ($this->ends_at && $now->gt($this->ends_at)) return false;

        return true;
    }

    /**
     * Check if this campaign applies to a given cluster.
     */
    public function appliesToCluster(int $clusterId): bool
    {
        return match ($this->scope) {
            'global' => true,
            'country' => Cluster::where('id', $clusterId)
                ->where('country_id', $this->country_id)->exists(),
            'cluster' => $this->cluster_id === $clusterId,
            'cross_cluster' => in_array($clusterId, $this->target_clusters ?? []),
            default => false,
        };
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeRunning($query)
    {
        $now = now();
        return $query->active()
            ->where(fn ($q) => $q->whereNull('starts_at')->orWhere('starts_at', '<=', $now))
            ->where(fn ($q) => $q->whereNull('ends_at')->orWhere('ends_at', '>=', $now));
    }

    public function scopeForCluster($query, int $clusterId)
    {
        $cluster = Cluster::find($clusterId);
        return $query->where(function ($q) use ($clusterId, $cluster) {
            $q->where('scope', 'global')
              ->orWhere(fn ($q2) => $q2->where('scope', 'country')->where('country_id', $cluster?->country_id))
              ->orWhere(fn ($q2) => $q2->where('scope', 'cluster')->where('cluster_id', $clusterId))
              ->orWhere(fn ($q2) => $q2->where('scope', 'cross_cluster')->whereJsonContains('target_clusters', $clusterId));
        });
    }
}
