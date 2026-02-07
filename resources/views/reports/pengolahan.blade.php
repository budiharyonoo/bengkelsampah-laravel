@extends('dashboard')
@section('content')
@include('partials.dashboard-header-bar', ['title' => 'Laporan Pengolahan'])
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Pengolahan - Admin Panel</title>
    <link href="https://fonts.googleapis.com/css2?family=Urbanist:wght@400;500;600;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
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
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
        }

        .filter-group {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
        }

        .filter-select {
            padding: 8px 12px;
            border: 1px solid #E5E6E6;
            border-radius: 8px;
            font-size: 14px;
            background: #fff;
            color: #374151;
            min-width: 150px;
        }

        .btn-group {
            display: flex;
            gap: 0.5rem;
        }

        .btn {
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s;
        }

        .btn-success {
            background: #22c55e;
            color: white;
        }

        .btn-success:hover {
            background: #16a34a;
        }

        .btn-danger {
            background: #ef4444;
            color: white;
        }

        .btn-danger:hover {
            background: #dc2626;
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

        .summary-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .summary-card {
            background: #fff;
            border: 1px solid #E5E6E6;
            border-radius: 12px;
            padding: 1.5rem;
        }

        .summary-card.total {
            border-left: 4px solid #387478;
        }

        .summary-card.qty {
            border-left: 4px solid #f59e0b;
        }

        .summary-card.output {
            border-left: 4px solid #22c55e;
        }

        .summary-card .label {
            font-size: 14px;
            color: #6b7280;
            margin-bottom: 8px;
        }

        .summary-card .value {
            font-size: 28px;
            font-weight: 700;
            color: #1e293b;
        }

        .method-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 2rem;
        }

        .method-table th,
        .method-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #E5E6E6;
        }

        .method-table th {
            font-weight: 600;
            color: #6b7280;
            font-size: 13px;
            background: #f9fafb;
        }

        .method-table td {
            font-size: 14px;
            color: #374151;
        }

        .method-table tr:hover {
            background: #fafafa;
        }

        .text-right {
            text-align: right !important;
        }

        .period-info {
            font-size: 14px;
            color: #6b7280;
            margin-bottom: 1rem;
        }

        .section-title {
            font-size: 16px;
            font-weight: 600;
            margin: 2rem 0 1rem;
            color: #374151;
        }

        .summary-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 2rem;
        }

        .summary-table th,
        .summary-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #E5E6E6;
        }

        .summary-table th {
            font-weight: 600;
            color: #6b7280;
            font-size: 13px;
            background: #f9fafb;
        }

        .summary-table td {
            font-size: 14px;
            color: #374151;
        }

        .summary-table tr:hover {
            background: #fafafa;
        }

        .detail-table {
            width: 100%;
            border-collapse: collapse;
        }

        .detail-table th,
        .detail-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #E5E6E6;
        }

        .detail-table th {
            font-weight: 600;
            color: #6b7280;
            font-size: 13px;
            background: #f9fafb;
        }

        .detail-table td {
            font-size: 14px;
            color: #374151;
        }

        .text-green {
            color: #22c55e;
        }

        .text-red {
            color: #ef4444;
        }

        .method-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .method-badge.kompos {
            background: #dcfce7;
            color: #166534;
        }

        .method-badge.daur_ulang {
            background: #dbeafe;
            color: #1e40af;
        }

        .method-badge.rdf {
            background: #fef3c7;
            color: #92400e;
        }

        .method-badge.lainnya {
            background: #f3f4f6;
            color: #4b5563;
        }

        .empty-state {
            text-align: center;
            padding: 3rem;
            color: #9ca3af;
        }

        .empty-state i {
            font-size: 48px;
            margin-bottom: 1rem;
        }

        .efficiency-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
        }

        .efficiency-good {
            background: #dcfce7;
            color: #166534;
        }

        .efficiency-medium {
            background: #fef3c7;
            color: #92400e;
        }

        .efficiency-low {
            background: #fee2e2;
            color: #991b1b;
        }

        /* Filter Dropdown Styles */
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
    </style>
</head>
<body>
    <div class="container">
        <div class="period-info">
            Periode: {{ $startDate->format('d M Y') }} - {{ $endDate->format('d M Y') }}
        </div>

        <div class="controls">
            <form method="GET" class="filter-group">
                <select name="periode" class="filter-select" onchange="this.form.submit()">
                    <option value="harian" {{ $periode == 'harian' ? 'selected' : '' }}>Harian</option>
                    <option value="mingguan" {{ $periode == 'mingguan' ? 'selected' : '' }}>Mingguan</option>
                    <option value="bulanan" {{ $periode == 'bulanan' ? 'selected' : '' }}>Bulanan</option>
                    <option value="enam_bulanan" {{ $periode == 'enam_bulanan' ? 'selected' : '' }}>6 Bulan</option>
                    <option value="tahunan" {{ $periode == 'tahunan' ? 'selected' : '' }}>Tahunan</option>
                </select>
<!-- Filter Bank Sampah (Searchable) -->
                <div class="filter-dropdown">
                    <button type="button" class="filter-button" onclick="toggleDropdown('bankDropdown')">
                        {{ $selectedBankSampah ? $bankSampahList->where('id', $selectedBankSampah)->first()->nama_bank_sampah : 'Semua Bank Sampah' }}
                        <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
                            <path d="M3 4.5L6 7.5L9 4.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                    <div id="bankDropdown" class="filter-dropdown-content">
                        <!-- Search input -->
                        <div class="filter-search-wrapper">
                            <input type="text" id="bankSearchInput" class="filter-search-input"
                                placeholder="Cari bank sampah..." onkeyup="filterBankOptions()"
                                onclick="event.stopPropagation()">
                        </div>
                        <!-- Options -->
                        <div class="filter-option" onclick="selectFilter('bank_sampah_id', '', 'Semua Bank Sampah')">
                            Semua Bank Sampah
                        </div>
                        @foreach($bankSampahList as $bs)
                            <div class="filter-option" data-bank-name="{{ strtolower($bs->nama_bank_sampah) }}"
                                onclick="selectFilter('bank_sampah_id', '{{ $bs->id }}', '{{ $bs->nama_bank_sampah }}')">
                                {{ $bs->nama_bank_sampah }}
                            </div>
                        @endforeach
                    </div>
                </div>
                <input type="hidden" name="bank_sampah_id" value="{{ $selectedBankSampah ?? '' }}" id="bank_sampah_id_input">
            </form>

            <div class="export-dropdown">
                <button class="export-button" id="exportButton">
                    <span>Export</span>
                    <img src="{{ asset('icon/ic_trailing.svg') }}" alt="Export" width="16" height="16">
                </button>
                <div class="export-dropdown-content" id="exportDropdown">
                    <div class="export-option" onclick="exportData('csv')">
                        <img src="{{ asset('icon/ic_laporan.svg') }}" alt="CSV">
                        <span>Export CSV</span>
                    </div>
                    <div class="export-option" onclick="exportData('excel')">
                        <img src="{{ asset('icon/ic_laporan.svg') }}" alt="Excel">
                        <span>Export Excel</span>
                    </div>
                    <div class="export-option" onclick="exportData('pdf')">
                        <img src="{{ asset('icon/ic_laporan.svg') }}" alt="PDF">
                        <span>Export PDF</span>
                    </div>
                </div>
            </div>

            <!-- Hidden forms for export -->
            <form method="POST" action="{{ route('reports.pengolahan.export.excel') }}" id="exportExcelForm" style="display:none;">
                @csrf
                <input type="hidden" name="periode" value="{{ $periode }}">
                <input type="hidden" name="bank_sampah_id" value="{{ $selectedBankSampah }}">
            </form>
            <form method="POST" action="{{ route('reports.pengolahan.export.pdf') }}" id="exportPdfForm" style="display:none;">
                @csrf
                <input type="hidden" name="periode" value="{{ $periode }}">
                <input type="hidden" name="bank_sampah_id" value="{{ $selectedBankSampah }}">
            </form>
            <form method="POST" action="{{ route('reports.pengolahan.export.csv') }}" id="exportCsvForm" style="display:none;">
                @csrf
                <input type="hidden" name="periode" value="{{ $periode }}">
                <input type="hidden" name="bank_sampah_id" value="{{ $selectedBankSampah }}">
            </form>
        </div>

        <div class="summary-cards">
            <div class="summary-card total">
                <div class="label">Total Transaksi Pengolahan</div>
                <div class="value">{{ $processingMetrics['count'] ?? 0 }}</div>
            </div>
            <div class="summary-card qty">
                <div class="label">Total Input (Kg)</div>
                <div class="value">{{ number_format($processingMetrics['total_quantity'] ?? 0, 2, ',', '.') }}</div>
            </div>
        </div>

        <h3 class="section-title">Ringkasan Pengolahan per Offtaker</h3>

        @if(count($processingByOfftaker) > 0)
            <table class="summary-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Offtaker</th>
                        <th class="text-right">Jumlah Transaksi</th>
                        <th class="text-right">Total Qty (Kg)</th>
                    </tr>
                </thead>
                <tbody>
                    @php $totalQty = 0; @endphp
                    @foreach($processingByOfftaker as $index => $item)
                        @php
                            $totalQty += $item['total_quantity'] ?? 0;
                        @endphp
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item['name'] ?? '-' }}</td>
                            <td class="text-right">{{ $item['count'] ?? 0 }}</td>
                            <td class="text-right">{{ number_format($item['total_quantity'] ?? 0, 2, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr style="background: #f3f4f6; font-weight: 600;">
                        <td colspan="3">Total</td>
                        <td class="text-right">{{ number_format($totalQty, 2, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        @else
            <div class="empty-state">
                <i class="fa-solid fa-recycle"></i>
                <p>Tidak ada data pengolahan untuk periode ini</p>
                <p style="font-size: 13px; color: #9ca3af; margin-top: 0.5rem;">Coba ubah periode atau filter bank sampah untuk melihat data lain</p>
            </div>
        @endif

        <h3 class="section-title">Transaksi Terbaru</h3>

        @if(count($recentTransactions) > 0)
            <table class="detail-table">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Tanggal</th>
                        <th>Offtaker</th>
                        <th>Bank Sampah</th>
                        <th>Metode Pengolahan</th>
                        <th class="text-right">Total Qty (Kg)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentTransactions as $trx)
                        <tr>
                            <td>{{ $trx->kode_transaksi }}</td>
                            <td>{{ $trx->tanggal_transaksi ? \Carbon\Carbon::parse($trx->tanggal_transaksi)->format('d M Y') : '-' }}</td>
                            <td>{{ $trx->offtaker->nama ?? '-' }}</td>
                            <td>{{ $trx->bankSampah->nama_bank_sampah ?? '-' }}</td>
                            <td>{{ ucfirst($trx->metode_pengolahan ?? '-') }}</td>
                            <td class="text-right">{{ number_format($trx->total_quantity, 2, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="empty-state">
                <i class="fa-solid fa-receipt"></i>
                <p>Belum ada transaksi</p>
            </div>
        @endif
    </div>

    <script>
        function toggleDropdown(dropdownId) {
            const dropdown = document.getElementById(dropdownId);
            dropdown.classList.toggle('show');

            // Clear search input and show all options when opening
            if (dropdown.classList.contains('show') && dropdownId === 'bankDropdown') {
                const searchInput = document.getElementById('bankSearchInput');
                if (searchInput) {
                    searchInput.value = '';
                    searchInput.focus();
                    filterBankOptions();
                }
            }
        }

        function selectFilter(field, value, displayText) {
            // Update hidden input
            const hiddenInput = document.querySelector(`input[name="${field}"]`);
            if (hiddenInput) {
                hiddenInput.value = value;
            }

            // Update button text
            const button = event.target.closest('.filter-dropdown').querySelector('.filter-button');
            if (button) {
                button.innerHTML = displayText +
                    '<svg width="12" height="12" viewBox="0 0 12 12" fill="none"><path d="M3 4.5L6 7.5L9 4.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>';
            }

            // Close dropdown
            const dropdownId = event.target.closest('.filter-dropdown-content').id;
            document.getElementById(dropdownId).classList.remove('show');

            // Submit form
            const form = event.target.closest('form');
            if (form) {
                form.submit();
            }
        }

        function filterBankOptions() {
            const searchInput = document.getElementById('bankSearchInput');
            const searchTerm = searchInput.value.toLowerCase().trim();
            const dropdown = document.getElementById('bankDropdown');
            const options = dropdown.querySelectorAll('.filter-option');

            options.forEach(option => {
                // Skip "Semua Bank Sampah" option - always show it
                if (option.textContent.trim() === 'Semua Bank Sampah') {
                    option.classList.remove('hidden');
                    return;
                }

                // Get bank name from data attribute
                const bankName = option.getAttribute('data-bank-name') || option.textContent.toLowerCase();

                // Show/hide based on search match
                if (bankName.includes(searchTerm)) {
                    option.classList.remove('hidden');
                } else {
                    option.classList.add('hidden');
                }
            });
        }

        // Export dropdown functionality
        const exportButton = document.getElementById('exportButton');
        const exportDropdown = document.getElementById('exportDropdown');

        if (exportButton) {
            exportButton.addEventListener('click', function(e) {
                e.stopPropagation();
                exportDropdown.classList.toggle('show');
            });
        }

        function exportData(format) {
            exportDropdown.classList.remove('show');

            if (format === 'excel') {
                document.getElementById('exportExcelForm').submit();
            } else if (format === 'pdf') {
                document.getElementById('exportPdfForm').submit();
            } else if (format === 'csv') {
                document.getElementById('exportCsvForm').submit();
            }
        }

        // Close dropdowns when clicking outside
        window.onclick = function(event) {
            if (!event.target.matches('.filter-button') && !event.target.closest('.filter-button')) {
                const dropdowns = document.getElementsByClassName('filter-dropdown-content');
                for (let dropdown of dropdowns) {
                    if (dropdown.classList.contains('show')) {
                        dropdown.classList.remove('show');
                    }
                }
            }
            
            // Close export dropdown when clicking outside
            if (!event.target.matches('.export-button') && !event.target.closest('.export-button')) {
                if (exportDropdown && exportDropdown.classList.contains('show')) {
                    exportDropdown.classList.remove('show');
                }
            }
        }
    </script>
</body>
</html>
@endsection
