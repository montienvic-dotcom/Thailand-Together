<?php

namespace App\Models\App;

use App\Models\Global\Cluster;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Application extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'slug', 'code', 'description', 'icon', 'color',
        'type', 'base_url', 'source', 'source_version',
        'is_active', 'show_in_menu', 'sort_order', 'settings',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'show_in_menu' => 'boolean',
        'settings' => 'array',
    ];

    public function modules(): HasMany
    {
        return $this->hasMany(Module::class);
    }

    public function activeModules(): HasMany
    {
        return $this->modules()->where('is_active', true);
    }

    public function clusters(): BelongsToMany
    {
        return $this->belongsToMany(Cluster::class, 'cluster_application')
            ->withPivot(['is_active', 'config_overrides'])
            ->withTimestamps();
    }

    public function isFromCodeCanyon(): bool
    {
        return $this->source === 'codecanyon';
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeVisibleInMenu($query)
    {
        return $query->where('show_in_menu', true);
    }
}
