<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function store(CategoryRequest $request): RedirectResponse
    {
        $baseSlug = Str::slug($request->name);
        $slug = $baseSlug;
        $counter = 1;

        while (Category::where('slug', $slug)->exists()) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }

        Category::create([
            'name' => $request->name,
            'slug' => $slug,
            'type' => $request->type,
            'description' => $request->description,
        ]);

        $tab = match($request->type) {
            'product' => 'kategori_produk',
            'income' => 'kategori_pemasukan',
            'expense' => 'kategori_pengeluaran',
            default => 'kategori_produk',
        };

        return redirect()->route('settings.index', ['tab' => $tab])
            ->with('success', "Kategori '{$request->name}' berhasil ditambahkan.");
    }

    public function update(CategoryRequest $request, Category $category): RedirectResponse
    {
        $baseSlug = Str::slug($request->name);
        $slug = $baseSlug;
        $counter = 1;

        while (Category::where('slug', $slug)->where('id', '!=', $category->id)->exists()) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }

        $category->update([
            'name' => $request->name,
            'slug' => $slug,
            'type' => $request->type,
            'description' => $request->description,
        ]);

        $tab = match($request->type) {
            'product' => 'kategori_produk',
            'income' => 'kategori_pemasukan',
            'expense' => 'kategori_pengeluaran',
            default => 'kategori_produk',
        };

        return redirect()->route('settings.index', ['tab' => $tab])
            ->with('success', "Kategori '{$request->name}' berhasil diperbarui.");
    }

    public function destroy(Category $category): RedirectResponse
    {
        // Cek relasi produk dan transaksi
        $productCount = $category->products()->count();
        $transactionCount = $category->transactions()->count();

        $tab = match($category->type) {
            'product' => 'kategori_produk',
            'income' => 'kategori_pemasukan',
            'expense' => 'kategori_pengeluaran',
            default => 'kategori_produk',
        };

        if ($productCount > 0 || $transactionCount > 0) {
            $message = "Kategori '{$category->name}' tidak dapat dihapus karena masih terkait dengan ";
            $parts = [];
            if ($productCount > 0) $parts[] = "{$productCount} produk";
            if ($transactionCount > 0) $parts[] = "{$transactionCount} transaksi";
            $message .= implode(' dan ', $parts) . ".";

            return redirect()->route('settings.index', ['tab' => $tab])
                ->with('error', $message);
        }

        $name = $category->name;
        $category->delete();

        return redirect()->route('settings.index', ['tab' => $tab])
            ->with('success', "Kategori '{$name}' berhasil dihapus.");
    }
}
