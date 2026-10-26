<x-layouts.app title="Daftar Pemasukan">
    <x-slot:header>
        Pencatatan Pemasukan & Penjualan
    </x-slot:header>

    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-900 dark:text-white">Pemasukan Kas</h2>
            <p class="text-xs text-slate-500 mt-0.5">Kelola data transaksi penjualan pulsa, kartu, voucher, dan jasa konter.</p>
        </div>
        <x-button href="{{ route('incomes.create') }}" variant="primary" class="font-semibold shadow-xs">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Catat Pemasukan Baru
        </x-button>
    </div>

    <!-- Filter Bar -->
    <x-card class="mb-6">
        <form method="GET" action="{{ route('incomes.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <div class="lg:col-span-2">
                <x-input
                    name="search"
                    placeholder="Cari no invoice atau catatan..."
                    value="{{ request('search') }}"
                />
            </div>
            <div>
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
                <x-input
                    name="start_date"
                    type="date"
                    value="{{ request('start_date') }}"
                />
            </div>
            <div class="flex items-center gap-2">
                <x-input
                    name="end_date"
                    type="date"
                    value="{{ request('end_date') }}"
                />
                <x-button type="submit" variant="secondary" class="flex-shrink-0">
                    Filter
                </x-button>
                @if(request()->hasAny(['search', 'category_id', 'start_date', 'end_date']))
                    <a href="{{ route('incomes.index') }}" class="p-2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200" title="Reset Filter">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    </a>
                @endif
            </div>
        </form>
    </x-card>

    <!-- Total Filter Banner -->
    <div class="mb-4 flex items-center justify-between text-xs text-slate-500 px-1">
        <span>Menampilkan <strong class="text-slate-800 dark:text-slate-200">{{ $incomes->total() }}</strong> transaksi pemasukan</span>
        <span>Total Terfilter: <strong class="text-emerald-600 text-sm font-bold">@rupiah($totalAmount)</strong></span>
    </div>

    <!-- Incomes Table -->
    <x-card>
        <div class="overflow-x-auto -mx-6">
            <table class="min-w-full divide-y divide-slate-200/80 dark:divide-slate-700 text-left text-xs">
                <thead class="bg-slate-50/70 dark:bg-slate-850 text-slate-500 uppercase font-semibold tracking-wider">
                    <tr>
                        <th class="px-6 py-3.5">Invoice</th>
                        <th class="px-6 py-3.5">Tanggal</th>
                        <th class="px-6 py-3.5">Kategori</th>
                        <th class="px-6 py-3.5">Item Terjual / Keterangan</th>
                        <th class="px-6 py-3.5">Metode</th>
                        <th class="px-6 py-3.5 text-right">Nominal</th>
                        <th class="px-6 py-3.5 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60 font-medium">
                    @forelse($incomes as $inc)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-750/30 transition-colors">
                            <td class="px-6 py-3.5 font-mono font-bold text-indigo-600">
                                <a href="{{ route('incomes.show', $inc) }}" class="hover:underline">
                                    {{ $inc->invoice_number }}
                                </a>
                            </td>
                            <td class="px-6 py-3.5 text-slate-500">
                                {{ $inc->transaction_date->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-3.5">
                                <x-badge color="sky">{{ $inc->category->name ?? 'Umum' }}</x-badge>
                            </td>
                            <td class="px-6 py-3.5 text-slate-700 dark:text-slate-300">
                                @if($inc->items->isNotEmpty())
                                    @foreach($inc->items as $it)
                                        <div class="font-semibold text-slate-800 dark:text-slate-200">
                                            {{ $it->product_name }} <span class="text-slate-400 font-normal">({{ $it->quantity }}x)</span>
                                        </div>
                                    @endforeach
                                @else
                                    <span class="font-medium">{{ $inc->notes ?? '-' }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-3.5 uppercase font-semibold text-slate-600 dark:text-slate-400">
                                <x-badge color="slate">{{ $inc->payment_method }}</x-badge>
                            </td>
                            <td class="px-6 py-3.5 text-right font-bold text-emerald-600 text-sm">
                                + @rupiah($inc->total_amount)
                            </td>
                            <td class="px-6 py-3.5 text-center">
                                <div class="inline-flex items-center gap-2">
                                    <a href="{{ route('incomes.show', $inc) }}" class="p-1.5 rounded-lg text-slate-500 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-700" title="Detail Invoice">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </a>

                                    <!-- Delete / Cancel Button with Alpine confirm modal dispatch -->
                                    <button
                                        type="button"
                                        @click="$dispatch('open-modal', 'cancel-income-{{ $inc->id }}')"
                                        class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/30"
                                        title="Batalkan Transaksi"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>

                                    <!-- Modal Konfirmasi Pembatalan -->
                                    <x-modal name="cancel-income-{{ $inc->id }}" title="Konfirmasi Pembatalan Transaksi">
                                        <div class="text-left text-sm text-slate-600 dark:text-slate-400">
                                            <p class="mb-3">Apakah Anda yakin ingin membatalkan transaksi pemasukan <strong>{{ $inc->invoice_number }}</strong> sejumlah <strong>@rupiah($inc->total_amount)</strong>?</p>
                                            @if($inc->items->isNotEmpty())
                                                <div class="p-3 bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 rounded-xl text-amber-800 dark:text-amber-300 text-xs">
                                                    <strong>Catatan:</strong> Stok produk pada transaksi ini akan secara otomatis dikembalikan ke inventaris.
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
                            <td colspan="7" class="px-6 py-8 text-center text-slate-400">
                                Tidak ada data pemasukan yang ditemukan.
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
