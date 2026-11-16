<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\ServerDeposit;
use App\Models\Transaction;
use App\Models\User;
use App\Services\ReportService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfitCalculationTest extends TestCase
{
    use RefreshDatabase;

    public function test_kulakan_sales_and_deposit_do_not_falsely_plunge_profit_into_minus(): void
    {
        $user = User::factory()->create();
        $today = Carbon::today();

        // 1. Kategori dan Produk
        $productCategory = Category::factory()->create(['name' => 'Aksesoris', 'type' => 'product']);
        $product = Product::factory()->create([
            'category_id' => $productCategory->id,
            'cost_price' => 15000,
            'selling_price' => 25000,
            'stock' => 10,
        ]);

        // 2. Transaksi Penjualan: Omzet 50.000 (2x @ 25.000), Modal HPP 30.000 -> Laba Kotor = 20.000
        $this->actingAs($user)->post(route('incomes.store'), [
            'category_id' => $productCategory->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'quantity' => 2,
            'unit_price' => 25000,
            'amount' => 50000,
            'cost_price' => 15000,
            'payment_method' => 'cash',
            'transaction_date' => $today->toDateTimeString(),
        ]);

        // 3. User melakukan Top-Up Deposit Rp 500.000 (masuk ke pengeluaran toko kategori kulakan-deposit)
        $this->actingAs($user)->post('/server-deposit/topup', [
            'amount' => 500000,
            'record_expense' => '1',
            'payment_method' => 'transfer',
            'notes' => 'Isi saldo SiDOMPUL',
        ]);

        // 4. User ada pengeluaran operasional toko kecil: Jajan Rp 5.000
        $jajanCategory = Category::firstOrCreate(['slug' => 'jajan'], ['name' => 'Beli Jajan', 'type' => 'expense']);
        Transaction::create([
            'invoice_number' => 'EXP-TEST-001',
            'type' => 'pengeluaran',
            'category_id' => $jajanCategory->id,
            'user_id' => $user->id,
            'transaction_date' => $today,
            'total_amount' => 5000,
            'payment_method' => 'cash',
            'notes' => 'Beli kopi & gorengan',
        ]);

        // 5. Verifikasi Laporan Keuangan di ReportController (Laba Tidak Boleh Minus!)
        $response = $this->actingAs($user)->get(route('reports.index', ['tab' => 'laba_rugi']));
        $response->assertStatus(200);

        // Ambil data financialSummary dari view
        $summary = $response->viewData('financialSummary');

        $this->assertEquals(50000, $summary['total_income']);
        $this->assertEquals(30000, $summary['total_cogs']);
        $this->assertEquals(20000, $summary['gross_profit']); // Laba Kotor = 20.000
        $this->assertEquals(5000, $summary['operational_expense']); // Beban operasional riil = 5.000
        $this->assertEquals(500000, $summary['kulakan_expense']); // Belanja modal persediaan = 500.000
        $this->assertEquals(505000, $summary['total_expense']); // Total kas keluar = 505.000

        // Laba Bersih HARUS POSITIF: 20.000 - 5.000 = +15.000 (TIDAK BOLEH MINUS -485.000!)
        $this->assertEquals(15000, $summary['net_profit']);

        // 6. Verifikasi Tren Harian Dashboard (Grafik tidak boleh drop ke minus)
        $reportService = new ReportService();
        $dailyTrend = $reportService->getDailyTrend(7);
        $todayTrend = collect($dailyTrend)->firstWhere('date', $today->toDateString());

        $this->assertNotNull($todayTrend);
        $this->assertEquals(20000, $todayTrend['gross_profit']);
        $this->assertEquals(5000, $todayTrend['expense']); // Operasional
        $this->assertEquals(500000, $todayTrend['kulakan_expense']); // Kulakan
        $this->assertEquals(15000, $todayTrend['net_profit']); // +15.000
    }
}
