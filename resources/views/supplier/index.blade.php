@extends('layouts.pos')

@section('title', 'Data Supplier - POS Toko Buku')
@section('page-title', 'Supplier')

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
        display: inline-flex;
        align-items: center;
        gap: 6px;
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
        padding: 16px 22px;
        border-bottom: 1px solid #e2e8f0;
    }
    .custom-table td {
        padding: 16px 22px;
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
        padding: 18px 22px;
        border-top: 1px solid #f1f5f9;
    }
</style>
@endsection

@section('content')
<div class="container-fluid px-0">
    
    <!-- Title & Add Button -->
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-1"><i class="bi bi-building text-primary me-2"></i>Data Supplier</h3>
            <p class="text-muted small mb-0">Kelola riwayat distributor dan mitra penyuplai pasokan barang toko Anda.</p>
        </div>
        <a href="{{ route('supplier.create') }}" class="btn btn-add">
            <i class="bi bi-plus-circle-fill"></i> Tambah Supplier
        </a>
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
        <form action="{{ route('supplier.index') }}" method="GET" class="d-flex gap-2">
            <div class="flex-grow-1 position-relative">
                <i class="bi bi-search text-muted position-absolute top-50 start-0 translate-middle-y ms-3"></i>
                <input type="text" name="keyword" class="form-control search-input ps-5" 
                    placeholder="Cari berdasarkan nama supplier..." value="{{ $keyword ?? '' }}">
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
                            <th style="width: 80px;">No</th>
                            <th>Nama Supplier</th>
                            <th>No Telepon</th>
                            <th>Alamat</th>
                            <th class="text-center" style="width: 180px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($supplier as $item)
                        <tr>
                            <td class="fw-semibold text-muted">{{ $loop->iteration + ($supplier->currentPage() - 1) * $supplier->perPage() }}</td>
                            <td class="fw-bold text-dark">{{ $item->nama_supplier }}</td>
                            <td class="text-muted"><i class="bi bi-telephone-fill text-secondary me-1" style="font-size: 0.8rem;"></i> {{ $item->no_telepon ?? '-' }}</td>
                            <td class="text-muted"><i class="bi bi-geo-alt-fill text-secondary me-1" style="font-size: 0.8rem;"></i> {{ $item->alamat ?? '-' }}</td>
                            <td>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('supplier.edit', $item->id) }}" class="btn btn-action btn-edit">
                                        <i class="bi bi-pencil-square me-1"></i>Edit
                                    </a>

                                    <form action="{{ route('supplier.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus supplier ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-action btn-delete">
                                            <i class="bi bi-trash me-1"></i>Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-5">
                                <i class="bi bi-inbox fs-2 d-block mb-2 text-muted"></i>
                                Belum ada data supplier terdaftar.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Custom Pagination -->
            @if ($supplier->hasPages())
                <div class="pagination-wrapper">
                    {{ $supplier->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection