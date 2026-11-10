@extends('layouts.app')

@section('title', 'Kelola Kategori - SerbaAda Mart')

@section('content')
<div class="container py-4">

    <!-- Alert Notifikasi Flash -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-3 border-0 shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" onclick="this.closest('.alert').remove()"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-3 border-0 shadow-sm" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" onclick="this.closest('.alert').remove()"></button>
        </div>
    @endif

    <div class="row g-4">
        <!-- Form Tambah Kategori -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-3">
                <h5 class="fw-bold mb-3"><i class="bi bi-folder-plus me-2" style="color: var(--arasta-accent);"></i>Tambah Kategori</h5>
                <form action="{{ route('categories.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nama Kategori</label>
                        <input type="text" name="name" class="form-control" placeholder="Contoh: Makanan" required autofocus>
                    </div>
                    <button type="submit" class="btn text-white w-100 fw-bold" style="background-color: var(--arasta-accent);">
                        Simpan Kategori
                    </button>
                </form>
            </div>
        </div>

        <!-- Tabel Data Kategori -->
        <div class="col-md-8">
            <div class="card border-0 shadow-sm rounded-4 p-3">
                <h5 class="fw-bold mb-3"><i class="bi bi-tags me-2" style="color: var(--arasta-accent);"></i>Daftar Kategori</h5>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th style="width: 10%;">#</th>
                                <th>Nama Kategori</th>
                                <th style="width: 25%;" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($categories as $index => $cat)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td class="fw-bold text-dark">{{ $cat->name }}</td>
                                    <td class="text-center">
                                        <!-- Form Tombol Hapus Aktif -->
                                        <form action="{{ route('categories.destroy', $cat->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori {{ $cat->name }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="bi bi-trash me-1"></i> Hapus
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-4">Belum ada kategori yang ditambahkan.</td>
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