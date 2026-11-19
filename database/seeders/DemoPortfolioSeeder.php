<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Kasbon;
use App\Models\Product;
use App\Models\ServerDeposit;
use App\Models\ServerDepositMutation;
use App\Models\Setting;
use App\Models\StockMutation;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoPortfolioSeeder extends Seeder
{
    /**
     * Isi database baru dengan data simulasi konter yang realistis dan rapi
     * khusus untuk subdomain Demo Portofolio KonterKu.
     */
    public function run(): void
    {
        $this->call([
            CategorySeeder::class,
            ExpenseCategoriesSeeder::class,
            SettingSeeder::class,
        ]);

        // 1. Pengaturan Toko Simulasi
        Setting::set('shop_name', 'KonterKu Cell (Demo Portofolio)');
        Setting::set('shop_phone', '0812-3456-7890');
        Setting::set('shop_address', 'Jl. Merdeka Raya No. 88, Bandung');
        Setting::set('receipt_footer', 'Terima kasih telah bertransaksi di KonterKu Cell! Simpan struk ini sebagai bukti pembayaran resmi.');
        Setting::set('low_stock_threshold', '5');

        // 2. Akun Demo Utama
        $demoEmail = config('app.demo_email', 'demo@konterku.com');
        $demoPassword = config('app.demo_password', 'demo123');

        $demoUser = User::updateOrCreate(
            ['email' => $demoEmail],
            [
                'name' => 'Demo Pemilik Konter',
                'phone' => '081234567890',
                'password' => Hash::make($demoPassword),
                'role' => 'admin',
            ]
        );

        // 3. Ambil Kategori Kunci
        $catPulsaTsel = Category::firstOrCreate(['slug' => 'pulsa-telkomsel'], ['name' => 'Pulsa Telkomsel', 'type' => 'product']);
        $catPulsaIsat = Category::firstOrCreate(['slug' => 'pulsa-indosat'], ['name' => 'Pulsa Indosat', 'type' => 'product']);
        $catVoucherTri = Category::firstOrCreate(['slug' => 'voucher-tri'], ['name' => 'Voucher Tri', 'type' => 'product']);
        $catVoucherAxis = Category::firstOrCreate(['slug' => 'voucher-axis'], ['name' => 'Voucher Axis', 'type' => 'product']);
        $catAksesoris = Category::firstOrCreate(['slug' => 'aksesoris-hp'], ['name' => 'Aksesoris HP', 'type' => 'product']);
        $catTokenPln = Category::firstOrCreate(['slug' => 'token-listrik-pln'], ['name' => 'Token Listrik PLN', 'type' => 'product']);
        $catTransfer = Category::firstOrCreate(['slug' => 'transfer-uang'], ['name' => 'Transfer Uang', 'type' => 'transaction']);
        $catTarik = Category::firstOrCreate(['slug' => 'tarik-tunai'], ['name' => 'Tarik Tunai', 'type' => 'transaction']);

        $catJajan = Category::firstOrCreate(['slug' => 'jajan'], ['name' => 'Beli Jajan', 'type' => 'expense']);
        $catBensin = Category::firstOrCreate(['slug' => 'beli-bensin'], ['name' => 'Beli Bensin', 'type' => 'expense']);
        $catOps = Category::firstOrCreate(['slug' => 'operasional-toko'], ['name' => 'Operasional Toko', 'type' => 'expense']);
        $catKulakanDeposit = Category::firstOrCreate(['slug' => 'kulakan-deposit'], ['name' => 'Kulakan Saldo / Deposit', 'type' => 'expense']);
        $catKulakanSales = Category::firstOrCreate(['slug' => 'kulakan-sales'], ['name' => 'Tagihan Sales', 'type' => 'expense']);

        // 4. Katalog Produk Realistis (Termasuk beberapa stok menipis untuk demo indikator alert)
        $productsData = [
            ['code' => 'PLS-TSEL10', 'name' => 'Pulsa Telkomsel 10.000', 'category_id' => $catPulsaTsel->id, 'cost_price' => 10350, 'selling_price' => 12000, 'stock' => 0, 'min_stock' => 0, 'unit' => 'transaksi'],
            ['code' => 'PLS-TSEL25', 'name' => 'Pulsa Telkomsel 25.000', 'category_id' => $catPulsaTsel->id, 'cost_price' => 24900, 'selling_price' => 27000, 'stock' => 0, 'min_stock' => 0, 'unit' => 'transaksi'],
            ['code' => 'PLS-ISAT20', 'name' => 'Pulsa Indosat 20.000', 'category_id' => $catPulsaIsat->id, 'cost_price' => 19950, 'selling_price' => 22000, 'stock' => 0, 'min_stock' => 0, 'unit' => 'transaksi'],
            ['code' => 'PLN-50', 'name' => 'Token Listrik PLN 50.000', 'category_id' => $catTokenPln->id, 'cost_price' => 50250, 'selling_price' => 53000, 'stock' => 0, 'min_stock' => 0, 'unit' => 'transaksi'],
            ['code' => 'VCR-TRI3GB', 'name' => 'Voucher Tri Happy 3GB 3 Hari', 'category_id' => $catVoucherTri->id, 'cost_price' => 9500, 'selling_price' => 12000, 'stock' => 24, 'min_stock' => 5, 'unit' => 'lembar'],
            ['code' => 'VCR-TRI6GB', 'name' => 'Voucher Tri Happy 6GB 5 Hari', 'category_id' => $catVoucherTri->id, 'cost_price' => 15500, 'selling_price' => 18000, 'stock' => 3, 'min_stock' => 5, 'unit' => 'lembar'],
            ['code' => 'VCR-AX5GB', 'name' => 'Voucher Axis AIGO 5GB 5 Hari', 'category_id' => $catVoucherAxis->id, 'cost_price' => 14000, 'selling_price' => 17000, 'stock' => 18, 'min_stock' => 5, 'unit' => 'lembar'],
            ['code' => 'AKS-KBLTC', 'name' => 'Kabel Data Type-C Fast Charging 1M', 'category_id' => $catAksesoris->id, 'cost_price' => 12000, 'selling_price' => 25000, 'stock' => 14, 'min_stock' => 5, 'unit' => 'pcs'],
            ['code' => 'AKS-ADP20W', 'name' => 'Kepala Charger 20W Fast Charging', 'category_id' => $catAksesoris->id, 'cost_price' => 25000, 'selling_price' => 45000, 'stock' => 2, 'min_stock' => 5, 'unit' => 'pcs'],
            ['code' => 'AKS-TG01', 'name' => 'Tempered Glass Full Cover', 'category_id' => $catAksesoris->id, 'cost_price' => 6000, 'selling_price' => 15000, 'stock' => 20, 'min_stock' => 5, 'unit' => 'pcs'],
        ];

        $products = [];
        foreach ($productsData as $pd) {
            $prod = Product::updateOrCreate(['code' => $pd['code']], array_merge($pd, ['is_active' => true]));
            $products[$pd['code']] = $prod;

            if ($prod->unit !== 'transaksi' && !StockMutation::where('product_id', $prod->id)->exists()) {
                StockMutation::create([
                    'product_id' => $prod->id,
                    'user_id' => $demoUser->id,
                    'type' => 'in',
                    'quantity' => $prod->stock,
                    'stock_before' => 0,
                    'stock_after' => $prod->stock,
                    'notes' => 'Stok awal katalog simulasi portofolio',
                ]);
            }
        }

        // 5. Saldo Server Deposit Tunggal + Mutasi Simulasi
        $deposit = ServerDeposit::getPrimary();
        $deposit->update([
            'name' => 'Saldo Server Aplikasi (All Provider)',
            'balance' => 1845500,
            'min_alert_balance' => 300000,
        ]);

        if (!ServerDepositMutation::exists()) {
            ServerDepositMutation::create([
                'server_deposit_id' => $deposit->id,
                'user_id' => $demoUser->id,
                'type' => 'in',
                'amount' => 2000000,
                'balance_before' => 0,
                'balance_after' => 2000000,
                'notes' => 'Top-Up Saldo Deposit Server (All Provider)',
                'created_at' => Carbon::now()->subDays(5),
                'updated_at' => Carbon::now()->subDays(5),
            ]);
            ServerDepositMutation::create([
                'server_deposit_id' => $deposit->id,
                'user_id' => $demoUser->id,
                'type' => 'out',
                'amount' => 154500,
                'balance_before' => 2000000,
                'balance_after' => 1845500,
                'notes' => 'Pemotongan transaksi penjualan pulsa & token PLN kasir',
                'created_at' => Carbon::now()->subDays(1),
                'updated_at' => Carbon::now()->subDays(1),
            ]);
        }

        // 6. Buat Riwayat Transaksi 7 Hari Terakhir (hanya jika belum ada transaksi demo)
        if (!Transaction::where('invoice_number', 'like', 'DEMO-%')->exists()) {
            $counter = 1;
            for ($dayOffset = 6; $dayOffset >= 0; $dayOffset--) {
                $txDay = Carbon::now()->subDays($dayOffset)->setHour(10)->setMinute(15);

                // Penjualan Voucher Fisik (Tunai)
                $vcrQty = 3 + ($dayOffset % 3);
                $vcrTotal = $vcrQty * 12000;
                $tx1 = Transaction::create([
                    'invoice_number' => 'DEMO-INV-' . str_pad((string) $counter++, 4, '0', STR_PAD_LEFT),
                    'type' => 'pemasukan',
                    'category_id' => $catVoucherTri->id,
                    'user_id' => $demoUser->id,
                    'transaction_date' => $txDay->copy(),
                    'total_amount' => $vcrTotal,
                    'payment_method' => 'cash',
                    'notes' => 'Penjualan Voucher Tri Happy 3GB',
                ]);
                TransactionItem::create([
                    'transaction_id' => $tx1->id,
                    'product_id' => $products['VCR-TRI3GB']->id,
                    'product_name' => $products['VCR-TRI3GB']->name,
                    'quantity' => $vcrQty,
                    'unit_price' => 12000,
                    'cost_price' => 9500,
                    'subtotal' => $vcrTotal,
                ]);

                // Penjualan Aksesoris (QRIS)
                $tx2 = Transaction::create([
                    'invoice_number' => 'DEMO-INV-' . str_pad((string) $counter++, 4, '0', STR_PAD_LEFT),
                    'type' => 'pemasukan',
                    'category_id' => $catAksesoris->id,
                    'user_id' => $demoUser->id,
                    'transaction_date' => $txDay->copy()->addHours(2),
                    'total_amount' => 45000,
                    'payment_method' => 'qris',
                    'notes' => 'Pembayaran QRIS Aksesoris Charger',
                ]);
                TransactionItem::create([
                    'transaction_id' => $tx2->id,
                    'product_id' => $products['AKS-ADP20W']->id,
                    'product_name' => $products['AKS-ADP20W']->name,
                    'quantity' => 1,
                    'unit_price' => 45000,
                    'cost_price' => 25000,
                    'subtotal' => 45000,
                ]);

                // Penjualan Pulsa & Token PLN (Tunai)
                $tx3 = Transaction::create([
                    'invoice_number' => 'DEMO-INV-' . str_pad((string) $counter++, 4, '0', STR_PAD_LEFT),
                    'type' => 'pemasukan',
                    'category_id' => $catTokenPln->id,
                    'user_id' => $demoUser->id,
                    'transaction_date' => $txDay->copy()->addHours(4),
                    'total_amount' => 53000,
                    'payment_method' => 'cash',
                    'notes' => 'Token PLN 50rb (ID: 142100998877)',
                ]);
                TransactionItem::create([
                    'transaction_id' => $tx3->id,
                    'product_id' => $products['PLN-50']->id,
                    'product_name' => $products['PLN-50']->name,
                    'quantity' => 1,
                    'unit_price' => 53000,
                    'cost_price' => 50250,
                    'subtotal' => 53000,
                ]);

                // Transfer Uang / Kirim (Tunai) setiap 2 hari
                if ($dayOffset % 2 === 0) {
                    $tx4 = Transaction::create([
                        'invoice_number' => 'DEMO-INV-' . str_pad((string) $counter++, 4, '0', STR_PAD_LEFT),
                        'type' => 'pemasukan',
                        'category_id' => $catTransfer->id,
                        'user_id' => $demoUser->id,
                        'transaction_date' => $txDay->copy()->addHours(5),
                        'total_amount' => 205000,
                        'payment_method' => 'cash',
                        'notes' => 'Layanan Transfer BRI (No/Rek: 012345678901234) | Biaya Admin: Rp 5.000',
                    ]);
                    TransactionItem::create([
                        'transaction_id' => $tx4->id,
                        'product_id' => null,
                        'product_name' => 'Transfer BRI Rp 200.000',
                        'quantity' => 1,
                        'unit_price' => 205000,
                        'cost_price' => 200000,
                        'subtotal' => 205000,
                    ]);
                }

                // Tarik Tunai (Cash Out) di hari ganjil
                if ($dayOffset % 2 === 1) {
                    $tx5 = Transaction::create([
                        'invoice_number' => 'DEMO-INV-' . str_pad((string) $counter++, 4, '0', STR_PAD_LEFT),
                        'type' => 'pemasukan',
                        'category_id' => $catTarik->id,
                        'user_id' => $demoUser->id,
                        'transaction_date' => $txDay->copy()->addHours(6),
                        'total_amount' => 105000,
                        'payment_method' => 'cash',
                        'notes' => 'Layanan Tarik Tunai Mandiri (No/Rek: 081299887766) | Biaya Admin: Rp 5.000',
                    ]);
                    TransactionItem::create([
                        'transaction_id' => $tx5->id,
                        'product_id' => null,
                        'product_name' => 'Tarik Tunai Mandiri Rp 100.000',
                        'quantity' => 1,
                        'unit_price' => 105000,
                        'cost_price' => 100000,
                        'subtotal' => 105000,
                    ]);
                }

                // Pengeluaran Operasional Ringan (Jajan / Bensin)
                if ($dayOffset % 2 === 0) {
                    Transaction::create([
                        'invoice_number' => 'DEMO-EXP-' . str_pad((string) $counter++, 4, '0', STR_PAD_LEFT),
                        'type' => 'pengeluaran',
                        'category_id' => $dayOffset === 0 ? $catJajan->id : $catBensin->id,
                        'user_id' => $demoUser->id,
                        'transaction_date' => $txDay->copy()->addHours(7),
                        'total_amount' => $dayOffset === 0 ? 10000 : 15000,
                        'payment_method' => 'cash',
                        'notes' => $dayOffset === 0 ? 'Kopi & jajan kasir' : 'Bensin operasional konter',
                    ]);
                }
            }

            // Contoh Kulakan Deposit & Tagihan Sales (Aset Persediaan — Tidak memotong laba bersih!)
            Transaction::create([
                'invoice_number' => 'DEMO-EXP-9001',
                'type' => 'pengeluaran',
                'category_id' => $catKulakanDeposit->id,
                'user_id' => $demoUser->id,
                'transaction_date' => Carbon::now()->subDays(5)->setHour(9),
                'total_amount' => 2000000,
                'payment_method' => 'transfer',
                'notes' => 'Top-Up Saldo Deposit Server (Transfer Bank)',
            ]);

            Transaction::create([
                'invoice_number' => 'DEMO-EXP-9002',
                'type' => 'pengeluaran',
                'category_id' => $catKulakanSales->id,
                'user_id' => $demoUser->id,
                'transaction_date' => Carbon::now()->subDays(3)->setHour(14),
                'total_amount' => 450000,
                'payment_method' => 'transfer',
                'notes' => '[ORDER SALES] Kulakan stok voucher fisik Tri & Axis',
            ]);
        }

        // 7. Catatan Buku Piutang / Kasbon Simulasi
        if (!Kasbon::exists()) {
            Kasbon::create([
                'user_id' => $demoUser->id,
                'product_id' => $products['VCR-TRI6GB']->id,
                'product_name' => $products['VCR-TRI6GB']->name,
                'quantity' => 2,
                'cost_price' => 31000,
                'amount' => 36000,
                'profit' => 5000,
                'customer_name' => 'Rudi Hartono',
                'customer_phone' => '081234560001',
                'description' => 'Ambil 2 Voucher Tri 6GB bayar besok',
                'debt_date' => Carbon::now()->subDays(4)->toDateString(),
                'due_date' => Carbon::now()->subDay()->toDateString(), // Lewat jatuh tempo untuk demo alert!
                'status' => 'unpaid',
            ]);

            Kasbon::create([
                'user_id' => $demoUser->id,
                'product_id' => $products['AKS-KBLTC']->id,
                'product_name' => $products['AKS-KBLTC']->name,
                'quantity' => 1,
                'cost_price' => 12000,
                'amount' => 25000,
                'profit' => 13000,
                'customer_name' => 'Siti Aminah',
                'customer_phone' => '081234560002',
                'description' => 'Kabel Data Type-C bayar hari ini',
                'debt_date' => Carbon::now()->subDays(2)->toDateString(),
                'due_date' => Carbon::today()->toDateString(), // Jatuh tempo hari ini
                'status' => 'unpaid',
            ]);

            Kasbon::create([
                'user_id' => $demoUser->id,
                'product_id' => $products['PLS-TSEL25']->id,
                'product_name' => $products['PLS-TSEL25']->name,
                'quantity' => 1,
                'cost_price' => 24900,
                'amount' => 27000,
                'profit' => 2100,
                'customer_name' => 'Agus Setiawan',
                'customer_phone' => '081234560003',
                'description' => 'Pulsa Telkomsel 25rb (Sudah Lunas)',
                'debt_date' => Carbon::now()->subDays(3)->toDateString(),
                'due_date' => Carbon::now()->subDay()->toDateString(),
                'paid_at' => Carbon::now()->subDay(),
                'status' => 'paid',
            ]);
        }
    }
}
