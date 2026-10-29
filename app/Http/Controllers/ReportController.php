<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Setting;
use App\Models\StockMutation;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Services\ReportService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index(Request $request, ReportService $reportService): View
    {
        $tab = $request->input('tab', 'ringkasan');
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->toDateString());
        $search = $request->input('search');
        $categoryId = $request->input('category_id');
        $paymentMethod = $request->input('payment_method');
        $stockStatus = $request->input('stock_status', 'all');
        $mutationType = $request->input('mutation_type');

        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->endOfDay();

        // Kategori untuk dropdown filter
        $categories = Category::orderBy('name')->get();

        // 1. DATA RINGKASAN ARUS KAS & LABA RUGI KOMPREHENSIF
        $financialSummary = $this->calculateFinancialSummary($start, $end);

        // 2. DATA SESUAI TAB YANG AKTIF
        $incomes = null;
        $incomesSummary = [];
        $expenses = null;
        $expensesSummary = [];
        $products = null;
        $productsSummary = [];
        $mutations = null;
        $mutationsSummary = [];

        if ($tab === 'pemasukan') {
            $incomesQuery = Transaction::with(['category', 'user', 'items'])
                ->where('type', 'pemasukan')
                ->whereBetween('transaction_date', [$start, $end]);

            if ($search) {
                $incomesQuery->where(function ($q) use ($search) {
                    $q->where('invoice_number', 'like', "%{$search}%")
                      ->orWhere('notes', 'like', "%{$search}%")
                      ->orWhereHas('items', function ($iq) use ($search) {
                          $iq->where('product_name', 'like', "%{$search}%");
                      });
                });
            }

            if ($categoryId) {
                $incomesQuery->where('category_id', $categoryId);
            }

            if ($paymentMethod) {
                $incomesQuery->where('payment_method', $paymentMethod);
            }

            // Hitung total dari query terfilter
            $totalIncomeFiltered = (clone $incomesQuery)->sum('total_amount');
            $incomeIds = (clone $incomesQuery)->pluck('id');
            $totalCostFiltered = TransactionItem::whereIn('transaction_id', $incomeIds)
                ->selectRaw('SUM(cost_price * quantity) as total_cost')
                ->value('total_cost') ?? 0;

            $incomesSummary = [
                'total_amount' => (float) $totalIncomeFiltered,
                'total_cost' => (float) $totalCostFiltered,
                'total_profit' => (float) ($totalIncomeFiltered - $totalCostFiltered),
                'total_count' => (clone $incomesQuery)->count(),
            ];

            $incomes = $incomesQuery->latest('transaction_date')->paginate(15)->withQueryString();
        }

        if ($tab === 'pengeluaran') {
            $expensesQuery = Transaction::with(['category', 'user'])
                ->where('type', 'pengeluaran')
                ->whereBetween('transaction_date', [$start, $end]);

            if ($search) {
                $expensesQuery->where(function ($q) use ($search) {
                    $q->where('invoice_number', 'like', "%{$search}%")
                      ->orWhere('notes', 'like', "%{$search}%");
                });
            }

            if ($categoryId) {
                $expensesQuery->where('category_id', $categoryId);
            }

            if ($paymentMethod) {
                $expensesQuery->where('payment_method', $paymentMethod);
            }

            $totalExpenseFiltered = (clone $expensesQuery)->sum('total_amount');

            $expensesSummary = [
                'total_amount' => (float) $totalExpenseFiltered,
                'total_count' => (clone $expensesQuery)->count(),
            ];

            $expenses = $expensesQuery->latest('transaction_date')->paginate(15)->withQueryString();
        }

        if ($tab === 'produk_stok') {
            $productsQuery = Product::with('category');

            if ($search) {
                $productsQuery->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('code', 'like', "%{$search}%");
                });
            }

            if ($categoryId) {
                $productsQuery->where('category_id', $categoryId);
            }

            if ($stockStatus === 'low') {
                $productsQuery->whereColumn('stock', '<=', 'min_stock')->where('stock', '>', 0);
            } elseif ($stockStatus === 'empty') {
                $productsQuery->where('stock', '<=', 0);
            } elseif ($stockStatus === 'safe') {
                $productsQuery->whereColumn('stock', '>', 'min_stock');
            }

            $allFilteredProducts = (clone $productsQuery)->get();
            $productsSummary = [
                'total_sku' => $allFilteredProducts->count(),
                'total_units' => $allFilteredProducts->sum('stock'),
                'total_cost_value' => $allFilteredProducts->sum(fn($p) => $p->stock * $p->cost_price),
                'total_selling_value' => $allFilteredProducts->sum(fn($p) => $p->stock * $p->selling_price),
                'potential_profit' => $allFilteredProducts->sum(fn($p) => ($p->stock * $p->selling_price) - ($p->stock * $p->cost_price)),
            ];

            $products = $productsQuery->orderBy('name')->paginate(20)->withQueryString();
        }

        if ($tab === 'mutasi_stok') {
            $mutationsQuery = StockMutation::with(['product.category', 'user'])
                ->whereBetween('created_at', [$start, $end]);

            if ($search) {
                $mutationsQuery->where(function ($q) use ($search) {
                    $q->where('notes', 'like', "%{$search}%")
                      ->orWhereHas('product', function ($pq) use ($search) {
                          $pq->where('name', 'like', "%{$search}%")
                             ->orWhere('code', 'like', "%{$search}%");
                      });
                });
            }

            if ($mutationType) {
                $mutationsQuery->where('type', $mutationType);
            }

            $mutationsSummary = [
                'total_in' => (clone $mutationsQuery)->where('type', 'in')->sum('quantity'),
                'total_out' => (clone $mutationsQuery)->where('type', 'out')->sum('quantity'),
                'total_adjustment' => (clone $mutationsQuery)->where('type', 'adjustment')->count(),
                'total_count' => (clone $mutationsQuery)->count(),
            ];

            $mutations = $mutationsQuery->latest('created_at')->paginate(15)->withQueryString();
        }

        return view('reports.index', compact(
            'tab',
            'startDate',
            'endDate',
            'search',
            'categoryId',
            'paymentMethod',
            'stockStatus',
            'mutationType',
            'categories',
            'financialSummary',
            'incomes',
            'incomesSummary',
            'expenses',
            'expensesSummary',
            'products',
            'productsSummary',
            'mutations',
            'mutationsSummary'
        ));
    }

    public function print(Request $request): View
    {
        $tab = $request->input('tab', 'ringkasan');
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->toDateString());

        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->endOfDay();

        $financialSummary = $this->calculateFinancialSummary($start, $end);

        $shopName = Setting::get('shop_name', 'KonterKu');
        $shopPhone = Setting::get('shop_phone', '');
        $shopAddress = Setting::get('shop_address', '');

        $incomes = Transaction::with(['category', 'user', 'items'])
            ->where('type', 'pemasukan')
            ->whereBetween('transaction_date', [$start, $end])
            ->latest('transaction_date')
            ->get();

        $expenses = Transaction::with(['category', 'user'])
            ->where('type', 'pengeluaran')
            ->whereBetween('transaction_date', [$start, $end])
            ->latest('transaction_date')
            ->get();

        $products = Product::with('category')->orderBy('name')->get();

        return view('reports.print', compact(
            'tab',
            'startDate',
            'endDate',
            'financialSummary',
            'incomes',
            'expenses',
            'products',
            'shopName',
            'shopPhone',
            'shopAddress'
        ));
    }

    public function export(Request $request): StreamedResponse
    {
        $tab = $request->input('tab', 'pemasukan');
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->toDateString());

        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->endOfDay();

        $tabName = $tab === 'laba_rugi' ? 'Laba-Keuntungan' : $tab;
        $filename = "Laporan-{$tabName}-{$startDate}-sd-{$endDate}.csv";

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response()->stream(function () use ($tab, $start, $end) {
            $handle = fopen('php://output', 'w');
            // Menulis UTF-8 BOM agar Excel dapat membuka karakter dengan benar
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            if ($tab === 'pemasukan') {
                fputcsv($handle, ['No', 'No Faktur', 'Tanggal & Waktu', 'Item Terjual', 'Kategori', 'Metode Bayar', 'Modal (HPP)', 'Total Omzet', 'Laba Bersih', 'Kasir']);
                $transactions = Transaction::with(['category', 'user', 'items'])
                    ->where('type', 'pemasukan')
                    ->whereBetween('transaction_date', [$start, $end])
                    ->latest('transaction_date')
                    ->cursor();

                $no = 1;
                foreach ($transactions as $tx) {
                    $itemNames = $tx->items->map(fn($it) => "{$it->product_name} ({$it->quantity}x)")->join(', ') ?: ($tx->category->name ?? 'Penjualan');
                    $costPrice = $tx->items->sum(fn($it) => $it->cost_price * $it->quantity);
                    $profit = $tx->total_amount - $costPrice;

                    fputcsv($handle, [
                        $no++,
                        $tx->invoice_number,
                        $tx->transaction_date ? Carbon::parse($tx->transaction_date)->format('d/m/Y H:i') : '-',
                        $itemNames,
                        $tx->category->name ?? 'Umum',
                        strtoupper($tx->payment_method),
                        $costPrice,
                        $tx->total_amount,
                        $profit,
                        $tx->user->name ?? 'Kasir',
                    ]);
                }
            } elseif ($tab === 'pengeluaran') {
                fputcsv($handle, ['No', 'No Faktur', 'Tanggal & Waktu', 'Kategori Pengeluaran', 'Keterangan', 'Metode Bayar', 'Nominal Pengeluaran', 'Pencatat']);
                $expenses = Transaction::with(['category', 'user'])
                    ->where('type', 'pengeluaran')
                    ->whereBetween('transaction_date', [$start, $end])
                    ->latest('transaction_date')
                    ->cursor();

                $no = 1;
                foreach ($expenses as $exp) {
                    fputcsv($handle, [
                        $no++,
                        $exp->invoice_number,
                        $exp->transaction_date ? Carbon::parse($exp->transaction_date)->format('d/m/Y H:i') : '-',
                        $exp->category->name ?? 'Operasional',
                        $exp->notes,
                        strtoupper($exp->payment_method),
                        $exp->total_amount,
                        $exp->user->name ?? 'Kasir',
                    ]);
                }
            } elseif ($tab === 'produk_stok') {
                fputcsv($handle, ['No', 'Kode Produk', 'Nama Produk', 'Kategori', 'Stok Fisik', 'Satuan', 'Batas Minimum', 'Harga Modal (HPP)', 'Harga Jual', 'Nilai Aset Modal', 'Estimasi Nilai Jual', 'Potensi Laba']);
                $products = Product::with('category')->orderBy('name')->cursor();

                $no = 1;
                foreach ($products as $p) {
                    $costValue = $p->stock * $p->cost_price;
                    $sellingValue = $p->stock * $p->selling_price;
                    $potentialProfit = $sellingValue - $costValue;

                    fputcsv($handle, [
                        $no++,
                        $p->code,
                        $p->name,
                        $p->category->name ?? 'Umum',
                        $p->stock,
                        $p->unit ?? 'pcs',
                        $p->min_stock,
                        $p->cost_price,
                        $p->selling_price,
                        $costValue,
                        $sellingValue,
                        $potentialProfit,
                    ]);
                }
            } elseif ($tab === 'mutasi_stok') {
                fputcsv($handle, ['No', 'Waktu Mutasi', 'Kode Produk', 'Nama Produk', 'Tipe Mutasi', 'Jumlah', 'Stok Awal', 'Stok Akhir', 'Keterangan', 'Operator']);
                $mutations = StockMutation::with(['product', 'user'])
                    ->whereBetween('created_at', [$start, $end])
                    ->latest('created_at')
                    ->cursor();

                $no = 1;
                foreach ($mutations as $m) {
                    fputcsv($handle, [
                        $no++,
                        $m->created_at->format('d/m/Y H:i'),
                        $m->product->code ?? '-',
                        $m->product->name ?? 'Produk Dihapus',
                        strtoupper($m->type),
                        $m->quantity,
                        $m->stock_before,
                        $m->stock_after,
                        $m->notes ?? '-',
                        $m->user->name ?? 'Sistem',
                    ]);
                }
            } else {
                // Ekspor Laba Keuntungan / Ringkasan Keuangan
                $financialSummary = $this->calculateFinancialSummary($start, $end);
                fputcsv($handle, ['KOMPONEN LAPORAN LABA KEUNTUNGAN', 'NILAI (RP)']);
                fputcsv($handle, ['Total Pemasukan (Omzet Kasir)', $financialSummary['total_income']]);
                fputcsv($handle, ['Beban Pokok Penjualan (Modal HPP Terdata)', $financialSummary['total_cogs']]);
                fputcsv($handle, ['Laba Kotor Penjualan (Gross Profit)', $financialSummary['gross_profit']]);
                fputcsv($handle, ['Total Beban Pengeluaran Toko', $financialSummary['total_expense']]);
                fputcsv($handle, ['Laba Keuntungan Bersih Toko (Net Profit)', $financialSummary['net_profit']]);
                fputcsv($handle, ['Arus Kas Bersih (Net Cash Flow)', $financialSummary['net_cashflow']]);
                fputcsv($handle, ['Kelengkapan Data HPP Item Transaksi (%)', $financialSummary['cost_coverage_percentage'] . '%']);
            }

            fclose($handle);
        }, 200, $headers);
    }

    /**
     * Hitung ringkasan keuangan dan analisis kelengkapan data HPP untuk akurasi laba rugi.
     */
    private function calculateFinancialSummary(Carbon $start, Carbon $end): array
    {
        $transactions = Transaction::whereBetween('transaction_date', [$start, $end])->get();

        $totalIncome = (float) $transactions->where('type', 'pemasukan')->sum('total_amount');
        $totalExpense = (float) $transactions->where('type', 'pengeluaran')->sum('total_amount');
        $netCashflow = $totalIncome - $totalExpense;

        $incomeIds = $transactions->where('type', 'pemasukan')->pluck('id');

        // Ambil semua item penjualan untuk periode ini
        $saleItems = TransactionItem::whereIn('transaction_id', $incomeIds)->get();

        $totalCogs = 0;
        $totalSaleItemCount = $saleItems->count();
        $itemsWithValidCostCount = 0;

        foreach ($saleItems as $it) {
            $totalCogs += ($it->cost_price * $it->quantity);
            if ($it->cost_price > 0) {
                $itemsWithValidCostCount++;
            }
        }

        $grossProfit = $totalIncome - $totalCogs;
        $netProfit = $grossProfit - $totalExpense;

        $hasCompleteCostData = $totalSaleItemCount === 0 || ($totalSaleItemCount > 0 && $itemsWithValidCostCount === $totalSaleItemCount);
        $costCoveragePercentage = $totalSaleItemCount > 0 ? round(($itemsWithValidCostCount / $totalSaleItemCount) * 100, 1) : 100;

        // Breakdown per metode pembayaran
        $paymentMethodsIncome = Transaction::where('type', 'pemasukan')
            ->whereBetween('transaction_date', [$start, $end])
            ->selectRaw('payment_method, sum(total_amount) as total')
            ->groupBy('payment_method')
            ->pluck('total', 'payment_method')
            ->toArray();

        $paymentMethodsExpense = Transaction::where('type', 'pengeluaran')
            ->whereBetween('transaction_date', [$start, $end])
            ->selectRaw('payment_method, sum(total_amount) as total')
            ->groupBy('payment_method')
            ->pluck('total', 'payment_method')
            ->toArray();

        // Breakdown pengeluaran per kategori
        $expensesByCategory = Transaction::with('category')
            ->where('type', 'pengeluaran')
            ->whereBetween('transaction_date', [$start, $end])
            ->selectRaw('category_id, sum(total_amount) as total, count(*) as count')
            ->groupBy('category_id')
            ->get();

        return [
            'total_income' => $totalIncome,
            'total_expense' => $totalExpense,
            'net_cashflow' => $netCashflow,
            'total_cogs' => $totalCogs,
            'gross_profit' => $grossProfit,
            'net_profit' => $netProfit,
            'income_count' => $transactions->where('type', 'pemasukan')->count(),
            'expense_count' => $transactions->where('type', 'pengeluaran')->count(),
            'total_sale_item_count' => $totalSaleItemCount,
            'items_with_valid_cost_count' => $itemsWithValidCostCount,
            'has_complete_cost_data' => $hasCompleteCostData,
            'cost_coverage_percentage' => $costCoveragePercentage,
            'payment_methods_income' => $paymentMethodsIncome,
            'payment_methods_expense' => $paymentMethodsExpense,
            'expenses_by_category' => $expensesByCategory,
        ];
    }
}
