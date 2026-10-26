<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        if ($this->has('items') && is_array($this->items)) {
            return [
                'items' => ['required', 'array', 'min:1'],
                'items.*.category_id' => ['required', 'exists:categories,id'],
                'items.*.code' => ['required', 'string', 'max:50', 'distinct', Rule::unique('products', 'code')],
                'items.*.name' => ['required', 'string', 'max:255'],
                'items.*.cost_price' => ['required', 'numeric', 'min:0'],
                'items.*.selling_price' => ['required', 'numeric', 'min:0'],
                'items.*.stock' => ['required', 'integer', 'min:0'],
                'items.*.min_stock' => ['required', 'integer', 'min:0'],
                'items.*.unit' => ['required', 'string', 'max:30'],
                'items.*.is_active' => ['nullable'],
                'items.*.notes' => ['nullable', 'string', 'max:500'],
            ];
        }

        $productId = $this->route('product') ? $this->route('product')->id : null;

        return [
            'category_id' => ['required', 'exists:categories,id'],
            'code' => ['required', 'string', 'max:50', Rule::unique('products', 'code')->ignore($productId)],
            'name' => ['required', 'string', 'max:255'],
            'cost_price' => ['required', 'numeric', 'min:0'],
            'selling_price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'min_stock' => ['required', 'integer', 'min:0'],
            'unit' => ['required', 'string', 'max:30'],
            'is_active' => ['nullable'],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'items.required' => 'Daftar produk tidak boleh kosong.',
            'items.min' => 'Minimal harus ada 1 produk yang diinput.',
            'items.*.category_id.required' => 'Kategori produk pada setiap baris wajib dipilih.',
            'items.*.code.required' => 'Kode / SKU produk pada setiap baris wajib diisi.',
            'items.*.code.distinct' => 'Terdapat kode / SKU produk yang duplikat dalam daftar yang diinput.',
            'items.*.code.unique' => 'Kode / SKU produk sudah digunakan di database.',
            'items.*.name.required' => 'Nama produk pada setiap baris wajib diisi.',
            'items.*.cost_price.required' => 'Harga modal pada setiap baris wajib diisi.',
            'items.*.selling_price.required' => 'Harga jual pada setiap baris wajib diisi.',
            'items.*.stock.required' => 'Jumlah stok pada setiap baris wajib diisi.',
            'items.*.min_stock.required' => 'Batas minimum stok wajib diisi.',
            'items.*.unit.required' => 'Satuan unit produk wajib diisi.',
            'category_id.required' => 'Kategori produk wajib dipilih.',
            'code.required' => 'Kode / barcode produk wajib diisi.',
            'code.unique' => 'Kode / barcode produk sudah digunakan.',
            'name.required' => 'Nama produk wajib diisi.',
            'cost_price.required' => 'Harga modal produk wajib diisi.',
            'selling_price.required' => 'Harga jual produk wajib diisi.',
            'stock.required' => 'Jumlah stok awal wajib diisi.',
            'min_stock.required' => 'Batas stok minimum wajib diisi.',
            'unit.required' => 'Satuan unit produk wajib diisi.',
        ];
    }
}
