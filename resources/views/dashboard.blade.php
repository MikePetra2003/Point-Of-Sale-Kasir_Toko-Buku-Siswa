@extends('layouts.pos')

@section('title', 'Dashboard - POS Toko Buku')
@section('page-title', 'Dashboard')

@section('styles')
<style>
    .welcome-banner {
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
        border: 1px solid #e2e8f0;
        position: relative;
        overflow: hidden;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .welcome-banner:hover {
        box-shadow: 0 12px 28px rgba(0, 0, 0, 0.06);
    }
    .welcome-banner::before {
        content: '';
        position: absolute;
        width: 400px;
        height: 400px;
        background: radial-gradient(circle, rgba(59, 130, 246, 0.06) 0%, transparent 70%);
        top: -200px;
        right: -100px;
        border-radius: 50%;
        pointer-events: none;
    }
    .welcome-banner::after {
        content: '';
        position: absolute;
        width: 300px;
        height: 300px;
        background: radial-gradient(circle, rgba(16, 185, 129, 0.05) 0%, transparent 70%);
        bottom: -150px;
        left: -50px;
        border-radius: 50%;
        pointer-events: none;
    }

    .kpi-card {
        background: #ffffff;
        border-radius: 18px;
        border: 1px solid #e2e8f0;
        padding: 26px;
        position: relative;
        overflow: hidden;
        transition: all 0.3s var(--transition-smooth, cubic-bezier(0.4, 0, 0.2, 1));
        display: flex;
        align-items: center;
        justify-content: space-between;
        box-shadow: 0 1px 3px rgba(0,0,0,0.03);
    }
    .kpi-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.08);
        border-color: transparent;
    }
    .kpi-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 4px;
    }
    .kpi-card.kpi-teal::before { background: linear-gradient(90deg, #0ea5e9, #14b8a6); }
    .kpi-card.kpi-blue::before { background: linear-gradient(90deg, #6366f1, #3b82f6); }
    .kpi-card.kpi-purple::before { background: linear-gradient(90deg, #a855f7, #7c3aed); }
    .kpi-card.kpi-orange::before { background: linear-gradient(90deg, #f59e0b, #ea580c); }

    .kpi-info h6 {
        font-size: 0.78rem;
        font-weight: 600;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        margin-bottom: 10px;
    }
    .kpi-info .kpi-value {
        font-size: 1.75rem;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.2;
    }
    .kpi-icon-wrapper {
        width: 58px;
        height: 58px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.6rem;
        transition: all 0.3s ease;
    }
    .kpi-teal .kpi-icon-wrapper { background: rgba(14, 165, 233, 0.1); color: #0284c7; }
    .kpi-blue .kpi-icon-wrapper { background: rgba(99, 102, 241, 0.1); color: #4f46e5; }
    .kpi-purple .kpi-icon-wrapper { background: rgba(168, 85, 247, 0.1); color: #7c3aed; }
    .kpi-orange .kpi-icon-wrapper { background: rgba(245, 158, 11, 0.1); color: #d97706; }

    .kpi-card:hover .kpi-icon-wrapper {
        transform: scale(1.12) rotate(8deg);
    }

    .revenue-card {
        border-radius: 20px;
        padding: 30px 32px;
        color: #ffffff;
        position: relative;
        overflow: hidden;
        transition: all 0.3s ease;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .revenue-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 16px 32px rgba(0, 0, 0, 0.15);
    }
    .revenue-card.bg-income {
        background: linear-gradient(135deg, #10b981 0%, #059669 50%, #047857 100%);
    }
    .revenue-card.bg-profit {
        background: linear-gradient(135deg, #6366f1 0%, #4f46e5 50%, #4338ca 100%);
    }
    .revenue-card::before {
        content: '';
        position: absolute;
        width: 200px;
        height: 200px;
        background: rgba(255,255,255,0.05);
        border-radius: 50%;
        top: -80px;
        right: -40px;
    }
    .revenue-info h6 {
        font-size: 0.82rem;
        font-weight: 500;
        opacity: 0.9;
        margin-bottom: 8px;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    .revenue-info .revenue-value {
        font-size: 1.9rem;
        font-weight: 800;
        line-height: 1.2;
    }
    .revenue-info .revenue-label {
        font-size: 0.78rem;
        opacity: 0.75;
        margin-top: 8px;
    }
    .revenue-icon-wrapper {
        width: 64px;
        height: 64px;
        border-radius: 16px;
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(8px);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.8rem;
        transition: transform 0.3s ease;
    }
    .revenue-card:hover .revenue-icon-wrapper {
        transform: scale(1.12) rotate(-6deg);
    }

    .financial-card {
        background: #ffffff;
        border-radius: 18px;
        border: 1px solid #e2e8f0;
        padding: 26px;
        position: relative;
        transition: all 0.3s ease;
        box-shadow: 0 1px 3px rgba(0,0,0,0.03);
    }
    .financial-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px rgba(0,0,0,0.06);
    }
    .financial-card::after {
        content: '';
        position: absolute;
        left: 0;
        top: 20%;
        height: 60%;
        width: 4px;
        border-radius: 0 4px 4px 0;
    }
    .financial-card.piutang::after { background: linear-gradient(180deg, #ef4444, #dc2626); }
    .financial-card.hutang::after { background: linear-gradient(180deg, #f59e0b, #d97706); }
    .financial-card.stok-rendah::after { background: linear-gradient(180deg, #10b981, #059669); }

    .financial-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 18px;
    }
    .financial-header i {
        font-size: 1.4rem;
    }
    .financial-header h6 {
        font-size: 0.88rem;
        font-weight: 700;
        margin: 0;
        color: #475569;
    }
    .financial-value {
        font-size: 1.6rem;
        font-weight: 800;
        margin-bottom: 8px;
    }
    .financial-desc {
        font-size: 0.8rem;
        color: #64748b;
        margin-bottom: 18px;
        line-height: 1.5;
    }
    .financial-link {
        font-size: 0.85rem;
        font-weight: 600;
        color: #3b82f6;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        transition: all 0.2s;
        padding: 6px 12px;
        border-radius: 8px;
        background: rgba(59, 130, 246, 0.05);
    }
    .financial-link:hover {
        color: #1d4ed8;
        gap: 8px;
        background: rgba(59, 130, 246, 0.1);
    }

    .chart-card {
        background: #ffffff;
        border-radius: 18px;
        border: 1px solid #e2e8f0;
        padding: 26px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.03);
        transition: all 0.3s ease;
    }
    .chart-card:hover {
        box-shadow: 0 12px 28px rgba(0,0,0,0.06);
    }
    .chart-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 24px;
        border-bottom: 1px solid #f1f5f9;
        padding-bottom: 16px;
    }
    .chart-title-group {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .chart-title-group i {
        font-size: 1.3rem;
        color: #6366f1;
    }
    .chart-title {
        font-size: 1.05rem;
        font-weight: 700;
        color: #0f172a;
        margin: 0;
    }
    .chart-subtitle {
        font-size: 0.76rem;
        color: #64748b;
        margin-top: 3px;
    }

    .table-card {
        background: #ffffff;
        border-radius: 18px;
        border: 1px solid #e2e8f0;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0,0,0,0.03);
        transition: all 0.3s ease;
    }
    .table-card:hover {
        box-shadow: 0 12px 28px rgba(0,0,0,0.06);
    }
    .table-card-header {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 22px 26px;
        border-bottom: 1px solid #e2e8f0;
        background: linear-gradient(180deg, #fafbfb 0%, #f8fafc 100%);
    }
    .table-card-header i {
        font-size: 1.3rem;
        color: #475569;
    }
    .table-card-header h5 {
        font-size: 1.05rem;
        font-weight: 700;
        margin: 0;
        color: #0f172a;
    }
    .custom-table {
        margin-bottom: 0;
    }
    .custom-table th {
        background: #f8fafc;
        color: #64748b;
        font-weight: 600;
        font-size: 0.76rem;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        padding: 15px 26px;
        border-bottom: 1px solid #e2e8f0;
    }
    .custom-table td {
        padding: 17px 26px;
        font-size: 0.875rem;
        color: #334155;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }
    .custom-table tbody tr:last-child td {
        border-bottom: none;
    }
    .custom-table tbody tr {
        transition: background-color 0.2s ease;
    }
    .custom-table tbody tr:hover {
        background-color: #f8fafc;
    }

    .badge-lunas-new {
        background-color: rgba(16, 185, 129, 0.12);
        color: #065f46;
        font-weight: 600;
        font-size: 0.73rem;
        padding: 6px 14px;
        border-radius: 20px;
        display: inline-block;
        white-space: nowrap;
    }
    .badge-belum-new {
        background-color: rgba(239, 68, 68, 0.12);
        color: #991b1b;
        font-weight: 600;
        font-size: 0.73rem;
        padding: 6px 14px;
        border-radius: 20px;
        display: inline-block;
        white-space: nowrap;
    }

    .stok-card {
        background: #ffffff;
        border-radius: 18px;
        border: 1px solid #e2e8f0;
        padding: 22px 24px;
        height: 100%;
        transition: all 0.3s ease;
        box-shadow: 0 1px 3px rgba(0,0,0,0.03);
    }
    .stok-card:hover {
        box-shadow: 0 12px 28px rgba(0,0,0,0.06);
    }
    .stok-header {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 14px;
        border-bottom: 1px solid #f1f5f9;
        padding-bottom: 12px;
    }
    .stok-header i {
        font-size: 1.15rem;
        color: #ef4444;
    }
    .stok-header h5 {
        font-size: 0.95rem;
        font-weight: 700;
        margin: 0;
        color: #0f172a;
    }
    .stok-list-container {
        display: flex;
        flex-direction: column;
        gap: 8px;
        max-height: 160px;
        overflow-y: auto;
        padding-right: 4px;
    }
    .stok-list-container::-webkit-scrollbar {
        width: 6px;
    }
    .stok-list-container::-webkit-scrollbar-thumb {
        background: #e2e8f0;
        border-radius: 8px;
    }
    .stok-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 9px 12px;
        border-radius: 10px;
        border: 1px solid #f1f5f9;
        transition: all 0.2s ease;
    }
    .stok-item:hover {
        background: #f8fafc;
        border-color: #cbd5e1;
        transform: translateX(4px);
    }
    .stok-item .stok-name {
        font-size: 0.82rem;
        font-weight: 600;
        color: #334155;
    }
    .stok-item .stok-kategori {
        font-size: 0.7rem;
        color: #94a3b8;
        margin-top: 2px;
    }
    .stok-item .stok-badge {
        background: rgba(239, 68, 68, 0.12);
        color: #b91c1c;
        font-weight: 700;
        font-size: 0.75rem;
        padding: 4px 12px;
        border-radius: 8px;
        min-width: 40px;
        text-align: center;
    }
    .empty-stok-state {
        text-align: center;
        padding: 18px 10px;
    }
    .empty-stok-state i {
        font-size: 1.8rem;
        color: #10b981;
        background: rgba(16, 185, 129, 0.1);
        width: 52px;
        height: 52px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 10px;
    }

    @media (max-width: 767.98px) {
        .kpi-info .kpi-value { font-size: 1.4rem; }
        .revenue-info .revenue-value { font-size: 1.5rem; }
    }
</style>
@endsection

@section('content')
<!-- Welcome Banner -->
<div class="row mb-4">
    <div class="col-12">
        <div class="welcome-banner p-4 rounded-4 shadow-sm border-0 position-relative">
            <div class="welcome-content position-relative z-index-2">
                <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill fw-semibold mb-2">
                    <i class="bi bi-calendar3 me-1"></i> Performa Hari Ini
                </span>
                <h2 class="welcome-title fw-bold text-dark mt-2">Selamat Datang Kembali, {{ Auth::user()->name }}</h2>
                <p class="welcome-text text-muted mb-0">Berikut adalah ringkasan kinerja penjualan, laporan penjualan, dan status stok toko buku Anda hari ini.</p>
            </div>
        </div>
    </div>
</div>

<!-- Summary Cards (KPIs) -->
<div class="row g-4 mb-4">
    <!-- Total Pelanggan -->
    <div class="col-xl-3 col-md-6">
        <div class="kpi-card kpi-teal">
            <div class="kpi-info">
                <h6>Total Pelanggan</h6>
                <div class="kpi-value">{{ number_format($totalPelanggan) }}</div>
            </div>
            <div class="kpi-icon-wrapper">
                <i class="bi bi-people-fill"></i>
            </div>
        </div>
    </div>
    <!-- Total Produk -->
    <div class="col-xl-3 col-md-6">
        <div class="kpi-card kpi-blue">
            <div class="kpi-info">
                <h6>Total Produk</h6>
                <div class="kpi-value">{{ number_format($totalBarang) }}</div>
            </div>
            <div class="kpi-icon-wrapper">
                <i class="bi bi-box-seam-fill"></i>
            </div>
        </div>
    </div>
    <!-- Total Transaksi -->
    <div class="col-xl-3 col-md-6">
        <div class="kpi-card kpi-purple">
            <div class="kpi-info">
                <h6>Total Transaksi</h6>
                <div class="kpi-value">{{ number_format($totalPenjualan) }}</div>
            </div>
            <div class="kpi-icon-wrapper">
                <i class="bi bi-cart-check-fill"></i>
            </div>
        </div>
    </div>
    <!-- Penjualan Hari Ini -->
    <div class="col-xl-3 col-md-6">
        <div class="kpi-card kpi-orange">
            <div class="kpi-info">
                <h6>Penjualan Hari Ini</h6>
                <div class="kpi-value">Rp {{ number_format($pendapatanHariIni, 0, ',', '.') }}</div>
            </div>
            <div class="kpi-icon-wrapper">
                <i class="bi bi-graph-up-arrow"></i>
            </div>
        </div>
    </div>
</div>

<!-- Large Revenue and Profit Banners -->
<div class="row g-4 mb-4">
    <!-- Pendapatan Bulan Ini -->
    <div class="col-lg-6">
        <div class="revenue-card bg-income h-100">
            <div class="revenue-info">
                <h6>Pendapatan Bulan Ini</h6>
                <div class="revenue-value">Rp {{ number_format($pendapatanBulanIni, 0, ',', '.') }}</div>
                <div class="revenue-label">Total transaksi lunas selama bulan berjalan</div>
            </div>
            <div class="revenue-icon-wrapper">
                <i class="bi bi-cash-coin"></i>
            </div>
        </div>
    </div>
    <!-- Keuntungan Bulan Ini -->
    <div class="col-lg-6">
        <div class="revenue-card bg-profit h-100">
            <div class="revenue-info">
                <h6>Keuntungan Bulan Ini</h6>
                <div class="revenue-value">Rp {{ number_format($keuntunganBulanIni, 0, ',', '.') }}</div>
                <div class="revenue-label">Estimasi keuntungan bersih (pendapatan - modal beli)</div>
            </div>
            <div class="revenue-icon-wrapper">
                <i class="bi bi-trophy-fill"></i>
            </div>
        </div>
    </div>
</div>

<!-- Chart Visualizations -->
<div class="row g-4 mb-4">
    <!-- Chart Penjualan (col-lg-8) -->
    <div class="col-lg-8">
        <div class="chart-card h-100">
            <div class="chart-header">
                <div class="chart-title-group">
                    <i class="bi bi-bar-chart-line-fill" style="color: #6366f1;"></i>
                    <div>
                        <h5 class="chart-title">Tren Penjualan & Transaksi</h5>
                        <span class="chart-subtitle">Statistik harian selama 7 hari terakhir</span>
                    </div>
                </div>
            </div>
            <div class="chart-body" style="position: relative; height: 320px;">
                <canvas id="chartPenjualan"></canvas>
            </div>
        </div>
    </div>
    <!-- Chart Keuntungan (col-lg-4) -->
    <div class="col-lg-4">
        <div class="chart-card h-100">
            <div class="chart-header">
                <div class="chart-title-group">
                    <i class="bi bi-graph-up text-success"></i>
                    <div>
                        <h5 class="chart-title">Tren Keuntungan</h5>
                        <span class="chart-subtitle">Analisis laba bersih 7 hari terakhir</span>
                    </div>
                </div>
            </div>
            <div class="chart-body" style="position: relative; height: 320px;">
                <canvas id="chartKeuntungan"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Financial Status (Piutang, Hutang, Status Stok Menipis) -->
<div class="row g-4 mb-4 align-items-start">
    <!-- Piutang Belum Lunas -->
    <div class="col-lg-4 col-md-6">
        <div class="financial-card piutang">
            <div>
                <div class="financial-header">
                    <i class="bi bi-person-dash-fill text-danger"></i>
                    <h6>Piutang Belum Lunas</h6>
                </div>
                <div class="financial-value text-danger">Rp {{ number_format($totalNilaiPiutang, 0, ',', '.') }}</div>
                <div class="financial-desc">{{ $totalPiutangAktif }} tagihan pelanggan aktif belum diselesaikan</div>
            </div>
            <a href="{{ route('piutang.index') }}" class="financial-link mt-2">
                Kelola Piutang <i class="bi bi-arrow-right"></i>
            </a>
        </div>
    </div>
    <!-- Hutang Belum Lunas -->
    <div class="col-lg-4 col-md-6">
        <div class="financial-card hutang">
            <div>
                <div class="financial-header">
                    <i class="bi bi-wallet2 text-warning"></i>
                    <h6>Hutang Belum Lunas</h6>
                </div>
                <div class="financial-value text-warning">Rp {{ number_format($totalHutang, 0, ',', '.') }}</div>
                <div class="financial-desc">Total kewajiban pembayaran belanja ke supplier</div>
            </div>
            <a href="{{ route('pembelian.index') }}" class="financial-link mt-2">
                Kelola Hutang <i class="bi bi-arrow-right"></i>
            </a>
        </div>
    </div>
    <!-- Status Stok Menipis -->
    <div class="col-lg-4 col-md-6">
        <div class="stok-card">
            <div class="stok-header">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <h5>Status Stok Menipis</h5>
            </div>
            <div class="stok-list-container">
                @forelse($stokMenipis->take(5) as $item)
                <div class="stok-item">
                    <div>
                        <div class="stok-name">{{ $item->nama_barang }}</div>
                        <div class="stok-kategori"><i class="bi bi-tag-fill me-1"></i>{{ $item->kategori->nama_kategori ?? '-' }}</div>
                    </div>
                    <div class="stok-badge">{{ $item->stok }}</div>
                </div>
                @empty
                <div class="empty-stok-state">
                    <i class="bi bi-check-circle-fill"></i>
                    <h6 class="fw-bold text-dark mt-2">Semua Stok Aman!</h6>
                    <p class="text-muted small mb-0">Tidak ada produk dengan stok menipis saat ini.</p>
                </div>
                @endforelse
            </div>
            @if($stokMenipis->count() > 5)
            <div class="text-center mt-3 pt-2 border-top">
                <a href="{{ route('barang.index') }}" class="financial-link">
                    Lihat Semua ({{ $stokMenipis->count() }}) <i class="bi bi-arrow-right"></i>
                </a>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Penjualan Terbaru -->
<div class="row g-4">
    <!-- Table Penjualan Terbaru -->
    <div class="col-12">
        <div class="table-card h-100">
            <div class="table-card-header">
                <i class="bi bi-clock-history"></i>
                <h5>Penjualan Terbaru</h5>
            </div>
            <div class="table-responsive">
                <table class="table custom-table">
                    <thead>
                        <tr>
                            <th>No. Nota</th>
                            <th>Pelanggan</th>
                            <th>Tanggal</th>
                            <th>Total</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($penjualanTerbaru as $penjualan)
                        <tr>
                            <td class="fw-bold text-primary">{{ $penjualan->nomor_invoice }}</td>
                            <td>{{ $penjualan->nama_pelanggan_display }}</td>
                            <td>{{ $penjualan->tanggal_penjualan->format('d/m/Y H:i') }}</td>
                            <td class="fw-semibold text-dark">Rp {{ number_format($penjualan->total_akhir, 0, ',', '.') }}</td>
                            <td>
                                @if($penjualan->status_pembayaran === 'lunas')
                                    <span class="badge-lunas-new">Lunas</span>
                                @else
                                    <span class="badge-belum-new">Belum Lunas</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">Belum ada transaksi penjualan terbaru</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
<script>
    const labels = @json($labels);

    // Chart Penjualan
    const ctxPenjualan = document.getElementById('chartPenjualan');
    if (ctxPenjualan) {
        new Chart(ctxPenjualan, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Penjualan (Rp)',
                    data: @json($dataPenjualan),
                    backgroundColor: 'rgba(99, 102, 241, 0.8)',
                    borderColor: 'rgba(99, 102, 241, 1)',
                    borderWidth: 1.5,
                    borderRadius: 6,
                    barPercentage: 0.5,
                }, {
                    label: 'Jumlah Transaksi',
                    data: @json($dataTransaksi),
                    type: 'line',
                    borderColor: '#f97316',
                    backgroundColor: 'rgba(249, 115, 22, 0.1)',
                    borderWidth: 3,
                    pointBackgroundColor: '#f97316',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 1.5,
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    tension: 0.35,
                    yAxisID: 'y1',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { intersect: false, mode: 'index' },
                plugins: {
                    legend: { 
                        position: 'top', 
                        align: 'end',
                        labels: { 
                            usePointStyle: true, 
                            padding: 16,
                            font: { family: "'Inter', sans-serif", size: 11, weight: 500 }
                        } 
                    },
                    tooltip: {
                        padding: 12,
                        backgroundColor: '#1e293b',
                        titleFont: { family: "'Inter', sans-serif", size: 12, weight: 600 },
                        bodyFont: { family: "'Inter', sans-serif", size: 12 },
                        callbacks: {
                            label: function(ctx) {
                                if (ctx.datasetIndex === 0) return 'Penjualan: Rp ' + ctx.raw.toLocaleString('id-ID');
                                return 'Transaksi: ' + ctx.raw + ' kali';
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { family: "'Inter', sans-serif", size: 10 } }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f1f5f9' },
                        ticks: { 
                            font: { family: "'Inter', sans-serif", size: 10 },
                            callback: v => 'Rp ' + (v/1000).toLocaleString('id-ID') + 'k' 
                        }
                    },
                    y1: {
                        position: 'right',
                        beginAtZero: true,
                        grid: { drawOnChartArea: false },
                        ticks: { 
                            stepSize: 1,
                            font: { family: "'Inter', sans-serif", size: 10 }
                        }
                    }
                }
            }
        });
    }

    // Chart Keuntungan
    const ctxKeuntungan = document.getElementById('chartKeuntungan');
    if (ctxKeuntungan) {
        new Chart(ctxKeuntungan, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Laba Bersih (Rp)',
                    data: @json($dataKeuntungan),
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.08)',
                    borderWidth: 3,
                    pointBackgroundColor: '#10b981',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    tension: 0.38,
                    fill: true,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { 
                        position: 'top', 
                        align: 'end',
                        labels: { 
                            usePointStyle: true, 
                            padding: 16,
                            font: { family: "'Inter', sans-serif", size: 11, weight: 500 }
                        } 
                    },
                    tooltip: {
                        padding: 12,
                        backgroundColor: '#1e293b',
                        titleFont: { family: "'Inter', sans-serif", size: 12, weight: 600 },
                        bodyFont: { family: "'Inter', sans-serif", size: 12 },
                        callbacks: {
                            label: function(ctx) { return 'Laba Bersih: Rp ' + ctx.raw.toLocaleString('id-ID'); }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { family: "'Inter', sans-serif", size: 10 } }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f1f5f9' },
                        ticks: { 
                            font: { family: "'Inter', sans-serif", size: 10 },
                            callback: v => 'Rp ' + (v/1000).toLocaleString('id-ID') + 'k' 
                        }
                    }
                }
            }
        });
    }
</script>
@endsection
