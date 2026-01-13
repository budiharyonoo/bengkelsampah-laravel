@extends('dashboard')
@section('content')
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User - Admin Panel</title>
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
            transition: all 0.2s;
        }

        .delete-button:hover {
            background: #FEF2F2;
        }

        .delete-button img {
            width: 16px;
            height: 16px;
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

        .checkbox-cell {
            width: 20px;
            text-align: center;
        }

        .user-checkbox {
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

        .user-checkbox:checked {
            background-color: #39746E;
            border-color: #39746E;
        }

        .user-checkbox:checked::after {
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
    @include('partials.dashboard-header-bar', ['title' => 'User'])

    <div class="container">
        <div class="controls">
            <div class="search-filter">
                    <div class="search-box">
                    <input type="text" class="search-input" placeholder="Cari user disini" id="searchInput">
                    <button type="button" class="search-btn" id="searchBtn" onclick="filterUsers()">
                        <img src="{{ asset('icon/ic_search.svg') }}" alt="Search" class="search-icon">
                    </button>
                </div>
            </div>
            <div class="action-buttons">
                <button class="delete-button" id="deleteButton">
                    <span>Hapus User</span>
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
            </div>
        </div>

        <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>
                            <input type="checkbox" id="selectAll" class="select-all-checkbox">
                                </th>
                                <th>Nama</th>
                                <th>Identifier</th>
                                <th>Jenis Nasabah</th>
                                <th>Poin</th>
                                <th>XP</th>
                                <th>Setoran</th>
                        <th>Sampah (kg)</th>
                                <th>Tanggal Registrasi</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                <tbody id="userTableBody">
                    @foreach ($users as $user)
                            <tr>
                        <td class="checkbox-cell">
                            <input type="checkbox" class="user-checkbox" value="{{ $user->id }}" onchange="updateSelectedUsers()">
                                </td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->identifier }}</td>
                                <td>{{ $user->jenis_nasabah }}</td>
                        <td>{{ number_format($user->poin, 2, ',', '.') }}</td>
                        <td>{{ number_format($user->xp, 0) }}</td>
                                <td>{{ $user->setor }}</td>
                        <td>{{ number_format($user->sampah, 1) }}</td>
                                <td>{{ $user->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                            <div class="action-buttons-cell">
                                <button class="action-btn detail-btn" data-id="{{ $user->id }}">
                                    <img src="/icon/ic_detail.svg" alt="Detail">
                                </button>
                                <button class="action-btn edit-btn" data-id="{{ $user->id }}">
                                    <img src="/icon/ic_edit.svg" alt="Edit">
                                </button>
                                <button class="action-btn delete-btn" data-id="{{ $user->id }}">
                                    <img src="/icon/ic_delete.svg" alt="Delete">
                                            </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
            <div class="no-results" id="noResults" style="display: none;">
                Tidak ada user yang ditemukan
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
            <h2 class="modal-title">Hapus User</h2>
            <p class="modal-subtitle">Apakah Anda yakin ingin menghapus user yang dipilih?</p>
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
            <h2 class="modal-title">Export Excel User</h2>
            <p class="modal-subtitle">Apakah Anda yakin ingin mengekspor data user?</p>
            
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
            <h2 class="modal-title">Export CSV User</h2>
            <p class="modal-subtitle">Apakah Anda yakin ingin mengekspor data user?</p>
            
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
            <h2 class="modal-title">Export PDF User</h2>
            <p class="modal-subtitle">Apakah Anda yakin ingin mengekspor data user?</p>
            
            <div class="modal-buttons">
                <button class="modal-button cancel-button" id="cancelPdfBtn">Batal</button>
                <button class="modal-button confirm-button" id="confirmPdfBtn">Export PDF</button>
            </div>
        </div>
    </div>

<script>
        let selectedUsers = [];

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

            // Initial data loading from server-side
            const initialPaginationData = {
                current_page: {{ $users->currentPage() }},
                total: {{ $users->total() }},
                per_page: {{ $users->perPage() }},
                first_item: {{ $users->firstItem() ?? 0 }},
                last_item: {{ $users->lastItem() ?? 0 }}
            };
            
            // Handle no results and pagination visibility
            const tbody = document.getElementById('userTableBody');
            const noResults = document.getElementById('noResults');
            const paginationContainer = document.getElementById('paginationContainer');
            
            if ({{ $users->total() }} === 0) {
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
                    filterUsers();
                }
            });

            // Delete button
            document.getElementById('deleteButton').addEventListener('click', function() {
                if (selectedUsers.length > 0) { 
                    showDeleteModal(true); 
                }
            });

            // Select all checkbox
            document.getElementById('selectAll').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.user-checkbox');
                checkboxes.forEach(cb => { cb.checked = this.checked; });
                updateSelectedUsers();
            });

            // Modal buttons
            document.getElementById('closeModalBtn').addEventListener('click', closeDeleteModal);
            document.getElementById('cancelDeleteBtn').addEventListener('click', closeDeleteModal);
            document.getElementById('confirmDeleteBtn').addEventListener('click', confirmDelete);

            // Event delegation for action buttons
            document.addEventListener('click', function(e) {
                if (e.target.closest('.detail-btn')) {
                    const id = e.target.closest('.detail-btn').getAttribute('data-id');
                    window.location.href = '{{ route("dashboard.user.show", ":id") }}'.replace(':id', id);
                } else if (e.target.closest('.edit-btn')) {
                    const id = e.target.closest('.edit-btn').getAttribute('data-id');
                    window.location.href = '{{ route("dashboard.user.edit", ":id") }}'.replace(':id', id);
                } else if (e.target.closest('.delete-btn')) {
                    const id = parseInt(e.target.closest('.delete-btn').getAttribute('data-id'));
                    selectedUsers = [id];
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

        function filterUsers() {
            const searchTerm = document.getElementById('searchInput').value;
            
            // Show loading for search
            const overlay = document.getElementById('pageLoadingOverlay');
            const loadingText = overlay.querySelector('p');
            loadingText.textContent = 'Mencari data...';
            overlay.style.display = 'flex';
            
            // Always use server-side search for consistent browser behavior
            const url = new URL(window.location);
            if (searchTerm.trim() === '') {
                url.searchParams.delete('search');
    } else {
                url.searchParams.set('search', searchTerm);
            }
            url.searchParams.delete('page'); // Reset to first page when searching
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

        function updateSelectedUsers() {
            const checkboxes = document.querySelectorAll('.user-checkbox:checked');
            selectedUsers = Array.from(checkboxes).map(cb => parseInt(cb.value));
            const selectAllCheckbox = document.getElementById('selectAll');
            const allCheckboxes = document.querySelectorAll('.user-checkbox');
            selectAllCheckbox.checked = allCheckboxes.length > 0 && selectedUsers.length === allCheckboxes.length;
            
            const deleteButton = document.getElementById('deleteButton');
            if (selectedUsers.length > 0) {
                deleteButton.style.display = 'flex';
            } else {
                deleteButton.style.display = 'none';
            }
        }

        function changePage(page) {
            // Show loading for pagination
            const overlay = document.getElementById('pageLoadingOverlay');
            const loadingText = overlay.querySelector('p');
            loadingText.textContent = 'Memuat halaman...';
            overlay.style.display = 'flex';
            
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
                title.textContent = 'Hapus User';
                subtitle.textContent = `Apakah Anda yakin ingin menghapus ${selectedUsers.length} user yang dipilih?`;
            } else {
                title.textContent = 'Hapus User';
                subtitle.textContent = 'Apakah Anda yakin ingin menghapus user yang dipilih?';
            }
            
            modal.classList.add('show');
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.remove('show');
        }

        function confirmDelete() {
            const ids = selectedUsers;
            
            if (!ids || ids.length === 0) {
                alert('Tidak ada user yang dipilih');
                closeDeleteModal();
                return;
            }
            
            // Show loading for delete operation
            const overlay = document.getElementById('pageLoadingOverlay');
            const loadingText = overlay.querySelector('p');
            loadingText.textContent = 'Menghapus user...';
            overlay.style.display = 'flex';
            
            // Get CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (!csrfToken) {
                console.error('CSRF token not found');
                hideLoading();
                alert('CSRF token tidak ditemukan. Silakan refresh halaman.');
                return;
            }
            
            fetch('{{ route("dashboard.user.bulk-destroy") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ 
                    user_ids: ids,
                    _token: csrfToken.getAttribute('content')
                })
            })
            .then(response => {
                console.log('Response status:', response.status);
                console.log('Response headers:', response.headers);
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                // Check if response is JSON
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    throw new Error('Response is not JSON');
                }
                
                return response.json();
            })
            .then(data => {
                console.log('Response data:', data);
                hideLoading();
                
                if (data.success) {
                    // Show success message
                    alert(data.message);
                    // Reload page to reflect changes
                    location.reload();
                } else {
                    alert('Gagal menghapus user: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
                hideLoading();
                
                if (error.message.includes('302')) {
                    alert('Sesi telah berakhir. Silakan login kembali.');
                    window.location.href = '/login';
                } else {
                    alert('Terjadi kesalahan saat menghapus user: ' + error.message);
                }
            })
            .finally(() => {
                closeDeleteModal();
            });
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
            formData.append('period', 'all');
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
            
            fetch('{{ route("dashboard.user.export.excel") }}', {
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
                a.download = `user_export_all_${new Date().toISOString().slice(0, 10)}.xlsx`;
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
            formData.append('period', 'all');
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
            
            fetch('{{ route("dashboard.user.export.csv") }}', {
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
                a.download = `user_export_all_${new Date().toISOString().slice(0, 10)}.csv`;
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
            formData.append('period', 'all');
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
            
            fetch('{{ route("dashboard.user.export.pdf") }}', {
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
                a.download = `user_export_all_${new Date().toISOString().slice(0, 10)}.pdf`;
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