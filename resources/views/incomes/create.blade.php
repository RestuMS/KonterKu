<x-layouts.app title="Catat Pemasukan Baru">
    <x-slot:header>
        Catat Transaksi Pemasukan
    </x-slot:header>

    <div class="max-w-3xl mx-auto">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-slate-900 dark:text-white">Form Transaksi Pemasukan</h2>
                <p class="text-xs text-slate-500 mt-0.5">Catat penjualan produk konter atau pemasukan jasa non-produk.</p>
            </div>
            <x-button href="{{ route('incomes.index') }}" variant="secondary" size="sm">
                &larr; Kembali
            </x-button>
        </div>

        <x-card>
            <form
                method="POST"
                action="{{ route('incomes.store') }}"
                class="space-y-6"
                x-data="{
                    mode: 'product',
                    selectedProduct: '{{ old('product_id', '') }}',
                    qty: {{ old('quantity', 1) }},
                    unitPrice: 0,
                    amountInput: '{{ old('amount', '') }}',
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

                <!-- Mode Selector Tab: Produk Konter vs Jasa / Pemasukan Umum -->
                <div>
                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">
                        Jenis Transaksi Pemasukan
                    </label>
                    <div class="grid grid-cols-2 gap-3">
                        <button
                            type="button"
                            @click="mode = 'product'"
                            :class="mode === 'product' ? 'border-indigo-600 bg-indigo-50/50 dark:bg-indigo-950/40 text-indigo-700 dark:text-indigo-300 ring-2 ring-indigo-500/20' : 'border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-750'"
                            class="p-3.5 rounded-xl border text-left flex items-center gap-3 transition-all cursor-pointer"
                        >
                            <div class="w-8 h-8 rounded-lg bg-indigo-600/10 text-indigo-600 flex items-center justify-center font-bold">
                                🛒
                            </div>
                            <div>
                                <span class="text-sm font-semibold block leading-tight">Penjualan Produk</span>
                                <span class="text-[11px] opacity-75">Stok otomatis berkurang</span>
                            </div>
                        </button>

                        <button
                            type="button"
                            @click="mode = 'general'; selectedProduct = '';"
                            :class="mode === 'general' ? 'border-indigo-600 bg-indigo-50/50 dark:bg-indigo-950/40 text-indigo-700 dark:text-indigo-300 ring-2 ring-indigo-500/20' : 'border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-750'"
                            class="p-3.5 rounded-xl border text-left flex items-center gap-3 transition-all cursor-pointer"
                        >
                            <div class="w-8 h-8 rounded-lg bg-indigo-600/10 text-indigo-600 flex items-center justify-center font-bold">
                                ⚡
                            </div>
                            <div>
                                <span class="text-sm font-semibold block leading-tight">Jasa & Lain-lain</span>
                                <span class="text-[11px] opacity-75">Transfer, token, servis, fee admin</span>
                            </div>
                        </button>
                    </div>
                </div>

                <!-- Product Selection (When in Product Mode) -->
                <div x-show="mode === 'product'" class="space-y-4 pt-2 border-t border-slate-100 dark:border-slate-700/60">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                            Pilih Produk Konter <span class="text-rose-500">*</span>
                        </label>
                        <select
                            name="product_id"
                            x-model="selectedProduct"
                            @change="updatePrice()"
                            class="block w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 text-sm py-2.5 px-3.5"
                        >
                            <option value="">-- Pilih Produk Yang Terjual --</option>
                            @foreach($products as $p)
                                <option value="{{ $p->id }}" data-price="{{ $p->selling_price }}">
                                    {{ $p->name }} (Kode: {{ $p->code }}) — Stok: {{ $p->stock }} {{ $p->unit }} — Rp {{ number_format($p->selling_price, 0, ',', '.') }}
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
                                @input="if (mode === 'product' && selectedProduct) amountInput = unitPrice * (qty || 1)"
                                helper="Berapa unit yang dibeli pelanggan"
                            />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                                Subtotal Otomatis
                            </label>
                            <div class="p-2.5 bg-slate-50 dark:bg-slate-750 border border-slate-200 dark:border-slate-700 rounded-xl text-emerald-600 font-bold text-base flex items-center">
                                <span>Rp </span>
                                <span class="ml-1" x-text="new Intl.NumberFormat('id-ID').format(total)">0</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Date, Category, Payment Method, Amount -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2 border-t border-slate-100 dark:border-slate-700/60">
                    <x-input
                        label="Tanggal Transaksi"
                        name="transaction_date"
                        type="datetime-local"
                        value="{{ old('transaction_date', now()->format('Y-m-d\TH:i')) }}"
                        required
                    />

                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                            Kategori Pemasukan <span class="text-rose-500">*</span>
                        </label>
                        <select
                            name="category_id"
                            required
                            class="block w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 text-sm py-2.5 px-3.5"
                        >
                            <option value="">-- Pilih Kategori Pemasukan --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Total Amount Input -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                            Total Nominal Pembayaran <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400 font-bold text-sm pointer-events-none">Rp</span>
                            <input
                                type="number"
                                name="amount"
                                min="0.01"
                                step="0.01"
                                x-model="amountInput"
                                placeholder="Contoh: 50000"
                                required
                                class="w-full pl-10 pr-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 text-sm font-mono font-bold"
                            />
                        </div>
                        <p class="text-[11px] text-slate-400 mt-1">Nominal uang tunai/transfer yang diterima dari pelanggan (harus &gt; 0)</p>
                    </div>

                    <x-select label="Metode Pembayaran" name="payment_method" required>
                        <option value="cash" {{ old('payment_method') === 'cash' ? 'selected' : '' }}>Tunai (Cash)</option>
                        <option value="transfer" {{ old('payment_method') === 'transfer' ? 'selected' : '' }}>Transfer Bank</option>
                        <option value="qris" {{ old('payment_method') === 'qris' ? 'selected' : '' }}>QRIS / E-Wallet</option>
                    </x-select>
                </div>

                <x-textarea
                    label="Catatan Transaksi (Opsional)"
                    name="notes"
                    placeholder="Contoh: No HP pelanggan 0812xxx, pembelian pulsa regular, dsb."
                    value="{{ old('notes') }}"
                />

                <div class="pt-4 border-t border-slate-100 dark:border-slate-700/60 flex items-center justify-end gap-3">
                    <x-button href="{{ route('incomes.index') }}" variant="secondary">
                        Batal
                    </x-button>
                    <button
                        type="submit"
                        :disabled="submitting"
                        class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm transition-all shadow-md shadow-indigo-600/20 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer"
                    >
                        <svg x-show="submitting" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                        <span x-text="submitting ? 'Menyimpan...' : 'Simpan Transaksi Pemasukan'">Simpan Transaksi Pemasukan</span>
                    </button>
                </div>
            </form>
        </x-card>
    </div>
</x-layouts.app>
