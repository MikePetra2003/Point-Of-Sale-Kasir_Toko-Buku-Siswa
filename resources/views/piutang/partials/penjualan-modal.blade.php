<div class="modal fade penjualan-modal" id="{{ $modalId }}" tabindex="-1" aria-labelledby="{{ $modalId }}Label"
    aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title" id="{{ $modalId }}Label">
                        <i class="bi bi-receipt me-2"></i>Detail Penjualan {{ $penjualan->nomor_invoice }}
                    </h5>
                    <div class="modal-subtitle">
                        {{ $penjualan->tanggal_penjualan->format('d/m/Y H:i') }} &bull;
                        {{ $penjualan->nama_pelanggan_display }}
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <div class="modal-info-grid">
                    <div class="modal-info-item">
                        <span>Kasir</span>
                        <strong>{{ $penjualan->user->name ?? '-' }}</strong>
                    </div>
                    <div class="modal-info-item">
                        <span>Pelanggan</span>
                        <strong>{{ $penjualan->nama_pelanggan_display }}</strong>
                    </div>
                    <div class="modal-info-item">
                        <span>Status</span>
                        @if ($penjualan->status_pembayaran === 'lunas')
                            <strong><span class="badge-status badge-lunas">Lunas</span></strong>
                        @else
                            <strong><span class="badge-status badge-belum">Belum Lunas</span></strong>
                        @endif
                    </div>
                    <div class="modal-info-item">
                        <span>Total Akhir</span>
                        <strong>Rp {{ number_format($penjualan->total_akhir, 0, ',', '.') }}</strong>
                    </div>
                </div>

                <h6 class="modal-section-title"><i class="bi bi-bag-check me-2"></i>Barang yang Dibeli</h6>
                <div class="table-responsive">
                    <table class="modal-detail-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Barang</th>
                                <th class="text-center">Qty</th>
                                <th class="text-end">Harga</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($penjualan->detailPenjualan as $detail)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td class="fw-semibold">{{ $detail->barang->nama_barang ?? '-' }}</td>
                                    <td class="text-center">{{ $detail->jumlah }}</td>
                                    <td class="text-end">Rp {{ number_format($detail->harga_jual, 0, ',', '.') }}</td>
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
                    <div><span>Total Harga</span><strong>Rp {{ number_format($penjualan->total_harga, 0, ',', '.') }}</strong></div>
                    @if ($penjualan->diskon > 0)
                        <div><span>Diskon</span><strong class="text-danger">- Rp {{ number_format($penjualan->diskon, 0, ',', '.') }}</strong></div>
                    @endif
                    <div class="modal-summary-total"><span>Total Akhir</span><strong>Rp {{ number_format($penjualan->total_akhir, 0, ',', '.') }}</strong></div>
                </div>

                <h6 class="modal-section-title"><i class="bi bi-credit-card me-2"></i>Rincian Pembayaran</h6>
                <div class="table-responsive">
                    <table class="modal-detail-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Metode</th>
                                <th class="text-end">Jumlah</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($penjualan->pembayaran as $bayar)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $bayar->tanggal_pembayaran->format('d/m/Y H:i') }}</td>
                                    <td><span
                                            class="badge-status badge-lunas">{{ strtoupper($bayar->metode_pembayaran) }}</span>
                                    </td>
                                    <td class="text-end fw-semibold">Rp {{ number_format($bayar->jumlah_bayar, 0, ',', '.') }}</td>
                                    <td>
                                        @if ($bayar->status_pembayaran === 'berhasil')
                                            <span class="badge-status badge-lunas">Berhasil</span>
                                        @else
                                            <span
                                                class="badge-status badge-sebagian">{{ ucfirst($bayar->status_pembayaran) }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-3">Belum ada rincian pembayaran</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>