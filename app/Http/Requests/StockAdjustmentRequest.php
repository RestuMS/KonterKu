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
        $minVal = in_array($this->input('adjustment_type'), ['add', 'reduce']) ? 1 : 0;

        return [
            'product_id' => ['required', 'exists:products,id'],
            'adjustment_type' => ['required', 'in:set,add,reduce'],
            'stock_value' => ['required', 'integer', "min:{$minVal}"],
            'notes' => ['required', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'product_id.required' => 'Produk wajib dipilih.',
            'product_id.exists' => 'Produk yang dipilih tidak valid atau sudah tidak ada.',
            'adjustment_type.required' => 'Jenis penyesuaian stok wajib dipilih.',
            'stock_value.required' => 'Nilai jumlah stok wajib diisi.',
            'stock_value.min' => $this->input('adjustment_type') === 'set' 
                ? 'Nilai stok opname tidak boleh bernilai negatif.' 
                : 'Jumlah penambahan atau pengurangan stok minimal 1 unit.',
            'notes.required' => 'Alasan / keterangan penyesuaian stok wajib diisi.',
        ];
    }
}
