<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\ServerDeposit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServerDepositTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_displays_single_server_deposit_widget(): void
    {
        $user = User::factory()->create();

        ServerDeposit::getPrimary();
        ServerDeposit::setBalance(1500000);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Saldo Aplikasi Server All Provider');
        $response->assertSee('Sisa Saldo Deposit Saat Ini');
        $response->assertSee('1.500.000');
    }

    public function test_user_can_topup_server_deposit(): void
    {
        $user = User::factory()->create();

        ServerDeposit::setBalance(1000000);

        $response = $this->actingAs($user)->post('/server-deposit/topup', [
            'amount' => 500000,
        ]);

        $response->assertRedirect('/dashboard');
        $response->assertSessionHas('success');

        $this->assertEquals(1500000, (float) ServerDeposit::getPrimary()->balance);
    }

    public function test_user_can_adjust_server_deposit_balance(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/server-deposit/adjust', [
            'new_balance' => 875000,
        ]);

        $response->assertRedirect('/dashboard');
        $response->assertSessionHas('success');

        $this->assertEquals(875000, (float) ServerDeposit::getPrimary()->balance);
    }

    public function test_user_can_update_server_deposit_settings(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->put('/server-deposit/settings', [
            'name' => 'Fastpay All-In-One Konter',
            'min_alert_balance' => 150000,
            'account_id' => 'AGEN-9921',
            'notes' => 'Akun server utama konter',
        ]);

        $response->assertRedirect('/dashboard');
        $response->assertSessionHas('success');

        $deposit = ServerDeposit::getPrimary();
        $this->assertEquals('Fastpay All-In-One Konter', $deposit->name);
        $this->assertEquals(150000, (float) $deposit->min_alert_balance);
        $this->assertEquals('AGEN-9921', $deposit->account_id);
    }

    public function test_income_automatically_deducts_server_deposit_balance(): void
    {
        $user = User::factory()->create();
        ServerDeposit::setBalance(1000000);

        $category = Category::factory()->create(['name' => 'Pulsa All Operator', 'type' => 'transaction']);

        // Catat penjualan pulsa: harga jual 12.000, modal 10.200
        $response = $this->actingAs($user)->post('/incomes', [
            'product_name' => 'Pulsa Telkomsel 10k',
            'category_id' => $category->id,
            'payment_method' => 'cash',
            'amount' => 12000,
            'quantity' => 1,
            'cost_price' => 10200,
            'customer_target' => '081234567890',
        ]);

        $response->assertRedirect('/incomes');
        $response->assertSessionHas('success');

        // Saldo server harus otomatis terpotong sebesar modal (10.200): 1.000.000 - 10.200 = 989.800
        $this->assertEquals(989800, (float) ServerDeposit::getPrimary()->balance);
    }

    public function test_income_cancellation_automatically_refunds_server_deposit_balance(): void
    {
        $user = User::factory()->create();
        ServerDeposit::setBalance(1000000);

        $category = Category::factory()->create(['name' => 'Pulsa', 'type' => 'transaction']);

        $this->actingAs($user)->post('/incomes', [
            'product_name' => 'Pulsa Indosat 25k',
            'category_id' => $category->id,
            'payment_method' => 'cash',
            'amount' => 27000,
            'quantity' => 1,
            'cost_price' => 25200,
            'customer_target' => '085712345678',
        ]);

        $this->assertEquals(974800, (float) ServerDeposit::getPrimary()->balance);

        $transaction = \App\Models\Transaction::where('type', 'pemasukan')->latest()->first();

        // Batalkan / hapus transaksi
        $response = $this->actingAs($user)->delete('/incomes/' . $transaction->id);

        $response->assertRedirect('/incomes');
        $response->assertSessionHas('success');

        // Saldo server harus kembali utuh ke 1.000.000
        $this->assertEquals(1000000, (float) ServerDeposit::getPrimary()->balance);

        // Mutasi saldo deposit harus tercatat
        $latestMutation = \App\Models\ServerDepositMutation::latest('id')->first();
        $this->assertNotNull($latestMutation);
        $this->assertEquals('in', $latestMutation->type);
        $this->assertEquals(25200, (float) $latestMutation->amount);
    }

    public function test_server_deposit_mutations_are_recorded_properly(): void
    {
        $deposit = ServerDeposit::setBalance(500000);
        $this->assertEquals(500000, (float) $deposit->balance);

        // Deduct
        ServerDeposit::deductBalance(50000, 'Penjualan pulsa');
        $this->assertEquals(450000, (float) ServerDeposit::getPrimary()->balance);

        // Add
        ServerDeposit::addBalance(100000, 'Top-up saldo');
        $this->assertEquals(550000, (float) ServerDeposit::getPrimary()->balance);

        $mutations = \App\Models\ServerDepositMutation::where('server_deposit_id', $deposit->id)->get();
        $this->assertGreaterThanOrEqual(3, $mutations->count());
    }

    public function test_income_deletion_decreases_stock_out_mutation_and_restores_physical_stock(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['name' => 'Aksesoris', 'type' => 'all']);
        $product = \App\Models\Product::create([
            'category_id' => $category->id,
            'name' => 'Kabel Data Type-C',
            'code' => 'KBL-TC',
            'cost_price' => 15000,
            'selling_price' => 25000,
            'stock' => 20,
            'unit' => 'pcs',
            'min_stock' => 2,
            'is_active' => true,
        ]);

        // Catat penjualan produk fisik 2 unit
        $this->actingAs($user)->post('/incomes', [
            'product_id' => $product->id,
            'product_name' => $product->name,
            'category_id' => $category->id,
            'payment_method' => 'cash',
            'quantity' => 2,
            'cost_price' => 15000,
            'amount' => 50000,
            'customer_target' => 'Pembeli Toko',
        ]);

        $product->refresh();
        $this->assertEquals(18, $product->stock); // Berkurang 2
        $this->assertEquals(2, (int) \App\Models\StockMutation::where('type', 'out')->sum('quantity')); // Out = 2

        $transaction = \App\Models\Transaction::where('type', 'pemasukan')->latest()->first();

        // Batalkan / hapus transaksi karena kasir salah pilih
        $this->actingAs($user)->delete('/incomes/' . $transaction->id);

        $product->refresh();
        $this->assertEquals(20, $product->stock); // Stok fisik kembali utuh ke 20
        // Stok out mutasi otomatis berkurang kembali menjadi 0 (tidak tersisa sebagai out)
        $this->assertEquals(0, (int) \App\Models\StockMutation::where('type', 'out')->sum('quantity'));
        // Tidak ada catatan 'in' palsu yang tersisa
        $this->assertEquals(0, (int) \App\Models\StockMutation::where('type', 'in')->sum('quantity'));
    }

    public function test_server_deposit_mutations_page_renders_successfully_with_data_and_filters(): void
    {
        $user = User::factory()->create();
        $deposit = ServerDeposit::setBalance(750000);
        ServerDeposit::addBalance(200000, 'Top-up bank BCA');
        ServerDeposit::deductBalance(50000, 'Penjualan pulsa 50k');

        $response = $this->actingAs($user)->get(route('server-deposit.mutations'));
        $response->assertOk();
        $response->assertSee('Riwayat Mutasi Saldo Server');
        $response->assertSee('Top-up bank BCA');
        $response->assertSee('Penjualan pulsa 50k');

        // Test filter by type = in
        $responseIn = $this->actingAs($user)->get(route('server-deposit.mutations', ['type' => 'in']));
        $responseIn->assertOk();
        $responseIn->assertSee('Top-up bank BCA');

        // Test search filter
        $responseSearch = $this->actingAs($user)->get(route('server-deposit.mutations', ['search' => 'pulsa 50k']));
        $responseSearch->assertOk();
        $responseSearch->assertSee('Penjualan pulsa 50k');
    }
}
