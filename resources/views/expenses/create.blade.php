<x-layouts.app title="Catat Pengeluaran Baru">
    <x-slot:header>
        Catat Pengeluaran Kas
    </x-slot:header>

    <div class="max-w-2xl mx-auto">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-slate-900 dark:text-white">Form Pengeluaran Toko</h2>
                <p class="text-xs text-slate-500 mt-0.5">Catat biaya operasional seperti listrik, sewa, internet, gaji, atau belanja stok.</p>
            </div>
            <x-button href="{{ route('expenses.index') }}" variant="secondary" size="sm">
                &larr; Kembali
            </x-button>
        </div>

        <x-card>
            <form method="POST" action="{{ route('expenses.store') }}" class="space-y-5">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <x-input
                        label="Tanggal Pengeluaran"
                        name="transaction_date"
                        type="datetime-local"
                        value="{{ old('transaction_date', now()->format('Y-m-d\TH:i')) }}"
                        required
                    />

                    <x-select label="Kategori Pengeluaran" name="category_id">
                        <option value="">-- Pilih Kategori --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </x-select>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <x-input
                        label="Nominal Pengeluaran"
                        name="amount"
                        type="number"
                        min="1"
                        step="0.01"
                        prefix="Rp"
                        placeholder="Contoh: 150000"
                        value="{{ old('amount') }}"
                        required
                    />

                    <x-select label="Metode Pembayaran" name="payment_method" required>
                        <option value="cash" {{ old('payment_method') === 'cash' ? 'selected' : '' }}>Tunai (Cash)</option>
                        <option value="transfer" {{ old('payment_method') === 'transfer' ? 'selected' : '' }}>Transfer Bank</option>
                        <option value="qris" {{ old('payment_method') === 'qris' ? 'selected' : '' }}>QRIS / E-Wallet</option>
                    </x-select>
                </div>

                <x-textarea
                    label="Keterangan / Rincian Pengeluaran"
                    name="notes"
                    placeholder="Contoh: Pembelian token listrik konter bulan ini, beli kabel rol, kulakan voucher, dsb."
                    value="{{ old('notes') }}"
                    required
                />

                <div class="pt-4 border-t border-slate-100 dark:border-slate-700/60 flex items-center justify-end gap-3">
                    <x-button href="{{ route('expenses.index') }}" variant="secondary">
                        Batal
                    </x-button>
                    <x-button type="submit" variant="danger" class="font-semibold shadow-xs">
                        Simpan Pengeluaran
                    </x-button>
                </div>
            </form>
        </x-card>
    </div>
</x-layouts.app>
