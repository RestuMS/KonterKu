<x-layouts.app title="Pengaturan Konter & Master Data">
    <x-slot:header>
        Pengaturan Konter & Master Data
    </x-slot:header>

    @php
        $activeTab = request('tab', 'profil');
    @endphp

    <div class="space-y-6">
        <!-- ================================================================= -->
        <!-- 1. HEADER PENGATURAN                                              -->
        <!-- ================================================================= -->
        <div class="p-6 sm:p-7 rounded-3xl bg-gradient-to-r from-slate-50/90 via-white to-blue-50/50 dark:from-slate-850 dark:via-slate-800 dark:to-slate-850 border border-slate-200/90 dark:border-slate-700 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="w-13 h-13 rounded-2xl bg-gradient-to-tr from-slate-700 to-slate-900 text-white flex items-center justify-center shadow-lg shadow-slate-700/25 shrink-0">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight">
                        Pengaturan Konter & Master Data
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">
                        Kelola profil usaha, ambang batas stok minimum, dan master kategori produk, pemasukan, serta pengeluaran.
                    </p>
                </div>
            </div>
        </div>

        <!-- ================================================================= -->
        <!-- 2. TABS NAVIGASI MENU PENGATURAN                                  -->
        <!-- ================================================================= -->
        <div class="flex items-center gap-2 overflow-x-auto pb-1 scrollbar-thin text-xs font-bold border-b border-slate-200 dark:border-slate-800">
            <!-- 1. Profil Usaha & Stok -->
            <a
                href="{{ route('settings.index', ['tab' => 'profil']) }}"
                class="px-4 py-2.5 rounded-2xl flex items-center gap-2 transition-all shrink-0 {{ $activeTab === 'profil' ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/25' : 'bg-white dark:bg-slate-850 text-slate-600 dark:text-slate-300 hover:bg-slate-100 border border-slate-200 dark:border-slate-800' }}"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                <span>Profil Usaha & Batas Stok</span>
            </a>

            <!-- 2. Kategori Produk -->
            <a
                href="{{ route('settings.index', ['tab' => 'kategori_produk']) }}"
                class="px-4 py-2.5 rounded-2xl flex items-center gap-2 transition-all shrink-0 {{ $activeTab === 'kategori_produk' ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/25' : 'bg-white dark:bg-slate-850 text-slate-600 dark:text-slate-300 hover:bg-slate-100 border border-slate-200 dark:border-slate-800' }}"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                <span>Kategori Produk ({{ $productCategories->count() }})</span>
            </a>

            <!-- 3. Kategori Pemasukan -->
            <a
                href="{{ route('settings.index', ['tab' => 'kategori_pemasukan']) }}"
                class="px-4 py-2.5 rounded-2xl flex items-center gap-2 transition-all shrink-0 {{ $activeTab === 'kategori_pemasukan' ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/25' : 'bg-white dark:bg-slate-850 text-slate-600 dark:text-slate-300 hover:bg-slate-100 border border-slate-200 dark:border-slate-800' }}"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/></svg>
                <span>Kategori Pemasukan ({{ $incomeCategories->count() }})</span>
            </a>

            <!-- 4. Kategori Pengeluaran -->
            <a
                href="{{ route('settings.index', ['tab' => 'kategori_pengeluaran']) }}"
                class="px-4 py-2.5 rounded-2xl flex items-center gap-2 transition-all shrink-0 {{ $activeTab === 'kategori_pengeluaran' ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/25' : 'bg-white dark:bg-slate-850 text-slate-600 dark:text-slate-300 hover:bg-slate-100 border border-slate-200 dark:border-slate-800' }}"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"/></svg>
                <span>Kategori Pengeluaran ({{ $expenseCategories->count() }})</span>
            </a>

            <!-- 5. Keamanan & Akun -->
            <a
                href="{{ route('settings.index', ['tab' => 'keamanan']) }}"
                class="px-4 py-2.5 rounded-2xl flex items-center gap-2 transition-all shrink-0 {{ $activeTab === 'keamanan' ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/25' : 'bg-white dark:bg-slate-850 text-slate-600 dark:text-slate-300 hover:bg-slate-100 border border-slate-200 dark:border-slate-800' }}"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                <span>Profil Akun & Sandi</span>
            </a>
        </div>

        <!-- ================================================================= -->
        <!-- 3. KONTEN MASING-MASING TAB PENGATURAN                             -->
        <!-- ================================================================= -->

        <!-- TAB 1: PROFIL USAHA & BATAS MINIMUM STOK -->
        @if($activeTab === 'profil')
            <div class="max-w-4xl space-y-6">
                <div class="p-6 sm:p-7 rounded-3xl bg-white dark:bg-slate-850 border border-slate-200/90 dark:border-slate-800 shadow-xs space-y-6">
                    <div class="border-b border-slate-100 dark:border-slate-800 pb-4">
                        <h3 class="text-sm font-black uppercase tracking-wider text-slate-900 dark:text-white">
                            Identitas Konter & Pengaturan Toko
                        </h3>
                        <p class="text-xs text-slate-500 mt-0.5">Informasi ini akan tercetak otomatis pada struk belanja dan kop laporan resmi.</p>
                    </div>

                    <form method="POST" action="{{ route('settings.update') }}" class="space-y-5">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Nama Konter -->
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">
                                    Nama Konter / Toko <span class="text-rose-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    name="shop_name"
                                    value="{{ old('shop_name', $settings['shop_name']) }}"
                                    required
                                    placeholder="Contoh: KonterKu Cell"
                                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-xs font-medium focus:ring-2 focus:ring-indigo-500 shadow-2xs"
                                >
                            </div>

                            <!-- No Telepon / WA -->
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">
                                    No. Telepon / WhatsApp
                                </label>
                                <input
                                    type="text"
                                    name="shop_phone"
                                    value="{{ old('shop_phone', $settings['shop_phone']) }}"
                                    placeholder="Contoh: 081234567890"
                                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-xs font-medium focus:ring-2 focus:ring-indigo-500 shadow-2xs"
                                >
                            </div>
                        </div>

                        <!-- Batas Minimum Stok -->
                        <div class="p-4 rounded-2xl bg-amber-50/70 dark:bg-amber-950/30 border border-amber-200/80 dark:border-amber-800/60 space-y-2">
                            <label class="block text-xs font-black uppercase tracking-wider text-amber-900 dark:text-amber-300">
                                Batas Minimum Notifikasi Stok Menipis (Default Threshold) <span class="text-rose-500">*</span>
                            </label>
                            <div class="flex items-center gap-3">
                                <input
                                    type="number"
                                    name="low_stock_threshold"
                                    min="1"
                                    value="{{ old('low_stock_threshold', $settings['low_stock_threshold']) }}"
                                    required
                                    class="w-32 px-3 py-2 rounded-xl border border-amber-300 dark:border-amber-700 bg-white dark:bg-slate-800 font-mono font-bold text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500 shadow-2xs"
                                >
                                <span class="text-xs text-amber-800 dark:text-amber-400 font-medium">Unit / Pcs (Peringatan warna kuning/merah akan muncul saat stok mencapai batas ini).</span>
                            </div>
                        </div>

                        <!-- Alamat Konter -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">
                                Alamat Fisik Konter
                            </label>
                            <textarea
                                name="shop_address"
                                rows="2"
                                placeholder="Contoh: Jl. Diponegoro No. 88, KonterKu Seluler"
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-xs font-medium focus:ring-2 focus:ring-indigo-500 shadow-2xs"
                            >{{ old('shop_address', $settings['shop_address']) }}</textarea>
                        </div>

                        <!-- Footer Struk -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">
                                Pesan Footer Struk Belanja Kasir
                            </label>
                            <input
                                type="text"
                                name="receipt_footer"
                                value="{{ old('receipt_footer', $settings['receipt_footer']) }}"
                                placeholder="Contoh: Terima kasih telah berbelanja! Simpan struk ini sebagai bukti pembayaran."
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-xs font-medium focus:ring-2 focus:ring-indigo-500 shadow-2xs"
                            >
                        </div>

                        <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex justify-end">
                            <button
                                type="submit"
                                class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs uppercase tracking-wider transition-colors shadow-md shadow-indigo-600/25 cursor-pointer"
                            >
                                Simpan Pengaturan Usaha
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif

        <!-- TAB 2, 3, 4: KELOLA MASTER KATEGORI (PRODUK / PEMASUKAN / PENGELUARAN) -->
        @if(in_array($activeTab, ['kategori_produk', 'kategori_pemasukan', 'kategori_pengeluaran']))
            @php
                $targetType = match($activeTab) {
                    'kategori_produk' => 'product',
                    'kategori_pemasukan' => 'income',
                    'kategori_pengeluaran' => 'expense',
                };
                $categoryList = match($activeTab) {
                    'kategori_produk' => $productCategories,
                    'kategori_pemasukan' => $incomeCategories,
                    'kategori_pengeluaran' => $expenseCategories,
                };
                $tabTitle = match($activeTab) {
                    'kategori_produk' => 'Kategori Produk',
                    'kategori_pemasukan' => 'Kategori Pemasukan',
                    'kategori_pengeluaran' => 'Kategori Pengeluaran',
                };
                $tabDescription = match($activeTab) {
                    'kategori_produk' => 'Kelola klasifikasi produk fisik (Pulsa, Paket Data, Voucher, Perdana, Aksesoris, dll).',
                    'kategori_pemasukan' => 'Kelola kategori arus kas masuk kasir (Penjualan, Transfer Uang, Tarik Tunai, dll).',
                    'kategori_pengeluaran' => 'Kelola pos beban pengeluaran kas (Jajan, Beli Kuota, Beli Mainan, Beli Bensin, Operasional).',
                };
            @endphp

            <div class="space-y-6">
                <!-- Header Card & Tombol Tambah Kategori -->
                <div class="p-6 rounded-3xl bg-white dark:bg-slate-850 border border-slate-200/90 dark:border-slate-800 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h3 class="text-sm font-black uppercase tracking-wider text-slate-900 dark:text-white flex items-center gap-2">
                            <span>Master {{ $tabTitle }}</span>
                            <span class="px-2 py-0.5 rounded-full bg-indigo-100 text-indigo-800 text-[10px] font-mono font-bold">{{ $categoryList->count() }} Data</span>
                        </h3>
                        <p class="text-xs text-slate-500 mt-0.5">{{ $tabDescription }}</p>
                    </div>

                    <button
                        type="button"
                        @click="$dispatch('open-modal', 'add-category-modal')"
                        class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs uppercase tracking-wider transition-all shadow-md shadow-indigo-600/25 cursor-pointer flex items-center gap-1.5 self-start sm:self-auto"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 4v16m8-8H4"/></svg>
                        <span>Tambah {{ $tabTitle }}</span>
                    </button>
                </div>

                <!-- Tabel Daftar Kategori -->
                <div class="rounded-3xl bg-white dark:bg-slate-850 border border-slate-200/90 dark:border-slate-800 shadow-xs overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200/80 dark:divide-slate-800 text-left text-xs">
                            <thead class="bg-slate-50/90 dark:bg-slate-800/90 text-slate-500 uppercase font-black tracking-wider text-[11px]">
                                <tr>
                                    <th class="px-6 py-4">Nama Kategori</th>
                                    <th class="px-5 py-4">Slug / Kode URL</th>
                                    <th class="px-5 py-4">Keterangan</th>
                                    <th class="px-4 py-4 text-center">Data Terkait</th>
                                    <th class="px-6 py-4 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80 font-medium">
                                @forelse($categoryList as $cat)
                                    @php
                                        $relatedCount = $activeTab === 'kategori_produk' ? ($cat->products_count ?? 0) : ($cat->transactions_count ?? 0);
                                        $relatedLabel = $activeTab === 'kategori_produk' ? 'Produk' : 'Transaksi';
                                    @endphp
                                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors">
                                        <!-- Nama -->
                                        <td class="px-6 py-4">
                                            <span class="font-bold text-slate-900 dark:text-white text-xs">{{ $cat->name }}</span>
                                        </td>

                                        <!-- Slug -->
                                        <td class="px-5 py-4">
                                            <span class="font-mono text-[11px] text-slate-500 bg-slate-100 dark:bg-slate-800 px-2 py-0.5 rounded-md border border-slate-200 dark:border-slate-700">
                                                {{ $cat->slug }}
                                            </span>
                                        </td>

                                        <!-- Deskripsi -->
                                        <td class="px-5 py-4 text-slate-500 max-w-xs truncate">
                                            {{ $cat->description ?: '-' }}
                                        </td>

                                        <!-- Data Terkait -->
                                        <td class="px-4 py-4 text-center">
                                            <span class="px-2.5 py-0.5 rounded-full font-mono text-[11px] font-bold {{ $relatedCount > 0 ? 'bg-indigo-50 text-indigo-700 border border-indigo-200' : 'bg-slate-100 text-slate-400' }}">
                                                {{ $relatedCount }} {{ $relatedLabel }}
                                            </span>
                                        </td>

                                        <!-- Aksi -->
                                        <td class="px-6 py-4 text-center">
                                            <div class="inline-flex items-center gap-1.5">
                                                <!-- Tombol Edit Modal -->
                                                <button
                                                    type="button"
                                                    @click="$dispatch('open-modal', 'edit-category-{{ $cat->id }}')"
                                                    class="p-1.5 rounded-xl text-slate-400 hover:text-amber-600 hover:bg-amber-50 transition-colors cursor-pointer"
                                                    title="Edit Kategori"
                                                >
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                </button>

                                                <!-- Tombol Hapus Modal -->
                                                <button
                                                    type="button"
                                                    @click="$dispatch('open-modal', 'delete-category-{{ $cat->id }}')"
                                                    class="p-1.5 rounded-xl text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition-colors cursor-pointer"
                                                    title="Hapus Kategori"
                                                >
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                            </div>

                                            <!-- Modal Edit Kategori -->
                                            <x-modal name="edit-category-{{ $cat->id }}" title="Edit {{ $tabTitle }}">
                                                <form method="POST" action="{{ route('categories.update', $cat) }}" class="space-y-4 text-left">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="type" value="{{ $cat->type }}">

                                                    <div>
                                                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">
                                                            Nama Kategori <span class="text-rose-500">*</span>
                                                        </label>
                                                        <input
                                                            type="text"
                                                            name="name"
                                                            value="{{ $cat->name }}"
                                                            required
                                                            class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-600 text-xs font-medium"
                                                        >
                                                    </div>

                                                    <div>
                                                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">
                                                            Keterangan / Deskripsi
                                                        </label>
                                                        <input
                                                            type="text"
                                                            name="description"
                                                            value="{{ $cat->description }}"
                                                            placeholder="Keterangan singkat..."
                                                            class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-600 text-xs font-medium"
                                                        >
                                                    </div>

                                                    <div class="pt-3 border-t border-slate-100 flex justify-end gap-2">
                                                        <x-button type="button" variant="secondary" size="sm" @click="$dispatch('close-modal', 'edit-category-{{ $cat->id }}')">
                                                            Batal
                                                        </x-button>
                                                        <x-button type="submit" variant="primary" size="sm">
                                                            Simpan Perubahan
                                                        </x-button>
                                                    </div>
                                                </form>
                                            </x-modal>

                                            <!-- Modal Hapus Kategori -->
                                            <x-modal name="delete-category-{{ $cat->id }}" title="Hapus {{ $tabTitle }}">
                                                <div class="text-left text-xs text-slate-600 space-y-3">
                                                    <p>Apakah Anda yakin ingin menghapus kategori <strong>{{ $cat->name }}</strong>?</p>
                                                    @if($relatedCount > 0)
                                                        <div class="p-3 bg-amber-50 border border-amber-200 rounded-xl text-amber-800">
                                                            ⚠️ <strong>Peringatan:</strong> Kategori ini masih memiliki {{ $relatedCount }} {{ $relatedLabel }} terkait dan sistem akan menolak penghapusan demi integritas data.
                                                        </div>
                                                    @endif
                                                </div>

                                                <x-slot:footer>
                                                    <x-button type="button" variant="secondary" size="sm" @click="$dispatch('close-modal', 'delete-category-{{ $cat->id }}')">
                                                        Batal
                                                    </x-button>
                                                    <form method="POST" action="{{ route('categories.destroy', $cat) }}">
                                                        @csrf
                                                        @method('DELETE')
                                                        <x-button type="submit" variant="danger" size="sm">
                                                            Ya, Hapus Kategori
                                                        </x-button>
                                                    </form>
                                                </x-slot:footer>
                                            </x-modal>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-12 text-center text-slate-400">
                                            Belum ada data kategori untuk jenis ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Modal Tambah Kategori Baru -->
                <x-modal name="add-category-modal" title="Tambah {{ $tabTitle }} Baru">
                    <form method="POST" action="{{ route('categories.store') }}" class="space-y-4 text-left">
                        @csrf
                        <input type="hidden" name="type" value="{{ $targetType }}">

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">
                                Nama Kategori <span class="text-rose-500">*</span>
                            </label>
                            <input
                                type="text"
                                name="name"
                                required
                                placeholder="Contoh: {{ $activeTab === 'kategori_pengeluaran' ? 'Gaji Karyawan, Listrik, Bensin' : ($activeTab === 'kategori_pemasukan' ? 'Top-up E-Wallet, Pulsa' : 'Kartu Perdana XL, Aksesoris') }}"
                                class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-600 text-xs font-medium"
                            >
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">
                                Keterangan / Deskripsi
                            </label>
                            <input
                                type="text"
                                name="description"
                                placeholder="Keterangan singkat..."
                                class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-600 text-xs font-medium"
                            >
                        </div>

                        <div class="pt-3 border-t border-slate-100 flex justify-end gap-2">
                            <x-button type="button" variant="secondary" size="sm" @click="$dispatch('close-modal', 'add-category-modal')">
                                Batal
                            </x-button>
                            <x-button type="submit" variant="primary" size="sm">
                                Simpan Kategori
                            </x-button>
                        </div>
                    </form>
                </x-modal>
            </div>
        @endif

        <!-- TAB 5: AKUN PENGGUNA & KATA SANDI -->
        @if($activeTab === 'keamanan')
            <div class="max-w-4xl space-y-6">
                <!-- Profil Akun Pengguna -->
                <div class="p-6 sm:p-7 rounded-3xl bg-white dark:bg-slate-850 border border-slate-200/90 dark:border-slate-800 shadow-xs space-y-5">
                    <div class="border-b border-slate-100 dark:border-slate-800 pb-3">
                        <h3 class="text-sm font-black uppercase tracking-wider text-slate-900 dark:text-white">
                            Profil Pengguna Aktif
                        </h3>
                    </div>

                    <form method="POST" action="{{ route('settings.profile') }}" class="space-y-4">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Nama Lengkap</label>
                                <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="w-full px-3 py-2 rounded-xl border text-xs">
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Alamat Email</label>
                                <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full px-3 py-2 rounded-xl border text-xs">
                            </div>
                        </div>

                        <div class="pt-3 flex justify-end">
                            <x-button type="submit" variant="primary" size="sm">
                                Perbarui Profil
                            </x-button>
                        </div>
                    </form>
                </div>

                <!-- Ganti Sandi -->
                <div class="p-6 sm:p-7 rounded-3xl bg-white dark:bg-slate-850 border border-slate-200/90 dark:border-slate-800 shadow-xs space-y-5">
                    <div class="border-b border-slate-100 dark:border-slate-800 pb-3">
                        <h3 class="text-sm font-black uppercase tracking-wider text-slate-900 dark:text-white">
                            Ganti Kata Sandi
                        </h3>
                    </div>

                    <form method="POST" action="{{ route('settings.password') }}" class="space-y-4">
                        @csrf
                        @method('PUT')

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Kata Sandi Saat Ini</label>
                            <input type="password" name="current_password" required class="w-full px-3 py-2 rounded-xl border text-xs">
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Kata Sandi Baru</label>
                                <input type="password" name="password" required class="w-full px-3 py-2 rounded-xl border text-xs">
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Konfirmasi Sandi Baru</label>
                                <input type="password" name="password_confirmation" required class="w-full px-3 py-2 rounded-xl border text-xs">
                            </div>
                        </div>

                        <div class="pt-3 flex justify-end">
                            <x-button type="submit" variant="danger" size="sm">
                                Ganti Kata Sandi
                            </x-button>
                        </div>
                    </form>
                </div>
            </div>
        @endif
    </div>
</x-layouts.app>
