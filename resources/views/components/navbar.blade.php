@props(['title' => 'Dashboard'])

@php
    $lowStockCount = $lowStockCount ?? (\App\Models\Product::where('is_active', true)->whereColumn('stock', '<=', 'min_stock')->count());
@endphp

<header class="sticky top-0 z-30 bg-white/80 dark:bg-slate-800/80 backdrop-blur-md border-b border-slate-200/80 dark:border-slate-700/80 transition-colors">
    <div class="px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
        <!-- Left: Mobile Toggle & Page Title -->
        <div class="flex items-center gap-3">
            <button
                type="button"
                @click="mobileSidebar = !mobileSidebar"
                class="lg:hidden p-2 rounded-xl text-slate-500 hover:text-slate-700 hover:bg-slate-100 dark:hover:bg-slate-700 focus:outline-hidden"
                aria-label="Toggle navigation"
            >
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>

            <div>
                <h1 class="text-lg font-bold text-slate-900 dark:text-white tracking-tight">{{ $title }}</h1>
            </div>
        </div>

        <!-- Right: Actions & User Dropdown -->
        <div class="flex items-center gap-2.5 sm:gap-3">
            <!-- Alert Stok Menipis Interaktif -->
            @if(isset($lowStockCount) && $lowStockCount > 0)
                <a
                    href="{{ route('products.index', ['low_stock' => 1]) }}"
                    class="inline-flex items-center gap-1.5 px-2.5 py-1.5 text-xs font-bold rounded-xl bg-rose-50 text-rose-700 hover:bg-rose-100 dark:bg-rose-950/50 dark:text-rose-300 dark:hover:bg-rose-900/60 transition-all border border-rose-200/80 dark:border-rose-800/60 shadow-2xs group"
                    title="{{ $lowStockCount }} produk berada di bawah batas stok minimum! Klik untuk filter & kulakan."
                >
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-rose-500"></span>
                    </span>
                    <span class="hidden md:inline font-bold">Stok Menipis</span>
                    <span class="px-1.5 py-0.5 rounded-md bg-rose-200/80 dark:bg-rose-900 text-rose-800 dark:text-rose-200 font-mono font-black text-[11px] leading-none">{{ $lowStockCount }}</span>
                </a>
            @endif

            <!-- Quick New Transaction Shortcut -->
            <a
                href="{{ route('incomes.create') }}"
                class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg bg-indigo-50 text-indigo-700 hover:bg-indigo-100 dark:bg-indigo-950/50 dark:text-indigo-300 dark:hover:bg-indigo-900/60 transition-colors border border-indigo-200/60 dark:border-indigo-800/40"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Pemasukan Baru
            </a>

            <!-- User Menu Dropdown -->
            <x-dropdown align="right" width="56">
                <x-slot name="trigger">
                    <button class="flex items-center gap-2.5 p-1.5 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors text-left cursor-pointer">
                        <div class="w-8 h-8 rounded-lg bg-indigo-600 text-white font-bold flex items-center justify-center text-xs shadow-xs">
                            {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
                        </div>
                        <div class="hidden md:block text-left">
                            <div class="text-xs font-semibold text-slate-800 dark:text-slate-200 leading-tight">
                                {{ Auth::user()->name ?? 'Administrator' }}
                            </div>
                            <div class="text-[10px] text-slate-500 capitalize">
                                {{ Auth::user()->role ?? 'Admin' }}
                            </div>
                        </div>
                        <svg class="w-4 h-4 text-slate-400 hidden sm:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                </x-slot>

                <x-slot name="content">
                    <div class="px-4 py-2.5 border-b border-slate-100 dark:border-slate-700/60 text-xs">
                        <div class="font-semibold text-slate-900 dark:text-slate-100">{{ Auth::user()->name ?? 'User' }}</div>
                        <div class="text-slate-500 truncate">{{ Auth::user()->email ?? '' }}</div>
                    </div>

                    <a href="{{ route('settings.index') }}" class="flex items-center gap-2 px-4 py-2 text-xs text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Pengaturan Konter
                    </a>

                    <a href="{{ route('settings.index', ['tab' => 'keamanan']) }}" class="flex items-center gap-2 px-4 py-2 text-xs text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                        </svg>
                        Profil & Ganti Sandi
                    </a>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-left flex items-center gap-2 px-4 py-2 text-xs text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/30">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                            Keluar (Logout)
                        </button>
                    </form>
                </x-slot>
            </x-dropdown>
        </div>
    </div>
</header>
