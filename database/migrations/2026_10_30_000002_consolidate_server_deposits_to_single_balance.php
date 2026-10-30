<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Hitung total saldo yang ada sebelumnya jika ada
        $totalExisting = DB::table('server_deposits')->sum('balance') ?: 1500000;

        // Kosongkan tabel dan buat 1 catatan saldo server all-provider tunggal
        DB::table('server_deposits')->truncate();

        DB::table('server_deposits')->insert([
            'name' => 'Saldo Aplikasi Server All Provider',
            'provider_type' => 'all_provider',
            'balance' => $totalExisting,
            'min_alert_balance' => 100000,
            'account_id' => 'Akun All Provider',
            'notes' => 'Saldo deposit induk untuk seluruh transaksi: pulsa, paket data, e-wallet, token PLN, BPJS, pajak, game, & voucher.',
            'last_checked_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Tidak perlu di-revert khusus
    }
};

