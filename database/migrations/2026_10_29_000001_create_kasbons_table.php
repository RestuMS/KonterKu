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
        Schema::create('kasbons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('customer_name')->index();
            $table->string('customer_phone', 25)->nullable();
            $table->decimal('amount', 14, 2)->default(0);
            $table->date('debt_date')->index();
            $table->date('due_date')->nullable()->index();
            $table->text('description')->nullable();
            $table->enum('status', ['unpaid', 'paid'])->default('unpaid')->index();
            $table->dateTime('paid_at')->nullable()->index();
            $table->string('payment_method', 30)->nullable();
            $table->foreignId('transaction_id')->nullable()->constrained('transactions')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kasbons');
    }
};
