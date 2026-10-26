<x-layouts.app title="Pengaturan Konter & Akun">
    <x-slot:header>
        Pengaturan Konter & Akun
    </x-slot:header>

    <div class="max-w-3xl mx-auto space-y-8">
        <!-- 1. Identitas Konter & Toko -->
        <div>
            <div class="mb-4">
                <h2 class="text-xl font-bold text-slate-900 dark:text-white">Identitas Toko / Konter</h2>
                <p class="text-xs text-slate-500 mt-0.5">Sesuaikan nama usaha, kontak, dan ambang batas peringatan stok menipis.</p>
            </div>

            <x-card>
                <form method="POST" action="{{ route('settings.update') }}" class="space-y-5">
                    @csrf
                    @method('PUT')

                    <x-input
                        label="Nama Konter / Toko"
                        name="shop_name"
                        value="{{ old('shop_name', $settings['shop_name']) }}"
                        placeholder="Contoh: KonterKu Cell"
                        required
                        helper="Nama ini akan tampil di navbar, struk transaksi, dan kop laporan keuangan"
                    />

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <x-input
                            label="Nomor Telepon / WhatsApp"
                            name="shop_phone"
                            value="{{ old('shop_phone', $settings['shop_phone']) }}"
                            placeholder="Contoh: 081234567890"
                        />

                        <x-input
                            label="Batas Minimum Notifikasi Stok Menipis"
                            name="low_stock_threshold"
                            type="number"
                            min="1"
                            value="{{ old('low_stock_threshold', $settings['low_stock_threshold']) }}"
                            required
                            helper="Batas peringatan untuk produk yang perlu segera di-restock"
                        />
                    </div>

                    <x-textarea
                        label="Alamat Fisik Konter"
                        name="shop_address"
                        value="{{ old('shop_address', $settings['shop_address']) }}"
                        placeholder="Contoh: Jl. Diponegoro No. 88, KonterKu Seluler"
                    />

                    <x-textarea
                        label="Catatan / Pesan Footer Struk Belanja"
                        name="receipt_footer"
                        value="{{ old('receipt_footer', $settings['receipt_footer']) }}"
                        placeholder="Contoh: Terima kasih telah berbelanja! Simpan struk ini sebagai bukti pembayaran."
                    />

                    <div class="pt-4 border-t border-slate-100 dark:border-slate-700/60 flex items-center justify-end">
                        <x-button type="submit" variant="primary" class="font-semibold shadow-xs">
                            Simpan Pengaturan Toko
                        </x-button>
                    </div>
                </form>
            </x-card>
        </div>

        <!-- 2. Pengelolaan Akun Pengguna -->
        <div>
            <div class="mb-4">
                <h2 class="text-xl font-bold text-slate-900 dark:text-white">Profil Akun Pengguna</h2>
                <p class="text-xs text-slate-500 mt-0.5">Kelola identitas akun Anda yang sedang aktif.</p>
            </div>

            <x-card>
                <form method="POST" action="{{ route('settings.profile') }}" class="space-y-5">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <x-input
                            label="Nama Lengkap"
                            name="name"
                            value="{{ old('name', $user->name) }}"
                            required
                        />

                        <x-input
                            label="Alamat Email"
                            name="email"
                            type="email"
                            value="{{ old('email', $user->email) }}"
                            required
                        />
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <x-input
                            label="Nomor HP Kasir / Admin"
                            name="phone"
                            value="{{ old('phone', $user->phone) }}"
                            placeholder="Contoh: 081298765432"
                        />

                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                                Hak Akses (Role)
                            </label>
                            <div class="p-2.5 bg-slate-100 dark:bg-slate-750 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-600 dark:text-slate-300 text-sm font-semibold capitalize flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                {{ $user->role ?? 'admin' }}
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-100 dark:border-slate-700/60 flex items-center justify-end">
                        <x-button type="submit" variant="primary" class="font-semibold shadow-xs">
                            Perbarui Profil Akun
                        </x-button>
                    </div>
                </form>
            </x-card>
        </div>

        <!-- 3. Pengamanan Sandi (Password) -->
        <div>
            <div class="mb-4">
                <h2 class="text-xl font-bold text-slate-900 dark:text-white">Ganti Kata Sandi</h2>
                <p class="text-xs text-slate-500 mt-0.5">Pastikan akun menggunakan kata sandi yang kuat untuk menjaga keamanan kasir.</p>
            </div>

            <x-card>
                <form method="POST" action="{{ route('settings.password') }}" class="space-y-5">
                    @csrf
                    @method('PUT')

                    <x-input
                        label="Kata Sandi Saat Ini"
                        name="current_password"
                        type="password"
                        placeholder="••••••••"
                        required
                    />

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <x-input
                            label="Kata Sandi Baru"
                            name="password"
                            type="password"
                            placeholder="Minimal 6 karakter"
                            required
                        />

                        <x-input
                            label="Ulangi Kata Sandi Baru"
                            name="password_confirmation"
                            type="password"
                            placeholder="Konfirmasi kata sandi"
                            required
                        />
                    </div>

                    <div class="pt-4 border-t border-slate-100 dark:border-slate-700/60 flex items-center justify-end">
                        <x-button type="submit" variant="primary" class="font-semibold shadow-xs">
                            Ganti Kata Sandi
                        </x-button>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</x-layouts.app>
