<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Keuangan & Stok {{ $shopName }} ({{ $startDate }} s.d. {{ $endDate }})</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #1e293b;
            margin: 0;
            padding: 24px;
            font-size: 11px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #334155;
            padding-bottom: 12px;
            margin-bottom: 16px;
        }
        .header h1 {
            margin: 0;
            font-size: 20px;
        }
        .header p {
            margin: 3px 0;
            color: #64748b;
        }
        .summary-box {
            display: flex;
            justify-content: space-between;
            margin-bottom: 18px;
            gap: 10px;
        }
        .summary-item {
            flex: 1;
            padding: 8px 10px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            text-align: center;
        }
        .summary-item span {
            display: block;
            color: #64748b;
            font-size: 10px;
            margin-bottom: 3px;
            font-weight: bold;
        }
        .summary-item strong {
            font-size: 14px;
            color: #0f172a;
            font-family: monospace;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #cbd5e1;
            padding: 5px 8px;
            text-align: left;
        }
        th {
            background-color: #f1f5f9;
            font-weight: bold;
            font-size: 10px;
            text-transform: uppercase;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-mono { font-family: monospace; }
        .income { color: #16a34a; font-weight: bold; }
        .expense { color: #dc2626; font-weight: bold; }
        .no-print {
            margin-bottom: 16px;
        }
        @media print {
            .no-print { display: none; }
            body { padding: 0; }
        }
    </style>
</head>
<body>
    <div class="no-print">
        <button onclick="window.print()" style="padding: 8px 16px; background: #4f46e5; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: bold;">
            Cetak / Simpan PDF
        </button>
        <button onclick="window.close()" style="padding: 8px 16px; background: #64748b; color: white; border: none; border-radius: 6px; cursor: pointer; margin-left: 8px;">
            Tutup
        </button>
    </div>

    <div class="header">
        <h1>{{ $shopName }}</h1>
        <p>{{ $shopAddress }} | Telp / WA: {{ $shopPhone }}</p>
        <p style="font-weight: bold; margin-top: 6px; color: #334155; text-transform: uppercase;">
            @if(($tab ?? request('tab')) === 'laba_rugi')
                LAPORAN LABA KEUNTUNGAN TOKO
            @elseif(($tab ?? request('tab')) === 'kasbon')
                BUKU LAPORAN PIUTANG KASBON PELANGGAN
            @else
                LAPORAN KEUANGAN DAN ARUS KAS
            @endif
        </p>
        <p>Periode: {{ \Carbon\Carbon::parse($startDate)->format('d F Y') }} s.d. {{ \Carbon\Carbon::parse($endDate)->format('d F Y') }}</p>
    </div>

    @if(($tab ?? request('tab')) === 'kasbon')
        <!-- Ringkasan Khusus Kasbon / Piutang -->
        <div class="summary-box">
            <div class="summary-item">
                <span>TOTAL KASBON DICATAT</span>
                <strong style="color: #4f46e5;">Rp {{ number_format($kasbonsSummary['total_amount'], 0, ',', '.') }}</strong>
                <small style="display: block; color: #64748b; font-size: 9px;">({{ $kasbonsSummary['total_count'] }} transaksi)</small>
            </div>
            <div class="summary-item">
                <span>PIUTANG BELUM BAYAR</span>
                <strong style="color: #dc2626;">Rp {{ number_format($kasbonsSummary['unpaid_amount'], 0, ',', '.') }}</strong>
                <small style="display: block; color: #64748b; font-size: 9px;">({{ $kasbonsSummary['unpaid_count'] }} belum lunas)</small>
            </div>
            <div class="summary-item">
                <span>PIUTANG SUDAH LUNAS</span>
                <strong style="color: #16a34a;">Rp {{ number_format($kasbonsSummary['paid_amount'], 0, ',', '.') }}</strong>
                <small style="display: block; color: #64748b; font-size: 9px;">({{ $kasbonsSummary['paid_count'] }} terselesaikan)</small>
            </div>
            <div class="summary-item">
                <span>LEWAT JATUH TEMPO</span>
                <strong style="color: #d97706;">{{ $kasbonsSummary['overdue_count'] }}</strong>
                <small style="display: block; color: #64748b; font-size: 9px;">Perlu segera ditagih</small>
            </div>
        </div>

        <h3 style="margin-bottom: 6px; font-size: 12px;">Rincian Catatan Buku Piutang Kasbon Pelanggan</h3>
        <table>
            <thead>
                <tr>
                    <th style="width: 25px;" class="text-center">No</th>
                    <th>Tanggal Kasbon</th>
                    <th>Nama Pelanggan</th>
                    <th>No HP / WA</th>
                    <th>Barang / Layanan</th>
                    <th class="text-center">Qty</th>
                    <th class="text-right">Nominal Kasbon</th>
                    <th class="text-center">Jatuh Tempo</th>
                    <th class="text-center">Status</th>
                    <th>Tgl Pelunasan</th>
                    <th>Petugas</th>
                </tr>
            </thead>
            <tbody>
                @forelse($kasbons as $index => $k)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ \Carbon\Carbon::parse($k->debt_date)->format('d/m/Y') }}</td>
                        <td><strong>{{ $k->customer_name }}</strong></td>
                        <td class="font-mono">{{ $k->customer_phone ?? '-' }}</td>
                        <td>{{ $k->product_name ?? ($k->description ?? 'Kasbon') }}</td>
                        <td class="text-center font-mono">{{ $k->quantity ?? 1 }}</td>
                        <td class="text-right font-mono font-bold">Rp {{ number_format($k->amount, 0, ',', '.') }}</td>
                        <td class="text-center">
                            @if($k->due_date)
                                {{ \Carbon\Carbon::parse($k->due_date)->format('d/m/Y') }}
                                @if($k->is_overdue)
                                    <small style="display: block; color: #dc2626; font-weight: bold;">(Jatuh Tempo)</small>
                                @endif
                            @else
                                -
                            @endif
                        </td>
                        <td class="text-center">
                            @if($k->is_paid)
                                <strong style="color: #16a34a;">LUNAS</strong>
                            @else
                                <strong style="color: #d97706;">BELUM LUNAS</strong>
                            @endif
                        </td>
                        <td>{{ $k->paid_at ? \Carbon\Carbon::parse($k->paid_at)->format('d/m/Y H:i') : '-' }}</td>
                        <td>{{ $k->user->name ?? 'Kasir' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="11" class="text-center" style="padding: 12px; color: #94a3b8;">
                            Tidak ada data catatan kasbon pada periode ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    @else
        <!-- Ringkasan Keuangan Utama -->
        <div class="summary-box">
            <div class="summary-item">
                <span>TOTAL PEMASUKAN</span>
                <strong style="color: #16a34a;">Rp {{ number_format($financialSummary['total_income'], 0, ',', '.') }}</strong>
                <small style="display: block; color: #64748b; font-size: 9px;">({{ $financialSummary['income_count'] }} transaksi)</small>
            </div>
            <div class="summary-item">
                <span>MODAL KULAKAN (HPP)</span>
                <strong style="color: #d97706;">Rp {{ number_format($financialSummary['total_cogs'], 0, ',', '.') }}</strong>
                <small style="display: block; color: #64748b; font-size: 9px;">({{ $financialSummary['cost_coverage_percentage'] }}% terdata)</small>
            </div>
            <div class="summary-item">
                <span>TOTAL PENGELUARAN</span>
                <strong style="color: #dc2626;">Rp {{ number_format($financialSummary['total_expense'], 0, ',', '.') }}</strong>
                <small style="display: block; color: #64748b; font-size: 9px;">({{ $financialSummary['expense_count'] }} transaksi)</small>
            </div>
            <div class="summary-item">
                <span>KAS LACI BERSIH</span>
                <strong>Rp {{ number_format($financialSummary['net_cashflow'], 0, ',', '.') }}</strong>
                <small style="display: block; color: #64748b; font-size: 9px;">Pemasukan - Beban Kas</small>
            </div>
            <div class="summary-item">
                <span>LABA KEUNTUNGAN BERSIH</span>
                <strong style="color: #16a34a;">Rp {{ number_format($financialSummary['net_profit'], 0, ',', '.') }}</strong>
                <small style="display: block; color: #64748b; font-size: 9px;">Omzet &minus; Modal HPP &minus; Beban</small>
            </div>
        </div>

        <!-- Catatan Integritas HPP -->
        @if(!$financialSummary['has_complete_cost_data'])
            <p style="background: #fffbeb; border: 1px solid #fef3c7; color: #92400e; padding: 6px 10px; border-radius: 6px; font-size: 10px; margin-bottom: 15px;">
                * <strong>Catatan Integritas:</strong> Sebagian item transaksi penjualan tercatat tanpa data harga modal kulakan (Rp 0). Nilai laba keuntungan bersih di atas merupakan estimasi transparan dari modal yang tercatat.
            </p>
        @endif

        @if(($tab ?? request('tab')) === 'laba_rugi')
            <table style="margin-bottom: 20px;">
                <thead>
                    <tr>
                        <th colspan="2" style="background-color: #0f172a; color: white; text-transform: uppercase;">Struktur Pembukuan Laba Keuntungan</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>A. Pendapatan Penjualan Kasir (Revenue)</strong> ({{ $financialSummary['income_count'] }} Transaksi)</td>
                        <td class="text-right font-mono font-bold">Rp {{ number_format($financialSummary['total_income'], 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td><strong>B. Beban Pokok Penjualan (Modal Kulakan HPP)</strong> ({{ $financialSummary['cost_coverage_percentage'] }}% Terdata)</td>
                        <td class="text-right font-mono font-bold expense">- Rp {{ number_format($financialSummary['total_cogs'], 0, ',', '.') }}</td>
                    </tr>
                    <tr style="background-color: #f8fafc;">
                        <td><strong>C. Laba Kotor Penjualan (Gross Profit = A &minus; B)</strong></td>
                        <td class="text-right font-mono font-bold income">Rp {{ number_format($financialSummary['gross_profit'], 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td><strong>D. Beban Pengeluaran Toko & Operasional</strong> ({{ $financialSummary['expense_count'] }} Catatan)</td>
                        <td class="text-right font-mono font-bold expense">- Rp {{ number_format($financialSummary['total_expense'], 0, ',', '.') }}</td>
                    </tr>
                    <tr style="background-color: #f0fdf4;">
                        <td><strong>E. Laba Keuntungan Bersih Toko (Net Profit = C &minus; D)</strong></td>
                        <td class="text-right font-mono font-bold income" style="font-size: 13px;">Rp {{ number_format($financialSummary['net_profit'], 0, ',', '.') }}</td>
                    </tr>
                </tbody>
            </table>
        @endif

        <h3 style="margin-bottom: 6px; font-size: 12px;">Rincian Transaksi Pemasukan (Kas Masuk)</h3>
        <table>
            <thead>
                <tr>
                    <th style="width: 25px;" class="text-center">No</th>
                    <th>No. Faktur</th>
                    <th>Waktu</th>
                    <th>Item Produk / Keterangan</th>
                    <th>Kategori</th>
                    <th>Metode</th>
                    <th class="text-right">Modal HPP</th>
                    <th class="text-right">Omzet</th>
                    <th class="text-right">Laba</th>
                </tr>
            </thead>
            <tbody>
                @forelse($incomes as $index => $tx)
                    @php
                        $costPrice = $tx->items->sum(fn($it) => $it->cost_price * $it->quantity);
                        $profit = $tx->total_amount - $costPrice;
                    @endphp
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td class="font-mono">{{ $tx->invoice_number }}</td>
                        <td>{{ \Carbon\Carbon::parse($tx->transaction_date)->format('d/m/Y H:i') }}</td>
                        <td>
                            @if($tx->items->isNotEmpty())
                                @foreach($tx->items as $it)
                                    <span>{{ $it->product_name }} ({{ $it->quantity }}x)</span>
                                @endforeach
                            @else
                                {{ $tx->notes ?? ($tx->category->name ?? '-') }}
                            @endif
                        </td>
                        <td>{{ $tx->category->name ?? '-' }}</td>
                        <td class="text-center">{{ strtoupper($tx->payment_method) }}</td>
                        <td class="text-right font-mono">Rp {{ number_format($costPrice, 0, ',', '.') }}</td>
                        <td class="text-right font-mono font-bold">Rp {{ number_format($tx->total_amount, 0, ',', '.') }}</td>
                        <td class="text-right font-mono income">+ Rp {{ number_format($profit, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center" style="padding: 12px; color: #94a3b8;">
                            Tidak ada transaksi pemasukan pada periode ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <h3 style="margin-top: 20px; margin-bottom: 6px; font-size: 12px;">Rincian Catatan Pengeluaran (Kas Keluar)</h3>
        <table>
            <thead>
                <tr>
                    <th style="width: 25px;" class="text-center">No</th>
                    <th>No. Invoice</th>
                    <th>Waktu</th>
                    <th>Kategori Pengeluaran</th>
                    <th>Keterangan / Rincian</th>
                    <th>Metode</th>
                    <th class="text-right">Nominal Beban</th>
                </tr>
            </thead>
            <tbody>
                @forelse($expenses as $index => $exp)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td class="font-mono">{{ $exp->invoice_number }}</td>
                        <td>{{ \Carbon\Carbon::parse($exp->transaction_date)->format('d/m/Y H:i') }}</td>
                        <td>{{ $exp->category->name ?? 'Operasional' }}</td>
                        <td>{{ $exp->notes }}</td>
                        <td class="text-center">{{ strtoupper($exp->payment_method) }}</td>
                        <td class="text-right font-mono expense">- Rp {{ number_format($exp->total_amount, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center" style="padding: 12px; color: #94a3b8;">
                            Tidak ada transaksi pengeluaran pada periode ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    @endif

    <div style="margin-top: 30px; display: flex; justify-content: space-between; text-align: center;">
        <div style="width: 200px;">
            <p>Dicetak pada:<br>{{ now()->format('d/m/Y H:i') }} WIB</p>
        </div>
        <div style="width: 200px;">
            <p>Penanggung Jawab Toko,</p>
            <br><br><br>
            <p>( ........................................ )</p>
        </div>
    </div>
</body>
</html>
