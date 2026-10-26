<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\Transaction;
use App\Services\ReportService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request, ReportService $reportService): View
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->toDateString());

        $summary = $reportService->getFinancialSummary($startDate, $endDate);

        $transactions = Transaction::with(['category', 'user', 'items'])
            ->whereBetween('transaction_date', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay(),
            ])
            ->latest('transaction_date')
            ->paginate(15)
            ->withQueryString();

        $paymentMethods = Transaction::whereBetween('transaction_date', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay(),
            ])
            ->selectRaw('payment_method, type, sum(total_amount) as total')
            ->groupBy('payment_method', 'type')
            ->get();

        return view('reports.index', compact('summary', 'transactions', 'paymentMethods', 'startDate', 'endDate'));
    }

    public function print(Request $request, ReportService $reportService): View
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->toDateString());

        $summary = $reportService->getFinancialSummary($startDate, $endDate);

        $transactions = Transaction::with(['category', 'user', 'items'])
            ->whereBetween('transaction_date', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay(),
            ])
            ->orderBy('transaction_date', 'asc')
            ->get();

        $shopName = Setting::get('shop_name', 'KonterKu');
        $shopPhone = Setting::get('shop_phone', '');
        $shopAddress = Setting::get('shop_address', '');

        return view('reports.print', compact('summary', 'transactions', 'startDate', 'endDate', 'shopName', 'shopPhone', 'shopAddress'));
    }
}
