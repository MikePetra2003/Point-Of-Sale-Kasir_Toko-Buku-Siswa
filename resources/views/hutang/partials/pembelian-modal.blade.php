@php
    $hutangSupplier = $pembelian->hutangSupplier;
    $estimasiBunga = $hutangSupplier?->bunga ?? 0;
@endphp

<div class="modal fade penjualan-modal" id="{{ $modalId }}" tabindex="-1" aria-labelledby="{{ $modalId }}Label" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title" id="{{ $modalId }}Label">
                        <i class="bi bi-receipt me-2"></i>Detail Pembelian {{ $pembelian->nomor_faktur }}
                    </h5>
                    <div class="modal-subtitle">
                        {{ $pembelian->tanggal_pembelian->format('d/m/Y H:i') }} &bull;
                        {{ $pembelian->supplier->nama_supplier ?? '-' }}
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>

            <div class="modal-body">
                <div class="modal-info-grid">
                    <div class="modal-info-item">
                        <span>Pencatat</span>
                        <strong>{{ $pembelian->user->name ?? '-' }}</strong>
                    </div>
                    <div class="modal-info-item">
                        <span>Supplier</span>
                        <strong>{{ $pembelian->supplier->nama_supplier ?? '-' }}</strong>
                    </div>
                    <div class="modal-info-item">
                        <span>Status Bayar</span>
                        @if ($pembelian->status_pembayaran === 'lunas')
                            <strong><span class="badge-status badge-lunas">Lunas</span></strong>
                        @else
                            <strong><span class="badge-status badge-belum">Belum Lunas</span></strong>
                        @endif
                    </div>
                    <div class="modal-info-item">
                        <span>Total Harga</span>
                        <strong>Rp {{ number_format($pembelian->total_harga, 0, ',', '.') }}</strong>
                    </div>
                </div>

                <h6 class="modal-section-title"><i class="bi bi-box-seam me-2"></i>Barang yang Dibeli</h6>
                <div class="table-responsive">
                    <table class="modal-detail-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Barang</th>
                                <th class="text-center">Jumlah</th>
                                <th class="text-end">Harga Beli</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($pembelian->detailPembelian as $detail)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td class="fw-semibold">{{ $detail->barang->nama_barang ?? '-' }}</td>
                                    <td class="text-center">
                                        <strong>{{ $detail->jumlah_satuan ?: $detail->jumlah }} {{ $detail->satuan->nama_satuan ?? 'pcs' }}</strong>
                                    </td>
                                    <td class="text-end">Rp {{ number_format($detail->harga_beli, 0, ',', '.') }}</td>
                                    <td class="text-end">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-3">Belum ada detail barang</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="modal-summary">
                    <div>
                        <span>Total Awal</span>
                        <strong>Rp {{ number_format($pembelian->total_harga, 0, ',', '.') }}</strong>
                    </div>
                    @if ($pembelian->diskon > 0)
                        <div>
                            <span>Diskon ({{ number_format($pembelian->diskon_persen, 2, ',', '.') }}%)</span>
                            <strong class="text-danger">- Rp {{ number_format($pembelian->diskon, 0, ',', '.') }}</strong>
                        </div>
                    @endif
                    <div>
                        <span>Total Setelah Diskon</span>
                        <strong>Rp {{ number_format($pembelian->total_akhir, 0, ',', '.') }}</strong>
                    </div>
                    <div>
                        <span>Dibayar Supplier</span>
                        <strong class="text-success">Rp {{ number_format($pembelian->total_dibayar_supplier, 0, ',', '.') }}</strong>
                    </div>
                    <div>
                        <span>Sisa Hutang</span>
                        <strong class="{{ $pembelian->sisa_hutang_supplier > 0 ? 'text-danger' : 'text-success' }}">
                            Rp {{ number_format($pembelian->sisa_hutang_supplier, 0, ',', '.') }}
                        </strong>
                    </div>
                    @if ($estimasiBunga > 0)
                        <div>
                            <span>Estimasi Bunga ({{ \App\Models\HutangSupplier::BUNGA_PERSEN }}%)</span>
                            <strong class="text-warning">Rp {{ number_format($estimasiBunga, 0, ',', '.') }}</strong>
                        </div>
                        <div class="modal-summary-total">
                            <span>Total Jika Terlambat</span>
                            <strong>Rp {{ number_format($pembelian->sisa_hutang_supplier + $estimasiBunga, 0, ',', '.') }}</strong>
                        </div>
                    @else
                        <div class="modal-summary-total">
                            <span>Total Akhir</span>
                            <strong>Rp {{ number_format($pembelian->total_akhir, 0, ',', '.') }}</strong>
                        </div>
                    @endif
                </div>
            </div>


        </div>
    </div>
</div>
