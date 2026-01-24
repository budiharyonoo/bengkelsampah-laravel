@extends('dashboard')
@section('content')
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Redeem Requests - Admin Panel</title>
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
            flex-wrap: wrap;
        }

        .search-filter {
            display: flex;
            gap: 1rem;
            flex: 1;
            flex-wrap: wrap;
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

        .filter-group {
            display: flex;
            gap: 0.5rem;
            align-items: center;
        }

        .filter-label {
            font-family: 'Urbanist', sans-serif;
            font-size: 14px;
            font-weight: 500;
            color: #374151;
            white-space: nowrap;
        }

        .date-input,
        .status-select {
            padding: 6px 12px;
            border: 1px solid #EFF0F0;
            background: #EFF0F0;
            border-radius: 8px;
            font-family: 'Urbanist', sans-serif;
            font-size: 14px;
            color: #6B7271;
            outline: none;
            transition: background-color 0.2s;
        }

        .date-input:focus,
        .status-select:focus {
            background: #fff;
        }

        .status-select {
            min-width: 150px;
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
        }

        .action-btn:hover {
            background-color: #f3f4f6;
        }

        .action-btn img {
            width: 16px;
            height: 16px;
        }

        .action-btn.edit-btn {
            color: #f59e0b;
        }

        .action-btn.edit-btn:hover {
            background-color: #fffbeb;
        }

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

        .table-container {
            margin-top: 1rem;
            overflow-x: auto;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            font-family: 'Urbanist', sans-serif;
            font-size: 14px;
        }

        .table th {
            background: #F8F9FA;
            padding: 12px 16px;
            text-align: left;
            font-weight: 600;
            color: #374151;
            border-bottom: 1px solid #E5E7EB;
            font-size: 13px;
        }

        .table td {
            padding: 12px 16px;
            border-bottom: 1px solid #F3F4F6;
            vertical-align: middle;
        }

        .table tbody tr:hover {
            background: #F9FAFB;
        }

        .user-info-cell {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .user-name-text {
            font-weight: 600;
            color: #1e293b;
        }

        .user-identifier-text {
            font-size: 12px;
            color: #6B7280;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            text-align: center;
        }

        .status-badge.waiting {
            background: #FEF3C7;
            color: #D97706;
        }

        .status-badge.approved {
            background: #D1FAE5;
            color: #10B981;
        }

        .status-badge.rejected {
            background: #FEE2E2;
            color: #EF4444;
        }

        .status-badge.cancelled {
            background: #F3F4F6;
            color: #6B7280;
        }

        .action-buttons-cell {
            display: flex;
            gap: 4px;
            justify-content: center;
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

        .no-results {
            text-align: center;
            padding: 2rem;
            color: #6B7280;
            font-family: 'Urbanist', sans-serif;
            font-size: 16px;
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
            padding: 2rem;
            max-width: 400px;
            width: 90%;
            position: relative;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }

        .modal-close {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: none;
            border: none;
            cursor: pointer;
            padding: 4px;
            border-radius: 4px;
            transition: background-color 0.2s;
        }

        .modal-close:hover {
            background-color: #F3F4F6;
        }

        .modal-icon {
            width: 48px;
            height: 48px;
            margin: 0 auto 1rem;
            display: block;
        }

        .modal-title {
            font-family: 'Urbanist', sans-serif;
            font-size: 20px;
            font-weight: 700;
            color: #1F2937;
            text-align: center;
            margin-bottom: 0.5rem;
        }

        .modal-subtitle {
            font-family: 'Urbanist', sans-serif;
            font-size: 14px;
            color: #6B7280;
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .modal-buttons {
            display: flex;
            gap: 10px;
            margin-top: 20px;
            width: 100%;
        }

        .modal-button {
            flex: 1;
            padding: 12px 20px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
            text-decoration: none;
            display: inline-block;
        }

        .cancel-button {
            background-color: #f3f4f6;
            color: #374151;
        }

        .cancel-button:hover {
            background-color: #e5e7eb;
        }

        .confirm-button {
            background-color: #39746E;
            color: white;
        }

        .confirm-button:hover {
            background-color: #2d5a55;
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
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @media (max-width: 768px) {
            .controls {
                flex-direction: column;
                align-items: stretch;
            }

            .search-filter {
                order: 2;
                flex-direction: column;
            }

            .search-box {
                max-width: 100%;
            }

            .action-buttons {
                order: 1;
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
            <p style="margin-top: 1rem; font-family: 'Urbanist', sans-serif; color: #374151;">Memuat data...</p>
        </div>
    </div>

    <!-- ================= HEADER & FILTER ================= -->
    @include('partials.dashboard-header-bar', ['title' => 'User Redeem Requests'])

    <div class="container">
        <div class="controls">
            <div class="search-filter">
                <div class="search-box">
                    <input type="text" class="search-input" placeholder="Cari nama user atau item" id="searchInput">
                    <button type="button" class="search-btn" id="searchBtn" onclick="filterRedeems()">
                        <img src="{{ asset('icon/ic_search.svg') }}" alt="Search" class="search-icon">
                    </button>
                </div>
                <div class="filter-group">
                    <span class="filter-label">Tanggal Request:</span>
                    <input type="date" class="date-input" id="dateFrom" onchange="filterRedeems()">
                    <span class="filter-label">-</span>
                    <input type="date" class="date-input" id="dateTo" onchange="filterRedeems()">
                </div>
                <div class="filter-group">
                    <span class="filter-label">Status:</span>
                    <select class="status-select" id="statusFilter" onchange="filterRedeems()">
                        <option value="">All</option>
                        <option value="waiting">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                    </select>
                </div>
            </div>
            <div class="action-buttons">
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
            </div>
        </div>

        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal Request</th>
                        <th>User</th>
                        <th>Nama Item</th>
                        <th>Point yang di-redeem</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="redeemTableBody">
                    @foreach ($redeems as $index => $redeem)
                    <tr>
                        <td>{{ ($redeems->currentPage() - 1) * $redeems->perPage() + $index + 1 }}</td>
                        <td>{{ $redeem->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            <div class="user-info-cell">
                                <span class="user-name-text">{{ $redeem->user_name }}</span>
                                <span class="user-identifier-text">{{ $redeem->user_identifier }}</span>
                            </div>
                        </td>
                        <td>{{ $redeem->redeem_item_name }}</td>
                        <td>{{ number_format($redeem->point_used, 0, ',', '.') }}</td>
                        <td>
                            <span class="status-badge {{ $redeem->status }}">
                                @if($redeem->status === 'waiting')
                                    Pending
                                @elseif($redeem->status === 'approved')
                                    Approved
                                @elseif($redeem->status === 'rejected')
                                    Rejected
                                @else
                                    {{ ucfirst($redeem->status) }}
                                @endif
                            </span>
                        </td>
                        <td>
                            <div class="action-buttons-cell">
                                <button class="action-btn edit-btn" data-id="{{ $redeem->id }}">
                                    <img src="/icon/ic_edit.svg" alt="Edit">
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="no-results" id="noResults" style="display: none;">
                Tidak ada redeem request yang ditemukan
            </div>
        </div>

        <div class="pagination-container" id="paginationContainer">
            <div class="pagination-info" id="paginationInfo"></div>
            <div class="pagination" id="pagination"></div>
        </div>
    </div>

    <!-- Export Excel Modal -->
    <div class="modal" id="exportExcelModal">
        <div class="modal-content">
            <button class="modal-close" id="closeExportModalBtn">
                <img src="{{ asset('icon/ic_close.svg') }}" alt="Close">
            </button>
            <img src="{{ asset('icon/ic_laporan.svg') }}" alt="Export" class="modal-icon">
            <h2 class="modal-title">Export Excel Redeem Requests</h2>
            <p class="modal-subtitle">Apakah Anda yakin ingin mengekspor data redeem requests?</p>

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
            <h2 class="modal-title">Export CSV Redeem Requests</h2>
            <p class="modal-subtitle">Apakah Anda yakin ingin mengekspor data redeem requests?</p>

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
            <h2 class="modal-title">Export PDF Redeem Requests</h2>
            <p class="modal-subtitle">Apakah Anda yakin ingin mengekspor data redeem requests?</p>

            <div class="modal-buttons">
                <button class="modal-button cancel-button" id="cancelPdfBtn">Batal</button>
                <button class="modal-button confirm-button" id="confirmPdfBtn">Export PDF</button>
            </div>
        </div>
    </div>

<script>
    // Loading utility functions
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

        // Hide loading after a short delay
        setTimeout(() => {
            hideLoading();
        }, 500);

        // Initial data loading from server-side
        const initialPaginationData = {
            current_page: {{ $redeems->currentPage() }},
            total: {{ $redeems->total() }},
            per_page: {{ $redeems->perPage() }},
            first_item: {{ $redeems->firstItem() ?? 0 }},
            last_item: {{ $redeems->lastItem() ?? 0 }}
        };

        // Handle no results and pagination visibility
        const tbody = document.getElementById('redeemTableBody');
        const noResults = document.getElementById('noResults');
        const paginationContainer = document.getElementById('paginationContainer');

        if ({{ $redeems->total() }} === 0) {
            tbody.style.display = 'none';
            noResults.style.display = 'block';
            paginationContainer.style.display = 'none';
        } else {
            tbody.style.display = 'table-row-group';
            noResults.style.display = 'none';
            paginationContainer.style.display = 'flex';
        }

        renderPagination(initialPaginationData);

        // Populate filters with current values
        const urlParams = new URLSearchParams(window.location.search);
        const currentSearch = urlParams.get('search');
        const currentDateFrom = urlParams.get('date_from');
        const currentDateTo = urlParams.get('date_to');
        const currentStatus = urlParams.get('status');

        if (currentSearch) {
            document.getElementById('searchInput').value = currentSearch;
        }
        if (currentDateFrom) {
            document.getElementById('dateFrom').value = currentDateFrom;
        }
        if (currentDateTo) {
            document.getElementById('dateTo').value = currentDateTo;
        }
        if (currentStatus) {
            document.getElementById('statusFilter').value = currentStatus;
        }

        // Search functionality
        const searchInput = document.getElementById('searchInput');

        // Search only when Enter is pressed
        searchInput.addEventListener('keyup', function(e) {
            if (e.key === 'Enter') {
                filterRedeems();
            }
        });

        // Event delegation for action buttons
        document.addEventListener('click', function(e) {
            if (e.target.closest('.edit-btn')) {
                const id = e.target.closest('.edit-btn').getAttribute('data-id');
                window.location.href = '{{ route("dashboard.user-redeem.edit", ":id") }}'.replace(':id', id);
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
            if (e.target.classList.contains('modal')) {
                e.target.classList.remove('show');
            }
        });

        // Excel Export Modal
        document.getElementById('closeExportModalBtn').addEventListener('click', function() {
            document.getElementById('exportExcelModal').classList.remove('show');
        });
        document.getElementById('cancelExportBtn').addEventListener('click', function() {
            document.getElementById('exportExcelModal').classList.remove('show');
        });
        document.getElementById('confirmExportBtn').addEventListener('click', function() {
            exportExcel();
        });

        // CSV Export Modal
        document.getElementById('closeCsvModalBtn').addEventListener('click', function() {
            document.getElementById('exportCsvModal').classList.remove('show');
        });
        document.getElementById('cancelCsvBtn').addEventListener('click', function() {
            document.getElementById('exportCsvModal').classList.remove('show');
        });
        document.getElementById('confirmCsvBtn').addEventListener('click', function() {
            exportCsv();
        });

        // PDF Export Modal
        document.getElementById('closePdfModalBtn').addEventListener('click', function() {
            document.getElementById('exportPdfModal').classList.remove('show');
        });
        document.getElementById('cancelPdfBtn').addEventListener('click', function() {
            document.getElementById('exportPdfModal').classList.remove('show');
        });
        document.getElementById('confirmPdfBtn').addEventListener('click', function() {
            exportPdf();
        });
    });

    function filterRedeems() {
        const searchTerm = document.getElementById('searchInput').value;
        const dateFrom = document.getElementById('dateFrom').value;
        const dateTo = document.getElementById('dateTo').value;
        const status = document.getElementById('statusFilter').value;

        // Show loading for filter
        showLoading('Memfilter data...');

        // Build URL with filters
        const url = new URL(window.location);

        if (searchTerm.trim() === '') {
            url.searchParams.delete('search');
        } else {
            url.searchParams.set('search', searchTerm);
        }

        if (dateFrom === '') {
            url.searchParams.delete('date_from');
        } else {
            url.searchParams.set('date_from', dateFrom);
        }

        if (dateTo === '') {
            url.searchParams.delete('date_to');
        } else {
            url.searchParams.set('date_to', dateTo);
        }

        if (status === '') {
            url.searchParams.delete('status');
        } else {
            url.searchParams.set('status', status);
        }

        url.searchParams.delete('page'); // Reset to first page when filtering
        window.location.href = url.toString();
    }

    function renderPagination(paginationData) {
        const paginationContainer = document.getElementById('paginationContainer');
        const paginationInfo = document.getElementById('paginationInfo');
        const pagination = document.getElementById('pagination');

        const { current_page, total, per_page, first_item, last_item } = paginationData;

        if (total === 0) {
            paginationInfo.textContent = '0-0 of 0';
            pagination.innerHTML = '';
            return;
        }

        paginationInfo.textContent = `${first_item}-${last_item} of ${total}`;

        const totalPages = Math.ceil(total / per_page);

        let paginationHTML = '';
        paginationHTML += `<button class="pagination-btn" onclick="changePage(${current_page - 1})" ${current_page <= 1 ? 'disabled' : ''}>&lt;</button>`;
        paginationHTML += `<button class="pagination-btn" onclick="changePage(${current_page + 1})" ${current_page >= totalPages ? 'disabled' : ''}>&gt;</button>`;
        pagination.innerHTML = paginationHTML;
    }

    function changePage(page) {
        // Show loading for pagination
        showLoading('Memuat halaman...');

        const url = new URL(window.location);
        url.searchParams.set('page', page);
        window.location.href = url.toString();
    }

    // Export Functions
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

    function exportExcel() {
        showLoading('Mengexport Excel...');

        const formData = new FormData();
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

        // Add current filters
        const urlParams = new URLSearchParams(window.location.search);
        const dateFrom = urlParams.get('date_from');
        const dateTo = urlParams.get('date_to');
        const status = urlParams.get('status');

        if (dateFrom) formData.append('date_from', dateFrom);
        if (dateTo) formData.append('date_to', dateTo);
        if (status) formData.append('status', status);

        fetch('{{ route("dashboard.user-redeem.export.excel") }}', {
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
            a.download = `user_redeem_export_${new Date().toISOString().slice(0, 10)}.xlsx`;
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
            hideLoading();
            document.getElementById('exportExcelModal').classList.remove('show');
        })
        .catch(error => {
            console.error('Export error:', error);
            hideLoading();
            alert('Gagal export Excel: ' + error.message);
        });
    }

    function exportCsv() {
        showLoading('Mengexport CSV...');

        const formData = new FormData();
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

        // Add current filters
        const urlParams = new URLSearchParams(window.location.search);
        const dateFrom = urlParams.get('date_from');
        const dateTo = urlParams.get('date_to');
        const status = urlParams.get('status');

        if (dateFrom) formData.append('date_from', dateFrom);
        if (dateTo) formData.append('date_to', dateTo);
        if (status) formData.append('status', status);

        fetch('{{ route("dashboard.user-redeem.export.csv") }}', {
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
            a.download = `user_redeem_export_${new Date().toISOString().slice(0, 10)}.csv`;
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
            hideLoading();
            document.getElementById('exportCsvModal').classList.remove('show');
        })
        .catch(error => {
            console.error('Export error:', error);
            hideLoading();
            alert('Gagal export CSV: ' + error.message);
        });
    }

    function exportPdf() {
        showLoading('Mengexport PDF...');

        const formData = new FormData();
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

        // Add current filters
        const urlParams = new URLSearchParams(window.location.search);
        const dateFrom = urlParams.get('date_from');
        const dateTo = urlParams.get('date_to');
        const status = urlParams.get('status');

        if (dateFrom) formData.append('date_from', dateFrom);
        if (dateTo) formData.append('date_to', dateTo);
        if (status) formData.append('status', status);

        fetch('{{ route("dashboard.user-redeem.export.pdf") }}', {
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
            a.download = `user_redeem_export_${new Date().toISOString().slice(0, 10)}.pdf`;
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
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
