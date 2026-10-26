<x-layouts.app title="Dashboard">
    <x-slot:header>
        Dashboard Ringkasan
    </x-slot:header>

    <!-- Top Action Banner -->
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 p-5 rounded-2xl bg-gradient-to-r from-indigo-700 via-indigo-600 to-indigo-800 text-white shadow-lg shadow-indigo-600/20">
        <div>
            <h2 class="text-xl font-bold tracking-tight">Selamat Datang di KonterKu</h2>
            <p class="text-xs text-indigo-100 mt-1">Kelola pencatatan transaksi kasir, pengeluaran toko, dan inventaris barang secara real-time.</p>
        </div>
        <div class="flex items-center gap-2.5 flex-wrap">
            <x-button href="{{ route('incomes.create') }}" variant="secondary" size="sm" class="bg-white text-indigo-700 hover:bg-indigo-50 border-transparent font-semibold shadow-xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Pemasukan Baru
            </x-button>
            <x-button href="{{ route('expenses.create') }}" variant="secondary" size="sm" class="bg-indigo-900/60 text-white hover:bg-indigo-900/90 border-indigo-400/30">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                Pengeluaran Baru
            </x-button>
            <x-button href="{{ route('products.create') }}" variant="secondary" size="sm" class="bg-indigo-900/60 text-white hover:bg-indigo-900/90 border-indigo-400/30">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                Tambah Produk
            </x-button>
        </div>
    </div>

    <!-- Statistics Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <!-- Pemasukan Hari Ini -->
        <x-stat-card
            title="Pemasukan Hari Ini"
            :value="'Rp ' . number_format($todayIncome, 0, ',', '.')"
            :subtitle="$todayTransactionsCount . ' transaksi tercatat hari ini'"
            color="emerald"
        >
            <x-slot:icon>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/></svg>
            </x-slot:icon>
        </x-stat-card>

        <!-- Pengeluaran Hari Ini -->
        <x-stat-card
            title="Pengeluaran Hari Ini"
            :value="'Rp ' . number_format($todayExpense, 0, ',', '.')"
            subtitle="Operasional & belanja stok"
            color="rose"
        >
            <x-slot:icon>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"/></svg>
            </x-slot:icon>
        </x-stat-card>

        <!-- Laba Bersih Bulan Ini -->
        <x-stat-card
            title="Saldo Bersih Bulan Ini"
            :value="'Rp ' . number_format($monthlyNet, 0, ',', '.')"
            :subtitle="'Omzet: Rp ' . number_format($monthlyIncome, 0, ',', '.')"
            color="indigo"
        >
            <x-slot:icon>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </x-slot:icon>
        </x-stat-card>

        <!-- Peringatan Stok Menipis -->
        <x-stat-card
            title="Stok Perlu Restock"
            :value="$stockSummary['low_stock_count'] . ' Produk'"
            :subtitle="'Dari total ' . $stockSummary['total_products'] . ' jenis produk'"
            :color="$stockSummary['low_stock_count'] > 0 ? 'amber' : 'emerald'"
        >
            <x-slot:icon>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </x-slot:icon>
        </x-stat-card>
    </div>

    <!-- 2 Column Section: Tren 7 Hari & Peringatan Stok -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Tren 7 Hari Terakhir -->
        <div class="lg:col-span-2">
            <x-card title="Tren Arus Kas 7 Hari Terakhir" subtitle="Perbandingan pemasukan dan pengeluaran harian">
                <div class="space-y-4">
                    @forelse($dailyTrend as $item)
                        @php
                            $maxVal = max(100000, max(array_column($dailyTrend, 'income')), max(array_column($dailyTrend, 'expense')));
                            $incPct = $maxVal > 0 ? min(100, round(($item['income'] / $maxVal) * 100)) : 0;
                            $expPct = $maxVal > 0 ? min(100, round(($item['expense'] / $maxVal) * 100)) : 0;
                        @endphp
                        <div>
                            <div class="flex items-center justify-between text-xs mb-1 font-medium">
                                <span class="text-slate-600 dark:text-slate-400 w-16">{{ $item['label'] }}</span>
                                <div class="flex items-center gap-4">
                                    <span class="text-emerald-600 font-semibold">+ @rupiah($item['income'])</span>
                                    <span class="text-rose-600 font-semibold">- @rupiah($item['expense'])</span>
                                </div>
                            </div>
                            <div class="flex h-2.5 rounded-full bg-slate-100 dark:bg-slate-700 overflow-hidden gap-1">
                                <div class="bg-emerald-500 rounded-full transition-all duration-300" style="width: {{ $incPct }}%"></div>
                                <div class="bg-rose-500 rounded-full transition-all duration-300" style="width: {{ $expPct }}%"></div>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-slate-500 text-center py-4">Belum ada riwayat arus kas 7 hari terakhir.</p>
                    @endforelse
                </div>

                <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-700/60 flex items-center justify-center gap-6 text-xs text-slate-500">
                    <span class="inline-flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-emerald-500"></span> Pemasukan</span>
                    <span class="inline-flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-rose-500"></span> Pengeluaran</span>
                </div>
            </x-card>
        </div>

        <!-- Peringatan Stok Menipis -->
        <div class="lg:col-span-1">
            <x-card title="Peringatan Stok Menipis" subtitle="Perlu segera kulakan / restock">
                <x-slot:action>
                    <a href="{{ route('stocks.create') }}" class="text-xs text-indigo-600 hover:text-indigo-800 font-semibold">
                        + Restock
                    </a>
                </x-slot:action>

                <div class="divide-y divide-slate-100 dark:divide-slate-700/60">
                    @forelse($stockSummary['low_stock_items']->take(5) as $prod)
                        <div class="py-3 first:pt-0 last:pb-0 flex items-center justify-between">
                            <div class="min-w-0 pr-3">
                                <p class="text-xs font-semibold text-slate-800 dark:text-slate-200 truncate">{{ $prod->name }}</p>
                                <p class="text-[11px] text-slate-500">Kode: {{ $prod->code }} | Min: {{ $prod->min_stock }} {{ $prod->unit }}</p>
                            </div>
                            <div class="text-right flex-shrink-0">
                                <x-badge color="rose" size="sm">
                                    Sisa {{ $prod->stock }} {{ $prod->unit }}
                                </x-badge>
                            </div>
                        </div>
                    @empty
                        <div class="py-8 text-center text-xs text-slate-500">
                            <svg class="w-8 h-8 mx-auto text-emerald-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Seluruh stok produk berada dalam batas aman!
                        </div>
                    @endforelse
                </div>

                @if($stockSummary['low_stock_count'] > 5)
                    <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-700/60 text-center">
                        <a href="{{ route('products.index', ['low_stock' => 1]) }}" class="text-xs text-indigo-600 font-semibold hover:underline">
                            Lihat {{ $stockSummary['low_stock_count'] - 5 }} produk menipis lainnya &rarr;
                        </a>
                    </div>
                @endif
            </x-card>
        </div>
    </div>

    <!-- Recent Transactions Table -->
    <x-card title="Transaksi Terbaru" subtitle="Daftar transaksi arus kas masuk dan keluar terkini">
        <x-slot:action>
            <a href="{{ route('reports.index') }}" class="text-xs text-indigo-600 hover:text-indigo-800 font-semibold">
                Lihat Semua Transaksi &rarr;
            </a>
        </x-slot:action>

        <div class="overflow-x-auto -mx-6">
            <table class="min-w-full divide-y divide-slate-200/80 dark:divide-slate-700 text-left text-xs">
                <thead class="bg-slate-50/70 dark:bg-slate-850 text-slate-500 uppercase font-semibold tracking-wider">
                    <tr>
                        <th class="px-6 py-3.5">Invoice</th>
                        <th class="px-6 py-3.5">Waktu</th>
                        <th class="px-6 py-3.5">Jenis</th>
                        <th class="px-6 py-3.5">Kategori / Keterangan</th>
                        <th class="px-6 py-3.5">Metode</th>
                        <th class="px-6 py-3.5 text-right">Nominal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60 font-medium">
                    @forelse($recentTransactions as $tx)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-750/30 transition-colors">
                            <td class="px-6 py-3.5 font-mono text-slate-900 dark:text-slate-100 font-semibold">
                                @if($tx->type === 'pemasukan')
                                    <a href="{{ route('incomes.show', $tx) }}" class="text-indigo-600 hover:underline">{{ $tx->invoice_number }}</a>
                                @else
                                    {{ $tx->invoice_number }}
                                @endif
                            </td>
                            <td class="px-6 py-3.5 text-slate-500">
                                @tanggalWaktu($tx->transaction_date)
                            </td>
                            <td class="px-6 py-3.5">
                                @if($tx->type === 'pemasukan')
                                    <x-badge color="emerald">Pemasukan</x-badge>
                                @else
                                    <x-badge color="rose">Pengeluaran</x-badge>
                                @endif
                            </td>
                            <td class="px-6 py-3.5 text-slate-700 dark:text-slate-300">
                                <span class="font-semibold">{{ $tx->category->name ?? 'Umum' }}</span>
                                @if($tx->notes)
                                    <span class="block text-[11px] text-slate-400 truncate max-w-xs">{{ $tx->notes }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-3.5 capitalize text-slate-600 dark:text-slate-400">
                                <x-badge color="slate">{{ $tx->payment_method }}</x-badge>
                            </td>
                            <td class="px-6 py-3.5 text-right font-bold {{ $tx->type === 'pemasukan' ? 'text-emerald-600' : 'text-rose-600' }}">
                                {{ $tx->type === 'pemasukan' ? '+' : '-' }} @rupiah($tx->total_amount)
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4">
                                <x-empty-state
                                    title="Belum Ada Transaksi Tercatat"
                                    description="Aktivitas arus kas masuk dan keluar hari ini masih kosong. Mulai dengan mencatat transaksi pertama Anda!"
                                >
                                    <x-slot:action>
                                        <x-button href="{{ route('incomes.create') }}" variant="primary" size="sm">
                                            + Catat Pemasukan
                                        </x-button>
                                    </x-slot:action>
                                </x-empty-state>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>
</x-layouts.app>
