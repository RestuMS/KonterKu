<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            // Pulsa Operator
            [
                'name' => 'Pulsa Telkomsel',
                'slug' => 'pulsa-telkomsel',
                'type' => 'product',
                'description' => 'Isi ulang pulsa reguler Telkomsel (SimPATI, AS, Loop)',
            ],
            [
                'name' => 'Pulsa Indosat',
                'slug' => 'pulsa-indosat',
                'type' => 'product',
                'description' => 'Isi ulang pulsa reguler Indosat Ooredoo IM3',
            ],
            [
                'name' => 'Pulsa Tri',
                'slug' => 'pulsa-tri',
                'type' => 'product',
                'description' => 'Isi ulang pulsa reguler 3 (Tri)',
            ],
            [
                'name' => 'Pulsa XL',
                'slug' => 'pulsa-xl',
                'type' => 'product',
                'description' => 'Isi ulang pulsa reguler XL Axiata',
            ],
            [
                'name' => 'Pulsa Axis',
                'slug' => 'pulsa-axis',
                'type' => 'product',
                'description' => 'Isi ulang pulsa reguler Axis',
            ],
            [
                'name' => 'Pulsa Smartfren',
                'slug' => 'pulsa-smartfren',
                'type' => 'product',
                'description' => 'Isi ulang pulsa reguler Smartfren',
            ],
            [
                'name' => 'Pulsa By.U',
                'slug' => 'pulsa-byu',
                'type' => 'product',
                'description' => 'Isi ulang pulsa reguler By.U Telkomsel',
            ],

            // Voucher Fisik Operator
            [
                'name' => 'Voucher Telkomsel',
                'slug' => 'voucher-telkomsel',
                'type' => 'product',
                'description' => 'Voucher fisik kuota internet Telkomsel',
            ],
            [
                'name' => 'Voucher Indosat',
                'slug' => 'voucher-indosat',
                'type' => 'product',
                'description' => 'Voucher fisik kuota internet Indosat IM3',
            ],
            [
                'name' => 'Voucher Tri',
                'slug' => 'voucher-tri',
                'type' => 'product',
                'description' => 'Voucher fisik kuota internet Tri (3 AON / Happy)',
            ],
            [
                'name' => 'Voucher XL',
                'slug' => 'voucher-xl',
                'type' => 'product',
                'description' => 'Voucher fisik kuota internet XL Xtra Combo',
            ],
            [
                'name' => 'Voucher Axis',
                'slug' => 'voucher-axis',
                'type' => 'product',
                'description' => 'Voucher fisik kuota internet Axis AIGO / Bronet',
            ],
            [
                'name' => 'Voucher Smartfren',
                'slug' => 'voucher-smartfren',
                'type' => 'product',
                'description' => 'Voucher fisik kuota internet Smartfren Nonstop / Unlimited',
            ],
            [
                'name' => 'Voucher By.U',
                'slug' => 'voucher-byu',
                'type' => 'product',
                'description' => 'Voucher fisik kuota internet By.U',
            ],

            // Paket Internet Operator
            [
                'name' => 'Paket Internet Telkomsel',
                'slug' => 'paket-internet-telkomsel',
                'type' => 'product',
                'description' => 'Injeksi paket kuota internet Telkomsel',
            ],
            [
                'name' => 'Paket Internet Indosat',
                'slug' => 'paket-internet-indosat',
                'type' => 'product',
                'description' => 'Injeksi paket kuota internet Indosat Freedom',
            ],
            [
                'name' => 'Paket Internet Tri',
                'slug' => 'paket-internet-tri',
                'type' => 'product',
                'description' => 'Injeksi paket kuota internet Tri Happy / AON',
            ],
            [
                'name' => 'Paket Internet XL',
                'slug' => 'paket-internet-xl',
                'type' => 'product',
                'description' => 'Injeksi paket kuota internet XL Xtra Combo Flex',
            ],
            [
                'name' => 'Paket Internet Axis',
                'slug' => 'paket-internet-axis',
                'type' => 'product',
                'description' => 'Injeksi paket kuota internet Axis Bronet / Warnet',
            ],
            [
                'name' => 'Paket Internet Smartfren',
                'slug' => 'paket-internet-smartfren',
                'type' => 'product',
                'description' => 'Injeksi paket kuota internet Smartfren Kuota / Unlimited',
            ],
            [
                'name' => 'Paket Internet By.U',
                'slug' => 'paket-internet-byu',
                'type' => 'product',
                'description' => 'Injeksi paket kuota internet By.U Yang Bikin Kumat / Nagih',
            ],

            // Kartu Perdana Operator
            [
                'name' => 'Kartu Perdana Telkomsel',
                'slug' => 'kartu-perdana-telkomsel',
                'type' => 'product',
                'description' => 'Kartu SIM perdana baru Telkomsel',
            ],
            [
                'name' => 'Kartu Perdana Indosat',
                'slug' => 'kartu-perdana-indosat',
                'type' => 'product',
                'description' => 'Kartu SIM perdana baru Indosat IM3',
            ],
            [
                'name' => 'Kartu Perdana Tri',
                'slug' => 'kartu-perdana-tri',
                'type' => 'product',
                'description' => 'Kartu SIM perdana baru Tri',
            ],
            [
                'name' => 'Kartu Perdana XL',
                'slug' => 'kartu-perdana-xl',
                'type' => 'product',
                'description' => 'Kartu SIM perdana baru XL Axiata',
            ],
            [
                'name' => 'Kartu Perdana Axis',
                'slug' => 'kartu-perdana-axis',
                'type' => 'product',
                'description' => 'Kartu SIM perdana baru Axis',
            ],
            [
                'name' => 'Kartu Perdana Smartfren',
                'slug' => 'kartu-perdana-smartfren',
                'type' => 'product',
                'description' => 'Kartu SIM perdana baru Smartfren',
            ],
            [
                'name' => 'Kartu Perdana By.U',
                'slug' => 'kartu-perdana-byu',
                'type' => 'product',
                'description' => 'Kartu SIM perdana baru By.U',
            ],

            // Token Listrik PLN
            [
                'name' => 'Token Listrik PLN',
                'slug' => 'token-listrik-pln',
                'type' => 'product',
                'description' => 'Token stroom listrik prabayar PLN (20k, 50k, 100k, dll.)',
            ],

            // E-Wallet & Bank Digital
            [
                'name' => 'E-Wallet DANA',
                'slug' => 'ewallet-dana',
                'type' => 'all',
                'description' => 'Top up saldo akun dompet digital DANA',
            ],
            [
                'name' => 'E-Wallet GoPay',
                'slug' => 'ewallet-gopay',
                'type' => 'all',
                'description' => 'Top up saldo akun dompet digital GoPay / Gojek',
            ],
            [
                'name' => 'E-Wallet ShopeePay',
                'slug' => 'ewallet-shopeepay',
                'type' => 'all',
                'description' => 'Top up saldo akun dompet digital ShopeePay',
            ],
            [
                'name' => 'E-Wallet SeaBank',
                'slug' => 'ewallet-seabank',
                'type' => 'all',
                'description' => 'Top up saldo / transfer bank digital SeaBank',
            ],
            [
                'name' => 'E-Wallet OVO',
                'slug' => 'ewallet-ovo',
                'type' => 'all',
                'description' => 'Top up saldo akun dompet digital OVO',
            ],

            // Aksesoris & Sparepart
            [
                'name' => 'Aksesoris & Sparepart',
                'slug' => 'aksesoris-sparepart',
                'type' => 'product',
                'description' => 'Kabel data, charger, adaptor, tempered glass, headset, dll.',
            ],

            // Jasa Servis & Transaksi
            [
                'name' => 'Jasa Servis & Transfer',
                'slug' => 'jasa-servis-transfer',
                'type' => 'transaction',
                'description' => 'Jasa servis ringan, transfer uang, tarik tunai, pembayaran tagihan',
            ],
            [
                'name' => 'Operasional Toko',
                'slug' => 'operasional-toko',
                'type' => 'transaction',
                'description' => 'Pengeluaran operasional konter (listrik, sewa, perlengkapan toko)',
            ],
        ];

        foreach ($categories as $cat) {
            Category::updateOrCreate(['slug' => $cat['slug']], $cat);
        }
    }
}
