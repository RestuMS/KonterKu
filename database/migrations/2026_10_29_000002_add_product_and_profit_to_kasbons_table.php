<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('kasbons', function (Blueprint $table) {
            $table->foreignId('product_id')->nullable()->after('amount')->constrained('products')->nullOnDelete();
            $table->string('product_name')->nullable()->after('product_id');
            $table->integer('quantity')->default(1)->after('product_name');
            $table->decimal('cost_price', 14, 2)->default(0)->after('quantity');
            $table->decimal('profit', 14, 2)->default(0)->after('cost_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kasbons', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->dropColumn(['product_id', 'product_name', 'quantity', 'cost_price', 'profit']);
        });
    }
};
