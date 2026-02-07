@extends('dashboard')
@section('content')
<!-- ================= HEADER & FILTER ================= -->
@include('partials.dashboard-header-bar', ['title' => 'Item Redeem'])
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Item Redeem - Admin Panel</title>
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
            font-family: 'Urbanist', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
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
            background: #fff;
            border: 1px solid #E5E6E6;
            border-radius: 8px;
            font-family: 'Urbanist', sans-serif;
            font-size: 15px;
            font-weight: 600;
            color: #39746E;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            height: 37px;
            transition: all 0.2s;
        }

        .filter-button:hover {
            background: #F8F9FA;
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
            min-width: 160px;
            padding: 0.5rem;
            margin-top: 4px;
        }

        .filter-dropdown-content.show {
            display: block;
        }

        .filter-option {
            padding: 8px 12px;
            cursor: pointer;
            transition: background-color 0.2s;
            border-radius: 4px;
            font-family: 'Urbanist', sans-serif;
            font-size: 14px;
            color: #1e293b;
        }

        .filter-option:hover {
            background-color: #f3f4f6;
        }

        .filter-option.active {
            background-color: #E3F4F1;
            color: #39746E;
            font-weight: 600;
        }

        .action-buttons {
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .add-button {
            padding: 0 1rem;
            background: #39746E;
            border: none;
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

        .add-button:hover {
            background: #2d5a55;
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

        .delete-button:hover {
            background: #FDCED1;
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

        .table-container {
            margin-top: 20px;
            overflow-x: auto;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            font-family: 'Urbanist', sans-serif;
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
        }

        .table tr:hover {
            background: #F8F9FA;
        }

        .checkbox-cell {
            width: 20px;
            text-align: center;
        }

        .item-checkbox, .select-all-checkbox {
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

        .item-checkbox:checked, .select-all-checkbox:checked {
            background-color: #39746E;
            border-color: #39746E;
        }

        .item-checkbox:checked::after, .select-all-checkbox:checked::after {
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

        .action-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
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

        .action-btn.delete-btn {
            color: #ef4444;
        }

        .action-btn.delete-btn:hover {
            background-color: #fef2f2;
        }

        .status-badge {
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
            display: inline-block;
        }

        .status-badge.active {
            background: #E3F4F1;
            color: #39746E;
        }

        .status-badge.inactive {
            background: #FEE2E2;
            color: #DC2626;
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

        .pagination {
            display: flex;
            gap: 8px;
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
            padding: 3rem;
            color: #6b7280;
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

        @media (max-width: 768px) {
            .controls {
                flex-direction: column;
                align-items: stretch;
            }

            .search-filter {
                order: 2;
                flex-direction: column;
            }

            .action-buttons {
                order: 1;
                justify-content: center;
                flex-wrap: wrap;
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

    <div class="container">
        <div class="controls">
            <div class="search-filter">
                <div class="search-box">
                    <input type="text" class="search-input" placeholder="Cari item redeem disini" id="searchInput">
                    <img src="{{ asset('icon/ic_search.svg') }}" alt="Search" class="search-icon">
                </div>
                <div class="filter-dropdown">
                    <button class="filter-button" id="filterButton">
                        <span id="filterButtonText">Semua Status</span>
                        <img src="{{ asset('icon/ic_trailing.svg') }}" alt="Filter" width="16" height="16">
                    </button>
                    <div class="filter-dropdown-content" id="filterDropdown">
                        <div class="filter-option active" data-status="">Semua Status</div>
                        <div class="filter-option" data-status="active">Active</div>
                        <div class="filter-option" data-status="inactive">Inactive</div>
                    </div>
                </div>
            </div>
            <div class="action-buttons">
                <button class="delete-button" id="deleteButton" style="display: none;">
                    <span>Hapus Item</span>
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
                    <span>Tambah Item</span>
                    <img src="{{ asset('icon/ic_add.svg') }}" alt="Add" width="16" height="16">
                </button>
            </div>
        </div>

        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th class="checkbox-cell">
                            <input type="checkbox" id="selectAll" class="select-all-checkbox">
                        </th>
                        <th style="min-width: 60px;">No</th>
                        <th style="min-width: 200px;">Nama Item</th>
                        <th style="min-width: 300px;">Deskripsi Item</th>
                        <th style="min-width: 150px;">Point Required</th>
                        <th style="min-width: 100px;">Status</th>
                        <th style="min-width: 150px;">Created At</th>
                        <th class="action-cell">Action</th>
                    </tr>
                </thead>
                <tbody id="redeemItemsTableBody">
                    @foreach ($redeemItems as $index => $item)
                        <tr>
                            <td class="checkbox-cell">
                                <input type="checkbox" class="item-checkbox" value="{{ $item->id }}" onchange="updateSelectedItems()">
                            </td>
                            <td>{{ $redeemItems->firstItem() + $index }}</td>
                            <td style="font-weight: 600; color: #39746E;">{{ $item->name }}</td>
                            <td style="color: #6B7271;">{{ $item->description ? Str::limit($item->description, 60) : '-' }}</td>
                            <td style="font-weight: 600;">{{ number_format($item->points_required, 0, ',', '.') }} poin</td>
                            <td>
                                <span class="status-badge {{ $item->is_active ? 'active' : 'inactive' }}">
                                    {{ $item->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td style="color: #6B7271;">{{ $item->created_at->format('d M Y H:i') }}</td>
                            <td class="action-cell">
                                <div class="action-buttons-cell">
                                    <a href="{{ route('dashboard.redeem-item.edit', $item->id) }}" class="action-btn edit-btn" title="Edit">
                                        <img src="/icon/ic_edit.svg" alt="Edit">
                                    </a>
                                    <button class="action-btn delete-btn" data-id="{{ $item->id }}" title="Hapus">
                                        <img src="/icon/ic_delete.svg" alt="Delete">
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="no-results" id="noResults" style="display: none;">
                <h3>Belum ada data item redeem</h3>
                <p>Mulai dengan menambahkan item redeem pertama Anda</p>
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
            <h2 class="modal-title">Hapus Item Redeem</h2>
            <p class="modal-subtitle">Apakah Anda yakin ingin menghapus item redeem yang dipilih?</p>
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
            <h2 class="modal-title">Export Excel Item Redeem</h2>
            <p class="modal-subtitle">Apakah Anda yakin ingin mengekspor data item redeem?</p>
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
            <h2 class="modal-title">Export CSV Item Redeem</h2>
            <p class="modal-subtitle">Apakah Anda yakin ingin mengekspor data item redeem?</p>
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
            <h2 class="modal-title">Export PDF Item Redeem</h2>
            <p class="modal-subtitle">Apakah Anda yakin ingin mengekspor data item redeem?</p>
            <div class="modal-buttons">
                <button class="modal-button cancel-button" id="cancelPdfBtn">Batal</button>
                <button class="modal-button confirm-button" id="confirmPdfBtn">Export PDF</button>
            </div>
        </div>
    </div>

    <script>
        let selectedItems = [];
        let currentStatus = '';

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
            showLoading('Memuat data...');

            setTimeout(() => {
                hideLoading();
            }, 500);

            const initialPaginationData = {
                current_page: {{ $redeemItems->currentPage() }},
                total: {{ $redeemItems->total() }},
                per_page: {{ $redeemItems->perPage() }},
                first_item: {{ $redeemItems->firstItem() ?? 0 }},
                last_item: {{ $redeemItems->lastItem() ?? 0 }}
            };

            const tbody = document.getElementById('redeemItemsTableBody');
            const noResults = document.getElementById('noResults');
            const paginationContainer = document.getElementById('paginationContainer');

            if ({{ $redeemItems->total() }} === 0) {
                tbody.style.display = 'none';
                noResults.style.display = 'block';
                paginationContainer.style.display = 'none';
            } else {
                tbody.style.display = 'table-row-group';
                noResults.style.display = 'none';
                paginationContainer.style.display = 'flex';
            }

            renderPagination(initialPaginationData);

            const urlParams = new URLSearchParams(window.location.search);
            const currentSearch = urlParams.get('search');
            const currentStatusParam = urlParams.get('status');

            if (currentSearch) {
                document.getElementById('searchInput').value = currentSearch;
            }

            if (currentStatusParam) {
                currentStatus = currentStatusParam;
                updateFilterButtonText(currentStatusParam);
                updateActiveFilterOption(currentStatusParam);
            }

            setupEventListeners();
        });

        function setupEventListeners() {
            const searchInput = document.getElementById('searchInput');
            if (searchInput) {
                searchInput.addEventListener('keyup', function(e) {
                    if (e.key === 'Enter') {
                        filterData();
                    }
                });
            }

            const selectAll = document.getElementById('selectAll');
            if (selectAll) {
                selectAll.addEventListener('change', function() {
                    const checkboxes = document.querySelectorAll('.item-checkbox');
                    checkboxes.forEach(cb => { cb.checked = this.checked; });
                    updateSelectedItems();
                });
            }

            const addButton = document.getElementById('addButton');
            if (addButton) {
                addButton.addEventListener('click', function() {
                    window.location.href = '{{ route("dashboard.redeem-item.create") }}';
                });
            }

            const deleteButton = document.getElementById('deleteButton');
            if (deleteButton) {
                deleteButton.addEventListener('click', function() {
                    if (selectedItems.length > 0) { showDeleteModal(true); }
                });
            }

            document.getElementById('closeModalBtn').addEventListener('click', closeDeleteModal);
            document.getElementById('cancelDeleteBtn').addEventListener('click', closeDeleteModal);
            document.getElementById('confirmDeleteBtn').addEventListener('click', confirmDelete);

            const tableBody = document.getElementById('redeemItemsTableBody');
            if (tableBody) {
                tableBody.addEventListener('click', function(e) {
                    if (e.target.closest('.delete-btn')) {
                        const id = parseInt(e.target.closest('.delete-btn').getAttribute('data-id'));
                        selectedItems = [id];
                        showDeleteModal(false);
                    }
                });
            }

            document.getElementById('deleteModal').addEventListener('click', function(e) {
                if (e.target === this) {
                    closeDeleteModal();
                }
            });

            // Filter dropdown
            const filterButton = document.getElementById('filterButton');
            filterButton.addEventListener('click', function(e) {
                e.stopPropagation();
                document.getElementById('filterDropdown').classList.toggle('show');
            });

            const filterOptions = document.querySelectorAll('.filter-option');
            filterOptions.forEach(option => {
                option.addEventListener('click', function() {
                    const status = this.getAttribute('data-status');
                    currentStatus = status;
                    updateFilterButtonText(status);
                    updateActiveFilterOption(status);
                    document.getElementById('filterDropdown').classList.remove('show');
                    filterData();
                });
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
                if (!e.target.closest('.filter-dropdown')) {
                    document.getElementById('filterDropdown').classList.remove('show');
                }
            });

            // Export modals
            document.getElementById('closeExportModalBtn').addEventListener('click', () => document.getElementById('exportExcelModal').classList.remove('show'));
            document.getElementById('cancelExportBtn').addEventListener('click', () => document.getElementById('exportExcelModal').classList.remove('show'));
            document.getElementById('confirmExportBtn').addEventListener('click', exportExcel);

            document.getElementById('closeCsvModalBtn').addEventListener('click', () => document.getElementById('exportCsvModal').classList.remove('show'));
            document.getElementById('cancelCsvBtn').addEventListener('click', () => document.getElementById('exportCsvModal').classList.remove('show'));
            document.getElementById('confirmCsvBtn').addEventListener('click', exportCsv);

            document.getElementById('closePdfModalBtn').addEventListener('click', () => document.getElementById('exportPdfModal').classList.remove('show'));
            document.getElementById('cancelPdfBtn').addEventListener('click', () => document.getElementById('exportPdfModal').classList.remove('show'));
            document.getElementById('confirmPdfBtn').addEventListener('click', exportPdf);
        }

        function updateFilterButtonText(status) {
            const buttonText = document.getElementById('filterButtonText');
            if (status === '') {
                buttonText.textContent = 'Semua Status';
            } else if (status === 'active') {
                buttonText.textContent = 'Active';
            } else if (status === 'inactive') {
                buttonText.textContent = 'Inactive';
            }
        }

        function updateActiveFilterOption(status) {
            const filterOptions = document.querySelectorAll('.filter-option');
            filterOptions.forEach(option => {
                option.classList.remove('active');
                if (option.getAttribute('data-status') === status) {
                    option.classList.add('active');
                }
            });
        }

        function filterData() {
            const searchTerm = document.getElementById('searchInput').value;

            showLoading('Mencari data...');

            const url = new URL(window.location);
            if (searchTerm.trim() === '') {
                url.searchParams.delete('search');
            } else {
                url.searchParams.set('search', searchTerm);
            }
            if (currentStatus === '') {
                url.searchParams.delete('status');
            } else {
                url.searchParams.set('status', currentStatus);
            }
            url.searchParams.delete('page');
            window.location.href = url.toString();
        }

        function changePage(page) {
            if (page < 1) return;

            showLoading('Memuat halaman...');

            const url = new URL(window.location);
            url.searchParams.set('page', page);
            window.location.href = url.toString();
        }

        function renderPagination(paginationData) {
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

        function updateSelectedItems() {
            const checkboxes = document.querySelectorAll('.item-checkbox:checked');
            selectedItems = Array.from(checkboxes).map(cb => parseInt(cb.value));
            const selectAllCheckbox = document.getElementById('selectAll');
            const allCheckboxes = document.querySelectorAll('.item-checkbox');
            selectAllCheckbox.checked = allCheckboxes.length > 0 && selectedItems.length === allCheckboxes.length;

            const deleteButton = document.getElementById('deleteButton');
            if (deleteButton) {
                if (selectedItems.length > 0) {
                    deleteButton.style.display = 'flex';
                } else {
                    deleteButton.style.display = 'none';
                }
            }
        }

        function showDeleteModal(isMultiple) {
            const modal = document.getElementById('deleteModal');
            const title = document.querySelector('#deleteModal .modal-title');
            const subtitle = document.querySelector('#deleteModal .modal-subtitle');

            if (isMultiple) {
                title.textContent = 'Hapus Item Redeem';
                subtitle.textContent = `Apakah Anda yakin ingin menonaktifkan ${selectedItems.length} item redeem yang dipilih?`;
            } else {
                title.textContent = 'Hapus Item Redeem';
                subtitle.textContent = 'Apakah Anda yakin ingin menonaktifkan item redeem ini?';
            }

            modal.classList.add('show');
        }

        function closeDeleteModal() {
            const modal = document.getElementById('deleteModal');
            if (modal) modal.classList.remove('show');
        }

        function confirmDelete() {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            let url = '';
            let body = {};
            let isMultiple = selectedItems.length > 1;

            showLoading('Menghapus data...');

            if (isMultiple) {
                url = `/dashboard/redeem-item/0`;
                body = { ids: selectedItems };
            } else {
                url = `/dashboard/redeem-item/${selectedItems[0]}`;
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
                hideLoading();
                closeDeleteModal();

                if (data.success) {
                    alert(data.message || 'Item redeem berhasil dihapus');
                    location.reload();
                } else {
                    alert('Gagal menghapus item redeem: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Delete error:', error);
                hideLoading();
                closeDeleteModal();
                alert('Terjadi kesalahan saat menghapus item redeem');
            });
        }

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

            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("dashboard.redeem-item.export.excel") }}';

            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            form.appendChild(csrfToken);

            document.body.appendChild(form);
            form.submit();
            document.body.removeChild(form);

            setTimeout(() => {
                hideLoading();
                document.getElementById('exportExcelModal').classList.remove('show');
            }, 1000);
        }

        function exportCsv() {
            showLoading('Mengexport CSV...');

            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("dashboard.redeem-item.export.csv") }}';

            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            form.appendChild(csrfToken);

            document.body.appendChild(form);
            form.submit();
            document.body.removeChild(form);

            setTimeout(() => {
                hideLoading();
                document.getElementById('exportCsvModal').classList.remove('show');
            }, 1000);
        }

        function exportPdf() {
            showLoading('Mengexport PDF...');

            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("dashboard.redeem-item.export.pdf") }}';

            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            form.appendChild(csrfToken);

            document.body.appendChild(form);
            form.submit();
            document.body.removeChild(form);

            setTimeout(() => {
                hideLoading();
                document.getElementById('exportPdfModal').classList.remove('show');
            }, 1000);
        }
    </script>
</body>
</html>
@endsection
