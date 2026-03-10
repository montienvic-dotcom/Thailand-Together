<?php

namespace App\Models\MasterData;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MdTag extends Model
{
    protected $table = 'md_tag';
    protected $primaryKey = 'tag_id';
    public $timestamps = false;

    protected $fillable = [
        'tag_id',
        'tag_code',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function i18n(): HasMany
    {
        return $this->hasMany(MdTagI18n::class, 'tag_id', 'tag_id');
    }
}
