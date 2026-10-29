<?php

namespace App\Http\Controllers;

use App\Http\Requests\KasbonRequest;
use App\Models\Category;
use App\Models\Kasbon;
use App\Models\Product;
use App\Models\StockMutation;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class KasbonController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->input('status', 'all');
        $search = $request->input('search');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // 1. DATA DASHBOARD STATISTIK KASBON
        $now = Carbon::today();
        $totalUnpaidAmount = (float) Kasbon::unpaid()->sum('amount');
        $totalUnpaidProfit = (float) Kasbon::unpaid()->sum('profit');
        $totalUnpaidCount = Kasbon::unpaid()->count();

        $totalPaidAmount = (float) Kasbon::paid()->sum('amount');
        $totalPaidProfit = (float) Kasbon::paid()->sum('profit');
        $totalPaidCount = Kasbon::paid()->count();

        $overdueCount = Kasbon::unpaid()
            ->whereNotNull('due_date')
            ->where('due_date', '<', $now)
            ->count();

        $todayDebtsCount = Kasbon::whereDate('debt_date', $now)->count();

        // 2. QUERY DAFTAR KASBON
        $query = Kasbon::with(['user', 'product']);

        if ($status === 'unpaid') {
            $query->unpaid();
        } elseif ($status === 'paid') {
            $query->paid();
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%")
                  ->orWhere('product_name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        if ($startDate) {
            $query->whereDate('debt_date', '>=', Carbon::parse($startDate)->startOfDay());
        }

        if ($endDate) {
            $query->whereDate('debt_date', '<=', Carbon::parse($endDate)->endOfDay());
        }

        // Urutkan: Kasbon belum lunas tampil paling atas, lalu berdasarkan tanggal berhutang terbaru
        $kasbons = $query->orderByRaw("CASE WHEN status = 'unpaid' THEN 0 ELSE 1 END")
            ->latest('debt_date')
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        // Ambil daftar produk aktif untuk dropdown form kasbon
        $products = Product::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'code', 'cost_price', 'selling_price', 'stock']);

        return view('kasbons.index', compact(
            'kasbons',
            'products',
            'status',
            'search',
            'startDate',
            'endDate',
            'totalUnpaidAmount',
            'totalUnpaidProfit',
            'totalUnpaidCount',
            'totalPaidAmount',
            'totalPaidProfit',
            'totalPaidCount',
            'overdueCount',
            'todayDebtsCount'
        ));
    }

    public function store(KasbonRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $quantity = (int) ($validated['quantity'] ?? 1);
        $amount = (float) $validated['amount'];
        $costPrice = (float) ($validated['cost_price'] ?? 0);
        $productName = $validated['product_name'] ?? null;

        // Jika memilih produk dari katalog
        $product = null;
        if (!empty($validated['product_id'])) {
            $product = Product::find($validated['product_id']);
            if ($product) {
                if (empty($productName)) {
                    $productName = $product->name;
                }
                if (!isset($validated['cost_price']) || $validated['cost_price'] === '') {
                    $costPrice = (float) $product->cost_price;
                }
            }
        }

        // Hitung laba dari kasbon ini: Laba = Nominal Jual - (Modal HPP x Qty)
        $profit = max(0, $amount - ($costPrice * $quantity));

        DB::transaction(function () use ($validated, $quantity, $amount, $costPrice, $profit, $productName, $product) {
            $kasbon = Kasbon::create([
                'user_id' => auth()->id(),
                'customer_name' => $validated['customer_name'],
                'customer_phone' => $validated['customer_phone'] ?? null,
                'product_id' => $validated['product_id'] ?? null,
                'product_name' => $productName,
                'quantity' => $quantity,
                'cost_price' => $costPrice,
                'amount' => $amount,
                'profit' => $profit,
                'debt_date' => $validated['debt_date'],
                'due_date' => $validated['due_date'] ?? null,
                'description' => $validated['description'] ?? null,
                'status' => 'unpaid',
                'notes' => $validated['notes'] ?? null,
            ]);

            // Kurangi stok dan catat mutasi keluar jika produk fisik dari katalog dipilih
            if ($product) {
                $stockBefore = $product->stock;
                $product->decrement('stock', $quantity);

                StockMutation::create([
                    'product_id' => $product->id,
                    'user_id' => auth()->id(),
                    'type' => 'out',
                    'quantity' => $quantity,
                    'stock_before' => $stockBefore,
                    'stock_after' => $product->stock,
                    'notes' => "Kasbon pelanggan an. {$validated['customer_name']} (Hutang tgl " . Carbon::parse($validated['debt_date'])->format('d/m/Y') . ")",
                ]);
            }
        });

        return redirect()->route('kasbons.index')
            ->with('success', "Kasbon an. {$validated['customer_name']} sebesar Rp " . number_format($amount, 0, ',', '.') . " (Estimasi Laba: Rp " . number_format($profit, 0, ',', '.') . ") berhasil dicatat.");
    }

    public function update(KasbonRequest $request, Kasbon $kasbon): RedirectResponse
    {
        // Proteksi integritas pembukuan: Larang edit kasbon yang sudah lunas
        if ($kasbon->status === 'paid') {
            return redirect()->route('kasbons.index')
                ->with('error', "Kasbon an. {$kasbon->customer_name} sudah berstatus LUNAS dan tercatat di pembukuan pemasukan. Batalkan status lunas terlebih dahulu jika ingin mengoreksi data.");
        }

        $validated = $request->validated();

        $quantity = (int) ($validated['quantity'] ?? $kasbon->quantity ?: 1);
        $amount = (float) $validated['amount'];
        $costPrice = isset($validated['cost_price']) ? (float) $validated['cost_price'] : (float) $kasbon->cost_price;
        $profit = max(0, $amount - ($costPrice * $quantity));

        $validated['quantity'] = $quantity;
        $validated['cost_price'] = $costPrice;
        $validated['profit'] = $profit;

        $kasbon->update($validated);

        return redirect()->route('kasbons.index')
            ->with('success', "Catatan kasbon an. {$kasbon->customer_name} berhasil diperbarui.");
    }

    /**
     * Tandai kasbon sudah lunas / terbayar.
     * LOGIKA KEUANGAN:
     * Pemasukan, Modal HPP, dan Laba Bersih masuk ke pembukuan PADA HARI KASBON DIBAYAR (paid_at),
     * bukan pada tanggal berhutang.
     */
    public function pay(Request $request, Kasbon $kasbon): RedirectResponse
    {
        $validated = $request->validate([
            'payment_method' => 'required|in:cash,transfer,qris',
            'paid_at' => 'required|date',
            'notes' => 'nullable|string|max:500',
            'record_to_income' => 'nullable|boolean',
        ]);

        $paidAt = Carbon::parse($validated['paid_at']);
        $recordToIncome = $request->boolean('record_to_income', true);

        DB::transaction(function () use ($kasbon, $validated, $paidAt, $recordToIncome) {
            $transactionId = null;

            if ($recordToIncome) {
                // Kategori pemasukan untuk pelunasan kasbon
                $category = Category::firstOrCreate(
                    ['slug' => 'pelunasan-kasbon'],
                    ['name' => 'Pelunasan Kasbon', 'type' => 'income']
                );

                $invoiceNumber = 'KASBON-' . strtoupper(Str::random(6));

                // 1. Buat Header Transaksi Pemasukan Kasir pada tanggal pembayaran ($paidAt)
                $transaction = Transaction::create([
                    'invoice_number' => $invoiceNumber,
                    'type' => 'pemasukan',
                    'category_id' => $category->id,
                    'user_id' => auth()->id(),
                    'transaction_date' => $paidAt,
                    'total_amount' => $kasbon->amount,
                    'payment_method' => $validated['payment_method'],
                    'notes' => "Pelunasan kasbon an. {$kasbon->customer_name}" . ($kasbon->product_name ? " ({$kasbon->product_name})" : "") . " - Berhutang tgl: " . Carbon::parse($kasbon->debt_date)->format('d/m/Y'),
                ]);

                // 2. Buat TransactionItem agar Modal HPP dan Laba Bersih terhitung di Dashboard & Laporan pada tanggal $paidAt
                $qty = $kasbon->quantity ?: 1;
                TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $kasbon->product_id,
                    'product_name' => $kasbon->product_name ?: ($kasbon->description ?: 'Pelunasan Kasbon'),
                    'quantity' => $qty,
                    'cost_price' => $kasbon->cost_price ?: 0,
                    'unit_price' => $kasbon->amount / max(1, $qty),
                    'subtotal' => $kasbon->amount,
                ]);

                $transactionId = $transaction->id;
            }

            // 3. Update status kasbon
            $kasbon->update([
                'status' => 'paid',
                'paid_at' => $paidAt,
                'payment_method' => $validated['payment_method'],
                'transaction_id' => $transactionId,
                'notes' => $validated['notes'] ?? $kasbon->notes,
            ]);
        });

        $methodLabel = match ($validated['payment_method']) {
            'cash' => 'Tunai (Cash)',
            'transfer' => 'Transfer Bank',
            'qris' => 'QRIS / E-Wallet',
            default => strtoupper($validated['payment_method']),
        };

        $tglBayar = $paidAt->format('d/m/Y H:i');

        return redirect()->route('kasbons.index')
            ->with('success', "Kasbon an. {$kasbon->customer_name} berhasil ditandai LUNAS pada {$tglBayar} ({$methodLabel}). Pemasukan Rp " . number_format($kasbon->amount, 0, ',', '.') . " & Laba Bersih Rp " . number_format($kasbon->profit, 0, ',', '.') . " telah masuk ke pembukuan tanggal {$tglBayar}.");
    }

    /**
     * Batalkan status lunas (kembalikan ke belum lunas)
     */
    public function unpay(Kasbon $kasbon): RedirectResponse
    {
        DB::transaction(function () use ($kasbon) {
            if ($kasbon->transaction_id) {
                TransactionItem::where('transaction_id', $kasbon->transaction_id)->delete();
                Transaction::where('id', $kasbon->transaction_id)->delete();
            }

            $kasbon->update([
                'status' => 'unpaid',
                'paid_at' => null,
                'payment_method' => null,
                'transaction_id' => null,
            ]);
        });

        return redirect()->route('kasbons.index')
            ->with('success', "Status kasbon an. {$kasbon->customer_name} telah dikembalikan menjadi BELUM LUNAS. Transaksi pemasukan & laba terkait telah dibatalkan dari pembukuan.");
    }

    public function destroy(Kasbon $kasbon): RedirectResponse
    {
        DB::transaction(function () use ($kasbon) {
            // Jika ada transaksi pemasukan pelunasan, hapus
            if ($kasbon->transaction_id) {
                TransactionItem::where('transaction_id', $kasbon->transaction_id)->delete();
                Transaction::where('id', $kasbon->transaction_id)->delete();
            }

            // Jika kasbon belum lunas dan punya produk stok fisik, kembalikan stoknya
            if ($kasbon->status === 'unpaid' && $kasbon->product_id && $product = Product::find($kasbon->product_id)) {
                $stockBefore = $product->stock;
                $product->increment('stock', $kasbon->quantity ?: 1);

                StockMutation::create([
                    'product_id' => $product->id,
                    'user_id' => auth()->id(),
                    'type' => 'in',
                    'quantity' => $kasbon->quantity ?: 1,
                    'stock_before' => $stockBefore,
                    'stock_after' => $product->stock,
                    'notes' => "Pengembalian stok pembatalan kasbon an. {$kasbon->customer_name}",
                ]);
            }

            $kasbon->delete();
        });

        return redirect()->route('kasbons.index')
            ->with('success', "Catatan kasbon an. {$kasbon->customer_name} berhasil dihapus.");
    }
}
