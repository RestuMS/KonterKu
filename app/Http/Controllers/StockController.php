<?php

namespace App\Http\Controllers;

use App\Http\Requests\StockAdjustmentRequest;
use App\Models\Category;
use App\Models\Product;
use App\Models\StockMutation;
use App\Services\StockService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class StockController extends Controller
{
    public function index(Request $request): View
    {
        $query = StockMutation::with(['product.category', 'user']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('product', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $startDate = \Carbon\Carbon::parse($request->start_date)->startOfDay();
            $endDate = \Carbon\Carbon::parse($request->end_date)->endOfDay();
            if ($startDate->gt($endDate)) {
                [$startDate, $endDate] = [$endDate->copy()->startOfDay(), $startDate->copy()->endOfDay()];
            }
            $query->whereBetween('created_at', [$startDate, $endDate]);
        } elseif ($request->filled('start_date')) {
            $query->where('created_at', '>=', \Carbon\Carbon::parse($request->start_date)->startOfDay());
        } elseif ($request->filled('end_date')) {
            $query->where('created_at', '<=', \Carbon\Carbon::parse($request->end_date)->endOfDay());
        }

        $mutations = $query->latest()->paginate(15)->withQueryString();

        // Metrik Statistik Mutasi Stok
        $totalIn = StockMutation::where('type', 'in')->sum('quantity');
        $totalOut = StockMutation::where('type', 'out')->sum('quantity');
        $totalAdjustment = StockMutation::where('type', 'adjustment')->count();
        $todayCount = StockMutation::whereDate('created_at', \Carbon\Carbon::today())->count();

        return view('stocks.index', compact(
            'mutations',
            'totalIn',
            'totalOut',
            'totalAdjustment',
            'todayCount'
        ));
    }

    public function create(): View
    {
        $products = Product::with('category')
            ->where('is_active', true)
            ->orderBy('category_id', 'asc')
            ->orderBy('selling_price', 'asc')
            ->orderBy('id', 'asc')
            ->get();
        $recentMutations = StockMutation::with(['product', 'user'])->latest()->limit(5)->get();
        return view('stocks.create', compact('products', 'recentMutations'));
    }

    public function store(StockAdjustmentRequest $request, StockService $stockService): RedirectResponse
    {
        $product = Product::findOrFail($request->product_id);
        $type = $request->adjustment_type;
        $val = (int) $request->stock_value;
        $notes = $request->notes;
        $userId = Auth::id();

        try {
            if ($type === 'set') {
                $stockService->adjustStock($product, $val, $notes, $userId);
            } elseif ($type === 'add') {
                $stockService->addStock($product, $val, null, null, $notes, $userId);
            } elseif ($type === 'inject') {
                $costPrice = $request->filled('cost_price') ? (float)$request->cost_price : (float)$product->cost_price;
                $result = $stockService->injectVoucher($product, $val, $costPrice, $notes, $userId);
                return redirect()->route('stocks.index')
                    ->with('success', "⚡ Berhasil inject {$val} pcs {$product->name}! Saldo server terpotong Rp " . number_format($result['total_cost'], 0, ',', '.') . " dan stok fisik bertambah.");
            } elseif ($type === 'reduce') {
                if ($product->stock < $val) {
                    return back()->withInput()->with('error', "Stok saat ini ({$product->stock}) lebih kecil dari pengurangan ({$val}).");
                }
                $stockService->reduceStock($product, $val, null, null, $notes, $userId);
            }

            return redirect()->route('stocks.index')
                ->with('success', "Penyesuaian stok untuk {$product->name} berhasil disimpan.");
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', 'Gagal memproses penyesuaian stok: ' . $e->getMessage());
        }
    }

    /**
     * Tampilan form khusus untuk Aktivasi & Inject Voucher dari Saldo Server.
     */
    public function injectForm(Request $request): View
    {
        $products = Product::with('category')
            ->where('is_active', true)
            ->orderBy('category_id', 'asc')
            ->orderBy('selling_price', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $categories = Category::whereIn('type', ['product', 'all'])->orderBy('id')->get();
        $serverDeposit = \App\Models\ServerDeposit::getPrimary();

        $recentInjects = StockMutation::with(['product.category', 'user'])
            ->where('type', 'in')
            ->where('notes', 'like', '%[INJECT VOUCHER]%')
            ->latest('id')
            ->limit(8)
            ->get();

        return view('stocks.inject', compact('products', 'categories', 'serverDeposit', 'recentInjects'));
    }

    /**
     * Proses transaksi inject voucher (potong saldo server + tambah stok fisik secara atomik).
     * Mendukung:
     * 1. Pilih voucher yang pernah dibuat (dropdown).
     * 2. Daftarkan voucher baru langsung satu pintu di form inject ini.
     */
    public function processInject(Request $request, StockService $stockService): RedirectResponse
    {
        $isNew = $request->boolean('is_new') || $request->filled('new_product_name');

        if ($isNew) {
            $validated = $request->validate([
                'new_product_name' => ['required', 'string', 'max:150'],
                'category_id'      => ['required', 'exists:categories,id'],
                'selling_price'    => ['required', 'numeric', 'min:0'],
                'quantity'         => ['required', 'integer', 'min:1'],
                'cost_price'       => ['required', 'numeric', 'min:0'],
                'inject_date'      => ['nullable', 'date'],
                'notes'            => ['nullable', 'string', 'max:255'],
            ], [
                'new_product_name.required' => 'Nama voucher baru wajib diisi.',
                'category_id.required'      => 'Kategori operator voucher wajib dipilih.',
                'selling_price.required'    => 'Harga jual kasir wajib diisi.',
                'cost_price.required'       => 'Harga modal (HPP server) wajib diisi.',
                'quantity.required'         => 'Jumlah inject minimal 1 lembar.',
                'quantity.min'              => 'Jumlah inject minimal 1 lembar.',
            ]);

            $name = trim($validated['new_product_name']);
            $costPrice = (float) $validated['cost_price'];
            $sellingPrice = (float) $validated['selling_price'];
            $qty = (int) $validated['quantity'];

            // Buat kode SKU otomatis yang unik dan rapi
            $cleanName = preg_replace('/[^A-Za-z0-9]/', '', strtoupper($name));
            $baseCode = 'VCR-' . substr($cleanName, 0, 10);
            $code = $baseCode;
            $counter = 1;
            while (Product::where('code', $code)->exists()) {
                $code = "{$baseCode}-{$counter}";
                $counter++;
            }

            // Daftarkan produk voucher baru langsung di sistem dengan stok awal 0
            $product = Product::create([
                'category_id'   => $validated['category_id'],
                'name'          => $name,
                'code'          => $code,
                'cost_price'    => $costPrice,
                'selling_price' => $sellingPrice,
                'stock'         => 0,
                'min_stock'     => 2,
                'unit'          => 'pcs',
                'is_active'     => true,
            ]);
        } else {
            $validated = $request->validate([
                'product_id'  => ['required', 'exists:products,id'],
                'quantity'    => ['required', 'integer', 'min:1'],
                'cost_price'  => ['required', 'numeric', 'min:0'],
                'inject_date' => ['nullable', 'date'],
                'notes'       => ['nullable', 'string', 'max:255'],
            ], [
                'product_id.required' => 'Produk voucher wajib dipilih.',
                'product_id.exists'   => 'Produk voucher tidak valid.',
                'quantity.required'   => 'Jumlah inject wajib diisi minimal 1 lembar.',
                'quantity.min'        => 'Jumlah inject minimal 1 lembar.',
                'cost_price.required' => 'Harga modal (HPP server) wajib diisi.',
                'cost_price.min'      => 'Harga modal tidak boleh bernilai negatif.',
            ]);

            $product = Product::findOrFail($validated['product_id']);
            $qty = (int) $validated['quantity'];
            $costPrice = (float) $validated['cost_price'];
        }

        $injectDate = $request->filled('inject_date') ? \Carbon\Carbon::parse($request->inject_date) : now();

        try {
            $result = $stockService->injectVoucher(
                $product,
                $qty,
                $costPrice,
                $validated['notes'] ?? null,
                Auth::id(),
                $injectDate
            );

            $msg = $isNew
                ? "⚡ Berhasil mendaftarkan voucher '{$product->name}' & langsung inject {$qty} pcs! Saldo server terpotong Rp " . number_format($result['total_cost'], 0, ',', '.') . " dan otomatis siap dijual di kasir."
                : "⚡ Berhasil inject {$qty} pcs {$product->name}! Saldo server terpotong Rp " . number_format($result['total_cost'], 0, ',', '.') . " dan otomatis masuk ke stok fisik etalase.";

            return redirect()->route('stocks.inject')->with('success', $msg);
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', 'Gagal memproses inject voucher: ' . $e->getMessage());
        }
    }
}
