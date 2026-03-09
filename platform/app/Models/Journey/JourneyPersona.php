<?php

namespace App\Models\Journey;

use Illuminate\Database\Eloquent\Model;

class JourneyPersona extends Model
{
    protected $table = 'journey_persona';
    public $timestamps = false;

    protected $fillable = ['journey_id', 'persona_code'];
}
