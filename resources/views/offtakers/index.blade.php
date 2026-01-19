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

        .offtaker-checkbox, .select-all-checkbox {
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

        .offtaker-checkbox:checked, .select-all-checkbox:checked {
            background-color: #39746E;
            border-color: #39746E;
        }

        .offtaker-checkbox:checked::after, .select-all-checkbox:checked::after {
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

    <div style="padding: 0 2rem 2rem 2rem;">
        <div class="container">
            <div class="controls">
                <div class="search-filter">
                    <div class="search-box">
                        <input type="text" class="search-input" placeholder="Cari offtaker disini" id="searchInput">
                        <img src="{{ asset('icon/ic_search.svg') }}" alt="Search" class="search-icon">
                    </div>
                </div>
                <div class="action-buttons">
                    <button class="delete-button" id="deleteButton" style="display: none;">
                        <span>Hapus Offtaker</span>
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
                        <span>Tambah Offtaker</span>
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
                            <th>Kode</th>
                            <th>Nama</th>
                            <th>Tipe</th>
                            <th>PIC</th>
                            <th>Kontak</th>
                            <th>Transaksi</th>
                            <th>Status</th>
                            <th class="action-cell">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="offtakerTableBody">
                        <!-- Data akan diisi oleh JavaScript -->
                    </tbody>
                </table>
                <div class="no-results" id="noResults" style="display: none;">
                    Tidak ada offtaker yang ditemukan
                </div>
            </div>

            <div class="pagination-container" id="paginationContainer">
                <div class="pagination-info" id="paginationInfo"></div>
                <div class="pagination" id="pagination"></div>
            </div>
        </div>
    </div>

    <div class="modal" id="deleteModal">
        <div class="modal-content">
            <button class="modal-close" id="closeModalBtn">
                <img src="{{ asset('icon/ic_close.svg') }}" alt="Close">
            </button>
            <img src="{{ asset('icon/ic_dialog_delete.svg') }}" alt="Delete" class="modal-icon">
            <h2 class="modal-title">Hapus Offtaker</h2>
            <p class="modal-subtitle">Apakah Anda yakin ingin menghapus offtaker yang dipilih?</p>
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
            <h2 class="modal-title">Export Excel Offtaker</h2>
            <p class="modal-subtitle">Export data offtaker ke format Excel</p>

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
            <h2 class="modal-title">Export CSV Offtaker</h2>
            <p class="modal-subtitle">Export data offtaker ke format CSV</p>

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
            <h2 class="modal-title">Export PDF Offtaker</h2>
            <p class="modal-subtitle">Export data offtaker ke format PDF</p>

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

        // Data offtaker dari backend
        let offtakers = [
            @foreach ($offtakers as $offtaker)
            {
                id: {{ $offtaker->id }},
                kode: @json($offtaker->kode_offtaker),
                nama: @json($offtaker->nama),
                tipe: @json($offtaker->tipe),
                nama_pic: @json($offtaker->nama_pic),
                kontak_pic: @json($offtaker->kontak_pic),
                transaksi_count: {{ $offtaker->waste_transactions_count ?? 0 }},
                is_active: {{ $offtaker->is_active ? 'true' : 'false' }}
            },
            @endforeach
        ];
        let filteredOfftakers = [...offtakers];
        let selectedOfftakers = [];
        let currentPage = 1;
        const itemsPerPage = 10;
        let totalPages = 1;

        document.addEventListener('DOMContentLoaded', function() {
            // Show loading on initial page load
            showLoading('Memuat data...');

            // Hide loading after a short delay to simulate loading
            setTimeout(() => {
                hideLoading();
            }, 500);

            // Initial data loading
            const initialPaginationData = {
                current_page: {{ $offtakers->currentPage() }},
                total: {{ $offtakers->total() }},
                per_page: {{ $offtakers->perPage() }},
                first_item: {{ $offtakers->firstItem() ?? 0 }},
                last_item: {{ $offtakers->lastItem() ?? 0 }}
            };

            renderOfftakers();
            renderPagination(initialPaginationData);
            setupEventListeners();
        });

        function setupEventListeners() {
            // Search only when Enter is pressed
            document.getElementById('searchInput').addEventListener('keyup', function(e) {
                if (e.key === 'Enter') {
                    filterOfftakers();
                }
            });

            document.getElementById('selectAll').addEventListener('change', function() {
                const checkboxes = document.querySelectorAll('.offtaker-checkbox');
                checkboxes.forEach(cb => { cb.checked = this.checked; });
                updateSelectedOfftakers();
            });

            document.getElementById('addButton').addEventListener('click', function() {
                window.location.href = '{{ route("offtakers.create") }}';
            });

            document.getElementById('deleteButton').addEventListener('click', function() {
                if (selectedOfftakers.length > 0) { showDeleteModal(true); }
            });

            // Modal close
            document.getElementById('closeModalBtn').addEventListener('click', closeDeleteModal);
            document.getElementById('cancelDeleteBtn').addEventListener('click', closeDeleteModal);
            document.getElementById('confirmDeleteBtn').addEventListener('click', confirmDelete);

            // Action buttons in table
            document.getElementById('offtakerTableBody').addEventListener('click', function(e) {
                if (e.target.closest('.detail-btn')) {
                    const id = e.target.closest('.detail-btn').getAttribute('data-id');
                    window.location.href = `/offtakers/${id}`;
                } else if (e.target.closest('.edit-btn')) {
                    const id = e.target.closest('.edit-btn').getAttribute('data-id');
                    window.location.href = `/offtakers/${id}/edit`;
                } else if (e.target.closest('.delete-btn')) {
                    const id = parseInt(e.target.closest('.delete-btn').getAttribute('data-id'));
                    selectedOfftakers = [id];
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

            // Export Modal Event Listeners
            document.getElementById('closeExportModalBtn').addEventListener('click', closeExportExcelModal);
            document.getElementById('cancelExportBtn').addEventListener('click', closeExportExcelModal);
            document.getElementById('confirmExportBtn').addEventListener('click', confirmExportExcel);

            document.getElementById('exportExcelModal').addEventListener('click', function(e) {
                if (e.target === this) {
                    closeExportExcelModal();
                }
            });

            document.getElementById('closeCsvModalBtn').addEventListener('click', closeExportCsvModal);
            document.getElementById('cancelCsvBtn').addEventListener('click', closeExportCsvModal);
            document.getElementById('confirmCsvBtn').addEventListener('click', confirmExportCsv);

            document.getElementById('exportCsvModal').addEventListener('click', function(e) {
                if (e.target === this) {
                    closeExportCsvModal();
                }
            });

            document.getElementById('closePdfModalBtn').addEventListener('click', closeExportPdfModal);
            document.getElementById('cancelPdfBtn').addEventListener('click', closeExportPdfModal);
            document.getElementById('confirmPdfBtn').addEventListener('click', confirmExportPdf);

            document.getElementById('exportPdfModal').addEventListener('click', function(e) {
                if (e.target === this) {
                    closeExportPdfModal();
                }
            });
        }

        function filterOfftakers() {
            const searchTerm = document.getElementById('searchInput').value;

            // Show loading for search
            showLoading('Mencari data...');

            currentPage = 1;
            loadOfftakers(searchTerm, currentPage);
        }

        function changePage(page) {
            if (page < 1) return;

            // Show loading for pagination
            showLoading('Memuat halaman...');

            const searchTerm = document.getElementById('searchInput').value;
            loadOfftakers(searchTerm, page);
            document.querySelector('.table-container').scrollIntoView({ behavior: 'smooth' });
        }

        function loadOfftakers(search, page) {
            const url = new URL(window.location.href);
            url.searchParams.set('search', search);
            url.searchParams.set('page', page);

            fetch(url.toString(), {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                console.log('Server response:', data);

                // Hide loading after data is loaded
                hideLoading();

                if (data.offtakers && data.offtakers.data) {
                    offtakers = data.offtakers.data.map(offtaker => ({
                        id: offtaker.id,
                        kode: offtaker.kode_offtaker,
                        nama: offtaker.nama,
                        tipe: offtaker.tipe,
                        nama_pic: offtaker.nama_pic,
                        kontak_pic: offtaker.kontak_pic,
                        transaksi_count: offtaker.waste_transactions_count || 0,
                        is_active: offtaker.is_active
                    }));

                    filteredOfftakers = [...offtakers];
                    currentPage = data.offtakers.current_page;

                    renderOfftakers();

                    const paginationData = {
                        current_page: data.offtakers.current_page,
                        total: data.offtakers.total,
                        per_page: data.offtakers.per_page,
                        first_item: data.offtakers.from || 0,
                        last_item: data.offtakers.to || 0
                    };
                    renderPagination(paginationData);

                    selectedOfftakers = [];
                    updateSelectedOfftakers();
                } else {
                    offtakers = [];
                    filteredOfftakers = [];
                    renderOfftakers();
                    renderPagination({
                        current_page: 1,
                        total: 0,
                        per_page: itemsPerPage,
                        first_item: 0,
                        last_item: 0
                    });
                    selectedOfftakers = [];
                    updateSelectedOfftakers();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                hideLoading();
                offtakers = [];
                filteredOfftakers = [];
                renderOfftakers();
                renderPagination({
                    current_page: 1,
                    total: 0,
                    per_page: itemsPerPage,
                    first_item: 0,
                    last_item: 0
                });
                selectedOfftakers = [];
                updateSelectedOfftakers();
            });
        }

        function renderOfftakers() {
            const tbody = document.getElementById('offtakerTableBody');
            const noResults = document.getElementById('noResults');
            const paginationContainer = document.getElementById('paginationContainer');

            if (filteredOfftakers.length === 0) {
                tbody.innerHTML = '';
                noResults.style.display = 'block';
                paginationContainer.style.display = 'none';
                return;
            }

            noResults.style.display = 'none';
            paginationContainer.style.display = 'flex';

            tbody.innerHTML = filteredOfftakers.map(offtaker => {
                let tipeBadge = '';
                if (offtaker.tipe === 'buyer') {
                    tipeBadge = '<span class="badge badge-buyer">Pembeli</span>';
                } else if (offtaker.tipe === 'processor') {
                    tipeBadge = '<span class="badge badge-processor">Pengolah</span>';
                } else {
                    tipeBadge = '<span class="badge badge-both">Keduanya</span>';
                }

                const statusBadge = offtaker.is_active
                    ? '<span class="badge badge-active">Aktif</span>'
                    : '<span class="badge badge-inactive">Nonaktif</span>';

                return `
                    <tr>
                        <td class="checkbox-cell">
                            <input type="checkbox" class="offtaker-checkbox" value="${offtaker.id}" onchange="updateSelectedOfftakers()">
                        </td>
                        <td><strong>${offtaker.kode}</strong></td>
                        <td>${offtaker.nama}</td>
                        <td>${tipeBadge}</td>
                        <td>${offtaker.nama_pic || '-'}</td>
                        <td>${offtaker.kontak_pic || '-'}</td>
                        <td>${offtaker.transaksi_count}</td>
                        <td>${statusBadge}</td>
                        <td class="action-cell">
                            <div class="action-buttons-cell">
                                <button class="action-btn detail-btn" data-id="${offtaker.id}" title="Detail">
                                    <img src="/icon/ic_detail.svg" alt="Detail">
                                </button>
                                <button class="action-btn edit-btn" data-id="${offtaker.id}" title="Edit">
                                    <img src="/icon/ic_edit.svg" alt="Edit">
                                </button>
                                <button class="action-btn delete-btn" data-id="${offtaker.id}" title="Hapus">
                                    <img src="/icon/ic_delete.svg" alt="Delete">
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            }).join('');
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

        function updateSelectedOfftakers() {
            const checkboxes = document.querySelectorAll('.offtaker-checkbox:checked');
            selectedOfftakers = Array.from(checkboxes).map(cb => parseInt(cb.value));
            const selectAllCheckbox = document.getElementById('selectAll');
            const allCheckboxes = document.querySelectorAll('.offtaker-checkbox');
            selectAllCheckbox.checked = allCheckboxes.length > 0 && selectedOfftakers.length === allCheckboxes.length;

            const deleteButton = document.getElementById('deleteButton');
            if (selectedOfftakers.length > 0) {
                deleteButton.style.display = 'flex';
            } else {
                deleteButton.style.display = 'none';
            }
        }

        function showDeleteModal(isMultiple) {
            const modal = document.getElementById('deleteModal');
            const title = document.querySelector('#deleteModal .modal-title');
            const subtitle = document.querySelector('#deleteModal .modal-subtitle');

            if (isMultiple) {
                title.textContent = 'Hapus Offtaker';
                subtitle.textContent = `Apakah Anda yakin ingin menghapus ${selectedOfftakers.length} offtaker yang dipilih?`;
            } else {
                title.textContent = 'Hapus Offtaker';
                subtitle.textContent = 'Apakah Anda yakin ingin menghapus offtaker ini?';
            }

            modal.classList.add('show');
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.remove('show');
        }

        function confirmDelete() {
            // Show loading for delete operation
            showLoading('Menghapus offtaker...');

            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            // For now, just close the modal and show alert
            // TODO: Implement actual delete endpoint when ready
            setTimeout(() => {
                hideLoading();
                alert('Delete functionality will be implemented when backend routes are ready');
                closeDeleteModal();
            }, 1000);
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

        function confirmExportExcel() {
            showLoading('Mengexport Excel...');

            // TODO: Implement actual export when backend routes are ready
            setTimeout(() => {
                hideLoading();
                alert('Export Excel functionality will be implemented when backend routes are ready');
                closeExportExcelModal();
            }, 1000);
        }

        function showExportCsvModal() {
            document.getElementById('exportCsvModal').classList.add('show');
        }

        function closeExportCsvModal() {
            document.getElementById('exportCsvModal').classList.remove('show');
        }

        function confirmExportCsv() {
            showLoading('Mengexport CSV...');

            // TODO: Implement actual export when backend routes are ready
            setTimeout(() => {
                hideLoading();
                alert('Export CSV functionality will be implemented when backend routes are ready');
                closeExportCsvModal();
            }, 1000);
        }

        function showExportPdfModal() {
            document.getElementById('exportPdfModal').classList.add('show');
        }

        function closeExportPdfModal() {
            document.getElementById('exportPdfModal').classList.remove('show');
        }

        function confirmExportPdf() {
            showLoading('Mengexport PDF...');

            // TODO: Implement actual export when backend routes are ready
            setTimeout(() => {
                hideLoading();
                alert('Export PDF functionality will be implemented when backend routes are ready');
                closeExportPdfModal();
            }, 1000);
        }
    </script>
</body>
</html>
@endsection
