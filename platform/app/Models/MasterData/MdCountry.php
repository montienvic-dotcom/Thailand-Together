<?php

namespace App\Models\MasterData;

use Illuminate\Database\Eloquent\Model;

class MdCountry extends Model
{
    protected $table = 'md_country';
    protected $primaryKey = 'country_code';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'country_code',
        'country_name_th',
        'country_name_en',
        'continent',
    ];
}
