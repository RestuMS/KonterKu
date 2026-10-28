<x-layouts.app title="Catat Pengeluaran Baru">
    <x-slot:header>
        Catat Pengeluaran Kas
    </x-slot:header>

    @php
        $nowFormatted = now()->format('Y-m-d\TH:i');
        $yesterdayFormatted = now()->subDay()->format('Y-m-d\TH:i');
    @endphp

    <div class="space-y-6">
        <!-- ================================================================= -->
        <!-- 1. HEADER FORM ELEGAN & CERAH                                     -->
        <!-- ================================================================= -->
        <div class="p-6 sm:p-7 rounded-3xl bg-gradient-to-r from-rose-50/80 via-white to-amber-50/50 dark:from-slate-850 dark:via-slate-800 dark:to-slate-850 border border-rose-100/80 dark:border-slate-700 shadow-sm">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-13 h-13 rounded-2xl bg-gradient-to-tr from-rose-600 to-rose-500 text-white flex items-center justify-center shadow-lg shadow-rose-600/25 shrink-0">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight">
                            Pencatatan Pengeluaran Kas
                        </h2>
                        <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">
                            Pilih kategori cepat: jajan, beli kuota, beli mainan, beli bensin, atau operasional toko.
                        </p>
                    </div>
                </div>

                <x-button 
                    href="{{ route('expenses.index') }}" 
                    variant="secondary" 
                    size="sm"
                    class="bg-white hover:bg-slate-50 text-slate-700 dark:bg-slate-800 dark:text-slate-200 border-slate-200 dark:border-slate-700 font-bold text-xs shadow-2xs self-start sm:self-auto"
                >
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    <span>Daftar Pengeluaran</span>
                </x-button>
            </div>
        </div>

        <!-- ================================================================= -->
        <!-- 2. WORKSPACE FORM 2-KOLOM (FORM KAS KELUAR + SIDEBAR RIWAYAT)      -->
        <!-- ================================================================= -->
        <div 
            x-data="window.expenseFormManager({
                categories: {{ Js::from($categories) }},
                initialCategoryId: '{{ old('category_id') }}',
                initialAmount: '{{ old('amount', '') }}',
                initialNotes: '{{ old('notes', '') }}',
                initialPaymentMethod: '{{ old('payment_method', 'cash') }}',
                initialTransactionDate: '{{ old('transaction_date', $nowFormatted) }}'
            })"
            class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start"
        >
            <!-- ============================================================= -->
            <!-- KOLOM KIRI (8 COL): FORM INPUT INTERAKTIF                    -->
            <!-- ============================================================= -->
            <div class="lg:col-span-8 space-y-6">
                <form method="POST" action="{{ route('expenses.store') }}" @submit="handleSubmit($event)" class="space-y-6">
                    @csrf

                    <!-- 1. KARTU PILIHAN KATEGORI CEPAT (INTERACTIVE PRESET CARDS) -->
                    <div class="p-5 sm:p-6 rounded-3xl bg-white dark:bg-slate-850 border border-slate-200/90 dark:border-slate-800 shadow-xs space-y-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <label class="text-xs font-black uppercase tracking-wider text-slate-900 dark:text-white flex items-center gap-2">
                                    <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                                    </svg>
                                    <span>Pilih Kategori Pengeluaran <span class="text-rose-500">*</span></span>
                                </label>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">Klik salah satu kartu di bawah untuk memilih kategori secara instan</p>
                            </div>
                            
                            <span 
                                x-show="selectedCategoryName" 
                                class="inline-flex items-center gap-1 px-3 py-1 rounded-xl text-xs font-bold font-mono bg-rose-50 dark:bg-rose-950/60 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-800"
                                x-text="'Kategori: ' + selectedCategoryName"
                            ></span>
                        </div>

                        <!-- Grid Kartu Kategori Preset -->
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                            <!-- 1. Jajan -->
                            <button
                                type="button"
                                @click="selectPresetByName('jajan', 'Jajan makan siang / camilan')"
                                :class="isCategoryActive('jajan') ? 'border-rose-500 ring-2 ring-rose-500/30 bg-rose-50/60 dark:bg-rose-950/40 text-rose-900 dark:text-rose-200' : 'border-slate-200 dark:border-slate-700 hover:border-rose-300 dark:hover:border-slate-600 bg-slate-50/50 dark:bg-slate-800/60 text-slate-700 dark:text-slate-300'"
                                class="p-3.5 rounded-2xl border text-left transition-all cursor-pointer group relative overflow-hidden"
                            >
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-700 dark:bg-amber-950/80 dark:text-amber-300 flex items-center justify-center shrink-0 shadow-2xs group-hover:scale-105 transition-transform">
                                        <!-- SVG Makanan / Jajan -->
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-2.21 0-4 1.79-4 4v1h8v-1c0-2.21-1.79-4-4-4z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 16h14a2 2 0 012 2v1a1 1 0 01-1 1H4a1 1 0 01-1-1v-1a2 2 0 012-2z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v4"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="font-black text-xs">Jajan</h4>
                                        <p class="text-[10px] text-slate-400 mt-0.5 leading-tight">Makan, minum, snack</p>
                                    </div>
                                </div>
                                <div x-show="isCategoryActive('jajan')" class="absolute top-2 right-2 text-rose-600">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                </div>
                            </button>

                            <!-- 2. Beli Kuota -->
                            <button
                                type="button"
                                @click="selectPresetByName('kuota', 'Beli paket kuota data')"
                                :class="isCategoryActive('kuota') ? 'border-sky-500 ring-2 ring-sky-500/30 bg-sky-50/60 dark:bg-sky-950/40 text-sky-900 dark:text-sky-200' : 'border-slate-200 dark:border-slate-700 hover:border-sky-300 dark:hover:border-slate-600 bg-slate-50/50 dark:bg-slate-800/60 text-slate-700 dark:text-slate-300'"
                                class="p-3.5 rounded-2xl border text-left transition-all cursor-pointer group relative overflow-hidden"
                            >
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-sky-100 text-sky-700 dark:bg-sky-950/80 dark:text-sky-300 flex items-center justify-center shrink-0 shadow-2xs group-hover:scale-105 transition-transform">
                                        <!-- SVG Kuota / Internet -->
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="font-black text-xs">Beli Kuota</h4>
                                        <p class="text-[10px] text-slate-400 mt-0.5 leading-tight">Paket data internet</p>
                                    </div>
                                </div>
                                <div x-show="isCategoryActive('kuota')" class="absolute top-2 right-2 text-sky-600">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                </div>
                            </button>

                            <!-- 3. Beli Mainan -->
                            <button
                                type="button"
                                @click="selectPresetByName('mainan', 'Beli mainan / hiburan')"
                                :class="isCategoryActive('mainan') ? 'border-purple-500 ring-2 ring-purple-500/30 bg-purple-50/60 dark:bg-purple-950/40 text-purple-900 dark:text-purple-200' : 'border-slate-200 dark:border-slate-700 hover:border-purple-300 dark:hover:border-slate-600 bg-slate-50/50 dark:bg-slate-800/60 text-slate-700 dark:text-slate-300'"
                                class="p-3.5 rounded-2xl border text-left transition-all cursor-pointer group relative overflow-hidden"
                            >
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-purple-100 text-purple-700 dark:bg-purple-950/80 dark:text-purple-300 flex items-center justify-center shrink-0 shadow-2xs group-hover:scale-105 transition-transform">
                                        <!-- SVG Mainan / Game -->
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="font-black text-xs">Beli Mainan</h4>
                                        <p class="text-[10px] text-slate-400 mt-0.5 leading-tight">Mainan, hobi, game</p>
                                    </div>
                                </div>
                                <div x-show="isCategoryActive('mainan')" class="absolute top-2 right-2 text-purple-600">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                </div>
                            </button>

                            <!-- 4. Beli Bensin -->
                            <button
                                type="button"
                                @click="selectPresetByName('bensin', 'Beli bensin pertalite motor')"
                                :class="isCategoryActive('bensin') ? 'border-emerald-500 ring-2 ring-emerald-500/30 bg-emerald-50/60 dark:bg-emerald-950/40 text-emerald-900 dark:text-emerald-200' : 'border-slate-200 dark:border-slate-700 hover:border-emerald-300 dark:hover:border-slate-600 bg-slate-50/50 dark:bg-slate-800/60 text-slate-700 dark:text-slate-300'"
                                class="p-3.5 rounded-2xl border text-left transition-all cursor-pointer group relative overflow-hidden"
                            >
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-700 dark:bg-emerald-950/80 dark:text-emerald-300 flex items-center justify-center shrink-0 shadow-2xs group-hover:scale-105 transition-transform">
                                        <!-- SVG Bensin / Transport -->
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="font-black text-xs">Beli Bensin</h4>
                                        <p class="text-[10px] text-slate-400 mt-0.5 leading-tight">BBM motor, parkir</p>
                                    </div>
                                </div>
                                <div x-show="isCategoryActive('bensin')" class="absolute top-2 right-2 text-emerald-600">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                </div>
                            </button>

                            <!-- 5. Operasional Toko -->
                            <button
                                type="button"
                                @click="selectPresetByName('operasional', 'Biaya listrik / operasional toko')"
                                :class="isCategoryActive('operasional') ? 'border-indigo-500 ring-2 ring-indigo-500/30 bg-indigo-50/60 dark:bg-indigo-950/40 text-indigo-900 dark:text-indigo-200' : 'border-slate-200 dark:border-slate-700 hover:border-indigo-300 dark:hover:border-slate-600 bg-slate-50/50 dark:bg-slate-800/60 text-slate-700 dark:text-slate-300'"
                                class="p-3.5 rounded-2xl border text-left transition-all cursor-pointer group relative overflow-hidden"
                            >
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-indigo-100 text-indigo-700 dark:bg-indigo-950/80 dark:text-indigo-300 flex items-center justify-center shrink-0 shadow-2xs group-hover:scale-105 transition-transform">
                                        <!-- SVG Operasional Toko -->
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="font-black text-xs">Operasional</h4>
                                        <p class="text-[10px] text-slate-400 mt-0.5 leading-tight">Listrik, ATK, plastik</p>
                                    </div>
                                </div>
                                <div x-show="isCategoryActive('operasional')" class="absolute top-2 right-2 text-indigo-600">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                </div>
                            </button>

                            <!-- 6. Kategori Lainnya (Dropdown Selector) -->
                            <div class="p-3.5 rounded-2xl border border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/60 flex flex-col justify-center">
                                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">
                                    Pilih Manual:
                                </label>
                                <select 
                                    name="category_id" 
                                    x-model="categoryId"
                                    @change="onManualCategoryChange()"
                                    class="w-full text-xs font-bold rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white py-1.5 px-2 focus:ring-2 focus:ring-rose-500 shadow-2xs"
                                >
                                    <option value="">-- Kategori Lain --</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        @error('category_id')
                            <p class="text-[11px] text-rose-500 font-bold mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- 2. KARTU NOMINAL & TOMBOL CEPAT NOMINAL (AMOUNT SECTION) -->
                    <div class="p-5 sm:p-6 rounded-3xl bg-white dark:bg-slate-850 border border-slate-200/90 dark:border-slate-800 shadow-xs space-y-4">
                        <div class="flex items-center justify-between">
                            <label class="text-xs font-black uppercase tracking-wider text-slate-900 dark:text-white flex items-center gap-2">
                                <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span>Nominal Pengeluaran Kas <span class="text-rose-500">*</span></span>
                            </label>

                            <!-- Live Rupiah Preview -->
                            <div class="text-right">
                                <span class="text-[11px] text-slate-400 font-medium mr-1.5">Terbilang:</span>
                                <strong class="text-base font-black font-mono text-rose-600 dark:text-rose-400" x-text="formatRupiah(amount)"></strong>
                            </div>
                        </div>

                        <!-- Input Nominal Utama -->
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-4 flex items-center font-mono font-black text-rose-600 dark:text-rose-400 text-lg pointer-events-none">
                                Rp
                            </span>
                            <input
                                type="number"
                                name="amount"
                                x-model.number="amount"
                                min="1"
                                step="100"
                                required
                                placeholder="0"
                                class="w-full pl-13 pr-4 py-3.5 rounded-2xl border @error('amount') border-rose-500 ring-2 ring-rose-500/20 @else border-rose-200 dark:border-rose-900/60 @enderror bg-rose-50/30 dark:bg-rose-950/20 text-slate-900 dark:text-white font-mono font-black text-2xl focus:ring-3 focus:ring-rose-500/30 focus:border-rose-500 shadow-2xs transition-all"
                            >
                        </div>

                        <!-- Tombol Cepat Nominal (+ 5rb, + 10rb, + 20rb, + 50rb, + 100rb) -->
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Tombol Cepat Tambah Nominal:</span>
                                <button type="button" @click="amount = 0" class="text-[11px] font-bold text-slate-400 hover:text-rose-600 transition-colors">
                                    Reset (Rp 0)
                                </button>
                            </div>
                            <div class="flex items-center gap-2 flex-wrap text-xs font-mono font-bold">
                                <button type="button" @click="addAmount(5000)" class="px-3 py-1.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:border-rose-400 hover:text-rose-600 transition-colors cursor-pointer">
                                    +5.000
                                </button>
                                <button type="button" @click="addAmount(10000)" class="px-3 py-1.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:border-rose-400 hover:text-rose-600 transition-colors cursor-pointer">
                                    +10.000
                                </button>
                                <button type="button" @click="addAmount(20000)" class="px-3 py-1.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:border-rose-400 hover:text-rose-600 transition-colors cursor-pointer">
                                    +20.000
                                </button>
                                <button type="button" @click="addAmount(50000)" class="px-3 py-1.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:border-rose-400 hover:text-rose-600 transition-colors cursor-pointer">
                                    +50.000
                                </button>
                                <button type="button" @click="addAmount(100000)" class="px-3 py-1.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:border-rose-400 hover:text-rose-600 transition-colors cursor-pointer">
                                    +100.000
                                </button>
                            </div>
                        </div>

                        @error('amount')
                            <p class="text-[11px] text-rose-500 font-bold mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- 3. KARTU KETERANGAN / RINCIAN & SARAN CEPAT -->
                    <div class="p-5 sm:p-6 rounded-3xl bg-white dark:bg-slate-850 border border-slate-200/90 dark:border-slate-800 shadow-xs space-y-4">
                        <label class="text-xs font-black uppercase tracking-wider text-slate-900 dark:text-white flex items-center justify-between">
                            <span class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                <span>Keterangan / Rincian Pengeluaran <span class="text-rose-500">*</span></span>
                            </span>
                            <span class="text-[11px] font-normal text-slate-400 lowercase">contoh: jajan bakso & es teh</span>
                        </label>

                        <input
                            type="text"
                            name="notes"
                            x-model="notes"
                            required
                            placeholder="Tuliskan keterangan pengeluaran disini..."
                            class="w-full px-4 py-3 rounded-xl border @error('notes') border-rose-500 ring-2 ring-rose-500/20 @else border-slate-300 dark:border-slate-600 @enderror bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-xs font-medium focus:ring-2 focus:ring-rose-500 shadow-2xs"
                        >

                        <!-- Saran Cepat Keterangan (Suggestion Chips) -->
                        <div>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-2">
                                Contoh Cepat (Klik untuk menyalin):
                            </span>
                            <div class="flex items-center gap-1.5 flex-wrap text-xs">
                                <template x-for="sug in currentSuggestions" :key="sug">
                                    <button 
                                        type="button" 
                                        @click="notes = sug" 
                                        class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-rose-100 dark:bg-slate-800 dark:hover:bg-rose-950/40 text-slate-700 hover:text-rose-700 dark:text-slate-300 dark:hover:text-rose-300 border border-slate-200 dark:border-slate-700 text-[11px] font-medium transition-colors cursor-pointer"
                                        x-text="sug"
                                    ></button>
                                </template>
                            </div>
                        </div>

                        @error('notes')
                            <p class="text-[11px] text-rose-500 font-bold mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- 4. METODE BAYAR & TANGGAL WAKTU -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <!-- Metode Bayar -->
                        <div class="p-5 rounded-3xl bg-white dark:bg-slate-850 border border-slate-200/90 dark:border-slate-800 shadow-xs space-y-3">
                            <label class="text-xs font-black uppercase tracking-wider text-slate-900 dark:text-white flex items-center gap-2">
                                <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                                <span>Sumber Kas / Metode Bayar <span class="text-rose-500">*</span></span>
                            </label>

                            <div class="grid grid-cols-3 gap-2">
                                <!-- Tunai -->
                                <label 
                                    :class="paymentMethod === 'cash' ? 'border-rose-500 bg-rose-50/60 dark:bg-rose-950/40 text-rose-700 dark:text-rose-300 ring-2 ring-rose-500/20' : 'border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-50'"
                                    class="p-2.5 rounded-xl border text-center text-xs font-bold cursor-pointer transition-all flex flex-col items-center justify-center gap-1 shadow-2xs"
                                >
                                    <input type="radio" name="payment_method" value="cash" x-model="paymentMethod" class="sr-only">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                    <span>Tunai</span>
                                </label>

                                <!-- QRIS / E-Wallet -->
                                <label 
                                    :class="paymentMethod === 'qris' ? 'border-indigo-500 bg-indigo-50/60 dark:bg-indigo-950/40 text-indigo-700 dark:text-indigo-300 ring-2 ring-indigo-500/20' : 'border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-50'"
                                    class="p-2.5 rounded-xl border text-center text-xs font-bold cursor-pointer transition-all flex flex-col items-center justify-center gap-1 shadow-2xs"
                                >
                                    <input type="radio" name="payment_method" value="qris" x-model="paymentMethod" class="sr-only">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                    <span>QRIS</span>
                                </label>

                                <!-- Transfer Bank -->
                                <label 
                                    :class="paymentMethod === 'transfer' ? 'border-sky-500 bg-sky-50/60 dark:bg-sky-950/40 text-sky-700 dark:text-sky-300 ring-2 ring-sky-500/20' : 'border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-50'"
                                    class="p-2.5 rounded-xl border text-center text-xs font-bold cursor-pointer transition-all flex flex-col items-center justify-center gap-1 shadow-2xs"
                                >
                                    <input type="radio" name="payment_method" value="transfer" x-model="paymentMethod" class="sr-only">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                    <span>Transfer</span>
                                </label>
                            </div>

                            @error('payment_method')
                                <p class="text-[11px] text-rose-500 font-bold mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Tanggal & Waktu -->
                        <div class="p-5 rounded-3xl bg-white dark:bg-slate-850 border border-slate-200/90 dark:border-slate-800 shadow-xs space-y-3">
                            <div class="flex items-center justify-between">
                                <label class="text-xs font-black uppercase tracking-wider text-slate-900 dark:text-white flex items-center gap-2">
                                    <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <span>Waktu Transaksi <span class="text-rose-500">*</span></span>
                                </label>
                                <button type="button" @click="transactionDate = '{{ $nowFormatted }}'" class="text-[11px] font-bold text-rose-600 hover:text-rose-700">
                                    Sekarang
                                </button>
                            </div>

                            <input
                                type="datetime-local"
                                name="transaction_date"
                                x-model="transactionDate"
                                required
                                class="w-full px-3 py-2.5 rounded-xl border @error('transaction_date') border-rose-500 ring-2 ring-rose-500/20 @else border-slate-300 dark:border-slate-600 @enderror bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-xs font-medium focus:ring-2 focus:ring-rose-500 shadow-2xs"
                            >

                            @error('transaction_date')
                                <p class="text-[11px] text-rose-500 font-bold mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- TOMBOL SIMPAN PENGELUARAN -->
                    <div class="pt-2">
                        <button
                            type="submit"
                            :disabled="submitting || amount <= 0 || !notes"
                            class="w-full inline-flex items-center justify-center gap-2.5 px-6 py-4 rounded-2xl bg-rose-600 hover:bg-rose-700 text-white font-black text-sm uppercase tracking-wider transition-all shadow-lg shadow-rose-600/30 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer hover:shadow-xl"
                        >
                            <svg x-show="submitting" class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                            <svg x-show="!submitting" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span x-text="submitting ? 'Menyimpan Catatan Pengeluaran...' : 'Simpan Catatan Pengeluaran'">Simpan Catatan Pengeluaran</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- ============================================================= -->
            <!-- KOLOM KANAN (4 COL): RINGKASAN LIVE & RIWAYAT TERAKHIR       -->
            <!-- ============================================================= -->
            <div class="lg:col-span-4 space-y-6">
                <!-- 1. KARTU RINGKASAN LIVE PENGELUARAN -->
                <div class="p-6 rounded-3xl bg-gradient-to-br from-rose-500/10 via-amber-500/10 to-rose-500/5 dark:from-rose-950/40 dark:via-amber-950/30 dark:to-slate-850 border border-rose-200/80 dark:border-rose-800/60 shadow-xs space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-rose-200/60 dark:border-rose-900/40">
                        <span class="text-xs font-black uppercase tracking-wider text-rose-900 dark:text-rose-300 flex items-center gap-2">
                            <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <span>Ringkasan Kas Keluar</span>
                        </span>
                        <span class="w-2 h-2 rounded-full bg-rose-500 animate-pulse"></span>
                    </div>

                    <div class="space-y-3 text-xs">
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500 dark:text-slate-400">Kategori:</span>
                            <span class="font-bold text-slate-900 dark:text-white" x-text="selectedCategoryName || 'Belum dipilih'"></span>
                        </div>

                        <div class="flex items-center justify-between">
                            <span class="text-slate-500 dark:text-slate-400">Metode Bayar:</span>
                            <span class="font-bold text-slate-900 dark:text-white uppercase font-mono" x-text="paymentMethod"></span>
                        </div>

                        <div class="flex items-start justify-between gap-2">
                            <span class="text-slate-500 dark:text-slate-400 shrink-0">Keterangan:</span>
                            <span class="font-medium text-slate-800 dark:text-slate-200 text-right truncate max-w-[180px]" x-text="notes || '-'"></span>
                        </div>

                        <div class="pt-3 border-t border-rose-200/60 dark:border-rose-900/40 flex items-center justify-between">
                            <div>
                                <span class="text-[10px] text-slate-400 uppercase tracking-wider font-bold block">Nominal Kas:</span>
                                <span class="text-xs text-rose-600 font-semibold">Mengurangi kas toko</span>
                            </div>
                            <div class="text-right">
                                <span class="text-xl font-black font-mono text-rose-600 dark:text-rose-400" x-text="'- ' + formatRupiah(amount)"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. KARTU 5 PENGELUARAN TERAKHIR (REFERENCE) -->
                <div class="p-6 rounded-3xl bg-white dark:bg-slate-850 border border-slate-200/90 dark:border-slate-800 shadow-xs space-y-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xs font-black uppercase tracking-wider text-slate-900 dark:text-white flex items-center gap-2">
                            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>Pengeluaran Terakhir</span>
                        </h3>
                        <a href="{{ route('expenses.index') }}" class="text-[11px] font-bold text-rose-600 hover:text-rose-700">
                            Semua &rarr;
                        </a>
                    </div>

                    <div class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($recentExpenses as $rec)
                            <div class="py-2.5 first:pt-0 last:pb-0 flex items-center justify-between gap-2 text-xs">
                                <div class="min-w-0">
                                    <h5 class="font-bold text-slate-800 dark:text-slate-200 truncate">{{ $rec->notes }}</h5>
                                    <div class="flex items-center gap-1.5 text-[10px] text-slate-400 mt-0.5">
                                        <span>{{ $rec->category->name ?? 'Operasional' }}</span>
                                        <span>&bull;</span>
                                        <span>@tanggalWaktu($rec->transaction_date)</span>
                                    </div>
                                </div>
                                <span class="font-mono font-bold text-rose-600 dark:text-rose-400 shrink-0">
                                    - @rupiah($rec->total_amount)
                                </span>
                            </div>
                        @empty
                            <p class="text-xs text-slate-400 py-2">Belum ada riwayat pengeluaran kas.</p>
                        @endforelse
                    </div>
                </div>

                <!-- 3. TIPS AKUNTANSI SINGKAT -->
                <div class="p-4 rounded-2xl bg-amber-50/70 dark:bg-amber-950/30 border border-amber-200/80 dark:border-amber-800/50 text-[11px] text-amber-900 dark:text-amber-300 flex items-start gap-2.5 leading-relaxed">
                    <svg class="w-4 h-4 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span><strong>Catatan Kasir:</strong> Catat setiap pengeluaran sekecil apapun (jajan, bensin, kuota) agar laporan laba bersih dan pembukuan kas fisik konter Anda selalu akurat dan seimbang.</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Script Pengelola Form Pengeluaran (Anti-Race Condition) -->
    <script>
        window.expenseFormManager = function(config) {
            return {
                categories: config.categories || [],
                categoryId: config.initialCategoryId || '',
                amount: config.initialAmount ? Number(config.initialAmount) : 0,
                notes: config.initialNotes || '',
                paymentMethod: config.initialPaymentMethod || 'cash',
                transactionDate: config.initialTransactionDate || '',
                submitting: false,

                // Koleksi saran keterangan berdasarkan kata kunci kategori
                suggestionsMap: {
                    jajan: ['Makan Siang & Minum', 'Kopi & Gorengan', 'Snack / Cemilan Santai', 'Beli Minuman Dingin', 'Jajan Sore'],
                    kuota: ['Beli Paket Kuota 25GB', 'Kuota Data Darurat Kasir', 'Perpanjang Paket Data', 'Voucher Kuota Harian'],
                    mainan: ['Beli Mainan Anak', 'Top-up Game Santai', 'Beli Aksesoris Mainan', 'Koleksi Miniatur'],
                    bensin: ['Beli Bensin Pertalite Motor', 'Beli Pertamax', 'Bensin Antar Pesanan', 'Parkir & Tiket Kendaraan'],
                    operasional: ['Beli Token Listrik PLN Toko', 'Beli Kantong Plastik & Kresek', 'Kertas Thermal Struk Kasir', 'Lakban, Nota & ATK']
                },

                get selectedCategoryName() {
                    if (!this.categoryId) return '';
                    const found = this.categories.find(c => c.id == this.categoryId);
                    return found ? found.name : '';
                },

                get currentSuggestions() {
                    const name = this.selectedCategoryName.toLowerCase();
                    if (name.includes('jajan')) return this.suggestionsMap.jajan;
                    if (name.includes('kuota')) return this.suggestionsMap.kuota;
                    if (name.includes('mainan')) return this.suggestionsMap.mainan;
                    if (name.includes('bensin')) return this.suggestionsMap.bensin;
                    if (name.includes('operasional')) return this.suggestionsMap.operasional;
                    return ['Pengeluaran Keperluan Toko', 'Pengeluaran Pribadi', 'Biaya Transportasi', 'Lain-lain'];
                },

                isCategoryActive(keyword) {
                    const name = this.selectedCategoryName.toLowerCase();
                    return name.includes(keyword);
                },

                selectPresetByName(keyword, defaultNote) {
                    const match = this.categories.find(c => c.name.toLowerCase().includes(keyword));
                    if (match) {
                        this.categoryId = match.id;
                        if (!this.notes || this.isDefaultPresetNote(this.notes)) {
                            this.notes = defaultNote;
                        }
                    }
                },

                isDefaultPresetNote(note) {
                    const defaults = [
                        'Jajan makan siang / camilan',
                        'Beli paket kuota data',
                        'Beli mainan / hiburan',
                        'Beli bensin pertalite motor',
                        'Biaya listrik / operasional toko'
                    ];
                    return defaults.includes(note);
                },

                onManualCategoryChange() {
                    // Ketika dropdown diubah manual
                },

                addAmount(val) {
                    this.amount = Number(this.amount || 0) + Number(val);
                },

                formatRupiah(number) {
                    const num = Number(number) || 0;
                    return 'Rp ' + new Intl.NumberFormat('id-ID').format(num);
                },

                handleSubmit(e) {
                    if (this.amount <= 0) {
                        e.preventDefault();
                        alert('Nominal pengeluaran harus lebih besar dari Rp 0.');
                        return;
                    }
                    if (!this.notes.trim()) {
                        e.preventDefault();
                        alert('Keterangan rincian pengeluaran wajib diisi.');
                        return;
                    }
                    this.submitting = true;
                }
            };
        };
    </script>
</x-layouts.app>
