@extends('dashboard')
@section('content')
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Transaksi - Admin Panel</title>
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
            font-size: 22px;
            font-weight: 400;
            color: #39746E;
        }

        .header-separator {
            font-size: 22px;
            color: #39746E;
        }

        .header-subtitle {
            font-size: 22px;
            font-weight: 700;
            color: #39746E;
        }

        .main-container {
            display: grid;
            grid-template-columns: 1fr 350px;
            gap: 1rem;
            margin: 0 2rem 2rem 2rem;
        }

        .form-container {
            background: #fff;
            border: 1px solid #E5E6E6;
            border-radius: 16px;
            padding: 24px;
        }

        .summary-container {
            background: #fff;
            border: 1px solid #E5E6E6;
            border-radius: 16px;
            padding: 24px;
            height: fit-content;
            position: sticky;
            top: 100px;
        }

        .form-header {
            margin-bottom: 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .form-title {
            font-size: 18px;
            font-weight: 700;
            color: #242E2C;
        }

        .form-actions {
            display: flex;
            gap: 8px;
        }

        .btn-cancel {
            padding: 8px 16px;
            background: transparent;
            border: 1px solid #FDCED1;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            color: #F73541;
            cursor: pointer;
            text-decoration: none;
        }

        .btn-save {
            padding: 8px 16px;
            background: #39746E;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            color: #DFF0EE;
            cursor: pointer;
        }

        .btn-save:hover {
            background: #2d5a55;
        }

        .btn-save:disabled {
            background: #9CA3AF;
            cursor: not-allowed;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-size: 14px;
            font-weight: 500;
            color: #374151;
            margin-bottom: 8px;
        }

        .form-label span {
            color: #EF4444;
        }

        .form-input, .form-select {
            width: 100%;
            padding: 12px;
            border: 1px solid #E5E6E6;
            border-radius: 8px;
            font-size: 14px;
            color: #1e293b;
            background: #fff;
        }

        .form-input:focus, .form-select:focus {
            outline: none;
            border-color: #39746E;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .form-error {
            color: #EF4444;
            font-size: 12px;
            margin-top: 4px;
        }

        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 16px;
            font-size: 14px;
        }

        .alert-error {
            background: #FEE2E2;
            color: #991B1B;
            border: 1px solid #FECACA;
        }

        .section-title {
            font-size: 16px;
            font-weight: 700;
            color: #242E2C;
            margin: 24px 0 16px 0;
            padding-bottom: 8px;
            border-bottom: 1px solid #E5E6E6;
        }

        /* Items Section - Table Styles */
        .items-section {
            background: #fff;
            border: 1px solid #E5E6E6;
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 16px;
        }

        .items-header {
            background: #F8F9FA;
            padding: 16px 20px;
            border-bottom: 1px solid #E5E6E6;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .items-header h3 {
            font-size: 18px;
            font-weight: 700;
            color: #39746E;
            margin: 0;
        }

        .items-count {
            background: #39746E;
            color: #fff;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
        }

        .desktop-add-btn {
            padding: 8px 16px;
            background: #39746E;
            color: #fff;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s;
        }

        .desktop-add-btn:hover:not(:disabled) {
            background: #2d5a55;
        }

        .desktop-add-btn:disabled {
            background: #D1D5DB;
            color: #9CA3AF;
            cursor: not-allowed;
            opacity: 0.6;
        }

        .button-hint {
            font-size: 12px;
            color: #6B7271;
            margin-top: 8px;
            display: none;
        }

        .button-hint.show {
            display: block;
        }

        /* Items Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
        }

        .items-table th {
            background: #F8F9FA;
            padding: 12px 16px;
            text-align: left;
            font-weight: 600;
            font-size: 14px;
            color: #39746E;
            border-bottom: 1px solid #E5E6E6;
        }

        .items-table td {
            padding: 12px 16px;
            border-bottom: 1px solid #E5E6E6;
            font-size: 14px;
        }

        .items-table tr:hover {
            background: #F8F9FA;
        }

        .item-name {
            font-weight: 600;
            color: #39746E;
        }

        .item-total {
            font-weight: 600;
            color: #0FB7A6;
        }

        .item-actions {
            display: flex;
            gap: 8px;
            justify-content: center;
        }

        .btn-small {
            padding: 4px 8px;
            border: none;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
        }

        .btn-danger {
            background: #FEE2E2;
            color: #991B1B;
        }

        .btn-danger:hover {
            background: #FCA5A5;
        }

        .stock-info {
            font-size: 11px;
            color: #6B7271;
            margin-top: 2px;
        }

        /* Modal Styles */
        .modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(0,0,0,0.35);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 99999;
        }

        .modal.show {
            display: flex;
        }

        .modal-content {
            background: #fff;
            border-radius: 12px;
            padding: 32px 24px 24px 24px;
            width: 90%;
            max-width: 500px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 8px 32px rgba(0,0,0,0.12);
            position: relative;
        }

        .modal-close {
            position: absolute;
            top: 18px;
            right: 18px;
            background: none;
            border: none;
            cursor: pointer;
            padding: 0;
            width: 24px;
            height: 24px;
        }

        .modal-title {
            font-size: 20px;
            font-weight: 700;
            color: #39746E;
            margin-bottom: 24px;
            text-align: center;
        }

        .modal-buttons {
            display: flex;
            gap: 12px;
            justify-content: flex-end;
            margin-top: 24px;
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

        /* Mobile Sticky Add Button */
        .mobile-sticky-add-btn {
            display: none;
        }

        .summary-title {
            font-size: 16px;
            font-weight: 700;
            color: #242E2C;
            margin-bottom: 16px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #E5E6E6;
            font-size: 14px;
        }

        .summary-row:last-child {
            border-bottom: none;
        }

        .summary-label {
            color: #6B7271;
        }

        .summary-value {
            font-weight: 600;
            color: #1e293b;
        }

        .summary-total {
            margin-top: 16px;
            padding-top: 16px;
            border-top: 2px solid #E5E6E6;
        }

        .summary-total .summary-value {
            font-size: 20px;
            color: #059669;
        }

        .profit-display {
            margin-top: 8px;
            padding: 12px;
            background: #D1FAE5;
            border-radius: 8px;
            text-align: center;
        }

        .profit-label {
            font-size: 12px;
            color: #065F46;
        }

        .profit-value {
            font-size: 18px;
            font-weight: 700;
            color: #065F46;
        }

        @media (max-width: 1024px) {
            .main-container {
                grid-template-columns: 1fr;
            }
            .summary-container {
                position: static;
            }
        }

        @media (max-width: 768px) {
            /* Header/Breadcrumb */
            .header {
                padding: 0.75rem 1rem;
            }

            .header-left h1 {
                font-size: 16px;
            }

            .header-separator {
                font-size: 16px;
            }

            .header-subtitle {
                font-size: 16px;
            }

            /* Main Container */
            .main-container {
                margin: 0 0 2rem 0;
                gap: 1rem;
            }

            /* Form Container - Reduced Padding */
            .form-container {
                padding: 16px;
                border-radius: 0;
                border-left: none;
                border-right: none;
            }

            /* Form Header - Stack Vertically */
            .form-header {
                flex-direction: column;
                align-items: stretch;
                gap: 12px;
                margin-bottom: 20px;
            }

            .form-title {
                font-size: 16px;
            }

            .form-actions {
                width: 100%;
                flex-direction: column-reverse;
                gap: 8px;
            }

            .btn-cancel,
            .btn-save {
                width: 100%;
                padding: 12px 16px;
                font-size: 14px;
                justify-content: center;
                display: flex;
            }

            /* Form Groups */
            .form-group {
                margin-bottom: 16px;
            }

            .form-row {
                grid-template-columns: 1fr;
                gap: 0;
            }

            /* Summary Container */
            .summary-container {
                padding: 16px;
                border-radius: 0;
                border-left: none;
                border-right: none;
            }

            /* Section Title */
            .section-title {
                font-size: 15px;
                margin: 20px 0 12px 0;
            }

            /* Items Table → Card Layout on Mobile */
            .items-section {
                margin-bottom: 80px;
            }

            .items-header {
                flex-direction: column;
                align-items: stretch !important;
                gap: 12px;
            }

            .items-header .desktop-add-btn {
                width: 100%;
                justify-content: center;
                height: 48px;
            }

            .items-table {
                display: block;
            }

            .items-table thead {
                display: none;
            }

            .items-table tbody {
                display: block;
            }

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

            .items-table td::before {
                content: attr(data-label);
                font-weight: 600;
                color: #6B7271;
                font-size: 13px;
                flex-shrink: 0;
                margin-right: 12px;
            }

            .items-table td.item-name {
                font-size: 15px;
                font-weight: 700;
                color: #39746E;
                border-bottom: 2px solid #E3F4F1;
                padding-bottom: 12px;
            }

            .items-table td.item-name::before {
                display: none;
            }

            .item-actions::before {
                display: none;
            }

            .btn-small {
                width: 100%;
                padding: 8px 16px;
                font-size: 13px;
                border-radius: 6px;
            }

            /* Mobile Sticky Add Button */
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
                transition: all 0.2s;
            }

            .mobile-sticky-add-btn .btn:active:not(:disabled) {
                transform: scale(0.98);
            }

            .mobile-sticky-add-btn .btn:disabled {
                background: #D1D5DB;
                color: #9CA3AF;
                cursor: not-allowed;
                opacity: 0.6;
                box-shadow: none;
            }

            /* Modal on Mobile - Fullscreen */
            .modal-content {
                margin: 0;
                width: 100%;
                height: 100%;
                max-height: 100%;
                border-radius: 0;
                padding: 1.5rem;
            }

            .form-control {
                height: 48px;
                font-size: 16px;
            }

            .modal .btn {
                height: 48px;
                font-size: 16px;
            }
        }

        /* Searchable Select Dropdown Styles */
        .custom-select-wrapper {
            position: relative;
            width: 100%;
        }

        .custom-select-trigger {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px;
            border: 1px solid #E5E6E6;
            border-radius: 8px;
            background: #fff;
            cursor: pointer;
            font-size: 14px;
            color: #1e293b;
            transition: border-color 0.2s;
        }

        .custom-select-trigger:hover {
            border-color: #39746E;
        }

        .custom-select-trigger.active {
            border-color: #39746E;
            border-bottom-left-radius: 0;
            border-bottom-right-radius: 0;
        }

        .custom-select-trigger .selected-text {
            flex: 1;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .custom-select-trigger .placeholder {
            color: #9CA3AF;
        }

        .custom-select-trigger .arrow {
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.2s;
        }

        .custom-select-trigger.active .arrow {
            transform: rotate(180deg);
        }

        .custom-select-dropdown {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: #fff;
            border: 1px solid #39746E;
            border-top: none;
            border-bottom-left-radius: 8px;
            border-bottom-right-radius: 8px;
            z-index: 1000;
            display: none;
            max-height: 280px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .custom-select-dropdown.show {
            display: block;
        }

        .custom-select-search {
            padding: 10px 12px;
            border-bottom: 1px solid #E5E6E6;
        }

        .custom-select-search input {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #E5E6E6;
            border-radius: 6px;
            font-size: 14px;
            outline: none;
        }

        .custom-select-search input:focus {
            border-color: #39746E;
        }

        .custom-select-options {
            max-height: 200px;
            overflow-y: auto;
        }

        .custom-select-option {
            padding: 12px 16px;
            cursor: pointer;
            font-size: 14px;
            color: #1e293b;
            transition: background-color 0.15s;
        }

        .custom-select-option:hover {
            background: #F0FDF4;
        }

        .custom-select-option.selected {
            background: #D1FAE5;
            color: #065F46;
            font-weight: 600;
        }

        .custom-select-option.hidden {
            display: none;
        }

        .custom-select-no-results {
            padding: 12px 16px;
            color: #9CA3AF;
            font-size: 14px;
            text-align: center;
            display: none;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-left">
            <h1>Transaksi Sampah</h1>
            <span class="header-separator">/</span>
            <span class="header-subtitle">Transaksi Baru</span>
        </div>
    </div>

    <form action="{{ route('waste-transactions.sales.store') }}" method="POST" enctype="multipart/form-data" id="sale-form">
        @csrf
        <div class="main-container">
            <div class="form-container">
                @if(session('error'))
                    <div class="alert alert-error">{{ session('error') }}</div>
                @endif

                <div class="form-header">
                    <h2 class="form-title">Informasi Transaksi</h2>
                    <div class="form-actions">
                        <a href="{{ route('waste-transactions.index') }}" class="btn-cancel">Batal</a>
                        <button type="submit" class="btn-save" id="submit-btn">Simpan Transaksi</button>
                    </div>
                </div>

                <!-- Transaction Type FIRST -->
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Tipe Transaksi <span>*</span></label>
                        <select name="type" id="transaction_type" class="form-select" required onchange="onTransactionTypeChange()">
                            <option value="sale" {{ old('type', 'sale') === 'sale' ? 'selected' : '' }}>Penjualan</option>
                            <option value="processing" {{ old('type') === 'processing' ? 'selected' : '' }}>Pengolahan</option>
                        </select>
                        @error('type')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group" id="metode-pengolahan-group" style="display: none;">
                        <label class="form-label">Metode Daur Ulang <span>*</span></label>
                        <input type="text" name="metode_pengolahan" id="metode_pengolahan" class="form-input" value="{{ old('metode_pengolahan') }}" placeholder="Contoh: Daur Ulang, Kompos, dll">
                        @error('metode_pengolahan')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Bank Sampah and Offtaker -->
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Bank Sampah <span>*</span></label>
                        @php
                            $isCabang = Auth::guard('admin')->check() && Auth::guard('admin')->user()->role == 'cabang';
                            $cabangBankSampahId = $isCabang ? Auth::guard('admin')->user()->id_bank_sampah : null;
                        @endphp
                        <input type="hidden" name="bank_sampah_id" id="bank_sampah_id" value="{{ old('bank_sampah_id', $isCabang ? $cabangBankSampahId : $bankSampahId) }}" required>

                        @if($isCabang)
                            {{-- Readonly display for cabang role --}}
                            @php
                                $selectedBankSampah = $bankSampahList->firstWhere('id', $cabangBankSampahId);
                            @endphp
                            <div class="form-input" style="background-color: #f3f4f6; cursor: not-allowed;">
                                {{ $selectedBankSampah ? $selectedBankSampah->nama_bank_sampah : 'Bank Sampah Tidak Ditemukan' }}
                            </div>
                        @else
                            {{-- Editable dropdown for non-cabang roles --}}
                            <div class="custom-select-wrapper" id="bank-sampah-select">
                                <div class="custom-select-trigger" onclick="toggleDropdown('bank-sampah-select')">
                                    <span class="selected-text placeholder">Pilih Bank Sampah</span>
                                    <span class="arrow">
                                        <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
                                            <path d="M2.5 4.5L6 8L9.5 4.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </span>
                                </div>
                                <div class="custom-select-dropdown">
                                    <div class="custom-select-search">
                                        <input type="text" placeholder="Cari bank sampah..." onkeyup="filterOptions('bank-sampah-select', this.value)">
                                    </div>
                                    <div class="custom-select-options">
                                        @foreach($bankSampahList as $bs)
                                            <div class="custom-select-option" data-value="{{ $bs->id }}" data-text="{{ $bs->nama_bank_sampah }}" onclick="selectOption('bank-sampah-select', '{{ $bs->id }}', '{{ $bs->nama_bank_sampah }}')">
                                                {{ $bs->nama_bank_sampah }}
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="custom-select-no-results">Tidak ada hasil</div>
                                </div>
                            </div>
                        @endif
                        @error('bank_sampah_id')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Pembeli/Offtaker <span>*</span></label>
                        <input type="hidden" name="offtaker_id" id="offtaker_id" value="{{ old('offtaker_id') }}" required>
                        <div class="custom-select-wrapper" id="offtaker-select">
                            <div class="custom-select-trigger" onclick="toggleDropdown('offtaker-select')">
                                <span class="selected-text placeholder">Pilih Pembeli</span>
                                <span class="arrow">
                                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
                                        <path d="M2.5 4.5L6 8L9.5 4.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </span>
                            </div>
                            <div class="custom-select-dropdown">
                                <div class="custom-select-search">
                                    <input type="text" placeholder="Cari pembeli..." onkeyup="filterOptions('offtaker-select', this.value)">
                                </div>
                                <div class="custom-select-options">
                                    @foreach($offtakerList as $of)
                                        <div class="custom-select-option" data-value="{{ $of->id }}" data-text="{{ $of->nama }}" onclick="selectOption('offtaker-select', '{{ $of->id }}', '{{ $of->nama }}')">
                                            {{ $of->nama }}
                                        </div>
                                    @endforeach
                                </div>
                                <div class="custom-select-no-results">Tidak ada hasil</div>
                            </div>
                        </div>
                        @error('offtaker_id')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Tanggal Transaksi <span>*</span></label>
                        <input type="date" name="tanggal_transaksi" class="form-input" value="{{ old('tanggal_transaksi', date('Y-m-d')) }}" required>
                        @error('tanggal_transaksi')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Foto Struk</label>
                        <input type="file" name="struk" class="form-input" accept="image/*">
                        @error('struk')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <h3 class="section-title">Item Transaksi</h3>

                <div class="items-section">
                    <div class="items-header">
                        <div style="display: flex; align-items: center; gap: 16px;">
                            <h3 style="margin: 0;">Daftar Sampah</h3>
                            <span class="items-count" id="items-count">0 item</span>
                        </div>
                        <div style="display: flex; flex-direction: column; align-items: flex-end;">
                            <button type="button" id="desktop-add-btn" class="desktop-add-btn" onclick="showAddItemModal()" disabled>
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <line x1="12" y1="5" x2="12" y2="19"></line>
                                    <line x1="5" y1="12" x2="19" y2="12"></line>
                                </svg>
                                Tambah Item Sampah
                            </button>
                            <small class="button-hint show" id="button-hint">Pilih Bank Sampah terlebih dahulu</small>
                        </div>
                    </div>

                    <table class="items-table">
                        <thead>
                            <tr>
                                <th>Nama Sampah</th>
                                <th>Qty (kg)</th>
                                <th>Harga Jual/kg</th>
                                <th>Total</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="items-tbody">
                            <!-- Items will be added here dynamically -->
                        </tbody>
                    </table>
                </div>

                <div class="form-group" style="margin-top: 20px;">
                    <label class="form-label">Catatan</label>
                    <textarea name="notes" class="form-input" rows="3" placeholder="Catatan tambahan (opsional)">{{ old('notes') }}</textarea>
                </div>
            </div>

            <div class="summary-container">
                <h3 class="summary-title">Ringkasan</h3>
                <div class="summary-row">
                    <span class="summary-label">Total Qty</span>
                    <span class="summary-value" id="total-qty">0 kg</span>
                </div>
                <div class="summary-row">
                    <span class="summary-label">Total Harga Beli</span>
                    <span class="summary-value" id="total-harga-beli">Rp 0</span>
                </div>
                <div class="summary-row summary-total">
                    <span class="summary-label">Total Transaksi</span>
                    <span class="summary-value" id="total-transaksi">Rp 0</span>
                </div>
                <div class="profit-display">
                    <div class="profit-label">Estimasi Laba</div>
                    <div class="profit-value" id="profit-value">Rp 0</div>
                </div>
            </div>
        </div>
    </form>

    <!-- Add Item Modal -->
    <div id="addItemModal" class="modal">
        <div class="modal-content">
            <button class="modal-close" onclick="closeAddItemModal()">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#6B7271" stroke-width="2">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
            <h2 class="modal-title">Tambah Item Sampah</h2>

            <form id="addItemForm">
                <div class="form-group">
                    <label class="form-label">Jenis Sampah <span>*</span></label>
                    <select id="modal_sampah_id" class="form-select" required onchange="updateModalInfo()">
                        <option value="">Pilih Jenis Sampah</option>
                    </select>
                    <div class="stock-info" id="modal-stock-info"></div>
                </div>

                <div class="form-group">
                    <label class="form-label">Qty (kg) <span>*</span></label>
                    <input type="number" id="modal_quantity" class="form-input" step="0.01" min="0.01" required onchange="updateModalTotal()">
                </div>

                <div class="form-group">
                    <label class="form-label">Harga Jual/kg <span>*</span></label>
                    <input type="number" id="modal_harga_jual" class="form-input" min="0" required onchange="updateModalTotal()">
                </div>

                <div class="form-group">
                    <label class="form-label">Total</label>
                    <input type="text" id="modal_total" class="form-input" readonly>
                </div>

                <div class="modal-buttons">
                    <button type="button" class="modal-button cancel-button" onclick="closeAddItemModal()">Batal</button>
                    <button type="button" class="modal-button confirm-button" onclick="addItemFromModal()">Tambah Item</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Mobile Sticky Add Button -->
    <div class="mobile-sticky-add-btn">
        <button type="button" id="mobile-add-btn" class="btn" onclick="showAddItemModal()" disabled>
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            <span id="mobile-btn-text">Pilih Bank Sampah terlebih dahulu</span>
        </button>
    </div>

    <script>
        let itemIndex = 0;
        let inventoryData = @json($inventory);

        // Custom Select Functions
        function toggleDropdown(wrapperId) {
            const wrapper = document.getElementById(wrapperId);
            const trigger = wrapper.querySelector('.custom-select-trigger');
            const dropdown = wrapper.querySelector('.custom-select-dropdown');

            // Close all other dropdowns
            document.querySelectorAll('.custom-select-wrapper').forEach(w => {
                if (w.id !== wrapperId) {
                    w.querySelector('.custom-select-trigger').classList.remove('active');
                    w.querySelector('.custom-select-dropdown').classList.remove('show');
                }
            });

            trigger.classList.toggle('active');
            dropdown.classList.toggle('show');

            if (dropdown.classList.contains('show')) {
                const searchInput = dropdown.querySelector('input');
                searchInput.value = '';
                searchInput.focus();
                filterOptions(wrapperId, '');
            }
        }

        function selectOption(wrapperId, value, text) {
            const wrapper = document.getElementById(wrapperId);
            const trigger = wrapper.querySelector('.custom-select-trigger');
            const dropdown = wrapper.querySelector('.custom-select-dropdown');
            const selectedText = trigger.querySelector('.selected-text');
            const hiddenInput = wrapperId === 'bank-sampah-select' ? document.getElementById('bank_sampah_id') : document.getElementById('offtaker_id');

            // Update hidden input
            hiddenInput.value = value;

            // Update display
            selectedText.textContent = text;
            selectedText.classList.remove('placeholder');

            // Update selected state
            wrapper.querySelectorAll('.custom-select-option').forEach(opt => {
                opt.classList.remove('selected');
                if (opt.dataset.value === value) {
                    opt.classList.add('selected');
                }
            });

            // Close dropdown
            trigger.classList.remove('active');
            dropdown.classList.remove('show');

            // Trigger inventory update if bank sampah changed
            if (wrapperId === 'bank-sampah-select') {
                if (value) {
                    fetchInventory(value);
                    toggleAddItemButtons(true);
                } else {
                    toggleAddItemButtons(false);
                }
            }
        }

        function toggleAddItemButtons(enabled) {
            const desktopBtn = document.getElementById('desktop-add-btn');
            const mobileBtn = document.getElementById('mobile-add-btn');
            const hint = document.getElementById('button-hint');
            const mobileText = document.getElementById('mobile-btn-text');

            if (enabled) {
                desktopBtn.disabled = false;
                mobileBtn.disabled = false;
                hint.classList.remove('show');
                mobileText.textContent = 'Tambah Item Sampah';
            } else {
                desktopBtn.disabled = true;
                mobileBtn.disabled = true;
                hint.classList.add('show');
                mobileText.textContent = 'Pilih Bank Sampah terlebih dahulu';
            }
        }

        function filterOptions(wrapperId, searchTerm) {
            const wrapper = document.getElementById(wrapperId);
            const options = wrapper.querySelectorAll('.custom-select-option');
            const noResults = wrapper.querySelector('.custom-select-no-results');
            let hasVisible = false;

            searchTerm = searchTerm.toLowerCase();

            options.forEach(option => {
                const text = option.dataset.text.toLowerCase();
                if (text.includes(searchTerm)) {
                    option.classList.remove('hidden');
                    hasVisible = true;
                } else {
                    option.classList.add('hidden');
                }
            });

            noResults.style.display = hasVisible ? 'none' : 'block';
        }

        // Close dropdowns when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.custom-select-wrapper')) {
                document.querySelectorAll('.custom-select-wrapper').forEach(wrapper => {
                    wrapper.querySelector('.custom-select-trigger').classList.remove('active');
                    wrapper.querySelector('.custom-select-dropdown').classList.remove('show');
                });
            }
        });

        function fetchInventory(bankSampahId) {
            fetch(`{{ url('dashboard/waste-transactions/get-inventory') }}?bank_sampah_id=${bankSampahId}`, {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                inventoryData = data;
                updateAllSampahOptions();
            });
        }

        function updateAllSampahOptions() {
            const modalSelect = document.getElementById('modal_sampah_id');
            if (!modalSelect) return;

            const currentValue = modalSelect.value;
            modalSelect.innerHTML = '<option value="">Pilih Jenis Sampah</option>';
            inventoryData.forEach(item => {
                const option = document.createElement('option');
                option.value = item.sampah_id;
                option.textContent = `${item.sampah_nama} (${item.quantity} ${item.sampah_satuan})`;
                option.dataset.stock = item.quantity;
                option.dataset.satuan = item.sampah_satuan;
                option.dataset.nama = item.sampah_nama;
                option.dataset.avgHargaBeli = item.avg_harga_beli || 0;
                if (item.sampah_id == currentValue) option.selected = true;
                modalSelect.appendChild(option);
            });
        }

        // Modal Functions
        function showAddItemModal() {
            document.getElementById('addItemModal').classList.add('show');
            updateAllSampahOptions();
            document.getElementById('addItemForm').reset();
            document.getElementById('modal_total').value = '';
            document.getElementById('modal-stock-info').textContent = '';
        }

        function closeAddItemModal() {
            document.getElementById('addItemModal').classList.remove('show');
        }

        function updateModalInfo() {
            const select = document.getElementById('modal_sampah_id');
            const stockInfo = document.getElementById('modal-stock-info');
            const option = select.options[select.selectedIndex];

            if (option.dataset.stock) {
                stockInfo.textContent = `Stok tersedia: ${option.dataset.stock} ${option.dataset.satuan}`;
            } else {
                stockInfo.textContent = '';
            }
            updateModalTotal();
        }

        function updateModalTotal() {
            const qty = parseFloat(document.getElementById('modal_quantity').value) || 0;
            const price = parseFloat(document.getElementById('modal_harga_jual').value) || 0;
            const total = qty * price;

            document.getElementById('modal_total').value = total > 0 ? `Rp ${total.toLocaleString('id-ID')}` : '';
        }

        function addItemFromModal() {
            const sampahId = document.getElementById('modal_sampah_id').value;
            const qty = document.getElementById('modal_quantity').value;
            const price = document.getElementById('modal_harga_jual').value;

            if (!sampahId || !qty || !price) {
                alert('Semua field harus diisi!');
                return;
            }

            const select = document.getElementById('modal_sampah_id');
            const option = select.options[select.selectedIndex];
            const sampahNama = option.dataset.nama;
            const satuan = option.dataset.satuan || 'kg';
            const avgHargaBeli = parseFloat(option.dataset.avgHargaBeli) || 0;
            const total = parseFloat(qty) * parseFloat(price);

            addItemToTable(sampahId, sampahNama, qty, price, total, satuan, avgHargaBeli);
            closeAddItemModal();
            calculateSummary();
        }

        function addItemToTable(sampahId, sampahNama, qty, price, total, satuan, avgHargaBeli) {
            const tbody = document.getElementById('items-tbody');
            const tr = document.createElement('tr');
            tr.dataset.index = itemIndex;

            tr.innerHTML = `
                <td class="item-name" data-label="Nama Sampah">
                    ${sampahNama}
                    <input type="hidden" name="items[${itemIndex}][sampah_id]" value="${sampahId}">
                    <input type="hidden" name="items[${itemIndex}][avg_harga_beli]" value="${avgHargaBeli}">
                </td>
                <td data-label="Qty">${parseFloat(qty).toFixed(2)} ${satuan}
                    <input type="hidden" name="items[${itemIndex}][quantity]" value="${qty}">
                </td>
                <td data-label="Harga Jual">Rp ${parseFloat(price).toLocaleString('id-ID')}
                    <input type="hidden" name="items[${itemIndex}][harga_jual]" value="${price}">
                </td>
                <td class="item-total" data-label="Total">Rp ${total.toLocaleString('id-ID')}</td>
                <td class="item-actions">
                    <button type="button" class="btn-small btn-danger" onclick="removeItem(${itemIndex})">Hapus</button>
                </td>
            `;

            tbody.appendChild(tr);
            itemIndex++;
        }

        function removeItem(index) {
            const tr = document.querySelector(`tr[data-index="${index}"]`);
            if (tr) {
                tr.remove();
                calculateSummary();
            }
        }

        function calculateSummary() {
            const tbody = document.getElementById('items-tbody');
            const rows = tbody.querySelectorAll('tr');

            let totalQty = 0;
            let totalPenjualan = 0;
            let totalHargaBeli = 0;
            let itemCount = rows.length;

            rows.forEach(row => {
                const qtyInput = row.querySelector('input[name*="[quantity]"]');
                const priceInput = row.querySelector('input[name*="[harga_jual]"]');
                const avgHargaBeliInput = row.querySelector('input[name*="[avg_harga_beli]"]');

                const qty = parseFloat(qtyInput?.value) || 0;
                const price = parseFloat(priceInput?.value) || 0;
                const avgHargaBeli = parseFloat(avgHargaBeliInput?.value) || 0;

                totalQty += qty;
                totalPenjualan += qty * price;
                totalHargaBeli += qty * avgHargaBeli;
            });

            // Calculate actual profit
            const profit = totalPenjualan - totalHargaBeli;

            // Update item count
            document.getElementById('items-count').textContent = `${itemCount} item`;

            // Update summary
            document.getElementById('total-qty').textContent = totalQty.toFixed(2) + ' kg';
            document.getElementById('total-harga-beli').textContent = 'Rp ' + totalHargaBeli.toLocaleString('id-ID');
            document.getElementById('total-transaksi').textContent = 'Rp ' + totalPenjualan.toLocaleString('id-ID');
            document.getElementById('profit-value').textContent = 'Rp ' + profit.toLocaleString('id-ID');
        }

        function toggleMetodePengolahan() {
            const typeSelect = document.getElementById('transaction_type');
            const metodeGroup = document.getElementById('metode-pengolahan-group');
            const metodeInput = document.getElementById('metode_pengolahan');
            const isProcessing = typeSelect.value === 'processing';

            // Only toggle metode pengolahan field - everything else stays the same
            if (isProcessing) {
                metodeGroup.style.display = 'block';
                metodeInput.required = true;
            } else {
                metodeGroup.style.display = 'none';
                metodeInput.required = false;
                metodeInput.value = '';
            }
        }

        function onTransactionTypeChange() {
            // Toggle metode pengolahan visibility
            toggleMetodePengolahan();

            // Load offtakers based on transaction type
            const type = document.getElementById('transaction_type').value;
            loadOfftakersByType(type);
        }

        function loadOfftakersByType(type) {
            fetch(`{{ url('dashboard/waste-transactions/get-offtakers-by-type') }}?type=${type}`, {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                updateOfftakerOptions(data);
            })
            .catch(error => {
                console.error('Error loading offtakers:', error);
            });
        }

        function updateOfftakerOptions(offtakers) {
            const wrapper = document.getElementById('offtaker-select');
            const optionsContainer = wrapper.querySelector('.custom-select-options');
            const currentValue = document.getElementById('offtaker_id').value;

            // Clear existing options
            optionsContainer.innerHTML = '';

            // Add new options
            offtakers.forEach(offtaker => {
                const option = document.createElement('div');
                option.className = 'custom-select-option';
                option.dataset.value = offtaker.id;
                option.dataset.text = offtaker.nama;
                option.textContent = offtaker.nama;
                option.onclick = function() {
                    selectOption('offtaker-select', offtaker.id, offtaker.nama);
                };

                // Restore selection if it exists in new list
                if (offtaker.id == currentValue) {
                    option.classList.add('selected');
                }

                optionsContainer.appendChild(option);
            });

            // Reset selection if current value not in new list
            const hasCurrentValue = offtakers.some(of => of.id == currentValue);
            if (!hasCurrentValue && currentValue) {
                document.getElementById('offtaker_id').value = '';
                const selectedText = wrapper.querySelector('.selected-text');
                selectedText.textContent = 'Pilih Pembeli';
                selectedText.classList.add('placeholder');
            }
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Metode Pengolahan visibility
            toggleMetodePengolahan();

            // Load offtakers based on initial transaction type
            const initialType = document.getElementById('transaction_type').value;
            loadOfftakersByType(initialType);

            // Initialize Bank Sampah select if value exists
            const bankSampahValue = document.getElementById('bank_sampah_id').value;
            const bankSampahSelect = document.getElementById('bank-sampah-select');

            if (bankSampahValue) {
                // If there's a dropdown (non-cabang user)
                if (bankSampahSelect) {
                    const bankOption = document.querySelector('#bank-sampah-select .custom-select-option[data-value="' + bankSampahValue + '"]');
                    if (bankOption) {
                        selectOption('bank-sampah-select', bankSampahValue, bankOption.dataset.text);
                    }
                } else {
                    // For cabang users (no dropdown, bank sampah is readonly)
                    // Fetch inventory and enable buttons
                    fetchInventory(bankSampahValue);
                    toggleAddItemButtons(true);
                }
            } else {
                // Disable buttons if no bank sampah selected
                toggleAddItemButtons(false);
            }

            // Initialize Offtaker select if value exists (after offtakers are loaded)
            setTimeout(function() {
                const offtakerValue = document.getElementById('offtaker_id').value;
                if (offtakerValue) {
                    const offtakerOption = document.querySelector('#offtaker-select .custom-select-option[data-value="' + offtakerValue + '"]');
                    if (offtakerOption) {
                        selectOption('offtaker-select', offtakerValue, offtakerOption.dataset.text);
                    }
                }
            }, 500);

            // Update initial count
            calculateSummary();
        });
    </script>
</body>
</html>
@endsection
