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
            'items'       => 'required|array|min:1',
            'items.*.id'  => 'required|exists:products,id',
            'items.*.qty' => 'required|integer|min:1',
            'pay_amount'  => 'required|numeric',
        ]);

        $totalPrice = 0;
        $itemsToProcess = [];

        foreach ($request->items as $item) {
            $product = Product::findOrFail($item['id']);

            if ($product->stock < $item['qty']) {
                return redirect()->back()->with('error', "Stok barang '{$product->name}' tidak cukup!");
            }

            $subtotal = $product->sell_price * $item['qty'];
            $totalPrice += $subtotal;

            $itemsToProcess[] = [
                'product'  => $product,
                'qty'      => $item['qty'],
                'price'    => $product->sell_price,
                'subtotal' => $subtotal,
            ];
        }

        if ($request->pay_amount < $totalPrice) {
            return redirect()->back()->with('error', 'Uang pembayaran kurang!');
        }

        $changeAmount = $request->pay_amount - $totalPrice;
        $createdTransaction = null;

        DB::transaction(function () use ($request, $itemsToProcess, $totalPrice, $changeAmount, &$createdTransaction) {
            $user = User::first() ?? User::create([
                'name'     => 'Kasir Utama',
                'username' => 'kasir',
                'email'    => 'kasir@serbaada.com',
                'password' => bcrypt('123456'),
                'role'     => 'kasir',
            ]);

            $createdTransaction = Transaction::create([
                'user_id'        => $user->id,
                'invoice_number' => 'TRX-' . date('YmdHis'),
                'total_price'    => $totalPrice,
                'pay_amount'     => $request->pay_amount,
                'change_amount'  => $changeAmount,
            ]);

            foreach ($itemsToProcess as $item) {
                TransactionDetail::create([
                    'transaction_id' => $createdTransaction->id,
                    'product_id'     => $item['product']->id,
                    'qty'            => $item['qty'],
                    'price'          => $item['price'],
                    'subtotal'       => $item['subtotal'],
                ]);

                $item['product']->decrement('stock', $item['qty']);
            }
        });

        return redirect()->back()->with([
            'success'  => 'Transaksi Berhasil! Kembalian: Rp ' . number_format($changeAmount, 0, ',', '.'),
            'print_id' => $createdTransaction->id,
        ]);
    }

    // 3. Tampilkan Halaman Struk Thermal Penjualan
    public function print($id)
    {
        $transaction = Transaction::with(['user', 'details.product'])->findOrFail($id);
        return view('transactions.print', compact('transaction'));
    }
}