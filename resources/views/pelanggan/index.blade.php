@extends('layouts.pos')

@section('title', 'Data Pelanggan - POS Toko Buku')
@section('page-title', 'Data Pelanggan')

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

        .page-header .btn-add {
            background: linear-gradient(135deg, #10b981, #059669);
            border: none;
            color: #fff;
            font-weight: 600;
            padding: 11px 22px;
            border-radius: 12px;
            font-size: 0.875rem;
            transition: all 0.2s;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);
        }

        .page-header .btn-add:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(16, 185, 129, 0.3);
            color: #fff;
        }

        .search-card {
            background: #fff;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            padding: 18px 22px;
            margin-bottom: 22px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
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
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
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
        }

        .search-card .btn-search:hover {
            background: #2563eb;
            color: #fff;
        }

        .customer-form-card {
            background: #fff;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            padding: 20px 22px;
            margin-bottom: 22px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
        }

        .customer-form-title {
            display: flex;
            align-items: center;
            gap: 12px;
            color: #0f172a;
            font-size: 1rem;
            font-weight: 800;
            margin-bottom: 18px;
        }

        .customer-form-title i {
            width: 36px;
            height: 36px;
            border-radius: 11px;
            background: rgba(16, 185, 129, 0.1);
            color: #059669;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .customer-form-grid {
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(0, 1fr) auto;
            gap: 14px;
            align-items: end;
        }

        .customer-form-card .form-label {
            color: #64748b;
            font-size: 0.76rem;
            font-weight: 700;
            margin-bottom: 7px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .customer-form-card .form-control {
            border-radius: 11px;
            border: 1px solid #e2e8f0;
            padding: 11px 15px;
            font-size: 0.875rem;
            background: #f8fafc;
        }

        .customer-form-card .form-control:focus {
            border-color: #10b981;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
            background: #fff;
        }

        .btn-save-customer {
            background: linear-gradient(135deg, #10b981, #059669);
            border: none;
            color: #fff;
            font-weight: 700;
            padding: 11px 20px;
            border-radius: 11px;
            font-size: 0.875rem;
            white-space: nowrap;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);
        }

        .btn-save-customer:hover {
            color: #fff;
            box-shadow: 0 8px 20px rgba(16, 185, 129, 0.3);
            transform: translateY(-1px);
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
            font-size: 0.875rem;
        }

        .data-card .table thead th {
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            font-weight: 600;
            color: #64748b;
            text-transform: uppercase;
            font-size: 0.73rem;
            letter-spacing: 0.06em;
            padding: 15px 22px;
        }

        .data-card .table tbody td {
            padding: 16px 22px;
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

        .pelanggan-name {
            font-weight: 600;
            color: #0f172a;
        }

        .pelanggan-initial {
            width: 38px;
            height: 38px;
            border-radius: 11px;
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            color: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.82rem;
            margin-right: 12px;
            box-shadow: 0 2px 6px rgba(59, 130, 246, 0.2);
        }

        .btn-action {
            padding: 7px 14px;
            border-radius: 9px;
            font-size: 0.8rem;
            font-weight: 600;
            border: none;
            transition: all 0.2s;
        }

        .btn-edit {
            background: rgba(245, 158, 11, 0.1);
            color: #92400e;
        }

        .btn-edit:hover {
            background: #f59e0b;
            color: #fff;
            transform: translateY(-1px);
        }

        .btn-delete {
            background: rgba(239, 68, 68, 0.1);
            color: #991b1b;
        }

        .btn-delete:hover {
            background: #ef4444;
            color: #fff;
            transform: translateY(-1px);
        }

        .pagination-wrapper {
            padding: 18px 22px;
            border-top: 1px solid #f1f5f9;
        }

        .alert-pos {
            border-radius: 14px;
            border: none;
            padding: 15px 22px;
            font-size: 0.875rem;
            font-weight: 500;
        }

        @media (max-width: 991.98px) {
            .customer-form-grid {
                grid-template-columns: 1fr;
            }

            .btn-save-customer {
                width: 100%;
            }
        }
    </style>
@endsection

@section('content')
    @php($isOwner = auth()->user()?->role === 'owner')

    <!-- Alert -->
    @if (session('success'))
        <div class="alert alert-success alert-pos alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger alert-pos alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>Periksa kembali form pelanggan baru.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Page Header -->
    <div class="page-header">
        <h2><i class="bi bi-people-fill me-2"></i>Data Pelanggan</h2>
    </div>

    <!-- Customer Form -->
    <div class="customer-form-card">
        <div class="customer-form-title">
            <i class="bi bi-person-plus-fill"></i>
            <span>Pelanggan Baru</span>
        </div>
        <form action="{{ route('pelanggan.store') }}" method="POST" class="customer-form-grid">
            @csrf
            <div>
                <label class="form-label">Nama Pelanggan <span class="text-danger">*</span></label>
                <input type="text" name="nama_pelanggan" class="form-control @error('nama_pelanggan') is-invalid @enderror"
                    value="{{ old('nama_pelanggan') }}" placeholder="Masukkan nama pelanggan" required>
                @error('nama_pelanggan')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div>
                <label class="form-label">No HP</label>
                <input type="text" name="no_telepon" class="form-control @error('no_telepon') is-invalid @enderror"
                    value="{{ old('no_telepon') }}" placeholder="Contoh: 08123456789">
                @error('no_telepon')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <button type="submit" class="btn btn-save-customer">
                <i class="bi bi-save me-1"></i>Simpan
            </button>
        </form>
    </div>

    <!-- Search -->
    <div class="search-card">
        <form action="{{ route('pelanggan.index') }}" method="GET" class="d-flex gap-2">
            <input type="text" name="keyword" class="form-control"
                placeholder="Cari No ID, nama pelanggan, atau no telepon..." value="{{ $keyword ?? '' }}">
            <button type="submit" class="btn btn-search">
                <i class="bi bi-search me-1"></i> Cari
            </button>
            @if($keyword)
                <a href="{{ route('pelanggan.index') }}" class="btn btn-outline-secondary" style="border-radius:10px;">
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
                        <th width="60">No</th>
                        <th>No ID</th>
                        <th>Pelanggan</th>
                        <th>No Telepon</th>
                        <th>Terdaftar</th>
                        <th width="160">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pelanggan as $item)
                        <tr>
                            <td class="text-muted">
                                {{ $loop->iteration + ($pelanggan->currentPage() - 1) * $pelanggan->perPage() }}</td>
                            <td><span class="fw-bold text-primary">{{ $item->no_id_pelanggan ?? '-' }}</span></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <span class="pelanggan-initial">{{ strtoupper(substr($item->nama_pelanggan, 0, 1)) }}</span>
                                    <span class="pelanggan-name">{{ $item->nama_pelanggan }}</span>
                                </div>
                            </td>
                            <td>
                                @if($item->no_telepon)
                                    <i class="bi bi-telephone text-muted me-1"></i>{{ $item->no_telepon }}
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-muted">{{ $item->created_at ? $item->created_at->format('d M Y') : '-' }}</td>
                            <td>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('pelanggan.edit', $item->id) }}" class="btn btn-action btn-edit">
                                        <i class="bi bi-pencil-square me-1"></i>Edit
                                    </a>
                                    @if ($isOwner)
                                    <form action="{{ route('pelanggan.destroy', $item->id) }}" method="POST" class="d-inline"
                                        onsubmit="return confirm('Yakin ingin menghapus pelanggan ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-action btn-delete">
                                            <i class="bi bi-trash me-1"></i>Hapus
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <i class="bi bi-people fs-1 text-muted"></i>
                                <p class="text-muted mt-2 mb-0">Belum ada data pelanggan</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($pelanggan->hasPages())
            <div class="pagination-wrapper">
                {{ $pelanggan->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
@endsection
