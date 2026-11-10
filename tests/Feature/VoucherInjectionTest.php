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

    public function test_voucher_accessible_in_products_and_stocks_while_inject_focuses_on_vouchers(): void
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
            'unit' => 'voucher',
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

        // 1. /products/create harus menampilkan kategori voucher fisik untuk order dari sales
        $productCreateResponse = $this->actingAs($this->user)->get(route('products.create'));
        $productCreateResponse->assertOk();
        $catsInProductCreate = $productCreateResponse->viewData('categories');
        $this->assertTrue($catsInProductCreate->contains('id', $vcrCat->id));
        $this->assertTrue($catsInProductCreate->contains('id', $pulsaCat->id));

        // 2. /stocks/create (penyesuaian stok) harus menampilkan voucher fisik untuk restock dari sales
        $stockCreateResponse = $this->actingAs($this->user)->get(route('stocks.create'));
        $stockCreateResponse->assertOk();
        $productsInStockCreate = $stockCreateResponse->viewData('products');
        $this->assertTrue($productsInStockCreate->contains('id', $vcrProduct->id));
        $this->assertTrue($productsInStockCreate->contains('id', $pulsaProduct->id));

        // 3. /stocks/inject tetap fokus hanya menampilkan produk dan kategori voucher fisik
        $injectResponse = $this->actingAs($this->user)->get(route('stocks.inject'));
        $injectResponse->assertOk();
        $productsInInject = $injectResponse->viewData('products');
        $catsInInject = $injectResponse->viewData('categories');
        $this->assertTrue($productsInInject->contains('id', $vcrProduct->id));
        $this->assertFalse($productsInInject->contains('id', $pulsaProduct->id));
        $this->assertTrue($catsInInject->contains('id', $vcrCat->id));
        $this->assertFalse($catsInInject->contains('id', $pulsaCat->id));
    }

    public function test_sales_voucher_created_in_products_does_not_deduct_server_on_sale_and_supports_optional_expense(): void
    {
        ServerDeposit::setBalance(500000);

        $vcrCat = Category::create([
            'name' => 'Voucher Tri',
            'slug' => 'voucher-tri',
            'type' => 'product',
        ]);

        // Input produk baru voucher fisik dari Sales tanpa mencentang pengeluaran
        $resNoExp = $this->actingAs($this->user)->post(route('products.store'), [
            'items' => [
                [
                    'category_id' => $vcrCat->id,
                    'code' => 'VCR-TRI-SL1',
                    'name' => 'Voucher Tri 6GB Sales',
                    'cost_price' => 15000,
                    'selling_price' => 20000,
                    'stock' => 10,
                    'min_stock' => 2,
                    'unit' => 'voucher',
                    'is_active' => 1,
                ],
            ],
        ]);
        $resNoExp->assertRedirect(route('products.index'));

        $vcrNoExp = Product::where('code', 'VCR-TRI-SL1')->first();
        $this->assertNotNull($vcrNoExp);
        $this->assertEquals(10, $vcrNoExp->stock);
        $this->assertEquals(0, Transaction::where('type', 'pengeluaran')->count());
        $this->assertEquals(500000, (float) ServerDeposit::getPrimary()->balance);

        // Input produk baru voucher fisik dari Sales DENGAN centang catat pengeluaran (Transfer)
        $resWithExp = $this->actingAs($this->user)->post(route('products.store'), [
            'items' => [
                [
                    'category_id' => $vcrCat->id,
                    'code' => 'VCR-TRI-SL2',
                    'name' => 'Voucher Tri 12GB Sales',
                    'cost_price' => 25000,
                    'selling_price' => 30000,
                    'stock' => 4,
                    'min_stock' => 2,
                    'unit' => 'voucher',
                    'is_active' => 1,
                ],
            ],
            'record_expense' => '1',
            'payment_method' => 'transfer',
            'expense_notes' => 'Sales Tri Mas Adi',
        ]);
        $resWithExp->assertRedirect(route('products.index'));

        $vcrWithExp = Product::where('code', 'VCR-TRI-SL2')->first();
        $this->assertNotNull($vcrWithExp);
        $this->assertEquals(4, $vcrWithExp->stock);

        // Pengeluaran kulakan harus otomatis tercatat sebesar 4 x 25.000 = 100.000 via Transfer
        $expenseTx = Transaction::where('type', 'pengeluaran')->latest('id')->first();
        $this->assertNotNull($expenseTx);
        $this->assertEquals(100000, (float) $expenseTx->total_amount);
        $this->assertEquals('transfer', $expenseTx->payment_method);
        $this->assertStringContainsString('[ORDER SALES]', $expenseTx->notes);
        $this->assertStringContainsString('Sales Tri Mas Adi', $expenseTx->notes);

        // Saldo server tidak boleh ikut terpotong saat input kulakan Sales
        $this->assertEquals(500000, (float) ServerDeposit::getPrimary()->balance);

        // Jual kedua jenis voucher fisik hasil order Sales di kasir -> Saldo Server tetap 500.000!
        $this->actingAs($this->user)->post(route('incomes.store'), [
            'product_id' => $vcrNoExp->id,
            'product_name' => $vcrNoExp->name,
            'category_id' => $vcrCat->id,
            'payment_method' => 'cash',
            'quantity' => 2,
            'cost_price' => 15000,
            'amount' => 40000,
            'transaction_date' => now()->format('Y-m-d\TH:i'),
        ])->assertRedirect(route('incomes.index'));

        $this->actingAs($this->user)->post(route('incomes.store'), [
            'product_id' => $vcrWithExp->id,
            'product_name' => $vcrWithExp->name,
            'category_id' => $vcrCat->id,
            'payment_method' => 'cash',
            'quantity' => 1,
            'cost_price' => 25000,
            'amount' => 30000,
            'transaction_date' => now()->format('Y-m-d\TH:i'),
        ])->assertRedirect(route('incomes.index'));

        $this->assertEquals(8, $vcrNoExp->fresh()->stock);
        $this->assertEquals(3, $vcrWithExp->fresh()->stock);
        $this->assertEquals(500000, (float) ServerDeposit::getPrimary()->balance);
    }

    public function test_restock_from_sales_in_stocks_supports_optional_expense_and_prevents_server_deduction(): void
    {
        ServerDeposit::setBalance(500000);

        $vcrCat = Category::create([
            'name' => 'Voucher XL',
            'slug' => 'voucher-xl',
            'type' => 'product',
        ]);

        $voucher = Product::create([
            'category_id' => $vcrCat->id,
            'code' => 'VCR-XL-5GB',
            'name' => 'Voucher XL 5GB Sales',
            'cost_price' => 18000,
            'selling_price' => 23000,
            'stock' => 2,
            'min_stock' => 2,
            'unit' => 'voucher',
            'is_active' => true,
        ]);

        // 1. Tambah stok dari Sales tanpa mencentang pengeluaran
        $this->actingAs($this->user)->post(route('stocks.store'), [
            'product_id' => $voucher->id,
            'adjustment_type' => 'add',
            'stock_value' => 5,
            'notes' => 'Titipan Sales XL tanpa nota kas',
        ])->assertRedirect(route('stocks.index'));

        $this->assertEquals(7, $voucher->fresh()->stock);
        $this->assertEquals(0, Transaction::where('type', 'pengeluaran')->count());

        // 2. Tambah stok dari Sales DENGAN centang catat pengeluaran Cash & nominal custom diskon nota (10 pcs x 18rb = 180rb, bayar 175rb)
        $this->actingAs($this->user)->post(route('stocks.store'), [
            'product_id' => $voucher->id,
            'adjustment_type' => 'add',
            'stock_value' => 10,
            'notes' => 'Order Sales XL Pak Budi',
            'record_expense' => '1',
            'payment_method' => 'cash',
            'expense_amount' => 175000,
        ])->assertRedirect(route('stocks.index'));

        $this->assertEquals(17, $voucher->fresh()->stock);

        $expenseTx = Transaction::where('type', 'pengeluaran')->latest('id')->first();
        $this->assertNotNull($expenseTx);
        $this->assertEquals(175000, (float) $expenseTx->total_amount);
        $this->assertEquals('cash', $expenseTx->payment_method);
        $this->assertStringContainsString('[ORDER SALES]', $expenseTx->notes);

        $latestStockMut = StockMutation::where('product_id', $voucher->id)->latest('id')->first();
        $this->assertNotNull($latestStockMut);
        $this->assertEquals(Transaction::class, $latestStockMut->reference_type);
        $this->assertEquals($expenseTx->id, $latestStockMut->reference_id);
        $this->assertStringContainsString('[ORDER SALES]', $latestStockMut->notes);

        // 3. Penjualan voucher hasil restock Sales di kasir tidak boleh memotong Saldo Server
        $this->actingAs($this->user)->post(route('incomes.store'), [
            'product_id' => $voucher->id,
            'product_name' => $voucher->name,
            'category_id' => $vcrCat->id,
            'payment_method' => 'cash',
            'quantity' => 3,
            'cost_price' => 18000,
            'amount' => 69000,
            'transaction_date' => now()->format('Y-m-d\TH:i'),
        ])->assertRedirect(route('incomes.index'));

        $this->assertEquals(14, $voucher->fresh()->stock);
        $this->assertEquals(500000, (float) ServerDeposit::getPrimary()->balance);
    }

    public function test_expense_module_includes_tagihan_sales_preset_and_filter(): void
    {
        // Kategori produk/layanan kasir (type = 'all') tidak boleh ikut muncul di Pengeluaran
        $ewalletCat = Category::create([
            'name' => 'E-Wallet DANA',
            'slug' => 'ewallet-dana',
            'type' => 'all',
        ]);
        $wifiCat = Category::create([
            'name' => 'Tagihan WiFi IndiHome',
            'slug' => 'tagihan-indihome',
            'type' => 'all',
        ]);

        $createRes = $this->actingAs($this->user)->get(route('expenses.create'));
        $createRes->assertOk();
        $createRes->assertSee('Tagihan Sales');

        $expenseCats = $createRes->viewData('categories');
        $this->assertFalse($expenseCats->contains('id', $ewalletCat->id));
        $this->assertFalse($expenseCats->contains('id', $wifiCat->id));

        $salesCategory = Category::where('slug', 'kulakan-sales')->first();
        $this->assertNotNull($salesCategory);
        $this->assertEquals('Tagihan Sales', $salesCategory->name);
        $this->assertTrue($expenseCats->contains('id', $salesCategory->id));

        // Simpan tagihan sales langsung dari form pengeluaran manual
        $this->actingAs($this->user)->post(route('expenses.store'), [
            'category_id' => $salesCategory->id,
            'amount' => 350000,
            'payment_method' => 'transfer',
            'notes' => 'Bayar Tagihan Sales Tri',
            'transaction_date' => now()->format('Y-m-d\TH:i'),
        ])->assertRedirect(route('expenses.index'));

        $indexRes = $this->actingAs($this->user)->get(route('expenses.index', ['category_id' => $salesCategory->id]));
        $indexRes->assertOk();
        $indexRes->assertSee('Tagihan Sales');
        $indexRes->assertSee('Bayar Tagihan Sales Tri');

        $indexCats = $indexRes->viewData('categories');
        $this->assertFalse($indexCats->contains('id', $ewalletCat->id));
        $this->assertFalse($indexCats->contains('id', $wifiCat->id));
    }
}
