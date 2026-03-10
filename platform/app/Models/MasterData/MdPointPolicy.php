<?php

namespace App\Models\MasterData;

use Illuminate\Database\Eloquent\Model;

class MdPointPolicy extends Model
{
    protected $table = 'md_point_policy';
    protected $primaryKey = 'policy_code';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'policy_code',
        'normal_divisor',
        'goal_multiplier',
        'special_multiplier',
        'mission_checkin_normal',
        'mission_checkin_goal',
        'mission_checkin_special',
        'mission_review_normal',
        'mission_review_goal',
        'mission_review_special',
    ];

    protected $casts = [
        'goal_multiplier' => 'decimal:3',
        'special_multiplier' => 'decimal:3',
    ];
}
