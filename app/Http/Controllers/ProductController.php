<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Models\StockMutation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
        // Voucher fisik dipusatkan penginputannya melalui menu "Inject Voucher" agar saldo server & stok fisik sinkron satu pintu
        $categories = Category::whereIn('type', ['product', 'all'])
            ->where('slug', 'not like', 'voucher-%')
            ->where('name', 'not like', 'Voucher%')
            ->orderBy('name')
            ->get();
        $existingCodes = Product::pluck('code')->toArray();
        return view('products.create', compact('categories', 'existingCodes'));
    }

    public function store(ProductRequest $request): RedirectResponse
    {
        if ($request->has('items') && is_array($request->items)) {
            $count = 0;
            DB::transaction(function () use ($request, &$count) {
                foreach ($request->items as $item) {
                    $item['is_active'] = isset($item['is_active']) && $item['is_active'];
                    $product = Product::create($item);
                    $count++;

                    // Catat mutasi stok masuk awal jika ada stok
                    if ($product->stock > 0) {
                        StockMutation::create([
                            'product_id' => $product->id,
                            'user_id' => Auth::id(),
                            'type' => 'in',
                            'quantity' => $product->stock,
                            'stock_before' => 0,
                            'stock_after' => $product->stock,
                            'reference_type' => Product::class,
                            'reference_id' => $product->id,
                            'notes' => 'Stok awal produk baru',
                        ]);
                    }
                }
            });

            return redirect()->route('products.index')
                ->with('success', "Berhasil menambahkan {$count} produk baru sekaligus.");
        }

        $data = $request->validated();
        $data['is_active'] = $request->has('is_active');

        DB::transaction(function () use ($data) {
            $product = Product::create($data);

            // Catat mutasi stok masuk awal jika ada stok
            if ($product->stock > 0) {
                StockMutation::create([
                    'product_id' => $product->id,
                    'user_id' => Auth::id(),
                    'type' => 'in',
                    'quantity' => $product->stock,
                    'stock_before' => 0,
                    'stock_after' => $product->stock,
                    'reference_type' => Product::class,
                    'reference_id' => $product->id,
                    'notes' => 'Stok awal produk baru',
                ]);
            }
        });

        return redirect()->route('products.index')
            ->with('success', "Produk {$data['name']} berhasil ditambahkan.");
    }

    public function edit(Product $product): View
    {
        $this->ensureCategoriesExist();
        $categories = Category::whereIn('type', ['product', 'all'])->orderBy('name')->get();
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
