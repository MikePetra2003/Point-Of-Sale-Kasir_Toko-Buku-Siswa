@extends('layouts.pos')

@section('title', 'Laporan Penjualan - POS Toko Buku')
@section('page-title', 'Laporan Penjualan')

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

    .stat-card {
        background: #ffffff;
        border-radius: 18px;
        border: 1px solid #e2e8f0;
        padding: 26px;
        position: relative;
        overflow: hidden;
        transition: all 0.2s ease;
        box-shadow: 0 1px 3px rgba(0,0,0,0.03);
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 28px rgba(0,0,0,0.06);
    }
    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 4px;
    }
    .stat-card.stat-blue::before { background: linear-gradient(90deg, #3b82f6, #6366f1); }
    .stat-card.stat-green::before { background: linear-gradient(90deg, #10b981, #059669); }
    .stat-card.stat-red::before { background: linear-gradient(90deg, #ef4444, #f43f5e); }
    .stat-card.stat-purple::before { background: linear-gradient(90deg, #8b5cf6, #a855f7); }

    .stat-card .stat-value {
        font-size: 1.8rem;
        font-weight: 800;
        line-height: 1.2;
        color: #0f172a;
    }
    .stat-card .stat-label {
        font-size: 0.78rem;
        font-weight: 600;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        margin-top: 8px;
    }

    .filter-input {
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        padding: 8px 14px;
        font-size: 0.875rem;
        color: #334155;
        transition: all 0.2s;
        background: #f8fafc;
    }
    .filter-input:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        outline: none;
        background: #fff;
    }
    .btn-filter {
        background-color: #3b82f6;
        border-color: #3b82f6;
        font-weight: 600;
        font-size: 0.875rem;
        border-radius: 10px;
        padding: 9px 18px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .btn-filter:hover {
        background-color: #2563eb;
        border-color: #2563eb;
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

    .badge-lunas {
        background-color: rgba(16, 185, 129, 0.1);
        color: #065f46;
        font-weight: 600;
        font-size: 0.75rem;
        padding: 6px 14px;
        border-radius: 20px;
    }
    .badge-sebagian {
        background-color: rgba(245, 158, 11, 0.1);
        color: #92400e;
        font-weight: 600;
        font-size: 0.75rem;
        padding: 6px 14px;
        border-radius: 20px;
    }
    .badge-belum {
        background-color: rgba(239, 68, 68, 0.1);
        color: #991b1b;
        font-weight: 600;
        font-size: 0.75rem;
        padding: 6px 14px;
        border-radius: 20px;
    }

    .terlaris-badge {
        background-color: rgba(59, 130, 246, 0.1);
        color: #1d4ed8;
        font-weight: 700;
        font-size: 0.78rem;
        padding: 5px 12px;
        border-radius: 8px;
    }

    @media print {
        .no-print {
            display: none !important;
        }
        body {
            background: #ffffff;
            color: #000000;
            font-size: 11px;
        }
        .custom-card {
            border: none !important;
            box-shadow: none !important;
        }
        .custom-table th {
            background-color: #f1f5f9 !important;
            color: #000000 !important;
            padding: 8px 10px !important;
        }
        .custom-table td {
            padding: 8px 10px !important;
            border-bottom: 1px solid #cbd5e1 !important;
        }
        .stat-card {
            border: 1px solid #cbd5e1 !important;
            box-shadow: none !important;
            padding: 12px !important;
        }
    }
</style>
@endsection

@section('content')
@php
    $penjualan = $data['penjualan'];
    $tanggalMulai = $data['tanggalMulai'];
    $tanggalAkhir = $data['tanggalAkhir'];
    $totalTransaksi = $data['totalTransaksi'];
    $totalPendapatan = $data['totalPendapatan'];
    $totalLunas = $data['totalLunas'];
    $totalBelumLunas = $data['totalBelumLunas'];
    $barangTerlaris = $data['barangTerlaris'];
    $exportQuery = [
        'tanggal_mulai' => $tanggalMulai,
        'tanggal_akhir' => $tanggalAkhir,
    ];
@endphp

<div class="container-fluid px-0 pb-5">
    
    <!-- Title Section -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-1"><i class="bi bi-file-earmark-bar-graph-fill text-primary me-2"></i>Laporan Penjualan</h3>
            <p class="text-muted small mb-0">Rincian pendapatan penjualan dan pelacakan arus kas masuk toko buku Anda.</p>
        </div>
        
        <!-- Filter Tanggal Card -->
        <div class="d-flex flex-wrap align-items-end gap-3 no-print">
            <a href="{{ route('laporan.penjualan.export.xlsx', $exportQuery) }}" class="btn btn-outline-success btn-sm fw-semibold py-2">
                <i class="bi bi-file-earmark-spreadsheet-fill me-1"></i> Export Excel
            </a>
            <a href="{{ route('laporan.penjualan.export.pdf', $exportQuery) }}" class="btn btn-outline-danger btn-sm fw-semibold py-2">
                <i class="bi bi-file-earmark-pdf-fill me-1"></i> Export PDF
            </a>
            <div class="custom-card p-3 w-100 w-md-auto">
                <form action="{{ route('laporan.penjualan') }}" method="GET" class="row g-2 align-items-end">
                    <div class="col-auto">
                        <label class="form-label text-muted small fw-semibold mb-1">Mulai</label>
                        <input type="date" name="tanggal_mulai" class="form-control form-control-sm filter-input" value="{{ $tanggalMulai }}">
                    </div>
                    <div class="col-auto">
                        <label class="form-label text-muted small fw-semibold mb-1">Akhir</label>
                        <input type="date" name="tanggal_akhir" class="form-control form-control-sm filter-input" value="{{ $tanggalAkhir }}">
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-primary btn-sm btn-filter">
                            <i class="bi bi-funnel-fill"></i> Filter
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Summary Metrics Row -->
    <div class="row g-4 mb-4">
        <!-- Total Transaksi -->
        <div class="col-md-4 col-sm-6">
            <div class="stat-card stat-blue h-100">
                <div class="stat-value">{{ number_format($totalTransaksi) }}</div>
                <div class="stat-label">Total Transaksi</div>
            </div>
        </div>
        <!-- Total Pendapatan -->
        <div class="col-md-4 col-sm-6">
            <div class="stat-card stat-green h-100">
                <div class="stat-value" style="color: #10b981;">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</div>
                <div class="stat-label">Total Pendapatan</div>
            </div>
        </div>
        <!-- Lunas / Belum Lunas -->
        <div class="col-md-4 col-sm-6">
            <div class="stat-card stat-purple h-100">
                <div class="stat-value">
                    <span style="color: #10b981;">{{ $totalLunas }}</span>
                    <small class="text-muted" style="font-size: 1.1rem; font-weight: 500;">/</small>
                    <span class="text-danger">{{ $totalBelumLunas }}</span>
                </div>
                <div class="stat-label">Lunas / Belum Lunas</div>
            </div>
        </div>
    </div>

    <!-- Detail Transaksi Table Card -->
    <div class="custom-card overflow-hidden mb-4">
        <div class="card-header bg-white border-0 px-4 pt-4 pb-2">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-list-ul text-primary fs-5"></i>
                <h5 class="fw-bold text-dark mb-0">Detail Transaksi Penjualan</h5>
            </div>
            <p class="text-muted small mt-1 mb-0">Periode: {{ \Carbon\Carbon::parse($tanggalMulai)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($tanggalAkhir)->format('d/m/Y') }}</p>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table custom-table align-middle">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Invoice</th>
                            <th>Tanggal</th>
                            <th>Pelanggan</th>
                            <th>Kasir</th>
                            <th class="text-end">Total Harga</th>
                            <th class="text-end">Total Akhir</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($penjualan as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td class="fw-bold text-primary">{{ $item->nomor_invoice }}</td>
                            <td>{{ $item->tanggal_penjualan->format('d/m/Y H:i') }}</td>
                            <td>{{ $item->nama_pelanggan_display }}</td>
                            <td>{{ $item->user->name ?? '-' }}</td>
                            <td class="text-end">Rp {{ number_format($item->total_harga, 0, ',', '.') }}</td>
                            <td class="text-end fw-bold text-dark">Rp {{ number_format($item->total_akhir, 0, ',', '.') }}</td>
                            <td>
                                @if ($item->status_pembayaran === 'lunas')
                                    <span class="badge-lunas">Lunas</span>
                                @else
                                    <span class="badge-belum">Belum Lunas</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="bi bi-folder-x fs-3 d-block mb-2"></i>
                                Tidak ada transaksi di periode ini.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if ($penjualan->count() > 0)
                    <tfoot style="background: #fafbfb;">
                        <tr class="fw-bold text-dark">
                            <td colspan="5" class="text-end py-3">TOTAL:</td>
                            <td class="text-end py-3">Rp {{ number_format($penjualan->sum('total_harga'), 0, ',', '.') }}</td>
                            <td class="text-end text-success py-3" style="font-size: 1rem;">Rp {{ number_format($penjualan->sum('total_akhir'), 0, ',', '.') }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>

    <!-- Barang Terlaris Table Card -->
    @if ($barangTerlaris->count() > 0)
    <div class="custom-card overflow-hidden">
        <div class="card-header bg-white border-0 px-4 pt-4 pb-2">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-trophy-fill text-warning fs-5"></i>
                <h5 class="fw-bold text-dark mb-0">Top 10 Barang Terlaris</h5>
            </div>
            <p class="text-muted small mt-1 mb-0">Daftar produk paling diminati berdasarkan total kuantitas terjual.</p>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table custom-table align-middle">
                    <thead>
                        <tr>
                            <th style="width: 80px;">No</th>
                            <th>Nama Barang</th>
                            <th class="text-center" style="width: 200px;">Total Terjual</th>
                            <th class="text-end" style="width: 250px;">Total Pendapatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($barangTerlaris as $item)
                        <tr>
                            <td class="fw-semibold">{{ $loop->iteration }}</td>
                            <td class="fw-semibold text-dark">{{ $item->barang->nama_barang ?? '-' }}</td>
                            <td class="text-center">
                                <span class="terlaris-badge">{{ $item->total_terjual }} pcs</span>
                            </td>
                            <td class="text-end fw-bold text-success">Rp {{ number_format($item->total_pendapatan, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

</div>
@endsection
