<x-layouts.app title="Tambah Produk Baru">
    <x-slot:header>
        Tambah Produk Baru
    </x-slot:header>

    @php
        // Mengelompokkan kategori untuk pemilih visual cepat
        $pulsaCats = $categories->filter(fn($c) => str_starts_with(strtolower($c->name), 'pulsa'));
        $voucherCats = $categories->filter(fn($c) => str_starts_with(strtolower($c->name), 'voucher'));
        $tokenCats = $categories->filter(fn($c) => str_contains(strtolower($c->name), 'token'));
        $otherCats = $categories->reject(fn($c) => 
            str_starts_with(strtolower($c->name), 'pulsa') || 
            str_starts_with(strtolower($c->name), 'voucher') || 
            str_contains(strtolower($c->name), 'token')
        );
    @endphp

    <div 
        x-data="{
            categoryId: '{{ old('category_id', '') }}',
            categoryName: '',
            categorySlug: '',
            name: '{{ old('name', '') }}',
            code: '{{ old('code', '') }}',
            costPrice: '{{ old('cost_price', '') }}',
            sellingPrice: '{{ old('selling_price', '') }}',
            stock: '{{ old('stock', 0) }}',
            minStock: '{{ old('min_stock', 5) }}',
            unit: '{{ old('unit', 'pcs') }}',
            notes: '{{ old('notes', '') }}',
            isActive: {{ old('is_active', '1') ? 'true' : 'false' }},
            isAutoCode: true,
            categories: {{ Js::from($categories) }},

            init() {
                if (this.categoryId) {
                    const found = this.categories.find(c => c.id == this.categoryId);
                    if (found) {
                        this.categoryName = found.name;
                        this.categorySlug = found.slug;
                    }
                }
            },

            selectCategory(cat) {
                const oldCatName = this.categoryName;
                this.categoryId = cat.id;
                this.categoryName = cat.name;
                this.categorySlug = cat.slug;

                // Aturan Auto-fill Nama Produk
                if (!this.name || this.name.trim() === '') {
                    this.name = cat.name + ' ';
                } else if (oldCatName && this.name.startsWith(oldCatName)) {
                    // Ganti prefix lama dengan prefix baru
                    this.name = cat.name + this.name.substring(oldCatName.length);
                } else {
                    // Periksa apakah nama produk diawali kategori lain di daftar
                    let replaced = false;
                    for (const c of this.categories) {
                        if (this.name.startsWith(c.name)) {
                            this.name = cat.name + this.name.substring(c.name.length);
                            replaced = true;
                            break;
                        }
                    }
                    if (!replaced) {
                        this.name = cat.name + ' ' + this.name;
                    }
                }

                // Saran satuan unit cerdas
                const lower = cat.name.toLowerCase();
                if (lower.includes('voucher')) {
                    this.unit = 'voucher';
                } else if (lower.includes('kartu') || lower.includes('perdana')) {
                    this.unit = 'kartu';
                } else if (lower.includes('pulsa') || lower.includes('token')) {
                    this.unit = 'transaksi';
                } else if (lower.includes('aksesoris')) {
                    this.unit = 'pcs';
                }

                // Auto-generate SKU jika belum ada atau hasil generate otomatis sebelumnya
                if (!this.code || this.isAutoCode) {
                    this.generateSku();
                }

                // Fokus otomatis ke input Nama Produk dan letakkan kursor di akhir teks
                this.$nextTick(() => {
                    const input = document.getElementById('name_input');
                    if (input) {
                        input.focus();
                        const len = input.value.length;
                        input.setSelectionRange(len, len);
                    }
                });
            },

            onSelectChange(event) {
                const id = event.target.value;
                const found = this.categories.find(c => c.id == id);
                if (found) {
                    this.selectCategory(found);
                } else {
                    this.categoryId = '';
                    this.categoryName = '';
                    this.categorySlug = '';
                }
            },

            appendDenom(denom) {
                const trimmed = this.name.trim();
                // Jika sudah ada angka nominal di nama, ganti angka tersebut
                if (/\d+[\.\d]*/.test(trimmed)) {
                    this.name = trimmed.replace(/\d+[\.\d]*/, denom);
                } else {
                    this.name = trimmed + ' ' + denom;
                }
                this.generateSku();

                this.$nextTick(() => {
                    const input = document.getElementById('name_input');
                    if (input) {
                        input.focus();
                        const len = input.value.length;
                        input.setSelectionRange(len, len);
                    }
                });
            },

            setUnit(val) {
                this.unit = val;
            },

            generateSku() {
                let prefix = 'PROD';
                const lower = this.categoryName.toLowerCase();
                if (lower.includes('pulsa telkomsel')) prefix = 'PULSA-TSEL';
                else if (lower.includes('pulsa indosat')) prefix = 'PULSA-ISAT';
                else if (lower.includes('pulsa tri')) prefix = 'PULSA-TRI';
                else if (lower.includes('pulsa xl')) prefix = 'PULSA-XL';
                else if (lower.includes('pulsa axis')) prefix = 'PULSA-AXIS';
                else if (lower.includes('pulsa smartfren')) prefix = 'PULSA-SMART';
                else if (lower.includes('voucher telkomsel')) prefix = 'VCR-TSEL';
                else if (lower.includes('voucher indosat')) prefix = 'VCR-ISAT';
                else if (lower.includes('voucher tri')) prefix = 'VCR-TRI';
                else if (lower.includes('voucher xl')) prefix = 'VCR-XL';
                else if (lower.includes('voucher axis')) prefix = 'VCR-AXIS';
                else if (lower.includes('voucher smartfren')) prefix = 'VCR-SMART';
                else if (lower.includes('token')) prefix = 'PLN';
                else if (lower.includes('perdana')) prefix = 'SIM';
                else if (lower.includes('paket')) prefix = 'DATA';
                else if (this.categorySlug) prefix = this.categorySlug.toUpperCase().substring(0, 8);

                // Ekstrak angka dari nama (seperti 50.000 -> 50K atau 50000)
                const match = this.name.match(/\d+[\.\d]*/);
                let denomPart = '';
                if (match) {
                    const rawNum = match[0].replace(/\./g, '');
                    if (rawNum >= 1000) {
                        denomPart = (rawNum / 1000) + 'K';
                    } else {
                        denomPart = rawNum;
                    }
                } else {
                    denomPart = Math.floor(100 + Math.random() * 900);
                }

                this.code = prefix + '-' + denomPart;
                this.isAutoCode = true;
            },

            get profit() {
                const c = parseFloat(this.costPrice) || 0;
                const s = parseFloat(this.sellingPrice) || 0;
                return s - c;
            },

            get marginPct() {
                const c = parseFloat(this.costPrice) || 0;
                const s = parseFloat(this.sellingPrice) || 0;
                if (s <= 0) return 0;
                return (((s - c) / s) * 100).toFixed(1);
            },

            get totalStockVal() {
                const c = parseFloat(this.costPrice) || 0;
                const qty = parseInt(this.stock) || 0;
                return c * qty;
            },

            formatRupiah(num) {
                return 'Rp ' + Math.round(num).toLocaleString('id-ID');
            }
        }"
        class="max-w-6xl mx-auto space-y-6"
    >
        <!-- Top Navigation & Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 text-xs font-semibold text-slate-500 mb-1">
                    <a href="{{ route('products.index') }}" class="hover:text-indigo-600 transition-colors">Produk</a>
                    <span>/</span>
                    <span class="text-indigo-600 dark:text-indigo-400">Tambah Baru</span>
                </div>
                <h2 class="text-2xl font-black tracking-tight text-slate-900 dark:text-white flex items-center gap-2">
                    <span>Tambah Produk Konter Baru</span>
                </h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                    Klik kategori operator di bawah untuk mengisi nama produk secara otomatis dengan cepat.
                </p>
            </div>
            <div class="flex items-center gap-3">
                <x-button href="{{ route('products.index') }}" variant="secondary" size="sm">
                    &larr; Kembali ke Katalog
                </x-button>
            </div>
        </div>

        <form method="POST" action="{{ route('products.store') }}" class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            @csrf

            <!-- Left & Center: Input Fields (8 Cols) -->
            <div class="lg:col-span-8 space-y-6">

                <!-- CARD 1: KATEGORI CEPAT & NAMA PRODUK -->
                <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 sm:p-7 border border-slate-200/80 dark:border-slate-700/80 shadow-xs space-y-6">
                    <div class="border-b border-slate-100 dark:border-slate-700/60 pb-4">
                        <div class="flex items-center gap-2 text-xs font-extrabold uppercase tracking-wider text-indigo-600 dark:text-indigo-400 mb-1">
                            <span class="w-2 h-2 rounded-full bg-indigo-600"></span>
                            Langkah 1
                        </div>
                        <h3 class="text-base font-black text-slate-900 dark:text-white">Pilih Kategori & Nama Produk</h3>
                        <p class="text-xs text-slate-500 mt-0.5">
                            Klik salah satu tombol kategori di bawah ini agar otomatis mengisi awalan nama produk Anda.
                        </p>
                    </div>

                    <!-- Kategori Cepat: Pulsa Operator -->
                    <div class="space-y-2">
                        <div class="flex items-center gap-2 text-xs font-bold text-slate-700 dark:text-slate-300">
                            <span class="w-5 h-5 rounded-md bg-sky-100 dark:bg-sky-950/60 text-sky-600 dark:text-sky-400 inline-flex items-center justify-center text-[11px]">📱</span>
                            <span>Kategori Pulsa Reguler:</span>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            @foreach($pulsaCats as $cat)
                                <button
                                    type="button"
                                    @click="selectCategory({{ Js::from($cat) }})"
                                    :class="categoryId == {{ $cat->id }} 
                                        ? 'bg-sky-600 text-white shadow-md shadow-sky-600/20 ring-2 ring-sky-500 ring-offset-2 dark:ring-offset-slate-800' 
                                        : 'bg-slate-50 dark:bg-slate-750 text-slate-700 dark:text-slate-200 hover:bg-sky-50 hover:text-sky-700 hover:border-sky-300 dark:hover:bg-sky-950/40 border border-slate-200/80 dark:border-slate-700'"
                                    class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all duration-150 flex items-center gap-1.5 cursor-pointer active:scale-95"
                                >
                                    <span x-show="categoryId == {{ $cat->id }}">✓</span>
                                    <span>{{ $cat->name }}</span>
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <!-- Kategori Cepat: Voucher Fisik Operator -->
                    <div class="space-y-2 pt-2">
                        <div class="flex items-center gap-2 text-xs font-bold text-slate-700 dark:text-slate-300">
                            <span class="w-5 h-5 rounded-md bg-amber-100 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 inline-flex items-center justify-center text-[11px]">🎟️</span>
                            <span>Kategori Voucher Fisik:</span>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            @foreach($voucherCats as $cat)
                                <button
                                    type="button"
                                    @click="selectCategory({{ Js::from($cat) }})"
                                    :class="categoryId == {{ $cat->id }} 
                                        ? 'bg-amber-500 text-white shadow-md shadow-amber-500/20 ring-2 ring-amber-400 ring-offset-2 dark:ring-offset-slate-800' 
                                        : 'bg-slate-50 dark:bg-slate-750 text-slate-700 dark:text-slate-200 hover:bg-amber-50 hover:text-amber-800 hover:border-amber-300 dark:hover:bg-amber-950/40 border border-slate-200/80 dark:border-slate-700'"
                                    class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all duration-150 flex items-center gap-1.5 cursor-pointer active:scale-95"
                                >
                                    <span x-show="categoryId == {{ $cat->id }}">✓</span>
                                    <span>{{ $cat->name }}</span>
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <!-- Kategori Cepat: Token & Kategori Lainnya -->
                    <div class="space-y-2 pt-2">
                        <div class="flex items-center gap-2 text-xs font-bold text-slate-700 dark:text-slate-300">
                            <span class="w-5 h-5 rounded-md bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 inline-flex items-center justify-center text-[11px]">⚡</span>
                            <span>Token & Kategori Lainnya:</span>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            @foreach($tokenCats as $cat)
                                <button
                                    type="button"
                                    @click="selectCategory({{ Js::from($cat) }})"
                                    :class="categoryId == {{ $cat->id }} 
                                        ? 'bg-orange-500 text-white shadow-md shadow-orange-500/20 ring-2 ring-orange-400 ring-offset-2 dark:ring-offset-slate-800' 
                                        : 'bg-slate-50 dark:bg-slate-750 text-slate-700 dark:text-slate-200 hover:bg-orange-50 hover:text-orange-800 hover:border-orange-300 dark:hover:bg-orange-950/40 border border-slate-200/80 dark:border-slate-700'"
                                    class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all duration-150 flex items-center gap-1.5 cursor-pointer active:scale-95"
                                >
                                    <span x-show="categoryId == {{ $cat->id }}">✓</span>
                                    <span>⚡ {{ $cat->name }}</span>
                                </button>
                            @endforeach

                            @foreach($otherCats as $cat)
                                <button
                                    type="button"
                                    @click="selectCategory({{ Js::from($cat) }})"
                                    :class="categoryId == {{ $cat->id }} 
                                        ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/20 ring-2 ring-indigo-500 ring-offset-2 dark:ring-offset-slate-800' 
                                        : 'bg-slate-50 dark:bg-slate-750 text-slate-700 dark:text-slate-200 hover:bg-indigo-50 hover:text-indigo-800 hover:border-indigo-300 dark:hover:bg-indigo-950/40 border border-slate-200/80 dark:border-slate-700'"
                                    class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all duration-150 flex items-center gap-1.5 cursor-pointer active:scale-95"
                                >
                                    <span x-show="categoryId == {{ $cat->id }}">✓</span>
                                    <span>{{ $cat->name }}</span>
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <!-- Dropdown Kategori & Kode SKU -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 pt-3">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 mb-1.5">
                                Kategori Terpilih <span class="text-rose-500">*</span>
                            </label>
                            <select
                                name="category_id"
                                x-model="categoryId"
                                @change="onSelectChange($event)"
                                required
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-750 text-slate-900 dark:text-white text-xs font-semibold focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 shadow-2xs"
                            >
                                <option value="">-- Pilih Kategori Produk --</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-[11px] text-slate-400 mt-1">Dapat dipilih melalui tombol cepat di atas atau dropdown ini.</p>
                        </div>

                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300">
                                    Kode / SKU / Barcode <span class="text-rose-500">*</span>
                                </label>
                                <button
                                    type="button"
                                    @click="generateSku()"
                                    class="text-[11px] text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 font-bold inline-flex items-center gap-1 cursor-pointer"
                                >
                                    ⚡ Auto Generate
                                </button>
                            </div>
                            <input
                                type="text"
                                name="code"
                                x-model="code"
                                @input="isAutoCode = false"
                                placeholder="Contoh: PULSA-TSEL-50K"
                                required
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-750 text-slate-900 dark:text-white text-xs font-mono font-bold focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 shadow-2xs"
                            >
                            <p class="text-[11px] text-slate-400 mt-1">Kode unik untuk barcode atau SKU inventaris.</p>
                        </div>
                    </div>

                    <!-- Input Nama Produk dengan Prefix Cerdas -->
                    <div class="pt-2">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 mb-1.5">
                            Nama Lengkap Produk <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <input
                                type="text"
                                id="name_input"
                                name="name"
                                x-model="name"
                                @input="isAutoCode = false"
                                placeholder="Nama produk (otomatis terisi saat memilih kategori)..."
                                required
                                class="w-full px-4 py-3 rounded-2xl border-2 border-indigo-200 dark:border-indigo-900/60 bg-indigo-50/20 dark:bg-indigo-950/20 text-slate-900 dark:text-white text-sm font-bold focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 shadow-2xs"
                            >
                        </div>
                        
                        <!-- Quick Denomination Buttons (Nominal Cepat) -->
                        <div class="mt-3 flex items-center gap-1.5 flex-wrap">
                            <span class="text-[11px] font-semibold text-slate-400">Pilihan Nominal Cepat:</span>
                            @foreach(['5.000', '10.000', '15.000', '20.000', '25.000', '50.000', '100.000'] as $denom)
                                <button
                                    type="button"
                                    @click="appendDenom('{{ $denom }}')"
                                    class="px-2.5 py-1 rounded-lg text-[11px] font-bold font-mono bg-slate-100 hover:bg-indigo-100 hover:text-indigo-700 dark:bg-slate-700 dark:hover:bg-indigo-950/60 dark:hover:text-indigo-300 text-slate-700 dark:text-slate-300 transition-colors cursor-pointer"
                                >
                                    + {{ $denom }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- CARD 2: PENETAPAN HARGA & MANAJEMEN STOK -->
                <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 sm:p-7 border border-slate-200/80 dark:border-slate-700/80 shadow-xs space-y-6">
                    <div class="border-b border-slate-100 dark:border-slate-700/60 pb-4">
                        <div class="flex items-center gap-2 text-xs font-extrabold uppercase tracking-wider text-emerald-600 dark:text-emerald-400 mb-1">
                            <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
                            Langkah 2
                        </div>
                        <h3 class="text-base font-black text-slate-900 dark:text-white">Harga Modal, Harga Jual & Persediaan Stok</h3>
                        <p class="text-xs text-slate-500 mt-0.5">
                            Tentukan nominal harga beli kulakan dan harga jual untuk pembeli di kasir konter.
                        </p>
                    </div>

                    <!-- Input Harga Modal & Harga Jual -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <!-- Harga Modal -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 mb-1.5">
                                Harga Modal (Kulakan) <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative rounded-xl shadow-2xs">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 text-xs font-bold">
                                    Rp
                                </div>
                                <input
                                    type="number"
                                    name="cost_price"
                                    x-model="costPrice"
                                    min="0"
                                    step="0.01"
                                    placeholder="Contoh: 49500"
                                    required
                                    class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-750 text-slate-900 dark:text-white text-xs font-mono font-bold focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                >
                            </div>
                            <p class="text-[11px] text-slate-400 mt-1">Biaya modal beli per 1 unit/transaksi.</p>
                        </div>

                        <!-- Harga Jual -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 mb-1.5">
                                Harga Jual (Pelanggan) <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative rounded-xl shadow-2xs">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 text-xs font-bold">
                                    Rp
                                </div>
                                <input
                                    type="number"
                                    name="selling_price"
                                    x-model="sellingPrice"
                                    min="0"
                                    step="0.01"
                                    placeholder="Contoh: 52000"
                                    required
                                    class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-750 text-slate-900 dark:text-white text-xs font-mono font-bold focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                >
                            </div>
                            <p class="text-[11px] text-slate-400 mt-1">Harga yang dibayar pembeli di konter.</p>
                        </div>
                    </div>

                    <!-- Input Stok Awal, Min Stok, & Satuan Unit -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 pt-2">
                        <!-- Stok Awal -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 mb-1.5">
                                Stok Awal <span class="text-rose-500">*</span>
                            </label>
                            <input
                                type="number"
                                name="stock"
                                x-model="stock"
                                min="0"
                                placeholder="0"
                                required
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-750 text-slate-900 dark:text-white text-xs font-bold focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 shadow-2xs"
                            >
                            <p class="text-[11px] text-slate-400 mt-1">Untuk pulsa/token isi 0 atau 999.</p>
                        </div>

                        <!-- Batas Minimum Stok -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 mb-1.5">
                                Batas Minimum Stok <span class="text-rose-500">*</span>
                            </label>
                            <input
                                type="number"
                                name="min_stock"
                                x-model="minStock"
                                min="0"
                                placeholder="5"
                                required
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-750 text-slate-900 dark:text-white text-xs font-bold focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 shadow-2xs"
                            >
                            <p class="text-[11px] text-slate-400 mt-1">Muncul notifikasi jika tersisa &le; angka ini.</p>
                        </div>

                        <!-- Satuan Unit -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 mb-1.5">
                                Satuan Unit <span class="text-rose-500">*</span>
                            </label>
                            <input
                                type="text"
                                name="unit"
                                x-model="unit"
                                placeholder="pcs / voucher"
                                required
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-750 text-slate-900 dark:text-white text-xs font-bold focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 shadow-2xs"
                            >
                            <!-- Unit chips -->
                            <div class="mt-2 flex items-center gap-1 flex-wrap">
                                @foreach(['pcs', 'voucher', 'kartu', 'transaksi', 'lembar'] as $u)
                                    <button
                                        type="button"
                                        @click="setUnit('{{ $u }}')"
                                        :class="unit === '{{ $u }}' ? 'bg-indigo-600 text-white' : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300'"
                                        class="px-2 py-0.5 rounded text-[10px] font-semibold transition-colors cursor-pointer"
                                    >
                                        {{ $u }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Catatan Tambahan (Opsional) -->
                    <div class="pt-2">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 mb-1.5">
                            Catatan Tambahan (Opsional)
                        </label>
                        <textarea
                            name="notes"
                            x-model="notes"
                            rows="2"
                            placeholder="Keterangan tambahan masa aktif kuota, garansi, atau detail produk..."
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-750 text-slate-900 dark:text-white text-xs focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 shadow-2xs"
                        ></textarea>
                    </div>
                </div>
            </div>

            <!-- Right Column: Live Profit Preview & Actions (4 Cols) -->
            <div class="lg:col-span-4 space-y-6">

                <!-- STICKY CARD: LIVE PREVIEW & KALKULATOR LABA -->
                <div class="sticky top-20 space-y-6">
                    
                    <!-- Live Product Card Preview -->
                    <div class="bg-gradient-to-br from-slate-900 via-indigo-950 to-slate-900 rounded-3xl p-6 text-white border border-indigo-900/50 shadow-xl shadow-indigo-950/20 relative overflow-hidden">
                        <div class="absolute -right-8 -top-8 w-32 h-32 bg-indigo-500/10 rounded-full blur-2xl pointer-events-none"></div>

                        <div class="flex items-center justify-between text-xs mb-3">
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-white/10 border border-white/15 text-[11px] font-semibold text-indigo-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                                Live Preview Kartu
                            </span>
                            <span class="font-mono text-indigo-300 text-xs" x-text="code || 'SKU-KODE'"></span>
                        </div>

                        <!-- Card Content -->
                        <div class="my-4">
                            <span class="text-[11px] uppercase tracking-wider font-bold text-indigo-400 block" x-text="categoryName || 'Pilih Kategori'"></span>
                            <h4 class="text-lg font-black text-white mt-1 line-clamp-2" x-text="name || 'Nama Produk Konter'"></h4>
                            
                            <div class="mt-4 flex items-baseline justify-between pt-3 border-t border-white/10">
                                <div>
                                    <span class="text-[10px] text-slate-400 uppercase font-bold block">Harga Jual</span>
                                    <span class="text-xl font-black text-emerald-400 font-mono" x-text="formatRupiah(sellingPrice || 0)"></span>
                                </div>
                                <div class="text-right">
                                    <span class="text-[10px] text-slate-400 uppercase font-bold block">Stok Tersedia</span>
                                    <span class="text-sm font-bold text-white font-mono" x-text="(stock || 0) + ' ' + (unit || 'pcs')"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Real-time Financial Breakdown -->
                        <div class="mt-4 pt-4 border-t border-white/10 space-y-2.5 text-xs bg-white/5 rounded-2xl p-3.5 backdrop-blur-xs">
                            <div class="flex items-center justify-between text-slate-300">
                                <span>Harga Modal:</span>
                                <span class="font-mono font-semibold" x-text="formatRupiah(costPrice || 0)"></span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-slate-300">Estimasi Laba/Unit:</span>
                                <span class="font-mono font-black" :class="profit >= 0 ? 'text-emerald-400' : 'text-rose-400'" x-text="(profit >= 0 ? '+' : '') + formatRupiah(profit)"></span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-slate-300">Margin Keuntungan:</span>
                                <span class="font-mono font-black" :class="profit >= 0 ? 'text-emerald-400' : 'text-rose-400'" x-text="marginPct + '%'"></span>
                            </div>
                            <div class="flex items-center justify-between pt-2 border-t border-white/10 text-slate-300">
                                <span>Total Modal Stok:</span>
                                <span class="font-mono font-semibold text-white" x-text="formatRupiah(totalStockVal)"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Action Card: Status & Submit Button -->
                    <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 border border-slate-200/80 dark:border-slate-700/80 shadow-xs space-y-5">
                        <!-- Toggle Switch: Aktifkan Produk -->
                        <div>
                            <label class="flex items-start gap-3 cursor-pointer select-none">
                                <div class="relative inline-flex items-center cursor-pointer mt-0.5">
                                    <input type="checkbox" name="is_active" value="1" x-model="isActive" class="sr-only peer">
                                    <div class="w-11 h-6 bg-slate-200 peer-focus:outline-hidden rounded-full peer dark:bg-slate-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-slate-600 peer-checked:bg-indigo-600"></div>
                                </div>
                                <div>
                                    <span class="text-xs font-bold text-slate-800 dark:text-slate-200 block">Status Produk Aktif</span>
                                    <span class="text-[11px] text-slate-500 block leading-tight">Dapat dipilih dan ditransaksikan langsung pada modul kasir.</span>
                                </div>
                            </label>
                        </div>

                        <!-- Tombol Submit & Batal -->
                        <div class="pt-3 border-t border-slate-100 dark:border-slate-700/60 space-y-2.5">
                            <button
                                type="submit"
                                class="w-full py-3 px-4 rounded-xl text-xs font-bold text-white bg-gradient-to-r from-indigo-600 via-indigo-700 to-indigo-800 hover:from-indigo-700 hover:to-indigo-900 shadow-md shadow-indigo-600/25 active:scale-98 transition-all flex items-center justify-center gap-2 cursor-pointer"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span>Simpan Produk Baru</span>
                            </button>

                            <a
                                href="{{ route('products.index') }}"
                                class="w-full py-2.5 px-4 rounded-xl text-xs font-bold text-slate-600 dark:text-slate-400 bg-slate-100 dark:bg-slate-750 hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors flex items-center justify-center"
                            >
                                Batal
                            </a>
                        </div>
                    </div>

                </div>

            </div>
        </form>
    </div>
</x-layouts.app>
