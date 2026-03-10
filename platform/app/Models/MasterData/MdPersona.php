<?php

namespace App\Models\MasterData;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MdPersona extends Model
{
    protected $table = 'md_persona';
    protected $primaryKey = 'persona_code';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'persona_code',
        'persona_name_th',
        'persona_name_en',
    ];

    public function i18n(): HasMany
    {
        return $this->hasMany(MdPersonaI18n::class, 'persona_code', 'persona_code');
    }
}
