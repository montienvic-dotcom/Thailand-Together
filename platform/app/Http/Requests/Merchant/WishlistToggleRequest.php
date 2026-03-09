<?php

namespace App\Http\Requests\Merchant;

use Illuminate\Foundation\Http\FormRequest;

class WishlistToggleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => 'required|integer',
            'merchant_id' => 'required|integer|exists:merchant,merchant_id',
            'is_wishlist' => 'required|boolean',
        ];
    }
}
