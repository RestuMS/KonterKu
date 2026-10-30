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
            ->orderBy('name')
            ->get();

        $incomeCategories = \App\Models\Category::whereIn('type', ['income', 'all'])
            ->orWhere(function($q) {
                $q->where('type', 'transaction')
                  ->whereNotIn('slug', ['jajan', 'beli-kuota', 'beli-mainan', 'beli-bensin', 'operasional-toko']);
            })
            ->withCount('transactions')
            ->orderBy('name')
            ->get();

        $expenseCategories = \App\Models\Category::where('type', 'expense')
            ->orWhere(function($q) {
                $q->whereIn('type', ['transaction', 'all'])
                  ->whereIn('slug', ['jajan', 'beli-kuota', 'beli-mainan', 'beli-bensin', 'operasional-toko']);
            })
            ->withCount('transactions')
            ->orderBy('name')
            ->get();

        return view('settings.index', compact(
            'settings', 
            'user', 
            'productCategories', 
            'incomeCategories', 
            'expenseCategories'
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
}
