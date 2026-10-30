<x-layouts.app title="Dashboard">
    <x-slot:header>
        Dashboard Ringkasan
    </x-slot:header>

    <!-- Welcome Hero Banner -->
    <div class="relative overflow-hidden mb-8 rounded-3xl bg-gradient-to-br from-slate-900 via-indigo-950 to-slate-900 border border-indigo-900/40 text-white p-6 sm:p-8 shadow-xl shadow-indigo-950/20">
        <!-- Background Subtle Glows -->
        <div class="absolute -right-12 -top-12 w-64 h-64 bg-indigo-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -left-12 -bottom-12 w-64 h-64 bg-sky-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col xl:flex-row xl:items-center xl:justify-between gap-5">
            <div class="min-w-0 max-w-lg lg:max-w-xl">
                <!-- Status Badge & Tanggal Terkini (Kotak Merah 1) -->
                <div class="inline-flex items-center gap-2.5 px-3.5 py-1.5 rounded-full bg-white/10 hover:bg-white/15 border border-white/20 backdrop-blur-md shadow-xs mb-3.5 transition-all">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-400 shadow-xs shadow-emerald-400/50"></span>
                    </span>
                    <span class="text-xs font-bold text-white tracking-wide">Sistem KonterKu Aktif</span>
                    <span class="text-white/30">•</span>
                    <span class="text-xs font-medium text-slate-200 flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span>{{ now()->locale('id')->translatedFormat('l, d F Y') }}</span>
                    </span>
                </div>
                <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-white">
                    Selamat Datang Bos, <span class="bg-gradient-to-r from-white via-indigo-100 to-indigo-300 bg-clip-text text-transparent">{{ Auth::user()->name ?? 'Administrator' }}</span> 👋
                </h2>
                <p class="text-xs sm:text-sm text-slate-300/90 mt-1.5 leading-relaxed">
                    Pantau kinerja penjualan harian, tren grafik kas, produk terlaris, serta pergerakan laba rugi transaksi konter secara real-time.
                </p>
            </div>

            <!-- Quick Info Stat Cards (3 Kartu Sejajar Berdampingan) -->
            <div class="flex items-center gap-2.5 sm:gap-3 shrink-0 flex-nowrap overflow-x-auto pb-1 sm:pb-0">
                <!-- Total Produk -->
                <div class="flex items-center gap-2.5 sm:gap-3 px-3.5 py-2.5 sm:px-4 sm:py-3 rounded-2xl bg-white/10 hover:bg-white/15 border border-white/20 backdrop-blur-md shadow-sm shadow-black/20 transition-all duration-200 shrink-0">
                    <div class="w-9 h-9 rounded-xl bg-indigo-500/25 border border-indigo-400/30 text-indigo-200 flex items-center justify-center shrink-0 shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    </div>
                    <div>
                        <span class="text-[10px] uppercase font-extrabold tracking-wider text-slate-300 block">Total Produk</span>
                        <div class="flex items-baseline gap-1">
                            <span class="text-base sm:text-lg font-black font-mono text-white tracking-tight">{{ $stockSummary['total_products'] }}</span>
                            <span class="text-xs font-semibold text-slate-300">Item</span>
                        </div>
                    </div>
                </div>

                <!-- Transaksi Hari Ini -->
                <div class="flex items-center gap-2.5 sm:gap-3 px-3.5 py-2.5 sm:px-4 sm:py-3 rounded-2xl bg-white/10 hover:bg-white/15 border border-white/20 backdrop-blur-md shadow-sm shadow-black/20 transition-all duration-200 shrink-0">
                    <div class="w-9 h-9 rounded-xl bg-emerald-500/25 border border-emerald-400/30 text-emerald-300 flex items-center justify-center shrink-0 shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <span class="text-[10px] uppercase font-extrabold tracking-wider text-slate-300 block">Transaksi Hari Ini</span>
                        <div class="flex items-baseline gap-1">
                            <span class="text-base sm:text-lg font-black font-mono text-emerald-400 tracking-tight">{{ $todayTransactionsCount }}</span>
                            <span class="text-xs font-semibold text-emerald-300/80">Selesai</span>
                        </div>
                    </div>
                </div>

                <!-- Total Piutang (Berada di samping Transaksi Hari Ini) -->
                <a href="{{ route('kasbons.index') }}" class="flex items-center gap-2.5 sm:gap-3 px-3.5 py-2.5 sm:px-4 sm:py-3 rounded-2xl {{ $totalUnpaidKasbon > 0 ? 'bg-amber-500/15 hover:bg-amber-500/25 border border-amber-400/30' : 'bg-white/10 hover:bg-white/15 border border-white/20' }} backdrop-blur-md shadow-sm shadow-black/20 transition-all duration-200 group shrink-0">
                    <div class="w-9 h-9 rounded-xl {{ $totalUnpaidKasbon > 0 ? 'bg-amber-500/25 border border-amber-400/30 text-amber-300' : 'bg-white/10 border border-white/20 text-slate-300' }} flex items-center justify-center shrink-0 shadow-sm group-hover:scale-105 transition-transform">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    <div>
                        <span class="text-[10px] uppercase font-extrabold tracking-wider {{ $totalUnpaidKasbon > 0 ? 'text-amber-200' : 'text-slate-300' }} block">Total Piutang</span>
                        <div class="flex items-baseline gap-1">
                            <span class="text-base sm:text-lg font-black font-mono {{ $totalUnpaidKasbon > 0 ? 'text-amber-400' : 'text-white' }} tracking-tight">@rupiah($totalUnpaidKasbon)</span>
                            <span class="text-xs font-semibold {{ $totalUnpaidKasbon > 0 ? 'text-amber-300/80' : 'text-slate-400' }}">({{ $totalUnpaidKasbonCount }})</span>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <!-- Alert Notifikasi Piutang Kasbon (Jika Ada Hutang Menggantung) -->
    @if($totalUnpaidKasbon > 0)
        <div class="mb-8 p-4 sm:p-5 rounded-2xl bg-gradient-to-r from-amber-500/10 via-amber-500/5 to-transparent border border-amber-300/80 dark:border-amber-700/60 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 shadow-xs">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-amber-500 text-white flex items-center justify-center shrink-0 shadow-md shadow-amber-500/25">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <h4 class="text-sm font-black text-slate-900 dark:text-white flex items-center gap-2 flex-wrap">
                        <span>Perhatian: Ada Piutang Kasbon Belum Lunas Sebesar @rupiah($totalUnpaidKasbon)</span>
                        @if($overdueKasbonCount > 0)
                            <span class="px-2 py-0.5 rounded-full text-[11px] font-extrabold bg-rose-100 text-rose-700 dark:bg-rose-950 dark:text-rose-300 border border-rose-300/80">
                                {{ $overdueKasbonCount }} Lewat Jatuh Tempo
                            </span>
                        @endif
                    </h4>
                    <p class="text-xs text-slate-600 dark:text-slate-400 mt-0.5">
                        Tercatat <strong>{{ $totalUnpaidKasbonCount }} transaksi kasbon pelanggan</strong> yang belum melunasi pembayaran (potensi laba tertahan: <strong class="text-amber-600 dark:text-amber-400 font-mono">+@rupiah($totalUnpaidKasbonProfit)</strong>).
                    </p>
                </div>
            </div>
            <a href="{{ route('kasbons.index') }}" class="inline-flex items-center justify-center gap-1.5 px-4 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold shadow-sm shadow-amber-500/30 transition-all shrink-0">
                <span>Buka Buku Kasbon</span>
                <span>&rarr;</span>
            </a>
        </div>
    @endif

    <!-- Statistics Cards Grid (Modern Elevated Aesthetic - 5 Kolom Finansial) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-5 mb-8">
        <!-- Pemasukan Hari Ini -->
        <div class="relative overflow-hidden bg-white dark:bg-slate-800 rounded-2xl p-5 border border-slate-200/80 dark:border-slate-700/80 shadow-xs hover:shadow-md transition-all duration-200">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Pemasukan Hari Ini</span>
                <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shadow-xs">
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
                    <span>hari ini</span>
                </div>
            </div>
        </div>

        <!-- Pengeluaran Hari Ini -->
        <div class="relative overflow-hidden bg-white dark:bg-slate-800 rounded-2xl p-5 border border-slate-200/80 dark:border-slate-700/80 shadow-xs hover:shadow-md transition-all duration-200">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Pengeluaran Hari Ini</span>
                <div class="w-10 h-10 rounded-xl bg-rose-50 dark:bg-rose-950/50 text-rose-600 dark:text-rose-400 flex items-center justify-center shadow-xs">
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
                    <span>& belanja toko</span>
                </div>
            </div>
        </div>

        <!-- Laba Bersih Bulan Ini -->
        <div class="relative overflow-hidden bg-white dark:bg-slate-800 rounded-2xl p-5 border border-slate-200/80 dark:border-slate-700/80 shadow-xs hover:shadow-md transition-all duration-200">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Laba Bersih Bulan Ini</span>
                <div class="w-10 h-10 rounded-xl {{ $monthlyNetProfit >= 0 ? 'bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400' : 'bg-rose-50 dark:bg-rose-950/50 text-rose-600 dark:text-rose-400' }} flex items-center justify-center shadow-xs">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <div class="mt-4">
                <div class="text-2xl font-black font-mono tracking-tight {{ $monthlyNetProfit >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                    {{ $monthlyNetProfit >= 0 ? '+' : '' }}@rupiah($monthlyNetProfit)
                </div>
                <div class="mt-2 flex items-center justify-between text-xs text-slate-500">
                    <span class="inline-flex items-center gap-1 font-semibold {{ $monthlyNetProfit >= 0 ? 'text-emerald-700 dark:text-emerald-300 bg-emerald-50 dark:bg-emerald-950/40' : 'text-rose-700 dark:text-rose-300 bg-rose-50 dark:bg-rose-950/40' }} px-2 py-0.5 rounded-md">
                        {{ $monthlyIncome > 0 ? round(($monthlyNetProfit / $monthlyIncome) * 100, 1) : 0 }}% Margin
                    </span>
                    <span class="text-slate-400 text-[11px] font-mono">Net Profit</span>
                </div>
            </div>
        </div>

        <!-- Total Piutang Kasbon Menggantung -->
        <a href="{{ route('kasbons.index') }}" class="relative overflow-hidden bg-white dark:bg-slate-800 rounded-2xl p-5 border {{ $totalUnpaidKasbon > 0 ? 'border-amber-300/90 dark:border-amber-700/80 shadow-xs' : 'border-slate-200/80 dark:border-slate-700/80 shadow-xs' }} hover:shadow-md hover:border-amber-400 dark:hover:border-amber-500 transition-all duration-200 group block">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider group-hover:text-amber-600 dark:group-hover:text-amber-400 transition-colors">Total Piutang Kasbon</span>
                <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-950/50 text-amber-600 dark:text-amber-400 flex items-center justify-center shadow-xs">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <div class="text-2xl font-black font-mono text-amber-600 dark:text-amber-400 tracking-tight">
                    @rupiah($totalUnpaidKasbon)
                </div>
                <div class="mt-2 flex items-center justify-between text-xs text-slate-500">
                    @if($overdueKasbonCount > 0)
                        <span class="inline-flex items-center gap-1 text-rose-600 font-semibold bg-rose-50 dark:bg-rose-950/40 px-2 py-0.5 rounded-md">
                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500 animate-pulse"></span>
                            {{ $overdueKasbonCount }} jatuh tempo
                        </span>
                    @elseif($totalUnpaidKasbonCount > 0)
                        <span class="inline-flex items-center gap-1 text-amber-700 dark:text-amber-300 font-semibold bg-amber-50 dark:bg-amber-950/40 px-2 py-0.5 rounded-md">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                            {{ $totalUnpaidKasbonCount }} pelanggan
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 text-emerald-600 font-semibold bg-emerald-50 dark:bg-emerald-950/40 px-2 py-0.5 rounded-md">
                            Semua lunas
                        </span>
                    @endif
                    <span class="text-indigo-600 dark:text-indigo-400 text-[11px] font-bold group-hover:underline">Buka Kasbon &rarr;</span>
                </div>
            </div>
        </a>

        <!-- Stok Perlu Restock -->
        <div class="relative overflow-hidden bg-white dark:bg-slate-800 rounded-2xl p-5 border border-slate-200/80 dark:border-slate-700/80 shadow-xs hover:shadow-md transition-all duration-200">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Stok Perlu Restock</span>
                <div class="w-10 h-10 rounded-xl {{ $stockSummary['low_stock_count'] > 0 ? 'bg-amber-50 dark:bg-amber-950/50 text-amber-600 dark:text-amber-400' : 'bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400' }} flex items-center justify-center shadow-xs">
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

    <!-- WIDGET CATATAN SALDO APLIKASI & SERVER KONTER -->
    @include('dashboard.partials.server-deposits-widget')

    <!-- 1. Row: Grafik Tren Laba Bersih 7 Hari Terakhir & Peringatan Stok Menipis -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Grafik Tren Laba Bersih (Chart.js Modern Canvas) -->
        <div class="lg:col-span-2">
            <x-card title="Grafik Tren Laba Bersih 7 Hari Terakhir" subtitle="Visualisasi dinamika naik turunnya keuntungan bersih harian toko setelah dipotong modal & beban">
                <x-slot:action>
                    <div class="flex items-center gap-2 flex-wrap text-xs">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200/80 dark:border-emerald-800/60 text-emerald-700 dark:text-emerald-300 font-bold shadow-2xs">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span>Total 7 Hari: {{ $sevenDaysNetProfit >= 0 ? '+' : '' }}@rupiah($sevenDaysNetProfit)</span>
                        </span>
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-semibold">
                            Rata-rata: {{ $avgDailyNetProfit >= 0 ? '+' : '' }}@rupiah($avgDailyNetProfit)/hari
                        </span>
                    </div>
                </x-slot:action>

                <!-- Chart Container -->
                <div class="relative w-full h-72 sm:h-80 pt-2">
                    <canvas id="netProfitChart"></canvas>
                </div>

                <!-- Chart Footer Legend & Net Summary -->
                <div class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-700/60 flex flex-wrap items-center justify-between gap-4 text-xs text-slate-500">
                    <div class="flex items-center gap-3">
                        <span class="inline-flex items-center gap-1.5 font-bold text-emerald-600 dark:text-emerald-400">
                            <span class="w-3 h-3 rounded-full bg-emerald-500 shadow-xs shadow-emerald-500/50"></span>
                            <span>Laba Bersih Harian (Net Profit)</span>
                        </span>
                        <span class="text-slate-400 text-[11px] hidden sm:inline">
                            (Omzet Kasir &minus; Modal HPP &minus; Beban Operasional)
                        </span>
                    </div>
                    <div class="text-right font-medium">
                        <span>Laba Bersih Hari Ini: </span>
                        <strong class="{{ $todayNetProfit >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600' }} font-mono font-bold">
                            {{ $todayNetProfit >= 0 ? '+' : '' }}@rupiah($todayNetProfit)
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
                const canvas = document.getElementById('netProfitChart');
                if (!canvas) return;

                const ctx = canvas.getContext('2d');

                const chartLabels = {!! json_encode(array_column($dailyTrend, 'label')) !!};
                const netProfitData = {!! json_encode(array_column($dailyTrend, 'net_profit')) !!};
                const incomeData = {!! json_encode(array_column($dailyTrend, 'income')) !!};
                const cogsData = {!! json_encode(array_column($dailyTrend, 'cogs')) !!};
                const grossProfitData = {!! json_encode(array_column($dailyTrend, 'gross_profit')) !!};
                const expenseData = {!! json_encode(array_column($dailyTrend, 'expense')) !!};

                // Gradient mulus untuk Laba Bersih
                const profitGradient = ctx.createLinearGradient(0, 0, 0, 290);
                profitGradient.addColorStop(0, 'rgba(16, 185, 129, 0.35)');
                profitGradient.addColorStop(0.5, 'rgba(16, 185, 129, 0.10)');
                profitGradient.addColorStop(1, 'rgba(16, 185, 129, 0.0)');

                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: chartLabels,
                        datasets: [
                            {
                                label: 'Laba Bersih',
                                data: netProfitData,
                                borderColor: '#10b981', // Emerald 500
                                backgroundColor: profitGradient,
                                borderWidth: 3.5,
                                fill: true,
                                tension: 0.36,
                                pointBackgroundColor: '#10b981',
                                pointBorderColor: '#ffffff',
                                pointBorderWidth: 2.5,
                                pointRadius: 5,
                                pointHoverRadius: 8,
                                pointHoverBackgroundColor: '#047857',
                                pointHoverBorderColor: '#ffffff',
                                pointHoverBorderWidth: 3,
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
                                backgroundColor: 'rgba(15, 23, 42, 0.96)',
                                titleColor: '#ffffff',
                                bodyColor: '#f1f5f9',
                                titleFont: { family: "'Plus Jakarta Sans', sans-serif", size: 12, weight: 'bold' },
                                bodyFont: { family: "'Plus Jakarta Sans', sans-serif", size: 11 },
                                padding: 14,
                                cornerRadius: 14,
                                borderColor: 'rgba(51, 65, 85, 0.6)',
                                borderWidth: 1,
                                displayColors: false,
                                callbacks: {
                                    title: function(items) {
                                        return '📅 Tanggal: ' + items[0].label;
                                    },
                                    label: function(context) {
                                        const val = context.parsed.y || 0;
                                        const sign = val >= 0 ? '+' : '';
                                        return '⭐ Laba Bersih: ' + sign + 'Rp ' + val.toLocaleString('id-ID');
                                    },
                                    afterBody: function(items) {
                                        const idx = items[0].dataIndex;
                                        const inc = incomeData[idx] || 0;
                                        const cogs = cogsData[idx] || 0;
                                        const gross = grossProfitData[idx] || 0;
                                        const exp = expenseData[idx] || 0;
                                        return [
                                            '───────────────────────',
                                            '• Omzet Kasir: Rp ' + inc.toLocaleString('id-ID'),
                                            '• Modal Kulakan (HPP): -Rp ' + cogs.toLocaleString('id-ID'),
                                            '• Laba Kotor Penjualan: Rp ' + gross.toLocaleString('id-ID'),
                                            '• Beban Pengeluaran: -Rp ' + exp.toLocaleString('id-ID'),
                                        ];
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
                                grid: {
                                    color: 'rgba(148, 163, 184, 0.12)',
                                    drawBorder: false,
                                },
                                ticks: {
                                    font: { family: "'Plus Jakarta Sans', sans-serif", size: 10, weight: '500' },
                                    color: '#94a3b8',
                                    callback: function(value) {
                                        const sign = value < 0 ? '-' : (value > 0 ? '+' : '');
                                        const abs = Math.abs(value);
                                        if (abs >= 1000000) {
                                            return sign + 'Rp ' + (abs / 1000000).toFixed(1) + 'jt';
                                        } else if (abs >= 1000) {
                                            return sign + 'Rp ' + (abs / 1000).toFixed(0) + 'rb';
                                        }
                                        return sign + 'Rp ' + abs;
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
