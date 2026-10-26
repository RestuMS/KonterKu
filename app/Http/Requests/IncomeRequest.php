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
            'category_id' => ['required', 'exists:categories,id'],
            'payment_method' => ['required', 'in:cash,transfer,qris'],
            'notes' => ['nullable', 'string', 'max:500'],
            'product_id' => ['nullable', 'exists:products,id'],
            'quantity' => ['nullable', 'integer', 'min:1'],
            'amount' => ['required', 'numeric', 'gt:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'amount.required' => 'Nominal transaksi pemasukan wajib diisi.',
            'amount.gt' => 'Nominal transaksi harus lebih besar dari 0 (tidak boleh 0 atau negatif).',
            'amount.numeric' => 'Nominal transaksi harus berupa angka yang valid.',
            'transaction_date.required' => 'Tanggal transaksi wajib diisi.',
            'transaction_date.date' => 'Format tanggal transaksi tidak valid.',
            'category_id.required' => 'Kategori pemasukan wajib dipilih.',
            'category_id.exists' => 'Kategori yang dipilih tidak valid.',
            'payment_method.required' => 'Metode pembayaran wajib dipilih.',
            'payment_method.in' => 'Metode pembayaran harus berupa tunai, transfer, atau QRIS.',
            'quantity.min' => 'Jumlah kuantitas minimal 1 unit.',
        ];
    }
}
