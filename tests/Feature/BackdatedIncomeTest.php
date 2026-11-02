<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\ServerDeposit;
use App\Models\ServerDepositMutation;
use App\Models\StockMutation;
use App\Models\Transaction;
use App\Models\User;
use App\Services\ReportService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BackdatedIncomeTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Category $pulsaCategory;
    private Category $physicalCategory;
    private Product $pulsaProduct;
    private Product $perdanaProduct;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();

        $this->pulsaCategory = Category::create([
            'name' => 'Pulsa Reguler',
            'slug' => 'pulsa-reguler',
            'type' => 'product',
        ]);

        $this->physicalCategory = Category::create([
            'name' => 'Kartu Perdana',
            'slug' => 'kartu-perdana',
            'type' => 'product',
        ]);

        $this->pulsaProduct = Product::create([
            'category_id' => $this->pulsaCategory->id,
            'code' => 'TSEL10',
            'name' => 'Pulsa Telkomsel 10K',
            'cost_price' => 10200,
            'selling_price' => 12000,
            'stock' => 100,
            'min_stock' => 5,
            'unit' => 'trx',
            'is_active' => true,
        ]);

        $this->perdanaProduct = Product::create([
            'category_id' => $this->physicalCategory->id,
            'code' => 'PERD-TRI-10GB',
            'name' => 'Perdana Tri 10GB',
            'cost_price' => 20000,
            'selling_price' => 30000,
            'stock' => 25,
            'min_stock' => 2,
            'unit' => 'pcs',
            'is_active' => true,
        ]);
    }

    public function test_backdated_income_stores_past_transaction_date_and_invoice(): void
    {
        $pastDate = Carbon::now()->subDays(3)->setTime(14, 30, 0);

        $response = $this->actingAs($this->user)->post(route('incomes.store'), [
            'product_id' => $this->pulsaProduct->id,
            'product_name' => $this->pulsaProduct->name,
            'category_id' => $this->pulsaCategory->id,
            'payment_method' => 'cash',
            'quantity' => 1,
            'cost_price' => 10200,
            'amount' => 12000,
            'customer_target' => '081234567890',
            'transaction_date' => $pastDate->format('Y-m-d\TH:i'),
        ]);

        $response->assertRedirect(route('incomes.index'));

        $tx = Transaction::latest('id')->first();
        $this->assertNotNull($tx);
        $this->assertEquals($pastDate->format('Y-m-d H:i'), $tx->transaction_date->format('Y-m-d H:i'));
        $this->assertStringStartsWith('TRX-' . $pastDate->format('Ymd'), $tx->invoice_number);
        $this->assertTrue($tx->isBackdated());
    }

    public function test_backdated_income_creates_stock_mutation_with_matching_transaction_date(): void
    {
        $pastDate = Carbon::now()->subDays(2)->setTime(10, 15, 0);

        $response = $this->actingAs($this->user)->post(route('incomes.store'), [
            'product_id' => $this->perdanaProduct->id,
            'product_name' => $this->perdanaProduct->name,
            'category_id' => $this->physicalCategory->id,
            'payment_method' => 'cash',
            'quantity' => 2,
            'cost_price' => 20000,
            'amount' => 60000,
            'customer_target' => 'Perdana Tri',
            'transaction_date' => $pastDate->format('Y-m-d\TH:i'),
        ]);

        $response->assertRedirect(route('incomes.index'));

        $tx = Transaction::latest('id')->first();
        $mutation = StockMutation::where('reference_type', Transaction::class)
            ->where('reference_id', $tx->id)
            ->first();

        $this->assertNotNull($mutation);
        $this->assertEquals('out', $mutation->type);
        $this->assertEquals(2, $mutation->quantity);
        // Tanggal created_at pada mutasi stok harus cocok dengan tanggal transaksi lampau
        $this->assertEquals($pastDate->format('Y-m-d H:i'), $mutation->created_at->format('Y-m-d H:i'));
        $this->assertStringContainsString('susulan', strtolower($mutation->notes));
    }

    public function test_backdated_income_creates_server_deposit_mutation_with_matching_transaction_date(): void
    {
        ServerDeposit::setBalance(500000);
        $pastDate = Carbon::now()->subDays(4)->setTime(16, 45, 0);

        $response = $this->actingAs($this->user)->post(route('incomes.store'), [
            'product_id' => $this->pulsaProduct->id,
            'product_name' => $this->pulsaProduct->name,
            'category_id' => $this->pulsaCategory->id,
            'payment_method' => 'cash',
            'quantity' => 1,
            'cost_price' => 10200,
            'amount' => 12000,
            'customer_target' => '085712345678',
            'transaction_date' => $pastDate->format('Y-m-d\TH:i'),
        ]);

        $response->assertRedirect(route('incomes.index'));

        $tx = Transaction::latest('id')->first();
        $depositMutation = ServerDepositMutation::where('reference_type', Transaction::class)
            ->where('reference_id', $tx->id)
            ->where('type', 'out')
            ->first();

        $this->assertNotNull($depositMutation);
        $this->assertEquals(10200, (float) $depositMutation->amount);
        // Tanggal created_at pada mutasi saldo server harus mencerminkan tanggal transaksi lampau
        $this->assertEquals($pastDate->format('Y-m-d H:i'), $depositMutation->created_at->format('Y-m-d H:i'));
    }

    public function test_future_transaction_date_is_rejected(): void
    {
        $futureDate = Carbon::now()->addDays(2);

        $response = $this->actingAs($this->user)->post(route('incomes.store'), [
            'product_id' => $this->pulsaProduct->id,
            'product_name' => $this->pulsaProduct->name,
            'category_id' => $this->pulsaCategory->id,
            'payment_method' => 'cash',
            'quantity' => 1,
            'cost_price' => 10200,
            'amount' => 12000,
            'customer_target' => '081234567890',
            'transaction_date' => $futureDate->format('Y-m-d\TH:i'),
        ]);

        $response->assertSessionHasErrors('transaction_date');
    }

    public function test_backdated_income_financial_summary_attributes_to_past_date(): void
    {
        $pastDate = Carbon::now()->subDays(5)->setTime(11, 0, 0);

        $this->actingAs($this->user)->post(route('incomes.store'), [
            'product_id' => $this->perdanaProduct->id,
            'product_name' => $this->perdanaProduct->name,
            'category_id' => $this->physicalCategory->id,
            'payment_method' => 'cash',
            'quantity' => 1,
            'cost_price' => 20000,
            'amount' => 30000,
            'customer_target' => 'Perdana Tri',
            'transaction_date' => $pastDate->format('Y-m-d\TH:i'),
        ]);

        $reportService = new ReportService();

        // Summary pada hari ini TIDAK boleh menghitung pemasukan 5 hari lalu
        $todaySummary = $reportService->getFinancialSummary(
            Carbon::today()->toDateString(),
            Carbon::today()->toDateString()
        );
        $this->assertEquals(0, $todaySummary['total_income']);

        // Summary pada tanggal 5 hari lalu HARUS menghitung pemasukan ini
        $pastSummary = $reportService->getFinancialSummary(
            $pastDate->toDateString(),
            $pastDate->toDateString()
        );
        $this->assertEquals(30000, $pastSummary['total_income']);
        $this->assertEquals(10000, $pastSummary['sales_profit']); // 30.000 - 20.000 = 10.000 laba
    }

    public function test_updating_transaction_date_synchronizes_mutation_dates(): void
    {
        $pastDate1 = Carbon::now()->subDays(3)->setTime(12, 0, 0);

        $this->actingAs($this->user)->post(route('incomes.store'), [
            'product_id' => $this->perdanaProduct->id,
            'product_name' => $this->perdanaProduct->name,
            'category_id' => $this->physicalCategory->id,
            'payment_method' => 'cash',
            'quantity' => 1,
            'cost_price' => 20000,
            'amount' => 30000,
            'customer_target' => 'Perdana Tri',
            'transaction_date' => $pastDate1->format('Y-m-d\TH:i'),
        ]);

        $tx = Transaction::latest('id')->first();
        $this->assertNotNull($tx);

        // Update ke tanggal lain: 6 hari lalu
        $pastDate2 = Carbon::now()->subDays(6)->setTime(9, 30, 0);

        $updateResponse = $this->actingAs($this->user)->put(route('incomes.update', $tx), [
            'category_id' => $this->physicalCategory->id,
            'payment_method' => 'cash',
            'amount' => 35000,
            'transaction_date' => $pastDate2->format('Y-m-d\TH:i'),
        ]);

        $updateResponse->assertRedirect(route('incomes.show', $tx));

        $tx->refresh();
        $this->assertEquals($pastDate2->format('Y-m-d H:i'), $tx->transaction_date->format('Y-m-d H:i'));

        // Cek mutasi stok ter-sinkronisasi tanggalnya
        $stockMutation = StockMutation::where('reference_type', Transaction::class)
            ->where('reference_id', $tx->id)
            ->first();
        $this->assertNotNull($stockMutation);
        $this->assertEquals($pastDate2->format('Y-m-d H:i'), $stockMutation->created_at->format('Y-m-d H:i'));
    }
}

