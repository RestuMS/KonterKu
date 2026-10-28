<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class ExpenseCategoriesSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Jajan',
                'slug' => 'jajan',
                'type' => 'transaction',
                'description' => 'Pengeluaran makan, jajan, snack & minum',
            ],
            [
                'name' => 'Beli Kuota',
                'slug' => 'beli-kuota',
                'type' => 'transaction',
                'description' => 'Pengeluaran paket data internet pribadi atau operasional',
            ],
            [
                'name' => 'Beli Mainan',
                'slug' => 'beli-mainan',
                'type' => 'transaction',
                'description' => 'Pengeluaran mainan, hobi & belanja santai',
            ],
            [
                'name' => 'Beli Bensin',
                'slug' => 'beli-bensin',
                'type' => 'transaction',
                'description' => 'Biaya bahan bakar bensin & transportasi kendaraan',
            ],
        ];

        foreach ($categories as $cat) {
            Category::firstOrCreate(['slug' => $cat['slug']], $cat);
        }
    }
}
