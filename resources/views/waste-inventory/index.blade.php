@extends('dashboard')
@section('content')
@include('partials.dashboard-header-bar', ['title' => 'Inventori Sampah'])
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventori Sampah - Admin Panel</title>
    <link href="https://fonts.googleapis.com/css2?family=Urbanist:wght@400;500;600;700;900&display=swap" rel="stylesheet">
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

        .page-container {
            padding: 0 2rem 2rem 2rem;
        }

        .metrics-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
            margin-bottom: 24px;
        }

        .metric-card {
            background: #fff;
            border: 1px solid #E5E6E6;
            border-radius: 12px;
            padding: 20px;
        }

        .metric-label {
            font-size: 13px;
            color: #6B7271;
            margin-bottom: 8px;
        }

        .metric-value {
            font-size: 24px;
            font-weight: 700;
            color: #39746E;
        }

        .metric-value.secondary {
            color: #1E40AF;
        }

        .metric-value.tertiary {
            color: #9D174D;
        }

        .metric-subtext {
            font-size: 12px;
            color: #6B7271;
            margin-top: 4px;
        }

        .container {
            background: #fff;
            border: 1px solid #E5E6E6;
            border-radius: 16px;
            padding: 24px;
            margin-bottom: 24px;
        }

        .container-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
        }

        .container-title {
            font-size: 18px;
            font-weight: 700;
            color: #242E2C;
        }

        .filter-select {
            padding: 8px 12px;
            border: 1px solid #E5E6E6;
            border-radius: 8px;
            font-size: 14px;
            color: #1e293b;
            background: #fff;
        }

        .bank-sampah-section {
            margin-bottom: 24px;
        }

        .bank-sampah-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 16px;
            background: #F9FAFB;
            border-radius: 8px;
            margin-bottom: 12px;
        }

        .bank-sampah-name {
            font-size: 15px;
            font-weight: 600;
            color: #242E2C;
        }

        .bank-sampah-code {
            font-size: 12px;
            color: #6B7271;
        }

        .btn-detail {
            padding: 6px 12px;
            background: #EBF5FF;
            border: none;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            color: #1E40AF;
            cursor: pointer;
            text-decoration: none;
        }

        .btn-detail:hover {
            background: #DBEAFE;
        }

        .btn-detail-primary {
            padding: 6px 12px;
            background: #39746E;
            border: none;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            color: #fff;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }

        .btn-detail-primary:hover {
            background: #2d5f5a;
        }

        .btn-detail-secondary {
            padding: 6px 12px;
            background: #F0FDF4;
            border: 1px solid #86EFAC;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            color: #15803D;
            cursor: pointer;
            text-decoration: none;
        }

        .btn-detail-secondary:hover {
            background: #DCFCE7;
        }

        .table-container {
            overflow-x: auto;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .data-table th,
        .data-table td {
            padding: 10px 12px;
            text-align: left;
            border-bottom: 1px solid #E5E6E6;
        }

        .data-table th {
            font-weight: 600;
            font-size: 12px;
            color: #6B7271;
            text-transform: uppercase;
        }

        .data-table td {
            font-size: 13px;
            color: #1e293b;
        }

        .data-table tbody tr:hover {
            background: #F9FAFB;
        }

        .low-stock {
            color: #DC2626;
        }

        .badge-warning {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: 600;
            background: #FEF3C7;
            color: #92400E;
            margin-left: 4px;
        }

        .empty-state {
            text-align: center;
            padding: 40px;
            color: #6B7271;
        }

        /* Filter Dropdown Styles */
        .filter-dropdown {
            position: relative;
            display: inline-block;
        }

        .filter-button {
            font-size: 14px;
            font-weight: 400;
            color: #39746E;
            background: #EFF0F0;
            border: 1px solid #EFF0F0;
            border-radius: 8px;
            padding: 8px 32px 8px 12px;
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            transition: background 0.2s;
            min-width: 200px;
        }

        .filter-button:hover,
        .filter-button:focus {
            background: #fff;
            border: 1px solid #39746E;
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
            font-size: 14px;
            color: #39746E;
            padding: 10px 18px;
            cursor: pointer;
            border-radius: 8px;
            transition: background 0.2s;
        }

        .filter-option:hover {
            background: #E3F4F1;
        }

        /* Search input wrapper */
        .filter-search-wrapper {
            padding: 8px 12px;
            border-bottom: 1px solid #E5E6E6;
            background: #FAFAFA;
        }

        /* Search input field */
        .filter-search-input {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #E5E6E6;
            border-radius: 6px;
            font-size: 13px;
            color: #39746E;
            background: #fff;
            outline: none;
            transition: border-color 0.2s;
        }

        .filter-search-input:focus {
            border-color: #39746E;
        }

        .filter-search-input::placeholder {
            color: #A8A8A8;
        }

        /* Hide filtered options */
        .filter-option.hidden {
            display: none;
        }

        .alerts-container {
            margin-bottom: 24px;
        }

        .alert-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 16px;
            background: #FEF3C7;
            border: 1px solid #FCD34D;
            border-radius: 8px;
            margin-bottom: 8px;
        }

        .alert-text {
            font-size: 13px;
            color: #92400E;
        }

        .alert-value {
            font-size: 13px;
            font-weight: 600;
            color: #92400E;
        }

        .top-waste-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 12px;
        }

        .top-waste-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px;
            background: #F9FAFB;
            border-radius: 8px;
        }

        .top-waste-rank {
            width: 28px;
            height: 28px;
            background: #39746E;
            color: #fff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 700;
        }

        .top-waste-info {
            flex: 1;
        }

        .top-waste-name {
            font-size: 13px;
            font-weight: 600;
            color: #242E2C;
        }

        .top-waste-qty {
            font-size: 12px;
            color: #6B7271;
        }

        @media (max-width: 768px) {
            .metrics-grid {
                grid-template-columns: 1fr;
            }
        }

        /* Modal Styles */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }

        .modal-overlay.show {
            display: flex;
        }

        .modal-content {
            background: #fff;
            border-radius: 16px;
            width: 90%;
            max-width: 700px;
            max-height: 80vh;
            overflow-y: auto;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.15);
        }

        .modal-header {
            padding: 20px 24px;
            border-bottom: 1px solid #E5E6E6;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-title {
            font-size: 18px;
            font-weight: 700;
            color: #242E2C;
        }

        .modal-close {
            background: none;
            border: none;
            font-size: 24px;
            color: #6B7271;
            cursor: pointer;
            padding: 0;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
        }

        .modal-close:hover {
            background: #F9FAFB;
        }

        .modal-body {
            padding: 24px;
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
            font-size: 12px;
            color: #6B7271;
            text-transform: uppercase;
            background: #F9FAFB;
        }

        .detail-table td {
            font-size: 13px;
            color: #1e293b;
        }

        .detail-table tbody tr:hover {
            background: #F9FAFB;
        }
    </style>
    <script>
        function toggleDropdown(dropdownId) {
            const dropdown = document.getElementById(dropdownId);
            dropdown.classList.toggle('show');

            // Clear search input and show all options when opening
            if (dropdown.classList.contains('show') && dropdownId === 'bankDropdown') {
                const searchInput = document.getElementById('bankSearchInput');
                if (searchInput) {
                    searchInput.value = '';
                    searchInput.focus(); // Auto-focus for better UX
                    filterBankOptions(); // Reset filter to show all options
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
                    '<svg width="12" height="12" viewBox="0 0 12 12" fill="none" style="margin-left: auto;"><path d="M3 4.5L6 7.5L9 4.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>';
            }

            // Close dropdown
            const dropdownId = event.target.closest('.filter-dropdown-content').id;
            document.getElementById(dropdownId).classList.remove('show');

            // Submit form
            const form = document.getElementById('inventoryFilterForm');
            if (form) {
                form.submit();
            }
        }

        /**
         * Filter bank sampah options based on search input
         */
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

                // Get bank name from data attribute (case-insensitive)
                const bankName = option.getAttribute('data-bank-name') || option.textContent.toLowerCase();

                // Show/hide based on search match
                if (bankName.includes(searchTerm)) {
                    option.classList.remove('hidden');
                } else {
                    option.classList.add('hidden');
                }
            });
        }

        // Close dropdowns when clicking outside
        window.onclick = function(event) {
            if (!event.target.matches('.filter-button')) {
                const dropdowns = document.getElementsByClassName('filter-dropdown-content');
                for (let dropdown of dropdowns) {
                    if (dropdown.classList.contains('show')) {
                        dropdown.classList.remove('show');
                    }
                }
            }

            // Close modal when clicking on overlay
            if (event.target.classList.contains('modal-overlay')) {
                closeModal();
            }
        }

        /**
         * Show category details from button data attribute
         */
        function showCategoryDetailsFromButton(button) {
            const categoryData = JSON.parse(button.getAttribute('data-category'));
            showCategoryDetails(categoryData);
        }

        /**
         * Show category details in modal
         */
        function showCategoryDetails(categoryGroup) {
            const modal = document.getElementById('categoryModal');
            const modalTitle = document.getElementById('modalTitle');
            const modalBody = document.getElementById('modalBody');

            // Set modal title
            modalTitle.textContent = 'Detail ' + (categoryGroup.category.nama || 'Lainnya');

            // Convert items to array if it's an object
            const items = Array.isArray(categoryGroup.items)
                ? categoryGroup.items
                : Object.values(categoryGroup.items);

            // Build table HTML
            let tableHTML = `
                <table class="detail-table">
                    <thead>
                        <tr>
                            <th>Jenis Sampah</th>
                            <th>Stok</th>
                            <th>Satuan</th>
                        </tr>
                    </thead>
                    <tbody>
            `;

            items.forEach(item => {
                const isLowStock = item.quantity < 10;
                tableHTML += `
                    <tr>
                        <td>
                            ${item.sampah.nama}
                            ${isLowStock ? '<span class="badge-warning">Stok Rendah</span>' : ''}
                        </td>
                        <td class="${isLowStock ? 'low-stock' : ''}">
                            ${parseFloat(item.quantity).toLocaleString('id-ID', {minimumFractionDigits: 2, maximumFractionDigits: 2})}
                        </td>
                        <td>${item.unit}</td>
                    </tr>
                `;
            });

            tableHTML += `
                    </tbody>
                </table>
            `;

            modalBody.innerHTML = tableHTML;

            // Show modal
            modal.classList.add('show');
        }

        /**
         * Close modal
         */
        function closeModal() {
            const modal = document.getElementById('categoryModal');
            modal.classList.remove('show');
        }
    </script>
</head>
<body>
    <div class="page-container">
        <!-- Metrics -->
        <div class="metrics-grid">
            <div class="metric-card">
                <div class="metric-label">Total Tersimpan</div>
                <div class="metric-value">{{ number_format($summary['stored_kg'], 2) }} kg</div>
                <div class="metric-subtext">Nilai: Rp {{ number_format($summary['stored_value'], 0, ',', '.') }}</div>
            </div>
            <div class="metric-card">
                <div class="metric-label">Total Terjual</div>
                <div class="metric-value secondary">{{ number_format($summary['sold_kg'], 2) }} kg</div>
                <div class="metric-subtext">Nilai: Rp {{ number_format($summary['sold_value'], 0, ',', '.') }}</div>
            </div>
            <div class="metric-card">
                <div class="metric-label">Total Diolah</div>
                <div class="metric-value tertiary">{{ number_format($summary['processed_kg'], 2) }} kg</div>
                <div class="metric-subtext">Nilai: Rp {{ number_format($summary['processed_value'], 0, ',', '.') }}</div>
            </div>
        </div>

        <!-- Low Stock Alerts -->
        @if($lowStockAlerts->count() > 0)
            <div class="container alerts-container">
                <div class="container-header">
                    <h3 class="container-title">Peringatan Stok Rendah</h3>
                </div>
                @foreach($lowStockAlerts->take(5) as $alert)
                    <div class="alert-item">
                        <span class="alert-text">
                            {{ $alert->sampah->nama }} di {{ $alert->bankSampah->nama_bank_sampah }}
                        </span>
                        <span class="alert-value">{{ number_format($alert->quantity, 2) }} {{ $alert->unit }}</span>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- Top Waste Types -->
        @if($topWasteTypes->count() > 0)
            <div class="container">
                <div class="container-header">
                    <h3 class="container-title">Top 10 Jenis Sampah</h3>
                </div>
                <div class="top-waste-list">
                    @foreach($topWasteTypes as $index => $item)
                        <div class="top-waste-item">
                            <div class="top-waste-rank">{{ $index + 1 }}</div>
                            <div class="top-waste-info">
                                <div class="top-waste-name">{{ $item->sampah->nama ?? 'Unknown' }}</div>
                                <div class="top-waste-qty">{{ number_format($item->quantity, 2) }} {{ $item->unit }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Filter -->
        <div class="container">
            <div class="container-header">
                <h3 class="container-title">Inventori per Bank Sampah</h3>
                <form method="GET" action="{{ route('waste-inventory.index') }}" id="inventoryFilterForm">
                    <div class="filter-dropdown">
                        <button type="button" class="filter-button" onclick="toggleDropdown('bankDropdown')">
                            {{ $bankSampahId ? $bankSampahList->where('id', $bankSampahId)->first()->nama_bank_sampah : 'Semua Bank Sampah' }}
                            <svg width="12" height="12" viewBox="0 0 12 12" fill="none" style="margin-left: auto;">
                                <path d="M3 4.5L6 7.5L9 4.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </button>
                        <div id="bankDropdown" class="filter-dropdown-content">
                            <!-- Search input -->
                            <div class="filter-search-wrapper">
                                <input
                                    type="text"
                                    id="bankSearchInput"
                                    class="filter-search-input"
                                    placeholder="Cari bank sampah..."
                                    onkeyup="filterBankOptions()"
                                    onclick="event.stopPropagation()"
                                >
                            </div>

                            <!-- Options -->
                            <div class="filter-option" onclick="selectFilter('bank_sampah_id', '', 'Semua Bank Sampah')">
                                Semua Bank Sampah
                            </div>
                            @foreach($bankSampahList as $bs)
                                <div class="filter-option"
                                    data-bank-name="{{ strtolower($bs->nama_bank_sampah) }}"
                                    onclick="selectFilter('bank_sampah_id', '{{ $bs->id }}', '{{ $bs->nama_bank_sampah }}')">
                                    {{ $bs->nama_bank_sampah }}
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <!-- Hidden input for form submission -->
                    <input type="hidden" name="bank_sampah_id" value="{{ $bankSampahId ?? '' }}">
                </form>
            </div>

            @if($inventoryGrouped->count() > 0)
                @foreach($inventoryGrouped as $bsId => $items)
                    @php
                        $bs = $items->first()?->bankSampah ?? \App\Models\BankSampah::find($bsId);
                    @endphp
                    <div class="bank-sampah-section">
                        <div class="bank-sampah-header">
                            <div>
                                <span class="bank-sampah-name">{{ $bs->nama_bank_sampah ?? 'Bank Sampah' }}</span>
                                <span class="bank-sampah-code">{{ $bs->kode_bank_sampah ?? '' }}</span>
                            </div>
                            <a href="{{ route('waste-inventory.show', $bsId) }}" class="btn-detail-primary">📊 Detail Lengkap</a>
                        </div>
                        <div class="table-container">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Kategori</th>
                                        <th>Jumlah Jenis</th>
                                        <th>Total Stok</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($items as $categoryGroup)
                                        @php
                                            $categoryData = [
                                                'category' => $categoryGroup->category,
                                                'items' => $categoryGroup->items->values()->toArray(),
                                                'total_quantity' => $categoryGroup->total_quantity,
                                                'item_count' => $categoryGroup->item_count
                                            ];
                                        @endphp
                                        <tr>
                                            <td>{{ $categoryGroup->category->nama ?? 'Lainnya' }}</td>
                                            <td>{{ $categoryGroup->item_count }} jenis</td>
                                            <td>{{ number_format($categoryGroup->total_quantity, 2) }} kg</td>
                                            <td>
                                                <button
                                                    class="btn-detail-secondary"
                                                    data-category='@json($categoryData)'
                                                    onclick='showCategoryDetailsFromButton(this)'>
                                                    👁️ Lihat Item
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="empty-state">
                    <p>Belum ada data inventori.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Category Details Modal -->
    <div id="categoryModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modalTitle" class="modal-title">Detail Kategori</h3>
                <button class="modal-close" onclick="closeModal()">&times;</button>
            </div>
            <div id="modalBody" class="modal-body">
                <!-- Content will be populated by JavaScript -->
            </div>
        </div>
    </div>
</body>
</html>
@endsection
