<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        $products = Product::with('category')->where('stock', '>', 0)->get();
        $transactions = Transaction::with('user')->latest()->take(10)->get();

        return view('transactions.index', compact('categories', 'products', 'transactions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'pay_amount' => 'required|numeric',
            'discount' => 'nullable|numeric|min:0',
        ]);

        $items = $request->items;
        $rawTotal = 0;

        // Hitung Subtotal Kategori & Cek Stok
        foreach ($items as $item) {
            $product = Product::findOrFail($item['id']);
            if ($product->stock < $item['qty']) {
                return redirect()->back()->with('error', "Stok produk {$product->name} tidak mencukupi!");
            }
            $rawTotal += $product->sell_price * $item['qty'];
        }

        // Hitung Nominal Diskon
        $discount = $request->discount ?? 0;
        $afterDiscount = max(0, $rawTotal - $discount);

        // Hitung Pajak 10% (After Discount)
        $tax = round($afterDiscount * 0.10);

        // Grand Total Akhir
        $grandTotal = $afterDiscount + $tax;

        // Cek Uang Pembayaran
        if ($request->pay_amount < $grandTotal) {
            return redirect()->back()->with('error', 'Uang pembayarannya kurang!');
        }

        // Simpan Transaksi Utama
        $transaction = Transaction::create([
            'invoice_number' => 'TRX-' . date('YmdHis'),
            'user_id'        => auth()->id(),
            'total_price'    => $grandTotal,
            'discount'       => $discount,
            'tax'            => $tax, // <-- Simpan pajak
            'pay_amount'     => $request->pay_amount,
            'change_amount'  => $request->pay_amount - $grandTotal,
        ]);

        // Simpan Rincian & Potong Stok
        foreach ($items as $item) {
            $product = Product::findOrFail($item['id']);
            
            TransactionDetail::create([
                'transaction_id' => $transaction->id,
                'product_id'     => $product->id,
                'qty'            => $item['qty'],
                'price'          => $product->sell_price,
                'subtotal'       => $product->sell_price * $item['qty'],
            ]);

            $product->decrement('stock', $item['qty']);
        }

        return redirect()->route('transactions.index')
            ->with('success', 'Transaksi Berhasil!')
            ->with('print_id', $transaction->id);
    }

    // 3. Tampilkan Halaman Struk Thermal Penjualan
    public function print($id)
    {
        $transaction = Transaction::with(['user', 'details.product'])->findOrFail($id);
        return view('transactions.print', compact('transaction'));
    }
}