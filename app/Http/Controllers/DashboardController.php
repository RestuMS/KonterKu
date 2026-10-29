<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Services\ReportService;
use Carbon\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(ReportService $reportService): View
    {
        $today = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        // 1. Statistik Hari Ini
        $todayIncome = (float) Transaction::where('type', 'pemasukan')
            ->whereDate('transaction_date', $today)
            ->sum('total_amount');

        $todayExpense = (float) Transaction::where('type', 'pengeluaran')
            ->whereDate('transaction_date', $today)
            ->sum('total_amount');

        $todayTransactionsCount = Transaction::whereDate('transaction_date', $today)->count();

        // Modal HPP & Laba Bersih Hari Ini
        $todayIncomeIds = Transaction::where('type', 'pemasukan')
            ->whereDate('transaction_date', $today)
            ->pluck('id');
        $todayCogs = (float) (TransactionItem::whereIn('transaction_id', $todayIncomeIds)
            ->selectRaw('SUM(cost_price * quantity) as total_cogs')
            ->value('total_cogs') ?? 0);
        $todayGrossProfit = $todayIncome - $todayCogs;
        $todayNetProfit = $todayGrossProfit - $todayExpense;

        // 2. Statistik Bulan Ini
        $monthlyIncome = (float) Transaction::where('type', 'pemasukan')
            ->whereBetween('transaction_date', [$startOfMonth, $endOfMonth])
            ->sum('total_amount');

        $monthlyExpense = (float) Transaction::where('type', 'pengeluaran')
            ->whereBetween('transaction_date', [$startOfMonth, $endOfMonth])
            ->sum('total_amount');

        // Modal HPP & Laba Bersih Bulan Ini
        $monthlyIncomeIds = Transaction::where('type', 'pemasukan')
            ->whereBetween('transaction_date', [$startOfMonth, $endOfMonth])
            ->pluck('id');
        $monthlyCogs = (float) (TransactionItem::whereIn('transaction_id', $monthlyIncomeIds)
            ->selectRaw('SUM(cost_price * quantity) as total_cogs')
            ->value('total_cogs') ?? 0);
        $monthlyGrossProfit = $monthlyIncome - $monthlyCogs;
        $monthlyNetProfit = $monthlyGrossProfit - $monthlyExpense;
        $monthlyNet = $monthlyIncome - $monthlyExpense; // Saldo kas flow

        // 3. Produk & Stok
        $stockSummary = $reportService->getStockSummary();

        // 4. Transaksi Terbaru
        $recentTransactions = Transaction::with(['category', 'user', 'items'])
            ->latest('transaction_date')
            ->take(8)
            ->get();

        // 5. Tren 7 Hari Terakhir (Fokus Laba Bersih Harian)
        $dailyTrend = $reportService->getDailyTrend(7);
        $sevenDaysIncome = (float) array_sum(array_column($dailyTrend, 'income'));
        $sevenDaysExpense = (float) array_sum(array_column($dailyTrend, 'expense'));
        $sevenDaysNetProfit = (float) array_sum(array_column($dailyTrend, 'net_profit'));
        $sevenDaysNet = $sevenDaysIncome - $sevenDaysExpense;
        $avgDailyNetProfit = count($dailyTrend) > 0 ? round($sevenDaysNetProfit / count($dailyTrend)) : 0;

        // 6. 5 Produk Terlaris
        $topProducts = $reportService->getTopProducts(5);

        return view('dashboard.index', compact(
            'todayIncome',
            'todayExpense',
            'todayTransactionsCount',
            'todayCogs',
            'todayGrossProfit',
            'todayNetProfit',
            'monthlyIncome',
            'monthlyExpense',
            'monthlyCogs',
            'monthlyGrossProfit',
            'monthlyNetProfit',
            'monthlyNet',
            'stockSummary',
            'recentTransactions',
            'dailyTrend',
            'sevenDaysIncome',
            'sevenDaysExpense',
            'sevenDaysNetProfit',
            'sevenDaysNet',
            'avgDailyNetProfit',
            'topProducts'
        ));
    }
}
