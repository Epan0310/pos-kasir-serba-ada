@extends('layouts.app')

@section('title', 'Riwayat Transaksi - SerbaAda Mart')

@section('content')
<div class="container-fluid p-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="fw-bold mb-1"><i class="bi bi-clock-history me-2"></i>Riwayat Transaksi</h4>
            <p class="text-muted small mb-0">Daftar seluruh transaksi penjualan yang telah diproses.</p>
        </div>
        <a href="{{ route('transactions.index') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-cart-plus me-1"></i> Ke Layar Kasir
        </a>
    </div>

    <!-- Card Filter -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('transactions.history') }}" method="GET" class="row g-2">
                <div class="col-md-5">
                    <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari Nomor Invoice..." value="{{ request('search') }}">
                </div>
                <div class="col-md-4">
                    <input type="date" name="date" class="form-control form-control-sm" value="{{ request('date') }}">
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-dark w-100"><i class="bi bi-filter me-1"></i> Filter</button>
                    <a href="{{ route('transactions.history') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-counterclockwise"></i> Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabel Riwayat -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size: 0.9rem;">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">No Invoice</th>
                            <th>Tanggal & Waktu</th>
                            <th>Kasir</th>
                            <th>Item Pembelian</th>
                            <th>Diskon</th>
                            <th>Pajak (10%)</th>
                            <th>Total Tagihan</th>
                            <th class="text-end pe-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $trx)
                            <tr>
                                <td class="ps-3 fw-bold text-primary">{{ $trx->invoice_number }}</td>
                                <td>{{ $trx->created_at->format('d/m/Y H:i') }}</td>
                                <td><span class="badge bg-secondary">{{ $trx->user->name ?? 'Kasir' }}</span></td>
                                <td>
                                    <span class="fw-bold">{{ $trx->details->sum('qty') }} pcs</span> 
                                    <small class="text-muted">({{ $trx->details->count() }} jenis barang)</small>
                                </td>
                                <td class="text-danger">- Rp {{ number_format($trx->discount, 0, ',', '.') }}</td>
                                <td class="text-muted">+ Rp {{ number_format($trx->tax, 0, ',', '.') }}</td>
                                <td class="fw-bold text-success">Rp {{ number_format($trx->total_price, 0, ',', '.') }}</td>
                                <td class="text-end pe-3">
                                    <button class="btn btn-sm btn-outline-info me-1" data-bs-toggle="modal" data-bs-target="#detailModal{{ $trx->id }}" title="Lihat Rincian">
                                        <i class="bi bi-eye"></i> Detail
                                    </button>
                                    <a href="{{ route('transactions.print', $trx->id) }}" target="_blank" class="btn btn-sm btn-outline-dark" title="Cetak Ulang Struk">
                                        <i class="bi bi-printer"></i> Struk
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">
                                    <i class="bi bi-inbox fs-3 d-block mb-1"></i>
                                    Belum ada data riwayat transaksi.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($transactions->hasPages())
            <div class="card-footer bg-white border-0 py-3">
                {{ $transactions->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
</div>

<!-- MODAL DETAIL TRANSAKSI (DITARUH DI LUAR TABEL) -->
@foreach($transactions as $trx)
    <div class="modal fade" id="detailModal{{ $trx->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title fw-bold">Detail Invoice: {{ $trx->invoice_number }}</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3 border-bottom pb-2" style="font-size: 0.85rem;">
                        <div><strong>Tanggal:</strong> {{ $trx->created_at->format('d-m-Y H:i:s') }}</div>
                        <div><strong>Kasir:</strong> {{ $trx->user->name ?? 'Kasir Store' }}</div>
                    </div>

                    <table class="table table-sm borderless align-middle mb-3" style="font-size: 0.85rem;">
                        <thead>
                            <tr class="table-light">
                                <th>Produk</th>
                                <th class="text-center">Qty</th>
                                <th class="text-end">Harga</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($trx->details as $item)
                                <tr>
                                    <td>{{ $item->product->name ?? 'Produk Dihapus' }}</td>
                                    <td class="text-center">{{ $item->qty }}</td>
                                    <td class="text-end">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                                    <td class="text-end">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="bg-light p-2 rounded" style="font-size: 0.85rem;">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Subtotal Produk:</span>
                            <span>Rp {{ number_format($trx->details->sum('subtotal'), 0, ',', '.') }}</span>
                        </div>
                        @if($trx->discount > 0)
                            <div class="d-flex justify-content-between mb-1 text-danger">
                                <span>Diskon Promo:</span>
                                <span>- Rp {{ number_format($trx->discount, 0, ',', '.') }}</span>
                            </div>
                        @endif
                        @if($trx->tax > 0)
                            <div class="d-flex justify-content-between mb-1 text-muted">
                                <span>Pajak (10%):</span>
                                <span>+ Rp {{ number_format($trx->tax, 0, ',', '.') }}</span>
                            </div>
                        @endif
                        <div class="d-flex justify-content-between fw-bold border-top pt-1 text-dark">
                            <span>Total Paid:</span>
                            <span>Rp {{ number_format($trx->total_price, 0, ',', '.') }}</span>
                        </div>
                        <div class="d-flex justify-content-between text-muted mt-1" style="font-size: 0.75rem;">
                            <span>Bayar: Rp {{ number_format($trx->pay_amount, 0, ',', '.') }}</span>
                            <span>Kembali: Rp {{ number_format($trx->change_amount, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="{{ route('transactions.print', $trx->id) }}" target="_blank" class="btn btn-sm btn-dark w-100">
                        <i class="bi bi-printer me-1"></i> Cetak Ulang Struk Thermal
                    </a>
                </div>
            </div>
        </div>
    </div>
@endforeach
@endsection