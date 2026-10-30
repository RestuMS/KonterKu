<div
    x-data="{
        topupModal: false,
        adjustModal: false,
        settingsModal: false,
        topupAmount: 500000,
        newBalance: {{ (float) ($serverDeposit->balance ?? 0) }},
        setTopup(val) {
            this.topupAmount = val;
        }
    }"
    class="mb-8"
>
    <!-- KARTU INDUK SALDO APLIKASI SERVER (ALL PROVIDER) -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-slate-900 via-indigo-950 to-slate-900 text-white p-6 sm:p-7 border border-indigo-900/50 shadow-xl shadow-indigo-950/20">
        <!-- Ambient Glowing Background Accents -->
        <div class="absolute -top-12 -right-12 w-64 h-64 bg-indigo-500/15 rounded-full blur-3xl pointer-events-none animate-pulse-glow"></div>
        <div class="absolute -bottom-12 -left-12 w-64 h-64 bg-sky-500/10 rounded-full blur-3xl pointer-events-none animate-pulse-glow" style="animation-delay: 2s;"></div>

        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <!-- Informasi Saldo Utama -->
            <div class="space-y-3.5 max-w-2xl">
                <!-- Header Badge & Kategori All Provider -->
                <div class="flex items-center gap-2.5 flex-wrap">
                    <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-indigo-500 to-sky-400 text-white flex items-center justify-center shadow-md shadow-indigo-500/30 shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <h3 class="text-base sm:text-lg font-black tracking-tight text-white">
                                {{ $serverDeposit->name ?? 'Saldo Aplikasi Server All Provider' }}
                            </h3>
                            @if($serverDeposit->isLow())
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-rose-500/20 text-rose-300 border border-rose-500/40">
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-400 animate-ping"></span>
                                    Saldo Kritis (Segera Top-Up)
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/40">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                                    Saldo Aman
                                </span>
                            @endif
                        </div>
                        <p class="text-xs text-slate-300/90 mt-0.5">
                            Satu saldo terpadu untuk: <span class="text-indigo-200 font-semibold">Pulsa, Paket Data, E-Wallet, Token PLN, Tagihan Pajak/BPJS, Top-up Game & Voucher Fisik</span>.
                        </p>
                    </div>
                </div>

                <!-- Display Angka Saldo Besar -->
                <div class="pt-1">
                    <span class="text-[11px] uppercase font-bold tracking-wider text-slate-300 block">
                        Sisa Saldo Deposit Saat Ini
                    </span>
                    <div class="flex items-baseline gap-3 mt-1 flex-wrap">
                        <span class="text-3xl sm:text-4xl font-black font-mono tracking-tight text-white drop-shadow-sm">
                            @rupiah($serverDeposit->balance)
                        </span>
                        @if($serverDeposit->account_id)
                            <span class="px-2.5 py-1 rounded-xl bg-white/10 text-slate-300 font-mono text-xs border border-white/15">
                                ID/No: {{ $serverDeposit->account_id }}
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Indikator Otomatisasi & Terakhir Diperbarui -->
                <div class="flex items-center gap-3 flex-wrap text-xs text-slate-300/80 pt-1">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl bg-white/10 border border-white/15 text-indigo-200 text-[11px] font-bold">
                        <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        <span>Otomatis Berkurang Setiap Transaksi Kasir Dicatat</span>
                    </span>
                    <span>•</span>
                    <span class="text-[11px] text-slate-400">
                        Batas minimum: <strong class="text-slate-200">@rupiah($serverDeposit->min_alert_balance)</strong>
                    </span>
                    <span>•</span>
                    <span class="text-[11px] text-slate-400">
                        {{ $serverDeposit->last_checked_at ? 'Terakhir dicek ' . $serverDeposit->last_checked_at->diffForHumans() : 'Aktif' }}
                    </span>
                </div>
            </div>

            <!-- Tombol Aksi Saldo (Top-Up, Koreksi, Riwayat, Pengaturan) -->
            <div class="grid grid-cols-2 lg:flex lg:flex-col sm:grid-cols-4 gap-2.5 shrink-0 justify-start lg:justify-center">
                <!-- Tombol 1: Top-Up / Tambah Saldo -->
                <button
                    type="button"
                    @click="$dispatch('open-modal', 'topup-deposit-modal')"
                    class="px-3.5 py-2.5 rounded-2xl bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-400 hover:to-teal-500 text-white font-bold text-xs shadow-md shadow-emerald-600/30 flex items-center justify-center gap-1.5 active:scale-95 transition-all cursor-pointer"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>+ Isi / Top-Up</span>
                </button>

                <!-- Tombol 2: Koreksi / Set Saldo Baru -->
                <button
                    type="button"
                    @click="$dispatch('open-modal', 'adjust-deposit-modal')"
                    class="px-3.5 py-2.5 rounded-2xl bg-white/10 hover:bg-white/20 text-white font-semibold text-xs border border-white/20 flex items-center justify-center gap-1.5 active:scale-95 transition-all cursor-pointer backdrop-blur-md"
                >
                    <svg class="w-4 h-4 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    <span>Koreksi Saldo</span>
                </button>

                <!-- Tombol 3: Riwayat Mutasi Saldo (Halaman Khusus) -->
                <a
                    href="{{ route('server-deposit.mutations') }}"
                    class="px-3.5 py-2.5 rounded-2xl bg-indigo-500/30 hover:bg-indigo-500/50 text-indigo-100 hover:text-white font-bold text-xs border border-indigo-400/40 flex items-center justify-center gap-1.5 active:scale-95 transition-all cursor-pointer backdrop-blur-md"
                    title="Buka Halaman Riwayat & Log Mutasi Saldo Server"
                >
                    <svg class="w-4 h-4 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Riwayat Saldo &rarr;</span>
                </a>

                <!-- Tombol 4: Pengaturan Server -->
                <button
                    type="button"
                    @click="$dispatch('open-modal', 'settings-deposit-modal')"
                    class="px-3 py-2.5 rounded-2xl bg-white/5 hover:bg-white/10 text-slate-300 hover:text-white font-medium text-xs flex items-center justify-center gap-1.5 transition-all cursor-pointer"
                >
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span>Pengaturan</span>
                </button>
            </div>
        </div>
    </div>

    <!-- ===================================================================== -->
    <!-- MODAL 1: TOP-UP / TAMBAH SALDO DEPOSIT                                -->
    <!-- ===================================================================== -->
    <x-modal name="topup-deposit-modal" title="Top-Up / Tambah Saldo Deposit Server" maxWidth="md">
        <form method="POST" action="{{ route('server-deposit.topup') }}" class="space-y-4">
            @csrf

            <p class="text-xs text-slate-500 dark:text-slate-400">
                Masukkan nominal isi ulang deposit yang baru saja Anda transfer ke aplikasi server agen.
            </p>

            <!-- Pilihan Cepat Nominal Top-Up -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">
                    Pilihan Cepat Nominal:
                </label>
                <div class="grid grid-cols-3 gap-2">
                    <button
                        type="button"
                        @click="setTopup(100000)"
                        class="px-2.5 py-1.5 rounded-xl text-xs font-bold font-mono border border-slate-200 dark:border-slate-700 hover:bg-indigo-50 dark:hover:bg-indigo-950/40 hover:text-indigo-600 transition-colors cursor-pointer"
                    >
                        + 100.000
                    </button>
                    <button
                        type="button"
                        @click="setTopup(200000)"
                        class="px-2.5 py-1.5 rounded-xl text-xs font-bold font-mono border border-slate-200 dark:border-slate-700 hover:bg-indigo-50 dark:hover:bg-indigo-950/40 hover:text-indigo-600 transition-colors cursor-pointer"
                    >
                        + 200.000
                    </button>
                    <button
                        type="button"
                        @click="setTopup(500000)"
                        class="px-2.5 py-1.5 rounded-xl text-xs font-bold font-mono border border-slate-200 dark:border-slate-700 hover:bg-indigo-50 dark:hover:bg-indigo-950/40 hover:text-indigo-600 transition-colors cursor-pointer"
                    >
                        + 500.000
                    </button>
                    <button
                        type="button"
                        @click="setTopup(1000000)"
                        class="px-2.5 py-1.5 rounded-xl text-xs font-bold font-mono border border-slate-200 dark:border-slate-700 hover:bg-indigo-50 dark:hover:bg-indigo-950/40 hover:text-indigo-600 transition-colors cursor-pointer"
                    >
                        + 1.000.000
                    </button>
                    <button
                        type="button"
                        @click="setTopup(2000000)"
                        class="px-2.5 py-1.5 rounded-xl text-xs font-bold font-mono border border-slate-200 dark:border-slate-700 hover:bg-indigo-50 dark:hover:bg-indigo-950/40 hover:text-indigo-600 transition-colors cursor-pointer"
                    >
                        + 2.000.000
                    </button>
                    <button
                        type="button"
                        @click="setTopup(5000000)"
                        class="px-2.5 py-1.5 rounded-xl text-xs font-bold font-mono border border-slate-200 dark:border-slate-700 hover:bg-indigo-50 dark:hover:bg-indigo-950/40 hover:text-indigo-600 transition-colors cursor-pointer"
                    >
                        + 5.000.000
                    </button>
                </div>
            </div>

            <!-- Input Nominal Top Up -->
            <div>
                <label for="amount" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">
                    Nominal Tambah Saldo (Rp) <span class="text-rose-500">*</span>
                </label>
                <div class="relative rounded-2xl shadow-2xs">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 font-bold text-sm">
                        Rp
                    </span>
                    <input
                        type="number"
                        name="amount"
                        id="amount"
                        x-model="topupAmount"
                        required
                        min="1000"
                        step="1000"
                        class="w-full pl-10 pr-3.5 py-2.5 rounded-2xl border border-slate-300 dark:border-slate-600 text-sm font-black font-mono dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20"
                    >
                </div>
            </div>

            <div class="pt-3 border-t border-slate-100 dark:border-slate-700 flex justify-end gap-2">
                <button
                    type="button"
                    @click="$dispatch('close-modal', 'topup-deposit-modal')"
                    class="px-3.5 py-2 rounded-xl text-xs font-bold bg-slate-100 hover:bg-slate-200 text-slate-700 dark:bg-slate-800 dark:text-slate-300 cursor-pointer"
                >
                    Batal
                </button>
                <button
                    type="submit"
                    class="px-4 py-2 rounded-xl text-xs font-bold bg-emerald-600 hover:bg-emerald-700 text-white shadow-xs cursor-pointer flex items-center gap-1.5"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 4v16m8-8H4"/></svg>
                    <span>Tambahkan ke Saldo</span>
                </button>
            </div>
        </form>
    </x-modal>

    <!-- ===================================================================== -->
    <!-- MODAL 2: KOREKSI / ATUR LANGSUNG SISA SALDO                           -->
    <!-- ===================================================================== -->
    <x-modal name="adjust-deposit-modal" title="Koreksi / Set Sisa Saldo Server" maxWidth="md">
        <form method="POST" action="{{ route('server-deposit.adjust') }}" class="space-y-4">
            @csrf

            <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                Gunakan menu ini jika ada selisih saldo di aplikasi agen Anda dan Anda ingin menyesuaikan angka di sistem agar sama persis dengan aplikasi aslinya.
            </p>

            <div>
                <label for="new_balance" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">
                    Sisa Saldo Terkini di Aplikasi Server (Rp) <span class="text-rose-500">*</span>
                </label>
                <div class="relative rounded-2xl shadow-2xs">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 font-bold text-sm">
                        Rp
                    </span>
                    <input
                        type="number"
                        name="new_balance"
                        id="new_balance"
                        x-model="newBalance"
                        required
                        min="0"
                        step="100"
                        class="w-full pl-10 pr-3.5 py-2.5 rounded-2xl border border-slate-300 dark:border-slate-600 text-sm font-black font-mono dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20"
                    >
                </div>
            </div>

            <div class="pt-3 border-t border-slate-100 dark:border-slate-700 flex justify-end gap-2">
                <button
                    type="button"
                    @click="$dispatch('close-modal', 'adjust-deposit-modal')"
                    class="px-3.5 py-2 rounded-xl text-xs font-bold bg-slate-100 hover:bg-slate-200 text-slate-700 dark:bg-slate-800 dark:text-slate-300 cursor-pointer"
                >
                    Batal
                </button>
                <button
                    type="submit"
                    class="px-4 py-2 rounded-xl text-xs font-bold bg-indigo-600 hover:bg-indigo-700 text-white shadow-xs cursor-pointer"
                >
                    Simpan Saldo Terkini
                </button>
            </div>
        </form>
    </x-modal>

    <!-- ===================================================================== -->
    <!-- MODAL 3: PENGATURAN SERVER DEPOSIT                                    -->
    <!-- ===================================================================== -->
    <x-modal name="settings-deposit-modal" title="Pengaturan Server Deposit All Provider" maxWidth="md">
        <form method="POST" action="{{ route('server-deposit.settings') }}" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label for="server_name" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">
                    Nama Server / Aplikasi Konter <span class="text-rose-500">*</span>
                </label>
                <input
                    type="text"
                    name="name"
                    id="server_name"
                    value="{{ old('name', $serverDeposit->name) }}"
                    required
                    placeholder="Contoh: Server All Provider (Payfazz / SiDOMPUL / Digipos)"
                    class="w-full px-3 py-2.5 rounded-2xl border border-slate-300 dark:border-slate-600 text-xs font-medium dark:bg-slate-800 text-slate-900 dark:text-slate-100"
                >
            </div>

            <div>
                <label for="min_alert_balance" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">
                    Batas Peringatan Saldo Menipis (Rp) <span class="text-rose-500">*</span>
                </label>
                <input
                    type="number"
                    name="min_alert_balance"
                    id="min_alert_balance"
                    value="{{ old('min_alert_balance', (float) $serverDeposit->min_alert_balance) }}"
                    required
                    min="0"
                    step="5000"
                    class="w-full px-3 py-2.5 rounded-2xl border border-slate-300 dark:border-slate-600 text-xs font-mono font-bold dark:bg-slate-800 text-slate-900 dark:text-slate-100"
                >
                <p class="text-[11px] text-slate-400 mt-1">Sistem akan menampilkan status "Saldo Kritis" jika saldo deposit turun di bawah nominal ini.</p>
            </div>

            <div>
                <label for="account_id" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">
                    Nomor HP / ID Akun Agen (Opsional)
                </label>
                <input
                    type="text"
                    name="account_id"
                    id="account_id"
                    value="{{ old('account_id', $serverDeposit->account_id) }}"
                    placeholder="Contoh: 081234567890 / ID-AGEN-881"
                    class="w-full px-3 py-2.5 rounded-2xl border border-slate-300 dark:border-slate-600 text-xs font-medium dark:bg-slate-800 text-slate-900 dark:text-slate-100"
                >
            </div>

            <div>
                <label for="notes" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">
                    Catatan Server (Opsional)
                </label>
                <input
                    type="text"
                    name="notes"
                    id="notes"
                    value="{{ old('notes', $serverDeposit->notes) }}"
                    placeholder="Catatan tambahan..."
                    class="w-full px-3 py-2.5 rounded-2xl border border-slate-300 dark:border-slate-600 text-xs dark:bg-slate-800 text-slate-900 dark:text-slate-100"
                >
            </div>

            <div class="pt-3 border-t border-slate-100 dark:border-slate-700 flex justify-end gap-2">
                <button
                    type="button"
                    @click="$dispatch('close-modal', 'settings-deposit-modal')"
                    class="px-3.5 py-2 rounded-xl text-xs font-bold bg-slate-100 hover:bg-slate-200 text-slate-700 dark:bg-slate-800 dark:text-slate-300 cursor-pointer"
                >
                    Batal
                </button>
                <button
                    type="submit"
                    class="px-4 py-2 rounded-xl text-xs font-bold bg-indigo-600 hover:bg-indigo-700 text-white shadow-xs cursor-pointer"
                >
                    Simpan Pengaturan
                </button>
            </div>
        </form>
    </x-modal>
</div>
