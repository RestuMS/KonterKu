<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\StockMutation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductStockLogicTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_categories_are_auto_seeded_when_visiting_product_create_with_empty_table(): void
    {
        Category::query()->delete();
        $this->assertEquals(0, Category::count());

        $response = $this->actingAs($this->user)->get(route('products.create'));

        $response->assertOk();
        $this->assertGreaterThan(0, Category::count());
        $this->assertDatabaseHas('categories', ['slug' => 'pulsa-telkomsel']);
        $this->assertDatabaseHas('categories', ['slug' => 'voucher-telkomsel']);
        $this->assertDatabaseHas('categories', ['slug' => 'aksesoris-hp']);
    }

    public function test_physical_product_creates_initial_stock_mutation(): void
    {
        $category = Category::create([
            'name' => 'Voucher Telkomsel',
            'slug' => 'voucher-telkomsel-test',
            'type' => 'product',
        ]);

        $payload = [
            'category_id' => $category->id,
            'name' => 'Voucher Tsel 5GB',
            'code' => 'VCR-TSEL-5GB',
            'cost_price' => 15000,
            'selling_price' => 18000,
            'stock' => 20,
            'min_stock' => 5,
            'unit' => 'voucher',
            'is_active' => '1',
        ];

        $response = $this->actingAs($this->user)->post(route('products.store'), $payload);

        $response->assertRedirect(route('products.index'));

        $product = Product::where('code', 'VCR-TSEL-5GB')->firstOrFail();
        $this->assertEquals(20, $product->stock);

        $this->assertDatabaseHas('stock_mutations', [
            'product_id' => $product->id,
            'type' => 'in',
            'quantity' => 20,
            'stock_before' => 0,
            'stock_after' => 20,
            'notes' => 'Stok awal produk baru',
        ]);
    }

    public function test_digital_product_with_zero_stock_does_not_create_stock_mutation(): void
    {
        $category = Category::create([
            'name' => 'Pulsa Telkomsel',
            'slug' => 'pulsa-telkomsel-test',
            'type' => 'product',
        ]);

        $payload = [
            'category_id' => $category->id,
            'name' => 'Pulsa Telkomsel 10.000',
            'code' => 'PULSA-TSEL-10K',
            'cost_price' => 10200,
            'selling_price' => 12000,
            'stock' => 0,
            'min_stock' => 0,
            'unit' => 'transaksi',
            'is_active' => '1',
        ];

        $response = $this->actingAs($this->user)->post(route('products.store'), $payload);

        $response->assertRedirect(route('products.index'));

        $product = Product::where('code', 'PULSA-TSEL-10K')->firstOrFail();
        $this->assertEquals(0, $product->stock);

        $this->assertDatabaseMissing('stock_mutations', [
            'product_id' => $product->id,
        ]);
    }

    public function test_multi_product_batch_insert_handles_both_physical_and_digital(): void
    {
        $catFisik = Category::create([
            'name' => 'Voucher Tri',
            'slug' => 'voucher-tri-test',
            'type' => 'product',
        ]);

        $catDigital = Category::create([
            'name' => 'Pulsa Tri',
            'slug' => 'pulsa-tri-test',
            'type' => 'product',
        ]);

        $payload = [
            'items' => [
                [
                    'category_id' => $catFisik->id,
                    'name' => 'Voucher Tri 10GB',
                    'code' => 'VCR-TRI-10GB',
                    'cost_price' => 20000,
                    'selling_price' => 24000,
                    'stock' => 15,
                    'min_stock' => 3,
                    'unit' => 'voucher',
                    'is_active' => '1',
                ],
                [
                    'category_id' => $catDigital->id,
                    'name' => 'Pulsa Tri 5.000',
                    'code' => 'PULSA-TRI-5K',
                    'cost_price' => 5100,
                    'selling_price' => 7000,
                    'stock' => 0,
                    'min_stock' => 0,
                    'unit' => 'transaksi',
                    'is_active' => '1',
                ],
            ]
        ];

        $response = $this->actingAs($this->user)->post(route('products.store'), $payload);

        $response->assertRedirect(route('products.index'));

        $this->assertDatabaseHas('products', ['code' => 'VCR-TRI-10GB', 'stock' => 15]);
        $this->assertDatabaseHas('products', ['code' => 'PULSA-TRI-5K', 'stock' => 0]);

        $prodFisik = Product::where('code', 'VCR-TRI-10GB')->first();
        $prodDigital = Product::where('code', 'PULSA-TRI-5K')->first();

        $this->assertDatabaseHas('stock_mutations', [
            'product_id' => $prodFisik->id,
            'type' => 'in',
            'quantity' => 15,
        ]);

        $this->assertDatabaseMissing('stock_mutations', [
            'product_id' => $prodDigital->id,
        ]);
    }

    public function test_low_stock_filter_excludes_digital_products_with_zero_stock(): void
    {
        $cat = Category::create(['name' => 'Test', 'slug' => 'test-cat', 'type' => 'product']);

        Product::create([
            'category_id' => $cat->id,
            'name' => 'Pulsa Test',
            'code' => 'PULSA-TEST',
            'cost_price' => 5000,
            'selling_price' => 7000,
            'stock' => 0,
            'min_stock' => 0,
            'unit' => 'transaksi',
            'is_active' => true,
        ]);

        Product::create([
            'category_id' => $cat->id,
            'name' => 'Voucher Menipis',
            'code' => 'VCR-MENIPIS',
            'cost_price' => 10000,
            'selling_price' => 12000,
            'stock' => 2,
            'min_stock' => 5,
            'unit' => 'voucher',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->user)->get(route('products.index', ['low_stock' => '1']));

        $response->assertOk();
        $response->assertSee('VCR-MENIPIS');
        $response->assertDontSee('PULSA-TEST');
    }

    public function test_batch_product_creation_with_tri_and_byu_does_not_conflict(): void
    {
        $catTri = Category::create(['name' => 'Pulsa Tri', 'slug' => 'pulsa-tri-test', 'type' => 'product']);
        $catByu = Category::create(['name' => 'Pulsa By.U', 'slug' => 'pulsa-byu-test', 'type' => 'product']);

        $payload = [
            'items' => [
                [
                    'category_id' => $catTri->id,
                    'name' => 'Pulsa Tri 5.000',
                    'code' => 'PULSA-TRI-5K',
                    'cost_price' => 5100,
                    'selling_price' => 7000,
                    'stock' => 0,
                    'min_stock' => 0,
                    'unit' => 'transaksi',
                    'is_active' => '1',
                ],
                [
                    'category_id' => $catByu->id,
                    'name' => 'Pulsa By.U 5.000',
                    'code' => 'PULSA-BYU-5K',
                    'cost_price' => 5150,
                    'selling_price' => 7000,
                    'stock' => 0,
                    'min_stock' => 0,
                    'unit' => 'transaksi',
                    'is_active' => '1',
                ],
            ]
        ];

        $response = $this->actingAs($this->user)->post(route('products.store'), $payload);

        $response->assertRedirect(route('products.index'));
        $this->assertDatabaseHas('products', ['code' => 'PULSA-TRI-5K']);
        $this->assertDatabaseHas('products', ['code' => 'PULSA-BYU-5K']);
    }

    public function test_products_are_ordered_by_category_and_selling_price_ascending(): void
    {
        $cat = Category::firstOrCreate(
            ['slug' => 'e-wallet-dana'],
            ['name' => 'E-Wallet DANA', 'type' => 'product']
        );

        // Input data secara acak (15k duluan, baru 5k, 20k, 10k)
        $p15 = Product::create([
            'category_id' => $cat->id,
            'name' => 'E-Wallet DANA 15.000',
            'code' => 'DANA-15K',
            'cost_price' => 15068,
            'selling_price' => 17000,
            'stock' => 0,
            'unit' => 'transaksi',
            'is_active' => true,
        ]);

        $p5 = Product::create([
            'category_id' => $cat->id,
            'name' => 'E-Wallet DANA 5.000',
            'code' => 'DANA-5K',
            'cost_price' => 5068,
            'selling_price' => 7000,
            'stock' => 0,
            'unit' => 'transaksi',
            'is_active' => true,
        ]);

        $p20 = Product::create([
            'category_id' => $cat->id,
            'name' => 'E-Wallet DANA 20.000',
            'code' => 'DANA-20K',
            'cost_price' => 20068,
            'selling_price' => 22000,
            'stock' => 0,
            'unit' => 'transaksi',
            'is_active' => true,
        ]);

        $p10 = Product::create([
            'category_id' => $cat->id,
            'name' => 'E-Wallet DANA 10.000',
            'code' => 'DANA-10K',
            'cost_price' => 10068,
            'selling_price' => 12000,
            'stock' => 0,
            'unit' => 'transaksi',
            'is_active' => true,
        ]);

        // Verifikasi pada ProductController (index)
        $response = $this->actingAs($this->user)->get(route('products.index', ['category_id' => $cat->id]));
        $response->assertOk();
        $productsInView = $response->viewData('products');
        $this->assertEquals([$p5->id, $p10->id, $p15->id, $p20->id], $productsInView->pluck('id')->values()->toArray());

        // Verifikasi pada IncomeController (create kasir)
        $responseCashier = $this->actingAs($this->user)->get(route('incomes.create'));
        $responseCashier->assertOk();
        $cashierProducts = $responseCashier->viewData('products')->where('category_id', $cat->id);
        $this->assertEquals([$p5->id, $p10->id, $p15->id, $p20->id], $cashierProducts->pluck('id')->values()->toArray());
    }
}

