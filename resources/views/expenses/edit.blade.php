<x-layouts.app title="Edit Pengeluaran {{ $expense->invoice_number }}">
    <x-slot:header>
        Edit Catatan Pengeluaran
    </x-slot:header>

    <div class="max-w-2xl mx-auto space-y-6">
        <!-- Header Navigasi & Info Faktur -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2 border-b border-slate-200 dark:border-slate-800">
            <div>
                <div class="flex items-center gap-2 text-xs font-semibold text-slate-500 mb-1">
                    <a href="{{ route('expenses.index') }}" class="hover:text-rose-600 transition-colors">Pengeluaran</a>
                    <span>/</span>
                    <span class="font-mono text-slate-700 dark:text-slate-300">{{ $expense->invoice_number }}</span>
                    <span>/</span>
                    <span class="text-rose-600 dark:text-rose-400">Edit</span>
                </div>
                <h2 class="text-xl font-black text-slate-900 dark:text-white flex items-center gap-2">
                    <span>Edit Pengeluaran:</span>
                    <span class="font-mono text-rose-600 dark:text-rose-400">{{ $expense->invoice_number }}</span>
                </h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                    Koreksi nominal, kategori, catatan belanja, atau metode pembayaran toko.
                </p>
            </div>
            <x-button href="{{ route('expenses.index') }}" variant="secondary" size="sm" class="self-start sm:self-auto font-semibold">
                &larr; Batal & Kembali
            </x-button>
        </div>

        <x-card class="border-slate-200 dark:border-slate-800 shadow-md">
            <form
                method="POST"
                action="{{ route('expenses.update', $expense) }}"
                class="space-y-5"
                x-data="{
                    amount: {{ old('amount', (float)$expense->total_amount) }},
                    paymentMethod: '{{ old('payment_method', $expense->payment_method) }}',
                    submitting: false,
                    formatRupiah(num) {
                        return new Intl.NumberFormat('id-ID').format(Math.round(num || 0));
                    }
                }"
                @submit="submitting = true"
            >
                @csrf
                @method('PUT')

                <!-- 1. Nominal Pengeluaran -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1 flex items-center justify-between">
                        <span>Nominal Biaya / Pengeluaran <span class="text-rose-500">*</span></span>
                        <span class="text-xs font-black font-mono text-rose-600 dark:text-rose-400" x-text="'Rp ' + formatRupiah(amount)"></span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-rose-600 font-bold text-sm pointer-events-none">Rp</span>
                        <input
                            type="number"
                            name="amount"
                            min="1"
                            step="0.01"
                            x-model.number="amount"
                            required
                            placeholder="0"
                            class="w-full pl-11 pr-3.5 py-3 rounded-xl border @error('amount') border-rose-500 ring-1 ring-rose-500 @else border-rose-200 dark:border-rose-900/60 @enderror bg-rose-50/20 dark:bg-rose-950/20 text-rose-700 dark:text-rose-300 text-lg font-mono font-black focus:ring-2 focus:ring-rose-500 shadow-2xs"
                        >
                    </div>
                    @error('amount')
                        <p class="text-[11px] text-rose-500 font-bold mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- 2. Kategori Pengeluaran (Wajib Dipilih) -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">
                        Kategori Pengeluaran <span class="text-rose-500">*</span>
                    </label>
                    <select
                        name="category_id"
                        required
                        class="block w-full rounded-xl border @error('category_id') border-rose-500 ring-1 ring-rose-500 @else border-slate-300 dark:border-slate-600 @enderror bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs font-medium py-2.5 px-3 focus:ring-2 focus:ring-rose-500 shadow-2xs"
                    >
                        <option value="">-- Pilih Kategori Pengeluaran --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', $expense->category_id) == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <p class="text-[11px] text-rose-500 font-bold mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- 3. Keterangan / Keperluan -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">
                        Keterangan / Keperluan <span class="text-rose-500">*</span>
                    </label>
                    <textarea
                        name="notes"
                        rows="3"
                        required
                        placeholder="Contoh: Belanja token listrik konter, jajan kasir, bensin operasional..."
                        class="w-full px-3.5 py-2.5 rounded-xl border @error('notes') border-rose-500 ring-1 ring-rose-500 @else border-slate-300 dark:border-slate-600 @enderror bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-xs font-medium focus:ring-2 focus:ring-rose-500 shadow-2xs resize-none"
                    >{{ old('notes', $expense->notes) }}</textarea>
                    @error('notes')
                        <p class="text-[11px] text-rose-500 font-bold mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- 4. Metode Pembayaran -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                        Metode Pembayaran <span class="text-rose-500">*</span>
                    </label>
                    <div class="grid grid-cols-3 gap-2.5">
                        <label 
                            :class="paymentMethod === 'cash' ? 'border-rose-600 bg-rose-50 dark:bg-rose-950/60 text-rose-800 dark:text-rose-200 font-bold ring-1 ring-rose-500/30' : 'border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300'"
                            class="p-2.5 rounded-xl border text-center text-xs cursor-pointer transition-all flex items-center justify-center gap-1.5"
                        >
                            <input type="radio" name="payment_method" value="cash" x-model="paymentMethod" class="sr-only">
                            <span>💵 Tunai (Cash)</span>
                        </label>

                        <label 
                            :class="paymentMethod === 'transfer' ? 'border-rose-600 bg-rose-50 dark:bg-rose-950/60 text-rose-800 dark:text-rose-200 font-bold ring-1 ring-rose-500/30' : 'border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300'"
                            class="p-2.5 rounded-xl border text-center text-xs cursor-pointer transition-all flex items-center justify-center gap-1.5"
                        >
                            <input type="radio" name="payment_method" value="transfer" x-model="paymentMethod" class="sr-only">
                            <span>🏦 Transfer Bank</span>
                        </label>

                        <label 
                            :class="paymentMethod === 'qris' ? 'border-rose-600 bg-rose-50 dark:bg-rose-950/60 text-rose-800 dark:text-rose-200 font-bold ring-1 ring-rose-500/30' : 'border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300'"
                            class="p-2.5 rounded-xl border text-center text-xs cursor-pointer transition-all flex items-center justify-center gap-1.5"
                        >
                            <input type="radio" name="payment_method" value="qris" x-model="paymentMethod" class="sr-only">
                            <span>📲 QRIS</span>
                        </label>
                    </div>
                    @error('payment_method')
                        <p class="text-[11px] text-rose-500 font-bold mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- 5. Tanggal & Waktu Transaksi -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">
                        Tanggal & Waktu Pengeluaran
                    </label>
                    <input
                        type="datetime-local"
                        name="transaction_date"
                        value="{{ old('transaction_date', $expense->transaction_date ? \Carbon\Carbon::parse($expense->transaction_date)->format('Y-m-d\TH:i') : '') }}"
                        class="block w-full rounded-xl border @error('transaction_date') border-rose-500 ring-1 ring-rose-500 @else border-slate-300 dark:border-slate-600 @enderror bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs font-medium py-2 px-3 focus:ring-2 focus:ring-rose-500 shadow-2xs"
                    >
                    @error('transaction_date')
                        <p class="text-[11px] text-rose-500 font-bold mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tombol Simpan Perubahan -->
                <div class="pt-3 border-t border-slate-200 dark:border-slate-800 flex items-center justify-end gap-3">
                    <x-button href="{{ route('expenses.index') }}" variant="secondary" size="md">
                        Batal
                    </x-button>
                    <x-button type="submit" variant="danger" size="md" class="font-bold flex items-center gap-2">
                        <svg x-show="submitting" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                        <span x-text="submitting ? 'Menyimpan...' : 'Simpan Perubahan Pengeluaran'">Simpan Perubahan Pengeluaran</span>
                    </x-button>
                </div>
            </form>
        </x-card>
    </div>
</x-layouts.app>
