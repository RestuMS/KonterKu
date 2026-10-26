<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IncomeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'transaction_date' => ['required', 'date'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'payment_method' => ['required', 'in:cash,transfer,qris'],
            'notes' => ['nullable', 'string', 'max:500'],
            'product_id' => ['nullable', 'exists:products,id'],
            'quantity' => ['nullable', 'integer', 'min:1'],
            'amount' => ['required', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'amount.required' => 'Nominal transaksi wajib diisi.',
            'amount.min' => 'Nominal transaksi tidak boleh kurang dari 0.',
            'transaction_date.required' => 'Tanggal transaksi wajib diisi.',
            'payment_method.required' => 'Metode pembayaran wajib dipilih.',
        ];
    }
}
