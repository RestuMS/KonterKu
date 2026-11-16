<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\URL;
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
        // Paksa HTTPS saat di lingkungan produksi / domain ber-SSL agar tidak terjadi masalah Mixed Content
        if (app()->environment('production') || str_starts_with(config('app.url'), 'https://')) {
            URL::forceScheme('https');
        }
        Blade::directive('rupiah', function ($expression) {
            return "<?php echo 'Rp ' . number_format((float) ($expression), 0, ',', '.'); ?>";
        });

        Blade::directive('tanggal', function ($expression) {
            return "<?php echo ($expression) ? \Carbon\Carbon::parse($expression)->format('d/m/Y') : '-'; ?>";
        });

        Blade::directive('tanggalWaktu', function ($expression) {
            return "<?php echo ($expression) ? \Carbon\Carbon::parse($expression)->format('d/m/Y H:i') : '-'; ?>";
        });

        // Bagikan data indikator stok menipis & kasbon belum lunas ke sidebar & navbar (dioptimalkan)
        View::composer(['components.sidebar', 'components.navbar'], function ($view) {
            static $counts = null;

            if ($counts === null) {
                if (app()->environment('testing')) {
                    $counts = [
                        'lowStockCount' => Product::where('is_active', true)
                            ->whereColumn('stock', '<=', 'min_stock')
                            ->count(),
                        'unpaidKasbonCount' => \App\Models\Kasbon::where('status', 'unpaid')->count(),
                    ];
                } else {
                    // Cache ringan 20 detik agar perpindahan navbar instan & tidak membebani query database di setiap klik
                    $counts = \Illuminate\Support\Facades\Cache::remember('sidebar_nav_indicators', 20, function () {
                        return [
                            'lowStockCount' => Product::where('is_active', true)
                                ->whereColumn('stock', '<=', 'min_stock')
                                ->count(),
                            'unpaidKasbonCount' => \App\Models\Kasbon::where('status', 'unpaid')->count(),
                        ];
                    });
                }
            }

            $view->with($counts);
        });
    }
}
