@extends('layouts.pos')

@section('title', 'Produk Barang - POS SYSTEM')
@section('page-title', 'Produk Barang')

@section('styles')
<style>
    .custom-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 18px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.03);
        transition: all 0.3s ease;
    }
    .custom-card:hover {
        box-shadow: 0 8px 24px rgba(0,0,0,0.06);
    }

    .search-input {
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        padding: 11px 16px;
        font-size: 0.875rem;
        color: #334155;
        transition: all 0.2s;
        background: #f8fafc;
    }
    .search-input:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        outline: none;
        background: #fff;
    }
    .btn-search {
        border-radius: 10px;
        padding: 11px 22px;
        font-weight: 600;
        font-size: 0.875rem;
    }

    .btn-add {
        background: linear-gradient(135deg, #10b981, #059669);
        border: none;
        color: #ffffff;
        font-weight: 600;
        border-radius: 12px;
        padding: 11px 22px;
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);
        transition: all 0.2s;
        text-decoration: none;
    }
    .btn-add:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(16, 185, 129, 0.3);
        background: linear-gradient(135deg, #059669, #047857);
        color: #ffffff;
    }

    .custom-table {
        margin-bottom: 0;
    }
    .custom-table th {
        background-color: #f8fafc;
        color: #64748b;
        font-weight: 600;
        font-size: 0.75rem;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        padding: 15px 20px;
        border-bottom: 1px solid #e2e8f0;
    }
    .custom-table td {
        padding: 15px 20px;
        font-size: 0.875rem;
        color: #334155;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }
    .custom-table tbody tr {
        transition: background-color 0.15s ease;
    }
    .custom-table tbody tr:hover {
        background-color: #f8fafc;
    }

    .badge-safe {
        background-color: rgba(16, 185, 129, 0.1);
        color: #065f46;
        font-weight: 600;
        font-size: 0.78rem;
        padding: 5px 12px;
        border-radius: 20px;
        display: inline-block;
    }
    .badge-warning-stock {
        background-color: rgba(245, 158, 11, 0.1);
        color: #92400e;
        font-weight: 600;
        font-size: 0.78rem;
        padding: 5px 12px;
        border-radius: 20px;
        display: inline-block;
    }
    .badge-danger-stock {
        background-color: rgba(239, 68, 68, 0.1);
        color: #991b1b;
        font-weight: 600;
        font-size: 0.78rem;
        padding: 5px 12px;
        border-radius: 20px;
        display: inline-block;
    }

    .btn-action {
        padding: 7px 14px;
        border-radius: 9px;
        font-size: 0.8rem;
        font-weight: 600;
        border: none;
        transition: all 0.2s;
    }

    .btn-edit {
        background: rgba(245, 158, 11, 0.1);
        color: #92400e;
        text-decoration: none;
    }

    .btn-edit:hover {
        background: #f59e0b;
        color: #fff;
        transform: translateY(-1px);
    }

    .btn-delete {
        background: rgba(239, 68, 68, 0.1);
        color: #991b1b;
    }

    .btn-delete:hover {
        background: #ef4444;
        color: #fff;
        transform: translateY(-1px);
    }

    .pagination-wrapper {
        padding: 16px 20px;
        border-top: 1px solid #f1f5f9;
    }
</style>
@endsection

@section('content')
@php($isOwner = Auth::user()->role === 'owner')

<div class="container-fluid px-0">
    
    <!-- Title -->
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-1"><i class="bi bi-box-seam text-primary me-2"></i>Produk Barang</h3>
            <p class="text-muted small mb-0">Kelola informasi produk buku, kategori, harga jual, dan tingkat stok inventaris kasir.</p>
        </div>
        @if ($isOwner)
            <a href="{{ route('barang.create') }}" class="btn btn-add">
                <i class="bi bi-plus-circle me-1"></i>Tambah Barang
            </a>
        @endif
    </div>

    <!-- Alert Success -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert" style="background-color: rgba(16, 185, 129, 0.12); color: #065f46;">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Search Card Filter -->
    <div class="custom-card p-3 mb-4">
        <form action="{{ route('barang.index') }}" method="GET" class="d-flex gap-2">
            <div class="flex-grow-1 position-relative">
                <i class="bi bi-search text-muted position-absolute top-50 start-0 translate-middle-y ms-3"></i>
                <input type="text" name="keyword" class="form-control search-input ps-5"
                    placeholder="Cari kode atau nama barang..." value="{{ $keyword ?? '' }}">
            </div>
            <button type="submit" class="btn btn-primary btn-search"><i class="bi bi-funnel-fill me-1"></i> Cari</button>
        </form>
    </div>

    <!-- Table Card -->
    <div class="custom-card overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table custom-table align-middle">
                    <thead>
                        <tr>
                            <th style="width: 70px;">No</th>
                            <th>Kode</th>
                            <th>Nama Barang</th>
                            <th>Kategori</th>
                            <th>Satuan</th>
                            <th>Supplier</th>
                            <th class="text-end">Harga Beli</th>
                            <th class="text-end">Harga Jual</th>
                            <th class="text-center" style="width: 100px;">Stok</th>
                            <th class="text-center" style="width: 110px;">Status</th>
                            @if ($isOwner)
                                <th class="text-center" style="width: 180px;">Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($barang as $item)
                        <tr>
                            <td>{{ $loop->iteration + ($barang->currentPage() - 1) * $barang->perPage() }}</td>
                            <td class="text-primary fw-semibold"><small>{{ $item->kode_barang }}</small></td>
                            <td class="fw-bold text-dark">{{ $item->nama_barang }}</td>
                            <td>{{ $item->kategori->nama_kategori ?? '-' }}</td>
                            <td>
                                <div>{{ $item->satuan->nama_satuan ?? 'pcs' }} <small class="text-muted">(dasar)</small></div>
                                @foreach ($item->barangSatuan->where('is_satuan_dasar', false) as $unit)
                                    <div class="text-muted small">
                                        {{ $unit->satuan->nama_satuan ?? '-' }} = {{ $unit->konversi_ke_satuan_dasar }} {{ $item->satuan->nama_satuan ?? 'pcs' }}
                                    </div>
                                @endforeach
                            </td>
                            <td class="text-muted"><small>{{ $item->supplier->nama_supplier ?? '-' }}</small></td>
                            <td class="text-end text-muted">Rp {{ number_format($item->harga_beli, 0, ',', '.') }}</td>
                            <td class="text-end fw-semibold text-dark">Rp {{ number_format($item->harga_jual, 0, ',', '.') }}</td>
                            <td class="text-center">
                                @if ($item->stok > 20)
                                    <span class="badge-safe">{{ $item->stok }} pcs</span>
                                @elseif ($item->stok > 5)
                                    <span class="badge-warning-stock">{{ $item->stok }} pcs</span>
                                @else
                                    <span class="badge-danger-stock">{{ $item->stok }} pcs</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if ($item->is_active)
                                    <span class="badge-safe">Aktif</span>
                                @else
                                    <span class="badge-danger-stock">Tidak Aktif</span>
                                @endif
                            </td>
                            @if ($isOwner)
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('barang.edit', $item->id) }}" class="btn btn-action btn-edit">
                                            <i class="bi bi-pencil-square me-1"></i>Edit
                                        </a>

                                        <form action="{{ route('barang.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus produk barang ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-action btn-delete">
                                                <i class="bi bi-trash me-1"></i>Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            @endif
                        </tr>
                        @empty
                        <tr>
                            <td colspan="{{ $isOwner ? 11 : 10 }}" class="text-center text-muted py-5">
                                <i class="bi bi-inbox fs-2 d-block mb-2 text-muted"></i>
                                Belum ada data produk barang tercatat.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($barang->hasPages())
        <div class="pagination-wrapper bg-white border-0 px-4 py-3 d-flex justify-content-center border-top">
            {{ $barang->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>
</div>
@endsection
