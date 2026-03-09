<?php

namespace App\Http\Requests\Merchant;

use Illuminate\Foundation\Http\FormRequest;

class ReviewRequest extends FormRequest
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
            'place_id' => 'nullable|integer|exists:place,place_id',
            'journey_code' => 'nullable|string|exists:journey,journey_code',
            'rating' => 'required|integer|min:1|max:5',
            'title' => 'nullable|string|max:255',
            'review_text' => 'nullable|string|max:2000',
        ];
    }
}
