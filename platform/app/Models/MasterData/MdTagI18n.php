<?php

namespace App\Models\MasterData;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MdTagI18n extends Model
{
    protected $table = 'md_tag_i18n';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'tag_id',
        'lang',
        'tag_name',
    ];

    public function tag(): BelongsTo
    {
        return $this->belongsTo(MdTag::class, 'tag_id', 'tag_id');
    }
}
