<?php

namespace App\Models\Journey;

use Illuminate\Database\Eloquent\Model;

class JourneyMarket extends Model
{
    protected $table = 'journey_market';
    public $timestamps = false;

    protected $fillable = ['journey_id', 'country_code', 'fit_level'];
}
