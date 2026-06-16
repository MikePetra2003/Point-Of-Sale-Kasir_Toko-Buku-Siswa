@extends('layouts.pos')

@section('title', 'Riwayat Pembelian Dari Supplier')

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

    .badge-selesai {
        background-color: rgba(16, 185, 129, 0.1);
        color: #065f46;
        font-weight: 600;
        font-size: 0.75rem;
        padding: 6px 14px;
        border-radius: 20px;
        display: inline-block;
    }
    .badge-pending {
        background-color: rgba(245, 158, 11, 0.1);
        color: #92400e;
        font-weight: 600;
        font-size: 0.75rem;
        padding: 6px 14px;
        border-radius: 20px;
        display: inline-block;
    }
    .badge-batal {
        background-color: rgba(239, 68, 68, 0.1);
        color: #991b1b;
        font-weight: 600;
        font-size: 0.75rem;
        padding: 6px 14px;
        border-radius: 20px;
        display: inline-block;
    }

    .btn-detail {
        border-radius: 9px;
        padding: 6px 12px;
        font-weight: 600;
        font-size: 0.78rem;
        background-color: rgba(59, 130, 246, 0.06);
        border: 1px solid rgba(59, 130, 246, 0.2);
        color: #2563eb;
        transition: all 0.2s;
    }
    .btn-detail:hover {
        background-color: #2563eb;
        color: #ffffff;
        border-color: #2563eb;
        transform: translateY(-1px);
    }

    .btn-delete {
        border-radius: 9px;
        padding: 6px 12px;
        font-weight: 600;
        font-size: 0.78rem;
        background-color: rgba(239, 68, 68, 0.06);
        border: 1px solid rgba(239, 68, 68, 0.2);
        color: #ef4444;
        transition: all 0.2s;
    }
    .btn-delete:hover {
        background-color: #ef4444;
        color: #ffffff;
        border-color: #ef4444;
        transform: translateY(-1px);
    }

    .pagination {
        margin-bottom: 0;
        gap: 4px;
    }
    .page-link {
        border-radius: 8px !important;
        border: 1px solid #e2e8f0;
        color: #475569;
        font-weight: 600;
        padding: 6px 12px;
    }
    .page-item.active .page-link {
        background-color: #3b82f6;
        border-color: #3b82f6;
        color: #ffffff;
    }
</style>
@endsection

@section('content')
<div class="container-fluid px-0">
    
    <!-- Title & Add Button -->
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-1"><i class="bi bi-truck text-primary me-2"></i>Riwayat Pembelian Dari Supplier</h3>
        </div>
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
        <form action="{{ route('pembelian.index') }}" method="GET" class="d-flex gap-2">
            <div class="flex-grow-1 position-relative">
                <i class="bi bi-search text-muted position-absolute top-50 start-0 translate-middle-y ms-3"></i>
                <input type="text" name="keyword" class="form-control search-input ps-5"
                    placeholder="Cari nomor faktur atau nama supplier..." value="{{ $keyword ?? '' }}">
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
                            <th>No. Faktur</th>
                            <th>Tanggal</th>
                            <th>Supplier</th>
                            <th>Total Harga</th>
                            <th>Status Pembayaran</th>
                            <th class="text-center" style="width: 170px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pembelian as $item)
                        <tr>
                            <td>{{ $loop->iteration + ($pembelian->currentPage() - 1) * $pembelian->perPage() }}</td>
                            <td class="fw-bold text-primary">{{ $item->nomor_faktur }}</td>
                            <td>{{ $item->tanggal_pembelian->format('d/m/Y H:i') }}</td>
                            <td class="fw-semibold text-dark">{{ $item->supplier->nama_supplier ?? '-' }}</td>
                            <td>
                                <div class="fw-bold text-dark">Rp {{ number_format($item->total_akhir, 0, ',', '.') }}</div>
                                @if ($item->diskon > 0)
                                    <div class="text-muted small">
                                        Awal Rp {{ number_format($item->total_harga, 0, ',', '.') }}
                                        &bull; Diskon {{ number_format($item->diskon_persen, 2, ',', '.') }}%
                                    </div>
                                @endif
                                <div class="text-muted small">
                                    Dibayar Rp {{ number_format($item->total_dibayar_supplier, 0, ',', '.') }}
                                    @if ($item->sisa_hutang_supplier > 0)
                                        &bull; Sisa Rp {{ number_format($item->sisa_hutang_supplier, 0, ',', '.') }}
                                    @endif
                                </div>
                            </td>
                            <td>
                                @if ($item->status_pembayaran === 'lunas')
                                    <span class="badge-selesai">Lunas</span>
                                @else
                                    <span class="badge-pending">Belum Lunas</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="{{ route('pembelian.show', $item->id) }}" class="btn btn-detail">
                                        <i class="bi bi-eye-fill me-1"></i> Detail
                                    </a>
                                    <form action="{{ route('pembelian.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus/membatalkan transaksi pembelian ini? Tindakan ini akan mengurangi kembali stok barang terkait.')" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-delete">
                                            <i class="bi bi-trash-fill me-1"></i> Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-5">
                                <i class="bi bi-inbox fs-2 d-block mb-2 text-muted"></i>
                                Belum ada data pembelian tercatat.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($pembelian->hasPages())
        <div class="card-footer bg-white border-0 px-4 py-3 d-flex justify-content-center border-top">
            {{ $pembelian->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
