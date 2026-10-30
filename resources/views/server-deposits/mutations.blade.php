<x-layouts.app title="Riwayat Mutasi Saldo Server">
    <x-slot:header>
        Riwayat & Mutasi Saldo Server
    </x-slot:header>

    @php
        $isFiltered = request()->hasAny(['search', 'type', 'start_date', 'end_date']);
    @endphp

    <div
        x-data="{
            topupAmount: 500000,
            newBalance: {{ (float) ($serverDeposit->balance ?? 0) }},
            setTopup(val) {
                this.topupAmount = val;
            }
        }"
        class="space-y-6"
    >
        <!-- ================================================================= -->
        <!-- 1. HEADER HALAMAN ELEGAN & CERAH                                  -->
        <!-- ================================================================= -->
        <div class="p-6 sm:p-7 rounded-3xl bg-gradient-to-r from-indigo-50/80 via-white to-sky-50/50 dark:from-slate-850 dark:via-slate-800 dark:to-slate-850 border border-indigo-100/80 dark:border-slate-700 shadow-sm">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-5">
                <div class="flex items-start sm:items-center gap-4">
                    <!-- Icon Mutasi Saldo Vektor -->
                    <div class="w-13 h-13 rounded-2xl bg-gradient-to-tr from-indigo-600 to-sky-500 text-white flex items-center justify-center shadow-lg shadow-indigo-600/25 shrink-0">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2.5 flex-wrap">
                            <h2 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight">
                                Riwayat Mutasi Saldo Server
                            </h2>
                            <span class="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full bg-indigo-100/80 dark:bg-indigo-950/60 border border-indigo-200 dark:border-indigo-800 text-indigo-800 dark:text-indigo-300 text-xs font-bold shadow-2xs">
                                <span class="w-2 h-2 rounded-full bg-indigo-500 animate-pulse"></span>
                                <span>{{ $mutations->total() }} Log Mutasi</span>
                            </span>
                        </div>
                        <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">
                            Pantau pergerakan saldo deposit: pengisian saldo (top-up), pemotongan transaksi kasir (pulsa, data, token PLN, e-wallet), dan koreksi saldo.
                        </p>
                    </div>
                </div>

                <!-- Tombol Aksi Cepat -->
                <div class="flex items-center gap-2.5 flex-wrap self-start md:self-auto">
                    <x-button 
                        href="{{ route('dashboard') }}" 
                        variant="secondary" 
                        size="sm" 
                        class="bg-white hover:bg-slate-50 text-slate-700 dark:bg-slate-800 dark:text-slate-200 border-slate-200 dark:border-slate-700 font-bold text-xs shadow-2xs transition-all"
                    >
                        <svg class="w-4 h-4 mr-1.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        <span>Dashboard</span>
                    </x-button>

                    <button
                        type="button"
                        @click="$dispatch('open-modal', 'adjust-deposit-modal')"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 font-bold text-xs shadow-2xs hover:bg-slate-50 transition-all cursor-pointer"
                    >
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        <span>Koreksi Saldo</span>
                    </button>

                    <button 
                        type="button"
                        @click="$dispatch('open-modal', 'topup-deposit-modal')"
                        class="inline-flex items-center gap-2 px-4.5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs uppercase tracking-wider transition-all shadow-md shadow-emerald-600/25 cursor-pointer hover:shadow-lg"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span>+ Isi / Top-Up Saldo</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- ================================================================= -->
        <!-- 2. EMPAT KARTU METRIK STATISTIK SALDO (CERAH & ELEGAN)            -->
        <!-- ================================================================= -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
            <!-- 1. Sisa Saldo Server Utama (Indigo / Sky) -->
            <div class="p-5 rounded-3xl bg-gradient-to-br from-indigo-50/90 via-white to-sky-50/40 dark:from-indigo-950/40 dark:via-slate-850 dark:to-slate-850 border border-indigo-200/80 dark:border-indigo-900/50 shadow-xs hover:shadow-md transition-all">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-indigo-900 dark:text-indigo-300 uppercase tracking-wider">
                        Sisa Saldo Server
                    </span>
                    <div class="w-10 h-10 rounded-2xl bg-indigo-600 text-white flex items-center justify-center shadow-md shadow-indigo-600/25">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                    </div>
                </div>

                <div class="mt-3">
                    <div class="text-2xl font-black font-mono tracking-tight text-slate-900 dark:text-white">
                        @rupiah($serverDeposit->balance)
                    </div>
                    <div class="mt-2 flex items-center justify-between text-xs text-slate-500 dark:text-slate-400">
                        <span>Status Saldo:</span>
                        @if($serverDeposit->isLow())
                            <span class="font-bold font-mono px-2 py-0.5 rounded-full bg-rose-100 text-rose-800 dark:bg-rose-950/80 dark:text-rose-300 border border-rose-200 dark:border-rose-800">
                                Saldo Kritis
                            </span>
                        @else
                            <span class="font-bold font-mono px-2 py-0.5 rounded-full bg-emerald-100/80 dark:bg-emerald-950/80 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300">
                                Saldo Aman
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- 2. Total Saldo Masuk (Emerald Zamrud) -->
            <div class="p-5 rounded-3xl bg-gradient-to-br from-emerald-50/90 via-white to-teal-50/40 dark:from-emerald-950/40 dark:via-slate-850 dark:to-slate-850 border border-emerald-200/80 dark:border-emerald-900/50 shadow-xs hover:shadow-md transition-all">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-emerald-900 dark:text-emerald-300 uppercase tracking-wider">
                        Total Saldo Masuk (In)
                    </span>
                    <div class="w-10 h-10 rounded-2xl bg-emerald-600 text-white flex items-center justify-center shadow-md shadow-emerald-600/25">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                        </svg>
                    </div>
                </div>

                <div class="mt-3">
                    <div class="text-2xl font-black font-mono tracking-tight text-emerald-600 dark:text-emerald-400">
                        +@rupiah($totalIn)
                    </div>
                    <div class="mt-2 flex items-center justify-between text-xs text-slate-500 dark:text-slate-400">
                        <span>Top-up & refund batal</span>
                        <span class="font-bold font-mono px-2 py-0.5 rounded-full bg-emerald-100/80 dark:bg-emerald-950/80 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300">
                            Masuk
                        </span>
                    </div>
                </div>
            </div>

            <!-- 3. Total Saldo Terpotong (Rose) -->
            <div class="p-5 rounded-3xl bg-gradient-to-br from-rose-50/90 via-white to-pink-50/40 dark:from-rose-950/40 dark:via-slate-850 dark:to-slate-850 border border-rose-200/80 dark:border-rose-900/50 shadow-xs hover:shadow-md transition-all">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-rose-900 dark:text-rose-300 uppercase tracking-wider">
                        Saldo Terpotong (Out)
                    </span>
                    <div class="w-10 h-10 rounded-2xl bg-rose-600 text-white flex items-center justify-center shadow-md shadow-rose-600/25">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                        </svg>
                    </div>
                </div>

                <div class="mt-3">
                    <div class="text-2xl font-black font-mono tracking-tight text-rose-600 dark:text-rose-400">
                        -@rupiah($totalOut)
                    </div>
                    <div class="mt-2 flex items-center justify-between text-xs text-slate-500 dark:text-slate-400">
                        <span>Transaksi kasir konter</span>
                        <span class="font-bold font-mono px-2 py-0.5 rounded-full bg-rose-100/80 dark:bg-rose-950/80 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-300">
                            Terpotong
                        </span>
                    </div>
                </div>
            </div>

            <!-- 4. Mutasi Hari Ini (Sky / Cyan) -->
            <div class="p-5 rounded-3xl bg-gradient-to-br from-sky-50/70 via-white to-blue-50/30 dark:from-slate-850 dark:via-slate-850 dark:to-slate-850 border border-sky-200/70 dark:border-slate-700 shadow-xs hover:shadow-md transition-all">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-sky-900 dark:text-sky-300 uppercase tracking-wider">
                        Aktivitas Hari Ini
                    </span>
                    <div class="w-10 h-10 rounded-2xl bg-sky-500 text-white flex items-center justify-center shadow-md shadow-sky-500/25">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>

                <div class="mt-3">
                    <div class="text-2xl font-black font-mono tracking-tight text-slate-900 dark:text-white">
                        {{ number_format($todayMutationsCount) }}
                    </div>
                    <div class="mt-2 flex items-center justify-between text-xs text-slate-500 dark:text-slate-400">
                        <span>Pergerakan saldo</span>
                        <span class="font-bold font-mono px-2 py-0.5 rounded-full bg-sky-100/80 dark:bg-sky-950/80 border border-sky-200 dark:border-sky-800 text-sky-800 dark:text-sky-300">
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
            <!-- Preset Periode Cepat & Preset Tipe Mutasi -->
            <div class="flex items-center justify-between gap-3 overflow-x-auto pb-1 scrollbar-thin text-xs">
                <div class="flex items-center gap-1.5 font-semibold">
                    <span class="text-slate-400 text-[11px] uppercase tracking-wider font-bold mr-1 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <span>Periode:</span>
                    </span>

                    <!-- Semua Waktu -->
                    <a
                        href="{{ route('server-deposit.mutations', array_merge(request()->except(['start_date', 'end_date', 'page']))) }}"
                        class="px-3.5 py-1.5 rounded-xl transition-all cursor-pointer {{ !request('start_date') && !request('end_date') ? 'bg-indigo-600 text-white font-bold shadow-xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200' }}"
                    >
                        Semua Waktu
                    </a>

                    <!-- Hari Ini -->
                    <a
                        href="{{ route('server-deposit.mutations', array_merge(request()->except(['page']), ['start_date' => $todayStr, 'end_date' => $todayStr])) }}"
                        class="px-3.5 py-1.5 rounded-xl transition-all cursor-pointer {{ request('start_date') === $todayStr && request('end_date') === $todayStr ? 'bg-indigo-600 text-white font-bold shadow-xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200' }}"
                    >
                        Hari Ini
                    </a>

                    <!-- 7 Hari Terakhir -->
                    <a
                        href="{{ route('server-deposit.mutations', array_merge(request()->except(['page']), ['start_date' => $weekAgoStr, 'end_date' => $todayStr])) }}"
                        class="px-3.5 py-1.5 rounded-xl transition-all cursor-pointer {{ request('start_date') === $weekAgoStr && request('end_date') === $todayStr ? 'bg-indigo-600 text-white font-bold shadow-xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200' }}"
                    >
                        7 Hari Terakhir
                    </a>

                    <!-- Bulan Ini -->
                    <a
                        href="{{ route('server-deposit.mutations', array_merge(request()->except(['page']), ['start_date' => $monthStartStr, 'end_date' => $monthEndStr])) }}"
                        class="px-3.5 py-1.5 rounded-xl transition-all cursor-pointer {{ request('start_date') === $monthStartStr && request('end_date') === $monthEndStr ? 'bg-indigo-600 text-white font-bold shadow-xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200' }}"
                    >
                        Bulan Ini
                    </a>
                </div>

                @if($isFiltered)
                    <a 
                        href="{{ route('server-deposit.mutations') }}" 
                        class="text-xs text-rose-600 hover:text-rose-700 font-bold flex items-center gap-1 shrink-0 px-2.5 py-1 rounded-xl bg-rose-50 dark:bg-rose-950/50 border border-rose-200 dark:border-rose-800"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        <span>Reset Filter</span>
                    </a>
                @endif
            </div>

            <!-- Preset Tipe Mutasi -->
            <div class="flex items-center gap-1.5 overflow-x-auto pb-1 scrollbar-thin text-xs pt-2 border-t border-slate-100 dark:border-slate-800">
                <span class="text-slate-400 text-[11px] uppercase tracking-wider font-bold mr-1 flex items-center gap-1 shrink-0">
                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                    <span>Tipe Mutasi:</span>
                </span>

                <!-- Semua Tipe -->
                <a
                    href="{{ route('server-deposit.mutations', array_merge(request()->except(['type', 'page']))) }}"
                    class="px-3 py-1.5 rounded-xl transition-all cursor-pointer shrink-0 {{ !request('type') ? 'bg-indigo-600 text-white font-bold shadow-xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200' }}"
                >
                    Semua Mutasi
                </a>

                <!-- Masuk / Top-Up -->
                <a
                    href="{{ route('server-deposit.mutations', array_merge(request()->except(['page']), ['type' => 'in'])) }}"
                    class="px-3 py-1.5 rounded-xl transition-all cursor-pointer flex items-center gap-1.5 shrink-0 {{ request('type') === 'in' ? 'bg-emerald-600 text-white font-bold shadow-xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200' }}"
                >
                    <span class="w-1.5 h-1.5 rounded-full {{ request('type') === 'in' ? 'bg-white' : 'bg-emerald-500' }}"></span>
                    <span>Saldo Masuk (+ Top-Up/Refund)</span>
                </a>

                <!-- Keluar / Terpotong Kasir -->
                <a
                    href="{{ route('server-deposit.mutations', array_merge(request()->except(['page']), ['type' => 'out'])) }}"
                    class="px-3 py-1.5 rounded-xl transition-all cursor-pointer flex items-center gap-1.5 shrink-0 {{ request('type') === 'out' ? 'bg-rose-600 text-white font-bold shadow-xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200' }}"
                >
                    <span class="w-1.5 h-1.5 rounded-full {{ request('type') === 'out' ? 'bg-white' : 'bg-rose-500' }}"></span>
                    <span>Saldo Terpotong (- Transaksi)</span>
                </a>

                <!-- Koreksi Manual -->
                <a
                    href="{{ route('server-deposit.mutations', array_merge(request()->except(['page']), ['type' => 'set'])) }}"
                    class="px-3 py-1.5 rounded-xl transition-all cursor-pointer flex items-center gap-1.5 shrink-0 {{ request('type') === 'set' ? 'bg-amber-600 text-white font-bold shadow-xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200' }}"
                >
                    <span class="w-1.5 h-1.5 rounded-full {{ request('type') === 'set' ? 'bg-white' : 'bg-amber-500' }}"></span>
                    <span>Koreksi Saldo Manual (±)</span>
                </a>
            </div>

            <!-- Form Filter Input Fields -->
            <form method="GET" action="{{ route('server-deposit.mutations') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3.5 pt-3 border-t border-slate-100 dark:border-slate-800">
                <!-- Pencarian (5 Col) -->
                <div class="sm:col-span-2 lg:col-span-5">
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Cari Keterangan / Invoice</label>
                    <div class="relative">
                        <input
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Ketik nomor invoice, top-up, atau catatan..."
                            class="w-full pl-9 pr-3 py-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-slate-800 focus:bg-white text-slate-900 dark:text-white placeholder-slate-400 text-xs font-medium focus:ring-2 focus:ring-indigo-500 shadow-2xs"
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
                        class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-slate-800 focus:bg-white text-slate-900 dark:text-white text-xs font-medium focus:ring-2 focus:ring-indigo-500 shadow-2xs"
                    >
                </div>

                <!-- Sampai Tanggal (3 Col) -->
                <div class="lg:col-span-3">
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Sampai Tanggal</label>
                    <input
                        type="date"
                        name="end_date"
                        value="{{ request('end_date') }}"
                        class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-slate-800 focus:bg-white text-slate-900 dark:text-white text-xs font-medium focus:ring-2 focus:ring-indigo-500 shadow-2xs"
                    >
                </div>

                <!-- Tombol Submit (2 Col) -->
                <div class="sm:col-span-2 lg:col-span-2 flex items-end">
                    <button
                        type="submit"
                        class="w-full py-2 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs uppercase tracking-wider transition-colors shadow-xs cursor-pointer flex items-center justify-center gap-1.5"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <span>Filter</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- ================================================================= -->
        <!-- 4. TABEL RIWAYAT LOG MUTASI SALDO SERVER                          -->
        <!-- ================================================================= -->
        <div class="rounded-3xl bg-white dark:bg-slate-850 border border-slate-200/90 dark:border-slate-800 shadow-xs overflow-hidden">
            <!-- Header Kartu Tabel -->
            <div class="p-5 sm:p-6 border-b border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 bg-slate-50/60 dark:bg-slate-800/40">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-indigo-100 text-indigo-600 dark:bg-indigo-950/60 dark:text-indigo-400 flex items-center justify-center font-bold text-sm shadow-2xs">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-black uppercase tracking-wider text-slate-900 dark:text-white">
                            Catatan Pergerakan Saldo Deposit
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Riwayat audit saldo masuk, pemotongan penjualan kasir, dan penyesuaian nominal</p>
                    </div>
                </div>

                <div class="text-xs text-slate-500 dark:text-slate-400 font-mono self-start sm:self-auto">
                    Menampilkan <strong class="text-slate-900 dark:text-white font-bold">{{ $mutations->firstItem() ?? 0 }}-{{ $mutations->lastItem() ?? 0 }}</strong> dari <strong class="text-slate-900 dark:text-white font-bold">{{ $mutations->total() }}</strong> catatan
                </div>
            </div>

            <!-- Tampilan Desktop: Tabel Lengkap -->
            <div class="hidden md:block overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200/80 dark:divide-slate-800 text-left text-xs">
                    <thead class="bg-slate-50/90 dark:bg-slate-800/90 text-slate-500 dark:text-slate-400 uppercase font-black tracking-wider text-[11px] border-b border-slate-200 dark:border-slate-800">
                        <tr>
                            <th class="px-6 py-4">Waktu & Tanggal</th>
                            <th class="px-4 py-4 text-center">Tipe Mutasi</th>
                            <th class="px-5 py-4 text-right">Perubahan Nominal</th>
                            <th class="px-5 py-4 text-center">Transisi Saldo</th>
                            <th class="px-6 py-4">Keterangan / Alasan</th>
                            <th class="px-5 py-4">Operator</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80 font-medium">
                        @forelse($mutations as $m)
                            <tr class="hover:bg-indigo-50/30 dark:hover:bg-slate-800/50 transition-colors group">
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

                                <!-- 2. Tipe Mutasi -->
                                <td class="px-4 py-4.5 text-center whitespace-nowrap">
                                    @if($m->type === 'in')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl text-[11px] font-bold bg-emerald-50 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/70 shadow-2xs">
                                            <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                                            <span>Masuk / Top-Up</span>
                                        </span>
                                    @elseif($m->type === 'out')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl text-[11px] font-bold bg-rose-50 text-rose-800 dark:bg-rose-950/60 dark:text-rose-300 border border-rose-200 dark:border-rose-800/70 shadow-2xs">
                                            <svg class="w-3.5 h-3.5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                                            <span>Terpotong Kasir</span>
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl text-[11px] font-bold bg-amber-50 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300 border border-amber-200 dark:border-amber-800/70 shadow-2xs">
                                            <span>Koreksi Saldo</span>
                                        </span>
                                    @endif
                                </td>

                                <!-- 3. Perubahan Nominal -->
                                <td class="px-5 py-4.5 text-right font-mono font-black text-sm whitespace-nowrap">
                                    @if($m->type === 'in')
                                        <span class="text-emerald-600 dark:text-emerald-400">
                                            +@rupiah($m->amount)
                                        </span>
                                    @elseif($m->type === 'out')
                                        <span class="text-rose-600 dark:text-rose-400">
                                            -@rupiah($m->amount)
                                        </span>
                                    @else
                                        <span class="text-amber-600 dark:text-amber-400">
                                            &plusmn;@rupiah($m->amount)
                                        </span>
                                    @endif
                                </td>

                                <!-- 4. Transisi Saldo -->
                                <td class="px-5 py-4.5 text-center whitespace-nowrap">
                                    <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs font-mono font-bold shadow-2xs">
                                        <span class="text-slate-500">@rupiah($m->balance_before)</span>
                                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                        <span class="text-slate-900 dark:text-white font-black">@rupiah($m->balance_after)</span>
                                    </div>
                                </td>

                                <!-- 5. Keterangan / Alasan -->
                                <td class="px-6 py-4.5">
                                    <div class="text-xs font-bold text-slate-800 dark:text-slate-200 max-w-sm break-words">
                                        {{ $m->notes ?? '-' }}
                                    </div>
                                    @if($m->reference_type)
                                        <span class="text-[10px] text-slate-400 font-mono block mt-0.5">
                                            Ref: {{ class_basename($m->reference_type) }} #{{ $m->reference_id }}
                                        </span>
                                    @endif
                                </td>

                                <!-- 6. Operator -->
                                <td class="px-5 py-4.5 whitespace-nowrap text-slate-600 dark:text-slate-400">
                                    <div class="flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                        <span class="text-xs">{{ $m->user->name ?? 'Kasir / Sistem' }}</span>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-14">
                                    <x-empty-state
                                        title="Belum Ada Riwayat Mutasi Saldo"
                                        description="Tidak ditemukan riwayat mutasi pada kriteria pencarian ini. Mutasi akan tercatat otomatis saat Anda melakukan top-up, mencatat penjualan kasir, atau mengoreksi saldo deposit."
                                    >
                                        <x-slot:action>
                                            <button
                                                type="button"
                                                @click="$dispatch('open-modal', 'topup-deposit-modal')"
                                                class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs uppercase tracking-wider transition-colors shadow-xs cursor-pointer inline-flex items-center gap-1.5"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 4v16m8-8H4"/></svg>
                                                <span>+ Isi / Top-Up Saldo</span>
                                            </button>
                                        </x-slot:action>
                                    </x-empty-state>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Tampilan Mobile: Kartu Responsif (md:hidden) -->
            <div class="block md:hidden divide-y divide-slate-100 dark:divide-slate-800">
                @forelse($mutations as $m)
                    <div class="p-4.5 space-y-3 hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors">
                        <div class="flex items-center justify-between">
                            @if($m->type === 'in')
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-lg text-[10px] font-black uppercase tracking-wider bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-700">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                                    <span>Masuk (+ Top-Up)</span>
                                </span>
                            @elseif($m->type === 'out')
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-lg text-[10px] font-black uppercase tracking-wider bg-rose-100 text-rose-800 dark:bg-rose-950/60 dark:text-rose-300 border border-rose-300 dark:border-rose-700">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                                    <span>Terpotong Kasir</span>
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-lg text-[10px] font-black uppercase tracking-wider bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300 border border-amber-300 dark:border-amber-700">
                                    <span>Koreksi Saldo</span>
                                </span>
                            @endif

                            <div class="flex items-center gap-1 text-[11px] text-slate-400 font-medium">
                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span>@tanggalWaktu($m->created_at)</span>
                            </div>
                        </div>

                        <div>
                            <h4 class="font-bold text-slate-900 dark:text-white text-xs leading-snug">
                                {{ $m->notes ?? 'Mutasi saldo server' }}
                            </h4>
                            <p class="text-[11px] text-slate-400 mt-0.5">
                                Operator: {{ $m->user->name ?? 'Kasir / Sistem' }}
                            </p>
                        </div>

                        <div class="flex items-center justify-between text-xs pt-2 border-t border-slate-100 dark:border-slate-800">
                            <div>
                                <span class="text-[10px] text-slate-400 uppercase tracking-wider block font-bold">Nominal:</span>
                                <strong class="font-mono text-sm font-black {{ $m->type === 'in' ? 'text-emerald-600 dark:text-emerald-400' : ($m->type === 'out' ? 'text-rose-600 dark:text-rose-400' : 'text-amber-600 dark:text-amber-400') }}">
                                    {{ $m->type === 'in' ? '+' : ($m->type === 'out' ? '-' : '±') }} @rupiah($m->amount)
                                </strong>
                            </div>
                            <div class="text-right">
                                <span class="text-[10px] text-slate-400 uppercase tracking-wider block font-bold">Transisi Saldo:</span>
                                <span class="font-mono font-bold text-xs text-slate-700 dark:text-slate-300">
                                    @rupiah($m->balance_before) &rarr; <span class="text-slate-900 dark:text-white font-black">@rupiah($m->balance_after)</span>
                                </span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-6 text-center text-xs text-slate-400">
                        Tidak ada riwayat mutasi saldo pada kriteria pencarian ini.
                    </div>
                @endforelse
            </div>

            <!-- Pagination Bar -->
            <div class="p-5 sm:p-6 bg-slate-50/50 dark:bg-slate-800/20 border-t border-slate-100 dark:border-slate-800">
                {{ $mutations->links() }}
            </div>
        </div>
    </div>

    <!-- ===================================================================== -->
    <!-- MODAL 1: TOP-UP / TAMBAH SALDO DEPOSIT                                -->
    <!-- ===================================================================== -->
    <x-modal name="topup-deposit-modal" title="Top-Up / Tambah Saldo Deposit Server" maxWidth="md">
        <form method="POST" action="{{ route('server-deposit.topup') }}" class="space-y-4">
            @csrf

            <p class="text-xs text-slate-500 dark:text-slate-400">
                Masukkan nominal isi ulang deposit yang baru saja Anda transfer ke aplikasi server agen.
            </p>

            <!-- Pilihan Cepat Nominal Top-Up -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">
                    Pilihan Cepat Nominal:
                </label>
                <div class="grid grid-cols-3 gap-2">
                    <button
                        type="button"
                        @click="setTopup(100000)"
                        class="px-2.5 py-1.5 rounded-xl text-xs font-bold font-mono border border-slate-200 dark:border-slate-700 hover:bg-indigo-50 dark:hover:bg-indigo-950/40 hover:text-indigo-600 transition-colors cursor-pointer"
                    >
                        100.000
                    </button>
                    <button
                        type="button"
                        @click="setTopup(200000)"
                        class="px-2.5 py-1.5 rounded-xl text-xs font-bold font-mono border border-slate-200 dark:border-slate-700 hover:bg-indigo-50 dark:hover:bg-indigo-950/40 hover:text-indigo-600 transition-colors cursor-pointer"
                    >
                        200.000
                    </button>
                    <button
                        type="button"
                        @click="setTopup(300000)"
                        class="px-2.5 py-1.5 rounded-xl text-xs font-bold font-mono border border-slate-200 dark:border-slate-700 hover:bg-indigo-50 dark:hover:bg-indigo-950/40 hover:text-indigo-600 transition-colors cursor-pointer"
                    >
                        300.000
                    </button>
                    <button
                        type="button"
                        @click="setTopup(500000)"
                        class="px-2.5 py-1.5 rounded-xl text-xs font-bold font-mono border border-slate-200 dark:border-slate-700 hover:bg-indigo-50 dark:hover:bg-indigo-950/40 hover:text-indigo-600 transition-colors cursor-pointer"
                    >
                        500.000
                    </button>
                    <button
                        type="button"
                        @click="setTopup(1000000)"
                        class="px-2.5 py-1.5 rounded-xl text-xs font-bold font-mono border border-slate-200 dark:border-slate-700 hover:bg-indigo-50 dark:hover:bg-indigo-950/40 hover:text-indigo-600 transition-colors cursor-pointer"
                    >
                        1.000.000
                    </button>
                    <button
                        type="button"
                        @click="setTopup(2000000)"
                        class="px-2.5 py-1.5 rounded-xl text-xs font-bold font-mono border border-slate-200 dark:border-slate-700 hover:bg-indigo-50 dark:hover:bg-indigo-950/40 hover:text-indigo-600 transition-colors cursor-pointer"
                    >
                        2.000.000
                    </button>
                </div>
            </div>

            <!-- Input Nominal Bebas -->
            <div>
                <label for="amount" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">
                    Nominal Tambah Saldo (Rp)
                </label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center font-bold text-slate-400 text-xs">
                        Rp
                    </span>
                    <input
                        type="number"
                        name="amount"
                        id="amount"
                        x-model.number="topupAmount"
                        min="1000"
                        step="1000"
                        required
                        class="w-full pl-9 pr-3 py-2.5 rounded-2xl border border-slate-300 dark:border-slate-600 text-sm font-mono font-bold dark:bg-slate-800 text-slate-900 dark:text-slate-100"
                    >
                </div>
            </div>

            <div class="pt-3 border-t border-slate-100 dark:border-slate-700 flex justify-end gap-2">
                <button
                    type="button"
                    @click="$dispatch('close-modal', 'topup-deposit-modal')"
                    class="px-3.5 py-2 rounded-xl text-xs font-bold bg-slate-100 hover:bg-slate-200 text-slate-700 dark:bg-slate-800 dark:text-slate-300 cursor-pointer"
                >
                    Batal
                </button>
                <button
                    type="submit"
                    class="px-4 py-2 rounded-xl text-xs font-bold bg-emerald-600 hover:bg-emerald-700 text-white shadow-xs cursor-pointer"
                >
                    Konfirmasi Top-Up
                </button>
            </div>
        </form>
    </x-modal>

    <!-- ===================================================================== -->
    <!-- MODAL 2: KOREKSI / SET SALDO REAL SECARA MANUAL                       -->
    <!-- ===================================================================== -->
    <x-modal name="adjust-deposit-modal" title="Koreksi / Set Sisa Saldo Server" maxWidth="md">
        <form method="POST" action="{{ route('server-deposit.adjust') }}" class="space-y-4">
            @csrf

            <p class="text-xs text-slate-500 dark:text-slate-400">
                Gunakan menu ini apabila saldo di sistem KonterKu berselisih dengan saldo riil yang ada pada aplikasi server agen Anda.
            </p>

            <div>
                <label for="new_balance" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">
                    Sisa Saldo Riil Saat Ini (Rp)
                </label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center font-bold text-slate-400 text-xs">
                        Rp
                    </span>
                    <input
                        type="number"
                        name="new_balance"
                        id="new_balance"
                        x-model.number="newBalance"
                        min="0"
                        step="1"
                        required
                        class="w-full pl-9 pr-3 py-2.5 rounded-2xl border border-slate-300 dark:border-slate-600 text-sm font-mono font-bold dark:bg-slate-800 text-slate-900 dark:text-slate-100"
                    >
                </div>
            </div>

            <div class="pt-3 border-t border-slate-100 dark:border-slate-700 flex justify-end gap-2">
                <button
                    type="button"
                    @click="$dispatch('close-modal', 'adjust-deposit-modal')"
                    class="px-3.5 py-2 rounded-xl text-xs font-bold bg-slate-100 hover:bg-slate-200 text-slate-700 dark:bg-slate-800 dark:text-slate-300 cursor-pointer"
                >
                    Batal
                </button>
                <button
                    type="submit"
                    class="px-4 py-2 rounded-xl text-xs font-bold bg-indigo-600 hover:bg-indigo-700 text-white shadow-xs cursor-pointer"
                >
                    Simpan Koreksi Saldo
                </button>
            </div>
        </form>
    </x-modal>
</x-layouts.app>

