<?php

namespace App\Models\Auth;

use App\Models\App\Application;
use App\Models\App\Module;
use App\Models\Global\Cluster;
use App\Models\Global\Country;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Group extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'slug', 'description', 'scope',
        'country_id', 'cluster_id', 'is_active', 'sort_order',
    ];

    protected $casts = [
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

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'group_user')->withTimestamps();
    }

    /**
     * Get apps this group has access to in a given cluster.
     */
    public function appAccessInCluster(int $clusterId)
    {
        return \Illuminate\Support\Facades\DB::table('group_app_access')
            ->where('group_id', $this->id)
            ->where('cluster_id', $clusterId)
            ->where('has_access', true)
            ->pluck('application_id');
    }

    /**
     * Get modules this group has access to in a given cluster.
     */
    public function moduleAccessInCluster(int $clusterId)
    {
        return \Illuminate\Support\Facades\DB::table('group_module_access')
            ->where('group_id', $this->id)
            ->where('cluster_id', $clusterId)
            ->where('has_access', true)
            ->pluck('module_id');
    }

    public function scopeGlobal($query)
    {
        return $query->where('scope', 'global');
    }

    public function scopeForCountry($query, int $countryId)
    {
        return $query->where(function ($q) use ($countryId) {
            $q->where('scope', 'global')
              ->orWhere(fn ($q2) => $q2->where('scope', 'country')->where('country_id', $countryId));
        });
    }

    public function scopeForCluster($query, int $clusterId)
    {
        return $query->where(function ($q) use ($clusterId) {
            $q->where('scope', 'global')
              ->orWhere('scope', 'country')
              ->orWhere(fn ($q2) => $q2->where('scope', 'cluster')->where('cluster_id', $clusterId));
        });
    }
}
