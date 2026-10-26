<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StockAdjustmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_id' => ['required', 'exists:products,id'],
            'adjustment_type' => ['required', 'in:set,add,reduce'],
            'stock_value' => ['required', 'integer', 'min:0'],
            'notes' => ['required', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'product_id.required' => 'Produk wajib dipilih.',
            'adjustment_type.required' => 'Jenis penyesuaian stok wajib dipilih.',
            'stock_value.required' => 'Nilai jumlah stok wajib diisi.',
            'stock_value.min' => 'Nilai jumlah stok tidak boleh negatif.',
            'notes.required' => 'Alasan penyesuaian stok wajib diisi.',
        ];
    }
}
