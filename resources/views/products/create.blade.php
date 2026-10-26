<x-layouts.app title="Tambah Produk Baru">
    <x-slot:header>
        Tambah Produk Baru
    </x-slot:header>

    <div class="max-w-3xl mx-auto">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-slate-900 dark:text-white">Form Produk Baru</h2>
                <p class="text-xs text-slate-500 mt-0.5">Daftarkan produk baru untuk dijual di konter.</p>
            </div>
            <x-button href="{{ route('products.index') }}" variant="secondary" size="sm">
                &larr; Kembali
            </x-button>
        </div>

        <x-card>
            <form method="POST" action="{{ route('products.store') }}" class="space-y-5">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <x-input
                        label="Kode / Barcode / SKU"
                        name="code"
                        placeholder="Contoh: VOUCHER-TELK-5GB"
                        value="{{ old('code') }}"
                        required
                        helper="Kode unik untuk identifikasi produk"
                    />

                    <x-select label="Kategori Produk" name="category_id" required>
                        <option value="">-- Pilih Kategori --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </x-select>
                </div>

                <x-input
                    label="Nama Produk"
                    name="name"
                    placeholder="Contoh: Voucher Telkomsel 5GB 30 Hari"
                    value="{{ old('name') }}"
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
                        placeholder="Contoh: 18000"
                        value="{{ old('cost_price') }}"
                        required
                        helper="Harga kulakan modal per unit"
                    />

                    <x-input
                        label="Harga Jual"
                        name="selling_price"
                        type="number"
                        min="0"
                        step="0.01"
                        prefix="Rp"
                        placeholder="Contoh: 22000"
                        value="{{ old('selling_price') }}"
                        required
                        helper="Harga jual kepada pembeli"
                    />
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <x-input
                        label="Jumlah Stok Awal"
                        name="stock"
                        type="number"
                        min="0"
                        placeholder="Contoh: 20"
                        value="{{ old('stock', 0) }}"
                        required
                    />

                    <x-input
                        label="Batas Minimum Stok"
                        name="min_stock"
                        type="number"
                        min="0"
                        placeholder="Contoh: 5"
                        value="{{ old('min_stock', 5) }}"
                        required
                        helper="Peringatan saat stok menipis"
                    />

                    <x-input
                        label="Satuan Unit"
                        name="unit"
                        placeholder="Contoh: pcs / voucher / kartu"
                        value="{{ old('unit', 'pcs') }}"
                        required
                    />
                </div>

                <x-textarea
                    label="Catatan Produk (Opsional)"
                    name="notes"
                    placeholder="Keterangan tambahan mengenai produk..."
                    value="{{ old('notes') }}"
                />

                <div class="pt-2">
                    <label class="flex items-center gap-2 cursor-pointer select-none text-sm text-slate-700 dark:text-slate-300 font-medium">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', '1') ? 'checked' : '' }} class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                        <span>Aktifkan produk untuk transaksi kasir</span>
                    </label>
                </div>

                <div class="pt-4 border-t border-slate-100 dark:border-slate-700/60 flex items-center justify-end gap-3">
                    <x-button href="{{ route('products.index') }}" variant="secondary">
                        Batal
                    </x-button>
                    <x-button type="submit" variant="primary" class="font-semibold shadow-xs">
                        Simpan Produk
                    </x-button>
                </div>
            </form>
        </x-card>
    </div>
</x-layouts.app>
