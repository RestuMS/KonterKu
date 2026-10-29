<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use App\Models\Product;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Blade::directive('rupiah', function ($expression) {
            return "<?php echo 'Rp ' . number_format((float) ($expression), 0, ',', '.'); ?>";
        });

        Blade::directive('tanggal', function ($expression) {
            return "<?php echo ($expression) ? \Carbon\Carbon::parse($expression)->format('d/m/Y') : '-'; ?>";
        });

        Blade::directive('tanggalWaktu', function ($expression) {
            return "<?php echo ($expression) ? \Carbon\Carbon::parse($expression)->format('d/m/Y H:i') : '-'; ?>";
        });

        // Bagikan data indikator stok menipis & kasbon belum lunas ke sidebar & navbar
        View::composer(['components.sidebar', 'components.navbar'], function ($view) {
            $lowStockCount = 0;
            if (Schema::hasTable('products')) {
                $lowStockCount = Product::where('is_active', true)
                    ->whereColumn('stock', '<=', 'min_stock')
                    ->count();
            }

            $unpaidKasbonCount = 0;
            if (Schema::hasTable('kasbons')) {
                $unpaidKasbonCount = \App\Models\Kasbon::where('status', 'unpaid')->count();
            }

            $view->with([
                'lowStockCount' => $lowStockCount,
                'unpaidKasbonCount' => $unpaidKasbonCount,
            ]);
        });
    }
}
