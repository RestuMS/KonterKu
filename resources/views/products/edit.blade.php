<x-layouts.app title="Edit Produk {{ $product->name }}">
    <x-slot:header>
        Edit Produk
    </x-slot:header>

    <div class="max-w-3xl mx-auto">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-slate-900 dark:text-white">Edit Produk: {{ $product->name }}</h2>
                <p class="text-xs text-slate-500 mt-0.5">Perbarui informasi harga, kategori, atau nama produk.</p>
            </div>
            <x-button href="{{ route('products.index') }}" variant="secondary" size="sm">
                &larr; Kembali
            </x-button>
        </div>

        <x-card>
            <form method="POST" action="{{ route('products.update', $product) }}" class="space-y-5">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <x-input
                        label="Kode / Barcode / SKU"
                        name="code"
                        value="{{ old('code', $product->code) }}"
                        required
                    />

                    <x-select label="Kategori Produk" name="category_id" required>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </x-select>
                </div>

                <x-input
                    label="Nama Produk"
                    name="name"
                    value="{{ old('name', $product->name) }}"
                    required
                />

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <x-input
                        label="Harga Modal (Beli)"
                        name="cost_price"
                        type="number"
                        min="0"
                        step="0.01"
                        prefix="Rp"
                        value="{{ old('cost_price', $product->cost_price) }}"
                        required
                    />

                    <x-input
                        label="Harga Jual"
                        name="selling_price"
                        type="number"
                        min="0"
                        step="0.01"
                        prefix="Rp"
                        value="{{ old('selling_price', $product->selling_price) }}"
                        required
                    />
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <x-input
                        label="Jumlah Stok"
                        name="stock"
                        type="number"
                        min="0"
                        value="{{ old('stock', $product->stock) }}"
                        required
                        helper="Untuk penyesuaian besar gunakan menu Mutasi Stok"
                    />

                    <x-input
                        label="Batas Minimum Stok"
                        name="min_stock"
                        type="number"
                        min="0"
                        value="{{ old('min_stock', $product->min_stock) }}"
                        required
                    />

                    <x-input
                        label="Satuan Unit"
                        name="unit"
                        value="{{ old('unit', $product->unit) }}"
                        required
                    />
                </div>

                <x-textarea
                    label="Catatan Produk (Opsional)"
                    name="notes"
                    value="{{ old('notes', $product->notes) }}"
                />

                <div class="pt-2">
                    <label class="flex items-center gap-2 cursor-pointer select-none text-sm text-slate-700 dark:text-slate-300 font-medium">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $product->is_active) ? 'checked' : '' }} class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                        <span>Aktifkan produk untuk transaksi kasir</span>
                    </label>
                </div>

                <div class="pt-4 border-t border-slate-100 dark:border-slate-700/60 flex items-center justify-end gap-3">
                    <x-button href="{{ route('products.index') }}" variant="secondary">
                        Batal
                    </x-button>
                    <x-button type="submit" variant="primary" class="font-semibold shadow-xs">
                        Perbarui Data Produk
                    </x-button>
                </div>
            </form>
        </x-card>
    </div>
</x-layouts.app>
