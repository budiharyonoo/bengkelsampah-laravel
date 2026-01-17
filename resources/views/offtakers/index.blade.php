@extends('dashboard')
@section('content')
@include('partials.dashboard-header-bar', ['title' => 'Offtaker'])
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Offtaker - Admin Panel</title>
    <link href="https://fonts.googleapis.com/css2?family=Urbanist:wght@400;500;600;700;900&display=swap" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Urbanist', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        body {
            background: #fff;
            color: #1e293b;
        }

        .container {
            max-width: 1400px;
            background: #fff;
            border: 1px solid #E5E6E6;
            border-radius: 16px;
            padding: 16px 16px 16px 24px;
        }

        .controls {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .search-filter {
            display: flex;
            gap: 1rem;
            flex: 1;
        }

        .search-box {
            position: relative;
            flex: 1;
            max-width: 300px;
            border-radius: 18px;
            border: 1px solid #EFF0F0;
            background: #EFF0F0;
            display: flex;
            align-items: center;
        }

        .search-input {
            width: 100%;
            border: none;
            outline: none;
            background: transparent;
            font-size: 15px;
            font-weight: 400;
            color: #6B7271;
            padding: 6px 40px 6px 14px;
            border-radius: 18px;
        }

        .search-input::placeholder {
            color: #6B7271;
        }

        .search-btn {
            position: absolute;
            right: 8px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            padding: 4px;
        }

        .filter-select {
            padding: 8px 12px;
            border: 1px solid #E5E6E6;
            border-radius: 8px;
            font-size: 14px;
            color: #1e293b;
            background: #fff;
            min-width: 120px;
        }

        .btn-add {
            padding: 8px 16px;
            background: #39746E;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            color: #DFF0EE;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-add:hover {
            background: #2d5a55;
        }

        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 16px;
            font-size: 14px;
        }

        .alert-success {
            background: #D1FAE5;
            color: #065F46;
            border: 1px solid #A7F3D0;
        }

        .alert-error {
            background: #FEE2E2;
            color: #991B1B;
            border: 1px solid #FECACA;
        }

        .table-container {
            overflow-x: auto;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .data-table th,
        .data-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #E5E6E6;
        }

        .data-table th {
            font-weight: 600;
            font-size: 13px;
            color: #6B7271;
            text-transform: uppercase;
        }

        .data-table td {
            font-size: 14px;
            color: #1e293b;
        }

        .data-table tbody tr:hover {
            background: #F9FAFB;
        }

        .badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }

        .badge-buyer {
            background: #DBEAFE;
            color: #1E40AF;
        }

        .badge-processor {
            background: #FCE7F3;
            color: #9D174D;
        }

        .badge-both {
            background: #E0E7FF;
            color: #3730A3;
        }

        .badge-active {
            background: #D1FAE5;
            color: #065F46;
        }

        .badge-inactive {
            background: #FEE2E2;
            color: #991B1B;
        }

        .action-btn {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 500;
            cursor: pointer;
            border: none;
            margin-right: 4px;
            text-decoration: none;
        }

        .btn-view {
            background: #EBF5FF;
            color: #1E40AF;
        }

        .btn-edit {
            background: #FEF3C7;
            color: #92400E;
        }

        .btn-delete {
            background: #FEE2E2;
            color: #991B1B;
        }

        .btn-toggle {
            background: #E5E6E6;
            color: #374151;
        }

        .pagination-container {
            display: flex;
            justify-content: center;
            margin-top: 1rem;
        }

        .pagination-container .pagination {
            display: flex;
            gap: 4px;
        }

        .pagination-container .page-link {
            padding: 6px 12px;
            border: 1px solid #E5E6E6;
            border-radius: 6px;
            font-size: 14px;
            color: #374151;
            text-decoration: none;
        }

        .pagination-container .page-item.active .page-link {
            background: #39746E;
            color: #fff;
            border-color: #39746E;
        }

        .empty-state {
            text-align: center;
            padding: 40px;
            color: #6B7271;
        }

        .empty-state h3 {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 8px;
        }
    </style>
</head>
<body>
    <div style="padding: 0 2rem 2rem 2rem;">
        <div class="container">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if(session('error'))
                <div class="alert alert-error">{{ session('error') }}</div>
            @endif

            <div class="controls">
                <form method="GET" action="{{ route('offtakers.index') }}" class="search-filter">
                    <div class="search-box">
                        <input type="text" name="search" class="search-input" placeholder="Cari offtaker..." value="{{ request('search') }}">
                        <button type="submit" class="search-btn">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#6B7271" stroke-width="2">
                                <circle cx="11" cy="11" r="8"></circle>
                                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                            </svg>
                        </button>
                    </div>
                    <select name="tipe" class="filter-select" onchange="this.form.submit()">
                        <option value="">Semua Tipe</option>
                        <option value="buyer" {{ request('tipe') === 'buyer' ? 'selected' : '' }}>Pembeli</option>
                        <option value="processor" {{ request('tipe') === 'processor' ? 'selected' : '' }}>Pengolah</option>
                        <option value="both" {{ request('tipe') === 'both' ? 'selected' : '' }}>Keduanya</option>
                    </select>
                    <select name="is_active" class="filter-select" onchange="this.form.submit()">
                        <option value="">Semua Status</option>
                        <option value="true" {{ request('is_active') === 'true' ? 'selected' : '' }}>Aktif</option>
                        <option value="false" {{ request('is_active') === 'false' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </form>
                <a href="{{ route('offtakers.create') }}" class="btn-add">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                    Tambah Offtaker
                </a>
            </div>

            <div class="table-container">
                @if($offtakers->count() > 0)
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Nama</th>
                                <th>Tipe</th>
                                <th>PIC</th>
                                <th>Kontak</th>
                                <th>Transaksi</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($offtakers as $offtaker)
                                <tr>
                                    <td><strong>{{ $offtaker->kode_offtaker }}</strong></td>
                                    <td>{{ $offtaker->nama }}</td>
                                    <td>
                                        @if($offtaker->tipe === 'buyer')
                                            <span class="badge badge-buyer">Pembeli</span>
                                        @elseif($offtaker->tipe === 'processor')
                                            <span class="badge badge-processor">Pengolah</span>
                                        @else
                                            <span class="badge badge-both">Keduanya</span>
                                        @endif
                                    </td>
                                    <td>{{ $offtaker->nama_pic }}</td>
                                    <td>{{ $offtaker->kontak_pic }}</td>
                                    <td>{{ $offtaker->waste_transactions_count ?? 0 }}</td>
                                    <td>
                                        @if($offtaker->is_active)
                                            <span class="badge badge-active">Aktif</span>
                                        @else
                                            <span class="badge badge-inactive">Nonaktif</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('offtakers.show', $offtaker) }}" class="action-btn btn-view">Lihat</a>
                                        <a href="{{ route('offtakers.edit', $offtaker) }}" class="action-btn btn-edit">Edit</a>
                                        <form action="{{ route('offtakers.toggle-status', $offtaker) }}" method="POST" style="display: inline;">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="action-btn btn-toggle">
                                                {{ $offtaker->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="empty-state">
                        <h3>Belum ada offtaker</h3>
                        <p>Tambahkan offtaker baru untuk memulai pencatatan penjualan dan pengolahan sampah.</p>
                    </div>
                @endif
            </div>

            @if($offtakers->hasPages())
                <div class="pagination-container">
                    {{ $offtakers->links() }}
                </div>
            @endif
        </div>
    </div>
</body>
</html>
@endsection
