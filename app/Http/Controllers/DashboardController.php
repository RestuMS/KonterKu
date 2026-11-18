<?php

namespace App\Http\Controllers;

use App\Models\Kasbon;
use App\Models\Product;
use App\Models\ServerDeposit;
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

        // 1. Statistik Kasbon / Piutang Menggantung
        $unpaidKasbonQuery = Kasbon::unpaid();
        $totalUnpaidKasbon = (float) (clone $unpaidKasbonQuery)->sum('amount');
        $totalUnpaidKasbonCount = (clone $unpaidKasbonQuery)->count();
        $totalUnpaidKasbonProfit = (float) (clone $unpaidKasbonQuery)->sum('profit');
        $overdueKasbonCount = (clone $unpaidKasbonQuery)
            ->whereNotNull('due_date')
            ->whereDate('due_date', '<', $today)
            ->count();
        $dueTodayKasbonCount = (clone $unpaidKasbonQuery)
            ->whereNotNull('due_date')
            ->whereDate('due_date', $today)
            ->count();
        $urgentKasbons = Kasbon::unpaid()
            ->whereNotNull('due_date')
            ->whereDate('due_date', '<=', $today)
            ->orderBy('due_date')
            ->take(5)
            ->get();
        $recentUnpaidKasbons = Kasbon::unpaid()
            ->latest('debt_date')
            ->take(4)
            ->get();

        // 1. Statistik Hari Ini (menggunakan mesin hitung tunggal yang sama persis dengan Laporan Laba Keuntungan)
        $todaySummary = $reportService->calculateFinancialSummary(
            $today->copy()->startOfDay(),
            $today->copy()->endOfDay()
        );
        $todayIncome = $todaySummary['total_income'];
        $todayExpense = $todaySummary['total_expense'];
        $todayOperationalExpense = $todaySummary['operational_expense'];
        $todayKulakanExpense = $todaySummary['kulakan_expense'];
        $todayTransactionsCount = $todaySummary['income_count'] + $todaySummary['expense_count'];
        $todayCogs = $todaySummary['total_cogs'];
        $todayGrossProfit = $todaySummary['gross_profit'];
        $todayNetProfit = $todaySummary['net_profit'];
        $todayCashDrawerNet = $todaySummary['cash_drawer_net'];

        // 2. Statistik Bulan Ini (menggunakan mesin hitung tunggal yang sama persis dengan Laporan Laba Keuntungan)
        $monthlySummary = $reportService->calculateFinancialSummary($startOfMonth, $endOfMonth);
        $monthlyIncome = $monthlySummary['total_income'];
        $monthlyExpense = $monthlySummary['total_expense'];
        $monthlyOperationalExpense = $monthlySummary['operational_expense'];
        $monthlyKulakanExpense = $monthlySummary['kulakan_expense'];
        $monthlyCogs = $monthlySummary['total_cogs'];
        $monthlyGrossProfit = $monthlySummary['gross_profit'];
        $monthlyNetProfit = $monthlySummary['net_profit'];
        $monthlyNet = $monthlySummary['net_cashflow'];
        $monthlyCashDrawerNet = $monthlySummary['cash_drawer_net'];

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

        // 7. Catatan Saldo Deposit Server Aplikasi Tunggal
        $serverDeposit = ServerDeposit::getPrimary();

        return view('dashboard.index', compact(
            'todayIncome',
            'todayExpense',
            'todayOperationalExpense',
            'todayKulakanExpense',
            'todayTransactionsCount',
            'todayCogs',
            'todayGrossProfit',
            'todayNetProfit',
            'todayCashDrawerNet',
            'monthlyIncome',
            'monthlyExpense',
            'monthlyOperationalExpense',
            'monthlyKulakanExpense',
            'monthlyCogs',
            'monthlyGrossProfit',
            'monthlyNetProfit',
            'monthlyNet',
            'monthlyCashDrawerNet',
            'stockSummary',
            'recentTransactions',
            'dailyTrend',
            'sevenDaysIncome',
            'sevenDaysExpense',
            'sevenDaysNetProfit',
            'sevenDaysNet',
            'avgDailyNetProfit',
            'topProducts',
            'totalUnpaidKasbon',
            'totalUnpaidKasbonCount',
            'totalUnpaidKasbonProfit',
            'overdueKasbonCount',
            'dueTodayKasbonCount',
            'urgentKasbons',
            'recentUnpaidKasbons',
            'serverDeposit'
        ));
    }
}
