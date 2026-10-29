<x-layouts.app title="Buku Kasbon & Piutang">
    <x-slot:header>Buku Kasbon & Piutang</x-slot:header>

    <div
        class="space-y-6"
        x-data="kasbonPage()"
    >
        <!-- ================================================================= -->
        <!-- 1. HEADER HALAMAN & TOMBOL TAMBAH KASBON                         -->
        <!-- ================================================================= -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white dark:bg-slate-850 p-6 rounded-3xl border border-slate-200/90 dark:border-slate-800 shadow-xs">
            <div class="space-y-1">
                <div class="flex items-center gap-2.5">
                    <span class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-amber-500 to-amber-400 text-white flex items-center justify-center shadow-md shadow-amber-500/25">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </span>
                    <h1 class="text-xl sm:text-2xl font-black tracking-tight text-slate-900 dark:text-white">
                        Buku Kasbon & Piutang Pelanggan
                    </h1>
                </div>
                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 pl-1">
                    Catat siapa yang berhutang, produk yang diambil, serta laba riil yang otomatis masuk ke pembukuan saat dilunasi.
                </p>
            </div>

            <!-- Tombol Tambah Kasbon Baru -->
            <button
                type="button"
                @click="createModalOpen = true"
                class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm shadow-md shadow-indigo-600/25 transition-all hover:scale-[1.02] active:scale-[0.98] cursor-pointer shrink-0"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 4v16m8-8H4"/>
                </svg>
                <span>+ Catat Kasbon Baru</span>
            </button>
        </div>

        <!-- ================================================================= -->
        <!-- 2. DASHBOARD RINGKASAN METRIK KASBON                              -->
        <!-- ================================================================= -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
            <!-- 1. Total Hutang Belum Lunas & Estimasi Laba Tertahan -->
            <div class="p-5 rounded-3xl bg-gradient-to-br from-amber-50/90 via-white to-orange-50/40 dark:from-amber-950/40 dark:via-slate-850 dark:to-slate-850 border border-amber-200/80 dark:border-amber-900/50 shadow-xs relative overflow-hidden">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-amber-900 dark:text-amber-300 uppercase tracking-wider">Total Hutang Menggantung</span>
                    <div class="w-10 h-10 rounded-2xl bg-amber-500 text-white flex items-center justify-center shadow-md shadow-amber-500/25">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
                <div class="mt-3">
                    <div class="text-2xl font-black font-mono tracking-tight text-amber-600 dark:text-amber-400">
                        @rupiah($totalUnpaidAmount)
                    </div>
                    <div class="mt-2 text-xs text-slate-500 dark:text-slate-400 flex items-center justify-between">
                        <span>Laba Tertahan:</span>
                        <span class="font-bold font-mono px-2 py-0.5 rounded-full bg-amber-100 dark:bg-amber-950 text-amber-800 dark:text-amber-300 text-[11px]">
                            +@rupiah($totalUnpaidProfit)
                        </span>
                    </div>
                    <div class="mt-1.5 text-[11px] text-slate-400 flex items-center justify-between">
                        <span>Belum dibayar:</span>
                        <span class="font-semibold text-slate-700 dark:text-slate-300">{{ $totalUnpaidCount }} Pelanggan</span>
                    </div>
                </div>
            </div>

            <!-- 2. Total Kasbon Terlunasi & Laba Masuk Pembukuan -->
            <div class="p-5 rounded-3xl bg-gradient-to-br from-emerald-50/90 via-white to-teal-50/40 dark:from-emerald-950/40 dark:via-slate-850 dark:to-slate-850 border border-emerald-200/80 dark:border-emerald-900/50 shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-emerald-900 dark:text-emerald-300 uppercase tracking-wider">Kasbon Sudah Lunas</span>
                    <div class="w-10 h-10 rounded-2xl bg-emerald-600 text-white flex items-center justify-center shadow-md shadow-emerald-600/25">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
                <div class="mt-3">
                    <div class="text-2xl font-black font-mono tracking-tight text-emerald-600 dark:text-emerald-400">
                        @rupiah($totalPaidAmount)
                    </div>
                    <div class="mt-2 text-xs text-slate-500 dark:text-slate-400 flex items-center justify-between">
                        <span>Laba Terealisasi:</span>
                        <span class="font-bold font-mono px-2 py-0.5 rounded-full bg-emerald-100 dark:bg-emerald-950 text-emerald-800 dark:text-emerald-300 text-[11px]">
                            +@rupiah($totalPaidProfit)
                        </span>
                    </div>
                    <div class="mt-1.5 text-[11px] text-slate-400 flex items-center justify-between">
                        <span>Sudah masuk kas:</span>
                        <span class="font-semibold text-emerald-700 dark:text-emerald-300">{{ $totalPaidCount }} Transaksi</span>
                    </div>
                </div>
            </div>

            <!-- 3. Lewat Jatuh Tempo (Overdue) -->
            <div class="p-5 rounded-3xl bg-gradient-to-br from-rose-50/90 via-white to-pink-50/40 dark:from-rose-950/40 dark:via-slate-850 dark:to-slate-850 border border-rose-200/80 dark:border-rose-900/50 shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-rose-900 dark:text-rose-300 uppercase tracking-wider">Lewat Jatuh Tempo</span>
                    <div class="w-10 h-10 rounded-2xl bg-rose-600 text-white flex items-center justify-center shadow-md shadow-rose-600/25">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                </div>
                <div class="mt-3">
                    <div class="text-2xl font-black font-mono tracking-tight {{ $overdueCount > 0 ? 'text-rose-600 dark:text-rose-400' : 'text-slate-700 dark:text-slate-300' }}">
                        {{ $overdueCount }} Orang
                    </div>
                    <div class="mt-2 text-xs text-slate-500 dark:text-slate-400 flex items-center justify-between">
                        <span>Lewat tanggal janji bayar</span>
                        <span class="font-bold text-[11px] {{ $overdueCount > 0 ? 'text-rose-600 font-semibold' : 'text-slate-400' }}">
                            {{ $overdueCount > 0 ? 'Perlu Ditagih' : 'Semua Aman' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- 4. Kasbon Baru Hari Ini -->
            <div class="p-5 rounded-3xl bg-gradient-to-br from-indigo-50/90 via-white to-sky-50/40 dark:from-indigo-950/40 dark:via-slate-850 dark:to-slate-850 border border-indigo-200/80 dark:border-indigo-900/50 shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-indigo-900 dark:text-indigo-300 uppercase tracking-wider">Hutang Baru Hari Ini</span>
                    <div class="w-10 h-10 rounded-2xl bg-indigo-600 text-white flex items-center justify-center shadow-md shadow-indigo-600/25">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                </div>
                <div class="mt-3">
                    <div class="text-2xl font-black font-mono tracking-tight text-indigo-600 dark:text-indigo-400">
                        {{ $todayDebtsCount }} Catatan
                    </div>
                    <div class="mt-2 text-xs text-slate-500 dark:text-slate-400 flex items-center justify-between">
                        <span>Tanggal: {{ date('d/m/Y') }}</span>
                        <span class="font-bold text-[11px] text-indigo-600">Aktivitas Hari Ini</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- ================================================================= -->
        <!-- 3. INFO LOGIKA KEUANGAN (CASH BASIS SAAT BAYAR)                   -->
        <!-- ================================================================= -->
        <div class="p-4 sm:p-5 rounded-3xl bg-gradient-to-r from-indigo-50/80 via-white to-sky-50/80 dark:from-indigo-950/30 dark:via-slate-850 dark:to-slate-850 border border-indigo-200/80 dark:border-indigo-900/50 text-xs">
            <div class="flex items-start gap-3">
                <div class="w-8 h-8 rounded-xl bg-indigo-600 text-white flex items-center justify-center shrink-0 shadow-sm shadow-indigo-600/30">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div class="space-y-0.5">
                    <h4 class="font-black text-slate-900 dark:text-white text-xs sm:text-sm">
                        Logika Pembukuan Kasbon: Pemasukan & Laba Bersih Masuk Pada Hari Kasbon Dilunasi
                    </h4>
                    <p class="text-slate-600 dark:text-slate-300 leading-relaxed text-[11px] sm:text-xs">
                        Saat kasbon dicatat hari ini, uang belum diterima sehingga <strong>tidak dihitung sebagai pemasukan atau laba hari ini</strong>. Begitu kasbon <strong>dilunasi (misal besok)</strong>, omzet pemasukan, modal HPP, dan laba bersih secara otomatis <strong>masuk ke laporan & dashboard tepat pada tanggal pembayaran tersebut</strong>.
                    </p>
                </div>
            </div>
        </div>

        <!-- ================================================================= -->
        <!-- 4. TOOLBAR FILTER & PENCARIAN                                     -->
        <!-- ================================================================= -->
        <div class="p-5 sm:p-6 rounded-3xl bg-white dark:bg-slate-850 border border-slate-200/90 dark:border-slate-800 shadow-xs space-y-4">
            <!-- Filter Tabs Status -->
            <div class="flex items-center gap-2 overflow-x-auto pb-1 scrollbar-thin text-xs font-bold border-b border-slate-200 dark:border-slate-800">
                <!-- Semua -->
                <a
                    href="{{ route('kasbons.index', array_merge(request()->except(['page']), ['status' => 'all'])) }}"
                    class="px-4 py-2.5 rounded-2xl flex items-center gap-2 transition-all shrink-0 {{ $status === 'all' ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/25' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200' }}"
                >
                    <span>Semua Kasbon</span>
                    <span class="px-2 py-0.5 rounded-full text-[11px] {{ $status === 'all' ? 'bg-indigo-700 text-white' : 'bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300' }}">
                        {{ $totalUnpaidCount + $totalPaidCount }}
                    </span>
                </a>

                <!-- Belum Lunas -->
                <a
                    href="{{ route('kasbons.index', array_merge(request()->except(['page']), ['status' => 'unpaid'])) }}"
                    class="px-4 py-2.5 rounded-2xl flex items-center gap-2 transition-all shrink-0 {{ $status === 'unpaid' ? 'bg-amber-500 text-slate-900 font-black shadow-md shadow-amber-500/25' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200' }}"
                >
                    <span class="w-2 h-2 rounded-full bg-amber-500 {{ $status === 'unpaid' ? 'bg-slate-900' : '' }}"></span>
                    <span>Belum Lunas (Prioritas)</span>
                    <span class="px-2 py-0.5 rounded-full text-[11px] {{ $status === 'unpaid' ? 'bg-slate-900 text-amber-300' : 'bg-amber-100 text-amber-800' }}">
                        {{ $totalUnpaidCount }}
                    </span>
                </a>

                <!-- Sudah Lunas -->
                <a
                    href="{{ route('kasbons.index', array_merge(request()->except(['page']), ['status' => 'paid'])) }}"
                    class="px-4 py-2.5 rounded-2xl flex items-center gap-2 transition-all shrink-0 {{ $status === 'paid' ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/25' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200' }}"
                >
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    <span>Sudah Lunas</span>
                    <span class="px-2 py-0.5 rounded-full text-[11px] {{ $status === 'paid' ? 'bg-emerald-700 text-white' : 'bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300' }}">
                        {{ $totalPaidCount }}
                    </span>
                </a>
            </div>

            <!-- Form Pencarian & Tanggal Berhutang -->
            <form method="GET" action="{{ route('kasbons.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 text-xs">
                <input type="hidden" name="status" value="{{ $status }}">

                <!-- Input Pencarian -->
                <div class="lg:col-span-5 relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </span>
                    <input
                        type="text"
                        name="search"
                        value="{{ $search }}"
                        placeholder="Cari nama pelanggan, produk, telepon..."
                        class="w-full pl-10 pr-4 py-2.5 rounded-2xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none transition-all"
                    >
                </div>

                <!-- Dari Tanggal Berhutang -->
                <div class="lg:col-span-3">
                    <div class="flex items-center gap-1.5">
                        <span class="text-slate-400 font-bold shrink-0">Hutang Dari:</span>
                        <input
                            type="date"
                            name="start_date"
                            value="{{ $startDate }}"
                            class="w-full px-3 py-2.5 rounded-2xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none transition-all"
                        >
                    </div>
                </div>

                <!-- Sampai Tanggal Berhutang -->
                <div class="lg:col-span-3">
                    <div class="flex items-center gap-1.5">
                        <span class="text-slate-400 font-bold shrink-0">S/d:</span>
                        <input
                            type="date"
                            name="end_date"
                            value="{{ $endDate }}"
                            class="w-full px-3 py-2.5 rounded-2xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none transition-all"
                        >
                    </div>
                </div>

                <!-- Tombol Submit & Reset -->
                <div class="lg:col-span-1 flex items-center gap-2">
                    <button
                        type="submit"
                        class="w-full py-2.5 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold transition-all shadow-sm"
                        title="Terapkan Filter"
                    >
                        Filter
                    </button>
                    @if($search || $startDate || $endDate || $status !== 'all')
                        <a
                            href="{{ route('kasbons.index') }}"
                            class="p-2.5 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-600 dark:bg-slate-800 dark:text-slate-300 transition-all"
                            title="Reset Filter"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- ================================================================= -->
        <!-- 5. TABEL CATATAN KASBON, PRODUK, NOMINAL & LABA                   -->
        <!-- ================================================================= -->
        <div class="bg-white dark:bg-slate-850 rounded-3xl border border-slate-200/90 dark:border-slate-800 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="bg-slate-50/80 dark:bg-slate-800/60 border-b border-slate-200/80 dark:border-slate-800 text-slate-500 dark:text-slate-400 font-bold uppercase tracking-wider text-[11px]">
                            <th class="py-4 px-4 sm:px-6">Tanggal Berhutang</th>
                            <th class="py-4 px-4 sm:px-6">Pelanggan / Peminjam</th>
                            <th class="py-4 px-4 sm:px-6">Produk & Rincian</th>
                            <th class="py-4 px-4 sm:px-6 text-right">Nominal Hutang</th>
                            <th class="py-4 px-4 sm:px-6 text-right">Laba Keuntungan</th>
                            <th class="py-4 px-4 sm:px-6 text-center">Status Pembayaran</th>
                            <th class="py-4 px-4 sm:px-6 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($kasbons as $k)
                            <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition-colors {{ $k->is_paid ? 'opacity-85' : '' }}">
                                <!-- 1. TANGGAL BERHUTANG & JATUH TEMPO -->
                                <td class="py-4 px-4 sm:px-6 whitespace-nowrap">
                                    <div class="space-y-1">
                                        <div class="flex items-center gap-2">
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-xl text-xs font-black font-mono {{ $k->is_paid ? 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300' : 'bg-amber-100/90 dark:bg-amber-950 text-amber-900 dark:text-amber-200 border border-amber-200 dark:border-amber-900' }}">
                                                📅 @tanggal($k->debt_date)
                                            </span>
                                        </div>
                                        <div class="text-[11px] text-slate-400 flex items-center gap-1.5 pl-1">
                                            <span>{{ \Carbon\Carbon::parse($k->debt_date)->diffForHumans() }}</span>
                                        </div>

                                        @if($k->due_date)
                                            <div class="text-[11px] flex items-center gap-1 pt-0.5 pl-1">
                                                <span class="text-slate-400">Jatuh tempo:</span>
                                                <span class="font-semibold {{ $k->is_overdue ? 'text-rose-600 dark:text-rose-400 font-bold' : 'text-slate-700 dark:text-slate-300' }}">
                                                    @tanggal($k->due_date)
                                                </span>
                                                @if($k->is_overdue)
                                                    <span class="px-1.5 py-0.2 rounded-md bg-rose-100 text-rose-700 text-[10px] font-black uppercase">Lewat!</span>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </td>

                                <!-- 2. NAMA PELANGGAN & NOMOR KONTAK -->
                                <td class="py-4 px-4 sm:px-6 whitespace-nowrap">
                                    <div class="space-y-1">
                                        <div class="font-black text-slate-900 dark:text-white text-sm flex items-center gap-2">
                                            <span>{{ $k->customer_name }}</span>
                                            @if(!$k->is_paid)
                                                <span class="w-2 h-2 rounded-full bg-amber-500" title="Belum Lunas"></span>
                                            @endif
                                        </div>

                                        @if($k->customer_phone)
                                            <div class="flex items-center gap-2">
                                                <span class="text-slate-500 font-mono text-[11px]">{{ $k->customer_phone }}</span>
                                                @if(!$k->is_paid && $k->whatsapp_reminder_url)
                                                    <a
                                                        href="{{ $k->whatsapp_reminder_url }}"
                                                        target="_blank"
                                                        rel="noopener noreferrer"
                                                        class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg bg-emerald-100 hover:bg-emerald-200 text-emerald-800 text-[10px] font-bold transition-all shadow-2xs"
                                                        title="Kirim pengingat tagihan via WhatsApp"
                                                    >
                                                        <span>📲 Chat WA</span>
                                                    </a>
                                                @endif
                                            </div>
                                        @endif

                                        <div class="text-[10px] text-slate-400">
                                            Kasir: {{ $k->user->name ?? 'Kasir' }}
                                        </div>
                                    </div>
                                </td>

                                <!-- 3. PRODUK & RINCIAN BARANG -->
                                <td class="py-4 px-4 sm:px-6 max-w-xs">
                                    <div class="space-y-1">
                                        <div class="font-bold text-slate-900 dark:text-white flex items-center gap-1.5 flex-wrap">
                                            <span>{{ $k->product_name ?: ($k->description ?: 'Kasbon Barang') }}</span>
                                            @if($k->quantity > 1)
                                                <span class="px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-mono font-bold text-[10px]">
                                                    {{ $k->quantity }}x
                                                </span>
                                            @endif
                                        </div>

                                        <!-- Info Modal HPP -->
                                        @if($k->cost_price > 0)
                                            <div class="text-[11px] text-slate-500 dark:text-slate-400 flex items-center gap-2">
                                                <span>Modal HPP:</span>
                                                <span class="font-mono text-slate-700 dark:text-slate-300 font-semibold">@rupiah($k->cost_price * ($k->quantity ?: 1))</span>
                                            </div>
                                        @endif

                                        @if($k->notes)
                                            <div class="text-[11px] text-slate-400 italic">
                                                "{{ $k->notes }}"
                                            </div>
                                        @endif
                                    </div>
                                </td>

                                <!-- 4. NOMINAL HUTANG -->
                                <td class="py-4 px-4 sm:px-6 text-right whitespace-nowrap">
                                    <div class="text-base font-black font-mono tracking-tight {{ $k->is_paid ? 'text-slate-600 dark:text-slate-400 line-through' : 'text-amber-600 dark:text-amber-400' }}">
                                        @rupiah($k->amount)
                                    </div>
                                    <div class="text-[10px] text-slate-400">
                                        Harga Jual
                                    </div>
                                </td>

                                <!-- 5. LABA KEUNTUNGAN -->
                                <td class="py-4 px-4 sm:px-6 text-right whitespace-nowrap">
                                    <div class="inline-flex flex-col items-end">
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl text-xs font-black font-mono {{ $k->is_paid ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300' }}">
                                            <span>+@rupiah($k->profit)</span>
                                        </span>
                                        <span class="text-[10px] text-slate-400 mt-0.5">
                                            {{ $k->is_paid ? 'Laba Masuk Kas' : 'Estimasi Laba' }}
                                        </span>
                                    </div>
                                </td>

                                <!-- 6. STATUS PEMBAYARAN -->
                                <td class="py-4 px-4 sm:px-6 text-center whitespace-nowrap">
                                    @if($k->is_paid)
                                        <div class="inline-flex flex-col items-center">
                                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-xl text-xs font-black bg-emerald-100 dark:bg-emerald-950 text-emerald-800 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                                <span>LUNAS</span>
                                            </span>
                                            @if($k->paid_at)
                                                <span class="text-[10px] text-emerald-700 dark:text-emerald-400 font-bold mt-1 font-mono">
                                                    Dibayar: @tanggal($k->paid_at)
                                                </span>
                                            @endif
                                            @if($k->payment_method)
                                                <span class="text-[10px] font-bold text-slate-500 uppercase mt-0.5">
                                                    Via: {{ $k->payment_method }}
                                                </span>
                                            @endif
                                        </div>
                                    @else
                                        <div class="inline-flex flex-col items-center">
                                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-xl text-xs font-black bg-amber-100 dark:bg-amber-950 text-amber-900 dark:text-amber-200 border border-amber-200 dark:border-amber-800">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-ping"></span>
                                                <span>BELUM LUNAS</span>
                                            </span>
                                            <span class="text-[10px] text-slate-400 mt-1">
                                                Laba belum masuk kas
                                            </span>
                                        </div>
                                    @endif
                                </td>

                                <!-- 7. AKSI -->
                                <td class="py-4 px-4 sm:px-6 text-center whitespace-nowrap">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <!-- Tombol Tandai Lunas (Jika Belum Lunas) -->
                                        @if(!$k->is_paid)
                                            <button
                                                type="button"
                                                @click='openPayModal(@json($k))'
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-sm shadow-emerald-600/25 transition-all hover:scale-105 active:scale-95 cursor-pointer"
                                                title="Update Kasbon Sudah Terbayar / Lunas"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M5 13l4 4L19 7"/></svg>
                                                <span>Tandai Lunas</span>
                                            </button>
                                        @else
                                            <!-- Tombol Batalkan Lunas -->
                                            <form method="POST" action="{{ route('kasbons.unpay', $k) }}" onsubmit="return confirm('Batalkan status lunas kasbon an. {{ $k->customer_name }}? Catatan pemasukan & laba terkait akan dibatalkan.')">
                                                @csrf
                                                <button
                                                    type="submit"
                                                    class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 dark:bg-slate-800 dark:text-slate-300 text-xs font-semibold transition-all"
                                                    title="Batalkan status lunas (kembalikan ke belum lunas)"
                                                >
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                                                    <span>Batal Lunas</span>
                                                </button>
                                            </form>
                                        @endif

                                        <!-- Tombol Edit Kasbon (Hanya untuk yang belum lunas) -->
                                        @if($k->status === 'unpaid')
                                            <button
                                                type="button"
                                                @click='openEditModal(@json($k))'
                                                class="p-2 rounded-xl text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 dark:hover:bg-slate-800 transition-colors cursor-pointer"
                                                title="Edit Rincian Kasbon"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                            </button>
                                        @else
                                            <span
                                                class="p-2 rounded-xl text-slate-300 dark:text-slate-600 cursor-not-allowed inline-flex items-center"
                                                title="Kasbon berstatus LUNAS terkunci dari edit untuk menjaga akurasi laporan kasir"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                            </span>
                                        @endif

                                        <!-- Tombol Hapus Kasbon -->
                                        <form method="POST" action="{{ route('kasbons.destroy', $k) }}" onsubmit="return confirm('Yakin ingin menghapus catatan kasbon an. {{ $k->customer_name }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button
                                                type="submit"
                                                class="p-2 rounded-xl text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-slate-800 transition-colors"
                                                title="Hapus Kasbon"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 px-6 text-center">
                                    <div class="max-w-xs mx-auto space-y-3">
                                        <div class="w-16 h-16 rounded-3xl bg-amber-50 text-amber-500 mx-auto flex items-center justify-center">
                                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                            </svg>
                                        </div>
                                        <h3 class="font-black text-slate-800 dark:text-white text-sm">Tidak Ada Catatan Kasbon</h3>
                                        <p class="text-xs text-slate-400">
                                            Belum ada catatan hutang pelanggan yang cocok dengan kriteria filter saat ini.
                                        </p>
                                        <button
                                            type="button"
                                            @click="createModalOpen = true"
                                            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-indigo-600 text-white font-bold text-xs shadow-sm hover:bg-indigo-700 transition-all"
                                        >
                                            <span>+ Catat Kasbon Baru</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($kasbons->hasPages())
                <div class="p-4 border-t border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/50">
                    {{ $kasbons->links() }}
                </div>
            @endif
        </div>

        <!-- ================================================================= -->
        <!-- 6. MODAL FORMULIR TAMBAH KASBON BARU DENGAN PILIH PRODUK & LABA   -->
        <!-- ================================================================= -->
        <div
            x-show="createModalOpen"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4"
            style="display: none;"
        >
            <div
                @click.away="createModalOpen = false"
                class="bg-white dark:bg-slate-850 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-2xl max-w-xl w-full p-6 sm:p-7 space-y-5"
            >
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-4">
                    <div class="flex items-center gap-2.5">
                        <span class="w-9 h-9 rounded-2xl bg-amber-500 text-white flex items-center justify-center shadow-md shadow-amber-500/25">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 4v16m8-8H4"/></svg>
                        </span>
                        <div>
                            <h3 class="text-base font-black text-slate-900 dark:text-white">Catat Kasbon Baru</h3>
                            <p class="text-xs text-slate-400">Pilih produk, tentukan nominal hutang & lihat labanya otomatis</p>
                        </div>
                    </div>
                    <button
                        type="button"
                        @click="createModalOpen = false"
                        class="p-2 rounded-xl text-slate-400 hover:text-slate-600 hover:bg-slate-100 dark:hover:bg-slate-800"
                    >
                        ✕
                    </button>
                </div>

                <form method="POST" action="{{ route('kasbons.store') }}" class="space-y-4 text-xs">
                    @csrf

                    <!-- 1. Identitas Pelanggan (Siapa yang Kasbon) -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div class="space-y-1.5">
                            <label class="font-bold text-slate-700 dark:text-slate-200">
                                Siapa yang Kasbon? <span class="text-rose-500">*</span>
                            </label>
                            <input
                                type="text"
                                name="customer_name"
                                x-model="createCustomerName"
                                required
                                placeholder="Contoh: Pak Budi, Mas Dimas, Bu Ani"
                                class="w-full px-4 py-2.5 rounded-2xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none text-xs"
                            >
                        </div>

                        <div class="space-y-1.5">
                            <label class="font-bold text-slate-700 dark:text-slate-200">
                                No. WhatsApp / HP (Opsional)
                            </label>
                            <input
                                type="text"
                                name="customer_phone"
                                x-model="createCustomerPhone"
                                placeholder="081234567890 (bisa chat tagihan WA)"
                                class="w-full px-4 py-2.5 rounded-2xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none text-xs"
                            >
                        </div>
                    </div>

                    <!-- 2. Pilih Produk dari Katalog Konter -->
                    <div class="space-y-1.5">
                        <label class="font-bold text-slate-700 dark:text-slate-200 flex items-center justify-between">
                            <span>Produk Apa yang Dihutang?</span>
                            <span class="text-[10px] text-indigo-600 font-semibold cursor-pointer" @click="selectedProductId = ''; onProductSelect();">
                                Atur Manual / Kustom
                            </span>
                        </label>
                        <select
                            name="product_id"
                            x-model="selectedProductId"
                            @change="onProductSelect()"
                            class="w-full px-4 py-2.5 rounded-2xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none text-xs font-medium"
                        >
                            <option value="">-- Pilih Produk dari Katalog Konter (Atau Input Manual) --</option>
                            <template x-for="p in products" :key="p.id">
                                <option :value="p.id" :disabled="p.stock <= 0" x-text="p.name + ' (Stok: ' + p.stock + ' | Jual: Rp ' + Number(p.selling_price).toLocaleString('id-ID') + ' | Modal: Rp ' + Number(p.cost_price).toLocaleString('id-ID') + ')' + (p.stock <= 0 ? ' [STOK KOSONG]' : '')"></option>
                            </template>
                        </select>

                        <!-- Peringatan jika stok kurang dari Qty -->
                        <div x-show="selectedProduct && selectedProduct.stock < (parseInt(createQuantity) || 1)" class="p-2.5 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-300 dark:border-rose-800 text-rose-700 dark:text-rose-300 text-xs flex items-center gap-2" style="display: none;">
                            <span>⚠️</span>
                            <span>Peringatan: Sisa stok produk ini hanya <strong x-text="selectedProduct ? selectedProduct.stock : 0"></strong> unit. Kurangi kuantitas atau tambah stok terlebih dahulu.</span>
                        </div>
                    </div>

                    <!-- Nama Produk & Kuantitas -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div class="sm:col-span-2 space-y-1.5">
                            <label class="font-bold text-slate-700 dark:text-slate-200">
                                Nama Barang / Produk
                            </label>
                            <input
                                type="text"
                                name="product_name"
                                x-model="createProductName"
                                placeholder="Contoh: Token Listrik 20.000 / Pulsa 25rb"
                                class="w-full px-4 py-2.5 rounded-2xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none text-xs"
                            >
                        </div>

                        <div class="space-y-1.5">
                            <label class="font-bold text-slate-700 dark:text-slate-200">
                                Jumlah (Qty)
                            </label>
                            <input
                                type="number"
                                name="quantity"
                                x-model="createQuantity"
                                @input="onQuantityChange()"
                                min="1"
                                class="w-full px-4 py-2.5 rounded-2xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white font-mono font-bold focus:ring-2 focus:ring-indigo-500 outline-none text-xs text-center"
                            >
                        </div>
                    </div>

                    <!-- 3. Rincian Finansial: Modal HPP, Nominal Jual & Hitung Laba Otomatis -->
                    <div class="p-4 rounded-2xl bg-indigo-50/70 dark:bg-indigo-950/40 border border-indigo-200/80 dark:border-indigo-900/60 space-y-3">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <!-- Modal HPP Satuan -->
                            <div class="space-y-1">
                                <label class="font-bold text-slate-700 dark:text-slate-300 text-[11px]">
                                    Modal Pokok Satuan (HPP)
                                </label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400 font-bold text-xs">Rp</span>
                                    <input
                                        type="number"
                                        name="cost_price"
                                        x-model="createCostPrice"
                                        @input="calculateProfit()"
                                        step="100"
                                        min="0"
                                        class="w-full pl-9 pr-3 py-2 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white font-mono font-bold outline-none text-xs"
                                    >
                                </div>
                            </div>

                            <!-- Nominal Hutang (Harga Jual) -->
                            <div class="space-y-1">
                                <label class="font-bold text-slate-700 dark:text-slate-300 text-[11px]">
                                    Total Nominal Hutang (Harga Jual) <span class="text-rose-500">*</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400 font-bold text-xs">Rp</span>
                                    <input
                                        type="number"
                                        name="amount"
                                        x-model="createAmount"
                                        @input="calculateProfit()"
                                        required
                                        min="100"
                                        step="100"
                                        class="w-full pl-9 pr-3 py-2 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-amber-600 dark:text-amber-400 font-mono font-black outline-none text-sm"
                                    >
                                </div>
                            </div>
                        </div>

                        <!-- Panel Kalkulasi Laba Keuntungan Bersih -->
                        <div class="p-3 rounded-xl bg-white dark:bg-slate-800 border border-indigo-100 dark:border-indigo-900 flex items-center justify-between">
                            <div>
                                <span class="text-[10px] uppercase font-bold text-slate-400 block tracking-wider">Laba Keuntungan yang Didapat</span>
                                <span class="text-xs text-slate-500">Omzet &minus; Modal HPP (Masuk saat dibayar)</span>
                            </div>
                            <div class="text-right">
                                <span class="text-base font-black font-mono text-emerald-600 dark:text-emerald-400">
                                    +Rp <span x-text="Number(createProfit).toLocaleString('id-ID')">0</span>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- 4. Tanggal Berhutang & Tanggal Jatuh Tempo -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div class="space-y-1.5">
                            <label class="font-bold text-slate-700 dark:text-slate-200">
                                Tanggal Berhutang <span class="text-rose-500">*</span>
                            </label>
                            <input
                                type="date"
                                name="debt_date"
                                required
                                x-model="createDebtDate"
                                class="w-full px-3 py-2.5 rounded-2xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none text-xs"
                            >
                        </div>

                        <div class="space-y-1.5">
                            <label class="font-bold text-slate-700 dark:text-slate-200">
                                Janji Bayar (Jatuh Tempo)
                            </label>
                            <input
                                type="date"
                                name="due_date"
                                x-model="createDueDate"
                                class="w-full px-3 py-2.5 rounded-2xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none text-xs"
                            >
                        </div>
                    </div>

                    <!-- Keterangan Tambahan -->
                    <div class="space-y-1.5">
                        <label class="font-bold text-slate-700 dark:text-slate-200">
                            Catatan Tambahan (Opsional)
                        </label>
                        <input
                            type="text"
                            name="notes"
                            x-model="createNotes"
                            placeholder="Contoh: Janji bayar besok sore pas pulang kerja"
                            class="w-full px-4 py-2.5 rounded-2xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none text-xs"
                        >
                    </div>

                    <!-- Tombol Aksi Modal -->
                    <div class="pt-3 flex items-center justify-end gap-2 border-t border-slate-100 dark:border-slate-800">
                        <button
                            type="button"
                            @click="createModalOpen = false"
                            class="px-4 py-2.5 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-700 dark:bg-slate-800 dark:text-slate-300 font-bold transition-all"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            :disabled="selectedProduct && selectedProduct.stock < (parseInt(createQuantity) || 1)"
                            class="px-5 py-2.5 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white font-black shadow-md shadow-indigo-600/25 transition-all disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer"
                        >
                            Simpan Catatan Kasbon
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ================================================================= -->
        <!-- 7. MODAL PELUNASAN KASBON (UPDATE SUDAH TERBAYAR)                 -->
        <!-- ================================================================= -->
        <div
            x-show="payModalOpen"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4"
            style="display: none;"
        >
            <div
                @click.away="payModalOpen = false"
                class="bg-white dark:bg-slate-850 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-2xl max-w-md w-full p-6 sm:p-7 space-y-5"
            >
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-4">
                    <div class="flex items-center gap-2.5">
                        <span class="w-9 h-9 rounded-2xl bg-emerald-600 text-white flex items-center justify-center shadow-md shadow-emerald-600/25">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M5 13l4 4L19 7"/></svg>
                        </span>
                        <div>
                            <h3 class="text-base font-black text-slate-900 dark:text-white">Pelunasan Kasbon</h3>
                            <p class="text-xs text-slate-400">Pemasukan & laba masuk pada tanggal pembayaran ini</p>
                        </div>
                    </div>
                    <button
                        type="button"
                        @click="payModalOpen = false"
                        class="p-2 rounded-xl text-slate-400 hover:text-slate-600 hover:bg-slate-100 dark:hover:bg-slate-800"
                    >
                        ✕
                    </button>
                </div>

                <!-- Info Pelanggan, Produk, Nominal & Laba yang Masuk -->
                <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800/60 space-y-2">
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-500 dark:text-slate-400">Pelanggan:</span>
                        <span x-text="payCustomerName" class="font-black text-slate-900 dark:text-white"></span>
                    </div>
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-500 dark:text-slate-400">Produk yang Dilunasi:</span>
                        <span x-text="payProductName" class="font-bold text-slate-800 dark:text-slate-200"></span>
                    </div>
                    <div class="pt-2 border-t border-emerald-200/60 dark:border-emerald-800/60 flex items-center justify-between">
                        <div>
                            <span class="text-[10px] uppercase font-bold text-emerald-800 dark:text-emerald-300 block">Nominal Pembayaran</span>
                            <span class="text-xl font-black font-mono text-emerald-600 dark:text-emerald-400">
                                Rp <span x-text="Number(payAmount).toLocaleString('id-ID')"></span>
                            </span>
                        </div>
                        <div class="text-right">
                            <span class="text-[10px] uppercase font-bold text-slate-400 block">Laba Masuk Kas</span>
                            <span class="text-sm font-black font-mono text-emerald-700 dark:text-emerald-300">
                                +Rp <span x-text="Number(payProfit).toLocaleString('id-ID')"></span>
                            </span>
                        </div>
                    </div>
                </div>

                <form :action="payActionUrl" method="POST" class="space-y-4 text-xs">
                    @csrf

                    <!-- Tanggal Pelunasan (Bisa Diisi Besok / Kapan Saja Sesuai Tanggal Uang Diterima) -->
                    <div class="space-y-1.5">
                        <label class="font-bold text-slate-700 dark:text-slate-200">
                            Tanggal & Waktu Kasbon Dibayar <span class="text-rose-500">*</span>
                        </label>
                        <input
                            type="datetime-local"
                            name="paid_at"
                            x-model="payPaidAt"
                            required
                            class="w-full px-3 py-2.5 rounded-2xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500 outline-none text-xs font-semibold"
                        >
                        <p class="text-[11px] text-slate-500 dark:text-slate-400">
                            💡 Pemasukan & laba kasbon ini akan tercatat di dashboard & laporan pada tanggal ini.
                        </p>
                    </div>

                    <!-- Metode Pembayaran -->
                    <div class="space-y-1.5">
                        <label class="font-bold text-slate-700 dark:text-slate-200">
                            Metode Pembayaran <span class="text-rose-500">*</span>
                        </label>
                        <select
                            name="payment_method"
                            required
                            class="w-full px-4 py-2.5 rounded-2xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500 outline-none text-xs font-semibold"
                        >
                            <option value="cash">💵 Uang Tunai (Cash / Laci Kasir)</option>
                            <option value="transfer">🏦 Transfer Bank (BCA, BRI, Mandiri, dll)</option>
                            <option value="qris">📲 QRIS / E-Wallet (DANA, GoPay, OVO, ShopeePay)</option>
                        </select>
                    </div>

                    <!-- Catatan Pelunasan -->
                    <div class="space-y-1.5">
                        <label class="font-bold text-slate-700 dark:text-slate-200">
                            Catatan Pelunasan (Opsional)
                        </label>
                        <input
                            type="text"
                            name="notes"
                            placeholder="Contoh: Dibayar lunas lusa sore"
                            class="w-full px-4 py-2 rounded-2xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500 outline-none text-xs"
                        >
                    </div>

                    <input type="hidden" name="record_to_income" value="1">

                    <!-- Tombol Aksi Modal -->
                    <div class="pt-3 flex items-center justify-end gap-2 border-t border-slate-100 dark:border-slate-800">
                        <button
                            type="button"
                            @click="payModalOpen = false"
                            class="px-4 py-2.5 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-700 dark:bg-slate-800 dark:text-slate-300 font-bold transition-all"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            class="px-5 py-2.5 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white font-black shadow-md shadow-emerald-600/25 transition-all"
                        >
                            ✓ Konfirmasi Lunas & Masuk Pembukuan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ================================================================= -->
        <!-- 8. MODAL EDIT CATATAN KASBON                                      -->
        <!-- ================================================================= -->
        <div
            x-show="editModalOpen"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4"
            style="display: none;"
        >
            <div
                @click.away="editModalOpen = false"
                class="bg-white dark:bg-slate-850 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-2xl max-w-lg w-full p-6 sm:p-7 space-y-5"
            >
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-4">
                    <div class="flex items-center gap-2.5">
                        <span class="w-9 h-9 rounded-2xl bg-indigo-600 text-white flex items-center justify-center shadow-md shadow-indigo-600/25">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                        </span>
                        <div>
                            <h3 class="text-base font-black text-slate-900 dark:text-white">Edit Catatan Kasbon</h3>
                            <p class="text-xs text-slate-400">Perbarui rincian, produk, atau tanggal kasbon</p>
                        </div>
                    </div>
                    <button
                        type="button"
                        @click="editModalOpen = false"
                        class="p-2 rounded-xl text-slate-400 hover:text-slate-600 hover:bg-slate-100 dark:hover:bg-slate-800"
                    >
                        ✕
                    </button>
                </div>

                <form :action="editActionUrl" method="POST" class="space-y-4 text-xs">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div class="space-y-1.5">
                            <label class="font-bold text-slate-700 dark:text-slate-200">
                                Nama Pelanggan <span class="text-rose-500">*</span>
                            </label>
                            <input
                                type="text"
                                name="customer_name"
                                x-model="editCustomerName"
                                required
                                class="w-full px-4 py-2.5 rounded-2xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none text-xs"
                            >
                        </div>

                        <div class="space-y-1.5">
                            <label class="font-bold text-slate-700 dark:text-slate-200">
                                Nomor WhatsApp / HP
                            </label>
                            <input
                                type="text"
                                name="customer_phone"
                                x-model="editCustomerPhone"
                                class="w-full px-4 py-2.5 rounded-2xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none text-xs"
                            >
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div class="sm:col-span-2 space-y-1.5">
                            <label class="font-bold text-slate-700 dark:text-slate-200">
                                Nama Barang / Produk
                            </label>
                            <input
                                type="text"
                                name="product_name"
                                x-model="editProductName"
                                class="w-full px-4 py-2.5 rounded-2xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none text-xs"
                            >
                        </div>

                        <div class="space-y-1.5">
                            <label class="font-bold text-slate-700 dark:text-slate-200">
                                Jumlah (Qty)
                            </label>
                            <input
                                type="number"
                                name="quantity"
                                x-model="editQuantity"
                                @input="calculateEditProfit()"
                                min="1"
                                class="w-full px-4 py-2.5 rounded-2xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white font-mono font-bold focus:ring-2 focus:ring-indigo-500 outline-none text-xs text-center"
                            >
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div class="space-y-1.5">
                            <label class="font-bold text-slate-700 dark:text-slate-200">
                                Modal HPP Satuan (Rp)
                            </label>
                            <input
                                type="number"
                                name="cost_price"
                                x-model="editCostPrice"
                                @input="calculateEditProfit()"
                                class="w-full px-3 py-2.5 rounded-2xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white font-mono font-bold focus:ring-2 focus:ring-indigo-500 outline-none text-xs"
                            >
                        </div>

                        <div class="space-y-1.5">
                            <label class="font-bold text-slate-700 dark:text-slate-200">
                                Total Nominal Hutang (Rp) <span class="text-rose-500">*</span>
                            </label>
                            <input
                                type="number"
                                name="amount"
                                x-model="editAmount"
                                @input="calculateEditProfit()"
                                required
                                class="w-full px-3 py-2.5 rounded-2xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-amber-600 dark:text-amber-400 font-mono font-black focus:ring-2 focus:ring-indigo-500 outline-none text-sm"
                            >
                        </div>
                    </div>

                    <!-- Panel Laba Edit -->
                    <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800 flex items-center justify-between border border-slate-200 dark:border-slate-700">
                        <span class="text-[11px] font-bold text-slate-500">Laba Bersih Terhitung:</span>
                        <span class="text-sm font-black font-mono text-emerald-600 dark:text-emerald-400">
                            +Rp <span x-text="Number(editProfit).toLocaleString('id-ID')"></span>
                        </span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div class="space-y-1.5">
                            <label class="font-bold text-slate-700 dark:text-slate-200">
                                Tanggal Berhutang <span class="text-rose-500">*</span>
                            </label>
                            <input
                                type="date"
                                name="debt_date"
                                x-model="editDebtDate"
                                required
                                class="w-full px-3 py-2.5 rounded-2xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none text-xs"
                            >
                        </div>

                        <div class="space-y-1.5">
                            <label class="font-bold text-slate-700 dark:text-slate-200">
                                Janji Bayar (Jatuh Tempo)
                            </label>
                            <input
                                type="date"
                                name="due_date"
                                x-model="editDueDate"
                                class="w-full px-3 py-2.5 rounded-2xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none text-xs"
                            >
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <label class="font-bold text-slate-700 dark:text-slate-200">
                            Catatan Tambahan
                        </label>
                        <input
                            type="text"
                            name="notes"
                            x-model="editNotes"
                            class="w-full px-4 py-2 rounded-2xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none text-xs"
                        >
                    </div>

                    <div class="pt-3 flex items-center justify-end gap-2 border-t border-slate-100 dark:border-slate-800">
                        <button
                            type="button"
                            @click="editModalOpen = false"
                            class="px-4 py-2.5 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-700 dark:bg-slate-800 dark:text-slate-300 font-bold transition-all"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            class="px-5 py-2.5 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white font-black shadow-md shadow-indigo-600/25 transition-all"
                        >
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <x-slot:scripts>
        <script>
            function kasbonPage() {
                return {
                    // Daftar Produk dari Server
                    products: @json($products),

                    // Modal Tambah Kasbon State
                    createModalOpen: false,
                    selectedProductId: '',
                    createCustomerName: '',
                    createCustomerPhone: '',
                    createProductName: '',
                    createQuantity: 1,
                    createCostPrice: 0,
                    createAmount: 0,
                    createProfit: 0,
                    createDebtDate: '{{ date('Y-m-d') }}',
                    createDueDate: '',
                    createDescription: '',
                    createNotes: '',

                    // Handler Saat Memilih Produk di Form
                    onProductSelect() {
                        if (!this.selectedProductId) {
                            this.createProductName = '';
                            this.createCostPrice = 0;
                            this.createAmount = 0;
                            this.createProfit = 0;
                            return;
                        }

                        const p = this.products.find(function(item) {
                            return item.id == this.selectedProductId;
                        }.bind(this));

                        if (p) {
                            this.createProductName = p.name;
                            this.createCostPrice = parseFloat(p.cost_price) || 0;
                            const unitSelling = parseFloat(p.selling_price) || 0;
                            const qty = parseInt(this.createQuantity) || 1;
                            this.createAmount = unitSelling * qty;
                            this.calculateProfit();
                        }
                    },

                    // Produk yang sedang dipilih
                    get selectedProduct() {
                        if (!this.selectedProductId) return null;
                        const self = this;
                        return this.products.find(function(item) {
                            return item.id == self.selectedProductId;
                        });
                    },

                    // Hitung Ulang Nominal & Laba
                    calculateProfit() {
                        const qty = parseInt(this.createQuantity) || 1;
                        const totalCost = (parseFloat(this.createCostPrice) || 0) * qty;
                        const totalSelling = parseFloat(this.createAmount) || 0;
                        this.createProfit = Math.max(0, totalSelling - totalCost);
                    },

                    onQuantityChange() {
                        if (this.selectedProductId) {
                            const p = this.products.find(function(item) {
                                return item.id == this.selectedProductId;
                            }.bind(this));

                            if (p) {
                                const unitSelling = parseFloat(p.selling_price) || 0;
                                const qty = parseInt(this.createQuantity) || 1;
                                this.createAmount = unitSelling * qty;
                            }
                        }
                        this.calculateProfit();
                    },

                    // Modal Pelunasan (Bayar Kasbon)
                    payModalOpen: false,
                    payActionUrl: '',
                    payCustomerName: '',
                    payProductName: '',
                    payAmount: 0,
                    payProfit: 0,
                    payDebtDate: '',
                    payPaidAt: '{{ date('Y-m-d\TH:i') }}',

                    openPayModal(kasbon) {
                        this.payActionUrl = '{{ url('/kasbons') }}/' + kasbon.id + '/pay';
                        this.payCustomerName = kasbon.customer_name || '';
                        this.payProductName = kasbon.product_name || kasbon.description || 'Kasbon Produk';
                        this.payAmount = kasbon.amount || 0;
                        this.payProfit = kasbon.profit || 0;
                        this.payDebtDate = kasbon.debt_date ? kasbon.debt_date.substring(0, 10) : '';
                        this.payPaidAt = '{{ date('Y-m-d\TH:i') }}';
                        this.payModalOpen = true;
                    },

                    // Modal Edit Kasbon
                    editModalOpen: false,
                    editActionUrl: '',
                    editCustomerName: '',
                    editCustomerPhone: '',
                    editProductName: '',
                    editQuantity: 1,
                    editCostPrice: 0,
                    editAmount: 0,
                    editProfit: 0,
                    editDebtDate: '',
                    editDueDate: '',
                    editDescription: '',
                    editNotes: '',

                    openEditModal(kasbon) {
                        this.editActionUrl = '{{ url('/kasbons') }}/' + kasbon.id;
                        this.editCustomerName = kasbon.customer_name || '';
                        this.editCustomerPhone = kasbon.customer_phone || '';
                        this.editProductName = kasbon.product_name || '';
                        this.editQuantity = kasbon.quantity || 1;
                        this.editCostPrice = parseFloat(kasbon.cost_price) || 0;
                        this.editAmount = parseFloat(kasbon.amount) || 0;
                        this.editProfit = parseFloat(kasbon.profit) || 0;
                        this.editDebtDate = kasbon.debt_date ? kasbon.debt_date.substring(0, 10) : '';
                        this.editDueDate = kasbon.due_date ? kasbon.due_date.substring(0, 10) : '';
                        this.editDescription = kasbon.description || '';
                        this.editNotes = kasbon.notes || '';
                        this.editModalOpen = true;
                    },

                    calculateEditProfit() {
                        const qty = parseInt(this.editQuantity) || 1;
                        const totalCost = (parseFloat(this.editCostPrice) || 0) * qty;
                        const totalSelling = parseFloat(this.editAmount) || 0;
                        this.editProfit = Math.max(0, totalSelling - totalCost);
                    }
                };
            }
        </script>
    </x-slot:scripts>
</x-layouts.app>
