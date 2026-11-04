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
        $tokenCats = $categories->filter(fn($c) => str_contains(strtolower($c->name), 'token') || (str_contains(strtolower($c->name), 'listrik') && !str_contains(strtolower($c->name), 'tagihan') && !str_contains(strtolower($c->name), 'pajak')));
        $plnPascaCats = $categories->filter(fn($c) => str_contains(strtolower($c->name), 'pajak') || (str_contains(strtolower($c->name), 'listrik') && (str_contains(strtolower($c->name), 'tagihan') || str_contains(strtolower($c->name), 'pajak'))));
        $bpjsCats = $categories->filter(fn($c) => str_contains(strtolower($c->name), 'bpjs'));
        $wifiCats = $categories->filter(fn($c) => str_contains(strtolower($c->name), 'wifi') || str_contains(strtolower($c->name), 'indihome') || str_contains(strtolower($c->name), 'iconnet') || str_contains(strtolower($c->name), 'myrepublic') || str_contains(strtolower($c->name), 'first media') || str_contains(strtolower($c->name), 'firstmedia'));
        $gameCats = $categories->filter(fn($c) => str_contains(strtolower($c->name), 'game'));
        $otherCats = $categories->reject(fn($c) => 
            str_starts_with(strtolower($c->name), 'pulsa') || 
            str_starts_with(strtolower($c->name), 'voucher') || 
            str_starts_with(strtolower($c->name), 'paket internet') || 
            str_starts_with(strtolower($c->name), 'kartu perdana') || 
            str_starts_with(strtolower($c->name), 'e-wallet') || 
            str_contains(strtolower($c->name), 'token') ||
            str_contains(strtolower($c->name), 'listrik') ||
            str_contains(strtolower($c->name), 'pajak') ||
            str_contains(strtolower($c->name), 'bpjs') ||
            str_contains(strtolower($c->name), 'wifi') ||
            str_contains(strtolower($c->name), 'indihome') ||
            str_contains(strtolower($c->name), 'iconnet') ||
            str_contains(strtolower($c->name), 'myrepublic') ||
            str_contains(strtolower($c->name), 'first media') ||
            str_contains(strtolower($c->name), 'firstmedia') ||
            str_contains(strtolower($c->name), 'game')
        );
    @endphp

    <div 
        x-data="{
            categories: {{ Js::from($categories) }},
            existingCodes: {{ Js::from($existingCodes ?? []) }},
            items: [],
            showBatchModal: false,

            // State Generator Seri Cepat
            batchGen: {
                categoryId: '',
                gameType: 'ff', // 'ff' = Free Fire, 'ml' = Mobile Legends
                selectedDenoms: ['5.000', '10.000', '25.000', '50.000', '100.000'],
                baseMargin: 2000,
                defaultStock: 0,
                defaultMinStock: 0
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
                        manual_code: Boolean(item.code),
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
                    notes: prefill.notes || '',
                    manual_code: Boolean(prefill.code)
                });
            },

            duplicateRow(index) {
                const source = this.items[index];
                const uid = Date.now() + Math.floor(Math.random() * 1000);
                
                // Buat kode SKU baru dengan suffix acak agar unik
                let newCode = source.code ? source.code + '-CPY' : '';
                if (source.code && source.code.includes('-')) {
                    const parts = source.code.split('-');
                    newCode = parts.slice(0, -1).join('-') + '-' + Math.floor(100 + Math.random() * 900);
                }

                // Pastikan newCode tidak bentrok dengan database atau form
                const allUsed = [...(this.existingCodes || []).map(c => c.toUpperCase()), ...this.items.map(it => (it.code || '').toUpperCase())];
                let suffix = 2;
                while (allUsed.includes(newCode.toUpperCase())) {
                    newCode = source.code + '-' + suffix++;
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
                    notes: source.notes,
                    manual_code: false
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

            isDigitalCategory(catId) {
                if (!catId) return false;
                const cat = this.categories.find(c => c.id == catId);
                if (!cat) return false;
                const lower = cat.name.toLowerCase();
                return lower.includes('pulsa') || 
                       lower.includes('paket') || 
                       lower.includes('token') || 
                       lower.includes('listrik') || 
                       lower.includes('pajak') || 
                       lower.includes('pln') || 
                       lower.includes('bpjs') || 
                       lower.includes('wifi') || 
                       lower.includes('indihome') || 
                       lower.includes('iconnet') || 
                       lower.includes('myrepublic') || 
                       lower.includes('firstmedia') || 
                       lower.includes('first media') || 
                       lower.includes('game') || 
                       lower.includes('e-wallet') || 
                       lower.includes('transfer') || 
                       lower.includes('tarik') || 
                       lower.includes('jasa');
            },

            isGameCategory(catId) {
                if (!catId) return false;
                const cat = this.categories.find(c => c.id == catId);
                return cat ? cat.name.toLowerCase().includes('game') : false;
            },

            setRowGamePreset(index, gameKey) {
                const item = this.items[index];
                if (gameKey === 'ff') {
                    item.name = 'Top Up Game Free Fire ';
                } else if (gameKey === 'ml') {
                    item.name = 'Top Up Game Mobile Legends ';
                } else {
                    item.name = 'Top Up Game ';
                }
                this.generateItemSku(index);
            },

            onRowCategorySelect(index, cat) {
                const item = this.items[index];
                const oldCat = this.categories.find(c => c.id == item.category_id);
                const oldCatName = oldCat ? oldCat.name : '';

                item.category_id = cat.id;
                const lower = cat.name.toLowerCase();

                // Auto-fill awalan nama produk spesifik
                if (lower.includes('game')) {
                    if (!item.name || item.name.trim() === '' || (oldCatName && item.name.startsWith(oldCatName))) {
                        item.name = 'Top Up Game Free Fire ';
                    }
                } else if (lower.includes('bpjs')) {
                    item.name = 'Tagihan BPJS Kesehatan';
                } else if (lower.includes('pajak') || (lower.includes('listrik') && lower.includes('tagihan'))) {
                    item.name = 'Tagihan Listrik Pascabayar PLN';
                } else if (lower.includes('indihome')) {
                    item.name = 'Tagihan WiFi Telkom IndiHome';
                } else if (lower.includes('iconnet')) {
                    item.name = 'Tagihan WiFi ICONNET PLN';
                } else if (lower.includes('myrepublic')) {
                    item.name = 'Tagihan WiFi MyRepublic';
                } else if (lower.includes('first media') || lower.includes('firstmedia')) {
                    item.name = 'Tagihan WiFi First Media';
                } else if (!item.name || item.name.trim() === '') {
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

                // Saran satuan & penyesuaian stok otomatis (Fisik vs Digital/Server)
                const isDigital = this.isDigitalCategory(cat.id);

                if (isDigital) {
                    item.unit = 'transaksi';
                    item.stock = 0;
                    item.min_stock = 0;
                } else if (lower.includes('voucher')) {
                    item.unit = 'voucher';
                    if (!item.stock || item.stock == 0) item.stock = 10;
                    if (!item.min_stock || item.min_stock == 0) item.min_stock = 3;
                } else if (lower.includes('kartu perdana') || lower.includes('perdana')) {
                    item.unit = 'kartu';
                    if (!item.stock || item.stock == 0) item.stock = 5;
                    if (!item.min_stock || item.min_stock == 0) item.min_stock = 2;
                } else {
                    item.unit = 'pcs';
                    if (!item.stock || item.stock == 0) item.stock = 10;
                    if (!item.min_stock || item.min_stock == 0) item.min_stock = 3;
                }

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
                let current = (item.name || '').trim();

                // Jika sudah ada nominal di bagian belakang, bersihkan dulu
                if (/\s\d+[\.\d]*(\s*(Diamond|DM|UC|Robux|K))?$/i.test(current)) {
                    current = current.replace(/\s\d+[\.\d]*(\s*(Diamond|DM|UC|Robux|K))?$/i, '').trim();
                } else if (/(Member Mingguan|Member Bulanan|Weekly Diamond Pass|WDP)$/i.test(current)) {
                    current = current.replace(/\s*(Member Mingguan|Member Bulanan|Weekly Diamond Pass|WDP)$/i, '').trim();
                }

                item.name = current + (current.length > 0 ? ' ' : '') + denom;
                item.manual_code = false;
                this.generateItemSku(index);
            },

            // Deteksi Brand Provider & Layanan Cerdas
            detectBrand(name, catName = '') {
                const n = (name || '').toLowerCase();
                const c = (catName || '').toLowerCase();

                // 1. Prioritas Utama: Nama Produk yang diketik kasir (karena lebih spesifik jika kasir ketik langsung)
                if (n.includes('by.u') || n.includes('by u') || n.includes('byu') || n.includes('by-u')) return 'BYU';
                if (n.includes('telkomsel') || n.includes('tsel')) return 'TSEL';
                if (n.includes('indosat') || n.includes('im3') || n.includes('isat')) return 'ISAT';
                if (n.includes('tri') || n.includes('three') || /\b3\b/.test(n)) return 'TRI';
                if (n.includes('smartfren') || n.includes('smart')) return 'SMART';
                if (n.includes('axis')) return 'AXIS';
                if (/\bxl\b/.test(n) || n.includes('xl ') || n.startsWith('xl')) return 'XL';

                // 2. Prioritas Kedua: Nama Kategori pilihan jika di nama belum ada kata kunci
                if (c.includes('by.u') || c.includes('by u') || c.includes('byu') || c.includes('by-u')) return 'BYU';
                if (c.includes('telkomsel') || c.includes('tsel')) return 'TSEL';
                if (c.includes('indosat') || c.includes('im3') || c.includes('isat')) return 'ISAT';
                if (c.includes('tri') || c.includes('three') || /\b3\b/.test(c)) return 'TRI';
                if (c.includes('smartfren') || c.includes('smart')) return 'SMART';
                if (c.includes('axis')) return 'AXIS';
                if (/\bxl\b/.test(c) || c.includes('xl')) return 'XL';

                return 'GEN';
            },

            // Handler input nama produk kasir (Auto match brand kategori & generate SKU)
            onNameInput(index) {
                const item = this.items[index];
                if (!item.name) return;

                // Sinkronisasi Kategori Otomatis jika nama memuat brand yang beda dengan kategori saat ini
                const detectedBrand = this.detectBrand(item.name);
                const currentCat = this.categories.find(c => c.id == item.category_id);

                if (detectedBrand !== 'GEN') {
                    const brandSlug = detectedBrand === 'BYU' ? 'byu' :
                                      detectedBrand === 'TSEL' ? 'telkomsel' :
                                      detectedBrand === 'ISAT' ? 'indosat' :
                                      detectedBrand === 'TRI' ? 'tri' :
                                      detectedBrand === 'SMART' ? 'smartfren' :
                                      detectedBrand === 'AXIS' ? 'axis' :
                                      detectedBrand === 'XL' ? 'xl' : '';

                    if (brandSlug) {
                        const catType = (currentCat ? currentCat.slug : '').split('-')[0] || 'pulsa';
                        const betterCat = this.categories.find(c => c.slug.includes(brandSlug) && c.slug.includes(catType));
                        if (betterCat && betterCat.id != item.category_id) {
                            item.category_id = betterCat.id;
                        }
                    }
                }

                // Generate SKU otomatis jika kasir belum mengubah kode secara manual
                if (!item.manual_code) {
                    this.generateItemSku(index);
                }
            },

            generateItemSku(index) {
                const item = this.items[index];
                const cat = this.categories.find(c => c.id == item.category_id);
                if (!cat) return;

                let prefix = 'PROD';
                const lowerCat = cat.name.toLowerCase();
                const lowerName = (item.name || '').toLowerCase();

                let prov = this.detectBrand(item.name, cat.name);

                if (lowerCat.includes('pulsa') || lowerName.includes('pulsa')) prefix = 'PULSA-' + prov;
                else if (lowerCat.includes('voucher') || lowerName.includes('voucher')) prefix = 'VCR-' + prov;
                else if (lowerCat.includes('paket internet') || lowerCat.includes('paket data') || lowerName.includes('paket') || lowerName.includes('kuota') || lowerName.includes('data')) prefix = 'DATA-' + prov;
                else if (lowerCat.includes('kartu perdana') || lowerName.includes('perdana')) prefix = 'SIM-' + prov;
                else if (lowerCat.includes('token') || lowerName.includes('token')) prefix = 'PLN';
                else if (lowerCat.includes('dana') || lowerName.includes('dana')) prefix = 'DANA';
                else if (lowerCat.includes('gopay') || lowerName.includes('gopay')) prefix = 'GOPAY';
                else if (lowerCat.includes('shopeepay') || lowerName.includes('shopee')) prefix = 'SPAY';
                else if (lowerCat.includes('seabank') || lowerName.includes('seabank')) prefix = 'SEABANK';
                else if (lowerCat.includes('ovo') || lowerName.includes('ovo')) prefix = 'OVO';
                else if (lowerCat.includes('bpjs') || lowerName.includes('bpjs')) prefix = 'BPJS-KES';
                else if (lowerCat.includes('pajak') || (lowerCat.includes('listrik') && lowerCat.includes('tagihan')) || lowerName.includes('pajak listrik')) prefix = 'PLN-PASCA';
                else if (lowerCat.includes('indihome') || lowerName.includes('indihome')) prefix = 'WIFI-INDI';
                else if (lowerCat.includes('iconnet') || lowerName.includes('iconnet')) prefix = 'WIFI-ICON';
                else if (lowerCat.includes('myrepublic') || lowerName.includes('myrepublic')) prefix = 'WIFI-MYREP';
                else if (lowerCat.includes('first media') || lowerCat.includes('firstmedia') || lowerName.includes('first media')) prefix = 'WIFI-FM';
                else if (lowerCat.includes('game') || lowerName.includes('game') || lowerName.includes('diamond')) {
                    if (lowerName.includes('free fire') || lowerName.includes('ff')) {
                        prefix = 'GAME-FF';
                    } else if (lowerName.includes('mobile legends') || lowerName.includes('mlbb') || lowerName.includes('ml')) {
                        prefix = 'GAME-ML';
                    } else {
                        // Game baru manual lainnya
                        const cleaned = lowerName.replace(/top\s*up/g, '').replace(/game/g, '').trim();
                        const firstWord = cleaned.split(/\s+/)[0] || 'NEW';
                        prefix = 'GAME-' + firstWord.toUpperCase().substring(0, 6);
                    }
                } else if (cat.slug) {
                    prefix = cat.slug.toUpperCase().substring(0, 8);
                }

                // Denom part extraction (Mendukung format: 5k, 10k, 5rb, 5.000, 5000, Diamond)
                let denomPart = '';
                if (lowerName.includes('weekly') || lowerName.includes('wdp')) {
                    denomPart = 'WDP';
                } else if (lowerName.includes('mingguan')) {
                    denomPart = 'MINGGU';
                } else if (lowerName.includes('bulanan')) {
                    denomPart = 'BULAN';
                } else {
                    const kMatch = item.name.match(/\b(\d+)\s*k\b/i);
                    const rbMatch = item.name.match(/\b(\d+)\s*rb\b/i);
                    const numMatch = item.name.match(/\d+[\.\d]*/);

                    if (kMatch) {
                        denomPart = kMatch[1] + 'K';
                    } else if (rbMatch) {
                        denomPart = rbMatch[1] + 'K';
                    } else if (numMatch) {
                        const rawNum = parseInt(numMatch[0].replace(/\./g, '')) || 0;
                        if (rawNum >= 1000) {
                            denomPart = (rawNum / 1000) + 'K';
                        } else if (rawNum > 0) {
                            denomPart = rawNum + (lowerCat.includes('game') || lowerName.includes('diamond') ? 'DM' : '');
                        } else {
                            denomPart = Math.floor(100 + Math.random() * 900);
                        }
                    } else {
                        denomPart = Math.floor(100 + Math.random() * 900);
                    }
                }

                let baseCode = prefix + '-' + denomPart;
                let finalCode = baseCode;

                // Pastikan kode unik, tidak tabrakan dengan database atau baris lain di form ini
                const otherCodes = this.items.filter((_, idx) => idx !== index).map(it => (it.code || '').trim().toUpperCase());
                const dbCodes = (this.existingCodes || []).map(c => c.trim().toUpperCase());
                let counter = 1;
                while (dbCodes.includes(finalCode.toUpperCase()) || otherCodes.includes(finalCode.toUpperCase())) {
                    counter++;
                    finalCode = baseCode + '-' + counter;
                }

                item.code = finalCode;
            },

            // Cek apakah kode SKU saat ini duplikat
            isCodeDuplicate(index) {
                const item = this.items[index];
                if (!item.code || !item.code.trim()) return false;
                const upperCode = item.code.trim().toUpperCase();

                const dbCodes = (this.existingCodes || []).map(c => c.trim().toUpperCase());
                if (dbCodes.includes(upperCode)) return true;

                const duplicateInRows = this.items.some((it, i) => i !== index && (it.code || '').trim().toUpperCase() === upperCode);
                return duplicateInRows;
            },

            getDuplicateMsg(index) {
                const item = this.items[index];
                if (!item.code) return '';
                const upperCode = item.code.trim().toUpperCase();
                const dbCodes = (this.existingCodes || []).map(c => c.trim().toUpperCase());

                if (dbCodes.includes(upperCode)) {
                    return `⚠️ SKU ${item.code} sudah terdaftar di database.`;
                }
                return `⚠️ SKU ${item.code} kembar dengan baris lain di form ini.`;
            },

            makeCodeUnique(index) {
                this.items[index].manual_code = false;
                this.generateItemSku(index);
            },

            // Handler saat kategori generator batch berubah
            onBatchCategoryChange() {
                const cat = this.categories.find(c => c.id == this.batchGen.categoryId);
                if (!cat) return;
                const lower = cat.name.toLowerCase();
                const isDigital = this.isDigitalCategory(cat.id);
                if (isDigital) {
                    this.batchGen.defaultStock = 0;
                    this.batchGen.defaultMinStock = 0;
                } else if (lower.includes('voucher')) {
                    this.batchGen.defaultStock = 10;
                    this.batchGen.defaultMinStock = 3;
                } else if (lower.includes('kartu perdana')) {
                    this.batchGen.defaultStock = 5;
                    this.batchGen.defaultMinStock = 2;
                } else {
                    this.batchGen.defaultStock = 10;
                    this.batchGen.defaultMinStock = 3;
                }

                if (lower.includes('game')) {
                    this.batchGen.gameType = 'ff';
                    this.batchGen.selectedDenoms = ['50 Diamond', '70 Diamond', '140 Diamond', '355 Diamond', '720 Diamond'];
                } else {
                    this.batchGen.selectedDenoms = ['5.000', '10.000', '25.000', '50.000', '100.000'];
                }
            },

            onBatchGameTypeChange(type) {
                this.batchGen.gameType = type;
                if (type === 'ff') {
                    this.batchGen.selectedDenoms = ['50 Diamond', '70 Diamond', '140 Diamond', '355 Diamond', '720 Diamond'];
                } else if (type === 'ml') {
                    this.batchGen.selectedDenoms = ['86 Diamond', '172 Diamond', '257 Diamond', '706 Diamond', 'Weekly Diamond Pass'];
                }
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
                const isDigital = this.isDigitalCategory(cat.id);
                let unit = 'transaksi';
                if (lower.includes('voucher')) unit = 'voucher';
                else if (lower.includes('kartu perdana')) unit = 'kartu';
                else if (isDigital) unit = 'transaksi';
                else unit = 'pcs';

                const isGame = lower.includes('game');

                this.batchGen.selectedDenoms.forEach((denomStr) => {
                    const cleanNominal = parseInt(denomStr.replace(/\./g, '')) || 0;
                    const costPrice = cleanNominal > 0 ? cleanNominal : 0;
                    const margin = parseInt(this.batchGen.baseMargin) || 2000;
                    const sellingPrice = costPrice > 0 ? costPrice + margin : 0;

                    let prefix = 'PROD';
                    let prov = this.detectBrand('', cat.name);

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
                    else if (lower.includes('bpjs')) prefix = 'BPJS-KES';
                    else if (lower.includes('pajak') || (lower.includes('listrik') && lower.includes('tagihan'))) prefix = 'PLN-PASCA';
                    else if (lower.includes('indihome')) prefix = 'WIFI-INDI';
                    else if (lower.includes('iconnet')) prefix = 'WIFI-ICON';
                    else if (lower.includes('myrepublic')) prefix = 'WIFI-MYREP';
                    else if (lower.includes('first media') || lower.includes('firstmedia')) prefix = 'WIFI-FM';
                    else if (isGame) {
                        prefix = this.batchGen.gameType === 'ff' ? 'GAME-FF' : 'GAME-ML';
                    }

                    let denomCode = cleanNominal >= 1000 ? (cleanNominal / 1000) + 'K' : cleanNominal;
                    if (isGame) {
                        if (denomStr.toLowerCase().includes('weekly') || denomStr.toLowerCase().includes('wdp')) {
                            denomCode = 'WDP';
                        } else if (denomStr.toLowerCase().includes('mingguan')) {
                            denomCode = 'MINGGU';
                        } else {
                            const dmMatch = denomStr.match(/\d+/);
                            denomCode = dmMatch ? dmMatch[0] + 'DM' : denomStr;
                        }
                    }

                    let baseCode = prefix + '-' + denomCode;
                    let finalCode = baseCode;
                    let counter = 1;
                    const existingInFormAndDb = [
                        ...(this.existingCodes || []).map(c => c.toUpperCase()),
                        ...this.items.map(it => (it.code || '').toUpperCase())
                    ];
                    while (existingInFormAndDb.includes(finalCode.toUpperCase())) {
                        counter++;
                        finalCode = baseCode + '-' + counter;
                    }

                    let fullProdName = cat.name + ' ' + denomStr;
                    if (isGame) {
                        const gameTitle = this.batchGen.gameType === 'ff' ? 'Free Fire' : 'Mobile Legends';
                        fullProdName = 'Top Up Game ' + gameTitle + ' ' + denomStr;
                    }

                    this.addNewRow({
                        category_id: cat.id,
                        name: fullProdName,
                        code: finalCode,
                        cost_price: costPrice,
                        selling_price: sellingPrice,
                        stock: this.batchGen.defaultStock,
                        min_stock: this.batchGen.defaultMinStock,
                        unit: unit,
                        is_active: true,
                        notes: '',
                        manual_code: false
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

        <!-- PANDUAN LOGIKA KATEGORI & STOK AWAL -->
        <div class="bg-gradient-to-r from-blue-50/90 via-indigo-50/80 to-purple-50/80 dark:from-slate-800 dark:via-slate-800/90 dark:to-slate-800 rounded-3xl p-5 border border-indigo-100 dark:border-slate-700 shadow-2xs">
            <div class="flex items-start gap-3.5">
                <div class="w-10 h-10 rounded-2xl bg-indigo-600 text-white flex items-center justify-center shrink-0 shadow-sm text-base">
                    💡
                </div>
                <div class="flex-1 text-xs">
                    <h4 class="text-sm font-black text-slate-900 dark:text-white">
                        Panduan Kategori & Logika Pengisian Stok Awal
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mt-2.5 text-slate-600 dark:text-slate-300">
                        <div class="p-3 rounded-2xl bg-white/90 dark:bg-slate-750/90 border border-slate-200/70 dark:border-slate-700">
                            <strong class="text-emerald-700 dark:text-emerald-300 font-bold flex items-center gap-1.5 mb-1 text-xs">
                                <span>📦</span> 1. Produk Fisik (Voucher Fisik, Perdana, Aksesoris)
                            </strong>
                            <p class="leading-relaxed text-[11.5px] text-slate-600 dark:text-slate-300">
                                Isi <strong>Stok Awal</strong> sesuai jumlah fisik barang riil yang ada di rak/laci (satuan: <em>pcs, voucher, kartu</em>). Sistem otomatis mencatat mutasi stok awal masuk dan memotong stok setiap kali kasir mencatat transaksi penjualan.
                            </p>
                        </div>
                        <div class="p-3 rounded-2xl bg-white/90 dark:bg-slate-750/90 border border-slate-200/70 dark:border-slate-700">
                            <strong class="text-amber-700 dark:text-amber-300 font-bold flex items-center gap-1.5 mb-1 text-xs">
                                <span>⚡</span> 2. Produk Digital / Server (Pulsa, Paket Data, Token PLN, E-Wallet)
                            </strong>
                            <p class="leading-relaxed text-[11.5px] text-slate-600 dark:text-slate-300">
                                Produk digital tidak memiliki fisik di toko. <strong>Stok Awal cukup diisi 0</strong> (satuan: <em>transaksi</em>). Ketersediaan modal dan transaksi digital dipotong otomatis dari <strong>Saldo Server Konter</strong>.
                            </p>
                        </div>
                    </div>
                </div>
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
                                    @if($pulsaCats->isNotEmpty())
                                        <optgroup label="📱 Pulsa Reguler (All Operator)">
                                            @foreach($pulsaCats as $cat)
                                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                            @endforeach
                                        </optgroup>
                                    @endif
                                    @if($voucherCats->isNotEmpty())
                                        <optgroup label="🎟️ Voucher Fisik & Kuota">
                                            @foreach($voucherCats as $cat)
                                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                            @endforeach
                                        </optgroup>
                                    @endif
                                    @if($paketCats->isNotEmpty())
                                        <optgroup label="🌐 Paket Internet & Data">
                                            @foreach($paketCats as $cat)
                                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                            @endforeach
                                        </optgroup>
                                    @endif
                                    @if($perdanaCats->isNotEmpty())
                                        <optgroup label="💳 Kartu Perdana Segel">
                                            @foreach($perdanaCats as $cat)
                                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                            @endforeach
                                        </optgroup>
                                    @endif
                                    @if($ewalletCats->isNotEmpty())
                                        <optgroup label="👛 E-Wallet & Bank Digital">
                                            @foreach($ewalletCats as $cat)
                                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                            @endforeach
                                        </optgroup>
                                    @endif
                                    @if($tokenCats->isNotEmpty())
                                        <optgroup label="⚡ Token Listrik PLN">
                                            @foreach($tokenCats as $cat)
                                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                            @endforeach
                                        </optgroup>
                                    @endif
                                    @if($plnPascaCats->isNotEmpty())
                                        <optgroup label="⚡ Tagihan & Pajak Listrik PLN">
                                            @foreach($plnPascaCats as $cat)
                                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                            @endforeach
                                        </optgroup>
                                    @endif
                                    @if($bpjsCats->isNotEmpty())
                                        <optgroup label="🏥 BPJS Kesehatan">
                                            @foreach($bpjsCats as $cat)
                                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                            @endforeach
                                        </optgroup>
                                    @endif
                                    @if($wifiCats->isNotEmpty())
                                        <optgroup label="📡 Tagihan WiFi & Internet Rumah">
                                            @foreach($wifiCats as $cat)
                                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                            @endforeach
                                        </optgroup>
                                    @endif
                                    @if($gameCats->isNotEmpty())
                                        <optgroup label="🎮 Top Up Game Online">
                                            @foreach($gameCats as $cat)
                                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                            @endforeach
                                        </optgroup>
                                    @endif
                                    @if($otherCats->isNotEmpty())
                                        <optgroup label="📦 Aksesoris, Jasa & Lainnya">
                                            @foreach($otherCats as $cat)
                                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                            @endforeach
                                        </optgroup>
                                    @endif
                                </select>
                            </div>

                            <!-- 2. Nama Lengkap Produk (5 Cols) -->
                            <div class="md:col-span-6">
                                <div class="flex items-center justify-between mb-1 gap-2 flex-wrap">
                                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300">
                                        Nama Lengkap Produk <span class="text-rose-500">*</span>
                                    </label>
                                    
                                    <!-- Mini Quick Helper Pills -->
                                    <template x-if="isGameCategory(item.category_id)">
                                        <div class="flex items-center gap-1 flex-wrap">
                                            <!-- Pilihan Game Khusus -->
                                            <button
                                                type="button"
                                                @click="setRowGamePreset(index, 'ff')"
                                                :class="item.name.toLowerCase().includes('free fire') ? 'bg-amber-500 text-white font-bold' : 'bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300'"
                                                class="px-2 py-0.5 rounded text-[10px] cursor-pointer hover:bg-amber-400"
                                            >
                                                🔥 Free Fire
                                            </button>
                                            <button
                                                type="button"
                                                @click="setRowGamePreset(index, 'ml')"
                                                :class="item.name.toLowerCase().includes('mobile legends') ? 'bg-blue-600 text-white font-bold' : 'bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300'"
                                                class="px-2 py-0.5 rounded text-[10px] cursor-pointer hover:bg-blue-500"
                                            >
                                                ⚔️ Mobile Legends
                                            </button>
                                            <button
                                                type="button"
                                                @click="setRowGamePreset(index, 'custom')"
                                                :class="!item.name.toLowerCase().includes('free fire') && !item.name.toLowerCase().includes('mobile legends') ? 'bg-indigo-600 text-white font-bold' : 'bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300'"
                                                class="px-2 py-0.5 rounded text-[10px] cursor-pointer hover:bg-indigo-500"
                                                title="Game baru tinggal ketik manual"
                                            >
                                                🎮 Game Baru (Manual)
                                            </button>
                                        </div>
                                    </template>

                                    <template x-if="!isGameCategory(item.category_id)">
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
                                    </template>
                                </div>

                                <!-- Sub-Pills Denominasi Game jika Game Terpilih -->
                                <template x-if="isGameCategory(item.category_id)">
                                    <div class="mb-1.5 flex items-center gap-1 flex-wrap">
                                        <!-- Jika Free Fire -->
                                        <template x-if="item.name.toLowerCase().includes('free fire')">
                                            <div class="flex items-center gap-1 flex-wrap">
                                                <span class="text-[10px] text-amber-600 dark:text-amber-400 font-bold">FF:</span>
                                                @foreach(['50 Diamond', '70 Diamond', '140 Diamond', '355 Diamond', '720 Diamond', 'Member Mingguan'] as $d)
                                                    <button
                                                        type="button"
                                                        @click="appendRowDenom(index, '{{ $d }}')"
                                                        class="px-1.5 py-0.5 rounded text-[10px] bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 hover:bg-amber-100 text-amber-800 dark:text-amber-300 font-medium cursor-pointer"
                                                    >
                                                        {{ $d }}
                                                    </button>
                                                @endforeach
                                            </div>
                                        </template>

                                        <!-- Jika Mobile Legends -->
                                        <template x-if="item.name.toLowerCase().includes('mobile legends')">
                                            <div class="flex items-center gap-1 flex-wrap">
                                                <span class="text-[10px] text-blue-600 dark:text-blue-400 font-bold">ML:</span>
                                                @foreach(['86 Diamond', '172 Diamond', '257 Diamond', '706 Diamond', 'Weekly Diamond Pass'] as $d)
                                                    <button
                                                        type="button"
                                                        @click="appendRowDenom(index, '{{ $d }}')"
                                                        class="px-1.5 py-0.5 rounded text-[10px] bg-blue-50 dark:bg-blue-950/40 border border-blue-200 dark:border-blue-800 hover:bg-blue-100 text-blue-800 dark:text-blue-300 font-medium cursor-pointer"
                                                    >
                                                        {{ $d }}
                                                    </button>
                                                @endforeach
                                            </div>
                                        </template>

                                        <!-- Jika Game Baru / Manual -->
                                        <template x-if="!item.name.toLowerCase().includes('free fire') && !item.name.toLowerCase().includes('mobile legends')">
                                            <div class="text-[10px] text-indigo-600 dark:text-indigo-400 italic">
                                                💡 Ketik nama game & nominal bebas (contoh: PUBG 60 UC, Roblox 100 Robux). Masuk kategori Top Up Game & potong Saldo Server.
                                            </div>
                                        </template>
                                    </div>
                                </template>

                                <input
                                    type="text"
                                    :name="`items[${index}][name]`"
                                    x-model="item.name"
                                    @input="onNameInput(index)"
                                    placeholder="Contoh: Pulsa Tri 10.000 / Top Up Game Free Fire 140 Diamond"
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
                                        @click="makeCodeUnique(index)"
                                        class="text-[10px] text-indigo-600 dark:text-indigo-400 font-bold hover:underline cursor-pointer flex items-center gap-1"
                                        title="Buat ulang kode SKU otomatis yang unik"
                                    >
                                        <span>⚡ Buat Otomatis</span>
                                    </button>
                                </div>
                                <input
                                    type="text"
                                    :name="`items[${index}][code]`"
                                    x-model="item.code"
                                    @input="item.manual_code = true"
                                    placeholder="SKU-10K"
                                    required
                                    :class="isCodeDuplicate(index) ? 'border-rose-500 ring-1 ring-rose-500 bg-rose-50/50 dark:bg-rose-950/20 text-rose-900 dark:text-rose-200' : 'border-slate-300 dark:border-slate-600'"
                                    class="w-full px-3 py-2 rounded-xl border bg-white dark:bg-slate-750 text-slate-900 dark:text-white text-xs font-mono font-bold focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 shadow-2xs"
                                >
                                <template x-if="isCodeDuplicate(index)">
                                    <div class="mt-1 flex items-center justify-between text-[10px] text-rose-500 font-bold">
                                        <span x-text="getDuplicateMsg(index)"></span>
                                        <button
                                            type="button"
                                            @click="makeCodeUnique(index)"
                                            class="text-indigo-600 dark:text-indigo-400 underline hover:text-indigo-800 ml-1 shrink-0 cursor-pointer"
                                        >
                                            ⚡ Buat Unik
                                        </button>
                                    </div>
                                </template>
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
                                <div class="flex items-center justify-between mb-1">
                                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300">
                                        Stok Awal <span class="text-rose-500">*</span>
                                    </label>
                                    <template x-if="isDigitalCategory(item.category_id)">
                                        <span class="text-[9px] font-black px-1.5 py-0.5 rounded bg-amber-100 text-amber-800 dark:bg-amber-950/70 dark:text-amber-300" title="Produk digital menggunakan saldo server">
                                            ⚡ Server
                                        </span>
                                    </template>
                                    <template x-if="item.category_id && !isDigitalCategory(item.category_id)">
                                        <span class="text-[9px] font-black px-1.5 py-0.5 rounded bg-emerald-100 text-emerald-800 dark:bg-emerald-950/70 dark:text-emerald-300" title="Produk fisik di rak/laci">
                                            📦 Fisik
                                        </span>
                                    </template>
                                </div>
                                <input
                                    type="number"
                                    :name="`items[${index}][stock]`"
                                    x-model="item.stock"
                                    min="0"
                                    required
                                    class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-750 text-slate-900 dark:text-white text-xs font-bold focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 shadow-2xs"
                                >
                                <p class="text-[10px] text-slate-400 mt-1" x-show="isDigitalCategory(item.category_id)">
                                    Isi 0 (Pakai Saldo Server)
                                </p>
                                <p class="text-[10px] text-slate-400 mt-1" x-show="item.category_id && !isDigitalCategory(item.category_id)">
                                    Jumlah fisik di rak
                                </p>
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
                            @change="onBatchCategoryChange()"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-750 text-slate-900 dark:text-white font-semibold focus:ring-2 focus:ring-indigo-500 shadow-2xs text-xs"
                        >
                            <option value="">-- Pilih Kategori (Misal: Pulsa Telkomsel, Top Up Game, Voucher Tri, E-Wallet DANA) --</option>
                            @if($pulsaCats->isNotEmpty())
                                <optgroup label="📱 Pulsa Reguler (All Operator)">
                                    @foreach($pulsaCats as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                    @endforeach
                                </optgroup>
                            @endif
                            @if($voucherCats->isNotEmpty())
                                <optgroup label="🎟️ Voucher Fisik & Kuota">
                                    @foreach($voucherCats as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                    @endforeach
                                </optgroup>
                            @endif
                            @if($paketCats->isNotEmpty())
                                <optgroup label="🌐 Paket Internet & Data">
                                    @foreach($paketCats as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                    @endforeach
                                </optgroup>
                            @endif
                            @if($perdanaCats->isNotEmpty())
                                <optgroup label="💳 Kartu Perdana Segel">
                                    @foreach($perdanaCats as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                    @endforeach
                                </optgroup>
                            @endif
                            @if($ewalletCats->isNotEmpty())
                                <optgroup label="👛 E-Wallet & Bank Digital">
                                    @foreach($ewalletCats as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                    @endforeach
                                </optgroup>
                            @endif
                            @if($tokenCats->isNotEmpty())
                                <optgroup label="⚡ Token Listrik PLN">
                                    @foreach($tokenCats as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                    @endforeach
                                </optgroup>
                            @endif
                            @if($plnPascaCats->isNotEmpty())
                                <optgroup label="⚡ Tagihan & Pajak Listrik PLN">
                                    @foreach($plnPascaCats as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                    @endforeach
                                </optgroup>
                            @endif
                            @if($bpjsCats->isNotEmpty())
                                <optgroup label="🏥 BPJS Kesehatan">
                                    @foreach($bpjsCats as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                    @endforeach
                                </optgroup>
                            @endif
                            @if($wifiCats->isNotEmpty())
                                <optgroup label="📡 Tagihan WiFi & Internet Rumah">
                                    @foreach($wifiCats as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                    @endforeach
                                </optgroup>
                            @endif
                            @if($gameCats->isNotEmpty())
                                <optgroup label="🎮 Top Up Game Online">
                                    @foreach($gameCats as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                    @endforeach
                                </optgroup>
                            @endif
                            @if($otherCats->isNotEmpty())
                                <optgroup label="📦 Aksesoris, Jasa & Lainnya">
                                    @foreach($otherCats as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                    @endforeach
                                </optgroup>
                            @endif
                        </select>
                        <template x-if="isDigitalCategory(batchGen.categoryId)">
                            <p class="text-[11px] text-amber-600 dark:text-amber-400 mt-1 font-semibold flex items-center gap-1">
                                <span>⚡</span> Kategori digital menggunakan Saldo Server induk. Stok awal otomatis diset 0.
                            </p>
                        </template>

                        <!-- Pilihan Game Khusus saat Kategori Top Up Game Terpilih -->
                        <template x-if="isGameCategory(batchGen.categoryId)">
                            <div class="mt-3 p-3 rounded-2xl bg-indigo-50/70 dark:bg-slate-750 border border-indigo-100 dark:border-slate-700">
                                <label class="block font-bold text-slate-800 dark:text-slate-200 mb-2">
                                    🎮 Pilih Game Default:
                                </label>
                                <div class="grid grid-cols-2 gap-2">
                                    <button
                                        type="button"
                                        @click="onBatchGameTypeChange('ff')"
                                        :class="batchGen.gameType === 'ff' ? 'bg-amber-500 text-white font-extrabold shadow-sm' : 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700'"
                                        class="px-3 py-2 rounded-xl text-xs font-bold transition-all flex items-center justify-center gap-1.5 cursor-pointer"
                                    >
                                        <span>🔥 Free Fire</span>
                                    </button>
                                    <button
                                        type="button"
                                        @click="onBatchGameTypeChange('ml')"
                                        :class="batchGen.gameType === 'ml' ? 'bg-blue-600 text-white font-extrabold shadow-sm' : 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700'"
                                        class="px-3 py-2 rounded-xl text-xs font-bold transition-all flex items-center justify-center gap-1.5 cursor-pointer"
                                    >
                                        <span>⚔️ Mobile Legends</span>
                                    </button>
                                </div>
                                <p class="text-[10px] text-slate-500 mt-1.5">
                                    Untuk game lainnya (seperti PUBG / Roblox), Anda dapat mengetiknya langsung secara manual di tabel baris produk.
                                </p>
                            </div>
                        </template>
                    </div>

                    <!-- 2. Centang Pilihan Nominal -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label class="block font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300">
                                <span x-show="!isGameCategory(batchGen.categoryId)">Pilih Nominal yang Ingin Dibuat:</span>
                                <span x-show="isGameCategory(batchGen.categoryId) && batchGen.gameType === 'ff'">Pilih Denominasi Diamond Free Fire:</span>
                                <span x-show="isGameCategory(batchGen.categoryId) && batchGen.gameType === 'ml'">Pilih Denominasi Diamond Mobile Legends:</span>
                            </label>
                            <span class="text-[10px] text-indigo-600 font-bold" x-text="batchGen.selectedDenoms.length + ' dipilih'"></span>
                        </div>

                        <!-- Checkbox Denominasi Khusus Game Free Fire -->
                        <div x-show="isGameCategory(batchGen.categoryId) && batchGen.gameType === 'ff'" class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                            @foreach(['50 Diamond', '70 Diamond', '140 Diamond', '355 Diamond', '720 Diamond', '1440 Diamond', 'Member Mingguan', 'Member Bulanan'] as $d)
                                <label class="flex items-center gap-2 p-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-750 cursor-pointer hover:border-amber-400 select-none">
                                    <input
                                        type="checkbox"
                                        value="{{ $d }}"
                                        x-model="batchGen.selectedDenoms"
                                        class="rounded text-amber-500 focus:ring-amber-500"
                                    >
                                    <span class="font-bold text-[11px]">{{ $d }}</span>
                                </label>
                            @endforeach
                        </div>

                        <!-- Checkbox Denominasi Khusus Game Mobile Legends -->
                        <div x-show="isGameCategory(batchGen.categoryId) && batchGen.gameType === 'ml'" class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                            @foreach(['86 Diamond', '172 Diamond', '257 Diamond', '344 Diamond', '706 Diamond', 'Weekly Diamond Pass'] as $d)
                                <label class="flex items-center gap-2 p-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-750 cursor-pointer hover:border-blue-400 select-none">
                                    <input
                                        type="checkbox"
                                        value="{{ $d }}"
                                        x-model="batchGen.selectedDenoms"
                                        class="rounded text-blue-600 focus:ring-blue-500"
                                    >
                                    <span class="font-bold text-[11px]">{{ $d }}</span>
                                </label>
                            @endforeach
                        </div>

                        <!-- Checkbox Denominasi Normal (Pulsa, Paket, Token, E-Wallet, Voucher, Fisik) -->
                        <div x-show="!isGameCategory(batchGen.categoryId)" class="grid grid-cols-2 sm:grid-cols-3 gap-2">
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
