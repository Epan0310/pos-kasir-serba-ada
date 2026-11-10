@extends('layouts.app')

@section('title', 'Laporan Penjualan - SerbaAda Mart')

@section('content')
<div class="container py-4">
    <!-- Title & Filter Periode -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h4 class="fw-bold mb-1"><i class="bi bi-graph-up-arrow me-2" style="color: var(--arasta-accent);"></i>Laporan Penjualan & Keuangan</h4>
            <p class="text-muted small mb-0">Ringkasan performa toko dan analisis keuntungan bersih</p>
        </div>

        <form action="{{ route('reports.index') }}" method="GET" class="d-flex gap-2 align-items-center bg-white p-2 rounded-4 border">
            <div>
                <input type="date" name="start_date" class="form-control form-control-sm border-0" value="{{ $startDate }}">
            </div>
            <span class="text-muted small">s/d</span>
            <div>
                <input type="date" name="end_date" class="form-control form-control-sm border-0" value="{{ $endDate }}">
            </div>
            <button type="submit" class="btn btn-sm text-white fw-bold px-3" style="background-color: var(--arasta-accent); border-radius: 8px;">
                <i class="bi bi-filter me-1"></i> Filter
            </button>
        </form>
    </div>

    <!-- Metric Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="bg-white p-4 rounded-4 border border-start border-4 border-warning shadow-sm">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted fw-bold d-block mb-1 text-uppercase">Total Omset</small>
                        <h3 class="fw-bold mb-0 text-dark">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h3>
                    </div>
                    <div class="rounded-circle p-3 bg-warning bg-opacity-10 text-warning">
                        <i class="bi bi-wallet2 fs-3"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="bg-white p-4 rounded-4 border border-start border-4 border-success shadow-sm">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted fw-bold d-block mb-1 text-uppercase">Laba Bersih (Est.)</small>
                        <h3 class="fw-bold mb-0 text-success">Rp {{ number_format($totalProfit, 0, ',', '.') }}</h3>
                    </div>
                    <div class="rounded-circle p-3 bg-success bg-opacity-10 text-success">
                        <i class="bi bi-cash-coin fs-3"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="bg-white p-4 rounded-4 border border-start border-4 border-dark shadow-sm">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted fw-bold d-block mb-1 text-uppercase">Total Transaksi</small>
                        <h3 class="fw-bold mb-0 text-dark">{{ number_format($totalTransactions) }} <span class="fs-6 text-muted font-normal">Struk</span></h3>
                    </div>
                    <div class="rounded-circle p-3 bg-dark bg-opacity-10 text-dark">
                        <i class="bi bi-receipt fs-3"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Produk Terlaris (Top 5) -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 h-100">
                <h6 class="fw-bold mb-3"><i class="bi bi-fire me-2 text-danger"></i>Produk Terlaris</h6>
                <div class="list-group list-group-flush">
                    @forelse($topProducts as $index => $top)
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-2 border-bottom">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge rounded-circle {{ $index == 0 ? 'bg-warning text-dark' : 'bg-light text-dark border' }}" style="width:24px; height:24px; display:inline-flex; align-items:center; justify-content:center;">
                                    {{ $index + 1 }}
                                </span>
                                <div>
                                    <div class="fw-bold small text-truncate" style="max-width: 140px;">{{ $top->product->name ?? 'Produk Dihapus' }}</div>
                                    <small class="text-muted" style="font-size: 11px;">Rp {{ number_format($top->total_sales, 0, ',', '.') }}</small>
                                </div>
                            </div>
                            <span class="badge bg-dark rounded-pill">{{ $top->total_qty }} terjual</span>
                        </div>
                    @empty
                        <div class="text-center text-muted py-4 small">Belum ada transaksi di periode ini.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Tabel Riwayat Transaksi -->
        <div class="col-md-8">
            <div class="card border-0 shadow-sm rounded-4 p-3 h-100">
                <h6 class="fw-bold mb-3"><i class="bi bi-clock-history me-2"></i>Rincian Transaksi Penjualan</h6>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>No Struk</th>
                                <th>Waktu</th>
                                <th>Kasir</th>
                                <th>Total Tagihan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transactions as $trx)
                                <tr>
                                    <td><span class="badge bg-secondary">{{ $trx->invoice_number }}</span></td>
                                    <td class="small">{{ $trx->created_at->format('d/m/Y H:i') }}</td>
                                    <td>{{ $trx->user->name ?? 'Kasir' }}</td>
                                    <td class="fw-bold text-dark">Rp {{ number_format($trx->total_price, 0, ',', '.') }}</td>
                                    <td>
                                        <a href="{{ route('transactions.print', $trx->id) }}" target="_blank" class="btn btn-sm btn-outline-dark" title="Cetak Struk">
                                            <i class="bi bi-printer"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">Tidak ada data transaksi pada rentang tanggal ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection