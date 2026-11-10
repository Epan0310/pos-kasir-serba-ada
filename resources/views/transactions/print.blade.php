<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Struk Penjualan - {{ $transaction->invoice_number }}</title>
    <style>
        @page {
            margin: 0;
        }
        body {
            font-family: 'Courier New', Courier, monospace;
            width: 58mm;
            margin: 0 auto;
            padding: 10px;
            font-size: 12px;
            color: #000;
        }
        .text-center { text-align: center; }
        .text-end { text-align: right; }
        .fw-bold { font-weight: bold; }
        .line {
            border-top: 1px dashed #000;
            margin: 6px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        td {
            padding: 2px 0;
            vertical-align: top;
        }
    </style>
</head>
<body onload="window.print()">

    <!-- Header Struk -->
    <div class="text-center">
        <h3 style="margin: 0; font-size: 14px;">SERBA ADA MART</h3>
        <small>Jl. Raya Utama No. 1, City</small><br>
        <small>Telp: 0812-3456-7890</small>
    </div>

    <div class="line"></div>

    <!-- Informasi Transaksi -->
    <table>
        <tr>
            <td>No : {{ $transaction->invoice_number }}</td>
        </tr>
        <tr>
            <td>Tgl: {{ $transaction->created_at->format('d-m-Y H:i') }}</td>
        </tr>
        <tr>
            <td>Kasir: {{ $transaction->user->name ?? 'Kasir Store' }}</td>
        </tr>
    </table>

    <div class="line"></div>

    <!-- Detail Barang -->
    <table>
        @foreach($transaction->details as $detail)
            <tr>
                <td colspan="2" class="fw-bold">{{ $detail->product->name }}</td>
            </tr>
            <tr>
                <td>{{ $detail->qty }} x {{ number_format($detail->price, 0, ',', '.') }}</td>
                <td class="text-end">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
            </tr>
        @endforeach
    </table>

    <div class="line"></div>

    <!-- Rincian Pembayaran, Diskon & Pajak -->
    <table>
        <tr>
            <td>Subtotal</td>
            <td class="text-end">Rp {{ number_format($transaction->details->sum('subtotal'), 0, ',', '.') }}</td>
        </tr>
        @if($transaction->discount > 0)
            <tr>
                <td>Diskon Promo</td>
                <td class="text-end">- Rp {{ number_format($transaction->discount, 0, ',', '.') }}</td>
            </tr>
        @endif
        @if($transaction->tax > 0)
            <tr>
                <td>Pajak (10%)</td>
                <td class="text-end">+ Rp {{ number_format($transaction->tax, 0, ',', '.') }}</td>
            </tr>
        @endif
        <tr>
            <td class="fw-bold">TOTAL</td>
            <td class="text-end fw-bold">Rp {{ number_format($transaction->total_price, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Bayar</td>
            <td class="text-end">Rp {{ number_format($transaction->pay_amount, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Kembali</td>
            <td class="text-end">Rp {{ number_format($transaction->change_amount, 0, ',', '.') }}</td>
        </tr>
    </table>

    <div class="line"></div>

    <!-- Footer -->
    <div class="text-center" style="margin-top: 8px;">
        <small>-- Terima Kasih --</small><br>
        <small>Barang yang sudah dibeli<br>tidak dapat ditukar</small>
    </div>

</body>
</html>