<?php

namespace App\Models\MasterData;

use Illuminate\Database\Eloquent\Model;

class MdPartnerTier extends Model
{
    protected $table = 'md_partner_tier';
    protected $primaryKey = 'tier_code';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'tier_code',
        'tier_name_th',
        'tier_name_en',
    ];
}
