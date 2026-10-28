<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExpenseRequest;
use App\Models\Category;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ExpenseController extends Controller
{
    public function index(Request $request): View
    {
        $query = Transaction::with(['category', 'user'])
            ->where('type', 'pengeluaran');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        if ($request->filled('start_date')) {
            $query->where('transaction_date', '>=', Carbon::parse($request->start_date)->startOfDay());
        }

        if ($request->filled('end_date')) {
            $query->where('transaction_date', '<=', Carbon::parse($request->end_date)->endOfDay());
        }

        $expenses = $query->latest('transaction_date')->paginate(12)->withQueryString();
        
        $categories = Category::where('type', 'transaction')
            ->orWhere(function($q) {
                $q->where('type', 'all')->whereNotIn('name', ['Transfer Uang', 'Tarik Tunai']);
            })
            ->get();

        $totalAmount = (clone $query)->sum('total_amount');

        $todayAmount = Transaction::where('type', 'pengeluaran')
            ->whereDate('transaction_date', Carbon::today())
            ->sum('total_amount');

        $monthAmount = Transaction::where('type', 'pengeluaran')
            ->whereBetween('transaction_date', [
                Carbon::now()->startOfMonth(),
                Carbon::now()->endOfMonth(),
            ])
            ->sum('total_amount');

        return view('expenses.index', compact(
            'expenses', 
            'categories', 
            'totalAmount', 
            'todayAmount', 
            'monthAmount'
        ));
    }

    public function create(): View
    {
        $categories = Category::where('type', 'transaction')
            ->orWhere(function($q) {
                $q->where('type', 'all')->whereNotIn('name', ['Transfer Uang', 'Tarik Tunai']);
            })
            ->get();

        // 5 pengeluaran terakhir untuk referensi cepat kasir
        $recentExpenses = Transaction::with('category')
            ->where('type', 'pengeluaran')
            ->latest('transaction_date')
            ->limit(5)
            ->get();

        return view('expenses.create', compact('categories', 'recentExpenses'));
    }

    public function store(ExpenseRequest $request): RedirectResponse
    {
        $invoiceNumber = 'EXP-' . Carbon::parse($request->transaction_date)->format('Ymd') . '-' . strtoupper(substr(uniqid(), -5));

        Transaction::create([
            'invoice_number' => $invoiceNumber,
            'type' => 'pengeluaran',
            'category_id' => $request->category_id,
            'user_id' => Auth::id(),
            'transaction_date' => $request->transaction_date,
            'total_amount' => $request->amount,
            'payment_method' => $request->payment_method,
            'notes' => $request->notes,
        ]);

        return redirect()->route('expenses.index')
            ->with('success', "Pengeluaran {$invoiceNumber} senilai Rp " . number_format($request->amount, 0, ',', '.') . " berhasil dicatat.");
    }

    public function destroy(Transaction $expense): RedirectResponse
    {
        if ($expense->type !== 'pengeluaran') {
            abort(404);
        }

        $invoice = $expense->invoice_number;
        $expense->delete();

        return redirect()->route('expenses.index')
            ->with('success', "Catatan pengeluaran {$invoice} berhasil dihapus.");
    }
}
