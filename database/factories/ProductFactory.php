<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $costPrice = fake()->numberBetween(5000, 100000);
        $sellingPrice = $costPrice + fake()->numberBetween(2000, 25000);

        return [
            'category_id' => Category::factory(),
            'code' => 'PRD-' . fake()->unique()->numerify('#####'),
            'name' => fake()->words(3, true),
            'cost_price' => $costPrice,
            'selling_price' => $sellingPrice,
            'stock' => fake()->numberBetween(10, 100),
            'min_stock' => 5,
            'unit' => fake()->randomElement(['pcs', 'voucher', 'kartu']),
            'is_active' => true,
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
