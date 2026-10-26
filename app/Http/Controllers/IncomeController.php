<?php

namespace App\Http\Controllers;

use App\Http\Requests\IncomeRequest;
use App\Models\Category;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Services\StockService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class IncomeController extends Controller
{
    public function index(Request $request): View
    {
        $query = Transaction::with(['category', 'user', 'items.product'])
            ->where('type', 'pemasukan');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('transaction_date', [
                Carbon::parse($request->start_date)->startOfDay(),
                Carbon::parse($request->end_date)->endOfDay(),
            ]);
        }

        $incomes = $query->latest('transaction_date')->paginate(10)->withQueryString();
        $categories = Category::whereIn('type', ['product', 'transaction', 'all'])->get();
        $totalAmount = (clone $query)->sum('total_amount');

        return view('incomes.index', compact('incomes', 'categories', 'totalAmount'));
    }

    public function create(): View
    {
        $categories = Category::whereIn('type', ['product', 'transaction', 'all'])->get();
        $products = Product::where('is_active', true)->where('stock', '>', 0)->get();

        return view('incomes.create', compact('categories', 'products'));
    }

    public function store(IncomeRequest $request, StockService $stockService): RedirectResponse
    {
        DB::beginTransaction();
        try {
            $invoiceNumber = 'TRX-' . Carbon::parse($request->transaction_date)->format('Ymd') . '-' . strtoupper(substr(uniqid(), -5));

            $transaction = Transaction::create([
                'invoice_number' => $invoiceNumber,
                'type' => 'pemasukan',
                'category_id' => $request->category_id,
                'user_id' => Auth::id(),
                'transaction_date' => $request->transaction_date,
                'total_amount' => $request->amount,
                'payment_method' => $request->payment_method,
                'notes' => $request->notes,
            ]);

            // Jika transaksi terkait produk fisik / voucher konter
            if ($request->filled('product_id')) {
                $product = Product::findOrFail($request->product_id);
                $qty = (int) ($request->quantity ?? 1);

                if ($product->stock < $qty) {
                    DB::rollBack();
                    return back()->withInput()->with('error', "Stok produk {$product->name} tidak mencukupi (sisa: {$product->stock}).");
                }

                TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'quantity' => $qty,
                    'cost_price' => $product->cost_price,
                    'unit_price' => $request->amount / $qty,
                    'subtotal' => $request->amount,
                ]);

                // Kurangi stok produk secara atomik
                $stockService->reduceStock(
                    $product,
                    $qty,
                    Transaction::class,
                    $transaction->id,
                    "Penjualan invoice: {$invoiceNumber}",
                    Auth::id()
                );
            }

            DB::commit();

            return redirect()->route('incomes.index')
                ->with('success', "Transaksi pemasukan {$invoiceNumber} berhasil dicatat.");
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal menyimpan transaksi pemasukan: ' . $e->getMessage());
        }
    }

    public function show(Transaction $income): View
    {
        $income->load(['category', 'user', 'items.product']);
        return view('incomes.show', compact('income'));
    }

    public function destroy(Transaction $income, StockService $stockService): RedirectResponse
    {
        if ($income->type !== 'pemasukan') {
            abort(404);
        }

        DB::beginTransaction();
        try {
            // Jika ada item produk yang terjual, kembalikan stoknya
            foreach ($income->items as $item) {
                if ($item->product) {
                    $stockService->addStock(
                        $item->product,
                        $item->quantity,
                        Transaction::class,
                        $income->id,
                        "Pembatalan transaksi invoice: {$income->invoice_number}",
                        Auth::id()
                    );
                }
            }

            $invoice = $income->invoice_number;
            $income->delete();

            DB::commit();

            return redirect()->route('incomes.index')
                ->with('success', "Transaksi {$invoice} berhasil dibatalkan dan stok dikembalikan.");
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal membatalkan transaksi: ' . $e->getMessage());
        }
    }
}
