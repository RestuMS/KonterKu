<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE categories MODIFY COLUMN type VARCHAR(50) NOT NULL DEFAULT 'product'");

        // Update kategori pengeluaran yang sudah ada agar bertipe 'expense'
        DB::table('categories')
            ->whereIn('slug', ['jajan', 'beli-kuota', 'beli-mainan', 'beli-bensin', 'operasional-toko'])
            ->update(['type' => 'expense']);

        // Update kategori transaksi pemasukan jika ada
        DB::table('categories')
            ->whereIn('slug', ['transfer-uang', 'tarik-tunai'])
            ->update(['type' => 'income']);
    }

    public function down(): void
    {
        // Revert jika perlu
    }
};
