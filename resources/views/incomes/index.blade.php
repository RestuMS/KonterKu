<x-layouts.app title="Daftar Pemasukan">
    <x-slot:header>
        Pencatatan Pemasukan & Penjualan
    </x-slot:header>

    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-black text-slate-900 dark:text-white">Pemasukan Kas & Penjualan</h2>
            <p class="text-xs text-slate-500 mt-0.5">Kelola seluruh arus kas masuk dari penjualan pulsa, kuota, voucher, e-wallet, dan jasa konter.</p>
        </div>
        <x-button href="{{ route('incomes.create') }}" variant="primary" class="font-bold shadow-md shadow-indigo-600/20">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>Catat Pemasukan Baru</span>
        </x-button>
    </div>

    <!-- Ringkasan Finansial: Memisahkan Pemasukan (Omzet) vs Modal vs Laba -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <!-- 1. Total Omzet Pemasukan -->
        <div class="p-5 rounded-2xl bg-white dark:bg-slate-800 border border-slate-200/80 dark:border-slate-700 shadow-xs">
            <div class="flex items-center justify-between text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                <span>Total Omzet Pemasukan</span>
                <span class="w-8 h-8 rounded-xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-sm">💰</span>
            </div>
            <div class="text-2xl font-black text-indigo-600 dark:text-indigo-400 font-mono mt-3">
                @rupiah($totalAmount)
            </div>
            <p class="text-[11px] text-slate-400 mt-1">Uang bruto yang dibayarkan konsumen ke konter.</p>
        </div>

        <!-- 2. Total Modal Kulakan (HPP) -->
        <div class="p-5 rounded-2xl bg-white dark:bg-slate-800 border border-slate-200/80 dark:border-slate-700 shadow-xs">
            <div class="flex items-center justify-between text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                <span>Total Modal Kulakan (HPP)</span>
                <span class="w-8 h-8 rounded-xl bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 flex items-center justify-center text-sm">📦</span>
            </div>
            <div class="text-2xl font-black text-slate-700 dark:text-slate-200 font-mono mt-3">
                @rupiah($totalCost)
            </div>
            <p class="text-[11px] text-slate-400 mt-1">Nilai modal barang dan biaya pokok yang terjual.</p>
        </div>

        <!-- 3. Estimasi Laba Bersih -->
        <div class="p-5 rounded-2xl bg-white dark:bg-slate-800 border border-slate-200/80 dark:border-slate-700 shadow-xs">
            <div class="flex items-center justify-between text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                <span>Estimasi Laba Bersih</span>
                <span class="w-8 h-8 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-sm">📈</span>
            </div>
            <div class="text-2xl font-black text-emerald-600 dark:text-emerald-400 font-mono mt-3 flex items-baseline gap-2">
                <span>+ @rupiah($totalProfit)</span>
                @if($totalAmount > 0)
                    <span class="text-xs font-bold px-2 py-0.5 rounded-full bg-emerald-100 dark:bg-emerald-950/80 text-emerald-800 dark:text-emerald-300">
                        {{ number_format(($totalProfit / $totalAmount) * 100, 1) }}%
                    </span>
                @endif
            </div>
            <p class="text-[11px] text-slate-400 mt-1">Omzet dikurangi Modal (Keuntungan nyata konter).</p>
        </div>
    </div>

    <!-- Filter Bar Lengkap -->
    <x-card class="mb-6">
        <form method="GET" action="{{ route('incomes.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3">
            <div class="lg:col-span-2">
                <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Pencarian</label>
                <x-input
                    name="search"
                    placeholder="No invoice, nama item, catatan..."
                    value="{{ request('search') }}"
                />
            </div>
            <div>
                <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Kategori</label>
                <x-select name="category_id">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </x-select>
            </div>
            <div>
                <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Metode Bayar</label>
                <x-select name="payment_method">
                    <option value="">Semua Metode</option>
                    <option value="cash" {{ request('payment_method') === 'cash' ? 'selected' : '' }}>Tunai (Cash)</option>
                    <option value="transfer" {{ request('payment_method') === 'transfer' ? 'selected' : '' }}>Transfer Bank</option>
                    <option value="qris" {{ request('payment_method') === 'qris' ? 'selected' : '' }}>QRIS / E-Wallet</option>
                </x-select>
            </div>
            <div>
                <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Dari Tanggal</label>
                <x-input
                    name="start_date"
                    type="date"
                    value="{{ request('start_date') }}"
                />
            </div>
            <div>
                <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Sampai Tanggal</label>
                <div class="flex items-center gap-2">
                    <x-input
                        name="end_date"
                        type="date"
                        value="{{ request('end_date') }}"
                    />
                    <x-button type="submit" variant="secondary" class="shrink-0 font-bold">
                        Filter
                    </x-button>
                    @if(request()->hasAny(['search', 'category_id', 'payment_method', 'start_date', 'end_date']))
                        <a href="{{ route('incomes.index') }}" class="p-2 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-slate-100 dark:hover:bg-slate-700" title="Reset Filter">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </x-card>

    <!-- Table Incomes -->
    <x-card>
        <div class="overflow-x-auto -mx-6">
            <table class="min-w-full divide-y divide-slate-200/80 dark:divide-slate-700 text-left text-xs">
                <thead class="bg-slate-50/80 dark:bg-slate-850 text-slate-500 dark:text-slate-400 uppercase font-bold tracking-wider">
                    <tr>
                        <th class="px-6 py-3.5">Invoice & Waktu</th>
                        <th class="px-4 py-3.5">Kategori</th>
                        <th class="px-5 py-3.5">Item Terjual / Catatan</th>
                        <th class="px-4 py-3.5">Metode</th>
                        <th class="px-4 py-3.5 text-right">Harga Modal</th>
                        <th class="px-4 py-3.5 text-right">Nominal Omzet</th>
                        <th class="px-5 py-3.5 text-right">Laba Bersih</th>
                        <th class="px-6 py-3.5 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60 font-medium">
                    @forelse($incomes as $inc)
                        <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-750/30 transition-colors">
                            <!-- Invoice & Waktu -->
                            <td class="px-6 py-3.5">
                                <a href="{{ route('incomes.show', $inc) }}" class="font-mono font-bold text-indigo-600 dark:text-indigo-400 hover:underline block">
                                    {{ $inc->invoice_number }}
                                </a>
                                <span class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 block">
                                    @tanggalWaktu($inc->transaction_date)
                                </span>
                            </td>

                            <!-- Kategori -->
                            <td class="px-4 py-3.5">
                                <x-badge color="sky" size="sm">{{ $inc->category->name ?? 'Umum' }}</x-badge>
                            </td>

                            <!-- Item Terjual / Keterangan -->
                            <td class="px-5 py-3.5 text-slate-700 dark:text-slate-300">
                                @if($inc->items->isNotEmpty())
                                    @foreach($inc->items as $it)
                                        <div class="font-bold text-slate-900 dark:text-slate-100">
                                            {{ $it->product_name }} <span class="text-indigo-600 dark:text-indigo-400 font-mono">({{ $it->quantity }}x)</span>
                                        </div>
                                    @endforeach
                                @else
                                    <span class="font-medium text-slate-600 dark:text-slate-300">{{ $inc->notes ?? '-' }}</span>
                                @endif

                                @if($inc->notes && $inc->items->isNotEmpty())
                                    <span class="block text-[11px] text-slate-400 mt-0.5 truncate max-w-xs">{{ $inc->notes }}</span>
                                @endif
                            </td>

                            <!-- Metode Bayar -->
                            <td class="px-4 py-3.5 uppercase text-slate-600 dark:text-slate-400">
                                <x-badge color="slate" size="sm">{{ $inc->payment_method }}</x-badge>
                            </td>

                            <!-- Harga Modal -->
                            <td class="px-4 py-3.5 text-right font-mono text-slate-600 dark:text-slate-300 font-semibold">
                                @if($inc->cost_price > 0)
                                    @rupiah($inc->cost_price)
                                @else
                                    <span class="text-slate-400 text-xs">-</span>
                                @endif
                            </td>

                            <!-- Nominal Omzet Pemasukan -->
                            <td class="px-4 py-3.5 text-right font-mono font-black text-slate-900 dark:text-white text-sm">
                                @rupiah($inc->total_amount)
                            </td>

                            <!-- Laba Bersih -->
                            <td class="px-5 py-3.5 text-right">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold text-emerald-700 bg-emerald-50 dark:bg-emerald-950/50 dark:text-emerald-300 border border-emerald-200/80 dark:border-emerald-800/50 font-mono">
                                    + @rupiah($inc->profit)
                                </span>
                            </td>

                            <!-- Tombol Aksi (Detail, Edit, Batalkan) -->
                            <td class="px-6 py-3.5 text-center">
                                <div class="inline-flex items-center gap-1.5">
                                    <!-- Detail Invoice -->
                                    <a
                                        href="{{ route('incomes.show', $inc) }}"
                                        class="p-1.5 rounded-lg text-slate-500 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors"
                                        title="Lihat Detail Struk"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </a>

                                    <!-- Edit Transaksi -->
                                    <a
                                        href="{{ route('incomes.edit', $inc) }}"
                                        class="p-1.5 rounded-lg text-slate-500 hover:text-amber-600 hover:bg-amber-50 dark:hover:bg-slate-700 transition-colors"
                                        title="Edit Transaksi Pemasukan"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>

                                    <!-- Delete / Cancel Button with Alpine confirm modal dispatch -->
                                    <button
                                        type="button"
                                        @click="$dispatch('open-modal', 'cancel-income-{{ $inc->id }}')"
                                        class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/30 transition-colors cursor-pointer"
                                        title="Batalkan & Kembalikan Stok"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>

                                    <!-- Modal Konfirmasi Pembatalan -->
                                    <x-modal name="cancel-income-{{ $inc->id }}" title="Konfirmasi Pembatalan Transaksi">
                                        <div class="text-left text-sm text-slate-600 dark:text-slate-400 space-y-3">
                                            <p>Apakah Anda yakin ingin membatalkan transaksi pemasukan <strong>{{ $inc->invoice_number }}</strong> sejumlah <strong>@rupiah($inc->total_amount)</strong>?</p>
                                            
                                            @if($inc->items->isNotEmpty())
                                                <div class="p-3 bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 rounded-xl text-amber-800 dark:text-amber-300 text-xs">
                                                    <strong>Perlindungan Stok:</strong> Stok produk pada invoice ini akan secara otomatis dikembalikan ke inventaris konter secara aman.
                                                </div>
                                            @endif
                                        </div>

                                        <x-slot:footer>
                                            <x-button type="button" variant="secondary" size="sm" @click="$dispatch('close-modal', 'cancel-income-{{ $inc->id }}')">
                                                Tutup
                                            </x-button>
                                            <form method="POST" action="{{ route('incomes.destroy', $inc) }}">
                                                @csrf
                                                @method('DELETE')
                                                <x-button type="submit" variant="danger" size="sm">
                                                    Ya, Batalkan Transaksi
                                                </x-button>
                                            </form>
                                        </x-slot:footer>
                                    </x-modal>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-8">
                                <x-empty-state
                                    title="Belum Ada Transaksi Pemasukan"
                                    description="Aktivitas arus kas masuk masih kosong. Catat penjualan pulsa, kuota, kartu perdana, atau jasa transaksi sekarang."
                                >
                                    <x-slot:action>
                                        <x-button href="{{ route('incomes.create') }}" variant="primary" size="sm">
                                            + Catat Pemasukan Baru
                                        </x-button>
                                    </x-slot:action>
                                </x-empty-state>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $incomes->links() }}
        </div>
    </x-card>
</x-layouts.app>
