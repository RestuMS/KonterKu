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

    public function test_dashboard_and_report_net_profit_match_and_cash_drawer_is_accurate(): void
    {
        $user = User::factory()->create();
        $today = Carbon::today();

        // 1. Kategori produk & jasa
        $accCategory = Category::factory()->create(['name' => 'Aksesoris', 'type' => 'product']);
        $tarikCategory = Category::firstOrCreate(['slug' => 'tarik-tunai'], ['name' => 'Tarik Tunai', 'type' => 'transaction']);
        $bensinCategory = Category::firstOrCreate(['slug' => 'beli-bensin'], ['name' => 'Beli Bensin', 'type' => 'expense']);

        $product = Product::factory()->create([
            'category_id' => $accCategory->id,
            'cost_price' => 100000,
            'selling_price' => 150000,
            'stock' => 10,
        ]);

        // 2. Penjualan Tunai (Cash): Rp 150.000 (HPP 100.000 -> Laba Kotor +50.000, Kas Laci Masuk +150.000)
        $this->actingAs($user)->post(route('incomes.store'), [
            'category_id' => $accCategory->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'quantity' => 1,
            'unit_price' => 150000,
            'amount' => 150000,
            'cost_price' => 100000,
            'payment_method' => 'cash',
            'transaction_date' => $today->toDateTimeString(),
        ]);

        // 3. Penjualan QRIS: 2x @ 150.000 = Rp 300.000 (HPP 200.000 -> Laba Kotor +100.000, Kas Laci Masuk = 0 karena QRIS)
        $this->actingAs($user)->post(route('incomes.store'), [
            'category_id' => $accCategory->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'quantity' => 2,
            'unit_price' => 150000,
            'amount' => 300000,
            'cost_price' => 100000,
            'payment_method' => 'qris',
            'transaction_date' => $today->copy()->addMinutes(5)->toDateTimeString(),
        ]);

        // 4. Tarik Tunai (Cash Out): Pokok Rp 100.000 + Admin Rp 5.000 (metode cash -> keluar pokok 100.000, masuk admin 5.000)
        $this->actingAs($user)->post(route('incomes.store'), [
            'category_id' => $tarikCategory->id,
            'product_name' => 'Tarik Tunai BRI Rp 100.000',
            'quantity' => 1,
            'unit_price' => 105000,
            'amount' => 105000,
            'cost_price' => 100000,
            'payment_method' => 'cash',
            'customer_target' => '081234567890',
            'notes' => 'Layanan Tarik Tunai BRI',
            'transaction_date' => $today->copy()->addMinutes(10)->toDateTimeString(),
        ]);

        // 5. Pengeluaran Operasional Tunai (Bensin buat kulakan) Rp 15.000 -> HARUS memotong laba & keluar dari laci!
        Transaction::create([
            'invoice_number' => 'EXP-BENSIN-01',
            'type' => 'pengeluaran',
            'category_id' => $bensinCategory->id,
            'user_id' => $user->id,
            'transaction_date' => $today,
            'total_amount' => 15000,
            'payment_method' => 'cash',
            'notes' => 'Beli bensin motor buat kulakan',
        ]);

        // 6. Top-Up Saldo Server via Transfer Bank Rp 500.000 -> TIDAK memotong laba & TIDAK memotong kas laci tunai!
        $this->actingAs($user)->post('/server-deposit/topup', [
            'amount' => 500000,
            'record_expense' => '1',
            'payment_method' => 'transfer',
            'notes' => 'Isi saldo server transfer bank',
        ]);

        // Cek Laporan Laba Keuntungan
        $reportRes = $this->actingAs($user)->get(route('reports.index', ['tab' => 'laba_rugi']));
        $reportRes->assertStatus(200);
        $summary = $reportRes->viewData('financialSummary');

        // Total Omzet = 150.000 + 300.000 + 105.000 = 555.000
        // Total HPP = 100.000 + 200.000 + 100.000 = 400.000
        // Laba Kotor = 155.000
        // Beban Operasional (Bensin 15.000) = 15.000 (meskipun notes ada kata "kulakan"!)
        // Laba Bersih = 155.000 - 15.000 = 140.000
        $this->assertEquals(555000, $summary['total_income']);
        $this->assertEquals(400000, $summary['total_cogs']);
        $this->assertEquals(155000, $summary['gross_profit']);
        $this->assertEquals(15000, $summary['operational_expense']);
        $this->assertEquals(500000, $summary['kulakan_expense']);
        $this->assertEquals(140000, $summary['net_profit']);

        // Cek Kas Laci Tunai (Hanya Fisik Cash):
        // Masuk: 150.000 (jual cash) + 5.000 (admin tarik tunai cash) = 155.000
        // Keluar: 15.000 (bensin cash) + 100.000 (pokok tarik tunai diserahkan) = 115.000
        // Kas Laci Bersih = 155.000 - 115.000 = +40.000 (bukan -110.000 seperti net_cashflow!)
        $this->assertEquals(155000, $summary['cash_drawer_in']);
        $this->assertEquals(115000, $summary['cash_drawer_out']);
        $this->assertEquals(40000, $summary['cash_drawer_net']);

        // Cek Dashboard HARUS 100% SAMA PERSIS dengan Laporan Laba Keuntungan!
        $dashRes = $this->actingAs($user)->get(route('dashboard'));
        $dashRes->assertStatus(200);
        $this->assertEquals($summary['net_profit'], $dashRes->viewData('monthlyNetProfit'));
        $this->assertEquals($summary['net_profit'], $dashRes->viewData('todayNetProfit'));
        $this->assertEquals($summary['operational_expense'], $dashRes->viewData('monthlyOperationalExpense'));
        $this->assertEquals($summary['cash_drawer_net'], $dashRes->viewData('monthlyCashDrawerNet'));
    }
}
