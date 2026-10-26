<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
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

        // Statistik Hari Ini (Data nyata dari database)
        $todayIncome = Transaction::where('type', 'pemasukan')
            ->whereDate('transaction_date', $today)
            ->sum('total_amount');

        $todayExpense = Transaction::where('type', 'pengeluaran')
            ->whereDate('transaction_date', $today)
            ->sum('total_amount');

        $todayTransactionsCount = Transaction::whereDate('transaction_date', $today)->count();

        // Statistik Bulan Ini
        $monthlyIncome = Transaction::where('type', 'pemasukan')
            ->whereBetween('transaction_date', [$startOfMonth, $endOfMonth])
            ->sum('total_amount');

        $monthlyExpense = Transaction::where('type', 'pengeluaran')
            ->whereBetween('transaction_date', [$startOfMonth, $endOfMonth])
            ->sum('total_amount');

        $monthlyNet = $monthlyIncome - $monthlyExpense;

        // Produk & Stok
        $stockSummary = $reportService->getStockSummary();

        // Transaksi Terbaru
        $recentTransactions = Transaction::with(['category', 'user'])
            ->latest('transaction_date')
            ->take(6)
            ->get();

        // Tren 7 Hari Terakhir
        $dailyTrend = $reportService->getDailyTrend(7);

        return view('dashboard.index', compact(
            'todayIncome',
            'todayExpense',
            'todayTransactionsCount',
            'monthlyIncome',
            'monthlyExpense',
            'monthlyNet',
            'stockSummary',
            'recentTransactions',
            'dailyTrend'
        ));
    }
}
