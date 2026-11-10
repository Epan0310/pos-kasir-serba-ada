<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk #{{ $transaction->invoice_number }}</title>
    <style>
        /* CSS khusus format Kertas Thermal 58mm */
        @page {
            size: 58mm auto;
            margin: 0;
        }

        body {
            font-family: 'Courier New', Courier, monospace;
            width: 58mm;
            margin: 0 auto;
            padding: 10px;
            font-size: 11px;
            color: #000;
            background: #fff;
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .fw-bold { font-weight: bold; }
        
        .line {
            border-bottom: 1px dashed #000;
            margin: 6px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td {
            vertical-align: top;
            padding: 2px 0;
        }

        .btn-print-box {
            margin-bottom: 15px;
            text-align: center;
        }

        .btn-print {
            background: #1C1917;
            color: #fff;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
        }

        /* Sembunyikan tombol cetak saat dicetak ke printer */
        @media print {
            .btn-print-box { display: none; }
        }
    </style>
</head>
<body>

    <div class="btn-print-box">
        <button onclick="window.print()" class="btn-print">Cetak Struk</button>
    </div>

    <!-- Header Struk -->
    <div class="text-center">
        <div class="fw-bold" style="font-size: 14px;">SERBA ADA MART</div>
        <div>Jl. Raya Utama No. 1, City</div>
        <div>Telp: 0812-3456-7890</div>
    </div>

    <div class="line"></div>

    <!-- Info Transaksi -->
    <div>
        <div>No   : {{ $transaction->invoice_number }}</div>
        <div>Tgl  : {{ $transaction->created_at->format('d-m-Y H:i') }}</div>
        <div>Kasir: {{ $transaction->user->name ?? 'Kasir' }}</div>
    </div>

    <div class="line"></div>

    <!-- Item Belanja -->
    <table>
        @foreach($transaction->details as $detail)
            <tr>
                <td colspan="2" class="fw-bold">{{ $detail->product->name }}</td>
            </tr>
            <tr>
                <td>{{ $detail->qty }} x {{ number_format($detail->price, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
            </tr>
        @endforeach
    </table>

    <div class="line"></div>

    <!-- Total & Pembayaran -->
    <table>
        <tr>
            <td>Total</td>
            <td class="text-right fw-bold">Rp {{ number_format($transaction->total_price, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Bayar</td>
            <td class="text-right">Rp {{ number_format($transaction->pay_amount, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Kembali</td>
            <td class="text-right fw-bold">Rp {{ number_format($transaction->change_amount, 0, ',', '.') }}</td>
        </tr>
    </table>

    <div class="line"></div>

    <!-- Footer Struk -->
    <div class="text-center" style="margin-top: 10px;">
        <div>-- Terima Kasih --</div>
        <div>Barang yang sudah dibeli</div>
        <div>tidak dapat ditukar</div>
    </div>

    <script>
        // Otomatis buka dialog print saat halaman selesai dimuat
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>