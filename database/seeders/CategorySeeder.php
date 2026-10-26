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
            [
                'name' => 'Pulsa & Paket Data',
                'slug' => 'pulsa-paket-data',
                'type' => 'product',
                'description' => 'Produk isi ulang pulsa dan paket kuota data internet',
            ],
            [
                'name' => 'Voucher Fisik',
                'slug' => 'voucher-fisik',
                'type' => 'product',
                'description' => 'Voucher gosok fisik paket data semua operator',
            ],
            [
                'name' => 'Kartu Perdana',
                'slug' => 'kartu-perdana',
                'type' => 'product',
                'description' => 'Kartu SIM perdana baru dan segel',
            ],
            [
                'name' => 'Aksesoris & Sparepart',
                'slug' => 'aksesoris-sparepart',
                'type' => 'product',
                'description' => 'Kabel data, charger, casing, tempered glass, headset, dll.',
            ],
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
