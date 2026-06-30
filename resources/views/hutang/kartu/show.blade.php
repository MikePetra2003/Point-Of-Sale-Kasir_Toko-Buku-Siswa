@extends('layouts.pos')

@section('title', 'Detail Kartu Hutang - POS Toko Buku')
@section('page-title', 'Detail Kartu Hutang')

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

        .info-card,
        .data-card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
            margin-bottom: 22px;
        }

        .info-card {
            padding: 24px;
        }

        .info-grid {
            display: grid;
            gap: 18px;
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }

        .info-item .label {
            color: #64748b;
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }

        .info-item .value {
            color: #0f172a;
            font-size: 1rem;
            font-weight: 800;
            margin-top: 6px;
        }

        .data-card {
            overflow: hidden;
        }

        .data-card .table {
            font-size: 0.84rem;
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
            font-size: 0.78rem;
            font-weight: 700;
            gap: 6px;
            padding: 7px 12px;
            text-decoration: none;
        }

        .btn-detail:hover {
            border-color: #2563eb;
            color: #1d4ed8;
        }

        @media (max-width: 991.98px) {
            .info-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 575.98px) {
            .info-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endsection

@section('content')
    <div class="page-header">
        <div>
            <h2><i class="bi bi-journal-check me-2"></i>Kartu Hutang</h2>
            <div class="text-muted mt-1">{{ $supplier->nama_supplier }} - {{ $supplier->no_telepon ?? 'Tanpa kontak' }}</div>
        </div>
        <a href="{{ route('kartu.hutang.index') }}" class="btn btn-outline-secondary" style="border-radius:11px;font-weight:600;">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>

    <div class="info-card">
        <div class="info-grid">
            <div class="info-item">
                <div class="label">Supplier</div>
                <div class="value">{{ $supplier->nama_supplier }}</div>
            </div>
            <div class="info-item">
                <div class="label">Jumlah Transaksi</div>
                <div class="value">{{ $summary['jumlah_transaksi'] }}</div>
            </div>
            <div class="info-item">
                <div class="label">Total Hutang</div>
                <div class="value">Rp {{ number_format($summary['total_hutang'], 0, ',', '.') }}</div>
            </div>
            <div class="info-item">
                <div class="label">Sisa Hutang</div>
                <div class="value {{ $summary['sisa_hutang'] > 0 ? 'text-danger' : 'text-success' }}">
                    Rp {{ number_format($summary['sisa_hutang'], 0, ',', '.') }}
                </div>
            </div>
        </div>
    </div>

    <div class="data-card">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Bukti</th>
                        <th>Keterangan</th>
                        <th class="text-end">Hutang Bertambah</th>
                        <th class="text-end">Pembayaran Pokok</th>
                        <th class="text-end">Bunga</th>
                        <th class="text-end">Saldo</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($mutasi as $item)
                        <tr>
                            <td class="text-muted">{{ $loop->iteration }}</td>
                            <td>{{ $item['tanggal']->format('d/m/Y H:i') }}</td>
                            <td class="fw-semibold">{{ $item['bukti'] }}</td>
                            <td>{{ $item['keterangan'] }}</td>
                            <td class="text-end">
                                {{ $item['hutang'] > 0 ? 'Rp '.number_format($item['hutang'], 0, ',', '.') : '-' }}
                            </td>
                            <td class="text-end text-success fw-semibold">
                                {{ $item['pembayaran'] > 0 ? 'Rp '.number_format($item['pembayaran'], 0, ',', '.') : '-' }}
                            </td>
                            <td class="text-end text-warning fw-semibold">
                                {{ $item['bunga'] > 0 ? 'Rp '.number_format($item['bunga'], 0, ',', '.') : '-' }}
                            </td>
                            <td class="text-end fw-bold {{ $item['saldo'] > 0 ? 'text-danger' : 'text-success' }}">
                                Rp {{ number_format($item['saldo'], 0, ',', '.') }}
                            </td>
                            <td>
                                <a href="{{ route('hutang.show', $item['hutang_id']) }}" class="btn-detail">
                                    <i class="bi bi-eye"></i> Detail
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
