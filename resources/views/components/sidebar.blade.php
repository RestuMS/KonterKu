@php
    $lowStockCount = $lowStockCount ?? (\App\Models\Product::where('is_active', true)->whereColumn('stock', '<=', 'min_stock')->count());
    $unpaidKasbonCount = $unpaidKasbonCount ?? (\App\Models\Kasbon::where('status', 'unpaid')->count());
@endphp

<aside
    class="fixed inset-y-0 left-0 z-40 w-64 bg-slate-900 text-white flex flex-col transition-transform duration-300 ease-in-out lg:translate-x-0 border-r border-slate-800"
    :class="mobileSidebar ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
>
    <!-- Brand / Logo -->
    <div class="h-16 px-6 flex items-center justify-between border-b border-slate-800/80">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-indigo-600 to-indigo-400 flex items-center justify-center text-white shadow-md shadow-indigo-500/20">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
            </div>
            <div>
                <span class="text-base font-bold tracking-tight text-white block leading-none">KonterKu</span>
                <span class="text-[10px] text-slate-400 font-medium tracking-wider uppercase">Keuangan & Stok</span>
            </div>
        </a>

        <!-- Mobile Close Button -->
        <button
            type="button"
            @click="mobileSidebar = false"
            class="lg:hidden p-1.5 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800"
            aria-label="Tutup navigasi"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    <!-- Navigation Links -->
    <nav class="flex-1 px-4 py-5 space-y-1.5 overflow-y-auto">
        <div class="px-3 pb-2 text-[11px] font-semibold tracking-wider text-slate-400 uppercase">
            Menu Utama
        </div>

        <!-- Dashboard -->
        <a
            href="{{ route('dashboard') }}"
            class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 {{ request()->routeIs('dashboard') ? 'bg-indigo-600 text-white shadow-sm shadow-indigo-600/30' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}"
        >
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            Dashboard
        </a>

        <!-- Pemasukan -->
        <a
            href="{{ route('incomes.index') }}"
            class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 {{ request()->routeIs('incomes.*') ? 'bg-indigo-600 text-white shadow-sm shadow-indigo-600/30' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}"
        >
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/>
            </svg>
            Pemasukan
        </a>

        <!-- Pengeluaran -->
        <a
            href="{{ route('expenses.index') }}"
            class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 {{ request()->routeIs('expenses.*') ? 'bg-indigo-600 text-white shadow-sm shadow-indigo-600/30' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}"
        >
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"/>
            </svg>
            Pengeluaran
        </a>

        <!-- Buku Kasbon / Catatan Hutang -->
        <div class="flex items-center justify-between rounded-xl transition-all duration-150 {{ request()->routeIs('kasbons.*') ? 'bg-indigo-600 text-white shadow-sm shadow-indigo-600/30' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
            <a
                href="{{ route('kasbons.index') }}"
                class="flex-1 flex items-center gap-3 px-3 py-2.5 text-sm font-medium {{ request()->routeIs('kasbons.*') ? 'text-white' : 'text-slate-300 hover:text-white' }}"
            >
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
                <span>Buku Kasbon</span>
            </a>

            @if(isset($unpaidKasbonCount) && $unpaidKasbonCount > 0)
                <a
                    href="{{ route('kasbons.index', ['status' => 'unpaid']) }}"
                    class="mr-2.5 inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-black bg-amber-500 hover:bg-amber-600 text-slate-900 shadow-sm shadow-amber-500/50 transition-all hover:scale-105 active:scale-95 cursor-pointer"
                    title="{{ $unpaidKasbonCount }} orang belum melunasi hutang/kasbon"
                >
                    <span class="w-1.5 h-1.5 rounded-full bg-slate-900 animate-ping"></span>
                    <span>{{ $unpaidKasbonCount > 99 ? '99+' : $unpaidKasbonCount }}</span>
                </a>
            @endif
        </div>

        <!-- Produk & Stok -->
        <div class="flex items-center justify-between rounded-xl transition-all duration-150 {{ request()->routeIs('products.*') ? 'bg-indigo-600 text-white shadow-sm shadow-indigo-600/30' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
            <a
                href="{{ route('products.index') }}"
                class="flex-1 flex items-center gap-3 px-3 py-2.5 text-sm font-medium {{ request()->routeIs('products.*') ? 'text-white' : 'text-slate-300 hover:text-white' }}"
            >
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
                <span>Produk & Stok</span>
            </a>

            @if(isset($lowStockCount) && $lowStockCount > 0)
                <a
                    href="{{ route('products.index', ['low_stock' => 1]) }}"
                    class="mr-2.5 inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-black bg-rose-500 hover:bg-rose-600 text-white shadow-sm shadow-rose-500/50 transition-all hover:scale-105 active:scale-95 cursor-pointer"
                    title="{{ $lowStockCount }} produk berada di bawah batas stok minimum! Klik untuk langsung filter produk menipis."
                >
                    <span class="w-1.5 h-1.5 rounded-full bg-white animate-ping"></span>
                    <span>{{ $lowStockCount > 99 ? '99+' : $lowStockCount }}</span>
                </a>
            @endif
        </div>

        <!-- Mutasi Stok -->
        <a
            href="{{ route('stocks.index') }}"
            class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 {{ request()->routeIs('stocks.*') ? 'bg-indigo-600 text-white shadow-sm shadow-indigo-600/30' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}"
        >
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
            </svg>
            Mutasi Stok
        </a>

        <!-- Saldo Server (Mutasi Saldo) -->
        <a
            href="{{ route('server-deposit.mutations') }}"
            class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 {{ request()->routeIs('server-deposit.*') ? 'bg-indigo-600 text-white shadow-sm shadow-indigo-600/30' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}"
        >
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
            </svg>
            Saldo Server
        </a>

        <!-- Laporan -->
        <a
            href="{{ route('reports.index') }}"
            class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 {{ request()->routeIs('reports.*') ? 'bg-indigo-600 text-white shadow-sm shadow-indigo-600/30' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}"
        >
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Laporan
        </a>

        <div class="px-3 pt-4 pb-2 text-[11px] font-semibold tracking-wider text-slate-400 uppercase">
            Konfigurasi
        </div>

        <!-- Pengaturan -->
        <a
            href="{{ route('settings.index') }}"
            class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 {{ request()->routeIs('settings.*') ? 'bg-indigo-600 text-white shadow-sm shadow-indigo-600/30' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}"
        >
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            Pengaturan
        </a>
    </nav>

    <!-- Bottom User Status -->
    <div class="p-4 border-t border-slate-800">
        <div class="flex items-center gap-3 px-2 py-2">
            <div class="w-2.5 h-2.5 rounded-full bg-emerald-500 ring-4 ring-emerald-500/20"></div>
            <div class="text-xs text-slate-400">
                Sistem Siap Operasi
            </div>
        </div>
    </div>
</aside>
