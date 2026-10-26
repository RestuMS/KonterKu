<?php

namespace App\Http\Controllers;

use App\Http\Requests\SettingRequest;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
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

        return view('settings.index', compact('settings'));
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
}
