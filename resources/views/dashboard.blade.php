@extends('layouts.dashboard')

@section('title', 'Bengkel Sampah - Dashboard')

@section('content')
    <!-- ================= HEADER & FILTER ================= -->
    <div class="dashboard-header-bar"
        style="position:sticky;top:0;z-index:20;background:#fff;box-shadow:var(--card-shadow);border-radius:14px;margin-bottom:1.5rem;padding:1.2rem 2rem;display:flex;flex-wrap:wrap;align-items:center;gap:1.2rem;justify-content:space-between;">
        <div style="display:flex;align-items:center;gap:1.2rem;flex:1;min-width:220px;">
            <form method="GET" action="{{ route('dashboard') }}" id="mainFilterForm"
                style="display:flex;gap:1rem;align-items:center;">
                @php $isCabang = Auth::guard('admin')->user()->role !== 'admin'; @endphp
                @if (!$isCabang)
                    <!-- Filter Cabang (Filter Utama) -->
                    <div class="filter-dropdown">
                        <button type="button" class="filter-button" onclick="toggleDropdown('bankDropdown')">
                            {{ request('bank_sampah_id') ? $bankSampahList->where('id', request('bank_sampah_id'))->first()->nama_bank_sampah : 'Pilih Bank Sampah' }}
                            <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
                                <path d="M3 4.5L6 7.5L9 4.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round" />
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
                            <div class="filter-option" onclick="selectFilter('bank_sampah_id', '', 'Pilih Bank Sampah')">
                                Semua Bank Sampah</div>
                            @foreach ($bankSampahList ?? [] as $bank)
                                <div class="filter-option"
                                    data-bank-name="{{ strtolower($bank->nama_bank_sampah) }}"
                                    onclick="selectFilter('bank_sampah_id', '{{ $bank->id }}', '{{ $bank->nama_bank_sampah }}')">
                                    {{ $bank->nama_bank_sampah }}</div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Filter Periode -->
                <div class="filter-dropdown">
                    <button type="button" class="filter-button" onclick="toggleDropdown('periodeDropdown')">
                        @php
                            $periode = request('periode', 'harian');
                            $periodeText =
                                [
                                    'harian' => 'Harian',
                                    'mingguan' => 'Mingguan',
                                    'bulanan' => 'Bulanan',
                                    'enam_bulanan' => '6 Bulanan',
                                    'tahunan' => 'Tahunan',
                                    'range' => request('range_date') ? request('range_date') : 'Range Waktu',
                                ][$periode] ?? 'Harian';
                        @endphp
                        {{ $periodeText }}
                        <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
                            <path d="M3 4.5L6 7.5L9 4.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                    </button>
                    <div id="periodeDropdown" class="filter-dropdown-content">
                        <div class="filter-option" onclick="selectFilter('periode', 'harian', 'Harian')">Harian</div>
                        <div class="filter-option" onclick="selectFilter('periode', 'mingguan', 'Mingguan')">Mingguan</div>
                        <div class="filter-option" onclick="selectFilter('periode', 'bulanan', 'Bulanan')">Bulanan</div>
                        <div class="filter-option" onclick="selectFilter('periode', 'enam_bulanan', '6 Bulanan')">6 Bulanan
                        </div>
                        <div class="filter-option" onclick="selectFilter('periode', 'tahunan', 'Tahunan')">Tahunan</div>
                        <div class="filter-option" onclick="showDateRangePicker()">Range Waktu</div>
                    </div>
                </div>

                <!-- Hidden inputs for form submission -->
                <input type="hidden" name="bank_sampah_id" value="{{ request('bank_sampah_id', '') }}">
                <input type="hidden" name="periode" value="{{ request('periode', 'harian') }}">
                <input type="hidden" name="range_date" id="range-date-hidden" value="{{ request('range_date') }}" />

                <!-- Date Range Picker Modal -->
                <div id="dateRangeModal" class="modal" style="display: none;">
                    <div class="modal-content" style="max-width: 400px;">
                        <button class="modal-close" onclick="closeDateRangeModal()">
                            <img src="{{ asset('icon/ic_close.svg') }}" alt="Close">
                        </button>
                        <h2 class="modal-title">Pilih Range Waktu</h2>
                        <p class="modal-subtitle">Pilih tanggal mulai dan selesai</p>

                        <div class="form-group">
                            <label class="form-label">Tanggal Mulai</label>
                            <input type="date" id="start-date" class="form-control" value="{{ request('start_date') }}">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Tanggal Selesai</label>
                            <input type="date" id="end-date" class="form-control" value="{{ request('end_date') }}">
                        </div>

                        <div class="modal-buttons">
                            <button class="modal-button cancel-button" onclick="closeDateRangeModal()">Batal</button>
                            <button class="modal-button confirm-button" onclick="applyDateRange()">Terapkan</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div style="display:flex;align-items:center;gap:1.2rem;">
            {{-- @if (!$isCabang)
                <!-- Zenziva Balance Cards -->
                <div style="display:flex;gap:0.8rem;align-items:center;">
                    <div
                        style="background:#E8F5E8;border-radius:10px;padding:6px 12px;display:flex;align-items:center;gap:0.5rem;border:1px solid #BBF7D0;">
                        <i class="fas fa-key" style="font-size:0.9rem;color:#166534;"></i>
                        <div style="font-size:0.85rem;font-weight:600;color:#166534;">
                            OTP: @if (isset($zenzivaOtpBalance['status']) && $zenzivaOtpBalance['status'] == '1')
                                Rp{{ $zenzivaOtpBalance['balance'] }}
                            @else
                                <span style="color:#EF4444;">Gagal</span>
                            @endif
                        </div>
                    </div>
                </div>
            @endif --}}

            <!-- Avatar User with Dropdown -->
            <div style="position:relative;">
                <div style="width:40px;height:40px;border-radius:50%;background:#0FB7A6;color:white;display:flex;align-items:center;justify-content:center;font-weight:600;font-size:14px;cursor:pointer;transition:background 0.2s;"
                    onclick="toggleUserDropdown()" onmouseover="this.style.background='#0DA594'"
                    onmouseout="this.style.background='#0FB7A6'">
                    {{ strtoupper(substr(Auth::guard('admin')->user()->name ?? 'A', 0, 2)) }}
                </div>

                <!-- User Dropdown Menu -->
                <div id="userDropdown"
                    style="position:absolute;right:0;top:48px;width:280px;background:#fff;border-radius:12px;box-shadow:0 4px 20px rgba(0,0,0,0.15);border:1px solid #E5E6E6;z-index:50;display:none;">
                    <div style="padding:20px;border-bottom:1px solid #F3F4F6;">
                        <div style="display:flex;align-items:center;gap:12px;">
                            <div
                                style="width:48px;height:48px;background:#0FB7A6;border-radius:50%;display:flex;align-items:center;justify-content:center;color:white;font-weight:600;font-size:18px;">
                                {{ strtoupper(substr(Auth::guard('admin')->user()->name ?? 'A', 0, 2)) }}
                            </div>
                            <div style="flex:1;min-width:0;">
                                <p
                                    style="font-size:16px;font-weight:600;color:#39746E;margin:0 0 4px 0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                    {{ Auth::guard('admin')->user()->name ?? 'Admin' }}</p>
                                <p
                                    style="font-size:13px;color:#6B7271;margin:0 0 8px 0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                    {{ Auth::guard('admin')->user()->email ?? 'admin@example.com' }}</p>
                                <div>
                                    <span
                                        style="display:inline-flex;align-items:center;padding:4px 8px;border-radius:12px;font-size:11px;font-weight:600;{{ Auth::guard('admin')->user()->role === 'admin' ? 'background:#DBEAFE;color:#1E40AF;' : 'background:#D1FAE5;color:#065F46;' }}">
                                        {{ Auth::guard('admin')->user()->role === 'admin' ? 'Super Admin' : 'Admin Cabang' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div style="padding:8px;">
                        <a href="{{ route('admin.profile') }}"
                            style="display:flex;align-items:center;padding:12px 16px;font-size:14px;color:#39746E;border-radius:8px;text-decoration:none;transition:background 0.2s;"
                            onmouseover="this.style.background='#F3F4F6'"
                            onmouseout="this.style.background='transparent'">
                            <svg style="width:16px;height:16px;margin-right:12px;color:#6B7271;" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            View Profile
                        </a>
                        <form method="POST" action="{{ route('admin.logout') }}" style="margin:0;">
                            @csrf
                            <button type="submit"
                                style="display:flex;align-items:center;padding:12px 16px;font-size:14px;color:#DC2626;border-radius:8px;text-decoration:none;transition:background 0.2s;background:none;border:none;width:100%;cursor:pointer;text-align:left;"
                                onmouseover="this.style.background='#FEF2F2'"
                                onmouseout="this.style.background='transparent'">
                                <svg style="width:16px;height:16px;margin-right:12px;color:#DC2626;" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                                    </path>
                                </svg>
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Filter Dropdown Styles (from transaksi page) */
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
            min-width: 160px;
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

        /* Hide filtered options */
        .filter-option.hidden {
            display: none;
        }

        .date-input {
            padding: 8px 12px;
            border: 1px solid #E5E6E6;
            border-radius: 18px;
            font-size: 14px;
            background: #EFF0F0;
            border: 1px solid #EFF0F0;
            color: #39746E;
        }

        .date-input:focus {
            background: #fff;
            border: 1px solid #0FB7A6;
            outline: none;
        }

        /* Modal Styles */
        .modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(0, 0, 0, 0.35);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 99999;
        }

        .modal-content {
            background: #fff;
            border-radius: 12px;
            padding: 32px 24px 24px 24px;
            min-width: 0;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12);
            position: relative;
        }

        .modal-close {
            position: absolute;
            top: 18px;
            right: 18px;
            background: none;
            border: none;
            cursor: pointer;
        }

        .modal-icon {
            display: block;
            margin: 0 auto 12px auto;
            width: 38px;
            height: 38px;
        }

        .modal-title {
            font-size: 20px;
            font-weight: 700;
            color: #39746E;
            margin-bottom: 6px;
            text-align: center;
        }

        .modal-subtitle {
            font-size: 14px;
            color: #6B7271;
            margin-bottom: 18px;
            text-align: center;
        }

        .form-group {
            margin-bottom: 16px;
        }

        .form-label {
            font-weight: 600;
            color: #39746E;
            margin-bottom: 6px;
            display: block;
        }

        .form-control,
        .form-select,
        .form-input {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #E5E6E6;
            border-radius: 6px;
            font-size: 14px;
            margin-top: 2px;
        }

        .modal-buttons {
            display: flex;
            gap: 12px;
            justify-content: flex-end;
            margin-top: 18px;
        }

        .modal-button {
            font-size: 14px;
            font-weight: 600;
            border-radius: 6px;
            padding: 8px 18px;
            cursor: pointer;
            border: none;
        }

        .cancel-button {
            background: #6B7271;
            color: #fff;
        }

        .cancel-button:hover {
            background: #5a5f5e;
        }

        .confirm-button {
            background: #39746E;
            color: #fff;
        }

        .confirm-button:hover {
            background: #2d5a55;
        }

        /* KPI Total Pembelian 100% sesuai referensi user */
        .kpi-card-ref {
            background: white;
            border-radius: 10px;
            padding: 15px 15px 15px 15px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.06);
            width: 100%;
            /* max-width: 340px; */
            position: relative;
            overflow: hidden;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            min-height: 100px;
        }

        .kpi-card-ref .percentage-badge {
            position: absolute;
            top: 0;
            right: 0;
            background: #2dd4bf;
            color: white;
            padding: 5px 10px 5px 10px;
            border-radius: 0 10px 0 10px;
            font-size: 8px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 4px;
            z-index: 2;
            transition: background 0.2s, color 0.2s;
        }

        .kpi-card-ref .percentage-badge.down {
            background: #fee2e2;
            color: #dc2626;
        }

        .kpi-card-ref .arrow-up {
            width: 0;
            height: 0;
            border-left: 4px solid transparent;
            border-right: 4px solid transparent;
            border-bottom: 5px solid white;
        }

        .kpi-card-ref .arrow-down {
            width: 0;
            height: 0;
            border-left: 4px solid transparent;
            border-right: 4px solid transparent;
            border-top: 5px solid #dc2626;
        }

        .kpi-card-ref .header-row {
            display: flex;
            align-items: center;
            gap: 5px;
            margin-top: 10px;
            margin-bottom: 5px;
        }

        .kpi-card-ref .icon-container {
            width: 20px;
            height: 20px;
            background: #e8f5f3;
            border-radius: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .kpi-card-ref .icon-svg {
            width: 17px;
            height: 17px;
            display: block;
        }

        .kpi-card-ref .title {
            font-size: 10px;
            font-weight: 700;
            color: #1f2937;
            letter-spacing: -0.5px;
            margin: 0;
        }

        .kpi-card-ref .main-amount {
            font-size: 20px;
            font-weight: 700;
            color: #065f46;
            margin-bottom: 0px;
            letter-spacing: -1px;
        }

        .kpi-card-ref .increase-amount {
            font-size: 10px;
            font-weight: 600;
            color: #2dd4bf;
            display: flex;
            align-items: center;
            gap: 4px;
            justify-content: flex-end;
            margin-top: auto;
            margin-right: 2px;
            transition: color 0.2s;
        }

        .kpi-card-ref .increase-amount.down {
            color: #dc2626;
        }

        .kpi-card-ref .plus-sign {
            font-size: 10px;
        }

        @media (max-width: 900px) {
            .kpi-card-ref {
                max-width: 100%;
                grid-column: span 1 !important;
            }

            .kpi-grid {
                grid-template-columns: 1fr 1fr !important;
            }
        }

        @media (max-width: 600px) {
            .kpi-card-ref {
                padding: 10px;
                border-radius: 7px;
                min-height: 70px;
                grid-column: span 1 !important;
            }

            .kpi-card-ref .header-row {
                margin-bottom: 4px;
                gap: 3px;
            }

            .kpi-card-ref .icon-container {
                width: 14px;
                height: 14px;
                border-radius: 3px;
            }

            .kpi-card-ref .icon-svg {
                width: 11px;
                height: 11px;
            }

            .kpi-card-ref .title {
                font-size: 7px;
            }

            .kpi-card-ref .main-amount {
                font-size: 11px;
            }

            .kpi-card-ref .increase-amount {
                font-size: 7px;
            }

            .kpi-card-ref .percentage-badge {
                font-size: 6px;
                padding: 2px 5px 2px 5px;
                border-radius: 0 5px 0 5px;
            }

            .kpi-grid {
                grid-template-columns: 1fr !important;
            }
        }

        .kpi-card-ref .icon-fa {
            font-size: 11px;
            color: #2dd4bf;
            display: block;
        }

        @media (max-width: 900px) {
            .kpi-card-ref .icon-fa {
                font-size: 9px;
            }
        }

        @media (max-width: 600px) {
            .kpi-card-ref .icon-fa {
                font-size: 8px;
            }
        }

        /* Financial Metrics Enhanced Styling */
        .financial-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            border: 1px solid #e5e7eb;
            transition: all 0.3s ease;
        }

        .financial-card:hover {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            transform: translateY(-2px);
        }

        .financial-card-header {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .financial-icon {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 20px;
            flex-shrink: 0;
        }

        .financial-title-group {
            flex: 1;
        }

        .financial-label {
            font-size: 0.875rem;
            font-weight: 600;
            color: #6b7280;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 0.025em;
        }

        .financial-description {
            font-size: 0.75rem;
            color: #9ca3af;
            margin: 0.25rem 0 0 0;
        }

        .financial-amount {
            font-size: 1.875rem;
            font-weight: 700;
            color: #065f46;
            margin-bottom: 1rem;
            line-height: 1.2;
        }

        .financial-amount.negative-amount {
            color: #dc2626;
        }

        .financial-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 0.75rem;
            border-top: 1px solid #e5e7eb;
        }

        .financial-change {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.875rem;
            font-weight: 600;
        }

        .financial-change.positive {
            color: #059669;
        }

        .financial-change.negative {
            color: #dc2626;
        }

        .financial-change i {
            font-size: 0.75rem;
        }

        .financial-percentage {
            font-size: 0.875rem;
            font-weight: 600;
            padding: 0.25rem 0.75rem;
            border-radius: 999px;
        }

        .financial-percentage.positive {
            background-color: #d1fae5;
            color: #059669;
        }

        .financial-percentage.negative {
            background-color: #fee2e2;
            color: #dc2626;
        }

        .financial-metric {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.875rem;
        }

        .metric-label {
            color: #6b7280;
            font-weight: 500;
        }

        .metric-value {
            color: #065f46;
            font-weight: 700;
        }

        .financial-status {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.875rem;
            font-weight: 600;
            padding: 0.25rem 0.75rem;
            border-radius: 999px;
        }

        .financial-status.profitable {
            background-color: #d1fae5;
            color: #059669;
        }

        .financial-status.loss {
            background-color: #fee2e2;
            color: #dc2626;
        }

        .financial-status i {
            font-size: 0.5rem;
        }

        /* Responsive for financial metrics */
        @media (max-width: 1024px) {
            .financial-metrics-grid {
                grid-template-columns: 1fr !important;
                gap: 1rem !important;
            }
        }

        /* tCO2e Hero Card */
        .tco2e-hero-card {
            background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
            border-radius: 16px;
            padding: 1.5rem;
            text-align: center;
            position: relative;
            overflow: visible;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            min-height: 200px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .tco2e-hero-card::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle, rgba(16, 185, 129, 0.1) 0%, transparent 70%);
            pointer-events: none;
        }

        .tco2e-header {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }

        .tco2e-icon {
            width: 40px;
            height: 40px;
            background: #10b981;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.2rem;
        }

        .tco2e-title {
            font-size: 1rem;
            font-weight: 600;
            color: #065f46;
        }

        /* Circular Progress Ring */
        .tco2e-ring-container {
            position: relative;
            width: 140px;
            height: 140px;
            margin: 0 auto 1rem;
        }

        .tco2e-ring {
            transform: rotate(-90deg);
            width: 100%;
            height: 100%;
        }

        .tco2e-ring-bg {
            fill: none;
            stroke: #d1fae5;
            stroke-width: 8;
        }

        .tco2e-ring-progress {
            fill: none;
            stroke: url(#tco2e-gradient);
            stroke-width: 8;
            stroke-linecap: round;
            stroke-dasharray: 377;
            stroke-dashoffset: 377;
            animation: tco2e-progress 1.5s ease-out forwards;
        }

        @keyframes tco2e-progress {
            to {
                stroke-dashoffset: 0;
            }
        }

        /* Value Display */
        .tco2e-value-container {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
        }

        .tco2e-value {
            font-size: 2rem;
            font-weight: 700;
            color: #065f46;
            line-height: 1;
            font-family: 'Segoe UI', system-ui, sans-serif;
        }

        .tco2e-unit {
            font-size: 0.9rem;
            font-weight: 600;
            color: #10b981;
            margin-top: 0.25rem;
        }

        .tco2e-description {
            font-size: 0.85rem;
            color: #047857;
            margin-top: 0.5rem;
        }

        /* Count-up Animation */
        .tco2e-animated {
            animation: tco2e-fadeIn 0.5s ease-out;
        }

        @keyframes tco2e-fadeIn {
            from { opacity: 0; transform: translate(-50%, -50%) scale(0.9); }
            to { opacity: 1; transform: translate(-50%, -50%) scale(1); }
        }

        /* Tooltip */
        .tco2e-info-trigger {
            position: absolute;
            top: 1rem;
            right: 1rem;
            cursor: pointer;
        }

        .tco2e-tooltip {
            display: none;
            position: absolute;
            right: 0;
            top: 100%;
            background: white;
            padding: 1rem;
            border-radius: 8px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
            min-width: 280px;
            z-index: 100;
            text-align: left;
            font-size: 0.82rem;
            line-height: 1.5;
        }

        .tco2e-info-trigger:hover .tco2e-tooltip,
        .tco2e-info-trigger:focus .tco2e-tooltip {
            display: block;
        }

        /* Mobile Responsive */
        @media (max-width: 600px) {
            .tco2e-hero-card {
                padding: 1rem;
                min-height: 180px;
            }
            .tco2e-ring-container {
                width: 120px;
                height: 120px;
            }
            .tco2e-value {
                font-size: 1.6rem;
            }
            .tco2e-tooltip {
                min-width: 220px;
                right: -50px;
            }
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
            console.log('selectFilter called:', field, value, displayText); // Debug log

            // Update hidden input
            const hiddenInput = document.querySelector(`input[name="${field}"]`);
            if (hiddenInput) {
                hiddenInput.value = value;
                console.log('Updated hidden input:', field, '=', value); // Debug log
            }

            // Update button text
            const button = event.target.closest('.filter-dropdown').querySelector('.filter-button');
            if (button) {
                button.innerHTML = displayText +
                    '<svg width="12" height="12" viewBox="0 0 12 12" fill="none"><path d="M3 4.5L6 7.5L9 4.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>';
                console.log('Updated button text to:', displayText); // Debug log
            }

            // Close dropdown
            const dropdownId = event.target.closest('.filter-dropdown-content').id;
            document.getElementById(dropdownId).classList.remove('show');

            // Submit form
            const form = document.getElementById('mainFilterForm');
            if (form) {
                console.log('Submitting form...'); // Debug log
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

        function showDateRangePicker() {
            // Close dropdown first
            document.getElementById('periodeDropdown').classList.remove('show');

            // Show modal
            document.getElementById('dateRangeModal').style.display = 'flex';
        }

        function closeDateRangeModal() {
            document.getElementById('dateRangeModal').style.display = 'none';
        }

        function applyDateRange() {
            const startDate = document.getElementById('start-date').value;
            const endDate = document.getElementById('end-date').value;

            if (!startDate || !endDate) {
                alert('Pilih tanggal mulai dan selesai');
                return;
            }

            // Format date range for display
            const start = new Date(startDate);
            const end = new Date(endDate);
            const dateRangeText = start.toLocaleDateString('id-ID', {
                    day: '2-digit',
                    month: 'short',
                    year: 'numeric'
                }) +
                ' - ' +
                end.toLocaleDateString('id-ID', {
                    day: '2-digit',
                    month: 'short',
                    year: 'numeric'
                });

            // Update hidden inputs
            document.querySelector('input[name="periode"]').value = 'range';
            document.querySelector('input[name="start_date"]').value = startDate;
            document.querySelector('input[name="end_date"]').value = endDate;
            document.getElementById('range-date-hidden').value = dateRangeText;

            // Update button text
            const button = document.querySelector('.filter-dropdown .filter-button');
            button.innerHTML = dateRangeText +
                '<svg width="12" height="12" viewBox="0 0 12 12" fill="none"><path d="M3 4.5L6 7.5L9 4.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>';

            // Close modal
            closeDateRangeModal();

            // Submit form
            document.getElementById('mainFilterForm').submit();
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

            // Close modal when clicking outside
            const modal = document.getElementById('dateRangeModal');
            if (event.target === modal) {
                closeDateRangeModal();
            }
        }

        // Add missing hidden inputs for start_date and end_date
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('mainFilterForm');
            if (form) {
                // Add missing hidden inputs if they don't exist
                if (!form.querySelector('input[name="start_date"]')) {
                    const startDateInput = document.createElement('input');
                    startDateInput.type = 'hidden';
                    startDateInput.name = 'start_date';
                    startDateInput.value = '{{ request('start_date') }}';
                    form.appendChild(startDateInput);
                }

                if (!form.querySelector('input[name="end_date"]')) {
                    const endDateInput = document.createElement('input');
                    endDateInput.type = 'hidden';
                    endDateInput.name = 'end_date';
                    endDateInput.value = '{{ request('end_date') }}';
                    form.appendChild(endDateInput);
                }
            }
        });

        // User dropdown functions
        function toggleUserDropdown() {
            const dropdown = document.getElementById('userDropdown');
            if (dropdown.style.display === 'none' || dropdown.style.display === '') {
                dropdown.style.display = 'block';
            } else {
                dropdown.style.display = 'none';
            }
        }

        // Close user dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const userDropdown = document.getElementById('userDropdown');
            const avatarButton = event.target.closest('[onclick="toggleUserDropdown()"]');

            if (userDropdown && !avatarButton && !userDropdown.contains(event.target)) {
                userDropdown.style.display = 'none';
            }
        });
    </script>

    <!-- ================= KPI BAR (COMPACT DESIGN) ================= -->
    @php
        $periode = request('periode', 'harian');
        $totalPembelian = $dashboardSummary['nilai_setoran'] ?? 0;
        $totalPembelianPrev = $dashboardSummary['nilai_setoran_prev'] ?? 0;
        $pembelianDelta = $totalPembelian - $totalPembelianPrev;
        $pembelianPercent =
            $totalPembelianPrev > 0 ? ($pembelianDelta / $totalPembelianPrev) * 100 : ($totalPembelian > 0 ? 100 : 0);

        $totalSetor = $dashboardSummary['setoran'] ?? 0;
        $totalSetorPrev = $dashboardSummary['setoran_prev'] ?? 0;
        $setorDelta = $totalSetor - $totalSetorPrev;
        $setorPercent = $totalSetorPrev > 0 ? ($setorDelta / $totalSetorPrev) * 100 : ($totalSetor > 0 ? 100 : 0);

        $totalCustomer = $dashboardSummary['user'] ?? 0;
        $totalCustomerPrev = $dashboardSummary['user_prev'] ?? 0;
        $customerDelta = $totalCustomer - $totalCustomerPrev;
        $customerPercent =
            $totalCustomerPrev > 0 ? ($customerDelta / $totalCustomerPrev) * 100 : ($totalCustomer > 0 ? 100 : 0);

        $totalPoin = $dashboardSummary['poin_redeem'] ?? 0;
        $totalPoinPrev = $dashboardSummary['poin_redeem_prev'] ?? 0;
        $poinDelta = $totalPoin - $totalPoinPrev;
        $poinPercent = $totalPoinPrev > 0 ? ($poinDelta / $totalPoinPrev) * 100 : ($totalPoin > 0 ? 100 : 0);

        $totalSampahKg = $dashboardSummary['total_sampah_kg'] ?? 0;
        $totalSampahKgPrev = $dashboardSummary['total_sampah_kg_prev'] ?? 0;
        $sampahKgDelta = $totalSampahKg - $totalSampahKgPrev;
        $sampahKgPercent =
            $totalSampahKgPrev > 0 ? ($sampahKgDelta / $totalSampahKgPrev) * 100 : ($totalSampahKg > 0 ? 100 : 0);

        $totalSampahUnit = $dashboardSummary['total_sampah_unit'] ?? 0;
        $totalSampahUnitPrev = $dashboardSummary['total_sampah_unit_prev'] ?? 0;
        $sampahUnitDelta = $totalSampahUnit - $totalSampahUnitPrev;
        $sampahUnitPercent =
            $totalSampahUnitPrev > 0
                ? ($sampahUnitDelta / $totalSampahUnitPrev) * 100
                : ($totalSampahUnit > 0
                    ? 100
                    : 0);

        // Dampak lingkungan - hanya tCO₂e (Small City CO2e Model)
        $co2SavedTon = $environmentalImpact['co2_saved_ton'] ?? 0;

        // Sales metrics (Penjualan ke Offtaker)
        $totalPenjualan = $dashboardSummary['total_penjualan'] ?? 0;
        $totalPenjualanPrev = $dashboardSummary['total_penjualan_prev'] ?? 0;
        $penjualanDelta = $totalPenjualan - $totalPenjualanPrev;
        $penjualanPercent =
            $totalPenjualanPrev > 0 ? ($penjualanDelta / $totalPenjualanPrev) * 100 : ($totalPenjualan > 0 ? 100 : 0);

        // Profit metrics (Laba Kotor = Total Penjualan - Harga Beli Total)
        $labaKotor = $dashboardSummary['laba_kotor'] ?? 0;

        // Inventory status
        $totalSampahTersimpan = $dashboardSummary['total_sampah_tersimpan'] ?? 0;
        $totalSampahTerjual = $dashboardSummary['total_sampah_terjual'] ?? 0;
        $totalSampahDiolah = $dashboardSummary['total_sampah_diolah'] ?? 0;

        // Customer Terdaftar Bank Sampah metrics
        $totalRegisteredBankUsers = $dashboardSummary['registered_bank_users'] ?? 0;
        $totalRegisteredBankUsersPrev = $dashboardSummary['registered_bank_users_prev'] ?? 0;
        $registeredBankUsersDelta = $totalRegisteredBankUsers - $totalRegisteredBankUsersPrev;
        $registeredBankUsersPercent =
            $totalRegisteredBankUsersPrev > 0
                ? ($registeredBankUsersDelta / $totalRegisteredBankUsersPrev) * 100
                : ($totalRegisteredBankUsers > 0 ? 100 : 0);
    @endphp
    <!-- Row 1: Core Business Metrics -->
    <div class="kpi-grid"
        style="display:grid;grid-template-columns:repeat(4,1fr);gap:1rem;margin-bottom:1rem;">
        <!-- Total Setor -->
        <div class="kpi-card-ref">
            <div class="percentage-badge @if ($setorDelta < 0) down @endif">
                @if ($setorDelta < 0)
                    <span class="arrow-down"></span>
                @else
                    <span class="arrow-up"></span>
                @endif
                {{ number_format(abs($setorPercent), 1) }}%
            </div>
            <div class="header-row">
                <div class="icon-container">
                    <i class="fa-solid fa-money-check-dollar icon-fa"></i>
                </div>
                <h1 class="title">Total Setor</h1>
            </div>
            <div class="main-amount">{{ number_format($totalSetor) }}</div>
            <div class="increase-amount @if ($setorDelta < 0) down @endif"><span
                    class="plus-sign">{{ $setorDelta >= 0 ? '+' : '-' }}</span>{{ number_format(abs($setorDelta), 0, ',', '.') }}
            </div>
        </div>
        <!-- Total Customer -->
        <div class="kpi-card-ref">
            <div class="percentage-badge @if ($customerDelta < 0) down @endif">
                @if ($customerDelta < 0)
                    <span class="arrow-down"></span>
                @else
                    <span class="arrow-up"></span>
                @endif
                {{ number_format(abs($customerPercent), 1) }}%
            </div>
            <div class="header-row">
                <div class="icon-container">
                    <i class="fa-solid fa-users icon-fa"></i>
                </div>
                <h1 class="title">Total Customer</h1>
            </div>
            <div class="main-amount">{{ number_format($totalCustomer) }}</div>
            <div class="increase-amount @if ($customerDelta < 0) down @endif"><span
                    class="plus-sign">{{ $customerDelta >= 0 ? '+' : '-' }}</span>{{ number_format(abs($customerDelta), 0, ',', '.') }}
            </div>
        </div>
        <!-- Customer Terdaftar Bank Sampah (NEW) -->
        <div class="kpi-card-ref">
            <div class="percentage-badge @if ($registeredBankUsersDelta < 0) down @endif">
                @if ($registeredBankUsersDelta < 0)
                    <span class="arrow-down"></span>
                @else
                    <span class="arrow-up"></span>
                @endif
                {{ number_format(abs($registeredBankUsersPercent), 1) }}%
            </div>
            <div class="header-row">
                <div class="icon-container">
                    <i class="fa-solid fa-user-check icon-fa"></i>
                </div>
                <h1 class="title">Customer Terdaftar Bank Sampah</h1>
            </div>
            <div class="main-amount">{{ number_format($totalRegisteredBankUsers) }}</div>
            <div class="increase-amount @if ($registeredBankUsersDelta < 0) down @endif"><span
                    class="plus-sign">{{ $registeredBankUsersDelta >= 0 ? '+' : '-' }}</span>{{ number_format(abs($registeredBankUsersDelta), 0, ',', '.') }}
            </div>
        </div>
        <!-- Total Poin -->
        <div class="kpi-card-ref">
            <div class="percentage-badge @if ($poinDelta < 0) down @endif">
                @if ($poinDelta < 0)
                    <span class="arrow-down"></span>
                @else
                    <span class="arrow-up"></span>
                @endif
                {{ number_format(abs($poinPercent), 1) }}%
            </div>
            <div class="header-row">
                <div class="icon-container">
                    <i class="fa-solid fa-coins icon-fa"></i>
                </div>
                <h1 class="title">Total Poin</h1>
            </div>
            <div class="main-amount">{{ number_format($totalPoin) }}</div>
            <div class="increase-amount @if ($poinDelta < 0) down @endif"><span
                    class="plus-sign">{{ $poinDelta >= 0 ? '+' : '-' }}</span>{{ number_format(abs($poinDelta), 0, ',', '.') }}
            </div>
        </div>
    </div>

    <!-- Row 2: Waste & Inventory Metrics -->
    <div class="kpi-grid"
        style="display:grid;grid-template-columns:repeat(3,1fr);gap:1rem;margin-bottom:1rem;">
        <!-- Total Sampah (Kg) -->
        <div class="kpi-card-ref">
            <div class="percentage-badge @if ($sampahKgDelta < 0) down @endif">
                @if ($sampahKgDelta < 0)
                    <span class="arrow-down"></span>
                @else
                    <span class="arrow-up"></span>
                @endif
                {{ number_format(abs($sampahKgPercent), 1) }}%
            </div>
            <div class="header-row">
                <div class="icon-container">
                    <i class="fa-solid fa-trash-arrow-up icon-fa"></i>
                </div>
                <h1 class="title">Total Sampah (Kg)</h1>
            </div>
            <div class="main-amount">{{ number_format($totalSampahKg, 2, ',', '.') }} Kg</div>
            <div class="increase-amount @if ($sampahKgDelta < 0) down @endif"><span
                    class="plus-sign">{{ $sampahKgDelta >= 0 ? '+' : '-' }}</span>{{ number_format(abs($sampahKgDelta), 2, ',', '.') }}
            </div>
        </div>
        <!-- Total Sampah (Unit) -->
        <div class="kpi-card-ref">
            <div class="percentage-badge @if ($sampahUnitDelta < 0) down @endif">
                @if ($sampahUnitDelta < 0)
                    <span class="arrow-down"></span>
                @else
                    <span class="arrow-up"></span>
                @endif
                {{ number_format(abs($sampahUnitPercent), 1) }}%
            </div>
            <div class="header-row">
                <div class="icon-container">
                    <i class="fa-solid fa-trash-arrow-up icon-fa"></i>
                </div>
                <h1 class="title">Total Sampah (Unit)</h1>
            </div>
            <div class="main-amount">{{ number_format($totalSampahUnit, 0, ',', '.') }} Unit</div>
            <div class="increase-amount @if ($sampahUnitDelta < 0) down @endif"><span
                    class="plus-sign">{{ $sampahUnitDelta >= 0 ? '+' : '-' }}</span>{{ number_format(abs($sampahUnitDelta), 0, ',', '.') }}
            </div>
        </div>
        <!-- Sampah Tersimpan -->
        <div class="kpi-card-ref">
            <div class="percentage-badge" style="background-color: #e3f8fc; color: #0891b2;">
                <i class="fa-solid fa-warehouse" style="font-size: 10px;"></i>
            </div>
            <div class="header-row">
                <div class="icon-container" style="background: linear-gradient(135deg, #0891b2, #06b6d4);">
                    <i class="fa-solid fa-box icon-fa"></i>
                </div>
                <h1 class="title">Sampah Tersimpan</h1>
            </div>
            <div class="main-amount">{{ number_format($totalSampahTersimpan, 2, ',', '.') }} Kg</div>
            <div class="increase-amount" style="font-size: 0.7rem; color: #888;">
                Stok tersedia
            </div>
        </div>
    </div>

    <!-- Row 3: Financial Metrics -->
    <div style="margin-top:1.7rem;margin-bottom:2rem;">
        <h3 style="font-size:1.1rem;font-weight:700;color:#1f2937;margin-bottom:1rem;">
            <i class="fa-solid fa-chart-line"></i> Ringkasan Finansial
        </h3>
        <div class="financial-metrics-grid" style="display:grid;grid-template-columns:repeat(3,1fr);gap:1.5rem;">
            <!-- Total Pembelian -->
            <div class="financial-card">
                <div class="financial-card-header">
                    <div class="financial-icon" style="background: linear-gradient(135deg, #0fb7a6, #0da594);">
                        <i class="fa-solid fa-cart-shopping"></i>
                    </div>
                    <div class="financial-title-group">
                        <h4 class="financial-label">Total Pembelian</h4>
                        <p class="financial-description">Pembelian sampah dari nasabah</p>
                    </div>
                </div>
                <div class="financial-amount">Rp{{ number_format($totalPembelian, 0, ',', '.') }}</div>
                <div class="financial-footer">
                    <div class="financial-change @if($pembelianDelta >= 0) positive @else negative @endif">
                        <i class="fa-solid fa-arrow-{{ $pembelianDelta >= 0 ? 'up' : 'down' }}"></i>
                        <span>{{ $pembelianDelta >= 0 ? '+' : '' }}Rp{{ number_format(abs($pembelianDelta), 0, ',', '.') }}</span>
                    </div>
                    <div class="financial-percentage @if($pembelianDelta >= 0) positive @else negative @endif">
                        {{ number_format(abs($pembelianPercent), 1) }}%
                    </div>
                </div>
            </div>

            <!-- Total Penjualan -->
            <div class="financial-card">
                <div class="financial-card-header">
                    <div class="financial-icon" style="background: linear-gradient(135deg, #10b981, #059669);">
                        <i class="fa-solid fa-hand-holding-dollar"></i>
                    </div>
                    <div class="financial-title-group">
                        <h4 class="financial-label">Total Penjualan</h4>
                        <p class="financial-description">Penjualan ke offtaker</p>
                    </div>
                </div>
                <div class="financial-amount">Rp{{ number_format($totalPenjualan, 0, ',', '.') }}</div>
                <div class="financial-footer">
                    <div class="financial-change @if($penjualanDelta >= 0) positive @else negative @endif">
                        <i class="fa-solid fa-arrow-{{ $penjualanDelta >= 0 ? 'up' : 'down' }}"></i>
                        <span>{{ $penjualanDelta >= 0 ? '+' : '' }}Rp{{ number_format(abs($penjualanDelta), 0, ',', '.') }}</span>
                    </div>
                    <div class="financial-percentage @if($penjualanDelta >= 0) positive @else negative @endif">
                        {{ number_format(abs($penjualanPercent), 1) }}%
                    </div>
                </div>
            </div>

            <!-- Laba Kotor -->
            <div class="financial-card">
                <div class="financial-card-header">
                    <div class="financial-icon" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
                        <i class="fa-solid fa-money-bill-trend-up"></i>
                    </div>
                    <div class="financial-title-group">
                        <h4 class="financial-label">Laba Kotor</h4>
                        <p class="financial-description">Penjualan - Pembelian</p>
                    </div>
                </div>
                <div class="financial-amount @if($labaKotor < 0) negative-amount @endif">
                    Rp{{ number_format($labaKotor, 0, ',', '.') }}
                </div>
                <div class="financial-footer">
                    <div class="financial-metric">
                        <span class="metric-label">Margin:</span>
                        <span class="metric-value">
                            @if($totalPenjualan > 0)
                                {{ number_format($labaKotor / $totalPenjualan * 100, 1) }}%
                            @else
                                0%
                            @endif
                        </span>
                    </div>
                    <div class="financial-status @if($labaKotor >= 0) profitable @else loss @endif">
                        <i class="fa-solid fa-circle"></i>
                        {{ $labaKotor >= 0 ? 'Untung' : 'Rugi' }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ================= COMPARISON CHART SECTION ================= -->
    <div style="display:flex;gap:1.5rem;align-items:stretch;flex-wrap:wrap;">
        <div style="flex:2 1 400px;min-width:320px;display:flex;flex-direction:column;">
            <!-- Comparison Chart Section (existing) -->
            <div class="comparison-chart-section"
                style="background:#fff;border-radius:14px;padding:2rem;height:100%;box-shadow:0 2px 10px rgba(0,0,0,0.06);">
                <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:2rem;">
                    <div>
                        <div style="font-size:1rem;color:#374151;font-weight:500;margin-bottom:1rem;">Perbandingan</div>
                        <div style="font-size:0.8rem;color:#374151;font-weight:500;">Total Pembelian</div>
                        <div style="color:#0fb7a6;font-size:2rem;font-weight:700;margin:0;">
                            Rp{{ number_format($dashboardSummary['nilai_setoran'] ?? 0, 0, ',', '.') }}</div>
                    </div>
                    <div style="display:flex;gap:1rem;align-items:center;">
                        <div style="display:flex;align-items:center;gap:0.5rem;">
                            <div style="width:12px;height:12px;background:#0fb7a6;border-radius:2px;"></div>
                            <span style="color:#9ca3af;font-size:0.875rem;">{{ $legendCurrent ?? '' }}</span>
                        </div>
                        <div style="display:flex;align-items:center;gap:0.5rem;">
                            <div style="width:12px;height:12px;background:#065f46;border-radius:2px;"></div>
                            <span style="color:#9ca3af;font-size:0.875rem;">{{ $legendPrevious ?? '' }}</span>
                        </div>
                    </div>
                </div>

                <div style="position:relative;height:300px;">
                    <canvas id="comparisonChart" style="width:100%;height:100%;"></canvas>
                </div>
            </div>
        </div>
        <!-- tCO₂e Hero Card -->
        <div style="flex:1 1 280px;min-width:240px;max-width:360px;">
            <div class="tco2e-hero-card">
                <!-- Info Tooltip Trigger -->
                <div class="tco2e-info-trigger" tabindex="0">
                    <i class="fa-solid fa-circle-info" style="color:#10b981;font-size:16px;"></i>
                    <div class="tco2e-tooltip">
                        <strong style="color:#065f46;">Rumus Perhitungan CO₂e</strong>
                        <p style="margin:0.5rem 0;color:#374151;">
                            <strong>Small City Model:</strong><br>
                            Total Sampah (kg) × 1,18305 / 1000 = tCO₂e
                        </p>
                        <p style="margin:0.5rem 0;color:#6b7280;font-size:0.78rem;">
                            Multiplier 1,18305 = 1 + (EF_CH₄ × GWP_CH₄) + (EF_N₂O × GWP_N₂O)<br>
                            = 1 + (0,0065 × 21) + (0,00015 × 310)
                        </p>
                        <p style="margin:0.5rem 0;color:#6b7280;font-size:0.78rem;">
                            <strong>Konstanta:</strong><br>
                            • GWP CH₄ = 21 (kota kecil)<br>
                            • GWP N₂O = 310 (kota kecil)<br>
                            • EF CH₄ = 0,0065<br>
                            • EF N₂O = 0,00015
                        </p>
                        <p style="margin-top:0.75rem;color:#9ca3af;font-size:0.72rem;">
                            Sumber: IPCC Guidelines for Small Cities
                        </p>
                    </div>
                </div>

                <!-- Header -->
                <div class="tco2e-header">
                    <div class="tco2e-icon">
                        <i class="fa-solid fa-earth-americas"></i>
                    </div>
                    <span class="tco2e-title">Dampak Lingkungan</span>
                </div>

                <!-- Circular Progress Ring with Value -->
                <div class="tco2e-ring-container">
                    <svg class="tco2e-ring" viewBox="0 0 128 128">
                        <defs>
                            <linearGradient id="tco2e-gradient" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" style="stop-color:#10b981"/>
                                <stop offset="100%" style="stop-color:#059669"/>
                            </linearGradient>
                        </defs>
                        <circle class="tco2e-ring-bg" cx="64" cy="64" r="60"/>
                        <circle class="tco2e-ring-progress" cx="64" cy="64" r="60"/>
                    </svg>
                    <div class="tco2e-value-container tco2e-animated">
                        <div class="tco2e-value" data-target="{{ $co2SavedTon }}">
                            {{ number_format($co2SavedTon, 2, ',', '.') }}
                        </div>
                        <div class="tco2e-unit">tCO₂e</div>
                    </div>
                </div>

                <!-- Description -->
                <div class="tco2e-description">
                    Emisi karbon yang berhasil dicegah
                </div>
            </div>
        </div>
    </div>

    <!-- ================= 2 CARD: 5 TRANSAKSI TERAKHIR & 5 TOP SAMPAH ================= -->
    <div style="display:flex;gap:1.5rem;align-items:stretch;flex-wrap:wrap;margin-top:1.5rem;width:100%;">
        <!-- Card 5 Transaksi Terakhir (lebih lebar) -->
        <div
            style="flex:2 1 0;min-width:260px;background:#fff;border-radius:10px;box-shadow:0 2px 8px rgba(0,0,0,0.04);padding:1rem 0.7rem 0.7rem 0.7rem;display:flex;flex-direction:column;">
            <div
                style="font-size:0.98rem;font-weight:700;color:#1f2937;margin-bottom:0.7rem;display:flex;align-items:center;gap:0.4rem;">
                <i class="fa-solid fa-receipt" style="color:#2dd4bf;font-size:1em;"></i>Transaksi Terakhir
            </div>
            <div style="overflow-x:auto;">
                <table style="width:100%;border-collapse:collapse;font-size:0.89em;">
                    <thead>
                        <tr style="color:#6b7280;font-weight:600;text-align:left;">
                            <th style="padding:4px 2px;">Tanggal</th>
                            <th style="padding:4px 2px;">User</th>
                            <th style="padding:4px 2px;">Tipe</th>
                            <th style="padding:4px 2px;">Total</th>
                            <th style="padding:4px 2px;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($lastTransactions ?? [] as $trx)
                            <tr style="border-top:1px solid #f3f4f6;">
                                <td style="padding:4px 2px;white-space:nowrap;">
                                    {{ $trx->created_at->format('d M Y H:i') }}</td>
                                <td style="padding:4px 2px;">{{ $trx->user->name ?? '-' }}</td>
                                <td style="padding:4px 2px;">
                                    {{ method_exists($trx, 'getTipeSetorTextAttribute') ? $trx->tipe_setor_text : ucfirst($trx->tipe_setor) }}
                                </td>
                                <td style="padding:4px 2px;">
                                    Rp{{ number_format($trx->aktual_total ?? $trx->estimasi_total, 0, ',', '.') }}</td>
                                <td style="padding:4px 2px;">
                                    <span
                                        style="font-size:0.93em;font-weight:600;color:
                  @if ($trx->status == 'selesai' || $trx->status == 'berhasil') #22c55e
                  @elseif($trx->status == 'batal')#ef4444
                  @else #6b7280 @endif;">
                                        {{ ucfirst($trx->status) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                        @if (empty($lastTransactions) || count($lastTransactions) == 0)
                            <tr>
                                <td colspan="5" style="padding:8px 0;color:#9ca3af;text-align:center;">Belum ada
                                    transaksi</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
        <!-- Card Top User Setor (lebih ramping) -->
        <div
            style="flex:1.5 1 0;min-width:180px;background:#fff;border-radius:10px;box-shadow:0 2px 8px rgba(0,0,0,0.04);padding:1rem 0.7rem 0.7rem 0.7rem;display:flex;flex-direction:column;">
            <div
                style="font-size:0.98rem;font-weight:700;color:#1f2937;margin-bottom:0.7rem;display:flex;align-items:center;gap:0.4rem;">
                <i class="fa-solid fa-user" style="color:#2dd4bf;font-size:1em;"></i>Top User Setor
            </div>
            <div style="overflow-x:auto;">
                <table style="width:100%;border-collapse:collapse;font-size:0.89em;">
                    <thead>
                        <tr style="color:#6b7280;font-weight:600;text-align:left;">
                            <th style="padding:4px 2px;">Nama User</th>
                            <th style="padding:4px 2px;">Setoran</th>
                            <th style="padding:4px 2px;">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($topUserSetor ?? [] as $user)
                            <tr style="border-top:1px solid #f3f4f6;">
                                <td style="padding:4px 2px;">{{ $user['name'] ?? '-' }}</td>
                                <td style="padding:4px 2px;">{{ $user['total_setoran'] }}</td>
                                <td style="padding:4px 2px;">Rp{{ number_format($user['total_nilai'], 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                        @if (empty($topUserSetor) || count($topUserSetor) == 0)
                            <tr>
                                <td colspan="3" style="padding:8px 0;color:#9ca3af;text-align:center;">Belum ada data
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Card Top Sampah Setor (lebih ramping) -->
        <div
            style="flex:1.3 1 0;min-width:180px;background:#fff;border-radius:10px;box-shadow:0 2px 8px rgba(0,0,0,0.04);padding:1rem 0.7rem 0.7rem 0.7rem;display:flex;flex-direction:column;">
            <div
                style="font-size:0.98rem;font-weight:700;color:#1f2937;margin-bottom:0.7rem;display:flex;align-items:center;gap:0.4rem;">
                <i class="fa-solid fa-dumpster" style="color:#2dd4bf;font-size:1em;"></i>Top Sampah Disetor
            </div>
            <div style="overflow-x:auto;">
                <table style="width:100%;border-collapse:collapse;font-size:0.89em;">
                    <thead>
                        <tr style="color:#6b7280;font-weight:600;text-align:left;">
                            <th style="padding:4px 2px;">Gambar</th>
                            <th style="padding:4px 2px;">Nama</th>
                            <th style="padding:4px 2px;">Berat</th>
                            <th style="padding:4px 2px;">Transaksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($topSampahSetor ?? [] as $item)
                            <tr style="border-top:1px solid #f3f4f6;">
                                <td style="padding:4px 2px;text-align:center;">
                                    @if (!empty($item['gambar']))
                                        <img src="{{ $item['gambar'] }}" alt="{{ $item['nama'] }}"
                                            style="width:22px;height:22px;object-fit:cover;border-radius:50%;background:#f3f4f6;">
                                    @else
                                        <span
                                            style="width:22px;height:22px;display:inline-flex;align-items:center;justify-content:center;border-radius:50%;background:#f3f4f6;color:#a3a3a3;font-size:1em;">
                                            <i class="fa-solid fa-recycle"></i>
                                        </span>
                                    @endif
                                </td>
                                <td style="padding:4px 2px;">{{ $item['nama'] ?? '-' }}</td>
                                <td style="padding:4px 2px;">{{ number_format($item['total_berat'], 2, ',', '.') }} <span
                                        style="color:#6b7280;font-size:0.97em;">{{ $item['satuan'] ?? (str_contains(strtolower($item['nama'] ?? ''), 'unit') ? 'unit' : 'kg') }}</span>
                                </td>
                                <td style="padding:4px 2px;">{{ $item['jumlah_transaksi'] }}</td>
                            </tr>
                        @endforeach
                        @if (empty($topSampahSetor) || count($topSampahSetor) == 0)
                            <tr>
                                <td colspan="4" style="padding:8px 0;color:#9ca3af;text-align:center;">Belum ada data
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Responsive helper -->
    <style>
        @media (max-width: 900px) {

            .dashboard-header-bar,
            .dashboard-kpi-bar {
                padding: 0.7rem 0.3rem !important;
            }

            .dashboard-kpi-bar {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (max-width: 600px) {
            .dashboard-kpi-bar {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // ================= PERBAIKAN SCRIPT CHART =================

        // 1. Pastikan semua elemen chart ada sebelum menginisialisasi
        document.addEventListener('DOMContentLoaded', function() {

            // Chart: Tren Setoran - Tambahkan pengecekan elemen
            const trendChart = document.getElementById('chart-setoran-tren');
            if (trendChart) {
                new Chart(trendChart, {
                    type: 'line',
                    data: {
                        labels: @json($trendDays ?? []),
                        datasets: [{
                            label: 'Setoran Selesai',
                            data: @json($trendValues ?? []),
                            borderColor: '#00B6A0',
                            backgroundColor: 'rgba(0,182,160,0.12)',
                            fill: true,
                            tension: 0.3,
                            pointRadius: 2,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            }

            // Chart: Distribusi Status - Tambahkan pengecekan elemen
            const statusChart = document.getElementById('chart-status-setoran');
            if (statusChart) {
                new Chart(statusChart, {
                    type: 'doughnut',
                    data: {
                        labels: @json($statusLabels ?? []),
                        datasets: [{
                            data: @json($statusCounts ?? []),
                            backgroundColor: ['#00B6A0', '#3B82F6', '#F59E0B', '#10B981',
                                '#EF4444'],
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }
                });
            }

            // Chart: Top 5 Jenis Sampah - Tambahkan pengecekan elemen
            const topSampahChart = document.getElementById('chart-top-sampah');
            if (topSampahChart) {
                new Chart(topSampahChart, {
                    type: 'bar',
                    data: {
                        labels: @json(array_keys($topSampah ?? [])),
                        datasets: [{
                            label: 'Total (kg/unit)',
                            data: @json(array_values($topSampah ?? [])),
                            backgroundColor: '#00B6A0',
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            }

            // Comparison Chart - Perbaikan dengan validasi data yang lebih baik
            const comparisonChartEl = document.getElementById('comparisonChart');
            if (comparisonChartEl) {
                const comparisonData = @json($comparisonData ?? null);

                // Validasi data comparison
                if (comparisonData &&
                    comparisonData.current &&
                    comparisonData.previous &&
                    Array.isArray(comparisonData.current) &&
                    Array.isArray(comparisonData.previous) &&
                    comparisonData.current.length > 0) {

                    const currentLabels = comparisonData.current.map(item => item.label || '');
                    const currentValues = comparisonData.current.map(item => parseFloat(item.value) || 0);
                    const previousValues = comparisonData.previous.map(item => parseFloat(item.value) || 0);

                    new Chart(comparisonChartEl, {
                        type: 'line',
                        data: {
                            labels: currentLabels,
                            datasets: [{
                                    label: '{{ $legendCurrent ?? 'Periode Sekarang' }}',
                                    data: currentValues,
                                    borderColor: '#0fb7a6',
                                    backgroundColor: 'rgba(15, 183, 166, 0.1)',
                                    fill: true,
                                    tension: 0.4,
                                    pointRadius: 3,
                                    pointHoverRadius: 6,
                                    pointHoverBackgroundColor: '#0fb7a6',
                                    pointHoverBorderColor: '#fff',
                                    pointHoverBorderWidth: 2,
                                },
                                {
                                    label: '{{ $legendPrevious ?? 'Periode Sebelumnya' }}',
                                    data: previousValues,
                                    borderColor: '#065f46',
                                    backgroundColor: 'rgba(6, 95, 70, 0.1)',
                                    fill: true,
                                    tension: 0.4,
                                    pointRadius: 3,
                                    pointHoverRadius: 6,
                                    pointHoverBackgroundColor: '#065f46',
                                    pointHoverBorderColor: '#fff',
                                    pointHoverBorderWidth: 2,
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            interaction: {
                                intersect: false,
                                mode: 'index',
                            },
                            plugins: {
                                legend: {
                                    display: false
                                },
                                tooltip: {
                                    backgroundColor: '#fff',
                                    titleColor: '#1f2937',
                                    bodyColor: '#1f2937',
                                    borderColor: '#e5e7eb',
                                    borderWidth: 1,
                                    cornerRadius: 8,
                                    displayColors: true,
                                    callbacks: {
                                        title: function(context) {
                                            return context[0].label;
                                        },
                                        label: function(context) {
                                            return context.dataset.label + ': Rp' + context.parsed.y
                                                .toLocaleString('id-ID');
                                        }
                                    }
                                }
                            },
                            scales: {
                                x: {
                                    grid: {
                                        display: false
                                    },
                                    ticks: {
                                        color: '#9ca3af',
                                        font: {
                                            size: 12
                                        }
                                    }
                                },
                                y: {
                                    beginAtZero: true,
                                    grid: {
                                        color: 'rgba(156, 163, 175, 0.1)'
                                    },
                                    ticks: {
                                        color: '#9ca3af',
                                        font: {
                                            size: 12
                                        },
                                        callback: function(value) {
                                            return 'Rp' + value.toLocaleString('id-ID');
                                        }
                                    }
                                }
                            }
                        }
                    });
                } else {
                    // Jika tidak ada data, tampilkan pesan
                    comparisonChartEl.parentElement.innerHTML = `
                <div style="display:flex;align-items:center;justify-content:center;height:300px;color:#9ca3af;font-size:0.875rem;">
                    <div style="text-align:center;">
                        <i class="fas fa-chart-line" style="font-size:2rem;margin-bottom:1rem;opacity:0.5;"></i>
                        <div>Data comparison tidak tersedia</div>
                    </div>
                </div>
            `;
                }
            }
        });

        // tCO2e Counter Animation
        (function() {
            const valueEl = document.querySelector('.tco2e-value');
            if (!valueEl) return;

            const target = parseFloat(valueEl.dataset.target) || 0;
            const duration = 1500;
            const startTime = performance.now();

            function animate(currentTime) {
                const elapsed = currentTime - startTime;
                const progress = Math.min(elapsed / duration, 1);

                // Easing function (ease-out cubic)
                const easeOut = 1 - Math.pow(1 - progress, 3);
                const current = target * easeOut;

                valueEl.textContent = current.toLocaleString('id-ID', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });

                if (progress < 1) {
                    requestAnimationFrame(animate);
                }
            }

            requestAnimationFrame(animate);
        })();
    </script>
@endsection
