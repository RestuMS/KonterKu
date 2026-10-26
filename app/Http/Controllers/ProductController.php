<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
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
            $query->whereColumn('stock', '<=', 'min_stock');
        }

        $products = $query->latest()->paginate(10)->withQueryString();
        $categories = Category::whereIn('type', ['product', 'all'])->get();

        return view('products.index', compact('products', 'categories'));
    }

    public function create(): View
    {
        $categories = Category::whereIn('type', ['product', 'all'])->orderBy('id')->get();
        return view('products.create', compact('categories'));
    }

    public function store(ProductRequest $request): RedirectResponse
    {
        if ($request->has('items') && is_array($request->items)) {
            $count = 0;
            DB::transaction(function () use ($request, &$count) {
                foreach ($request->items as $item) {
                    $item['is_active'] = isset($item['is_active']) && $item['is_active'];
                    Product::create($item);
                    $count++;
                }
            });

            return redirect()->route('products.index')
                ->with('success', "Berhasil menambahkan {$count} produk baru sekaligus.");
        }

        $data = $request->validated();
        $data['is_active'] = $request->has('is_active');

        Product::create($data);

        return redirect()->route('products.index')
            ->with('success', "Produk {$data['name']} berhasil ditambahkan.");
    }

    public function edit(Product $product): View
    {
        $categories = Category::whereIn('type', ['product', 'all'])->orderBy('id')->get();
        return view('products.edit', compact('product', 'categories'));
    }

    public function update(ProductRequest $request, Product $product): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = $request->has('is_active');

        $product->update($data);

        return redirect()->route('products.index')
            ->with('success', "Produk {$product->name} berhasil diperbarui.");
    }

    public function destroy(Product $product): RedirectResponse
    {
        // Periksa apakah produk sudah pernah dipakai dalam transaksi
        if ($product->transactionItems()->exists()) {
            $product->update(['is_active' => false]);

            return redirect()->route('products.index')
                ->with('info', "Produk {$product->name} memiliki riwayat transaksi sehingga dinonaktifkan (arsip) dan tidak dihapus permanen demi keakuratan laporan keuangan.");
        }

        $name = $product->name;
        $product->delete();

        return redirect()->route('products.index')
            ->with('success', "Produk {$name} berhasil dihapus.");
    }
}
