<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\ServerDeposit;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BillPaymentIncomeTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private ServerDeposit $deposit;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        ServerDeposit::setBalance(1000000);
        $this->deposit = ServerDeposit::getPrimary();
    }

    public function test_can_record_electricity_bill_payment_without_stock_and_deducts_server_deposit(): void
    {
        $category = Category::create([
            'name' => 'Tagihan / Pajak Listrik PLN',
            'slug' => 'tagihan-pajak-listrik-pln',
            'type' => 'all',
        ]);

        $response = $this->actingAs($this->user)->post(route('incomes.store'), [
            'product_id' => null,
            'product_name' => 'Tagihan / Pajak Listrik PLN',
            'category_id' => $category->id,
            'payment_method' => 'cash',
            'quantity' => 1,
            'cost_price' => 245000, // Tagihan Asli (Modal)
            'amount' => 248000,     // Tagihan Jual (Total Bayar)
            'customer_target' => '512345678901',
        ]);

        $response->assertRedirect(route('incomes.index'));

        $transaction = Transaction::where('type', 'pemasukan')->latest('id')->first();
        $this->assertNotNull($transaction);
        $this->assertEquals(248000.00, (float) $transaction->total_amount);
        $this->assertEquals(245000.00, (float) $transaction->cost_price);
        $this->assertEquals(3000.00, (float) $transaction->profit);

        $item = $transaction->items->first();
        $this->assertNotNull($item);
        $this->assertNull($item->product_id); // Bebas stok / tidak ada produk katalog
        $this->assertEquals('Tagihan / Pajak Listrik PLN', $item->product_name);
        $this->assertEquals(245000.00, (float) $item->cost_price);
        $this->assertEquals(248000.00, (float) $item->subtotal);

        // Saldo server harus berkurang sebesar Tagihan Asli (Rp 245.000)
        $this->deposit->refresh();
        $this->assertEquals(755000.00, (float) $this->deposit->balance);
    }

    public function test_can_record_bpjs_bill_payment_without_stock_and_deducts_server_deposit(): void
    {
        $category = Category::create([
            'name' => 'BPJS Kesehatan',
            'slug' => 'bpjs-kesehatan',
            'type' => 'all',
        ]);

        $response = $this->actingAs($this->user)->post(route('incomes.store'), [
            'product_id' => null,
            'product_name' => 'Tagihan BPJS Kesehatan',
            'category_id' => $category->id,
            'payment_method' => 'cash',
            'quantity' => 1,
            'cost_price' => 150000, // Tagihan Asli (Modal)
            'amount' => 153000,     // Tagihan Jual (Total Bayar)
            'customer_target' => '8888801234567890',
        ]);

        $response->assertRedirect(route('incomes.index'));

        $transaction = Transaction::where('type', 'pemasukan')->latest('id')->first();
        $this->assertNotNull($transaction);
        $this->assertEquals(153000.00, (float) $transaction->total_amount);
        $this->assertEquals(150000.00, (float) $transaction->cost_price);
        $this->assertEquals(3000.00, (float) $transaction->profit);

        // Saldo server harus berkurang sebesar Tagihan Asli (Rp 150.000)
        $this->deposit->refresh();
        $this->assertEquals(850000.00, (float) $this->deposit->balance);
    }

    public function test_can_record_wifi_bill_payment_without_stock_and_deducts_server_deposit(): void
    {
        $category = Category::create([
            'name' => 'Tagihan WiFi IndiHome',
            'slug' => 'tagihan-indihome',
            'type' => 'all',
        ]);

        $response = $this->actingAs($this->user)->post(route('incomes.store'), [
            'product_id' => null,
            'product_name' => 'Tagihan WiFi IndiHome',
            'category_id' => $category->id,
            'payment_method' => 'cash',
            'quantity' => 1,
            'cost_price' => 320000, // Tagihan Asli (Modal)
            'amount' => 325000,     // Tagihan Jual (Total Bayar)
            'customer_target' => '122456789012',
        ]);

        $response->assertRedirect(route('incomes.index'));

        $transaction = Transaction::where('type', 'pemasukan')->latest('id')->first();
        $this->assertNotNull($transaction);
        $this->assertEquals(325000.00, (float) $transaction->total_amount);
        $this->assertEquals(320000.00, (float) $transaction->cost_price);
        $this->assertEquals(5000.00, (float) $transaction->profit);

        // Saldo server harus berkurang sebesar Tagihan Asli (Rp 320.000)
        $this->deposit->refresh();
        $this->assertEquals(680000.00, (float) $this->deposit->balance);
    }

    public function test_bill_payment_requires_cost_price_greater_than_zero(): void
    {
        $category = Category::create([
            'name' => 'BPJS Kesehatan',
            'slug' => 'bpjs-kesehatan',
            'type' => 'all',
        ]);

        $response = $this->actingAs($this->user)->post(route('incomes.store'), [
            'product_name' => 'Tagihan BPJS Kesehatan',
            'category_id' => $category->id,
            'payment_method' => 'cash',
            'quantity' => 1,
            'cost_price' => 0, // Sengaja 0
            'amount' => 150000,
            'customer_target' => '8888801234567890',
        ]);

        $response->assertSessionHasErrors('cost_price');
    }

    public function test_cancelling_bill_payment_refunds_server_deposit_accurately(): void
    {
        $category = Category::create([
            'name' => 'Tagihan WiFi ICONNET',
            'slug' => 'tagihan-iconnet',
            'type' => 'all',
        ]);

        // Simpan transaksi tagihan
        $this->actingAs($this->user)->post(route('incomes.store'), [
            'product_name' => 'Tagihan WiFi ICONNET',
            'category_id' => $category->id,
            'payment_method' => 'cash',
            'quantity' => 1,
            'cost_price' => 200000,
            'amount' => 205000,
            'customer_target' => 'ICON123456',
        ]);

        $transaction = Transaction::where('type', 'pemasukan')->latest('id')->first();
        $this->deposit->refresh();
        $this->assertEquals(800000.00, (float) $this->deposit->balance);

        // Batalkan transaksi
        $cancelResponse = $this->actingAs($this->user)->delete(route('incomes.destroy', $transaction));
        $cancelResponse->assertRedirect(route('incomes.index'));

        // Saldo server harus kembali menjadi Rp 1.000.000 (refund penuh tagihan asli)
        $this->deposit->refresh();
        $this->assertEquals(1000000.00, (float) $this->deposit->balance);
    }
}
