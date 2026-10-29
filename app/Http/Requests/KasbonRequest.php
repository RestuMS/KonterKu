<?php

namespace App\Http\Requests;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;

class KasbonRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_name'  => ['required', 'string', 'max:150'],
            'customer_phone' => ['nullable', 'string', 'max:25'],
            'product_id'     => ['nullable', 'exists:products,id'],
            'product_name'   => ['nullable', 'string', 'max:255'],
            'quantity'       => ['nullable', 'integer', 'min:1'],
            'cost_price'     => ['nullable', 'numeric', 'min:0'],
            'amount'         => ['required', 'numeric', 'min:100'],
            'debt_date'      => ['required', 'date'],
            'due_date'       => ['nullable', 'date', 'after_or_equal:debt_date'],
            'description'    => ['nullable', 'string', 'max:500'],
            'notes'          => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'customer_name.required'  => 'Nama pelanggan / peminjam kasbon wajib diisi.',
            'customer_name.max'       => 'Nama pelanggan maksimal 150 karakter.',
            'amount.required'         => 'Nominal hutang kasbon wajib diisi.',
            'amount.numeric'          => 'Nominal hutang harus berupa angka yang valid.',
            'amount.min'              => 'Nominal hutang kasbon minimal Rp 100.',
            'debt_date.required'      => 'Tanggal berhutang kasbon wajib diisi.',
            'debt_date.date'          => 'Format tanggal berhutang tidak valid.',
            'due_date.date'           => 'Format tanggal jatuh tempo tidak valid.',
            'due_date.after_or_equal' => 'Tanggal jatuh tempo tidak boleh mendahului tanggal berhutang.',
            'quantity.integer'        => 'Jumlah kuantitas harus berupa bilangan bulat.',
            'quantity.min'            => 'Jumlah kuantitas minimal 1 unit.',
            'cost_price.numeric'      => 'Harga modal harus berupa angka.',
            'cost_price.min'          => 'Harga modal tidak boleh bernilai negatif.',
            'product_id.exists'       => 'Produk katalog yang dipilih tidak valid atau sudah tidak tersedia.',
        ];
    }

    /**
     * Validasi tambahan:
     * 1. Cek stok produk fisik katalog saat pencatatan kasbon baru (store)
     * 2. Proteksi kasbon berstatus 'paid' (lunas) agar tidak bisa diedit sembarangan (update)
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $kasbon = $this->route('kasbon');

            // Proteksi saat update: Larang edit jika kasbon sudah lunas
            if ($kasbon && $kasbon->status === 'paid') {
                $validator->errors()->add(
                    'status',
                    "Kasbon an. {$kasbon->customer_name} sudah berstatus LUNAS dan tercatat di pembukuan pemasukan. Batalkan status lunas terlebih dahulu jika ingin mengoreksi data kasbon ini."
                );
                return;
            }

            // Validasi ketersediaan stok fisik katalog
            if ($this->filled('product_id')) {
                $product = Product::find($this->product_id);
                if ($product) {
                    $requestedQty = (int) ($this->quantity ?? 1);
                    
                    // Pada mode store: Stok harus mencukupi kuantitas kasbon
                    if (!$kasbon && $product->stock < $requestedQty) {
                        $validator->errors()->add(
                            'product_id',
                            "Stok produk '{$product->name}' tidak mencukupi untuk dicatat sebagai kasbon (sisa stok: {$product->stock} {$product->unit}, diminta: {$requestedQty} {$product->unit})."
                        );
                    }
                }
            }
        });
    }
}
