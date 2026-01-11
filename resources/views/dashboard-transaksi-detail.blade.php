@extends('dashboard')
@section('content')
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Transaksi #{{ $transaction->id }} - Admin Panel</title>
    <link href="https://fonts.googleapis.com/css2?family=Urbanist:wght@400;700;900&display=swap" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Urbanist', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; }
        body { background: #fff; color: #1e293b; }
        .header { padding: 1rem 2rem 0rem 2rem; display: flex; justify-content: space-between; align-items: center; }
        .header h1 { font-size: 22px; font-weight: 700; color: #39746E; }
        .back-button { display: flex; align-items: center; gap: 0.5rem; color: #0FB7A6; text-decoration: none; font-weight: 600; font-size: 14px; }
        .back-button:hover { text-decoration: underline; }
        .container { max-width: 1400px; margin: 1rem 2rem 1rem 2rem; background: #fff; border: 1px solid #E5E6E6; border-radius: 16px; padding: 24px; }

        /* Transaction Header */
        .transaction-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; padding-bottom: 16px; border-bottom: 2px solid #E3F4F1; }
        .transaction-id { font-size: 24px; font-weight: 700; color: #39746E; }
        .status-badge { padding: 8px 16px; border-radius: 8px; font-size: 14px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
        .status-dikonfirmasi { background: #FEF3C7; color: #92400E; border: 1px solid #FDE68A; }
        .status-diproses { background: #DBEAFE; color: #1E40AF; border: 1px solid #93C5FD; }
        .status-dijemput { background: #E8F5E8; color: #166534; border: 1px solid #BBF7D0; }
        .status-selesai { background: #E8F5E8; color: #166534; border: 1px solid #BBF7D0; }
        .status-batal { background: #FEE2E2; color: #991B1B; border: 1px solid #FCA5A5; }

        /* Compact Info Grid */
        .info-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 16px; margin-bottom: 24px; }
        .info-card { background: #F8F9FA; padding: 16px; border-radius: 8px; border: 1px solid #E5E6E6; }
        .info-card h3 { font-size: 16px; font-weight: 700; color: #39746E; margin-bottom: 12px; }
        .info-item { display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px; font-size: 14px; }
        .info-item:last-child { margin-bottom: 0; }
        .info-label { font-weight: 600; color: #6B7271; }
        .info-value { font-weight: 600; color: #1e293b; text-align: right; }

        /* Status Update Section */
        .status-section { background: #F8F9FA; padding: 20px; border-radius: 8px; border: 1px solid #E5E6E6; margin-bottom: 24px; }
        .status-section h3 { font-size: 18px; font-weight: 700; color: #39746E; margin-bottom: 16px; }
        .status-form { display: flex; gap: 12px; align-items: center; }
        .status-select { padding: 8px 12px; border: 1px solid #E5E6E6; border-radius: 6px; font-size: 14px; background: #fff; }
        .btn { padding: 8px 16px; border: none; border-radius: 6px; font-size: 14px; font-weight: 600; cursor: pointer; transition: all 0.2s; }
        .btn-primary { background: #39746E; color: #fff; }
        .btn-primary:hover { background: #2d5a55; }
        .btn-secondary { background: #6B7271; color: #fff; }
        .btn-secondary:hover { background: #5a5f5e; }

        /* Items Section - Main Focus */
        .items-section { background: #fff; border: 1px solid #E5E6E6; border-radius: 8px; overflow: hidden; }
        .items-header { background: #F8F9FA; padding: 16px 20px; border-bottom: 1px solid #E5E6E6; display: flex; justify-content: space-between; align-items: center; }
        .items-header h3 { font-size: 18px; font-weight: 700; color: #39746E; }
        .items-count { background: #39746E; color: #fff; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600; }

        /* Items Table */
        .items-table { width: 100%; border-collapse: collapse; }
        .items-table th { background: #F8F9FA; padding: 12px 16px; text-align: left; font-weight: 600; font-size: 14px; color: #39746E; border-bottom: 1px solid #E5E6E6; }
        .items-table td { padding: 12px 16px; border-bottom: 1px solid #E5E6E6; font-size: 14px; }
        .items-table tr:hover { background: #F8F9FA; }
        .item-name { font-weight: 600; color: #39746E; }
        .item-input { width: 80px; padding: 4px 8px; border: 1px solid #E5E6E6; border-radius: 4px; font-size: 14px; text-align: center; }
        .item-input:focus { outline: none; border-color: #39746E; }
        /* Calculator input wrapper */
        .calc-wrapper { display: flex; gap: 6px; align-items: center; }
        .calc-input { width: 90px; padding: 4px 8px; border: 1px solid #E5E6E6; border-radius: 4px; font-size: 13px; text-align: center; }
        .calc-input:focus { outline: none; border-color: #39746E; }
        .calc-input::placeholder {  font-size: 11px; }
        .item-total { font-weight: 600; color: #0FB7A6; }
        .item-actions { display: flex; gap: 8px; }
        .btn-small { padding: 4px 8px; border: none; border-radius: 4px; font-size: 12px; font-weight: 600; cursor: pointer; }
        .btn-danger { background: #FEE2E2; color: #991B1B; }
        .btn-danger:hover { background: #FCA5A5; }
        .btn-primary { background: #DBEAFE; color: #1E40AF; }
        .btn-primary:hover { background: #93C5FD; }

        /* Summary Section */
        .summary-section { background: #F8F9FA; padding: 20px; border-radius: 8px; border: 1px solid #E5E6E6; margin-top: 24px; }
        .summary-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; }
        .summary-item { text-align: center; }
        .summary-label { font-size: 14px; color: #6B7271; margin-bottom: 4px; }
        .summary-value { font-size: 20px; font-weight: 700; color: #39746E; }

        /* Loading Overlay */
        .loading-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); display: none; justify-content: center; align-items: center; z-index: 9999; }
        .loading-content { background: white; padding: 2rem; border-radius: 8px; text-align: center; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15); }
        .loading-spinner { width: 40px; height: 40px; border: 4px solid #f3f3f3; border-top: 4px solid #00B6A0; border-radius: 50%; animation: spin 1s linear infinite; margin: 0 auto 1rem auto; }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }

        /* Modal Styles */
        .form-group { margin-bottom: 1rem; }
        .form-group label { display: block; margin-bottom: 0.5rem; font-weight: 600; color: #39746E; }
        .form-control { width: 100%; padding: 8px 12px; border: 1px solid #E5E6E6; border-radius: 6px; font-size: 14px; }
        .form-control:focus { outline: none; border-color: #39746E; }
        /* Calculator input in modal */
        #modal_calc { background: #fff; text-align: center; }
        /* Readonly input styling */
        .form-control[readonly] { background: #F8F9FA; cursor: not-allowed; }

        /* ========================================
           MOBILE RESPONSIVE STYLES
           Mobile-first optimization for transaction detail
           ======================================== */
        @media (max-width: 768px) {
            /* --- Container & Layout --- */
            .container {
                margin: 0.5rem;
                padding: 16px;
            }

            .header {
                padding: 1rem;
            }

            .header h1 {
                font-size: 18px;
            }

            /* --- Transaction Header (ID & Status) --- */
            .transaction-header {
                flex-direction: column;
                align-items: flex-start !important;
                gap: 16px;
            }

            .transaction-id {
                font-size: 18px;
            }

            /* --- Status Form (Critical for Mobile UX) --- */
            .status-form {
                width: 100%;
                flex-direction: column;
                gap: 12px;
                align-items: stretch;
            }

            .status-select {
                width: 100%;
                height: 48px;
                font-size: 16px;
                padding: 12px;
                margin-right: 0 !important;
            }

            .status-form .btn {
                width: 100%;
                height: 48px;
                font-size: 16px;
                margin-left: 0 !important;
            }

            #petugasFields {
                flex-direction: column;
                width: 100%;
                gap: 12px;
            }

            #petugasFields input {
                width: 100%;
                height: 48px;
                min-width: unset !important;
            }

            /* --- Summary Section (Compact 2x2 grid) --- */
            .summary-section {
                padding: 16px;
            }

            .summary-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 12px;
            }

            .summary-value {
                font-size: 16px;
            }

            .summary-label {
                font-size: 12px;
            }

            /* --- Items Table → Card Layout --- */
            .items-section {
                margin-bottom: 80px; /* Space for sticky button */
            }

            .items-header {
                flex-direction: column;
                align-items: stretch !important;
                gap: 12px;
            }

            /* Make add button full width on mobile for better touch target */
            .items-header .desktop-add-btn {
                width: 100%;
                justify-content: center;
                display: flex;
                align-items: center;
                height: 48px;
            }

            .items-table {
                display: block;
            }

            .items-table thead {
                display: none; /* Hide table header on mobile */
            }

            .items-table tbody {
                display: block;
            }

            /* Each table row becomes a card */
            .items-table tbody tr {
                display: block;
                margin: 0 12px 12px 12px;
                padding: 16px;
                background: #fff;
                border: 1px solid #E5E6E6;
                border-radius: 12px;
                box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            }

            .items-table tbody tr:last-child {
                margin-bottom: 0;
            }

            /* Table cells become flex rows */
            .items-table td {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 8px 0;
                border: none;
                border-bottom: 1px solid #F0F0F0;
                font-size: 14px;
            }

            .items-table td:last-child {
                border-bottom: none;
                padding-top: 12px;
                justify-content: center;
            }

            /* Mobile data labels via ::before pseudo-element */
            .items-table td::before {
                content: attr(data-label);
                font-weight: 600;
                color: #6B7271;
                font-size: 13px;
                flex-shrink: 0;
                margin-right: 12px;
            }

            /* Item name styling - header of each card */
            .items-table td.item-name {
                font-size: 15px;
                font-weight: 700;
                color: #39746E;
                border-bottom: 2px solid #E3F4F1;
                padding-bottom: 12px;
            }

            .items-table td.item-name::before {
                display: none; /* No label for item name */
            }

            /* Input field dalam card - larger touch target */
            .item-input {
                width: auto;
                min-width: 0;
                height: 36px;
                font-size: 14px;
                text-align: center;
                border-radius: 6px;
            }

            /* Calculator wrapper for mobile - side by side, prevent overflow */
            .calc-wrapper {
                display: flex;
                flex-direction: row;
                gap: 4px;
                width: 100%;
                max-width: 100%;
                box-sizing: border-box;
            }

            .calc-input {
                flex: 1 1 0;
                min-width: 0;
                width: 0;
                height: 36px;
                font-size: 12px;
                padding: 4px 6px;
            }

            .calc-wrapper .item-input {
                flex: 1 1 0;
                min-width: 0;
                width: 0;
            }

            /* Action button dalam card - full width tapi compact */
            .item-actions {
                justify-content: center;
                padding-top: 8px;
            }

            .item-actions::before {
                display: none; /* No label for actions */
            }

            .btn-small {
                width: 100%;
                padding: 8px 16px;
                font-size: 13px;
                border-radius: 6px;
            }

            /* Style header add button for mobile - keep visible but compact */
            .desktop-add-btn {
                padding: 10px 14px;
                font-size: 14px;
                white-space: nowrap;
            }

            /* --- Info Grid (Single Column) --- */
            .info-grid {
                grid-template-columns: 1fr;
                gap: 12px;
            }

            .info-card {
                padding: 14px;
            }

            .info-item {
                flex-direction: column;
                align-items: flex-start;
                gap: 4px;
            }

            .info-value {
                text-align: left;
            }

            /* --- Add Item Modal (Mobile Fullscreen) --- */
            #addItemModal > div {
                margin: 0;
                width: 100%;
                height: 100%;
                max-height: 100%;
                border-radius: 0;
                padding: 1.5rem;
            }

            #addItemModal .form-control {
                height: 48px;
                font-size: 16px;
            }

            #addItemModal .btn {
                height: 48px;
                font-size: 16px;
            }
        }

        /* --- Mobile Sticky Add Button --- */
        .mobile-sticky-add-btn {
            display: none; /* Hidden by default on desktop */
        }

        @media (max-width: 768px) {
            .mobile-sticky-add-btn {
                display: block;
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
                padding: 16px 20px;
                padding-bottom: calc(16px + env(safe-area-inset-bottom));
                background: linear-gradient(to top, #fff 85%, transparent);
                z-index: 100;
            }

            .mobile-sticky-add-btn .btn {
                width: 100%;
                height: 52px;
                font-size: 16px;
                font-weight: 700;
                border-radius: 12px;
                background: #39746E;
                color: #fff;
                box-shadow: 0 4px 12px rgba(57, 116, 110, 0.3);
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
                border: none;
                cursor: pointer;
            }

            .mobile-sticky-add-btn .btn:active {
                transform: scale(0.98);
            }
        }
    </style>
</head>
<body>
    <!-- Loading Overlay -->
    <div class="loading-overlay" id="pageLoadingOverlay">
        <div class="loading-content">
            <div class="loading-spinner"></div>
            <p id="loadingText">Memuat data...</p>
        </div>
    </div>

    <!-- Header -->
    <header class="header">
        <h1>Detail Transaksi</h1>
    </header>

    <div class="container">
        <!-- Transaction Header -->
        <div class="transaction-header" style="display: flex; justify-content: space-between; align-items: center;">
            <div style="display: flex; align-items: center; gap: 16px;">
            <div class="transaction-id">Transaksi #{{ $transaction->id }}</div>
                <span class="status-badge status-{{ $transaction->status }}" style="margin-left: 12px;">{{ ucfirst($transaction->status) }}</span>
        </div>
            @if(in_array($transaction->status, ['dikonfirmasi', 'diproses', 'dijemput']))
            <form class="status-form" id="statusForm" method="POST" action="{{ route('dashboard.transaksi.update-status', $transaction->id) }}" style="margin: 0; display: flex; align-items: center; gap: 8px;">
                @csrf
                @method('PUT')
                <select class="status-select" id="newStatus" name="status" style="margin-right: 8px;">
                    <option value="">Update Status</option>
                    @if($transaction->status === 'dikonfirmasi')
                        <option value="diproses">Diproses</option>
                        <option value="batal">Batal</option>
                    @elseif($transaction->status === 'diproses')
                        <option value="dijemput">Dijemput</option>
                        <option value="batal">Batal</option>
                    @elseif($transaction->status === 'dijemput')
                        <option value="selesai">Selesai</option>
                        <option value="batal">Batal</option>
                    @endif
                </select>
                <div id="petugasFields" style="display:none; gap:8px; align-items:center;">
                    <input type="text" name="petugas_nama" id="petugas_nama" class="form-control" placeholder="Nama Petugas" style="min-width:120px;">
                    <input type="text" name="petugas_contact" id="petugas_contact" class="form-control" placeholder="Kontak Petugas" style="min-width:120px;">
                </div>
                <button type="submit" class="btn" style="background: #39746E; color: #fff; padding: 8px 16px; font-size: 14px; height: 36px; border-radius: 6px; font-weight: 600;" onclick="showLoading('Mengupdate status...')">Update</button>
                <a href="{{ route('dashboard.transaksi') }}" class="btn" style="border: 1.5px solid #F44336; color: #F44336; background: #fff; padding: 8px 18px; font-size: 14px; height: 36px; border-radius: 6px; font-weight: 600; margin-left: 8px;" onclick="showLoading('Kembali ke daftar transaksi...')">Kembali</a>
            </form>
            @elseif($transaction->status === 'selesai')
            <div style="display: flex; align-items: center; gap: 8px;">
                <button type="button" class="btn" style="background: #4CAF50; color: #fff; padding: 8px 18px; font-size: 14px; height: 36px; border-radius: 6px; font-weight: 600; display: flex; align-items: center; gap: 6px;" onclick="exportStrukPdf({{ $transaction->id }})">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="6,9 6,2 18,2 18,9"></polyline>
                        <path d="M6,18H4a2,2 0 0,1 -2,-2V11a2,2 0 0,1 2,-2H20a2,2 0 0,1 2,2v5a2,2 0 0,1 -2,2H18"></path>
                        <polyline points="6,14 6,18 18,18 18,14"></polyline>
                    </svg>
                    Cetak Struk
                </button>
                <a href="{{ route('dashboard.transaksi') }}" class="btn" style="border: 1.5px solid #F44336; color: #F44336; background: #fff; padding: 8px 18px; font-size: 14px; height: 36px; border-radius: 6px; font-weight: 600; text-decoration: none;" onclick="showLoading('Kembali ke daftar transaksi...')">Kembali</a>
                </div>
            @endif
            </div>

        @php
            $items = is_array($transaction->items_json) ? $transaction->items_json : (json_decode($transaction->items_json, true) ?: []);
        @endphp

        <!-- Summary Section -->
        <div class="summary-section" style="margin-bottom: 32px;">
            <div class="summary-grid">
                <div class="summary-item">
                    <div class="summary-label">Total Estimasi Berat</div>
                    <div class="summary-value">{{ number_format(array_sum(array_column($items, 'estimasi_berat')), 1) }} kg</div>
                </div>
                <div class="summary-item">
                    <div class="summary-label">Total Aktual Berat</div>
                    <div class="summary-value">
                        @php
                            $totalAktualBerat = array_sum(array_map(function($item) {
                                return isset($item['aktual_berat']) && $item['aktual_berat'] !== null ? $item['aktual_berat'] : 0;
                            }, $items));
                        @endphp
                        {{ $totalAktualBerat > 0 ? number_format($totalAktualBerat, 1) . ' kg' : '-' }}
                </div>
                </div>
                <div class="summary-item">
                    <div class="summary-label">Total Estimasi</div>
                    <div class="summary-value">Rp {{ number_format($transaction->estimasi_total) }}</div>
                </div>
                <div class="summary-item">
                    <div class="summary-label">Total Aktual</div>
                    <div class="summary-value">
                        {{ $transaction->aktual_total ? 'Rp ' . number_format($transaction->aktual_total) : '-' }}
                </div>
                </div>
                </div>
            </div>

        <div class="section">
            <!-- Items Section - Main Focus -->
            @if($items && count($items) > 0)
            <div class="items-section">
                <div class="items-header" style="display: flex; justify-content: space-between; align-items: center;">
                    <div style="display: flex; align-items: center; gap: 16px;">
                        <h3 style="margin: 0;">Daftar Sampah</h3>
                        <span class="items-count">{{ count($items) }} item</span>
                </div>
                    @if($transaction->status === 'dijemput')
                    {{-- Add button - full width on mobile, inline on desktop --}}
                    <button type="button" class="btn desktop-add-btn" style="background: #39746E; color: #fff; border-radius: 6px; font-weight: 600;" onclick="showLoading('Membuka modal tambah item...'); showAddItemModal(); hideLoading();">
                        + Tambah Item Sampah
                    </button>
                    @endif
                </div>

                <table class="items-table">
                    <thead>
                        <tr>
                            <th>Nama Sampah</th>
                            <th>Estimasi (kg)</th>
                            <th>Harga/kg</th>
                            <th>Estimasi Total</th>
                            @if(in_array($transaction->status, ['dijemput', 'selesai']))
                            <th>Aktual (kg)</th>
                            <th>Aktual Total</th>
                            @endif
                @if($transaction->status === 'dijemput')
                            <th>Aksi</th>
                @endif
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($items as $index => $item)
                        <tr @if(isset($item['status']) && $item['status'] === 'dihapus') style="opacity:0.5; background:#f5f5f5; text-decoration:line-through;" @endif>
                            {{-- Item name - no data-label needed, acts as card header on mobile --}}
                            <td class="item-name">
                                {{ $item['sampah_nama'] ?? 'Sampah ' . ($index + 1) }}
                                @if(isset($item['status']) && $item['status'] === 'ditambah')
                                    <span style="background:#4CAF50; color:white; border-radius:4px; padding:2px 8px; font-size:12px; margin-left:8px;">Ditambah</span>
                                @elseif(isset($item['status']) && $item['status'] === 'dihapus')
                                    <span style="background:#F44336; color:white; border-radius:4px; padding:2px 8px; font-size:12px; margin-left:8px;">Dihapus</span>
                                @endif
                            </td>
                            {{-- Data columns with data-label for mobile card layout --}}
                            <td data-label="Estimasi (kg)">{{ number_format($item['estimasi_berat'] ?? 0, 1) }}</td>
                            <td data-label="Harga/kg">Rp {{ number_format($item['harga_per_satuan'] ?? 0) }}</td>
                            <td data-label="Est. Total" class="item-total">Rp {{ number_format(($item['estimasi_berat'] ?? 0) * ($item['harga_per_satuan'] ?? 0)) }}</td>
                            @if(in_array($transaction->status, ['dijemput', 'selesai']))
                            <td data-label="Aktual (kg)">
                            @if($transaction->status === 'dijemput')
                                <div class="calc-wrapper">
                                    <input type="text" class="calc-input" id="calc-{{ $index }}"
                                           placeholder="cth: 1+2+3"
                                           oninput="calculateExpression({{ $index }}, this.value, {{ $item['harga_per_satuan'] ?? 0 }})"
                                           @if(isset($item['status']) && $item['status'] === 'dihapus') disabled @endif>
                                    <input type="number" class="item-input" name="aktual_berat[{{ $index }}]"
                                           id="aktual-berat-{{ $index }}"
                                           value="{{ $item['aktual_berat'] ?? '' }}"
                                           step="0.1" min="0" readonly
                                           @if(isset($item['status']) && $item['status'] === 'dihapus') disabled @endif>
                                </div>
                            @else
                            {{ number_format($item['aktual_berat'] ?? 0, 1) }}
                        @endif
                            </td>
                            <td data-label="Aktual Total" class="item-total" id="aktual-total-{{ $index }}">
                                @if(isset($item['aktual_berat']) && $item['aktual_berat'] > 0)
                                    Rp {{ number_format($item['aktual_berat'] * ($item['harga_per_satuan'] ?? 0)) }}
                            @else
                                    -
                                @endif
                            </td>
                            @endif
                            @if($transaction->status === 'dijemput')
                            {{-- Action column - no data-label, centered on mobile --}}
                            <td class="item-actions">
                                @if(isset($item['status']) && $item['status'] === 'dihapus')
                                    <button type="button" class="btn-small btn-primary" onclick="showLoading('Membatalkan penghapusan...'); restoreItem({{ $index }}); hideLoading();">Batal</button>
                                @else
                                    <button type="button" class="btn-small btn-danger" onclick="showLoading('Menghapus item...'); removeItem({{ $index }}); hideLoading();">Hapus</button>
                                @endif
                            </td>
                            @endif
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                </div>
                @endif
                </div>

            <!-- Info Grid -->
            <div class="info-grid" style="margin-top: 32px;">
                <!-- User Info -->
                <div class="info-card">
                    <h3>Informasi User</h3>
                <div class="info-item">
                        <span class="info-label">Nama</span>
                        <span class="info-value">{{ $transaction->user_name }}</span>
                </div>
                <div class="info-item">
                        <span class="info-label">Email/Phone</span>
                        <span class="info-value">{{ $transaction->user_identifier }}</span>
                </div>
                <div class="info-item">
                        <span class="info-label">User ID</span>
                        <span class="info-value">#{{ $transaction->user_id }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Label Alamat</span>
                    <span class="info-value">{{ $transaction->address_name ?? '-' }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Detail Alamat</span>
                    <span class="info-value">{{ $transaction->address_full_address ?? '-' }}</span>
                </div>
                @if($transaction->address_phone)
                <div class="info-item">
                    <span class="info-label">Nomor Handphone</span>
                    <span class="info-value">{{ $transaction->address_phone }}</span>
                </div>
                @endif
                @if($transaction->address_is_default)
                <div class="info-item">
                    <span class="info-label">Alamat Default</span>
                    <span class="info-value">Ya</span>
                </div>
                @endif
        </div>

                <!-- Bank Info -->
                <div class="info-card">
                    <h3>Bank Sampah</h3>
            <div class="info-item">
                        <span class="info-label">Nama</span>
                        <span class="info-value">{{ $transaction->bank_sampah_name }}</span>
            </div>
                    @if($transaction->bank_sampah_code)
            <div class="info-item">
                        <span class="info-label">Kode</span>
                        <span class="info-value">{{ $transaction->bank_sampah_code }}</span>
            </div>
                    @endif
            <div class="info-item">
                        <span class="info-label">Alamat</span>
                        <span class="info-value">{{ $transaction->bank_sampah_address }}</span>
            </div>
            <div class="info-item">
                        <span class="info-label">Kontak</span>
                        <span class="info-value">{{ $transaction->bank_sampah_phone ?? '-' }}</span>
            </div>
                    @if($transaction->petugas_nama)
                    <div class="info-item">
                        <span class="info-label">Nama Petugas</span>
                        <span class="info-value">{{ $transaction->petugas_nama }}</span>
        </div>
        @endif
                    @if($transaction->petugas_contact)
                    <div class="info-item">
                        <span class="info-label">Kontak Petugas</span>
                        <span class="info-value">{{ $transaction->petugas_contact }}</span>
            </div>
            @endif
            </div>

                <!-- Transaction Info -->
                <div class="info-card">
                    <h3>Informasi Transaksi</h3>
                    <div class="info-item">
                        <span class="info-label">Status</span>
                        <span class="info-value">{{ ucfirst($transaction->status) }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Tipe Setor</span>
                        <span class="info-value">{{ ucfirst($transaction->tipe_setor) }}</span>
                        </div>
                    <div class="info-item">
                        <span class="info-label">Tipe Layanan</span>
                        <span class="info-value">{{ ucfirst($transaction->tipe_layanan ?? '-') }}</span>
                        </div>
                    <div class="info-item">
                        <span class="info-label">Tanggal Transaksi</span>
                        <span class="info-value">{{ $transaction->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                    @if($transaction->tanggal_penjemputan)
                    <div class="info-item">
                        <span class="info-label">Tanggal Penjemputan</span>
                        <span class="info-value">{{ \Carbon\Carbon::parse($transaction->tanggal_penjemputan)->format('d/m/Y') }}</span>
            </div>
            @endif
                    @if($transaction->waktu_penjemputan)
                    <div class="info-item">
                        <span class="info-label">Waktu Penjemputan</span>
                        <span class="info-value">{{ \Carbon\Carbon::parse($transaction->waktu_penjemputan)->format('H:i') }}</span>
                    </div>
                        @endif
                    @if($transaction->tanggal_selesai)
                    <div class="info-item">
                        <span class="info-label">Tanggal Selesai</span>
                        <span class="info-value">{{ \Carbon\Carbon::parse($transaction->tanggal_selesai)->format('d/m/Y H:i') }}</span>
                    </div>
                        @endif
                    <div class="info-item">
                        <span class="info-label">Estimasi Total</span>
                        <span class="info-value">Rp {{ number_format($transaction->estimasi_total) }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Aktual Total</span>
                        <span class="info-value">{{ $transaction->aktual_total ? 'Rp ' . number_format($transaction->aktual_total) : '-' }}</span>
                </div>
            </div>
                <!-- Foto Sampah Card di dalam info-grid -->
                <div class="info-card full-width" style="grid-column: 1 / -1;">
                    <h3>Foto Sampah</h3>
                    <div style="display: flex; justify-content: center; align-items: center; min-height: 220px;">
                        @if($transaction->foto_sampah)
                            <img src="{{ $transaction->foto_sampah }}" alt="Foto Sampah" style="max-width: 320px; max-height: 200px; border-radius: 12px; border: 1px solid #E5E6E6; background: #F8F9FA; object-fit: cover;" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <div class="no-image-placeholder" style="display:none; width: 160px; height: 130px; background: #F8F9FA; border-radius: 12px; border: 1px solid #E5E6E6; align-items: center; justify-content: center; color: #9CA3AF; font-family: 'Urbanist', sans-serif; font-size: 22px; font-weight: 500; text-align: center;">No Image</div>
        @else
                            <div class="no-image-placeholder" style="width: 160px; height: 130px; background: #F8F9FA; border-radius: 12px; border: 1px solid #E5E6E6; display: flex; align-items: center; justify-content: center; color: #9CA3AF; font-family: 'Urbanist', sans-serif; font-size: 22px; font-weight: 500; text-align: center;">No Image</div>
                @endif
            </div>
        </div>
                </div>
            </div>
    </div>

    <!-- Image Modal -->
    <div id="imageModal" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.9);">
        <span style="position: absolute; top: 15px; right: 35px; color: #f1f1f1; font-size: 40px; font-weight: bold; cursor: pointer;" onclick="closeImageModal()">&times;</span>
        <img id="modalImage" style="margin: auto; display: block; width: 80%; max-width: 700px; margin-top: 50px;">
    </div>

    <!-- Add Item Modal -->
    <div id="addItemModal" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5);">
        <div style="background: white; margin: 5% auto; padding: 2rem; border-radius: 12px; width: 90%; max-width: 500px; max-height: 80vh; overflow-y: auto;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <h3 style="color: #39746E; margin: 0;">Tambah Item Sampah</h3>
                <span style="font-size: 24px; cursor: pointer; color: #6B7271;" onclick="closeAddItemModal()">&times;</span>
            </div>

            <form id="addItemForm">
                <div class="form-group">
                    <label for="sampah_id">Jenis Sampah: <span style="color: #DC2626;">*</span></label>
                    <select name="sampah_id" id="sampah_id" class="form-control" required onchange="updateHarga()">
                        <option value="">Pilih Jenis Sampah</option>
                        @foreach(\App\Models\Sampah::all() as $sampah)
                        @php
                            $price = $sampah->prices()->where('bank_sampah_id', $transaction->bank_sampah_id)->first();
                            $harga = $price ? $price->harga : 0;
                        @endphp
                        <option value="{{ $sampah->id }}" data-harga="{{ $harga }}" data-satuan="{{ $sampah->satuan }}" data-nama="{{ $sampah->nama }}">
                            {{ $sampah->nama }} ({{ $sampah->satuan }})
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="aktual_berat">Aktual (kg): <span style="color: #DC2626;">*</span></label>
                    <div style="display: flex; gap: 8px;">
                        <input type="text" id="modal_calc" class="form-control" placeholder="cth: 1+2+3" oninput="calculateModalExpression(this.value)" style="flex: 2;">
                        <input type="number" name="aktual_berat" id="aktual_berat" class="form-control" step="0.1" required readonly style="flex: 1;">
                    </div>
                </div>

                <div class="form-group">
                    <label for="harga_per_satuan">Harga per Satuan:</label>
                    <input type="number" name="harga_per_satuan" id="harga_per_satuan" class="form-control" readonly>
                </div>

                <div class="form-group">
                    <label for="aktual_total">Total Aktual:</label>
                    <input type="number" name="aktual_total" id="aktual_total" class="form-control" readonly>
                </div>

                <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                    <button type="button" class="btn btn-primary" onclick="addItem()">Tambah Item</button>
                    <button type="button" class="btn" onclick="closeAddItemModal()" style="background: #6B7271; color: white;">Batal</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Mobile Sticky Add Button - Fixed at bottom for easy thumb access --}}
    @if($transaction->status === 'dijemput')
    <div class="mobile-sticky-add-btn">
        <button type="button" class="btn" onclick="showLoading('Membuka modal tambah item...'); showAddItemModal(); hideLoading();">
            + Tambah Item Sampah
        </button>
    </div>
    @endif

    <script>
        // Global variables
        let currentItems = @json($items);
        let deletedItems = [];
        let addedItems = [];

        // Form validation for status update
        document.getElementById('statusForm')?.addEventListener('submit', function(e) {
            const status = document.getElementById('newStatus').value;

            if (!status) {
                e.preventDefault();
                alert('Pilih status terlebih dahulu');
                return false;
            }

            // Validate petugas fields when status is 'dijemput'
            if (status === 'dijemput') {
                const petugasNama = document.getElementById('petugas_nama').value.trim();
                const petugasContact = document.getElementById('petugas_contact').value.trim();

                if (!petugasNama) {
                    e.preventDefault();
                    alert('Nama petugas harus diisi');
                    return false;
                }

                if (!petugasContact) {
                    e.preventDefault();
                    alert('Kontak petugas harus diisi');
                    return false;
                }
            }

            if (status === 'selesai') {
                e.preventDefault();

                // Validate that all non-deleted items have aktual_berat
                const nonDeletedCurrentItems = currentItems.filter(item => item.status !== 'dihapus');
                const itemsWithoutAktualBerat = [];

                // Check current items
                nonDeletedCurrentItems.forEach((item, index) => {
                    if (!item.aktual_berat || item.aktual_berat <= 0) {
                        itemsWithoutAktualBerat.push(`Item ${index + 1}: ${item.sampah_nama}`);
                    }
                });

                // Check added items
                addedItems.forEach((item, index) => {
                    if (!item.aktual_berat || item.aktual_berat <= 0) {
                        itemsWithoutAktualBerat.push(`Item baru: ${item.sampah_nama}`);
                    }
                });

                // Show error if there are items without aktual_berat
                if (itemsWithoutAktualBerat.length > 0) {
                    alert('Mohon isi berat aktual untuk item berikut:\n' + itemsWithoutAktualBerat.join('\n'));
                    return;
                }

                // Prepare all items data
                const allCurrentItems = currentItems.map(item => {
                    if (item.status === 'dihapus') {
                        item.aktual_berat = null;
                        item.aktual_total = null;
                    } else {
                        if (typeof item.aktual_berat === 'undefined' || item.aktual_berat === null || item.aktual_berat <= 0) {
                            alert(`Berat aktual untuk ${item.sampah_nama} harus diisi`);
                            return null;
                        }
                        if (typeof item.aktual_total === 'undefined' || item.aktual_total === null) {
                            item.aktual_total = item.aktual_berat * item.harga_per_satuan;
                        }
                    }
                    if (typeof item.status === 'undefined' || item.status === null) {
                        item.status = '';
                    }
                    return item;
                });

                if (allCurrentItems.includes(null)) return;

                const allAddedItems = addedItems.map(item => {
                    if (typeof item.aktual_berat === 'undefined' || item.aktual_berat === null || item.aktual_berat <= 0) {
                        alert(`Berat aktual untuk ${item.sampah_nama} harus diisi`);
                        return null;
                    }
                    if (typeof item.aktual_total === 'undefined' || item.aktual_total === null) {
                        item.aktual_total = item.aktual_berat * item.harga_per_satuan;
                    }
                    if (typeof item.status === 'undefined' || item.status === null) {
                        item.status = 'ditambah';
                    }
                    return item;
                });

                if (allAddedItems.includes(null)) return;

                // Combine all items and submit
                const allItems = [...allCurrentItems, ...allAddedItems];
                const itemsJson = JSON.stringify(allItems);
                const activeItems = allItems.filter(item => item.status !== 'dihapus');
                const totalAktual = activeItems.reduce((sum, item) => sum + (item.aktual_total || 0), 0);

                // Add hidden fields
                const itemsInput = document.createElement('input');
                itemsInput.type = 'hidden';
                itemsInput.name = 'items_json';
                itemsInput.value = itemsJson;
                this.appendChild(itemsInput);

                const totalInput = document.createElement('input');
                totalInput.type = 'hidden';
                totalInput.name = 'aktual_total';
                totalInput.value = totalAktual;
                this.appendChild(totalInput);

                this.submit();
            }
        });

        // Image modal functions
        function openImageModal(src) {
            document.getElementById('modalImage').src = src;
            document.getElementById('imageModal').style.display = 'block';
        }

        function closeImageModal() {
            document.getElementById('imageModal').style.display = 'none';
        }

        // Close modal when clicking outside
        document.getElementById('imageModal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                closeImageModal();
            }
        });

        // Add Item Modal functions
        function showAddItemModal() {
            document.getElementById('addItemModal').style.display = 'block';
            // Hide sticky button when modal is open
            const stickyBtn = document.querySelector('.mobile-toggle');
            if (stickyBtn) stickyBtn.style.display = 'none';
            updateSampahDropdown();
        }

        function closeAddItemModal() {
            document.getElementById('addItemModal').style.display = 'none';
            document.getElementById('addItemForm').reset();
            // Show sticky button when modal is closed
            const stickyBtn = document.querySelector('.mobile-toggle');
            if (stickyBtn) stickyBtn.style.display = '';

            // Reset dropdown
            const select = document.getElementById('sampah_id');
            for (let i = 1; i < select.options.length; i++) {
                const option = select.options[i];
                option.style.display = 'block';
                option.disabled = false;
            }
        }

        // Close add item modal when clicking outside
        document.getElementById('addItemModal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                closeAddItemModal();
            }
        });

        // Update harga when sampah is selected
        function updateHarga() {
            const select = document.getElementById('sampah_id');
            const selectedOption = select.options[select.selectedIndex];
            const hargaInput = document.getElementById('harga_per_satuan');

            if (selectedOption.value) {
                hargaInput.value = selectedOption.getAttribute('data-harga');
                calculateTotal();
            } else {
                hargaInput.value = '';
                document.getElementById('aktual_total').value = '';
            }
        }

        // Update dropdown to hide existing sampah
        function updateSampahDropdown() {
            const select = document.getElementById('sampah_id');
            const currentValue = select.value;

            const existingSampahIds = new Set();

            currentItems.forEach(item => {
                if (item.status !== 'dihapus') {
                    existingSampahIds.add(item.sampah_id);
                }
            });

            addedItems.forEach(item => {
                existingSampahIds.add(item.sampah_id);
            });

            for (let i = 1; i < select.options.length; i++) {
                const option = select.options[i];
                const sampahId = parseInt(option.value);

                if (existingSampahIds.has(sampahId)) {
                    option.style.display = 'none';
                    option.disabled = true;
                } else {
                    option.style.display = 'block';
                    option.disabled = false;
                }
            }

            if (currentValue && existingSampahIds.has(parseInt(currentValue))) {
                select.value = '';
                updateHarga();
            }
        }

        // Calculate expression from modal calculator input
        function calculateModalExpression(expression) {
            if (!expression || expression.trim() === '') {
                return;
            }

            try {
                // Only allow numbers and basic operators (+, -, *, /)
                const sanitized = expression.replace(/[^0-9+\-*/.]/g, '');
                if (sanitized === '') return;

                // Evaluate the expression safely
                const result = Function('"use strict"; return (' + sanitized + ')')();

                if (!isNaN(result) && isFinite(result)) {
                    const rounded = Math.round(result * 10) / 10; // Round to 1 decimal
                    const beratInput = document.getElementById('aktual_berat');
                    if (beratInput) {
                        beratInput.value = rounded;
                        // Trigger the calculateTotal function
                        calculateTotal();
                    }
                }
            } catch (e) {
                // Invalid expression, ignore
            }
        }

        // Calculate total when berat is changed
        function calculateTotal() {
            const berat = parseFloat(document.getElementById('aktual_berat').value) || 0;
            const harga = parseFloat(document.getElementById('harga_per_satuan').value) || 0;
            const total = berat * harga;
            document.getElementById('aktual_total').value = total.toFixed(2);
        }

        // Add new item
        function addItem() {
            const sampahSelect = document.getElementById('sampah_id');
            const selectedOption = sampahSelect.options[sampahSelect.selectedIndex];
            const aktualBerat = parseFloat(document.getElementById('aktual_berat').value);
            const hargaPerSatuan = parseFloat(document.getElementById('harga_per_satuan').value);
            const aktualTotal = parseFloat(document.getElementById('aktual_total').value);

            if (!sampahSelect.value || !aktualBerat) {
                alert('Mohon isi semua field yang diperlukan');
                return;
            }

            const selectedSampahId = parseInt(sampahSelect.value);
            const selectedSampahNama = selectedOption.getAttribute('data-nama');

            // Check if sampah already exists
            const existingCurrentItem = currentItems.find(item =>
                item.sampah_id === selectedSampahId && item.status !== 'dihapus'
            );

            const existingAddedItem = addedItems.find(item =>
                item.sampah_id === selectedSampahId
            );

            if (existingCurrentItem) {
                alert(`Sampah "${selectedSampahNama}" sudah ada dalam daftar.`);
                return;
            }

            if (existingAddedItem) {
                alert(`Sampah "${selectedSampahNama}" sudah ditambahkan sebelumnya.`);
                return;
            }

            const newItem = {
                sampah_id: selectedSampahId,
                sampah_nama: selectedSampahNama,
                sampah_satuan: selectedOption.getAttribute('data-satuan'),
                estimasi_berat: 0,
                harga_per_satuan: hargaPerSatuan,
                aktual_berat: aktualBerat,
                aktual_total: aktualTotal,
                status: 'ditambah'
            };

            addedItems.push(newItem);

            // Add new row to table
            addTableRow(newItem, currentItems.length + addedItems.length - 1);

            closeAddItemModal();
            updateSummary();
            updateSampahDropdown();
        }

        // Add new row to table (with data-label attributes for mobile card layout)
        function addTableRow(item, index) {
            const tbody = document.querySelector('.items-table tbody');
            const newRow = document.createElement('tr');

            // Build row HTML with data-label attributes for mobile responsive
            newRow.innerHTML = `
                <td class="item-name">${item.sampah_nama} <span style="background: #4CAF50; color: white; border-radius: 4px; padding: 2px 8px; font-size: 12px; margin-left: 8px;">Ditambah</span></td>
                <td data-label="Estimasi (kg)">0.0</td>
                <td data-label="Harga/kg">Rp ${number_format(item.harga_per_satuan)}</td>
                <td data-label="Est. Total" class="item-total">Rp 0</td>
                <td data-label="Aktual (kg)">
                    <div class="calc-wrapper">
                        <input type="text" class="calc-input" id="calc-${index}"
                               placeholder="cth: 1+2+3"
                               oninput="calculateExpression(${index}, this.value, ${item.harga_per_satuan})">
                        <input type="number" class="item-input" name="aktual_berat[${index}]"
                               id="aktual-berat-${index}"
                               value="${item.aktual_berat || ''}"
                               step="0.1" min="0" readonly>
                    </div>
                </td>
                <td data-label="Aktual Total" class="item-total" id="aktual-total-${index}">
                    ${item.aktual_total ? 'Rp ' + number_format(item.aktual_total) : '-'}
                </td>
                <td class="item-actions">
                    <button type="button" class="btn-small btn-danger" onclick="showLoading('Menghapus item...'); removeAddedItem(${index}); hideLoading();">Hapus</button>
                </td>
            `;

            tbody.appendChild(newRow);
        }

        // Remove added item
        function removeAddedItem(index) {
            if (confirm('Apakah Anda yakin ingin menghapus item ini?')) {
                showLoading('Menghapus item...');
                    const addedIndex = index - currentItems.length;
                    addedItems.splice(addedIndex, 1);

                // Remove row from table
                const tbody = document.querySelector('.items-table tbody');
                const rows = tbody.querySelectorAll('tr');
                if (rows[index]) {
                    rows[index].remove();
            }

            updateSummary();
            updateSampahDropdown();
            hideLoading();
            }
        }

        // Remove item (for existing items)
        function removeItem(index) {
            if (confirm('Apakah Anda yakin ingin menghapus item ini?')) {
                showLoading('Menghapus item...');
                currentItems[index].status = 'dihapus';
                currentItems[index].aktual_berat = null;
                currentItems[index].aktual_total = null;
                deletedItems.push(index);

                // Update row appearance
                const tbody = document.querySelector('.items-table tbody');
                const row = tbody.querySelectorAll('tr')[index];
                if (row) {
                    row.style.opacity = '0.5';
                    row.style.backgroundColor = '#f5f5f5';

                    // Update button
                    const button = row.querySelector('.btn-danger');
                    if (button) {
                button.textContent = 'Batal';
                        button.className = 'btn-small btn-primary';
                button.onclick = () => { showLoading('Membatalkan penghapusan...'); restoreItem(index); hideLoading(); };
                    }

                    // Disable input
                    const input = row.querySelector('.item-input');
                    if (input) {
                        input.disabled = true;
                        input.value = '';
                }

                    // Update total display
                    const totalCell = row.querySelector('#aktual-total-' + index);
                    if (totalCell) {
                        totalCell.textContent = '-';
                }
            }

                updateSummary();
                updateSampahDropdown();
                hideLoading();
            }
        }

        // Restore item
        function restoreItem(index) {
            showLoading('Membatalkan penghapusan...');
            const deletedIndex = deletedItems.indexOf(index);
            if (deletedIndex > -1) {
                deletedItems.splice(deletedIndex, 1);
            }
            currentItems[index].status = '';

            // Update row appearance
            const tbody = document.querySelector('.items-table tbody');
            const row = tbody.querySelectorAll('tr')[index];
            if (row) {
                row.style.opacity = '1';
                row.style.backgroundColor = '';

                // Update button
                const button = row.querySelector('.btn-primary');
                if (button) {
                    button.textContent = 'Hapus';
                    button.className = 'btn-small btn-danger';
                    button.onclick = () => { showLoading('Menghapus item...'); removeItem(index); hideLoading(); };
                }

                // Enable input
                const input = row.querySelector('.item-input');
                if (input) {
                    input.disabled = false;
                }
            }

            updateSummary();
            updateSampahDropdown();
        }

        // Calculate expression from calculator input (e.g., "1+2+3" = 6)
        function calculateExpression(index, expression, harga) {
            if (!expression || expression.trim() === '') {
                return;
            }

            try {
                // Only allow numbers and basic operators (+, -, *, /)
                const sanitized = expression.replace(/[^0-9+\-*/.]/g, '');
                if (sanitized === '') return;

                // Evaluate the expression safely
                const result = Function('"use strict"; return (' + sanitized + ')')();

                if (!isNaN(result) && isFinite(result)) {
                    const rounded = Math.round(result * 10) / 10; // Round to 1 decimal
                    const beratInput = document.getElementById('aktual-berat-' + index);
                    if (beratInput) {
                        beratInput.value = rounded;
                        // Trigger the updateItemTotal function
                        updateItemTotal(index, rounded, harga);
                    }
                }
            } catch (e) {
                // Invalid expression, ignore
            }
        }

        // Update item total when weight changes
        function updateItemTotal(index, berat, harga) {
            const beratValue = parseFloat(berat) || 0;
            const total = beratValue * harga;

            if (index < currentItems.length) {
                currentItems[index].aktual_berat = beratValue;
                currentItems[index].aktual_total = total;
            } else {
                const addedIndex = index - currentItems.length;
                addedItems[addedIndex].aktual_berat = beratValue;
                addedItems[addedIndex].aktual_total = total;
            }

            // Update total display
            const totalCell = document.getElementById('aktual-total-' + index);
            if (totalCell) {
                totalCell.textContent = total > 0 ? 'Rp ' + number_format(total) : '-';
            }

            updateSummary();
        }

        // Update summary
        function updateSummary() {
            const allItems = [...currentItems.filter(item => item.status !== 'dihapus'), ...addedItems];

            const totalEstimasiBerat = allItems.reduce((sum, item) => sum + (item.estimasi_berat || 0), 0);
            const totalAktualBerat = allItems.reduce((sum, item) => sum + (item.aktual_berat || 0), 0);
            const totalEstimasi = allItems.reduce((sum, item) => sum + ((item.estimasi_berat || 0) * item.harga_per_satuan), 0);
            const totalAktual = allItems.reduce((sum, item) => sum + (item.aktual_total || 0), 0);

            // Update summary display
            const summarySection = document.querySelector('.summary-section');
            if (summarySection) {
                const summaryGrid = summarySection.querySelector('.summary-grid');
                if (summaryGrid) {
                    summaryGrid.innerHTML = `
                        <div class="summary-item">
                            <div class="summary-label">Total Estimasi Berat</div>
                            <div class="summary-value">${number_format(totalEstimasiBerat, 1)} kg</div>
                    </div>
                        <div class="summary-item">
                            <div class="summary-label">Total Aktual Berat</div>
                            <div class="summary-value">${totalAktualBerat > 0 ? number_format(totalAktualBerat, 1) + ' kg' : '-'}</div>
                    </div>
                        <div class="summary-item">
                            <div class="summary-label">Total Estimasi</div>
                            <div class="summary-value">Rp ${number_format(totalEstimasi)}</div>
                    </div>
                        <div class="summary-item">
                            <div class="summary-label">Total Aktual</div>
                            <div class="summary-value">${totalAktual > 0 ? 'Rp ' + number_format(totalAktual) : '-'}</div>
                    </div>
                `;
            }
        }
        }

        // Loading utility functions
        function showLoading(message = 'Memuat data...') {
            const overlay = document.getElementById('pageLoadingOverlay');
            const loadingText = document.getElementById('loadingText');
            loadingText.textContent = message;
            overlay.style.display = 'flex';
        }

        function hideLoading() {
            const overlay = document.getElementById('pageLoadingOverlay');
            overlay.style.display = 'none';
        }

        // Show loading on initial page load
        document.addEventListener('DOMContentLoaded', function() {
            showLoading('Memuat detail transaksi...');

            // Hide loading after a short delay to simulate loading
            setTimeout(() => {
                hideLoading();
            }, 500);

            // Handle status change to show/hide petugas fields
            const statusSelect = document.getElementById('newStatus');
            if (statusSelect) {
                statusSelect.addEventListener('change', function() {
                    const petugasFields = document.getElementById('petugasFields');
                    if (this.value === 'dijemput') {
                        petugasFields.style.display = 'flex';
                    } else {
                        petugasFields.style.display = 'none';
                    }
                });
            }
        });

        // Number format helper
        function number_format(number, decimals = 0) {
            return new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: decimals,
                maximumFractionDigits: decimals
            }).format(number);
        }

        function exportStrukPdf(transaksiId) {
            showLoading('Mengexport PDF...');
            fetch(`/dashboard/transaksi/${transaksiId}/print-struk`)
            .then(response => {
                if (response.ok) return response.blob();
                throw new Error('Export gagal');
            })
            .then(blob => {
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = `struk_transaksi_${transaksiId}.pdf`;
                document.body.appendChild(a);
                a.click();
                window.URL.revokeObjectURL(url);
                document.body.removeChild(a);
                hideLoading();
            })
            .catch(() => {
                hideLoading();
                alert('Export PDF gagal!');
            });
        }
    </script>
</body>
</html>
@endsection
