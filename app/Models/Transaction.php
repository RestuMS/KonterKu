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
}
