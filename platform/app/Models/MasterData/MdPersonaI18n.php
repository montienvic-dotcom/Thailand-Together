<?php

namespace App\Models\MasterData;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MdPersonaI18n extends Model
{
    protected $table = 'md_persona_i18n';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'persona_code',
        'lang',
        'persona_name',
    ];

    public function persona(): BelongsTo
    {
        return $this->belongsTo(MdPersona::class, 'persona_code', 'persona_code');
    }
}
