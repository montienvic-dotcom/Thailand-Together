<?php

namespace App\Models\Journey;

use App\Models\Traits\BelongsToCluster;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Journey extends Model
{
    use BelongsToCluster;

    protected $table = 'journey';
    protected $primaryKey = 'journey_id';

    protected $fillable = [
        'journey_code', 'journey_group', 'journey_name_th', 'journey_name_en',
        'group_size', 'gmv_per_person', 'gmv_per_group',
        'tp_total_normal', 'tp_total_goal', 'tp_total_special',
        'total_minutes_sum', 'luxury_tone_th', 'luxury_tone_en',
        'target_visitors', 'status', 'cluster_id',
    ];

    protected $casts = [
        'gmv_per_person' => 'decimal:2',
        'gmv_per_group' => 'decimal:2',
    ];

    public function i18n(): HasMany
    {
        return $this->hasMany(JourneyI18n::class, 'journey_id', 'journey_id');
    }

    public function tags(): HasMany
    {
        return $this->hasMany(JourneyTag::class, 'journey_id', 'journey_id');
    }

    public function personas(): HasMany
    {
        return $this->hasMany(JourneyPersona::class, 'journey_id', 'journey_id');
    }

    public function markets(): HasMany
    {
        return $this->hasMany(JourneyMarket::class, 'journey_id', 'journey_id');
    }

    public function zones(): HasMany
    {
        return $this->hasMany(JourneyZone::class, 'journey_id', 'journey_id');
    }

    public function next5(): HasMany
    {
        return $this->hasMany(JourneyNext5::class, 'journey_id', 'journey_id')
            ->orderBy('next_rank');
    }

    public function steps(): HasMany
    {
        return $this->hasMany(JourneyStep::class, 'journey_id', 'journey_id')
            ->orderBy('step_no');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'ACTIVE');
    }

    public function scopeByCode($query, string $code)
    {
        return $query->where('journey_code', $code);
    }
}
