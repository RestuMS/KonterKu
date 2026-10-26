<x-layouts.app title="Pengaturan Konter">
    <x-slot:header>
        Pengaturan Konter & Aplikasi
    </x-slot:header>

    <div class="max-w-2xl mx-auto">
        <div class="mb-6">
            <h2 class="text-xl font-bold text-slate-900 dark:text-white">Pengaturan Umum</h2>
            <p class="text-xs text-slate-500 mt-0.5">Sesuaikan identitas konter dan batas minimum notifikasi stok menipis.</p>
        </div>

        <x-card>
            <form method="POST" action="{{ route('settings.update') }}" class="space-y-5">
                @csrf
                @method('PUT')

                <x-input
                    label="Nama Konter / Toko"
                    name="shop_name"
                    value="{{ old('shop_name', $settings['shop_name']) }}"
                    placeholder="Contoh: KonterKu Pulsa & Cell"
                    required
                    helper="Akan tampil pada struk dan header laporan"
                />

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <x-input
                        label="No. Telepon / WhatsApp"
                        name="shop_phone"
                        value="{{ old('shop_phone', $settings['shop_phone']) }}"
                        placeholder="Contoh: 081234567890"
                    />

                    <x-input
                        label="Ambang Batas Peringatan Stok Menipis"
                        name="low_stock_threshold"
                        type="number"
                        min="1"
                        value="{{ old('low_stock_threshold', $settings['low_stock_threshold']) }}"
                        required
                        helper="Batas default stok untuk indikator restock"
                    />
                </div>

                <x-textarea
                    label="Alamat Toko"
                    name="shop_address"
                    value="{{ old('shop_address', $settings['shop_address']) }}"
                    placeholder="Contoh: Jl. Raya Utama No. 12, Konter Cell"
                />

                <x-textarea
                    label="Pesan Footer Struk Pembelian"
                    name="receipt_footer"
                    value="{{ old('receipt_footer', $settings['receipt_footer']) }}"
                    placeholder="Contoh: Terima kasih atas kunjungan Anda! Garansi voucher 24 jam."
                />

                <div class="pt-4 border-t border-slate-100 dark:border-slate-700/60 flex items-center justify-end">
                    <x-button type="submit" variant="primary" class="font-semibold shadow-xs">
                        Simpan Pengaturan
                    </x-button>
                </div>
            </form>
        </x-card>
    </div>
</x-layouts.app>
