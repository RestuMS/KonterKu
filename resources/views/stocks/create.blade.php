<x-layouts.app title="Penyesuaian Stok Barang">
    <x-slot:header>
        Penyesuaian / Opname Stok
    </x-slot:header>

    <div class="space-y-6">
        <!-- ================================================================= -->
        <!-- 1. HEADER FORM ELEGAN & CERAH                                     -->
        <!-- ================================================================= -->
        <div class="p-6 sm:p-7 rounded-3xl bg-gradient-to-r from-blue-50/80 via-white to-indigo-50/50 dark:from-slate-850 dark:via-slate-800 dark:to-slate-850 border border-blue-100/80 dark:border-slate-700 shadow-sm">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-13 h-13 rounded-2xl bg-gradient-to-tr from-blue-600 to-indigo-600 text-white flex items-center justify-center shadow-lg shadow-blue-600/25 shrink-0">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight">
                            Penyesuaian & Opname Stok
                        </h2>
                        <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">
                            Catat barang datang dari kulakan, koreksi barang rusak/hilang, atau samakan stok dengan hasil hitung fisik di rak konter.
                        </p>
                    </div>
                </div>

                <x-button 
                    href="{{ route('stocks.index') }}" 
                    variant="secondary" 
                    size="sm"
                    class="bg-white hover:bg-slate-50 text-slate-700 dark:bg-slate-800 dark:text-slate-200 border-slate-200 dark:border-slate-700 font-bold text-xs shadow-2xs self-start sm:self-auto"
                >
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    <span>Log Mutasi Stok</span>
                </x-button>
            </div>
        </div>

        <!-- ================================================================= -->
        <!-- 2. WORKSPACE FORM 2-KOLOM (FORM INPUT + LIVE SIMULASI SIDEBAR)     -->
        <!-- ================================================================= -->
        <div 
            x-data="window.stockAdjustmentManager({
                products: {{ $products->map(fn($p) => [
                    'id' => $p->id,
                    'name' => $p->name,
                    'code' => $p->code,
                    'stock' => (int) $p->stock,
                    'min_stock' => (int) $p->min_stock,
                    'unit' => $p->unit ?? 'pcs',
                    'category' => $p->category->name ?? 'Umum'
                ])->toJson() }},
                initialProductId: '{{ old('product_id', request('product_id', '')) }}',
                initialType: '{{ old('adjustment_type', 'add') }}',
                initialValue: '{{ old('stock_value', '') }}',
                initialNotes: '{{ old('notes', '') }}'
            })"
            class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start"
        >
            <!-- ============================================================= -->
            <!-- KOLOM KIRI (8 COL): FORM INPUT UTAMA                          -->
            <!-- ============================================================= -->
            <div class="lg:col-span-8 space-y-6">
                <form method="POST" action="{{ route('stocks.store') }}" @submit="handleSubmit($event)" class="space-y-6">
                    @csrf

                    <!-- 1. KARTU PILIH PRODUK & STATUS STOK AKTIF -->
                    <div class="p-5 sm:p-6 rounded-3xl bg-white dark:bg-slate-850 border border-slate-200/90 dark:border-slate-800 shadow-xs space-y-4">
                        <div class="flex items-center justify-between">
                            <label class="text-xs font-black uppercase tracking-wider text-slate-900 dark:text-white flex items-center gap-2">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                                <span>Pilih Produk Konter <span class="text-rose-500">*</span></span>
                            </label>
                            
                            <span 
                                x-show="selectedProduct" 
                                class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs font-bold font-mono bg-blue-50 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800"
                            >
                                <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                                <span x-text="'Kode: ' + (selectedProduct ? selectedProduct.code : '-')"></span>
                            </span>
                        </div>

                        <!-- Dropdown Pemilihan Produk -->
                        <div class="relative">
                            <select
                                name="product_id"
                                x-model="selectedId"
                                @change="onProductChange()"
                                required
                                class="w-full text-xs sm:text-sm font-semibold rounded-2xl border @error('product_id') border-rose-500 ring-2 ring-rose-500/20 @else border-slate-300 dark:border-slate-600 @enderror bg-slate-50/50 dark:bg-slate-800 text-slate-900 dark:text-white py-3 px-3.5 focus:ring-2 focus:ring-blue-500 shadow-2xs transition-all"
                            >
                                <option value="">-- Pilih Produk Yang Akan Disesuaikan --</option>
                                @foreach($products as $p)
                                    <option value="{{ $p->id }}" {{ old('product_id', request('product_id')) == $p->id ? 'selected' : '' }}>
                                        {{ $p->name }} ({{ $p->code }}) &mdash; Stok Sistem: {{ $p->stock }} {{ $p->unit }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Kartu Informasi Detail Stok Produk Terpilih -->
                        <div 
                            x-show="selectedProduct" 
                            x-transition
                            class="p-4 rounded-2xl bg-gradient-to-r from-blue-50/70 via-indigo-50/40 to-white dark:from-slate-800 dark:to-slate-850 border border-blue-100 dark:border-slate-700 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3"
                        >
                            <div class="space-y-1">
                                <h4 class="font-black text-sm text-slate-900 dark:text-white" x-text="selectedProduct ? selectedProduct.name : ''"></h4>
                                <div class="flex items-center gap-2 text-xs text-slate-500">
                                    <span class="font-mono" x-text="selectedProduct ? selectedProduct.code : ''"></span>
                                    <span>&bull;</span>
                                    <span x-text="selectedProduct ? selectedProduct.category : ''"></span>
                                </div>
                            </div>

                            <div class="flex items-center gap-3 self-start sm:self-auto">
                                <div class="text-right">
                                    <span class="text-[10px] text-slate-400 uppercase tracking-wider block font-bold">Stok di Sistem:</span>
                                    <div class="font-mono font-black text-lg text-slate-900 dark:text-white">
                                        <span x-text="selectedProduct ? selectedProduct.stock : 0"></span>
                                        <span class="text-xs font-normal text-slate-500" x-text="selectedProduct ? selectedProduct.unit : 'pcs'"></span>
                                    </div>
                                </div>

                                <div>
                                    <span 
                                        :class="getStockStatusBadgeClass()" 
                                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl text-xs font-bold border shadow-2xs"
                                        x-text="getStockStatusText()"
                                    ></span>
                                </div>
                            </div>
                        </div>

                        @error('product_id')
                            <p class="text-[11px] text-rose-500 font-bold mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- 2. KARTU TINDAKAN PENYESUAIAN (3 ACTION CARDS) -->
                    <div class="p-5 sm:p-6 rounded-3xl bg-white dark:bg-slate-850 border border-slate-200/90 dark:border-slate-800 shadow-xs space-y-4">
                        <label class="text-xs font-black uppercase tracking-wider text-slate-900 dark:text-white flex items-center gap-2">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                            </svg>
                            <span>Pilih Tindakan Penyesuaian <span class="text-rose-500">*</span></span>
                        </label>

                        <!-- 3 Kartu Pilihan Tindakan -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <!-- 1. Tambah Stok -->
                            <label 
                                @click="onTypeChange('add')"
                                :class="type === 'add' ? 'border-emerald-500 ring-2 ring-emerald-500/25 bg-emerald-50/60 dark:bg-emerald-950/40 text-emerald-950 dark:text-emerald-200' : 'border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/60 hover:bg-slate-100/60 text-slate-700 dark:text-slate-300'"
                                class="p-4 rounded-2xl border text-left cursor-pointer transition-all flex flex-col justify-between gap-3 relative overflow-hidden group shadow-2xs"
                            >
                                <input type="radio" name="adjustment_type" value="add" x-model="type" class="sr-only">
                                
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-700 dark:bg-emerald-950/80 dark:text-emerald-300 flex items-center justify-center shrink-0 shadow-2xs group-hover:scale-105 transition-transform">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                                    </div>
                                    <div>
                                        <h4 class="font-black text-xs text-slate-900 dark:text-white">+ Tambah Stok</h4>
                                        <span class="text-[10px] text-emerald-700 dark:text-emerald-400 font-bold block mt-0.5">Restock / Kulakan</span>
                                    </div>
                                </div>
                                
                                <p class="text-[10px] text-slate-500 dark:text-slate-400 leading-tight">
                                    Menambah stok fisik yang masuk dari kulakan atau pesanan baru.
                                </p>

                                <div x-show="type === 'add'" class="absolute top-2.5 right-2.5 text-emerald-600">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                </div>
                            </label>

                            <!-- 2. Kurangi Stok -->
                            <label 
                                @click="onTypeChange('reduce')"
                                :class="type === 'reduce' ? 'border-rose-500 ring-2 ring-rose-500/25 bg-rose-50/60 dark:bg-rose-950/40 text-rose-950 dark:text-rose-200' : 'border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/60 hover:bg-slate-100/60 text-slate-700 dark:text-slate-300'"
                                class="p-4 rounded-2xl border text-left cursor-pointer transition-all flex flex-col justify-between gap-3 relative overflow-hidden group shadow-2xs"
                            >
                                <input type="radio" name="adjustment_type" value="reduce" x-model="type" class="sr-only">
                                
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-rose-100 text-rose-700 dark:bg-rose-950/80 dark:text-rose-300 flex items-center justify-center shrink-0 shadow-2xs group-hover:scale-105 transition-transform">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20 12H4"/></svg>
                                    </div>
                                    <div>
                                        <h4 class="font-black text-xs text-slate-900 dark:text-white">- Kurangi Stok</h4>
                                        <span class="text-[10px] text-rose-700 dark:text-rose-400 font-bold block mt-0.5">Rusak / Hilang</span>
                                    </div>
                                </div>
                                
                                <p class="text-[10px] text-slate-500 dark:text-slate-400 leading-tight">
                                    Mengurangi stok akibat barang cacat, expired, atau selisih hilang.
                                </p>

                                <div x-show="type === 'reduce'" class="absolute top-2.5 right-2.5 text-rose-600">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                </div>
                            </label>

                            <!-- 3. Set Nilai Opname -->
                            <label 
                                @click="onTypeChange('set')"
                                :class="type === 'set' ? 'border-amber-500 ring-2 ring-amber-500/25 bg-amber-50/60 dark:bg-amber-950/40 text-amber-950 dark:text-amber-200' : 'border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/60 hover:bg-slate-100/60 text-slate-700 dark:text-slate-300'"
                                class="p-4 rounded-2xl border text-left cursor-pointer transition-all flex flex-col justify-between gap-3 relative overflow-hidden group shadow-2xs"
                            >
                                <input type="radio" name="adjustment_type" value="set" x-model="type" class="sr-only">
                                
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-700 dark:bg-amber-950/80 dark:text-amber-300 flex items-center justify-center shrink-0 shadow-2xs group-hover:scale-105 transition-transform">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 8h16M4 16h16"/></svg>
                                    </div>
                                    <div>
                                        <h4 class="font-black text-xs text-slate-900 dark:text-white">= Set Opname</h4>
                                        <span class="text-[10px] text-amber-700 dark:text-amber-400 font-bold block mt-0.5">Audit Hitung Fisik</span>
                                    </div>
                                </div>
                                
                                <p class="text-[10px] text-slate-500 dark:text-slate-400 leading-tight">
                                    Menyamakan stok sistem langsung dengan jumlah fisik asli di rak.
                                </p>

                                <div x-show="type === 'set'" class="absolute top-2.5 right-2.5 text-amber-600">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                </div>
                            </label>
                        </div>

                        @error('adjustment_type')
                            <p class="text-[11px] text-rose-500 font-bold mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- 3. KARTU INPUT JUMLAH & TOMBOL CEPAT NOMINAL STOK -->
                    <div class="p-5 sm:p-6 rounded-3xl bg-white dark:bg-slate-850 border border-slate-200/90 dark:border-slate-800 shadow-xs space-y-4">
                        <div class="flex items-center justify-between">
                            <label class="text-xs font-black uppercase tracking-wider text-slate-900 dark:text-white flex items-center gap-2">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/>
                                </svg>
                                <span x-text="getValueInputLabel()"></span> <span class="text-rose-500">*</span>
                            </label>

                            <!-- Pratinjau Satuan -->
                            <span class="text-xs font-bold text-slate-400" x-text="'Satuan: ' + (selectedProduct ? selectedProduct.unit : 'pcs')"></span>
                        </div>

                        <!-- Input Nilai Kuantitas -->
                        <div class="relative">
                            <input
                                type="number"
                                name="stock_value"
                                x-model.number="stockValue"
                                min="0"
                                required
                                placeholder="0"
                                class="w-full pl-4 pr-16 py-3.5 rounded-2xl border @error('stock_value') border-rose-500 ring-2 ring-rose-500/20 @else border-slate-300 dark:border-slate-600 @enderror bg-slate-50/50 dark:bg-slate-800 text-slate-900 dark:text-white font-mono font-black text-2xl focus:ring-3 focus:ring-blue-500/20 focus:border-blue-500 shadow-2xs transition-all"
                            >
                            <span class="absolute inset-y-0 right-0 pr-4 flex items-center font-bold text-slate-400 text-sm pointer-events-none" x-text="selectedProduct ? selectedProduct.unit : 'pcs'">
                            </span>
                        </div>

                        <!-- Tombol Cepat Kuantitas (+1, +5, +10, +20, +50, +100) -->
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Tombol Cepat Tambah Kuantitas:</span>
                                <button type="button" @click="stockValue = 0" class="text-[11px] font-bold text-slate-400 hover:text-rose-600 transition-colors">
                                    Reset (0)
                                </button>
                            </div>
                            <div class="flex items-center gap-2 flex-wrap text-xs font-mono font-bold">
                                <button type="button" @click="addValue(1)" class="px-3 py-1.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:border-blue-400 hover:text-blue-600 transition-colors cursor-pointer">
                                    +1
                                </button>
                                <button type="button" @click="addValue(5)" class="px-3 py-1.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:border-blue-400 hover:text-blue-600 transition-colors cursor-pointer">
                                    +5
                                </button>
                                <button type="button" @click="addValue(10)" class="px-3 py-1.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:border-blue-400 hover:text-blue-600 transition-colors cursor-pointer">
                                    +10
                                </button>
                                <button type="button" @click="addValue(20)" class="px-3 py-1.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:border-blue-400 hover:text-blue-600 transition-colors cursor-pointer">
                                    +20
                                </button>
                                <button type="button" @click="addValue(50)" class="px-3 py-1.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:border-blue-400 hover:text-blue-600 transition-colors cursor-pointer">
                                    +50
                                </button>
                                <button type="button" @click="addValue(100)" class="px-3 py-1.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:border-blue-400 hover:text-blue-600 transition-colors cursor-pointer">
                                    +100
                                </button>
                            </div>
                        </div>

                        <!-- Bar Kalkulasi Live Hasil Perubahan -->
                        <div 
                            x-show="selectedProduct" 
                            class="p-3.5 rounded-2xl border text-xs font-mono flex items-center justify-between"
                            :class="calculatedStock < 0 ? 'bg-rose-50 border-rose-200 text-rose-800' : 'bg-blue-50/60 dark:bg-slate-800/80 border-blue-100 dark:border-slate-700 text-slate-700 dark:text-slate-300'"
                        >
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-slate-500">Stok Awal: <strong class="text-slate-900 dark:text-white" x-text="currentStock"></strong></span>
                                <span class="text-slate-400 font-normal" x-text="getCalculationOperator()"></span>
                                <span class="font-bold text-slate-500">Jumlah: <strong class="text-slate-900 dark:text-white" x-text="stockValue || 0"></strong></span>
                            </div>

                            <div class="text-right">
                                <span class="text-slate-500 font-sans text-[11px] mr-1">Estimasi Stok Akhir:</span>
                                <strong 
                                    class="text-base font-black"
                                    :class="type === 'add' ? 'text-emerald-600' : (type === 'reduce' ? (calculatedStock < 0 ? 'text-rose-600' : 'text-rose-600') : 'text-amber-600')"
                                    x-text="calculatedStock + ' ' + (selectedProduct ? selectedProduct.unit : 'pcs')"
                                ></strong>
                            </div>
                        </div>

                        @error('stock_value')
                            <p class="text-[11px] text-rose-500 font-bold mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- 4. KARTU ALASAN / KETERANGAN PENYESUAIAN (SMART REASON PILLS) -->
                    <div class="p-5 sm:p-6 rounded-3xl bg-white dark:bg-slate-850 border border-slate-200/90 dark:border-slate-800 shadow-xs space-y-4">
                        <label class="text-xs font-black uppercase tracking-wider text-slate-900 dark:text-white flex items-center justify-between">
                            <span class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                <span>Alasan / Keterangan Penyesuaian <span class="text-rose-500">*</span></span>
                            </span>
                            <span class="text-[11px] font-normal text-slate-400 lowercase">wajib untuk jejak audit stok</span>
                        </label>

                        <input
                            type="text"
                            name="notes"
                            x-model="notes"
                            required
                            placeholder="Tuliskan keterangan / alasan penyesuaian stok..."
                            class="w-full px-4 py-3 rounded-xl border @error('notes') border-rose-500 ring-2 ring-rose-500/20 @else border-slate-300 dark:border-slate-600 @enderror bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-xs font-medium focus:ring-2 focus:ring-blue-500 shadow-2xs"
                        >

                        <!-- Saran Cepat Alasan (Dinamis Sesuai Tindakan) -->
                        <div>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-2">
                                Saran Alasan Cepat (Klik untuk menyalin):
                            </span>
                            <div class="flex items-center gap-1.5 flex-wrap text-xs">
                                <template x-for="r in currentReasons" :key="r">
                                    <button 
                                        type="button" 
                                        @click="notes = r" 
                                        class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-blue-100 dark:bg-slate-800 dark:hover:bg-blue-950/40 text-slate-700 hover:text-blue-700 dark:text-slate-300 dark:hover:text-blue-300 border border-slate-200 dark:border-slate-700 text-[11px] font-medium transition-colors cursor-pointer"
                                        x-text="r"
                                    ></button>
                                </template>
                            </div>
                        </div>

                        @error('notes')
                            <p class="text-[11px] text-rose-500 font-bold mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- TOMBOL SIMPAN PENYESUAIAN STOK -->
                    <div class="pt-2">
                        <button
                            type="submit"
                            :disabled="submitting || !selectedId || stockValue === null || stockValue === '' || !notes || (type === 'reduce' && calculatedStock < 0)"
                            class="w-full inline-flex items-center justify-center gap-2.5 px-6 py-4 rounded-2xl bg-blue-600 hover:bg-blue-700 text-white font-black text-sm uppercase tracking-wider transition-all shadow-lg shadow-blue-600/30 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer hover:shadow-xl"
                        >
                            <svg x-show="submitting" class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                            <svg x-show="!submitting" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span x-text="submitting ? 'Memproses Penyesuaian...' : 'Simpan Penyesuaian Stok'">Simpan Penyesuaian Stok</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- ============================================================= -->
            <!-- KOLOM KANAN (4 COL): LIVE AUDIT PREVIEW & RIWAYAT TERAKHIR    -->
            <!-- ============================================================= -->
            <div class="lg:col-span-4 space-y-6">
                <!-- 1. KARTU RINGKASAN AUDIT MUTASI LIVE -->
                <div class="p-6 rounded-3xl bg-gradient-to-br from-blue-500/10 via-indigo-500/10 to-blue-500/5 dark:from-blue-950/40 dark:via-indigo-950/30 dark:to-slate-850 border border-blue-200/80 dark:border-blue-800/60 shadow-xs space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-blue-200/60 dark:border-blue-900/40">
                        <span class="text-xs font-black uppercase tracking-wider text-blue-900 dark:text-blue-300 flex items-center gap-2">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <span>Pratinjau Audit Stok</span>
                        </span>
                        <span class="w-2 h-2 rounded-full bg-blue-500 animate-pulse"></span>
                    </div>

                    <div class="space-y-3 text-xs">
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500 dark:text-slate-400">Produk:</span>
                            <span class="font-bold text-slate-900 dark:text-white truncate max-w-[170px]" x-text="selectedProduct ? selectedProduct.name : 'Belum dipilih'"></span>
                        </div>

                        <div class="flex items-center justify-between">
                            <span class="text-slate-500 dark:text-slate-400">Tindakan:</span>
                            <span class="font-bold font-mono uppercase" :class="getTypeColorClass()" x-text="getTypeTitle()"></span>
                        </div>

                        <div class="flex items-center justify-between">
                            <span class="text-slate-500 dark:text-slate-400">Stok Saat Ini:</span>
                            <span class="font-bold font-mono text-slate-900 dark:text-white" x-text="currentStock + ' ' + (selectedProduct ? selectedProduct.unit : 'pcs')"></span>
                        </div>

                        <div class="pt-3 border-t border-blue-200/60 dark:border-blue-900/40 flex items-center justify-between">
                            <div>
                                <span class="text-[10px] text-slate-400 uppercase tracking-wider font-bold block">Hasil Akhir:</span>
                                <span class="text-xs text-blue-600 font-semibold">Tersimpan di fisik</span>
                            </div>
                            <div class="text-right">
                                <span 
                                    class="text-xl font-black font-mono" 
                                    :class="calculatedStock < 0 ? 'text-rose-600' : 'text-blue-600 dark:text-blue-400'"
                                    x-text="calculatedStock + ' ' + (selectedProduct ? selectedProduct.unit : 'pcs')"
                                ></span>
                            </div>
                        </div>

                        <!-- Peringatan Jika Stok Minus -->
                        <div 
                            x-show="type === 'reduce' && calculatedStock < 0" 
                            class="p-2.5 rounded-xl bg-rose-50 dark:bg-rose-950/60 border border-rose-300 dark:border-rose-800 text-[11px] text-rose-800 dark:text-rose-200 font-semibold flex items-center gap-2"
                        >
                            <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            <span>Stok tidak mencukupi untuk pengurangan ini!</span>
                        </div>
                    </div>
                </div>

                <!-- 2. KARTU 5 MUTASI TERAKHIR UNTUK REFERENSI KASIR -->
                <div class="p-6 rounded-3xl bg-white dark:bg-slate-850 border border-slate-200/90 dark:border-slate-800 shadow-xs space-y-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xs font-black uppercase tracking-wider text-slate-900 dark:text-white flex items-center gap-2">
                            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>Mutasi Terakhir Toko</span>
                        </h3>
                        <a href="{{ route('stocks.index') }}" class="text-[11px] font-bold text-blue-600 hover:text-blue-700">
                            Semua &rarr;
                        </a>
                    </div>

                    <div class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($recentMutations as $rm)
                            <div class="py-2.5 first:pt-0 last:pb-0 flex items-center justify-between gap-2 text-xs">
                                <div class="min-w-0">
                                    <h5 class="font-bold text-slate-800 dark:text-slate-200 truncate">{{ $rm->product->name ?? 'Produk' }}</h5>
                                    <div class="flex items-center gap-1.5 text-[10px] text-slate-400 mt-0.5">
                                        <span class="font-medium">
                                            @if($rm->type === 'in')
                                                <span class="text-emerald-600 font-bold">Masuk (+)</span>
                                            @elseif($rm->type === 'out')
                                                <span class="text-rose-600 font-bold">Keluar (-)</span>
                                            @else
                                                <span class="text-amber-600 font-bold">Opname (=)</span>
                                            @endif
                                        </span>
                                        <span>&bull;</span>
                                        <span>{{ $rm->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                                <div class="text-right shrink-0">
                                    <span class="font-mono font-bold text-xs {{ $rm->type === 'in' ? 'text-emerald-600' : ($rm->type === 'out' ? 'text-rose-600' : 'text-amber-600') }}">
                                        {{ $rm->type === 'in' ? '+' : ($rm->type === 'out' ? '-' : '') }}{{ $rm->quantity }}
                                    </span>
                                    <span class="block text-[10px] text-slate-400 font-mono">{{ $rm->stock_before }} &rarr; {{ $rm->stock_after }}</span>
                                </div>
                            </div>
                        @empty
                            <p class="text-xs text-slate-400 py-2">Belum ada catatan mutasi stok.</p>
                        @endforelse
                    </div>
                </div>

                <!-- 3. TIPS BEST PRACTICE STOCK OPNAME -->
                <div class="p-4 rounded-2xl bg-blue-50/70 dark:bg-blue-950/30 border border-blue-200/80 dark:border-blue-800/50 text-[11px] text-blue-900 dark:text-blue-300 flex items-start gap-2.5 leading-relaxed">
                    <svg class="w-4 h-4 text-blue-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span><strong>Tips Inventaris:</strong> Lakukan stock opname rutin setiap akhir pekan atau saat pergantian shift kasir untuk memastikan jumlah voucher fisik dan kartu perdana selalu sinkron dengan sistem konter.</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Script Pengelola Penyesuaian Stok (Anti-Race Condition) -->
    <script>
        window.stockAdjustmentManager = function(config) {
            return {
                products: config.products || [],
                selectedId: config.initialProductId || '',
                type: config.initialType || 'add',
                stockValue: config.initialValue !== '' ? Number(config.initialValue) : 0,
                notes: config.initialNotes || '',
                submitting: false,

                // Koleksi saran alasan dinamis
                reasonsMap: {
                    add: [
                        'Kulakan Stok Baru dari Supplier',
                        'Restock Barang Masuk',
                        'Bonus / Hadiah dari Distributor',
                        'Koreksi Selisih Lebih'
                    ],
                    reduce: [
                        'Barang Rusak / Cacat Pabrik',
                        'Kemasan Sobek / Segel Rusak',
                        'Barang Hilang / Selisih Kurang',
                        'Kadaluarsa / Expired'
                    ],
                    set: [
                        'Hasil Hitung Opname Bulanan',
                        'Audit Rutin Akhir Pekan',
                        'Koreksi Fisik Rak Konter',
                        'Sinkronisasi Stok Awal'
                    ]
                },

                get selectedProduct() {
                    if (!this.selectedId) return null;
                    return this.products.find(p => p.id == this.selectedId) || null;
                },

                get currentStock() {
                    return this.selectedProduct ? this.selectedProduct.stock : 0;
                },

                get calculatedStock() {
                    const current = this.currentStock;
                    const val = Number(this.stockValue || 0);

                    if (this.type === 'add') {
                        return current + val;
                    } else if (this.type === 'reduce') {
                        return current - val;
                    } else if (this.type === 'set') {
                        return val;
                    }
                    return current;
                },

                get currentReasons() {
                    return this.reasonsMap[this.type] || [];
                },

                onProductChange() {
                    // Ketika produk diganti
                },

                onTypeChange(newType) {
                    this.type = newType;
                    // Auto suggest default note if empty
                    if (!this.notes || this.isDefaultReason(this.notes)) {
                        this.notes = this.reasonsMap[newType][0] || '';
                    }
                },

                isDefaultReason(note) {
                    const allDefaults = [
                        ...this.reasonsMap.add,
                        ...this.reasonsMap.reduce,
                        ...this.reasonsMap.set
                    ];
                    return allDefaults.includes(note);
                },

                addValue(val) {
                    this.stockValue = Number(this.stockValue || 0) + Number(val);
                },

                getValueInputLabel() {
                    if (this.type === 'add') return 'Jumlah Penambahan Stok';
                    if (this.type === 'reduce') return 'Jumlah Pengurangan Stok';
                    return 'Jumlah Stok Fisik Sebenarnya (Hasil Opname)';
                },

                getCalculationOperator() {
                    if (this.type === 'add') return '+';
                    if (this.type === 'reduce') return '-';
                    return '&rarr; set ke';
                },

                getTypeTitle() {
                    if (this.type === 'add') return '+ Tambah (Restock)';
                    if (this.type === 'reduce') return '- Kurang (Rusak/Hilang)';
                    return '= Set Nilai Opname';
                },

                getTypeColorClass() {
                    if (this.type === 'add') return 'text-emerald-600 dark:text-emerald-400';
                    if (this.type === 'reduce') return 'text-rose-600 dark:text-rose-400';
                    return 'text-amber-600 dark:text-amber-400';
                },

                getStockStatusBadgeClass() {
                    if (!this.selectedProduct) return '';
                    const stock = this.selectedProduct.stock;
                    const min = this.selectedProduct.min_stock;
                    if (stock <= 0) return 'bg-rose-100 text-rose-800 border-rose-200 dark:bg-rose-950 dark:text-rose-300 dark:border-rose-800';
                    if (stock <= min) return 'bg-amber-100 text-amber-800 border-amber-200 dark:bg-amber-950 dark:text-amber-300 dark:border-amber-800';
                    return 'bg-emerald-100 text-emerald-800 border-emerald-200 dark:bg-emerald-950 dark:text-emerald-300 dark:border-emerald-800';
                },

                getStockStatusText() {
                    if (!this.selectedProduct) return '';
                    const stock = this.selectedProduct.stock;
                    const min = this.selectedProduct.min_stock;
                    if (stock <= 0) return 'Stok Habis (0)';
                    if (stock <= min) return 'Stok Menipis';
                    return 'Stok Aman';
                },

                handleSubmit(e) {
                    if (!this.selectedId) {
                        e.preventDefault();
                        alert('Silakan pilih produk yang ingin disesuaikan.');
                        return;
                    }
                    if (this.stockValue === null || this.stockValue === '') {
                        e.preventDefault();
                        alert('Silakan isi jumlah nilai stok.');
                        return;
                    }
                    if (this.type === 'reduce' && this.calculatedStock < 0) {
                        e.preventDefault();
                        alert('Pengurangan melebihi stok yang tersedia saat ini.');
                        return;
                    }
                    if (!this.notes.trim()) {
                        e.preventDefault();
                        alert('Alasan penyesuaian stok wajib diisi.');
                        return;
                    }
                    this.submitting = true;
                }
            };
        };
    </script>
</x-layouts.app>
