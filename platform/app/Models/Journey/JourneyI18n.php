<?php

namespace App\Models\Journey;

use Illuminate\Database\Eloquent\Model;

class JourneyI18n extends Model
{
    protected $table = 'journey_i18n';

    protected $fillable = ['journey_id', 'lang', 'name', 'description'];
}
