<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportService
{
    /**
     * Mesin hitung tunggal (Single Source of Truth) untuk Dashboard & Laporan Keuangan.
     * Menjamin perhitungan Omzet, HPP, Laba Kotor, Beban Operasional, Belanja Modal Kulakan,
     * Laba Bersih Toko, serta Arus Kas Laci Tunai 100% seragam di seluruh aplikasi.
     */
    public function calculateFinancialSummary(Carbon $start, Carbon $end): array
    {
        $transactions = Transaction::with(['category', 'items'])
            ->whereBetween('transaction_date', [$start, $end])
            ->get();

        $incomeTransactions = $transactions->where('type', 'pemasukan');
        $expenseTransactions = $transactions->where('type', 'pengeluaran');

        $totalIncome = (float) $incomeTransactions->sum('total_amount');
        $totalExpense = (float) $expenseTransactions->sum('total_amount');
        $netCashflow = $totalIncome - $totalExpense;

        // Pisahkan Belanja Modal / Kulakan Stok & Saldo Server (Aset Toko) vs Beban Operasional Toko
        $kulakanExpense = 0.0;
        $operationalExpense = 0.0;
        $kulakanCount = 0;
        $operationalCount = 0;

        $paymentMethodsExpense = [
            'cash' => 0.0,
            'qris' => 0.0,
            'transfer' => 0.0,
        ];

        $cashDrawerExpenseOut = 0.0;
        $expenseCategoryMap = [];

        foreach ($expenseTransactions as $expTx) {
            $amount = (float) $expTx->total_amount;
            $isKulakan = $expTx->isKulakan();

            if ($isKulakan) {
                $kulakanExpense += $amount;
                $kulakanCount++;
            } else {
                $operationalExpense += $amount;
                $operationalCount++;
            }

            $method = strtolower($expTx->payment_method ?? 'cash');
            if (!isset($paymentMethodsExpense[$method])) {
                $paymentMethodsExpense[$method] = 0.0;
            }
            $paymentMethodsExpense[$method] += $amount;

            if ($method === 'cash') {
                $cashDrawerExpenseOut += $amount;
            }

            $catKey = ($expTx->category_id ?? 0) . '_' . ($isKulakan ? '1' : '0');
            if (!isset($expenseCategoryMap[$catKey])) {
                $expenseCategoryMap[$catKey] = (object) [
                    'category_id' => $expTx->category_id,
                    'category' => $expTx->category,
                    'total' => 0.0,
                    'count' => 0,
                    'is_kulakan' => $isKulakan,
                ];
            }
            $expenseCategoryMap[$catKey]->total += $amount;
            $expenseCategoryMap[$catKey]->count++;
        }

        // Hitung HPP (COGS), Kelengkapan Modal, Metode Pembayaran Masuk, & Pergerakan Kas Laci Tunai
        $totalCogs = 0.0;
        $totalSaleItemCount = 0;
        $itemsWithValidCostCount = 0;

        $paymentMethodsIncome = [
            'cash' => 0.0,
            'qris' => 0.0,
            'transfer' => 0.0,
        ];

        $cashDrawerIn = 0.0;
        $cashDrawerTarikTunaiOut = 0.0;

        foreach ($incomeTransactions as $incTx) {
            $txAmount = (float) $incTx->total_amount;
            $txCogs = 0.0;

            foreach ($incTx->items as $it) {
                $itemCost = (float) $it->cost_price * (int) $it->quantity;
                $txCogs += $itemCost;
                $totalSaleItemCount++;
                if ((float) $it->cost_price > 0) {
                    $itemsWithValidCostCount++;
                }
            }
            $totalCogs += $txCogs;

            $method = strtolower($incTx->payment_method ?? 'cash');
            if (!isset($paymentMethodsIncome[$method])) {
                $paymentMethodsIncome[$method] = 0.0;
            }
            $paymentMethodsIncome[$method] += $txAmount;

            // Logika Akurat Pergerakan Uang Fisik di Laci Kasir (Cash Drawer):
            // 1. Transaksi Tarik Tunai (Cash Out):
            //    Kasir menyerahkan uang tunai pokok (txCogs) dari laci ke pelanggan -> keluar laci.
            //    Jika metode bayar 'cash' (potong admin di tempat), biaya admin (fee) masuk ke laci.
            //    Jika metode bayar 'transfer'/'qris' (pokok+admin ditransfer ke rekening), tidak ada tunai masuk laci.
            // 2. Transaksi Penjualan / Transfer Kirim Uang / Pelunasan Kasbon:
            //    Jika bayar tunai ('cash') -> seluruh total_amount masuk ke laci kasir.
            //    Jika bayar QRIS / Transfer -> masuk ke rekening/e-wallet, BUKAN laci tunai.
            if ($incTx->isTarikTunai()) {
                if ($txCogs > 0) {
                    $cashDrawerTarikTunaiOut += $txCogs;
                }
                if ($method === 'cash') {
                    $adminFee = max(0.0, $txAmount - $txCogs);
                    $cashDrawerIn += $adminFee;
                }
            } else {
                if ($method === 'cash') {
                    $cashDrawerIn += $txAmount;
                }
            }
        }

        $cashDrawerOut = $cashDrawerExpenseOut + $cashDrawerTarikTunaiOut;
        $cashDrawerNet = $cashDrawerIn - $cashDrawerOut;

        $grossProfit = $totalIncome - $totalCogs;
        // Laba Bersih Toko = Laba Kotor Penjualan - Beban Operasional Toko
        // (Belanja modal kulakan stok/saldo tidak memotong laba karena modal barang yang terjual sudah terpotong di HPP)
        $netProfit = $grossProfit - $operationalExpense;

        $hasCompleteCostData = $totalSaleItemCount === 0 || ($totalSaleItemCount > 0 && $itemsWithValidCostCount === $totalSaleItemCount);
        $costCoveragePercentage = $totalSaleItemCount > 0 ? round(($itemsWithValidCostCount / $totalSaleItemCount) * 100, 1) : 100;

        $expensesByCategory = collect(array_values($expenseCategoryMap))
            ->sortByDesc('total')
            ->values();

        return [
            'total_income' => $totalIncome,
            'total_expense' => $totalExpense,
            'operational_expense' => $operationalExpense,
            'kulakan_expense' => $kulakanExpense,
            'operational_count' => $operationalCount,
            'kulakan_count' => $kulakanCount,
            'net_cashflow' => $netCashflow,
            'cash_drawer_in' => $cashDrawerIn,
            'cash_drawer_out' => $cashDrawerOut,
            'cash_drawer_expense_out' => $cashDrawerExpenseOut,
            'cash_drawer_tarik_tunai_out' => $cashDrawerTarikTunaiOut,
            'cash_drawer_net' => $cashDrawerNet,
            'total_cogs' => $totalCogs,
            'gross_profit' => $grossProfit,
            'net_profit' => $netProfit,
            'income_count' => $incomeTransactions->count(),
            'expense_count' => $expenseTransactions->count(),
            'total_sale_item_count' => $totalSaleItemCount,
            'items_with_valid_cost_count' => $itemsWithValidCostCount,
            'has_complete_cost_data' => $hasCompleteCostData,
            'cost_coverage_percentage' => $costCoveragePercentage,
            'payment_methods_income' => $paymentMethodsIncome,
            'payment_methods_expense' => $paymentMethodsExpense,
            'expenses_by_category' => $expensesByCategory,
        ];
    }

    /**
     * Hitung ringkasan keuangan (pemasukan, pengeluaran, keuntungan kotor/bersih).
     */
    public function getFinancialSummary(?string $startDate = null, ?string $endDate = null): array
    {
        $start = $startDate ? Carbon::parse($startDate)->startOfDay() : Carbon::now()->startOfMonth();
        $end = $endDate ? Carbon::parse($endDate)->endOfDay() : Carbon::now()->endOfDay();

        $summary = $this->calculateFinancialSummary($start, $end);

        return [
            'start_date' => $start->toDateString(),
            'end_date' => $end->toDateString(),
            'total_income' => $summary['total_income'],
            'total_expense' => $summary['total_expense'],
            'net_income' => $summary['net_cashflow'],
            'sales_profit' => $summary['gross_profit'],
            'net_profit' => $summary['net_profit'],
            'cash_drawer_net' => $summary['cash_drawer_net'],
            'income_count' => $summary['income_count'],
            'expense_count' => $summary['expense_count'],
            'total_transactions' => $summary['income_count'] + $summary['expense_count'],
        ];
    }

    /**
     * Dapatkan tren keuangan & laba bersih beberapa hari terakhir (untuk chart/grafik dashboard).
     */
    public function getDailyTrend(int $days = 7): array
    {
        $startDate = Carbon::now()->subDays($days - 1)->startOfDay();
        $endDate = Carbon::now()->endOfDay();

        // Ambil transaksi dalam rentang hari tersebut
        $transactions = Transaction::with('category')
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->select('id', 'type', 'category_id', 'total_amount', 'transaction_date', 'notes')
            ->get();

        $incomeIds = $transactions->where('type', 'pemasukan')->pluck('id');

        // Ambil total modal kulakan (HPP) per transaksi pemasukan
        $itemsByTx = TransactionItem::whereIn('transaction_id', $incomeIds)
            ->select('transaction_id', DB::raw('(cost_price * quantity) as cogs'))
            ->get()
            ->groupBy('transaction_id');

        $cogsByTxId = [];
        foreach ($itemsByTx as $txId => $items) {
            $cogsByTxId[$txId] = $items->sum('cogs');
        }

        $trend = [];
        $current = $startDate->copy();

        while ($current <= $endDate) {
            $dateStr = $current->toDateString();
            $dayLabel = $current->locale('id')->isoFormat('D MMM');

            $dayTxs = $transactions->filter(function ($t) use ($dateStr) {
                return Carbon::parse($t->transaction_date)->toDateString() === $dateStr;
            });

            $income = (float) $dayTxs->where('type', 'pemasukan')->sum('total_amount');
            $dayExpenses = $dayTxs->where('type', 'pengeluaran');
            $totalExpense = (float) $dayExpenses->sum('total_amount');

            $cogs = 0;
            foreach ($dayTxs->where('type', 'pemasukan') as $incTx) {
                $cogs += ($cogsByTxId[$incTx->id] ?? 0);
            }

            $grossProfit = $income - $cogs;

            $operationalExpense = 0.0;
            $kulakanExpense = 0.0;
            foreach ($dayExpenses as $expTx) {
                if ($expTx->isKulakan()) {
                    $kulakanExpense += (float) $expTx->total_amount;
                } else {
                    $operationalExpense += (float) $expTx->total_amount;
                }
            }

            // Laba Bersih = Laba Kotor Penjualan - Beban Operasional Toko
            // (Modal kulakan persediaan tidak memotong laba karena modal barang terjual sudah terhitung di HPP)
            $netProfit = $grossProfit - $operationalExpense;

            $trend[] = [
                'date' => $dateStr,
                'label' => $dayLabel,
                'income' => $income,
                'expense' => $operationalExpense,
                'total_expense' => $totalExpense,
                'operational_expense' => $operationalExpense,
                'kulakan_expense' => $kulakanExpense,
                'cogs' => (float) $cogs,
                'gross_profit' => (float) $grossProfit,
                'net_profit' => (float) $netProfit,
            ];

            $current->addDay();
        }

        return $trend;
    }

    /**
     * Dapatkan rekapitulasi stok barang (termasuk nilai aset modal & harga jual).
     */
    public function getStockSummary(): array
    {
        $products = Product::with('category')->get();

        $totalProducts = $products->count();
        $activeProducts = $products->where('is_active', true)->count();
        $lowStockProducts = $products->filter(fn($p) => $p->stock <= $p->min_stock);

        $totalAssetValue = $products->sum(fn($p) => $p->stock * $p->cost_price);
        $totalPotentialRevenue = $products->sum(fn($p) => $p->stock * $p->selling_price);

        return [
            'total_products' => $totalProducts,
            'active_products' => $activeProducts,
            'low_stock_count' => $lowStockProducts->count(),
            'low_stock_items' => $lowStockProducts->values(),
            'total_asset_value' => (float) $totalAssetValue,
            'total_potential_revenue' => (float) $totalPotentialRevenue,
        ];
    }

    /**
     * Dapatkan produk terlaris berdasarkan kuantitas penjualan.
     */
    public function getTopProducts(int $limit = 5): \Illuminate\Support\Collection
    {
        return TransactionItem::whereHas('transaction', function ($q) {
                $q->where('type', 'pemasukan');
            })
            ->select(
                'product_id',
                'product_name',
                DB::raw('SUM(quantity) as total_sold'),
                DB::raw('SUM(subtotal) as total_revenue'),
                DB::raw('SUM(cost_price * quantity) as total_cost'),
                DB::raw('SUM(subtotal - (cost_price * quantity)) as total_profit')
            )
            ->groupBy('product_id', 'product_name')
            ->orderByDesc('total_sold')
            ->with(['product.category'])
            ->take($limit)
            ->get();
    }
}
