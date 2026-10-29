<x-layouts.app title="Mutasi Stok Barang">
    <x-slot:header>
        Riwayat Mutasi & Pergerakan Stok
    </x-slot:header>

    @php
        $isFiltered = request()->hasAny(['search', 'type', 'start_date', 'end_date']);
    @endphp

    <div class="space-y-6">
        <!-- ================================================================= -->
        <!-- 1. HEADER HALAMAN ELEGAN & CERAH (LIGHT & AIRY)                   -->
        <!-- ================================================================= -->
        <div class="p-6 sm:p-7 rounded-3xl bg-gradient-to-r from-blue-50/80 via-white to-indigo-50/50 dark:from-slate-850 dark:via-slate-800 dark:to-slate-850 border border-blue-100/80 dark:border-slate-700 shadow-sm">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-5">
                <div class="flex items-start sm:items-center gap-4">
                    <!-- Icon Mutasi Stok Vektor -->
                    <div class="w-13 h-13 rounded-2xl bg-gradient-to-tr from-blue-600 to-indigo-600 text-white flex items-center justify-center shadow-lg shadow-blue-600/25 shrink-0">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2.5 flex-wrap">
                            <h2 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight">
                                Mutasi & Log Pergerakan Stok
                            </h2>
                            <span class="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full bg-blue-100/80 dark:bg-blue-950/60 border border-blue-200 dark:border-blue-800 text-blue-800 dark:text-blue-300 text-xs font-bold shadow-2xs">
                                <span class="w-2 h-2 rounded-full bg-blue-500 animate-pulse"></span>
                                <span>{{ $mutations->total() }} Log Mutasi</span>
                            </span>
                        </div>
                        <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">
                            Pantau riwayat pergerakan stok: barang masuk (kulakan), keluar (penjualan kasir), dan penyesuaian fisik (stock opname).
                        </p>
                    </div>
                </div>

                <!-- Tombol Aksi Cepat -->
                <div class="flex items-center gap-2.5 flex-wrap self-start md:self-auto">
                    <x-button 
                        href="{{ route('products.index') }}" 
                        variant="secondary" 
                        size="sm" 
                        class="bg-white hover:bg-slate-50 text-slate-700 dark:bg-slate-800 dark:text-slate-200 border-slate-200 dark:border-slate-700 font-bold text-xs shadow-2xs transition-all"
                    >
                        <svg class="w-4 h-4 mr-1.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                        </svg>
                        <span>Katalog Produk</span>
                    </x-button>

                    <a 
                        href="{{ route('stocks.create') }}" 
                        class="inline-flex items-center gap-2 px-4.5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs uppercase tracking-wider transition-all shadow-md shadow-blue-600/25 cursor-pointer hover:shadow-lg"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span>Penyesuaian / Opname Stok</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- ================================================================= -->
        <!-- 2. EMPAT KARTU METRIK STATISTIK STOK (CERAH & ELEGAN)             -->
        <!-- ================================================================= -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
            <!-- 1. Total Barang Masuk (Emerald Zamrud) -->
            <div class="p-5 rounded-3xl bg-gradient-to-br from-emerald-50/90 via-white to-teal-50/40 dark:from-emerald-950/40 dark:via-slate-850 dark:to-slate-850 border border-emerald-200/80 dark:border-emerald-900/50 shadow-xs hover:shadow-md transition-all">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-emerald-900 dark:text-emerald-300 uppercase tracking-wider">
                        Barang Masuk (In)
                    </span>
                    <!-- Icon Vektor Masuk -->
                    <div class="w-10 h-10 rounded-2xl bg-emerald-600 text-white flex items-center justify-center shadow-md shadow-emerald-600/25">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                        </svg>
                    </div>
                </div>

                <div class="mt-3">
                    <div class="text-2xl font-black font-mono tracking-tight text-emerald-600 dark:text-emerald-400">
                        +{{ number_format($totalIn) }}
                    </div>
                    <div class="mt-2 flex items-center justify-between text-xs text-slate-500 dark:text-slate-400">
                        <span>Restock & kulakan</span>
                        <span class="font-bold font-mono px-2 py-0.5 rounded-full bg-emerald-100/80 dark:bg-emerald-950/80 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300">
                            Unit Masuk
                        </span>
                    </div>
                </div>
            </div>

            <!-- 2. Total Barang Keluar (Rose Zamrud) -->
            <div class="p-5 rounded-3xl bg-gradient-to-br from-rose-50/90 via-white to-pink-50/40 dark:from-rose-950/40 dark:via-slate-850 dark:to-slate-850 border border-rose-200/80 dark:border-rose-900/50 shadow-xs hover:shadow-md transition-all">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-rose-900 dark:text-rose-300 uppercase tracking-wider">
                        Barang Keluar (Out)
                    </span>
                    <!-- Icon Vektor Keluar -->
                    <div class="w-10 h-10 rounded-2xl bg-rose-600 text-white flex items-center justify-center shadow-md shadow-rose-600/25">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                        </svg>
                    </div>
                </div>

                <div class="mt-3">
                    <div class="text-2xl font-black font-mono tracking-tight text-rose-600 dark:text-rose-400">
                        -{{ number_format($totalOut) }}
                    </div>
                    <div class="mt-2 flex items-center justify-between text-xs text-slate-500 dark:text-slate-400">
                        <span>Penjualan & kasir</span>
                        <span class="font-bold font-mono px-2 py-0.5 rounded-full bg-rose-100/80 dark:bg-rose-950/80 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-300">
                            Unit Keluar
                        </span>
                    </div>
                </div>
            </div>

            <!-- 3. Penyesuaian / Opname (Amber Zamrud) -->
            <div class="p-5 rounded-3xl bg-gradient-to-br from-amber-50/70 via-white to-orange-50/30 dark:from-slate-850 dark:via-slate-850 dark:to-slate-850 border border-amber-200/70 dark:border-slate-700 shadow-xs hover:shadow-md transition-all">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-amber-900 dark:text-amber-300 uppercase tracking-wider">
                        Audit & Opname
                    </span>
                    <!-- Icon Vektor Audit/Sliders -->
                    <div class="w-10 h-10 rounded-2xl bg-amber-500 text-white flex items-center justify-center shadow-md shadow-amber-500/25">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                        </svg>
                    </div>
                </div>

                <div class="mt-3">
                    <div class="text-2xl font-black font-mono tracking-tight text-amber-600 dark:text-amber-400">
                        {{ number_format($totalAdjustment) }}
                    </div>
                    <div class="mt-2 flex items-center justify-between text-xs text-slate-500 dark:text-slate-400">
                        <span>Penyesuaian manual</span>
                        <span class="font-bold font-mono px-2 py-0.5 rounded-full bg-amber-100/80 dark:bg-amber-950/80 border border-amber-200 dark:border-amber-800 text-amber-800 dark:text-amber-300">
                            Aktivitas Opname
                        </span>
                    </div>
                </div>
            </div>

            <!-- 4. Mutasi Hari Ini (Indigo Lembut) -->
            <div class="p-5 rounded-3xl bg-gradient-to-br from-indigo-50/90 via-white to-sky-50/40 dark:from-indigo-950/40 dark:via-slate-850 dark:to-slate-850 border border-indigo-200/80 dark:border-indigo-900/50 shadow-xs hover:shadow-md transition-all">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-indigo-900 dark:text-indigo-300 uppercase tracking-wider">
                        Aktivitas Hari Ini
                    </span>
                    <!-- Icon Vektor Jam/Waktu -->
                    <div class="w-10 h-10 rounded-2xl bg-indigo-600 text-white flex items-center justify-center shadow-md shadow-indigo-600/25">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>

                <div class="mt-3">
                    <div class="text-2xl font-black font-mono tracking-tight text-slate-900 dark:text-white">
                        {{ number_format($todayCount) }}
                    </div>
                    <div class="mt-2 flex items-center justify-between text-xs text-slate-500 dark:text-slate-400">
                        <span>Pergerakan stok</span>
                        <span class="font-bold font-mono px-2 py-0.5 rounded-full bg-indigo-100/80 dark:bg-indigo-950/80 border border-indigo-200 dark:border-indigo-800 text-indigo-800 dark:text-indigo-300">
                            Hari Ini
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- ================================================================= -->
        <!-- 3. TOOLBAR FILTER & PENCARIAN TERANG (LIGHT THEMED)               -->
        <!-- ================================================================= -->
        <div class="p-5 sm:p-6 rounded-3xl bg-white dark:bg-slate-850 border border-slate-200/90 dark:border-slate-800 shadow-xs space-y-4">
            <!-- Preset Tipe Mutasi Cepat (Tabs Cerah) -->
            <div class="flex items-center justify-between gap-3 overflow-x-auto pb-1 scrollbar-thin text-xs">
                <div class="flex items-center gap-1.5 font-semibold">
                    <span class="text-slate-400 text-[11px] uppercase tracking-wider font-bold mr-1 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                        </svg>
                        <span>Tipe Mutasi:</span>
                    </span>
                    
                    <!-- Semua Tipe -->
                    <a
                        href="{{ route('stocks.index', array_merge(request()->except(['type', 'page']))) }}"
                        class="px-3.5 py-1.5 rounded-xl transition-all cursor-pointer {{ !request('type') ? 'bg-blue-600 text-white font-bold shadow-xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200' }}"
                    >
                        Semua Mutasi
                    </a>

                    <!-- Masuk (Restock) -->
                    <a
                        href="{{ route('stocks.index', array_merge(request()->except(['page']), ['type' => 'in'])) }}"
                        class="px-3.5 py-1.5 rounded-xl transition-all cursor-pointer flex items-center gap-1.5 {{ request('type') === 'in' ? 'bg-emerald-600 text-white font-bold shadow-xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200' }}"
                    >
                        <span class="w-1.5 h-1.5 rounded-full {{ request('type') === 'in' ? 'bg-white' : 'bg-emerald-500' }}"></span>
                        <span>Stok Masuk (+)</span>
                    </a>

                    <!-- Keluar (Terjual) -->
                    <a
                        href="{{ route('stocks.index', array_merge(request()->except(['page']), ['type' => 'out'])) }}"
                        class="px-3.5 py-1.5 rounded-xl transition-all cursor-pointer flex items-center gap-1.5 {{ request('type') === 'out' ? 'bg-rose-600 text-white font-bold shadow-xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200' }}"
                    >
                        <span class="w-1.5 h-1.5 rounded-full {{ request('type') === 'out' ? 'bg-white' : 'bg-rose-500' }}"></span>
                        <span>Stok Keluar (-)</span>
                    </a>

                    <!-- Penyesuaian (Opname) -->
                    <a
                        href="{{ route('stocks.index', array_merge(request()->except(['page']), ['type' => 'adjustment'])) }}"
                        class="px-3.5 py-1.5 rounded-xl transition-all cursor-pointer flex items-center gap-1.5 {{ request('type') === 'adjustment' ? 'bg-amber-600 text-white font-bold shadow-xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200' }}"
                    >
                        <span class="w-1.5 h-1.5 rounded-full {{ request('type') === 'adjustment' ? 'bg-white' : 'bg-amber-500' }}"></span>
                        <span>Penyesuaian Opname (=)</span>
                    </a>
                </div>

                @if($isFiltered)
                    <a 
                        href="{{ route('stocks.index') }}" 
                        class="text-xs text-rose-600 hover:text-rose-700 font-bold flex items-center gap-1 shrink-0 px-2.5 py-1 rounded-xl bg-rose-50 dark:bg-rose-950/50 border border-rose-200 dark:border-rose-800"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        <span>Reset Filter</span>
                    </a>
                @endif
            </div>

            <!-- Form Filter Input Fields -->
            <form method="GET" action="{{ route('stocks.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3.5 pt-3 border-t border-slate-100 dark:border-slate-800">
                <!-- Pencarian (5 Col) -->
                <div class="sm:col-span-2 lg:col-span-5">
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Cari Produk</label>
                    <div class="relative">
                        <input
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Ketik nama produk atau kode barang..."
                            class="w-full pl-9 pr-3 py-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-slate-800 focus:bg-white text-slate-900 dark:text-white placeholder-slate-400 text-xs font-medium focus:ring-2 focus:ring-blue-500 shadow-2xs"
                        >
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400 pointer-events-none">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </span>
                    </div>
                </div>

                <!-- Dari Tanggal (2 Col) -->
                <div class="lg:col-span-2">
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Dari Tanggal</label>
                    <input
                        type="date"
                        name="start_date"
                        value="{{ request('start_date') }}"
                        class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-slate-800 focus:bg-white text-slate-900 dark:text-white text-xs font-medium focus:ring-2 focus:ring-blue-500 shadow-2xs"
                    >
                </div>

                <!-- Sampai Tanggal & Tombol Cari (3 Col) -->
                <div class="lg:col-span-3">
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Sampai Tanggal</label>
                    <input
                        type="date"
                        name="end_date"
                        value="{{ request('end_date') }}"
                        class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-slate-800 focus:bg-white text-slate-900 dark:text-white text-xs font-medium focus:ring-2 focus:ring-blue-500 shadow-2xs"
                    >
                </div>

                <!-- Tombol Submit (2 Col) -->
                <div class="sm:col-span-2 lg:col-span-2 flex items-end">
                    <button
                        type="submit"
                        class="w-full py-2 px-4 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs uppercase tracking-wider transition-colors shadow-xs cursor-pointer flex items-center justify-center gap-1.5"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <span>Filter</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- ================================================================= -->
        <!-- 4. TABEL RIWAYAT LOG MUTASI STOK BERSIH (LIGHT THEME)             -->
        <!-- ================================================================= -->
        <div class="rounded-3xl bg-white dark:bg-slate-850 border border-slate-200/90 dark:border-slate-800 shadow-xs overflow-hidden">
            <!-- Header Kartu Tabel -->
            <div class="p-5 sm:p-6 border-b border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 bg-slate-50/60 dark:bg-slate-800/40">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-blue-100 text-blue-600 dark:bg-blue-950/60 dark:text-blue-400 flex items-center justify-center font-bold text-sm shadow-2xs">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-black uppercase tracking-wider text-slate-900 dark:text-white">
                            Daftar Catatan Pergerakan Stok
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Riwayat audit fisik, transaksi kasir, dan penambahan inventaris toko</p>
                    </div>
                </div>
                
                <div class="text-xs text-slate-500 dark:text-slate-400 font-mono self-start sm:self-auto">
                    Menampilkan <strong class="text-slate-900 dark:text-white font-bold">{{ $mutations->firstItem() ?? 0 }}-{{ $mutations->lastItem() ?? 0 }}</strong> dari <strong class="text-slate-900 dark:text-white font-bold">{{ $mutations->total() }}</strong> catatan
                </div>
            </div>

            <!-- Tampilan Desktop: Tabel Lengkap -->
            <div class="hidden md:block overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200/80 dark:divide-slate-800 text-left text-xs">
                    <!-- Head Tabel Terang -->
                    <thead class="bg-slate-50/90 dark:bg-slate-800/90 text-slate-500 dark:text-slate-400 uppercase font-black tracking-wider text-[11px] border-b border-slate-200 dark:border-slate-800">
                        <tr>
                            <th class="px-6 py-4">Waktu & Tanggal</th>
                            <th class="px-5 py-4">Item Produk</th>
                            <th class="px-4 py-4 text-center">Tipe Mutasi</th>
                            <th class="px-4 py-4 text-center">Perubahan Qty</th>
                            <th class="px-5 py-4 text-center">Transisi Stok Fisik</th>
                            <th class="px-5 py-4">Keterangan / Alasan</th>
                            <th class="px-5 py-4">Operator</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80 font-medium">
                        @forelse($mutations as $m)
                            <tr class="hover:bg-blue-50/30 dark:hover:bg-slate-800/50 transition-colors group">
                                <!-- 1. Waktu & Tanggal -->
                                <td class="px-6 py-4.5 whitespace-nowrap">
                                    <div class="flex items-center gap-1.5 text-slate-700 dark:text-slate-300 font-medium">
                                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        <span>@tanggalWaktu($m->created_at)</span>
                                    </div>
                                    <span class="text-[10px] text-slate-400 block mt-0.5 font-mono">
                                        {{ $m->created_at->diffForHumans() }}
                                    </span>
                                </td>

                                <!-- 2. Item Produk -->
                                <td class="px-5 py-4.5">
                                    <div class="font-bold text-slate-900 dark:text-white text-xs leading-snug">
                                        {{ $m->product->name ?? 'Produk Telah Dihapus' }}
                                    </div>
                                    <div class="flex items-center gap-1.5 mt-1">
                                        <span class="font-mono text-[10px] px-1.5 py-0.2 rounded bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-700">
                                            {{ $m->product->code ?? '-' }}
                                        </span>
                                        @if(isset($m->product->category))
                                            <span class="text-[10px] text-slate-400">&bull; {{ $m->product->category->name }}</span>
                                        @endif
                                    </div>
                                </td>

                                <!-- 3. Tipe Mutasi -->
                                <td class="px-4 py-4.5 text-center whitespace-nowrap">
                                    @if($m->type === 'in')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl text-[11px] font-bold bg-emerald-50 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/70 shadow-2xs">
                                            <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                                            <span>Masuk (Restock)</span>
                                        </span>
                                    @elseif($m->type === 'out')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl text-[11px] font-bold bg-rose-50 text-rose-800 dark:bg-rose-950/60 dark:text-rose-300 border border-rose-200 dark:border-rose-800/70 shadow-2xs">
                                            <svg class="w-3.5 h-3.5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                                            <span>Keluar (Terjual)</span>
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl text-[11px] font-bold bg-amber-50 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300 border border-amber-200 dark:border-amber-800/70 shadow-2xs">
                                            <svg class="w-3.5 h-3.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                                            <span>Penyesuaian (Opname)</span>
                                        </span>
                                    @endif
                                </td>

                                <!-- 4. Perubahan Qty -->
                                <td class="px-4 py-4.5 text-center font-mono font-black text-sm whitespace-nowrap">
                                    @if($m->type === 'in')
                                        <span class="text-emerald-600 dark:text-emerald-400">
                                            +{{ $m->quantity }} <span class="text-xs font-normal text-slate-400">{{ $m->product->unit ?? 'pcs' }}</span>
                                        </span>
                                    @elseif($m->type === 'out')
                                        <span class="text-rose-600 dark:text-rose-400">
                                            -{{ $m->quantity }} <span class="text-xs font-normal text-slate-400">{{ $m->product->unit ?? 'pcs' }}</span>
                                        </span>
                                    @else
                                        <span class="text-amber-600 dark:text-amber-400">
                                            &plusmn;{{ $m->quantity }} <span class="text-xs font-normal text-slate-400">{{ $m->product->unit ?? 'pcs' }}</span>
                                        </span>
                                    @endif
                                </td>

                                <!-- 5. Transisi Stok -->
                                <td class="px-5 py-4.5 text-center whitespace-nowrap">
                                    <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs font-mono font-bold shadow-2xs">
                                        <span class="text-slate-500">{{ $m->stock_before }}</span>
                                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                        <span class="text-slate-900 dark:text-white font-black">{{ $m->stock_after }}</span>
                                    </div>
                                </td>

                                <!-- 6. Keterangan / Alasan -->
                                <td class="px-5 py-4.5">
                                    <div class="text-xs text-slate-700 dark:text-slate-300 max-w-xs break-words">
                                        {{ $m->notes ?? '-' }}
                                    </div>
                                    @if($m->reference_type)
                                        <span class="text-[10px] text-slate-400 font-mono block mt-0.5">
                                            Ref: {{ class_basename($m->reference_type) }} #{{ $m->reference_id }}
                                        </span>
                                    @endif
                                </td>

                                <!-- 7. Operator -->
                                <td class="px-5 py-4.5 whitespace-nowrap text-slate-600 dark:text-slate-400">
                                    <div class="flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                        <span class="text-xs">{{ $m->user->name ?? 'Sistem' }}</span>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-14">
                                    <x-empty-state
                                        title="Belum Ada Riwayat Mutasi"
                                        description="Tidak ditemukan riwayat mutasi pada kriteria ini. Mutasi akan tercatat otomatis saat kasir melakukan transaksi penjualan atau saat Anda melakukan penyesuaian opname stok."
                                    >
                                        <x-slot:action>
                                            <x-button href="{{ route('stocks.create') }}" variant="primary" size="sm" class="font-bold">
                                                + Penyesuaian / Opname Stok
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
                @forelse($mutations as $m)
                    <div class="p-4.5 space-y-3 hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors">
                        <div class="flex items-center justify-between">
                            <span class="font-mono text-xs text-slate-500 font-bold bg-slate-100 dark:bg-slate-800 px-2 py-0.5 rounded-lg border border-slate-200 dark:border-slate-700">
                                {{ $m->product->code ?? '-' }}
                            </span>
                            <div class="flex items-center gap-1 text-[11px] text-slate-400 font-medium">
                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span>@tanggalWaktu($m->created_at)</span>
                            </div>
                        </div>

                        <div>
                            <h4 class="font-bold text-slate-900 dark:text-white text-xs leading-snug">
                                {{ $m->product->name ?? 'Produk Dihapus' }}
                            </h4>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1">
                                {{ $m->notes ?? 'Tanpa keterangan' }}
                            </p>
                        </div>

                        <div class="flex items-center justify-between text-xs pt-2 border-t border-slate-100 dark:border-slate-800">
                            <div>
                                <span class="text-[10px] text-slate-400 uppercase tracking-wider block font-bold">Tipe:</span>
                                @if($m->type === 'in')
                                    <span class="text-emerald-600 font-bold">Masuk (+)</span>
                                @elseif($m->type === 'out')
                                    <span class="text-rose-600 font-bold">Keluar (-)</span>
                                @else
                                    <span class="text-amber-600 font-bold">Opname (=)</span>
                                @endif
                            </div>
                            <div class="text-right">
                                <span class="text-[10px] text-slate-400 uppercase tracking-wider block font-bold">Perubahan:</span>
                                <span class="font-mono font-black text-xs {{ $m->type === 'in' ? 'text-emerald-600' : ($m->type === 'out' ? 'text-rose-600' : 'text-amber-600') }}">
                                    {{ $m->type === 'in' ? '+' : ($m->type === 'out' ? '-' : '') }}{{ $m->quantity }} {{ $m->product->unit ?? 'pcs' }}
                                </span>
                            </div>
                        </div>

                        <div class="flex items-center justify-between pt-2">
                            <div class="flex items-center gap-1.5 font-mono text-xs font-bold text-slate-700 dark:text-slate-300">
                                <span>{{ $m->stock_before }}</span>
                                <span>&rarr;</span>
                                <span class="text-blue-600 dark:text-blue-400 font-black">{{ $m->stock_after }}</span>
                            </div>

                            <span class="text-[11px] text-slate-400">
                                {{ $m->user->name ?? 'Sistem' }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="p-6 text-center text-xs text-slate-400">
                        Tidak ada riwayat mutasi stok ditemukan.
                    </div>
                @endforelse
            </div>

            <!-- Pagination Bar -->
            <div class="p-5 sm:p-6 bg-slate-50/50 dark:bg-slate-800/20 border-t border-slate-100 dark:border-slate-800">
                {{ $mutations->links() }}
            </div>
        </div>
    </div>
</x-layouts.app>
