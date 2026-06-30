@extends('layouts.pos')

@section('title', 'Kartu Piutang - POS Toko Buku')
@section('page-title', 'Kartu Piutang')

@section('styles')
    <style>
        .page-header {
            align-items: center;
            display: flex;
            flex-wrap: wrap;
            gap: 16px;
            justify-content: space-between;
            margin-bottom: 24px;
        }

        .page-header h2 {
            color: #0f172a;
            font-size: 1.5rem;
            font-weight: 800;
            margin: 0;
        }

        .summary-grid {
            display: grid;
            gap: 16px;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            margin-bottom: 22px;
        }

        .summary-card,
        .filter-card,
        .data-card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
        }

        .summary-card {
            padding: 20px 22px;
        }

        .summary-card .label {
            color: #64748b;
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }

        .summary-card .value {
            color: #0f172a;
            font-size: 1.25rem;
            font-weight: 800;
            margin-top: 8px;
        }

        .filter-card {
            margin-bottom: 22px;
            padding: 18px 22px;
        }

        .filter-card .form-control,
        .filter-card .form-select {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 11px;
            font-size: 0.875rem;
            padding: 11px 16px;
        }

        .data-card {
            overflow: hidden;
        }

        .data-card .table {
            font-size: 0.85rem;
            margin-bottom: 0;
        }

        .data-card thead th {
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            color: #64748b;
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.05em;
            padding: 14px 16px;
            text-transform: uppercase;
        }

        .data-card tbody td {
            border-bottom: 1px solid #f1f5f9;
            color: #334155;
            padding: 14px 16px;
            vertical-align: middle;
        }

        .btn-detail {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            color: #2563eb;
            display: inline-flex;
            font-size: 0.8rem;
            font-weight: 700;
            gap: 6px;
            padding: 8px 14px;
            text-decoration: none;
        }

        .btn-detail:hover {
            border-color: #2563eb;
            color: #1d4ed8;
        }

        .pagination-wrapper {
            border-top: 1px solid #f1f5f9;
            padding: 16px 20px;
        }

        @media (max-width: 991.98px) {
            .summary-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 575.98px) {
            .summary-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endsection

@section('content')
    <div class="page-header">
        <h2><i class="bi bi-journal-text me-2"></i>Kartu Piutang</h2>
        <a href="{{ route('piutang.index') }}" class="btn btn-outline-secondary" style="border-radius:11px;font-weight:600;">
            <i class="bi bi-arrow-left me-1"></i> Daftar Piutang
        </a>
    </div>

    <div class="summary-grid">
        <div class="summary-card">
            <div class="label">Pelanggan Berpiutang</div>
            <div class="value">{{ $stats['pelanggan'] }}</div>
        </div>
        <div class="summary-card">
            <div class="label">Total Piutang</div>
            <div class="value">Rp {{ number_format($stats['total_piutang'], 0, ',', '.') }}</div>
        </div>
        <div class="summary-card">
            <div class="label">Total Dibayar</div>
            <div class="value text-success">Rp {{ number_format($stats['total_dibayar'], 0, ',', '.') }}</div>
        </div>
        <div class="summary-card">
            <div class="label">Sisa Piutang</div>
            <div class="value text-danger">Rp {{ number_format($stats['sisa_piutang'], 0, ',', '.') }}</div>
        </div>
    </div>

    <div class="filter-card">
        <form action="{{ route('kartu.piutang.index') }}" method="GET" class="d-flex gap-2 flex-wrap">
            <input type="text" name="keyword" class="form-control" style="flex:1;min-width:220px;"
                placeholder="Cari nama, ID, atau telepon pelanggan..." value="{{ $keyword ?? '' }}">
            <select name="status" class="form-select" style="max-width:190px;">
                <option value="semua" {{ ($status ?? 'semua') === 'semua' ? 'selected' : '' }}>Semua Status</option>
                <option value="belum_lunas" {{ ($status ?? '') === 'belum_lunas' ? 'selected' : '' }}>Belum Lunas</option>
                <option value="sebagian" {{ ($status ?? '') === 'sebagian' ? 'selected' : '' }}>Sebagian</option>
                <option value="lunas" {{ ($status ?? '') === 'lunas' ? 'selected' : '' }}>Lunas</option>
            </select>
            <button type="submit" class="btn btn-primary" style="border-radius:11px;font-weight:600;">
                <i class="bi bi-search me-1"></i> Cari
            </button>
            @if($keyword || ($status ?? 'semua') !== 'semua')
                <a href="{{ route('kartu.piutang.index') }}" class="btn btn-outline-secondary" style="border-radius:11px;">
                    <i class="bi bi-x-lg me-1"></i> Reset
                </a>
            @endif
        </form>
    </div>

    <div class="data-card">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>ID Pelanggan</th>
                        <th>Pelanggan</th>
                        <th>Transaksi</th>
                        <th>Total Piutang</th>
                        <th>Dibayar</th>
                        <th>Sisa</th>
                        <th>Jatuh Tempo Terakhir</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($kartuPiutang as $item)
                        <tr>
                            <td class="text-muted">{{ $loop->iteration + ($kartuPiutang->currentPage() - 1) * $kartuPiutang->perPage() }}</td>
                            <td class="fw-semibold">{{ $item->pelanggan->no_id_pelanggan ?? '-' }}</td>
                            <td>{{ $item->pelanggan->nama_pelanggan_display ?? '-' }}</td>
                            <td>{{ $item->jumlah_transaksi }}</td>
                            <td>Rp {{ number_format($item->total_piutang, 0, ',', '.') }}</td>
                            <td class="text-success fw-semibold">Rp {{ number_format($item->total_dibayar, 0, ',', '.') }}</td>
                            <td class="{{ $item->sisa_piutang > 0 ? 'text-danger' : 'text-success' }} fw-bold">
                                Rp {{ number_format($item->sisa_piutang, 0, ',', '.') }}
                            </td>
                            <td>
                                {{ $item->jatuh_tempo_terakhir ? \Carbon\Carbon::parse($item->jatuh_tempo_terakhir)->format('d/m/Y') : '-' }}
                            </td>
                            <td>
                                <a href="{{ route('kartu.piutang.show', $item->pelanggan_id) }}" class="btn-detail">
                                    <i class="bi bi-list-ul"></i> Mutasi
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-5 text-center text-muted">
                                <i class="bi bi-journal-x fs-1"></i>
                                <p class="mb-0 mt-2">Belum ada data kartu piutang</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($kartuPiutang->hasPages())
            <div class="pagination-wrapper">
                {{ $kartuPiutang->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
@endsection
