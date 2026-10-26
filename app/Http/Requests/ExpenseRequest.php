<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExpenseRequest extends FormRequest
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
            'amount' => ['required', 'numeric', 'min:1'],
            'payment_method' => ['required', 'in:cash,transfer,qris'],
            'notes' => ['required', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'amount.required' => 'Nominal pengeluaran wajib diisi.',
            'amount.min' => 'Nominal pengeluaran minimal Rp 1.',
            'notes.required' => 'Keterangan pengeluaran wajib diisi.',
            'transaction_date.required' => 'Tanggal pengeluaran wajib diisi.',
            'payment_method.required' => 'Metode pembayaran wajib dipilih.',
        ];
    }
}
