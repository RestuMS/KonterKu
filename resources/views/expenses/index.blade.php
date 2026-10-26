<x-layouts.app title="Daftar Pengeluaran">
    <x-slot:header>
        Pencatatan Pengeluaran Konter
    </x-slot:header>

    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-900 dark:text-white">Pengeluaran Kas</h2>
            <p class="text-xs text-slate-500 mt-0.5">Catat seluruh beban operasional, belanja persediaan/kulakan, dan biaya toko.</p>
        </div>
        <x-button href="{{ route('expenses.create') }}" variant="danger" class="font-semibold shadow-xs">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Catat Pengeluaran Baru
        </x-button>
    </div>

    <!-- Filter Bar -->
    <x-card class="mb-6">
        <form method="GET" action="{{ route('expenses.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <div class="lg:col-span-2">
                <x-input
                    name="search"
                    placeholder="Cari no invoice atau catatan pengeluaran..."
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
                    <a href="{{ route('expenses.index') }}" class="p-2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200" title="Reset Filter">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    </a>
                @endif
            </div>
        </form>
    </x-card>

    <!-- Total Filter Banner -->
    <div class="mb-4 flex items-center justify-between text-xs text-slate-500 px-1">
        <span>Menampilkan <strong class="text-slate-800 dark:text-slate-200">{{ $expenses->total() }}</strong> transaksi pengeluaran</span>
        <span>Total Pengeluaran: <strong class="text-rose-600 text-sm font-bold">@rupiah($totalAmount)</strong></span>
    </div>

    <!-- Expenses Table -->
    <x-card>
        <div class="overflow-x-auto -mx-6">
            <table class="min-w-full divide-y divide-slate-200/80 dark:divide-slate-700 text-left text-xs">
                <thead class="bg-slate-50/70 dark:bg-slate-850 text-slate-500 uppercase font-semibold tracking-wider">
                    <tr>
                        <th class="px-6 py-3.5">Invoice</th>
                        <th class="px-6 py-3.5">Tanggal</th>
                        <th class="px-6 py-3.5">Kategori</th>
                        <th class="px-6 py-3.5">Keterangan Pengeluaran</th>
                        <th class="px-6 py-3.5">Metode</th>
                        <th class="px-6 py-3.5 text-right">Nominal</th>
                        <th class="px-6 py-3.5 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60 font-medium">
                    @forelse($expenses as $exp)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-750/30 transition-colors">
                            <td class="px-6 py-3.5 font-mono font-bold text-slate-900 dark:text-slate-100">
                                {{ $exp->invoice_number }}
                            </td>
                            <td class="px-6 py-3.5 text-slate-500">
                                @tanggalWaktu($exp->transaction_date)
                            </td>
                            <td class="px-6 py-3.5">
                                <x-badge color="amber">{{ $exp->category->name ?? 'Operasional' }}</x-badge>
                            </td>
                            <td class="px-6 py-3.5 text-slate-800 dark:text-slate-200">
                                {{ $exp->notes }}
                            </td>
                            <td class="px-6 py-3.5 uppercase font-semibold text-slate-600 dark:text-slate-400">
                                <x-badge color="slate">{{ $exp->payment_method }}</x-badge>
                            </td>
                            <td class="px-6 py-3.5 text-right font-bold text-rose-600 text-sm">
                                - @rupiah($exp->total_amount)
                            </td>
                            <td class="px-6 py-3.5 text-center">
                                <div class="inline-flex items-center gap-2">
                                    <button
                                        type="button"
                                        @click="$dispatch('open-modal', 'delete-expense-{{ $exp->id }}')"
                                        class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/30"
                                        title="Hapus Pengeluaran"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>

                                    <!-- Modal Konfirmasi Hapus Pengeluaran -->
                                    <x-modal name="delete-expense-{{ $exp->id }}" title="Konfirmasi Hapus Pengeluaran">
                                        <div class="text-left text-sm text-slate-600 dark:text-slate-400">
                                            <p>Apakah Anda yakin ingin menghapus catatan pengeluaran <strong>{{ $exp->invoice_number }}</strong> sejumlah <strong>@rupiah($exp->total_amount)</strong>?</p>
                                        </div>

                                        <x-slot:footer>
                                            <x-button type="button" variant="secondary" size="sm" @click="$dispatch('close-modal', 'delete-expense-{{ $exp->id }}')">
                                                Batal
                                            </x-button>
                                            <form method="POST" action="{{ route('expenses.destroy', $exp) }}">
                                                @csrf
                                                @method('DELETE')
                                                <x-button type="submit" variant="danger" size="sm">
                                                    Ya, Hapus Pengeluaran
                                                </x-button>
                                            </form>
                                        </x-slot:footer>
                                    </x-modal>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4">
                                <x-empty-state
                                    title="Belum Ada Pengeluaran"
                                    description="Belum ada catatan pengeluaran kas atau biaya operasional toko konter yang tercatat."
                                >
                                    <x-slot:action>
                                        <x-button href="{{ route('expenses.create') }}" variant="danger" size="sm">
                                            + Catat Pengeluaran Baru
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
            {{ $expenses->links() }}
        </div>
    </x-card>
</x-layouts.app>
