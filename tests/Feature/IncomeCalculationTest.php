<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IncomeCalculationTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Category $category;
    private Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->category = Category::create([
            'name' => 'Pulsa & E-Wallet',
            'slug' => 'pulsa-ewallet',
            'type' => 'all',
        ]);

        $this->product = Product::create([
            'category_id' => $this->category->id,
            'name' => 'Top Up GoPay 10K',
            'code' => 'GOPAY10K',
            'cost_price' => 10000,
            'selling_price' => 12000,
            'stock' => 50,
            'unit' => 'pcs',
            'min_stock' => 5,
            'is_active' => true,
        ]);
    }

    public function test_income_with_quantity_two_stores_correct_revenue_and_positive_profit(): void
    {
        // 2 unit @ 12.000 = total amount 24.000, modal 10.000 x 2 = 20.000, laba = +4.000
        $response = $this->actingAs($this->user)->post(route('incomes.store'), [
            'product_id' => $this->product->id,
            'product_name' => $this->product->name,
            'category_id' => $this->category->id,
            'payment_method' => 'cash',
            'quantity' => 2,
            'cost_price' => 10000,
            'unit_price' => 12000,
            'amount' => 24000,
            'customer_target' => '081234567890',
            'transaction_date' => now()->format('Y-m-d\TH:i'),
        ]);

        $response->assertRedirect(route('incomes.index'));

        $transaction = Transaction::where('type', 'pemasukan')->latest()->first();
        $this->assertNotNull($transaction);
        $this->assertEquals(24000.00, (float) $transaction->total_amount);
        $this->assertEquals(20000.00, (float) $transaction->cost_price);
        $this->assertEquals(4000.00, (float) $transaction->profit);

        $item = $transaction->items->first();
        $this->assertNotNull($item);
        $this->assertEquals(2, $item->quantity);
        $this->assertEquals(10000.00, (float) $item->cost_price);
        $this->assertEquals(12000.00, (float) $item->unit_price);
        $this->assertEquals(24000.00, (float) $item->subtotal);
    }

    public function test_safety_fallback_auto_corrects_unit_price_submitted_as_amount_when_qty_greater_than_one(): void
    {
        // Simulasi jika user/form mengirim amount = 12.000 (harga satuan) padahal quantity = 2
        $response = $this->actingAs($this->user)->post(route('incomes.store'), [
            'product_id' => $this->product->id,
            'product_name' => $this->product->name,
            'category_id' => $this->category->id,
            'payment_method' => 'cash',
            'quantity' => 2,
            'cost_price' => 10000,
            'unit_price' => 12000,
            'amount' => 12000, // Sengaja dikirim harga satuan
            'customer_target' => '081234567890',
            'transaction_date' => now()->format('Y-m-d\TH:i'),
        ]);

        $response->assertRedirect(route('incomes.index'));

        $transaction = Transaction::where('type', 'pemasukan')->latest()->first();
        $this->assertNotNull($transaction);
        // Otomatis dikoreksi ke total_amount = 24.000, sehingga laba tetap positif +4.000 dan tidak minus!
        $this->assertEquals(24000.00, (float) $transaction->total_amount);
        $this->assertEquals(20000.00, (float) $transaction->cost_price);
        $this->assertEquals(4000.00, (float) $transaction->profit);
    }

    public function test_safety_fallback_auto_corrects_from_catalog_product_price_when_qty_greater_than_one(): void
    {
        // Simulasi jika amount dikirim sesuai selling_price katalog (12.000) tanpa unit_price eksplisit tapi qty = 2
        $response = $this->actingAs($this->user)->post(route('incomes.store'), [
            'product_id' => $this->product->id,
            'product_name' => $this->product->name,
            'category_id' => $this->category->id,
            'payment_method' => 'cash',
            'quantity' => 2,
            'cost_price' => 10000,
            'amount' => 12000, // Sengaja kirim harga satuan katalog
            'customer_target' => '081234567890',
            'transaction_date' => now()->format('Y-m-d\TH:i'),
        ]);

        $response->assertRedirect(route('incomes.index'));

        $transaction = Transaction::where('type', 'pemasukan')->latest()->first();
        $this->assertNotNull($transaction);
        $this->assertEquals(24000.00, (float) $transaction->total_amount);
        $this->assertEquals(20000.00, (float) $transaction->cost_price);
        $this->assertEquals(4000.00, (float) $transaction->profit);
    }

    public function test_validation_requires_customer_target_for_pulsa_and_ewallet_when_empty(): void
    {
        // Pulsa & E-Wallet wajib mengisi nomor tujuan / HP
        $response = $this->actingAs($this->user)->post(route('incomes.store'), [
            'product_id' => $this->product->id,
            'product_name' => $this->product->name,
            'category_id' => $this->category->id,
            'payment_method' => 'cash',
            'quantity' => 1,
            'cost_price' => 10000,
            'amount' => 12000,
            'customer_target' => '', // Dikosongkan
        ]);

        $response->assertSessionHasErrors('customer_target');
    }

    public function test_voucher_fisik_and_kartu_perdana_auto_generates_customer_target_without_requiring_phone_number(): void
    {
        // 1. Voucher Fisik Tri
        $voucherCategory = Category::create([
            'name' => 'Voucher Fisik',
            'slug' => 'voucher-fisik',
            'type' => 'all',
        ]);

        $responseVoucher = $this->actingAs($this->user)->post(route('incomes.store'), [
            'product_name' => 'Voucher Tri Happy 5GB',
            'category_id' => $voucherCategory->id,
            'payment_method' => 'cash',
            'quantity' => 1,
            'cost_price' => 15000,
            'amount' => 18000,
            'customer_target' => '', // Dikosongkan oleh user
        ]);

        $responseVoucher->assertRedirect(route('incomes.index'));
        $voucherTx = Transaction::where('type', 'pemasukan')->latest('id')->first();
        $this->assertNotNull($voucherTx);
        $this->assertStringContainsString('Voucher Tri', $voucherTx->notes);

        // 2. Kartu Perdana Telkomsel
        $perdanaCategory = Category::create([
            'name' => 'Kartu Perdana',
            'slug' => 'kartu-perdana',
            'type' => 'all',
        ]);

        $responsePerdana = $this->actingAs($this->user)->post(route('incomes.store'), [
            'product_name' => 'Perdana Telkomsel Lite 10GB',
            'category_id' => $perdanaCategory->id,
            'payment_method' => 'cash',
            'quantity' => 1,
            'cost_price' => 20000,
            'amount' => 25000,
            'customer_target' => '', // Dikosongkan oleh user
        ]);

        $responsePerdana->assertRedirect(route('incomes.index'));
        $perdanaTx = Transaction::where('type', 'pemasukan')->latest('id')->first();
        $this->assertNotNull($perdanaTx);
        $this->assertStringContainsString('Perdana Telkomsel', $perdanaTx->notes);
    }
}

