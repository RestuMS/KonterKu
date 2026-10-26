<x-layouts.app title="Dashboard">
    <x-slot:header>
        Dashboard Ringkasan
    </x-slot:header>

    <!-- Welcome Hero Banner -->
    <div class="relative overflow-hidden mb-8 rounded-3xl bg-gradient-to-br from-slate-900 via-indigo-950 to-slate-900 border border-indigo-900/40 text-white p-6 sm:p-8 shadow-xl shadow-indigo-950/20">
        <!-- Background Subtle Glows -->
        <div class="absolute -right-12 -top-12 w-64 h-64 bg-indigo-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -left-12 -bottom-12 w-64 h-64 bg-sky-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-5">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-500/15 border border-indigo-400/20 text-indigo-300 text-xs font-semibold mb-3">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span>Sistem KonterKu Aktif</span>
                    <span class="text-indigo-400/60">•</span>
                    <span>{{ now()->locale('id')->translatedFormat('l, d F Y') }}</span>
                </div>
                <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-white">
                    Selamat Datang, <span class="bg-gradient-to-r from-white via-indigo-100 to-indigo-300 bg-clip-text text-transparent">{{ Auth::user()->name ?? 'Administrator' }}</span> 👋
                </h2>
                <p class="text-xs sm:text-sm text-slate-300/90 mt-1.5 max-w-xl leading-relaxed">
                    Pantau kinerja penjualan harian, pergerakan stok pulsa & barang, serta arus kas toko secara akurat dan real-time.
                </p>
            </div>

            <!-- Quick Info Pills -->
            <div class="flex items-center gap-3 flex-wrap">
                <div class="px-4 py-2.5 rounded-2xl bg-white/5 border border-white/10 backdrop-blur-md">
                    <span class="text-[10px] uppercase font-bold tracking-wider text-slate-400 block">Total Produk</span>
                    <span class="text-base font-bold text-white">{{ $stockSummary['total_products'] }} <span class="text-xs font-normal text-slate-400">Item</span></span>
                </div>
                <div class="px-4 py-2.5 rounded-2xl bg-white/5 border border-white/10 backdrop-blur-md">
                    <span class="text-[10px] uppercase font-bold tracking-wider text-slate-400 block">Transaksi Hari Ini</span>
                    <span class="text-base font-bold text-emerald-400">{{ $todayTransactionsCount }} <span class="text-xs font-normal text-slate-400">Selesai</span></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards Grid (Modern Elevated Aesthetic) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
        <!-- Pemasukan Hari Ini -->
        <div class="relative overflow-hidden bg-white dark:bg-slate-800 rounded-2xl p-5 border border-slate-200/80 dark:border-slate-700/80 shadow-xs hover:shadow-md transition-all duration-200">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Pemasukan Hari Ini</span>
                <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shadow-2xs">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/></svg>
                </div>
            </div>
            <div class="mt-4">
                <div class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">
                    @rupiah($todayIncome)
                </div>
                <div class="mt-2 flex items-center gap-1.5 text-xs text-slate-500">
                    <span class="inline-flex items-center gap-1 text-emerald-600 font-semibold bg-emerald-50 dark:bg-emerald-950/40 px-2 py-0.5 rounded-md">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        {{ $todayTransactionsCount }} transaksi
                    </span>
                    <span>tercatat hari ini</span>
                </div>
            </div>
        </div>

        <!-- Pengeluaran Hari Ini -->
        <div class="relative overflow-hidden bg-white dark:bg-slate-800 rounded-2xl p-5 border border-slate-200/80 dark:border-slate-700/80 shadow-xs hover:shadow-md transition-all duration-200">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Pengeluaran Hari Ini</span>
                <div class="w-10 h-10 rounded-xl bg-rose-50 dark:bg-rose-950/50 text-rose-600 dark:text-rose-400 flex items-center justify-center shadow-2xs">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"/></svg>
                </div>
            </div>
            <div class="mt-4">
                <div class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">
                    @rupiah($todayExpense)
                </div>
                <div class="mt-2 flex items-center gap-1.5 text-xs text-slate-500">
                    <span class="inline-flex items-center gap-1 text-rose-600 font-semibold bg-rose-50 dark:bg-rose-950/40 px-2 py-0.5 rounded-md">
                        Operasional
                    </span>
                    <span>& belanja persediaan</span>
                </div>
            </div>
        </div>

        <!-- Saldo Kas Bersih Bulan Ini -->
        <div class="relative overflow-hidden bg-white dark:bg-slate-800 rounded-2xl p-5 border border-slate-200/80 dark:border-slate-700/80 shadow-xs hover:shadow-md transition-all duration-200">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Saldo Bersih Bulan Ini</span>
                <div class="w-10 h-10 rounded-xl bg-indigo-50 dark:bg-indigo-950/50 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shadow-2xs">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <div class="mt-4">
                <div class="text-2xl font-black {{ $monthlyNet >= 0 ? 'text-indigo-600 dark:text-indigo-400' : 'text-rose-600' }} tracking-tight">
                    @rupiah($monthlyNet)
                </div>
                <div class="mt-2 flex items-center gap-1.5 text-xs text-slate-500 truncate">
                    <span>Omzet:</span>
                    <strong class="text-slate-800 dark:text-slate-200">@rupiah($monthlyIncome)</strong>
                </div>
            </div>
        </div>

        <!-- Stok Perlu Restock -->
        <div class="relative overflow-hidden bg-white dark:bg-slate-800 rounded-2xl p-5 border border-slate-200/80 dark:border-slate-700/80 shadow-xs hover:shadow-md transition-all duration-200">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Stok Perlu Restock</span>
                <div class="w-10 h-10 rounded-xl {{ $stockSummary['low_stock_count'] > 0 ? 'bg-amber-50 dark:bg-amber-950/50 text-amber-600 dark:text-amber-400' : 'bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400' }} flex items-center justify-center shadow-2xs">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
            </div>
            <div class="mt-4">
                <div class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">
                    {{ $stockSummary['low_stock_count'] }} <span class="text-sm font-semibold text-slate-400">Produk</span>
                </div>
                <div class="mt-2 flex items-center gap-1.5 text-xs text-slate-500">
                    @if($stockSummary['low_stock_count'] > 0)
                        <span class="inline-flex items-center gap-1 text-amber-600 font-semibold bg-amber-50 dark:bg-amber-950/40 px-2 py-0.5 rounded-md">
                            Perlu Restock
                        </span>
                        <span>segera kulakan</span>
                    @else
                        <span class="inline-flex items-center gap-1 text-emerald-600 font-semibold bg-emerald-50 dark:bg-emerald-950/40 px-2 py-0.5 rounded-md">
                            Aman
                        </span>
                        <span>seluruh stok tercukupi</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- 2 Column Section: Tren 7 Hari & Peringatan Stok -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Tren 7 Hari Terakhir -->
        <div class="lg:col-span-2">
            <x-card title="Tren Arus Kas 7 Hari Terakhir" subtitle="Perbandingan pemasukan penjualan dan pengeluaran harian toko">
                <div class="space-y-4">
                    @forelse($dailyTrend as $item)
                        @php
                            $maxVal = max(100000, max(array_column($dailyTrend, 'income')), max(array_column($dailyTrend, 'expense')));
                            $incPct = $maxVal > 0 ? min(100, round(($item['income'] / $maxVal) * 100)) : 0;
                            $expPct = $maxVal > 0 ? min(100, round(($item['expense'] / $maxVal) * 100)) : 0;
                        @endphp
                        <div class="p-2.5 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-750/50 transition-colors">
                            <div class="flex items-center justify-between text-xs mb-1.5 font-medium">
                                <span class="font-bold text-slate-700 dark:text-slate-300 w-20">{{ $item['label'] }}</span>
                                <div class="flex items-center gap-4 text-xs font-mono">
                                    <span class="text-emerald-600 font-bold">+ @rupiah($item['income'])</span>
                                    <span class="text-rose-600 font-bold">- @rupiah($item['expense'])</span>
                                </div>
                            </div>
                            <div class="flex h-3 rounded-full bg-slate-100 dark:bg-slate-700/80 overflow-hidden gap-1 p-0.5">
                                <div class="bg-gradient-to-r from-emerald-500 to-emerald-400 rounded-full transition-all duration-500" style="width: {{ $incPct }}%"></div>
                                <div class="bg-gradient-to-r from-rose-500 to-rose-400 rounded-full transition-all duration-500" style="width: {{ $expPct }}%"></div>
                            </div>
                        </div>
                    @empty
                        <x-empty-state
                            title="Belum Ada Riwayat Arus Kas"
                            description="Belum ada transaksi dalam 7 hari terakhir."
                        />
                    @endforelse
                </div>

                <div class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-700/60 flex items-center justify-center gap-6 text-xs text-slate-500">
                    <span class="inline-flex items-center gap-1.5 font-semibold text-emerald-600">
                        <span class="w-3 h-3 rounded-full bg-emerald-500"></span> Pemasukan
                    </span>
                    <span class="inline-flex items-center gap-1.5 font-semibold text-rose-600">
                        <span class="w-3 h-3 rounded-full bg-rose-500"></span> Pengeluaran
                    </span>
                </div>
            </x-card>
        </div>

        <!-- Peringatan Stok Menipis -->
        <div class="lg:col-span-1">
            <x-card title="Peringatan Stok Menipis" subtitle="Daftar produk yang berada di bawah batas minimum">
                <x-slot:action>
                    <a href="{{ route('stocks.create') }}" class="inline-flex items-center gap-1 text-xs text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 font-bold px-2 py-1 rounded-lg bg-indigo-50 dark:bg-indigo-950/50">
                        + Restock
                    </a>
                </x-slot:action>

                <div class="divide-y divide-slate-100 dark:divide-slate-700/60">
                    @forelse($stockSummary['low_stock_items']->take(5) as $prod)
                        <div class="py-3.5 first:pt-0 last:pb-0 flex items-center justify-between gap-3">
                            <div class="min-w-0 flex-1">
                                <p class="text-xs font-bold text-slate-900 dark:text-slate-100 truncate">{{ $prod->name }}</p>
                                <p class="text-[11px] text-slate-500 mt-0.5 font-mono">Kode: {{ $prod->code }} • Min: {{ $prod->min_stock }} {{ $prod->unit }}</p>
                            </div>
                            <div class="text-right flex-shrink-0">
                                <x-badge color="rose" size="sm">
                                    Sisa {{ $prod->stock }} {{ $prod->unit }}
                                </x-badge>
                            </div>
                        </div>
                    @empty
                        <div class="py-8 text-center text-xs text-slate-500">
                            <div class="w-10 h-10 mx-auto rounded-full bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center mb-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            <span class="font-semibold text-slate-700 dark:text-slate-300 block">Stok Aman</span>
                            Seluruh produk berada di atas batas minimum.
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
    <x-card title="Transaksi Terbaru" subtitle="Daftar aktivitas arus kas masuk dan keluar terkini pada sistem">
        <x-slot:action>
            <a href="{{ route('reports.index') }}" class="text-xs text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 font-bold hover:underline">
                Lihat Semua Transaksi &rarr;
            </a>
        </x-slot:action>

        <div class="overflow-x-auto -mx-6">
            <table class="min-w-full divide-y divide-slate-200/80 dark:divide-slate-700 text-left text-xs">
                <thead class="bg-slate-50/70 dark:bg-slate-850 text-slate-500 uppercase font-bold tracking-wider">
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
                            <td class="px-6 py-3.5 font-mono text-slate-900 dark:text-slate-100 font-bold">
                                @if($tx->type === 'pemasukan')
                                    <a href="{{ route('incomes.show', $tx) }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">
                                        {{ $tx->invoice_number }}
                                    </a>
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
                                <span class="font-bold text-slate-900 dark:text-slate-100">{{ $tx->category->name ?? 'Umum' }}</span>
                                @if($tx->notes)
                                    <span class="block text-[11px] text-slate-400 truncate max-w-xs">{{ $tx->notes }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-3.5 capitalize text-slate-600 dark:text-slate-400">
                                <x-badge color="slate">{{ $tx->payment_method }}</x-badge>
                            </td>
                            <td class="px-6 py-3.5 text-right font-black text-sm {{ $tx->type === 'pemasukan' ? 'text-emerald-600' : 'text-rose-600' }}">
                                {{ $tx->type === 'pemasukan' ? '+' : '-' }} @rupiah($tx->total_amount)
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4">
                                <x-empty-state
                                    title="Belum Ada Transaksi Tercatat"
                                    description="Aktivitas arus kas masuk dan keluar masih kosong. Transaksi terbaru akan muncul secara otomatis di sini."
                                />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>
</x-layouts.app>
