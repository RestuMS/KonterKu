<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Setting;
use App\Models\StockMutation;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatabaseSchemaTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_user_with_role_and_phone(): void
    {
        $user = User::factory()->create([
            'role' => 'kasir',
            'phone' => '081298765432',
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'role' => 'kasir',
            'phone' => '081298765432',
        ]);
    }

    public function test_product_belongs_to_category_and_stores_decimal_and_integer_values(): void
    {
        $category = Category::factory()->create([
            'name' => 'Aksesoris',
            'type' => 'product',
        ]);

        $product = Product::factory()->create([
            'category_id' => $category->id,
            'code' => 'ACC-001',
            'name' => 'Kabel Type-C 1M',
            'cost_price' => 12500.50,
            'selling_price' => 25000.00,
            'stock' => 15,
            'min_stock' => 3,
            'unit' => 'pcs',
        ]);

        $this->assertEquals($category->id, $product->category->id);
        $this->assertSame('12500.50', (string) $product->cost_price);
        $this->assertSame('25000.00', (string) $product->selling_price);
        $this->assertIsInt($product->stock);
        $this->assertEquals(15, $product->stock);
    }

    public function test_transaction_with_items_and_stock_mutation(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['type' => 'all']);
        $product = Product::factory()->create([
            'category_id' => $category->id,
            'stock' => 10,
        ]);

        $transaction = Transaction::create([
            'invoice_number' => 'INV-TEST-001',
            'type' => 'pemasukan',
            'category_id' => $category->id,
            'user_id' => $user->id,
            'transaction_date' => now(),
            'total_amount' => 50000.00,
            'payment_method' => 'cash',
        ]);

        $item = TransactionItem::create([
            'transaction_id' => $transaction->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'quantity' => 2,
            'cost_price' => $product->cost_price,
            'unit_price' => 25000.00,
            'subtotal' => 50000.00,
        ]);

        $mutation = StockMutation::create([
            'product_id' => $product->id,
            'user_id' => $user->id,
            'type' => 'out',
            'quantity' => 2,
            'stock_before' => 10,
            'stock_after' => 8,
            'reference_type' => Transaction::class,
            'reference_id' => $transaction->id,
            'notes' => 'Penjualan ' . $transaction->invoice_number,
        ]);

        $this->assertCount(1, $transaction->items);
        $this->assertEquals($item->id, $transaction->items->first()->id);
        $this->assertDatabaseHas('stock_mutations', [
            'id' => $mutation->id,
            'product_id' => $product->id,
            'stock_after' => 8,
        ]);
    }

    public function test_settings_get_and_set(): void
    {
        Setting::set('store_title', 'Konter Mantap');
        $this->assertEquals('Konter Mantap', Setting::get('store_title'));
        $this->assertEquals('default_val', Setting::get('non_existing_key', 'default_val'));
    }
}
