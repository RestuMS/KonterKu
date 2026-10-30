<?php

namespace App\Http\Controllers;

use App\Models\ServerDeposit;
use App\Models\ServerDepositMutation;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ServerDepositController extends Controller
{
    /**
     * Halaman Utama Riwayat & Mutasi Saldo Server Aplikasi.
     */
    public function mutations(Request $request): View
    {
        $serverDeposit = ServerDeposit::getPrimary();
        $query = ServerDepositMutation::with('user');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('notes', 'like', "%{$search}%");
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('start_date')) {
            $query->where('created_at', '>=', Carbon::parse($request->start_date)->startOfDay());
        }

        if ($request->filled('end_date')) {
            $query->where('created_at', '<=', Carbon::parse($request->end_date)->endOfDay());
        }

        $mutations = $query->latest('id')->paginate(15)->withQueryString();

        // Metrik Statistik Ringkasan Finansial Saldo
        $totalIn = ServerDepositMutation::where('type', 'in')->sum('amount');
        $totalOut = ServerDepositMutation::where('type', 'out')->sum('amount');
        $todayMutationsCount = ServerDepositMutation::whereDate('created_at', Carbon::today())->count();

        // Preset strings untuk navigasi cepat
        $todayStr = Carbon::today()->toDateString();
        $weekAgoStr = Carbon::today()->subDays(6)->toDateString();
        $monthStartStr = Carbon::today()->startOfMonth()->toDateString();
        $monthEndStr = Carbon::today()->endOfMonth()->toDateString();

        return view('server-deposits.mutations', compact(
            'serverDeposit',
            'mutations',
            'totalIn',
            'totalOut',
            'todayMutationsCount',
            'todayStr',
            'weekAgoStr',
            'monthStartStr',
            'monthEndStr'
        ));
    }

    /**
     * Helper redirect kembali atau fallback ke dashboard.
     */
    private function redirectBackOrDashboard(): RedirectResponse
    {
        $previous = url()->previous();
        if ($previous && $previous !== url('/') && $previous !== 'http://localhost') {
            return back();
        }
        return redirect()->route('dashboard');
    }

    /**
     * Top-up / Tambah saldo deposit server aplikasi.
     */
    public function topup(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:1000'],
        ], [
            'amount.required' => 'Nominal top-up saldo wajib diisi.',
            'amount.numeric' => 'Nominal top-up harus berupa angka valid.',
            'amount.min' => 'Nominal top-up minimal Rp 1.000.',
        ]);

        $deposit = ServerDeposit::addBalance((float) $validated['amount'], 'Top-Up / Tambah Saldo Manual');

        return $this->redirectBackOrDashboard()
            ->with('success', 'Saldo deposit berhasil ditambah sebesar Rp ' . number_format($validated['amount'], 0, ',', '.') . '. Saldo saat ini: Rp ' . number_format($deposit->balance, 0, ',', '.') . '.');
    }

    /**
     * Koreksi / Atur langsung nominal saldo deposit server aplikasi.
     */
    public function adjust(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'new_balance' => ['required', 'numeric', 'min:0'],
        ], [
            'new_balance.required' => 'Nominal saldo baru wajib diisi.',
            'new_balance.numeric' => 'Nominal saldo harus berupa angka valid.',
            'new_balance.min' => 'Nominal saldo tidak boleh bernilai negatif.',
        ]);

        $deposit = ServerDeposit::setBalance((float) $validated['new_balance'], 'Koreksi Sisa Saldo Manual');

        return $this->redirectBackOrDashboard()
            ->with('success', 'Sisa saldo server berhasil dikoreksi menjadi Rp ' . number_format($deposit->balance, 0, ',', '.') . '.');
    }

    /**
     * Perbarui identitas server & batas saldo peringatan.
     */
    public function updateSettings(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'min_alert_balance' => ['required', 'numeric', 'min:0'],
            'account_id' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:500'],
        ], [
            'name.required' => 'Nama aplikasi/server wajib diisi.',
            'min_alert_balance.required' => 'Batas saldo minimal wajib diisi.',
        ]);

        $deposit = ServerDeposit::getPrimary();
        $deposit->update($validated);

        return $this->redirectBackOrDashboard()
            ->with('success', 'Pengaturan server saldo deposit berhasil diperbarui.');
    }

    /**
     * Fallback update resource.
     */
    public function update(Request $request, ServerDeposit $serverDeposit): RedirectResponse
    {
        if ($request->has('new_balance')) {
            return $this->adjust($request);
        }

        if ($request->has('amount')) {
            return $this->topup($request);
        }

        return $this->updateSettings($request);
    }
}
