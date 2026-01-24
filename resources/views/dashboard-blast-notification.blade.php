@extends('dashboard')
@section('content')
<!-- ================= HEADER & FILTER ================= -->
@include('partials.dashboard-header-bar', ['title' => 'Blast Notification'])
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blast Notification - Admin Panel</title>
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

        .status-badge {
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
            display: inline-block;
        }

        .status-badge.success {
            background: #E3F4F1;
            color: #39746E;
        }

        .status-badge.failed {
            background: #FEE2E2;
            color: #DC2626;
        }

        .topic-badge {
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
            display: inline-block;
            background: #F3F4F6;
            color: #374151;
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
                    <input type="text" class="search-input" placeholder="Cari notifikasi disini" id="searchInput">
                    <img src="{{ asset('icon/ic_search.svg') }}" alt="Search" class="search-icon">
                </div>
                <div class="filter-dropdown">
                    <button class="filter-button" id="filterButton">
                        <span id="filterButtonText">Semua Status</span>
                        <img src="{{ asset('icon/ic_trailing.svg') }}" alt="Filter" width="16" height="16">
                    </button>
                    <div class="filter-dropdown-content" id="filterDropdown">
                        <div class="filter-option active" data-status="">Semua Status</div>
                        <div class="filter-option" data-status="success">Success</div>
                        <div class="filter-option" data-status="failed">Failed</div>
                    </div>
                </div>
            </div>
            <div class="action-buttons">
                <button class="add-button" id="addButton">
                    <span>Kirim Notifikasi</span>
                    <img src="{{ asset('icon/ic_add.svg') }}" alt="Add" width="16" height="16">
                </button>
            </div>
        </div>

        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th style="min-width: 60px;">No</th>
                        <th style="min-width: 200px;">Judul Notifikasi</th>
                        <th style="min-width: 300px;">Isi Pesan</th>
                        <th style="min-width: 150px;">Topic Tujuan</th>
                        <th style="min-width: 200px;">Dikirim Oleh</th>
                        <th style="min-width: 150px;">Tanggal Kirim</th>
                        <th style="min-width: 100px;">Status</th>
                    </tr>
                </thead>
                <tbody id="notificationsTableBody">
                    @foreach ($notifications as $index => $notification)
                        <tr>
                            <td>{{ $notifications->firstItem() + $index }}</td>
                            <td style="font-weight: 600; color: #39746E;">{{ $notification->title }}</td>
                            <td style="color: #6B7271;">{{ Str::limit($notification->body, 60) }}</td>
                            <td>
                                <span class="topic-badge">{{ $notification->topic }}</span>
                            </td>
                            <td style="color: #6B7271;">
                                {{ $notification->sent_by_name }}
                                <span style="font-size: 12px; color: #9CA3AF;">({{ ucfirst($notification->sent_by_role) }})</span>
                            </td>
                            <td style="color: #6B7271;">{{ $notification->created_at->format('d M Y H:i') }}</td>
                            <td>
                                <span class="status-badge {{ $notification->status }}">
                                    {{ ucfirst($notification->status) }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="no-results" id="noResults" style="display: none;">
                <h3>Belum ada riwayat blast notification</h3>
                <p>Mulai dengan mengirim blast notification pertama Anda</p>
            </div>
        </div>

        <div class="pagination-container" id="paginationContainer">
            <div class="pagination-info" id="paginationInfo"></div>
            <div class="pagination" id="pagination"></div>
        </div>
    </div>

    <script>
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
                current_page: {{ $notifications->currentPage() }},
                total: {{ $notifications->total() }},
                per_page: {{ $notifications->perPage() }},
                first_item: {{ $notifications->firstItem() ?? 0 }},
                last_item: {{ $notifications->lastItem() ?? 0 }}
            };

            const tbody = document.getElementById('notificationsTableBody');
            const noResults = document.getElementById('noResults');
            const paginationContainer = document.getElementById('paginationContainer');

            if ({{ $notifications->total() }} === 0) {
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

            const addButton = document.getElementById('addButton');
            if (addButton) {
                addButton.addEventListener('click', function() {
                    window.location.href = '{{ route("dashboard.blast-notification.create") }}';
                });
            }

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

            document.addEventListener('click', function(e) {
                if (!e.target.closest('.filter-dropdown')) {
                    document.getElementById('filterDropdown').classList.remove('show');
                }
            });
        }

        function updateFilterButtonText(status) {
            const buttonText = document.getElementById('filterButtonText');
            if (status === '') {
                buttonText.textContent = 'Semua Status';
            } else if (status === 'success') {
                buttonText.textContent = 'Success';
            } else if (status === 'failed') {
                buttonText.textContent = 'Failed';
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
    </script>
</body>
</html>
@endsection
