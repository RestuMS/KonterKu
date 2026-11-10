<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\ServerDeposit;
use App\Models\ServerDepositMutation;
use App\Models\StockMutation;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VoucherInjectionTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_inject_voucher_form_renders_successfully(): void
    {
        ServerDeposit::setBalance(500000);

        $response = $this->actingAs($this->user)->get(route('stocks.inject'));

        $response->assertOk();
        $response->assertSee('Inject Stok Voucher');
        $response->assertSee('Satu Pintu');
    }

    public function test_inject_voucher_deducts_server_deposit_and_increases_physical_stock(): void
    {
        ServerDeposit::setBalance(500000);

        $category = Category::create([
            'name' => 'Voucher Tri',
            'slug' => 'voucher-tri',
            'type' => 'product',
        ]);

        $voucher = Product::create([
            'category_id' => $category->id,
            'name' => 'Voucher Tri 10GB',
            'code' => 'VCR-TRI-10GB',
            'cost_price' => 20000,
            'selling_price' => 25000,
            'stock' => 5,
            'min_stock' => 2,
            'unit' => 'pcs',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->user)->post(route('stocks.process-inject'), [
            'product_id' => $voucher->id,
            'quantity' => 10,
            'cost_price' => 20000,
            'notes' => 'Inject batch Rita seri 001-010',
        ]);

        $response->assertRedirect(route('stocks.inject'));
        $response->assertSessionHas('success');

        // 1. Saldo server harus berkurang 200.000 (10 x 20.000): 500.000 - 200.000 = 300.000
        $this->assertEquals(300000, (float) ServerDeposit::getPrimary()->balance);

        // 2. Stok fisik voucher harus bertambah 10 unit: 5 + 10 = 15
        $this->assertEquals(15, $voucher->fresh()->stock);

        // 3. Mutasi stok masuk tercatat
        $stockMutation = StockMutation::where('product_id', $voucher->id)
            ->where('type', 'in')
            ->latest('id')
            ->first();

        $this->assertNotNull($stockMutation);
        $this->assertEquals(10, $stockMutation->quantity);
        $this->assertStringContainsString('[INJECT VOUCHER]', $stockMutation->notes);

        // 4. Mutasi saldo server keluar tercatat
        $serverMutation = ServerDepositMutation::where('type', 'out')->latest('id')->first();
        $this->assertNotNull($serverMutation);
        $this->assertEquals(200000, (float) $serverMutation->amount);
        $this->assertStringContainsString('[INJECT VOUCHER]', $serverMutation->notes);

        // 5. Kedua mutasi saling terhubung
        $this->assertEquals($serverMutation->id, $stockMutation->reference_id);
        $this->assertEquals($stockMutation->id, $serverMutation->reference_id);
    }

    public function test_selling_injected_physical_voucher_reduces_stock_and_does_not_double_deduct_server_deposit(): void
    {
        // Kondisi awal: Saldo server 500.000
        ServerDeposit::setBalance(500000);

        $category = Category::create([
            'name' => 'Voucher Tri',
            'slug' => 'voucher-tri',
            'type' => 'product',
        ]);

        $voucher = Product::create([
            'category_id' => $category->id,
            'name' => 'Voucher Tri 10GB',
            'code' => 'VCR-TRI-10GB',
            'cost_price' => 20000,
            'selling_price' => 25000,
            'stock' => 0,
            'min_stock' => 2,
            'unit' => 'pcs',
            'is_active' => true,
        ]);

        // 1. Proses Inject 10 pcs voucher via Saldo Server (Saldo terpotong 200.000: 500.000 -> 300.000)
        $this->actingAs($this->user)->post(route('stocks.process-inject'), [
            'product_id' => $voucher->id,
            'quantity' => 10,
            'cost_price' => 20000,
        ]);

        $this->assertEquals(10, $voucher->fresh()->stock);
        $this->assertEquals(300000, (float) ServerDeposit::getPrimary()->balance);

        // 2. Beberapa hari kemudian, pembeli datang beli 1 pcs Voucher Tri seharga 25.000 tunai di kasir
        $response = $this->actingAs($this->user)->post(route('incomes.store'), [
            'product_id' => $voucher->id,
            'product_name' => $voucher->name,
            'category_id' => $category->id,
            'payment_method' => 'cash',
            'quantity' => 1,
            'cost_price' => 20000,
            'amount' => 25000,
            'transaction_date' => now()->format('Y-m-d\TH:i'),
        ]);

        $response->assertRedirect(route('incomes.index'));
        $response->assertSessionHas('success');

        // 1. Stok fisik voucher harus berkurang dari 10 menjadi 9
        $this->assertEquals(9, $voucher->fresh()->stock);

        // 2. Saldo server HARUS TETAP 300.000 (TIDAK BOLEH DOUBLE CUT!)
        $this->assertEquals(300000, (float) ServerDeposit::getPrimary()->balance);

        // 3. Tidak ada mutasi pengurangan saldo server untuk transaksi kasir ini
        $tx = Transaction::where('type', 'pemasukan')->latest('id')->first();
        $this->assertNotNull($tx);
        $this->assertDatabaseMissing('server_deposit_mutations', [
            'reference_type' => Transaction::class,
            'reference_id' => $tx->id,
        ]);
    }

    public function test_selling_digital_product_still_deducts_server_deposit(): void
    {
        ServerDeposit::setBalance(300000);

        $pulsaCategory = Category::create([
            'name' => 'Pulsa Tri',
            'slug' => 'pulsa-tri',
            'type' => 'product',
        ]);

        $digitalPulsa = Product::create([
            'category_id' => $pulsaCategory->id,
            'name' => 'Pulsa Tri 10.000',
            'code' => 'PULSA-TRI-10K',
            'cost_price' => 10100,
            'selling_price' => 12000,
            'stock' => 0,
            'unit' => 'transaksi',
            'is_active' => true,
        ]);

        // Penjualan pulsa reguler digital (unit 'transaksi')
        $response = $this->actingAs($this->user)->post(route('incomes.store'), [
            'product_id' => $digitalPulsa->id,
            'product_name' => $digitalPulsa->name,
            'category_id' => $pulsaCategory->id,
            'payment_method' => 'cash',
            'quantity' => 1,
            'cost_price' => 10100,
            'amount' => 12000,
            'customer_target' => '089876543210',
            'transaction_date' => now()->format('Y-m-d\TH:i'),
        ]);

        $response->assertRedirect(route('incomes.index'));

        // Saldo server HARUS terpotong sebesar modal (10.100): 300.000 - 10.100 = 289.900
        $this->assertEquals(289900, (float) ServerDeposit::getPrimary()->balance);
    }

    public function test_report_saldo_server_separates_kasir_vs_inject(): void
    {
        ServerDeposit::setBalance(1000000);

        $category = Category::create([
            'name' => 'Voucher Tri',
            'slug' => 'voucher-tri',
            'type' => 'product',
        ]);

        $voucher = Product::create([
            'category_id' => $category->id,
            'name' => 'Voucher Tri 5GB',
            'code' => 'VCR-TRI-5GB',
            'cost_price' => 15000,
            'selling_price' => 18000,
            'stock' => 0,
            'unit' => 'pcs',
            'is_active' => true,
        ]);

        // Inject 10 pcs voucher (Modal: 150.000)
        $this->actingAs($this->user)->post(route('stocks.process-inject'), [
            'product_id' => $voucher->id,
            'quantity' => 10,
            'cost_price' => 15000,
        ]);

        // Buka laporan saldo server
        $response = $this->actingAs($this->user)->get(route('reports.index', ['tab' => 'saldo_server']));
        $response->assertOk();

        $serverSummary = $response->viewData('serverSummary');
        $this->assertNotNull($serverSummary);
        $this->assertEquals(150000, (float) $serverSummary['total_inject']);
        $this->assertEquals(150000, (float) $serverSummary['total_out']);
        $this->assertEquals(0, (float) $serverSummary['total_kasir']);
    }

    public function test_can_register_and_inject_new_voucher_directly_in_one_step(): void
    {
        ServerDeposit::setBalance(500000);

        $category = Category::create([
            'name' => 'Voucher Telkomsel',
            'slug' => 'voucher-telkomsel',
            'type' => 'product',
        ]);

        // Daftarkan voucher baru langsung di menu inject tanpa lewat menu stok/produk dulu
        $response = $this->actingAs($this->user)->post(route('stocks.process-inject'), [
            'is_new' => '1',
            'new_product_name' => 'Voucher Telkomsel 3GB 7 Hari',
            'category_id' => $category->id,
            'cost_price' => 16500,
            'selling_price' => 20000,
            'quantity' => 10,
            'notes' => 'Inject perdana tanpa buka menu produk',
        ]);

        $response->assertRedirect(route('stocks.inject'));
        $response->assertSessionHas('success');

        // 1. Produk otomatis terbuat di database
        $product = Product::where('name', 'Voucher Telkomsel 3GB 7 Hari')->first();
        $this->assertNotNull($product);
        $this->assertEquals(16500, (float) $product->cost_price);
        $this->assertEquals(20000, (float) $product->selling_price);
        $this->assertEquals(10, $product->stock); // Stok langsung terisi 10 pcs
        $this->assertEquals('pcs', $product->unit);

        // 2. Saldo server langsung terpotong (10 x 16.500 = 165.000): 500.000 - 165.000 = 335.000
        $this->assertEquals(335000, (float) ServerDeposit::getPrimary()->balance);

        // 3. Mutasi stok dan mutasi server deposit tercatat saling terhubung
        $this->assertDatabaseHas('stock_mutations', [
            'product_id' => $product->id,
            'type' => 'in',
            'quantity' => 10,
        ]);
        $this->assertDatabaseHas('server_deposit_mutations', [
            'type' => 'out',
            'amount' => 165000,
        ]);

        // 4. Langsung bisa dijual di kasir dan saldo server tidak terpotong dobel
        $cashierResponse = $this->actingAs($this->user)->post(route('incomes.store'), [
            'product_id' => $product->id,
            'product_name' => $product->name,
            'category_id' => $category->id,
            'payment_method' => 'cash',
            'quantity' => 1,
            'cost_price' => 16500,
            'amount' => 20000,
            'transaction_date' => now()->format('Y-m-d\TH:i'),
        ]);

        $cashierResponse->assertRedirect(route('incomes.index'));
        $this->assertEquals(9, $product->fresh()->stock); // Stok berkurang jadi 9
        $this->assertEquals(335000, (float) ServerDeposit::getPrimary()->balance); // Saldo server tidak terpotong dobel
    }

    public function test_voucher_separation_between_products_stocks_and_inject(): void
    {
        $vcrCat = Category::create([
            'name' => 'Voucher Axis',
            'slug' => 'voucher-axis',
            'type' => 'product',
        ]);

        $pulsaCat = Category::create([
            'name' => 'Pulsa Axis',
            'slug' => 'pulsa-axis',
            'type' => 'product',
        ]);

        $vcrProduct = Product::create([
            'category_id' => $vcrCat->id,
            'name' => 'Voucher Axis AIGO 2GB',
            'code' => 'VCR-AXIS-2GB',
            'cost_price' => 8000,
            'selling_price' => 10000,
            'stock' => 5,
            'min_stock' => 2,
            'unit' => 'pcs',
            'is_active' => true,
        ]);

        $pulsaProduct = Product::create([
            'category_id' => $pulsaCat->id,
            'name' => 'Pulsa Axis 10.000',
            'code' => 'PULSA-AXIS-10K',
            'cost_price' => 10100,
            'selling_price' => 12000,
            'stock' => 0,
            'min_stock' => 0,
            'unit' => 'transaksi',
            'is_active' => true,
        ]);

        // 1. /products/create tidak boleh menampilkan kategori voucher fisik
        $productCreateResponse = $this->actingAs($this->user)->get(route('products.create'));
        $productCreateResponse->assertOk();
        $catsInProductCreate = $productCreateResponse->viewData('categories');
        $this->assertFalse($catsInProductCreate->contains('id', $vcrCat->id));
        $this->assertTrue($catsInProductCreate->contains('id', $pulsaCat->id));

        // 2. /stocks/create (penyesuaian stok umum) tidak menampilkan voucher fisik
        $stockCreateResponse = $this->actingAs($this->user)->get(route('stocks.create'));
        $stockCreateResponse->assertOk();
        $productsInStockCreate = $stockCreateResponse->viewData('products');
        $this->assertFalse($productsInStockCreate->contains('id', $vcrProduct->id));
        $this->assertTrue($productsInStockCreate->contains('id', $pulsaProduct->id));

        // 3. /stocks/inject hanya menampilkan produk dan kategori voucher fisik
        $injectResponse = $this->actingAs($this->user)->get(route('stocks.inject'));
        $injectResponse->assertOk();
        $productsInInject = $injectResponse->viewData('products');
        $catsInInject = $injectResponse->viewData('categories');
        $this->assertTrue($productsInInject->contains('id', $vcrProduct->id));
        $this->assertFalse($productsInInject->contains('id', $pulsaProduct->id));
        $this->assertTrue($catsInInject->contains('id', $vcrCat->id));
        $this->assertFalse($catsInInject->contains('id', $pulsaCat->id));
    }
}
