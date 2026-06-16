@extends('layouts.pos')

@section('title', 'Riwayat Penjualan - POS Toko Buku')
@section('page-title', 'Riwayat Penjualan')

@section('styles')
<style>
    .page-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 16px;
        margin-bottom: 28px;
    }
    .page-header h2 {
        font-size: 1.5rem;
        font-weight: 800;
        color: #0f172a;
        margin: 0;
    }

    .search-card {
        background: #fff;
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        padding: 18px 22px;
        margin-bottom: 22px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.03);
    }
    .search-card .form-control {
        border-radius: 11px;
        border: 1px solid #e2e8f0;
        padding: 11px 16px;
        font-size: 0.875rem;
        background: #f8fafc;
    }
    .search-card .form-control:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59,130,246,0.1);
        background: #fff;
    }
    .search-card .btn-search {
        background: #3b82f6;
        border: none;
        color: #fff;
        border-radius: 11px;
        padding: 11px 22px;
        font-weight: 600;
        font-size: 0.875rem;
        transition: all 0.2s;
    }
    .search-card .btn-search:hover { background: #2563eb; color: #fff; transform: translateY(-1px); }

    .data-card {
        background: #fff;
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0,0,0,0.03);
    }
    .data-card .table { margin-bottom: 0; font-size: 0.875rem; }
    .data-card .table thead th {
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        font-weight: 600;
        color: #64748b;
        text-transform: uppercase;
        font-size: 0.73rem;
        letter-spacing: 0.06em;
        padding: 15px 20px;
    }
    .data-card .table tbody td {
        padding: 16px 20px;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        color: #334155;
    }
    .data-card .table tbody tr:last-child td { border-bottom: none; }
    .data-card .table tbody tr:hover { background: #f8fafc; }

    .invoice-code {
        font-weight: 700;
        color: #1e293b;
        font-family: 'Courier New', monospace;
        font-size: 0.82rem;
        background: #f1f5f9;
        padding: 4px 10px;
        border-radius: 6px;
    }

    .badge-status {
        font-size: 0.73rem;
        font-weight: 600;
        padding: 6px 14px;
        border-radius: 20px;
    }
    .badge-lunas { background: rgba(16, 185, 129, 0.1); color: #065f46; }
    .badge-sebagian { background: rgba(245, 158, 11, 0.1); color: #92400e; }
    .badge-belum { background: rgba(239, 68, 68, 0.1); color: #991b1b; }

    .btn-detail {
        padding: 7px 16px;
        border-radius: 9px;
        font-size: 0.8rem;
        font-weight: 600;
        border: 1px solid #e2e8f0;
        background: #fff;
        color: #3b82f6;
        text-decoration: none;
        transition: all 0.2s;
    }
    .btn-detail:hover {
        background: rgba(59, 130, 246, 0.05);
        border-color: #3b82f6;
        color: #2563eb;
        transform: translateY(-1px);
    }

    .pagination-wrapper {
        padding: 18px 22px;
        border-top: 1px solid #f1f5f9;
    }

    .stats-row {
        display: flex;
        gap: 16px;
        margin-bottom: 22px;
        flex-wrap: wrap;
    }
    .stat-mini {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 18px 22px;
        flex: 1;
        min-width: 140px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.03);
        transition: all 0.2s;
    }
    .stat-mini:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.06); }
    .stat-mini .stat-label { font-size: 0.73rem; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 0.04em; }
    .stat-mini .stat-value { font-size: 1.4rem; font-weight: 800; color: #0f172a; margin-top: 6px; }
    .stat-mini .stat-value.text-success { color: #10b981 !important; }
    .stat-mini .stat-value.text-warning { color: #f59e0b !important; }

    .alert-pos {
        border-radius: 14px;
        border: none;
        padding: 15px 22px;
        font-size: 0.875rem;
        font-weight: 500;
    }
</style>
@endsection

@section('content')
@if (session('success'))
    <div class="alert alert-success alert-pos alert-dismissible fade show">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<!-- Page Header -->
<div class="page-header">
    <h2><i class="bi bi-receipt me-2"></i>Riwayat Penjualan</h2>
</div>

<!-- Stats -->
<div class="stats-row">
    <div class="stat-mini">
        <div class="stat-label">Total Transaksi</div>
        <div class="stat-value">{{ $penjualan->total() }}</div>
    </div>
    <div class="stat-mini">
        <div class="stat-label">Lunas</div>
        <div class="stat-value text-success">
            {{ $penjualan->where('status_pembayaran', 'lunas')->count() }}
        </div>
    </div>
    <div class="stat-mini">
        <div class="stat-label">Belum Lunas</div>
        <div class="stat-value text-warning">
            {{ $penjualan->where('status_pembayaran', '!=', 'lunas')->count() }}
        </div>
    </div>
</div>

<!-- Search -->
<div class="search-card">
    <form action="{{ route('penjualan.index') }}" method="GET" class="d-flex gap-2">
        <input type="text" name="keyword" class="form-control" placeholder="Cari nomor invoice atau nama pelanggan..." value="{{ $keyword ?? '' }}">
        <button type="submit" class="btn btn-search">
            <i class="bi bi-search me-1"></i> Cari
        </button>
        @if($keyword)
        <a href="{{ route('penjualan.index') }}" class="btn btn-outline-secondary" style="border-radius:10px;">
            <i class="bi bi-x-lg"></i>
        </a>
        @endif
    </form>
</div>

<!-- Data Table -->
<div class="data-card">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Invoice</th>
                    <th>Tanggal</th>
                    <th>Pelanggan</th>
                    <th>Kasir</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($penjualan as $item)
                <tr>
                    <td class="text-muted">{{ $loop->iteration + ($penjualan->currentPage() - 1) * $penjualan->perPage() }}</td>
                    <td><span class="invoice-code">{{ $item->nomor_invoice }}</span></td>
                    <td>{{ $item->tanggal_penjualan->format('d/m/Y H:i') }}</td>
                    <td>
                        @if ($item->nama_pelanggan_display === '-')
                            -
                        @else
                            <div class="d-flex align-items-center gap-2">
                                <span style="width:28px;height:28px;border-radius:50%;background:linear-gradient(135deg,#3b82f6,#8b5cf6);color:#fff;display:inline-flex;align-items:center;justify-content:center;font-size:0.7rem;font-weight:700;">
                                    {{ strtoupper(substr($item->nama_pelanggan_display, 0, 1)) }}
                                </span>
                                {{ $item->nama_pelanggan_display }}
                            </div>
                        @endif
                    </td>
                    <td>{{ $item->user->name ?? '-' }}</td>
                    <td class="fw-bold">Rp {{ number_format($item->total_akhir, 0, ',', '.') }}</td>
                    <td>
                        @if ($item->status_pembayaran === 'lunas')
                            <span class="badge-status badge-lunas">Lunas</span>
                        @else
                            <span class="badge-status badge-belum">Belum Lunas</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('penjualan.show', $item->id) }}" class="btn-detail">
                            <i class="bi bi-eye me-1"></i>Detail
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-5">
                        <i class="bi bi-inbox fs-1 text-muted"></i>
                        <p class="text-muted mt-2 mb-0">Belum ada data penjualan</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($penjualan->hasPages())
    <div class="pagination-wrapper">
        {{ $penjualan->appends(request()->query())->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>
@endsection
