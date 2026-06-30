<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'POS BUKU SISWA')</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --sidebar-width: 260px;
            --sidebar-bg: #0f172a;
            --sidebar-hover: #1e293b;
            --sidebar-active: #3b82f6;
            --content-bg: #f8fafc;
            --card-shadow: 0 1px 3px rgba(0,0,0,0.04), 0 4px 12px rgba(0,0,0,0.03);
            --card-shadow-hover: 0 4px 16px rgba(0,0,0,0.08), 0 8px 24px rgba(0,0,0,0.04);
            --transition-smooth: cubic-bezier(0.4, 0, 0.2, 1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--content-bg);
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

        input[type="number"]::-webkit-outer-spin-button,
        input[type="number"]::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        input[type="number"] {
            appearance: textfield;
            -moz-appearance: textfield;
        }

        /* ===== SIDEBAR ===== */
        .pos-sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: var(--sidebar-bg);
            background-image: linear-gradient(180deg, #0f172a 0%, #1e293b 100%);
            color: #fff;
            z-index: 1000;
            display: flex;
            flex-direction: column;
            transition: width 0.3s var(--transition-smooth), transform 0.3s var(--transition-smooth);
            box-shadow: 4px 0 24px rgba(0,0,0,0.12);
        }

        body.sidebar-collapsed {
            --sidebar-width: 72px;
        }

        body.sidebar-collapsed .pos-sidebar .sidebar-brand .brand-text {
            display: none;
        }

        body.sidebar-collapsed .pos-sidebar .sidebar-brand {
            justify-content: center;
            padding: 20px 0 16px;
        }

        body.sidebar-collapsed .sidebar-toggle-btn i {
            transform: rotate(180deg);
        }

        body.sidebar-collapsed .nav-section-title:first-of-type {
            display: none;
        }

        body.sidebar-collapsed .nav-section-title {
            opacity: 1;
            height: 2px;
            padding: 0;
            margin: 18px 16px;
            background: rgba(255, 255, 255, 0.1);
            font-size: 0;
            color: transparent;
            overflow: hidden;
        }

        body.sidebar-collapsed .nav-link span {
            display: none;
        }

        body.sidebar-collapsed .nav-link {
            justify-content: center;
            padding: 12px 0;
            gap: 0;
        }

        body.sidebar-collapsed .sidebar-user .user-info {
            display: none;
        }

        body.sidebar-collapsed .sidebar-footer {
            flex-direction: column;
            gap: 12px;
            padding: 16px 0;
            align-items: center;
            justify-content: center;
        }

        body.sidebar-collapsed .sidebar-user {
            justify-content: center;
            width: 100%;
        }

        .sidebar-toggle-btn i {
            transition: transform 0.3s ease;
        }

        .pos-sidebar .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 22px 20px 18px;
            border-bottom: 1px solid rgba(255,255,255,0.06);
        }

        .pos-sidebar .sidebar-brand .brand-icon {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            background: linear-gradient(135deg, #3b82f6, #10b981);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.15rem;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
            flex-shrink: 0;
        }

        .pos-sidebar .sidebar-brand .brand-text {
            font-weight: 700;
            font-size: 1.05rem;
            letter-spacing: -0.01em;
            background: linear-gradient(135deg, #fff 0%, #94a3b8 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .sidebar-toggle-btn {
            position: absolute;
            top: 50%;
            right: -14px;
            transform: translateY(-50%);
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: var(--sidebar-active);
            border: 3px solid var(--content-bg);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 0.7rem;
            z-index: 1001;
            transition: all 0.2s;
            box-shadow: 0 2px 8px rgba(59, 130, 246, 0.3);
        }

        .sidebar-toggle-btn:hover {
            background: #2563eb;
            transform: translateY(-50%) scale(1.1);
        }

        .sidebar-nav {
            flex: 1;
            overflow-y: auto;
            padding: 16px 12px;
        }

        .sidebar-nav::-webkit-scrollbar {
            width: 4px;
        }

        .sidebar-nav::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.1);
            border-radius: 4px;
        }

        .nav-section-title {
            font-size: 0.68rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: rgba(255,255,255,0.3);
            padding: 14px 14px 8px;
            margin-top: 8px;
        }

        .nav-item {
            margin-bottom: 2px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 11px 14px;
            border-radius: 10px;
            color: rgba(255,255,255,0.6);
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.2s ease;
            position: relative;
            margin: 1px 0;
        }

        .nav-link:hover {
            background: rgba(255,255,255,0.06);
            color: #fff;
        }

        .nav-link.active {
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.2), rgba(59, 130, 246, 0.1));
            color: #fff;
            font-weight: 600;
            box-shadow: inset 3px 0 0 var(--sidebar-active);
        }

        .nav-link.active i {
            color: #60a5fa;
        }

        .nav-link i {
            font-size: 1.15rem;
            width: 22px;
            text-align: center;
            transition: transform 0.2s;
        }

        .nav-link:hover i {
            transform: scale(1.05);
        }

        /* Sidebar Footer / User */
        .sidebar-footer {
            padding: 16px 16px;
            border-top: 1px solid rgba(255,255,255,0.06);
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: rgba(0,0,0,0.1);
        }

        .sidebar-user {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sidebar-user .user-avatar {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: linear-gradient(135deg, #10b981, #059669);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.85rem;
            color: #fff;
            box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);
        }

        .sidebar-user .user-info {
            line-height: 1.3;
        }

        .sidebar-user .user-info .user-name {
            font-size: 0.85rem;
            font-weight: 600;
            color: #fff;
        }

        .sidebar-user .user-info .user-role {
            font-size: 0.7rem;
            color: rgba(255,255,255,0.45);
            text-transform: none;
            letter-spacing: 0.06em;
        }

        .sidebar-footer .logout-btn {
            color: rgba(255,255,255,0.45);
            font-size: 1.15rem;
            cursor: pointer;
            transition: all 0.2s;
            background: none;
            border: none;
            padding: 8px;
            border-radius: 8px;
        }

        .sidebar-footer .logout-btn:hover {
            color: #ef4444;
            background: rgba(239, 68, 68, 0.1);
        }

        /* ===== MAIN CONTENT ===== */
        .pos-main {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            transition: margin-left 0.3s var(--transition-smooth);
        }

        .pos-topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 32px;
            background: rgba(255,255,255,0.8);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(226, 232, 240, 0.6);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .pos-topbar .page-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: #1f2937;
        }

        .pos-topbar .topbar-right {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .pos-topbar .topbar-date {
            text-align: right;
            line-height: 1.4;
        }

        .pos-topbar .topbar-date .date-text {
            font-size: 0.78rem;
            color: #64748b;
            font-weight: 500;
        }

        .pos-topbar .topbar-date .time-text {
            font-size: 0.85rem;
            font-weight: 700;
            color: var(--sidebar-active);
        }

        .pos-topbar .notification-btn {
            position: relative;
            width: 40px;
            height: 40px;
            border-radius: 10px;
            border: 1px solid #e2e8f0;
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #64748b;
            cursor: pointer;
            transition: all 0.2s;
        }

        .pos-topbar .notification-btn:hover {
            border-color: var(--sidebar-active);
            color: var(--sidebar-active);
            box-shadow: 0 2px 8px rgba(59, 130, 246, 0.1);
        }

        .pos-content {
            padding: 28px 32px 40px;
            animation: fadeInUp 0.4s ease;
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(8px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* ===== FOOTER ===== */
        .pos-footer {
            text-align: center;
            padding: 20px 32px;
            color: #9ca3af;
            font-size: 0.82rem;
        }

        /* ===== RESPONSIVE ===== */
        .mobile-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 1.4rem;
            color: #1f2937;
            cursor: pointer;
            padding: 6px;
            border-radius: 8px;
            transition: background 0.2s;
        }

        .mobile-toggle:hover {
            background: #f1f5f9;
        }

        @media (max-width: 991.98px) {
            .pos-sidebar {
                transform: translateX(-100%);
            }

            .pos-sidebar.show {
                transform: translateX(0);
            }

            .pos-main {
                margin-left: 0;
            }

            .mobile-toggle {
                display: block;
            }

            .sidebar-toggle-btn {
                display: none;
            }

            .sidebar-overlay {
                display: none;
                position: fixed;
                inset: 0;
                background: rgba(15, 23, 42, 0.6);
                backdrop-filter: blur(4px);
                z-index: 999;
            }

            .sidebar-overlay.show {
                display: block;
            }
        }

        @media (max-width: 575.98px) {
            .pos-topbar {
                padding: 12px 16px;
            }

            .pos-content {
                padding: 16px;
            }
        }
    </style>
    @yield('styles')
</head>
<body>
    <script>
        // Instant state restoration to prevent flashing
        if (localStorage.getItem('sidebar-collapsed') === 'true') {
            document.body.classList.add('sidebar-collapsed');
        }
    </script>
    <!-- Sidebar Overlay (mobile) -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Sidebar -->
    <aside class="pos-sidebar" id="posSidebar">
        @php($isOwner = Auth::user()->role === 'owner')

        <div class="sidebar-brand">
            <div class="brand-icon">
                <i class="bi bi-book"></i>
            </div>
            <span class="brand-text">Buku Siswa 2</span>
        </div>

        <div class="sidebar-toggle-btn" id="sidebarToggle" title="Toggle Sidebar">
            <i class="bi bi-chevron-left"></i>
        </div>

        <nav class="sidebar-nav">
            <!-- Menu Utama -->
            <div class="nav-section-title">Menu Utama</div>

            @if ($isOwner)
            <div class="nav-item">
                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="bi bi-house-fill"></i>
                    <span>Dashboard</span>
                </a>
            </div>
            @endif

            <div class="nav-item">
                <a href="{{ route('penjualan.create') }}" class="nav-link {{ request()->routeIs('penjualan.create') ? 'active' : '' }}">
                    <i class="bi bi-cart-fill"></i>
                    <span>Penjualan</span>
                </a>
            </div>

            <div class="nav-item">
                <a href="{{ route('penjualan.index') }}" class="nav-link {{ request()->routeIs('penjualan.index') ? 'active' : '' }}">
                    <i class="bi bi-clock-history"></i>
                    <span>Riwayat Penjualan</span>
                </a>
            </div>

            <!-- Master Data -->
            <div class="nav-section-title">Master Data</div>

            <div class="nav-item">
                <a href="{{ route('barang.index') }}" class="nav-link {{ request()->routeIs('barang.*') ? 'active' : '' }}">
                    <i class="bi bi-box-seam-fill"></i>
                    <span>Barang & Stok</span>
                </a>
            </div>

            <div class="nav-item">
                <a href="{{ route('pelanggan.index') }}" class="nav-link {{ request()->routeIs('pelanggan.*') ? 'active' : '' }}">
                    <i class="bi bi-people-fill"></i>
                    <span>Pelanggan</span>
                </a>
            </div>

            @if ($isOwner)
            <div class="nav-item">
                <a href="{{ route('supplier.index') }}" class="nav-link {{ request()->routeIs('supplier.*') ? 'active' : '' }}">
                    <i class="bi bi-building"></i>
                    <span>Supplier</span>
                </a>
            </div>
            @endif

            <!-- Keuangan -->
            @if ($isOwner)
            <div class="nav-section-title">Keuangan</div>

            <div class="nav-item">
                <a href="{{ route('piutang.index') }}" class="nav-link {{ request()->routeIs('piutang.*') ? 'active' : '' }}">
                    <i class="bi bi-wallet2"></i>
                    <span>Piutang Pelanggan</span>
                </a>
            </div>

            <div class="nav-item">
                <a href="{{ route('kartu.piutang.index') }}" class="nav-link {{ request()->routeIs('kartu.piutang.*') ? 'active' : '' }}">
                    <i class="bi bi-journal-text"></i>
                    <span>Kartu Piutang</span>
                </a>
            </div>

            <div class="nav-item">
                <a href="{{ route('hutang.index') }}" class="nav-link {{ request()->routeIs('hutang.*') ? 'active' : '' }}">
                    <i class="bi bi-cash-stack"></i>
                    <span>Hutang Supplier</span>
                </a>
            </div>

            <div class="nav-item">
                <a href="{{ route('kartu.hutang.index') }}" class="nav-link {{ request()->routeIs('kartu.hutang.*') ? 'active' : '' }}">
                    <i class="bi bi-journal-check"></i>
                    <span>Kartu Hutang</span>
                </a>
            </div>

            <div class="nav-item">
                <a href="{{ route('pembelian.create') }}" class="nav-link {{ request()->routeIs('pembelian.create') ? 'active' : '' }}">
                    <i class="bi bi-cart-plus"></i>
                    <span>Pembelian</span>
                </a>
            </div>

            <div class="nav-item">
                <a href="{{ route('pembelian.index') }}" class="nav-link {{ request()->routeIs('pembelian.index') || request()->routeIs('pembelian.show') ? 'active' : '' }}">
                    <i class="bi bi-truck"></i>
                    <span>Riwayat Pembelian</span>
                </a>
            </div>

            <div class="nav-item">
                <a href="{{ route('laporan.penjualan') }}" class="nav-link {{ request()->routeIs('laporan.*') ? 'active' : '' }}">
                    <i class="bi bi-file-earmark-bar-graph-fill"></i>
                    <span>Laporan Penjualan</span>
                </a>
            </div>
            @endif
        </nav>

        <!-- Sidebar Footer -->
        <div class="sidebar-footer">
            <div class="sidebar-user">
                <div class="user-avatar">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
                <div class="user-info">
                    <div class="user-name">{{ Auth::user()->name }}</div>
                    <div class="user-role">{{ match (Auth::user()->role) {
                        'owner' => 'OWNER',
                        'karyawan_kasir' => 'KARYAWAN KASIR',
                        default => ucwords(str_replace('_', ' ', Auth::user()->role ?? 'kasir')),
                    } }}</div>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}" style="margin:0;">
                @csrf
                <button type="submit" class="logout-btn" title="Logout">
                    <i class="bi bi-box-arrow-right"></i>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="pos-main">
        <!-- Top Bar -->
        <div class="pos-topbar">
            <div class="d-flex align-items-center gap-3">
                <button class="mobile-toggle" id="mobileToggle">
                    <i class="bi bi-list"></i>
                </button>
            </div>
            <div class="topbar-right">
                <div class="topbar-date">
                    <div class="date-text" id="currentDate"></div>
                    <div class="time-text" id="currentTime"></div>
                </div>
            </div>
        </div>

        <!-- Page Content -->
        <div class="pos-content">
            @yield('content')
        </div>


    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Date & Time
        function updateDateTime() {
            const now = new Date();
            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            document.getElementById('currentDate').textContent = now.toLocaleDateString('id-ID', options);
            document.getElementById('currentTime').textContent = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
        }
        updateDateTime();
        setInterval(updateDateTime, 1000);

        document.addEventListener('wheel', function(event) {
            if (event.target.matches('input[type="number"]') && document.activeElement === event.target) {
                event.preventDefault();
            }
        }, { passive: false });

        document.addEventListener('keydown', function(event) {
            if (
                event.target.matches('input[type="number"]') &&
                (event.key === 'ArrowUp' || event.key === 'ArrowDown')
            ) {
                event.preventDefault();
            }
        });

        // Sidebar Collapse/Expand Toggle (desktop)
        const sidebarToggle = document.getElementById('sidebarToggle');
        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', function() {
                document.body.classList.toggle('sidebar-collapsed');
                localStorage.setItem('sidebar-collapsed', document.body.classList.contains('sidebar-collapsed'));
            });
        }

        // Sidebar Toggle (mobile)
        const sidebar = document.getElementById('posSidebar');
        const overlay = document.getElementById('sidebarOverlay');
        const mobileToggle = document.getElementById('mobileToggle');

        if (mobileToggle) {
            mobileToggle.addEventListener('click', function() {
                sidebar.classList.toggle('show');
                overlay.classList.toggle('show');
            });
        }

        if (overlay) {
            overlay.addEventListener('click', function() {
                sidebar.classList.remove('show');
                overlay.classList.remove('show');
            });
        }
    </script>

    @yield('scripts')
</body>
</html>
