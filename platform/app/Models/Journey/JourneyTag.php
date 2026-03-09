<?php

namespace App\Models\Journey;

use Illuminate\Database\Eloquent\Model;

class JourneyTag extends Model
{
    protected $table = 'journey_tag';
    public $timestamps = false;

    protected $fillable = ['journey_id', 'tag_code'];
}
