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
     * Hitung ringkasan keuangan (pemasukan, pengeluaran, keuntungan kotor/bersih).
     */
    public function getFinancialSummary(?string $startDate = null, ?string $endDate = null): array
    {
        $start = $startDate ? Carbon::parse($startDate)->startOfDay() : Carbon::now()->startOfMonth();
        $end = $endDate ? Carbon::parse($endDate)->endOfDay() : Carbon::now()->endOfDay();

        $transactions = Transaction::whereBetween('transaction_date', [$start, $end])->get();

        $totalIncome = $transactions->where('type', 'pemasukan')->sum('total_amount');
        $totalExpense = $transactions->where('type', 'pengeluaran')->sum('total_amount');
        $netIncome = $totalIncome - $totalExpense;

        // Hitung estimasi keuntungan dari penjualan item produk (subtotal - (cost_price * qty))
        $salesProfit = TransactionItem::whereHas('transaction', function ($q) use ($start, $end) {
            $q->where('type', 'pemasukan')
              ->whereBetween('transaction_date', [$start, $end]);
        })->selectRaw('SUM(subtotal - (cost_price * quantity)) as total_profit')
          ->value('total_profit') ?? 0;

        $incomeCount = $transactions->where('type', 'pemasukan')->count();
        $expenseCount = $transactions->where('type', 'pengeluaran')->count();

        return [
            'start_date' => $start->toDateString(),
            'end_date' => $end->toDateString(),
            'total_income' => (float) $totalIncome,
            'total_expense' => (float) $totalExpense,
            'net_income' => (float) $netIncome,
            'sales_profit' => (float) $salesProfit,
            'income_count' => $incomeCount,
            'expense_count' => $expenseCount,
            'total_transactions' => $incomeCount + $expenseCount,
        ];
    }

    /**
     * Dapatkan tren pemasukan & pengeluaran beberapa hari terakhir (untuk chart/grafik dashboard).
     */
    public function getDailyTrend(int $days = 7): array
    {
        $startDate = Carbon::now()->subDays($days - 1)->startOfDay();
        $endDate = Carbon::now()->endOfDay();

        $trend = [];
        $current = $startDate->copy();

        while ($current <= $endDate) {
            $dateStr = $current->toDateString();
            $dayLabel = $current->locale('id')->isoFormat('D MMM');

            $income = Transaction::where('type', 'pemasukan')
                ->whereDate('transaction_date', $dateStr)
                ->sum('total_amount');

            $expense = Transaction::where('type', 'pengeluaran')
                ->whereDate('transaction_date', $dateStr)
                ->sum('total_amount');

            $trend[] = [
                'date' => $dateStr,
                'label' => $dayLabel,
                'income' => (float) $income,
                'expense' => (float) $expense,
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
