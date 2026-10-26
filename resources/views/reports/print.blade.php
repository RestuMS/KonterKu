<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Keuangan KonterKu ({{ $startDate }} s.d. {{ $endDate }})</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #1e293b;
            margin: 0;
            padding: 24px;
            font-size: 12px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #334155;
            padding-bottom: 12px;
            margin-bottom: 20px;
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
            margin-bottom: 20px;
            gap: 12px;
        }
        .summary-item {
            flex: 1;
            padding: 10px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            text-align: center;
        }
        .summary-item span {
            display: block;
            color: #64748b;
            font-size: 11px;
            margin-bottom: 4px;
        }
        .summary-item strong {
            font-size: 15px;
            color: #0f172a;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #cbd5e1;
            padding: 6px 10px;
            text-align: left;
        }
        th {
            background-color: #f1f5f9;
            font-weight: bold;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
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
        <p>{{ $shopAddress }} | Telp: {{ $shopPhone }}</p>
        <p style="font-weight: bold; margin-top: 6px; color: #334155;">LAPORAN KEUANGAN DAN ARUS KAS</p>
        <p>Periode: {{ \Carbon\Carbon::parse($startDate)->format('d F Y') }} s.d. {{ \Carbon\Carbon::parse($endDate)->format('d F Y') }}</p>
    </div>

    <div class="summary-box">
        <div class="summary-item">
            <span>TOTAL PEMASUKAN</span>
            <strong style="color: #16a34a;">Rp {{ number_format($summary['total_income'], 0, ',', '.') }}</strong>
            <small style="display: block; color: #64748b;">({{ $summary['income_count'] }} transaksi)</small>
        </div>
        <div class="summary-item">
            <span>TOTAL PENGELUARAN</span>
            <strong style="color: #dc2626;">Rp {{ number_format($summary['total_expense'], 0, ',', '.') }}</strong>
            <small style="display: block; color: #64748b;">({{ $summary['expense_count'] }} transaksi)</small>
        </div>
        <div class="summary-item">
            <span>ARUS KAS BERSIH</span>
            <strong>Rp {{ number_format($summary['net_income'], 0, ',', '.') }}</strong>
        </div>
        <div class="summary-item">
            <span>ESTIMASI LABA PENJUALAN</span>
            <strong style="color: #2563eb;">Rp {{ number_format($summary['sales_profit'], 0, ',', '.') }}</strong>
        </div>
    </div>

    <h3 style="margin-bottom: 6px;">Rincian Transaksi</h3>
    <table>
        <thead>
            <tr>
                <th style="width: 30px;" class="text-center">No</th>
                <th>No. Invoice</th>
                <th>Tanggal</th>
                <th>Tipe</th>
                <th>Kategori</th>
                <th>Keterangan / Rincian</th>
                <th>Metode</th>
                <th class="text-right">Nominal</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $index => $tx)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td style="font-family: monospace;">{{ $tx->invoice_number }}</td>
                    <td>{{ $tx->transaction_date->format('d/m/Y H:i') }}</td>
                    <td class="text-center">
                        {{ $tx->type === 'pemasukan' ? 'Pemasukan' : 'Pengeluaran' }}
                    </td>
                    <td>{{ $tx->category->name ?? '-' }}</td>
                    <td>
                        @if($tx->items->isNotEmpty())
                            @foreach($tx->items as $it)
                                <span>{{ $it->product_name }} ({{ $it->quantity }}x)</span>
                            @endforeach
                        @else
                            {{ $tx->notes ?? '-' }}
                        @endif
                    </td>
                    <td class="text-center">{{ strtoupper($tx->payment_method) }}</td>
                    <td class="text-right {{ $tx->type === 'pemasukan' ? 'income' : 'expense' }}">
                        {{ $tx->type === 'pemasukan' ? '+' : '-' }} Rp {{ number_format($tx->total_amount, 0, ',', '.') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center" style="padding: 20px; color: #94a3b8;">
                        Tidak ada transaksi pada periode ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 30px; display: flex; justify-content: space-between; text-align: center;">
        <div style="width: 200px;">
            <p>Dicetak pada:<br>{{ now()->format('d/m/Y H:i') }}</p>
        </div>
        <div style="width: 200px;">
            <p>Penanggung Jawab / Admin,</p>
            <br><br><br>
            <p>( ........................................ )</p>
        </div>
    </div>
</body>
</html>
