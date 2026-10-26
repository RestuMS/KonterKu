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
