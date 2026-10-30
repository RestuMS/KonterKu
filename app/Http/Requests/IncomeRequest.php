<?php

namespace App\Http\Requests;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;

class IncomeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Jika sedang update/edit transaksi, hanya harga jual, kategori, metode bayar, dan tanggal yang divalidasi
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            return [
                'amount'           => ['required', 'numeric', 'gt:0'],
                'category_id'      => ['required', 'exists:categories,id'],
                'payment_method'   => ['required', 'in:cash,transfer,qris'],
                'transaction_date' => ['nullable', 'date'],
            ];
        }

        // Aturan validasi untuk transaksi penjualan baru (store)
        return [
            'transaction_date' => ['nullable', 'date'],
            'product_name'     => ['required', 'string', 'max:255'],
            'category_id'      => ['required', 'exists:categories,id'],
            'payment_method'   => ['required', 'in:cash,transfer,qris'],
            'amount'           => ['required', 'numeric', 'gt:0'],
            'unit_price'       => ['nullable', 'numeric', 'min:0'],
            'quantity'         => ['required', 'integer', 'min:1', 'max:10000'],
            'cost_price'       => ['nullable', 'numeric', 'min:0'],
            'customer_target'  => ['nullable', 'string', 'max:100'],
            'notes'            => ['nullable', 'string', 'max:500'],
            'product_id'       => ['nullable', 'exists:products,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'amount.required'           => 'Total nominal pembayaran / harga jual wajib diisi.',
            'amount.numeric'            => 'Total nominal pembayaran harus berupa angka yang valid.',
            'amount.gt'                 => 'Total nominal pembayaran harus lebih besar dari Rp 0.',
            'category_id.required'      => 'Kategori transaksi pemasukan wajib dipilih.',
            'category_id.exists'        => 'Kategori yang dipilih tidak valid atau tidak ditemukan dalam sistem.',
            'payment_method.required'   => 'Metode pembayaran wajib dipilih.',
            'payment_method.in'         => 'Metode pembayaran harus berupa Tunai (Cash), QRIS, atau Transfer Bank.',
            'transaction_date.date'     => 'Format tanggal transaksi tidak valid.',
            'product_name.required'     => 'Nama produk / item transaksi penjualan wajib diisi.',
            'product_name.max'          => 'Nama produk maksimal 255 karakter.',
            'cost_price.numeric'        => 'Harga modal / HPP harus berupa angka.',
            'cost_price.min'            => 'Harga modal / HPP tidak boleh bernilai negatif.',
            'quantity.required'         => 'Jumlah kuantitas (Qty) wajib diisi.',
            'quantity.integer'          => 'Jumlah kuantitas harus berupa bilangan bulat.',
            'quantity.min'              => 'Jumlah kuantitas transaksi minimal 1 unit/kali.',
            'quantity.max'              => 'Jumlah kuantitas transaksi maksimal 10.000.',
            'customer_target.max'       => 'Nomor tujuan / rekening / ID pelanggan maksimal 100 karakter.',
            'notes.max'                 => 'Catatan transaksi maksimal 500 karakter.',
            'product_id.exists'         => 'Produk katalog yang dipilih tidak valid atau sudah tidak tersedia.',
        ];
    }

    /**
     * Validasi tambahan setelah validasi dasar:
     * 1. Cek nomor rekening/tujuan untuk layanan Transfer & Tarik Tunai
     * 2. Cek ketersediaan stok produk katalog
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if (!$this->isMethod('PUT') && !$this->isMethod('PATCH')) {
                // 1. Validasi Nomor Tujuan / Rekening sesuai Kategori Transaksi
                if ($this->filled('category_id')) {
                    $category = \App\Models\Category::find($this->category_id);
                    if ($category) {
                        $catName = strtolower($category->name);
                        $slug = strtolower($category->slug ?? '');
                        $prodName = strtolower($this->product_name ?? '');

                        $isVoucher = str_contains($catName, 'voucher') || str_contains($slug, 'voucher') || str_contains($prodName, 'voucher');
                        $isPerdana = str_contains($catName, 'perdana') || str_contains($slug, 'perdana') || str_contains($prodName, 'perdana');

                        // Voucher fisik & kartu perdana otomatis, tidak wajib nomor HP pembeli
                        if (!$isVoucher && !$isPerdana) {
                            $isTransfer = str_contains($catName, 'transfer') || str_contains($catName, 'tarik tunai') || str_contains($slug, 'transfer') || str_contains($slug, 'tarik');
                            $isToken = str_contains($catName, 'token') || str_contains($catName, 'pln') || str_contains($slug, 'token') || str_contains($slug, 'pln');
                            $isEwallet = str_contains($catName, 'wallet') || str_contains($catName, 'dana') || str_contains($catName, 'gopay') || str_contains($catName, 'shopee') || str_contains($catName, 'ovo') || str_contains($catName, 'seabank') || str_contains($slug, 'wallet') || str_contains($slug, 'ewallet');
                            $isData = str_contains($catName, 'paket') || str_contains($catName, 'data') || str_contains($catName, 'kuota') || str_contains($catName, 'internet') || str_contains($slug, 'paket') || str_contains($slug, 'data') || str_contains($slug, 'internet');
                            $isPulsa = str_contains($catName, 'pulsa') || str_contains($slug, 'pulsa');

                            $targetVal = trim($this->customer_target ?? '');

                            if ($isTransfer && empty($targetVal)) {
                                $validator->errors()->add('customer_target', "Nomor rekening / nomor tujuan wajib diisi untuk transaksi {$category->name}.");
                            } elseif ($isToken && empty($targetVal)) {
                                $validator->errors()->add('customer_target', 'Nomor meteran / ID Pelanggan PLN wajib diisi untuk transaksi Token Listrik.');
                            } elseif ($isEwallet && empty($targetVal)) {
                                $validator->errors()->add('customer_target', 'Nomor HP tujuan akun E-Wallet wajib diisi.');
                            } elseif ($isData && empty($targetVal)) {
                                $validator->errors()->add('customer_target', 'Nomor HP tujuan paket data wajib diisi.');
                            } elseif ($isPulsa && empty($targetVal)) {
                                $validator->errors()->add('customer_target', 'Nomor HP tujuan pulsa wajib diisi.');
                            }
                        }
                    }
                }

                // 2. Validasi Ketersediaan Stok Produk Katalog
                if ($this->filled('product_id')) {
                    $product = Product::find($this->product_id);
                    if ($product) {
                        $qty = (int) ($this->quantity ?? 1);
                        if ($product->stock < $qty) {
                            $validator->errors()->add(
                                'quantity',
                                "Stok produk '{$product->name}' tidak mencukupi (sisa stok: {$product->stock} {$product->unit}, diminta: {$qty} {$product->unit})."
                            );
                        }
                    }
                }
            }
        });
    }
}
