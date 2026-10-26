<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            'shop_name' => 'KonterKu',
            'shop_phone' => '081234567890',
            'shop_address' => 'Jl. Raya Utama No. 123',
            'receipt_footer' => 'Terima kasih telah berbelanja di KonterKu!',
            'low_stock_threshold' => '5',
        ];

        foreach ($settings as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }
    }
}
