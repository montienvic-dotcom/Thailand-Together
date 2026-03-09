<?php

namespace App\Models\Journey;

use App\Models\Merchant\Place;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JourneyStep extends Model
{
    protected $table = 'journey_step';
    public $timestamps = false;

    protected $fillable = [
        'journey_id', 'place_id', 'step_no',
        'duration_minutes', 'tp_normal', 'tp_goal', 'tp_special',
        'spend_estimate', 'step_note',
    ];

    protected $casts = [
        'spend_estimate' => 'decimal:2',
    ];

    public function journey(): BelongsTo
    {
        return $this->belongsTo(Journey::class, 'journey_id', 'journey_id');
    }

    public function place(): BelongsTo
    {
        return $this->belongsTo(Place::class, 'place_id', 'place_id');
    }
}
