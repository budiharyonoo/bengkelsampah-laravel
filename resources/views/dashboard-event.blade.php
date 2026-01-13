@extends('dashboard')
@section('content')
    <!DOCTYPE html>
    <html lang="id">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Event - Admin Panel</title>
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
                padding: 6px 36px 6px 14px;
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
                border-color: #39746E;
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
            }

            .filter-button {
                padding: 0 1rem;
                background: white;
                border: 1px solid #E5E6E6;
                border-radius: 8px;
                font-family: 'Urbanist', sans-serif;
                font-size: 15px;
                font-weight: 600;
                color: #39746E;
                cursor: pointer;
                display: flex;
                align-items: center;
                gap: 6px;
                height: 38px;
            }

            .filter-dropdown-content {
                display: none;
                position: absolute;
                top: 100%;
                left: 0;
                background: white;
                border: 1px solid #d1d5db;
                border-radius: 8px;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
                z-index: 10;
                min-width: 200px;
                padding: 0.5rem;
            }

            .filter-dropdown-content.show {
                display: block;
            }

            .filter-option {
                padding: 8px 12px;
                cursor: pointer;
                transition: background-color 0.2s;
            }

            .filter-option:hover {
                background-color: #f3f4f6;
            }

            .filter-option label {
                cursor: pointer;
                font-family: 'Urbanist', sans-serif;
                font-size: 15px;
                color: #1e293b;
            }

            .action-buttons {
                display: flex;
                gap: 8px;
                align-items: center;
            }

            .action-btn {
                width: 32px;
                height: 32px;
                border: 1px solid #E5E6E6;
                border-radius: 6px;
                background: #fff;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                transition: all 0.2s;
                text-decoration: none;
            }

            .action-btn:hover {
                transform: translateY(-1px);
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            }

            .action-btn img {
                width: 16px;
                height: 16px;
            }

            .action-btn.detail-btn {
                background-color: #3b82f6;
                border-color: #3b82f6;
            }

            .action-btn.detail-btn:hover {
                background-color: #2563eb;
                border-color: #2563eb;
            }

            .action-btn.edit-btn {
                background-color: #f59e0b;
                border-color: #f59e0b;
            }

            .action-btn.edit-btn:hover {
                background-color: #d97706;
                border-color: #d97706;
            }

            .action-btn.delete-btn {
                background-color: #ef4444;
                border-color: #ef4444;
            }

            .action-btn.delete-btn:hover {
                background-color: #dc2626;
                border-color: #dc2626;
            }

            .action-btn.complete-btn {
                background-color: #10b981;
                border-color: #10b981;
            }

            .action-btn.complete-btn:hover {
                background-color: #059669;
                border-color: #059669;
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

            .event-checkbox {
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

            .event-checkbox:checked {
                background-color: #39746E;
                border-color: #39746E;
            }

            .event-checkbox:checked::after {
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

            .image-cell {
                width: 100px;
            }

            .event-image {
                width: 100px;
                height: 56px;
                object-fit: cover;
                border-radius: 6px;
            }

            .title-cell {
                min-width: 200px;
                max-width: 300px;
            }

            .event-title {
                font-family: 'Urbanist', sans-serif;
                font-size: 12px;
                font-weight: 400;
                color: #6B7271;
                margin-bottom: 0.25rem;
            }

            .event-url {
                font-family: 'Urbanist', sans-serif;
                font-size: 12px;
                font-weight: 400;
                color: #6B7271;
                line-height: 1.5;
            }

            .event-location {
                font-family: 'Urbanist', sans-serif;
                font-size: 12px;
                font-weight: 400;
                color: #6B7271;
                line-height: 1.5;
            }

            .event-subtitle {
                font-size: 12px;
                color: #64748b;
            }

            .time-cell {
                min-width: 150px;
                max-width: 200px;
            }

            .event-time {
                font-family: 'Urbanist', sans-serif;
                font-size: 12px;
                font-weight: 400;
                color: #6B7271;
                line-height: 1.5;
            }

            .location-cell {
                min-width: 150px;
                max-width: 200px;
            }

            .event-location {
                font-family: 'Urbanist', sans-serif;
                font-size: 12px;
                font-weight: 400;
                color: #6B7271;
                line-height: 1.5;
            }

            .status-cell {
                width: 120px;
            }

            .status-tag {
                display: inline-block;
                padding: 0.25rem 0.75rem;
                border-radius: 20px;
                font-family: 'Urbanist', sans-serif;
                font-size: 12px;
                font-weight: 400;
            }

            .status-active {
                background: #DFF0EE;
                color: #28a745;
            }

            .status-completed {
                background: #f8f9fa;
                color: #6c757d;
            }

            .status-cancelled {
                background: #f8d7da;
                color: #dc3545;
            }

            .status-tag.status-cancelled {
                background-color: #fef2f2;
                color: #dc2626;
            }

            .result-badge {
                display: inline-block;
                padding: 4px 8px;
                border-radius: 12px;
                font-size: 11px;
                font-weight: 500;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }

            .result-badge.result-submitted {
                background-color: #f0fdf4;
                color: #16a34a;
            }

            .result-badge.result-pending {
                background-color: #fef3c7;
                color: #d97706;
            }

            .participants-cell {
                width: 100px;
                text-align: center;
            }

            .participants-count {
                font-family: 'Urbanist', sans-serif;
                font-size: 12px;
                font-weight: 600;
                color: #39746E;
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

            .action-button {
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

            .action-button:hover {
                background-color: #f3f4f6;
            }

            .action-button img {
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

            /* Export Form Styles */
            .export-form {
                margin: 20px 0;
            }

            .export-form .form-group {
                margin-bottom: 16px;
            }

            .export-form .form-label {
                display: block;
                font-family: 'Urbanist', sans-serif;
                font-size: 14px;
                font-weight: 500;
                color: #374151;
                margin-bottom: 8px;
            }

            .export-form .form-select,
            .export-form .form-input {
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

            .export-form .form-select:focus,
            .export-form .form-input:focus {
                outline: none;
                border-color: #39746E;
            }

            .date-range-row {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 12px;
            }

            .date-input-group {
                display: flex;
                flex-direction: column;
            }

            @media (max-width: 768px) {
                .date-range-row {
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
                from {
                    transform: rotate(0deg);
                }

                to {
                    transform: rotate(360deg);
                }
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
        </style>
    </head>

    <body>
        <!-- ================= HEADER & FILTER ================= -->
        @include('partials.dashboard-header-bar', ['title' => 'Event'])

        <!-- Loading Overlay -->
        <div class="loading-overlay" id="pageLoadingOverlay" style="display: none;">
            <div class="loading-content">
                <div class="loading-spinner-large"></div>
                <p>Memuat data...</p>
            </div>
        </div>

        <div class="container">
            <div class="controls">
                <div class="search-filter">
                    <div class="search-box">
                        <input type="text" class="search-input" placeholder="Cari data disini" id="searchInput">
                        <img src="{{ asset('icon/ic_search.svg') }}" alt="Search" class="search-icon">
                    </div>
                    <div class="filter-dropdown">
                        <button class="filter-button" id="filterButton">
                            <span>Semua Status</span>
                            <img src="{{ asset('icon/ic_trailing.svg') }}" alt="Filter" width="16" height="16">
                        </button>
                        <div class="filter-dropdown-content" id="filterDropdown">
                            <div class="filter-option">
                                <label>Semua Status</label>
                            </div>
                            <div class="filter-option">
                                <label>Active</label>
                            </div>
                            <div class="filter-option">
                                <label>Completed</label>
                            </div>
                            <div class="filter-option">
                                <label>Cancelled</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="action-buttons">
                    <button class="delete-button" id="deleteButton">
                        <span>Hapus Event</span>
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
                        <span>Tambah Event</span>
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
                            <th>Cover</th>
                            <th>Judul</th>
                            <th>Lokasi</th>
                            <th>URL</th>
                            <th>Tanggal</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="eventTableBody">
                        <!-- Data akan diisi oleh JavaScript -->
                    </tbody>
                </table>
                <div class="no-results" id="noResults" style="display: none;">
                    Tidak ada event yang ditemukan
                </div>
            </div>

            <div class="pagination-container" id="paginationContainer">
                <div class="pagination-info" id="paginationInfo"></div>
                <div class="pagination" id="pagination"></div>
            </div>
        </div>

        <div class="modal" id="deleteModal">
            <div class="modal-content">
                <button class="modal-close" id="closeModalBtn">
                    <img src="{{ asset('icon/ic_close.svg') }}" alt="Close">
                </button>
                <img src="{{ asset('icon/ic_dialog_delete.svg') }}" alt="Delete" class="modal-icon">
                <h2 class="modal-title">Hapus Event</h2>
                <p class="modal-subtitle">Apakah Anda yakin ingin menghapus event yang dipilih?</p>
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
                <h2 class="modal-title">Export Excel Event</h2>
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

                    <div class="form-group">
                        <label class="form-label">Filter Status (Opsional)</label>
                        <select class="form-select" id="exportStatus">
                            <option value="">Semua Status</option>
                            <option value="active">Active</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Berdasarkan Waktu</label>
                        <select class="form-select" id="exportTimeType">
                            <option value="created">Waktu Dibuat</option>
                            <option value="start">Waktu Mulai</option>
                            <option value="end">Waktu Berakhir</option>
                        </select>
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
                <h2 class="modal-title">Export CSV Event</h2>
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

                    <div class="form-group">
                        <label class="form-label">Filter Status (Opsional)</label>
                        <select class="form-select" id="csvExportStatus">
                            <option value="">Semua Status</option>
                            <option value="active">Active</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Berdasarkan Waktu</label>
                        <select class="form-select" id="csvExportTimeType">
                            <option value="created">Waktu Dibuat</option>
                            <option value="start">Waktu Mulai</option>
                            <option value="end">Waktu Berakhir</option>
                        </select>
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
                <h2 class="modal-title">Export PDF Event</h2>
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

                    <div class="form-group">
                        <label class="form-label">Filter Status (Opsional)</label>
                        <select class="form-select" id="pdfExportStatus">
                            <option value="">Semua Status</option>
                            <option value="active">Active</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Berdasarkan Waktu</label>
                        <select class="form-select" id="pdfExportTimeType">
                            <option value="created">Waktu Dibuat</option>
                            <option value="start">Waktu Mulai</option>
                            <option value="end">Waktu Berakhir</option>
                        </select>
                    </div>
                </div>

                <div class="modal-buttons">
                    <button class="modal-button cancel-button" id="cancelPdfBtn">Batal</button>
                    <button class="modal-button confirm-button" id="confirmPdfBtn">Export PDF</button>
                </div>
            </div>
        </div>

        <script>
            // Data event dari backend
            let events = [
                @foreach ($events as $event)
                    {
                        id: {{ $event->id }},
                        image: @json($event->cover ?? null),
                        title: @json($event->title),
                        url: @json($event->url),
                        start_datetime: @json($event->start_datetime->format('d M Y H:i')),
                        end_datetime: @json($event->end_datetime->format('d M Y H:i')),
                        location: @json($event->location),
                        status: @json($event->status ?? 'active'),
                        participants_count: {{ $event->participants_count ?? 0 }}
                    },
                @endforeach
            ];
            let filteredEvents = [...events];
            let selectedStatus = null;
            let selectedEvents = [];
            let currentPage = 1;
            const itemsPerPage = 10;
            let totalPages = 1;

            // Loading utility functions - moved to global scope
            function showLoading(message = 'Memuat data...') {
                const overlay = document.getElementById('pageLoadingOverlay');
                const loadingText = overlay.querySelector('p');
                loadingText.textContent = message;
                overlay.style.display = 'flex';
            }

            function hideLoading() {
                const overlay = document.getElementById('pageLoadingOverlay');
                overlay.style.display = 'none';
            }

            document.addEventListener('DOMContentLoaded', function() {
                // Show loading on initial page load
                showLoading('Memuat data...');

                // Hide loading after a short delay to simulate loading
                setTimeout(() => {
                    hideLoading();
                }, 500);

                // Initial data loading
                const initialPaginationData = {
                    current_page: {{ $events->currentPage() }},
                    total: {{ $events->total() }},
                    per_page: {{ $events->perPage() }},
                    first_item: {{ $events->firstItem() ?? 0 }},
                    last_item: {{ $events->lastItem() ?? 0 }}
                };

                renderEvents();
                renderPagination(initialPaginationData);
                setupEventListeners();
            });

            function setupEventListeners() {
                // Search functionality - only trigger on Enter key
                document.getElementById('searchInput').addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        filterEvents();
                    }
                });

                document.getElementById('filterButton').addEventListener('click', function(e) {
                    e.stopPropagation();
                    document.getElementById('filterDropdown').classList.toggle('show');
                });

                document.querySelectorAll('.filter-option').forEach(option => {
                    option.addEventListener('click', function() {
                        const status = this.querySelector('label').textContent;
                        selectedStatus = status === 'Semua Status' ? null : status.trim().toLowerCase();
                        document.getElementById('filterButton').querySelector('span').textContent = status;
                        document.getElementById('filterDropdown').classList.remove('show');
                        filterEvents();
                    });
                });

                document.getElementById('selectAll').addEventListener('change', function() {
                    const checkboxes = document.querySelectorAll('.event-checkbox');
                    checkboxes.forEach(cb => {
                        cb.checked = this.checked;
                    });
                    updateSelectedEvents();
                });

                document.getElementById('addButton').addEventListener('click', function() {
                    window.location.href = '{{ route('dashboard.event.create') }}';
                });

                document.getElementById('deleteButton').addEventListener('click', function() {
                    if (selectedEvents.length > 0) {
                        showDeleteModal(true);
                    }
                });

                document.addEventListener('click', function(e) {
                    if (!e.target.closest('.filter-dropdown')) {
                        document.getElementById('filterDropdown').classList.remove('show');
                    }
                });

                // Modal close
                document.getElementById('closeModalBtn').addEventListener('click', closeDeleteModal);
                document.getElementById('cancelDeleteBtn').addEventListener('click', closeDeleteModal);
                document.getElementById('confirmDeleteBtn').addEventListener('click', confirmDelete);

                // Action buttons in table
                document.getElementById('eventTableBody').addEventListener('click', function(e) {
                    if (e.target.closest('.detail-button')) {
                        const id = e.target.closest('.detail-button').getAttribute('data-id');
                        window.location.href = `/dashboard/event/${id}`;
                    } else if (e.target.closest('.edit-button')) {
                        const id = e.target.closest('.edit-button').getAttribute('data-id');
                        window.location.href = `/dashboard/event/${id}/edit`;
                    } else if (e.target.closest('.delete-button')) {
                        const id = parseInt(e.target.closest('.delete-button').getAttribute('data-id'));
                        selectedEvents = [id];
                        showDeleteModal(false);
                    }
                });

                document.getElementById('deleteModal').addEventListener('click', function(e) {
                    if (e.target === this) {
                        closeDeleteModal();
                    }
                });

                // Export dropdown functionality
                const exportButton = document.getElementById('exportButton');
                const exportDropdown = document.getElementById('exportDropdown');

                exportButton.addEventListener('click', function(e) {
                    e.stopPropagation();
                    exportDropdown.classList.toggle('show');
                });

                // Close dropdown when clicking outside
                document.addEventListener('click', function(e) {
                    if (!e.target.closest('.export-dropdown')) {
                        exportDropdown.classList.remove('show');
                    }
                });

                // Export functions
                window.exportData = function(type) {
                    exportDropdown.classList.remove('show');

                    if (type === 'excel') {
                        showExportExcelModal();
                    } else if (type === 'csv') {
                        showExportCsvModal();
                    } else if (type === 'pdf') {
                        showExportPdfModal();
                    } else {
                        alert('Format export ' + type + ' belum tersedia');
                    }
                };

                // Export modal event listeners
                document.getElementById('closeExportModalBtn').addEventListener('click', closeExportExcelModal);
                document.getElementById('cancelExportBtn').addEventListener('click', closeExportExcelModal);
                document.getElementById('confirmExportBtn').addEventListener('click', confirmExportExcel);

                document.getElementById('closeCsvModalBtn').addEventListener('click', closeExportCsvModal);
                document.getElementById('cancelCsvBtn').addEventListener('click', closeExportCsvModal);
                document.getElementById('confirmCsvBtn').addEventListener('click', confirmExportCsv);

                document.getElementById('closePdfModalBtn').addEventListener('click', closeExportPdfModal);
                document.getElementById('cancelPdfBtn').addEventListener('click', closeExportPdfModal);
                document.getElementById('confirmPdfBtn').addEventListener('click', confirmExportPdf);

                // Date range visibility for Excel modal
                document.getElementById('exportPeriod').addEventListener('change', handleExportPeriodChange);

                // Date range visibility for CSV modal
                document.getElementById('csvExportPeriod').addEventListener('change', handleCsvPeriodChange);

                // Date range visibility for PDF modal
                document.getElementById('pdfExportPeriod').addEventListener('change', handlePdfPeriodChange);

                // Close modals when clicking outside
                document.getElementById('exportExcelModal').addEventListener('click', function(e) {
                    if (e.target === this) {
                        closeExportExcelModal();
                    }
                });

                document.getElementById('exportCsvModal').addEventListener('click', function(e) {
                    if (e.target === this) {
                        closeExportCsvModal();
                    }
                });

                document.getElementById('exportPdfModal').addEventListener('click', function(e) {
                    if (e.target === this) {
                        closeExportPdfModal();
                    }
                });
            }

            function filterEvents() {
                const searchTerm = document.getElementById('searchInput').value;
                currentPage = 1;

                // Show loading for search
                const overlay = document.getElementById('pageLoadingOverlay');
                const loadingText = overlay.querySelector('p');
                loadingText.textContent = 'Mencari data...';
                overlay.style.display = 'flex';

                loadEvents(searchTerm, selectedStatus, currentPage);
            }

            function changePage(page) {
                if (page < 1) return;

                const searchTerm = document.getElementById('searchInput').value;

                // Show loading for pagination
                const overlay = document.getElementById('pageLoadingOverlay');
                const loadingText = overlay.querySelector('p');
                loadingText.textContent = 'Memuat halaman...';
                overlay.style.display = 'flex';

                loadEvents(searchTerm, selectedStatus, page);
                document.querySelector('.table-container').scrollIntoView({
                    behavior: 'smooth'
                });
            }

            function loadEvents(search, status, page) {
                const url = new URL(window.location.href);
                url.searchParams.set('search', search);
                if (status && status !== 'semua status') {
                    url.searchParams.set('status', status);
                }
                url.searchParams.set('page', page);

                fetch(url.toString(), {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        console.log('Server response:', data);

                        if (data.events && data.events.data) {
                            events = data.events.data.map(event => ({
                                id: event.id,
                                image: event.cover,
                                title: event.title,
                                start_datetime: new Date(event.start_datetime).toLocaleString('id-ID', {
                                    day: '2-digit',
                                    month: 'short',
                                    year: 'numeric',
                                    hour: '2-digit',
                                    minute: '2-digit'
                                }),
                                end_datetime: new Date(event.end_datetime).toLocaleString('id-ID', {
                                    day: '2-digit',
                                    month: 'short',
                                    year: 'numeric',
                                    hour: '2-digit',
                                    minute: '2-digit'
                                }),
                                location: event.location,
                                url: event.url || null,
                                status: event.status || 'active',
                                participants_count: event.participants_count || 0
                            }));

                            filteredEvents = [...events];
                            currentPage = data.events.current_page;

                            renderEvents();

                            const paginationData = {
                                current_page: data.events.current_page,
                                total: data.events.total,
                                per_page: data.events.per_page,
                                first_item: data.events.from || 0,
                                last_item: data.events.to || 0
                            };
                            renderPagination(paginationData);

                            selectedEvents = [];
                            updateSelectedEvents();
                        } else {
                            events = [];
                            filteredEvents = [];
                            renderEvents();
                            renderPagination({
                                current_page: 1,
                                total: 0,
                                per_page: itemsPerPage,
                                first_item: 0,
                                last_item: 0
                            });
                            selectedEvents = [];
                            updateSelectedEvents();
                        }

                        // Hide loading after data is loaded
                        document.getElementById('pageLoadingOverlay').style.display = 'none';
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        events = [];
                        filteredEvents = [];
                        renderEvents();
                        renderPagination({
                            current_page: 1,
                            total: 0,
                            per_page: itemsPerPage,
                            first_item: 0,
                            last_item: 0
                        });
                        selectedEvents = [];
                        updateSelectedEvents();

                        // Hide loading after error
                        document.getElementById('pageLoadingOverlay').style.display = 'none';
                    });
            }

            function renderEvents() {
                const tbody = document.getElementById('eventTableBody');
                const noResults = document.getElementById('noResults');
                const paginationContainer = document.getElementById('paginationContainer');

                if (filteredEvents.length === 0) {
                    tbody.innerHTML = '';
                    noResults.style.display = 'block';
                    paginationContainer.style.display = 'none';
                    return;
                }

                noResults.style.display = 'none';
                paginationContainer.style.display = 'flex';

                console.log(filteredEvents);

                tbody.innerHTML = filteredEvents.map(event => `
                <tr>
                    <td class="checkbox-cell">
                        <input type="checkbox" class="event-checkbox" value="${event.id}" onchange="updateSelectedEvents()">
                    </td>
                    <td class="image-cell">
                        ${event.image ? `<img src="${event.image}" alt="${event.title}" class="event-image" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">` :
                          '<div style="width:100px;height:56px;background:#f8f9fa;border-radius:6px;display:flex;align-items:center;justify-content:center;color:#6c757d;font-size:12px;">No Image</div>'}
                        <div style="width:100px;height:56px;background:#f8f9fa;border-radius:6px;display:flex;align-items:center;justify-content:center;color:#6c757d;font-size:12px;display:none;">No Image</div>
                    </td>
                    <td class="title-cell">
                        <div class="event-title">${event.title}</div>
                    </td>
                    <td class="location-cell">
                        <div class="event-location">${event.location}</div>
                    </td>
                    <td class="title-cell">
                        <div class="event-title">${event.url ?? ''}</div>
                    </td>
                    <td class="time-cell">
                        <div class="event-time">${event.start_datetime}<br><span style="color: #64748b;">s/d ${event.end_datetime}</span></div>
                    </td>
                    <td class="status-cell">
                        <span class="status-tag status-${event.status ? event.status.toLowerCase() : 'active'}">${event.status ? event.status.charAt(0).toUpperCase() + event.status.slice(1) : 'Active'}</span>
                    </td>
                    <td class="action-cell">
                        <div class="action-buttons-cell">
                            <button class="action-button detail-button" data-id="${event.id}">
                                <img src="/icon/ic_detail.svg" alt="Detail">
                            </button>
                            <button class="action-button edit-button" data-id="${event.id}">
                                <img src="/icon/ic_edit.svg" alt="Edit">
                            </button>
                            <button class="action-button delete-button" data-id="${event.id}">
                                <img src="/icon/ic_delete.svg" alt="Delete">
                            </button>
                        </div>
                    </td>
                </tr>
            `).join('');
            }

            function renderPagination(paginationData) {
                const paginationContainer = document.getElementById('paginationContainer');
                const paginationInfo = document.getElementById('paginationInfo');
                const pagination = document.getElementById('pagination');

                const {
                    current_page,
                    total,
                    per_page,
                    first_item,
                    last_item
                } = paginationData;

                if (total === 0) {
                    paginationInfo.textContent = '0-0 of 0';
                    pagination.innerHTML = '';
                    return;
                }

                paginationInfo.textContent = `${first_item}-${last_item} of ${total}`;

                const totalPages = Math.ceil(total / per_page);

                let paginationHTML = '';
                paginationHTML +=
                    `<button class="pagination-btn" onclick="changePage(${current_page - 1})" ${current_page <= 1 ? 'disabled' : ''}>&lt;</button>`;
                paginationHTML +=
                    `<button class="pagination-btn" onclick="changePage(${current_page + 1})" ${current_page >= totalPages ? 'disabled' : ''}>&gt;</button>`;
                pagination.innerHTML = paginationHTML;
            }

            function updateSelectedEvents() {
                const checkboxes = document.querySelectorAll('.event-checkbox:checked');
                selectedEvents = Array.from(checkboxes).map(cb => parseInt(cb.value));
                const selectAllCheckbox = document.getElementById('selectAll');
                const allCheckboxes = document.querySelectorAll('.event-checkbox');
                selectAllCheckbox.checked = allCheckboxes.length > 0 && selectedEvents.length === allCheckboxes.length;
                const deleteBtn = document.getElementById('deleteButton');
                if (selectedEvents.length > 0) {
                    deleteBtn.style.display = 'flex';
                } else {
                    deleteBtn.style.display = 'none';
                }
            }

            function showDeleteModal(isMultiple) {
                const modal = document.getElementById('deleteModal');
                const title = document.querySelector('#deleteModal .modal-title');
                const subtitle = document.querySelector('#deleteModal .modal-subtitle');

                if (isMultiple) {
                    title.textContent = 'Hapus Event';
                    subtitle.textContent = `Apakah Anda yakin ingin menghapus ${selectedEvents.length} event yang dipilih?`;
                } else {
                    title.textContent = 'Hapus Event';
                    subtitle.textContent = 'Apakah Anda yakin ingin menghapus event ini?';
                }

                modal.classList.add('show');
            }

            function closeDeleteModal() {
                document.getElementById('deleteModal').classList.remove('show');
            }

            function confirmDelete() {
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                let url = '';
                let body = {};
                let isMultiple = selectedEvents.length > 1;

                // Show loading for delete operation
                const overlay = document.getElementById('pageLoadingOverlay');
                const loadingText = overlay.querySelector('p');
                loadingText.textContent = 'Menghapus event...';
                overlay.style.display = 'flex';

                if (isMultiple) {
                    url = `/dashboard/event/0`;
                    body = {
                        ids: selectedEvents
                    };
                } else {
                    url = `/dashboard/event/${selectedEvents[0]}`;
                    body = {};
                }

                fetch(url, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                        },
                        body: isMultiple ? JSON.stringify(body) : null
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            closeDeleteModal();
                            loadEvents(
                                document.getElementById('searchInput').value,
                                selectedStatus,
                                currentPage
                            );
                        } else {
                            alert(data.message || 'Gagal menghapus event');
                            // Hide loading on error
                            document.getElementById('pageLoadingOverlay').style.display = 'none';
                        }
                    })
                    .catch(() => {
                        alert('Gagal menghapus event');
                        // Hide loading on error
                        document.getElementById('pageLoadingOverlay').style.display = 'none';
                    });
            }

            // Handle complete event
            function completeEvent(id) {
                if (confirm('Apakah Anda yakin ingin mengubah status event ini menjadi completed?')) {
                    fetch(`/dashboard/event/${id}/complete`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                showAlert('success', data.message);
                                loadEvents();
                            } else {
                                showAlert('error', data.message);
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            showAlert('error', 'Terjadi kesalahan saat mengubah status event');
                        });
                }
            }

            // Export Modal Functions - Global scope
            function showExportExcelModal() {
                const modal = document.getElementById('exportExcelModal');
                modal.classList.add('show');

                // Set default dates
                const today = new Date();
                const yesterday = new Date(today);
                yesterday.setDate(yesterday.getDate() - 1);

                document.getElementById('startDate').value = yesterday.toISOString().split('T')[0];
                document.getElementById('endDate').value = today.toISOString().split('T')[0];
            }

            function closeExportExcelModal() {
                document.getElementById('exportExcelModal').classList.remove('show');
            }

            function handleExportPeriodChange() {
                const period = document.getElementById('exportPeriod').value;
                const dateRangeGroup = document.getElementById('dateRangeGroup');

                if (period === 'range') {
                    dateRangeGroup.style.display = 'block';
                } else {
                    dateRangeGroup.style.display = 'none';
                }
            }

            function confirmExportExcel() {
                const period = document.getElementById('exportPeriod').value;
                const status = document.getElementById('exportStatus').value;
                const timeType = document.getElementById('exportTimeType').value;
                const startDate = document.getElementById('startDate').value;
                const endDate = document.getElementById('endDate').value;

                // Validate date range if selected
                if (period === 'range') {
                    if (!startDate || !endDate) {
                        console.error('Mohon pilih tanggal awal dan akhir');
                        return;
                    }
                    if (startDate > endDate) {
                        console.error('Tanggal awal tidak boleh lebih besar dari tanggal akhir');
                        return;
                    }
                }

                // Show loading on export button
                showLoading('Mengexport Excel...');

                // Prepare export data
                const exportData = {
                    period: period,
                    status: status,
                    time_type: timeType,
                    start_date: startDate,
                    end_date: endDate
                };

                // Call export API
                fetch('/dashboard/event/export/excel', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify(exportData)
                    })
                    .then(response => {
                        if (response.ok) {
                            return response.blob();
                        }
                        throw new Error('Export failed');
                    })
                    .then(blob => {
                        // Create download link
                        const url = window.URL.createObjectURL(blob);
                        const a = document.createElement('a');
                        a.href = url;
                        a.download = `event_export_${new Date().toISOString().split('T')[0]}.xlsx`;
                        document.body.appendChild(a);
                        a.click();
                        window.URL.revokeObjectURL(url);
                        document.body.removeChild(a);

                        // Close modal
                        closeExportExcelModal();
                    })
                    .catch(error => {
                        console.error('Export error:', error);
                    })
                    .finally(() => {
                        // Hide loading
                        hideLoading();
                    });
            }

            function showExportCsvModal() {
                const modal = document.getElementById('exportCsvModal');
                modal.classList.add('show');

                // Set default dates
                const today = new Date();
                const yesterday = new Date(today);
                yesterday.setDate(yesterday.getDate() - 1);

                document.getElementById('csvStartDate').value = yesterday.toISOString().split('T')[0];
                document.getElementById('csvEndDate').value = today.toISOString().split('T')[0];
            }

            function closeExportCsvModal() {
                document.getElementById('exportCsvModal').classList.remove('show');
            }

            function handleCsvPeriodChange() {
                const period = document.getElementById('csvExportPeriod').value;
                const dateRangeGroup = document.getElementById('csvDateRangeGroup');

                if (period === 'range') {
                    dateRangeGroup.style.display = 'block';
                } else {
                    dateRangeGroup.style.display = 'none';
                }
            }

            function confirmExportCsv() {
                const period = document.getElementById('csvExportPeriod').value;
                const status = document.getElementById('csvExportStatus').value;
                const timeType = document.getElementById('csvExportTimeType').value;
                const startDate = document.getElementById('csvStartDate').value;
                const endDate = document.getElementById('csvEndDate').value;

                // Validate date range if selected
                if (period === 'range') {
                    if (!startDate || !endDate) {
                        console.error('Mohon pilih tanggal awal dan akhir');
                        return;
                    }
                    if (startDate > endDate) {
                        console.error('Tanggal awal tidak boleh lebih besar dari tanggal akhir');
                        return;
                    }
                }

                // Show loading on export button
                showLoading('Mengexport CSV...');

                // Prepare export data
                const exportData = {
                    period: period,
                    status: status,
                    time_type: timeType,
                    start_date: startDate,
                    end_date: endDate
                };

                // Call export API
                fetch('/dashboard/event/export/csv', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify(exportData)
                    })
                    .then(response => {
                        if (response.ok) {
                            return response.blob();
                        }
                        throw new Error('Export failed');
                    })
                    .then(blob => {
                        // Create download link
                        const url = window.URL.createObjectURL(blob);
                        const a = document.createElement('a');
                        a.href = url;
                        a.download = `event_export_${new Date().toISOString().split('T')[0]}.csv`;
                        document.body.appendChild(a);
                        a.click();
                        window.URL.revokeObjectURL(url);
                        document.body.removeChild(a);

                        // Close modal
                        closeExportCsvModal();
                    })
                    .catch(error => {
                        console.error('Export error:', error);
                    })
                    .finally(() => {
                        // Hide loading
                        hideLoading();
                    });
            }

            function showExportPdfModal() {
                const modal = document.getElementById('exportPdfModal');
                modal.classList.add('show');

                // Set default dates
                const today = new Date();
                const yesterday = new Date(today);
                yesterday.setDate(yesterday.getDate() - 1);

                document.getElementById('pdfStartDate').value = yesterday.toISOString().split('T')[0];
                document.getElementById('pdfEndDate').value = today.toISOString().split('T')[0];
            }

            function closeExportPdfModal() {
                document.getElementById('exportPdfModal').classList.remove('show');
            }

            function handlePdfPeriodChange() {
                const period = document.getElementById('pdfExportPeriod').value;
                const dateRangeGroup = document.getElementById('pdfDateRangeGroup');

                if (period === 'range') {
                    dateRangeGroup.style.display = 'block';
                } else {
                    dateRangeGroup.style.display = 'none';
                }
            }

            function confirmExportPdf() {
                const period = document.getElementById('pdfExportPeriod').value;
                const status = document.getElementById('pdfExportStatus').value;
                const timeType = document.getElementById('pdfExportTimeType').value;
                const startDate = document.getElementById('pdfStartDate').value;
                const endDate = document.getElementById('pdfEndDate').value;

                // Validate date range if selected
                if (period === 'range') {
                    if (!startDate || !endDate) {
                        console.error('Mohon pilih tanggal awal dan akhir');
                        return;
                    }
                    if (startDate > endDate) {
                        console.error('Tanggal awal tidak boleh lebih besar dari tanggal akhir');
                        return;
                    }
                }

                // Show loading on export button
                showLoading('Mengexport PDF...');

                // Prepare export data
                const exportData = {
                    period: period,
                    status: status,
                    time_type: timeType,
                    start_date: startDate,
                    end_date: endDate
                };

                // Call export API
                fetch('/dashboard/event/export/pdf', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify(exportData)
                    })
                    .then(response => {
                        if (response.ok) {
                            return response.blob();
                        }
                        throw new Error('Export failed');
                    })
                    .then(blob => {
                        // Create download link
                        const url = window.URL.createObjectURL(blob);
                        const a = document.createElement('a');
                        a.href = url;
                        a.download = `event_export_${new Date().toISOString().split('T')[0]}.pdf`;
                        document.body.appendChild(a);
                        a.click();
                        window.URL.revokeObjectURL(url);
                        document.body.removeChild(a);

                        // Close modal
                        closeExportPdfModal();
                    })
                    .catch(error => {
                        console.error('Export error:', error);
                    })
                    .finally(() => {
                        // Hide loading
                        hideLoading();
                    });
            }
        </script>
    </body>

    </html>
@endsection
