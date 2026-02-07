@extends('dashboard')
@section('content')
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Detail User - Admin Panel</title>
    <link href="https://fonts.googleapis.com/css2?family=Urbanist:wght@400;500;600;700;900&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Urbanist', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }
        body {
            font-family: 'Urbanist', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #fff;
            color: #1e293b;
        }
        .header {
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header-left {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .header-left h1 {
            font-family: 'Urbanist', sans-serif;
            font-size: 22px;
            font-weight: 400;
            color: #39746E;
        }
        .header-separator {
            font-family: 'Urbanist', sans-serif;
            font-size: 22px;
            font-weight: 400;
            color: #39746E;
        }
        .header-subtitle {
            font-family: 'Urbanist', sans-serif;
            font-size: 22px;
            font-weight: 700;
            color: #39746E;
        }
        .user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .notification {
            position: relative;
            width: 24px;
            height: 24px;
            cursor: pointer;
        }
        .notification::before {
            content: "🔔";
            font-size: 18px;
        }
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #0FB7A6;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 14px;
            font-family: 'Urbanist', sans-serif;
        }
        .user-name {
            font-family: 'Urbanist', sans-serif;
            font-weight: 700;
            font-size: 16px;
            color: #39746E;
        }
        .main-container {
            display: flex;
            gap: 1rem;
            margin: 0 2rem 2rem 2rem;
        }
        .detail-container {
            flex: 1;
            background: #fff;
            border: 1px solid #E5E6E6;
            border-radius: 16px;
            padding: 24px;
        }
        .detail-header {
            margin-bottom: 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .detail-title {
            font-family: 'Urbanist', sans-serif;
            font-size: 18px;
            font-weight: 700;
            color: #242E2C;
        }
        .detail-actions {
            display: flex;
            gap: 8px;
        }
        .btn-back {
            padding: 8px 16px;
            background: transparent;
            border: 1px solid #E5E6E6;
            border-radius: 8px;
            font-family: 'Urbanist', sans-serif;
            font-size: 14px;
            font-weight: 600;
            color: #6B7271;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .btn-back:hover {
            background: #F8F9FA;
        }
        .btn-edit {
            padding: 8px 16px;
            background: #39746E;
            border: none;
            border-radius: 8px;
            font-family: 'Urbanist', sans-serif;
            font-size: 14px;
            font-weight: 600;
            color: #DFF0EE;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .btn-edit:hover {
            background: #2d5a55;
        }
        .info-section {
            margin-bottom: 32px;
        }
        .info-title {
            font-family: 'Urbanist', sans-serif;
            font-size: 16px;
            font-weight: 600;
            color: #39746E;
            margin-bottom: 16px;
            padding-bottom: 8px;
            border-bottom: 2px solid #E3F4F1;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
        }
        .info-item {
            display: flex;
            flex-direction: column;
        }
        .info-label {
            font-family: 'Urbanist', sans-serif;
            font-size: 14px;
            font-weight: 500;
            color: #6B7271;
            margin-bottom: 8px;
        }
        .info-value {
            font-family: 'Urbanist', sans-serif;
            font-size: 16px;
            font-weight: 600;
            color: #242E2C;
            padding: 12px;
            background: #F8F9FA;
            border-radius: 8px;
            border: 1px solid #E5E6E6;
        }
        .info-value.full-width {
            grid-column: 1 / -1;
        }
        .identifier-badge {
            display: inline-block;
            padding: 8px 16px;
            background: #E3F4F1;
            color: #39746E;
            border-radius: 20px;
            font-family: 'Urbanist', sans-serif;
            font-size: 14px;
            font-weight: 600;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 13px;
            font-weight: 700;
            margin-left: 6px;
            border: 1.5px solid;
            background: transparent;
        }
        .status-active { color: #059669; border-color: #059669; }
        .status-inactive { color: #DC2626; border-color: #DC2626; }
        .status-default { color: #6B7280; border-color: #6B7280; }
        .status-selesai { color: #059669; border-color: #059669; }
        .status-diproses { color: #d97706; border-color: #d97706; }
        .status-dikonfirmasi { color: #1d4ed8; border-color: #1d4ed8; }
        .status-batal { color: #dc2626; border-color: #dc2626; }
        .status-dijemput { color: #059669; border-color: #059669; }
        .status-pending { color: #f59e42; border-color: #f59e42; }
        .recent-list {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }
        .recent-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem;
            background: #F8F9FA;
            border-radius: 8px;
            border: 1px solid #E5E6E6;
        }
        .recent-bank {
            font-size: 15px;
            font-weight: 600;
            color: #242E2C;
        }
        .recent-details {
            font-size: 13px;
            color: #6B7271;
        }
        .recent-amount {
            font-size: 15px;
            font-weight: 600;
            color: #059669;
        }
        .empty-state {
            text-align: center;
            padding: 2rem;
            color: #6B7271;
        }
        .empty-state-icon {
            font-size: 48px;
            margin-bottom: 1rem;
        }
        .detail-card {
            background: #fff;
            border: 1px solid #E5E6E6;
            border-radius: 16px;
            padding: 24px;
            margin-bottom: 2rem;
        }
        .stats-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        .stat-card {
            background: #F8F9FA;
            border: 1px solid #E5E6E6;
            border-radius: 16px;
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            min-width: 180px;
        }
        .stat-title {
            font-size: 14px;
            font-weight: 500;
            color: #6B7271;
        }
        .stat-value {
            font-size: 24px;
            font-weight: 700;
            color: #242E2C;
        }
        .stat-desc {
            font-size: 13px;
            color: #059669;
            font-weight: 500;
        }
        @media (max-width: 768px) {
            .info-grid {
                grid-template-columns: 1fr;
            }
        }
        /* Loading Overlay Styles */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        }

        .loading-content {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .loading-spinner-large {
            width: 40px;
            height: 40px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #00B6A0;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 1rem;
        }

        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        /* Export Dropdown Styles */
        .export-dropdown {
            position: relative;
        }

        .export-button {
            padding: 0 1rem;
            background: #39746E;
            border: 1px solid #E5E6E6;
            border-radius: 8px;
            font-family: 'Urbanist', sans-serif;
            font-size: 14px;
            font-weight: 600;
            color: #DFF0EE;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            height: 37px;
            transition: all 0.2s;
        }

        .export-button:hover {
            background: #2d5a55;
        }

        .export-button img {
            filter: brightness(0) invert(1);
        }

        .export-dropdown-content {
            display: none;
            position: absolute;
            top: 100%;
            right: 0;
            background: white;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            z-index: 10;
            min-width: 180px;
            padding: 0.5rem;
            margin-top: 4px;
        }

        .export-dropdown-content.show {
            display: block;
        }

        .export-option {
            padding: 8px 12px;
            cursor: pointer;
            transition: background-color 0.2s;
            display: flex;
            align-items: center;
            gap: 8px;
            border-radius: 4px;
        }

        .export-option:hover {
            background-color: #f3f4f6;
        }

        .export-option img {
            width: 16px;
            height: 16px;
        }

        .export-option span {
            font-family: 'Urbanist', sans-serif;
            font-size: 14px;
            color: #1e293b;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }

        .modal.show {
            display: flex;
        }

        .modal-content {
            background: white;
            padding: 2rem;
            border-radius: 16px;
            max-width: 400px;
            width: 90%;
            text-align: center;
            position: relative;
        }

        .modal-close {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: none;
            border: none;
            cursor: pointer;
            padding: 0.25rem;
        }

        .modal-close img {
            width: 20px;
            height: 20px;
        }

        .modal-icon {
            width: 48px;
            height: 48px;
            margin-bottom: 1rem;
        }

        .modal-title {
            font-family: 'Urbanist', sans-serif;
            font-size: 18px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 0.5rem;
        }

        .modal-subtitle {
            font-family: 'Urbanist', sans-serif;
            font-size: 14px;
            color: #6b7280;
            margin-bottom: 1.5rem;
        }

        .modal-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
        }

        .modal-button {
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-family: 'Urbanist', sans-serif;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }

        .cancel-button {
            background: white;
            border: 1px solid #d1d5db;
            color: #374151;
        }

        .cancel-button:hover {
            background: #f3f4f6;
        }

        .confirm-button {
            background: #39746E;
            border: none;
            color: white;
        }

        .confirm-button:hover {
            background: #2d5a55;
        }
    </style>
</head>
<body>
    <!-- Loading Overlay -->
    <div class="loading-overlay" id="pageLoadingOverlay" style="display: none;">
        <div class="loading-content">
            <div class="loading-spinner-large"></div>
            <p id="loadingMessage">Memuat...</p>
        </div>
    </div>

    <header class="header">
        <div class="header-left">
            <h1>User</h1>
            <span class="header-separator">/</span>
            <span class="header-subtitle">Detail User</span>
        </div>
        <div class="user-info">
            <div class="notification"></div>
            <span class="user-name">{{ Auth::guard('admin')->user()->role ?? 'Admin' }}</span>
            <div class="user-avatar">{{ strtoupper(substr(Auth::guard('admin')->user()->name ?? 'A', 0, 2)) }}</div>
        </div>
    </header>
    <div class="main-container">
        <div style="width: 100%;">
            <div style="background:none; border:none; box-shadow:none; padding:0; margin-bottom:24px;">
                <div class="detail-header" style="border:none; margin-bottom:0; padding-bottom:0; justify-content: flex-end;">
                    <div class="detail-actions" style="gap:12px; justify-content: flex-end;">
                        <div class="export-dropdown">
                            <button class="export-button" id="exportButton">
                                <span>Export</span>
                                <img src="{{ asset('icon/ic_trailing.svg') }}" alt="Export" width="16" height="16">
                            </button>
                            <div class="export-dropdown-content" id="exportDropdown">
                                <div class="export-option" onclick="exportData('excel')">
                                    <img src="{{ asset('icon/ic_laporan.svg') }}" alt="Excel">
                                    <span>Export Excel</span>
                                </div>
                                <div class="export-option" onclick="exportData('csv')">
                                    <img src="{{ asset('icon/ic_laporan.svg') }}" alt="CSV">
                                    <span>Export CSV</span>
                                </div>
                                <div class="export-option" onclick="exportData('pdf')">
                                    <img src="{{ asset('icon/ic_laporan.svg') }}" alt="PDF">
                                    <span>Export PDF</span>
                                </div>
                            </div>
                        </div>
                        <a href="{{ route('dashboard.user') }}" class="btn-back" style="padding:8px 16px; border-radius:8px; background:transparent; border:1.5px solid #F73541; color:#F73541; font-family:'Urbanist',sans-serif; font-size:14px; font-weight:600; display:inline-flex; align-items:center; transition:background 0.2s;">
                            Kembali
                        </a>
                        <a href="{{ route('dashboard.user.edit', $user->id) }}" class="btn-edit" style="padding:8px 16px; background:#39746E; border:none; border-radius:8px; font-family:'Urbanist',sans-serif; font-size:14px; font-weight:600; color:#fff; display:flex; align-items:center; gap:8px;">
                            <img src='{{ asset('icon/ic_edit.svg') }}' alt='Edit' width='16' height='16' style='filter:brightness(0) invert(1);'>
                            Edit User
                        </a>
                    </div>
                </div>
            </div>

            <div class="stats-row">
                <div class="stat-card">
                    <div class="stat-title">Total Setoran</div>
                    <div class="stat-value">{{ number_format($user->setor) }}</div>
                    <div class="stat-desc">Total deposits made</div>
                </div>
                <div class="stat-card">
                    <div class="stat-title">Total Poin</div>
                    <div class="stat-value">{{ number_format($user->poin, 2, ',', '.') }}</div>
                    <div class="stat-desc">Points earned</div>
                </div>
                <div class="stat-card">
                    <div class="stat-title">Total XP</div>
                    <div class="stat-value">{{ number_format($user->xp) }}</div>
                    <div class="stat-desc">Experience points</div>
                </div>
                <div class="stat-card">
                    <div class="stat-title">Total Sampah</div>
                    <div class="stat-value">{{ number_format($user->sampah, 1) }} kg</div>
                    <div class="stat-desc">{{ number_format($user->sampah_unit ?? 0) }} unit</div>
                </div>
            </div>
            <div class="detail-card">
                <div class="info-section">
                    <h3 class="info-title">Data User</h3>
                    <div class="info-grid">
                        <div class="info-item">
                            <span class="info-label">Identifier</span>
                            <div class="info-value">
                                <span class="identifier-badge">{{ $user->identifier }}</span>
                            </div>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Nama User</span>
                            <div class="info-value">{{ $user->name }}</div>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Jenis Nasabah</span>
                            <div class="info-value">{{ $user->jenis_nasabah }}</div>
                        </div>
                        <div class="info-item">
                            <span class="info-label">No. Telepon</span>
                            <div class="info-value">{{ $user->phone ?? 'Tidak ada data' }}</div>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Tanggal Registrasi</span>
                            <div class="info-value">{{ $user->created_at->format('d M Y H:i') }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="detail-card">
                <div class="info-section">
                    <h3 class="info-title">Setoran Terbaru</h3>
                    <div class="recent-list">
                        @php
                            $recentSetoran = \App\Models\Setoran::where('user_id', $user->id)
                                ->with('bankSampah')
                                ->orderBy('created_at', 'desc')
                                ->limit(5)
                                ->get();
                        @endphp
                        @forelse($recentSetoran as $setoran)
                        <div class="recent-item">
                            <div>
                                <div class="recent-bank">{{ $setoran->bankSampah->nama_bank_sampah ?? 'Bank Sampah' }}</div>
                                <div class="recent-details">{{ $setoran->created_at->format('d M Y H:i') }}</div>
                            </div>
                            <div style="display: flex; flex-direction: column; align-items: flex-end; gap: 4px;">
                                <div class="recent-amount">Rp {{ number_format($setoran->aktual_total ?? $setoran->estimasi_total) }}</div>
                                @php
                                    $statusClass = 'status-' . ($setoran->status ?? 'default');
                                @endphp
                                <span class="status-badge {{ $statusClass }}">{{ ucfirst($setoran->status) }}</span>
                            </div>
                        </div>
                        @empty
                        <div class="empty-state">
                            <div class="empty-state-icon">📦</div>
                            <div style="font-weight: 600; margin-bottom: 0.5rem;">Belum ada setoran</div>
                            <div style="font-size: 14px;">Data setoran akan muncul di sini ketika ada transaksi</div>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
            <div class="detail-card">
                <div class="info-section">
                    <h3 class="info-title">Riwayat Poin Terbaru</h3>
                    <div class="recent-list">
                        @php
                            $recentPoints = \App\Models\Point::where('user_id', $user->id)
                                ->orderBy('created_at', 'desc')
                                ->limit(5)
                                ->get();
                        @endphp
                        @forelse($recentPoints as $point)
                        <div class="recent-item">
                            <div>
                                <div class="recent-bank">{{ $point->keterangan ?? 'Poin' }}</div>
                                <div class="recent-details">{{ $point->created_at->format('d M Y H:i') }}</div>
                            </div>
                            <div style="display: flex; flex-direction: column; align-items: flex-end; gap: 4px;">
                                <div class="recent-amount" style="color: {{ $point->type == 'redeem' ? '#DC2626' : '#059669' }};">
                                    {{ $point->type == 'redeem' ? '-' : '+' }}{{ number_format(abs($point->jumlah_point), 2, ',', '.') }} Poin
                                </div>
                                <span class="status-badge {{ $point->type == 'redeem' ? 'status-batal' : 'status-selesai' }}">
                                    {{ ucfirst($point->type) }}
                                </span>
                            </div>
                        </div>
                        @empty
                        <div class="empty-state">
                            <div class="empty-state-icon">🪙</div>
                            <div style="font-weight: 600; margin-bottom: 0.5rem;">Belum ada riwayat poin</div>
                            <div style="font-size: 14px;">Riwayat poin akan muncul di sini ketika ada transaksi</div>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
            <div class="detail-card">
                <div class="info-section">
                    <h3 class="info-title">Daftar Alamat User</h3>
                    <div class="recent-list">
                        @php
                            $userAddresses = \App\Models\Address::where('user_id', $user->id)
                                ->orderBy('is_default', 'desc')
                                ->orderBy('created_at', 'desc')
                                ->get();
                        @endphp
                        @forelse($userAddresses as $address)
                        <div class="recent-item">
                            <div style="flex:1;">
                                <div class="recent-bank" style="font-weight:600; font-size:15px; color:#242E2C;">{{ $address->label_alamat }}</div>
                                <div class="recent-details" style="font-size:14px; color:#1e293b; margin-top:2px;">
                                    {{ $address->detail_lain ? $address->detail_lain . ', ' : '' }}
                                    {{ $address->kecamatan }}, {{ $address->kota_kabupaten }}, {{ $address->provinsi }}, {{ $address->kode_pos }}
                                </div>
                                <div style="font-size:12px; color:#6B7271; margin-top:4px;">{{ $address->nomor_handphone }}</div>
                            </div>
                            <div style="display:flex;flex-direction:column;align-items:flex-end;gap:4px;">
                                @if($address->is_default)
                                    <span class="status-badge status-selesai">Default</span>
                                @endif
                            </div>
                        </div>
                        @empty
                        <div class="empty-state">
                            <div class="empty-state-icon">📍</div>
                            <div style="font-weight: 600; margin-bottom: 0.5rem;">Belum ada alamat</div>
                            <div style="font-size: 14px;">Alamat user akan muncul di sini</div>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Export Excel Modal -->
    <div class="modal" id="exportExcelModal">
        <div class="modal-content">
            <button class="modal-close" id="closeExportModalBtn">
                <img src="{{ asset('icon/ic_close.svg') }}" alt="Close">
            </button>
            <img src="{{ asset('icon/ic_laporan.svg') }}" alt="Export" class="modal-icon">
            <h2 class="modal-title">Export Excel Detail User</h2>
            <p class="modal-subtitle">Apakah Anda yakin ingin mengekspor detail user {{ $user->name }}?</p>

            <div class="modal-buttons">
                <button class="modal-button cancel-button" id="cancelExportBtn">Batal</button>
                <button class="modal-button confirm-button" id="confirmExportBtn">Export Excel</button>
            </div>
        </div>
    </div>

    <!-- Export CSV Modal -->
    <div class="modal" id="exportCsvModal">
        <div class="modal-content">
            <button class="modal-close" id="closeCsvModalBtn">
                <img src="{{ asset('icon/ic_close.svg') }}" alt="Close">
            </button>
            <img src="{{ asset('icon/ic_laporan.svg') }}" alt="Export" class="modal-icon">
            <h2 class="modal-title">Export CSV Detail User</h2>
            <p class="modal-subtitle">Apakah Anda yakin ingin mengekspor detail user {{ $user->name }}?</p>

            <div class="modal-buttons">
                <button class="modal-button cancel-button" id="cancelCsvBtn">Batal</button>
                <button class="modal-button confirm-button" id="confirmCsvBtn">Export CSV</button>
            </div>
        </div>
    </div>

    <!-- Export PDF Modal -->
    <div class="modal" id="exportPdfModal">
        <div class="modal-content">
            <button class="modal-close" id="closePdfModalBtn">
                <img src="{{ asset('icon/ic_close.svg') }}" alt="Close">
            </button>
            <img src="{{ asset('icon/ic_laporan.svg') }}" alt="Export" class="modal-icon">
            <h2 class="modal-title">Export PDF Detail User</h2>
            <p class="modal-subtitle">Apakah Anda yakin ingin mengekspor detail user {{ $user->name }}?</p>

            <div class="modal-buttons">
                <button class="modal-button cancel-button" id="cancelPdfBtn">Batal</button>
                <button class="modal-button confirm-button" id="confirmPdfBtn">Export PDF</button>
            </div>
        </div>
    </div>

    <script>
        // Loading helper functions
        function showLoading(message = 'Memuat...') {
            document.getElementById('loadingMessage').textContent = message;
            document.getElementById('pageLoadingOverlay').style.display = 'flex';
        }

        function hideLoading() {
            document.getElementById('pageLoadingOverlay').style.display = 'none';
        }

        // Show loading on page load
        document.addEventListener('DOMContentLoaded', function() {
            showLoading('Memuat data user...');
            setTimeout(() => {
                hideLoading();
            }, 500);
        });

        // Add loading for navigation
        document.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', function() {
                showLoading('Memuat halaman...');
            });
        });

        // Export Dropdown Toggle
        document.getElementById('exportButton').addEventListener('click', function(e) {
            e.stopPropagation();
            document.getElementById('exportDropdown').classList.toggle('show');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.export-dropdown')) {
                document.getElementById('exportDropdown').classList.remove('show');
            }
            if (e.target.classList.contains('modal')) {
                e.target.classList.remove('show');
            }
        });

        // Export Data Function
        function exportData(format) {
            document.getElementById('exportDropdown').classList.remove('show');
            switch(format) {
                case 'excel':
                    document.getElementById('exportExcelModal').classList.add('show');
                    break;
                case 'csv':
                    document.getElementById('exportCsvModal').classList.add('show');
                    break;
                case 'pdf':
                    document.getElementById('exportPdfModal').classList.add('show');
                    break;
                default:
                    alert('Format export tidak valid');
            }
        }

        // Excel Export Modal Events
        document.getElementById('closeExportModalBtn').addEventListener('click', function() {
            document.getElementById('exportExcelModal').classList.remove('show');
        });
        document.getElementById('cancelExportBtn').addEventListener('click', function() {
            document.getElementById('exportExcelModal').classList.remove('show');
        });
        document.getElementById('confirmExportBtn').addEventListener('click', function() {
            exportExcel();
        });

        // CSV Export Modal Events
        document.getElementById('closeCsvModalBtn').addEventListener('click', function() {
            document.getElementById('exportCsvModal').classList.remove('show');
        });
        document.getElementById('cancelCsvBtn').addEventListener('click', function() {
            document.getElementById('exportCsvModal').classList.remove('show');
        });
        document.getElementById('confirmCsvBtn').addEventListener('click', function() {
            exportCsv();
        });

        // PDF Export Modal Events
        document.getElementById('closePdfModalBtn').addEventListener('click', function() {
            document.getElementById('exportPdfModal').classList.remove('show');
        });
        document.getElementById('cancelPdfBtn').addEventListener('click', function() {
            document.getElementById('exportPdfModal').classList.remove('show');
        });
        document.getElementById('confirmPdfBtn').addEventListener('click', function() {
            exportPdf();
        });

        // Export Excel Function
        function exportExcel() {
            showLoading('Mengexport Excel...');

            const formData = new FormData();
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

            fetch('{{ route("dashboard.user.export-detail.excel", $user->id) }}', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (response.ok) {
                    return response.blob();
                }
                throw new Error('Export failed');
            })
            .then(blob => {
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = 'user_detail_{{ $user->identifier }}_' + new Date().toISOString().slice(0,19).replace(/[:T]/g, '-') + '.xlsx';
                document.body.appendChild(a);
                a.click();
                window.URL.revokeObjectURL(url);
                a.remove();
                hideLoading();
                document.getElementById('exportExcelModal').classList.remove('show');
            })
            .catch(error => {
                console.error('Export error:', error);
                hideLoading();
                alert('Gagal export Excel: ' + error.message);
            });
        }

        // Export CSV Function
        function exportCsv() {
            showLoading('Mengexport CSV...');

            const formData = new FormData();
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

            fetch('{{ route("dashboard.user.export-detail.csv", $user->id) }}', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (response.ok) {
                    return response.blob();
                }
                throw new Error('Export failed');
            })
            .then(blob => {
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = 'user_detail_{{ $user->identifier }}_' + new Date().toISOString().slice(0,19).replace(/[:T]/g, '-') + '.csv';
                document.body.appendChild(a);
                a.click();
                window.URL.revokeObjectURL(url);
                a.remove();
                hideLoading();
                document.getElementById('exportCsvModal').classList.remove('show');
            })
            .catch(error => {
                console.error('Export error:', error);
                hideLoading();
                alert('Gagal export CSV: ' + error.message);
            });
        }

        // Export PDF Function
        function exportPdf() {
            showLoading('Mengexport PDF...');

            const formData = new FormData();
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

            fetch('{{ route("dashboard.user.export-detail.pdf", $user->id) }}', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (response.ok) {
                    return response.blob();
                }
                throw new Error('Export failed');
            })
            .then(blob => {
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = 'user_detail_{{ $user->identifier }}_' + new Date().toISOString().slice(0,19).replace(/[:T]/g, '-') + '.pdf';
                document.body.appendChild(a);
                a.click();
                window.URL.revokeObjectURL(url);
                a.remove();
                hideLoading();
                document.getElementById('exportPdfModal').classList.remove('show');
            })
            .catch(error => {
                console.error('Export error:', error);
                hideLoading();
                alert('Gagal export PDF: ' + error.message);
            });
        }
    </script>
</body>
</html>
@endsection 