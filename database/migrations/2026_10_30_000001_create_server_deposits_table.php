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
        Schema::create('server_deposits', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nama Server/Aplikasi, misal: "Digipos Telkomsel", "DANA Bisnis"
            $table->string('provider_type', 40)->default('pulsa_data'); // pulsa_data, ewallet, pln_ppob, game, voucher, other
            $table->decimal('balance', 14, 2)->default(0); // Sisa saldo deposit
            $table->decimal('min_alert_balance', 14, 2)->default(50000); // Batas peringatan saldo menipis
            $table->string('account_id', 50)->nullable(); // Nomor HP / ID Agen
            $table->text('notes')->nullable();
            $table->timestamp('last_checked_at')->nullable();
            $table->timestamps();
        });

        // Masukkan data awal representatif sesuai kebutuhan usaha konter pengguna
        DB::table('server_deposits')->insert([
            [
                'name' => 'Server Pulsa & Paket Data (Digipos / SiDOMPUL)',
                'provider_type' => 'pulsa_data',
                'balance' => 450000,
                'min_alert_balance' => 100000,
                'account_id' => '081234567890',
                'notes' => 'Chip kuota & pulsa all operator',
                'last_checked_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'E-Wallet Mitra (DANA / GoPay / ShopeePay)',
                'provider_type' => 'ewallet',
                'balance' => 625000,
                'min_alert_balance' => 100000,
                'account_id' => 'DANA Bisnis Konter',
                'notes' => 'Top up saldo dompet digital pelanggan',
                'last_checked_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'PPOB, Token PLN, BPJS & Pajak',
                'provider_type' => 'pln_ppob',
                'balance' => 320000,
                'min_alert_balance' => 50000,
                'account_id' => 'ID PPOB-9921',
                'notes' => 'Pembayaran tagihan listrik, BPJS & PBB',
                'last_checked_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Server Top-Up Game (ML, FF, PUBG)',
                'provider_type' => 'game',
                'balance' => 180000,
                'min_alert_balance' => 50000,
                'account_id' => 'Akun UniPin / Mitra Game',
                'notes' => 'Isi diamond dan voucher game online',
                'last_checked_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Stok Voucher Fisik & Perdana',
                'provider_type' => 'voucher',
                'balance' => 850000,
                'min_alert_balance' => 150000,
                'account_id' => 'Etalase Depan',
                'notes' => 'Estimasi modal fisik voucher gosok & kartu perdana',
                'last_checked_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('server_deposits');
    }
};

