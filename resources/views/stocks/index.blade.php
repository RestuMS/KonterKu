<x-layouts.app title="Mutasi Stok Barang">
    <x-slot:header>
        Riwayat Mutasi & Pergerakan Stok
    </x-slot:header>

    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-900 dark:text-white">Riwayat Mutasi Stok</h2>
            <p class="text-xs text-slate-500 mt-0.5">Catatan log pergerakan stok keluar, masuk, dan penyesuaian fisik (stock opname).</p>
        </div>
        <x-button href="{{ route('stocks.create') }}" variant="primary" class="font-semibold shadow-xs">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
            Penyesuaian / Opname Stok
        </x-button>
    </div>

    <!-- Filter Bar -->
    <x-card class="mb-6">
        <form method="GET" action="{{ route('stocks.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div class="sm:col-span-2">
                <x-input
                    name="search"
                    placeholder="Cari nama atau kode produk..."
                    value="{{ request('search') }}"
                />
            </div>
            <div class="flex items-center gap-2">
                <x-select name="type">
                    <option value="">Semua Jenis Mutasi</option>
                    <option value="in" {{ request('type') === 'in' ? 'selected' : '' }}>Masuk (Restock)</option>
                    <option value="out" {{ request('type') === 'out' ? 'selected' : '' }}>Keluar (Penjualan)</option>
                    <option value="adjustment" {{ request('type') === 'adjustment' ? 'selected' : '' }}>Penyesuaian (Opname)</option>
                </x-select>
                <x-button type="submit" variant="secondary" size="sm" class="flex-shrink-0">
                    Filter
                </x-button>
                @if(request()->hasAny(['search', 'type']))
                    <a href="{{ route('stocks.index') }}" class="p-1.5 text-slate-400 hover:text-slate-600" title="Reset">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </a>
                @endif
            </div>
        </form>
    </x-card>

    <!-- Mutations Table -->
    <x-card>
        <div class="overflow-x-auto -mx-6">
            <table class="min-w-full divide-y divide-slate-200/80 dark:divide-slate-700 text-left text-xs">
                <thead class="bg-slate-50/70 dark:bg-slate-850 text-slate-500 uppercase font-semibold tracking-wider">
                    <tr>
                        <th class="px-6 py-3.5">Waktu</th>
                        <th class="px-6 py-3.5">Produk</th>
                        <th class="px-6 py-3.5 text-center">Tipe Mutasi</th>
                        <th class="px-6 py-3.5 text-center">Perubahan</th>
                        <th class="px-6 py-3.5 text-center">Stok Awal &rarr; Akhir</th>
                        <th class="px-6 py-3.5">Keterangan</th>
                        <th class="px-6 py-3.5">Operator</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60 font-medium">
                    @forelse($mutations as $m)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-750/30 transition-colors">
                            <td class="px-6 py-3.5 text-slate-500">
                                {{ $m->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-3.5">
                                <span class="font-bold text-slate-900 dark:text-slate-100 block">{{ $m->product->name ?? 'Produk Dihapus' }}</span>
                                <span class="font-mono text-[11px] text-slate-400">{{ $m->product->code ?? '-' }}</span>
                            </td>
                            <td class="px-6 py-3.5 text-center">
                                @if($m->type === 'in')
                                    <x-badge color="emerald">Masuk (Restock)</x-badge>
                                @elseif($m->type === 'out')
                                    <x-badge color="rose">Keluar (Terjual)</x-badge>
                                @else
                                    <x-badge color="amber">Penyesuaian</x-badge>
                                @endif
                            </td>
                            <td class="px-6 py-3.5 text-center font-bold text-sm {{ $m->type === 'in' ? 'text-emerald-600' : ($m->type === 'out' ? 'text-rose-600' : 'text-slate-700 dark:text-slate-300') }}">
                                {{ $m->type === 'in' ? '+' : ($m->type === 'out' ? '-' : '') }}{{ $m->quantity }}
                            </td>
                            <td class="px-6 py-3.5 text-center text-slate-600 dark:text-slate-400">
                                <span class="font-mono">{{ $m->stock_before }}</span> &rarr; <span class="font-mono font-bold text-slate-900 dark:text-slate-100">{{ $m->stock_after }}</span>
                            </td>
                            <td class="px-6 py-3.5 text-slate-600 dark:text-slate-400">
                                {{ $m->notes ?? '-' }}
                            </td>
                            <td class="px-6 py-3.5 text-slate-500">
                                {{ $m->user->name ?? 'Sistem' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-slate-400">
                                Belum ada riwayat mutasi stok. Mutasi akan tercatat otomatis saat transaksi atau opname.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $mutations->links() }}
        </div>
    </x-card>
</x-layouts.app>
