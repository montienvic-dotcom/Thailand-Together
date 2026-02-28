<?php

namespace App\Models\Global;

use App\Models\App\Application;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cluster extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'country_id', 'name', 'slug', 'code', 'description',
        'timezone', 'default_locale', 'settings', 'database_connection',
        'is_active', 'launch_date', 'sort_order',
    ];

    protected $casts = [
        'settings' => 'array',
        'is_active' => 'boolean',
        'launch_date' => 'date',
    ];

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function applications(): BelongsToMany
    {
        return $this->belongsToMany(Application::class, 'cluster_application')
            ->withPivot(['is_active', 'config_overrides'])
            ->withTimestamps();
    }

    public function activeApplications(): BelongsToMany
    {
        return $this->applications()->wherePivot('is_active', true);
    }

    public function menuItems(): HasMany
    {
        return $this->hasMany(\App\Models\Global\MenuItem::class);
    }

    public function getTimezoneAttribute($value): string
    {
        return $value ?? $this->country->timezone ?? 'UTC';
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForCountry($query, int $countryId)
    {
        return $query->where('country_id', $countryId);
    }
}
