@extends('dashboard')
@section('content')
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaksi - Admin Panel</title>
    <link href="https://fonts.googleapis.com/css2?family=Urbanist:wght@400;700;900&display=swap" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Urbanist', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; }
        body { background: #fff; color: #1e293b; }
        .header { padding: 1rem 2rem 0rem 2rem; display: flex; justify-content: space-between; align-items: center; }
        .header h1 { font-size: 22px; font-weight: 700; color: #39746E; }
        .user-info { display: flex; align-items: center; gap: 1rem; }
        .notification { position: relative; width: 24px; height: 24px; cursor: pointer; }
        .notification::before { content: "🔔"; font-size: 18px; }
        .user-avatar { width: 40px; height: 40px; border-radius: 50%; background: #0FB7A6; color: white; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 14px; }
        .user-name { font-weight: 700; font-size: 16px; color: #39746E; }
        .container { max-width: 1400px; background: #fff; border: 1px solid #E5E6E6; border-radius: 16px; padding: 16px 16px 16px 24px; }
        .controls { display: flex; justify-content: space-between; align-items: center; gap: 1rem; margin-bottom: 2rem; }
        .search-filter { display: flex; gap: 1rem; flex: 1; flex-wrap: wrap; }
        .search-box { position: relative; flex: 1; max-width: 300px; border-radius: 18px; border: 1px solid #EFF0F0; background: #EFF0F0; display: flex; align-items: center; }
        .search-input { width: 100%; border: none; outline: none; background: transparent; font-size: 15px; font-weight: 400; color: #6B7271; padding: 6px 36px 6px 14px; border-radius: 18px; }
        .search-input::placeholder { color: #6B7271; font-size: 15px; font-weight: 400; opacity: 1; }
        .search-box:focus-within { background: #fff; }
        .search-input:focus { background: #fff; }
        .search-icon { position: absolute; right: 12px; top: 50%; transform: translateY(-50%); width: 20px; height: 20px; pointer-events: none; }
        .filter-dropdown { position: relative; display: inline-block; }
        .filter-button { font-size: 15px; font-weight: 400; color: #39746E; background: #EFF0F0; border: 1px solid #EFF0F0; border-radius: 18px; padding: 6px 32px 6px 14px; display: flex; align-items: center; gap: 8px; cursor: pointer; transition: background 0.2s; }
        .filter-button:hover, .filter-button:focus { background: #fff; border: 1px solid #0FB7A6; }
        .filter-dropdown-content { display: none; position: absolute; left: 0; top: 100%; min-width: 160px; background: #fff; border: 1px solid #E5E6E6; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); margin-top: 6px; z-index: 10; }
        .filter-dropdown-content.show { display: block; }
        .filter-option { font-size: 15px; color: #39746E; padding: 10px 18px; cursor: pointer; border-radius: 8px; transition: background 0.2s; }
        .filter-option:hover { background: #E3F4F1; }
        /* Search input wrapper */
        .filter-search-wrapper { padding: 8px 12px; border-bottom: 1px solid #E5E6E6; background: #FAFAFA; }
        /* Search input field */
        .filter-search-input { width: 100%; padding: 8px 12px; border: 1px solid #E5E6E6; border-radius: 6px; font-size: 14px; color: #39746E; background: #fff; outline: none; transition: border-color 0.2s; }
        .filter-search-input:focus { border-color: #0FB7A6; }
        .filter-search-input::placeholder { color: #A8A8A8; }
        /* Hide filtered options */
        .filter-option.hidden { display: none; }
        .date-input { padding: 8px 12px; border: 1px solid #E5E6E6; border-radius: 18px; font-size: 14px; background: #EFF0F0; border: 1px solid #EFF0F0; color: #39746E; }
        .date-input:focus { background: #fff; border: 1px solid #0FB7A6; outline: none; }
        .action-buttons { display: flex; gap: 8px; align-items: center; }
        .export-dropdown { position: relative; }
        .export-button { padding: 0 1rem; background: #39746E; border: 1px solid #E5E6E6; border-radius: 8px; font-size: 15px; font-weight: 600; color: #DFF0EE; cursor: pointer; display: flex; align-items: center; gap: 8px; height: 37px; transition: all 0.2s; }
        .export-button:hover { background: #2d5a55; }
        .export-button img { filter: brightness(0) invert(1); }
        .export-dropdown-content { display: none; position: absolute; top: 100%; right: 0; background: white; border: 1px solid #d1d5db; border-radius: 8px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); z-index: 10; min-width: 180px; padding: 0.5rem; margin-top: 4px; }
        .export-dropdown-content.show { display: block; }
        .export-option { padding: 8px 12px; cursor: pointer; transition: background-color 0.2s; display: flex; align-items: center; gap: 8px; border-radius: 4px; }
        .export-option:hover { background-color: #f3f4f6; }
        .export-option img { width: 16px; height: 16px; }
        .export-option span { font-size: 14px; color: #1e293b; }
        .table-container { margin-top: 20px; overflow-x: auto; }
        .table { width: 100%; border-collapse: collapse; }
        .table th { background: #F8F9FA; padding: 12px 16px; text-align: left; font-weight: 600; font-size: 14px; color: #39746E; border-bottom: 1px solid #e2e8f0; }
        .table td { padding: 12px 16px; border-bottom: 1px solid #e2e8f0; color: #1e293b; }
        .table tr:hover { background: #F8F9FA; }
        .action-cell { width: 80px; text-align: center; }
        .action-btn { width: 32px; height: 32px; border: none; border-radius: 6px; background: transparent; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s; text-decoration: none; }
        .action-btn img { width: 20px; height: 20px; }
        .action-btn:hover { background-color: #f3f4f6; }
        .pagination-container { display: flex; justify-content: flex-end; align-items: center; margin-top: 8px; }
        .pagination-info { font-size: 16px; font-weight: 500; color: #6B7271; margin-right: 8px; }
        .pagination { display: flex; gap: 8px; }
        .pagination-btn { font-size: 18px; font-weight: 700; color: #39746E; background: none; border: none; padding: 4px 12px; border-radius: 6px; cursor: pointer; margin-left: 0; margin-right: 0; transition: background 0.2s; }
        .pagination-btn:disabled { color: #B0B0B0; cursor: not-allowed; background: none; }
        .pagination-btn:not(:disabled):hover { background: #EFF0F0; }
        .no-results { text-align: center; padding: 3rem; color: #6b7280; }
        .reset-btn { color: #F44336; background: #fff; border: 1px solid #F44336; padding: 6px 16px; font-size: 15px; border-radius: 18px; font-weight: 600; text-decoration: none; display: flex; align-items: center; transition: all 0.2s; }
        .reset-btn:hover { background: #F44336; color: #fff; }
        
        /* Status and Type Badges */
        .badge { display: inline-block; padding: 4px 8px; border-radius: 6px; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
        
        /* Type Badges */
        .badge-jual { background: #E8F5E8; color: #166534; border: 1px solid #BBF7D0; }
        .badge-sedekah { background: #FEF3C7; color: #92400E; border: 1px solid #FDE68A; }
        .badge-tabung { background: #DBEAFE; color: #1E40AF; border: 1px solid #93C5FD; }
        .badge-default { background: #F3F4F6; color: #374151; border: 1px solid #D1D5DB; }
        
        /* Status Badges */
        .badge-dikonfirmasi { background: #FEF3C7; color: #92400E; border: 1px solid #FDE68A; }
        .badge-diproses { background: #DBEAFE; color: #1E40AF; border: 1px solid #93C5FD; }
        .badge-dijemput { background: #E8F5E8; color: #166534; border: 1px solid #BBF7D0; }
        .badge-selesai { background: #E8F5E8; color: #166534; border: 1px solid #BBF7D0; }
        .badge-batal { background: #FEE2E2; color: #991B1B; border: 1px solid #FCA5A5; }
        
        .loading-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); display: none; justify-content: center; align-items: center; z-index: 99999; }
        .loading-content { background: white; padding: 2rem; border-radius: 8px; text-align: center; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15); display: flex; flex-direction: column; align-items: center; justify-content: center; }
        .loading-spinner-large { width: 40px; height: 40px; border: 4px solid #f3f3f3; border-top: 4px solid #00B6A0; border-radius: 50%; animation: spin 1s linear infinite; margin: 0 auto 1rem auto; }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
        @media (max-width: 768px) { .controls { flex-direction: column; align-items: stretch; } .search-filter { flex-direction: column; } .action-buttons { justify-content: center; } .table-container { overflow-x: auto; } .table th, .table td { padding: 8px 12px; font-size: 12px; } }
        .modal { position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(0,0,0,0.35); display: flex; align-items: center; justify-content: center; z-index: 99999; }
        .modal-content {
            background: #fff;
            border-radius: 12px;
            padding: 32px 24px 24px 24px;
            min-width: 0;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.12);
            position: relative;
        }
        .modal-close { position: absolute; top: 18px; right: 18px; background: none; border: none; cursor: pointer; }
        .modal-icon { display: block; margin: 0 auto 12px auto; width: 38px; height: 38px; }
        .modal-title { font-size: 20px; font-weight: 700; color: #39746E; margin-bottom: 6px; text-align: center; }
        .modal-subtitle { font-size: 14px; color: #6B7271; margin-bottom: 18px; text-align: center; }
        .export-form { margin-bottom: 0; }
        .form-group { margin-bottom: 16px; }
        .form-label { font-weight: 600; color: #39746E; margin-bottom: 6px; display: block; }
        .form-control, .form-select, .form-input { width: 100%; padding: 8px 12px; border: 1px solid #E5E6E6; border-radius: 6px; font-size: 14px; margin-top: 2px; }
        .modal-buttons { display: flex; gap: 12px; justify-content: flex-end; margin-top: 18px; }
        .modal-button { font-size: 14px; font-weight: 600; border-radius: 6px; padding: 8px 18px; cursor: pointer; border: none; }
        .cancel-button { background: #6B7271; color: #fff; }
        .cancel-button:hover { background: #5a5f5e; }
        .confirm-button { background: #39746E; color: #fff; }
        .confirm-button:hover { background: #2d5a55; }
    </style>
</head>
<body>
    <!-- Loading Overlay -->
    <div class="loading-overlay" id="pageLoadingOverlay" style="display: none;">
        <div class="loading-content">
            <div class="loading-spinner-large"></div>
            <p style="margin-top: 1rem; color: #374151;">Memuat data...</p>
        </div>
    </div>

    <!-- Header -->
    @include('partials.dashboard-header-bar', ['title' => 'Transaksi'])

    <div class="container">
        <!-- Controls -->
        <div class="controls">
            <!-- Filter Cabang (Filter Utama) -->
            @php $isCabang = Auth::guard('admin')->user()->role !== 'admin'; @endphp
            <div class="search-filter">
                @if(!$isCabang)
                <div class="filter-dropdown">
                    <button type="button" class="filter-button" onclick="toggleDropdown('bankDropdown')">
                        {{ request('bank_sampah_id') ? $bankSampahList->where('id', request('bank_sampah_id'))->first()->nama_bank_sampah : 'Pilih Bank Sampah' }}
                        <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
                            <path d="M3 4.5L6 7.5L9 4.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
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
                        <div class="filter-option" onclick="selectFilter('bank_sampah_id', '', 'Pilih Bank Sampah')">Semua Bank Sampah</div>
                        @foreach($bankSampahList as $bank)
                            <div class="filter-option"
                                data-bank-name="{{ strtolower($bank->nama_bank_sampah) }}"
                                onclick="selectFilter('bank_sampah_id', '{{ $bank->id }}', '{{ $bank->nama_bank_sampah }}')">{{ $bank->nama_bank_sampah }}</div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            <div class="action-buttons">
                <div class="export-dropdown">
                    <button class="export-button" id="exportButton" type="button">
                        <span>Export</span>
                        <img src="{{ asset('icon/ic_trailing.svg') }}" alt="Export" width="16" height="16">
                    </button>
                    <div class="export-dropdown-content" id="exportDropdown">
                        <div class="export-option" onclick="document.getElementById('exportExcelModal').style.display='flex'">
                            <img src="{{ asset('icon/ic_laporan.svg') }}" alt="Excel">
                            <span>Export Excel</span>
                        </div>
                        <div class="export-option" onclick="document.getElementById('exportCsvModal').style.display='flex'">
                            <img src="{{ asset('icon/ic_laporan.svg') }}" alt="CSV">
                            <span>Export CSV</span>
                        </div>
                        <div class="export-option" onclick="document.getElementById('exportPdfModal').style.display='flex'">
                            <img src="{{ asset('icon/ic_laporan.svg') }}" alt="PDF">
                            <span>Export PDF</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Lainnya -->
        <div class="controls" style="margin-top: 1rem;">
            <div class="search-filter">
                <form method="GET" action="{{ route('dashboard.transaksi') }}" class="search-filter" id="mainFilterForm">
                    <input type="hidden" name="bank_sampah_id" value="{{ request('bank_sampah_id', '') }}">
                    <input type="hidden" name="tipe_setor" value="{{ request('tipe_setor', '') }}">
                    <input type="hidden" name="status" value="{{ request('status', '') }}">
                    
                    <div class="search-box">
                        <input type="text" name="search" class="search-input" placeholder="Cari transaksi..." value="{{ request('search') }}">
                        <img src="{{ asset('icon/ic_search.svg') }}" alt="Search" class="search-icon">
                    </div>

                    <div class="filter-dropdown">
                        <button type="button" class="filter-button" onclick="toggleDropdown('tipeDropdown')">
                            {{ request('tipe_setor') ? ucfirst(request('tipe_setor')) : 'Tipe Transaksi' }}
                            <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
                                <path d="M3 4.5L6 7.5L9 4.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                        <div id="tipeDropdown" class="filter-dropdown-content">
                            <div class="filter-option" onclick="selectFilter('tipe_setor', '', 'Tipe Transaksi')">Semua Tipe</div>
                            <div class="filter-option" onclick="selectFilter('tipe_setor', 'jual', 'Jual')">Jual</div>
                            <div class="filter-option" onclick="selectFilter('tipe_setor', 'sedekah', 'Sedekah')">Sedekah</div>
                            <div class="filter-option" onclick="selectFilter('tipe_setor', 'tabung', 'Tabung')">Tabung</div>
                        </div>
                    </div>

                    <div class="filter-dropdown">
                        <button type="button" class="filter-button" onclick="toggleDropdown('statusDropdown')">
                            {{ request('status') ? ucfirst(request('status')) : 'Status' }}
                            <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
                                <path d="M3 4.5L6 7.5L9 4.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                        <div id="statusDropdown" class="filter-dropdown-content">
                            <div class="filter-option" onclick="selectFilter('status', '', 'Status')">Semua Status</div>
                            <div class="filter-option" onclick="selectFilter('status', 'dikonfirmasi', 'Dikonfirmasi')">Dikonfirmasi</div>
                            <div class="filter-option" onclick="selectFilter('status', 'diproses', 'Diproses')">Diproses</div>
                            <div class="filter-option" onclick="selectFilter('status', 'dijemput', 'Dijemput')">Dijemput</div>
                            <div class="filter-option" onclick="selectFilter('status', 'selesai', 'Selesai')">Selesai</div>
                            <div class="filter-option" onclick="selectFilter('status', 'batal', 'Batal')">Batal</div>
                        </div>
                    </div>

                        <input type="date" name="start_date" class="date-input" placeholder="Tanggal Mulai" value="{{ request('start_date') }}">
                    <span style="margin: 0 4px; color: #6B7271;">s/d</span>
                        <input type="date" name="end_date" class="date-input" placeholder="Tanggal Akhir" value="{{ request('end_date') }}">

                    <a href="{{ route('dashboard.transaksi') }}" class="reset-btn" onclick="showLoading('Mereset filter...')">Reset</a>
                </form>
            </div>
        </div>

        <!-- Table -->
        <div class="table-container">
            @if(isset($transaksi) && $transaksi->count() > 0)
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID Transaksi</th>
                            <th>Tanggal</th>
                            <th>User</th>
                            <th>Bank</th>
                            <th>Tipe</th>
                            <th>Status</th>
                            <th>Total Estimasi</th>
                            <th>Total Aktual</th>
                            <th class="action-cell">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($transaksi as $trx)
                            <tr>
                                <td>#{{ $trx->id }}</td>
                                <td>{{ $trx->created_at ? \Carbon\Carbon::parse($trx->created_at)->format('d/m/Y H:i') : '-' }}</td>
                                <td>
                                    <div style="display: flex; flex-direction: column;">
                                        <span style="font-weight: 600; color: #1e293b;">{{ $trx->user_name ?? '-' }}</span>
                                        <span style="font-size: 12px; color: #6b7280; margin-top: 2px;">{{ $trx->user_identifier ?? '-' }}</span>
                                    </div>
                                </td>
                                <td>{{ $trx->bank_sampah_name ?? '-' }}</td>
                                <td>
                                    @php
                                        $tipeClass = 'badge-default';
                                        $tipeValue = strtolower(trim($trx->tipe_setor ?? ''));
                                        if ($tipeValue === 'jual') {
                                            $tipeClass = 'badge-jual';
                                        } elseif ($tipeValue === 'sedekah') {
                                            $tipeClass = 'badge-sedekah';
                                        } elseif ($tipeValue === 'tabung') {
                                            $tipeClass = 'badge-tabung';
                                        }
                                    @endphp
                                    <span class="badge {{ $tipeClass }}">{{ ucfirst($trx->tipe_setor ?? '-') }}</span>
                                </td>
                                <td>
                                    @php
                                        $statusClass = 'badge-default';
                                        $status = strtolower(trim($trx->status ?? ''));
                                        if ($status === 'dikonfirmasi') {
                                            $statusClass = 'badge-dikonfirmasi';
                                        } elseif ($status === 'diproses') {
                                            $statusClass = 'badge-diproses';
                                        } elseif ($status === 'dijemput') {
                                            $statusClass = 'badge-dijemput';
                                        } elseif ($status === 'selesai') {
                                            $statusClass = 'badge-selesai';
                                        } elseif ($status === 'batal') {
                                            $statusClass = 'badge-batal';
                                        }
                                    @endphp
                                    <span class="badge {{ $statusClass }}">{{ ucfirst($trx->status ?? '-') }}</span>
                                </td>
                                <td>{{ number_format($trx->estimasi_total ?? $trx->total_estimasi ?? 0, 0) }}</td>
                                <td>{{ number_format($trx->aktual_total ?? $trx->total_aktual ?? 0, 0) }}</td>
                                <td class="action-cell">
                                    <a href="{{ route('dashboard.transaksi.show', $trx->id) }}" class="action-btn detail-btn" title="Detail">
                                            <img src="{{ asset('icon/ic_detail.svg') }}" alt="Detail">
                                        </a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="9" class="no-results">Tidak ada data transaksi</td></tr>
                        @endforelse
                    </tbody>
                </table>
            @else
                <div class="no-results">
                    <h3>Tidak ada transaksi ditemukan</h3>
                    <p>Coba ubah filter pencarian Anda</p>
                </div>
            @endif
        </div>

        <!-- Pagination -->
        @if(isset($transaksi) && $transaksi->hasPages())
            <div class="pagination-container">
                <div class="pagination-info">
                    {{ $transaksi->firstItem() ?? 0 }} - {{ $transaksi->lastItem() ?? 0 }} of {{ $transaksi->total() }}
                </div>
                <div class="pagination">
                    <button class="pagination-btn" onclick="showLoading('Memuat halaman...'); window.location.href='{{ $transaksi->previousPageUrl() }}'" {{ $transaksi->onFirstPage() ? 'disabled' : '' }}>&lt;</button>
                    <button class="pagination-btn" onclick="showLoading('Memuat halaman...'); window.location.href='{{ $transaksi->nextPageUrl() }}'" {{ !$transaksi->hasMorePages() ? 'disabled' : '' }}>&gt;</button>
                </div>
            </div>
        @endif
    </div>

    <!-- Export Modals -->
    <div class="modal" id="exportExcelModal" style="display:none;">
        <div class="modal-content">
            <button class="modal-close" onclick="closeExportModal('exportExcelModal')">
                <img src="{{ asset('icon/ic_close.svg') }}" alt="Close">
            </button>
            <img src="{{ asset('icon/ic_laporan.svg') }}" alt="Export" class="modal-icon">
            <h2 class="modal-title">Export Excel Transaksi</h2>
            <p class="modal-subtitle">Pilih filter data yang ingin diexport</p>
            <div class="export-form">
                <div class="form-group">
                    <label class="form-label">Bank Sampah</label>
                    <select name="bank_sampah_id" class="form-control">
                        <option value="">Semua Bank Sampah</option>
                        @foreach($bankSampahList as $bank)
                            <option value="{{ $bank->id }}">{{ $bank->nama_bank_sampah }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Tipe Transaksi</label>
                    <select name="tipe_setor" class="form-control">
                        <option value="">Semua Tipe</option>
                        <option value="jual">Jual</option>
                        <option value="tabung">Tabung</option>
                        <option value="sedekah">Sedekah</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-control">
                        <option value="">Semua Status</option>
                        <option value="dikonfirmasi">Dikonfirmasi</option>
                        <option value="diproses">Diproses</option>
                        <option value="dijemput">Dijemput</option>
                        <option value="selesai">Selesai</option>
                        <option value="batal">Batal</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Periode</label>
                    <select name="time_filter" class="form-control" onchange="toggleDateInputs(this.value)">
                        <option value="">Semua Periode</option>
                        <option value="harian">Hari Ini</option>
                        <option value="mingguan">Minggu Ini</option>
                        <option value="bulanan">Bulan Ini</option>
                        <option value="range">Rentang Tanggal</option>
                    </select>
                </div>
                <div class="form-group" id="dateRangeGroup" style="display:none;">
                    <label class="form-label">Tanggal Mulai</label>
                    <input type="date" name="start_date" class="form-control">
                </div>
                <div class="form-group" id="dateRangeGroup2" style="display:none;">
                    <label class="form-label">Tanggal Akhir</label>
                    <input type="date" name="end_date" class="form-control">
                </div>
            </div>
            <div class="modal-buttons">
                <button class="modal-button cancel-button" type="button" onclick="closeExportModal('exportExcelModal')">Batal</button>
                <button class="modal-button confirm-button" type="button" id="confirmExportExcelBtn">Export Excel</button>
            </div>
        </div>
    </div>

    <div class="modal" id="exportCsvModal" style="display:none;">
        <div class="modal-content">
            <button class="modal-close" onclick="closeExportModal('exportCsvModal')">
                <img src="{{ asset('icon/ic_close.svg') }}" alt="Close">
            </button>
            <img src="{{ asset('icon/ic_laporan.svg') }}" alt="Export" class="modal-icon">
            <h2 class="modal-title">Export CSV Transaksi</h2>
            <p class="modal-subtitle">Export data lengkap (Summary, Bank Performa, Items by Volume, Detail Transaksi)</p>
            <div class="export-form">
                <div class="form-group">
                    <label class="form-label">Bank Sampah</label>
                    <select name="bank_sampah_id" class="form-control">
                        <option value="">Semua Bank Sampah</option>
                        @foreach($bankSampahList as $bank)
                            <option value="{{ $bank->id }}">{{ $bank->nama_bank_sampah }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Tipe Transaksi</label>
                    <select name="tipe_setor" class="form-control">
                        <option value="">Semua Tipe</option>
                        <option value="jual">Jual</option>
                        <option value="tabung">Tabung</option>
                        <option value="sedekah">Sedekah</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-control">
                        <option value="">Semua Status</option>
                        <option value="dikonfirmasi">Dikonfirmasi</option>
                        <option value="diproses">Diproses</option>
                        <option value="dijemput">Dijemput</option>
                        <option value="selesai">Selesai</option>
                        <option value="batal">Batal</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Periode</label>
                    <select name="time_filter" class="form-control" onchange="toggleDateInputs(this.value)">
                        <option value="">Semua Periode</option>
                        <option value="harian">Hari Ini</option>
                        <option value="mingguan">Minggu Ini</option>
                        <option value="bulanan">Bulan Ini</option>
                        <option value="range">Rentang Tanggal</option>
                    </select>
                </div>
                <div class="form-group" id="dateRangeGroup" style="display:none;">
                    <label class="form-label">Tanggal Mulai</label>
                    <input type="date" name="start_date" class="form-control">
                </div>
                <div class="form-group" id="dateRangeGroup2" style="display:none;">
                    <label class="form-label">Tanggal Akhir</label>
                    <input type="date" name="end_date" class="form-control">
                </div>
            </div>
            <div class="modal-buttons">
                <button class="modal-button cancel-button" type="button" onclick="closeExportModal('exportCsvModal')">Batal</button>
                <button class="modal-button confirm-button" type="button" id="confirmExportCsvBtn">Export CSV</button>
            </div>
        </div>
    </div>

    <div class="modal" id="exportPdfModal" style="display:none;">
        <div class="modal-content">
            <button class="modal-close" onclick="closeExportModal('exportPdfModal')">
                <img src="{{ asset('icon/ic_close.svg') }}" alt="Close">
            </button>
            <img src="{{ asset('icon/ic_laporan.svg') }}" alt="Export" class="modal-icon">
            <h2 class="modal-title">Export PDF Transaksi</h2>
            <p class="modal-subtitle">Pilih filter data yang ingin diexport</p>
            <div class="export-form">
                <div class="form-group">
                    <label class="form-label">Bank Sampah</label>
                    <select name="bank_sampah_id" class="form-control">
                        <option value="">Semua Bank Sampah</option>
                        @foreach($bankSampahList as $bank)
                            <option value="{{ $bank->id }}">{{ $bank->nama_bank_sampah }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Tipe Transaksi</label>
                    <select name="tipe_setor" class="form-control">
                        <option value="">Semua Tipe</option>
                        <option value="jual">Jual</option>
                        <option value="tabung">Tabung</option>
                        <option value="sedekah">Sedekah</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-control">
                        <option value="">Semua Status</option>
                        <option value="dikonfirmasi">Dikonfirmasi</option>
                        <option value="diproses">Diproses</option>
                        <option value="dijemput">Dijemput</option>
                        <option value="selesai">Selesai</option>
                        <option value="batal">Batal</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Periode</label>
                    <select name="time_filter" class="form-control" onchange="toggleDateInputs(this.value)">
                        <option value="">Semua Periode</option>
                        <option value="harian">Hari Ini</option>
                        <option value="mingguan">Minggu Ini</option>
                        <option value="bulanan">Bulan Ini</option>
                        <option value="range">Rentang Tanggal</option>
                    </select>
                </div>
                <div class="form-group" id="dateRangeGroup" style="display:none;">
                    <label class="form-label">Tanggal Mulai</label>
                    <input type="date" name="start_date" class="form-control">
                </div>
                <div class="form-group" id="dateRangeGroup2" style="display:none;">
                    <label class="form-label">Tanggal Akhir</label>
                    <input type="date" name="end_date" class="form-control">
                </div>
            </div>
            <div class="modal-buttons">
                <button class="modal-button cancel-button" type="button" onclick="closeExportModal('exportPdfModal')">Batal</button>
                <button class="modal-button confirm-button" type="button" id="confirmExportPdfBtn">Export PDF</button>
            </div>
        </div>
    </div>

    <script>
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

        function toggleDropdown(dropdownId) {
            const dropdown = document.getElementById(dropdownId);
            const allDropdowns = document.querySelectorAll('.filter-dropdown-content');

            // Close all other dropdowns
            allDropdowns.forEach(d => {
                if (d.id !== dropdownId) {
                    d.classList.remove('show');
                }
            });

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

        function selectFilter(name, value, displayText) {
            // Show loading for filter changes
            showLoading('Menerapkan filter...');
            
            // Handle all filters consistently - update form inputs and submit
            if (name === 'bank_sampah_id') {
                // Update hidden input for bank filter
                let hiddenInput = document.querySelector('input[name="bank_sampah_id"]');
                if (hiddenInput) {
                    hiddenInput.value = value;
                }
                
                // Update button text while preserving icon
                const button = event.target.closest('.filter-dropdown').querySelector('.filter-button');
                const icon = button.querySelector('svg');
                button.innerHTML = displayText;
                if (icon) {
                    button.appendChild(icon);
                }
                
                // Close dropdown
                document.getElementById('bankDropdown').classList.remove('show');
            } else {
                // Handle other filters (tipe_setor, status)
                let input = document.querySelector(`input[name="${name}"]`);
                if (input) {
                    input.value = value;
                }
                
            // Update button text while preserving icon
            const button = event.target.closest('.filter-dropdown').querySelector('.filter-button');
            const icon = button.querySelector('svg');
            button.innerHTML = displayText;
            if (icon) {
                button.appendChild(icon);
            }
                
            // Close dropdown
                const dropdownMap = {
                    'tipe_setor': 'tipeDropdown',
                    'status': 'statusDropdown'
                };
                const dropdownId = dropdownMap[name];
                if (dropdownId) {
                    document.getElementById(dropdownId).classList.remove('show');
                }
            }
            
            // Auto-submit form for all filters (preserves all current filter values)
            document.querySelector('#mainFilterForm').submit();
        }

        function exportData(format) {
            showLoading('Mengexport data...');
            const currentParams = new URLSearchParams(window.location.search);
            currentParams.set('format', format);
            const exportUrl = '{{ route("dashboard.transaksi.export") }}?' + currentParams.toString();
            window.location.href = exportUrl;
            document.getElementById('exportDropdown').classList.remove('show');
            }

        // Export dropdown toggle
        document.getElementById('exportButton').addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('exportDropdown').classList.toggle('show');
        });

        // Search auto-submit
        document.addEventListener('DOMContentLoaded', function() {
            // Show loading on initial page load
            showLoading('Memuat data...');
            
            // Hide loading after a short delay to simulate loading
            setTimeout(() => {
                hideLoading();
            }, 500);
            
            const searchInput = document.querySelector('input[name="search"]');
            if (searchInput) {
                let timeout = null;
                searchInput.addEventListener('input', function() {
                    clearTimeout(timeout);
                    timeout = setTimeout(() => {
                        showLoading('Mencari data...');
                        document.querySelector('#mainFilterForm').submit();
                    }, 500);
                });
            }
            
            // Date inputs auto-submit
            const dateInputs = document.querySelectorAll('input[type="date"]');
            dateInputs.forEach(input => {
                input.addEventListener('change', function() {
                    showLoading('Menerapkan filter...');
                    document.querySelector('#mainFilterForm').submit();
                });
            });
        });

        // Close dropdowns when clicking outside
        document.addEventListener('click', function(event) {
            if (!event.target.closest('.filter-dropdown') && !event.target.closest('.export-dropdown')) {
                document.querySelectorAll('.filter-dropdown-content, .export-dropdown-content').forEach(dropdown => {
                    dropdown.classList.remove('show');
                });
            }
        });
        
        // Pagination click event
        document.addEventListener('click', function(e) {
            if (e.target.closest('.pagination-btn')) {
                showLoading('Memuat halaman...');
            }
        });

        function closeExportModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        // Export functions for modal
        function exportExcel() {
            const modal = document.getElementById('exportExcelModal');
            const formData = new FormData();
            
            // Get form data from modal
            const bankSampahId = modal.querySelector('select[name="bank_sampah_id"]').value;
            const tipeSetor = modal.querySelector('select[name="tipe_setor"]').value;
            const status = modal.querySelector('select[name="status"]').value;
            const timeFilter = modal.querySelector('select[name="time_filter"]').value;
            const startDate = modal.querySelector('input[name="start_date"]').value;
            const endDate = modal.querySelector('input[name="end_date"]').value;
            
            // Add to form data
            if (bankSampahId) formData.append('bank_sampah_id', bankSampahId);
            if (tipeSetor) formData.append('tipe_setor', tipeSetor);
            if (status) formData.append('status', status);
            if (timeFilter) formData.append('time_filter', timeFilter);
            if (startDate) formData.append('start_date', startDate);
            if (endDate) formData.append('end_date', endDate);
            
            // Close modal first, then show loading
            closeExportModal('exportExcelModal');
            showLoading('Mengexport Excel...');
            
            fetch('{{ route("dashboard.transaksi.export.excel") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.blob();
            })
            .then(blob => {
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = `laporan_transaksi_${new Date().toISOString().slice(0,19).replace(/:/g,'-')}.xlsx`;
                document.body.appendChild(a);
                a.click();
                window.URL.revokeObjectURL(url);
                document.body.removeChild(a);
                hideLoading();
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat export Excel');
                hideLoading();
            });
        }

        function exportCsv() {
            const modal = document.getElementById('exportCsvModal');
            const formData = new FormData();
            
            // Get form data from modal
            const bankSampahId = modal.querySelector('select[name="bank_sampah_id"]').value;
            const tipeSetor = modal.querySelector('select[name="tipe_setor"]').value;
            const status = modal.querySelector('select[name="status"]').value;
            const timeFilter = modal.querySelector('select[name="time_filter"]').value;
            const startDate = modal.querySelector('input[name="start_date"]').value;
            const endDate = modal.querySelector('input[name="end_date"]').value;
            
            // Add to form data
            if (bankSampahId) formData.append('bank_sampah_id', bankSampahId);
            if (tipeSetor) formData.append('tipe_setor', tipeSetor);
            if (status) formData.append('status', status);
            if (timeFilter) formData.append('time_filter', timeFilter);
            if (startDate) formData.append('start_date', startDate);
            if (endDate) formData.append('end_date', endDate);
            
            // Close modal first, then show loading
            closeExportModal('exportCsvModal');
            showLoading('Mengexport CSV...');
            
            fetch('{{ route("dashboard.transaksi.export.csv") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.blob();
            })
            .then(blob => {
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = `laporan_transaksi_${new Date().toISOString().slice(0,19).replace(/:/g,'-')}.csv`;
                document.body.appendChild(a);
                a.click();
                window.URL.revokeObjectURL(url);
                document.body.removeChild(a);
                hideLoading();
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat export CSV');
                hideLoading();
            });
        }

        function exportPdf() {
            const modal = document.getElementById('exportPdfModal');
            const formData = new FormData();
            
            // Get form data from modal
            const bankSampahId = modal.querySelector('select[name="bank_sampah_id"]').value;
            const tipeSetor = modal.querySelector('select[name="tipe_setor"]').value;
            const status = modal.querySelector('select[name="status"]').value;
            const timeFilter = modal.querySelector('select[name="time_filter"]').value;
            const startDate = modal.querySelector('input[name="start_date"]').value;
            const endDate = modal.querySelector('input[name="end_date"]').value;
            
            // Add to form data
            if (bankSampahId) formData.append('bank_sampah_id', bankSampahId);
            if (tipeSetor) formData.append('tipe_setor', tipeSetor);
            if (status) formData.append('status', status);
            if (timeFilter) formData.append('time_filter', timeFilter);
            if (startDate) formData.append('start_date', startDate);
            if (endDate) formData.append('end_date', endDate);
            
            // Close modal first, then show loading
            closeExportModal('exportPdfModal');
            showLoading('Mengexport PDF...');
            
            fetch('{{ route("dashboard.transaksi.export.pdf") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.blob();
            })
            .then(blob => {
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = `laporan_transaksi_${new Date().toISOString().slice(0,19).replace(/:/g,'-')}.pdf`;
                document.body.appendChild(a);
                a.click();
                window.URL.revokeObjectURL(url);
                document.body.removeChild(a);
                hideLoading();
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat export PDF');
                hideLoading();
            });
        }



        // Add event listeners for export buttons
        document.addEventListener('DOMContentLoaded', function() {
            const confirmExportExcelBtn = document.getElementById('confirmExportExcelBtn');
            const confirmExportCsvBtn = document.getElementById('confirmExportCsvBtn');
            const confirmExportPdfBtn = document.getElementById('confirmExportPdfBtn');
            
            if (confirmExportExcelBtn) {
                confirmExportExcelBtn.addEventListener('click', exportExcel);
            }
            if (confirmExportCsvBtn) {
                confirmExportCsvBtn.addEventListener('click', exportCsv);
            }
            if (confirmExportPdfBtn) {
                confirmExportPdfBtn.addEventListener('click', exportPdf);
            }
        });

        // Function to toggle date inputs based on time filter
        function toggleDateInputs(timeFilter) {
            // Get the modal that contains the changed select
            const select = event.target;
            const modal = select.closest('.modal');
            const dateRangeGroup = modal.querySelector('#dateRangeGroup');
            const dateRangeGroup2 = modal.querySelector('#dateRangeGroup2');
            
            if (timeFilter === 'range') {
                if (dateRangeGroup) dateRangeGroup.style.display = 'block';
                if (dateRangeGroup2) dateRangeGroup2.style.display = 'block';
            } else {
                if (dateRangeGroup) dateRangeGroup.style.display = 'none';
                if (dateRangeGroup2) dateRangeGroup2.style.display = 'none';
            }
        }
    </script>
</body>
</html>
@endsection 