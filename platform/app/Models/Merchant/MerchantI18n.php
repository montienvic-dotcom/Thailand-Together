<?php

namespace App\Models\Merchant;

use Illuminate\Database\Eloquent\Model;

class MerchantI18n extends Model
{
    protected $table = 'merchant_i18n';

    protected $fillable = ['merchant_id', 'lang', 'name', 'description'];
}
