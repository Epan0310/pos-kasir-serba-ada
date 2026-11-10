<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        // 1. Tangkap Filter Tanggal (Default: Hari Ini)
        $startDate = $request->input('start_date', date('Y-m-d'));
        $endDate   = $request->input('end_date', date('Y-m-d'));

        // 2. Query Transaksi Berdasarkan Range Tanggal
        $transactionsQuery = Transaction::with(['user', 'details.product'])
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate);

        $transactions = (clone $transactionsQuery)->latest()->get();

        // 3. Hitung Stat Ringkasan Keuangan
        $totalRevenue      = $transactions->sum('total_price');
        $totalTransactions = $transactions->count();
        
        // Hitung Laba Bersih = (Harga Jual - Harga Beli/Modal) x Qty
        $totalProfit = 0;
        foreach ($transactions as $trx) {
            foreach ($trx->details as $detail) {
                $buyPrice = $detail->product ? $detail->product->buy_price : 0;
                $totalProfit += ($detail->price - $buyPrice) * $detail->qty;
            }
        }

        // 4. Ambil Top 5 Produk Terlaris
        $topProducts = TransactionDetail::select('product_id', DB::raw('SUM(qty) as total_qty'), DB::raw('SUM(subtotal) as total_sales'))
            ->whereHas('transaction', function ($q) use ($startDate, $endDate) {
                $q->whereDate('created_at', '>=', $startDate)
                  ->whereDate('created_at', '<=', $endDate);
            })
            ->with('product')
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->take(5)
            ->get();

        return view('reports.index', compact(
            'transactions',
            'totalRevenue',
            'totalProfit',
            'totalTransactions',
            'topProducts',
            'startDate',
            'endDate'
        ));
    }
}