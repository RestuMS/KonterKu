<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\IncomeController;
use App\Http\Controllers\KasbonController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\StockController;
use Illuminate\Support\Facades\Route;

// Rute Autentikasi Publik
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

// Rute Internal Aplikasi (Wajib Autentikasi)
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/', function () {
        return redirect()->route('dashboard');
    });

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Pemasukan / Penjualan
    Route::resource('incomes', IncomeController::class);

    // Pengeluaran
    Route::resource('expenses', ExpenseController::class)->except(['show', 'edit', 'update']);

    // Buku Kasbon / Catatan Hutang Pelanggan
    Route::get('/kasbons', [KasbonController::class, 'index'])->name('kasbons.index');
    Route::post('/kasbons', [KasbonController::class, 'store'])->name('kasbons.store');
    Route::put('/kasbons/{kasbon}', [KasbonController::class, 'update'])->name('kasbons.update');
    Route::delete('/kasbons/{kasbon}', [KasbonController::class, 'destroy'])->name('kasbons.destroy');
    Route::post('/kasbons/{kasbon}/pay', [KasbonController::class, 'pay'])->name('kasbons.pay');
    Route::post('/kasbons/{kasbon}/unpay', [KasbonController::class, 'unpay'])->name('kasbons.unpay');

    // Produk & Stok
    Route::resource('products', ProductController::class);

    // Mutasi & Penyesuaian Stok
    Route::get('/stocks', [StockController::class, 'index'])->name('stocks.index');
    Route::get('/stocks/create', [StockController::class, 'create'])->name('stocks.create');
    Route::post('/stocks', [StockController::class, 'store'])->name('stocks.store');

    // Laporan
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/print', [ReportController::class, 'print'])->name('reports.print');
    Route::get('/reports/export', [ReportController::class, 'export'])->name('reports.export');

    // Pengaturan
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');
    Route::put('/settings/profile', [SettingController::class, 'updateProfile'])->name('settings.profile');
    Route::put('/settings/password', [SettingController::class, 'updatePassword'])->name('settings.password');

    // Manajemen Kategori di Pengaturan
    Route::resource('categories', \App\Http\Controllers\CategoryController::class)->only(['store', 'update', 'destroy']);
});

// Auto Live-Reload Endpoint (Khusus Local Development: Deteksi Perubahan File & Menu Baru Otomatis)
if (app()->environment('local')) {
    Route::get('/_dev/reload-check', function () {
        $dirs = [
            resource_path('views'),
            base_path('routes'),
            app_path('Http/Controllers'),
            app_path('View/Components'),
        ];

        $latest = 0;
        foreach ($dirs as $dir) {
            if (!is_dir($dir)) continue;
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS)
            );
            foreach ($iterator as $file) {
                if ($file->isFile()) {
                    $mtime = $file->getMTime();
                    if ($mtime > $latest) {
                        $latest = $mtime;
                    }
                }
            }
        }

        return response()->json([
            'latest' => $latest,
            'vite_active' => file_exists(public_path('hot')),
        ]);
    })->name('dev.reload-check');
}

