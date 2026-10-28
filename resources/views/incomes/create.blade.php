<x-layouts.app title="Catat Transaksi Penjualan Baru">
    <x-slot:header>
        Catat Transaksi Penjualan Baru
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

        $productData = $products->map(function($p) {
            $catSlug = strtolower($p->category->slug ?? '');
            $catName = strtolower($p->category->name ?? '');
            $pName = strtolower($p->name);

            // Pengelompokan Kategori Terpisah
            $group = 'other';
            if (str_contains($catSlug, 'pulsa') || (str_contains($pName, 'pulsa') && !str_contains($pName, 'voucher') && !str_contains($pName, 'paket'))) {
                $group = 'pulsa';
            } elseif (str_contains($catSlug, 'paket') || str_contains($catSlug, 'internet') || str_contains($pName, 'paket') || str_contains($pName, 'kuota') || str_contains($pName, 'gb')) {
                $group = 'paket_data';
            } elseif (str_contains($catSlug, 'voucher') || str_contains($pName, 'voucher')) {
                $group = 'voucher';
            } elseif (str_contains($catSlug, 'perdana') || str_contains($pName, 'perdana') || str_contains($pName, 'kartu')) {
                $group = 'perdana';
            } elseif (str_contains($catSlug, 'token') || str_contains($catSlug, 'pln') || str_contains($pName, 'token') || str_contains($pName, 'pln')) {
                $group = 'token_pln';
            } elseif (str_contains($catSlug, 'wallet') || str_contains($catSlug, 'dana') || str_contains($catSlug, 'gopay') || str_contains($catSlug, 'shopee') || str_contains($catSlug, 'ovo') || str_contains($catSlug, 'seabank') || str_contains($pName, 'wallet') || str_contains($pName, 'top up')) {
                $group = 'ewallet';
            }

            // Identifikasi Brand / Provider
            $brand = 'Umum';
            if (str_contains($catSlug, 'by.u') || str_contains($pName, 'by.u') || str_contains($pName, 'byu')) $brand = 'By.U';
            elseif (str_contains($catSlug, 'telkomsel') || str_contains($pName, 'telkomsel') || str_contains($pName, 'tsel')) $brand = 'Telkomsel';
            elseif (str_contains($catSlug, 'indosat') || str_contains($pName, 'indosat') || str_contains($pName, 'im3') || str_contains($pName, 'isat')) $brand = 'Indosat';
            elseif (str_contains($catSlug, 'xl') || str_contains($pName, 'xl')) $brand = 'XL';
            elseif (str_contains($catSlug, 'axis') || str_contains($pName, 'axis')) $brand = 'Axis';
            elseif (str_contains($catSlug, 'tri') || str_contains($pName, 'tri') || str_contains($pName, '3')) $brand = 'Tri';
            elseif (str_contains($catSlug, 'smartfren') || str_contains($pName, 'smartfren') || str_contains($pName, 'smart')) $brand = 'Smartfren';
            elseif (str_contains($catSlug, 'dana') || str_contains($pName, 'dana')) $brand = 'DANA';
            elseif (str_contains($catSlug, 'gopay') || str_contains($pName, 'gopay')) $brand = 'GoPay';
            elseif (str_contains($catSlug, 'shopee') || str_contains($pName, 'shopee')) $brand = 'ShopeePay';
            elseif (str_contains($catSlug, 'ovo') || str_contains($pName, 'ovo')) $brand = 'OVO';
            elseif (str_contains($catSlug, 'seabank') || str_contains($pName, 'seabank')) $brand = 'SeaBank';
            elseif (str_contains($catSlug, 'pln') || str_contains($pName, 'pln')) $brand = 'PLN';

            return [
                'id' => $p->id,
                'name' => $p->name,
                'code' => $p->code,
                'cost_price' => (float)$p->cost_price,
                'selling_price' => (float)$p->selling_price,
                'stock' => (int)$p->stock,
                'unit' => $p->unit,
                'category_id' => $p->category_id,
                'category_name' => $p->category->name ?? 'Umum',
                'group' => $group,
                'brand' => $brand,
            ];
        })->values();

        $categoryData = $cleanCategories->map(fn($c) => [
            'id' => $c->id,
            'name' => $c->name,
            'slug' => $c->slug,
        ])->values();
    @endphp

    <!-- Script Alpine.js Data Function (Global Window Scope) -->
    <script>
        window.posCashier = function() {
            return {
                // Mode Utama: 'catalog' (Filter by Produk) | 'transfer' (Transfer & Tarik Tunai)
                mode: 'catalog',

                // Filter Kategori Produk (Hanya Produk Konter)
                activeCategory: 'pulsa', // 'pulsa', 'paket_data', 'voucher', 'perdana', 'token_pln', 'ewallet'
                activeBrand: 'all',
                searchQuery: '',

                categoriesList: [
                    { id: 'pulsa', name: 'Pulsa Reguler', icon: '⚡' },
                    { id: 'paket_data', name: 'Paket Data / Kuota', icon: '🌐' },
                    { id: 'voucher', name: 'Voucher Fisik', icon: '🎟️' },
                    { id: 'perdana', name: 'Kartu Perdana', icon: '💳' },
                    { id: 'token_pln', name: 'Token Listrik PLN', icon: '💡' },
                    { id: 'ewallet', name: 'Top Up E-Wallet', icon: '👛' },
                ],

                telcoBrands: ['Telkomsel', 'Indosat', 'XL', 'Axis', 'Tri', 'Smartfren', 'By.U'],
                walletBrands: ['DANA', 'GoPay', 'ShopeePay', 'OVO', 'SeaBank'],

                // State Transfer & Tarik Tunai (BNI, BRI, BCA, Mandiri, BSI, dll.)
                transferType: 'transfer', // 'transfer' | 'tarik'
                transferBank: 'BRI',
                customBank: '',
                transferNominal: 100000,
                transferAdminFee: 5000,
                transferAccount: '',
                transferRecipient: '',
                bankList: ['BRI', 'BNI', 'BCA', 'Mandiri', 'BSI', 'SeaBank', 'Bank Jago', 'DANA', 'GoPay', 'ShopeePay', 'OVO', 'Lainnya'],
                nominalShortcuts: [50000, 100000, 200000, 300000, 500000, 1000000, 2000000],
                feeShortcuts: [3000, 5000, 7000, 10000],

                // Form Checkout Fields
                selectedProductId: '{{ old('product_id', '') }}',
                productName: '{{ old('product_name', '') }}',
                customerTarget: '{{ old('customer_target', '') }}',
                costPrice: {{ old('cost_price', 0) }},
                sellingPrice: {{ old('amount', 0) }},
                qty: {{ old('quantity', 1) }},
                categoryId: '{{ old('category_id', '') }}',
                paymentMethod: '{{ old('payment_method', 'cash') }}',
                transactionDate: '{{ old('transaction_date', now()->format('Y-m-d\TH:i')) }}',
                notes: '{{ old('notes', '') }}',
                submitting: false,
                validationErrors: [],

                // Database Items
                products: @json($productData),
                allCategories: @json($categoryData),

                // Helper Format Rupiah
                formatRupiah(num) {
                    return new Intl.NumberFormat('id-ID').format(Math.round(num || 0));
                },

                // Ganti Mode Transaksi
                setMode(newMode) {
                    this.mode = newMode;
                    this.validationErrors = [];
                    if (newMode === 'transfer') {
                        this.applyTransfer();
                    } else {
                        this.autoMatchCategoryDb();
                    }
                },

                // Brand List Aktif Berdasarkan Kategori
                get currentBrands() {
                    if (['pulsa', 'paket_data', 'voucher', 'perdana'].includes(this.activeCategory)) {
                        return this.telcoBrands;
                    } else if (this.activeCategory === 'ewallet') {
                        return this.walletBrands;
                    }
                    return [];
                },

                // Ganti Tab Kategori Produk
                selectCategory(catId) {
                    this.activeCategory = catId;
                    this.activeBrand = 'all';
                    this.autoMatchCategoryDb();
                },

                // Filter Produk Berdasarkan Kategori, Brand & Pencarian
                get matchingProducts() {
                    return this.products.filter(p => {
                        const pName = p.name.toLowerCase();
                        const catName = p.category_name.toLowerCase();

                        // 1. Filter Kategori
                        if (this.activeCategory === 'pulsa') {
                            const isPulsa = (pName.includes('pulsa') || catName.includes('pulsa')) && !pName.includes('voucher') && !pName.includes('paket') && !pName.includes('data');
                            if (!isPulsa) return false;
                        } else if (this.activeCategory === 'paket_data') {
                            const isData = pName.includes('paket') || pName.includes('data') || pName.includes('kuota') || pName.includes('gb') || catName.includes('paket') || catName.includes('data');
                            if (!isData) return false;
                        } else if (this.activeCategory === 'voucher') {
                            const isVoucher = pName.includes('voucher') || catName.includes('voucher');
                            if (!isVoucher) return false;
                        } else if (this.activeCategory === 'perdana') {
                            const isPerdana = pName.includes('perdana') || pName.includes('kartu') || catName.includes('perdana');
                            if (!isPerdana) return false;
                        } else if (this.activeCategory === 'token_pln') {
                            const isToken = pName.includes('token') || pName.includes('pln') || catName.includes('token') || catName.includes('pln');
                            if (!isToken) return false;
                        } else if (this.activeCategory === 'ewallet') {
                            const isWallet = catName.includes('wallet') || catName.includes('dana') || catName.includes('gopay') || catName.includes('shopee') || catName.includes('ovo') || catName.includes('seabank') || pName.includes('dana') || pName.includes('gopay') || pName.includes('top up');
                            if (!isWallet) return false;
                        }

                        // 2. Filter Brand
                        if (this.activeBrand && this.activeBrand !== 'all') {
                            const b = this.activeBrand.toLowerCase();
                            const matchesBrand = pName.includes(b) || catName.includes(b);
                            if (!matchesBrand) return false;
                        }

                        // 3. Filter Search
                        if (this.searchQuery && this.searchQuery.trim() !== '') {
                            const q = this.searchQuery.toLowerCase().trim();
                            const matchName = pName.includes(q);
                            const matchCode = p.code.toLowerCase().includes(q);
                            const matchPrice = p.selling_price.toString().includes(q);
                            if (!matchName && !matchCode && !matchPrice) return false;
                        }

                        return true;
                    });
                },

                // Pilih Produk dari Katalog
                selectProduct(prod) {
                    if (!prod) return;
                    this.selectedProductId = prod.id;
                    this.productName = prod.name;
                    this.costPrice = prod.cost_price;
                    this.sellingPrice = prod.selling_price;
                    this.qty = 1;
                    if (prod.category_id) {
                        this.categoryId = prod.category_id;
                    }
                    this.validationErrors = [];
                },

                // Batalkan Pilihan Produk
                clearSelectedProduct() {
                    this.selectedProductId = '';
                    this.productName = '';
                    this.costPrice = 0;
                    this.sellingPrice = 0;
                    this.customerTarget = '';
                    this.validationErrors = [];
                },

                // Hitung Layanan Transfer & Tarik Tunai
                applyTransfer() {
                    const nom = parseFloat(this.transferNominal) || 0;
                    const fee = parseFloat(this.transferAdminFee) || 0;
                    const bank = this.transferBank === 'Lainnya' ? (this.customBank || 'Bank') : this.transferBank;
                    const isTransfer = this.transferType === 'transfer';

                    this.selectedProductId = '';
                    this.costPrice = nom;
                    this.sellingPrice = nom + fee;
                    this.qty = 1;
                    this.customerTarget = this.transferAccount;
                    
                    let title = (isTransfer ? 'Transfer ' : 'Tarik Tunai ') + bank + ' Rp ' + this.formatRupiah(nom);
                    if (this.transferRecipient.trim()) {
                        title += ' a/n ' + this.transferRecipient.trim();
                    }
                    this.productName = title;

                    this.notes = (isTransfer ? 'Layanan Transfer ' : 'Layanan Tarik Tunai ') +
                        bank + ' (No/Rek: ' + (this.transferAccount || '-') + ')' +
                        (this.transferRecipient ? ' Penerima: ' + this.transferRecipient : '') +
                        ' | Biaya Admin: Rp ' + this.formatRupiah(fee);

                    const targetSlug = isTransfer ? 'transfer-uang' : 'tarik-tunai';
                    const cat = this.allCategories.find(c => c.slug === targetSlug || c.name.toLowerCase().includes(isTransfer ? 'transfer' : 'tarik'));
                    if (cat) {
                        this.categoryId = cat.id;
                    }
                },

                // Cocokkan kategori database otomatis
                autoMatchCategoryDb() {
                    if (this.mode === 'transfer') return;
                    const target = this.activeCategory;
                    let found = null;
                    if (target === 'pulsa') {
                        found = this.allCategories.find(c => c.slug.includes('pulsa') && (this.activeBrand !== 'all' ? c.slug.includes(this.activeBrand.toLowerCase()) : true));
                    } else if (target === 'paket_data') {
                        found = this.allCategories.find(c => c.slug.includes('paket') || c.slug.includes('internet'));
                    } else if (target === 'voucher') {
                        found = this.allCategories.find(c => c.slug.includes('voucher'));
                    } else if (target === 'perdana') {
                        found = this.allCategories.find(c => c.slug.includes('perdana'));
                    } else if (target === 'token_pln') {
                        found = this.allCategories.find(c => c.slug.includes('token') || c.slug.includes('pln'));
                    } else if (target === 'ewallet') {
                        found = this.allCategories.find(c => c.slug.includes('wallet') || c.slug.includes('dana'));
                    }
                    if (found) {
                        this.categoryId = found.id;
                    }
                },

                // Validasi Client-Side Form Sebelum Submit
                validateAndSubmit(e) {
                    this.validationErrors = [];

                    // 1. Validasi Nama Produk
                    if (!this.productName || !this.productName.trim()) {
                        this.validationErrors.push('Nama item / produk transaksi wajib diisi atau dipilih dari katalog.');
                    }

                    // 2. Validasi Kategori
                    if (!this.categoryId) {
                        this.validationErrors.push('Kategori transaksi wajib dipilih.');
                    }

                    // 3. Validasi Total Bayar Pelanggan
                    const jual = parseFloat(this.sellingPrice) || 0;
                    if (jual <= 0) {
                        this.validationErrors.push('Total bayar pelanggan (harga jual) wajib diisi dan harus lebih besar dari Rp 0.');
                    }

                    // 4. Validasi Modal Kulakan
                    const modal = parseFloat(this.costPrice) || 0;
                    if (modal < 0) {
                        this.validationErrors.push('Harga modal / HPP tidak boleh bernilai negatif.');
                    }

                    // 5. Validasi Kuantitas
                    const q = parseInt(this.qty) || 0;
                    if (q < 1) {
                        this.validationErrors.push('Jumlah kuantitas transaksi minimal 1 unit/kali.');
                    }

                    // 6. Validasi Khusus Layanan Transfer / Tarik Tunai
                    if (this.mode === 'transfer') {
                        if (!this.transferAccount || !this.transferAccount.trim()) {
                            this.validationErrors.push('No. Rekening / No. HP Tujuan wajib diisi untuk layanan transfer / tarik tunai.');
                        }
                        const transferNom = parseFloat(this.transferNominal) || 0;
                        if (transferNom < 1000) {
                            this.validationErrors.push('Nominal transfer / tarik tunai minimal Rp 1.000.');
                        }
                    }

                    // 7. Validasi Ketersediaan Stok Katalog
                    if (this.selectedProductId && this.currentSelectedProduct) {
                        if (this.currentSelectedProduct.stock < q) {
                            this.validationErrors.push('Stok produk ' + this.currentSelectedProduct.name + ' tidak mencukupi (sisa: ' + this.currentSelectedProduct.stock + ' ' + this.currentSelectedProduct.unit + ', diminta: ' + q + ' ' + this.currentSelectedProduct.unit + ').');
                        }
                    }

                    // Jika ada error validasi, cegah submit dan gulir ke atas
                    if (this.validationErrors.length > 0) {
                        e.preventDefault();
                        this.submitting = false;
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                        return false;
                    }

                    this.submitting = true;
                    return true;
                },

                // Kalkulasi Laba & Omzet
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
                    if (jual <= 0) return '0';
                    return ((this.unitProfit / jual) * 100).toFixed(1);
                },

                get totalRevenue() {
                    const jual = parseFloat(this.sellingPrice) || 0;
                    const q = parseInt(this.qty) || 1;
                    return jual * q;
                },

                get currentSelectedProduct() {
                    if (!this.selectedProductId) return null;
                    return this.products.find(p => p.id == this.selectedProductId);
                },

                init() {
                    if (this.selectedProductId) {
                        const p = this.products.find(item => item.id == this.selectedProductId);
                        if (p) this.selectProduct(p);
                    } else {
                        this.autoMatchCategoryDb();
                    }
                }
            };
        };
    </script>

    <div x-data="posCashier()" class="space-y-6">
        <!-- ================================================================= -->
        <!-- HEADER HALAMAN & RIWAYAT                                         -->
        <!-- ================================================================= -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2 border-b border-slate-200 dark:border-slate-800">
            <div class="flex items-center gap-3.5">
                <div class="w-11 h-11 rounded-2xl bg-indigo-600 text-white flex items-center justify-center font-black text-xl shadow-md shadow-indigo-600/30">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-xl font-black text-slate-900 dark:text-white tracking-tight flex items-center gap-2">
                        <span>Kasir Transaksi Penjualan Baru</span>
                        <span class="text-[10px] uppercase font-bold tracking-wider px-2 py-0.5 rounded-full bg-indigo-100 dark:bg-indigo-900/60 text-indigo-700 dark:text-indigo-300">POS KonterKu</span>
                    </h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Filter produk per kategori (pulsa, kuota, voucher, token, e-wallet) dan layanan transfer & tarik tunai.</p>
                </div>
            </div>
            <x-button href="{{ route('incomes.index') }}" variant="secondary" size="sm" class="font-semibold text-xs self-start sm:self-auto">
                &larr; Riwayat Transaksi
            </x-button>
        </div>

        <!-- ================================================================= -->
        <!-- ALERT KESALAHAN VALIDASI CLIENT-SIDE                             -->
        <!-- ================================================================= -->
        <div 
            x-show="validationErrors.length > 0" 
            x-transition 
            class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/70 border border-rose-300 dark:border-rose-800 text-rose-800 dark:text-rose-200 shadow-xs"
            style="display: none;"
        >
            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center gap-2 font-black text-xs uppercase tracking-wider text-rose-700 dark:text-rose-300">
                    <svg class="w-4 h-4 shrink-0 text-rose-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <span>Mohon Lengkapi Data Transaksi:</span>
                </div>
                <button type="button" @click="validationErrors = []" class="text-xs text-rose-500 hover:text-rose-700 font-bold cursor-pointer">✕ Tutup</button>
            </div>
            <ul class="list-disc pl-5 space-y-1 text-xs">
                <template x-for="(err, idx) in validationErrors" :key="idx">
                    <li x-text="err"></li>
                </template>
            </ul>
        </div>

        <!-- ================================================================= -->
        <!-- 2 TOMBOL MODE UTAMA: FILTER BY PRODUK vs TRANSFER & TARIK TUNAI   -->
        <!-- ================================================================= -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Mode 1: Filter by Produk (Katalog Stok) -->
            <button
                type="button"
                @click="setMode('catalog')"
                :class="mode === 'catalog' 
                    ? 'border-indigo-600 bg-indigo-50/70 dark:bg-indigo-950/60 ring-2 ring-indigo-500/20 shadow-md' 
                    : 'border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-850 hover:border-slate-300 dark:hover:border-slate-700'"
                class="p-4 rounded-2xl border text-left transition-all cursor-pointer flex items-center justify-between gap-4 group"
            >
                <div class="flex items-center gap-3.5 min-w-0">
                    <div 
                        :class="mode === 'catalog' ? 'bg-indigo-600 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 group-hover:bg-indigo-50 group-hover:text-indigo-600'" 
                        class="w-12 h-12 rounded-xl flex items-center justify-center text-2xl transition-colors shrink-0"
                    >
                        📱
                    </div>
                    <div class="min-w-0">
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-black uppercase tracking-wider text-slate-900 dark:text-white">Filter by Produk</span>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-indigo-100 dark:bg-indigo-900/60 text-indigo-700 dark:text-indigo-300">Katalog Stok</span>
                        </div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 truncate mt-0.5">Pulsa sendiri, paket data sendiri, voucher, token, e-wallet</p>
                    </div>
                </div>
                <div class="shrink-0 text-indigo-600 dark:text-indigo-400 font-bold text-lg" x-show="mode === 'catalog'">
                    ✓
                </div>
            </button>

            <!-- Mode 2: Transfer & Tarik Tunai (Bank & E-Wallet) -->
            <button
                type="button"
                @click="setMode('transfer')"
                :class="mode === 'transfer' 
                    ? 'border-emerald-600 bg-emerald-50/70 dark:bg-emerald-950/60 ring-2 ring-emerald-500/20 shadow-md' 
                    : 'border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-850 hover:border-slate-300 dark:hover:border-slate-700'"
                class="p-4 rounded-2xl border text-left transition-all cursor-pointer flex items-center justify-between gap-4 group"
            >
                <div class="flex items-center gap-3.5 min-w-0">
                    <div 
                        :class="mode === 'transfer' ? 'bg-emerald-600 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 group-hover:bg-emerald-50 group-hover:text-emerald-600'" 
                        class="w-12 h-12 rounded-xl flex items-center justify-center text-2xl transition-colors shrink-0"
                    >
                        💸
                    </div>
                    <div class="min-w-0">
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-black uppercase tracking-wider text-slate-900 dark:text-white">Transfer & Tarik Tunai</span>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-100 dark:bg-emerald-900/60 text-emerald-700 dark:text-emerald-300">Bank & E-Wallet</span>
                        </div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 truncate mt-0.5">Kirim saldo & tarik tunai (BRI, BNI, BCA, Mandiri, BSI, dll)</p>
                    </div>
                </div>
                <div class="shrink-0 text-emerald-600 dark:text-emerald-400 font-bold text-lg" x-show="mode === 'transfer'">
                    ✓
                </div>
            </button>
        </div>

        <!-- ================================================================= -->
        <!-- FORM UTAMA TRANSAKSI DENGAN VALIDASI LENGKAP                     -->
        <!-- ================================================================= -->
        <form
            method="POST"
            action="{{ route('incomes.store') }}"
            @submit="validateAndSubmit($event)"
            novalidate
        >
            @csrf

            <div class="flex flex-col lg:flex-row gap-6 items-start w-full">
                <!-- ========================================================= -->
                <!-- KOLOM KIRI (7/12): WORKBENCH KATALOG / TRANSFER            -->
                <!-- ========================================================= -->
                <div class="w-full lg:w-7/12 min-w-0 space-y-5">
                    
                    <!-- ----------------------------------------------------- -->
                    <!-- KONTEN MODE 1: FILTER BY PRODUK                        -->
                    <!-- ----------------------------------------------------- -->
                    <div x-show="mode === 'catalog'" class="space-y-4">
                        <!-- Tab Kategori Produk (Pulsa Sendiri, Paket Data Sendiri, dll) -->
                        <div class="p-4 rounded-2xl bg-white dark:bg-slate-850 border border-slate-200 dark:border-slate-800 shadow-xs space-y-3">
                            <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                Filter Kategori Produk:
                            </label>
                            
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                                <template x-for="cat in categoriesList" :key="cat.id">
                                    <button
                                        type="button"
                                        @click="selectCategory(cat.id)"
                                        :class="activeCategory === cat.id 
                                            ? 'border-indigo-600 bg-indigo-600 text-white font-bold shadow-xs' 
                                            : 'border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-100 font-semibold'"
                                        class="p-2.5 rounded-xl border text-xs flex items-center justify-center gap-2 transition-all cursor-pointer"
                                    >
                                        <span x-text="cat.icon"></span>
                                        <span x-text="cat.name" class="truncate"></span>
                                    </button>
                                </template>
                            </div>

                            <!-- Filter Operator / Brand (Jika Kategori Memiliki Brand) -->
                            <div x-show="currentBrands.length > 0" class="pt-3 border-t border-slate-100 dark:border-slate-800">
                                <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">
                                    Filter Operator / Brand:
                                </label>
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <button
                                        type="button"
                                        @click="activeBrand = 'all'"
                                        :class="activeBrand === 'all' 
                                            ? 'bg-slate-900 dark:bg-white text-white dark:text-slate-900 font-bold shadow-xs' 
                                            : 'bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-50 font-semibold'"
                                        class="px-3 py-1.5 rounded-xl text-xs transition-all cursor-pointer"
                                    >
                                        Semua
                                    </button>
                                    <template x-for="brand in currentBrands" :key="brand">
                                        <button
                                            type="button"
                                            @click="activeBrand = brand"
                                            :class="activeBrand === brand 
                                                ? 'bg-slate-900 dark:bg-white text-white dark:text-slate-900 font-bold shadow-xs' 
                                                : 'bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-50 font-semibold'"
                                            class="px-3 py-1.5 rounded-xl text-xs transition-all cursor-pointer"
                                            x-text="brand"
                                        ></button>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <!-- Panel Katalog Produk & Pencarian -->
                        <div class="p-4 sm:p-5 rounded-2xl bg-white dark:bg-slate-850 border border-slate-200 dark:border-slate-800 shadow-xs space-y-3.5">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2.5">
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-black uppercase tracking-wider text-slate-900 dark:text-white">Pilih Produk Dari Katalog</span>
                                    <span class="text-[11px] font-bold px-2 py-0.5 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 font-mono" x-text="matchingProducts.length + ' item'"></span>
                                </div>

                                <!-- Live Search Input -->
                                <div class="relative w-full sm:w-60">
                                    <input
                                        type="text"
                                        x-model="searchQuery"
                                        placeholder="Cari produk / paket..."
                                        class="w-full pl-8 pr-3 py-1.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-xs font-medium focus:ring-2 focus:ring-indigo-500"
                                    >
                                    <span class="absolute inset-y-0 left-0 pl-2.5 flex items-center text-slate-400 pointer-events-none text-xs">
                                        🔍
                                    </span>
                                </div>
                            </div>

                            <!-- Dropdown Cepat -->
                            <select
                                x-model="selectedProductId"
                                @change="selectProduct(products.find(p => p.id == selectedProductId))"
                                class="block w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs font-medium py-2.5 px-3 focus:ring-2 focus:ring-indigo-500 shadow-2xs"
                            >
                                <option value="">-- Pilih Dari Dropdown Produk --</option>
                                <template x-for="p in matchingProducts" :key="p.id">
                                    <option 
                                        :value="p.id" 
                                        :selected="p.id == selectedProductId"
                                        x-text="p.name + ' (Stok: ' + p.stock + ' ' + p.unit + ') — Jual: Rp ' + formatRupiah(p.selling_price) + ' | Modal: Rp ' + formatRupiah(p.cost_price)"
                                    ></option>
                                </template>
                            </select>

                            <input type="hidden" name="product_id" :value="selectedProductId">

                            <!-- Grid Kartu Produk Interaktif -->
                            <div class="space-y-2 pt-1">
                                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Atau Klik Kartu Produk:</span>
                                
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 max-h-[380px] overflow-y-auto pr-1 scrollbar-thin">
                                    <template x-for="p in matchingProducts" :key="p.id">
                                        <button
                                            type="button"
                                            @click="selectProduct(p)"
                                            :class="selectedProductId == p.id 
                                                ? 'border-indigo-600 bg-indigo-50 dark:bg-indigo-950/70 text-indigo-900 dark:text-indigo-200 ring-2 ring-indigo-500/30 font-bold shadow-xs' 
                                                : 'border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 hover:border-indigo-300 dark:hover:border-indigo-700 hover:bg-slate-50'"
                                            class="p-3 rounded-xl border text-left transition-all cursor-pointer flex flex-col justify-between gap-2 shadow-2xs group"
                                        >
                                            <div class="min-w-0 w-full">
                                                <div class="flex items-center justify-between gap-1 mb-1">
                                                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 truncate" x-text="p.brand || 'Umum'"></span>
                                                    <span 
                                                        :class="p.stock > 0 ? 'bg-emerald-100 dark:bg-emerald-900/60 text-emerald-800 dark:text-emerald-300' : 'bg-rose-100 dark:bg-rose-900/60 text-rose-800 dark:text-rose-300'"
                                                        class="text-[10px] font-mono font-bold px-1.5 py-0.5 rounded-full" 
                                                        x-text="'Stok: ' + p.stock + ' ' + p.unit"
                                                    ></span>
                                                </div>
                                                <h4 class="text-xs font-bold leading-snug line-clamp-2" x-text="p.name"></h4>
                                            </div>

                                            <div class="flex items-center justify-between pt-1 border-t border-slate-100 dark:border-slate-700/60 w-full">
                                                <span class="text-[10px] text-slate-400 font-mono" x-text="'Modal: ' + formatRupiah(p.cost_price)"></span>
                                                <span class="text-xs font-black text-indigo-600 dark:text-indigo-400 font-mono" x-text="'Rp ' + formatRupiah(p.selling_price)"></span>
                                            </div>
                                        </button>
                                    </template>
                                </div>

                                <!-- State Kosong -->
                                <div x-show="matchingProducts.length === 0" class="p-6 text-center text-xs text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-slate-800/50 rounded-xl border border-dashed border-slate-200 dark:border-slate-700">
                                    Tidak ada produk yang cocok dengan filter atau kata kunci ini. Anda tetap dapat menginput transaksi secara manual di panel kanan.
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ----------------------------------------------------- -->
                    <!-- KONTEN MODE 2: TRANSFER & TARIK TUNAI                  -->
                    <!-- ----------------------------------------------------- -->
                    <div x-show="mode === 'transfer'" class="p-5 rounded-2xl bg-white dark:bg-slate-850 border border-emerald-200 dark:border-emerald-800/60 shadow-xs space-y-4">
                        <div class="flex items-center justify-between pb-2 border-b border-emerald-100 dark:border-emerald-900/40">
                            <span class="text-xs font-black uppercase tracking-wider text-emerald-800 dark:text-emerald-300 flex items-center gap-2">
                                <span>💸 Form Transfer & Tarik Tunai</span>
                            </span>
                            <span class="text-[11px] font-bold text-emerald-600 dark:text-emerald-400">Agen Bank & E-Wallet</span>
                        </div>

                        <!-- Pilihan Jenis Layanan: Transfer Uang vs Tarik Tunai -->
                        <div>
                            <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">
                                Jenis Layanan:
                            </label>
                            <div class="grid grid-cols-2 gap-3">
                                <button
                                    type="button"
                                    @click="transferType = 'transfer'; applyTransfer();"
                                    :class="transferType === 'transfer' 
                                        ? 'border-emerald-600 bg-emerald-600 text-white font-bold shadow-xs' 
                                        : 'border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-semibold'"
                                    class="p-3 rounded-xl border text-xs flex items-center justify-center gap-2 transition-all cursor-pointer"
                                >
                                    <span class="text-base">↗️</span>
                                    <span>Transfer Uang (Kirim)</span>
                                </button>

                                <button
                                    type="button"
                                    @click="transferType = 'tarik'; applyTransfer();"
                                    :class="transferType === 'tarik' 
                                        ? 'border-teal-600 bg-teal-600 text-white font-bold shadow-xs' 
                                        : 'border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-semibold'"
                                    class="p-3 rounded-xl border text-xs flex items-center justify-center gap-2 transition-all cursor-pointer"
                                >
                                    <span class="text-base">↙️</span>
                                    <span>Tarik Tunai (Cash Out)</span>
                                </button>
                            </div>
                        </div>

                        <!-- Pilihan Bank / Channel (BNI, BRI, BCA, Mandiri, dll.) -->
                        <div>
                            <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">
                                Pilih Bank / E-Wallet Tujuan:
                            </label>
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <template x-for="bank in bankList" :key="bank">
                                    <button
                                        type="button"
                                        @click="transferBank = bank; applyTransfer();"
                                        :class="transferBank === bank 
                                            ? 'bg-slate-900 dark:bg-white text-white dark:text-slate-900 font-black shadow-xs ring-2 ring-emerald-500/30' 
                                            : 'bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200 hover:border-slate-400 font-bold'"
                                        class="px-3.5 py-1.5 rounded-xl text-xs transition-all cursor-pointer"
                                        x-text="bank"
                                    ></button>
                                </template>
                            </div>

                            <!-- Input Custom Bank jika Lainnya -->
                            <div x-show="transferBank === 'Lainnya'" class="mt-2.5">
                                <input
                                    type="text"
                                    x-model="customBank"
                                    @input="applyTransfer()"
                                    placeholder="Ketik nama bank / channel..."
                                    class="w-full px-3.5 py-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-xs font-bold text-slate-900 dark:text-white"
                                >
                            </div>
                        </div>

                        <!-- Input Rekening & Nama Penerima -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 pt-1">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1 flex items-center justify-between">
                                    <span x-text="transferType === 'transfer' ? 'No. Rekening / No. HP Tujuan' : 'No. Rekening / No. HP Penarik'"></span>
                                    <span class="text-rose-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    x-model="transferAccount"
                                    @input="applyTransfer()"
                                    placeholder="Contoh: 0812xxxx / No Rekening"
                                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-xs font-mono font-bold focus:ring-2 focus:ring-emerald-500 shadow-2xs"
                                >
                            </div>

                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1 flex items-center justify-between">
                                    <span>Nama Pemilik / Penerima</span>
                                    <span class="text-slate-400 font-normal lowercase">(opsional)</span>
                                </label>
                                <input
                                    type="text"
                                    x-model="transferRecipient"
                                    @input="applyTransfer()"
                                    placeholder="Contoh: Budi Santoso"
                                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-xs font-medium focus:ring-2 focus:ring-emerald-500 shadow-2xs"
                                >
                            </div>
                        </div>

                        <!-- Nominal & Biaya Admin -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-1">
                            <!-- Nominal Pokok -->
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">
                                    Nominal Uang (Rp) <span class="text-rose-500">*</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400 font-bold text-xs pointer-events-none">Rp</span>
                                    <input
                                        type="number"
                                        min="1000"
                                        step="1000"
                                        x-model.number="transferNominal"
                                        @input="applyTransfer()"
                                        class="w-full pl-10 pr-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm font-mono font-black focus:ring-2 focus:ring-emerald-500 shadow-2xs"
                                    >
                                </div>
                                <div class="flex items-center gap-1.5 flex-wrap mt-2">
                                    <template x-for="nom in nominalShortcuts" :key="nom">
                                        <button
                                            type="button"
                                            @click="transferNominal = nom; applyTransfer();"
                                            :class="transferNominal == nom ? 'bg-emerald-600 text-white font-bold' : 'bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 hover:bg-slate-100'"
                                            class="px-2 py-1 rounded-lg text-[10px] font-mono transition-colors cursor-pointer"
                                            x-text="formatRupiah(nom)"
                                        ></button>
                                    </template>
                                </div>
                            </div>

                            <!-- Biaya Admin -->
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1 flex items-center justify-between">
                                    <span>Biaya Admin (Fee Jasa)</span>
                                    <span class="text-emerald-600 dark:text-emerald-400 font-bold text-[11px]">(Laba Konter)</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-emerald-600 font-bold text-xs pointer-events-none">Rp</span>
                                    <input
                                        type="number"
                                        min="0"
                                        step="500"
                                        x-model.number="transferAdminFee"
                                        @input="applyTransfer()"
                                        class="w-full pl-10 pr-3.5 py-2.5 rounded-xl border border-emerald-300 dark:border-emerald-600 bg-white dark:bg-slate-800 text-emerald-700 dark:text-emerald-300 text-sm font-mono font-black focus:ring-2 focus:ring-emerald-500 shadow-2xs"
                                    >
                                </div>
                                <div class="flex items-center gap-1.5 flex-wrap mt-2">
                                    <template x-for="fee in feeShortcuts" :key="fee">
                                        <button
                                            type="button"
                                            @click="transferAdminFee = fee; applyTransfer();"
                                            :class="transferAdminFee == fee ? 'bg-emerald-600 text-white font-bold' : 'bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 hover:bg-slate-100'"
                                            class="px-2 py-1 rounded-lg text-[10px] font-mono transition-colors cursor-pointer"
                                            x-text="'+Rp ' + formatRupiah(fee)"
                                        ></button>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <!-- Ringkasan Rincian Transfer -->
                        <div class="p-3.5 rounded-xl bg-emerald-50/60 dark:bg-emerald-950/40 border border-emerald-200/80 dark:border-emerald-800/50 flex items-center justify-between text-xs">
                            <div>
                                <span class="text-slate-500 dark:text-slate-400 block text-[11px]">Modal Uang Keluar:</span>
                                <strong class="text-slate-800 dark:text-slate-200 font-mono" x-text="'Rp ' + formatRupiah(transferNominal)"></strong>
                            </div>
                            <div class="text-right">
                                <span class="text-emerald-600 dark:text-emerald-400 block text-[11px] font-bold">Total Tagihan Pelanggan:</span>
                                <strong class="text-emerald-700 dark:text-emerald-300 font-mono text-sm" x-text="'Rp ' + formatRupiah(transferNominal + transferAdminFee)"></strong>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ========================================================= -->
                <!-- KOLOM KANAN (5/12): CHECKOUT KASIR & RINGKASAN TAGIHAN     -->
                <!-- ========================================================= -->
                <div class="w-full lg:w-5/12 min-w-0 lg:sticky lg:top-6 space-y-4">
                    <x-card class="border-slate-200 dark:border-slate-800 shadow-md">
                        <div class="flex items-center justify-between pb-3 border-b border-slate-200 dark:border-slate-800">
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-indigo-600 animate-pulse"></span>
                                <h3 class="text-xs font-black uppercase tracking-wider text-slate-900 dark:text-white">
                                    Ringkasan Kasir & Pembayaran
                                </h3>
                            </div>
                            <button
                                type="button"
                                @click="clearSelectedProduct()"
                                class="text-[11px] text-slate-400 hover:text-rose-500 font-semibold cursor-pointer"
                            >
                                Reset Form
                            </button>
                        </div>

                        <div class="space-y-4 pt-3">
                            <!-- Nama Produk / Item Transaksi -->
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1 flex items-center justify-between">
                                    <span>Item Transaksi <span class="text-rose-500">*</span></span>
                                    <span x-show="selectedProductId" class="text-[11px] text-emerald-600 dark:text-emerald-400 font-bold">Katalog Stok</span>
                                </label>
                                <input
                                    type="text"
                                    name="product_name"
                                    x-model="productName"
                                    required
                                    placeholder="Nama paket, pulsa, atau transaksi..."
                                    class="w-full px-3.5 py-2.5 rounded-xl border @error('product_name') border-rose-500 ring-1 ring-rose-500 @else border-slate-300 dark:border-slate-600 @enderror bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-xs font-bold focus:ring-2 focus:ring-indigo-500 shadow-2xs"
                                >
                                @error('product_name')
                                    <p class="text-[11px] text-rose-500 font-bold mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- No. Tujuan / Rekening Pelanggan -->
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1 flex items-center justify-between">
                                    <span>No. Tujuan / Rek / ID Pelanggan</span>
                                    <span class="text-slate-400 font-normal lowercase">(opsional)</span>
                                </label>
                                <div class="relative">
                                    <input
                                        type="text"
                                        name="customer_target"
                                        x-model="customerTarget"
                                        placeholder="0812-xxxx / No Meteran / No Rekening"
                                        class="w-full pl-3.5 pr-10 py-2.5 rounded-xl border @error('customer_target') border-rose-500 ring-1 ring-rose-500 @else border-slate-300 dark:border-slate-600 @enderror bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-xs font-mono font-medium focus:ring-2 focus:ring-indigo-500 shadow-2xs"
                                    >
                                    <span class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 pointer-events-none text-xs">
                                        📞
                                    </span>
                                </div>
                                @error('customer_target')
                                    <p class="text-[11px] text-rose-500 font-bold mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Kategori Transaksi -->
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">
                                    Kategori Transaksi <span class="text-rose-500">*</span>
                                </label>
                                <select
                                    name="category_id"
                                    x-model="categoryId"
                                    required
                                    class="block w-full rounded-xl border @error('category_id') border-rose-500 ring-1 ring-rose-500 @else border-slate-300 dark:border-slate-600 @enderror bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs font-medium py-2.5 px-3 focus:ring-2 focus:ring-indigo-500 shadow-2xs"
                                >
                                    <option value="">-- Pilih Kategori --</option>
                                    @foreach($cleanCategories as $cat)
                                        <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                            {{ $cat->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <p class="text-[11px] text-rose-500 font-bold mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Rincian Harga Modal, Jual & Qty -->
                            <div class="grid grid-cols-2 gap-3 pt-1">
                                <!-- Harga Modal -->
                                <div>
                                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1">
                                        Modal / HPP (Rp)
                                    </label>
                                    <div class="relative">
                                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400 font-bold text-xs pointer-events-none">Rp</span>
                                        <input
                                            type="number"
                                            name="cost_price"
                                            min="0"
                                            step="0.01"
                                            x-model.number="costPrice"
                                            class="w-full pl-9 pr-3 py-2 rounded-xl border @error('cost_price') border-rose-500 ring-1 ring-rose-500 @else border-slate-300 dark:border-slate-600 @enderror bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 text-xs font-mono font-bold focus:ring-2 focus:ring-indigo-500 shadow-2xs"
                                        >
                                    </div>
                                    @error('cost_price')
                                        <p class="text-[11px] text-rose-500 font-bold mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Jumlah / Qty -->
                                <div>
                                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1">
                                        Qty <span class="text-rose-500">*</span>
                                    </label>
                                    <div class="flex items-center">
                                        <button
                                            type="button"
                                            @click="qty = Math.max(1, (parseInt(qty) || 1) - 1)"
                                            class="px-2.5 py-2 rounded-l-xl border border-r-0 border-slate-300 dark:border-slate-600 bg-slate-100 dark:bg-slate-750 text-slate-600 dark:text-slate-300 font-bold text-xs"
                                        >-</button>
                                        <input
                                            type="number"
                                            name="quantity"
                                            min="1"
                                            x-model.number="qty"
                                            required
                                            class="w-full py-2 border-y @error('quantity') border-rose-500 ring-1 ring-rose-500 @else border-slate-300 dark:border-slate-600 @enderror bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-xs font-mono font-bold text-center"
                                        >
                                        <button
                                            type="button"
                                            @click="qty = (parseInt(qty) || 1) + 1"
                                            class="px-2.5 py-2 rounded-r-xl border border-l-0 border-slate-300 dark:border-slate-600 bg-slate-100 dark:bg-slate-750 text-slate-600 dark:text-slate-300 font-bold text-xs"
                                        >+</button>
                                    </div>
                                    @error('quantity')
                                        <p class="text-[11px] text-rose-500 font-bold mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Total Bayar Pelanggan (Harga Jual Satuan) -->
                            <div class="pt-1">
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1 flex items-center justify-between">
                                    <span>Total Bayar Pelanggan <span class="text-rose-500">*</span></span>
                                    <span class="text-[11px] text-indigo-600 dark:text-indigo-400 font-semibold font-mono" x-show="qty > 1" x-text="'Total: Rp ' + formatRupiah(totalRevenue)"></span>
                                </label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-indigo-600 font-bold text-sm pointer-events-none">Rp</span>
                                    <input
                                        type="number"
                                        name="amount"
                                        min="0.01"
                                        step="0.01"
                                        x-model.number="sellingPrice"
                                        required
                                        placeholder="0"
                                        class="w-full pl-11 pr-3.5 py-3 rounded-xl border @error('amount') border-rose-500 ring-1 ring-rose-500 @else border-indigo-300 dark:border-indigo-600 @enderror bg-indigo-50/30 dark:bg-indigo-950/30 text-indigo-700 dark:text-indigo-300 text-base font-mono font-black focus:ring-2 focus:ring-indigo-500 shadow-2xs"
                                    >
                                </div>
                                @error('amount')
                                    <p class="text-[11px] text-rose-500 font-bold mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Kartu Laba Real-Time & Peringatan Rugi -->
                            <div class="p-3.5 rounded-xl bg-gradient-to-r from-emerald-500/10 via-teal-500/10 to-indigo-500/10 dark:from-emerald-950/40 dark:via-teal-950/40 dark:to-indigo-950/40 border border-emerald-300/80 dark:border-emerald-700/60 flex items-center justify-between">
                                <div>
                                    <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-800 dark:text-emerald-300 block">
                                        Estimasi Laba Transaksi
                                    </span>
                                    <span class="text-[10px] text-slate-500 dark:text-slate-400 font-mono" x-text="profitMargin + '% profit margin'"></span>
                                </div>
                                <div class="text-right font-mono">
                                    <span 
                                        :class="unitProfit >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400'"
                                        class="text-base font-black block" 
                                        x-text="(unitProfit >= 0 ? '+ Rp ' : '- Rp ') + formatRupiah(Math.abs(totalProfit))"
                                    ></span>
                                </div>
                            </div>

                            <!-- Peringatan Jual di Bawah Modal (Jika Rugi) -->
                            <div 
                                x-show="unitProfit < 0 && sellingPrice > 0" 
                                class="p-2.5 rounded-xl bg-amber-50 dark:bg-amber-950/50 border border-amber-300 dark:border-amber-800 text-[11px] text-amber-800 dark:text-amber-200 flex items-center gap-2 font-semibold"
                                style="display: none;"
                            >
                                <span class="text-sm">⚠️</span>
                                <span>Peringatan: Harga jual lebih kecil dari modal kulakan (Potensi rugi Rp <strong x-text="formatRupiah(Math.abs(totalProfit))"></strong>).</span>
                            </div>

                            <!-- Metode Pembayaran -->
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">
                                    Metode Pembayaran <span class="text-rose-500">*</span>
                                </label>
                                <div class="grid grid-cols-3 gap-2">
                                    <label 
                                        :class="paymentMethod === 'cash' ? 'border-indigo-600 bg-indigo-50 dark:bg-indigo-950/60 text-indigo-800 dark:text-indigo-200 font-bold' : 'border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300'"
                                        class="p-2 rounded-xl border text-center text-xs cursor-pointer transition-all"
                                    >
                                        <input type="radio" name="payment_method" value="cash" x-model="paymentMethod" class="sr-only">
                                        <span>💵 Tunai</span>
                                    </label>

                                    <label 
                                        :class="paymentMethod === 'qris' ? 'border-indigo-600 bg-indigo-50 dark:bg-indigo-950/60 text-indigo-800 dark:text-indigo-200 font-bold' : 'border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300'"
                                        class="p-2 rounded-xl border text-center text-xs cursor-pointer transition-all"
                                    >
                                        <input type="radio" name="payment_method" value="qris" x-model="paymentMethod" class="sr-only">
                                        <span>📲 QRIS</span>
                                    </label>

                                    <label 
                                        :class="paymentMethod === 'transfer' ? 'border-indigo-600 bg-indigo-50 dark:bg-indigo-950/60 text-indigo-800 dark:text-indigo-200 font-bold' : 'border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300'"
                                        class="p-2 rounded-xl border text-center text-xs cursor-pointer transition-all"
                                    >
                                        <input type="radio" name="payment_method" value="transfer" x-model="paymentMethod" class="sr-only">
                                        <span>🏦 Transfer</span>
                                    </label>
                                </div>
                                @error('payment_method')
                                    <p class="text-[11px] text-rose-500 font-bold mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Tanggal & Waktu -->
                            <div>
                                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1">
                                    Tanggal & Waktu Transaksi <span class="text-rose-500">*</span>
                                </label>
                                <input
                                    type="datetime-local"
                                    name="transaction_date"
                                    x-model="transactionDate"
                                    required
                                    class="block w-full rounded-xl border @error('transaction_date') border-rose-500 ring-1 ring-rose-500 @else border-slate-300 dark:border-slate-600 @enderror bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs font-medium py-2 px-3 focus:ring-2 focus:ring-indigo-500 shadow-2xs"
                                >
                                @error('transaction_date')
                                    <p class="text-[11px] text-rose-500 font-bold mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Catatan Transaksi -->
                            <div>
                                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1">
                                    Catatan / Keterangan (opsional)
                                </label>
                                <input
                                    type="text"
                                    name="notes"
                                    x-model="notes"
                                    placeholder="Keterangan tambahan..."
                                    class="w-full px-3 py-2 rounded-xl border @error('notes') border-rose-500 ring-1 ring-rose-500 @else border-slate-300 dark:border-slate-600 @enderror bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-xs font-medium focus:ring-2 focus:ring-indigo-500 shadow-2xs"
                                >
                                @error('notes')
                                    <p class="text-[11px] text-rose-500 font-bold mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Tombol Submit -->
                            <div class="pt-2">
                                <button
                                    type="submit"
                                    :disabled="submitting"
                                    class="w-full inline-flex items-center justify-center gap-2 px-5 py-3.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs uppercase tracking-wider transition-all shadow-md shadow-indigo-600/30 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer"
                                >
                                    <svg x-show="submitting" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                                    <span x-text="submitting ? 'Menyimpan Transaksi...' : 'Simpan Transaksi Penjualan'">Simpan Transaksi Penjualan</span>
                                </button>
                            </div>
                        </div>
                    </x-card>
                </div>
            </div>
        </form>
    </div>
</x-layouts.app>
