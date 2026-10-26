<x-layouts.app title="Detail Invoice {{ $income->invoice_number }}">
    <x-slot:header>
        Invoice {{ $income->invoice_number }}
    </x-slot:header>

    <div class="max-w-2xl mx-auto">
        <div class="mb-6 flex items-center justify-between">
            <x-button href="{{ route('incomes.index') }}" variant="secondary" size="sm">
                &larr; Kembali ke Daftar Pemasukan
            </x-button>
            <div class="flex items-center gap-2">
                <button type="button" onclick="window.print()" class="px-3 py-1.5 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-50 flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    Cetak Struk
                </button>
            </div>
        </div>

        <x-card class="print:shadow-none print:border-none">
            <!-- Header Struk -->
            <div class="text-center pb-6 border-b border-slate-200 dark:border-slate-700">
                <div class="w-10 h-10 mx-auto rounded-xl bg-indigo-600 text-white flex items-center justify-center font-bold mb-2">
                    K
                </div>
                <h3 class="text-lg font-bold text-slate-900 dark:text-white">KonterKu</h3>
                <p class="text-xs text-slate-500">Struk Bukti Pembayaran / Penjualan</p>
            </div>

            <!-- Metadata Transaksi -->
            <div class="grid grid-cols-2 gap-4 py-4 text-xs border-b border-slate-100 dark:border-slate-700/60">
                <div>
                    <span class="text-slate-400 block">Nomor Invoice</span>
                    <span class="font-mono font-bold text-slate-900 dark:text-slate-100 text-sm">{{ $income->invoice_number }}</span>
                </div>
                <div class="text-right">
                    <span class="text-slate-400 block">Waktu Transaksi</span>
                    <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $income->transaction_date->format('d F Y, H:i') }}</span>
                </div>
                <div>
                    <span class="text-slate-400 block">Kasir / Operator</span>
                    <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $income->user->name ?? 'Kasir' }}</span>
                </div>
                <div class="text-right">
                    <span class="text-slate-400 block">Metode Pembayaran</span>
                    <span class="font-semibold text-slate-800 dark:text-slate-200 uppercase">{{ $income->payment_method }}</span>
                </div>
            </div>

            <!-- Detail Item Transaksi -->
            <div class="py-4 border-b border-slate-100 dark:border-slate-700/60">
                <table class="w-full text-xs text-left">
                    <thead class="text-slate-400 uppercase font-semibold border-b border-slate-100 dark:border-slate-700/60">
                        <tr>
                            <th class="py-2">Deskripsi</th>
                            <th class="py-2 text-center">Qty</th>
                            <th class="py-2 text-right">Harga</th>
                            <th class="py-2 text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 dark:divide-slate-800 font-medium">
                        @forelse($income->items as $item)
                            <tr>
                                <td class="py-2.5 text-slate-800 dark:text-slate-200">
                                    {{ $item->product_name }}
                                </td>
                                <td class="py-2.5 text-center text-slate-600 dark:text-slate-400">
                                    {{ $item->quantity }}
                                </td>
                                <td class="py-2.5 text-right text-slate-600 dark:text-slate-400">
                                    @rupiah($item->unit_price)
                                </td>
                                <td class="py-2.5 text-right font-semibold text-slate-900 dark:text-slate-100">
                                    @rupiah($item->subtotal)
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="py-2.5 text-slate-800 dark:text-slate-200">
                                    {{ $income->category->name ?? 'Pemasukan Kas' }}
                                    @if($income->notes)
                                        <span class="block text-[11px] text-slate-400">{{ $income->notes }}</span>
                                    @endif
                                </td>
                                <td class="py-2.5 text-center text-slate-600 dark:text-slate-400">1</td>
                                <td class="py-2.5 text-right text-slate-600 dark:text-slate-400">@rupiah($income->total_amount)</td>
                                <td class="py-2.5 text-right font-semibold text-slate-900 dark:text-slate-100">@rupiah($income->total_amount)</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Total Amount -->
            <div class="pt-4 flex items-center justify-between text-base font-bold">
                <span class="text-slate-900 dark:text-slate-100">Total Pembayaran</span>
                <span class="text-emerald-600 text-lg">@rupiah($income->total_amount)</span>
            </div>

            @if($income->notes && $income->items->isNotEmpty())
                <div class="mt-4 p-3 bg-slate-50 dark:bg-slate-750 rounded-xl text-xs text-slate-500">
                    <span class="font-semibold block text-slate-700 dark:text-slate-300">Catatan:</span>
                    {{ $income->notes }}
                </div>
            @endif

            <div class="mt-6 pt-4 text-center border-t border-dashed border-slate-200 dark:border-slate-700 text-xs text-slate-400">
                Terima kasih atas kunjungan dan transaksi Anda di KonterKu!
            </div>
        </x-card>
    </div>
</x-layouts.app>
