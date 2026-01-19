@extends('dashboard')
@section('content')
    <!DOCTYPE html>
    <html lang="id">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>{{ $pageTitle ?? 'Transaksi Sampah' }} - Admin Panel</title>
        <link href="https://fonts.googleapis.com/css2?family=Urbanist:wght@400;700;900&display=swap" rel="stylesheet">
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
                flex-wrap: wrap;
            }

            .search-filter {
                display: flex;
                gap: 1rem;
                flex: 1;
                flex-wrap: wrap;
                align-items: center;
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
                padding: 6px 36px 6px 14px;
                border-radius: 18px;
            }

            .search-input::placeholder {
                color: #6B7271;
                font-size: 15px;
                font-weight: 400;
                opacity: 1;
            }

            .search-box:focus-within {
                background: #fff;
                border-color: #0FB7A6;
            }

            .search-input:focus {
                background: #fff;
            }

            .search-icon {
                position: absolute;
                right: 12px;
                top: 50%;
                transform: translateY(-50%);
                width: 20px;
                height: 20px;
                pointer-events: none;
            }

            .filter-dropdown {
                position: relative;
                display: inline-block;
            }

            .filter-button {
                font-size: 15px;
                font-weight: 400;
                color: #39746E;
                background: #EFF0F0;
                border: 1px solid #EFF0F0;
                border-radius: 18px;
                padding: 6px 32px 6px 14px;
                display: flex;
                align-items: center;
                gap: 8px;
                cursor: pointer;
                transition: background 0.2s;
            }

            .filter-button:hover,
            .filter-button:focus {
                background: #fff;
                border: 1px solid #0FB7A6;
            }

            .filter-dropdown-content {
                display: none;
                position: absolute;
                left: 0;
                top: 100%;
                min-width: 200px;
                background: #fff;
                border: 1px solid #E5E6E6;
                border-radius: 12px;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
                margin-top: 6px;
                z-index: 10;
                max-height: 300px;
                overflow-y: auto;
            }

            .filter-dropdown-content.show {
                display: block;
            }

            .filter-option {
                font-size: 15px;
                color: #39746E;
                padding: 10px 18px;
                cursor: pointer;
                border-radius: 8px;
                transition: background 0.2s;
            }

            .filter-option:hover {
                background: #E3F4F1;
            }

            .filter-search-wrapper {
                padding: 8px 12px;
                border-bottom: 1px solid #E5E6E6;
                background: #FAFAFA;
                position: sticky;
                top: 0;
            }

            .filter-search-input {
                width: 100%;
                padding: 8px 12px;
                border: 1px solid #E5E6E6;
                border-radius: 6px;
                font-size: 14px;
                color: #39746E;
                background: #fff;
                outline: none;
                transition: border-color 0.2s;
            }

            .filter-search-input:focus {
                border-color: #0FB7A6;
            }

            .filter-search-input::placeholder {
                color: #A8A8A8;
            }

            .filter-option.hidden {
                display: none;
            }

            .date-input {
                padding: 8px 12px;
                border: 1px solid #E5E6E6;
                border-radius: 18px;
                font-size: 14px;
                background: #EFF0F0;
                border: 1px solid #EFF0F0;
                color: #39746E;
            }

            .date-input:focus {
                background: #fff;
                border: 1px solid #0FB7A6;
                outline: none;
            }

            .action-buttons {
                display: flex;
                gap: 8px;
                align-items: center;
            }

            .btn-add {
                padding: 0 1rem;
                background: #39746E;
                border: 1px solid #39746E;
                border-radius: 8px;
                font-size: 15px;
                font-weight: 600;
                color: #DFF0EE;
                cursor: pointer;
                display: flex;
                align-items: center;
                gap: 8px;
                height: 37px;
                transition: all 0.2s;
                text-decoration: none;
            }

            .btn-add:hover {
                background: #2d5a55;
            }

            .export-dropdown {
                position: relative;
            }

            .export-button {
                padding: 0 1rem;
                background: #39746E;
                border: 1px solid #E5E6E6;
                border-radius: 8px;
                font-size: 15px;
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
                font-size: 14px;
                color: #1e293b;
            }

            .reset-btn {
                color: #F44336;
                background: #fff;
                border: 1px solid #F44336;
                padding: 6px 16px;
                font-size: 15px;
                border-radius: 18px;
                font-weight: 600;
                text-decoration: none;
                display: flex;
                align-items: center;
                transition: all 0.2s;
            }

            .reset-btn:hover {
                background: #F44336;
                color: #fff;
            }

            .table-container {
                margin-top: 20px;
                overflow-x: auto;
            }

            .table {
                width: 100%;
                border-collapse: collapse;
            }

            .table th {
                background: #F8F9FA;
                padding: 12px 16px;
                text-align: left;
                font-weight: 600;
                font-size: 14px;
                color: #39746E;
                border-bottom: 1px solid #e2e8f0;
            }

            .table td {
                padding: 12px 16px;
                border-bottom: 1px solid #e2e8f0;
                color: #1e293b;
                font-size: 14px;
            }

            .table tr:hover {
                background: #F8F9FA;
            }

            .action-cell {
                width: 80px;
                text-align: center;
            }

            .action-btn {
                width: 32px;
                height: 32px;
                border: none;
                border-radius: 6px;
                background: transparent;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                transition: all 0.2s;
                text-decoration: none;
            }

            .action-btn img {
                width: 20px;
                height: 20px;
            }

            .action-btn:hover {
                background-color: #f3f4f6;
            }

            .btn-view {
                background: #EBF5FF;
                color: #1E40AF;
                padding: 6px 12px;
                border-radius: 6px;
                font-size: 13px;
                font-weight: 500;
                text-decoration: none;
                display: inline-flex;
                align-items: center;
                gap: 4px;
            }

            .btn-view:hover {
                background: #DBEAFE;
            }

            .badge {
                display: inline-block;
                padding: 4px 8px;
                border-radius: 6px;
                font-size: 12px;
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }

            .badge-sale {
                background: #D1FAE5;
                color: #065F46;
                border: 1px solid #A7F3D0;
            }

            .badge-processing {
                background: #DBEAFE;
                color: #1E40AF;
                border: 1px solid #93C5FD;
            }

            .text-success {
                color: #059669;
            }

            .text-danger {
                color: #DC2626;
            }

            .text-muted {
                color: #6B7271;
                font-size: 12px;
            }

            .pagination-container {
                display: flex;
                justify-content: flex-end;
                align-items: center;
                margin-top: 8px;
            }

            .pagination-info {
                font-size: 16px;
                font-weight: 500;
                color: #6B7271;
                margin-right: 8px;
            }

            .pagination {
                display: flex;
                gap: 8px;
            }

            .pagination-btn {
                font-size: 18px;
                font-weight: 700;
                color: #39746E;
                background: none;
                border: none;
                padding: 4px 12px;
                border-radius: 6px;
                cursor: pointer;
                margin-left: 0;
                margin-right: 0;
                transition: background 0.2s;
            }

            .pagination-btn:disabled {
                color: #B0B0B0;
                cursor: not-allowed;
                background: none;
            }

            .pagination-btn:not(:disabled):hover {
                background: #EFF0F0;
            }

            .no-results {
                text-align: center;
                padding: 3rem;
                color: #6b7280;
            }

            .no-results h3 {
                font-size: 16px;
                font-weight: 600;
                margin-bottom: 8px;
                color: #39746E;
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

            .loading-overlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.5);
                display: none;
                justify-content: center;
                align-items: center;
                z-index: 99999;
            }

            .loading-content {
                background: white;
                padding: 2rem;
                border-radius: 8px;
                text-align: center;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
            }

            .loading-spinner-large {
                width: 40px;
                height: 40px;
                border: 4px solid #f3f3f3;
                border-top: 4px solid #00B6A0;
                border-radius: 50%;
                animation: spin 1s linear infinite;
                margin: 0 auto 1rem auto;
            }

            @keyframes spin {
                0% {
                    transform: rotate(0deg);
                }

                100% {
                    transform: rotate(360deg);
                }
            }

            .modal {
                position: fixed;
                top: 0;
                left: 0;
                width: 100vw;
                height: 100vh;
                background: rgba(0, 0, 0, 0.35);
                display: flex;
                align-items: center;
                justify-content: center;
                z-index: 99999;
            }

            .modal-content {
                background: #fff;
                border-radius: 12px;
                padding: 32px 24px 24px 24px;
                min-width: 0;
                width: 100%;
                max-width: 400px;
                box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12);
                position: relative;
            }

            .modal-close {
                position: absolute;
                top: 18px;
                right: 18px;
                background: none;
                border: none;
                cursor: pointer;
            }

            .modal-icon {
                display: block;
                margin: 0 auto 12px auto;
                width: 38px;
                height: 38px;
            }

            .modal-title {
                font-size: 20px;
                font-weight: 700;
                color: #39746E;
                margin-bottom: 6px;
                text-align: center;
            }

            .modal-subtitle {
                font-size: 14px;
                color: #6B7271;
                margin-bottom: 18px;
                text-align: center;
            }

            .form-group {
                margin-bottom: 16px;
            }

            .form-label {
                font-weight: 600;
                color: #39746E;
                margin-bottom: 6px;
                display: block;
            }

            .form-control {
                width: 100%;
                padding: 8px 12px;
                border: 1px solid #E5E6E6;
                border-radius: 6px;
                font-size: 14px;
                margin-top: 2px;
            }

            .modal-buttons {
                display: flex;
                gap: 12px;
                justify-content: flex-end;
                margin-top: 18px;
            }

            .modal-button {
                font-size: 14px;
                font-weight: 600;
                border-radius: 6px;
                padding: 8px 18px;
                cursor: pointer;
                border: none;
            }

            .cancel-button {
                background: #6B7271;
                color: #fff;
            }

            .cancel-button:hover {
                background: #5a5f5e;
            }

            .confirm-button {
                background: #39746E;
                color: #fff;
            }

            .confirm-button:hover {
                background: #2d5a55;
            }

            @media (max-width: 768px) {
                .controls {
                    flex-direction: column;
                    align-items: stretch;
                }

                .search-filter {
                    flex-direction: column;
                }

                .action-buttons {
                    justify-content: center;
                }

                .table-container {
                    overflow-x: auto;
                }

                .table th,
                .table td {
                    padding: 8px 12px;
                    font-size: 12px;
                }
            }
        </style>
    </head>

    <body>
        <!-- Loading Overlay -->
        <div class="loading-overlay" id="pageLoadingOverlay" style="display: none;">
            <div class="loading-content">
                <div class="loading-spinner-large"></div>
                <p style="margin-top: 1rem; color: #374151;">Memuat data...</p>
            </div>
        </div>

        <!-- Header -->
        @include('partials.dashboard-header-bar', ['title' => $pageTitle ?? 'Transaksi Sampah'])

        <div style="padding: 0 2rem 2rem 2rem;">
            <div class="container">
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                @if (session('error'))
                    <div class="alert alert-error">{{ session('error') }}</div>
                @endif

                <!-- Controls Row 1: Primary actions -->
                <div class="controls">
                    <div></div>
                    <div class="action-buttons">
                        <div class="export-dropdown">
                            <button class="export-button" id="exportButton" type="button">
                                <span>Export</span>
                                <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
                                    <path d="M3 4.5L6 7.5L9 4.5" stroke="currentColor" stroke-width="1.5"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </button>
                            <div class="export-dropdown-content" id="exportDropdown">
                                <div class="export-option"
                                    onclick="document.getElementById('exportCsvModal').style.display='flex'">
                                    <img src="{{ asset('icon/ic_laporan.svg') }}" alt="CSV">
                                    <span>Export CSV</span>
                                </div>
                                <div class="export-option"
                                    onclick="document.getElementById('exportExcelModal').style.display='flex'">
                                    <img src="{{ asset('icon/ic_laporan.svg') }}" alt="Excel">
                                    <span>Export Excel</span>
                                </div>
                                <div class="export-option"
                                    onclick="document.getElementById('exportPdfModal').style.display='flex'">
                                    <img src="{{ asset('icon/ic_laporan.svg') }}" alt="PDF">
                                    <span>Export PDF</span>
                                </div>
                            </div>
                        </div>
                        @if (isset($createRoute) && isset($createLabel))
                            <a href="{{ route($createRoute) }}" class="btn-add">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <line x1="12" y1="5" x2="12" y2="19"></line>
                                    <line x1="5" y1="12" x2="19" y2="12"></line>
                                </svg>
                                {{ $createLabel }}
                            </a>
                        @else
                            <a href="{{ route('waste-transactions.sales.create') }}" class="btn-add">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <line x1="12" y1="5" x2="12" y2="19"></line>
                                    <line x1="5" y1="12" x2="19" y2="12"></line>
                                </svg>
                                Tambah Penjualan
                            </a>
                            <a href="{{ route('waste-transactions.processing.create') }}" class="btn-add"
                                style="background: #1E40AF;">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <line x1="12" y1="5" x2="12" y2="19"></line>
                                    <line x1="5" y1="12" x2="19" y2="12"></line>
                                </svg>
                                Tambah Pengolahan
                            </a>
                        @endif
                    </div>
                </div>

                <!-- Controls Row 2: Filters -->
                <div class="controls">
                    <form method="GET" action="{{ route($indexRoute ?? 'waste-transactions.index') }}"
                        class="search-filter" id="mainFilterForm">
                        <div class="search-box">
                            <input type="text" name="search" class="search-input"
                                placeholder="Cari kode transaksi..." value="{{ request('search') }}">
                            <svg class="search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none"
                                stroke="#6B7271" stroke-width="2">
                                <circle cx="11" cy="11" r="8"></circle>
                                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                            </svg>
                        </div>

                        @php $isCabang = Auth::guard('admin')->user()->role !== 'admin'; @endphp
                        @if (!$isCabang)
                            <div class="filter-dropdown">
                                <button type="button" class="filter-button" onclick="toggleDropdown('bankDropdown')">
                                    {{ request('bank_sampah_id') ? $bankSampahList->where('id', request('bank_sampah_id'))->first()->nama_bank_sampah ?? 'Pilih Bank Sampah' : 'Pilih Bank Sampah' }}
                                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
                                        <path d="M3 4.5L6 7.5L9 4.5" stroke="currentColor" stroke-width="1.5"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </button>
                                <div id="bankDropdown" class="filter-dropdown-content">
                                    <div class="filter-search-wrapper">
                                        <input type="text" id="bankSearchInput" class="filter-search-input"
                                            placeholder="Cari bank sampah..." onkeyup="filterBankOptions()"
                                            onclick="event.stopPropagation()">
                                    </div>
                                    <div class="filter-option"
                                        onclick="selectFilter('bank_sampah_id', '', 'Pilih Bank Sampah')">Semua Bank Sampah
                                    </div>
                                    @foreach ($bankSampahList as $bs)
                                        <div class="filter-option"
                                            data-bank-name="{{ strtolower($bs->nama_bank_sampah) }}"
                                            onclick="selectFilter('bank_sampah_id', '{{ $bs->id }}', '{{ $bs->nama_bank_sampah }}')">
                                            {{ $bs->nama_bank_sampah }}</div>
                                    @endforeach
                                </div>
                                <input type="hidden" name="bank_sampah_id" value="{{ request('bank_sampah_id', '') }}">
                            </div>
                        @endif

                        <div class="filter-dropdown">
                            <button type="button" class="filter-button" onclick="toggleDropdown('offtakerDropdown')">
                                {{ request('offtaker_id') ? $offtakerList->where('id', request('offtaker_id'))->first()->nama ?? 'Pilih Offtaker' : 'Pilih Offtaker' }}
                                <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
                                    <path d="M3 4.5L6 7.5L9 4.5" stroke="currentColor" stroke-width="1.5"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </button>
                            <div id="offtakerDropdown" class="filter-dropdown-content">
                                <div class="filter-search-wrapper">
                                    <input type="text" id="offtakerSearchInput" class="filter-search-input"
                                        placeholder="Cari offtaker..." onkeyup="filterOfftakerOptions()"
                                        onclick="event.stopPropagation()">
                                </div>
                                <div class="filter-option" onclick="selectFilter('offtaker_id', '', 'Pilih Offtaker')">
                                    Semua Offtaker</div>
                                @foreach ($offtakerList as $of)
                                    <div class="filter-option" data-offtaker-name="{{ strtolower($of->nama) }}"
                                        onclick="selectFilter('offtaker_id', '{{ $of->id }}', '{{ $of->nama }}')">
                                        {{ $of->nama }}</div>
                                @endforeach
                            </div>
                            <input type="hidden" name="offtaker_id" value="{{ request('offtaker_id', '') }}">
                        </div>

                        <div class="filter-dropdown">
                            <button type="button" class="filter-button" onclick="toggleDropdown('typeDropdown')">
                                @php
                                    $typeLabel = 'Semua Tipe';
                                    if (request('type') === 'sale') {
                                        $typeLabel = 'Penjualan';
                                    } elseif (request('type') === 'processing') {
                                        $typeLabel = 'Pengolahan';
                                    }
                                @endphp
                                {{ $typeLabel }}
                                <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
                                    <path d="M3 4.5L6 7.5L9 4.5" stroke="currentColor" stroke-width="1.5"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </button>
                            <div id="typeDropdown" class="filter-dropdown-content">
                                <div class="filter-option" onclick="selectFilter('type', '', 'Semua Tipe')">
                                    Semua Tipe</div>
                                <div class="filter-option" onclick="selectFilter('type', 'sale', 'Penjualan')">
                                    Penjualan</div>
                                <div class="filter-option" onclick="selectFilter('type', 'processing', 'Pengolahan')">
                                    Pengolahan</div>
                            </div>
                            <input type="hidden" name="type" value="{{ request('type', '') }}">
                        </div>

                        <input type="date" name="start_date" class="date-input" placeholder="Tanggal Mulai"
                            value="{{ request('start_date') }}">
                        <span style="margin: 0 4px; color: #6B7271;">s/d</span>
                        <input type="date" name="end_date" class="date-input" placeholder="Tanggal Akhir"
                            value="{{ request('end_date') }}">

                        <a href="{{ route($indexRoute ?? 'waste-transactions.index') }}" class="reset-btn"
                            onclick="showLoading('Mereset filter...')">Reset</a>
                    </form>
                </div>

                <!-- Table -->
                <div class="table-container">
                    @if ($transactions->count() > 0)
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Kode</th>
                                    @if (!isset($transactionType) || $transactionType === 'all')
                                        <th>Tipe</th>
                                    @endif
                                    <th>Bank Sampah</th>
                                    <th>Offtaker</th>
                                    <th>Tanggal</th>
                                    <th>Qty (kg)</th>
                                    <th>Nilai</th>
                                    <th>Laba</th>
                                    <th>Metode Pengolahan</th>
                                    <th class="action-cell">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($transactions as $transaction)
                                    <tr>
                                        <td><strong>{{ $transaction->kode_transaksi }}</strong></td>
                                        @if (!isset($transactionType) || $transactionType === 'all')
                                            <td>
                                                @if ($transaction->type === 'sale')
                                                    <span class="badge badge-sale">Penjualan</span>
                                                @else
                                                    <span class="badge badge-processing">Pengolahan</span>
                                                @endif
                                            </td>
                                        @endif
                                        <td>
                                            <div style="display: flex; flex-direction: column;">
                                                <span
                                                    style="font-weight: 600;">{{ $transaction->bankSampah->nama_bank_sampah ?? '-' }}</span>
                                                <span
                                                    class="text-muted">{{ $transaction->bankSampah->kode_bank_sampah ?? '' }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div style="display: flex; flex-direction: column;">
                                                <span
                                                    style="font-weight: 600;">{{ $transaction->offtaker->nama ?? '-' }}</span>
                                                <span
                                                    class="text-muted">{{ $transaction->offtaker->kode_offtaker ?? '' }}</span>
                                            </div>
                                        </td>
                                        <td>{{ $transaction->tanggal_transaksi->format('d/m/Y') }}</td>
                                        <td>{{ number_format($transaction->total_quantity, 2, ',', '.') }}</td>
                                        <td>
                                            Rp {{ number_format($transaction->total_value, 0, ',', '.') }}
                                        </td>
                                        <td>
                                            @php $profit = $transaction->total_value - $transaction->harga_beli_total; @endphp
                                            <span class="{{ $profit >= 0 ? 'text-success' : 'text-danger' }}">
                                                Rp {{ number_format($profit, 0, ',', '.') }}
                                            </span>
                                        </td>
                                        <td>{{ $transaction->metode_pengolahan ?? '-' }}</td>
                                        <td class="action-cell">
                                            <a href="{{ route('waste-transactions.show', $transaction) }}"
                                                class="btn-view">Lihat</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="no-results">
                            @if (isset($transactionType) && $transactionType === 'sale')
                                <h3>Belum ada transaksi penjualan</h3>
                                <p>Buat transaksi penjualan sampah untuk memulai.</p>
                            @elseif(isset($transactionType) && $transactionType === 'processing')
                                <h3>Belum ada transaksi pengolahan</h3>
                                <p>Buat transaksi pengolahan sampah untuk memulai.</p>
                            @else
                                <h3>Belum ada transaksi</h3>
                                <p>Buat transaksi penjualan atau pengolahan sampah untuk memulai.</p>
                            @endif
                        </div>
                    @endif
                </div>

                <!-- Pagination -->
                @if ($transactions->hasPages())
                    <div class="pagination-container">
                        <div class="pagination-info">
                            {{ $transactions->firstItem() ?? 0 }} - {{ $transactions->lastItem() ?? 0 }} of
                            {{ $transactions->total() }}
                        </div>
                        <div class="pagination">
                            <button class="pagination-btn"
                                onclick="showLoading('Memuat halaman...'); window.location.href='{{ $transactions->previousPageUrl() }}'"
                                {{ $transactions->onFirstPage() ? 'disabled' : '' }}>&lt;</button>
                            <button class="pagination-btn"
                                onclick="showLoading('Memuat halaman...'); window.location.href='{{ $transactions->nextPageUrl() }}'"
                                {{ !$transactions->hasMorePages() ? 'disabled' : '' }}>&gt;</button>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Export Excel Modal -->
        <div class="modal" id="exportExcelModal" style="display:none;">
            <div class="modal-content">
                <button class="modal-close" onclick="closeExportModal('exportExcelModal')">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#6B7271"
                        stroke-width="2">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
                <img src="{{ asset('icon/ic_laporan.svg') }}" alt="Export" class="modal-icon">
                <h2 class="modal-title">Export Excel {{ $pageTitle ?? 'Transaksi Sampah' }}</h2>
                <p class="modal-subtitle">Pilih filter data yang ingin diexport</p>
                <div class="export-form">
                    <div class="form-group">
                        <label class="form-label">Bank Sampah</label>
                        <select name="bank_sampah_id" class="form-control">
                            <option value="">Semua Bank Sampah</option>
                            @foreach ($bankSampahList as $bank)
                                <option value="{{ $bank->id }}">{{ $bank->nama_bank_sampah }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Offtaker</label>
                        <select name="offtaker_id" class="form-control">
                            <option value="">Semua Offtaker</option>
                            @foreach ($offtakerList as $of)
                                <option value="{{ $of->id }}">{{ $of->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tipe Transaksi</label>
                        <select name="type" class="form-control">
                            <option value="">Semua Tipe</option>
                            <option value="sale">Penjualan</option>
                            <option value="processing">Pengolahan</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Periode</label>
                        <select name="time_filter" class="form-control" onchange="toggleDateInputs(this, 'excel')">
                            <option value="">Semua Periode</option>
                            <option value="harian">Hari Ini</option>
                            <option value="mingguan">Minggu Ini</option>
                            <option value="bulanan">Bulan Ini</option>
                            <option value="range">Rentang Tanggal</option>
                        </select>
                    </div>
                    <div class="form-group" id="excelDateRangeGroup1" style="display:none;">
                        <label class="form-label">Tanggal Mulai</label>
                        <input type="date" name="start_date" class="form-control">
                    </div>
                    <div class="form-group" id="excelDateRangeGroup2" style="display:none;">
                        <label class="form-label">Tanggal Akhir</label>
                        <input type="date" name="end_date" class="form-control">
                    </div>
                </div>
                <div class="modal-buttons">
                    <button class="modal-button cancel-button" type="button"
                        onclick="closeExportModal('exportExcelModal')">Batal</button>
                    <button class="modal-button confirm-button" type="button" onclick="exportExcel()">Export
                        Excel</button>
                </div>
            </div>
        </div>

        <!-- Export PDF Modal -->
        <div class="modal" id="exportPdfModal" style="display:none;">
            <div class="modal-content">
                <button class="modal-close" onclick="closeExportModal('exportPdfModal')">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#6B7271"
                        stroke-width="2">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
                <img src="{{ asset('icon/ic_laporan.svg') }}" alt="Export" class="modal-icon">
                <h2 class="modal-title">Export PDF {{ $pageTitle ?? 'Transaksi Sampah' }}</h2>
                <p class="modal-subtitle">Pilih filter data yang ingin diexport</p>
                <div class="export-form">
                    <div class="form-group">
                        <label class="form-label">Bank Sampah</label>
                        <select name="bank_sampah_id" class="form-control">
                            <option value="">Semua Bank Sampah</option>
                            @foreach ($bankSampahList as $bank)
                                <option value="{{ $bank->id }}">{{ $bank->nama_bank_sampah }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Offtaker</label>
                        <select name="offtaker_id" class="form-control">
                            <option value="">Semua Offtaker</option>
                            @foreach ($offtakerList as $of)
                                <option value="{{ $of->id }}">{{ $of->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tipe Transaksi</label>
                        <select name="type" class="form-control">
                            <option value="">Semua Tipe</option>
                            <option value="sale">Penjualan</option>
                            <option value="processing">Pengolahan</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Periode</label>
                        <select name="time_filter" class="form-control" onchange="toggleDateInputs(this, 'pdf')">
                            <option value="">Semua Periode</option>
                            <option value="harian">Hari Ini</option>
                            <option value="mingguan">Minggu Ini</option>
                            <option value="bulanan">Bulan Ini</option>
                            <option value="range">Rentang Tanggal</option>
                        </select>
                    </div>
                    <div class="form-group" id="pdfDateRangeGroup1" style="display:none;">
                        <label class="form-label">Tanggal Mulai</label>
                        <input type="date" name="start_date" class="form-control">
                    </div>
                    <div class="form-group" id="pdfDateRangeGroup2" style="display:none;">
                        <label class="form-label">Tanggal Akhir</label>
                        <input type="date" name="end_date" class="form-control">
                    </div>
                </div>
                <div class="modal-buttons">
                    <button class="modal-button cancel-button" type="button"
                        onclick="closeExportModal('exportPdfModal')">Batal</button>
                    <button class="modal-button confirm-button" type="button" onclick="exportPdf()">Export PDF</button>
                </div>
            </div>
        </div>

        <!-- Export CSV Modal -->
        <div class="modal" id="exportCsvModal" style="display:none;">
            <div class="modal-content">
                <button class="modal-close" onclick="closeExportModal('exportCsvModal')">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#6B7271"
                        stroke-width="2">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
                <img src="{{ asset('icon/ic_laporan.svg') }}" alt="Export" class="modal-icon">
                <h2 class="modal-title">Export CSV {{ $pageTitle ?? 'Transaksi Sampah' }}</h2>
                <p class="modal-subtitle">Pilih filter data yang ingin diexport</p>
                <div class="export-form">
                    <div class="form-group">
                        <label class="form-label">Bank Sampah</label>
                        <select name="bank_sampah_id" class="form-control">
                            <option value="">Semua Bank Sampah</option>
                            @foreach ($bankSampahList as $bank)
                                <option value="{{ $bank->id }}">{{ $bank->nama_bank_sampah }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Offtaker</label>
                        <select name="offtaker_id" class="form-control">
                            <option value="">Semua Offtaker</option>
                            @foreach ($offtakerList as $of)
                                <option value="{{ $of->id }}">{{ $of->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tipe Transaksi</label>
                        <select name="type" class="form-control">
                            <option value="">Semua Tipe</option>
                            <option value="sale">Penjualan</option>
                            <option value="processing">Pengolahan</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Periode</label>
                        <select name="time_filter" class="form-control" onchange="toggleDateInputs(this, 'csv')">
                            <option value="">Semua Periode</option>
                            <option value="harian">Hari Ini</option>
                            <option value="mingguan">Minggu Ini</option>
                            <option value="bulanan">Bulan Ini</option>
                            <option value="range">Rentang Tanggal</option>
                        </select>
                    </div>
                    <div class="form-group" id="csvDateRangeGroup1" style="display:none;">
                        <label class="form-label">Tanggal Mulai</label>
                        <input type="date" name="start_date" class="form-control">
                    </div>
                    <div class="form-group" id="csvDateRangeGroup2" style="display:none;">
                        <label class="form-label">Tanggal Akhir</label>
                        <input type="date" name="end_date" class="form-control">
                    </div>
                </div>
                <div class="modal-buttons">
                    <button class="modal-button cancel-button" type="button"
                        onclick="closeExportModal('exportCsvModal')">Batal</button>
                    <button class="modal-button confirm-button" type="button" onclick="exportCsv()">Export CSV</button>
                </div>
            </div>
        </div>

        <script>
            const transactionType = '{{ $transactionType ?? '' }}';

            function showLoading(message = 'Memuat data...') {
                const overlay = document.getElementById('pageLoadingOverlay');
                const loadingText = overlay.querySelector('p');
                loadingText.textContent = message;
                overlay.style.display = 'flex';
            }

            function hideLoading() {
                document.getElementById('pageLoadingOverlay').style.display = 'none';
            }

            function toggleDropdown(dropdownId) {
                const dropdown = document.getElementById(dropdownId);
                const allDropdowns = document.querySelectorAll('.filter-dropdown-content');
                allDropdowns.forEach(d => {
                    if (d.id !== dropdownId) d.classList.remove('show');
                });
                dropdown.classList.toggle('show');

                if (dropdown.classList.contains('show')) {
                    const searchInput = dropdown.querySelector('.filter-search-input');
                    if (searchInput) {
                        searchInput.value = '';
                        searchInput.focus();
                        filterOptions(searchInput, dropdownId);
                    }
                }
            }

            function filterBankOptions() {
                filterOptions(document.getElementById('bankSearchInput'), 'bankDropdown');
            }

            function filterOfftakerOptions() {
                filterOptions(document.getElementById('offtakerSearchInput'), 'offtakerDropdown');
            }

            function filterOptions(searchInput, dropdownId) {
                const searchTerm = searchInput.value.toLowerCase().trim();
                const dropdown = document.getElementById(dropdownId);
                const options = dropdown.querySelectorAll('.filter-option');

                options.forEach(option => {
                    const name = option.getAttribute('data-bank-name') || option.getAttribute('data-offtaker-name') ||
                        option.textContent.toLowerCase();
                    if (name.includes(searchTerm) || option.textContent.includes('Semua')) {
                        option.classList.remove('hidden');
                    } else {
                        option.classList.add('hidden');
                    }
                });
            }

            function selectFilter(name, value, displayText) {
                showLoading('Menerapkan filter...');

                let hiddenInput = document.querySelector(`input[name="${name}"]`);
                if (hiddenInput) hiddenInput.value = value;

                const button = event.target.closest('.filter-dropdown').querySelector('.filter-button');
                const icon = button.querySelector('svg');
                button.innerHTML = displayText + ' ';
                if (icon) button.appendChild(icon);

                const dropdownMap = {
                    'bank_sampah_id': 'bankDropdown',
                    'offtaker_id': 'offtakerDropdown'
                };
                if (dropdownMap[name]) document.getElementById(dropdownMap[name]).classList.remove('show');

                document.querySelector('#mainFilterForm').submit();
            }

            // Export dropdown toggle
            document.getElementById('exportButton').addEventListener('click', function(e) {
                e.preventDefault();
                document.getElementById('exportDropdown').classList.toggle('show');
            });

            // Search auto-submit
            document.addEventListener('DOMContentLoaded', function() {
                showLoading('Memuat data...');
                setTimeout(() => hideLoading(), 500);

                const searchInput = document.querySelector('input[name="search"]');
                if (searchInput) {
                    let timeout = null;
                    searchInput.addEventListener('input', function() {
                        clearTimeout(timeout);
                        timeout = setTimeout(() => {
                            showLoading('Mencari data...');
                            document.querySelector('#mainFilterForm').submit();
                        }, 500);
                    });
                }

                const dateInputs = document.querySelectorAll('input[type="date"]:not(.modal input)');
                dateInputs.forEach(input => {
                    input.addEventListener('change', function() {
                        showLoading('Menerapkan filter...');
                        document.querySelector('#mainFilterForm').submit();
                    });
                });
            });

            // Close dropdowns when clicking outside
            document.addEventListener('click', function(event) {
                if (!event.target.closest('.filter-dropdown') && !event.target.closest('.export-dropdown')) {
                    document.querySelectorAll('.filter-dropdown-content, .export-dropdown-content').forEach(
                        dropdown => {
                            dropdown.classList.remove('show');
                        });
                }
            });

            function closeExportModal(modalId) {
                document.getElementById(modalId).style.display = 'none';
            }

            function toggleDateInputs(select, type) {
                const group1 = document.getElementById(type + 'DateRangeGroup1');
                const group2 = document.getElementById(type + 'DateRangeGroup2');
                if (select.value === 'range') {
                    if (group1) group1.style.display = 'block';
                    if (group2) group2.style.display = 'block';
                } else {
                    if (group1) group1.style.display = 'none';
                    if (group2) group2.style.display = 'none';
                }
            }

            function exportExcel() {
                const modal = document.getElementById('exportExcelModal');
                const formData = new FormData();

                const bankSampahId = modal.querySelector('select[name="bank_sampah_id"]').value;
                const offtakerId = modal.querySelector('select[name="offtaker_id"]').value;
                const type = modal.querySelector('select[name="type"]').value;
                const timeFilter = modal.querySelector('select[name="time_filter"]').value;
                const startDate = modal.querySelector('input[name="start_date"]').value;
                const endDate = modal.querySelector('input[name="end_date"]').value;

                if (bankSampahId) formData.append('bank_sampah_id', bankSampahId);
                if (offtakerId) formData.append('offtaker_id', offtakerId);
                if (type) formData.append('type', type);
                if (timeFilter) formData.append('time_filter', timeFilter);
                if (startDate) formData.append('start_date', startDate);
                if (endDate) formData.append('end_date', endDate);

                closeExportModal('exportExcelModal');
                showLoading('Mengexport Excel...');

                fetch('{{ route('waste-transactions.export.excel') }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: formData
                    })
                    .then(response => {
                        if (!response.ok) throw new Error('Network response was not ok');
                        return response.blob();
                    })
                    .then(blob => {
                        const url = window.URL.createObjectURL(blob);
                        const a = document.createElement('a');
                        a.href = url;
                        const typeLabel = transactionType === 'sale' ? 'penjualan' : (transactionType === 'processing' ?
                            'pengolahan' : 'transaksi');
                        a.download = `laporan_${typeLabel}_sampah_${new Date().toISOString().slice(0,10)}.xlsx`;
                        document.body.appendChild(a);
                        a.click();
                        window.URL.revokeObjectURL(url);
                        document.body.removeChild(a);
                        hideLoading();
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Terjadi kesalahan saat export Excel');
                        hideLoading();
                    });
            }

            function exportPdf() {
                const modal = document.getElementById('exportPdfModal');
                const formData = new FormData();

                const bankSampahId = modal.querySelector('select[name="bank_sampah_id"]').value;
                const offtakerId = modal.querySelector('select[name="offtaker_id"]').value;
                const type = modal.querySelector('select[name="type"]').value;
                const timeFilter = modal.querySelector('select[name="time_filter"]').value;
                const startDate = modal.querySelector('input[name="start_date"]').value;
                const endDate = modal.querySelector('input[name="end_date"]').value;

                if (bankSampahId) formData.append('bank_sampah_id', bankSampahId);
                if (offtakerId) formData.append('offtaker_id', offtakerId);
                if (type) formData.append('type', type);
                if (timeFilter) formData.append('time_filter', timeFilter);
                if (startDate) formData.append('start_date', startDate);
                if (endDate) formData.append('end_date', endDate);

                closeExportModal('exportPdfModal');
                showLoading('Mengexport PDF...');

                fetch('{{ route('waste-transactions.export.pdf') }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: formData
                    })
                    .then(response => {
                        if (!response.ok) throw new Error('Network response was not ok');
                        return response.blob();
                    })
                    .then(blob => {
                        const url = window.URL.createObjectURL(blob);
                        const a = document.createElement('a');
                        a.href = url;
                        const typeLabel = transactionType === 'sale' ? 'penjualan' : (transactionType === 'processing' ?
                            'pengolahan' : 'transaksi');
                        a.download = `laporan_${typeLabel}_sampah_${new Date().toISOString().slice(0,10)}.pdf`;
                        document.body.appendChild(a);
                        a.click();
                        window.URL.revokeObjectURL(url);
                        document.body.removeChild(a);
                        hideLoading();
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Terjadi kesalahan saat export PDF');
                        hideLoading();
                    });
            }

            function exportCsv() {
                const modal = document.getElementById('exportCsvModal');
                const formData = new FormData();

                const bankSampahId = modal.querySelector('select[name="bank_sampah_id"]').value;
                const offtakerId = modal.querySelector('select[name="offtaker_id"]').value;
                const type = modal.querySelector('select[name="type"]').value;
                const timeFilter = modal.querySelector('select[name="time_filter"]').value;
                const startDate = modal.querySelector('input[name="start_date"]').value;
                const endDate = modal.querySelector('input[name="end_date"]').value;

                if (bankSampahId) formData.append('bank_sampah_id', bankSampahId);
                if (offtakerId) formData.append('offtaker_id', offtakerId);
                if (type) formData.append('type', type);
                if (timeFilter) formData.append('time_filter', timeFilter);
                if (startDate) formData.append('start_date', startDate);
                if (endDate) formData.append('end_date', endDate);

                closeExportModal('exportCsvModal');
                showLoading('Mengexport CSV...');

                fetch('{{ route('waste-transactions.export.csv') }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: formData
                    })
                    .then(response => {
                        if (!response.ok) throw new Error('Network response was not ok');
                        return response.blob();
                    })
                    .then(blob => {
                        const url = window.URL.createObjectURL(blob);
                        const a = document.createElement('a');
                        a.href = url;
                        const typeLabel = transactionType === 'sale' ? 'penjualan' : (transactionType === 'processing' ?
                            'pengolahan' : 'transaksi');
                        a.download = `laporan_${typeLabel}_sampah_${new Date().toISOString().slice(0,10)}.csv`;
                        document.body.appendChild(a);
                        a.click();
                        window.URL.revokeObjectURL(url);
                        document.body.removeChild(a);
                        hideLoading();
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Terjadi kesalahan saat export CSV');
                        hideLoading();
                    });
            }
        </script>
    </body>

    </html>
@endsection
