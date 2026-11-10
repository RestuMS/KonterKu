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
        ?int $userId = null,
        ?\Carbon\Carbon $mutationDate = null
    ): StockMutation {
        if ($quantity <= 0) {
            throw new InvalidArgumentException('Jumlah pengurangan stok harus lebih dari 0.');
        }

        return DB::transaction(function () use ($product, $quantity, $referenceType, $referenceId, $notes, $userId, $mutationDate) {
            $product->refresh();
            $stockBefore = $product->stock;
            $stockAfter = $stockBefore - $quantity;

            $product->update(['stock' => $stockAfter]);

            $mutation = new StockMutation([
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

            if ($mutationDate) {
                $mutation->created_at = $mutationDate;
                $mutation->updated_at = $mutationDate;
            }

            $mutation->save();

            return $mutation;
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
        ?int $userId = null,
        ?\Carbon\Carbon $mutationDate = null
    ): StockMutation {
        if ($quantity <= 0) {
            throw new InvalidArgumentException('Jumlah penambahan stok harus lebih dari 0.');
        }

        return DB::transaction(function () use ($product, $quantity, $referenceType, $referenceId, $notes, $userId, $mutationDate) {
            $product->refresh();
            $stockBefore = $product->stock;
            $stockAfter = $stockBefore + $quantity;

            $product->update(['stock' => $stockAfter]);

            $mutation = new StockMutation([
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

            if ($mutationDate) {
                $mutation->created_at = $mutationDate;
                $mutation->updated_at = $mutationDate;
            }

            $mutation->save();

            return $mutation;
        });
    }

    /**
     * Inject voucher kosong: Menambah stok fisik voucher dan memotong saldo server secara atomik.
     *
     * @return array{product: Product, quantity: int, total_cost: float, stock_mutation: StockMutation, server_mutation: \App\Models\ServerDepositMutation, deposit: \App\Models\ServerDeposit}
     */
    public function injectVoucher(
        Product $product,
        int $quantity,
        float $costPerUnit,
        ?string $notes = null,
        ?int $userId = null,
        ?\Carbon\Carbon $injectDate = null
    ): array {
        if ($quantity <= 0) {
            throw new InvalidArgumentException('Jumlah voucher yang di-inject harus lebih dari 0.');
        }

        if ($costPerUnit < 0) {
            throw new InvalidArgumentException('Harga modal tidak boleh bernilai negatif.');
        }

        return DB::transaction(function () use ($product, $quantity, $costPerUnit, $notes, $userId, $injectDate) {
            $totalCost = (float) ($quantity * $costPerUnit);
            $date = $injectDate ?? now();

            // 1. Potong Saldo Server
            $serverNotes = "[INJECT VOUCHER] Inject {$quantity} pcs {$product->name} (Masuk Stok Fisik)" . ($notes ? " - {$notes}" : '');
            $deposit = \App\Models\ServerDeposit::deductBalance(
                $totalCost,
                $serverNotes,
                StockMutation::class,
                null,
                $date
            );

            // Ambil mutasi saldo server yang baru saja dibuat
            $serverMutation = \App\Models\ServerDepositMutation::where('server_deposit_id', \App\Models\ServerDeposit::getPrimary()->id)
                ->where('type', 'out')
                ->latest('id')
                ->first();

            // 2. Tambah Stok Fisik Produk
            $stockNotes = "[INJECT VOUCHER] Inject {$quantity} pcs dari Saldo Server (Modal: Rp " . number_format($totalCost, 0, ',', '.') . ")" . ($notes ? " - {$notes}" : '');
            $stockMutation = $this->addStock(
                $product,
                $quantity,
                \App\Models\ServerDepositMutation::class,
                $serverMutation?->id,
                $stockNotes,
                $userId,
                $date
            );

            // 3. Tautkan ID mutasi stok ke mutasi saldo server
            if ($serverMutation && $stockMutation) {
                $serverMutation->update(['reference_id' => $stockMutation->id]);
            }

            return [
                'product' => $product,
                'quantity' => $quantity,
                'total_cost' => $totalCost,
                'stock_mutation' => $stockMutation,
                'server_mutation' => $serverMutation,
                'deposit' => $deposit,
            ];
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

    /**
     * Catat pengeluaran kulakan / order barang dari Sales ke tabel transaksi pengeluaran.
     */
    public function recordKulakanExpense(
        float $amount,
        string $paymentMethod = 'cash',
        string $description = 'Kulakan / Order Stok dari Sales',
        ?int $userId = null,
        ?\Carbon\Carbon $transactionDate = null
    ): \App\Models\Transaction {
        if ($amount <= 0) {
            throw new InvalidArgumentException('Nominal pengeluaran kulakan harus lebih dari 0.');
        }

        $txDate = $transactionDate ?? now();
        $method = in_array($paymentMethod, ['cash', 'transfer']) ? $paymentMethod : 'cash';

        $expenseCategory = \App\Models\Category::firstOrCreate(
            ['slug' => 'kulakan-sales'],
            [
                'name' => 'Tagihan Sales',
                'type' => 'expense',
                'description' => 'Pembayaran tagihan & kulakan stok barang konter atau voucher dari sales/distributor',
            ]
        );

        $invoiceNumber = 'EXP-' . $txDate->format('Ymd') . '-' . strtoupper(substr(uniqid('', true), -5));

        return \App\Models\Transaction::create([
            'invoice_number' => $invoiceNumber,
            'type' => 'pengeluaran',
            'category_id' => $expenseCategory->id,
            'user_id' => $userId ?? auth()->id(),
            'transaction_date' => $txDate,
            'total_amount' => $amount,
            'payment_method' => $method,
            'notes' => $description,
        ]);
    }
}
