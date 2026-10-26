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

    // Deteksi tipe icon
    if (str_contains($combinedText, 'pulsa') || str_contains($combinedText, 'kuota') || str_contains($combinedText, 'data') || str_contains($combinedText, 'internet') || str_contains($combinedText, 'paket')) {
        $type = 'pulsa';
        $bg = 'bg-gradient-to-br from-sky-500 to-indigo-600 shadow-sky-500/20 text-white';
    } elseif (str_contains($combinedText, 'voucher') || str_contains($combinedText, 'gosok')) {
        $type = 'voucher';
        $bg = 'bg-gradient-to-br from-amber-500 to-orange-600 shadow-amber-500/20 text-white';
    } elseif (str_contains($combinedText, 'perdana') || str_contains($combinedText, 'kartu') || str_contains($combinedText, 'sim')) {
        $type = 'kartu';
        $bg = 'bg-gradient-to-br from-purple-500 to-indigo-600 shadow-purple-500/20 text-white';
    } elseif (str_contains($combinedText, 'aksesoris') || str_contains($combinedText, 'kabel') || str_contains($combinedText, 'charger') || str_contains($combinedText, 'headset') || str_contains($combinedText, 'earphone') || str_contains($combinedText, 'case') || str_contains($combinedText, 'tempered')) {
        $type = 'aksesoris';
        $bg = 'bg-gradient-to-br from-emerald-500 to-teal-600 shadow-emerald-500/20 text-white';
    } elseif (str_contains($combinedText, 'servis') || str_contains($combinedText, 'transfer') || str_contains($combinedText, 'topup') || str_contains($combinedText, 'dana') || str_contains($combinedText, 'pln') || str_contains($combinedText, 'jasa')) {
        $type = 'jasa';
        $bg = 'bg-gradient-to-br from-blue-500 to-cyan-600 shadow-blue-500/20 text-white';
    } else {
        $type = 'default';
        $bg = 'bg-gradient-to-br from-slate-600 to-slate-800 shadow-slate-500/20 text-white';
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
    @if($type === 'pulsa')
        <!-- Pulsa & Paket Data (Phone + Cellular Waves) -->
        <svg class="{{ $iconSize }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2zM9 8h6M9 12h3" />
        </svg>
    @elseif($type === 'voucher')
        <!-- Voucher Fisik (Ticket / Scratch Coupon) -->
        <svg class="{{ $iconSize }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
        </svg>
    @elseif($type === 'kartu')
        <!-- Kartu Perdana (SIM Card Chip) -->
        <svg class="{{ $iconSize }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3H5a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2V9.414a2 2 0 00-.586-1.414l-4.414-4.414A2 2 0 0013.586 3H9zm0 0v4a2 2 0 002 2h4M9 13h6m-6 4h3" />
        </svg>
    @elseif($type === 'aksesoris')
        <!-- Aksesoris & Sparepart (Headset / Plug) -->
        <svg class="{{ $iconSize }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2z" />
        </svg>
    @elseif($type === 'jasa')
        <!-- Jasa Servis & Transfer (Lightning Fast Service) -->
        <svg class="{{ $iconSize }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
        </svg>
    @else
        <!-- Default Produk (Box Package) -->
        <svg class="{{ $iconSize }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
        </svg>
    @endif
</div>
