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
     * Kurangi saldo server saat ada transaksi pemasukan / penjualan kasir.
     */
    public static function deductBalance(float $amount, string $reason = '', ?string $refType = null, ?int $refId = null): ?self
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

        ServerDepositMutation::create([
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
