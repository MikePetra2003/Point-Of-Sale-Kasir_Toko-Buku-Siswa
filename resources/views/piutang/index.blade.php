@extends('layouts.pos')

@section('title', 'Piutang Pelanggan - POS Toko Buku')
@section('page-title', 'Piutang Pelanggan')

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

        .stats-row {
            display: flex;
            gap: 16px;
            margin-bottom: 26px;
            flex-wrap: wrap;
        }

        .stat-card-mini {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 22px 26px;
            flex: 1;
            min-width: 180px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
            transition: all 0.2s;
        }

        .stat-card-mini:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
        }

        .stat-card-mini::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 60px;
            height: 60px;
            border-bottom-left-radius: 40px;
            opacity: 0.08;
        }

        .stat-card-mini.danger::before {
            background: #ef4444;
        }

        .stat-card-mini.warning::before {
            background: #f59e0b;
        }

        .stat-card-mini.success::before {
            background: #10b981;
        }

        .stat-card-mini .stat-label {
            font-size: 0.73rem;
            color: #64748b;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .stat-card-mini .stat-value {
            font-size: 1.5rem;
            font-weight: 800;
            margin-top: 8px;
        }

        .stat-card-mini .stat-value.text-danger {
            color: #ef4444 !important;
        }

        .stat-card-mini .stat-value.text-warning {
            color: #f59e0b !important;
        }

        .stat-card-mini .stat-value.text-success {
            color: #10b981 !important;
        }

        .stat-card-mini .stat-desc {
            font-size: 0.76rem;
            color: #94a3b8;
            margin-top: 5px;
        }

        .filter-card {
            background: #fff;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            padding: 18px 22px;
            margin-bottom: 22px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
        }

        .filter-card .form-control,
        .filter-card .form-select {
            border-radius: 11px;
            border: 1px solid #e2e8f0;
            padding: 11px 16px;
            font-size: 0.875rem;
            background: #f8fafc;
        }

        .filter-card .form-control:focus,
        .filter-card .form-select:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
            background: #fff;
        }

        .filter-card .btn-search {
            background: #3b82f6;
            border: none;
            color: #fff;
            border-radius: 11px;
            padding: 11px 22px;
            font-weight: 600;
            font-size: 0.875rem;
        }

        .filter-card .btn-search:hover {
            background: #2563eb;
            color: #fff;
        }

        .data-card {
            background: #fff;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
        }

        .data-card .table {
            margin-bottom: 0;
            font-size: 0.85rem;
        }

        .data-card .table thead th {
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            font-weight: 600;
            color: #64748b;
            text-transform: uppercase;
            font-size: 0.73rem;
            letter-spacing: 0.06em;
            padding: 15px 18px;
        }

        .data-card .table tbody td {
            padding: 15px 18px;
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9;
            color: #334155;
        }

        .data-card .table tbody tr:last-child td {
            border-bottom: none;
        }

        .data-card .table tbody tr:hover {
            background: #f8fafc;
        }

        .badge-status {
            font-size: 0.73rem;
            font-weight: 600;
            padding: 6px 14px;
            border-radius: 20px;
        }

        .badge-lunas {
            background: rgba(16, 185, 129, 0.1);
            color: #065f46;
        }

        .badge-belum {
            background: rgba(239, 68, 68, 0.1);
            color: #991b1b;
        }

        .badge-sebagian {
            background: rgba(245, 158, 11, 0.1);
            color: #92400e;
        }

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

        .invoice-link {
            font-weight: 700;
            font-family: 'Courier New', monospace;
            font-size: 0.8rem;
            color: #2563eb;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border: none;
            background: transparent;
            padding: 0;
        }

        .invoice-link:hover {
            color: #1d4ed8;
            text-decoration: underline;
        }

        .invoice-missing {
            font-weight: 700;
            font-family: 'Courier New', monospace;
            font-size: 0.8rem;
            color: #9ca3af;
        }

        .progress-piutang {
            height: 6px;
            border-radius: 4px;
            background: #f3f4f6;
            margin-top: 6px;
            overflow: hidden;
        }

        .progress-piutang .bar {
            height: 100%;
            border-radius: 4px;
            background: linear-gradient(90deg, #10b981, #059669);
            transition: width 0.3s;
        }

        .jatuh-tempo-warning {
            color: #ef4444;
            font-weight: 600;
        }

        .jatuh-tempo-ok {
            color: #6b7280;
        }

        .pagination-wrapper {
            padding: 16px 20px;
            border-top: 1px solid #f3f4f6;
        }

        .alert-pos {
            border-radius: 12px;
            border: none;
            padding: 14px 20px;
            font-size: 0.88rem;
            font-weight: 500;
        }

        .penjualan-modal .modal-content {
            border: none;
            border-radius: 14px;
        }

        .penjualan-modal .modal-header {
            border-bottom: 1px solid #e5e7eb;
            padding: 20px 24px;
        }

        .penjualan-modal .modal-title {
            color: #1f2937;
            font-size: 1rem;
            font-weight: 800;
        }

        .penjualan-modal .modal-subtitle {
            color: #6b7280;
            font-size: 0.8rem;
            margin-top: 4px;
        }

        .modal-info-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 12px;
            margin-bottom: 20px;
        }

        .modal-info-item {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 12px 14px;
        }

        .modal-info-item span {
            color: #6b7280;
            display: block;
            font-size: 0.72rem;
            font-weight: 700;
            margin-bottom: 4px;
            text-transform: uppercase;
        }

        .modal-info-item strong {
            color: #1f2937;
            font-size: 0.88rem;
        }

        .modal-section-title {
            color: #1f2937;
            font-size: 0.92rem;
            font-weight: 800;
            margin: 18px 0 10px;
        }

        .modal-detail-table {
            border-collapse: collapse;
            font-size: 0.84rem;
            width: 100%;
        }

        .modal-detail-table thead th {
            background: #f9fafb;
            border-bottom: 2px solid #e5e7eb;
            color: #6b7280;
            font-size: 0.72rem;
            font-weight: 700;
            padding: 10px 12px;
            text-transform: uppercase;
        }

        .modal-detail-table tbody td {
            border-bottom: 1px solid #f3f4f6;
            color: #374151;
            padding: 10px 12px;
        }

        .modal-summary {
            border-top: 1px solid #e5e7eb;
            margin: 14px 0 4px auto;
            max-width: 320px;
            padding-top: 10px;
        }

        .modal-summary div {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
        }

        .modal-summary span {
            color: #6b7280;
        }

        .modal-summary-total {
            border-top: 2px solid #e5e7eb;
            margin-top: 6px;
            padding-top: 10px !important;
        }

        .modal-summary-total strong {
            color: #10b981;
            font-size: 1.05rem;
        }

        @media (max-width: 767.98px) {
            .modal-info-grid {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (max-width: 575.98px) {
            .modal-info-grid {
                grid-template-columns: 1fr;
            }
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
    @if (session('error'))
        <div class="alert alert-danger alert-pos alert-dismissible fade show">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Page Header -->
    <div class="page-header">
        <h2><i class="bi bi-wallet2 me-2"></i>Piutang Pelanggan</h2>
    </div>

    <!-- Stats -->
    <div class="stats-row">
        <div class="stat-card-mini danger">
            <div class="stat-label">Total Piutang Aktif</div>
            <div class="stat-value text-danger">{{ $piutang->where('status', '!=', 'lunas')->count() }}</div>
            <div class="stat-desc">Tagihan belum lunas</div>
        </div>
        <div class="stat-card-mini warning">
            <div class="stat-label">Total Nilai Piutang</div>
            <div class="stat-value text-warning">Rp
                {{ number_format($piutang->where('status', '!=', 'lunas')->sum('sisa_piutang'), 0, ',', '.') }}</div>
            <div class="stat-desc">Sisa yang harus dibayar</div>
        </div>
        <div class="stat-card-mini success">
            <div class="stat-label">Sudah Lunas</div>
            <div class="stat-value text-success">{{ $piutang->where('status', 'lunas')->count() }}</div>
            <div class="stat-desc">Tagihan selesai</div>
        </div>
    </div>

    <!-- Filter -->
    <div class="filter-card">
        <form action="{{ route('piutang.index') }}" method="GET" class="d-flex gap-2 flex-wrap">
            <input type="text" name="keyword" class="form-control" style="flex:1;min-width:200px;"
                placeholder="Cari nama pelanggan atau invoice..." value="{{ $keyword ?? '' }}">
            <select name="status" class="form-select" style="max-width:180px;">
                <option value="">Semua Status</option>
                <option value="belum_lunas" {{ ($status ?? '') == 'belum_lunas' ? 'selected' : '' }}>Belum Lunas</option>
                <option value="lunas" {{ ($status ?? '') == 'lunas' ? 'selected' : '' }}>Lunas</option>
            </select>
            <button type="submit" class="btn btn-search">
                <i class="bi bi-search me-1"></i> Cari
            </button>
            @if($keyword || $status)
                <a href="{{ route('piutang.index') }}" class="btn btn-outline-secondary" style="border-radius:10px;">
                    <i class="bi bi-x-lg"></i> Reset
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
                        <th>Pelanggan</th>
                        <th>Total Piutang</th>
                        <th>Progress</th>
                        <th>Sisa</th>
                        <th>Jatuh Tempo</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($piutang as $item)
                        @php
                            $persen = $item->total_piutang > 0 ? ($item->total_dibayar / $item->total_piutang) * 100 : 0;
                            $isOverdue = $item->tanggal_jatuh_tempo && $item->tanggal_jatuh_tempo->isPast() && $item->status !== 'lunas';
                        @endphp
                        <tr>
                            <td class="text-muted">{{ $loop->iteration + ($piutang->currentPage() - 1) * $piutang->perPage() }}
                            </td>
                            <td>
                                @if ($item->penjualan)
                                    <button type="button" class="invoice-link" data-bs-toggle="modal"
                                        data-bs-target="#penjualanModal{{ $item->penjualan->id }}"
                                        title="Lihat detail penjualan {{ $item->penjualan->nomor_invoice }}">
                                        <i class="bi bi-receipt"></i>{{ $item->penjualan->nomor_invoice }}
                                    </button>
                                @else
                                    <span class="invoice-missing">-</span>
                                @endif
                            </td>
                            <td>
                                @if (($item->pelanggan->nama_pelanggan_display ?? '-') === '-')
                                    -
                                @else
                                    <div class="d-flex align-items-center gap-2">
                                        <span
                                            style="width:28px;height:28px;border-radius:50%;background:linear-gradient(135deg,#f97316,#ea580c);color:#fff;display:inline-flex;align-items:center;justify-content:center;font-size:0.7rem;font-weight:700;">
                                            {{ strtoupper(substr($item->pelanggan->nama_pelanggan_display, 0, 1)) }}
                                        </span>
                                        <span class="fw-semibold">{{ $item->pelanggan->nama_pelanggan_display }}</span>
                                    </div>
                                @endif
                            </td>
                            <td>Rp {{ number_format($item->total_piutang, 0, ',', '.') }}</td>
                            <td style="min-width:120px;">
                                <div style="font-size:0.72rem;color:#6b7280;">{{ number_format($persen, 0) }}% dibayar</div>
                                <div class="progress-piutang">
                                    <div class="bar" style="width:{{ $persen }}%"></div>
                                </div>
                            </td>
                            <td class="fw-bold {{ $item->sisa_piutang > 0 ? 'text-danger' : 'text-success' }}">
                                Rp {{ number_format($item->sisa_piutang, 0, ',', '.') }}
                            </td>
                            <td>
                                @if($item->tanggal_jatuh_tempo)
                                    <span class="{{ $isOverdue ? 'jatuh-tempo-warning' : 'jatuh-tempo-ok' }}">
                                        {{ $item->tanggal_jatuh_tempo->format('d/m/Y') }}
                                        @if($isOverdue)
                                            <i class="bi bi-exclamation-circle-fill ms-1"></i>
                                        @endif
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if ($item->status === 'lunas')
                                    <span class="badge-status badge-lunas">Lunas</span>
                                @else
                                    <span class="badge-status badge-belum">Belum Lunas</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('piutang.show', $item->id) }}" class="btn-detail">
                                    <i class="bi bi-eye me-1"></i>Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                <i class="bi bi-wallet2 fs-1 text-muted"></i>
                                <p class="text-muted mt-2 mb-0">Belum ada data piutang</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($piutang->hasPages())
            <div class="pagination-wrapper">
                {{ $piutang->appends(request()->query())->links() }}
            </div>
        @endif
    </div>

    @foreach ($piutang as $item)
        @if ($item->penjualan)
            @include('piutang.partials.penjualan-modal', [
                'penjualan' => $item->penjualan,
                'modalId' => 'penjualanModal' . $item->penjualan->id,
            ])
        @endif
    @endforeach
@endsection
