<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Models\StockMutation;
use App\Models\Transaction;
use App\Services\StockService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ProductController extends Controller
{
    /**
     * Pastikan kategori standar usaha konter selalu tersedia di database.
     */
    protected function ensureCategoriesExist(): void
    {
        if (Category::whereIn('type', ['product', 'all'])->count() === 0) {
            (new \Database\Seeders\CategorySeeder())->run();
        }
    }

    public function index(Request $request): View
    {
        $this->ensureCategoriesExist();
        $query = Product::with('category');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        if ($request->boolean('low_stock')) {
            $query->whereColumn('stock', '<=', 'min_stock')
                  ->where('unit', '!=', 'transaksi');
        }

        match ($request->get('sort')) {
            'latest' => $query->latest(),
            'oldest' => $query->oldest(),
            'name_asc' => $query->orderBy('name', 'asc'),
            'name_desc' => $query->orderBy('name', 'desc'),
            'price_desc' => $query->orderBy('selling_price', 'desc'),
            'stock_asc' => $query->orderBy('stock', 'asc'),
            'stock_desc' => $query->orderBy('stock', 'desc'),
            default => $query->orderBy('category_id', 'asc')->orderBy('selling_price', 'asc')->orderBy('id', 'asc'),
        };

        $products = $query->paginate(15)->withQueryString();
        $categories = Category::whereIn('type', ['product', 'all'])->orderBy('id')->get();

        return view('products.index', compact('products', 'categories'));
    }

    public function create(): View
    {
        $this->ensureCategoriesExist();
        $categories = Category::whereIn('type', ['product', 'all'])->orderBy('name')->get();
        $existingCodes = Product::pluck('code')->toArray();
        return view('products.create', compact('categories', 'existingCodes'));
    }

    public function store(ProductRequest $request, StockService $stockService): RedirectResponse
    {
        $isBatch = $request->has('items') && is_array($request->items);
        $rawItems = $isBatch
            ? $request->items
            : [array_merge($request->validated(), ['is_active' => $request->has('is_active')])];

        $recordExpense = $request->boolean('record_expense');
        $paymentMethod = $request->input('payment_method', 'cash');
        $expenseNotes = trim((string) $request->input('expense_notes', ''));

        $count = 0;
        $recordedExpenseAmount = 0;

        DB::transaction(function () use (
            $rawItems, $isBatch, $request, $stockService,
            $recordExpense, $paymentMethod, $expenseNotes,
            &$count, &$recordedExpenseAmount
        ) {
            $createdMutations = [];
            $totalStockCost = 0;
            $stockedSummaries = [];

            foreach ($rawItems as $item) {
                $item['is_active'] = $isBatch
                    ? (isset($item['is_active']) && $item['is_active'])
                    : (bool) ($item['is_active'] ?? true);

                $product = Product::create(Arr::only($item, [
                    'category_id', 'code', 'name', 'cost_price', 'selling_price',
                    'stock', 'min_stock', 'unit', 'is_active', 'notes',
                ]));
                $count++;

                if ($product->stock > 0) {
                    $mutation = StockMutation::create([
                        'product_id' => $product->id,
                        'user_id' => Auth::id(),
                        'type' => 'in',
                        'quantity' => $product->stock,
                        'stock_before' => 0,
                        'stock_after' => $product->stock,
                        'reference_type' => Product::class,
                        'reference_id' => $product->id,
                        'notes' => $recordExpense
                            ? ('[ORDER SALES] Stok awal produk baru' . ($expenseNotes ? " ({$expenseNotes})" : ''))
                            : 'Stok awal produk baru',
                    ]);

                    if ($product->unit !== 'transaksi') {
                        $createdMutations[] = $mutation;
                        $totalStockCost += ((float) $product->cost_price * (int) $product->stock);
                        $stockedSummaries[] = "{$product->name} ({$product->stock} {$product->unit})";
                    }
                }
            }

            if ($recordExpense) {
                $finalAmount = $request->filled('expense_amount')
                    ? (float) $request->expense_amount
                    : $totalStockCost;

                if ($finalAmount > 0) {
                    $desc = '[ORDER SALES] Kulakan produk baru: ' . (!empty($stockedSummaries) ? implode(', ', $stockedSummaries) : 'Stok awal')
                        . ($expenseNotes ? " | {$expenseNotes}" : '');

                    $expenseTx = $stockService->recordKulakanExpense(
                        $finalAmount,
                        $paymentMethod,
                        $desc,
                        Auth::id()
                    );

                    foreach ($createdMutations as $mut) {
                        $mut->update([
                            'reference_type' => Transaction::class,
                            'reference_id' => $expenseTx->id,
                        ]);
                    }
                    $recordedExpenseAmount = $finalAmount;
                }
            }
        });

        $msg = $isBatch
            ? "Berhasil menambahkan {$count} produk baru sekaligus."
            : 'Produk ' . ($rawItems[0]['name'] ?? '') . ' berhasil ditambahkan.';

        if ($recordedExpenseAmount > 0) {
            $msg .= ' Pengeluaran kulakan Rp ' . number_format($recordedExpenseAmount, 0, ',', '.') . ' (' . strtoupper($paymentMethod) . ') otomatis tercatat.';
        }

        return redirect()->route('products.index')->with('success', $msg);
    }

    public function edit(Product $product): View
    {
        $this->ensureCategoriesExist();
        $categories = Category::whereIn('type', ['product', 'all'])->orderBy('name')->get();
        return view('products.edit', compact('product', 'categories'));
    }

    public function update(ProductRequest $request, Product $product): RedirectResponse
    {
        $data = Arr::only($request->validated(), [
            'category_id', 'code', 'name', 'cost_price', 'selling_price',
            'stock', 'min_stock', 'unit', 'is_active', 'notes',
        ]);
        $data['is_active'] = $request->has('is_active');
        $oldStock = (int) $product->stock;

        DB::transaction(function () use ($product, $data, $oldStock) {
            $product->update($data);

            $newStock = (int) $product->stock;
            if ($newStock !== $oldStock) {
                StockMutation::create([
                    'product_id' => $product->id,
                    'user_id' => Auth::id(),
                    'type' => 'adjustment',
                    'quantity' => abs($newStock - $oldStock),
                    'stock_before' => $oldStock,
                    'stock_after' => $newStock,
                    'reference_type' => Product::class,
                    'reference_id' => $product->id,
                    'notes' => 'Penyesuaian stok melalui edit produk',
                ]);
            }
        });

        return redirect()->route('products.index')
            ->with('success', "Produk {$product->name} berhasil diperbarui.");
    }

    public function destroy(Product $product): RedirectResponse
    {
        try {
            $name = $product->name;

            // Jika produk memiliki riwayat transaksi penjualan/pembelian
            if ($product->transactionItems()->exists()) {
                // Jika produk masih aktif, nonaktifkan terlebih dahulu agar tidak merusak audit
                if ($product->is_active) {
                    $product->update(['is_active' => false]);

                    return redirect()->route('products.index')
                        ->with('info', "Produk {$name} memiliki riwayat transaksi sehingga dinonaktifkan (arsip) dan disembunyikan dari penjualan kasir. Jika Anda tetap ingin menghapusnya permanen, klik tombol hapus sekali lagi.");
                }

                // Jika sudah nonaktif dan user tetap ingin menghapus permanen:
                // Putus referensi foreign key di item transaksi (product_id diset null) agar riwayat struk/nama produk di masa lalu tetap utuh
                DB::transaction(function () use ($product) {
                    $product->transactionItems()->update(['product_id' => null]);
                    $product->stockMutations()->delete();
                    $product->delete();
                });

                return redirect()->route('products.index')
                    ->with('success', "Produk {$name} berhasil dihapus permanen. Riwayat transaksi masa lalu tetap aman.");
            }

            // Jika produk belum pernah bertransaksi sama sekali:
            // Hapus riwayat mutasi stok awal, lalu hapus produk secara aman
            DB::transaction(function () use ($product) {
                $product->stockMutations()->delete();
                $product->delete();
            });

            return redirect()->route('products.index')
                ->with('success', "Produk {$name} berhasil dihapus.");
        } catch (\Throwable $e) {
            return redirect()->route('products.index')
                ->with('error', "Gagal menghapus produk: " . $e->getMessage());
        }
    }
}
