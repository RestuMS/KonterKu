<x-layouts.app title="Penyesuaian Stok Barang">
    <x-slot:header>
        Penyesuaian / Opname Stok
    </x-slot:header>

    <div class="max-w-2xl mx-auto">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-slate-900 dark:text-white">Form Penyesuaian Stok</h2>
                <p class="text-xs text-slate-500 mt-0.5">Tambah stok kulakan, catat barang rusak, atau samakan stok dengan hasil opname fisik.</p>
            </div>
            <x-button href="{{ route('stocks.index') }}" variant="secondary" size="sm">
                &larr; Kembali
            </x-button>
        </div>

        <x-card>
            <form
                method="POST"
                action="{{ route('stocks.store') }}"
                class="space-y-5"
                x-data="{
                    selectedId: '',
                    products: {{ $products->map(fn($p) => ['id' => $p->id, 'name' => $p->name, 'stock' => $p->stock, 'unit' => $p->unit])->toJson() }},
                    currentStock: null,
                    currentUnit: 'pcs',
                    type: 'add',
                    updateCurrent() {
                        const p = this.products.find(item => item.id == this.selectedId);
                        if (p) {
                            this.currentStock = p.stock;
                            this.currentUnit = p.unit;
                        } else {
                            this.currentStock = null;
                        }
                    }
                }"
            >
                @csrf

                <!-- Pilih Produk -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                        Pilih Produk <span class="text-rose-500">*</span>
                    </label>
                    <select
                        name="product_id"
                        x-model="selectedId"
                        @change="updateCurrent()"
                        required
                        class="block w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 text-sm py-2.5 px-3.5"
                    >
                        <option value="">-- Pilih Produk Yang Akan Disesuaikan --</option>
                        @foreach($products as $p)
                            <option value="{{ $p->id }}">
                                {{ $p->name }} (Kode: {{ $p->code }}) — Stok Sekarang: {{ $p->stock }} {{ $p->unit }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Info Stok Saat Ini -->
                <div x-show="currentStock !== null" class="p-3.5 bg-slate-50 dark:bg-slate-750 border border-slate-200 dark:border-slate-700 rounded-xl flex items-center justify-between text-xs">
                    <span class="text-slate-500">Stok Saat Ini di Sistem:</span>
                    <span class="font-bold text-slate-900 dark:text-white text-sm" x-text="`${currentStock} ${currentUnit}`"></span>
                </div>

                <!-- Jenis Penyesuaian -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                        Tindakan Penyesuaian <span class="text-rose-500">*</span>
                    </label>
                    <div class="grid grid-cols-3 gap-3">
                        <label class="flex flex-col items-center justify-center p-3 rounded-xl border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-750 cursor-pointer text-center" :class="type === 'add' ? 'border-indigo-600 bg-indigo-50/50 dark:bg-indigo-950/40 text-indigo-700' : ''">
                            <input type="radio" name="adjustment_type" value="add" x-model="type" class="sr-only">
                            <span class="text-xs font-bold block mb-0.5">+ Tambah Stok</span>
                            <span class="text-[10px] text-slate-400">Kulakan / Barang Datang</span>
                        </label>

                        <label class="flex flex-col items-center justify-center p-3 rounded-xl border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-750 cursor-pointer text-center" :class="type === 'reduce' ? 'border-rose-600 bg-rose-50/50 dark:bg-rose-950/40 text-rose-700' : ''">
                            <input type="radio" name="adjustment_type" value="reduce" x-model="type" class="sr-only">
                            <span class="text-xs font-bold block mb-0.5">- Kurangi Stok</span>
                            <span class="text-[10px] text-slate-400">Rusak / Hilang / Kadaluarsa</span>
                        </label>

                        <label class="flex flex-col items-center justify-center p-3 rounded-xl border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-750 cursor-pointer text-center" :class="type === 'set' ? 'border-amber-600 bg-amber-50/50 dark:bg-amber-950/40 text-amber-700' : ''">
                            <input type="radio" name="adjustment_type" value="set" x-model="type" class="sr-only">
                            <span class="text-xs font-bold block mb-0.5">= Set Nilai Opname</span>
                            <span class="text-[10px] text-slate-400">Hasil Hitung Fisik</span>
                        </label>
                    </div>
                </div>

                <!-- Input Nilai Stok -->
                <div>
                    <x-input
                        label="Jumlah Stok"
                        name="stock_value"
                        type="number"
                        min="0"
                        placeholder="Contoh: 10"
                        value="{{ old('stock_value') }}"
                        required
                        helper="Masukkan jumlah penambahan, pengurangan, atau nilai hasil opname"
                    />
                </div>

                <!-- Alasan / Catatan -->
                <x-textarea
                    label="Alasan Penyesuaian"
                    name="notes"
                    placeholder="Contoh: Kulakan dari supplier A, barang rusak saat pengiriman, hasil opname akhir pekan, dsb."
                    value="{{ old('notes') }}"
                    required
                />

                <div class="pt-4 border-t border-slate-100 dark:border-slate-700/60 flex items-center justify-end gap-3">
                    <x-button href="{{ route('stocks.index') }}" variant="secondary">
                        Batal
                    </x-button>
                    <x-button type="submit" variant="primary" class="font-semibold shadow-xs">
                        Simpan Penyesuaian Stok
                    </x-button>
                </div>
            </form>
        </x-card>
    </div>
</x-layouts.app>
