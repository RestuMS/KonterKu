<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Transaction>
 */
class TransactionFactory extends Factory
{
    protected $model = Transaction::class;

    public function definition(): array
    {
        $type = fake()->randomElement(['pemasukan', 'pengeluaran']);

        return [
            'invoice_number' => 'TRX-' . now()->format('Ymd') . '-' . fake()->unique()->numerify('####'),
            'type' => $type,
            'category_id' => Category::factory(),
            'user_id' => User::factory(),
            'transaction_date' => fake()->dateTimeBetween('-1 month', 'now'),
            'total_amount' => fake()->numberBetween(10000, 500000),
            'payment_method' => fake()->randomElement(['cash', 'transfer', 'qris']),
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
