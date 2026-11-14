<?php

namespace App\Http\Requests;

use App\Support\CurrencyHelper;
use Illuminate\Foundation\Http\FormRequest;

class StockAdjustmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $merge = [];
        if ($this->has('cost_price') && $this->input('cost_price') !== null && $this->input('cost_price') !== '') {
            $merge['cost_price'] = CurrencyHelper::parse($this->input('cost_price'));
        }
        if ($this->has('expense_amount') && $this->input('expense_amount') !== null && $this->input('expense_amount') !== '') {
            $merge['expense_amount'] = CurrencyHelper::parse($this->input('expense_amount'));
        }
        if (!empty($merge)) {
            $this->merge($merge);
        }
    }

    public function rules(): array
    {
        $minVal = in_array($this->input('adjustment_type'), ['add', 'reduce', 'inject']) ? 1 : 0;

        return [
            'product_id' => ['required', 'exists:products,id'],
            'adjustment_type' => ['required', 'in:set,add,reduce,inject'],
            'stock_value' => ['required', 'integer', "min:{$minVal}"],
            'cost_price' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:255'],
            'record_expense' => ['nullable'],
            'payment_method' => ['nullable', 'in:cash,transfer'],
            'expense_amount' => ['nullable', 'numeric', 'min:0'],
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
