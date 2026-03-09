<?php

namespace App\Http\Requests\Merchant;

use Illuminate\Foundation\Http\FormRequest;

class MerchantSearchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'q' => 'nullable|string|max:200',
            'journey_code' => 'nullable|string|max:10',
            'place_code' => 'nullable|string|max:80',
            'tier_code' => 'nullable|string|in:XL,E,M,S',
            'price_level' => 'nullable|integer|min:1|max:5',
            'min_rating' => 'nullable|numeric|min:0|max:5',
            'limit' => 'nullable|integer|min:1|max:200',
            'page' => 'nullable|integer|min:1',
            // User-only filters
            'user_id' => 'nullable|integer',
            'visited' => 'nullable|boolean',
            'is_favorite' => 'nullable|boolean',
            'is_wishlist' => 'nullable|boolean',
        ];
    }
}
