<x-layouts.app title="Inject Stok Voucher">
    <x-slot:header>
        Inject Stok Voucher
    </x-slot:header>

    <div class="space-y-6">
        <!-- ================================================================= -->
        <!-- 1. HEADER FORM ELEGAN & CERAH                                     -->
        <!-- ================================================================= -->
        <div class="p-6 sm:p-7 rounded-3xl bg-gradient-to-r from-indigo-50/90 via-white to-sky-50/50 dark:from-slate-850 dark:via-slate-800 dark:to-slate-850 border border-indigo-100/90 dark:border-slate-700 shadow-sm">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-13 h-13 rounded-2xl bg-gradient-to-tr from-indigo-600 to-sky-600 text-white flex items-center justify-center shadow-lg shadow-indigo-600/25 shrink-0">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2.5 flex-wrap">
                            <h2 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight">
                                Inject Stok Voucher Fisik
                            </h2>
                            <span class="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full bg-indigo-100/80 dark:bg-indigo-950/60 border border-indigo-200 dark:border-indigo-800 text-indigo-800 dark:text-indigo-300 text-xs font-bold shadow-2xs">
                                <span class="w-2 h-2 rounded-full bg-indigo-500 animate-pulse"></span>
                                <span>Satu Pintu: Langsung Potong Saldo & Terisi Stok</span>
                            </span>
                        </div>
                        <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">
                            Aktivasi paket ke voucher kosong. Saldo server otomatis terpotong detik ini dan voucher langsung siap dijual di kasir tanpa perlu ke menu produk/stok lagi.
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2.5 flex-wrap self-start sm:self-auto">
                    <x-button 
                        href="{{ route('server-deposit.mutations') }}" 
                        variant="secondary" 
                        size="sm"
                        class="bg-white hover:bg-slate-50 text-slate-700 dark:bg-slate-800 dark:text-slate-200 border-slate-200 dark:border-slate-700 font-bold text-xs shadow-2xs"
                    >
                        <svg class="w-4 h-4 mr-1.5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                        <span>Saldo Server</span>
                    </x-button>

                    <x-button 
                        href="{{ route('stocks.index') }}" 
                        variant="secondary" 
                        size="sm"
                        class="bg-white hover:bg-slate-50 text-slate-700 dark:bg-slate-800 dark:text-slate-200 border-slate-200 dark:border-slate-700 font-bold text-xs shadow-2xs"
                    >
                        <svg class="w-4 h-4 mr-1.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        <span>Log Stok</span>
                    </x-button>
                </div>
            </div>
        </div>

        @php
            $voucherProducts = $products->filter(fn($p) => 
                str_contains(strtolower($p->category->name ?? ''), 'voucher') || 
                str_contains(strtolower($p->name), 'voucher') ||
                str_contains(strtolower($p->category->name ?? ''), 'perdana') ||
                str_contains(strtolower($p->name), 'perdana')
            );
            $otherProducts = $products->reject(fn($p) => $voucherProducts->contains('id', $p->id));

            $voucherCategories = $categories->filter(fn($c) => 
                str_contains(strtolower($c->name), 'voucher') || 
                str_contains(strtolower($c->name), 'perdana') ||
                str_contains(strtolower($c->slug), 'voucher')
            );
            $otherCategories = $categories->reject(fn($c) => $voucherCategories->contains('id', $c->id));
        @endphp

        <!-- ================================================================= -->
        <!-- 2. WORKSPACE INJECT (FORM INPUT + LIVE SIMULASI SIDEBAR)          -->
        <!-- ================================================================= -->
        <div 
            x-data="injectVoucherManager({
                serverBalance: {{ (float) ($serverDeposit->balance ?? 0) }},
                products: {{ $products->map(fn($p) => [
                    'id' => $p->id,
                    'name' => $p->name,
                    'code' => $p->code,
                    'category' => $p->category->name ?? 'Umum',
                    'stock' => (int) $p->stock,
                    'unit' => $p->unit ?? 'pcs',
                    'cost_price' => (float) $p->cost_price,
                    'selling_price' => (float) $p->selling_price,
                ])->toJson() }},
                initialMode: '{{ old('is_new', $products->isEmpty() ? '1' : '0') }}',
                initialProductId: '{{ old('product_id', request('product_id', '')) }}',
                initialQty: '{{ old('quantity', 10) }}',
                initialCost: '{{ old('cost_price', '') }}',
                initialSell: '{{ old('selling_price', '') }}',
                initialName: '{{ old('new_product_name', '') }}',
                initialCatId: '{{ old('category_id', $voucherCategories->first()->id ?? '') }}',
            })"
            class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start"
        >
            <!-- ============================================================= -->
            <!-- KOLOM KIRI (8 COL): FORM INPUT INJECT VOUCHER                 -->
            <!-- ============================================================= -->
            <div class="lg:col-span-8 space-y-6">
                <!-- PILIHAN MODE INJECT: VOUCHER BARU vs VOUCHER YANG PERNAH DIBUAT -->
                <div class="p-1.5 rounded-2xl bg-slate-100 dark:bg-slate-800 flex items-center gap-1.5 border border-slate-200 dark:border-slate-700 shadow-2xs">
                    <button
                        type="button"
                        @click="setMode(false)"
                        :class="!isNew ? 'bg-white dark:bg-slate-900 text-indigo-600 dark:text-indigo-400 font-black shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 font-bold'"
                        class="flex-1 py-2.5 px-4 rounded-xl text-xs flex items-center justify-center gap-2 transition-all cursor-pointer"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                        <span>Pilih Voucher yang Pernah Di-inject (Dropdown)</span>
                    </button>

                    <button
                        type="button"
                        @click="setMode(true)"
                        :class="isNew ? 'bg-indigo-600 text-white font-black shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 font-bold'"
                        class="flex-1 py-2.5 px-4 rounded-xl text-xs flex items-center justify-center gap-2 transition-all cursor-pointer"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                        <span>+ Daftarkan & Inject Voucher Baru (Satu Pintu)</span>
                    </button>
                </div>

                <form method="POST" action="{{ route('stocks.process-inject') }}" @submit="handleSubmit($event)" class="space-y-6">
                    @csrf
                    <input type="hidden" name="is_new" :value="isNew ? '1' : '0'">

                    <!-- ========================================================= -->
                    <!-- 1A. JIKA MODE: DAFTARKAN VOUCHER BARU LANGSUNG SATU PINTU -->
                    <!-- ========================================================= -->
                    <div x-show="isNew" x-transition class="p-5 sm:p-6 rounded-3xl bg-white dark:bg-slate-850 border border-slate-200/90 dark:border-slate-800 shadow-xs space-y-4">
                        <div class="flex items-center justify-between">
                            <label class="text-xs font-black uppercase tracking-wider text-slate-900 dark:text-white flex items-center gap-2">
                                <span class="w-6 h-6 rounded-lg bg-indigo-100 text-indigo-600 dark:bg-indigo-950/60 dark:text-indigo-400 flex items-center justify-center text-xs font-bold">+</span>
                                <span>Informasi Voucher Baru <span class="text-rose-500">*</span></span>
                            </label>
                            <span class="text-[11px] font-bold text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-950/60 px-2.5 py-0.5 rounded-full border border-indigo-100 dark:border-indigo-800">
                                Otomatis terdaftar ke kasir
                            </span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-12 gap-4">
                            <!-- Nama Voucher Baru -->
                            <div class="sm:col-span-7 space-y-1.5">
                                <label class="text-xs font-bold text-slate-700 dark:text-slate-300">
                                    Nama Voucher Baru: <span class="text-rose-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    name="new_product_name"
                                    x-model="newName"
                                    placeholder="Contoh: Voucher Tri 5GB 3 Hari"
                                    class="w-full text-xs sm:text-sm font-semibold rounded-2xl border @error('new_product_name') border-rose-500 ring-2 ring-rose-500/20 @else border-slate-300 dark:border-slate-600 @enderror bg-slate-50/50 dark:bg-slate-800 text-slate-900 dark:text-white py-3 px-3.5 focus:ring-2 focus:ring-indigo-500 shadow-2xs transition-all"
                                >
                                @error('new_product_name')
                                    <p class="text-[11px] text-rose-500 font-bold">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Kategori Operator Voucher -->
                            <div class="sm:col-span-5 space-y-1.5">
                                <label class="text-xs font-bold text-slate-700 dark:text-slate-300">
                                    Operator / Kategori: <span class="text-rose-500">*</span>
                                </label>
                                <select
                                    name="category_id"
                                    x-model="newCategoryId"
                                    class="w-full text-xs sm:text-sm font-semibold rounded-2xl border @error('category_id') border-rose-500 ring-2 ring-rose-500/20 @else border-slate-300 dark:border-slate-600 @enderror bg-slate-50/50 dark:bg-slate-800 text-slate-900 dark:text-white py-3 px-3.5 focus:ring-2 focus:ring-indigo-500 shadow-2xs transition-all"
                                >
                                    @if($voucherCategories->isNotEmpty())
                                        <optgroup label="🎟️ Kategori Voucher Fisik">
                                            @foreach($voucherCategories as $c)
                                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                                            @endforeach
                                        </optgroup>
                                    @endif
                                    @if($otherCategories->isNotEmpty())
                                        <optgroup label="📦 Kategori Lainnya">
                                            @foreach($otherCategories as $c)
                                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                                            @endforeach
                                        </optgroup>
                                    @endif
                                </select>
                            </div>
                        </div>

                        <!-- Harga Jual ke Konsumen Kasir -->
                        <div class="space-y-1.5 pt-1">
                            <div class="flex items-center justify-between">
                                <label class="text-xs font-bold text-slate-700 dark:text-slate-300">
                                    Harga Jual Kasir (ke Pembeli): <span class="text-rose-500">*</span>
                                </label>
                                <span class="text-[10px] text-slate-400">Harga tarif yang dibayar pelanggan konter</span>
                            </div>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center font-bold text-slate-400 text-sm pointer-events-none">
                                    Rp
                                </span>
                                <input
                                    type="number"
                                    name="selling_price"
                                    x-model.number="sellingPrice"
                                    min="0"
                                    step="1"
                                    placeholder="Contoh: 15000"
                                    class="w-full pl-11 pr-4 py-3 rounded-2xl border @error('selling_price') border-rose-500 ring-2 ring-rose-500/20 @else border-slate-300 dark:border-slate-600 @enderror bg-slate-50/50 dark:bg-slate-800 text-slate-900 dark:text-white font-mono font-bold text-base focus:ring-2 focus:ring-indigo-500 shadow-2xs transition-all"
                                >
                            </div>
                            @error('selling_price')
                                <p class="text-[11px] text-rose-500 font-bold">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- ========================================================= -->
                    <!-- 1B. JIKA MODE: PILIH DARI VOUCHER YANG PERNAH DIBUAT      -->
                    <!-- ========================================================= -->
                    <div x-show="!isNew" x-transition class="p-5 sm:p-6 rounded-3xl bg-white dark:bg-slate-850 border border-slate-200/90 dark:border-slate-800 shadow-xs space-y-4">
                        <div class="flex items-center justify-between">
                            <label class="text-xs font-black uppercase tracking-wider text-slate-900 dark:text-white flex items-center gap-2">
                                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                                </svg>
                                <span>Pilih Voucher yang Pernah Di-inject <span class="text-rose-500">*</span></span>
                            </label>

                            <button type="button" @click="setMode(true)" class="text-xs font-bold text-indigo-600 dark:text-indigo-400 hover:underline">
                                + Daftarkan Voucher Baru
                            </button>
                        </div>

                        <!-- Dropdown Pemilihan Produk Voucher yang Sudah Ada -->
                        <div class="relative">
                            <select
                                name="product_id"
                                x-model="selectedId"
                                @change="onProductChange()"
                                class="w-full text-xs sm:text-sm font-semibold rounded-2xl border @error('product_id') border-rose-500 ring-2 ring-rose-500/20 @else border-slate-300 dark:border-slate-600 @enderror bg-slate-50/50 dark:bg-slate-800 text-slate-900 dark:text-white py-3 px-3.5 focus:ring-2 focus:ring-indigo-500 shadow-2xs transition-all"
                            >
                                <option value="">-- Pilih Voucher Yang Akan Di-inject --</option>
                                
                                @if($voucherProducts->isNotEmpty())
                                    <optgroup label="🎟️ Voucher Fisik & Kuota Operator">
                                        @foreach($voucherProducts as $p)
                                            <option value="{{ $p->id }}" {{ old('product_id', request('product_id')) == $p->id ? 'selected' : '' }}>
                                                {{ $p->name }} (Modal: @rupiah($p->cost_price) | Jual: @rupiah($p->selling_price))
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @endif

                                @if($otherProducts->isNotEmpty())
                                    <optgroup label="📦 Produk Fisik Lainnya">
                                        @foreach($otherProducts as $p)
                                            <option value="{{ $p->id }}" {{ old('product_id', request('product_id')) == $p->id ? 'selected' : '' }}>
                                                {{ $p->name }} (Modal: @rupiah($p->cost_price) | Jual: @rupiah($p->selling_price))
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @endif
                            </select>
                        </div>

                        <!-- Kartu Informasi Detail Voucher Terpilih -->
                        <div 
                            x-show="selectedProduct" 
                            x-transition
                            class="p-4 rounded-2xl bg-gradient-to-r from-indigo-50/70 via-sky-50/40 to-white dark:from-slate-800 dark:to-slate-850 border border-indigo-100 dark:border-slate-700 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3"
                        >
                            <div class="space-y-1">
                                <h4 class="font-black text-sm text-slate-900 dark:text-white" x-text="selectedProduct ? selectedProduct.name : ''"></h4>
                                <div class="flex items-center gap-2 text-xs text-slate-500">
                                    <span class="font-mono" x-text="selectedProduct ? selectedProduct.code : ''"></span>
                                    <span>&bull;</span>
                                    <span x-text="selectedProduct ? selectedProduct.category : ''"></span>
                                </div>
                            </div>

                            <div class="flex items-center gap-4 self-start sm:self-auto">
                                <div class="text-right">
                                    <span class="text-[10px] text-slate-400 uppercase tracking-wider block font-bold">Stok Saat Ini:</span>
                                    <div class="font-mono font-black text-base text-slate-900 dark:text-white">
                                        <span x-text="selectedProduct ? selectedProduct.stock : 0"></span>
                                        <span class="text-xs font-normal text-slate-500" x-text="selectedProduct ? selectedProduct.unit : 'pcs'"></span>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="text-[10px] text-slate-400 uppercase tracking-wider block font-bold">HPP Server:</span>
                                    <div class="font-mono font-bold text-xs text-indigo-600 dark:text-indigo-400">
                                        <span x-text="formatRupiah(selectedProduct ? selectedProduct.cost_price : 0)"></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @error('product_id')
                            <p class="text-[11px] text-rose-500 font-bold mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- ========================================================= -->
                    <!-- 2. KARTU JUMLAH LEMBAR INJECT & MODAL SALDO SERVER        -->
                    <!-- ========================================================= -->
                    <div class="p-5 sm:p-6 rounded-3xl bg-white dark:bg-slate-850 border border-slate-200/90 dark:border-slate-800 shadow-xs space-y-5">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <!-- Kuantitas Inject (Pcs) -->
                            <div class="space-y-2">
                                <div class="flex items-center justify-between">
                                    <label class="text-xs font-black uppercase tracking-wider text-slate-900 dark:text-white flex items-center gap-2">
                                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/>
                                        </svg>
                                        <span>Jumlah Lembar Inject <span class="text-rose-500">*</span></span>
                                    </label>
                                    <span class="text-xs font-bold text-slate-400">Satuan: pcs / lembar</span>
                                </div>

                                <div class="relative">
                                    <input
                                        type="number"
                                        name="quantity"
                                        x-model.number="quantity"
                                        min="1"
                                        required
                                        placeholder="10"
                                        class="w-full pl-4 pr-16 py-3.5 rounded-2xl border @error('quantity') border-rose-500 ring-2 ring-rose-500/20 @else border-slate-300 dark:border-slate-600 @enderror bg-slate-50/50 dark:bg-slate-800 text-slate-900 dark:text-white font-mono font-black text-2xl focus:ring-3 focus:ring-indigo-500/20 focus:border-indigo-500 shadow-2xs transition-all"
                                    >
                                    <span class="absolute inset-y-0 right-0 pr-4 flex items-center font-bold text-slate-400 text-sm pointer-events-none">
                                        pcs
                                    </span>
                                </div>

                                <!-- Tombol Cepat Kuantitas (+5, +10, +20, +50, +100) -->
                                <div class="flex items-center gap-1.5 flex-wrap pt-1">
                                    <span class="text-[10px] text-slate-400 font-bold uppercase mr-1">Cepat:</span>
                                    <template x-for="q in [5, 10, 20, 50, 100]" :key="q">
                                        <button
                                            type="button"
                                            @click="quantity = q"
                                            :class="quantity === q ? 'bg-indigo-600 text-white font-black' : 'bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold'"
                                            class="px-2.5 py-1 rounded-lg text-xs font-mono transition-all cursor-pointer"
                                            x-text="'+' + q"
                                        ></button>
                                    </template>
                                </div>
                            </div>

                            <!-- Harga Modal per Lembar (HPP Server) -->
                            <div class="space-y-2">
                                <div class="flex items-center justify-between">
                                    <label class="text-xs font-black uppercase tracking-wider text-slate-900 dark:text-white flex items-center gap-2">
                                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <span>Modal Saldo Server per Pcs <span class="text-rose-500">*</span></span>
                                    </label>
                                    <span class="text-[10px] text-slate-400 font-bold">Saldo server dipotong</span>
                                </div>

                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center font-bold text-slate-400 text-sm pointer-events-none">
                                        Rp
                                    </span>
                                    <input
                                        type="number"
                                        name="cost_price"
                                        x-model.number="costPrice"
                                        min="0"
                                        step="1"
                                        required
                                        placeholder="0"
                                        class="w-full pl-11 pr-4 py-3.5 rounded-2xl border @error('cost_price') border-rose-500 ring-2 ring-rose-500/20 @else border-slate-300 dark:border-slate-600 @enderror bg-slate-50/50 dark:bg-slate-800 text-slate-900 dark:text-white font-mono font-black text-xl focus:ring-3 focus:ring-emerald-500/20 focus:border-emerald-500 shadow-2xs transition-all"
                                    >
                                </div>

                                <p class="text-[10px] text-slate-400 pt-1">
                                    Biaya saldo server chip/distributor per 1 lembar paket kuota.
                                </p>
                            </div>
                        </div>

                        <!-- 3. Tanggal Inject & Keterangan Tambahan -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2 border-t border-slate-100 dark:border-slate-800">
                            <div>
                                <label class="text-xs font-bold text-slate-700 dark:text-slate-300 block mb-1">
                                    Waktu / Tanggal Inject:
                                </label>
                                <input
                                    type="datetime-local"
                                    name="inject_date"
                                    value="{{ old('inject_date', now()->format('Y-m-d\TH:i')) }}"
                                    class="w-full text-xs font-semibold rounded-xl border border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white py-2.5 px-3 focus:ring-2 focus:ring-indigo-500 shadow-2xs"
                                >
                            </div>

                            <div>
                                <label class="text-xs font-bold text-slate-700 dark:text-slate-300 block mb-1">
                                    Catatan / Seri Voucher (Opsional):
                                </label>
                                <input
                                    type="text"
                                    name="notes"
                                    placeholder="Contoh: Inject via Rita Tri SN 001 - 020"
                                    value="{{ old('notes') }}"
                                    class="w-full text-xs font-semibold rounded-xl border border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white py-2.5 px-3 focus:ring-2 focus:ring-indigo-500 shadow-2xs"
                                >
                            </div>
                        </div>
                    </div>

                    <!-- Tombol Aksi Submit Form -->
                    <div class="flex items-center justify-end gap-3 pt-2">
                        <x-button 
                            href="{{ route('stocks.index') }}" 
                            variant="secondary" 
                            class="rounded-2xl px-6 py-3 font-bold text-xs"
                        >
                            Batal
                        </x-button>

                        <button 
                            type="submit" 
                            :disabled="(isNew && !newName) || (!isNew && !selectedProduct) || quantity <= 0 || costPrice < 0"
                            class="inline-flex items-center gap-2 px-6 py-3.5 rounded-2xl bg-gradient-to-r from-indigo-600 to-sky-600 hover:from-indigo-700 hover:to-sky-700 text-white font-black text-xs uppercase tracking-wider shadow-lg shadow-indigo-600/30 transition-all cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed hover:scale-[1.01]"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                            <span x-text="isNew ? '⚡ Daftarkan & Potong Saldo Server' : '⚡ Konfirmasi & Potong Saldo Server'"></span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- ============================================================= -->
            <!-- KOLOM KANAN (4 COL): LIVE KALKULASI & PRATINJAU DAMPAK KEUANGAN -->
            <!-- ============================================================= -->
            <div class="lg:col-span-4 space-y-5">
                <!-- KARTU STATUS SALDO SERVER SAAT INI -->
                <div class="p-5 rounded-3xl bg-gradient-to-br from-indigo-900 via-slate-900 to-indigo-950 text-white shadow-xl space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-indigo-300 uppercase tracking-wider flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                            <span>Saldo Server Utama</span>
                        </span>
                        <span class="text-[10px] font-mono px-2 py-0.5 rounded-full bg-indigo-800/80 text-indigo-200 border border-indigo-700">
                            All Provider
                        </span>
                    </div>

                    <div>
                        <span class="text-[11px] text-indigo-300/80 block">Sisa Saldo Sebelum Inject:</span>
                        <div class="text-2xl font-black font-mono tracking-tight text-white mt-0.5">
                            @rupiah($serverDeposit->balance)
                        </div>
                    </div>

                    <div class="pt-3 border-t border-indigo-800/80 flex items-center justify-between text-xs text-indigo-300">
                        <span>Status Saldo:</span>
                        @if($serverDeposit->isLow())
                            <span class="px-2 py-0.5 rounded-full font-bold bg-rose-500/20 text-rose-300 border border-rose-500/40 text-[10px]">
                                Menipis
                            </span>
                        @else
                            <span class="px-2 py-0.5 rounded-full font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 text-[10px]">
                                Aman
                            </span>
                        @endif
                    </div>
                </div>

                <!-- KARTU KALKULASI DAMPAK LIVE -->
                <div class="p-5 rounded-3xl bg-white dark:bg-slate-850 border border-slate-200/90 dark:border-slate-800 shadow-xs space-y-4">
                    <h3 class="text-xs font-black uppercase tracking-wider text-slate-900 dark:text-white flex items-center gap-2">
                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                        <span>Pratinjau Hasil Inject</span>
                    </h3>

                    <div class="space-y-3 text-xs">
                        <!-- Total Saldo Terpotong -->
                        <div class="p-3.5 rounded-2xl bg-rose-50/70 dark:bg-rose-950/40 border border-rose-200/70 dark:border-rose-900/50 flex items-center justify-between">
                            <div>
                                <span class="text-[10px] font-bold uppercase tracking-wider text-rose-700 dark:text-rose-400 block">
                                    Saldo Terpotong:
                                </span>
                                <span class="text-[11px] text-slate-500" x-text="quantity + ' x ' + formatRupiah(costPrice)"></span>
                            </div>
                            <div class="text-right font-mono font-black text-base text-rose-600 dark:text-rose-400">
                                -<span x-text="formatRupiah(totalCost)"></span>
                            </div>
                        </div>

                        <!-- Perubahan Stok Fisik -->
                        <div class="p-3.5 rounded-2xl bg-emerald-50/70 dark:bg-emerald-950/40 border border-emerald-200/70 dark:border-emerald-900/50 flex items-center justify-between">
                            <div>
                                <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-700 dark:text-emerald-400 block">
                                    Stok Masuk Etalase:
                                </span>
                                <span class="text-[11px] text-slate-500" x-text="'Stok awal: ' + currentProductStock"></span>
                            </div>
                            <div class="text-right font-mono font-black text-base text-emerald-600 dark:text-emerald-400">
                                +<span x-text="quantity"></span> <span class="text-xs font-normal">pcs</span>
                            </div>
                        </div>

                        <!-- Simulasi Sisa Saldo Server -->
                        <div class="pt-3 border-t border-slate-100 dark:border-slate-800 space-y-2">
                            <div class="flex items-center justify-between text-slate-600 dark:text-slate-400">
                                <span>Sisa Saldo Server Baru:</span>
                                <span class="font-mono font-black text-slate-900 dark:text-white" x-text="formatRupiah(newServerBalance)"></span>
                            </div>
                            <div class="flex items-center justify-between text-slate-600 dark:text-slate-400">
                                <span>Total Stok Fisik Siap Jual:</span>
                                <span class="font-mono font-black text-slate-900 dark:text-white" x-text="newProductStock + ' pcs'"></span>
                            </div>
                            <template x-if="sellingPrice > costPrice">
                                <div class="flex items-center justify-between text-emerald-600 dark:text-emerald-400 font-bold pt-1">
                                    <span>Potensi Laba Kotor:</span>
                                    <span class="font-mono" x-text="'+' + formatRupiah(potentialProfit)"></span>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Kotak Peringatan & Edukasi Anti Double Cut -->
                    <div class="p-3.5 rounded-2xl bg-indigo-50/60 dark:bg-indigo-950/30 border border-indigo-100 dark:border-indigo-900/40 text-[11px] text-indigo-950 dark:text-indigo-200 space-y-1.5 leading-relaxed">
                        <div class="font-black flex items-center gap-1.5 text-indigo-700 dark:text-indigo-300">
                            <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
                            <span>Satu Pintu Langsung Siap Jual:</span>
                        </div>
                        <p class="text-slate-600 dark:text-slate-400">
                            Anda tidak perlu repot ke menu Stok/Produk lagi. Saldo server sebesar <strong class="text-indigo-600 dark:text-indigo-400" x-text="formatRupiah(totalCost)"></strong> langsung dialihkan menjadi stok fisik di rak. Saat voucher ini laku di kasir, sistem <strong>tidak akan memotong saldo server lagi</strong>.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- ================================================================= -->
        <!-- 3. TABEL RIWAYAT INJECT VOUCHER TERAKHIR                          -->
        <!-- ================================================================= -->
        <div class="rounded-3xl bg-white dark:bg-slate-850 border border-slate-200/90 dark:border-slate-800 shadow-xs overflow-hidden">
            <div class="p-5 sm:p-6 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between bg-slate-50/60 dark:bg-slate-800/40">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-indigo-100 text-indigo-600 dark:bg-indigo-950/60 dark:text-indigo-400 flex items-center justify-center font-bold text-sm shadow-2xs">
                        ⚡
                    </div>
                    <div>
                        <h3 class="text-sm font-black uppercase tracking-wider text-slate-900 dark:text-white">
                            Riwayat Inject Voucher Terakhir
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Catatan mutasi stok masuk yang bersumber dari saldo server digital</p>
                    </div>
                </div>

                <a href="{{ route('server-deposit.mutations') }}" class="text-xs font-bold text-indigo-600 dark:text-indigo-400 hover:underline">
                    Lihat Semua Mutasi Saldo &rarr;
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200/80 dark:divide-slate-800 text-left text-xs">
                    <thead class="bg-slate-50/90 dark:bg-slate-800/90 text-slate-500 dark:text-slate-400 uppercase font-black tracking-wider text-[11px]">
                        <tr>
                            <th class="px-6 py-4">Waktu</th>
                            <th class="px-5 py-4">Produk Voucher</th>
                            <th class="px-4 py-4 text-center">Jumlah Inject</th>
                            <th class="px-5 py-4 text-center">Transisi Stok</th>
                            <th class="px-5 py-4">Keterangan / Seri</th>
                            <th class="px-5 py-4">Operator</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800 font-medium">
                        @forelse($recentInjects as $inj)
                            <tr class="hover:bg-indigo-50/30 dark:hover:bg-slate-800/50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-slate-500">
                                    {{ $inj->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-5 py-4">
                                    <div class="font-bold text-slate-900 dark:text-white">
                                        {{ $inj->product->name ?? 'Produk Dihapus' }}
                                    </div>
                                    <span class="text-[10px] text-slate-400 font-mono">
                                        {{ $inj->product->code ?? '-' }} &bull; {{ $inj->product->category->name ?? 'Voucher' }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 text-center whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl text-[11px] font-mono font-black bg-indigo-50 text-indigo-700 dark:bg-indigo-950/60 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800 shadow-2xs">
                                        +{{ $inj->quantity }} {{ $inj->product->unit ?? 'pcs' }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-center whitespace-nowrap font-mono text-xs">
                                    <span class="text-slate-400">{{ $inj->stock_before }}</span>
                                    <span class="text-indigo-500 font-bold mx-1">&rarr;</span>
                                    <span class="font-black text-slate-900 dark:text-white">{{ $inj->stock_after }}</span>
                                </td>
                                <td class="px-5 py-4 text-slate-700 dark:text-slate-300 max-w-xs break-words">
                                    {{ $inj->notes ?? '-' }}
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap text-slate-500">
                                    {{ $inj->user->name ?? 'Kasir' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-slate-400 text-xs">
                                    Belum ada riwayat inject voucher fisik.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Script Alpine.js Handler untuk Form Inject Voucher -->
    <script>
        function injectVoucherManager(config) {
            return {
                serverBalance: config.serverBalance || 0,
                products: config.products || [],
                isNew: config.initialMode === '1',
                selectedId: config.initialProductId || '',
                newName: config.initialName || '',
                newCategoryId: config.initialCatId || '',
                sellingPrice: parseFloat(config.initialSell) || 0,
                quantity: parseInt(config.initialQty) || 10,
                costPrice: parseFloat(config.initialCost) || 0,

                init() {
                    if (!this.isNew) {
                        if (this.selectedId) {
                            this.onProductChange();
                        } else if (this.products.length > 0) {
                            this.selectedId = this.products[0].id;
                            this.onProductChange();
                        }
                    }
                },

                setMode(newMode) {
                    this.isNew = newMode;
                    if (this.isNew) {
                        if (!this.costPrice && this.selectedProduct) {
                            this.costPrice = this.selectedProduct.cost_price;
                            this.sellingPrice = this.selectedProduct.selling_price;
                        }
                    } else {
                        if (this.selectedId) {
                            this.onProductChange();
                        } else if (this.products.length > 0) {
                            this.selectedId = this.products[0].id;
                            this.onProductChange();
                        }
                    }
                },

                get selectedProduct() {
                    return this.products.find(p => p.id == this.selectedId) || null;
                },

                onProductChange() {
                    const p = this.selectedProduct;
                    if (p) {
                        this.costPrice = p.cost_price;
                        this.sellingPrice = p.selling_price;
                    }
                },

                get currentProductStock() {
                    if (this.isNew) return 0;
                    return this.selectedProduct ? this.selectedProduct.stock : 0;
                },

                get totalCost() {
                    return Math.max(0, (this.quantity || 0) * (this.costPrice || 0));
                },

                get potentialProfit() {
                    const profitPerPcs = Math.max(0, (this.sellingPrice || 0) - (this.costPrice || 0));
                    return (this.quantity || 0) * profitPerPcs;
                },

                get newServerBalance() {
                    return Math.max(0, this.serverBalance - this.totalCost);
                },

                get newProductStock() {
                    return this.currentProductStock + (parseInt(this.quantity) || 0);
                },

                formatRupiah(amount) {
                    return 'Rp ' + Number(amount || 0).toLocaleString('id-ID');
                },

                handleSubmit(e) {
                    if (this.isNew) {
                        if (!this.newName.trim()) {
                            e.preventDefault();
                            alert('Silakan isi nama voucher baru.');
                            return;
                        }
                        if (this.sellingPrice <= 0) {
                            e.preventDefault();
                            alert('Silakan tentukan harga jual kasir ke pelanggan.');
                            return;
                        }
                    } else {
                        if (!this.selectedProduct) {
                            e.preventDefault();
                            alert('Silakan pilih produk voucher yang ada atau beralih ke tombol "+ Daftarkan & Inject Voucher Baru".');
                            return;
                        }
                    }

                    if (this.quantity <= 0) {
                        e.preventDefault();
                        alert('Jumlah lembar voucher yang di-inject minimal 1 lembar.');
                        return;
                    }

                    if (this.totalCost > this.serverBalance) {
                        if (!confirm('⚠️ Perhatian: Total modal inject (' + this.formatRupiah(this.totalCost) + ') melebihi sisa saldo server saat ini (' + this.formatRupiah(this.serverBalance) + '). Tetap lanjutkan proses?')) {
                            e.preventDefault();
                            return;
                        }
                    }
                }
            };
        }
    </script>
</x-layouts.app>
