<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50 dark:bg-slate-900">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Autentikasi' }} — KonterKu</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS / Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="h-full text-slate-800 dark:text-slate-200 antialiased selection:bg-indigo-500 selection:text-white">
    <div class="min-h-screen lg:grid lg:grid-cols-12">
        <!-- ================================================================= -->
        <!-- LEFT PANEL: DYNAMIC BRAND SHOWCASE & LIVE TICKER (DESKTOP)       -->
        <!-- ================================================================= -->
        <aside
            x-data="{
                activeTicker: 0,
                tickers: [
                    { title: 'Paket Data Telkomsel 25GB', time: 'Baru saja', price: 'Rp 65.000', profit: '+Laba Rp 7.500', category: 'Kuota Data', icon: 'data' },
                    { title: 'Top-up E-Wallet DANA 50k', time: '1 mnt lalu', price: 'Rp 52.000', profit: '+Laba Rp 2.500', category: 'E-Wallet', icon: 'wallet' },
                    { title: 'Token Listrik PLN 100k', time: '3 mnt lalu', price: 'Rp 103.000', profit: '+Laba Rp 3.000', category: 'Tagihan PLN', icon: 'bolt' },
                    { title: 'Pelunasan Kasbon Mas Doni', time: '5 mnt lalu', price: 'Rp 45.000', profit: 'Status Lunas', category: 'Buku Kasbon', icon: 'kasbon' }
                ],
                timeString: '',
                updateClock() {
                    const now = new Date();
                    this.timeString = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' }) + ' WIB';
                },
                init() {
                    this.updateClock();
                    setInterval(() => this.updateClock(), 1000);
                    setInterval(() => {
                        this.activeTicker = (this.activeTicker + 1) % this.tickers.length;
                    }, 3500);
                }
            }"
            class="hidden lg:flex lg:col-span-5 xl:col-span-5 bg-gradient-to-br from-slate-950 via-indigo-950 to-slate-900 text-white relative overflow-hidden flex-col justify-between p-10 xl:p-14 border-r border-indigo-900/40"
        >
            <!-- Decorative Ambient Glowing Lights (Slow Moving & Pulsing) -->
            <div class="absolute -top-24 -left-24 w-96 h-96 bg-indigo-500/25 rounded-full blur-3xl pointer-events-none animate-pulse-glow"></div>
            <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-purple-500/20 rounded-full blur-3xl pointer-events-none animate-pulse-glow" style="animation-delay: 3s;"></div>
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-80 h-80 bg-sky-500/15 rounded-full blur-3xl pointer-events-none"></div>

            <!-- Top Brand Identity with Live Clock -->
            <div class="relative z-10 flex items-center justify-between">
                <div class="flex items-center gap-3.5">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-indigo-500 via-indigo-600 to-sky-400 text-white flex items-center justify-center shadow-lg shadow-indigo-500/35 shrink-0 animate-float">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h1 class="text-2xl font-black tracking-tight text-white">KonterKu</h1>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-indigo-500/25 text-indigo-300 border border-indigo-400/30">
                                POS Kasir v1.0
                            </span>
                        </div>
                        <p class="text-xs text-slate-400">Sistem Kasir & Pembukuan Konter Pintar</p>
                    </div>
                </div>

                <!-- Live Clock Badge -->
                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/10 border border-white/15 text-[11px] font-mono text-indigo-200 backdrop-blur-md">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                    <span x-text="timeString">--:--:-- WIB</span>
                </div>
            </div>

            <!-- Middle Value Propositions & Live Interactive Sales Simulation -->
            <div class="relative z-10 my-8 space-y-7">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 border border-white/15 text-xs text-indigo-200 mb-3.5 backdrop-blur-md">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-400"></span>
                        </span>
                        <span>Sistem Manajemen Keuangan Aktif</span>
                    </div>
                    <h2 class="text-2xl xl:text-3xl font-black tracking-tight text-white leading-tight">
                        Pencatatan Keuangan Konter Lebih Akurat & Bebas Ribet.
                    </h2>
                    <p class="text-xs xl:text-sm text-slate-300/90 mt-2 leading-relaxed">
                        Kelola stok pulsa & aksesoris, pantau transaksi harian, buku hutang kasbon, dan hitung laba bersih otomatis langsung di kasir.
                    </p>
                </div>

                <!-- 3 Feature Highlights with Subtle Hover Depth -->
                <div class="space-y-3">
                    <div class="flex items-center gap-3.5 p-3 rounded-2xl bg-white/5 hover:bg-white/10 border border-white/10 backdrop-blur-xs transition-all duration-200 group">
                        <div class="w-9 h-9 rounded-xl bg-indigo-500/25 text-indigo-300 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-xs font-bold text-white group-hover:text-indigo-200 transition-colors">Arus Kas & Laba Bersih Otomatis</h4>
                            <p class="text-[11px] text-slate-400">Laba penjualan langsung terhitung otomatis tanpa perlu kalkulator manual.</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3.5 p-3 rounded-2xl bg-white/5 hover:bg-white/10 border border-white/10 backdrop-blur-xs transition-all duration-200 group">
                        <div class="w-9 h-9 rounded-xl bg-sky-500/25 text-sky-300 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-xs font-bold text-white group-hover:text-sky-200 transition-colors">Stok Realtime & Notifikasi Menipis</h4>
                            <p class="text-[11px] text-slate-400">Peringatan otomatis saat kartu perdana atau voucher hampir habis.</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3.5 p-3 rounded-2xl bg-white/5 hover:bg-white/10 border border-white/10 backdrop-blur-xs transition-all duration-200 group">
                        <div class="w-9 h-9 rounded-xl bg-emerald-500/25 text-emerald-300 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-xs font-bold text-white group-hover:text-emerald-200 transition-colors">Buku Kasbon & Struk Belanja</h4>
                            <p class="text-[11px] text-slate-400">Kelola hutang pelanggan dengan rapi dan cetak struk Bluetooth cepat.</p>
                        </div>
                    </div>
                </div>

                <!-- LIVE TRANSACTION TICKER (Animasi Pergantian Data Otomatis) -->
                <div class="p-4 rounded-2xl bg-white/10 border border-white/15 backdrop-blur-md shadow-xl shadow-black/20">
                    <div class="flex items-center justify-between text-[11px] text-slate-300 mb-2 border-b border-white/10 pb-2">
                        <div class="flex items-center gap-1.5 font-bold">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                            <span>Simulasi Kasir Live</span>
                        </div>
                        <span class="text-[10px] text-slate-400 font-mono" x-text="tickers[activeTicker].time">Baru saja</span>
                    </div>

                    <template x-for="(item, idx) in tickers" :key="idx">
                        <div
                            x-show="activeTicker === idx"
                            x-transition:enter="transition ease-out duration-300 transform"
                            x-transition:enter-start="opacity-0 translate-y-2"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            class="flex items-center justify-between"
                        >
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-xl bg-emerald-500/25 text-emerald-300 flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <div>
                                    <div class="text-xs font-bold text-white leading-tight" x-text="item.title">Paket Data</div>
                                    <div class="text-[11px] text-slate-300 font-mono" x-text="item.price">Rp 65.000</div>
                                </div>
                            </div>
                            <span
                                class="px-2.5 py-1 rounded-lg bg-emerald-500/25 border border-emerald-400/30 text-emerald-300 font-mono text-[11px] font-extrabold"
                                x-text="item.profit"
                            >
                                +Laba Rp 7.500
                            </span>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Bottom Security Badges & Copyright -->
            <div class="relative z-10 pt-4 border-t border-white/10 flex flex-col sm:flex-row items-center justify-between gap-3 text-[11px] text-slate-400">
                <div class="flex items-center gap-3">
                    <span class="flex items-center gap-1 text-slate-300">
                        <svg class="w-3.5 h-3.5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        Terenkripsi
                    </span>
                    <span>•</span>
                    <span class="flex items-center gap-1 text-slate-300">
                        <svg class="w-3.5 h-3.5 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        Responsif
                    </span>
                    <span>•</span>
                    <span class="flex items-center gap-1 text-slate-300">
                        <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 00-9.78 2.096A4.001 4.001 0 003 15z"/></svg>
                        Siap Hosting
                    </span>
                </div>
                <div>&copy; {{ date('Y') }} KonterKu</div>
            </div>
        </aside>

        <!-- ================================================================= -->
        <!-- RIGHT PANEL: AUTHENTICATION FORM CARD CONTAINER                   -->
        <!-- ================================================================= -->
        <main class="lg:col-span-7 xl:col-span-7 flex flex-col justify-center items-center p-4 sm:p-8 lg:p-12 xl:p-16 min-h-screen relative bg-slate-50 dark:bg-slate-900 overflow-hidden">
            <!-- Background Ambient Glow in Right Panel (Bikin UI Nampak Bersinar & Hidup) -->
            <div class="absolute -top-36 -right-36 w-96 h-96 bg-indigo-500/10 dark:bg-indigo-500/15 rounded-full blur-3xl pointer-events-none animate-pulse-glow"></div>
            <div class="absolute -bottom-36 -left-36 w-96 h-96 bg-sky-500/10 dark:bg-sky-500/15 rounded-full blur-3xl pointer-events-none animate-pulse-glow" style="animation-delay: 2s;"></div>

            <!-- Mobile Brand Header (Tampil hanya pada layar HP/Tablet) -->
            <div class="lg:hidden text-center mb-6 w-full max-w-md relative z-10">
                <div class="inline-flex w-12 h-12 rounded-2xl bg-gradient-to-tr from-indigo-600 via-indigo-700 to-sky-500 items-center justify-center text-white shadow-lg shadow-indigo-500/30 mb-2.5 animate-float">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h2 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">KonterKu</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Sistem Kasir & Pembukuan Konter</p>
            </div>

            <!-- Form Card Wrapper -->
            <div class="w-full max-w-md sm:max-w-lg relative z-10">
                <!-- Card Container dengan Top Gradient Accent & Soft Glass Glow -->
                <div class="bg-white/95 dark:bg-slate-850/95 backdrop-blur-xl rounded-3xl shadow-2xl shadow-indigo-950/10 dark:shadow-none border border-slate-200/90 dark:border-slate-800 p-6 sm:p-9 relative overflow-hidden before:absolute before:inset-x-0 before:top-0 before:h-1.5 before:bg-gradient-to-r before:from-indigo-500 before:via-purple-500 before:to-sky-400">
                    <!-- Session Flash Messages -->
                    @if(session('success'))
                        <div class="mb-5 p-3.5 rounded-2xl bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200 dark:border-emerald-800/60 flex items-start gap-3 text-xs text-emerald-800 dark:text-emerald-200 animate-fadeIn shadow-xs">
                            <svg class="w-5 h-5 text-emerald-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <div class="font-medium leading-relaxed">{{ session('success') }}</div>
                        </div>
                    @endif

                    @if(session('status'))
                        <div class="mb-5 p-3.5 rounded-2xl bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200 dark:border-emerald-800/60 flex items-start gap-3 text-xs text-emerald-800 dark:text-emerald-200 shadow-xs">
                            <svg class="w-5 h-5 text-emerald-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <div class="font-medium leading-relaxed">{{ session('status') }}</div>
                        </div>
                    @endif

                    @if(session('info'))
                        <div class="mb-5 p-3.5 rounded-2xl bg-blue-50 dark:bg-blue-950/50 border border-blue-200 dark:border-blue-800/60 flex items-start gap-3 text-xs text-blue-800 dark:text-blue-200 shadow-xs">
                            <svg class="w-5 h-5 text-blue-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <div class="font-medium leading-relaxed">{{ session('info') }}</div>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="mb-5 p-3.5 rounded-2xl bg-rose-50 dark:bg-rose-950/50 border border-rose-200 dark:border-rose-800/60 flex items-start gap-3 text-xs text-rose-800 dark:text-rose-200 shadow-xs">
                            <svg class="w-5 h-5 text-rose-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <div class="font-medium leading-relaxed">{{ session('error') }}</div>
                        </div>
                    @endif

                    <!-- Slot Content -->
                    {{ $slot }}
                </div>

                <!-- Footer Copyright for Mobile -->
                <div class="mt-6 text-center text-xs text-slate-400 lg:hidden">
                    &copy; {{ date('Y') }} KonterKu — Kelola Keuangan & Persediaan Konter.
                </div>
            </div>
        </main>
    </div>
</body>
</html>
