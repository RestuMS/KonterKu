<x-layouts.app title="Pusat Laporan Keuangan & Stok">
    <x-slot:header>
        Pusat Laporan KonterKu
    </x-slot:header>

    @php
        $todayStr = now()->toDateString();
        $weekAgoStr = now()->subDays(6)->toDateString();
        $monthStartStr = now()->startOfMonth()->toDateString();
        $yearStartStr = now()->startOfYear()->toDateString();

        $isFiltered = request()->hasAny(['search', 'category_id', 'payment_method', 'stock_status', 'mutation_type']) || 
                      (request('start_date') && request('start_date') !== $monthStartStr) || 
                      (request('end_date') && request('end_date') !== $todayStr);
    @endphp

    <div class="space-y-6">
        <!-- ================================================================= -->
        <!-- 1. HEADER PUSAT LAPORAN ELEGAN & CERAH                            -->
        <!-- ================================================================= -->
        <div class="p-6 sm:p-7 rounded-3xl bg-gradient-to-r from-indigo-50/90 via-white to-sky-50/60 dark:from-slate-850 dark:via-slate-800 dark:to-slate-850 border border-indigo-100/80 dark:border-slate-700 shadow-sm">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-5">
                <div class="flex items-start sm:items-center gap-4">
                    <div class="w-13 h-13 rounded-2xl bg-gradient-to-tr from-indigo-600 to-indigo-500 text-white flex items-center justify-center shadow-lg shadow-indigo-600/25 shrink-0">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2.5 flex-wrap">
                            <h2 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight">
                                Pusat Laporan & Pembukuan Konter
                            </h2>
                            <span class="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full bg-indigo-100/80 dark:bg-indigo-950/60 border border-indigo-200 dark:border-indigo-800 text-indigo-800 dark:text-indigo-300 text-xs font-bold shadow-2xs">
                                <span class="w-2 h-2 rounded-full bg-indigo-500 animate-pulse"></span>
                                <span>Periode: @tanggal($startDate) &mdash; @tanggal($endDate)</span>
                            </span>
                        </div>
                        <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">
                            Laporan arus kas, laba keuntungan usaha, pemasukan kasir, pengeluaran toko, valuasi stok, dan riwayat mutasi barang.
                        </p>
                    </div>
                </div>

                <!-- Tombol Ekspor & Cetak -->
                <div class="flex items-center gap-2.5 flex-wrap self-start md:self-auto">
                    <!-- Tombol Ekspor CSV -->
                    <a
                        href="{{ route('reports.export', array_merge(request()->all(), ['tab' => $tab, 'start_date' => $startDate, 'end_date' => $endDate])) }}"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2.5 rounded-xl bg-white hover:bg-slate-50 text-slate-700 dark:bg-slate-800 dark:text-slate-200 border border-slate-200 dark:border-slate-700 font-bold text-xs shadow-2xs transition-all cursor-pointer"
                        title="Unduh laporan dalam format CSV (bisa dibuka di Excel / Google Sheets)"
                    >
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span>Ekspor CSV</span>
                    </a>

                    <!-- Tombol Cetak / PDF -->
                    <a
                        href="{{ route('reports.print', array_merge(request()->all(), ['tab' => $tab, 'start_date' => $startDate, 'end_date' => $endDate])) }}"
                        target="_blank"
                        class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs uppercase tracking-wider transition-all shadow-md shadow-indigo-600/25 cursor-pointer hover:shadow-lg"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                        </svg>
                        <span>Cetak / PDF</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- ================================================================= -->
        <!-- 2. TAB NAVIGASI ENAM LAPORAN UTAMA                                -->
        <!-- ================================================================= -->
        <div class="flex items-center gap-2 overflow-x-auto pb-1 scrollbar-thin text-xs font-bold border-b border-slate-200 dark:border-slate-800">
            <!-- 1. Ringkasan Arus Kas -->
            <a
                href="{{ route('reports.index', array_merge(request()->except(['page']), ['tab' => 'ringkasan'])) }}"
                class="px-4 py-2.5 rounded-2xl flex items-center gap-2 transition-all shrink-0 {{ $tab === 'ringkasan' ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/25' : 'bg-white dark:bg-slate-850 text-slate-600 dark:text-slate-300 hover:bg-slate-100 border border-slate-200 dark:border-slate-800' }}"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                <span>Ringkasan Arus Kas</span>
            </a>

            <!-- 2. Laba Keuntungan -->
            <a
                href="{{ route('reports.index', array_merge(request()->except(['page']), ['tab' => 'laba_rugi'])) }}"
                class="px-4 py-2.5 rounded-2xl flex items-center gap-2 transition-all shrink-0 {{ $tab === 'laba_rugi' ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/25' : 'bg-white dark:bg-slate-850 text-slate-600 dark:text-slate-300 hover:bg-slate-100 border border-slate-200 dark:border-slate-800' }}"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Laba Keuntungan</span>
            </a>

            <!-- 3. Laporan Pemasukan -->
            <a
                href="{{ route('reports.index', array_merge(request()->except(['page']), ['tab' => 'pemasukan'])) }}"
                class="px-4 py-2.5 rounded-2xl flex items-center gap-2 transition-all shrink-0 {{ $tab === 'pemasukan' ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/25' : 'bg-white dark:bg-slate-850 text-slate-600 dark:text-slate-300 hover:bg-slate-100 border border-slate-200 dark:border-slate-800' }}"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/></svg>
                <span>Laporan Pemasukan</span>
            </a>

            <!-- 4. Laporan Pengeluaran -->
            <a
                href="{{ route('reports.index', array_merge(request()->except(['page']), ['tab' => 'pengeluaran'])) }}"
                class="px-4 py-2.5 rounded-2xl flex items-center gap-2 transition-all shrink-0 {{ $tab === 'pengeluaran' ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/25' : 'bg-white dark:bg-slate-850 text-slate-600 dark:text-slate-300 hover:bg-slate-100 border border-slate-200 dark:border-slate-800' }}"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"/></svg>
                <span>Laporan Pengeluaran</span>
            </a>

            <!-- 5. Produk & Stok -->
            <a
                href="{{ route('reports.index', array_merge(request()->except(['page']), ['tab' => 'produk_stok'])) }}"
                class="px-4 py-2.5 rounded-2xl flex items-center gap-2 transition-all shrink-0 {{ $tab === 'produk_stok' ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/25' : 'bg-white dark:bg-slate-850 text-slate-600 dark:text-slate-300 hover:bg-slate-100 border border-slate-200 dark:border-slate-800' }}"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                <span>Produk & Valuasi Stok</span>
            </a>

            <!-- 6. Histori Mutasi Stok -->
            <a
                href="{{ route('reports.index', array_merge(request()->except(['page']), ['tab' => 'mutasi_stok'])) }}"
                class="px-4 py-2.5 rounded-2xl flex items-center gap-2 transition-all shrink-0 {{ $tab === 'mutasi_stok' ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/25' : 'bg-white dark:bg-slate-850 text-slate-600 dark:text-slate-300 hover:bg-slate-100 border border-slate-200 dark:border-slate-800' }}"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                <span>Histori Pergerakan Stok</span>
            </a>

            <!-- 7. Buku Piutang Kasbon -->
            <a
                href="{{ route('reports.index', array_merge(request()->except(['page']), ['tab' => 'kasbon'])) }}"
                class="px-4 py-2.5 rounded-2xl flex items-center gap-2 transition-all shrink-0 {{ $tab === 'kasbon' ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/25' : 'bg-white dark:bg-slate-850 text-slate-600 dark:text-slate-300 hover:bg-slate-100 border border-slate-200 dark:border-slate-800' }}"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                <span>Buku Piutang Kasbon</span>
            </a>
        </div>

        <!-- ================================================================= -->
        <!-- 3. TOOLBAR FILTER & PENCARIAN TERPADU                             -->
        <!-- ================================================================= -->
        <div class="p-5 sm:p-6 rounded-3xl bg-white dark:bg-slate-850 border border-slate-200/90 dark:border-slate-800 shadow-xs space-y-4">
            <!-- Preset Periode Waktu Cepat -->
            <div class="flex items-center justify-between gap-3 overflow-x-auto pb-1 scrollbar-thin text-xs">
                <div class="flex items-center gap-1.5 font-semibold">
                    <span class="text-slate-400 text-[11px] uppercase tracking-wider font-bold mr-1 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <span>Periode Cepat:</span>
                    </span>

                    <!-- Hari Ini -->
                    <a
                        href="{{ route('reports.index', array_merge(request()->except(['page']), ['start_date' => $todayStr, 'end_date' => $todayStr])) }}"
                        class="px-3 py-1.5 rounded-xl transition-all cursor-pointer {{ $startDate === $todayStr && $endDate === $todayStr ? 'bg-indigo-600 text-white font-bold shadow-xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200' }}"
                    >
                        Hari Ini
                    </a>

                    <!-- 7 Hari Terakhir -->
                    <a
                        href="{{ route('reports.index', array_merge(request()->except(['page']), ['start_date' => $weekAgoStr, 'end_date' => $todayStr])) }}"
                        class="px-3 py-1.5 rounded-xl transition-all cursor-pointer {{ $startDate === $weekAgoStr && $endDate === $todayStr ? 'bg-indigo-600 text-white font-bold shadow-xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200' }}"
                    >
                        7 Hari Terakhir
                    </a>

                    <!-- Bulan Ini -->
                    <a
                        href="{{ route('reports.index', array_merge(request()->except(['page']), ['start_date' => $monthStartStr, 'end_date' => $todayStr])) }}"
                        class="px-3 py-1.5 rounded-xl transition-all cursor-pointer {{ $startDate === $monthStartStr && $endDate === $todayStr ? 'bg-indigo-600 text-white font-bold shadow-xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200' }}"
                    >
                        Bulan Ini
                    </a>

                    <!-- Tahun Ini -->
                    <a
                        href="{{ route('reports.index', array_merge(request()->except(['page']), ['start_date' => $yearStartStr, 'end_date' => $todayStr])) }}"
                        class="px-3 py-1.5 rounded-xl transition-all cursor-pointer {{ $startDate === $yearStartStr && $endDate === $todayStr ? 'bg-indigo-600 text-white font-bold shadow-xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200' }}"
                    >
                        Tahun Ini
                    </a>
                </div>

                @if($isFiltered)
                    <a 
                        href="{{ route('reports.index', ['tab' => $tab]) }}" 
                        class="text-xs text-rose-600 hover:text-rose-700 font-bold flex items-center gap-1 shrink-0 px-2.5 py-1 rounded-xl bg-rose-50 dark:bg-rose-950/50 border border-rose-200 dark:border-rose-800"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        <span>Reset Filter</span>
                    </a>
                @endif
            </div>

            <!-- Form Filter Input Sesuai Tab Aktif -->
            <form method="GET" action="{{ route('reports.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3.5 pt-3 border-t border-slate-100 dark:border-slate-800">
                <input type="hidden" name="tab" value="{{ $tab }}">

                <!-- Rentang Tanggal (Kecuali Tab Produk Stok) -->
                @if($tab !== 'produk_stok')
                    <div class="lg:col-span-3">
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Dari Tanggal</label>
                        <input
                            type="date"
                            name="start_date"
                            value="{{ $startDate }}"
                            class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-xs font-medium focus:ring-2 focus:ring-indigo-500 shadow-2xs"
                        >
                    </div>

                    <div class="lg:col-span-3">
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Sampai Tanggal</label>
                        <input
                            type="date"
                            name="end_date"
                            value="{{ $endDate }}"
                            class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-xs font-medium focus:ring-2 focus:ring-indigo-500 shadow-2xs"
                        >
                    </div>
                @endif

                <!-- Pencarian Kata Kunci (Untuk Tab Transaksi, Stok, & Kasbon) -->
                @if(in_array($tab, ['pemasukan', 'pengeluaran', 'produk_stok', 'mutasi_stok', 'kasbon']))
                    <div class="{{ $tab === 'produk_stok' ? 'lg:col-span-5' : ($tab === 'kasbon' ? 'lg:col-span-4' : 'lg:col-span-3') }}">
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Pencarian</label>
                        <div class="relative">
                            <input
                                type="text"
                                name="search"
                                value="{{ $search }}"
                                placeholder="{{ $tab === 'kasbon' ? 'Cari nama pelanggan, no HP, rincian...' : 'Cari nama, invoice, kode...' }}"
                                class="w-full pl-8 pr-3 py-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-xs font-medium focus:ring-2 focus:ring-indigo-500 shadow-2xs"
                            >
                            <span class="absolute inset-y-0 left-0 pl-2.5 flex items-center text-slate-400 pointer-events-none">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </span>
                        </div>
                    </div>
                @endif

                <!-- Filter Status Kasbon -->
                @if($tab === 'kasbon')
                    <div class="lg:col-span-3">
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Status Piutang</label>
                        <select
                            name="kasbon_status"
                            class="block w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-xs font-medium py-2 px-3 focus:ring-2 focus:ring-indigo-500 shadow-2xs"
                        >
                            <option value="all" {{ $kasbonStatus === 'all' ? 'selected' : '' }}>Semua Status</option>
                            <option value="unpaid" {{ $kasbonStatus === 'unpaid' ? 'selected' : '' }}>Belum Lunas (Piutang Aktif)</option>
                            <option value="paid" {{ $kasbonStatus === 'paid' ? 'selected' : '' }}>Sudah Lunas</option>
                        </select>
                    </div>
                @endif

                <!-- Filter Kategori (Untuk Pemasukan, Pengeluaran, Produk) -->
                @if(in_array($tab, ['pemasukan', 'pengeluaran', 'produk_stok']))
                    <div class="{{ $tab === 'produk_stok' ? 'lg:col-span-4' : 'lg:col-span-3' }}">
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Kategori</label>
                        <select
                            name="category_id"
                            class="block w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-xs font-medium py-2 px-3 focus:ring-2 focus:ring-indigo-500 shadow-2xs"
                        >
                            <option value="">Semua Kategori</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ $categoryId == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <!-- Tombol Submit Terapkan -->
                <div class="sm:col-span-2 lg:col-span-12 flex justify-end gap-2 pt-1">
                    <button
                        type="submit"
                        class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs uppercase tracking-wider transition-colors shadow-xs cursor-pointer flex items-center gap-1.5"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <span>Terapkan Filter</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- ================================================================= -->
        <!-- 4. KONTEN DETAIL LAPORAN BERDASARKAN TAB AKTIF                    -->
        <!-- ================================================================= -->

        <!-- TAB 1: RINGKASAN ARUS KAS -->
        @if($tab === 'ringkasan')
            <div class="space-y-6">
                <!-- 4 Kartu Metrik Keuangan Utama -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
                    <!-- Total Pemasukan -->
                    <div class="p-5 rounded-3xl bg-gradient-to-br from-indigo-50/90 via-white to-sky-50/40 dark:from-indigo-950/40 dark:via-slate-850 dark:to-slate-850 border border-indigo-200/80 dark:border-indigo-900/50 shadow-xs">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-indigo-900 dark:text-indigo-300 uppercase tracking-wider">Total Pemasukan</span>
                            <div class="w-10 h-10 rounded-2xl bg-indigo-600 text-white flex items-center justify-center shadow-md shadow-indigo-600/25">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M7 11l5-5m0 0l5 5m-5-5v12"/></svg>
                            </div>
                        </div>
                        <div class="mt-3">
                            <div class="text-2xl font-black font-mono tracking-tight text-slate-900 dark:text-white">@rupiah($financialSummary['total_income'])</div>
                            <div class="mt-2 text-xs text-slate-500 dark:text-slate-400 flex items-center justify-between">
                                <span>Omzet kasir & jasa</span>
                                <span class="font-bold font-mono px-2 py-0.5 rounded-full bg-indigo-100 text-indigo-800 text-[11px]">{{ $financialSummary['income_count'] }} Transaksi</span>
                            </div>
                        </div>
                    </div>

                    <!-- Total Modal HPP Penjualan -->
                    <div class="p-5 rounded-3xl bg-gradient-to-br from-amber-50/70 via-white to-orange-50/30 dark:from-slate-850 dark:via-slate-850 dark:to-slate-850 border border-amber-200/70 dark:border-slate-700 shadow-xs">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-amber-900 dark:text-amber-300 uppercase tracking-wider">Modal Kulakan (HPP)</span>
                            <div class="w-10 h-10 rounded-2xl bg-amber-500 text-white flex items-center justify-center shadow-md shadow-amber-500/25">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                            </div>
                        </div>
                        <div class="mt-3">
                            <div class="text-2xl font-black font-mono tracking-tight text-slate-900 dark:text-white">@rupiah($financialSummary['total_cogs'])</div>
                            <div class="mt-2 text-xs text-slate-500 dark:text-slate-400 flex items-center justify-between">
                                <span>Harga pokok pulsa/stok</span>
                                <span class="font-bold font-mono px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 text-[11px]">{{ $financialSummary['cost_coverage_percentage'] }}% Terdata</span>
                            </div>
                        </div>
                    </div>

                    <!-- Total Pengeluaran Kas -->
                    <div class="p-5 rounded-3xl bg-gradient-to-br from-rose-50/90 via-white to-pink-50/40 dark:from-rose-950/40 dark:via-slate-850 dark:to-slate-850 border border-rose-200/80 dark:border-rose-900/50 shadow-xs">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-rose-900 dark:text-rose-300 uppercase tracking-wider">Total Pengeluaran</span>
                            <div class="w-10 h-10 rounded-2xl bg-rose-600 text-white flex items-center justify-center shadow-md shadow-rose-600/25">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M17 13l-5 5m0 0l-5-5m5 5V6"/></svg>
                            </div>
                        </div>
                        <div class="mt-3">
                            <div class="text-2xl font-black font-mono tracking-tight text-rose-600 dark:text-rose-400">- @rupiah($financialSummary['total_expense'])</div>
                            <div class="mt-2 text-xs text-slate-500 dark:text-slate-400 flex items-center justify-between">
                                <span>Jajan, bensin, toko</span>
                                <span class="font-bold font-mono px-2 py-0.5 rounded-full bg-rose-100 text-rose-800 text-[11px]">{{ $financialSummary['expense_count'] }} Catatan</span>
                            </div>
                        </div>
                    </div>

                    <!-- Laba Bersih Toko (Net Profit) -->
                    <div class="p-5 rounded-3xl bg-gradient-to-br from-emerald-50/90 via-white to-teal-50/40 dark:from-emerald-950/40 dark:via-slate-850 dark:to-slate-850 border border-emerald-200/80 dark:border-emerald-900/50 shadow-xs">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-emerald-900 dark:text-emerald-300 uppercase tracking-wider">Laba Bersih Toko</span>
                            <div class="w-10 h-10 rounded-2xl bg-emerald-600 text-white flex items-center justify-center shadow-md shadow-emerald-600/25">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                        </div>
                        <div class="mt-3">
                            <div class="text-2xl font-black font-mono tracking-tight {{ $financialSummary['net_profit'] >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                                {{ $financialSummary['net_profit'] >= 0 ? '+' : '' }}@rupiah($financialSummary['net_profit'])
                            </div>
                            <div class="mt-2 text-xs text-slate-500 dark:text-slate-400 flex items-center justify-between">
                                <span>Omzet &minus; HPP &minus; Beban</span>
                                <span class="font-bold font-mono px-2 py-0.5 rounded-full bg-emerald-100 dark:bg-emerald-950 text-emerald-800 dark:text-emerald-300 text-[11px]">Keuntungan Riil</span>
                            </div>
                            <div class="mt-2 pt-2 border-t border-slate-100 dark:border-slate-800 text-[11px] text-slate-400 flex items-center justify-between">
                                <span>Kas Laci Masuk:</span>
                                <span class="font-mono font-semibold text-slate-600 dark:text-slate-300">@rupiah($financialSummary['net_cashflow'])</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Baris Rincian Metode Pembayaran & Kategori Beban -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Metode Pembayaran Pemasukan vs Pengeluaran -->
                    <div class="p-6 rounded-3xl bg-white dark:bg-slate-850 border border-slate-200/90 dark:border-slate-800 shadow-xs space-y-4">
                        <h3 class="text-xs font-black uppercase tracking-wider text-slate-900 dark:text-white flex items-center gap-2">
                            <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                            <span>Arus Kas per Metode Pembayaran</span>
                        </h3>

                        <div class="space-y-3 text-xs">
                            <!-- Tunai -->
                            <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700/80 flex items-center justify-between">
                                <div class="flex items-center gap-2.5">
                                    <span class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold">💵</span>
                                    <div>
                                        <h5 class="font-bold text-slate-900 dark:text-white">Uang Tunai (Cash)</h5>
                                        <span class="text-[10px] text-slate-400">Kas laci konter</span>
                                    </div>
                                </div>
                                <div class="text-right font-mono">
                                    <div class="text-emerald-600 font-bold">+ @rupiah($financialSummary['payment_methods_income']['cash'] ?? 0)</div>
                                    <div class="text-rose-600 text-[11px]">- @rupiah($financialSummary['payment_methods_expense']['cash'] ?? 0)</div>
                                </div>
                            </div>

                            <!-- QRIS / E-Wallet -->
                            <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700/80 flex items-center justify-between">
                                <div class="flex items-center gap-2.5">
                                    <span class="w-8 h-8 rounded-xl bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold">📲</span>
                                    <div>
                                        <h5 class="font-bold text-slate-900 dark:text-white">QRIS & E-Wallet</h5>
                                        <span class="text-[10px] text-slate-400">DANA, GoPay, OVO, ShopeePay</span>
                                    </div>
                                </div>
                                <div class="text-right font-mono">
                                    <div class="text-emerald-600 font-bold">+ @rupiah($financialSummary['payment_methods_income']['qris'] ?? 0)</div>
                                    <div class="text-rose-600 text-[11px]">- @rupiah($financialSummary['payment_methods_expense']['qris'] ?? 0)</div>
                                </div>
                            </div>

                            <!-- Transfer Bank -->
                            <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700/80 flex items-center justify-between">
                                <div class="flex items-center gap-2.5">
                                    <span class="w-8 h-8 rounded-xl bg-sky-100 text-sky-700 flex items-center justify-center font-bold">🏦</span>
                                    <div>
                                        <h5 class="font-bold text-slate-900 dark:text-white">Transfer Antar Bank</h5>
                                        <span class="text-[10px] text-slate-400">BRI, BCA, BNI, Mandiri, BSI</span>
                                    </div>
                                </div>
                                <div class="text-right font-mono">
                                    <div class="text-emerald-600 font-bold">+ @rupiah($financialSummary['payment_methods_income']['transfer'] ?? 0)</div>
                                    <div class="text-rose-600 text-[11px]">- @rupiah($financialSummary['payment_methods_expense']['transfer'] ?? 0)</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Breakdown Pengeluaran Kas per Kategori -->
                    <div class="p-6 rounded-3xl bg-white dark:bg-slate-850 border border-slate-200/90 dark:border-slate-800 shadow-xs space-y-4">
                        <h3 class="text-xs font-black uppercase tracking-wider text-slate-900 dark:text-white flex items-center gap-2">
                            <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                            <span>Breakdown Pengeluaran Toko</span>
                        </h3>

                        <div class="divide-y divide-slate-100 dark:divide-slate-800">
                            @forelse($financialSummary['expenses_by_category'] as $ebc)
                                <div class="py-2.5 first:pt-0 last:pb-0 flex items-center justify-between text-xs">
                                    <div>
                                        <span class="font-bold text-slate-800 dark:text-slate-200">{{ $ebc->category->name ?? 'Operasional Toko' }}</span>
                                        <span class="text-[10px] text-slate-400 block">{{ $ebc->count }}x transaksi</span>
                                    </div>
                                    <span class="font-mono font-bold text-rose-600">@rupiah($ebc->total)</span>
                                </div>
                            @empty
                                <p class="text-xs text-slate-400 py-3 text-center">Belum ada catatan pengeluaran pada periode ini.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- TAB 2: LAPORAN LABA KEUNTUNGAN (NEW TAILWIND CSS UI/UX) -->
        @if($tab === 'laba_rugi')
            @php
                $totalIncome = (float) ($financialSummary['total_income'] ?? 0);
                $totalCogs = (float) ($financialSummary['total_cogs'] ?? 0);
                $grossProfit = (float) ($financialSummary['gross_profit'] ?? 0);
                $totalExpense = (float) ($financialSummary['total_expense'] ?? 0);
                $netProfit = (float) ($financialSummary['net_profit'] ?? 0);
                $incomeCount = (int) ($financialSummary['income_count'] ?? 0);
                $expenseCount = (int) ($financialSummary['expense_count'] ?? 0);

                // Rasio & Margin Finansial
                $grossMarginPct = $totalIncome > 0 ? round(($grossProfit / $totalIncome) * 100, 1) : 0;
                $cogsPct = $totalIncome > 0 ? min(100, max(0, round(($totalCogs / $totalIncome) * 100, 1))) : 0;
                $expensePct = $totalIncome > 0 ? min(100, max(0, round(($totalExpense / $totalIncome) * 100, 1))) : 0;
                $netProfitPct = $totalIncome > 0 ? round(($netProfit / $totalIncome) * 100, 1) : 0;
                $netProfitBarWidth = max(0, min(100, $netProfitPct));
                $avgProfitPerTx = $incomeCount > 0 ? round($grossProfit / $incomeCount) : 0;
                $avgTicketSize = $incomeCount > 0 ? round($totalIncome / $incomeCount) : 0;
            @endphp

            <div class="space-y-6">
                <!-- 1. NOTIFIKASI TRANSPARANSI AKURASI HPP -->
                <div class="p-4 sm:p-5 rounded-3xl {{ $financialSummary['has_complete_cost_data'] ? 'bg-emerald-50/80 dark:bg-emerald-950/30 border-emerald-200 dark:border-emerald-800 text-emerald-950 dark:text-emerald-200' : 'bg-amber-50/90 dark:bg-amber-950/30 border-amber-200 dark:border-amber-800 text-amber-950 dark:text-amber-200' }} border shadow-2xs">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="flex items-start gap-3">
                            <div class="w-9 h-9 rounded-2xl {{ $financialSummary['has_complete_cost_data'] ? 'bg-emerald-600 text-white shadow-emerald-600/25' : 'bg-amber-500 text-white shadow-amber-500/25' }} flex items-center justify-center shrink-0 shadow-md">
                                @if($financialSummary['has_complete_cost_data'])
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                @else
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                @endif
                            </div>
                            <div class="text-xs space-y-0.5">
                                <h4 class="font-black text-sm">
                                    {{ $financialSummary['has_complete_cost_data'] ? 'Akurasi Laba Keuntungan 100% Presisi' : 'Transparansi Data Modal Kulakan (' . $financialSummary['cost_coverage_percentage'] . '% Terdata)' }}
                                </h4>
                                <p class="text-slate-600 dark:text-slate-300">
                                    @if($financialSummary['has_complete_cost_data'])
                                        Semua item transaksi kasir memiliki catatan modal kulakan (HPP). Laba kotor dan laba keuntungan bersih dihitung secara presisi dari database.
                                    @else
                                        Sebagian transaksi belum memiliki catatan harga modal (Rp 0). Laba keuntungan di bawah merupakan estimasi transparan sesuai modal yang tercatat di database.
                                    @endif
                                </p>
                            </div>
                        </div>

                        <div class="self-start sm:self-auto shrink-0 flex items-center gap-2">
                            <span class="inline-flex items-center px-3 py-1 rounded-xl text-[11px] font-black {{ $financialSummary['has_complete_cost_data'] ? 'bg-emerald-100 dark:bg-emerald-900 text-emerald-800 dark:text-emerald-200' : 'bg-amber-100 dark:bg-amber-900 text-amber-800 dark:text-amber-200' }}">
                                HPP Terdata: {{ $financialSummary['cost_coverage_percentage'] }}%
                            </span>
                        </div>
                    </div>
                </div>

                <!-- 2. HERO HIGHLIGHT: LABA KEUNTUNGAN BERSIH TOKO (NET PROFIT) -->
                <div class="p-6 sm:p-8 rounded-3xl bg-gradient-to-br from-emerald-500/10 via-teal-500/5 to-indigo-500/10 dark:from-emerald-950/40 dark:via-slate-850 dark:to-slate-850 border border-emerald-200/90 dark:border-emerald-800/80 shadow-sm space-y-6">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6 pb-6 border-b border-emerald-100 dark:border-slate-800">
                        <div class="space-y-2">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-100 dark:bg-emerald-950 text-emerald-800 dark:text-emerald-300 font-black text-xs tracking-wider uppercase border border-emerald-200 dark:border-emerald-800">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                                    <span>Laba Keuntungan Bersih Toko (Net Profit)</span>
                                </span>
                                <span class="text-xs text-slate-500 dark:text-slate-400 font-semibold">
                                    Periode: @tanggal($startDate) &mdash; @tanggal($endDate)
                                </span>
                            </div>

                            <div class="flex items-baseline gap-3 flex-wrap">
                                <span class="text-3xl sm:text-4xl lg:text-5xl font-black font-mono tracking-tight {{ $netProfit >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                                    {{ $netProfit >= 0 ? '+' : '' }}@rupiah($netProfit)
                                </span>
                                @if($totalIncome > 0)
                                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-xl text-xs font-bold {{ $netProfit >= 0 ? 'bg-emerald-100/80 text-emerald-800 dark:bg-emerald-900/60 dark:text-emerald-200' : 'bg-rose-100 text-rose-800 dark:bg-rose-900/60 dark:text-rose-200' }}">
                                        {{ $netProfit >= 0 ? '▲' : '▼' }} {{ $netProfitPct }}% dari Omzet
                                    </span>
                                @endif
                            </div>

                            <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-300 max-w-2xl leading-relaxed">
                                Keuntungan bersih riil yang menjadi hak pemilik konter setelah seluruh modal kulakan barang terjual dan biaya pengeluaran operasional toko telah dilunasi.
                            </p>
                        </div>

                        <!-- 3 Kartu Metrik Ringkas Sebelah Kanan -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-1 gap-3 shrink-0 lg:w-72">
                            <div class="p-3.5 rounded-2xl bg-white/80 dark:bg-slate-800/80 border border-emerald-100 dark:border-slate-700 shadow-2xs flex items-center justify-between">
                                <div>
                                    <span class="text-[10px] uppercase tracking-wider font-bold text-slate-400 block">Margin Laba Kotor</span>
                                    <span class="text-sm font-black font-mono text-indigo-600 dark:text-indigo-400">{{ $grossMarginPct }}%</span>
                                </div>
                                <span class="text-[10px] px-2 py-0.5 rounded-lg bg-indigo-50 dark:bg-indigo-950 text-indigo-700 font-bold">Produk</span>
                            </div>

                            <div class="p-3.5 rounded-2xl bg-white/80 dark:bg-slate-800/80 border border-emerald-100 dark:border-slate-700 shadow-2xs flex items-center justify-between">
                                <div>
                                    <span class="text-[10px] uppercase tracking-wider font-bold text-slate-400 block">Rasio Beban Toko</span>
                                    <span class="text-sm font-black font-mono text-rose-600 dark:text-rose-400">{{ $expensePct }}%</span>
                                </div>
                                <span class="text-[10px] px-2 py-0.5 rounded-lg bg-rose-50 dark:bg-rose-950 text-rose-700 font-bold">Operasional</span>
                            </div>

                            <div class="p-3.5 rounded-2xl bg-white/80 dark:bg-slate-800/80 border border-emerald-100 dark:border-slate-700 shadow-2xs flex items-center justify-between">
                                <div>
                                    <span class="text-[10px] uppercase tracking-wider font-bold text-slate-400 block">Rata-rata Laba / Nota</span>
                                    <span class="text-sm font-black font-mono text-emerald-600 dark:text-emerald-400">+@rupiah($avgProfitPerTx)</span>
                                </div>
                                <span class="text-[10px] px-2 py-0.5 rounded-lg bg-emerald-50 dark:bg-emerald-950 text-emerald-700 font-bold">Kasir</span>
                            </div>
                        </div>
                    </div>

                    <!-- 3. WATERFALL FLOW: ALUR PERHITUNGAN LABA KEUNTUNGAN -->
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <h4 class="text-xs font-black uppercase tracking-wider text-slate-900 dark:text-white flex items-center gap-2">
                                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                                <span>Alur Dekomposisi Keuntungan Toko</span>
                            </h4>
                            <span class="text-[11px] text-slate-400 font-medium hidden sm:inline">Dari Total Kasir Menuju Keuntungan Bersih</span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-5 gap-3 relative">
                            <!-- Card 1: Omzet Penjualan -->
                            <div class="p-4 rounded-2xl bg-white dark:bg-slate-800 border border-indigo-100 dark:border-slate-700 shadow-2xs flex flex-col justify-between">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-[11px] font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider">1. Omzet Penjualan</span>
                                    <span class="w-7 h-7 rounded-xl bg-indigo-100 dark:bg-indigo-900/60 text-indigo-600 flex items-center justify-center text-xs font-bold">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                    </span>
                                </div>
                                <div>
                                    <div class="text-base font-black font-mono text-slate-900 dark:text-white">@rupiah($totalIncome)</div>
                                    <span class="text-[10px] text-slate-400">{{ $incomeCount }}x Transaksi Kasir</span>
                                </div>
                            </div>

                            <!-- Card 2: Modal Kulakan HPP -->
                            <div class="p-4 rounded-2xl bg-white dark:bg-slate-800 border border-amber-100 dark:border-slate-700 shadow-2xs flex flex-col justify-between">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-[11px] font-bold text-amber-600 dark:text-amber-400 uppercase tracking-wider">2. Modal HPP (&minus;)</span>
                                    <span class="w-7 h-7 rounded-xl bg-amber-100 dark:bg-amber-900/60 text-amber-600 flex items-center justify-center text-xs font-bold">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                    </span>
                                </div>
                                <div>
                                    <div class="text-base font-black font-mono text-amber-600 dark:text-amber-400">- @rupiah($totalCogs)</div>
                                    <span class="text-[10px] text-slate-400">Modal Stok Terjual</span>
                                </div>
                            </div>

                            <!-- Card 3: Laba Kotor Penjualan -->
                            <div class="p-4 rounded-2xl bg-white dark:bg-slate-800 border border-sky-100 dark:border-slate-700 shadow-2xs flex flex-col justify-between">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-[11px] font-bold text-sky-600 dark:text-sky-400 uppercase tracking-wider">3. Laba Kotor (=)</span>
                                    <span class="w-7 h-7 rounded-xl bg-sky-100 dark:bg-sky-900/60 text-sky-600 flex items-center justify-center text-xs font-bold">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                                    </span>
                                </div>
                                <div>
                                    <div class="text-base font-black font-mono text-sky-600 dark:text-sky-400">@rupiah($grossProfit)</div>
                                    <span class="text-[10px] text-slate-400">Margin: {{ $grossMarginPct }}%</span>
                                </div>
                            </div>

                            <!-- Card 4: Beban Pengeluaran Toko -->
                            <div class="p-4 rounded-2xl bg-white dark:bg-slate-800 border border-rose-100 dark:border-slate-700 shadow-2xs flex flex-col justify-between">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-[11px] font-bold text-rose-600 dark:text-rose-400 uppercase tracking-wider">4. Beban Toko (&minus;)</span>
                                    <span class="w-7 h-7 rounded-xl bg-rose-100 dark:bg-rose-900/60 text-rose-600 flex items-center justify-center text-xs font-bold">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                    </span>
                                </div>
                                <div>
                                    <div class="text-base font-black font-mono text-rose-600 dark:text-rose-400">- @rupiah($totalExpense)</div>
                                    <span class="text-[10px] text-slate-400">{{ $expenseCount }}x Pengeluaran Toko</span>
                                </div>
                            </div>

                            <!-- Card 5: Laba Keuntungan Bersih -->
                            <div class="p-4 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 text-white shadow-md shadow-emerald-500/25 flex flex-col justify-between">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-[11px] font-black uppercase tracking-wider text-emerald-100">5. Laba Keuntungan</span>
                                    <span class="w-7 h-7 rounded-xl bg-white/20 text-white flex items-center justify-center text-xs font-bold">
                                        ★
                                    </span>
                                </div>
                                <div>
                                    <div class="text-base font-black font-mono text-white">@rupiah($netProfit)</div>
                                    <span class="text-[10px] text-emerald-100">Keuntungan Bersih Toko</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 4. VISUAL PROPORSI ALOKASI SETIAP RP 100 OMZET -->
                <div class="p-6 rounded-3xl bg-white dark:bg-slate-850 border border-slate-200/90 dark:border-slate-800 shadow-xs space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                        <div>
                            <h4 class="text-xs font-black uppercase tracking-wider text-slate-900 dark:text-white flex items-center gap-2">
                                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/></svg>
                                <span>Distribusi Alokasi Omzet Penjualan</span>
                            </h4>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                Proporsi ke mana setiap rupiah omzet kasir terbagi untuk modal, beban toko, dan keuntungan bersih.
                            </p>
                        </div>
                        <span class="text-xs font-bold text-slate-400 font-mono">100% Total Omzet: @rupiah($totalIncome)</span>
                    </div>

                    <!-- Progress Bar Multi-Segment -->
                    <div class="w-full h-4 rounded-full bg-slate-100 dark:bg-slate-800 flex overflow-hidden p-0.5 border border-slate-200/80 dark:border-slate-700">
                        @if($totalIncome > 0)
                            <div style="width: {{ $cogsPct }}%" class="h-full bg-amber-500 rounded-l-full transition-all duration-500" title="Modal HPP: {{ $cogsPct }}%"></div>
                            <div style="width: {{ $expensePct }}%" class="h-full bg-rose-500 transition-all duration-500" title="Beban Toko: {{ $expensePct }}%"></div>
                            <div style="width: {{ $netProfitBarWidth }}%" class="h-full bg-emerald-500 rounded-r-full transition-all duration-500" title="Laba Keuntungan: {{ $netProfitPct }}%"></div>
                        @else
                            <div class="w-full h-full bg-slate-200 dark:bg-slate-700 rounded-full"></div>
                        @endif
                    </div>

                    <!-- Legenda 3 Pos Finansial -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-2 text-xs">
                        <div class="p-3 rounded-2xl bg-amber-50/70 dark:bg-amber-950/30 border border-amber-200/80 dark:border-amber-900/50 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full bg-amber-500 shrink-0"></span>
                                <div>
                                    <span class="font-bold text-slate-800 dark:text-slate-200 block">Modal Kulakan (HPP)</span>
                                    <span class="text-[11px] text-slate-500">Harga pokok beli stok</span>
                                </div>
                            </div>
                            <div class="text-right font-mono">
                                <span class="font-bold text-amber-700 dark:text-amber-400 block">@rupiah($totalCogs)</span>
                                <span class="text-[10px] text-slate-400">{{ $cogsPct }}%</span>
                            </div>
                        </div>

                        <div class="p-3 rounded-2xl bg-rose-50/70 dark:bg-rose-950/30 border border-rose-200/80 dark:border-rose-900/50 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full bg-rose-500 shrink-0"></span>
                                <div>
                                    <span class="font-bold text-slate-800 dark:text-slate-200 block">Beban Toko & Opex</span>
                                    <span class="text-[11px] text-slate-500">Jajan, kuota, bensin, dll</span>
                                </div>
                            </div>
                            <div class="text-right font-mono">
                                <span class="font-bold text-rose-700 dark:text-rose-400 block">@rupiah($totalExpense)</span>
                                <span class="text-[10px] text-slate-400">{{ $expensePct }}%</span>
                            </div>
                        </div>

                        <div class="p-3 rounded-2xl bg-emerald-50/70 dark:bg-emerald-950/30 border border-emerald-200/80 dark:border-emerald-900/50 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full bg-emerald-500 shrink-0"></span>
                                <div>
                                    <span class="font-bold text-slate-800 dark:text-slate-200 block">Laba Keuntungan Bersih</span>
                                    <span class="text-[11px] text-slate-500">Hak bersih pemilik konter</span>
                                </div>
                            </div>
                            <div class="text-right font-mono">
                                <span class="font-bold text-emerald-700 dark:text-emerald-400 block">@rupiah($netProfit)</span>
                                <span class="text-[10px] text-slate-400">{{ $netProfitPct }}%</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 5. DUA KOLOM: RINCIAN DEKOMPOSISI LABA KEUNTUNGAN & BREAKDOWN PENGELUARAN -->
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                    <!-- KOLOM KIRI (7): BUKU BESAR RINCIAN LABA KEUNTUNGAN -->
                    <div class="lg:col-span-7 p-6 sm:p-7 rounded-3xl bg-white dark:bg-slate-850 border border-slate-200/90 dark:border-slate-800 shadow-xs space-y-5">
                        <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                            <div>
                                <h3 class="text-sm font-black text-slate-900 dark:text-white uppercase tracking-wider flex items-center gap-2">
                                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                    <span>Rincian Pembukuan Laba Keuntungan</span>
                                </h3>
                                <p class="text-xs text-slate-400 mt-0.5">Struktur formal perhitungan laba usaha konter</p>
                            </div>
                            <span class="text-[11px] px-2.5 py-1 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-bold">
                                Mata Uang: IDR (Rp)
                            </span>
                        </div>

                        <div class="space-y-3.5 text-xs sm:text-sm">
                            <!-- BAGIAN A: PENDAPATAN PENJUALAN KASIR -->
                            <div class="p-4 rounded-2xl bg-slate-50/80 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700/80 space-y-2">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-7 h-7 rounded-lg bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-xs">
                                            A
                                        </div>
                                        <div>
                                            <h5 class="font-black text-slate-900 dark:text-white">Pendapatan Penjualan Kasir (Revenue)</h5>
                                            <span class="text-[11px] text-slate-500">Penerimaan kotor dari transaksi penjualan kasir konter</span>
                                        </div>
                                    </div>
                                    <span class="font-mono font-bold text-indigo-600 dark:text-indigo-400 text-sm">
                                        @rupiah($totalIncome)
                                    </span>
                                </div>
                                <div class="flex items-center justify-between text-[11px] text-slate-400 pt-2 border-t border-slate-200/60 dark:border-slate-700/60">
                                    <span>Total Transaksi Berhasil</span>
                                    <span class="font-mono font-semibold text-slate-700 dark:text-slate-300">{{ $incomeCount }} transaksi</span>
                                </div>
                            </div>

                            <!-- BAGIAN B: BEBAN POKOK PENJUALAN (HPP) -->
                            <div class="p-4 rounded-2xl bg-amber-50/40 dark:bg-slate-800/60 border border-amber-200/60 dark:border-slate-700/80 space-y-2">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-7 h-7 rounded-lg bg-amber-100 text-amber-800 flex items-center justify-center font-bold text-xs">
                                            B
                                        </div>
                                        <div>
                                            <h5 class="font-black text-slate-900 dark:text-white">Beban Pokok Penjualan (Modal Kulakan HPP)</h5>
                                            <span class="text-[11px] text-slate-500">Modal beli stok fisik, saldo kuota, dan voucher terjual</span>
                                        </div>
                                    </div>
                                    <span class="font-mono font-bold text-amber-600 dark:text-amber-400 text-sm">
                                        - @rupiah($totalCogs)
                                    </span>
                                </div>
                                <div class="flex items-center justify-between text-[11px] text-slate-400 pt-2 border-t border-amber-200/60 dark:border-slate-700/60">
                                    <span>Tingkat Kelengkapan Modal Item</span>
                                    <span class="font-mono font-semibold text-amber-700 dark:text-amber-400">{{ $financialSummary['cost_coverage_percentage'] }}% terdata</span>
                                </div>
                            </div>

                            <!-- BAGIAN C: LABA KOTOR PENJUALAN (SUBTOTAL = A - B) -->
                            <div class="p-4 rounded-2xl bg-sky-50/80 dark:bg-sky-950/40 border border-sky-200 dark:border-sky-800/80 flex items-center justify-between">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-7 h-7 rounded-lg bg-sky-600 text-white flex items-center justify-center font-bold text-xs shadow-xs">
                                        C
                                    </div>
                                    <div>
                                        <h5 class="font-black text-sky-950 dark:text-sky-200">Laba Kotor Penjualan (Gross Profit = A &minus; B)</h5>
                                        <span class="text-[11px] text-sky-700 dark:text-sky-300">Selisih penerimaan kasir dengan modal kulakan barang</span>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="font-mono font-black text-sky-700 dark:text-sky-400 text-base block">
                                        @rupiah($grossProfit)
                                    </span>
                                    <span class="text-[10px] font-bold text-sky-600 dark:text-sky-400 font-mono">Margin: {{ $grossMarginPct }}%</span>
                                </div>
                            </div>

                            <!-- BAGIAN D: BEBAN PENGELUARAN TOKO (OPERASIONAL) -->
                            <div class="p-4 rounded-2xl bg-rose-50/40 dark:bg-slate-800/60 border border-rose-200/60 dark:border-slate-700/80 space-y-2">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-7 h-7 rounded-lg bg-rose-100 text-rose-800 flex items-center justify-center font-bold text-xs">
                                            D
                                        </div>
                                        <div>
                                            <h5 class="font-black text-slate-900 dark:text-white">Beban Pengeluaran Toko & Operasional</h5>
                                            <span class="text-[11px] text-slate-500">Biaya harian konter: jajan, bensin, kuota, token, dll.</span>
                                        </div>
                                    </div>
                                    <span class="font-mono font-bold text-rose-600 dark:text-rose-400 text-sm">
                                        - @rupiah($totalExpense)
                                    </span>
                                </div>
                                <div class="flex items-center justify-between text-[11px] text-slate-400 pt-2 border-t border-rose-200/60 dark:border-slate-700/60">
                                    <span>Jumlah Catatan Beban Toko</span>
                                    <span class="font-mono font-semibold text-rose-600">{{ $expenseCount }} catatan</span>
                                </div>
                            </div>

                            <!-- BAGIAN E: LABA KEUNTUNGAN BERSIH AKHIR (FINAL = C - D) -->
                            <div class="p-5 rounded-2xl bg-gradient-to-r from-emerald-50 via-teal-50 to-emerald-50 dark:from-emerald-950/50 dark:to-slate-800 border-2 border-emerald-400/90 dark:border-emerald-700 shadow-sm flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-emerald-600 text-white flex items-center justify-center font-black text-sm shadow-md shadow-emerald-600/30">
                                        E
                                    </div>
                                    <div>
                                        <h5 class="font-black text-emerald-950 dark:text-emerald-200 text-sm sm:text-base">
                                            Laba Keuntungan Bersih (Net Profit = C &minus; D)
                                        </h5>
                                        <span class="text-xs text-emerald-800 dark:text-emerald-300">
                                            Keuntungan bersih riil yang menjadi hak pemilik konter
                                        </span>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="text-xl sm:text-2xl font-black font-mono text-emerald-600 dark:text-emerald-400 block">
                                        {{ $netProfit >= 0 ? '+' : '' }}@rupiah($netProfit)
                                    </span>
                                    <span class="text-[11px] font-bold text-emerald-700 dark:text-emerald-300 font-mono">
                                        {{ $netProfitPct }}% Net Margin
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- KOLOM KANAN (5): BREAKDOWN BEBAN PENGELUARAN TOKO & KARTU KESEHATAN FINANSIAL -->
                    <div class="lg:col-span-5 space-y-6">
                        <!-- Card 1: Breakdown Kategori Pengeluaran Toko -->
                        <div class="p-6 rounded-3xl bg-white dark:bg-slate-850 border border-slate-200/90 dark:border-slate-800 shadow-xs space-y-4">
                            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                                <h3 class="text-xs font-black uppercase tracking-wider text-slate-900 dark:text-white flex items-center gap-2">
                                    <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                                    <span>Rincian Beban Toko</span>
                                </h3>
                                <span class="text-xs font-mono font-bold text-rose-600">Total: @rupiah($totalExpense)</span>
                            </div>

                            <div class="space-y-3">
                                @forelse($financialSummary['expenses_by_category'] as $ebc)
                                    @php
                                        $catPercent = $totalExpense > 0 ? round(($ebc->total / $totalExpense) * 100, 1) : 0;
                                    @endphp
                                    <div class="p-3 rounded-2xl bg-slate-50/80 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700/80 space-y-1.5 text-xs">
                                        <div class="flex items-center justify-between font-bold">
                                            <div class="flex items-center gap-2">
                                                <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                                                <span class="text-slate-800 dark:text-slate-200">{{ $ebc->category->name ?? 'Operasional Toko' }}</span>
                                                <span class="text-[10px] font-normal text-slate-400">({{ $ebc->count }}x)</span>
                                            </div>
                                            <span class="font-mono text-rose-600 font-black">@rupiah($ebc->total)</span>
                                        </div>
                                        <div class="w-full h-1.5 rounded-full bg-slate-200 dark:bg-slate-700 overflow-hidden">
                                            <div style="width: {{ $catPercent }}%" class="h-full bg-rose-500 rounded-full"></div>
                                        </div>
                                        <div class="flex justify-end text-[10px] text-slate-400 font-mono">
                                            <span>{{ $catPercent }}% dari total beban</span>
                                        </div>
                                    </div>
                                @empty
                                    <div class="py-8 text-center text-xs text-slate-400">
                                        <svg class="w-8 h-8 text-slate-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        <span>Tidak ada catatan pengeluaran toko pada rentang tanggal ini.</span>
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        <!-- Card 2: Health Card & Rekomendasi Finansial Konter -->
                        <div class="p-6 rounded-3xl bg-gradient-to-br from-indigo-50/70 via-white to-sky-50/70 dark:from-slate-850 dark:to-slate-850 border border-indigo-100 dark:border-slate-700 shadow-xs space-y-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-2xl bg-indigo-600 text-white flex items-center justify-center font-bold shadow-md shadow-indigo-600/25">
                                    💡
                                </div>
                                <div>
                                    <h4 class="text-xs font-black uppercase tracking-wider text-slate-900 dark:text-white">
                                        Analisis Kesehatan Toko
                                    </h4>
                                    <span class="text-[11px] text-slate-500">Evaluasi performa keuntungan konter</span>
                                </div>
                            </div>

                            <div class="space-y-2.5 text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                                @if($netProfit > 0 && $netProfitPct >= 15)
                                    <div class="p-3 rounded-xl bg-emerald-100/70 dark:bg-emerald-950/40 text-emerald-900 dark:text-emerald-200 font-semibold border border-emerald-200 dark:border-emerald-800 flex items-center gap-2">
                                        <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        <span><strong>Performa Prima:</strong> Margin laba bersih Anda mencapai {{ $netProfitPct }}%, melampaui rata-rata standar usaha konter.</span>
                                    </div>
                                @elseif($netProfit > 0)
                                    <div class="p-3 rounded-xl bg-sky-100/70 dark:bg-sky-950/40 text-sky-900 dark:text-sky-200 font-semibold border border-sky-200 dark:border-sky-800 flex items-center gap-2">
                                        <svg class="w-4 h-4 text-sky-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        <span><strong>Performa Positif:</strong> Toko menghasilkan keuntungan {{ $netProfitPct }}%. Pertahankan efisiensi belanja harian agar margin semakin meningkat.</span>
                                    </div>
                                @else
                                    <div class="p-3 rounded-xl bg-rose-100/70 dark:bg-rose-950/40 text-rose-900 dark:text-rose-200 font-semibold border border-rose-200 dark:border-rose-800 flex items-center gap-2">
                                        <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                        <span><strong>Perhatian:</strong> Beban operasional melebihi laba kotor penjualan. Tinjau pos pengeluaran seperti jajan atau belanja non-produktif.</span>
                                    </div>
                                @endif
                                <p class="text-[11px] text-slate-500">
                                    Tip: Selalu pastikan memasukkan harga modal (HPP) saat kulakan barang baru agar angka keuntungan selalu akurat.
                                </p>
                            </div>

                            <div class="pt-2 flex items-center gap-2">
                                <a
                                    href="{{ route('reports.print', array_merge(request()->all(), ['tab' => 'laba_rugi', 'start_date' => $startDate, 'end_date' => $endDate])) }}"
                                    target="_blank"
                                    class="flex-1 py-2 px-3 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs text-center transition-all shadow-xs"
                                >
                                    Cetak Laba Keuntungan
                                </a>
                                <a
                                    href="{{ route('reports.export', array_merge(request()->all(), ['tab' => 'laba_rugi', 'start_date' => $startDate, 'end_date' => $endDate])) }}"
                                    class="py-2 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 dark:bg-slate-800 dark:text-slate-300 font-bold text-xs text-center transition-all"
                                >
                                    CSV
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- TAB 3: DETAIL LAPORAN PEMASUKAN -->
        @if($tab === 'pemasukan' && $incomes)
            <div class="space-y-6">
                <!-- Bar Total Ringkasan Terfilter -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="p-4 rounded-2xl bg-indigo-50 border border-indigo-200 text-xs">
                        <span class="text-slate-500 font-bold block">Total Omzet Terfilter:</span>
                        <strong class="font-mono font-black text-lg text-indigo-700">@rupiah($incomesSummary['total_amount'])</strong>
                    </div>
                    <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200 text-xs">
                        <span class="text-slate-500 font-bold block">Total Modal HPP Terfilter:</span>
                        <strong class="font-mono font-black text-lg text-amber-700">@rupiah($incomesSummary['total_cost'])</strong>
                    </div>
                    <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-xs">
                        <span class="text-slate-500 font-bold block">Total Laba Bersih Terfilter:</span>
                        <strong class="font-mono font-black text-lg text-emerald-700">+ @rupiah($incomesSummary['total_profit'])</strong>
                    </div>
                </div>

                <!-- Tabel Pemasukan -->
                <div class="rounded-3xl bg-white dark:bg-slate-850 border border-slate-200/90 dark:border-slate-800 shadow-xs overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200/80 dark:divide-slate-800 text-left text-xs">
                            <thead class="bg-slate-50/90 dark:bg-slate-800/90 text-slate-500 uppercase font-black tracking-wider text-[11px]">
                                <tr>
                                    <th class="px-6 py-4">Faktur & Waktu</th>
                                    <th class="px-5 py-4">Item Transaksi</th>
                                    <th class="px-4 py-4">Kategori</th>
                                    <th class="px-4 py-4">Metode Bayar</th>
                                    <th class="px-4 py-4 text-right">Modal Kulakan</th>
                                    <th class="px-4 py-4 text-right">Total Omzet</th>
                                    <th class="px-5 py-4 text-right">Laba Bersih</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80 font-medium">
                                @forelse($incomes as $inc)
                                    <tr class="hover:bg-indigo-50/30 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <a href="{{ route('incomes.show', $inc) }}" class="font-mono font-black text-indigo-600 hover:underline">
                                                {{ $inc->invoice_number }}
                                            </a>
                                            <span class="text-[11px] text-slate-400 block mt-0.5">@tanggalWaktu($inc->transaction_date)</span>
                                        </td>
                                        <td class="px-5 py-4">
                                            @if($inc->items->isNotEmpty())
                                                @foreach($inc->items as $it)
                                                    <div class="font-bold text-slate-900 dark:text-white text-xs leading-snug">
                                                        {{ $it->product_name }} <span class="text-indigo-600 font-mono font-black">({{ $it->quantity }}x)</span>
                                                    </div>
                                                @endforeach
                                            @else
                                                <div class="font-bold text-slate-900 dark:text-white text-xs">{{ $inc->category->name ?? 'Penjualan' }}</div>
                                            @endif
                                            @if($inc->notes)
                                                <span class="text-[10px] text-slate-400 block mt-0.5 truncate max-w-xs">{{ $inc->notes }}</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-4">
                                            <span class="px-2.5 py-1 rounded-xl text-[11px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                                {{ $inc->category->name ?? 'Umum' }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-4 uppercase font-bold text-slate-600 font-mono">
                                            {{ $inc->payment_method }}
                                        </td>
                                        <td class="px-4 py-4 text-right font-mono text-slate-500">
                                            @rupiah($inc->cost_price)
                                        </td>
                                        <td class="px-4 py-4 text-right font-mono font-black text-slate-900 dark:text-white">
                                            @rupiah($inc->total_amount)
                                        </td>
                                        <td class="px-5 py-4 text-right font-mono font-black text-emerald-600">
                                            + @rupiah($inc->profit)
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                                            Tidak ada data transaksi pemasukan pada filter ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="p-5 border-t border-slate-100 dark:border-slate-800">
                        {{ $incomes->links() }}
                    </div>
                </div>
            </div>
        @endif

        <!-- TAB 4: DETAIL LAPORAN PENGELUARAN -->
        @if($tab === 'pengeluaran' && $expenses)
            <div class="space-y-6">
                <!-- Bar Total Ringkasan Terfilter -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-xs">
                        <span class="text-slate-500 font-bold block">Total Pengeluaran Kas Terfilter:</span>
                        <strong class="font-mono font-black text-xl text-rose-600">- @rupiah($expensesSummary['total_amount'])</strong>
                    </div>
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 text-xs">
                        <span class="text-slate-500 font-bold block">Jumlah Catatan Pengeluaran:</span>
                        <strong class="font-mono font-black text-xl text-slate-800">{{ $expensesSummary['total_count'] }} Transaksi</strong>
                    </div>
                </div>

                <!-- Tabel Pengeluaran -->
                <div class="rounded-3xl bg-white dark:bg-slate-850 border border-slate-200/90 dark:border-slate-800 shadow-xs overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200/80 dark:divide-slate-800 text-left text-xs">
                            <thead class="bg-slate-50/90 dark:bg-slate-800/90 text-slate-500 uppercase font-black tracking-wider text-[11px]">
                                <tr>
                                    <th class="px-6 py-4">Invoice & Tanggal</th>
                                    <th class="px-5 py-4">Kategori</th>
                                    <th class="px-5 py-4">Keterangan Pengeluaran</th>
                                    <th class="px-4 py-4">Metode Bayar</th>
                                    <th class="px-4 py-4">Pencatat</th>
                                    <th class="px-6 py-4 text-right">Nominal Pengeluaran</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80 font-medium">
                                @forelse($expenses as $exp)
                                    <tr class="hover:bg-rose-50/30 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="font-mono font-bold text-rose-600">{{ $exp->invoice_number }}</span>
                                            <span class="text-[11px] text-slate-400 block mt-0.5">@tanggalWaktu($exp->transaction_date)</span>
                                        </td>
                                        <td class="px-5 py-4">
                                            <span class="px-2.5 py-1 rounded-xl text-[11px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                                {{ $exp->category->name ?? 'Operasional' }}
                                            </span>
                                        </td>
                                        <td class="px-5 py-4 font-bold text-slate-800 dark:text-slate-200">
                                            {{ $exp->notes }}
                                        </td>
                                        <td class="px-4 py-4 uppercase font-bold text-slate-600 font-mono">
                                            {{ $exp->payment_method }}
                                        </td>
                                        <td class="px-4 py-4 text-slate-500">
                                            {{ $exp->user->name ?? 'Kasir' }}
                                        </td>
                                        <td class="px-6 py-4 text-right font-mono font-black text-rose-600 text-sm">
                                            - @rupiah($exp->total_amount)
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                                            Tidak ada data catatan pengeluaran pada filter ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="p-5 border-t border-slate-100 dark:border-slate-800">
                        {{ $expenses->links() }}
                    </div>
                </div>
            </div>
        @endif

        <!-- TAB 5: LAPORAN PRODUK & VALUASI STOK -->
        @if($tab === 'produk_stok' && $products)
            <div class="space-y-6">
                <!-- Bar Metrik Valuasi Stok -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="p-4 rounded-2xl bg-blue-50 border border-blue-200 text-xs">
                        <span class="text-slate-500 font-bold block">Total SKU / Fisik Unit:</span>
                        <strong class="font-mono font-black text-lg text-blue-700">{{ $productsSummary['total_sku'] }} Produk ({{ number_format($productsSummary['total_units']) }} Unit)</strong>
                    </div>
                    <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200 text-xs">
                        <span class="text-slate-500 font-bold block">Nilai Aset Modal (HPP):</span>
                        <strong class="font-mono font-black text-lg text-amber-700">@rupiah($productsSummary['total_cost_value'])</strong>
                    </div>
                    <div class="p-4 rounded-2xl bg-indigo-50 border border-indigo-200 text-xs">
                        <span class="text-slate-500 font-bold block">Estimasi Nilai Penjualan:</span>
                        <strong class="font-mono font-black text-lg text-indigo-700">@rupiah($productsSummary['total_selling_value'])</strong>
                    </div>
                    <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-xs">
                        <span class="text-slate-500 font-bold block">Potensi Laba Kotor Stok:</span>
                        <strong class="font-mono font-black text-lg text-emerald-700">+ @rupiah($productsSummary['potential_profit'])</strong>
                    </div>
                </div>

                <!-- Tabel Produk & Valuasi -->
                <div class="rounded-3xl bg-white dark:bg-slate-850 border border-slate-200/90 dark:border-slate-800 shadow-xs overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200/80 dark:divide-slate-800 text-left text-xs">
                            <thead class="bg-slate-50/90 dark:bg-slate-800/90 text-slate-500 uppercase font-black tracking-wider text-[11px]">
                                <tr>
                                    <th class="px-6 py-4">Kode & Nama Produk</th>
                                    <th class="px-4 py-4">Kategori</th>
                                    <th class="px-4 py-4 text-center">Stok Fisik</th>
                                    <th class="px-4 py-4 text-right">Harga Modal</th>
                                    <th class="px-4 py-4 text-right">Harga Jual</th>
                                    <th class="px-4 py-4 text-right">Nilai Aset (HPP)</th>
                                    <th class="px-5 py-4 text-right">Estimasi Jual</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80 font-medium">
                                @forelse($products as $p)
                                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors">
                                        <td class="px-6 py-4">
                                            <div class="font-bold text-slate-900 dark:text-white text-xs">{{ $p->name }}</div>
                                            <span class="font-mono text-[10px] text-slate-400">{{ $p->code }}</span>
                                        </td>
                                        <td class="px-4 py-4">
                                            <span class="px-2 py-0.5 rounded-lg text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                                {{ $p->category->name ?? 'Umum' }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-4 text-center">
                                            <span class="font-mono font-bold {{ $p->stock <= 0 ? 'text-rose-600' : ($p->stock <= $p->min_stock ? 'text-amber-600' : 'text-slate-900 dark:text-white') }}">
                                                {{ $p->stock }} {{ $p->unit ?? 'pcs' }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-4 text-right font-mono text-slate-500">
                                            @rupiah($p->cost_price)
                                        </td>
                                        <td class="px-4 py-4 text-right font-mono font-bold text-slate-900 dark:text-white">
                                            @rupiah($p->selling_price)
                                        </td>
                                        <td class="px-4 py-4 text-right font-mono font-black text-amber-700">
                                            @rupiah($p->stock * $p->cost_price)
                                        </td>
                                        <td class="px-5 py-4 text-right font-mono font-black text-emerald-600">
                                            @rupiah($p->stock * $p->selling_price)
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                                            Tidak ada data produk pada filter ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="p-5 border-t border-slate-100 dark:border-slate-800">
                        {{ $products->links() }}
                    </div>
                </div>
            </div>
        @endif

        <!-- TAB 6: HISTORI MUTASI STOK -->
        @if($tab === 'mutasi_stok' && $mutations)
            <div class="space-y-6">
                <!-- Bar Ringkasan Mutasi Terfilter -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-xs">
                        <span class="text-slate-500 font-bold block">Total Barang Masuk (Restock):</span>
                        <strong class="font-mono font-black text-lg text-emerald-700">+{{ number_format($mutationsSummary['total_in']) }} Unit</strong>
                    </div>
                    <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-xs">
                        <span class="text-slate-500 font-bold block">Total Barang Keluar (Terjual):</span>
                        <strong class="font-mono font-black text-lg text-rose-700">-{{ number_format($mutationsSummary['total_out']) }} Unit</strong>
                    </div>
                    <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200 text-xs">
                        <span class="text-slate-500 font-bold block">Aktivitas Opname / Audit:</span>
                        <strong class="font-mono font-black text-lg text-amber-700">{{ number_format($mutationsSummary['total_adjustment']) }} Kali</strong>
                    </div>
                </div>

                <!-- Tabel Mutasi Stok -->
                <div class="rounded-3xl bg-white dark:bg-slate-850 border border-slate-200/90 dark:border-slate-800 shadow-xs overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200/80 dark:divide-slate-800 text-left text-xs">
                            <thead class="bg-slate-50/90 dark:bg-slate-800/90 text-slate-500 uppercase font-black tracking-wider text-[11px]">
                                <tr>
                                    <th class="px-6 py-4">Waktu Mutasi</th>
                                    <th class="px-5 py-4">Produk</th>
                                    <th class="px-4 py-4 text-center">Tipe</th>
                                    <th class="px-4 py-4 text-center">Perubahan</th>
                                    <th class="px-4 py-4 text-center">Transisi Stok</th>
                                    <th class="px-5 py-4">Keterangan</th>
                                    <th class="px-5 py-4">Operator</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80 font-medium">
                                @forelse($mutations as $m)
                                    <tr class="hover:bg-slate-50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap text-slate-500">
                                            @tanggalWaktu($m->created_at)
                                        </td>
                                        <td class="px-5 py-4">
                                            <div class="font-bold text-slate-900 dark:text-white text-xs">{{ $m->product->name ?? 'Produk Dihapus' }}</div>
                                            <span class="font-mono text-[10px] text-slate-400">{{ $m->product->code ?? '-' }}</span>
                                        </td>
                                        <td class="px-4 py-4 text-center">
                                            @if($m->type === 'in')
                                                <span class="px-2 py-0.5 rounded-lg text-[10px] font-bold bg-emerald-100 text-emerald-800">Masuk (+)</span>
                                            @elseif($m->type === 'out')
                                                <span class="px-2 py-0.5 rounded-lg text-[10px] font-bold bg-rose-100 text-rose-800">Keluar (-)</span>
                                            @else
                                                <span class="px-2 py-0.5 rounded-lg text-[10px] font-bold bg-amber-100 text-amber-800">Opname (=)</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-4 text-center font-mono font-black {{ $m->type === 'in' ? 'text-emerald-600' : ($m->type === 'out' ? 'text-rose-600' : 'text-amber-600') }}">
                                            {{ $m->type === 'in' ? '+' : ($m->type === 'out' ? '-' : '') }}{{ $m->quantity }}
                                        </td>
                                        <td class="px-4 py-4 text-center font-mono">
                                            {{ $m->stock_before }} &rarr; <strong class="font-bold text-slate-900 dark:text-white">{{ $m->stock_after }}</strong>
                                        </td>
                                        <td class="px-5 py-4 text-slate-600">
                                            {{ $m->notes ?? '-' }}
                                        </td>
                                        <td class="px-5 py-4 text-slate-500">
                                            {{ $m->user->name ?? 'Sistem' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                                            Tidak ada riwayat mutasi stok pada filter ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="p-5 border-t border-slate-100 dark:border-slate-800">
                        {{ $mutations->links() }}
                    </div>
                </div>
            </div>
        @endif

        <!-- ================================================================= -->
        <!-- TAB 7: BUKU PIUTANG KASBON PELANGGAN                              -->
        <!-- ================================================================= -->
        @if($tab === 'kasbon' && $kasbons)
            <div class="space-y-6">
                <!-- 4 KARTU RINGKASAN METRIK KASBON -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- 1. Total Kasbon Dicatat -->
                    <div class="p-5 rounded-3xl bg-white dark:bg-slate-850 border border-slate-200/80 dark:border-slate-800 shadow-xs relative overflow-hidden">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Kasbon Dicatat</span>
                            <span class="w-8 h-8 rounded-xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                            </span>
                        </div>
                        <h4 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white font-mono">
                            @rupiah($kasbonsSummary['total_amount'])
                        </h4>
                        <div class="mt-2 flex items-center gap-1.5 text-xs text-slate-500">
                            <span class="font-bold text-slate-700 dark:text-slate-300">{{ $kasbonsSummary['total_count'] }}</span>
                            <span>catatan transaksi</span>
                        </div>
                    </div>

                    <!-- 2. Total Piutang Belum Bayar -->
                    <div class="p-5 rounded-3xl bg-white dark:bg-slate-850 border border-rose-200/80 dark:border-rose-900/60 shadow-xs relative overflow-hidden">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-bold text-rose-600 dark:text-rose-400 uppercase tracking-wider">Piutang Belum Bayar</span>
                            <span class="w-8 h-8 rounded-xl bg-rose-50 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </span>
                        </div>
                        <h4 class="text-xl sm:text-2xl font-black text-rose-600 dark:text-rose-400 font-mono">
                            @rupiah($kasbonsSummary['unpaid_amount'])
                        </h4>
                        <div class="mt-2 flex items-center gap-1.5 text-xs text-slate-500">
                            <span class="font-bold text-rose-600 dark:text-rose-400">{{ $kasbonsSummary['unpaid_count'] }}</span>
                            <span>pelanggan belum melunasi</span>
                        </div>
                    </div>

                    <!-- 3. Total Sudah Lunas -->
                    <div class="p-5 rounded-3xl bg-white dark:bg-slate-850 border border-emerald-200/80 dark:border-emerald-900/60 shadow-xs relative overflow-hidden">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider">Total Sudah Lunas</span>
                            <span class="w-8 h-8 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </span>
                        </div>
                        <h4 class="text-xl sm:text-2xl font-black text-emerald-600 dark:text-emerald-400 font-mono">
                            @rupiah($kasbonsSummary['paid_amount'])
                        </h4>
                        <div class="mt-2 flex items-center gap-1.5 text-xs text-slate-500">
                            <span class="font-bold text-emerald-600 dark:text-emerald-400">{{ $kasbonsSummary['paid_count'] }}</span>
                            <span>catatan telah terlunasi</span>
                        </div>
                    </div>

                    <!-- 4. Jatuh Tempo / Overdue -->
                    <div class="p-5 rounded-3xl bg-white dark:bg-slate-850 border border-amber-200/80 dark:border-amber-900/60 shadow-xs relative overflow-hidden">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-bold text-amber-600 dark:text-amber-400 uppercase tracking-wider">Jatuh Tempo (Perlu Tagih)</span>
                            <span class="w-8 h-8 rounded-xl bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            </span>
                        </div>
                        <h4 class="text-xl sm:text-2xl font-black text-amber-600 dark:text-amber-400 font-mono">
                            {{ $kasbonsSummary['overdue_count'] }} <span class="text-sm font-sans font-bold text-slate-500">Tagihan</span>
                        </h4>
                        <div class="mt-2 flex items-center gap-1.5 text-xs text-slate-500">
                            <span>Estimasi Laba: <strong class="text-emerald-600 dark:text-emerald-400 font-mono">+@rupiah($kasbonsSummary['total_profit'])</strong></span>
                        </div>
                    </div>
                </div>

                <!-- TABEL RINCIAN BUKU PIUTANG KASBON -->
                <div class="rounded-3xl bg-white dark:bg-slate-850 border border-slate-200/90 dark:border-slate-800 shadow-xs overflow-hidden">
                    <div class="p-5 sm:p-6 border-b border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div>
                            <h3 class="text-base font-black text-slate-900 dark:text-white tracking-tight flex items-center gap-2">
                                <span>Buku Piutang & Catatan Kasbon Pelanggan</span>
                                <span class="text-xs px-2.5 py-0.5 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 font-bold">
                                    {{ $kasbons->total() }} Data
                                </span>
                            </h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                                Rincian utang piutang pelanggan konter, status pembayaran, serta pengingat tagihan.
                            </p>
                        </div>

                        <!-- Tombol Cetak & Ekspor Khusus Tab Kasbon -->
                        <div class="flex items-center gap-2 flex-wrap">
                            <a
                                href="{{ route('reports.print', array_merge(request()->all(), ['tab' => 'kasbon', 'start_date' => $startDate, 'end_date' => $endDate])) }}"
                                target="_blank"
                                class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md shadow-indigo-600/20 transition-all cursor-pointer"
                                title="Cetak Laporan Buku Kasbon atau Simpan PDF"
                            >
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                <span>Cetak Laporan Kasbon</span>
                            </a>

                            <a
                                href="{{ route('reports.export', array_merge(request()->all(), ['tab' => 'kasbon', 'start_date' => $startDate, 'end_date' => $endDate])) }}"
                                class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-750 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 font-bold text-xs transition-all cursor-pointer"
                                title="Ekspor Data Kasbon ke Format CSV"
                            >
                                <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                <span>Ekspor CSV</span>
                            </a>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200/80 dark:divide-slate-800 text-left text-xs">
                            <thead class="bg-slate-50 dark:bg-slate-800/60 text-slate-500 uppercase font-bold tracking-wider text-[11px]">
                                <tr>
                                    <th class="px-5 py-3.5">Tanggal Kasbon</th>
                                    <th class="px-5 py-3.5">Pelanggan</th>
                                    <th class="px-5 py-3.5">Barang / Layanan</th>
                                    <th class="px-4 py-3.5 text-right">Nominal Kasbon</th>
                                    <th class="px-4 py-3.5 text-right">Estimasi Laba</th>
                                    <th class="px-4 py-3.5 text-center">Jatuh Tempo</th>
                                    <th class="px-4 py-3.5 text-center">Status</th>
                                    <th class="px-5 py-3.5">Kasir</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800 font-medium">
                                @forelse($kasbons as $k)
                                    <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/40 transition-colors">
                                        <!-- Tanggal Kasbon -->
                                        <td class="px-5 py-4 whitespace-nowrap">
                                            <span class="font-bold text-slate-900 dark:text-white block">
                                                @tanggal($k->debt_date)
                                            </span>
                                            <span class="text-[11px] text-slate-400 font-mono">
                                                {{ $k->created_at->format('H:i') }} WIB
                                            </span>
                                        </td>

                                        <!-- Pelanggan & WhatsApp Reminder -->
                                        <td class="px-5 py-4">
                                            <div class="font-bold text-slate-900 dark:text-white">
                                                {{ $k->customer_name }}
                                            </div>
                                            @if($k->customer_phone)
                                                <div class="flex items-center gap-1.5 mt-0.5">
                                                    <span class="text-[11px] text-slate-500 font-mono">{{ $k->customer_phone }}</span>
                                                    @if(!$k->is_paid && $k->whatsapp_reminder_url)
                                                        <a 
                                                            href="{{ $k->whatsapp_reminder_url }}" 
                                                            target="_blank"
                                                            class="inline-flex items-center px-1.5 py-0.5 rounded bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-400 text-[10px] font-bold border border-emerald-200 dark:border-emerald-800 hover:bg-emerald-100 transition-colors"
                                                            title="Kirim pesan penagihan via WhatsApp"
                                                        >
                                                            <span>WA Tagih</span>
                                                        </a>
                                                    @endif
                                                </div>
                                            @endif
                                        </td>

                                        <!-- Barang / Layanan -->
                                        <td class="px-5 py-4">
                                            <span class="font-bold text-slate-800 dark:text-slate-200 block">
                                                {{ $k->product_name ?? ($k->description ?? 'Kasbon') }}
                                            </span>
                                            @if($k->quantity > 1)
                                                <span class="text-[11px] text-slate-400 font-mono block">
                                                    Qty: {{ $k->quantity }} unit
                                                </span>
                                            @endif
                                            @if($k->notes)
                                                <span class="text-[11px] text-slate-400 block truncate max-w-xs mt-0.5">
                                                    {{ $k->notes }}
                                                </span>
                                            @endif
                                        </td>

                                        <!-- Nominal Kasbon -->
                                        <td class="px-4 py-4 text-right font-mono font-black text-slate-900 dark:text-white whitespace-nowrap">
                                            @rupiah($k->amount)
                                        </td>

                                        <!-- Estimasi Laba -->
                                        <td class="px-4 py-4 text-right font-mono font-bold whitespace-nowrap {{ $k->profit > 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-400' }}">
                                            +@rupiah($k->profit)
                                        </td>

                                        <!-- Jatuh Tempo -->
                                        <td class="px-4 py-4 text-center whitespace-nowrap">
                                            @if($k->due_date)
                                                <span class="block {{ $k->is_overdue ? 'text-rose-600 dark:text-rose-400 font-bold' : 'text-slate-600 dark:text-slate-300 font-medium' }}">
                                                    @tanggal($k->due_date)
                                                </span>
                                                @if($k->is_overdue)
                                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-[10px] font-black bg-rose-100 text-rose-700 dark:bg-rose-950/60 dark:text-rose-300">
                                                        Jatuh Tempo
                                                    </span>
                                                @endif
                                            @else
                                                <span class="text-slate-400">-</span>
                                            @endif
                                        </td>

                                        <!-- Status Pembayaran -->
                                        <td class="px-4 py-4 text-center whitespace-nowrap">
                                            @if($k->is_paid)
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100/90 text-emerald-800 dark:bg-emerald-950/80 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                                    <span>Lunas</span>
                                                </span>
                                                @if($k->paid_at)
                                                    <span class="text-[10px] text-slate-400 block mt-0.5 font-mono">
                                                        {{ \Carbon\Carbon::parse($k->paid_at)->format('d/m/Y') }}
                                                    </span>
                                                @endif
                                            @else
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100/90 text-amber-800 dark:bg-amber-950/80 dark:text-amber-300 border border-amber-200 dark:border-amber-800">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                                    <span>Belum Bayar</span>
                                                </span>
                                            @endif
                                        </td>

                                        <!-- Kasir -->
                                        <td class="px-5 py-4 whitespace-nowrap text-slate-500 dark:text-slate-400 font-medium">
                                            {{ $k->user->name ?? 'Kasir' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="px-6 py-12 text-center text-slate-400">
                                            Tidak ada data catatan kasbon pada filter atau rentang tanggal ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="p-5 border-t border-slate-100 dark:border-slate-800">
                        {{ $kasbons->links() }}
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-layouts.app>
