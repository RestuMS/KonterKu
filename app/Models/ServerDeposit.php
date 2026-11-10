<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServerDeposit extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'provider_type',
        'balance',
        'min_alert_balance',
        'account_id',
        'notes',
        'last_checked_at',
    ];

    protected function casts(): array
    {
        return [
            'balance' => 'decimal:2',
            'min_alert_balance' => 'decimal:2',
            'last_checked_at' => 'datetime',
        ];
    }

    public function mutations(): HasMany
    {
        return $this->hasMany(ServerDepositMutation::class)->latest('id');
    }

    /**
     * Dapatkan satu akun saldo server utama (All Provider).
     */
    public static function getPrimary(): self
    {
        $deposit = self::first();

        if (!$deposit) {
            $deposit = self::create([
                'name' => 'Saldo Aplikasi Server All Provider',
                'provider_type' => 'all_provider',
                'balance' => 1500000,
                'min_alert_balance' => 100000,
                'account_id' => 'Akun All Provider',
                'notes' => 'Saldo deposit induk untuk seluruh transaksi: pulsa, paket data, e-wallet, token PLN, BPJS, pajak, game, & voucher.',
                'last_checked_at' => now(),
            ]);
        }

        return $deposit;
    }

    /**
     * Tentukan apakah transaksi / produk ini menggunakan saldo server all provider.
     * Produk fisik konter (aksesoris hp, casing, tempered glass) dan Voucher Fisik yang telah di-inject TIDAK memotong saldo server saat transaksi kasir.
     */
    public static function isServerProduct(?Category $category = null, ?string $productName = null, ?Product $product = null): bool
    {
        $catText = strtolower(($category?->name ?? '') . ' ' . ($category?->slug ?? ''));
        $prodText = strtolower($productName ?? '');
        $combined = trim($catText . ' ' . $prodText);

        if (empty($combined)) {
            return false;
        }

        // Kategori fisik / jasa servis konter yang BUKAN saldo server
        $nonServerKeywords = [
            'aksesoris', 'casing', 'case', 'kabel', 'charger', 'headset', 
            'earphone', 'tempered', 'servis', 'service', 'jasa', 'sparepart', 
            'baterai', 'lcd', 'mainan', 'jajan', 'bensin', 'operasional'
        ];
        foreach ($nonServerKeywords as $kw) {
            if (str_contains($combined, $kw)) {
                return false;
            }
        }

        // Produk fisik (Voucher Fisik maupun Kartu Perdana, bukan layanan digital unit 'transaksi'/'trx')
        // yang stoknya berasal dari INJECT VOUCHER (saldo server sudah dipotong di awal) ATAU dari
        // kulakan fisik / ORDER SALES / input stok produk, TIDAK boleh memotong saldo server saat penjualan kasir/kasbon.
        $isPhysicalVoucherOrPerdana = (
            str_contains($catText, 'voucher') || str_contains($prodText, 'voucher') ||
            str_contains($catText, 'perdana') || str_contains($prodText, 'perdana')
        );

        if ($isPhysicalVoucherOrPerdana && $product && !in_array(strtolower($product->unit ?? ''), ['transaksi', 'trx'])) {
            $hasPrePaidPhysicalStock = \App\Models\StockMutation::where('product_id', $product->id)
                ->where(function ($q) {
                    $q->where('type', 'in')
                      ->orWhere(function ($adj) {
                          $adj->where('type', 'adjustment')
                              ->whereColumn('stock_after', '>', 'stock_before');
                      });
                })
                ->exists();

            if ($hasPrePaidPhysicalStock) {
                return false;
            }
        }

        // Layanan server digital all provider konter
        $serverKeywords = [
            'pulsa', 'paket', 'data', 'kuota', 'internet', 'token', 'pln', 'listrik', 
            'wallet', 'ewallet', 'dana', 'gopay', 'shopee', 'ovo', 'seabank', 
            'transfer', 'tarik', 'voucher', 'perdana', 'game', 'bpjs', 'pajak', 'ppob',
            'wifi', 'indihome', 'iconnet', 'myrepublic', 'firstmedia', 'tagihan',
            'free fire', 'mobile legends', 'diamond'
        ];
        foreach ($serverKeywords as $kw) {
            if (str_contains($combined, $kw)) {
                return true;
            }
        }

        // Default: jika tidak ada kata kunci server, jangan potong saldo server
        return false;
    }

    /**
     * Kurangi saldo server saat ada transaksi pemasukan / penjualan kasir.
     */
    public static function deductBalance(
        float $amount, 
        string $reason = '', 
        ?string $refType = null, 
        ?int $refId = null,
        ?\Carbon\Carbon $mutationDate = null
    ): ?self
    {
        if ($amount <= 0) {
            return null;
        }

        $deposit = self::getPrimary();
        $balanceBefore = (float) $deposit->balance;
        $balanceAfter = max(0, $balanceBefore - $amount);

        $deposit->balance = $balanceAfter;
        $deposit->last_checked_at = now();
        $deposit->save();

        $mutation = new ServerDepositMutation([
            'server_deposit_id' => $deposit->id,
            'user_id' => auth()->id(),
            'type' => 'out',
            'amount' => $amount,
            'balance_before' => $balanceBefore,
            'balance_after' => $balanceAfter,
            'notes' => $reason ?: 'Pemotongan transaksi kasir',
            'reference_type' => $refType,
            'reference_id' => $refId,
        ]);

        if ($mutationDate) {
            $mutation->created_at = $mutationDate;
            $mutation->updated_at = $mutationDate;
        }

        $mutation->save();

        return $deposit;
    }

    /**
     * Tambah saldo server saat top up deposit atau pembatalan transaksi.
     */
    public static function addBalance(float $amount, string $reason = '', ?string $refType = null, ?int $refId = null): ?self
    {
        if ($amount <= 0) {
            return null;
        }

        $deposit = self::getPrimary();
        $balanceBefore = (float) $deposit->balance;
        $balanceAfter = $balanceBefore + $amount;

        $deposit->balance = $balanceAfter;
        $deposit->last_checked_at = now();
        $deposit->save();

        ServerDepositMutation::create([
            'server_deposit_id' => $deposit->id,
            'user_id' => auth()->id(),
            'type' => 'in',
            'amount' => $amount,
            'balance_before' => $balanceBefore,
            'balance_after' => $balanceAfter,
            'notes' => $reason ?: 'Top-up / pengembalian saldo deposit',
            'reference_type' => $refType,
            'reference_id' => $refId,
        ]);

        return $deposit;
    }

    /**
     * Atur / koreksi nominal saldo secara langsung.
     */
    public static function setBalance(float $newBalance, string $reason = ''): self
    {
        $deposit = self::getPrimary();
        $balanceBefore = (float) $deposit->balance;
        $balanceAfter = max(0, $newBalance);
        $diff = abs($balanceAfter - $balanceBefore);

        $deposit->balance = $balanceAfter;
        $deposit->last_checked_at = now();
        $deposit->save();

        ServerDepositMutation::create([
            'server_deposit_id' => $deposit->id,
            'user_id' => auth()->id(),
            'type' => 'set',
            'amount' => $diff,
            'balance_before' => $balanceBefore,
            'balance_after' => $balanceAfter,
            'notes' => $reason ?: 'Koreksi penyesuaian sisa saldo server',
            'reference_type' => null,
            'reference_id' => null,
        ]);

        return $deposit;
    }

    /**
     * Cek apakah saldo saat ini di bawah batas peringatan minimal.
     */
    public function isLow(): bool
    {
        return (float) $this->balance <= (float) $this->min_alert_balance;
    }

    /**
     * Label kategori berbahasa Indonesia.
     */
    public function getTypeLabelAttribute(): string
    {
        return 'All Provider (Pulsa, Kuota, E-Wallet, PPOB, Game, Voucher)';
    }
}
