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
                    Pantau kinerja penjualan harian, tren grafik kas, produk terlaris, serta pergerakan laba rugi transaksi konter secara real-time.
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

    <!-- 1. Row: Grafik Tren Arus Kas 7 Hari Terakhir & Peringatan Stok Menipis -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Grafik Tren Arus Kas (Chart.js Modern Canvas) -->
        <div class="lg:col-span-2">
            <x-card title="Grafik Tren Arus Kas 7 Hari Terakhir" subtitle="Visualisasi pergerakan pemasukan penjualan dan beban operasional harian">
                <x-slot:action>
                    <div class="flex items-center gap-2 flex-wrap text-xs">
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200/60 dark:border-emerald-800/40 text-emerald-700 dark:text-emerald-300 font-semibold">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            Masuk: @rupiah($sevenDaysIncome)
                        </span>
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-rose-50 dark:bg-rose-950/40 border border-rose-200/60 dark:border-rose-800/40 text-rose-700 dark:text-rose-300 font-semibold">
                            <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                            Keluar: @rupiah($sevenDaysExpense)
                        </span>
                    </div>
                </x-slot:action>

                <!-- Chart Container -->
                <div class="relative w-full h-72 sm:h-80 pt-2">
                    <canvas id="cashflowChart"></canvas>
                </div>

                <!-- Chart Footer Legend & Net Summary -->
                <div class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-700/60 flex flex-wrap items-center justify-between gap-4 text-xs text-slate-500">
                    <div class="flex items-center gap-5">
                        <span class="inline-flex items-center gap-2 font-semibold text-emerald-600 dark:text-emerald-400">
                            <span class="w-3 h-1.5 rounded-full bg-emerald-500"></span> Pemasukan
                        </span>
                        <span class="inline-flex items-center gap-2 font-semibold text-rose-600 dark:text-rose-400">
                            <span class="w-3 h-1.5 rounded-full bg-rose-500"></span> Pengeluaran
                        </span>
                    </div>
                    <div class="text-right font-medium">
                        <span>Arus Kas Bersih 7 Hari: </span>
                        <strong class="{{ $sevenDaysNet >= 0 ? 'text-indigo-600 dark:text-indigo-400' : 'text-rose-600' }} font-mono font-bold">
                            {{ $sevenDaysNet >= 0 ? '+' : '' }}@rupiah($sevenDaysNet)
                        </strong>
                    </div>
                </div>
            </x-card>
        </div>

        <!-- Peringatan Stok Menipis -->
        <div class="lg:col-span-1">
            <x-card title="Peringatan Stok Menipis" subtitle="Daftar produk di bawah batas minimum">
                <x-slot:action>
                    <a href="{{ route('stocks.create') }}" class="inline-flex items-center gap-1 text-xs text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 font-bold px-2 py-1 rounded-lg bg-indigo-50 dark:bg-indigo-950/50 hover:bg-indigo-100 transition-colors">
                        + Restock
                    </a>
                </x-slot:action>

                <div class="divide-y divide-slate-100 dark:divide-slate-700/60">
                    @forelse($stockSummary['low_stock_items']->take(5) as $prod)
                        <div class="py-3 first:pt-0 last:pb-0 flex items-center justify-between gap-3">
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
                        <a href="{{ route('products.index', ['low_stock' => 1]) }}" class="text-xs text-indigo-600 dark:text-indigo-400 font-semibold hover:underline">
                            Lihat {{ $stockSummary['low_stock_count'] - 5 }} produk menipis lainnya &rarr;
                        </a>
                    </div>
                @endif
            </x-card>
        </div>
    </div>

    <!-- 2. Row: Produk Terlaris (Top Selling Products with Category Icons) -->
    <div class="mb-8">
        <x-card title="Produk Terlaris (Top Selling)" subtitle="Peringkat 5 produk dengan volume penjualan tertinggi dan perputaran transaksi terbaik">
            <x-slot:action>
                <a href="{{ route('products.index') }}" class="inline-flex items-center gap-1 text-xs text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 font-bold hover:underline">
                    Lihat Semua Produk &rarr;
                </a>
            </x-slot:action>

            @if($topProducts->isNotEmpty())
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
                    @foreach($topProducts as $index => $item)
                        <div class="relative group flex flex-col justify-between p-4.5 rounded-2xl bg-slate-50/80 dark:bg-slate-800/80 border border-slate-200/80 dark:border-slate-700/80 hover:border-indigo-400 dark:hover:border-indigo-500 hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200">
                            <!-- Top Rank Badge -->
                            <div class="flex items-center justify-between gap-2 mb-3">
                                @if($index === 0)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-extrabold bg-amber-100 text-amber-800 dark:bg-amber-950/70 dark:text-amber-300 border border-amber-300/80 shadow-xs">
                                        👑 #1 Terlaris
                                    </span>
                                @elseif($index === 1)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-extrabold bg-slate-200 text-slate-800 dark:bg-slate-700 dark:text-slate-200 border border-slate-300/80 shadow-xs">
                                        🥈 #2
                                    </span>
                                @elseif($index === 2)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-extrabold bg-orange-100 text-orange-900 dark:bg-orange-950/70 dark:text-orange-300 border border-orange-300/80 shadow-xs">
                                        🥉 #3
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-extrabold bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400 border border-slate-200 dark:border-slate-700">
                                        #{{ $index + 1 }}
                                    </span>
                                @endif

                                <span class="text-[10px] font-mono font-bold text-slate-400 uppercase">
                                    {{ $item->product?->code ?? 'PROD' }}
                                </span>
                            </div>

                            <!-- Product Icon & Information -->
                            <div class="flex flex-col items-center text-center my-1">
                                <x-product-icon :category="$item->product?->category" :name="$item->product_name" size="lg" class="mb-3 group-hover:scale-105 transition-transform duration-200" />
                                
                                <h4 class="font-extrabold text-xs sm:text-sm text-slate-900 dark:text-white line-clamp-2 title-hover" title="{{ $item->product_name }}">
                                    {{ $item->product_name }}
                                </h4>

                                <span class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 truncate max-w-full">
                                    {{ $item->product?->category?->name ?? 'Katalog Konter' }}
                                </span>
                            </div>

                            <!-- Sales Metrics Box -->
                            <div class="mt-4 pt-3 border-t border-slate-200/70 dark:border-slate-700/60 space-y-2 text-xs">
                                <div class="flex items-center justify-between">
                                    <span class="text-slate-500 dark:text-slate-400 text-[11px]">Terjual:</span>
                                    <span class="font-bold text-slate-900 dark:text-white bg-slate-200/60 dark:bg-slate-700/60 px-2 py-0.5 rounded-md">
                                        {{ $item->total_sold }} {{ $item->product?->unit ?? 'pcs' }}
                                    </span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-slate-500 dark:text-slate-400 text-[11px]">Total Omzet:</span>
                                    <span class="font-bold text-indigo-600 dark:text-indigo-400 font-mono">
                                        @rupiah($item->total_revenue)
                                    </span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-slate-500 dark:text-slate-400 text-[11px]">Est. Laba:</span>
                                    <span class="font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/50 px-2 py-0.5 rounded-md font-mono">
                                        + @rupiah($item->total_profit)
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <x-empty-state
                    title="Belum Ada Riwayat Penjualan Produk"
                    description="Peringkat 5 produk terlaris akan otomatis dihitung dan ditampilkan di sini lengkap dengan icon kategori dan rincian labanya setelah transaksi penjualan dilakukan."
                >
                    <x-slot:action>
                        <a href="{{ route('incomes.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 shadow-md transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            Catat Penjualan Produk
                        </a>
                    </x-slot:action>
                </x-empty-state>
            @endif
        </x-card>
    </div>

    <!-- 3. Row: Transaksi Terbaru (With Modal, Jual, & Laba Columns) -->
    <x-card title="Transaksi Terbaru" subtitle="Daftar arus kas masuk dan keluar terkini lengkap dengan rincian harga modal, harga jual, dan estimasi laba">
        <x-slot:action>
            <a href="{{ route('reports.index') }}" class="inline-flex items-center gap-1 text-xs text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 font-bold hover:underline">
                Lihat Seluruh Laporan Transaksi &rarr;
            </a>
        </x-slot:action>

        <div class="overflow-x-auto -mx-6">
            <table class="min-w-full divide-y divide-slate-200/80 dark:divide-slate-700 text-left text-xs">
                <thead class="bg-slate-50/80 dark:bg-slate-850 text-slate-500 dark:text-slate-400 uppercase font-bold tracking-wider">
                    <tr>
                        <th class="px-6 py-3.5">Invoice & Waktu</th>
                        <th class="px-4 py-3.5">Jenis</th>
                        <th class="px-5 py-3.5">Kategori / Keterangan</th>
                        <th class="px-4 py-3.5">Metode</th>
                        <th class="px-4 py-3.5 text-right">Harga Modal</th>
                        <th class="px-4 py-3.5 text-right">Harga Jual</th>
                        <th class="px-5 py-3.5 text-right">Laba Bersih</th>
                        <th class="px-6 py-3.5 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60 font-medium">
                    @forelse($recentTransactions as $tx)
                        <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-750/40 transition-colors">
                            <!-- Invoice & Waktu -->
                            <td class="px-6 py-3.5">
                                <span class="font-mono text-slate-900 dark:text-slate-100 font-bold block">
                                    {{ $tx->invoice_number }}
                                </span>
                                <span class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 block">
                                    @tanggalWaktu($tx->transaction_date)
                                </span>
                            </td>

                            <!-- Jenis Badge -->
                            <td class="px-4 py-3.5">
                                @if($tx->type === 'pemasukan')
                                    <x-badge color="emerald" size="sm">Pemasukan</x-badge>
                                @else
                                    <x-badge color="rose" size="sm">Pengeluaran</x-badge>
                                @endif
                            </td>

                            <!-- Kategori & Keterangan -->
                            <td class="px-5 py-3.5 text-slate-700 dark:text-slate-300">
                                <span class="font-bold text-slate-900 dark:text-slate-100 block">
                                    {{ $tx->category->name ?? 'Umum' }}
                                </span>
                                @if($tx->notes)
                                    <span class="block text-[11px] text-slate-400 truncate max-w-xs mt-0.5">{{ $tx->notes }}</span>
                                @elseif($tx->items->isNotEmpty())
                                    <span class="block text-[11px] text-slate-400 truncate max-w-xs mt-0.5">
                                        {{ $tx->items->pluck('product_name')->join(', ') }}
                                    </span>
                                @endif
                            </td>

                            <!-- Metode Bayar -->
                            <td class="px-4 py-3.5 capitalize text-slate-600 dark:text-slate-400">
                                <x-badge color="slate" size="sm">{{ $tx->payment_method }}</x-badge>
                            </td>

                            <!-- Harga Modal -->
                            <td class="px-4 py-3.5 text-right font-mono">
                                @if($tx->type === 'pemasukan')
                                    @if($tx->cost_price > 0)
                                        <span class="text-slate-600 dark:text-slate-300 font-semibold">@rupiah($tx->cost_price)</span>
                                    @else
                                        <span class="text-slate-400 text-xs">-</span>
                                    @endif
                                @else
                                    <span class="text-slate-700 dark:text-slate-300 font-semibold">@rupiah($tx->cost_price)</span>
                                @endif
                            </td>

                            <!-- Harga Jual -->
                            <td class="px-4 py-3.5 text-right font-mono">
                                @if($tx->type === 'pemasukan')
                                    <span class="font-bold text-slate-900 dark:text-white">@rupiah($tx->selling_price)</span>
                                @else
                                    <span class="text-slate-400 text-xs">-</span>
                                @endif
                            </td>

                            <!-- Laba Bersih -->
                            <td class="px-5 py-3.5 text-right">
                                @if($tx->profit > 0)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-extrabold text-emerald-700 bg-emerald-50 dark:bg-emerald-950/50 dark:text-emerald-300 border border-emerald-200/80 dark:border-emerald-800/50 font-mono">
                                        + @rupiah($tx->profit)
                                    </span>
                                @elseif($tx->profit < 0)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-extrabold text-rose-700 bg-rose-50 dark:bg-rose-950/50 dark:text-rose-300 border border-rose-200/80 dark:border-rose-800/50 font-mono">
                                        - @rupiah(abs($tx->profit))
                                    </span>
                                @else
                                    <span class="text-xs text-slate-400 font-mono font-semibold">Rp 0</span>
                                @endif
                            </td>

                            <!-- Aksi -->
                            <td class="px-6 py-3.5 text-center">
                                @if($tx->type === 'pemasukan')
                                    <a href="{{ route('incomes.show', $tx) }}" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-950/40 hover:bg-indigo-100 transition-colors">
                                        Detail &rarr;
                                    </a>
                                @else
                                    <a href="{{ route('expenses.index', ['search' => $tx->invoice_number]) }}" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold text-slate-600 dark:text-slate-400 bg-slate-100 dark:bg-slate-750 hover:bg-slate-200 transition-colors">
                                        Lihat &rarr;
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-6">
                                <x-empty-state
                                    title="Belum Ada Transaksi Tercatat"
                                    description="Aktivitas arus kas masuk dan keluar masih kosong. Transaksi terbaru akan muncul secara otomatis di sini lengkap dengan rincian modal, omzet jual, dan labanya."
                                />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>

    <x-slot:scripts>
        <!-- Chart.js Library -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const canvas = document.getElementById('cashflowChart');
                if (!canvas) return;

                const ctx = canvas.getContext('2d');

                const chartLabels = {!! json_encode(array_column($dailyTrend, 'label')) !!};
                const incomeData = {!! json_encode(array_column($dailyTrend, 'income')) !!};
                const expenseData = {!! json_encode(array_column($dailyTrend, 'expense')) !!};

                // Gradients for line fill
                const incGradient = ctx.createLinearGradient(0, 0, 0, 280);
                incGradient.addColorStop(0, 'rgba(16, 185, 129, 0.32)');
                incGradient.addColorStop(1, 'rgba(16, 185, 129, 0.0)');

                const expGradient = ctx.createLinearGradient(0, 0, 0, 280);
                expGradient.addColorStop(0, 'rgba(244, 63, 94, 0.30)');
                expGradient.addColorStop(1, 'rgba(244, 63, 94, 0.0)');

                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: chartLabels,
                        datasets: [
                            {
                                label: 'Pemasukan',
                                data: incomeData,
                                borderColor: '#10b981',
                                backgroundColor: incGradient,
                                borderWidth: 3,
                                fill: true,
                                tension: 0.38,
                                pointBackgroundColor: '#10b981',
                                pointBorderColor: '#ffffff',
                                pointBorderWidth: 2,
                                pointRadius: 4,
                                pointHoverRadius: 7,
                            },
                            {
                                label: 'Pengeluaran',
                                data: expenseData,
                                borderColor: '#f43f5e',
                                backgroundColor: expGradient,
                                borderWidth: 3,
                                fill: true,
                                tension: 0.38,
                                pointBackgroundColor: '#f43f5e',
                                pointBorderColor: '#ffffff',
                                pointBorderWidth: 2,
                                pointRadius: 4,
                                pointHoverRadius: 7,
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: {
                            mode: 'index',
                            intersect: false,
                        },
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                backgroundColor: 'rgba(15, 23, 42, 0.94)',
                                titleColor: '#f8fafc',
                                bodyColor: '#f1f5f9',
                                titleFont: { family: "'Plus Jakarta Sans', sans-serif", size: 12, weight: 'bold' },
                                bodyFont: { family: "'Plus Jakarta Sans', sans-serif", size: 12 },
                                padding: 12,
                                cornerRadius: 12,
                                displayColors: true,
                                boxWidth: 8,
                                boxHeight: 8,
                                usePointStyle: true,
                                callbacks: {
                                    label: function(context) {
                                        const val = context.parsed.y || 0;
                                        return ' ' + context.dataset.label + ': Rp ' + val.toLocaleString('id-ID');
                                    },
                                    footer: function(tooltipItems) {
                                        let inc = 0;
                                        let exp = 0;
                                        tooltipItems.forEach(function(item) {
                                            if (item.datasetIndex === 0) inc = item.parsed.y || 0;
                                            if (item.datasetIndex === 1) exp = item.parsed.y || 0;
                                        });
                                        const diff = inc - exp;
                                        const sign = diff >= 0 ? '+' : '';
                                        return '\nSelisih Kas: ' + sign + 'Rp ' + diff.toLocaleString('id-ID');
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    font: { family: "'Plus Jakarta Sans', sans-serif", size: 11, weight: '600' },
                                    color: '#94a3b8'
                                }
                            },
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: 'rgba(148, 163, 184, 0.12)',
                                    drawBorder: false,
                                },
                                ticks: {
                                    font: { family: "'Plus Jakarta Sans', sans-serif", size: 10, weight: '500' },
                                    color: '#94a3b8',
                                    callback: function(value) {
                                        if (value >= 1000000) {
                                            return 'Rp ' + (value / 1000000).toFixed(1) + 'jt';
                                        } else if (value >= 1000) {
                                            return 'Rp ' + (value / 1000).toFixed(0) + 'rb';
                                        }
                                        return 'Rp ' + value;
                                    }
                                }
                            }
                        }
                    }
                });
            });
        </script>
    </x-slot:scripts>
</x-layouts.app>
