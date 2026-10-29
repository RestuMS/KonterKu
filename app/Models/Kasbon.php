<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Kasbon extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'customer_name',
        'customer_phone',
        'product_id',
        'product_name',
        'quantity',
        'cost_price',
        'amount',
        'profit',
        'debt_date',
        'due_date',
        'description',
        'status',
        'paid_at',
        'payment_method',
        'transaction_id',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'cost_price' => 'decimal:2',
            'profit' => 'decimal:2',
            'quantity' => 'integer',
            'debt_date' => 'date',
            'due_date' => 'date',
            'paid_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    public function scopeUnpaid($query)
    {
        return $query->where('status', 'unpaid');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function getIsPaidAttribute(): bool
    {
        return $this->status === 'paid';
    }

    public function getIsOverdueAttribute(): bool
    {
        if ($this->is_paid || empty($this->due_date)) {
            return false;
        }

        return Carbon::parse($this->due_date)->isPast() && !Carbon::parse($this->due_date)->isToday();
    }

    /**
     * Format nomor HP agar standar internasional 62xxx untuk link WhatsApp
     */
    public function getCleanPhoneAttribute(): ?string
    {
        if (empty($this->customer_phone)) {
            return null;
        }

        $phone = preg_replace('/[^0-9]/', '', $this->customer_phone);

        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        } elseif (!str_starts_with($phone, '62') && strlen($phone) > 6) {
            $phone = '62' . $phone;
        }

        return $phone;
    }

    /**
     * Link WhatsApp Web / App dengan template pesan pengingat tagihan ramah & sopan
     */
    public function getWhatsappReminderUrlAttribute(): ?string
    {
        $phone = $this->clean_phone;
        if (!$phone) {
            return null;
        }

        $shopName = Setting::get('shop_name', 'KonterKu');
        $nominal = 'Rp ' . number_format($this->amount, 0, ',', '.');
        $tglBerhutang = $this->debt_date ? Carbon::parse($this->debt_date)->translatedFormat('d F Y') : '-';
        $barang = $this->description ? " (rincian: {$this->description})" : '';

        $message = "Halo Kak *{$this->customer_name}*,\n\n"
            . "Kami dari *{$shopName}* ingin mengonfirmasi catatan kasbon/hutang sebesar *{$nominal}* pada tanggal *{$tglBerhutang}*{$barang}.\n\n"
            . "Jika ada kelonggaran, mohon konfirmasi untuk pelunasannya ya Kak. Terima kasih banyak atas kerjasamanya 🙏😊";

        return 'https://wa.me/' . $phone . '?text=' . urlencode($message);
    }
}
