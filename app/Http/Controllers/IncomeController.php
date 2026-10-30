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
        $products = Product::where('is_active', true)->with('category')->orderBy('name')->get();

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
            $transactionDate = $request->filled('transaction_date') 
                ? Carbon::parse($request->transaction_date) 
                : now();

            $invoiceNumber = 'TRX-' . $transactionDate->format('Ymd') . '-' . strtoupper(substr(uniqid(), -5));

            // Otomatis tentukan identitas tujuan untuk voucher fisik & perdana jika kosong
            $target = trim($request->customer_target ?? '');
            if (empty($target)) {
                $cat = Category::find($request->category_id);
                $catName = strtolower($cat?->name ?? '');
                $prodName = strtolower($request->product_name ?? '');

                $detectedBrand = '';
                $brands = ['Telkomsel', 'Indosat', 'Smartfren', 'Axis', 'By.U', 'Tri', 'XL'];
                foreach ($brands as $b) {
                    if (str_contains($catName, strtolower($b)) || str_contains($prodName, strtolower($b)) || ($b === 'Tri' && (str_contains($catName, 'three') || str_contains($prodName, 'three')))) {
                        $detectedBrand = $b;
                        break;
                    }
                }

                if (str_contains($catName, 'voucher') || str_contains($prodName, 'voucher')) {
                    $target = $detectedBrand ? "Voucher {$detectedBrand}" : "Voucher Fisik";
                } elseif (str_contains($catName, 'perdana') || str_contains($prodName, 'perdana')) {
                    $target = $detectedBrand ? "Perdana {$detectedBrand}" : "Kartu Perdana";
                }
            }

            $notes = trim(
                ($target ? 'No. Tujuan: ' . $target : '') .
                ($target && $request->notes ? ' | ' : '') .
                ($request->notes ?? '')
            ) ?: null;

            $qty = max(1, (int) ($request->quantity ?? 1));
            $rawAmount = (float) $request->amount;
            $unitPrice = $request->filled('unit_price') ? (float) $request->unit_price : ($rawAmount / $qty);

            // Deteksi & koreksi otomatis jika amount yang dikirim adalah harga satuan (safety fallback)
            if ($qty > 1) {
                if ($request->filled('unit_price') && (float) $request->unit_price > 0) {
                    if (abs($rawAmount - (float) $request->unit_price) < 0.01) {
                        $rawAmount = (float) $request->unit_price * $qty;
                    }
                } elseif ($request->filled('product_id')) {
                    $catProd = Product::find($request->product_id);
                    if ($catProd && abs($rawAmount - (float) $catProd->selling_price) < 0.01) {
                        $rawAmount = (float) $catProd->selling_price * $qty;
                        $unitPrice = (float) $catProd->selling_price;
                    }
                }
            }

            $totalAmount = $rawAmount;
            $unitPrice = $totalAmount / $qty;

            $transaction = Transaction::create([
                'invoice_number' => $invoiceNumber,
                'type' => 'pemasukan',
                'category_id' => $request->category_id,
                'user_id' => Auth::id(),
                'transaction_date' => $transactionDate,
                'total_amount' => $totalAmount,
                'payment_method' => $request->payment_method,
                'notes' => $notes,
            ]);

            // Hitung modal HPP yang memotong saldo server aplikasi konter
            $deductDepositAmount = 0;

            // Jika transaksi terkait produk dari katalog
            if ($request->filled('product_id')) {
                $product = Product::lockForUpdate()->findOrFail($request->product_id);

                if ($product->stock < $qty) {
                    DB::rollBack();
                    return back()->withInput()->with('error', "Stok produk {$product->name} tidak mencukupi (sisa stok: {$product->stock} {$product->unit}).");
                }

                $costPrice = $request->filled('cost_price') ? (float)$request->cost_price : (float)$product->cost_price;
                $deductDepositAmount = $costPrice > 0 ? ($costPrice * $qty) : $totalAmount;

                TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'quantity' => $qty,
                    'cost_price' => $costPrice,
                    'unit_price' => $unitPrice,
                    'subtotal' => $totalAmount,
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
            } elseif ($request->filled('product_name')) {
                // Jika transaksi jasa / produk manual tanpa ID katalog
                $costPrice = (float) ($request->cost_price ?? 0);
                $deductDepositAmount = $costPrice > 0 ? ($costPrice * $qty) : $totalAmount;

                TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => null,
                    'product_name' => $request->product_name,
                    'quantity' => $qty,
                    'cost_price' => $costPrice,
                    'unit_price' => $unitPrice,
                    'subtotal' => $totalAmount,
                ]);
            }

            // Otomatis kurangi saldo server deposit aplikasi konter
            if ($deductDepositAmount > 0) {
                \App\Models\ServerDeposit::deductBalance(
                    $deductDepositAmount, 
                    "Penjualan invoice: {$invoiceNumber}", 
                    Transaction::class, 
                    $transaction->id
                );
            }

            DB::commit();

            return redirect()->route('incomes.index')
                ->with('success', "Transaksi pemasukan {$invoiceNumber} berhasil dicatat.")
                ->with('recent_invoice_id', $transaction->id)
                ->with('recent_invoice_number', $invoiceNumber)
                ->with('recent_invoice_amount', $transaction->total_amount)
                ->with('recent_invoice_method', $transaction->payment_method);
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

    public function update(IncomeRequest $request, Transaction $income): RedirectResponse
    {
        if ($income->type !== 'pemasukan') {
            abort(404);
        }

        DB::beginTransaction();
        try {
            $income->load('items');

            // 1. Perbarui data transaksi utama (Hanya yang diizinkan untuk diedit)
            $income->update([
                'transaction_date' => $request->filled('transaction_date') ? Carbon::parse($request->transaction_date) : $income->transaction_date,
                'category_id'      => $request->category_id,
                'total_amount'     => $request->amount,
                'payment_method'   => $request->payment_method,
            ]);

            // 2. Sesuaikan unit_price & subtotal pada item transaksi yang sudah ada
            if ($income->items->isNotEmpty()) {
                foreach ($income->items as $item) {
                    $qty = (int) ($item->quantity ?: 1);
                    $item->update([
                        'unit_price' => $request->amount / $qty,
                        'subtotal'   => $request->amount,
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('incomes.show', $income)
                ->with('success', "Transaksi {$income->invoice_number} berhasil diperbarui.");
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

            // 1. Jika ada item produk fisik, kembalikan stok fisik & kurangi 'stok out' pada mutasi stok
            $refundDepositAmount = 0;
            foreach ($income->items as $item) {
                if ($item->product) {
                    // Hapus pencatatan mutasi stok 'out' dari transaksi ini agar 'stok out' berkurang kembali
                    \App\Models\StockMutation::where('product_id', $item->product_id)
                        ->where('reference_type', Transaction::class)
                        ->where('reference_id', $income->id)
                        ->delete();

                    // Kembalikan stok fisik produk ke inventaris toko
                    $item->product->increment('stock', $item->quantity);
                }
                $refundDepositAmount += ($item->cost_price > 0 ? ($item->cost_price * $item->quantity) : $item->subtotal);
            }

            if ($refundDepositAmount == 0 && $income->total_amount > 0) {
                $refundDepositAmount = (float) $income->total_amount;
            }

            // 2. Kembalikan saldo deposit server aplikasi
            $deposit = null;
            if ($refundDepositAmount > 0) {
                $deposit = \App\Models\ServerDeposit::addBalance(
                    $refundDepositAmount,
                    "Pembatalan transaksi invoice: {$income->invoice_number}",
                    Transaction::class,
                    $income->id
                );
            }

            $invoice = $income->invoice_number;
            $income->items()->delete();
            $income->delete();

            DB::commit();

            $msg = "Transaksi {$invoice} berhasil dibatalkan.";
            if ($deposit && $refundDepositAmount > 0) {
                $msg .= " Stok dikembalikan & Saldo Server sebesar Rp " . number_format($refundDepositAmount, 0, ',', '.') . " telah dikembalikan (Sisa saldo server: Rp " . number_format($deposit->balance, 0, ',', '.') . ").";
            } else {
                $msg .= " Stok telah dikembalikan ke inventaris.";
            }

            return redirect()->route('incomes.index')->with('success', $msg);
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal membatalkan transaksi: ' . $e->getMessage());
        }
    }
}
