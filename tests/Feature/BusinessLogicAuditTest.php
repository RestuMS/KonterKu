<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Kasbon;
use App\Models\Product;
use App\Models\ServerDeposit;
use App\Models\ServerDepositMutation;
use App\Models\StockMutation;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BusinessLogicAuditTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_selling_accessories_does_not_deduct_server_deposit(): void
    {
        $deposit = ServerDeposit::setBalance(500000);

        $accCategory = Category::create([
            'name' => 'Aksesoris HP',
            'slug' => 'aksesoris-hp',
            'type' => 'product',
        ]);

        $casing = Product::create([
            'category_id' => $accCategory->id,
            'code' => 'CASE-IP13',
            'name' => 'Casing iPhone 13 Clear',
            'cost_price' => 15000,
            'selling_price' => 35000,
            'stock' => 10,
            'min_stock' => 2,
            'unit' => 'pcs',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->user)->post(route('incomes.store'), [
            'product_id' => $casing->id,
            'product_name' => $casing->name,
            'category_id' => $accCategory->id,
            'payment_method' => 'cash',
            'quantity' => 1,
            'cost_price' => 15000,
            'amount' => 35000,
            'transaction_date' => now()->format('Y-m-d\TH:i'),
        ]);

        $response->assertRedirect(route('incomes.index'));

        // Saldo server harus TETAP 500.000 karena casing adalah barang fisik aksesoris
        $this->assertEquals(500000, (float) ServerDeposit::getPrimary()->balance);

        // Tidak boleh ada mutasi out pada saldo server untuk transaksi ini
        $tx = Transaction::where('type', 'pemasukan')->latest('id')->first();
        $this->assertNotNull($tx);
        $this->assertDatabaseMissing('server_deposit_mutations', [
            'reference_type' => Transaction::class,
            'reference_id' => $tx->id,
        ]);

        // Namun stok fisik casing harus berkurang menjadi 9
        $this->assertEquals(9, $casing->fresh()->stock);
    }

    public function test_selling_pulsa_deducts_server_deposit(): void
    {
        ServerDeposit::setBalance(500000);

        $pulsaCategory = Category::create([
            'name' => 'Pulsa Telkomsel',
            'slug' => 'pulsa-telkomsel',
            'type' => 'product',
        ]);

        $response = $this->actingAs($this->user)->post(route('incomes.store'), [
            'product_name' => 'Pulsa Telkomsel 25.000',
            'category_id' => $pulsaCategory->id,
            'payment_method' => 'cash',
            'quantity' => 1,
            'cost_price' => 25100,
            'amount' => 27000,
            'customer_target' => '081234567890',
            'transaction_date' => now()->format('Y-m-d\TH:i'),
        ]);

        $response->assertRedirect(route('incomes.index'));

        // Saldo server harus berkurang sebesar 25.100 (500.000 - 25.100 = 474.900)
        $this->assertEquals(474900, (float) ServerDeposit::getPrimary()->balance);

        // Mutasi saldo server harus tercatat
        $tx = Transaction::where('type', 'pemasukan')->latest('id')->first();
        $this->assertDatabaseHas('server_deposit_mutations', [
            'reference_type' => Transaction::class,
            'reference_id' => $tx->id,
            'type' => 'out',
            'amount' => 25100,
        ]);
    }

    public function test_creating_product_with_initial_stock_records_in_stock_mutation(): void
    {
        $cat = Category::create([
            'name' => 'Voucher Tri',
            'slug' => 'voucher-tri',
            'type' => 'product',
        ]);

        $response = $this->actingAs($this->user)->post(route('products.store'), [
            'category_id' => $cat->id,
            'code' => 'V-TRI-10GB',
            'name' => 'Voucher Tri Happy 10GB',
            'cost_price' => 25000,
            'selling_price' => 30000,
            'stock' => 15,
            'min_stock' => 3,
            'unit' => 'pcs',
        ]);

        $response->assertRedirect(route('products.index'));

        $product = Product::where('code', 'V-TRI-10GB')->first();
        $this->assertNotNull($product);
        $this->assertEquals(15, $product->stock);

        // Mutasi stok awal harus tercatat di stock_mutations
        $this->assertDatabaseHas('stock_mutations', [
            'product_id' => $product->id,
            'type' => 'in',
            'quantity' => 15,
            'stock_before' => 0,
            'stock_after' => 15,
            'notes' => 'Stok awal produk baru',
        ]);
    }

    public function test_expense_edit_includes_expense_type_categories(): void
    {
        $expenseCat = Category::create([
            'name' => 'Beli Jajan',
            'slug' => 'jajan',
            'type' => 'expense',
        ]);

        $expense = Transaction::create([
            'invoice_number' => 'EXP-20261102-00001',
            'type' => 'pengeluaran',
            'category_id' => $expenseCat->id,
            'user_id' => $this->user->id,
            'transaction_date' => now(),
            'total_amount' => 25000,
            'payment_method' => 'cash',
            'notes' => 'Beli gorengan kasir',
        ]);

        $response = $this->actingAs($this->user)->get(route('expenses.edit', $expense));
        $response->assertOk();
        $response->assertSee('Beli Jajan');
    }

    public function test_date_filter_swaps_gracefully_when_start_date_exceeds_end_date(): void
    {
        $cat = Category::create([
            'name' => 'Operasional Toko',
            'slug' => 'operasional-toko',
            'type' => 'expense',
        ]);

        Transaction::create([
            'invoice_number' => 'EXP-20261102-TEST1',
            'type' => 'pengeluaran',
            'category_id' => $cat->id,
            'user_id' => $this->user->id,
            'transaction_date' => Carbon::parse('2026-10-15 10:00:00'),
            'total_amount' => 50000,
            'payment_method' => 'cash',
            'notes' => 'Listrik konter',
        ]);

        // User memasukkan start_date yang terbalik (start: 20 Okt, end: 10 Okt)
        $response = $this->actingAs($this->user)->get(route('expenses.index', [
            'start_date' => '2026-10-20',
            'end_date' => '2026-10-10',
        ]));

        $response->assertOk();
        // Transaksi tanggal 15 Okt harus tetap tampil karena controller menukar rentang tanggal secara otomatis
        $response->assertSee('EXP-20261102-TEST1');
    }

    public function test_updating_kasbon_quantity_adjusts_product_stock_and_server_deposit(): void
    {
        ServerDeposit::setBalance(500000);

        $cat = Category::create([
            'name' => 'Voucher Indosat',
            'slug' => 'voucher-indosat',
            'type' => 'product',
        ]);

        $voucher = Product::create([
            'category_id' => $cat->id,
            'code' => 'V-ISAT-5GB',
            'name' => 'Voucher Indosat 5GB',
            'cost_price' => 20000,
            'selling_price' => 25000,
            'stock' => 10,
            'min_stock' => 2,
            'unit' => 'pcs',
            'is_active' => true,
        ]);

        // Catat kasbon awal (1 pcs)
        $response = $this->actingAs($this->user)->post(route('kasbons.store'), [
            'customer_name' => 'Budi Santoso',
            'product_id' => $voucher->id,
            'product_name' => $voucher->name,
            'quantity' => 1,
            'cost_price' => 20000,
            'amount' => 25000,
            'debt_date' => now()->toDateString(),
        ]);

        $kasbon = Kasbon::where('customer_name', 'Budi Santoso')->first();
        $this->assertNotNull($kasbon);
        $this->assertEquals(9, $voucher->fresh()->stock);
        $this->assertEquals(480000, (float) ServerDeposit::getPrimary()->balance);

        // Update kasbon: ubah kuantitas dari 1 pcs menjadi 2 pcs (amount jadi 50.000)
        $updateResponse = $this->actingAs($this->user)->put(route('kasbons.update', $kasbon), [
            'customer_name' => 'Budi Santoso',
            'product_id' => $voucher->id,
            'product_name' => $voucher->name,
            'quantity' => 2,
            'cost_price' => 20000,
            'amount' => 50000,
            'debt_date' => now()->toDateString(),
        ]);

        $updateResponse->assertRedirect(route('kasbons.index'));

        // Stok voucher harus berkurang 1 unit lagi (dari 9 menjadi 8)
        $this->assertEquals(8, $voucher->fresh()->stock);

        // Saldo server harus terpotong 20.000 lagi (dari 480.000 menjadi 460.000)
        $this->assertEquals(460000, (float) ServerDeposit::getPrimary()->balance);
    }

    public function test_user_can_download_database_backup_sql(): void
    {
        $response = $this->actingAs($this->user)->get(route('settings.backup'));

        $response->assertOk();
        $this->assertEquals('application/sql', $response->headers->get('Content-Type'));
        $this->assertStringContainsString('Backup-', (string) $response->headers->get('Content-Disposition'));

        // Ambil isi streaming SQL
        ob_start();
        $response->sendContent();
        $sqlContent = ob_get_clean();

        $this->assertStringContainsString('CADANGAN DATABASE SISTEM KONTERKU', $sqlContent);
        $this->assertStringContainsString('users', $sqlContent);
        $this->assertStringContainsString('categories', $sqlContent);
        $this->assertStringContainsString('products', $sqlContent);
        $this->assertStringContainsString('AKHIR CADANGAN', $sqlContent);
    }

    public function test_dashboard_displays_urgent_kasbon_reminder(): void
    {
        // Buat kasbon yang sudah lewat jatuh tempo
        Kasbon::create([
            'user_id' => $this->user->id,
            'customer_name' => 'Pak Joko Overdue',
            'customer_phone' => '081234567899',
            'amount' => 75000,
            'profit' => 15000,
            'debt_date' => Carbon::now()->subDays(5)->toDateString(),
            'due_date' => Carbon::now()->subDay()->toDateString(),
            'status' => 'unpaid',
        ]);

        $response = $this->actingAs($this->user)->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee('Pak Joko Overdue');
        $response->assertSee('Lewat Jatuh Tempo');
        $response->assertSee('WA Tagih');
    }

    public function test_report_saldo_server_tab_and_csv_export(): void
    {
        ServerDeposit::setBalance(500000);
        ServerDeposit::addBalance(150000, 'Top-Up Saldo Uji Coba');

        // 1. Uji tampilan tab saldo_server di laporan
        $response = $this->actingAs($this->user)->get(route('reports.index', [
            'tab' => 'saldo_server',
        ]));

        $response->assertOk();
        $response->assertSee('Sisa Saldo Server');
        $response->assertSee('Top-Up Saldo Uji Coba');

        // 2. Uji ekspor CSV tab saldo_server
        $exportResponse = $this->actingAs($this->user)->get(route('reports.export', [
            'tab' => 'saldo_server',
        ]));

        $exportResponse->assertOk();
        $this->assertStringContainsString('text/csv', (string) $exportResponse->headers->get('Content-Type'));

        ob_start();
        $exportResponse->sendContent();
        $csvContent = ob_get_clean();

        $this->assertStringContainsString('Waktu Mutasi', $csvContent);
        $this->assertStringContainsString('Tipe Mutasi', $csvContent);
        $this->assertStringContainsString('Top-Up Saldo Uji Coba', $csvContent);
    }

    public function test_expense_can_be_created_and_updated_with_exact_ten_thousand(): void
    {
        $cat = Category::create([
            'name' => 'Beli Jajan Kasir',
            'slug' => 'beli-jajan-kasir',
            'type' => 'expense',
        ]);

        // Simpan pengeluaran tepat Rp 10.000 (tidak boleh terblokir oleh step HTML5)
        $response = $this->actingAs($this->user)->post(route('expenses.store'), [
            'category_id' => $cat->id,
            'amount' => 10000,
            'payment_method' => 'cash',
            'notes' => 'Beli nasi bungkus siang',
            'transaction_date' => now()->format('Y-m-d\TH:i'),
        ]);

        $response->assertRedirect(route('expenses.index'));

        $expense = Transaction::where('type', 'pengeluaran')->latest('id')->first();
        $this->assertNotNull($expense);
        $this->assertEquals(10000, (float) $expense->total_amount);

        // Edit dan simpan kembali tetap Rp 10.000 (tidak boleh harus 10.001)
        $updateResponse = $this->actingAs($this->user)->put(route('expenses.update', $expense), [
            'category_id' => $cat->id,
            'amount' => 10000,
            'payment_method' => 'cash',
            'notes' => 'Beli nasi bungkus siang komplit',
            'transaction_date' => now()->format('Y-m-d\TH:i'),
        ]);

        $updateResponse->assertRedirect(route('expenses.index'));
        $this->assertEquals(10000, (float) $expense->fresh()->total_amount);

        // Verifikasi view create & edit memiliki step="any"
        $createView = $this->actingAs($this->user)->get(route('expenses.create'));
        $createView->assertOk();
        $createView->assertSee('step="any"', false);

        $editView = $this->actingAs($this->user)->get(route('expenses.edit', $expense));
        $editView->assertOk();
        $editView->assertSee('step="any"', false);
    }
}

