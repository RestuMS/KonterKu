<x-layouts.app title="Daftar Pemasukan & Kasir">
    <x-slot:header>
        Pemasukan & Kasir Penjualan
    </x-slot:header>

    @php
        // Filter out aksesoris, jasa hp, lain-lain dari kategori database
        $cleanCategories = $categories->filter(function($c) {
            $name = strtolower($c->name);
            return !str_contains($name, 'aksesoris') && 
                   !str_contains($name, 'jasa hp') && 
                   !str_contains($name, 'jasa servis') && 
                   !str_contains($name, 'lain-lain') &&
                   !str_contains($name, 'operasional');
        })->values();

        $profitMargin = $totalAmount > 0 ? round(($totalProfit / $totalAmount) * 100, 1) : 0;
        $costRatio = $totalAmount > 0 ? round(($totalCost / $totalAmount) * 100, 1) : 0;

        $todayStr = now()->format('Y-m-d');
        $weekAgoStr = now()->subDays(6)->format('Y-m-d');
        $monthStartStr = now()->startOfMonth()->format('Y-m-d');
        $monthEndStr = now()->endOfMonth()->format('Y-m-d');

        $isFiltered = request()->hasAny(['search', 'category_id', 'payment_method', 'start_date', 'end_date']);
    @endphp

    <div class="space-y-6">
        @if(session('recent_invoice_id'))
            <!-- ================================================================= -->
            <!-- BANNER SUKSES KASIR DENGAN OPSI CETAK STRUK & TRANSAKSI BARU      -->
            <!-- ================================================================= -->
            <div 
                x-data="{ show: true }" 
                x-show="show" 
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 -translate-y-2 scale-98"
                x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                class="p-5 sm:p-6 rounded-3xl bg-gradient-to-r from-emerald-600 via-teal-600 to-emerald-700 text-white shadow-xl shadow-emerald-600/20 border border-emerald-400/40 relative overflow-hidden"
            >
                <div class="absolute -right-10 -bottom-10 w-44 h-44 rounded-full bg-white/10 blur-2xl pointer-events-none"></div>
                <div class="relative flex flex-col md:flex-row md:items-center md:justify-between gap-5">
                    <div class="flex items-start sm:items-center gap-4">
                        <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl bg-white/20 backdrop-blur-md flex items-center justify-center shrink-0 shadow-inner border border-white/25">
                            <span class="text-2xl sm:text-3xl">🎉</span>
                        </div>
                        <div>
                            <div class="flex items-center gap-2.5 flex-wrap">
                                <h3 class="text-base sm:text-lg font-black tracking-tight">Transaksi Kasir Berhasil Dicatat!</h3>
                                <span class="px-2.5 py-0.5 rounded-full bg-black/25 text-emerald-100 font-mono text-xs font-bold border border-white/20 tracking-wider">
                                    {{ session('recent_invoice_number') }}
                                </span>
                            </div>
                            <p class="text-xs sm:text-sm text-emerald-100 mt-1">
                                Total Omzet Diterima: <span class="font-bold text-white text-sm sm:text-base">@rupiah(session('recent_invoice_amount'))</span>
                                @if(session('recent_invoice_method'))
                                    <span class="mx-1.5 opacity-60">&bull;</span>
                                    Metode: <span class="font-bold uppercase tracking-wider text-white">{{ session('recent_invoice_method') }}</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    <!-- Tombol Opsi Cepat Kasir -->
                    <div class="flex items-center gap-2.5 flex-wrap self-start md:self-auto">
                        <a 
                            href="{{ route('incomes.show', session('recent_invoice_id')) }}?auto_print=1" 
                            target="_blank"
                            class="px-4 py-2.5 rounded-2xl bg-white text-emerald-900 hover:bg-emerald-50 font-black text-xs sm:text-sm shadow-lg shadow-black/15 hover:shadow-xl transition-all flex items-center gap-2 active:scale-95 cursor-pointer"
                        >
                            <span class="text-base">🖨️</span>
                            <span>Langsung Cetak Struk</span>
                        </a>

                        <a 
                            href="{{ route('incomes.create') }}" 
                            class="px-4 py-2.5 rounded-2xl bg-emerald-800/80 hover:bg-emerald-800 text-white font-bold text-xs sm:text-sm border border-white/30 shadow-sm transition-all flex items-center gap-2 active:scale-95 cursor-pointer"
                        >
                            <span class="text-base">⚡</span>
                            <span>Transaksi Kasir Baru</span>
                        </a>

                        <button 
                            type="button" 
                            @click="show = false" 
                            class="w-9 h-9 rounded-xl bg-black/15 hover:bg-black/25 flex items-center justify-center text-white/80 hover:text-white transition-colors cursor-pointer"
                            title="Tutup Banner"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                </div>
            </div>
        @endif

        <!-- ================================================================= -->
        <!-- 1. HEADER HALAMAN ELEGAN & CERAH (LIGHT & AIRY)                   -->
        <!-- ================================================================= -->
        <div class="p-6 sm:p-7 rounded-3xl bg-gradient-to-r from-indigo-50/80 via-white to-sky-50/50 dark:from-slate-850 dark:via-slate-800 dark:to-slate-850 border border-indigo-100/80 dark:border-slate-700 shadow-sm">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-5">
                <div class="flex items-start sm:items-center gap-4">
                    <!-- Icon Kasir Vektor Gradien -->
                    <div class="w-13 h-13 rounded-2xl bg-gradient-to-tr from-indigo-600 to-indigo-500 text-white flex items-center justify-center shadow-lg shadow-indigo-600/25 shrink-0">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2.5 flex-wrap">
                            <h2 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight">
                                Pemasukan & Kasir Penjualan
                            </h2>
                            <span class="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full bg-emerald-100/80 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 text-xs font-bold shadow-2xs">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                <span>{{ $incomes->total() }} Transaksi</span>
                            </span>
                        </div>
                        <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">
                            Kelola riwayat arus kas masuk: pulsa, paket kuota, voucher, token PLN, e-wallet, transfer & tarik tunai.
                        </p>
                    </div>
                </div>

                <!-- Tombol Aksi Cepat (Cerah & Menarik) -->
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
                        href="{{ route('incomes.create') }}" 
                        class="inline-flex items-center gap-2 px-4.5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs uppercase tracking-wider transition-all shadow-md shadow-indigo-600/25 cursor-pointer hover:shadow-lg"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span>Catat Transaksi Baru</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- ================================================================= -->
        <!-- 2. TIGA KARTU METRIK FINANSIAL CERAH & ELEGAN (BUKAN GELAP)       -->
        <!-- ================================================================= -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
            <!-- 1. Total Omzet Penjualan (Indigo Lembut) -->
            <div class="p-5 sm:p-6 rounded-3xl bg-gradient-to-br from-indigo-50/90 via-white to-sky-50/40 dark:from-indigo-950/40 dark:via-slate-850 dark:to-slate-850 border border-indigo-200/70 dark:border-indigo-900/50 shadow-xs hover:shadow-md transition-all">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-indigo-900 dark:text-indigo-300 uppercase tracking-wider">
                        Total Omzet Penjualan
                    </span>
                    <!-- Icon Vektor Dompet / Kas -->
                    <div class="w-11 h-11 rounded-2xl bg-indigo-600 text-white flex items-center justify-center shadow-md shadow-indigo-600/25">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                        </svg>
                    </div>
                </div>

                <div class="mt-4">
                    <div class="text-2xl sm:text-3xl font-black font-mono tracking-tight text-slate-900 dark:text-white">
                        @rupiah($totalAmount)
                    </div>
                    <div class="mt-3 flex items-center justify-between text-xs text-slate-500 dark:text-slate-400">
                        <span>Penerimaan kotor pembeli</span>
                        <span class="font-bold font-mono px-2.5 py-0.5 rounded-full bg-indigo-100/80 dark:bg-indigo-950/80 border border-indigo-200 dark:border-indigo-800 text-indigo-700 dark:text-indigo-300">
                            {{ $incomes->total() }} Faktur
                        </span>
                    </div>
                </div>
            </div>

            <!-- 2. Total Modal Kulakan HPP (Amber Lembut) -->
            <div class="p-5 sm:p-6 rounded-3xl bg-gradient-to-br from-amber-50/70 via-white to-orange-50/30 dark:from-slate-850 dark:via-slate-850 dark:to-slate-850 border border-amber-200/70 dark:border-slate-700 shadow-xs hover:shadow-md transition-all">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-amber-900 dark:text-amber-300 uppercase tracking-wider">
                        Total Modal Kulakan (HPP)
                    </span>
                    <!-- Icon Vektor Paket Stok -->
                    <div class="w-11 h-11 rounded-2xl bg-amber-500 text-white flex items-center justify-center shadow-md shadow-amber-500/25">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    </div>
                </div>

                <div class="mt-4">
                    <div class="text-2xl sm:text-3xl font-black font-mono tracking-tight text-slate-900 dark:text-white">
                        @rupiah($totalCost)
                    </div>
                    <div class="mt-3 flex items-center justify-between text-xs text-slate-500 dark:text-slate-400">
                        <span>Biaya pokok pulsa & stok</span>
                        <span class="font-bold font-mono px-2.5 py-0.5 rounded-full bg-amber-100/80 dark:bg-amber-950/80 border border-amber-200 dark:border-amber-800 text-amber-800 dark:text-amber-300">
                            {{ $costRatio }}% Rasio Modal
                        </span>
                    </div>
                </div>
            </div>

            <!-- 3. Estimasi Laba Bersih (Emerald Zamrud Lembut) -->
            <div class="p-5 sm:p-6 rounded-3xl bg-gradient-to-br from-emerald-50/90 via-white to-teal-50/40 dark:from-emerald-950/40 dark:via-slate-850 dark:to-slate-850 border border-emerald-200/80 dark:border-emerald-900/50 shadow-xs hover:shadow-md transition-all">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-emerald-900 dark:text-emerald-300 uppercase tracking-wider">
                        Estimasi Laba Bersih
                    </span>
                    <!-- Icon Vektor Grafik Trend Naik -->
                    <div class="w-11 h-11 rounded-2xl bg-emerald-600 text-white flex items-center justify-center shadow-md shadow-emerald-600/25">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                        </svg>
                    </div>
                </div>

                <div class="mt-4">
                    <div class="text-2xl sm:text-3xl font-black font-mono tracking-tight text-emerald-600 dark:text-emerald-400">
                        + @rupiah($totalProfit)
                    </div>
                    <div class="mt-3 flex items-center justify-between text-xs text-slate-500 dark:text-slate-400">
                        <span>Keuntungan riil toko</span>
                        <span class="inline-flex items-center gap-1 font-bold font-mono px-2.5 py-0.5 rounded-full bg-emerald-100/90 dark:bg-emerald-950/80 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            {{ $profitMargin }}% Margin Laba
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
                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <span>Periode:</span>
                    </span>
                    
                    <!-- Semua Waktu -->
                    <a
                        href="{{ route('incomes.index', array_merge(request()->except(['start_date', 'end_date', 'page']))) }}"
                        class="px-3.5 py-1.5 rounded-xl transition-all cursor-pointer {{ !request('start_date') && !request('end_date') ? 'bg-indigo-600 text-white font-bold shadow-xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200' }}"
                    >
                        Semua Waktu
                    </a>

                    <!-- Hari Ini -->
                    <a
                        href="{{ route('incomes.index', array_merge(request()->except(['page']), ['start_date' => $todayStr, 'end_date' => $todayStr])) }}"
                        class="px-3.5 py-1.5 rounded-xl transition-all cursor-pointer {{ request('start_date') === $todayStr && request('end_date') === $todayStr ? 'bg-indigo-600 text-white font-bold shadow-xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200' }}"
                    >
                        Hari Ini
                    </a>

                    <!-- 7 Hari Terakhir -->
                    <a
                        href="{{ route('incomes.index', array_merge(request()->except(['page']), ['start_date' => $weekAgoStr, 'end_date' => $todayStr])) }}"
                        class="px-3.5 py-1.5 rounded-xl transition-all cursor-pointer {{ request('start_date') === $weekAgoStr && request('end_date') === $todayStr ? 'bg-indigo-600 text-white font-bold shadow-xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200' }}"
                    >
                        7 Hari Terakhir
                    </a>

                    <!-- Bulan Ini -->
                    <a
                        href="{{ route('incomes.index', array_merge(request()->except(['page']), ['start_date' => $monthStartStr, 'end_date' => $monthEndStr])) }}"
                        class="px-3.5 py-1.5 rounded-xl transition-all cursor-pointer {{ request('start_date') === $monthStartStr && request('end_date') === $monthEndStr ? 'bg-indigo-600 text-white font-bold shadow-xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200' }}"
                    >
                        Bulan Ini
                    </a>
                </div>

                @if($isFiltered)
                    <a 
                        href="{{ route('incomes.index') }}" 
                        class="text-xs text-rose-600 hover:text-rose-700 font-bold flex items-center gap-1 shrink-0 px-2.5 py-1 rounded-xl bg-rose-50 dark:bg-rose-950/50 border border-rose-200 dark:border-rose-800"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        <span>Reset Filter</span>
                    </a>
                @endif
            </div>

            <!-- Form Filter Input Fields -->
            <form method="GET" action="{{ route('incomes.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3.5 pt-3 border-t border-slate-100 dark:border-slate-800">
                <!-- Pencarian (4 Col) -->
                <div class="sm:col-span-2 lg:col-span-4">
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Cari Transaksi</label>
                    <div class="relative">
                        <input
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Ketik invoice, nama produk, no tujuan..."
                            class="w-full pl-9 pr-3 py-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-slate-800 focus:bg-white text-slate-900 dark:text-white placeholder-slate-400 text-xs font-medium focus:ring-2 focus:ring-indigo-500 shadow-2xs"
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
                        class="block w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-slate-800 focus:bg-white text-slate-900 dark:text-slate-100 text-xs font-medium py-2 px-3 focus:ring-2 focus:ring-indigo-500 shadow-2xs"
                    >
                        <option value="">Semua Kategori</option>
                        @foreach($cleanCategories as $cat)
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
                        class="block w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-slate-800 focus:bg-white text-slate-900 dark:text-slate-100 text-xs font-medium py-2 px-3 focus:ring-2 focus:ring-indigo-500 shadow-2xs"
                    >
                        <option value="">Semua Metode</option>
                        <option value="cash" {{ request('payment_method') === 'cash' ? 'selected' : '' }}>Tunai (Cash)</option>
                        <option value="qris" {{ request('payment_method') === 'qris' ? 'selected' : '' }}>QRIS / E-Wallet</option>
                        <option value="transfer" {{ request('payment_method') === 'transfer' ? 'selected' : '' }}>Transfer Bank</option>
                    </select>
                </div>

                <!-- Dari Tanggal (2 Col) -->
                <div class="lg:col-span-2">
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Dari Tanggal</label>
                    <input
                        type="date"
                        name="start_date"
                        value="{{ request('start_date') }}"
                        class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-slate-800 focus:bg-white text-slate-900 dark:text-white text-xs font-medium focus:ring-2 focus:ring-indigo-500 shadow-2xs"
                    >
                </div>

                <!-- Sampai Tanggal & Tombol Terapkan (2 Col) -->
                <div class="lg:col-span-2">
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Sampai Tanggal</label>
                    <div class="flex items-center gap-1.5">
                        <input
                            type="date"
                            name="end_date"
                            value="{{ request('end_date') }}"
                            class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-slate-800 focus:bg-white text-slate-900 dark:text-white text-xs font-medium focus:ring-2 focus:ring-indigo-500 shadow-2xs"
                        >
                        <button
                            type="submit"
                            class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs uppercase tracking-wider transition-colors shrink-0 shadow-xs cursor-pointer"
                        >
                            Cari
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- ================================================================= -->
        <!-- 4. TABEL RIWAYAT TRANSAKSI KASIR BERSIH (LIGHT THEME)             -->
        <!-- ================================================================= -->
        <div class="rounded-3xl bg-white dark:bg-slate-850 border border-slate-200/90 dark:border-slate-800 shadow-sm overflow-hidden">
            <!-- Header Kartu Tabel -->
            <div class="p-5 sm:p-6 border-b border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 bg-slate-50/60 dark:bg-slate-800/40">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-indigo-100 text-indigo-600 dark:bg-indigo-950/60 dark:text-indigo-400 flex items-center justify-center font-bold text-sm shadow-2xs">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-black uppercase tracking-wider text-slate-900 dark:text-white">
                            Riwayat Transaksi Penjualan Kasir
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Daftar rekaman penerimaan uang, biaya modal, dan laba riil per faktur</p>
                    </div>
                </div>
                
                <div class="text-xs text-slate-500 dark:text-slate-400 font-mono self-start sm:self-auto">
                    Menampilkan <strong class="text-slate-900 dark:text-white font-bold">{{ $incomes->firstItem() ?? 0 }}-{{ $incomes->lastItem() ?? 0 }}</strong> dari <strong class="text-slate-900 dark:text-white font-bold">{{ $incomes->total() }}</strong> faktur
                </div>
            </div>

            <!-- Tampilan Desktop: Tabel Lengkap -->
            <div class="hidden md:block overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200/80 dark:divide-slate-800 text-left text-xs">
                    <!-- Head Tabel Terang (Light Header) -->
                    <thead class="bg-slate-50/90 dark:bg-slate-800/90 text-slate-500 dark:text-slate-400 uppercase font-black tracking-wider text-[11px] border-b border-slate-200 dark:border-slate-800">
                        <tr>
                            <th class="px-6 py-4">Faktur & Waktu</th>
                            <th class="px-5 py-4">Item Transaksi & No. Tujuan</th>
                            <th class="px-4 py-4">Kategori</th>
                            <th class="px-4 py-4">Metode Bayar</th>
                            <th class="px-4 py-4 text-right">Modal Kulakan</th>
                            <th class="px-4 py-4 text-right">Total Bayar (Omzet)</th>
                            <th class="px-5 py-4 text-right">Laba Bersih</th>
                            <th class="px-6 py-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80 font-medium">
                        @forelse($incomes as $inc)
                            <tr class="hover:bg-indigo-50/40 dark:hover:bg-slate-800/50 transition-colors group">
                                <!-- 1. Faktur & Waktu -->
                                <td class="px-6 py-4.5">
                                    <a href="{{ route('incomes.show', $inc) }}" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-indigo-50 dark:bg-indigo-950/80 text-indigo-700 dark:text-indigo-300 font-mono font-black border border-indigo-200/80 dark:border-indigo-800/60 text-xs hover:bg-indigo-100 transition-colors">
                                        <span>{{ $inc->invoice_number }}</span>
                                    </a>
                                    <div class="flex items-center gap-1 text-[11px] text-slate-500 dark:text-slate-400 mt-1 font-normal">
                                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        <span>@tanggalWaktu($inc->transaction_date)</span>
                                    </div>
                                </td>

                                <!-- 2. Item Terjual & No Tujuan -->
                                <td class="px-5 py-4.5">
                                    @if($inc->items->isNotEmpty())
                                        @foreach($inc->items as $it)
                                            <div class="font-bold text-slate-900 dark:text-white text-xs leading-snug flex items-center gap-1.5">
                                                <span>{{ $it->product_name }}</span>
                                                @if($it->quantity > 1)
                                                    <span class="text-[10px] text-indigo-600 dark:text-indigo-400 font-mono font-black px-1.5 py-0.2 rounded-md bg-indigo-50 dark:bg-indigo-950/80 border border-indigo-200 dark:border-indigo-800">
                                                        {{ $it->quantity }}x
                                                    </span>
                                                @endif
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="font-bold text-slate-900 dark:text-white text-xs leading-snug">
                                            {{ $inc->category->name ?? 'Transaksi Penjualan' }}
                                        </div>
                                    @endif

                                    @if($inc->notes)
                                        <div class="mt-1 text-[11px] text-slate-500 dark:text-slate-400 font-mono flex items-center gap-1 truncate max-w-xs">
                                            <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/></svg>
                                            <span class="truncate">{{ $inc->notes }}</span>
                                        </div>
                                    @endif
                                </td>

                                <!-- 3. Kategori Transaksi -->
                                <td class="px-4 py-4.5">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-xl text-[11px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200/80 dark:border-slate-700">
                                        {{ $inc->category->name ?? 'Umum' }}
                                    </span>
                                </td>

                                <!-- 4. Metode Bayar (Icon Vektor Rapi) -->
                                <td class="px-4 py-4.5">
                                    @if($inc->payment_method === 'cash')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-[11px] font-bold bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/60 shadow-2xs">
                                            <svg class="w-3.5 h-3.5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                            <span>Tunai</span>
                                        </span>
                                    @elseif($inc->payment_method === 'qris')
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

                                <!-- 5. Harga Modal (HPP) -->
                                <td class="px-4 py-4.5 text-right font-mono text-slate-500 dark:text-slate-400 font-semibold text-xs">
                                    @if($inc->cost_price > 0)
                                        @rupiah($inc->cost_price)
                                    @else
                                        <span class="text-slate-400 text-xs">-</span>
                                    @endif
                                </td>

                                <!-- 6. Total Bayar Pelanggan (Omzet) -->
                                <td class="px-4 py-4.5 text-right font-mono font-black text-slate-900 dark:text-white text-sm">
                                    @rupiah($inc->total_amount)
                                </td>

                                <!-- 7. Laba Bersih Transaksi -->
                                <td class="px-5 py-4.5 text-right">
                                    @if($inc->profit >= 0)
                                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-xl text-xs font-black text-emerald-800 dark:text-emerald-200 bg-emerald-100/90 dark:bg-emerald-950/80 border border-emerald-300 dark:border-emerald-700 font-mono shadow-2xs">
                                            + @rupiah($inc->profit)
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-xl text-xs font-black text-rose-800 dark:text-rose-200 bg-rose-100/90 dark:bg-rose-950/80 border border-rose-300 dark:border-rose-700 font-mono shadow-2xs">
                                            - @rupiah(abs($inc->profit))
                                        </span>
                                    @endif
                                </td>

                                <!-- 8. Tombol Aksi Cepat (Icon Vektor) -->
                                <td class="px-6 py-4.5 text-center">
                                    <div class="inline-flex items-center gap-1">
                                        <!-- Lihat Detail -->
                                        <a
                                            href="{{ route('incomes.show', $inc) }}"
                                            class="p-2 rounded-xl text-slate-500 hover:text-indigo-600 hover:bg-indigo-50 dark:hover:bg-slate-750 transition-colors"
                                            title="Lihat Detail Faktur"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        </a>

                                        <!-- Edit Transaksi -->
                                        <a
                                            href="{{ route('incomes.edit', $inc) }}"
                                            class="p-2 rounded-xl text-slate-500 hover:text-amber-600 hover:bg-amber-50 dark:hover:bg-slate-750 transition-colors"
                                            title="Edit Transaksi (Harga, Kategori, Metode, Waktu)"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </a>

                                        <!-- Batalkan Transaksi -->
                                        <button
                                            type="button"
                                            @click="$dispatch('open-modal', 'cancel-income-{{ $inc->id }}')"
                                            class="p-2 rounded-xl text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/30 transition-colors cursor-pointer"
                                            title="Batalkan & Kembalikan Saldo/Stok"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-14">
                                    <x-empty-state
                                        title="Belum Ada Transaksi Pemasukan"
                                        description="Tidak ditemukan riwayat kas masuk pada kriteria pencarian ini. Anda dapat mencatat transaksi pulsa, paket data, atau layanan kasir baru."
                                    >
                                        <x-slot:action>
                                            <x-button href="{{ route('incomes.create') }}" variant="primary" size="sm" class="font-bold">
                                                + Catat Pemasukan Baru
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
                @forelse($incomes as $inc)
                    <div class="p-4.5 space-y-3 hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors">
                        <div class="flex items-center justify-between">
                            <a href="{{ route('incomes.show', $inc) }}" class="font-mono font-black text-xs text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-950/60 px-2 py-0.5 rounded-lg border border-indigo-200 dark:border-indigo-800">
                                {{ $inc->invoice_number }}
                            </a>
                            <div class="flex items-center gap-1 text-[11px] text-slate-400 font-medium">
                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span>@tanggalWaktu($inc->transaction_date)</span>
                            </div>
                        </div>

                        <div>
                            @if($inc->items->isNotEmpty())
                                @foreach($inc->items as $it)
                                    <h4 class="font-bold text-slate-900 dark:text-white text-xs leading-snug">
                                        {{ $it->product_name }} <span class="text-indigo-600 dark:text-indigo-400 font-mono font-black">({{ $it->quantity }}x)</span>
                                    </h4>
                                @endforeach
                            @else
                                <h4 class="font-bold text-slate-900 dark:text-white text-xs leading-snug">
                                    {{ $inc->category->name ?? 'Transaksi Penjualan' }}
                                </h4>
                            @endif

                            @if($inc->notes)
                                <p class="text-[11px] text-slate-400 mt-1 truncate flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/></svg>
                                    <span class="truncate">{{ $inc->notes }}</span>
                                </p>
                            @endif
                        </div>

                        <div class="flex items-center justify-between text-xs pt-2 border-t border-slate-100 dark:border-slate-800">
                            <div>
                                <span class="text-[10px] text-slate-400 uppercase tracking-wider block font-bold">Total Omzet:</span>
                                <strong class="text-slate-900 dark:text-white font-mono text-sm font-black">@rupiah($inc->total_amount)</strong>
                            </div>
                            <div class="text-right">
                                <span class="text-[10px] text-slate-400 uppercase tracking-wider block font-bold">Laba Bersih:</span>
                                <span class="font-mono font-black text-xs text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/60 px-2 py-0.5 rounded-lg border border-emerald-200 dark:border-emerald-800">
                                    + @rupiah($inc->profit)
                                </span>
                            </div>
                        </div>

                        <div class="flex items-center justify-between pt-2">
                            <span class="text-[11px] font-bold px-2.5 py-1 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
                                {{ $inc->category->name ?? 'Umum' }}
                            </span>

                            <div class="flex items-center gap-2">
                                <a href="{{ route('incomes.show', $inc) }}" class="px-3 py-1.5 text-xs rounded-xl border border-slate-200 dark:border-slate-700 font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-50">
                                    Detail
                                </a>
                                <a href="{{ route('incomes.edit', $inc) }}" class="px-3 py-1.5 text-xs rounded-xl border border-amber-200 dark:border-amber-800 font-bold text-amber-700 dark:text-amber-300 bg-amber-50 dark:bg-amber-950/40">
                                    Edit
                                </a>
                                <button
                                    type="button"
                                    @click="$dispatch('open-modal', 'cancel-income-{{ $inc->id }}')"
                                    class="px-3 py-1.5 text-xs rounded-xl border border-rose-200 dark:border-rose-800 font-bold text-rose-700 dark:text-rose-300 bg-rose-50 dark:bg-rose-950/40 hover:bg-rose-100 transition-colors cursor-pointer"
                                >
                                    Batal
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-6 text-center text-xs text-slate-400">
                        Tidak ada transaksi ditemukan pada kriteria ini.
                    </div>
                @endforelse
            </div>

            <!-- Pagination Bar -->
            <div class="p-5 sm:p-6 bg-slate-50/50 dark:bg-slate-800/20 border-t border-slate-100 dark:border-slate-800">
                {{ $incomes->links() }}
            </div>
        </div>

        <!-- Modal Konfirmasi Pembatalan Transaksi (Desktop & Mobile) -->
        @foreach($incomes as $inc)
            @php
                $refundAmount = $inc->cost_price > 0 ? $inc->cost_price : $inc->total_amount;
            @endphp
            <x-modal name="cancel-income-{{ $inc->id }}" title="Konfirmasi Pembatalan Transaksi">
                <div class="text-left text-sm text-slate-600 dark:text-slate-400 space-y-3">
                    <p>Apakah Anda yakin ingin membatalkan transaksi pemasukan <strong>{{ $inc->invoice_number }}</strong> senilai <strong>@rupiah($inc->total_amount)</strong>?</p>
                    
                    <!-- Informasi Pengembalian Saldo Server -->
                    <div class="p-3.5 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 rounded-xl text-emerald-800 dark:text-emerald-300 text-xs flex items-start gap-2.5">
                        <svg class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                        <div>
                            <strong class="font-bold">Pengembalian Saldo Server (Refund):</strong>
                            <p class="mt-0.5">Saldo deposit server Anda sebesar <span class="font-bold font-mono">@rupiah($refundAmount)</span> akan secara otomatis <strong class="underline decoration-emerald-500">dikembalikan (refund)</strong> ke saldo server aplikasi.</p>
                        </div>
                    </div>

                    @if($inc->items->isNotEmpty())
                        <div class="p-3.5 bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 rounded-xl text-amber-800 dark:text-amber-300 text-xs flex items-start gap-2.5">
                            <svg class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            <div>
                                <strong class="font-bold">Perlindungan Stok:</strong>
                                <p class="mt-0.5">Stok produk fisik pada transaksi ini akan secara otomatis dikembalikan ke inventaris konter secara aman.</p>
                            </div>
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
                            Ya, Batalkan & Kembalikan Saldo
                        </x-button>
                    </form>
                </x-slot:footer>
            </x-modal>
        @endforeach
    </div>
</x-layouts.app>
