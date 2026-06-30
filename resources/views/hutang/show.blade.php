@extends('layouts.pos')

@section('title', 'Detail Hutang - POS Toko Buku')
@section('page-title', 'Detail Hutang')

@section('styles')
    <style>
        .detail-card {
            background: #fff;
            border-radius: 18px;
            border: 1px solid #e2e8f0;
            padding: 30px;
            margin-bottom: 22px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
        }

        .detail-card h5 {
            font-size: 1rem;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 22px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 26px;
        }

        .info-item {
            margin-bottom: 14px;
        }

        .info-item .label {
            font-size: 0.73rem;
            color: #64748b;
            font-weight: 600;
            text-transform: uppercase;
            margin-bottom: 3px;
            letter-spacing: 0.04em;
        }

        .info-item .value {
            font-size: 0.92rem;
            font-weight: 600;
            color: #0f172a;
        }

        .info-item .value.danger {
            color: #ef4444;
        }

        .info-item .value.success {
            color: #10b981;
        }

        .info-item .value.warning {
            color: #f59e0b;
        }

        .invoice-link {
            color: #2563eb;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border: none;
            background: transparent;
            font-weight: 600;
            padding: 0;
        }

        .invoice-link:hover {
            color: #1d4ed8;
            text-decoration: underline;
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

        .badge-status {
            font-size: 0.75rem;
            font-weight: 700;
            padding: 5px 12px;
            border-radius: 20px;
        }

        .badge-lunas {
            background: #d1fae5;
            color: #065f46;
        }

        .badge-belum {
            background: #fee2e2;
            color: #991b1b;
        }

        .bunga-alert {
            background: rgba(245, 158, 11, 0.1);
            border: 1px solid rgba(245, 158, 11, 0.3);
            border-radius: 14px;
            padding: 18px 22px;
            margin-bottom: 22px;
            color: #92400e;
        }

        .bunga-alert .bunga-row {
            display: flex;
            justify-content: space-between;
            padding: 4px 0;
            font-size: 0.9rem;
        }

        .bunga-alert .bunga-total {
            border-top: 1px dashed rgba(146, 64, 14, 0.3);
            margin-top: 8px;
            padding-top: 10px;
            font-weight: 800;
            font-size: 1.05rem;
        }

        .history-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.85rem;
        }

        .history-table thead th {
            background: #f9fafb;
            padding: 12px 16px;
            font-weight: 700;
            color: #6b7280;
            text-transform: uppercase;
            font-size: 0.72rem;
            letter-spacing: 0.04em;
            border-bottom: 2px solid #e5e7eb;
        }

        .history-table tbody td {
            padding: 12px 16px;
            border-bottom: 1px solid #f3f4f6;
            color: #374151;
        }

        .history-table tbody tr:last-child td {
            border-bottom: none;
        }

        .history-table tbody tr:hover {
            background: #f9fafb;
        }

        .payment-form {
            background: #fff;
            border-radius: 18px;
            border: 2px solid #3b82f6;
            padding: 30px;
            box-shadow: 0 4px 16px rgba(59, 130, 246, 0.08);
        }

        .payment-form h5 {
            font-size: 1rem;
            font-weight: 700;
            color: #3b82f6;
            margin-bottom: 22px;
        }

        .payment-form .form-label {
            font-size: 0.8rem;
            font-weight: 600;
            color: #475569;
        }

        .payment-form .form-control,
        .payment-form .form-select {
            border-radius: 11px;
            border: 1px solid #e2e8f0;
            padding: 11px 15px;
            font-size: 0.875rem;
            background: #f8fafc;
        }

        .payment-form .form-control:focus,
        .payment-form .form-select:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
            background: #fff;
        }

        .btn-pay {
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            border: none;
            color: #fff;
            font-weight: 600;
            padding: 11px 26px;
            border-radius: 11px;
            font-size: 0.875rem;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.2);
        }

        .btn-pay:hover {
            background: #2563eb;
            color: #fff;
            transform: translateY(-1px);
        }

        .action-bar {
            display: flex;
            gap: 10px;
            margin-bottom: 22px;
        }

        .btn-action-bar {
            padding: 9px 18px;
            border-radius: 11px;
            font-size: 0.85rem;
            font-weight: 600;
            border: 1px solid #e2e8f0;
            background: #fff;
            color: #334155;
            text-decoration: none;
            transition: all 0.2s;
        }

        .btn-action-bar:hover {
            background: #f8fafc;
            border-color: #3b82f6;
            color: #3b82f6;
            transform: translateY(-1px);
        }

        .btn-action-bar.btn-warning-custom {
            color: #d97706;
            border-color: #f59e0b;
        }
        .btn-action-bar.btn-warning-custom:hover {
            background: #fffbeb;
            border-color: #d97706;
            color: #b45309;
        }

        .btn-action-bar.btn-danger-custom {
            color: #dc2626;
            border-color: #f87171;
            background: #fff;
            cursor: pointer;
        }
        .btn-action-bar.btn-danger-custom:hover {
            background: #fef2f2;
            border-color: #dc2626;
            color: #b91c1c;
        }

        .alert-pos {
            border-radius: 14px;
            border: none;
            padding: 15px 22px;
            font-size: 0.875rem;
        }

        @media (max-width: 767.98px) {
            .info-grid {
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

    <!-- Action Bar -->
    <div class="action-bar d-flex justify-content-between align-items-center flex-wrap gap-2">
        <a href="{{ route('hutang.index') }}" class="btn-action-bar">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
        <div class="d-flex gap-2">
            <a href="{{ route('hutang.edit', $hutang->id) }}" class="btn-action-bar btn-warning-custom">
                <i class="bi bi-pencil-square me-1"></i> Edit Hutang
            </a>
            <form action="{{ route('hutang.destroy', $hutang->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data hutang ini? Semua riwayat pelunasan terkait juga akan terhapus.')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-action-bar btn-danger-custom">
                    <i class="bi bi-trash3 me-1"></i> Hapus Hutang
                </button>
            </form>
        </div>
    </div>

    <!-- Detail Info -->
    <div class="detail-card">
        <h5><i class="bi bi-cash-stack"></i> Informasi Hutang</h5>

        <div class="info-grid">
            <div>
                <div class="info-item">
                    <div class="label">No. Faktur</div>
                    <div class="value">
                        @if ($hutang->pembelian)
                            <button type="button" class="invoice-link" data-bs-toggle="modal"
                                data-bs-target="#pembelianModalHutang{{ $hutang->id }}"
                                title="Lihat detail pembelian {{ $hutang->pembelian->nomor_faktur }}">
                                <i class="bi bi-receipt"></i>{{ $hutang->pembelian->nomor_faktur }}
                            </button>
                        @else
                            -
                        @endif
                    </div>
                </div>
                <div class="info-item">
                    <div class="label">Supplier</div>
                    <div class="value">{{ $hutang->supplier->nama_supplier ?? '-' }}</div>
                </div>
                <div class="info-item">
                    <div class="label">No. Telepon</div>
                    <div class="value">{{ $hutang->supplier->no_telepon ?? '-' }}</div>
                </div>
                <div class="info-item">
                    <div class="label">Jatuh Tempo</div>
                    <div class="value {{ $hutang->is_overdue ? 'danger' : '' }}">
                        {{ $hutang->tanggal_jatuh_tempo ? $hutang->tanggal_jatuh_tempo->format('d/m/Y') : '-' }}
                        @if($hutang->is_overdue)
                            <i class="bi bi-exclamation-circle-fill ms-1"></i> Lewat jatuh tempo
                        @endif
                    </div>
                </div>
            </div>
            <div>
                <div class="info-item">
                    <div class="label">Total Hutang</div>
                    <div class="value">Rp {{ number_format($hutang->total_hutang, 0, ',', '.') }}</div>
                </div>
                <div class="info-item">
                    <div class="label">Total Dibayar</div>
                    <div class="value success">Rp {{ number_format($hutang->total_dibayar, 0, ',', '.') }}</div>
                </div>
                <div class="info-item">
                    <div class="label">Sisa Hutang</div>
                    <div class="value {{ $hutang->sisa_hutang > 0 ? 'danger' : 'success' }}">
                        Rp {{ number_format($hutang->sisa_hutang, 0, ',', '.') }}
                    </div>
                </div>
                <div class="info-item">
                    <div class="label">Status</div>
                    <div class="value">
                        @if ($hutang->status === 'lunas')
                            <span class="badge-status badge-lunas">Lunas</span>
                        @else
                            <span class="badge-status badge-belum">Belum Lunas</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bunga keterlambatan -->
    @if ($hutang->bunga > 0)
        <div class="bunga-alert">
            <div class="fw-bold mb-2"><i class="bi bi-exclamation-triangle-fill me-2"></i>Hutang melewati jatuh tempo —
                dikenakan bunga {{ \App\Models\HutangSupplier::BUNGA_PERSEN }}%</div>
            <div class="bunga-row"><span>Sisa Hutang</span><span>Rp
                    {{ number_format($hutang->sisa_hutang, 0, ',', '.') }}</span></div>
            <div class="bunga-row"><span>Bunga ({{ \App\Models\HutangSupplier::BUNGA_PERSEN }}%)</span><span>Rp
                    {{ number_format($hutang->bunga, 0, ',', '.') }}</span></div>
            <div class="bunga-row bunga-total"><span>Total Harus Dibayar</span><span>Rp
                    {{ number_format($hutang->total_harus_bayar, 0, ',', '.') }}</span></div>
        </div>
    @endif

    <!-- Riwayat Pembayaran -->
    <div class="detail-card">
        <h5><i class="bi bi-clock-history"></i> Riwayat Pembayaran</h5>

        @if ($hutang->pembayaranHutang->count() > 0)
            <table class="history-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Metode</th>
                        <th style="text-align:right;">Bunga</th>
                        <th style="text-align:right;">Jumlah</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($hutang->pembayaranHutang as $bayar)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $bayar->tanggal_bayar->format('d/m/Y H:i') }}</td>
                            <td><span class="badge-status badge-lunas">{{ strtoupper($bayar->metode_pembayaran) }}</span></td>
                            <td style="text-align:right;">Rp {{ number_format($bayar->bunga, 0, ',', '.') }}</td>
                            <td style="text-align:right;font-weight:600;">Rp {{ number_format($bayar->jumlah_bayar, 0, ',', '.') }}
                            </td>
                            <td class="text-muted">{{ $bayar->keterangan ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="text-center py-4 text-muted">
                <i class="bi bi-inbox fs-2"></i>
                <p class="mt-2 mb-0">Belum ada pembayaran</p>
            </div>
        @endif
    </div>

    <!-- Form Bayar (jika belum lunas) -->
    @if ($hutang->status !== 'lunas')
        <div class="payment-form">
            <h5><i class="bi bi-cash-coin me-2"></i>Bayar Hutang</h5>

            <p class="text-muted small mb-3">
                Sisa hutang:
                <strong class="text-danger fs-6">Rp {{ number_format($hutang->sisa_hutang, 0, ',', '.') }}</strong>
                @if ($hutang->bunga > 0)
                    <span class="text-warning">(+ bunga keterlambatan Rp {{ number_format($hutang->bunga, 0, ',', '.') }},
                        total maks. bayar Rp {{ number_format($hutang->total_harus_bayar, 0, ',', '.') }})</span>
                @endif
            </p>

            <form method="POST" action="{{ route('hutang.bayar', $hutang->id) }}">
                @csrf
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Jumlah Bayar (Rp) *</label>
                        <input type="number" name="jumlah_bayar" class="form-control" required min="1"
                            max="{{ $hutang->total_harus_bayar }}"
                            value="{{ old('jumlah_bayar') }}"
                            placeholder="Maks: {{ number_format($hutang->total_harus_bayar, 0, ',', '.') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Metode *</label>
                        <select name="metode_pembayaran" class="form-select" required>
                            <option value="tunai" {{ old('metode_pembayaran', 'tunai') === 'tunai' ? 'selected' : '' }}>Tunai</option>
                            <option value="qris" {{ old('metode_pembayaran') === 'qris' ? 'selected' : '' }}>QRIS</option>
                            <option value="transfer" {{ old('metode_pembayaran') === 'transfer' ? 'selected' : '' }}>Transfer</option>
                        </select>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label">Keterangan</label>
                        <input type="text" name="keterangan" class="form-control" value="{{ old('keterangan') }}" placeholder="Opsional...">
                    </div>
                </div>
                <div class="mt-3">
                    <button type="submit" class="btn btn-pay" onclick="return confirm('Simpan pembayaran hutang ini?')">
                        <i class="bi bi-check-circle me-1"></i> Simpan Pembayaran
                    </button>
                </div>
            </form>
        </div>
    @endif

    @if ($hutang->pembelian)
        @include('hutang.partials.pembelian-modal', [
            'pembelian' => $hutang->pembelian,
            'modalId' => 'pembelianModalHutang' . $hutang->id,
        ])
    @endif
@endsection
