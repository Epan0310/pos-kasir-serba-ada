@extends('layouts.app')

@section('title', 'Kelola Produk - SerbaAda Mart')

<!-- Alert Stok Menipis untuk Admin -->
@php
    $lowStockProducts = $products->filter(function($p) { return $p->stock <= 5; });
@endphp

@if($lowStockProducts->count() > 0)
    <div class="alert alert-warning border-0 shadow-sm d-flex align-items-center justify-content-between mb-3" role="alert">
        <div class="d-flex align-items-center">
            <i class="bi bi-exclamation-triangle-fill fs-4 text-warning me-3"></i>
            <div>
                <strong class="d-block">Perhatian! Ada {{ $lowStockProducts->count() }} produk dengan stok menipis ($\le 5$ pcs).</strong>
                <small class="text-muted">Produk: {{ $lowStockProducts->pluck('name')->implode(', ') }}</small>
            </div>
        </div>
    </div>
@endif

@section('content')
<div class="container py-4">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" onclick="this.closest('.alert').remove()"></button>
        </div>
    @endif

    <div class="row g-4">
        <!-- Form Tambah Produk -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-3">
                <h5 class="fw-bold mb-3"><i class="bi bi-plus-circle me-2"></i>Tambah Produk Baru</h5>
                <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-2">
                        <label class="form-label small fw-bold">Kategori</label>
                        <select name="category_id" class="form-select" required>
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-bold">Kode Barang</label>
                        <input type="text" name="code" class="form-control" placeholder="BRG003" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-bold">Nama Produk</label>
                        <input type="text" name="name" class="form-control" placeholder="Nama Produk" required>
                    </div>
                    <div class="row g-2 mb-2">
                        <div class="col-6">
                            <label class="form-label small fw-bold">Harga Beli</label>
                            <input type="number" name="buy_price" class="form-control" placeholder="0" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold">Harga Jual</label>
                            <input type="number" name="sell_price" class="form-control" placeholder="0" required>
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-bold">Stok Awal</label>
                        <input type="number" name="stock" class="form-control" placeholder="10" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Foto Produk (Opsional)</label>
                        <input type="file" name="image" class="form-control" accept="image/*">
                    </div>
                    <button type="submit" class="btn text-white w-100 fw-bold" style="background-color: var(--arasta-accent);">
                        Simpan Produk
                    </button>
                </form>
            </div>
        </div>

        <!-- Tabel Data Produk -->
        <div class="col-md-8">
            <div class="card border-0 shadow-sm rounded-4 p-3">
                <h5 class="fw-bold mb-3"><i class="bi bi-box-seam me-2"></i>Daftar Produk</h5>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>Foto</th>
                                <th>Kode</th>
                                <th>Nama</th>
                                <th>Kategori</th>
                                <th>Harga Jual</th>
                                <th>Stok</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($products as $prod)
                                <tr>
                                    <td>
                                        @if($prod->image)
                                            <img src="{{ asset('storage/' . $prod->image) }}" alt="{{ $prod->name }}" width="45" height="45" class="rounded object-fit-cover">
                                        @else
                                            <span class="badge bg-secondary">No Pic</span>
                                        @endif
                                    </td>
                                    <td><span class="badge bg-dark">{{ $prod->code }}</span></td>
                                    <td class="fw-bold">{{ $prod->name }}</td>
                                    <td>{{ $prod->category->name }}</td>
                                    <td class="text-success fw-bold">Rp {{ number_format($prod->sell_price, 0, ',', '.') }}</td>
                                    
                                    <!-- Kolom Stok dengan Badge Peringatan -->
                                    <td>
                                        @if($prod->stock <= 0)
                                            <span class="badge bg-danger px-2 py-1">Habis (0)</span>
                                        @elseif($prod->stock <= 5)
                                            <span class="badge bg-warning text-dark px-2 py-1 fw-bold">
                                                <i class="bi bi-exclamation-circle me-1"></i>{{ $prod->stock }} (Menipis)
                                            </span>
                                        @else
                                            <span class="badge bg-success bg-opacity-75 px-2 py-1">{{ $prod->stock }}</span>
                                        @endif
                                    </td>

                                    <td>
                                        <button class="btn btn-sm btn-outline-warning me-1" data-bs-toggle="modal" data-bs-target="#editModal{{ $prod->id }}">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <form action="{{ route('products.destroy', $prod->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus produk ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>

                                <!-- Modal Edit Produk -->
                                <div class="modal fade" id="editModal{{ $prod->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content rounded-4">
                                            <div class="modal-header">
                                                <h5 class="modal-title fw-bold">Edit {{ $prod->name }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form action="{{ route('products.update', $prod->id) }}" method="POST" enctype="multipart/form-data">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-body">
                                                    <div class="mb-2">
                                                        <label class="form-label small fw-bold">Kategori</label>
                                                        <select name="category_id" class="form-select" required>
                                                            @foreach($categories as $cat)
                                                                <option value="{{ $cat->id }}" {{ $prod->category_id == $cat->id ? 'selected' : '' }}>
                                                                    {{ $cat->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="mb-2">
                                                        <label class="form-label small fw-bold">Kode Barang</label>
                                                        <input type="text" name="code" value="{{ $prod->code }}" class="form-control" required>
                                                    </div>
                                                    <div class="mb-2">
                                                        <label class="form-label small fw-bold">Nama Produk</label>
                                                        <input type="text" name="name" value="{{ $prod->name }}" class="form-control" required>
                                                    </div>
                                                    <div class="row g-2 mb-2">
                                                        <div class="col-6">
                                                            <label class="form-label small fw-bold">Harga Beli</label>
                                                            <input type="number" name="buy_price" value="{{ $prod->buy_price }}" class="form-control" required>
                                                        </div>
                                                        <div class="col-6">
                                                            <label class="form-label small fw-bold">Harga Jual</label>
                                                            <input type="number" name="sell_price" value="{{ $prod->sell_price }}" class="form-control" required>
                                                        </div>
                                                    </div>
                                                    <div class="mb-2">
                                                        <label class="form-label small fw-bold">Stok</label>
                                                        <input type="number" name="stock" value="{{ $prod->stock }}" class="form-control" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label small fw-bold">Upload Foto Baru (Opsional)</label>
                                                        <input type="file" name="image" class="form-control" accept="image/*">
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-warning text-white fw-bold">Update Produk</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection