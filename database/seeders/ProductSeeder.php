<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            // Pulsa Operator
            ['cat' => 'pulsa-telkomsel', 'code' => 'TSEL-P05', 'name' => 'Pulsa Telkomsel 5.000', 'cost' => 5400, 'sell' => 7000, 'stock' => 50],
            ['cat' => 'pulsa-telkomsel', 'code' => 'TSEL-P10', 'name' => 'Pulsa Telkomsel 10.000', 'cost' => 10300, 'sell' => 12000, 'stock' => 50],
            ['cat' => 'pulsa-telkomsel', 'code' => 'TSEL-P25', 'name' => 'Pulsa Telkomsel 25.000', 'cost' => 25100, 'sell' => 27000, 'stock' => 30],
            ['cat' => 'pulsa-telkomsel', 'code' => 'TSEL-P50', 'name' => 'Pulsa Telkomsel 50.000', 'cost' => 49800, 'sell' => 52000, 'stock' => 20],
            ['cat' => 'pulsa-indosat', 'code' => 'ISAT-P10', 'name' => 'Pulsa Indosat 10.000', 'cost' => 10250, 'sell' => 12000, 'stock' => 40],
            ['cat' => 'pulsa-indosat', 'code' => 'ISAT-P25', 'name' => 'Pulsa Indosat 25.000', 'cost' => 25000, 'sell' => 27000, 'stock' => 25],
            ['cat' => 'pulsa-indosat', 'code' => 'ISAT-P50', 'name' => 'Pulsa Indosat 50.000', 'cost' => 49700, 'sell' => 52000, 'stock' => 20],
            ['cat' => 'pulsa-xl', 'code' => 'XL-P10', 'name' => 'Pulsa XL 10.000', 'cost' => 10350, 'sell' => 12000, 'stock' => 35],
            ['cat' => 'pulsa-xl', 'code' => 'XL-P25', 'name' => 'Pulsa XL 25.000', 'cost' => 25150, 'sell' => 27000, 'stock' => 20],
            ['cat' => 'pulsa-axis', 'code' => 'AXIS-P10', 'name' => 'Pulsa Axis 10.000', 'cost' => 10200, 'sell' => 12000, 'stock' => 30],
            ['cat' => 'pulsa-tri', 'code' => 'TRI-P10', 'name' => 'Pulsa Tri 10.000', 'cost' => 10150, 'sell' => 12000, 'stock' => 30],
            ['cat' => 'pulsa-smartfren', 'code' => 'SMART-P10', 'name' => 'Pulsa Smartfren 10.000', 'cost' => 10100, 'sell' => 12000, 'stock' => 25],

            // Paket Data / Internet
            ['cat' => 'paket-internet-telkomsel', 'code' => 'TSEL-OMG14', 'name' => 'Paket Internet Telkomsel OMG! 14GB', 'cost' => 48000, 'sell' => 55000, 'stock' => 20],
            ['cat' => 'paket-internet-telkomsel', 'code' => 'TSEL-MAX25', 'name' => 'Paket Internet Telkomsel MAXstream 25GB', 'cost' => 65000, 'sell' => 73000, 'stock' => 15],
            ['cat' => 'paket-internet-indosat', 'code' => 'ISAT-FREE20', 'name' => 'Paket Internet Indosat Freedom 20GB', 'cost' => 42000, 'sell' => 48000, 'stock' => 20],
            ['cat' => 'paket-internet-xl', 'code' => 'XL-FLEX18', 'name' => 'Paket Internet XL Xtra Combo Flex 18GB', 'cost' => 39000, 'sell' => 45000, 'stock' => 15],
            ['cat' => 'paket-internet-axis', 'code' => 'AXIS-BRONET5', 'name' => 'Paket Internet Axis Bronet 24 Jam 5GB', 'cost' => 18000, 'sell' => 22000, 'stock' => 25],
            ['cat' => 'paket-internet-tri', 'code' => 'TRI-HAPPY10', 'name' => 'Paket Internet Tri Happy 10GB 30 Hari', 'cost' => 28000, 'sell' => 33000, 'stock' => 20],

            // Voucher Fisik
            ['cat' => 'voucher-telkomsel', 'code' => 'V-TSEL-3GB', 'name' => 'Voucher Fisik Telkomsel 3GB 7 Hari', 'cost' => 16500, 'sell' => 20000, 'stock' => 25],
            ['cat' => 'voucher-indosat', 'code' => 'V-ISAT-5GB', 'name' => 'Voucher Fisik Indosat Freedom 5GB', 'cost' => 26000, 'sell' => 30000, 'stock' => 20],
            ['cat' => 'voucher-axis', 'code' => 'V-AXIS-2GB', 'name' => 'Voucher Fisik Axis AIGO 2GB 5 Hari', 'cost' => 10500, 'sell' => 13000, 'stock' => 30],
            ['cat' => 'voucher-xl', 'code' => 'V-XL-7GB', 'name' => 'Voucher Fisik XL Xtra Combo 7GB', 'cost' => 24000, 'sell' => 28000, 'stock' => 15],

            // Kartu Perdana
            ['cat' => 'kartu-perdana-telkomsel', 'code' => 'SIM-TSEL-0', 'name' => 'Kartu Perdana Telkomsel Segel Baru', 'cost' => 5000, 'sell' => 10000, 'stock' => 20],
            ['cat' => 'kartu-perdana-indosat', 'code' => 'SIM-ISAT-6G', 'name' => 'Kartu Perdana Indosat IM3 Kuota 6GB', 'cost' => 19000, 'sell' => 25000, 'stock' => 15],

            // Token Listrik PLN
            ['cat' => 'token-listrik-pln', 'code' => 'PLN-20K', 'name' => 'Token Listrik PLN 20.000', 'cost' => 20500, 'sell' => 23000, 'stock' => 999],
            ['cat' => 'token-listrik-pln', 'code' => 'PLN-50K', 'name' => 'Token Listrik PLN 50.000', 'cost' => 50500, 'sell' => 53000, 'stock' => 999],
            ['cat' => 'token-listrik-pln', 'code' => 'PLN-100K', 'name' => 'Token Listrik PLN 100.000', 'cost' => 100500, 'sell' => 103000, 'stock' => 999],

            // E-Wallet
            ['cat' => 'ewallet-dana', 'code' => 'TOPUP-DANA', 'name' => 'Top Up Saldo DANA', 'cost' => 10000, 'sell' => 12000, 'stock' => 100],
            ['cat' => 'ewallet-gopay', 'code' => 'TOPUP-GOPAY', 'name' => 'Top Up Saldo GoPay', 'cost' => 10000, 'sell' => 12000, 'stock' => 100],
            ['cat' => 'ewallet-shopeepay', 'code' => 'TOPUP-SHOPEE', 'name' => 'Top Up Saldo ShopeePay', 'cost' => 10000, 'sell' => 12000, 'stock' => 100],
            ['cat' => 'ewallet-ovo', 'code' => 'TOPUP-OVO', 'name' => 'Top Up Saldo OVO', 'cost' => 10000, 'sell' => 12000, 'stock' => 100],
            ['cat' => 'ewallet-seabank', 'code' => 'TOPUP-SEABANK', 'name' => 'Top Up Saldo SeaBank', 'cost' => 10000, 'sell' => 12000, 'stock' => 100],
        ];

        foreach ($products as $p) {
            $cat = Category::where('slug', $p['cat'])->first();
            if ($cat) {
                Product::updateOrCreate(
                    ['code' => $p['code']],
                    [
                        'category_id' => $cat->id,
                        'name' => $p['name'],
                        'cost_price' => $p['cost'],
                        'selling_price' => $p['sell'],
                        'stock' => $p['stock'],
                        'min_stock' => 5,
                        'unit' => 'pcs',
                        'is_active' => true,
                    ]
                );
            }
        }
    }
}
