<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServerDepositMutation extends Model
{
    use HasFactory;

    protected $fillable = [
        'server_deposit_id',
        'user_id',
        'type',
        'amount',
        'balance_before',
        'balance_after',
        'notes',
        'reference_type',
        'reference_id',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'balance_before' => 'decimal:2',
            'balance_after' => 'decimal:2',
        ];
    }

    public function serverDeposit(): BelongsTo
    {
        return $this->belongsTo(ServerDeposit::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

