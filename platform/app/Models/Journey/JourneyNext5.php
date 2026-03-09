<?php

namespace App\Models\Journey;

use Illuminate\Database\Eloquent\Model;

class JourneyNext5 extends Model
{
    protected $table = 'journey_next5';
    public $timestamps = false;

    protected $fillable = ['journey_id', 'next_rank', 'next_journey_code'];
}
