<x-layouts.app title="Edit Transaksi Pemasukan {{ $income->invoice_number }}">
    <x-slot:header>
        Edit Transaksi Pemasukan
    </x-slot:header>

    @php
        $firstItem = $income->items->first();
        $initialMode = $firstItem ? 'product' : 'general';
        $initialProductId = $firstItem ? $firstItem->product_id : '';
        $initialQty = $firstItem ? $firstItem->quantity : 1;
        $initialPrice = $firstItem ? (float) $firstItem->unit_price : (float) $income->total_amount;
    @endphp

    <div class="max-w-3xl mx-auto space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <div class="flex items-center gap-2 text-xs font-semibold text-slate-500 mb-1">
                    <a href="{{ route('incomes.index') }}" class="hover:text-indigo-600 transition-colors">Pemasukan</a>
                    <span>/</span>
                    <a href="{{ route('incomes.show', $income) }}" class="hover:text-indigo-600 transition-colors">{{ $income->invoice_number }}</a>
                    <span>/</span>
                    <span class="text-indigo-600 dark:text-indigo-400">Edit</span>
                </div>
                <h2 class="text-xl font-black text-slate-900 dark:text-white flex items-center gap-2">
                    <span>Edit Transaksi:</span>
                    <span class="font-mono text-indigo-600 dark:text-indigo-400">{{ $income->invoice_number }}</span>
                </h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                    Perubahan pada item produk akan otomatis menyesuaikan stok inventaris secara konsisten.
                </p>
            </div>
            <x-button href="{{ route('incomes.show', $income) }}" variant="secondary" size="sm">
                &larr; Batal & Kembali
            </x-button>
        </div>

        <!-- Alert Notifikasi Aturan Transaksi -->
        <div class="p-4 rounded-2xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 text-xs text-amber-800 dark:text-amber-300 flex items-start gap-3">
            <svg class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            <div>
                <strong class="font-bold block">Integritas Riwayat Stok & Finansial:</strong>
                Jika Anda mengubah kuantitas atau mengganti produk, sistem akan otomatis mengembalikan stok lama dan memotong stok baru secara atomik agar laporan kas dan kartu stok tetap akurat.
            </div>
        </div>

        <x-card>
            <form
                method="POST"
                action="{{ route('incomes.update', $income) }}"
                class="space-y-6"
                x-data="{
                    mode: '{{ old('mode', $initialMode) }}',
                    selectedProduct: '{{ old('product_id', $initialProductId) }}',
                    qty: {{ old('quantity', $initialQty) }},
                    unitPrice: {{ $initialPrice }},
                    amountInput: '{{ old('amount', $income->total_amount) }}',
                    submitting: false,
                    products: {{ $products->map(fn($p) => ['id' => $p->id, 'name' => $p->name, 'price' => (float)$p->selling_price, 'stock' => $p->stock, 'unit' => $p->unit, 'category_id' => $p->category_id])->toJson() }},
                    updatePrice() {
                        const prod = this.products.find(p => p.id == this.selectedProduct);
                        if (prod) {
                            this.unitPrice = prod.price;
                            this.amountInput = this.unitPrice * (this.qty || 1);
                            if (prod.category_id) {
                                const catSelect = document.querySelector('select[name=category_id]');
                                if (catSelect) catSelect.value = prod.category_id;
                            }
                        }
                    },
                    get total() {
                        if (this.mode === 'product' && this.selectedProduct) {
                            return this.unitPrice * (this.qty || 1);
                        }
                        return parseFloat(this.amountInput) || 0;
                    }
                }"
                @submit="if(submitting) { $event.preventDefault(); return; } submitting = true;"
            >
                @csrf
                @method('PUT')

                <!-- Mode Selector Tab -->
                <div>
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">
                        Tipe Pemasukan
                    </label>
                    <div class="grid grid-cols-2 gap-3">
                        <button
                            type="button"
                            @click="mode = 'product'"
                            :class="mode === 'product' ? 'border-indigo-600 bg-indigo-50/50 dark:bg-indigo-950/40 text-indigo-700 dark:text-indigo-300 ring-2 ring-indigo-500/20' : 'border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-750'"
                            class="p-3.5 rounded-2xl border text-left flex items-center gap-3 transition-all cursor-pointer"
                        >
                            <div class="w-9 h-9 rounded-xl bg-indigo-600/10 text-indigo-600 flex items-center justify-center font-bold text-lg">
                                🛒
                            </div>
                            <div>
                                <span class="text-sm font-bold block leading-tight">Penjualan Produk Konter</span>
                                <span class="text-[11px] opacity-75">Stok otomatis disinkronkan</span>
                            </div>
                        </button>

                        <button
                            type="button"
                            @click="mode = 'general'; selectedProduct = '';"
                            :class="mode === 'general' ? 'border-indigo-600 bg-indigo-50/50 dark:bg-indigo-950/40 text-indigo-700 dark:text-indigo-300 ring-2 ring-indigo-500/20' : 'border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-750'"
                            class="p-3.5 rounded-2xl border text-left flex items-center gap-3 transition-all cursor-pointer"
                        >
                            <div class="w-9 h-9 rounded-xl bg-indigo-600/10 text-indigo-600 flex items-center justify-center font-bold text-lg">
                                ⚡
                            </div>
                            <div>
                                <span class="text-sm font-bold block leading-tight">Transfer, Tarik Tunai & Manual</span>
                                <span class="text-[11px] opacity-75">Transfer bank, tarik tunai, transaksi non-katalog</span>
                            </div>
                        </button>
                    </div>
                </div>

                <!-- Product Selection (When in Product Mode) -->
                <div x-show="mode === 'product'" class="space-y-4 pt-3 border-t border-slate-100 dark:border-slate-700/60">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                            Pilih Produk Konter <span class="text-rose-500">*</span>
                        </label>
                        <select
                            name="product_id"
                            x-model="selectedProduct"
                            @change="updatePrice()"
                            class="block w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-750 text-slate-900 dark:text-slate-100 text-xs font-semibold py-2.5 px-3.5 focus:ring-2 focus:ring-indigo-500 shadow-2xs"
                        >
                            <option value="">-- Pilih Produk Yang Terjual --</option>
                            @foreach($products as $p)
                                <option value="{{ $p->id }}" {{ old('product_id', $initialProductId) == $p->id ? 'selected' : '' }}>
                                    {{ $p->name }} (SKU: {{ $p->code }}) — Stok Tersedia: {{ $p->stock }} {{ $p->unit }} — Rp {{ number_format($p->selling_price, 0, ',', '.') }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <x-input
                                label="Jumlah (Qty)"
                                name="quantity"
                                type="number"
                                min="1"
                                x-model.number="qty"
                                @input="if (mode === 'product') amountInput = unitPrice * (qty || 1)"
                                helper="Jumlah unit yang dibeli pelanggan"
                            />
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                                Subtotal Otomatis
                            </label>
                            <div class="p-2.5 bg-slate-50 dark:bg-slate-750 border border-slate-200 dark:border-slate-700 rounded-xl text-emerald-600 font-bold text-sm flex items-center font-mono">
                                <span>Rp </span>
                                <span class="ml-1" x-text="new Intl.NumberFormat('id-ID').format(total)">0</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Date, Category, Payment Method, Amount -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-3 border-t border-slate-100 dark:border-slate-700/60">
                    <x-input
                        label="Tanggal & Waktu Transaksi"
                        name="transaction_date"
                        type="datetime-local"
                        value="{{ old('transaction_date', $income->transaction_date->format('Y-m-d\TH:i')) }}"
                        required
                    />

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                            Kategori Pemasukan <span class="text-rose-500">*</span>
                        </label>
                        <select
                            name="category_id"
                            required
                            class="block w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-750 text-slate-900 dark:text-slate-100 text-xs font-semibold py-2.5 px-3.5 focus:ring-2 focus:ring-indigo-500 shadow-2xs"
                        >
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id', $income->category_id) == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Total Amount Input -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                            Total Nominal Pembayaran (Rp) <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400 font-bold text-xs pointer-events-none">Rp</span>
                            <input
                                type="number"
                                name="amount"
                                min="0.01"
                                step="0.01"
                                x-model="amountInput"
                                required
                                placeholder="Contoh: 50000"
                                class="w-full pl-10 pr-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-750 text-slate-900 dark:text-white text-xs font-mono font-bold focus:ring-2 focus:ring-indigo-500 shadow-2xs"
                            >
                        </div>
                        <p class="text-[11px] text-slate-400 mt-1">Harus angka valid dan tidak boleh negatif.</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                            Metode Pembayaran <span class="text-rose-500">*</span>
                        </label>
                        <select
                            name="payment_method"
                            required
                            class="block w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-750 text-slate-900 dark:text-slate-100 text-xs font-semibold py-2.5 px-3.5 focus:ring-2 focus:ring-indigo-500 shadow-2xs"
                        >
                            <option value="cash" {{ old('payment_method', $income->payment_method) === 'cash' ? 'selected' : '' }}>Tunai (Cash)</option>
                            <option value="transfer" {{ old('payment_method', $income->payment_method) === 'transfer' ? 'selected' : '' }}>Transfer Bank</option>
                            <option value="qris" {{ old('payment_method', $income->payment_method) === 'qris' ? 'selected' : '' }}>QRIS / E-Wallet</option>
                        </select>
                    </div>
                </div>

                <x-textarea
                    label="Catatan Transaksi (Opsional)"
                    name="notes"
                    placeholder="Contoh: Pembelian nomor 0812xxx, transfer, dll."
                    value="{{ old('notes', $income->notes) }}"
                />

                <div class="pt-4 border-t border-slate-100 dark:border-slate-700/60 flex items-center justify-end gap-3">
                    <x-button href="{{ route('incomes.show', $income) }}" variant="secondary">
                        Batal
                    </x-button>
                    <button
                        type="submit"
                        :disabled="submitting"
                        class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs uppercase tracking-wider transition-all shadow-md shadow-indigo-600/20 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer"
                    >
                        <svg x-show="submitting" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                        <span x-text="submitting ? 'Menyimpan...' : 'Simpan Perubahan Pemasukan'">Simpan Perubahan Pemasukan</span>
                    </button>
                </div>
            </form>
        </x-card>
    </div>
</x-layouts.app>
