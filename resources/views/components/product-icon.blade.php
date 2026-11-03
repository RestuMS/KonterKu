@props([
    'category' => null,
    'name' => '',
    'size' => 'md',
    'class' => '',
])

@php
    $catSlug = '';
    $catName = '';
    if ($category instanceof \App\Models\Category) {
        $catSlug = $category->slug ?? '';
        $catName = $category->name ?? '';
    } elseif (is_string($category)) {
        $catSlug = \Illuminate\Support\Str::slug($category);
        $catName = $category;
    }

    $combinedText = strtolower($catSlug . ' ' . $catName . ' ' . $name);

    // Deteksi tipe icon spesifik konter
    if (str_contains($combinedText, 'game') || str_contains($combinedText, 'diamond') || str_contains($combinedText, 'free fire') || str_contains($combinedText, 'mobile legends')) {
        $type = 'game';
        $bg = 'bg-gradient-to-br from-rose-500 via-pink-600 to-indigo-600 shadow-pink-500/25 text-white';
    } elseif (str_contains($combinedText, 'wifi') || str_contains($combinedText, 'indihome') || str_contains($combinedText, 'iconnet') || str_contains($combinedText, 'myrepublic') || str_contains($combinedText, 'first media') || str_contains($combinedText, 'firstmedia')) {
        $type = 'wifi';
        $bg = 'bg-gradient-to-br from-teal-500 to-cyan-600 shadow-teal-500/25 text-white';
    } elseif (str_contains($combinedText, 'bpjs')) {
        $type = 'bpjs';
        $bg = 'bg-gradient-to-br from-emerald-600 to-green-700 shadow-emerald-600/25 text-white';
    } elseif (str_contains($combinedText, 'dana') || str_contains($combinedText, 'gopay') || str_contains($combinedText, 'shopeepay') || str_contains($combinedText, 'seabank') || str_contains($combinedText, 'ovo') || str_contains($combinedText, 'wallet')) {
        $type = 'ewallet';
        $bg = 'bg-gradient-to-br from-blue-600 to-indigo-700 shadow-blue-600/25 text-white';
    } elseif (str_contains($combinedText, 'token') || str_contains($combinedText, 'pln') || str_contains($combinedText, 'listrik') || str_contains($combinedText, 'pajak')) {
        $type = 'token';
        $bg = 'bg-gradient-to-br from-amber-500 to-orange-600 shadow-amber-500/25 text-white';
    } elseif (str_contains($combinedText, 'paket internet') || str_contains($combinedText, 'kuota') || str_contains($combinedText, 'data')) {
        $type = 'paket';
        $bg = 'bg-gradient-to-br from-sky-500 to-blue-600 shadow-sky-500/25 text-white';
    } elseif (str_contains($combinedText, 'voucher') || str_contains($combinedText, 'gosok')) {
        $type = 'voucher';
        $bg = 'bg-gradient-to-br from-orange-500 to-rose-500 shadow-orange-500/25 text-white';
    } elseif (str_contains($combinedText, 'perdana') || str_contains($combinedText, 'kartu') || str_contains($combinedText, 'sim')) {
        $type = 'kartu';
        $bg = 'bg-gradient-to-br from-purple-600 to-indigo-700 shadow-purple-600/25 text-white';
    } elseif (str_contains($combinedText, 'pulsa')) {
        $type = 'pulsa';
        $bg = 'bg-gradient-to-br from-indigo-500 to-violet-600 shadow-indigo-500/25 text-white';
    } elseif (str_contains($combinedText, 'transfer') || str_contains($combinedText, 'tarik') || str_contains($combinedText, 'tunai') || str_contains($combinedText, 'bank') || str_contains($combinedText, 'bri') || str_contains($combinedText, 'bni') || str_contains($combinedText, 'bca') || str_contains($combinedText, 'mandiri')) {
        $type = 'transfer';
        $bg = 'bg-gradient-to-br from-emerald-600 to-teal-700 shadow-emerald-600/25 text-white';
    } else {
        $type = 'default';
        $bg = 'bg-gradient-to-br from-slate-600 to-slate-800 shadow-slate-500/25 text-white';
    }

    $sizeClasses = match($size) {
        'sm' => 'w-8 h-8 rounded-lg p-1.5',
        'lg' => 'w-14 h-14 rounded-2xl p-3.5',
        default => 'w-11 h-11 rounded-xl p-2.5',
    };

    $iconSize = match($size) {
        'sm' => 'w-5 h-5',
        'lg' => 'w-7 h-7',
        default => 'w-6 h-6',
    };
@endphp

<div class="inline-flex items-center justify-center shrink-0 shadow-md {{ $bg }} {{ $sizeClasses }} {{ $class }}">
    @if($type === 'game')
        <!-- Game Top Up (Gamepad / Controller) -->
        <svg class="{{ $iconSize }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
            <circle cx="9" cy="12" r="1" fill="currentColor" />
            <circle cx="15" cy="12" r="1" fill="currentColor" />
        </svg>
    @elseif($type === 'wifi')
        <!-- WiFi / Internet Fiber (Wi-Fi Broadcast) -->
        <svg class="{{ $iconSize }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0" />
        </svg>
    @elseif($type === 'bpjs')
        <!-- BPJS Kesehatan (Heart / Shield Health) -->
        <svg class="{{ $iconSize }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
        </svg>
    @elseif($type === 'ewallet')
        <!-- E-Wallet & Bank Digital (Wallet / Card) -->
        <svg class="{{ $iconSize }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
        </svg>
    @elseif($type === 'token')
        <!-- Token Listrik PLN (Lightning Bolt) -->
        <svg class="{{ $iconSize }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
        </svg>
    @elseif($type === 'paket')
        <!-- Paket Internet (Globe / Wi-Fi Signal) -->
        <svg class="{{ $iconSize }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
        </svg>
    @elseif($type === 'pulsa')
        <!-- Pulsa Reguler (Smartphone + Signal) -->
        <svg class="{{ $iconSize }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2zM9 8h6M9 12h3" />
        </svg>
    @elseif($type === 'voucher')
        <!-- Voucher Fisik (Ticket / Barcode Voucher) -->
        <svg class="{{ $iconSize }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
        </svg>
    @elseif($type === 'kartu')
        <!-- Kartu Perdana (SIM Card Chip) -->
        <svg class="{{ $iconSize }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3H5a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2V9.414a2 2 0 00-.586-1.414l-4.414-4.414A2 2 0 0013.586 3H9zm0 0v4a2 2 0 002 2h4M9 13h6m-6 4h3" />
        </svg>
    @elseif($type === 'transfer')
        <!-- Transfer Uang & Tarik Tunai (Bank / Cash Exchange) -->
        <svg class="{{ $iconSize }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
        </svg>
    @else
        <!-- Default Produk (Box Package) -->
        <svg class="{{ $iconSize }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
        </svg>
    @endif
</div>
