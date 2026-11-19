<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50 dark:bg-slate-900">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'KonterKu' }} — Kelola Keuangan & Stok Konter</title>

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="alternate icon" href="{{ asset('favicon.ico') }}">

    <!-- DNS Prefetch & Preconnect untuk Kecepatan Loading -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="dns-prefetch" href="https://fonts.googleapis.com">
    <link rel="dns-prefetch" href="https://fonts.gstatic.com">
    <link rel="dns-prefetch" href="https://cdn.jsdelivr.net">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS / Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="h-full text-slate-800 dark:text-slate-200 antialiased" x-data="{ mobileSidebar: false }">
    <div class="min-h-full flex">
        <!-- Sidebar Navigation -->
        <x-sidebar />

        <!-- Mobile Backdrop -->
        <div
            x-show="mobileSidebar"
            x-transition:enter="transition-opacity ease-linear duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-linear duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-30 bg-slate-900/60 backdrop-blur-xs lg:hidden"
            @click="mobileSidebar = false"
            style="display: none;"
        ></div>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-w-0 lg:pl-64">
            <!-- Top Navbar -->
            <x-navbar :title="$header ?? 'KonterKu'" />

            @if(config('app.demo_mode', false))
                <!-- Banner Informasi Mode Demo Portofolio -->
                <div class="bg-gradient-to-r from-indigo-600 via-violet-600 to-emerald-600 text-white px-4 py-2 text-center text-[11px] sm:text-xs font-semibold shadow-xs print:hidden">
                    <div class="max-w-7xl mx-auto flex flex-wrap items-center justify-center gap-x-2 gap-y-0.5">
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-white/20 text-[10px] font-black uppercase tracking-wider">
                            ✨ Mode Demo Portofolio
                        </span>
                        <span>Data transaksi &amp; keuangan di sini adalah data simulasi. Anda bebas mencoba seluruh fitur kasir, stok, kasbon, &amp; laporan.</span>
                    </div>
                </div>
            @endif

            <!-- Page Body -->
            <main class="flex-1 p-4 sm:p-6 lg:p-8 max-w-7xl w-full mx-auto">
                <!-- Flash Notification Messages -->
                @if(session('success'))
                    <x-alert type="success" :message="session('success')" />
                @endif

                @if(session('error'))
                    <x-alert type="error" :message="session('error')" />
                @endif

                @if(session('info'))
                    <x-alert type="info" :message="session('info')" />
                @endif

                @if(session('warning'))
                    <x-alert type="warning" :message="session('warning')" />
                @endif

                @if(isset($errors) && $errors->any() && !session('error'))
                    <x-alert type="error">
                        <span class="font-bold">Terjadi kesalahan pengisian data:</span>
                        <ul class="list-disc pl-5 mt-1 space-y-0.5 text-xs">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </x-alert>
                @endif

                <!-- Dynamic Content Slot -->
                {{ $slot }}
            </main>

            <!-- Footer -->
            <footer class="mt-auto py-4 px-6 border-t border-slate-200/80 dark:border-slate-800 text-xs text-center text-slate-400">
                &copy; {{ date('Y') }} KonterKu — Sistem Manajemen Keuangan & Persediaan Konter.
            </footer>
        </div>
    </div>

    <!-- Instant Prefetch Navigation untuk Perpindahan Menu Super Cepat -->
    <script>
        (function() {
            const prefetched = new Set();
            function prefetchUrl(url) {
                if (!url || prefetched.has(url)) return;
                try {
                    const parsed = new URL(url, window.location.origin);
                    if (parsed.origin !== window.location.origin) return;
                    if (parsed.pathname === window.location.pathname && parsed.search === window.location.search) return;
                    if (parsed.pathname.includes('/logout') || parsed.pathname.includes('/delete') || parsed.pathname.includes('/download') || parsed.pathname.includes('/export')) return;
                    
                    prefetched.add(url);
                    const link = document.createElement('link');
                    link.rel = 'prefetch';
                    link.href = url;
                    link.as = 'document';
                    document.head.appendChild(link);
                } catch (e) {}
            }

            document.addEventListener('mouseover', function(e) {
                const a = e.target.closest('a');
                if (a && a.href && !a.hasAttribute('download') && a.target !== '_blank') {
                    prefetchUrl(a.href);
                }
            }, { passive: true });

            document.addEventListener('touchstart', function(e) {
                const a = e.target.closest('a');
                if (a && a.href && !a.hasAttribute('download') && a.target !== '_blank') {
                    prefetchUrl(a.href);
                }
            }, { passive: true });
        })();
    </script>

    {{ $scripts ?? '' }}
</body>
</html>
