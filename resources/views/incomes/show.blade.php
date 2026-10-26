<x-layouts.app title="Detail Invoice {{ $income->invoice_number }}">
    <x-slot:header>
        Invoice {{ $income->invoice_number }}
    </x-slot:header>

    <div class="max-w-2xl mx-auto space-y-6">
        <div class="flex items-center justify-between">
            <x-button href="{{ route('incomes.index') }}" variant="secondary" size="sm">
                &larr; Kembali ke Daftar Pemasukan
            </x-button>
            <div class="flex items-center gap-2">
                <a
                    href="{{ route('incomes.edit', $income) }}"
                    class="px-3 py-1.5 rounded-lg border border-amber-300 dark:border-amber-700 bg-amber-50 dark:bg-amber-950/40 text-xs font-bold text-amber-800 dark:text-amber-300 hover:bg-amber-100 flex items-center gap-1.5 shadow-2xs transition-colors"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    <span>Edit Transaksi</span>
                </a>

                <button
                    type="button"
                    onclick="window.print()"
                    class="px-3 py-1.5 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-50 flex items-center gap-1.5 shadow-2xs cursor-pointer"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Cetak Struk</span>
                </button>
            </div>
        </div>

        <x-card class="print:shadow-none print:border-none">
            <!-- Header Struk Konter -->
            <div class="text-center pb-6 border-b border-slate-200 dark:border-slate-700">
                <div class="w-11 h-11 mx-auto rounded-2xl bg-indigo-600 text-white flex items-center justify-center font-black text-lg mb-2 shadow-md shadow-indigo-600/30">
                    K
                </div>
                <h3 class="text-xl font-black text-slate-900 dark:text-white tracking-tight">KonterKu</h3>
                <p class="text-xs text-slate-500 mt-0.5">Struk Bukti Pembayaran Transaksi Resmi</p>
            </div>

            <!-- Metadata Transaksi -->
            <div class="grid grid-cols-2 gap-4 py-4 text-xs border-b border-slate-100 dark:border-slate-700/60">
                <div>
                    <span class="text-slate-400 block text-[11px] uppercase font-bold tracking-wider">Nomor Invoice</span>
                    <span class="font-mono font-black text-slate-900 dark:text-slate-100 text-sm">{{ $income->invoice_number }}</span>
                </div>
                <div class="text-right">
                    <span class="text-slate-400 block text-[11px] uppercase font-bold tracking-wider">Waktu Transaksi</span>
                    <span class="font-bold text-slate-800 dark:text-slate-200">@tanggalWaktu($income->transaction_date)</span>
                </div>
                <div>
                    <span class="text-slate-400 block text-[11px] uppercase font-bold tracking-wider">Kasir / Operator</span>
                    <span class="font-bold text-slate-800 dark:text-slate-200">{{ $income->user->name ?? 'Kasir Konter' }}</span>
                </div>
                <div class="text-right">
                    <span class="text-slate-400 block text-[11px] uppercase font-bold tracking-wider">Metode Pembayaran</span>
                    <span class="font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-wide">{{ $income->payment_method }}</span>
                </div>
            </div>

            <!-- Detail Item Transaksi -->
            <div class="py-4 border-b border-slate-100 dark:border-slate-700/60">
                <table class="w-full text-xs text-left">
                    <thead class="text-slate-400 uppercase font-bold border-b border-slate-100 dark:border-slate-700/60">
                        <tr>
                            <th class="py-2.5">Item / Layanan</th>
                            <th class="py-2.5 text-center">Qty</th>
                            <th class="py-2.5 text-right">Harga Jual</th>
                            <th class="py-2.5 text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800 font-medium">
                        @forelse($income->items as $item)
                            <tr>
                                <td class="py-3 text-slate-900 dark:text-slate-100 font-bold">
                                    {{ $item->product_name }}
                                    @if($item->product)
                                        <span class="text-[11px] text-slate-400 font-mono block mt-0.5">Kode: {{ $item->product->code }}</span>
                                    @endif
                                </td>
                                <td class="py-3 text-center text-slate-600 dark:text-slate-300 font-mono font-bold">
                                    {{ $item->quantity }}
                                </td>
                                <td class="py-3 text-right text-slate-600 dark:text-slate-300 font-mono">
                                    @rupiah($item->unit_price)
                                </td>
                                <td class="py-3 text-right font-black text-slate-900 dark:text-slate-100 font-mono">
                                    @rupiah($item->subtotal)
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="py-3 text-slate-900 dark:text-slate-100 font-bold">
                                    {{ $income->category->name ?? 'Pemasukan Kas' }}
                                    @if($income->notes)
                                        <span class="block text-[11px] text-slate-400 font-normal mt-0.5">{{ $income->notes }}</span>
                                    @endif
                                </td>
                                <td class="py-3 text-center text-slate-600 dark:text-slate-300 font-mono font-bold">1</td>
                                <td class="py-3 text-right text-slate-600 dark:text-slate-300 font-mono">@rupiah($income->total_amount)</td>
                                <td class="py-3 text-right font-black text-slate-900 dark:text-slate-100 font-mono">@rupiah($income->total_amount)</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Ringkasan Finansial: Modal, Omzet, & Laba -->
            <div class="py-4 border-b border-slate-100 dark:border-slate-700/60 space-y-2 text-xs">
                <div class="flex items-center justify-between text-slate-500">
                    <span>Harga Modal Produk (HPP):</span>
                    <span class="font-mono font-semibold text-slate-700 dark:text-slate-300">
                        @if($income->cost_price > 0)
                            @rupiah($income->cost_price)
                        @else
                            -
                        @endif
                    </span>
                </div>
                <div class="flex items-center justify-between text-slate-500">
                    <span>Estimasi Laba Bersih Transaksi:</span>
                    <span class="font-mono font-bold text-emerald-600 dark:text-emerald-400">
                        + @rupiah($income->profit)
                    </span>
                </div>
                <div class="pt-2 border-t border-slate-100 dark:border-slate-700/60 flex items-center justify-between text-base font-black">
                    <span class="text-slate-900 dark:text-white">Total Omzet Diterima:</span>
                    <span class="text-indigo-600 dark:text-indigo-400 font-mono text-lg">@rupiah($income->total_amount)</span>
                </div>
            </div>

            @if($income->notes && $income->items->isNotEmpty())
                <div class="p-3 bg-slate-50 dark:bg-slate-750 rounded-xl text-xs text-slate-500">
                    <span class="font-bold text-slate-700 dark:text-slate-300 block mb-0.5">Catatan Transaksi:</span>
                    {{ $income->notes }}
                </div>
            @endif

            <div class="mt-6 pt-4 text-center border-t border-dashed border-slate-200 dark:border-slate-700 text-xs text-slate-400">
                Terima kasih atas transaksi Anda di KonterKu! Simpan struk ini sebagai bukti pembayaran resmi.
            </div>
        </x-card>
    </div>
</x-layouts.app>
