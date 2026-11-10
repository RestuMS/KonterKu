<?php

namespace App\Http\Controllers;

use App\Http\Requests\SettingRequest;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function index(): View
    {
        $settings = [
            'shop_name' => Setting::get('shop_name', 'KonterKu'),
            'shop_phone' => Setting::get('shop_phone', ''),
            'shop_address' => Setting::get('shop_address', ''),
            'receipt_footer' => Setting::get('receipt_footer', 'Terima kasih telah berbelanja!'),
            'low_stock_threshold' => Setting::get('low_stock_threshold', '5'),
        ];

        $user = Auth::user();

        // Kategori terpisah sesuai kebutuhan pengaturan
        $productCategories = \App\Models\Category::where('type', 'product')
            ->withCount('products')
            ->orderBy('id')
            ->get();

        $incomeCategories = \App\Models\Category::whereIn('type', ['income', 'all'])
            ->orWhere(function($q) {
                $q->where('type', 'transaction')
                  ->whereNotIn('slug', ['kulakan-sales', 'jajan', 'beli-kuota', 'beli-mainan', 'beli-bensin', 'operasional-toko']);
            })
            ->withCount('transactions')
            ->orderBy('name')
            ->get();

        $expenseCategories = \App\Models\Category::where('type', 'expense')
            ->orWhere(function($q) {
                $q->whereIn('type', ['transaction', 'all'])
                  ->whereIn('slug', ['kulakan-sales', 'jajan', 'beli-kuota', 'beli-mainan', 'beli-bensin', 'operasional-toko']);
            })
            ->withCount('transactions')
            ->orderBy('name')
            ->get();

        $databaseStats = [
            'total_transactions' => \App\Models\Transaction::count(),
            'total_products' => \App\Models\Product::count(),
            'total_stock_mutations' => \App\Models\StockMutation::count(),
            'total_kasbons' => \App\Models\Kasbon::count(),
            'total_server_mutations' => \App\Models\ServerDepositMutation::count(),
            'server_balance' => (float) (\App\Models\ServerDeposit::getPrimary()->balance ?? 0),
        ];

        return view('settings.index', compact(
            'settings', 
            'user', 
            'productCategories', 
            'incomeCategories', 
            'expenseCategories',
            'databaseStats'
        ));
    }

    public function update(SettingRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        foreach ($validated as $key => $value) {
            Setting::set($key, (string) $value);
        }

        return redirect()->route('settings.index')
            ->with('success', 'Pengaturan konter berhasil diperbarui.');
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:30'],
        ], [
            'name.required' => 'Nama pengguna wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.unique' => 'Email ini sudah digunakan oleh akun lain.',
        ]);

        $user->update($validated);

        return redirect()->route('settings.index', ['tab' => 'keamanan'])
            ->with('success', 'Profil akun pengguna berhasil diperbarui.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::min(6)],
        ], [
            'current_password.required' => 'Kata sandi saat ini wajib diisi.',
            'current_password.current_password' => 'Kata sandi saat ini yang Anda masukkan salah.',
            'password.required' => 'Kata sandi baru wajib diisi.',
            'password.confirmed' => 'Konfirmasi kata sandi baru tidak cocok.',
            'password.min' => 'Kata sandi minimal terdiri dari 6 karakter.',
        ]);

        Auth::user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('settings.index', ['tab' => 'keamanan'])
            ->with('success', 'Kata sandi berhasil diubah.');
    }

    /**
     * Unduh file cadangan database lengkap (.sql) untuk pengamanan data konter.
     */
    public function backupDatabase(): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $shopName = Setting::get('shop_name', 'KonterKu');
        $slug = \Illuminate\Support\Str::slug($shopName) ?: 'konterku';
        $fileName = "Backup-{$slug}-" . now()->format('Y-m-d-His') . ".sql";

        $tables = [
            'users',
            'categories',
            'products',
            'transactions',
            'transaction_items',
            'stock_mutations',
            'kasbons',
            'server_deposits',
            'server_deposit_mutations',
            'settings',
        ];

        return response()->streamDownload(function () use ($tables, $shopName) {
            $pdo = \Illuminate\Support\Facades\DB::getPdo();
            $driver = \Illuminate\Support\Facades\DB::getDriverName();

            echo "-- ============================================================\n";
            echo "-- CADANGAN DATABASE SISTEM KONTERKU\n";
            echo "-- Nama Usaha: {$shopName}\n";
            echo "-- Tanggal & Waktu: " . now()->format('Y-m-d H:i:s') . "\n";
            echo "-- Driver Database: " . strtoupper($driver) . "\n";
            echo "-- ============================================================\n\n";

            if ($driver === 'mysql') {
                echo "SET FOREIGN_KEY_CHECKS=0;\n";
                echo "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\n";
                echo "SET NAMES utf8mb4;\n\n";
            }

            foreach ($tables as $table) {
                if (!\Illuminate\Support\Facades\Schema::hasTable($table)) {
                    continue;
                }

                echo "-- ------------------------------------------------------------\n";
                echo "-- Struktur & Data Tabel: `{$table}`\n";
                echo "-- ------------------------------------------------------------\n";

                // 1. CREATE TABLE
                if ($driver === 'mysql') {
                    $createRes = \Illuminate\Support\Facades\DB::select("SHOW CREATE TABLE `{$table}`");
                    $createSql = $createRes[0]->{'Create Table'} ?? '';
                    echo "DROP TABLE IF EXISTS `{$table}`;\n";
                    echo $createSql . ";\n\n";
                } elseif ($driver === 'sqlite') {
                    $createRes = \Illuminate\Support\Facades\DB::select("SELECT sql FROM sqlite_master WHERE type='table' AND name=?", [$table]);
                    $createSql = $createRes[0]->sql ?? '';
                    echo "DROP TABLE IF EXISTS \"{$table}\";\n";
                    if ($createSql) {
                        echo $createSql . ";\n\n";
                    }
                }

                // 2. DUMP DATA BARIS PER BARIS
                $count = \Illuminate\Support\Facades\DB::table($table)->count();
                if ($count > 0) {
                    echo "-- Dumping data untuk tabel `{$table}` ({$count} baris)\n";
                    \Illuminate\Support\Facades\DB::table($table)->orderBy('id')->chunk(100, function ($rows) use ($table, $pdo, $driver) {
                        foreach ($rows as $row) {
                            $rowArray = (array) $row;
                            $columns = array_keys($rowArray);
                            $values = [];

                            foreach ($rowArray as $val) {
                                if (is_null($val)) {
                                    $values[] = 'NULL';
                                } elseif (is_numeric($val) && !is_string($val)) {
                                    $values[] = $val;
                                } else {
                                    $values[] = $pdo->quote((string)$val);
                                }
                            }

                            if ($driver === 'mysql') {
                                $colsJoined = implode('`, `', $columns);
                                $valsJoined = implode(', ', $values);
                                echo "INSERT INTO `{$table}` (`{$colsJoined}`) VALUES ({$valsJoined});\n";
                            } else {
                                $colsJoined = implode('", "', $columns);
                                $valsJoined = implode(', ', $values);
                                echo "INSERT INTO \"{$table}\" (\"{$colsJoined}\") VALUES ({$valsJoined});\n";
                            }
                        }
                    });
                    echo "\n";
                }
            }

            if ($driver === 'mysql') {
                echo "SET FOREIGN_KEY_CHECKS=1;\n";
            }
            echo "-- ======================== AKHIR CADANGAN ========================\n";
        }, $fileName, [
            'Content-Type' => 'application/sql',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
        ]);
    }
}
