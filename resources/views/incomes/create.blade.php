<x-layouts.app title="Catat Transaksi Penjualan Baru">
    <x-slot:header>
        Catat Transaksi Penjualan Baru
    </x-slot:header>

    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Header Navigasi -->
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-2xl bg-indigo-50 dark:bg-indigo-950/60 border border-indigo-200 dark:border-indigo-800 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-black text-xl shadow-xs">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 4v16m8-8H4"/></svg>
                </div>
                <div>
                    <h2 class="text-xl font-black text-slate-900 dark:text-white tracking-tight">Catat Transaksi Penjualan Baru</h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Input penjualan pulsa, kuota, token, e-wallet & aksesoris</p>
                </div>
            </div>
            <x-button href="{{ route('incomes.index') }}" variant="secondary" size="sm">
                &larr; Kembali
            </x-button>
        </div>

        <x-card>
            <form
                method="POST"
                action="{{ route('incomes.store') }}"
                class="space-y-6"
                x-data="{
                    // Kategori & Provider State
                    activeCategory: 'Pulsa Reguler',
                    activeBrand: 'Telkomsel',
                    activeNominal: '',

                    categoriesList: [
                        'Pulsa Reguler',
                        'Paket Data / Kuota',
                        'Token Listrik PLN',
                        'Top Up E-Wallet',
                        'Voucher Game',
                        'Aksesoris HP',
                        'Jasa Servis HP',
                        'Transfer / Tarik Tunai',
                        'Lain-lain'
                    ],

                    telcoBrands: ['Telkomsel', 'Indosat', 'XL', 'Tri', 'Axis', 'Smartfren', 'by.U'],
                    walletBrands: ['DANA', 'GoPay', 'ShopeePay', 'OVO', 'SeaBank'],
                    plnBrands: ['PLN Prabayar', 'PLN Pascabayar'],
                    gameBrands: ['Mobile Legends', 'Free Fire', 'PUBG Mobile', 'Roblox', 'Genshin Impact'],

                    nominalShortcuts: ['5.000', '10.000', '20.000', '25.000', '50.000', '100.000'],

                    // Form Fields
                    selectedProductId: '{{ old('product_id', '') }}',
                    productName: '{{ old('product_name', '') }}',
                    customerTarget: '{{ old('customer_target', '') }}',
                    costPrice: {{ old('cost_price', 0) }},
                    sellingPrice: {{ old('amount', 0) }},
                    qty: {{ old('quantity', 1) }},
                    categoryId: '{{ old('category_id', '') }}',
                    paymentMethod: '{{ old('payment_method', 'cash') }}',
                    transactionDate: '{{ old('transaction_date', now()->format('Y-m-d\TH:i')) }}',
                    submitting: false,

                    // Produk dari Database
                    products: {{ $products->map(fn($p) => [
                        'id' => $p->id,
                        'name' => $p->name,
                        'code' => $p->code,
                        'cost_price' => (float)$p->cost_price,
                        'selling_price' => (float)$p->selling_price,
                        'stock' => $p->stock,
                        'unit' => $p->unit,
                        'category_id' => $p->category_id,
                        'category_name' => $p->category->name ?? 'Umum',
                    ])->toJson() }},

                    // Database Categories
                    allCategories: {{ $categories->map(fn($c) => ['id' => $c->id, 'name' => $c->name])->toJson() }},

                    // Dynamic Brand List sesuai kategori aktif
                    get currentBrands() {
                        if (this.activeCategory === 'Top Up E-Wallet') {
                            return this.walletBrands;
                        } else if (this.activeCategory === 'Token Listrik PLN') {
                            return this.plnBrands;
                        } else if (this.activeCategory === 'Voucher Game') {
                            return this.gameBrands;
                        } else if (['Pulsa Reguler', 'Paket Data / Kuota', 'Lain-lain'].includes(this.activeCategory)) {
                            return this.telcoBrands;
                        }
                        return [];
                    },

                    // Ganti Kategori
                    selectCategory(cat) {
                        this.activeCategory = cat;
                        if (cat === 'Top Up E-Wallet') {
                            this.activeBrand = 'DANA';
                        } else if (cat === 'Token Listrik PLN') {
                            this.activeBrand = 'PLN Prabayar';
                        } else if (cat === 'Voucher Game') {
                            this.activeBrand = 'Mobile Legends';
                        } else if (this.telcoBrands.length > 0) {
                            this.activeBrand = 'Telkomsel';
                        } else {
                            this.activeBrand = '';
                        }
                        this.activeNominal = '';
                        this.autoMatchCategoryDb();
                        this.findBestMatchingProduct();
                    },

                    // Ganti Brand / Provider
                    selectBrand(brand) {
                        this.activeBrand = brand;
                        this.autoMatchCategoryDb();
                        this.findBestMatchingProduct();
                    },

                    // Pilih Pintasan Nominal
                    selectNominal(nom) {
                        this.activeNominal = nom;
                        this.findBestMatchingProduct();
                    },

                    // Pencarian produk yang cocok di katalog database
                    get matchingProducts() {
                        return this.products.filter(p => {
                            const pName = p.name.toLowerCase();
                            const catName = p.category_name.toLowerCase();

                            // Filter Brand
                            if (this.activeBrand) {
                                const b = this.activeBrand.toLowerCase();
                                const matchesBrand = pName.includes(b) || catName.includes(b);
                                if (!matchesBrand) return false;
                            }

                            // Filter Kategori
                            if (this.activeCategory === 'Pulsa Reguler') {
                                return (pName.includes('pulsa') || catName.includes('pulsa')) && !pName.includes('voucher') && !pName.includes('paket');
                            } else if (this.activeCategory === 'Paket Data / Kuota') {
                                return pName.includes('data') || pName.includes('kuota') || pName.includes('paket') || pName.includes('gb') || catName.includes('kuota') || catName.includes('data');
                            } else if (this.activeCategory === 'Token Listrik PLN') {
                                return pName.includes('token') || pName.includes('pln') || catName.includes('pln') || catName.includes('token');
                            } else if (this.activeCategory === 'Top Up E-Wallet') {
                                return catName.includes('e-wallet') || catName.includes('wallet') || pName.includes('dana') || pName.includes('gopay') || pName.includes('shopee') || pName.includes('ovo') || pName.includes('seabank');
                            } else if (this.activeCategory === 'Voucher Game') {
                                return pName.includes('game') || pName.includes('diamond') || catName.includes('game');
                            }

                            return true;
                        });
                    },

                    // Pilih produk dari list katalog
                    selectProduct(prod) {
                        if (!prod) return;
                        this.selectedProductId = prod.id;
                        this.productName = prod.name;
                        this.costPrice = prod.cost_price;
                        this.sellingPrice = prod.selling_price;
                        if (prod.category_id) {
                            this.categoryId = prod.category_id;
                        }
                    },

                    // Bersihkan pilihan produk
                    clearSelectedProduct() {
                        this.selectedProductId = '';
                        this.productName = '';
                        this.costPrice = 0;
                        this.sellingPrice = 0;
                    },

                    // Otomatis cari produk terbaik saat brand/nominal diklik
                    findBestMatchingProduct() {
                        const matches = this.matchingProducts;
                        if (this.activeNominal) {
                            const rawNom = this.activeNominal.replace(/\./g, '');
                            const exact = matches.find(p => p.name.includes(this.activeNominal) || p.name.includes(rawNom) || p.selling_price == rawNom);
                            if (exact) {
                                this.selectProduct(exact);
                                return;
                            }
                        }
                        if (matches.length > 0 && !this.selectedProductId) {
                            this.selectProduct(matches[0]);
                        } else if (matches.length === 0) {
                            // Buat template nama otomatis jika belum ada di database
                            let generatedName = '';
                            if (this.activeCategory === 'Pulsa Reguler') {
                                generatedName = 'Pulsa ' + this.activeBrand + (this.activeNominal ? ' ' + this.activeNominal : '');
                            } else if (this.activeCategory === 'Top Up E-Wallet') {
                                generatedName = 'Top Up ' + this.activeBrand + (this.activeNominal ? ' ' + this.activeNominal : '');
                            } else if (this.activeCategory === 'Token Listrik PLN') {
                                generatedName = 'Token PLN ' + (this.activeNominal ? this.activeNominal : '');
                            } else if (this.activeBrand) {
                                generatedName = this.activeBrand + (this.activeNominal ? ' ' + this.activeNominal : '');
                            }
                            this.productName = generatedName;
                            this.selectedProductId = '';
                        }
                    },

                    // Cocokkan kategori database dengan kategori aktif
                    autoMatchCategoryDb() {
                        const target = (this.activeCategory + ' ' + (this.activeBrand || '')).toLowerCase();
                        const found = this.allCategories.find(c => target.includes(c.name.toLowerCase()) || c.name.toLowerCase().includes(this.activeBrand.toLowerCase()));
                        if (found) {
                            this.categoryId = found.id;
                        }
                    },

                    // Perhitungan Laba Real-time
                    get unitProfit() {
                        const jual = parseFloat(this.sellingPrice) || 0;
                        const modal = parseFloat(this.costPrice) || 0;
                        return jual - modal;
                    },

                    get totalProfit() {
                        const q = parseInt(this.qty) || 1;
                        return this.unitProfit * q;
                    },

                    get profitMargin() {
                        const jual = parseFloat(this.sellingPrice) || 0;
                        if (jual <= 0) return 0;
                        return ((this.unitProfit / jual) * 100).toFixed(1);
                    },

                    get totalRevenue() {
                        const jual = parseFloat(this.sellingPrice) || 0;
                        const q = parseInt(this.qty) || 1;
                        return jual * q;
                    },

                    init() {
                        if (this.selectedProductId) {
                            const p = this.products.find(item => item.id == this.selectedProductId);
                            if (p) this.selectProduct(p);
                        } else {
                            this.findBestMatchingProduct();
                            this.autoMatchCategoryDb();
                        }
                    }
                }"
                @submit="if(submitting) { $event.preventDefault(); return; } submitting = true;"
            >
                @csrf

                <!-- 1. KATEGORI UTAMA (Pills Sesuai Gambar) -->
                <div>
                    <div class="flex items-center gap-2 overflow-x-auto pb-2 scrollbar-thin">
                        <template x-for="cat in categoriesList" :key="cat">
                            <button
                                type="button"
                                @click="selectCategory(cat)"
                                :class="activeCategory === cat 
                                    ? 'border-indigo-600 bg-indigo-50/80 dark:bg-indigo-950/70 text-indigo-700 dark:text-indigo-300 ring-2 ring-indigo-500/20 font-bold shadow-2xs' 
                                    : 'border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-750 font-medium'"
                                class="px-4 py-2 rounded-xl border text-xs whitespace-nowrap transition-all cursor-pointer"
                                x-text="cat"
                            ></button>
                        </template>
                    </div>
                </div>

                <!-- 2. OPERATOR / PROVIDER / BRAND CHIPS -->
                <div x-show="currentBrands.length > 0" class="pt-1">
                    <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">
                        Operator / Provider / Brand
                    </label>
                    <div class="flex items-center gap-2 flex-wrap">
                        <template x-for="brand in currentBrands" :key="brand">
                            <button
                                type="button"
                                @click="selectBrand(brand)"
                                :class="activeBrand === brand 
                                    ? 'bg-slate-900 dark:bg-white text-white dark:text-slate-900 font-bold shadow-xs' 
                                    : 'bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-750 font-semibold'"
                                class="px-3.5 py-1.5 rounded-xl text-xs transition-all cursor-pointer"
                                x-text="brand"
                            ></button>
                        </template>
                    </div>
                </div>

                <!-- 3. PINTASAN NOMINAL PULSA CEPAT -->
                <div 
                    x-show="['Pulsa Reguler', 'Paket Data / Kuota', 'Token Listrik PLN', 'Top Up E-Wallet'].includes(activeCategory)" 
                    class="p-3.5 rounded-2xl bg-blue-50/50 dark:bg-blue-950/30 border border-blue-200/80 dark:border-blue-900/60"
                >
                    <span class="block text-xs font-bold text-blue-900 dark:text-blue-300 mb-2">
                        Pintasan Nominal Pulsa Cepat:
                    </span>
                    <div class="flex items-center gap-2 flex-wrap">
                        <template x-for="nom in nominalShortcuts" :key="nom">
                            <button
                                type="button"
                                @click="selectNominal(nom)"
                                :class="activeNominal === nom 
                                    ? 'border-blue-600 bg-blue-600 text-white font-bold shadow-xs' 
                                    : 'border-blue-200 dark:border-blue-800/80 bg-white dark:bg-slate-800 text-blue-700 dark:text-blue-300 hover:border-blue-400 font-semibold'"
                                class="px-3 py-1.5 rounded-xl border text-xs font-mono transition-all cursor-pointer"
                                x-text="nom"
                            ></button>
                        </template>
                    </div>
                </div>

                <!-- 4. PILIH DARI KATALOG DATABASE (PRODUK & STOK YANG TERSEDIA) -->
                <div class="p-4 rounded-2xl bg-slate-50/80 dark:bg-slate-850 border border-slate-200/80 dark:border-slate-700 space-y-3">
                    <div class="flex items-center justify-between">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                            Pilih Produk Dari Katalog
                        </label>
                        <span class="text-[11px] text-slate-500" x-text="'Tersedia: ' + products.length + ' produk aktif'"></span>
                    </div>

                    <!-- Dropdown Semua Produk -->
                    <select
                        x-model="selectedProductId"
                        @change="selectProduct(products.find(p => p.id == selectedProductId))"
                        class="block w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-750 text-slate-900 dark:text-slate-100 text-xs font-medium py-2.5 px-3.5 focus:ring-2 focus:ring-indigo-500 shadow-2xs"
                    >
                        <option value="">-- Pilih Produk Dari Katalog (Otomatis Tarik Modal & Jual) --</option>
                        <template x-for="p in products" :key="p.id">
                            <option 
                                :value="p.id" 
                                :selected="p.id == selectedProductId"
                                x-text="p.name + ' (Stok: ' + p.stock + ' ' + p.unit + ') — Modal: Rp ' + new Intl.NumberFormat('id-ID').format(p.cost_price) + ' | Jual: Rp ' + new Intl.NumberFormat('id-ID').format(p.selling_price)"
                            ></option>
                        </template>
                    </select>

                    <input type="hidden" name="product_id" :value="selectedProductId">

                    <!-- Quick Suggestions Chips jika ada produk yang cocok dengan kategori & brand -->
                    <div x-show="matchingProducts.length > 0" class="pt-1">
                        <span class="text-[11px] font-semibold text-slate-400 block mb-1.5">Rekomendasi Cepat:</span>
                        <div class="flex items-center gap-2 flex-wrap max-h-28 overflow-y-auto pr-1">
                            <template x-for="p in matchingProducts" :key="p.id">
                                <button
                                    type="button"
                                    @click="selectProduct(p)"
                                    :class="selectedProductId == p.id 
                                        ? 'bg-indigo-600 text-white font-bold ring-2 ring-indigo-500/30' 
                                        : 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 hover:border-indigo-400 font-medium'"
                                    class="px-2.5 py-1.5 rounded-lg text-[11px] flex items-center gap-1.5 transition-all cursor-pointer"
                                >
                                    <span x-text="p.name"></span>
                                    <span class="opacity-75 font-mono text-[10px]" x-text="'(Rp ' + new Intl.NumberFormat('id-ID').format(p.selling_price) + ')'"></span>
                                </button>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- 5. FORM DETAIL TRANSAKSI: NAMA PRODUK & NO TUJUAN -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Nama Produk / Paket * -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                            Nama Produk / Paket <span class="text-rose-500">*</span>
                        </label>
                        <input
                            type="text"
                            name="product_name"
                            x-model="productName"
                            required
                            placeholder="Contoh: Pulsa 25k, Freedom 30GB, Token 50k"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-750 text-slate-900 dark:text-white text-xs font-bold focus:ring-2 focus:ring-indigo-500 shadow-2xs"
                        >
                        <div x-show="selectedProductId" class="mt-1 flex items-center gap-1 text-[11px] text-emerald-600 dark:text-emerald-400 font-medium">
                            <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            <span>Tersinkron dengan katalog produk (stok otomatis dipotong)</span>
                        </div>
                    </div>

                    <!-- No. Tujuan / ID Pelanggan -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5 flex items-center justify-between">
                            <span>No. Tujuan / ID Pelanggan</span>
                            <span class="text-slate-400 font-normal lowercase">(opsional)</span>
                        </label>
                        <div class="relative">
                            <input
                                type="text"
                                name="customer_target"
                                x-model="customerTarget"
                                placeholder="0812-xxxx-xxxx / No Meteran"
                                class="w-full pl-3.5 pr-10 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-750 text-slate-900 dark:text-white text-xs font-mono font-medium focus:ring-2 focus:ring-indigo-500 shadow-2xs"
                            >
                            <span class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 pointer-events-none">
                                📞
                            </span>
                        </div>
                    </div>
                </div>

                <!-- 6. HARGA MODAL, HARGA JUAL & KUANTITAS -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-2 border-t border-slate-100 dark:border-slate-700/60">
                    <!-- Harga Modal / Beli (Rp) -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5 flex items-center justify-between">
                            <span>Harga Modal / Beli</span>
                            <span class="text-slate-400 font-normal lowercase">(HPP)</span>
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400 font-bold text-xs pointer-events-none">Rp</span>
                            <input
                                type="number"
                                name="cost_price"
                                min="0"
                                step="0.01"
                                x-model.number="costPrice"
                                placeholder="0"
                                class="w-full pl-10 pr-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-750 text-slate-700 dark:text-slate-200 text-xs font-mono font-semibold focus:ring-2 focus:ring-indigo-500 shadow-2xs"
                            >
                        </div>
                        <p class="text-[11px] text-slate-400 mt-1">Modal kulakan / harga dasar</p>
                    </div>

                    <!-- Harga Jual ke Pembeli (Rp) * -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                            Harga Jual ke Pembeli <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-indigo-500 font-bold text-xs pointer-events-none">Rp</span>
                            <input
                                type="number"
                                name="amount"
                                min="0.01"
                                step="0.01"
                                x-model.number="sellingPrice"
                                required
                                placeholder="0"
                                class="w-full pl-10 pr-3.5 py-2.5 rounded-xl border border-indigo-300 dark:border-indigo-600 bg-white dark:bg-slate-750 text-indigo-700 dark:text-indigo-300 text-xs font-mono font-black focus:ring-2 focus:ring-indigo-500 shadow-2xs"
                            >
                        </div>
                        <p class="text-[11px] text-slate-400 mt-1">Uang yang dibayar pelanggan</p>
                    </div>

                    <!-- Jumlah / Qty -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                            Jumlah (Qty) <span class="text-rose-500">*</span>
                        </label>
                        <input
                            type="number"
                            name="quantity"
                            min="1"
                            x-model.number="qty"
                            required
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-750 text-slate-900 dark:text-white text-xs font-mono font-bold focus:ring-2 focus:ring-indigo-500 shadow-2xs"
                        >
                        <p class="text-[11px] text-slate-400 mt-1">Kuantitas unit transaksi</p>
                    </div>
                </div>

                <!-- 7. KARTU LABA BERSIH REAL-TIME (OTOMATIS TARIK DARI HARGA MODAL & JUAL) -->
                <div class="p-4 rounded-2xl bg-gradient-to-r from-emerald-500/10 via-teal-500/10 to-indigo-500/10 dark:from-emerald-950/40 dark:via-teal-950/40 dark:to-indigo-950/40 border border-emerald-300/80 dark:border-emerald-700/60 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-emerald-600 text-white flex items-center justify-center font-bold text-lg shadow-sm shadow-emerald-600/30">
                            📈
                        </div>
                        <div>
                            <span class="text-xs font-bold uppercase tracking-wider text-emerald-800 dark:text-emerald-300 block">
                                Estimasi Laba Bersih Transaksi
                            </span>
                            <span class="text-[11px] text-slate-500 dark:text-slate-400">
                                Dihitung otomatis: Harga Jual - Modal Kulakan
                            </span>
                        </div>
                    </div>

                    <div class="text-left sm:text-right font-mono">
                        <div class="text-xl font-black text-emerald-600 dark:text-emerald-400 flex items-center sm:justify-end gap-2">
                            <span>+ Rp </span>
                            <span x-text="new Intl.NumberFormat('id-ID').format(totalProfit)">0</span>
                            <span class="text-xs font-bold px-2 py-0.5 rounded-full bg-emerald-100 dark:bg-emerald-900/60 text-emerald-800 dark:text-emerald-300" x-text="profitMargin + '% margin'"></span>
                        </div>
                        <span class="text-[11px] text-slate-500 font-normal">
                            Total Omzet: Rp <strong x-text="new Intl.NumberFormat('id-ID').format(totalRevenue)"></strong>
                        </span>
                    </div>
                </div>

                <!-- 8. KATEGORI TRANSAKSI, METODE PEMBAYARAN & WAKTU -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-2 border-t border-slate-100 dark:border-slate-700/60">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                            Kategori Transaksi <span class="text-rose-500">*</span>
                        </label>
                        <select
                            name="category_id"
                            x-model="categoryId"
                            required
                            class="block w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-750 text-slate-900 dark:text-slate-100 text-xs font-medium py-2.5 px-3.5 focus:ring-2 focus:ring-indigo-500 shadow-2xs"
                        >
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                            Metode Pembayaran <span class="text-rose-500">*</span>
                        </label>
                        <select
                            name="payment_method"
                            x-model="paymentMethod"
                            required
                            class="block w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-750 text-slate-900 dark:text-slate-100 text-xs font-semibold py-2.5 px-3.5 focus:ring-2 focus:ring-indigo-500 shadow-2xs"
                        >
                            <option value="cash">Tunai (Cash)</option>
                            <option value="qris">QRIS / E-Wallet</option>
                            <option value="transfer">Transfer Bank</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                            Tanggal & Waktu <span class="text-rose-500">*</span>
                        </label>
                        <input
                            type="datetime-local"
                            name="transaction_date"
                            x-model="transactionDate"
                            required
                            class="block w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-750 text-slate-900 dark:text-slate-100 text-xs font-medium py-2.5 px-3.5 focus:ring-2 focus:ring-indigo-500 shadow-2xs"
                        >
                    </div>
                </div>

                <!-- 9. TOMBOL AKSI SUBMIT -->
                <div class="pt-4 border-t border-slate-100 dark:border-slate-700/60 flex items-center justify-end gap-3">
                    <x-button href="{{ route('incomes.index') }}" variant="secondary">
                        Batal
                    </x-button>
                    <button
                        type="submit"
                        :disabled="submitting || sellingPrice <= 0"
                        class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs uppercase tracking-wider transition-all shadow-md shadow-indigo-600/20 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer"
                    >
                        <svg x-show="submitting" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                        <span x-text="submitting ? 'Menyimpan...' : 'Simpan Transaksi Penjualan'">Simpan Transaksi Penjualan</span>
                    </button>
                </div>
            </form>
        </x-card>
    </div>
</x-layouts.app>
