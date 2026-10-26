<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50 dark:bg-slate-900">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'KonterKu' }} — Kelola Keuangan & Stok Konter</title>

    <!-- Google Fonts Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS / Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
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

    {{ $scripts ?? '' }}
</body>
</html>
