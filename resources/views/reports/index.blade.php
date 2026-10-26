<x-layouts.app title="Laporan Keuangan & Transaksi">
    <x-slot:header>
        Laporan Keuangan & Penjualan
    </x-slot:header>

    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-900 dark:text-white">Laporan Arus Kas & Laba Rugi</h2>
            <p class="text-xs text-slate-500 mt-0.5">Analisis ringkasan pemasukan, pengeluaran, margin keuntungan, dan metode pembayaran.</p>
        </div>
        <div class="flex items-center gap-2.5">
            <x-button
                href="{{ route('reports.print', ['start_date' => $startDate, 'end_date' => $endDate]) }}"
                target="_blank"
                variant="secondary"
                class="font-semibold shadow-xs"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Cetak / Ekspor Laporan
            </x-button>
        </div>
    </div>

    <!-- Date Range Filter -->
    <x-card class="mb-6">
        <form method="GET" action="{{ route('reports.index') }}" class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-4 gap-3 items-end">
            <div>
                <x-input
                    label="Dari Tanggal"
                    name="start_date"
                    type="date"
                    value="{{ $startDate }}"
                    required
                />
            </div>
            <div>
                <x-input
                    label="Sampai Tanggal"
                    name="end_date"
                    type="date"
                    value="{{ $endDate }}"
                    required
                />
            </div>
            <div class="flex items-center gap-2">
                <x-button type="submit" variant="primary" class="w-full sm:w-auto">
                    Terapkan Rentang
                </x-button>
                <a href="{{ route('reports.index') }}" class="p-2 text-slate-400 hover:text-slate-600" title="Bulan Ini">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                </a>
            </div>
        </form>
    </x-card>

    <!-- Report Metric Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <!-- Total Pemasukan -->
        <x-stat-card
            title="Total Pemasukan"
            :value="'Rp ' . number_format($summary['total_income'], 0, ',', '.')"
            :subtitle="$summary['income_count'] . ' transaksi penjualan'"
            color="emerald"
        >
            <x-slot:icon>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/></svg>
            </x-slot:icon>
        </x-stat-card>

        <!-- Total Pengeluaran -->
        <x-stat-card
            title="Total Pengeluaran"
            :value="'Rp ' . number_format($summary['total_expense'], 0, ',', '.')"
            :subtitle="$summary['expense_count'] . ' transaksi pengeluaran'"
            color="rose"
        >
            <x-slot:icon>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"/></svg>
            </x-slot:icon>
        </x-stat-card>

        <!-- Saldo Kas Bersih -->
        <x-stat-card
            title="Arus Kas Bersih"
            :value="'Rp ' . number_format($summary['net_income'], 0, ',', '.')"
            subtitle="Pemasukan dikurangi pengeluaran"
            :color="$summary['net_income'] >= 0 ? 'indigo' : 'rose'"
        >
            <x-slot:icon>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </x-slot:icon>
        </x-stat-card>

        <!-- Estimasi Laba Produk -->
        <x-stat-card
            title="Estimasi Laba Produk"
            :value="'Rp ' . number_format($summary['sales_profit'], 0, ',', '.')"
            subtitle="Selisih harga jual dan modal"
            color="sky"
        >
            <x-slot:icon>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            </x-slot:icon>
        </x-stat-card>
    </div>

    <!-- Payment Methods Distribution -->
    <div class="mb-8">
        <x-card title="Rekapitulasi Metode Pembayaran">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                @php
                    $methods = ['cash' => 'Tunai (Cash)', 'transfer' => 'Transfer Bank', 'qris' => 'QRIS / E-Wallet'];
                @endphp
                @foreach($methods as $key => $label)
                    @php
                        $in = $paymentMethods->where('payment_method', $key)->where('type', 'pemasukan')->sum('total');
                        $out = $paymentMethods->where('payment_method', $key)->where('type', 'pengeluaran')->sum('total');
                    @endphp
                    <div class="p-4 rounded-xl border border-slate-100 dark:border-slate-700/60 bg-slate-50/60 dark:bg-slate-750/30">
                        <span class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase">{{ $label }}</span>
                        <div class="mt-2 space-y-1 text-xs">
                            <div class="flex justify-between">
                                <span class="text-slate-500">Masuk:</span>
                                <span class="font-semibold text-emerald-600">+ @rupiah($in)</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">Keluar:</span>
                                <span class="font-semibold text-rose-600">- @rupiah($out)</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </x-card>
    </div>

    <!-- Itemized Transactions Table -->
    <x-card title="Rincian Transaksi Periode Ini" subtitle="Daftar semua transaksi yang terjadi dalam rentang waktu yang dipilih">
        <div class="overflow-x-auto -mx-6">
            <table class="min-w-full divide-y divide-slate-200/80 dark:divide-slate-700 text-left text-xs">
                <thead class="bg-slate-50/70 dark:bg-slate-850 text-slate-500 uppercase font-semibold tracking-wider">
                    <tr>
                        <th class="px-6 py-3.5">Invoice</th>
                        <th class="px-6 py-3.5">Tanggal</th>
                        <th class="px-6 py-3.5">Jenis</th>
                        <th class="px-6 py-3.5">Kategori</th>
                        <th class="px-6 py-3.5">Keterangan / Rincian</th>
                        <th class="px-6 py-3.5">Metode</th>
                        <th class="px-6 py-3.5 text-right">Nominal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60 font-medium">
                    @forelse($transactions as $tx)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-750/30 transition-colors">
                            <td class="px-6 py-3.5 font-mono font-bold text-slate-900 dark:text-slate-100">
                                {{ $tx->invoice_number }}
                            </td>
                            <td class="px-6 py-3.5 text-slate-500">
                                @tanggalWaktu($tx->transaction_date)
                            </td>
                            <td class="px-6 py-3.5">
                                @if($tx->type === 'pemasukan')
                                    <x-badge color="emerald">Pemasukan</x-badge>
                                @else
                                    <x-badge color="rose">Pengeluaran</x-badge>
                                @endif
                            </td>
                            <td class="px-6 py-3.5">
                                <x-badge color="sky">{{ $tx->category->name ?? 'Umum' }}</x-badge>
                            </td>
                            <td class="px-6 py-3.5 text-slate-700 dark:text-slate-300">
                                @if($tx->items->isNotEmpty())
                                    @foreach($tx->items as $it)
                                        <span>{{ $it->product_name }} ({{ $it->quantity }}x)</span>
                                    @endforeach
                                @else
                                    {{ $tx->notes ?? '-' }}
                                @endif
                            </td>
                            <td class="px-6 py-3.5 uppercase font-semibold text-slate-600 dark:text-slate-400">
                                <x-badge color="slate">{{ $tx->payment_method }}</x-badge>
                            </td>
                            <td class="px-6 py-3.5 text-right font-bold {{ $tx->type === 'pemasukan' ? 'text-emerald-600' : 'text-rose-600' }}">
                                {{ $tx->type === 'pemasukan' ? '+' : '-' }} @rupiah($tx->total_amount)
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4">
                                <x-empty-state
                                    title="Tidak Ada Transaksi Ditemukan"
                                    description="Tidak ditemukan riwayat transaksi pemasukan maupun pengeluaran pada rentang tanggal yang dipilih."
                                />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $transactions->links() }}
        </div>
    </x-card>
</x-layouts.app>
