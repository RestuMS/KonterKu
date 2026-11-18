<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'type',
        'category_id',
        'user_id',
        'transaction_date',
        'total_amount',
        'payment_method',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'transaction_date' => 'datetime',
            'total_amount' => 'decimal:2',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(TransactionItem::class);
    }

    public function getCostPriceAttribute(): float
    {
        if ($this->type === 'pengeluaran') {
            return (float) $this->total_amount;
        }

        if ($this->relationLoaded('items') ? $this->items->isNotEmpty() : $this->items()->exists()) {
            return (float) $this->items->sum(function ($item) {
                return (float) $item->cost_price * (int) $item->quantity;
            });
        }

        return 0.0;
    }

    public function getSellingPriceAttribute(): float
    {
        if ($this->type === 'pemasukan') {
            return (float) $this->total_amount;
        }

        return 0.0;
    }

    public function getProfitAttribute(): float
    {
        if ($this->type === 'pengeluaran') {
            return -(float) $this->total_amount;
        }

        return $this->selling_price - $this->cost_price;
    }

    /**
     * Cek apakah transaksi dicatat mundur (tanggal transaksi lebih lampau dari tanggal input sistem).
     */
    public function isBackdated(): bool
    {
        if (!$this->transaction_date || !$this->created_at) {
            return false;
        }

        return $this->transaction_date->toDateString() < $this->created_at->toDateString();
    }

    /**
     * Cek apakah transaksi pengeluaran merupakan belanja stok persediaan / kulakan saldo server (Aset Toko).
     * Kategori operasional standar (jajan, bensin, kuota, mainan, operasional toko) tidak dianggap kulakan
     * kecuali secara eksplisit ditandai oleh sistem sebagai [ORDER SALES] atau top-up deposit.
     */
    public function isKulakan(): bool
    {
        if ($this->type !== 'pengeluaran') {
            return false;
        }

        $slug = strtolower($this->category?->slug ?? '');
        $catName = strtolower($this->category?->name ?? '');
        $notes = strtolower($this->notes ?? '');

        // 1. Kategori kulakan / aset persediaan eksplisit
        if (
            in_array($slug, ['kulakan-sales', 'kulakan-deposit'])
            || str_contains($slug, 'kulakan')
            || str_contains($catName, 'kulakan')
            || str_contains($catName, 'tagihan sales')
            || str_contains($catName, 'deposit')
        ) {
            return true;
        }

        // 2. Tag sistem otomatis dari fitur restock / inject / top-up saldo server
        if (
            str_contains($notes, '[order sales]')
            || str_contains($notes, 'top-up saldo deposit')
            || str_contains($notes, 'top-up saldo server')
        ) {
            return true;
        }

        // 3. Jika kategori operasional standar dipilih eksplisit oleh kasir, jangan timpa dengan kata umum di catatan
        $operationalSlugs = ['jajan', 'beli-kuota', 'beli-mainan', 'beli-bensin', 'operasional-toko'];
        if (in_array($slug, $operationalSlugs)) {
            return false;
        }

        // 4. Fallback untuk transaksi tanpa kategori jelas / kategori custom
        return str_contains($notes, 'kulakan')
            || str_contains($notes, 'top-up saldo')
            || str_contains($notes, 'isi saldo');
    }

    /**
     * Cek apakah transaksi pemasukan merupakan layanan Tarik Tunai (Cash Out)
     * di mana kasir menyerahkan uang tunai pokok dari laci kepada pelanggan.
     */
    public function isTarikTunai(): bool
    {
        if ($this->type !== 'pemasukan') {
            return false;
        }

        $slug = strtolower($this->category?->slug ?? '');
        $catName = strtolower($this->category?->name ?? '');
        $notes = strtolower($this->notes ?? '');

        return $slug === 'tarik-tunai'
            || str_contains($slug, 'tarik-tunai')
            || str_contains($catName, 'tarik tunai')
            || str_contains($notes, 'layanan tarik tunai');
    }
}
