<?php

namespace App\Models\Journey;

use Illuminate\Database\Eloquent\Model;

class JourneyZone extends Model
{
    protected $table = 'journey_zone';
    public $timestamps = false;

    protected $fillable = ['journey_id', 'zone_code', 'fit_level'];
}
