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
                'description' => 'Isi ulang pulsa reguler & transfer Telkomsel (SimPATI, By.U, AS)',
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

            // Token Listrik PLN
            [
                'name' => 'Token Listrik PLN',
                'slug' => 'token-listrik-pln',
                'type' => 'product',
                'description' => 'Token stroom listrik prabayar PLN (20k, 50k, 100k, dll.)',
            ],

            // Paket Data & Kartu Perdana
            [
                'name' => 'Paket Data Internet',
                'slug' => 'paket-data-internet',
                'type' => 'product',
                'description' => 'Injeksi paket kuota data langsung ke nomor pembeli',
            ],
            [
                'name' => 'Kartu Perdana',
                'slug' => 'kartu-perdana',
                'type' => 'product',
                'description' => 'Kartu SIM perdana baru dan segel semua operator',
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
                'description' => 'Jasa servis ringan, transfer uang, tarik tunai, top-up e-wallet',
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
