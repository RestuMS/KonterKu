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
                  ->orWhere('notes', 'like', "%{$search}%")
                  ->orWhereHas('items', function ($itemQuery) use ($search) {
                      $itemQuery->where('product_name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('transaction_date', [
                Carbon::parse($request->start_date)->startOfDay(),
                Carbon::parse($request->end_date)->endOfDay(),
            ]);
        }

        // Hitung total finansial langsung dari database (Pemasukan TIDAK SAMA dengan Laba)
        $totalAmount = (float) (clone $query)->sum('total_amount');
        
        $matchingTxIds = (clone $query)->pluck('id');
        $totalCost = (float) TransactionItem::whereIn('transaction_id', $matchingTxIds)
            ->sum(DB::raw('cost_price * quantity'));

        $totalProfit = $totalAmount - $totalCost;

        $incomes = $query->latest('transaction_date')->paginate(10)->withQueryString();
        $categories = Category::whereIn('type', ['product', 'transaction', 'all'])->orderBy('name')->get();

        return view('incomes.index', compact('incomes', 'categories', 'totalAmount', 'totalCost', 'totalProfit'));
    }

    public function create(): View
    {
        $categories = Category::whereIn('type', ['product', 'transaction', 'all'])->orderBy('name')->get();
        $products = Product::where('is_active', true)->where('stock', '>', 0)->with('category')->orderBy('name')->get();

        return view('incomes.create', compact('categories', 'products'));
    }

    public function store(IncomeRequest $request, StockService $stockService): RedirectResponse
    {
        // Hindari pencatatan ganda: cek transaksi serupa dalam rentang 3 detik terakhir oleh pengguna yang sama
        $recentDuplicate = Transaction::where('user_id', Auth::id())
            ->where('type', 'pemasukan')
            ->where('total_amount', $request->amount)
            ->where('category_id', $request->category_id)
            ->where('created_at', '>=', now()->subSeconds(3))
            ->first();

        if ($recentDuplicate) {
            return redirect()->route('incomes.index')
                ->with('info', "Transaksi serupa baru saja dicatat ({$recentDuplicate->invoice_number}). Pencatatan ganda berhasil dihindari.");
        }

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
                $product = Product::lockForUpdate()->findOrFail($request->product_id);
                $qty = (int) ($request->quantity ?? 1);

                if ($product->stock < $qty) {
                    DB::rollBack();
                    return back()->withInput()->with('error', "Stok produk {$product->name} tidak mencukupi (sisa stok: {$product->stock} {$product->unit}).");
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
        if ($income->type !== 'pemasukan') {
            abort(404);
        }

        $income->load(['category', 'user', 'items.product']);
        return view('incomes.show', compact('income'));
    }

    public function edit(Transaction $income): View
    {
        if ($income->type !== 'pemasukan') {
            abort(404);
        }

        $income->load(['category', 'items.product']);
        $categories = Category::whereIn('type', ['product', 'transaction', 'all'])->orderBy('name')->get();
        $products = Product::where('is_active', true)->with('category')->orderBy('name')->get();

        return view('incomes.edit', compact('income', 'categories', 'products'));
    }

    public function update(IncomeRequest $request, Transaction $income, StockService $stockService): RedirectResponse
    {
        if ($income->type !== 'pemasukan') {
            abort(404);
        }

        DB::beginTransaction();
        try {
            $income->load('items.product');

            // 1. Kembalikan stok item produk sebelumnya jika ada
            foreach ($income->items as $oldItem) {
                if ($oldItem->product) {
                    $stockService->addStock(
                        $oldItem->product,
                        $oldItem->quantity,
                        Transaction::class,
                        $income->id,
                        "Koreksi edit transaksi invoice: {$income->invoice_number} (pengembalian stok lama)",
                        Auth::id()
                    );
                }
            }

            // Hapus rincian item sebelumnya dan kosongkan relasi di memori
            $income->items()->delete();
            $income->unsetRelation('items');

            // 2. Perbarui data transaksi utama
            $income->update([
                'transaction_date' => $request->transaction_date,
                'category_id' => $request->category_id,
                'total_amount' => $request->amount,
                'payment_method' => $request->payment_method,
                'notes' => $request->notes,
            ]);

            // 3. Jika ada produk baru yang dipilih, lakukan pengurangan stok baru
            if ($request->filled('product_id')) {
                $product = Product::lockForUpdate()->findOrFail($request->product_id);
                $qty = (int) ($request->quantity ?? 1);

                if ($product->stock < $qty) {
                    DB::rollBack();
                    return back()->withInput()->with('error', "Stok produk {$product->name} tidak mencukupi untuk jumlah baru (sisa stok: {$product->stock} {$product->unit}).");
                }

                TransactionItem::create([
                    'transaction_id' => $income->id,
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
                    $income->id,
                    "Penyesuaian edit transaksi invoice: {$income->invoice_number}",
                    Auth::id()
                );
            }

            DB::commit();

            return redirect()->route('incomes.show', $income)
                ->with('success', "Transaksi {$income->invoice_number} berhasil diperbarui dan stok telah disesuaikan secara konsisten.");
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal memperbarui transaksi: ' . $e->getMessage());
        }
    }

    public function destroy(Transaction $income, StockService $stockService): RedirectResponse
    {
        if ($income->type !== 'pemasukan') {
            abort(404);
        }

        DB::beginTransaction();
        try {
            $income->load('items.product');

            // Jika ada item produk yang terjual, kembalikan stoknya secara aman
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
            $income->items()->delete();
            $income->delete();

            DB::commit();

            return redirect()->route('incomes.index')
                ->with('success', "Transaksi {$invoice} berhasil dibatalkan dan stok dikembalikan ke inventaris.");
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal membatalkan transaksi: ' . $e->getMessage());
        }
    }
}
