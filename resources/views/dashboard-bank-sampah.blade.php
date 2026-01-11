@extends('dashboard')
@section('content')
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bank Sampah - Admin Panel</title>
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
            font-family: 'Urbanist', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #fff;
            color: #1e293b;
        }

        .header {
            padding: 1rem 2rem 0rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header h1 {
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
            font-family: 'Urbanist', sans-serif;
            font-size: 15px;
            font-weight: 400;
            color: #6B7271;
            padding: 6px 40px 6px 14px;
            border-radius: 18px;
        }

        .search-input::placeholder {
            color: #6B7271;
            font-family: 'Urbanist', sans-serif;
            font-size: 15px;
            font-weight: 400;
            opacity: 1;
        }

        .search-box:focus-within {
            background: #fff;
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

        .search-btn {
            position: absolute;
            right: 8px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            padding: 4px;
            border-radius: 4px;
            transition: background-color 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .search-btn:hover {
            background-color: rgba(57, 116, 110, 0.1);
        }

        .search-btn img {
            width: 16px;
            height: 16px;
        }

        .search-box input {
            margin-right: 6px;
        }

        .action-buttons {
            display: flex;
            gap: 6px;
        }

        .action-btn {
            padding: 0.25rem;
            background: none;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 20%;
            transition: background-color 0.2s ease;
            flex-shrink: 0;
        }

        .action-btn:hover {
            background-color: #f3f4f6;
        }

        .action-btn img {
            width: 16px;
            height: 16px;
        }

        .action-btn.detail-btn {
            color: #3b82f6;
        }

        .action-btn.detail-btn:hover {
            background-color: #eff6ff;
        }

        .action-btn.edit-btn {
            color: #f59e0b;
        }

        .action-btn.edit-btn:hover {
            background-color: #fffbeb;
        }

        .action-btn.delete-btn {
            color: #ef4444;
        }

        .action-btn.delete-btn:hover {
            background-color: #fef2f2;
        }

        /* Export Button Styles */
        .export-dropdown {
            position: relative;
        }

        .export-button {
            padding: 0 1rem;
            background: #39746E;
            border: 1px solid #E5E6E6;
            border-radius: 8px;
            font-family: 'Urbanist', sans-serif;
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
            font-family: 'Urbanist', sans-serif;
            font-size: 14px;
            color: #1e293b;
        }

        /* Export Form Styles */
        .export-form {
            margin: 1rem 0;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-label {
            display: block;
            font-family: 'Urbanist', sans-serif;
            font-size: 14px;
            font-weight: 500;
            color: #374151;
            margin-bottom: 0.5rem;
        }

        .form-select, .form-input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            font-family: 'Urbanist', sans-serif;
            font-size: 14px;
            color: #1e293b;
            background: #fff;
            transition: border-color 0.2s;
        }

        .form-select:focus, .form-input:focus {
            outline: none;
            border-color: #39746E;
        }

        .date-range-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        .date-input-group {
            display: flex;
            flex-direction: column;
        }

        .delete-button {
            padding: 0 1rem;
            background: transparent;
            border: 1px solid #FDCED1;
            border-radius: 8px;
            font-family: 'Urbanist', sans-serif;
            font-size: 15px;
            font-weight: 600;
            color: #F73541;
            cursor: pointer;
            display: none;
            align-items: center;
            gap: 8px;
            height: 37px;
        }

        .add-button {
            padding: 0 1rem;
            background: #39746E;
            border: 1px solid #E5E6E6;
            border-radius: 8px;
            font-family: 'Urbanist', sans-serif;
            font-size: 15px;
            font-weight: 600;
            color: #DFF0EE;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            height: 37px;
        }

        .table-container {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            margin-top: 8px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            padding: 12px 16px;
            text-align: left;
            font-family: 'Urbanist', sans-serif;
            font-size: 14px;
            font-weight: 400;
            color: #6B7271;
            border-top: 1px solid #E5E6E6;
            border-bottom: 1px solid #E5E6E6;
        }

        td {
            padding: 0.5rem;
            border-bottom: 1px solid #e2e8f0;
            color: #1e293b;
        }

        .checkbox-cell {
            width: 20px;
            text-align: center;
        }

        .bank-checkbox {
            width: 17px;
            height: 17px;
            border-radius: 3px;
            border: 1px solid #E4E2DD;
            background-color: #FAF6F5;
            cursor: pointer;
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            position: relative;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .bank-checkbox:checked {
            background-color: #39746E;
            border-color: #39746E;
        }

        .bank-checkbox:checked::after {
            content: '';
            position: absolute;
            width: 5px;
            height: 8px;
            border: solid white;
            border-width: 0 2px 2px 0;
            transform: rotate(45deg);
            margin-top: -4px;
            margin-left: 0px;
        }

        .select-all-checkbox {
            width: 17px;
            height: 17px;
            border-radius: 3px;
            border: 1px solid #E4E2DD;
            background-color: #FAF6F5;
            cursor: pointer;
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            position: relative;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .select-all-checkbox:checked {
            background-color: #39746E;
            border-color: #39746E;
        }

        .select-all-checkbox:checked::after {
            content: '';
            position: absolute;
            width: 5px;
            height: 8px;
            border: solid white;
            border-width: 0 2px 2px 0;
            transform: rotate(45deg);
            margin-top: -4px;
            margin-left: 0px;
        }

        .action-cell {
            width: 100px;
            text-align: center;
        }

        .action-buttons-cell {
            display: flex;
            gap: 0.5rem;
            justify-content: center;
        }

        .action-btn {
            padding: 0.25rem;
            background: none;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 20%;
            transition: background-color 0.2s ease;
        }

        .action-btn:hover {
            background-color: #f3f4f6;
        }

        .action-btn img {
            width: 16px;
            height: 16px;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
        }

        .modal.show {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background: white;
            border-radius: 16px;
            padding: 24px;
            position: relative;
            max-width: 400px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
        }

        .modal-close {
            position: absolute;
            top: 16px;
            right: 16px;
            background: none;
            border: none;
            cursor: pointer;
            padding: 4px;
            border-radius: 4px;
            transition: background-color 0.2s;
        }

        .modal-close:hover {
            background-color: #f3f4f6;
        }

        .modal-close img {
            width: 20px;
            height: 20px;
        }

        .modal-icon {
            width: 48px;
            height: 48px;
            margin: 0 auto 16px;
            display: block;
        }

        .modal-title {
            font-family: 'Urbanist', sans-serif;
            font-size: 18px;
            font-weight: 700;
            color: #1e293b;
            text-align: center;
            margin-bottom: 8px;
        }

        .modal-subtitle {
            font-family: 'Urbanist', sans-serif;
            font-size: 14px;
            color: #6B7271;
            text-align: center;
            margin-bottom: 24px;
        }

        .modal-buttons {
            display: flex;
            gap: 12px;
            margin-top: 24px;
        }

        .modal-button {
            flex: 1;
            padding: 12px 16px;
            border-radius: 8px;
            font-family: 'Urbanist', sans-serif;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            transition: all 0.2s;
        }

        .cancel-button {
            background: #F8F9FA;
            color: #6B7271;
            border: 1px solid #E5E6E6;
        }

        .cancel-button:hover {
            background: #E9ECEF;
        }

        .confirm-button {
            background: #39746E;
            color: #DFF0EE;
        }

        .confirm-button:hover {
            background: #2d5a55;
        }

        .no-results {
            text-align: center;
            padding: 3rem;
            color: #6b7280;
        }

        @media (max-width: 768px) {
            .container {
                padding: 1rem;
            }

            .controls {
                flex-direction: column;
                align-items: stretch;
            }

            .search-filter {
                flex-direction: column;
            }

            .table-container {
                overflow-x: auto;
            }

            .table {
                min-width: 800px;
            }
        }

        .pagination-container {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            margin-top: 8px;
        }

        .pagination-info {
            font-family: 'Urbanist', sans-serif;
            font-size: 16px;
            font-weight: 500;
            color: #6B7271;
            margin-right: 8px;
        }

        .pagination-btn {
            font-family: 'Urbanist', sans-serif;
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

        .table tr:hover {
            background: #F8F9FA;
        }

        .kode-cell {
            min-width: 80px;
            max-width: 100px;
        }

        .bank-kode {
            font-family: 'Urbanist', sans-serif;
            font-size: 11px;
            font-weight: 600;
            color: #39746E;
        }

        .nama-cell {
            min-width: 150px;
            max-width: 200px;
        }

        .bank-nama {
            font-family: 'Urbanist', sans-serif;
            font-size: 11px;
            font-weight: 400;
            color: #6B7271;
            margin-bottom: 0.25rem;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .alamat-cell {
            min-width: 150px;
            max-width: 200px;
        }

        .bank-alamat {
            font-family: 'Urbanist', sans-serif;
            font-size: 11px;
            font-weight: 400;
            color: #6B7271;
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .penanggung-jawab-cell {
            min-width: 150px;
            max-width: 200px;
        }

        .bank-penanggung-jawab {
            font-family: 'Urbanist', sans-serif;
            font-size: 12px;
            font-weight: 400;
            color: #6B7271;
            line-height: 1.5;
        }

        .kontak-cell {
            min-width: 120px;
            max-width: 150px;
        }

        .bank-kontak {
            font-family: 'Urbanist', sans-serif;
            font-size: 12px;
            font-weight: 400;
            color: #6B7271;
            line-height: 1.5;
        }

        .foto-cell {
            width: 100px;
            text-align: center;
        }

        .tipe-layanan-cell {
            width: 120px;
            text-align: center;
        }

        .action-buttons-cell {
            display: flex;
            gap: 4px;
            justify-content: flex-start;
            min-width: 120px;
            flex-shrink: 0;
        }

        /* Memastikan kolom action tetap terlihat */
        th:last-child {
            position: sticky;
            right: 0;
            background: white;
            z-index: 10;
            box-shadow: -2px 0 4px rgba(0, 0, 0, 0.1);
        }

        td:last-child {
            position: sticky;
            right: 0;
            background: white;
            z-index: 10;
            box-shadow: -2px 0 4px rgba(0, 0, 0, 0.1);
        }

        /* Responsive design untuk device kecil */
        @media (max-width: 1200px) {
            .kode-cell {
                min-width: 70px;
                max-width: 80px;
            }
            
            .nama-cell {
                min-width: 120px;
                max-width: 150px;
            }
            
            .alamat-cell {
                min-width: 120px;
                max-width: 150px;
            }
            
            .penanggung-jawab-cell {
                min-width: 100px;
                max-width: 120px;
            }
            
            .kontak-cell {
                min-width: 100px;
                max-width: 120px;
            }
            
            .tipe-layanan-cell {
                width: 100px;
            }
            
            .action-buttons-cell {
                min-width: 100px;
            }
        }

        @media (max-width: 768px) {
            .table-container {
                overflow-x: auto;
            }
            
            table {
                min-width: 800px;
            }
            
            .kode-cell {
                min-width: 60px;
                max-width: 70px;
            }
            
            .nama-cell {
                min-width: 100px;
                max-width: 120px;
            }
            
            .alamat-cell {
                min-width: 100px;
                max-width: 120px;
            }
        }

        /* Loading Styles */
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

        .table-loading {
            position: relative;
            min-height: 200px;
        }

        .table-loading::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.8);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10;
        }

        .table-loading::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 30px;
            height: 30px;
            border: 3px solid #f3f3f3;
            border-top: 3px solid #00B6A0;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            z-index: 11;
        }

        .button-loading {
            position: relative;
            pointer-events: none;
            opacity: 0.7;
        }

        .button-loading::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 16px;
            height: 16px;
            border: 2px solid transparent;
            border-top: 2px solid currentColor;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        .search-box.loading .search-icon {
            animation: spin 1s linear infinite;
        }

        .filter-button.loading {
            pointer-events: none;
            opacity: 0.7;
        }

        .filter-button.loading::after {
            content: '';
            position: absolute;
            top: 50%;
            right: 12px;
            transform: translateY(-50%);
            width: 12px;
            height: 12px;
            border: 2px solid transparent;
            border-top: 2px solid #39746E;
            border-radius: 50%;
            animation: spin 1s linear infinite;
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

    @include('partials.dashboard-header-bar', ['title' => 'Bank Sampah'])
    <div class="container">
        <div class="controls">
            <div class="search-filter">
                <div class="search-box">
                    <input type="text" class="search-input" placeholder="Cari data disini" id="searchInput">
                    <button type="button" class="search-btn" id="searchBtn" onclick="filterBanks()">
                    <img src="{{ asset('icon/ic_search.svg') }}" alt="Search" class="search-icon">
                    </button>
                </div>
            </div>
            <div class="action-buttons">
                <button class="delete-button" id="deleteButton">
                    <span>Hapus Bank Sampah</span>
                    <img src="{{ asset('icon/ic_red_delete.svg') }}" alt="Delete" width="16" height="16">
                </button>
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
                <button class="add-button" id="addButton">
                    <span>Tambah Bank Sampah</span>
                    <img src="{{ asset('icon/ic_add.svg') }}" alt="Add" width="16" height="16">
                </button>
            </div>
        </div>

        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>
                            <input type="checkbox" id="selectAll" class="select-all-checkbox">
                        </th>
                        <th>Kode</th>
                        <th>Foto</th>
                        <th>Nama</th>
                        <th>Alamat</th>
                        <th>PIC</th>
                        <th>Kontak</th>
                        <th>Layanan</th>
                        <th>Maps</th>
                        <th>Koordinat</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="bankTableBody">
                    @foreach ($bankSampah as $bank)
                    <tr>
                        <td>
                            <input type="checkbox" class="bank-checkbox" value="{{ $bank->id }}" onchange="updateSelectedBanks()">
                        </td>
                        <td class="kode-cell">
                            <span class="bank-kode">{{ $bank->kode_bank_sampah }}</span>
                        </td>
                        <td class="foto-cell">
                            @if($bank->foto)
                                <img src="{{ $bank->foto }}" alt="Foto Bank Sampah" style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px;" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                <div style="width: 50px; height: 50px; background: #f3f4f6; border-radius: 8px; display: none; align-items: center; justify-content: center; color: #9ca3af; font-size: 10px; text-align: center; padding: 2px;">
                                    <span>{{ $bank->foto ? 'URL Error' : 'No Image' }}</span>
                                </div>
                            @else
                                <div style="width: 50px; height: 50px; background: #f3f4f6; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #9ca3af; font-size: 10px; text-align: center; padding: 2px;">
                                    <span>No Image</span>
                                </div>
                            @endif
                        </td>
                        <td class="nama-cell">
                            <span class="bank-nama">{{ $bank->nama_bank_sampah }}</span>
                        </td>
                        <td class="alamat-cell">
                            <span class="bank-alamat">{{ $bank->alamat_bank_sampah }}</span>
                        </td>
                        <td class="penanggung-jawab-cell">
                            <span class="bank-penanggung-jawab">{{ $bank->nama_penanggung_jawab }}</span>
                        </td>
                        <td class="kontak-cell">
                            <span class="bank-kontak">{{ $bank->kontak_penanggung_jawab }}</span>
                        </td>
                        <td class="tipe-layanan-cell">
                            @if($bank->tipe_layanan == 'jemput')
                                <span style="color: #059669; font-weight: 600; font-size: 12px;">Jemput</span>
                            @elseif($bank->tipe_layanan == 'tempat')
                                <span style="color: #DC2626; font-weight: 600; font-size: 12px;">Tempat</span>
                            @else
                                <span style="color: #7C3AED; font-weight: 600; font-size: 12px;">Keduanya</span>
                            @endif
                        </td>
                        <td class="maps-cell">
                            @if($bank->gmaps_link)
                                <a href="{{ $bank->gmaps_link }}" target="_blank" class="maps-link">
                                    <span style="color: #39746E; font-weight: 600; font-size: 12px; text-decoration: underline; cursor: pointer;">Lihat Maps</span>
                                </a>
                            @else
                                <span style="color: #9ca3af; font-size: 12px;">-</span>
                            @endif
                        </td>
                        <td class="koordinat-cell">
                            @if($bank->latitude && $bank->longitude)
                                <span style="color: #374151; font-size: 12px;">{{ $bank->latitude }}, {{ $bank->longitude }}</span>
                            @else
                                <span style="color: #9ca3af; font-size: 12px;">-</span>
                            @endif
                        </td>
                        <td>
                            <div class="action-buttons-cell">
                                <button class="action-btn detail-btn" data-id="{{ $bank->id }}">
                                    <img src="/icon/ic_detail.svg" alt="Detail">
                                </button>
                                <button class="action-btn edit-btn" data-id="{{ $bank->id }}">
                                    <img src="/icon/ic_edit.svg" alt="Edit">
                                </button>
                                <button class="action-btn delete-btn" data-id="{{ $bank->id }}">
                                    <img src="/icon/ic_delete.svg" alt="Delete">
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="no-results" id="noResults" style="display: none;">
                Tidak ada bank sampah yang ditemukan
            </div>
        </div>

        <div class="pagination-container" id="paginationContainer">
            <div class="pagination-info" id="paginationInfo"></div>
            <div class="pagination" id="pagination"></div>
        </div>
    </div>

    <!-- Delete Modal -->
    <div class="modal" id="deleteModal">
        <div class="modal-content">
            <button class="modal-close" id="closeModalBtn">
                <img src="{{ asset('icon/ic_close.svg') }}" alt="Close">
            </button>
            <img src="{{ asset('icon/ic_dialog_delete.svg') }}" alt="Delete" class="modal-icon">
            <h2 class="modal-title">Hapus Bank Sampah</h2>
            <p class="modal-subtitle">Apakah Anda yakin ingin menghapus bank sampah yang dipilih?</p>
            <div class="modal-buttons">
                <button class="modal-button cancel-button" id="cancelDeleteBtn">Batal</button>
                <button class="modal-button confirm-button" id="confirmDeleteBtn">Hapus</button>
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
            <h2 class="modal-title">Export Excel Bank Sampah</h2>
            <p class="modal-subtitle">Pilih periode data yang ingin diexport</p>
            
            <div class="export-form">
                <div class="form-group">
                    <label class="form-label">Periode Export</label>
                    <select class="form-select" id="exportPeriod">
                        <option value="all">Semua Data</option>
                        <option value="today">Hari Ini</option>
                        <option value="yesterday">Kemarin</option>
                        <option value="this_week">Minggu Ini</option>
                        <option value="last_week">Minggu Lalu</option>
                        <option value="this_month">Bulan Ini</option>
                        <option value="last_month">Bulan Lalu</option>
                        <option value="this_year">Tahun Ini</option>
                        <option value="last_year">Tahun Lalu</option>
                        <option value="range">Range Waktu</option>
                    </select>
                </div>
                
                <div class="form-group" id="dateRangeGroup" style="display: none;">
                    <div class="date-range-row">
                        <div class="date-input-group">
                            <label class="form-label">Dari Tanggal</label>
                            <input type="date" class="form-input" id="startDate">
                        </div>
                        <div class="date-input-group">
                            <label class="form-label">Sampai Tanggal</label>
                            <input type="date" class="form-input" id="endDate">
                        </div>
                    </div>
                </div>
            </div>
            
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
            <h2 class="modal-title">Export CSV Bank Sampah</h2>
            <p class="modal-subtitle">Pilih periode data yang ingin diexport</p>
            
            <div class="export-form">
                <div class="form-group">
                    <label class="form-label">Periode Export</label>
                    <select class="form-select" id="csvExportPeriod">
                        <option value="all">Semua Data</option>
                        <option value="today">Hari Ini</option>
                        <option value="yesterday">Kemarin</option>
                        <option value="this_week">Minggu Ini</option>
                        <option value="last_week">Minggu Lalu</option>
                        <option value="this_month">Bulan Ini</option>
                        <option value="last_month">Bulan Lalu</option>
                        <option value="this_year">Tahun Ini</option>
                        <option value="last_year">Tahun Lalu</option>
                        <option value="range">Range Waktu</option>
                    </select>
                </div>
                
                <div class="form-group" id="csvDateRangeGroup" style="display: none;">
                    <div class="date-range-row">
                        <div class="date-input-group">
                            <label class="form-label">Dari Tanggal</label>
                            <input type="date" class="form-input" id="csvStartDate">
                        </div>
                        <div class="date-input-group">
                            <label class="form-label">Sampai Tanggal</label>
                            <input type="date" class="form-input" id="csvEndDate">
                        </div>
                    </div>
                </div>
            </div>
            
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
            <h2 class="modal-title">Export PDF Bank Sampah</h2>
            <p class="modal-subtitle">Pilih periode data yang ingin diexport</p>
            
            <div class="export-form">
                <div class="form-group">
                    <label class="form-label">Periode Export</label>
                    <select class="form-select" id="pdfExportPeriod">
                        <option value="all">Semua Data</option>
                        <option value="today">Hari Ini</option>
                        <option value="yesterday">Kemarin</option>
                        <option value="this_week">Minggu Ini</option>
                        <option value="last_week">Minggu Lalu</option>
                        <option value="this_month">Bulan Ini</option>
                        <option value="last_month">Bulan Lalu</option>
                        <option value="this_year">Tahun Ini</option>
                        <option value="last_year">Tahun Lalu</option>
                        <option value="range">Range Waktu</option>
                    </select>
                </div>
                
                <div class="form-group" id="pdfDateRangeGroup" style="display: none;">
                    <div class="date-range-row">
                        <div class="date-input-group">
                            <label class="form-label">Dari Tanggal</label>
                            <input type="date" class="form-input" id="pdfStartDate">
                        </div>
                        <div class="date-input-group">
                            <label class="form-label">Sampai Tanggal</label>
                            <input type="date" class="form-input" id="pdfEndDate">
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="modal-buttons">
                <button class="modal-button cancel-button" id="cancelPdfBtn">Batal</button>
                <button class="modal-button confirm-button" id="confirmPdfBtn">Export PDF</button>
            </div>
        </div>
    </div>

    <script>
        // Loading Functions
        function showPageLoading(message = 'Memuat...') {
            const overlay = document.getElementById('pageLoadingOverlay');
            overlay.querySelector('p').textContent = message;
            overlay.style.display = 'flex';
        }

        function hidePageLoading() {
            document.getElementById('pageLoadingOverlay').style.display = 'none';
        }

        // Unified loading function - using the same loading as initial page load
        function showLoading(message = 'Memuat...') {
            showPageLoading(message);
        }

        function hideLoading() {
            hidePageLoading();
        }

        // Legacy functions - kept for compatibility but not used
        function showTableLoading() {
            showLoading('Memuat tabel...');
        }

        function hideTableLoading() {
            hideLoading();
        }

        function showSearchLoading() {
            showLoading('Mencari...');
        }

        function hideSearchLoading() {
            hideLoading();
        }

        function showFilterLoading() {
            showLoading('Memfilter...');
        }

        function hideFilterLoading() {
            hideLoading();
        }

        // Data bank sampah dari backend (server-side rendered)
        let banks = [
            @foreach ($bankSampah as $bank)
            {
                id: {{ $bank->id }},
                kode: @json($bank->kode_bank_sampah),
                nama: @json($bank->nama_bank_sampah),
                alamat: @json($bank->alamat_bank_sampah),
                penanggung_jawab: @json($bank->nama_penanggung_jawab),
                kontak: @json($bank->kontak_penanggung_jawab),
                foto: @json($bank->foto),
                tipe_layanan: @json($bank->tipe_layanan)
            },
            @endforeach
        ];
        let selectedBanks = [];

        document.addEventListener('DOMContentLoaded', function() {
            // Show loading on initial page load
            showLoading('Memuat data...');
            
            // Hide loading after a short delay to simulate loading
            setTimeout(() => {
                hideLoading();
            }, 500);

            // Initial data loading from server-side
            const initialPaginationData = {
                current_page: {{ $bankSampah->currentPage() }},
                total: {{ $bankSampah->total() }},
                per_page: {{ $bankSampah->perPage() }},
                first_item: {{ $bankSampah->firstItem() ?? 0 }},
                last_item: {{ $bankSampah->lastItem() ?? 0 }}
            };
            
            // Handle no results and pagination visibility
            const tbody = document.getElementById('bankTableBody');
            const noResults = document.getElementById('noResults');
            const paginationContainer = document.getElementById('paginationContainer');
            
            if ({{ $bankSampah->total() }} === 0) {
                tbody.style.display = 'none';
                noResults.style.display = 'block';
                paginationContainer.style.display = 'none';
            } else {
                tbody.style.display = 'table-row-group';
                noResults.style.display = 'none';
                paginationContainer.style.display = 'flex';
            }
            
            renderPagination(initialPaginationData);
            
            // Populate search input with current search term
            const urlParams = new URLSearchParams(window.location.search);
            const currentSearch = urlParams.get('search');
            if (currentSearch) {
                document.getElementById('searchInput').value = currentSearch;
            }
            
            // Search functionality
            const searchInput = document.getElementById('searchInput');
            
            // Search only when Enter is pressed
            searchInput.addEventListener('keyup', function(e) {
                if (e.key === 'Enter') {
                    filterBanks();
                }
            });

            // Add button
            document.getElementById('addButton').addEventListener('click', function() {
                window.location.href = '{{ route("dashboard.bank.create") }}';
            });

            // Delete button
            document.getElementById('deleteButton').addEventListener('click', function() {
                if (selectedBanks.length > 0) { 
                    showDeleteModal(true); 
                }
            });

            // Select all checkbox
            document.getElementById('selectAll').addEventListener('change', function() {
                const checkboxes = document.querySelectorAll('.bank-checkbox');
                checkboxes.forEach(cb => { cb.checked = this.checked; });
                updateSelectedBanks();
            });

            // Modal buttons
            document.getElementById('closeModalBtn').addEventListener('click', closeDeleteModal);
            document.getElementById('cancelDeleteBtn').addEventListener('click', closeDeleteModal);
            document.getElementById('confirmDeleteBtn').addEventListener('click', confirmDelete);

            // Event delegation for action buttons
            document.addEventListener('click', function(e) {
                if (e.target.closest('.detail-btn')) {
                    const id = e.target.closest('.detail-btn').getAttribute('data-id');
                    window.location.href = '{{ route("dashboard.bank.show", ":id") }}'.replace(':id', id);
                } else if (e.target.closest('.edit-btn')) {
                    const id = e.target.closest('.edit-btn').getAttribute('data-id');
                    window.location.href = '{{ route("dashboard.bank.edit", ":id") }}'.replace(':id', id);
                } else if (e.target.closest('.delete-btn')) {
                    const id = parseInt(e.target.closest('.delete-btn').getAttribute('data-id'));
                    selectedBanks = [id];
                    showDeleteModal(false);
                }
            });

            document.getElementById('deleteModal').addEventListener('click', function(e) { 
                if (e.target === this) { 
                    closeDeleteModal(); 
                } 
            });

            // Export dropdown
            document.getElementById('exportButton').addEventListener('click', function(e) {
                e.stopPropagation();
                document.getElementById('exportDropdown').classList.toggle('show');
            });

            document.addEventListener('click', function(e) {
                if (!e.target.closest('.export-dropdown')) {
                    document.getElementById('exportDropdown').classList.remove('show');
                }
            });

            // Export Excel Modal
            document.getElementById('closeExportModalBtn').addEventListener('click', closeExportExcelModal);
            document.getElementById('cancelExportBtn').addEventListener('click', closeExportExcelModal);
            document.getElementById('confirmExportBtn').addEventListener('click', confirmExportExcel);
            document.getElementById('exportPeriod').addEventListener('change', handleExportPeriodChange);
            
            document.getElementById('exportExcelModal').addEventListener('click', function(e) { 
                if (e.target === this) { 
                    closeExportExcelModal(); 
                } 
            });

            // Export CSV Modal
            document.getElementById('closeCsvModalBtn').addEventListener('click', closeExportCsvModal);
            document.getElementById('cancelCsvBtn').addEventListener('click', closeExportCsvModal);
            document.getElementById('confirmCsvBtn').addEventListener('click', confirmExportCsv);
            document.getElementById('csvExportPeriod').addEventListener('change', handleCsvPeriodChange);
            
            document.getElementById('exportCsvModal').addEventListener('click', function(e) { 
                if (e.target === this) { 
                    closeExportCsvModal(); 
                } 
            });

            // Export PDF Modal
            document.getElementById('closePdfModalBtn').addEventListener('click', closeExportPdfModal);
            document.getElementById('cancelPdfBtn').addEventListener('click', closeExportPdfModal);
            document.getElementById('confirmPdfBtn').addEventListener('click', confirmExportPdf);
            document.getElementById('pdfExportPeriod').addEventListener('change', handlePdfPeriodChange);
            
            document.getElementById('exportPdfModal').addEventListener('click', function(e) { 
                if (e.target === this) { 
                    closeExportPdfModal(); 
                } 
            });
        });

        function filterBanks() {
            const searchTerm = document.getElementById('searchInput').value;
            
            // Show loading indicator during search
            showSearchLoading();
            
            // Always use server-side search for consistent browser behavior
            const url = new URL(window.location);
            if (searchTerm.trim() === '') {
                url.searchParams.delete('search');
            } else {
                url.searchParams.set('search', searchTerm);
            }
            url.searchParams.delete('page'); // Reset to first page when searching
            
            // Navigate to the new URL
            window.location.href = url.toString();
        }

        function renderPagination(paginationData) {
            const paginationContainer = document.getElementById('paginationContainer');
            const paginationInfo = document.getElementById('paginationInfo');
            const pagination = document.getElementById('pagination');
            
            const { current_page, total, per_page, first_item, last_item } = paginationData;
            
            // Hide pagination if no data
            if (total === 0) {
                paginationContainer.style.display = 'none';
                return;
            }
            
            // Show pagination
            paginationContainer.style.display = 'flex';
            
            // Update pagination info
            paginationInfo.textContent = `${first_item}-${last_item} of ${total}`;
            
            // Calculate total pages
            const totalPages = Math.ceil(total / per_page);
            
            // Generate pagination buttons
            let paginationHTML = '';
            paginationHTML += `<button class="pagination-btn" onclick="changePage(${current_page - 1})" ${current_page <= 1 ? 'disabled' : ''}>&lt;</button>`;
            paginationHTML += `<button class="pagination-btn" onclick="changePage(${current_page + 1})" ${current_page >= totalPages ? 'disabled' : ''}>&gt;</button>`;
            pagination.innerHTML = paginationHTML;
        }

        function updateSelectedBanks() {
            const checkboxes = document.querySelectorAll('.bank-checkbox:checked');
            selectedBanks = Array.from(checkboxes).map(cb => parseInt(cb.value));
            
            const deleteButton = document.getElementById('deleteButton');
            if (selectedBanks.length > 0) {
                deleteButton.style.display = 'flex';
            } else {
                deleteButton.style.display = 'none';
            }
        }

        function changePage(page) {
            // Always use server-side pagination
                const url = new URL(window.location);
                url.searchParams.set('page', page);
                window.location.href = url.toString();
        }

        function showDeleteModal(isBulk) {
            const modal = document.getElementById('deleteModal');
            const title = modal.querySelector('.modal-title');
            const subtitle = modal.querySelector('.modal-subtitle');
            
            if (isBulk) {
                title.textContent = 'Hapus Bank Sampah';
                subtitle.textContent = `Apakah Anda yakin ingin menghapus ${selectedBanks.length} bank sampah yang dipilih?`;
            } else {
                title.textContent = 'Hapus Bank Sampah';
                subtitle.textContent = 'Apakah Anda yakin ingin menghapus bank sampah yang dipilih?';
            }
            
            modal.classList.add('show');
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.remove('show');
        }

        function confirmDelete() {
            const ids = selectedBanks;
            
            // Show loading
            showLoading('Menghapus bank sampah...');
            
            fetch('{{ route("dashboard.bank.bulk-destroy") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                },
                body: JSON.stringify({ ids: ids })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Gagal menghapus bank sampah: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat menghapus bank sampah');
            })
            .finally(() => {
                hideLoading();
                closeDeleteModal();
            });
        }

        // Export Functions
        function exportData(format) {
            document.getElementById('exportDropdown').classList.remove('show');
            
            switch(format) {
                case 'excel':
                    showExportExcelModal();
                    break;
                case 'csv':
                    showExportCsvModal();
                    break;
                case 'pdf':
                    showExportPdfModal();
                    break;
                default:
                    alert('Format export tidak valid');
            }
        }

        function showExportExcelModal() {
            document.getElementById('exportExcelModal').classList.add('show');
        }

        function closeExportExcelModal() {
            document.getElementById('exportExcelModal').classList.remove('show');
        }

        function handleExportPeriodChange() {
            const period = document.getElementById('exportPeriod').value;
            const dateRangeGroup = document.getElementById('dateRangeGroup');
            dateRangeGroup.style.display = period === 'range' ? 'block' : 'none';
        }

        function confirmExportExcel() {
            const period = document.getElementById('exportPeriod').value;
            const startDate = document.getElementById('startDate').value;
            const endDate = document.getElementById('endDate').value;
            
            // Validate date range if selected
            if (period === 'range') {
                if (!startDate || !endDate) {
                    alert('Mohon pilih tanggal awal dan akhir');
                    return;
                }
                if (startDate > endDate) {
                    alert('Tanggal awal tidak boleh lebih besar dari tanggal akhir');
                    return;
                }
            }
            
            // Show loading on export button
            showLoading('Mengexport Excel...');
            
            // Create form data for file download
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("dashboard.bank.export.excel") }}';
            
            // Add CSRF token
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            form.appendChild(csrfToken);
            
            // Add export data
            const exportData = {
                period: period,
                start_date: startDate,
                end_date: endDate
            };
            
            Object.keys(exportData).forEach(key => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = key;
                input.value = exportData[key];
                form.appendChild(input);
            });
            
            // Submit form for file download
            document.body.appendChild(form);
            form.submit();
            document.body.removeChild(form);
            
            // Close modal and hide loading
            setTimeout(() => {
                hideLoading();
                closeExportExcelModal();
            }, 1000);
        }

        function showExportCsvModal() {
            document.getElementById('exportCsvModal').classList.add('show');
        }

        function closeExportCsvModal() {
            document.getElementById('exportCsvModal').classList.remove('show');
        }

        function handleCsvPeriodChange() {
            const period = document.getElementById('csvExportPeriod').value;
            const dateRangeGroup = document.getElementById('csvDateRangeGroup');
            dateRangeGroup.style.display = period === 'range' ? 'block' : 'none';
        }

        function confirmExportCsv() {
            const period = document.getElementById('csvExportPeriod').value;
            const startDate = document.getElementById('csvStartDate').value;
            const endDate = document.getElementById('csvEndDate').value;
            
            // Validate date range if selected
            if (period === 'range') {
                if (!startDate || !endDate) {
                    alert('Mohon pilih tanggal awal dan akhir');
                    return;
                }
                if (startDate > endDate) {
                    alert('Tanggal awal tidak boleh lebih besar dari tanggal akhir');
                    return;
                }
            }
            
            // Show loading on export button
            showLoading('Mengexport CSV...');
            
            // Create form data for file download
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("dashboard.bank.export.csv") }}';
            
            // Add CSRF token
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            form.appendChild(csrfToken);
            
            // Add export data
            const exportData = {
                period: period,
                start_date: startDate,
                end_date: endDate
            };
            
            Object.keys(exportData).forEach(key => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = key;
                input.value = exportData[key];
                form.appendChild(input);
            });
            
            // Submit form for file download
            document.body.appendChild(form);
            form.submit();
            document.body.removeChild(form);
            
            // Close modal and hide loading
            setTimeout(() => {
                hideLoading();
                closeExportCsvModal();
            }, 1000);
        }

        function showExportPdfModal() {
            document.getElementById('exportPdfModal').classList.add('show');
        }

        function closeExportPdfModal() {
            document.getElementById('exportPdfModal').classList.remove('show');
        }

        function handlePdfPeriodChange() {
            const period = document.getElementById('pdfExportPeriod').value;
            const dateRangeGroup = document.getElementById('pdfDateRangeGroup');
            dateRangeGroup.style.display = period === 'range' ? 'block' : 'none';
        }

        function confirmExportPdf() {
            const period = document.getElementById('pdfExportPeriod').value;
            const startDate = document.getElementById('pdfStartDate').value;
            const endDate = document.getElementById('pdfEndDate').value;
            
            // Validate date range if selected
            if (period === 'range') {
                if (!startDate || !endDate) {
                    alert('Mohon pilih tanggal awal dan akhir');
                    return;
                }
                if (startDate > endDate) {
                    alert('Tanggal awal tidak boleh lebih besar dari tanggal akhir');
                    return;
                }
            }
            
            // Show loading on export button
            showLoading('Mengexport PDF...');
            
            // Create form data for file download
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("dashboard.bank.export.pdf") }}';
            
            // Add CSRF token
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            form.appendChild(csrfToken);
            
            // Add export data
            const exportData = {
                period: period,
                start_date: startDate,
                end_date: endDate
            };
            
            Object.keys(exportData).forEach(key => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = key;
                input.value = exportData[key];
                form.appendChild(input);
            });
            
            // Submit form for file download
            document.body.appendChild(form);
            form.submit();
            document.body.removeChild(form);
            
            // Close modal and hide loading
            setTimeout(() => {
                hideLoading();
                closeExportPdfModal();
            }, 1000);
        }
    </script>
</body>
</html>
@endsection 