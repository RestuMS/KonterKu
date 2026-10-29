<x-layouts.app title="Daftar Pengeluaran & Biaya">
    <x-slot:header>
        Pengeluaran Kas & Beban Konter
    </x-slot:header>

    @php
        $todayStr = now()->format('Y-m-d');
        $weekAgoStr = now()->subDays(6)->format('Y-m-d');
        $monthStartStr = now()->startOfMonth()->format('Y-m-d');
        $monthEndStr = now()->endOfMonth()->format('Y-m-d');

        $isFiltered = request()->hasAny(['search', 'category_id', 'payment_method', 'start_date', 'end_date']);
    @endphp

    <div class="space-y-6">
        <!-- ================================================================= -->
        <!-- 1. HEADER HALAMAN ELEGAN & CERAH (LIGHT & AIRY)                   -->
        <!-- ================================================================= -->
        <div class="p-6 sm:p-7 rounded-3xl bg-gradient-to-r from-rose-50/80 via-white to-amber-50/50 dark:from-slate-850 dark:via-slate-800 dark:to-slate-850 border border-rose-100/80 dark:border-slate-700 shadow-sm">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-5">
                <div class="flex items-start sm:items-center gap-4">
                    <!-- Icon Kas Keluar Vektor -->
                    <div class="w-13 h-13 rounded-2xl bg-gradient-to-tr from-rose-600 to-rose-500 text-white flex items-center justify-center shadow-lg shadow-rose-600/25 shrink-0">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M7 11l-3 3m0 0l3 3m-3-3h8"/>
                        </svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2.5 flex-wrap">
                            <h2 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight">
                                Pengeluaran Kas & Beban Konter
                            </h2>
                            <span class="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full bg-rose-100/80 dark:bg-rose-950/60 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-300 text-xs font-bold shadow-2xs">
                                <span class="w-2 h-2 rounded-full bg-rose-500 animate-pulse"></span>
                                <span>{{ $expenses->total() }} Transaksi</span>
                            </span>
                        </div>
                        <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">
                            Pantau dan catat seluruh biaya: jajan makan, beli kuota, beli mainan, beli bensin, dan operasional toko.
                        </p>
                    </div>
                </div>

                <!-- Tombol Aksi Cepat -->
                <div class="flex items-center gap-2.5 flex-wrap self-start md:self-auto">
                    <x-button 
                        href="{{ route('reports.index') }}" 
                        variant="secondary" 
                        size="sm" 
                        class="bg-white hover:bg-slate-50 text-slate-700 dark:bg-slate-800 dark:text-slate-200 border-slate-200 dark:border-slate-700 font-bold text-xs shadow-2xs transition-all"
                    >
                        <svg class="w-4 h-4 mr-1.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span>Laporan Keuangan</span>
                    </x-button>

                    <a 
                        href="{{ route('expenses.create') }}" 
                        class="inline-flex items-center gap-2 px-4.5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs uppercase tracking-wider transition-all shadow-md shadow-rose-600/25 cursor-pointer hover:shadow-lg"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span>Catat Pengeluaran Baru</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- ================================================================= -->
        <!-- 2. TIGA KARTU METRIK FINANSIAL PENGELUARAN (CERAH & ELEGAN)        -->
        <!-- ================================================================= -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
            <!-- 1. Total Pengeluaran Terfilter (Rose Lembut) -->
            <div class="p-5 sm:p-6 rounded-3xl bg-gradient-to-br from-rose-50/90 via-white to-pink-50/40 dark:from-rose-950/40 dark:via-slate-850 dark:to-slate-850 border border-rose-200/70 dark:border-rose-900/50 shadow-xs hover:shadow-md transition-all">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-rose-900 dark:text-rose-300 uppercase tracking-wider">
                        Total Pengeluaran Kas
                    </span>
                    <!-- Icon Vektor Kas Keluar -->
                    <div class="w-11 h-11 rounded-2xl bg-rose-600 text-white flex items-center justify-center shadow-md shadow-rose-600/25">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>

                <div class="mt-4">
                    <div class="text-2xl sm:text-3xl font-black font-mono tracking-tight text-rose-600 dark:text-rose-400">
                        @rupiah($totalAmount)
                    </div>
                    <div class="mt-3 flex items-center justify-between text-xs text-slate-500 dark:text-slate-400">
                        <span>Sesuai kriteria filter aktif</span>
                        <span class="font-bold font-mono px-2.5 py-0.5 rounded-full bg-rose-100/80 dark:bg-rose-950/80 border border-rose-200 dark:border-rose-800 text-rose-700 dark:text-rose-300">
                            {{ $expenses->total() }} Catatan
                        </span>
                    </div>
                </div>
            </div>

            <!-- 2. Pengeluaran Hari Ini (Amber Lembut) -->
            <div class="p-5 sm:p-6 rounded-3xl bg-gradient-to-br from-amber-50/70 via-white to-orange-50/30 dark:from-slate-850 dark:via-slate-850 dark:to-slate-850 border border-amber-200/70 dark:border-slate-700 shadow-xs hover:shadow-md transition-all">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-amber-900 dark:text-amber-300 uppercase tracking-wider">
                        Pengeluaran Hari Ini
                    </span>
                    <!-- Icon Vektor Kalender Harian -->
                    <div class="w-11 h-11 rounded-2xl bg-amber-500 text-white flex items-center justify-center shadow-md shadow-amber-500/25">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>

                <div class="mt-4">
                    <div class="text-2xl sm:text-3xl font-black font-mono tracking-tight text-slate-900 dark:text-white">
                        @rupiah($todayAmount)
                    </div>
                    <div class="mt-3 flex items-center justify-between text-xs text-slate-500 dark:text-slate-400">
                        <span>Biaya kas keluar hari ini</span>
                        <span class="font-bold font-mono px-2.5 py-0.5 rounded-full bg-amber-100/80 dark:bg-amber-950/80 border border-amber-200 dark:border-amber-800 text-amber-800 dark:text-amber-300">
                            {{ now()->translatedFormat('d M Y') }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- 3. Akumulasi Bulan Ini (Indigo Lembut) -->
            <div class="p-5 sm:p-6 rounded-3xl bg-gradient-to-br from-indigo-50/90 via-white to-sky-50/40 dark:from-indigo-950/40 dark:via-slate-850 dark:to-slate-850 border border-indigo-200/80 dark:border-indigo-900/50 shadow-xs hover:shadow-md transition-all">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-indigo-900 dark:text-indigo-300 uppercase tracking-wider">
                        Akumulasi Bulan Ini
                    </span>
                    <!-- Icon Vektor Grafik Batang -->
                    <div class="w-11 h-11 rounded-2xl bg-indigo-600 text-white flex items-center justify-center shadow-md shadow-indigo-600/25">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                </div>

                <div class="mt-4">
                    <div class="text-2xl sm:text-3xl font-black font-mono tracking-tight text-slate-900 dark:text-white">
                        @rupiah($monthAmount)
                    </div>
                    <div class="mt-3 flex items-center justify-between text-xs text-slate-500 dark:text-slate-400">
                        <span>Total beban toko & kas</span>
                        <span class="inline-flex items-center gap-1 font-bold font-mono px-2.5 py-0.5 rounded-full bg-indigo-100/90 dark:bg-indigo-950/80 border border-indigo-200 dark:border-indigo-800 text-indigo-800 dark:text-indigo-300">
                            {{ now()->translatedFormat('F Y') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- ================================================================= -->
        <!-- 3. TOOLBAR FILTER & PENCARIAN TERANG (LIGHT THEMED)               -->
        <!-- ================================================================= -->
        <div class="p-5 sm:p-6 rounded-3xl bg-white dark:bg-slate-850 border border-slate-200/90 dark:border-slate-800 shadow-xs space-y-4">
            <!-- Preset Waktu Cepat (Tabs Cerah) -->
            <div class="flex items-center justify-between gap-3 overflow-x-auto pb-1 scrollbar-thin text-xs">
                <div class="flex items-center gap-1.5 font-semibold">
                    <span class="text-slate-400 text-[11px] uppercase tracking-wider font-bold mr-1 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span>Periode:</span>
                    </span>
                    
                    <!-- Semua Waktu -->
                    <a
                        href="{{ route('expenses.index', array_merge(request()->except(['start_date', 'end_date', 'page']))) }}"
                        class="px-3.5 py-1.5 rounded-xl transition-all cursor-pointer {{ !request('start_date') && !request('end_date') ? 'bg-rose-600 text-white font-bold shadow-xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200' }}"
                    >
                        Semua Waktu
                    </a>

                    <!-- Hari Ini -->
                    <a
                        href="{{ route('expenses.index', array_merge(request()->except(['page']), ['start_date' => $todayStr, 'end_date' => $todayStr])) }}"
                        class="px-3.5 py-1.5 rounded-xl transition-all cursor-pointer {{ request('start_date') === $todayStr && request('end_date') === $todayStr ? 'bg-rose-600 text-white font-bold shadow-xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200' }}"
                    >
                        Hari Ini
                    </a>

                    <!-- 7 Hari Terakhir -->
                    <a
                        href="{{ route('expenses.index', array_merge(request()->except(['page']), ['start_date' => $weekAgoStr, 'end_date' => $todayStr])) }}"
                        class="px-3.5 py-1.5 rounded-xl transition-all cursor-pointer {{ request('start_date') === $weekAgoStr && request('end_date') === $todayStr ? 'bg-rose-600 text-white font-bold shadow-xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200' }}"
                    >
                        7 Hari Terakhir
                    </a>

                    <!-- Bulan Ini -->
                    <a
                        href="{{ route('expenses.index', array_merge(request()->except(['page']), ['start_date' => $monthStartStr, 'end_date' => $monthEndStr])) }}"
                        class="px-3.5 py-1.5 rounded-xl transition-all cursor-pointer {{ request('start_date') === $monthStartStr && request('end_date') === $monthEndStr ? 'bg-rose-600 text-white font-bold shadow-xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200' }}"
                    >
                        Bulan Ini
                    </a>
                </div>

                @if($isFiltered)
                    <a 
                        href="{{ route('expenses.index') }}" 
                        class="text-xs text-rose-600 hover:text-rose-700 font-bold flex items-center gap-1 shrink-0 px-2.5 py-1 rounded-xl bg-rose-50 dark:bg-rose-950/50 border border-rose-200 dark:border-rose-800"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        <span>Reset Filter</span>
                    </a>
                @endif
            </div>

            <!-- Form Filter Input Fields -->
            <form method="GET" action="{{ route('expenses.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3.5 pt-3 border-t border-slate-100 dark:border-slate-800">
                <!-- Pencarian (4 Col) -->
                <div class="sm:col-span-2 lg:col-span-4">
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Cari Pengeluaran</label>
                    <div class="relative">
                        <input
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Cari invoice, keterangan jajan, bensin..."
                            class="w-full pl-9 pr-3 py-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-slate-800 focus:bg-white text-slate-900 dark:text-white placeholder-slate-400 text-xs font-medium focus:ring-2 focus:ring-rose-500 shadow-2xs"
                        >
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400 pointer-events-none">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </span>
                    </div>
                </div>

                <!-- Kategori (2 Col) -->
                <div class="lg:col-span-2">
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Kategori</label>
                    <select
                        name="category_id"
                        class="block w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-slate-800 focus:bg-white text-slate-900 dark:text-slate-100 text-xs font-medium py-2 px-3 focus:ring-2 focus:ring-rose-500 shadow-2xs"
                    >
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Metode Pembayaran (2 Col) -->
                <div class="lg:col-span-2">
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Metode Bayar</label>
                    <select
                        name="payment_method"
                        class="block w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-slate-800 focus:bg-white text-slate-900 dark:text-slate-100 text-xs font-medium py-2 px-3 focus:ring-2 focus:ring-rose-500 shadow-2xs"
                    >
                        <option value="">Semua Metode</option>
                        <option value="cash" {{ request('payment_method') === 'cash' ? 'selected' : '' }}>Tunai (Cash)</option>
                        <option value="transfer" {{ request('payment_method') === 'transfer' ? 'selected' : '' }}>Transfer Bank</option>
                        <option value="qris" {{ request('payment_method') === 'qris' ? 'selected' : '' }}>QRIS / E-Wallet</option>
                    </select>
                </div>

                <!-- Dari Tanggal (2 Col) -->
                <div class="lg:col-span-2">
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Dari Tanggal</label>
                    <input
                        type="date"
                        name="start_date"
                        value="{{ request('start_date') }}"
                        class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-slate-800 focus:bg-white text-slate-900 dark:text-white text-xs font-medium focus:ring-2 focus:ring-rose-500 shadow-2xs"
                    >
                </div>

                <!-- Sampai Tanggal & Tombol Cari (2 Col) -->
                <div class="lg:col-span-2">
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Sampai Tanggal</label>
                    <div class="flex items-center gap-1.5">
                        <input
                            type="date"
                            name="end_date"
                            value="{{ request('end_date') }}"
                            class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-slate-800 focus:bg-white text-slate-900 dark:text-white text-xs font-medium focus:ring-2 focus:ring-rose-500 shadow-2xs"
                        >
                        <button
                            type="submit"
                            class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs uppercase tracking-wider transition-colors shrink-0 shadow-xs cursor-pointer"
                        >
                            Cari
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- ================================================================= -->
        <!-- 4. TABEL RIWAYAT PENGELUARAN KAS BERSIH (LIGHT THEME)              -->
        <!-- ================================================================= -->
        <div class="rounded-3xl bg-white dark:bg-slate-850 border border-slate-200/90 dark:border-slate-800 shadow-xs overflow-hidden">
            <!-- Header Kartu Tabel -->
            <div class="p-5 sm:p-6 border-b border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 bg-slate-50/60 dark:bg-slate-800/40">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-rose-100 text-rose-600 dark:bg-rose-950/60 dark:text-rose-400 flex items-center justify-center font-bold text-sm shadow-2xs">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-black uppercase tracking-wider text-slate-900 dark:text-white">
                            Riwayat Catatan Kas Keluar
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Daftar rekaman pengeluaran kas, beban harian, dan biaya toko</p>
                    </div>
                </div>
                
                <div class="text-xs text-slate-500 dark:text-slate-400 font-mono self-start sm:self-auto">
                    Menampilkan <strong class="text-slate-900 dark:text-white font-bold">{{ $expenses->firstItem() ?? 0 }}-{{ $expenses->lastItem() ?? 0 }}</strong> dari <strong class="text-slate-900 dark:text-white font-bold">{{ $expenses->total() }}</strong> transaksi
                </div>
            </div>

            <!-- Tampilan Desktop: Tabel Lengkap -->
            <div class="hidden md:block overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200/80 dark:divide-slate-800 text-left text-xs">
                    <!-- Head Tabel Terang -->
                    <thead class="bg-slate-50/90 dark:bg-slate-800/90 text-slate-500 dark:text-slate-400 uppercase font-black tracking-wider text-[11px] border-b border-slate-200 dark:border-slate-800">
                        <tr>
                            <th class="px-6 py-4">Faktur & Waktu</th>
                            <th class="px-5 py-4">Kategori</th>
                            <th class="px-5 py-4">Rincian / Keterangan</th>
                            <th class="px-4 py-4">Metode Bayar</th>
                            <th class="px-4 py-4">Pencatat</th>
                            <th class="px-5 py-4 text-right">Nominal Pengeluaran</th>
                            <th class="px-6 py-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80 font-medium">
                        @forelse($expenses as $exp)
                            @php
                                $catName = strtolower($exp->category->name ?? '');
                                $badgeStyle = match(true) {
                                    str_contains($catName, 'jajan') => 'bg-amber-100 text-amber-800 border-amber-200 dark:bg-amber-950/60 dark:text-amber-300 dark:border-amber-800',
                                    str_contains($catName, 'kuota') => 'bg-sky-100 text-sky-800 border-sky-200 dark:bg-sky-950/60 dark:text-sky-300 dark:border-sky-800',
                                    str_contains($catName, 'mainan') => 'bg-purple-100 text-purple-800 border-purple-200 dark:bg-purple-950/60 dark:text-purple-300 dark:border-purple-800',
                                    str_contains($catName, 'bensin') => 'bg-emerald-100 text-emerald-800 border-emerald-200 dark:bg-emerald-950/60 dark:text-emerald-300 dark:border-emerald-800',
                                    str_contains($catName, 'operasional') => 'bg-rose-100 text-rose-800 border-rose-200 dark:bg-rose-950/60 dark:text-rose-300 dark:border-rose-800',
                                    default => 'bg-slate-100 text-slate-700 border-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700',
                                };
                            @endphp
                            <tr class="hover:bg-rose-50/30 dark:hover:bg-slate-800/50 transition-colors group">
                                <!-- 1. Faktur & Waktu -->
                                <td class="px-6 py-4.5">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-rose-50 dark:bg-rose-950/80 text-rose-700 dark:text-rose-300 font-mono font-black border border-rose-200/80 dark:border-rose-800/60 text-xs">
                                        {{ $exp->invoice_number }}
                                    </span>
                                    <div class="flex items-center gap-1 text-[11px] text-slate-500 dark:text-slate-400 mt-1 font-normal">
                                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        <span>@tanggalWaktu($exp->transaction_date)</span>
                                    </div>
                                </td>

                                <!-- 2. Kategori Pengeluaran -->
                                <td class="px-5 py-4.5">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-xl text-[11px] font-bold border shadow-2xs {{ $badgeStyle }}">
                                        {{ $exp->category->name ?? 'Operasional' }}
                                    </span>
                                </td>

                                <!-- 3. Keterangan / Rincian -->
                                <td class="px-5 py-4.5">
                                    <div class="font-bold text-slate-900 dark:text-white text-xs leading-snug">
                                        {{ $exp->notes }}
                                    </div>
                                </td>

                                <!-- 4. Metode Bayar -->
                                <td class="px-4 py-4.5">
                                    @if($exp->payment_method === 'cash')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-[11px] font-bold bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/60 shadow-2xs">
                                            <svg class="w-3.5 h-3.5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                            <span>Tunai</span>
                                        </span>
                                    @elseif($exp->payment_method === 'qris')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-[11px] font-bold bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800/60 shadow-2xs">
                                            <svg class="w-3.5 h-3.5 text-indigo-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                            <span>QRIS</span>
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-[11px] font-bold bg-sky-50 dark:bg-sky-950/60 text-sky-700 dark:text-sky-300 border border-sky-200 dark:border-sky-800/60 shadow-2xs">
                                            <svg class="w-3.5 h-3.5 text-sky-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                            <span>Transfer</span>
                                        </span>
                                    @endif
                                </td>

                                <!-- 5. Operator / Kasir -->
                                <td class="px-4 py-4.5 text-slate-500 dark:text-slate-400 font-medium">
                                    <div class="flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                        <span>{{ $exp->user->name ?? 'Kasir' }}</span>
                                    </div>
                                </td>

                                <!-- 6. Nominal Pengeluaran -->
                                <td class="px-5 py-4.5 text-right font-mono font-black text-rose-600 dark:text-rose-400 text-sm">
                                    - @rupiah($exp->total_amount)
                                </td>

                                <!-- 7. Aksi Edit & Hapus -->
                                <td class="px-6 py-4.5 text-center">
                                    <div class="flex items-center justify-center gap-1">
                                        <a
                                            href="{{ route('expenses.edit', $exp) }}"
                                            class="p-2 rounded-xl text-slate-400 hover:text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-950/30 transition-colors"
                                            title="Edit Catatan Pengeluaran"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </a>

                                        <button
                                            type="button"
                                            @click="$dispatch('open-modal', 'delete-expense-{{ $exp->id }}')"
                                            class="p-2 rounded-xl text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/30 transition-colors cursor-pointer"
                                            title="Hapus Catatan Pengeluaran"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </div>

                                    <!-- Modal Konfirmasi Hapus -->
                                    <x-modal name="delete-expense-{{ $exp->id }}" title="Konfirmasi Hapus Pengeluaran">
                                        <div class="text-left text-sm text-slate-600 dark:text-slate-400 space-y-3">
                                            <p>Apakah Anda yakin ingin menghapus catatan pengeluaran <strong>{{ $exp->invoice_number }}</strong> senilai <strong class="text-rose-600 font-mono">@rupiah($exp->total_amount)</strong>?</p>
                                            <div class="p-3 bg-slate-50 dark:bg-slate-800 rounded-xl text-xs space-y-1">
                                                <p><strong>Keterangan:</strong> {{ $exp->notes }}</p>
                                                <p><strong>Kategori:</strong> {{ $exp->category->name ?? 'Operasional' }}</p>
                                            </div>
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
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-14">
                                    <x-empty-state
                                        title="Belum Ada Pengeluaran"
                                        description="Tidak ditemukan catatan pengeluaran pada kriteria ini. Mulai catat pengeluaran jajan, beli kuota, bensin, atau operasional sekarang."
                                    >
                                        <x-slot:action>
                                            <x-button href="{{ route('expenses.create') }}" variant="danger" size="sm" class="font-bold">
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

            <!-- Tampilan Mobile: Kartu Responsif Cerah (md:hidden) -->
            <div class="block md:hidden divide-y divide-slate-100 dark:divide-slate-800">
                @forelse($expenses as $exp)
                    @php
                        $catName = strtolower($exp->category->name ?? '');
                        $badgeStyle = match(true) {
                            str_contains($catName, 'jajan') => 'bg-amber-100 text-amber-800 border-amber-200 dark:bg-amber-950/60 dark:text-amber-300 dark:border-amber-800',
                            str_contains($catName, 'kuota') => 'bg-sky-100 text-sky-800 border-sky-200 dark:bg-sky-950/60 dark:text-sky-300 dark:border-sky-800',
                            str_contains($catName, 'mainan') => 'bg-purple-100 text-purple-800 border-purple-200 dark:bg-purple-950/60 dark:text-purple-300 dark:border-purple-800',
                            str_contains($catName, 'bensin') => 'bg-emerald-100 text-emerald-800 border-emerald-200 dark:bg-emerald-950/60 dark:text-emerald-300 dark:border-emerald-800',
                            str_contains($catName, 'operasional') => 'bg-rose-100 text-rose-800 border-rose-200 dark:bg-rose-950/60 dark:text-rose-300 dark:border-rose-800',
                            default => 'bg-slate-100 text-slate-700 border-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700',
                        };
                    @endphp
                    <div class="p-4.5 space-y-3 hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors">
                        <div class="flex items-center justify-between">
                            <span class="font-mono font-black text-xs text-rose-600 dark:text-rose-400 bg-rose-50 dark:bg-rose-950/60 px-2 py-0.5 rounded-lg border border-rose-200 dark:border-rose-800">
                                {{ $exp->invoice_number }}
                            </span>
                            <div class="flex items-center gap-1 text-[11px] text-slate-400 font-medium">
                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span>@tanggalWaktu($exp->transaction_date)</span>
                            </div>
                        </div>

                        <div>
                            <h4 class="font-bold text-slate-900 dark:text-white text-xs leading-snug">
                                {{ $exp->notes }}
                            </h4>
                        </div>

                        <div class="flex items-center justify-between text-xs pt-2 border-t border-slate-100 dark:border-slate-800">
                            <div>
                                <span class="text-[10px] text-slate-400 uppercase tracking-wider block font-bold">Kategori:</span>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-[10px] font-bold border mt-0.5 {{ $badgeStyle }}">
                                    {{ $exp->category->name ?? 'Operasional' }}
                                </span>
                            </div>
                            <div class="text-right">
                                <span class="text-[10px] text-slate-400 uppercase tracking-wider block font-bold">Nominal:</span>
                                <strong class="text-rose-600 dark:text-rose-400 font-mono text-sm font-black">
                                    - @rupiah($exp->total_amount)
                                </strong>
                            </div>
                        </div>

                        <div class="flex items-center justify-between pt-2">
                            <span class="text-[11px] font-bold px-2 py-0.5 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 uppercase">
                                {{ $exp->payment_method }}
                            </span>

                            <div class="flex items-center gap-2">
                                <a
                                    href="{{ route('expenses.edit', $exp) }}"
                                    class="px-3 py-1.5 text-xs rounded-xl border border-amber-200 dark:border-amber-800 font-bold text-amber-700 dark:text-amber-300 bg-amber-50 dark:bg-amber-950/40 hover:bg-amber-100"
                                >
                                    Edit
                                </a>
                                <button
                                    type="button"
                                    @click="$dispatch('open-modal', 'delete-expense-{{ $exp->id }}')"
                                    class="px-3 py-1.5 text-xs rounded-xl border border-rose-200 dark:border-rose-800 font-bold text-rose-700 dark:text-rose-300 bg-rose-50 dark:bg-rose-950/40 hover:bg-rose-100"
                                >
                                    Hapus
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-6 text-center text-xs text-slate-400">
                        Tidak ada catatan pengeluaran ditemukan.
                    </div>
                @endforelse
            </div>

            <!-- Pagination Bar -->
            <div class="p-5 sm:p-6 bg-slate-50/50 dark:bg-slate-800/20 border-t border-slate-100 dark:border-slate-800">
                {{ $expenses->links() }}
            </div>
        </div>
    </div>
</x-layouts.app>
