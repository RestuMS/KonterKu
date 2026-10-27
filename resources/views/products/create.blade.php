<x-layouts.app title="Tambah Produk Baru (Multi-Input Fleksibel)">
    <x-slot:header>
        Tambah Produk Baru
    </x-slot:header>

    @php
        // Mengelompokkan kategori secara sistematis
        $pulsaCats = $categories->filter(fn($c) => str_starts_with(strtolower($c->name), 'pulsa'));
        $voucherCats = $categories->filter(fn($c) => str_starts_with(strtolower($c->name), 'voucher'));
        $paketCats = $categories->filter(fn($c) => str_starts_with(strtolower($c->name), 'paket internet'));
        $perdanaCats = $categories->filter(fn($c) => str_starts_with(strtolower($c->name), 'kartu perdana'));
        $ewalletCats = $categories->filter(fn($c) => str_starts_with(strtolower($c->name), 'e-wallet'));
        $tokenCats = $categories->filter(fn($c) => str_contains(strtolower($c->name), 'token'));
        $otherCats = $categories->reject(fn($c) => 
            str_starts_with(strtolower($c->name), 'pulsa') || 
            str_starts_with(strtolower($c->name), 'voucher') || 
            str_starts_with(strtolower($c->name), 'paket internet') || 
            str_starts_with(strtolower($c->name), 'kartu perdana') || 
            str_starts_with(strtolower($c->name), 'e-wallet') || 
            str_contains(strtolower($c->name), 'token')
        );
    @endphp

    <div 
        x-data="{
            categories: {{ Js::from($categories) }},
            items: [],
            showBatchModal: false,

            // State Generator Seri Cepat
            batchGen: {
                categoryId: '',
                selectedDenoms: ['5.000', '10.000', '25.000', '50.000', '100.000'],
                baseMargin: 2000,
                defaultStock: 10,
                defaultMinStock: 3
            },

            init() {
                const oldItems = {{ Js::from(old('items', [])) }};
                if (oldItems && Array.isArray(oldItems) && oldItems.length > 0) {
                    this.items = oldItems.map((item, idx) => ({
                        uid: Date.now() + idx,
                        category_id: item.category_id || '',
                        name: item.name || '',
                        code: item.code || '',
                        cost_price: item.cost_price || '',
                        selling_price: item.selling_price || '',
                        stock: item.stock !== undefined ? item.stock : 0,
                        min_stock: item.min_stock !== undefined ? item.min_stock : 5,
                        unit: item.unit || 'pcs',
                        is_active: item.is_active !== undefined ? Boolean(item.is_active) : true,
                        notes: item.notes || '',
                    }));
                } else {
                    // Mulai dengan 1 baris produk kosong siap diisi
                    this.addNewRow();
                }
            },

            addNewRow(prefill = {}) {
                const uid = Date.now() + Math.floor(Math.random() * 1000);
                this.items.push({
                    uid: uid,
                    category_id: prefill.category_id || '',
                    name: prefill.name || '',
                    code: prefill.code || '',
                    cost_price: prefill.cost_price || '',
                    selling_price: prefill.selling_price || '',
                    stock: prefill.stock !== undefined ? prefill.stock : 0,
                    min_stock: prefill.min_stock !== undefined ? prefill.min_stock : 5,
                    unit: prefill.unit || 'pcs',
                    is_active: prefill.is_active !== undefined ? prefill.is_active : true,
                    notes: prefill.notes || ''
                });
            },

            duplicateRow(index) {
                const source = this.items[index];
                const uid = Date.now() + Math.floor(Math.random() * 1000);
                
                // Buat kode SKU baru dengan nomor acak agar unik
                let newCode = source.code ? source.code + '-CPY' : '';
                if (source.code && source.code.includes('-')) {
                    const parts = source.code.split('-');
                    newCode = parts.slice(0, -1).join('-') + '-' + Math.floor(100 + Math.random() * 900);
                }

                this.items.splice(index + 1, 0, {
                    uid: uid,
                    category_id: source.category_id,
                    name: source.name,
                    code: newCode,
                    cost_price: source.cost_price,
                    selling_price: source.selling_price,
                    stock: source.stock,
                    min_stock: source.min_stock,
                    unit: source.unit,
                    is_active: source.is_active,
                    notes: source.notes
                });
            },

            removeRow(index) {
                if (this.items.length > 1) {
                    this.items.splice(index, 1);
                } else {
                    // Jika tinggal 1, reset isinya saja
                    this.items[0] = {
                        uid: Date.now(),
                        category_id: '',
                        name: '',
                        code: '',
                        cost_price: '',
                        selling_price: '',
                        stock: 0,
                        min_stock: 5,
                        unit: 'pcs',
                        is_active: true,
                        notes: ''
                    };
                }
            },

            clearAllRows() {
                if (confirm('Yakin ingin mengosongkan seluruh daftar produk yang belum disimpan?')) {
                    this.items = [];
                    this.addNewRow();
                }
            },

            onRowCategorySelect(index, cat) {
                const item = this.items[index];
                const oldCat = this.categories.find(c => c.id == item.category_id);
                const oldCatName = oldCat ? oldCat.name : '';

                item.category_id = cat.id;

                // Auto-fill awalan nama produk
                if (!item.name || item.name.trim() === '') {
                    item.name = cat.name + ' ';
                } else if (oldCatName && item.name.startsWith(oldCatName)) {
                    item.name = cat.name + item.name.substring(oldCatName.length);
                } else {
                    let replaced = false;
                    for (const c of this.categories) {
                        if (item.name.startsWith(c.name)) {
                            item.name = cat.name + item.name.substring(c.name.length);
                            replaced = true;
                            break;
                        }
                    }
                    if (!replaced) {
                        item.name = cat.name + ' ' + item.name;
                    }
                }

                // Saran satuan otomatis
                const lower = cat.name.toLowerCase();
                if (lower.includes('voucher')) item.unit = 'voucher';
                else if (lower.includes('kartu perdana') || lower.includes('perdana')) item.unit = 'kartu';
                else if (lower.includes('pulsa') || lower.includes('paket') || lower.includes('token') || lower.includes('e-wallet')) item.unit = 'transaksi';
                else item.unit = 'pcs';

                // Auto generate kode SKU
                this.generateItemSku(index);
            },

            onRowCategoryChange(index, event) {
                const catId = event.target.value;
                const cat = this.categories.find(c => c.id == catId);
                if (cat) {
                    this.onRowCategorySelect(index, cat);
                }
            },

            appendRowDenom(index, denom) {
                const item = this.items[index];
                const trimmed = item.name.trim();
                if (/\d+[\.\d]*/.test(trimmed)) {
                    item.name = trimmed.replace(/\d+[\.\d]*/, denom);
                } else {
                    item.name = trimmed + ' ' + denom;
                }
                this.generateItemSku(index);
            },

            generateItemSku(index) {
                const item = this.items[index];
                const cat = this.categories.find(c => c.id == item.category_id);
                if (!cat) return;

                let prefix = 'PROD';
                const lower = cat.name.toLowerCase();

                let prov = 'GEN';
                if (lower.includes('telkomsel')) prov = 'TSEL';
                else if (lower.includes('indosat')) prov = 'ISAT';
                else if (lower.includes('tri')) prov = 'TRI';
                else if (lower.includes('xl')) prov = 'XL';
                else if (lower.includes('axis')) prov = 'AXIS';
                else if (lower.includes('smartfren')) prov = 'SMART';
                else if (lower.includes('by.u') || lower.includes('byu')) prov = 'BYU';

                if (lower.includes('pulsa')) prefix = 'PULSA-' + prov;
                else if (lower.includes('voucher')) prefix = 'VCR-' + prov;
                else if (lower.includes('paket internet') || lower.includes('paket data')) prefix = 'DATA-' + prov;
                else if (lower.includes('kartu perdana')) prefix = 'SIM-' + prov;
                else if (lower.includes('token')) prefix = 'PLN';
                else if (lower.includes('dana')) prefix = 'DANA';
                else if (lower.includes('gopay')) prefix = 'GOPAY';
                else if (lower.includes('shopeepay')) prefix = 'SPAY';
                else if (lower.includes('seabank')) prefix = 'SEABANK';
                else if (lower.includes('ovo')) prefix = 'OVO';
                else if (cat.slug) prefix = cat.slug.toUpperCase().substring(0, 8);

                const match = item.name.match(/\d+[\.\d]*/);
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

                item.code = prefix + '-' + denomPart;
            },

            // Fungsi Generator Seri Sekaligus
            generateBatchSeries() {
                if (!this.batchGen.categoryId) {
                    alert('Silakan pilih kategori terlebih dahulu.');
                    return;
                }

                const cat = this.categories.find(c => c.id == this.batchGen.categoryId);
                if (!cat) return;

                if (this.batchGen.selectedDenoms.length === 0) {
                    alert('Pilih minimal 1 nominal denominasi.');
                    return;
                }

                // Jika baris pertama masih kosong belum diisi, kita ganti baris pertama
                if (this.items.length === 1 && !this.items[0].name && !this.items[0].category_id) {
                    this.items = [];
                }

                const lower = cat.name.toLowerCase();
                let unit = 'transaksi';
                if (lower.includes('voucher')) unit = 'voucher';
                else if (lower.includes('kartu perdana')) unit = 'kartu';
                else unit = 'pcs';

                this.batchGen.selectedDenoms.forEach((denomStr) => {
                    const cleanNominal = parseInt(denomStr.replace(/\./g, '')) || 0;
                    const costPrice = cleanNominal > 0 ? cleanNominal : 0;
                    const margin = parseInt(this.batchGen.baseMargin) || 2000;
                    const sellingPrice = costPrice > 0 ? costPrice + margin : 0;

                    let prov = 'GEN';
                    if (lower.includes('telkomsel')) prov = 'TSEL';
                    else if (lower.includes('indosat')) prov = 'ISAT';
                    else if (lower.includes('tri')) prov = 'TRI';
                    else if (lower.includes('xl')) prov = 'XL';
                    else if (lower.includes('axis')) prov = 'AXIS';
                    else if (lower.includes('smartfren')) prov = 'SMART';
                    else if (lower.includes('by.u') || lower.includes('byu')) prov = 'BYU';

                    let prefix = 'PROD';
                    if (lower.includes('pulsa')) prefix = 'PULSA-' + prov;
                    else if (lower.includes('voucher')) prefix = 'VCR-' + prov;
                    else if (lower.includes('paket internet') || lower.includes('paket data')) prefix = 'DATA-' + prov;
                    else if (lower.includes('kartu perdana')) prefix = 'SIM-' + prov;
                    else if (lower.includes('token')) prefix = 'PLN';
                    else if (lower.includes('dana')) prefix = 'DANA';
                    else if (lower.includes('gopay')) prefix = 'GOPAY';
                    else if (lower.includes('shopeepay')) prefix = 'SPAY';
                    else if (lower.includes('seabank')) prefix = 'SEABANK';
                    else if (lower.includes('ovo')) prefix = 'OVO';

                    const denomCode = cleanNominal >= 1000 ? (cleanNominal / 1000) + 'K' : cleanNominal;

                    this.addNewRow({
                        category_id: cat.id,
                        name: cat.name + ' ' + denomStr,
                        code: prefix + '-' + denomCode,
                        cost_price: costPrice,
                        selling_price: sellingPrice,
                        stock: this.batchGen.defaultStock,
                        min_stock: this.batchGen.defaultMinStock,
                        unit: unit,
                        is_active: true,
                        notes: ''
                    });
                });

                this.showBatchModal = false;
            },

            // Kalkulasi Ringkasan Finansial Semua Item
            get totalItemsCount() {
                return this.items.length;
            },

            get grandTotalStockCost() {
                return this.items.reduce((sum, item) => {
                    const c = parseFloat(item.cost_price) || 0;
                    const s = parseInt(item.stock) || 0;
                    return sum + (c * s);
                }, 0);
            },

            get grandTotalStockRevenue() {
                return this.items.reduce((sum, item) => {
                    const sp = parseFloat(item.selling_price) || 0;
                    const s = parseInt(item.stock) || 0;
                    return sum + (sp * s);
                }, 0);
            },

            get grandTotalPotentialProfit() {
                return this.grandTotalStockRevenue - this.grandTotalStockCost;
            },

            formatRupiah(num) {
                return 'Rp ' + Math.round(num || 0).toLocaleString('id-ID');
            }
        }"
        class="max-w-7xl mx-auto space-y-6 pb-28"
    >
        <!-- Top Navigation & Action Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-white dark:bg-slate-800 p-5 rounded-3xl border border-slate-200/80 dark:border-slate-700/80 shadow-xs">
            <div>
                <div class="flex items-center gap-2 text-xs font-semibold text-slate-500 mb-1">
                    <a href="{{ route('products.index') }}" class="hover:text-indigo-600 transition-colors">Produk</a>
                    <span>/</span>
                    <span class="text-indigo-600 dark:text-indigo-400">Tambah Banyak Sekaligus</span>
                </div>
                <h2 class="text-2xl font-black tracking-tight text-slate-900 dark:text-white flex items-center gap-2.5">
                    <span>Input Produk Fleksibel (Bisa Berapapun)</span>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-extrabold bg-indigo-100 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800">
                        <span x-text="items.length"></span> Produk
                    </span>
                </h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                    Tambah 1 produk, 5 produk, atau puluhan produk sekaligus dalam satu kali klik simpan tanpa bolak-balik form.
                </p>
            </div>

            <div class="flex items-center gap-2.5 flex-wrap">
                <!-- Tombol Buka Generator Seri Cepat -->
                <button
                    type="button"
                    @click="showBatchModal = true"
                    class="px-3.5 py-2 rounded-xl text-xs font-bold text-amber-700 dark:text-amber-300 bg-amber-50 hover:bg-amber-100 dark:bg-amber-950/50 border border-amber-200/80 dark:border-amber-800 transition-all flex items-center gap-1.5 cursor-pointer shadow-2xs active:scale-95"
                >
                    <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    <span>⚡ Generator Seri Nominal</span>
                </button>

                <!-- Tombol Tambah Baris Manual -->
                <button
                    type="button"
                    @click="addNewRow()"
                    class="px-4 py-2 rounded-xl text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 shadow-md shadow-indigo-600/20 transition-all flex items-center gap-1.5 cursor-pointer active:scale-95"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>+ Tambah Baris</span>
                </button>

                <x-button href="{{ route('products.index') }}" variant="secondary" size="sm">
                    Kembali
                </x-button>
            </div>
        </div>

        <!-- FORM UTAMA -->
        <form method="POST" action="{{ route('products.store') }}" id="multiProductForm" class="space-y-6">
            @csrf

            <!-- DAFTAR ITEM PRODUK -->
            <div class="space-y-5">
                <template x-for="(item, index) in items" :key="item.uid">
                    <div class="relative bg-white dark:bg-slate-800 rounded-3xl p-5 sm:p-6 border-2 border-slate-200/90 dark:border-slate-700 shadow-sm transition-all hover:border-indigo-300 dark:hover:border-indigo-600 space-y-5">
                        
                        <!-- Header Baris Produk -->
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-700/60 gap-3">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-xl bg-indigo-600 text-white font-black text-xs flex items-center justify-center shadow-xs">
                                    <span x-text="'#' + (index + 1)"></span>
                                </span>
                                <div>
                                    <h4 class="text-sm font-black text-slate-900 dark:text-white flex items-center gap-2">
                                        <span x-text="item.name || 'Baris Produk Baru ' + (index + 1)"></span>
                                    </h4>
                                    <span class="text-[11px] text-slate-400 font-mono" x-text="item.code ? 'SKU: ' + item.code : 'Belum ada kode'"></span>
                                </div>
                            </div>

                            <!-- Live Row Financial Pill & Row Actions -->
                            <div class="flex items-center gap-2 self-end sm:self-auto flex-wrap">
                                <!-- Est Laba per Unit -->
                                <div class="px-2.5 py-1 rounded-lg bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200/60 dark:border-emerald-800 text-[11px] font-bold text-emerald-700 dark:text-emerald-300 font-mono">
                                    <span>Laba/Unit: </span>
                                    <span x-text="formatRupiah((parseFloat(item.selling_price) || 0) - (parseFloat(item.cost_price) || 0))"></span>
                                </div>

                                <!-- Tombol Duplikat / Kloning -->
                                <button
                                    type="button"
                                    @click="duplicateRow(index)"
                                    class="p-2 rounded-xl text-slate-600 dark:text-slate-300 hover:bg-indigo-50 hover:text-indigo-600 dark:hover:bg-slate-700 transition-colors cursor-pointer text-xs font-semibold flex items-center gap-1"
                                    title="Duplikat baris ini"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                    <span class="hidden md:inline">Salin</span>
                                </button>

                                <!-- Tombol Hapus Baris -->
                                <button
                                    type="button"
                                    @click="removeRow(index)"
                                    class="p-2 rounded-xl text-rose-500 hover:bg-rose-50 hover:text-rose-700 dark:hover:bg-rose-950/40 transition-colors cursor-pointer text-xs font-semibold flex items-center gap-1"
                                    title="Hapus baris ini"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    <span class="hidden md:inline">Hapus</span>
                                </button>
                            </div>
                        </div>

                        <!-- Grid Fields Baris -->
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                            
                            <!-- 1. Kategori Dropdown (3 Cols) -->
                            <div class="md:col-span-3">
                                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 mb-1">
                                    Kategori <span class="text-rose-500">*</span>
                                </label>
                                <select
                                    :name="`items[${index}][category_id]`"
                                    x-model="item.category_id"
                                    @change="onRowCategoryChange(index, $event)"
                                    required
                                    class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-750 text-slate-900 dark:text-white text-xs font-semibold focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 shadow-2xs"
                                >
                                    <option value="">-- Pilih Kategori --</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- 2. Nama Lengkap Produk (5 Cols) -->
                            <div class="md:col-span-6">
                                <div class="flex items-center justify-between mb-1">
                                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300">
                                        Nama Lengkap Produk <span class="text-rose-500">*</span>
                                    </label>
                                    <!-- Mini Quick Denom Pills -->
                                    <div class="flex items-center gap-1">
                                        @foreach(['5.000', '10.000', '25.000', '50.000', '100.000'] as $d)
                                            <button
                                                type="button"
                                                @click="appendRowDenom(index, '{{ $d }}')"
                                                class="px-1.5 py-0.5 rounded text-[10px] font-mono bg-slate-100 dark:bg-slate-700 hover:bg-indigo-100 hover:text-indigo-700 text-slate-600 dark:text-slate-300 cursor-pointer"
                                            >
                                                {{ $d }}
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                                <input
                                    type="text"
                                    :name="`items[${index}][name]`"
                                    x-model="item.name"
                                    @input="generateItemSku(index)"
                                    placeholder="Contoh: Pulsa Tri 10.000 / Voucher Telkomsel 5GB"
                                    required
                                    class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-750 text-slate-900 dark:text-white text-xs font-bold focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 shadow-2xs"
                                >
                            </div>

                            <!-- 3. Kode SKU (3 Cols) -->
                            <div class="md:col-span-3">
                                <div class="flex items-center justify-between mb-1">
                                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300">
                                        Kode SKU <span class="text-rose-500">*</span>
                                    </label>
                                    <button
                                        type="button"
                                        @click="generateItemSku(index)"
                                        class="text-[10px] text-indigo-600 font-bold hover:underline cursor-pointer"
                                    >
                                        ⚡ Generate
                                    </button>
                                </div>
                                <input
                                    type="text"
                                    :name="`items[${index}][code]`"
                                    x-model="item.code"
                                    placeholder="SKU-10K"
                                    required
                                    class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-750 text-slate-900 dark:text-white text-xs font-mono font-bold focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 shadow-2xs"
                                >
                            </div>

                            <!-- 4. Harga Modal (3 Cols) -->
                            <div class="md:col-span-3">
                                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 mb-1">
                                    Harga Modal (Rp) <span class="text-rose-500">*</span>
                                </label>
                                <input
                                    type="number"
                                    :name="`items[${index}][cost_price]`"
                                    x-model="item.cost_price"
                                    min="0"
                                    step="0.01"
                                    placeholder="Contoh: 9800"
                                    required
                                    class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-750 text-slate-900 dark:text-white text-xs font-mono font-bold focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 shadow-2xs"
                                >
                            </div>

                            <!-- 5. Harga Jual (3 Cols) -->
                            <div class="md:col-span-3">
                                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 mb-1">
                                    Harga Jual (Rp) <span class="text-rose-500">*</span>
                                </label>
                                <input
                                    type="number"
                                    :name="`items[${index}][selling_price]`"
                                    x-model="item.selling_price"
                                    min="0"
                                    step="0.01"
                                    placeholder="Contoh: 12000"
                                    required
                                    class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-750 text-slate-900 dark:text-white text-xs font-mono font-bold focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 shadow-2xs"
                                >
                            </div>

                            <!-- 6. Stok Awal (2 Cols) -->
                            <div class="md:col-span-2">
                                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 mb-1">
                                    Stok Awal <span class="text-rose-500">*</span>
                                </label>
                                <input
                                    type="number"
                                    :name="`items[${index}][stock]`"
                                    x-model="item.stock"
                                    min="0"
                                    required
                                    class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-750 text-slate-900 dark:text-white text-xs font-bold focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 shadow-2xs"
                                >
                            </div>

                            <!-- 7. Min Stok (2 Cols) -->
                            <div class="md:col-span-2">
                                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 mb-1">
                                    Min Stok <span class="text-rose-500">*</span>
                                </label>
                                <input
                                    type="number"
                                    :name="`items[${index}][min_stock]`"
                                    x-model="item.min_stock"
                                    min="0"
                                    required
                                    class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-750 text-slate-900 dark:text-white text-xs font-bold focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 shadow-2xs"
                                >
                            </div>

                            <!-- 8. Satuan (2 Cols) -->
                            <div class="md:col-span-2">
                                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 mb-1">
                                    Satuan <span class="text-rose-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    :name="`items[${index}][unit]`"
                                    x-model="item.unit"
                                    required
                                    class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-750 text-slate-900 dark:text-white text-xs font-bold focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 shadow-2xs"
                                >
                            </div>

                            <!-- Hidden Fields for Item Status & Notes -->
                            <input type="hidden" :name="`items[${index}][is_active]`" :value="item.is_active ? 1 : 0">
                            <input type="hidden" :name="`items[${index}][notes]`" :value="item.notes || ''">

                        </div>
                    </div>
                </template>
            </div>

            <!-- Tombol Tambah Baris di Bawah Daftar -->
            <div class="flex items-center justify-between p-4 rounded-2xl border-2 border-dashed border-slate-300 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/40">
                <div class="text-xs text-slate-500">
                    Ingin menambah varian produk lainnya? Klik tombol tambah baris untuk menambah produk berikutnya.
                </div>
                <div class="flex items-center gap-2">
                    <button
                        type="button"
                        @click="clearAllRows()"
                        class="px-3 py-2 rounded-xl text-xs font-bold text-slate-500 hover:text-rose-600 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors cursor-pointer"
                    >
                        Kosongkan Semua
                    </button>
                    <button
                        type="button"
                        @click="addNewRow()"
                        class="px-4 py-2 rounded-xl text-xs font-bold text-indigo-600 hover:text-white hover:bg-indigo-600 border border-indigo-200 dark:border-indigo-800 bg-indigo-50 dark:bg-indigo-950/40 transition-all flex items-center gap-1.5 cursor-pointer shadow-xs active:scale-95"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span>+ Tambah 1 Baris Lagi</span>
                    </button>
                </div>
            </div>

            <!-- STICKY BOTTOM BAR: RINGKASAN & SIMPAN SEMUA -->
            <div class="fixed bottom-0 left-0 right-0 z-30 lg:pl-64 bg-white/90 dark:bg-slate-900/90 backdrop-blur-md border-t border-slate-200/80 dark:border-slate-750 px-4 sm:px-8 py-3.5 shadow-2xl">
                <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-4">
                    
                    <!-- Ringkasan Finansial -->
                    <div class="flex items-center gap-4 sm:gap-6 text-xs flex-wrap">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-indigo-500 animate-pulse"></span>
                            <span class="text-slate-500">Total Siap Disimpan:</span>
                            <strong class="text-slate-900 dark:text-white font-black text-sm" x-text="items.length + ' Produk'"></strong>
                        </div>
                        <div class="hidden sm:block text-slate-300">|</div>
                        <div class="flex items-center gap-1.5">
                            <span class="text-slate-500">Total Modal Persediaan:</span>
                            <strong class="font-mono font-bold text-slate-800 dark:text-slate-200" x-text="formatRupiah(grandTotalStockCost)"></strong>
                        </div>
                        <div class="hidden md:block text-slate-300">|</div>
                        <div class="hidden md:flex items-center gap-1.5">
                            <span class="text-slate-500">Est. Laba Total:</span>
                            <strong class="font-mono font-bold text-emerald-600 dark:text-emerald-400" x-text="'+ ' + formatRupiah(grandTotalPotentialProfit)"></strong>
                        </div>
                    </div>

                    <!-- Tombol Aksi Simpan Semua -->
                    <div class="flex items-center gap-3 w-full sm:w-auto">
                        <x-button href="{{ route('products.index') }}" variant="secondary" size="md">
                            Batal
                        </x-button>

                        <button
                            type="submit"
                            class="flex-1 sm:flex-none px-6 py-2.5 rounded-xl text-xs sm:text-sm font-black text-white bg-gradient-to-r from-indigo-600 via-indigo-700 to-indigo-800 hover:from-indigo-700 hover:to-indigo-900 shadow-lg shadow-indigo-600/30 active:scale-98 transition-all flex items-center justify-center gap-2 cursor-pointer"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>Simpan Semua (<span x-text="items.length"></span>) Produk Sekaligus</span>
                        </button>
                    </div>

                </div>
            </div>

        </form>

        <!-- MODAL: GENERATOR SERI NOMINAL CEPAT -->
        <div
            x-show="showBatchModal"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs"
            style="display: none;"
        >
            <div
                @click.away="showBatchModal = false"
                class="bg-white dark:bg-slate-800 rounded-3xl max-w-xl w-full p-6 sm:p-7 border border-slate-200 dark:border-slate-700 shadow-2xl space-y-5"
            >
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-700 pb-3">
                    <div class="flex items-center gap-2">
                        <span class="w-8 h-8 rounded-xl bg-amber-500 text-white flex items-center justify-center text-sm">⚡</span>
                        <div>
                            <h3 class="text-base font-black text-slate-900 dark:text-white">Generator Seri Nominal Otomatis</h3>
                            <p class="text-xs text-slate-500">Pilih kategori dan nominal untuk membuat banyak produk sekaligus dalam 1 klik.</p>
                        </div>
                    </div>
                    <button
                        type="button"
                        @click="showBatchModal = false"
                        class="text-slate-400 hover:text-slate-600 text-lg p-1 cursor-pointer"
                    >
                        &times;
                    </button>
                </div>

                <div class="space-y-4 text-xs">
                    <!-- 1. Pilih Kategori -->
                    <div>
                        <label class="block font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 mb-1.5">
                            Pilih Kategori Produk <span class="text-rose-500">*</span>
                        </label>
                        <select
                            x-model="batchGen.categoryId"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-750 text-slate-900 dark:text-white font-semibold focus:ring-2 focus:ring-indigo-500 shadow-2xs"
                        >
                            <option value="">-- Pilih Kategori (Misal: Pulsa Telkomsel, Voucher Tri, E-Wallet DANA) --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- 2. Centang Pilihan Nominal -->
                    <div>
                        <label class="block font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 mb-2">
                            Pilih Nominal yang Ingin Dibuat:
                        </label>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                            @foreach(['5.000', '10.000', '15.000', '20.000', '25.000', '50.000', '100.000', '150.000', '200.000'] as $d)
                                <label class="flex items-center gap-2 p-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-750 cursor-pointer hover:border-indigo-400 select-none">
                                    <input
                                        type="checkbox"
                                        value="{{ $d }}"
                                        x-model="batchGen.selectedDenoms"
                                        class="rounded text-indigo-600 focus:ring-indigo-500"
                                    >
                                    <span class="font-mono font-bold">{{ $d }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- 3. Atur Margin Keuntungan Default -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                        <div>
                            <label class="block font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 mb-1">
                                Tambahan Untung (Markup Jual)
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400 font-bold">Rp</span>
                                <input
                                    type="number"
                                    x-model="batchGen.baseMargin"
                                    class="w-full pl-9 pr-3 py-2 rounded-xl border border-slate-300 dark:border-slate-600 font-mono font-bold"
                                    placeholder="2000"
                                >
                            </div>
                            <span class="text-[10px] text-slate-400">Harga Jual = Nominal + Markup</span>
                        </div>

                        <div>
                            <label class="block font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 mb-1">
                                Stok Awal Default
                            </label>
                            <input
                                type="number"
                                x-model="batchGen.defaultStock"
                                class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-600 font-bold"
                                placeholder="10"
                            >
                            <span class="text-[10px] text-slate-400">Stok untuk setiap nominal</span>
                        </div>
                    </div>
                </div>

                <!-- Tombol Aksi Generator -->
                <div class="pt-4 border-t border-slate-100 dark:border-slate-700 flex items-center justify-end gap-3">
                    <button
                        type="button"
                        @click="showBatchModal = false"
                        class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 dark:text-slate-400 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 cursor-pointer"
                    >
                        Batal
                    </button>
                    <button
                        type="button"
                        @click="generateBatchSeries()"
                        class="px-5 py-2 rounded-xl text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 shadow-md shadow-indigo-600/20 cursor-pointer flex items-center gap-1.5"
                    >
                        <span>🚀 Masukkan ke Daftar Produk</span>
                        <span x-text="'(' + batchGen.selectedDenoms.length + ' Item)'"></span>
                    </button>
                </div>
            </div>
        </div>

    </div>
</x-layouts.app>
