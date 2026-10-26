<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'shop_name' => ['required', 'string', 'max:100'],
            'shop_phone' => ['nullable', 'string', 'max:30'],
            'shop_address' => ['nullable', 'string', 'max:255'],
            'receipt_footer' => ['nullable', 'string', 'max:255'],
            'low_stock_threshold' => ['required', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'shop_name.required' => 'Nama konter wajib diisi.',
            'low_stock_threshold.required' => 'Ambang batas stok minimum wajib diisi.',
            'low_stock_threshold.min' => 'Ambang batas minimal adalah 1.',
        ];
    }
}
