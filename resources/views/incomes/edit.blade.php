<x-layouts.app title="Edit Transaksi Pemasukan {{ $income->invoice_number }}">
    <x-slot:header>
        Edit Transaksi Pemasukan
    </x-slot:header>

    @php
        // Filter out aksesoris, jasa hp, lain-lain dari kategori database
        $cleanCategories = $categories->filter(function($c) {
            $name = strtolower($c->name);
            return !str_contains($name, 'aksesoris') && 
                   !str_contains($name, 'jasa hp') && 
                   !str_contains($name, 'jasa servis') && 
                   !str_contains($name, 'lain-lain') &&
                   !str_contains($name, 'operasional');
        })->values();

        $firstItem = $income->items->first();
        $itemName = $firstItem ? $firstItem->product_name : ($income->category->name ?? 'Transaksi Penjualan');
        $itemQty = $firstItem ? (int)$firstItem->quantity : 1;
        $costPrice = $firstItem ? (float)$firstItem->cost_price : 0;
        $totalCost = $costPrice * $itemQty;
    @endphp

    <div class="max-w-3xl mx-auto space-y-6">
        <!-- Header Navigasi & Info Faktur -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2 border-b border-slate-200 dark:border-slate-800">
            <div>
                <div class="flex items-center gap-2 text-xs font-semibold text-slate-500 mb-1">
                    <a href="{{ route('incomes.index') }}" class="hover:text-indigo-600 transition-colors">Pemasukan</a>
                    <span>/</span>
                    <a href="{{ route('incomes.show', $income) }}" class="hover:text-indigo-600 transition-colors font-mono">{{ $income->invoice_number }}</a>
                    <span>/</span>
                    <span class="text-indigo-600 dark:text-indigo-400">Edit</span>
                </div>
                <h2 class="text-xl font-black text-slate-900 dark:text-white flex items-center gap-2">
                    <span>Edit Transaksi:</span>
                    <span class="font-mono text-indigo-600 dark:text-indigo-400">{{ $income->invoice_number }}</span>
                </h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                    Penyesuaian transaksi penjualan kasir yang sudah tersimpan.
                </p>
            </div>
            <x-button href="{{ route('incomes.show', $income) }}" variant="secondary" size="sm" class="self-start sm:self-auto font-semibold">
                &larr; Batal & Kembali
            </x-button>
        </div>

        <!-- Alert Ketentuan Edit Form Sesuai Permintaan -->
        <div class="p-4 rounded-2xl bg-indigo-50/70 dark:bg-indigo-950/40 border border-indigo-200 dark:border-indigo-800/80 text-xs text-indigo-900 dark:text-indigo-200 flex items-start gap-3 shadow-xs">
            <div class="w-8 h-8 rounded-xl bg-indigo-600 text-white flex items-center justify-center shrink-0 font-bold text-sm shadow-xs">
                ℹ️
            </div>
            <div class="space-y-1">
                <span class="font-bold block text-indigo-950 dark:text-indigo-100">Ketentuan Edit Transaksi Penjualan:</span>
                <p class="text-indigo-800/90 dark:text-indigo-300 leading-relaxed">
                    Demi menjaga konsistensi inventaris stok produk & kartu stok kasir, kolom <strong>Item Produk, Kuantitas (Qty), Modal, dan Catatan dibuat non-aktif (disabled)</strong>. Anda hanya dapat memperbarui <strong>Harga Jual / Total Bayar</strong>, <strong>Kategori Pemasukan</strong>, <strong>Metode Pembayaran</strong>, dan <strong>Tanggal Transaksi</strong>.
                </p>
            </div>
        </div>

        <x-card class="border-slate-200 dark:border-slate-800 shadow-md">
            <form
                method="POST"
                action="{{ route('incomes.update', $income) }}"
                class="space-y-6"
                x-data="{
                    amount: {{ old('amount', (float)$income->total_amount) }},
                    lockedCost: {{ $totalCost }},
                    submitting: false,
                    formatRupiah(num) {
                        return new Intl.NumberFormat('id-ID').format(Math.round(num || 0));
                    },
                    get profit() {
                        const bayar = parseFloat(this.amount) || 0;
                        return bayar - this.lockedCost;
                    },
                    get margin() {
                        const bayar = parseFloat(this.amount) || 0;
                        if (bayar <= 0) return '0';
                        return ((this.profit / bayar) * 100).toFixed(1);
                    }
                }"
                @submit="if(submitting) { $event.preventDefault(); return; } submitting = true;"
            >
                @csrf
                @method('PUT')

                <!-- ========================================================= -->
                <!-- 1. DATA TRANSAKSI TERKUNCI (DISABLED)                    -->
                <!-- ========================================================= -->
                <div class="p-4 sm:p-5 rounded-2xl bg-slate-50 dark:bg-slate-850 border border-slate-200 dark:border-slate-800 space-y-4">
                    <div class="flex items-center justify-between pb-2 border-b border-slate-200/80 dark:border-slate-800">
                        <span class="text-xs font-black uppercase tracking-wider text-slate-700 dark:text-slate-300 flex items-center gap-2">
                            <span>🔒 Rincian Item Transaksi</span>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-slate-200 dark:bg-slate-800 text-slate-600 dark:text-slate-400">Terkunci / Disabled</span>
                        </span>
                        <span class="text-[11px] text-slate-400 font-medium">Stok & data asli tetap terjaga</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <!-- Nama Item / Produk (Disabled) -->
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5 flex items-center justify-between">
                                <span>Item Produk / Transaksi</span>
                                <span class="text-[10px] text-slate-400 font-mono">🔒 Non-aktif</span>
                            </label>
                            <input
                                type="text"
                                value="{{ $itemName }}"
                                disabled
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-100/90 dark:bg-slate-800 text-slate-500 dark:text-slate-400 text-xs font-bold cursor-not-allowed shadow-none"
                            >
                        </div>

                        <!-- Jumlah / Kuantitas (Disabled) -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5 flex items-center justify-between">
                                <span>Kuantitas (Qty)</span>
                                <span class="text-[10px] text-slate-400 font-mono">🔒 Non-aktif</span>
                            </label>
                            <input
                                type="text"
                                value="{{ $itemQty }} unit / transaksi"
                                disabled
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-100/90 dark:bg-slate-800 text-slate-500 dark:text-slate-400 text-xs font-mono font-bold cursor-not-allowed shadow-none"
                            >
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-1">
                        <!-- Modal Kulakan / HPP (Disabled) -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5 flex items-center justify-between">
                                <span>Modal Kulakan / HPP (Terkunci)</span>
                                <span class="text-[10px] text-slate-400 font-mono">🔒 Non-aktif</span>
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400 font-bold text-xs pointer-events-none">Rp</span>
                                <input
                                    type="text"
                                    value="{{ number_format($totalCost, 0, ',', '.') }}"
                                    disabled
                                    class="w-full pl-10 pr-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-100/90 dark:bg-slate-800 text-slate-500 dark:text-slate-400 text-xs font-mono font-bold cursor-not-allowed shadow-none"
                                >
                            </div>
                        </div>

                        <!-- Catatan / No Tujuan Asli (Disabled) -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5 flex items-center justify-between">
                                <span>Catatan / Keterangan Asli</span>
                                <span class="text-[10px] text-slate-400 font-mono">🔒 Non-aktif</span>
                            </label>
                            <input
                                type="text"
                                value="{{ $income->notes ?: '(Tidak ada catatan)' }}"
                                disabled
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-100/90 dark:bg-slate-800 text-slate-500 dark:text-slate-400 text-xs truncate cursor-not-allowed shadow-none"
                            >
                        </div>
                    </div>
                </div>

                <!-- ========================================================= -->
                <!-- 2. DATA YANG BISA DI-EDIT (AKTIF)                         -->
                <!-- ========================================================= -->
                <div class="space-y-5 pt-2">
                    <div class="flex items-center gap-2 pb-2 border-b border-slate-100 dark:border-slate-800">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        <h3 class="text-xs font-black uppercase tracking-wider text-slate-900 dark:text-white">
                            Data yang Dapat Diubah (Aktif)
                        </h3>
                    </div>

                    <!-- Kategori Pemasukan & Tanggal Waktu Transaksi -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Kategori Pemasukan (Aktif) -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5 flex items-center justify-between">
                                <span>Kategori Pemasukan <span class="text-rose-500">*</span></span>
                                <span class="text-[11px] text-indigo-600 dark:text-indigo-400 font-bold">✓ Bisa Diedit</span>
                            </label>
                            <select
                                name="category_id"
                                required
                                class="block w-full rounded-xl border @error('category_id') border-rose-500 ring-1 ring-rose-500 @else border-slate-300 dark:border-slate-600 @enderror bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs font-semibold py-2.5 px-3.5 focus:ring-2 focus:ring-indigo-500 shadow-2xs"
                            >
                                @foreach($cleanCategories as $cat)
                                    <option value="{{ $cat->id }}" {{ old('category_id', $income->category_id) == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <p class="text-[11px] text-rose-500 font-bold mt-1">{{ $message }}</p>
                            @enderror
                            <p class="text-[11px] text-slate-400 mt-1">Ubah kategori pengelompokan laporan</p>
                        </div>

                        <!-- Tanggal & Waktu Transaksi (Aktif) -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5 flex items-center justify-between">
                                <span>Tanggal & Waktu Transaksi <span class="text-rose-500">*</span></span>
                                <span class="text-[11px] text-indigo-600 dark:text-indigo-400 font-bold">✓ Bisa Diedit</span>
                            </label>
                            <input
                                type="datetime-local"
                                name="transaction_date"
                                value="{{ old('transaction_date', $income->transaction_date->format('Y-m-d\TH:i')) }}"
                                required
                                class="block w-full rounded-xl border @error('transaction_date') border-rose-500 ring-1 ring-rose-500 @else border-slate-300 dark:border-slate-600 @enderror bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs font-medium py-2.5 px-3.5 focus:ring-2 focus:ring-indigo-500 shadow-2xs"
                            >
                            @error('transaction_date')
                                <p class="text-[11px] text-rose-500 font-bold mt-1">{{ $message }}</p>
                            @enderror
                            <p class="text-[11px] text-slate-400 mt-1">Sesuaikan tanggal pembukuan kasir</p>
                        </div>
                    </div>

                    <!-- Total Bayar / Harga Jual & Metode Pembayaran -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-1">
                        <!-- Total Bayar / Harga Jual (Aktif) -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5 flex items-center justify-between">
                                <span>Total Bayar / Harga Jual (Rp) <span class="text-rose-500">*</span></span>
                                <span class="text-[11px] text-emerald-600 dark:text-emerald-400 font-bold">✓ Bisa Diedit</span>
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-emerald-600 font-bold text-sm pointer-events-none">Rp</span>
                                <input
                                    type="number"
                                    name="amount"
                                    min="0.01"
                                    step="0.01"
                                    x-model="amount"
                                    required
                                    placeholder="0"
                                    class="w-full pl-11 pr-3.5 py-3 rounded-xl border @error('amount') border-rose-500 ring-1 ring-rose-500 @else border-emerald-300 dark:border-emerald-600 @enderror bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-base font-mono font-black focus:ring-2 focus:ring-emerald-500 shadow-2xs"
                                >
                            </div>
                            @error('amount')
                                <p class="text-[11px] text-rose-500 font-bold mt-1">{{ $message }}</p>
                            @enderror
                            <p class="text-[11px] text-slate-400 mt-1">Uang yang dibayarkan oleh pelanggan</p>
                        </div>

                        <!-- Metode Pembayaran / Bayar (Aktif) -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5 flex items-center justify-between">
                                <span>Metode Pembayaran (Bayar) <span class="text-rose-500">*</span></span>
                                <span class="text-[11px] text-indigo-600 dark:text-indigo-400 font-bold">✓ Bisa Diedit</span>
                            </label>
                            <select
                                name="payment_method"
                                required
                                class="block w-full rounded-xl border @error('payment_method') border-rose-500 ring-1 ring-rose-500 @else border-slate-300 dark:border-slate-600 @enderror bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs font-bold py-3 px-3.5 focus:ring-2 focus:ring-indigo-500 shadow-2xs"
                            >
                                <option value="cash" {{ old('payment_method', $income->payment_method) === 'cash' ? 'selected' : '' }}>💵 Tunai (Cash)</option>
                                <option value="qris" {{ old('payment_method', $income->payment_method) === 'qris' ? 'selected' : '' }}>📲 QRIS / E-Wallet</option>
                                <option value="transfer" {{ old('payment_method', $income->payment_method) === 'transfer' ? 'selected' : '' }}>🏦 Transfer Bank</option>
                            </select>
                            @error('payment_method')
                                <p class="text-[11px] text-rose-500 font-bold mt-1">{{ $message }}</p>
                            @enderror
                            <p class="text-[11px] text-slate-400 mt-1">Kanal pembayaran yang diterima toko</p>
                        </div>
                    </div>

                    <!-- Estimasi Laba Real-Time Berdasarkan Perubahan Harga Jual -->
                    <div class="p-4 rounded-2xl bg-gradient-to-r from-emerald-500/10 via-teal-500/10 to-indigo-500/10 dark:from-emerald-950/40 dark:via-teal-950/40 dark:to-indigo-950/40 border border-emerald-300/80 dark:border-emerald-700/60 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-emerald-600 text-white flex items-center justify-center font-bold text-lg shadow-xs">
                                📈
                            </div>
                            <div>
                                <span class="text-xs font-bold uppercase tracking-wider text-emerald-800 dark:text-emerald-300 block">
                                    Estimasi Laba Bersih Transaksi Baru:
                                </span>
                                <span class="text-[11px] text-slate-500 dark:text-slate-400">
                                    Dihitung: Harga Jual Baru - Modal Kulakan (Terkunci Rp {{ number_format($totalCost, 0, ',', '.') }})
                                </span>
                            </div>
                        </div>

                        <div class="text-left sm:text-right font-mono">
                            <div class="text-lg font-black flex items-center sm:justify-end gap-1.5" :class="profit >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400'">
                                <span x-text="profit >= 0 ? '+ Rp ' : '- Rp '"></span>
                                <span x-text="formatRupiah(Math.abs(profit))"></span>
                                <span class="text-xs font-bold px-2 py-0.5 rounded-full" :class="profit >= 0 ? 'bg-emerald-100 dark:bg-emerald-900/60 text-emerald-800 dark:text-emerald-300' : 'bg-rose-100 dark:bg-rose-900/60 text-rose-800 dark:text-rose-300'" x-text="margin + '% margin'"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tombol Aksi Simpan & Batal -->
                <div class="pt-4 border-t border-slate-100 dark:border-slate-700/60 flex items-center justify-end gap-3">
                    <x-button href="{{ route('incomes.show', $income) }}" variant="secondary">
                        Batal
                    </x-button>
                    <button
                        type="submit"
                        :disabled="submitting || amount <= 0"
                        class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs uppercase tracking-wider transition-all shadow-md shadow-indigo-600/30 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer"
                    >
                        <svg x-show="submitting" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                        <span x-text="submitting ? 'Menyimpan Perubahan...' : 'Simpan Perubahan Pemasukan'">Simpan Perubahan Pemasukan</span>
                    </button>
                </div>
            </form>
        </x-card>
    </div>
</x-layouts.app>
