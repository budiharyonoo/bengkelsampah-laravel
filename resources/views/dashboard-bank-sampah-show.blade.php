@extends('dashboard')
@section('content')
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Detail Bank Sampah - Admin Panel</title>
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
        .kode-badge {
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
        }


            margin-left: 6px;
            border: 1.5px solid;
            background: transparent;
        }
        .status-jemput { color: #059669; border-color: #059669; }
        .status-tempat { color: #DC2626; border-color: #DC2626; }
        .status-keduanya { color: #7C3AED; border-color: #7C3AED; }
        .status-selesai { color: #059669; border-color: #059669; }
        .status-diproses { color: #d97706; border-color: #d97706; }
        .status-dikonfirmasi { color: #1d4ed8; border-color: #1d4ed8; }
        .status-batal { color: #dc2626; border-color: #dc2626; }
        .status-dijemput { color: #059669; border-color: #059669; }
        .status-pending { color: #f59e42; border-color: #f59e42; }
        .status-default { color: #6B7280; border-color: #6B7280; }
        .recent-list, .admin-list {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }
        .recent-item, .admin-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem;
            background: #F8F9FA;
            border-radius: 8px;
            border: 1px solid #E5E6E6;
        }
        .recent-user, .admin-name {
            font-size: 15px;
            font-weight: 600;
            color: #242E2C;
        }
        .recent-details, .admin-email {
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
        .info-value img {
            max-width: 300px;
            max-height: 200px;
            border-radius: 8px;
            border: 1px solid #E5E6E6;
        }
        .info-value .placeholder-img {
            width: 300px;
            height: 200px;
            background: #F3F4F6;
            border-radius: 8px;
            border: 1px solid #E5E6E6;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #B0B0B0;
            font-size: 16px;
        }
        @media (max-width: 768px) {
            .info-grid {
                grid-template-columns: 1fr;
            }
            .stats-row {
                grid-template-columns: 1fr;
            }
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
            grid-template-columns: repeat(5, 1fr);
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
        .btn-add-admin {
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
            margin-bottom: 1rem;
            float: right;
        }
        .btn-add-admin:hover {
            background: #2d5a55;
        }
        .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }
        .admin-actions {
            display: flex;
            gap: 8px;
            align-items: center;
        }
        .btn-admin-edit, .btn-admin-delete {
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: background 0.2s;
        }
        .btn-admin-edit {
            background: #E3F4F1;
            color: #39746E;
        }
        .btn-admin-edit:hover {
            background: #B6E2DB;
        }
        .btn-admin-delete {
            background: #FDCED1;
            color: #DC2626;
        }
        .btn-admin-delete:hover {
            background: #F73541;
            color: #fff;
        }
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            animation: fadeIn 0.3s ease;
        }
        .modal.show, .modal.active {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        .modal-content {
            background: white;
            border-radius: 20px;
            padding: 16px;
            max-width: 500px;
            width: 90%;
            text-align: center;
            position: relative;
            animation: slideIn 0.3s ease;
        }
        @keyframes slideIn {
            from { transform: translateY(-50px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        .modal-icon {
            width: 46px;
            height: 46px;
            margin: 20px auto 12px;
            display: block;
        }
        .modal-title {
            font-family: 'Urbanist', sans-serif;
            font-size: 18px;
            font-weight: 700;
            color: #242E2C;
            margin-bottom: 4px;
        }
        .modal-subtitle {
            font-family: 'Urbanist', sans-serif;
            font-size: 14px;
            font-weight: 400;
            color: #6B7271;
            margin-bottom: 20px;
        }
        .modal-close {
            position: absolute;
            top: 16px;
            right: 20px;
            background: none;
            border: none;
            width: 18px;
            height: 18px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 25%;
            transition: background-color 0.2s ease;
        }
        .modal-close:hover {
            background-color: #f3f4f6;
        }
        .modal-close img {
            width: 18px;
            height: 18px;
        }
        .modal-buttons {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .modal-button {
            width: 100%;
            padding: 8px;
            border-radius: 8px;
            font-family: 'Urbanist', sans-serif;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
        }
        .cancel-button {
            background: transparent;
            border: 1px solid #FDCED1;
            color: #F73541;
        }
        .confirm-button {
            background: #39746E;
            border: none;
            color: white;
        }
        /* Modal form styles (match artikel create) */
        .form-group {
            margin-bottom: 1rem;
        }
        .form-label {
            font-size: 14px;
            color: #6B7271;
            margin-bottom: 4px;
            display: block;
            font-family: 'Urbanist', sans-serif;
        }
        .form-input {
            width: 100%;
            padding: 12px;
            border: 1px solid #E5E6E6;
            border-radius: 8px;
            font-family: 'Urbanist', sans-serif;
            font-size: 14px;
            color: #1e293b;
            background: #fff;
            transition: all 0.2s;
        }
        .form-input:focus {
            outline: none;
            border-color: #39746E;
        }
        .form-input::placeholder {
            color: #6B7271;
        }
        .modal-form-group {
            margin-bottom: 1rem;
            text-align: left;
        }
        .modal-form-label {
            font-size: 14px;
            color: #6B7271;
            margin-bottom: 4px;
            display: block;
            font-family: 'Urbanist', sans-serif;
        }
        .modal-form-input {
            width: 100%;
            padding: 12px;
            border: 1px solid #E5E6E6;
            border-radius: 8px;
            font-family: 'Urbanist', sans-serif;
            font-size: 14px;
            color: #1e293b;
            background: #fff;
            transition: all 0.2s;
        }
        .modal-form-input:focus {
            outline: none;
            border-color: #39746E;
        }
        .modal-form-input::placeholder {
            color: #6B7271;
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
            <h1>Bank Sampah</h1>
            <span class="header-separator">/</span>
            <span class="header-subtitle">Detail Bank Sampah</span>
        </div>
        <div class="user-info">
            <div class="notification"></div>
            <span class="user-name">{{ Auth::guard('admin')->user()->role ?? 'Admin' }}</span>
            <div class="user-avatar">{{ strtoupper(substr(Auth::guard('admin')->user()->name ?? 'A', 0, 2)) }}</div>
        </div>
    </header>
    <div class="main-container">
        <div style="width: 100%;">
            <div class="stats-row">
                <div class="stat-card">
                    <div class="stat-title">Total Setoran</div>
                    <div class="stat-value">{{ number_format($stats['total_setoran']) }}</div>
                    <div class="stat-desc">{{ number_format($stats['completion_rate'], 1) }}% completion rate</div>
                </div>
                <div class="stat-card">
                    <div class="stat-title">Pendapatan Total</div>
                    <div class="stat-value">Rp {{ number_format($stats['total_revenue']) }}</div>
                    <div class="stat-desc">Avg: Rp {{ number_format($stats['avg_setoran_value']) }}</div>
                </div>
                <div class="stat-card">
                    <div class="stat-title">User Terdaftar</div>
                    <div class="stat-value">{{ number_format($stats['registered_customers']) }}</div>
                    <div class="stat-desc">Registered users</div>
                </div>
                <div class="stat-card">
                    <div class="stat-title">User Transaksi</div>
                    <div class="stat-value">{{ number_format($stats['unique_transaction_users']) }}</div>
                    <div class="stat-desc">Active customers</div>
                </div>
                <div class="stat-card">
                    <div class="stat-title">Total Poin</div>
                    <div class="stat-value">{{ number_format($stats['total_points']) }}</div>
                    <div class="stat-desc">Points distributed</div>
                </div>
            </div>
            <div class="detail-card">
            <div class="info-section">
                <h3 class="info-title">Data Bank Sampah</h3>
                <div class="info-grid">
                    <div class="info-item">
                        <span class="info-label">Kode Bank Sampah</span>
                        <div class="info-value">
                            <span class="kode-badge">{{ $bankSampah->kode_bank_sampah }}</span>
                        </div>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Nama Bank Sampah</span>
                        <div class="info-value">{{ $bankSampah->nama_bank_sampah }}</div>
                    </div>
                    <div class="info-item full-width">
                        <span class="info-label">Alamat Bank Sampah</span>
                        <div class="info-value">{{ $bankSampah->alamat_bank_sampah }}</div>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Nama Penanggung Jawab</span>
                        <div class="info-value">{{ $bankSampah->nama_penanggung_jawab }}</div>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Kontak Penanggung Jawab</span>
                        <div class="info-value">{{ $bankSampah->kontak_penanggung_jawab }}</div>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Tipe Layanan</span>
                        <div class="info-value">
                            @if($bankSampah->tipe_layanan == 'jemput')
                                    <span class="status-badge status-jemput">Jemput Saja</span>
                            @elseif($bankSampah->tipe_layanan == 'tempat')
                                    <span class="status-badge status-tempat">Tempat Saja</span>
                            @else
                                    <span class="status-badge status-keduanya">Keduanya</span>
                            @endif
                        </div>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Google Maps</span>
                        <div class="info-value">
                            @if($bankSampah->gmaps_link)
                                <a href="{{ $bankSampah->gmaps_link }}" target="_blank" style="color: #39746E; text-decoration: underline; font-weight: 600;">
                                    Lihat Maps
                                </a>
                            @else
                                <span style="color: #9ca3af;">Tidak ada link maps</span>
                            @endif
                        </div>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Latitude</span>
                        <div class="info-value">
                            @if($bankSampah->latitude)
                                <span style="color: #374151;">{{ $bankSampah->latitude }}</span>
                            @else
                                <span style="color: #9ca3af;">-</span>
                            @endif
                        </div>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Longitude</span>
                        <div class="info-value">
                            @if($bankSampah->longitude)
                                <span style="color: #374151;">{{ $bankSampah->longitude }}</span>
                            @else
                                <span style="color: #9ca3af;">-</span>
                            @endif
                        </div>
                    </div>
                        <div class="info-item full-width">
                            <span class="info-label">Foto Bank Sampah</span>
                            <div class="info-value" style="padding: 0; background: none; border: none;">
                                @if($bankSampah->foto)
                                    <img src="{{ $bankSampah->foto }}" alt="Foto Bank Sampah">
                                @else
                                    <div class="placeholder-img">Tidak ada foto</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="detail-card">
                <div class="info-section">
                    <h3 class="info-title">Setoran Terbaru</h3>
                    <div class="recent-list">
                        @forelse($recentSetoran as $setoran)
                        <div class="recent-item">
                            <div>
                                <div class="recent-user">{{ $setoran->user_name ?? 'User' }}</div>
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
                    <div class="admin-header">
                        <h3 class="info-title">Akun Admin</h3>
                        <button class="btn-add-admin">+ Tambah Admin</button>
                    </div>
                    <div class="admin-list">
                        @php
                            $admins = \App\Models\Admin::where('id_bank_sampah', $bankSampah->id)->get();
                        @endphp
                        @forelse($admins as $admin)
                        <div class="admin-item">
                            <div>
                                <div class="admin-name">{{ $admin->name }}</div>
                                <div class="admin-email">{{ $admin->email }}</div>
                            </div>
                            <div class="admin-role">{{ ucfirst($admin->role) }}</div>
                            <div class="admin-actions">
                                <button class="btn-admin-edit" data-admin-id="{{ $admin->id }}">Edit</button>
                                <button class="btn-admin-delete" data-admin-id="{{ $admin->id }}">Hapus</button>
                            </div>
                        </div>
                        @empty
                        <div class="empty-state">
                            <div class="empty-state-icon">👤</div>
                            <div style="font-weight: 600; margin-bottom: 0.5rem;">Belum ada admin</div>
                            <div style="font-size: 14px;">Admin untuk bank sampah ini akan muncul di sini</div>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Create/Edit Admin -->
    <div class="modal" id="adminModal">
        <div class="modal-content">
            <button class="modal-close" onclick="closeAdminModal()">
                <img src="{{ asset('icon/ic_close.svg') }}" alt="Close">
            </button>
            <img src="{{ asset('icon/ic_dialog_add.svg') }}" alt="Add" class="modal-icon" id="adminModalIcon">
            <h2 class="modal-title" id="adminModalTitle">Tambah Admin</h2>
            <p class="modal-subtitle" id="adminModalSubtitle">Masukkan data admin baru</p>
            <form id="adminForm">
                <input type="hidden" name="admin_id" id="admin_id">
                <div class="form-group" style="margin: 20px 0;">
                    <input class="form-input" type="text" id="admin_name" name="name" placeholder="Masukkan nama admin" required>
                </div>
                <div class="form-group" style="margin: 20px 0;">
                    <div style="position: relative;">
                        <input class="form-input" type="text" id="admin_email" name="email" placeholder="Masukkan username" required style="padding-right: 140px;">
                        <span style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); color: #6B7271; font-size: 14px; pointer-events: none;">@bengkelsampah.com</span>
                    </div>
                </div>
                <div class="form-group" style="margin: 20px 0;">
                    <input class="form-input" type="password" id="admin_password" name="password" placeholder="Masukkan password" autocomplete="new-password">
                    <div id="adminPasswordNote" style="color:#6B7271;font-size:12px;margin-top:4px;"></div>
                </div>
                <div class="modal-buttons">
                    <button type="button" class="modal-button cancel-button" onclick="closeAdminModal()">Batal</button>
                    <button type="submit" class="modal-button confirm-button">Simpan</button>
                </div>
            </form>
        </div>
    </div>
    <!-- Modal Delete Admin -->
    <div class="modal" id="deleteAdminModal">
        <div class="modal-content">
            <button class="modal-close" onclick="closeDeleteAdminModal()">
                <img src="{{ asset('icon/ic_close.svg') }}" alt="Close">
            </button>
            <img src="{{ asset('icon/ic_dialog_delete.svg') }}" alt="Delete" class="modal-icon">
            <h2 class="modal-title">Hapus Admin</h2>
            <p class="modal-subtitle" id="deleteAdminText">Apakah Anda yakin ingin menghapus admin ini?</p>
            <div class="modal-buttons">
                <button type="button" class="modal-button cancel-button" onclick="closeDeleteAdminModal()">Batal</button>
                <button type="button" class="modal-button confirm-button" id="confirmDeleteAdminBtn">Hapus</button>
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

        // Modal logic (open/close, fill form, etc)
        function openAdminModal(isEdit = false, admin = null) {
            document.getElementById('adminModal').classList.add('show');
            document.getElementById('admin_id').value = '';
            document.getElementById('admin_name').value = '';
            document.getElementById('admin_email').value = '';
            document.getElementById('admin_password').value = '';
            document.getElementById('adminPasswordNote').textContent = isEdit ? '(Kosongkan jika tidak ingin mengubah password)' : '';
            document.getElementById('adminModalTitle').textContent = isEdit ? 'Edit Admin' : 'Tambah Admin';
            document.getElementById('adminModalSubtitle').textContent = isEdit ? 'Edit data admin' : 'Masukkan data admin baru';

            if (isEdit && admin) {
                document.getElementById('admin_id').value = admin.id;
                document.getElementById('admin_name').value = admin.name;
                // Extract username from email (remove @bengkelsampah.com)
                const username = admin.email.replace('@bengkelsampah.com', '');
                document.getElementById('admin_email').value = username;
                document.getElementById('admin_password').value = '';
            }
        }

        function closeAdminModal() {
            document.getElementById('adminModal').classList.remove('show');
        }

        function openDeleteAdminModal(adminId, adminName) {
            document.getElementById('deleteAdminModal').classList.add('show');
            document.getElementById('deleteAdminText').textContent = `Apakah Anda yakin ingin menghapus admin "${adminName}"?`;
            document.getElementById('confirmDeleteAdminBtn').onclick = () => confirmDeleteAdmin(adminId);
        }

        function closeDeleteAdminModal() {
            document.getElementById('deleteAdminModal').classList.remove('show');
        }

        function confirmDeleteAdmin(adminId) {
            // Show loading
            showLoading('Menghapus admin...');

            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            fetch(`/dashboard/admin/${adminId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Reload page to refresh admin list
                    location.reload();
                } else {
                    alert(data.message || 'Gagal menghapus admin');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Gagal menghapus admin: ' + error.message);
            })
            .finally(() => {
                closeDeleteAdminModal();
                hideLoading();
            });
        }

        // Handle email input to prevent @ symbol and ensure proper format
        document.addEventListener('DOMContentLoaded', function() {
            const emailInput = document.getElementById('admin_email');
            if (emailInput) {
                emailInput.addEventListener('input', function(e) {
                    // Remove @ symbol if user tries to type it
                    this.value = this.value.replace('@', '');
                });
            }

            // Handle form submission
            const adminForm = document.getElementById('adminForm');
            if (adminForm) {
                adminForm.addEventListener('submit', function(e) {
                    e.preventDefault();

                    const adminId = document.getElementById('admin_id').value;
                    const name = document.getElementById('admin_name').value;
                    const username = document.getElementById('admin_email').value;
                    const password = document.getElementById('admin_password').value;

                    // Validate required fields
                    if (!name.trim()) {
                        alert('Nama admin harus diisi');
                        return;
                    }
                    if (!username.trim()) {
                        alert('Username harus diisi');
                        return;
                    }
                    if (!adminId && !password.trim()) {
                        alert('Password harus diisi untuk admin baru');
                        return;
                    }

                    // Combine username with domain
                    const email = username + '@bengkelsampah.com';

                    // Show loading
                    showLoading('Menyimpan admin...');

                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    const isEdit = adminId !== '';

                    const url = isEdit ? `/dashboard/admin/${adminId}` : '/dashboard/admin';
                    const method = isEdit ? 'PUT' : 'POST';

                    const formData = {
                        name: name,
                        email: email,
                        id_bank_sampah: {{ $bankSampah->id }}
                    };

                    if (password.trim()) {
                        formData.password = password;
                    }

                    fetch(url, {
                        method: method,
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify(formData)
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            closeAdminModal();
                            // Reload page to refresh admin list
                            location.reload();
                        } else {
                            if (data.errors) {
                                const errorMessages = Object.values(data.errors).flat().join('\n');
                                alert('Validasi gagal:\n' + errorMessages);
                            } else {
                                alert(data.message || 'Gagal menyimpan admin');
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Gagal menyimpan admin: ' + error.message);
                    })
                    .finally(() => {
                        hideLoading();
                    });
                });
            }

            // Button event listeners
            document.querySelector('.btn-add-admin').addEventListener('click', function() {
                openAdminModal(false);
            });

            // Edit admin buttons
            document.querySelectorAll('.btn-admin-edit').forEach(btn => {
                btn.addEventListener('click', function() {
                    const adminId = this.getAttribute('data-admin-id');
                    const adminName = this.closest('.admin-item').querySelector('.admin-name').textContent;
                    const adminEmail = this.closest('.admin-item').querySelector('.admin-email').textContent;

                    const admin = {
                        id: adminId,
                        name: adminName,
                        email: adminEmail
                    };

                    openAdminModal(true, admin);
                });
            });

            // Delete admin buttons
            document.querySelectorAll('.btn-admin-delete').forEach(btn => {
                btn.addEventListener('click', function() {
                    const adminId = this.getAttribute('data-admin-id');
                    const adminName = this.closest('.admin-item').querySelector('.admin-name').textContent;

                    openDeleteAdminModal(adminId, adminName);
                });
            });
        });
    </script>
</body>
</html>
@endsection
