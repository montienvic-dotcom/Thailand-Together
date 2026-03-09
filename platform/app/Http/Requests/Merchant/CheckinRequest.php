<?php

namespace App\Http\Requests\Merchant;

use Illuminate\Foundation\Http\FormRequest;

class CheckinRequest extends FormRequest
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
            'place_id' => 'required|integer|exists:place,place_id',
            'journey_code' => 'nullable|string|exists:journey,journey_code',
            'checkin_method' => 'nullable|string|in:QR,GPS,MANUAL,NFC',
            'note' => 'nullable|string|max:255',
            'tp_awarded' => 'nullable|integer|min:0',
        ];
    }
}
