<?php

namespace App\Services;

use App\Models\Product;
use App\Models\StockMutation;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class StockService
{
    /**
     * Kurangi stok produk (misal: saat transaksi penjualan).
     */
    public function reduceStock(
        Product $product,
        int $quantity,
        ?string $referenceType = null,
        ?int $referenceId = null,
        ?string $notes = null,
        ?int $userId = null
    ): StockMutation {
        if ($quantity <= 0) {
            throw new InvalidArgumentException('Jumlah pengurangan stok harus lebih dari 0.');
        }

        return DB::transaction(function () use ($product, $quantity, $referenceType, $referenceId, $notes, $userId) {
            $product->refresh();
            $stockBefore = $product->stock;
            $stockAfter = $stockBefore - $quantity;

            $product->update(['stock' => $stockAfter]);

            return StockMutation::create([
                'product_id' => $product->id,
                'user_id' => $userId,
                'type' => 'out',
                'quantity' => $quantity,
                'stock_before' => $stockBefore,
                'stock_after' => $stockAfter,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'notes' => $notes,
            ]);
        });
    }

    /**
     * Tambah stok produk (misal: saat kulakan / restock).
     */
    public function addStock(
        Product $product,
        int $quantity,
        ?string $referenceType = null,
        ?int $referenceId = null,
        ?string $notes = null,
        ?int $userId = null
    ): StockMutation {
        if ($quantity <= 0) {
            throw new InvalidArgumentException('Jumlah penambahan stok harus lebih dari 0.');
        }

        return DB::transaction(function () use ($product, $quantity, $referenceType, $referenceId, $notes, $userId) {
            $product->refresh();
            $stockBefore = $product->stock;
            $stockAfter = $stockBefore + $quantity;

            $product->update(['stock' => $stockAfter]);

            return StockMutation::create([
                'product_id' => $product->id,
                'user_id' => $userId,
                'type' => 'in',
                'quantity' => $quantity,
                'stock_before' => $stockBefore,
                'stock_after' => $stockAfter,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'notes' => $notes,
            ]);
        });
    }

    /**
     * Penyesuaian stok manual (stock opname).
     */
    public function adjustStock(
        Product $product,
        int $newStock,
        ?string $notes = null,
        ?int $userId = null
    ): StockMutation {
        if ($newStock < 0) {
            throw new InvalidArgumentException('Nilai stok tidak boleh negatif.');
        }

        return DB::transaction(function () use ($product, $newStock, $notes, $userId) {
            $product->refresh();
            $stockBefore = $product->stock;
            $diff = $newStock - $stockBefore;

            $product->update(['stock' => $newStock]);

            return StockMutation::create([
                'product_id' => $product->id,
                'user_id' => $userId,
                'type' => 'adjustment',
                'quantity' => abs($diff),
                'stock_before' => $stockBefore,
                'stock_after' => $newStock,
                'reference_type' => null,
                'reference_id' => null,
                'notes' => $notes ?? 'Penyesuaian stok fisik (Opname)',
            ]);
        });
    }
}
