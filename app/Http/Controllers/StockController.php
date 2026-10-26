<?php

namespace App\Http\Controllers;

use App\Http\Requests\StockAdjustmentRequest;
use App\Models\Product;
use App\Models\StockMutation;
use App\Services\StockService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

        $mutations = $query->latest()->paginate(15)->withQueryString();

        return view('stocks.index', compact('mutations'));
    }

    public function create(): View
    {
        $products = Product::where('is_active', true)->orderBy('name')->get();
        return view('stocks.create', compact('products'));
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
}
