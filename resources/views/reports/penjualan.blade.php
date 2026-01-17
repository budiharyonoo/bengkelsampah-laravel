@extends('dashboard')
@section('content')
@include('partials.dashboard-header-bar', ['title' => 'Laporan Penjualan'])
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Penjualan - Admin Panel</title>
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
            align-items: center;
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

        .btn-primary {
            background: #387478;
            color: white;
        }

        .btn-primary:hover {
            background: #2d5a5d;
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

        .btn-outline {
            background: transparent;
            border: 1px solid #E5E6E6;
            color: #374151;
        }

        .btn-outline:hover {
            background: #f9fafb;
        }

        .btn-outline.active {
            background: #387478;
            color: white;
            border-color: #387478;
        }

        .toggle-group {
            display: flex;
            gap: 0;
        }

        .toggle-group .btn {
            border-radius: 0;
        }

        .toggle-group .btn:first-child {
            border-radius: 8px 0 0 8px;
        }

        .toggle-group .btn:last-child {
            border-radius: 0 8px 8px 0;
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

        .text-right {
            text-align: right !important;
        }

        .text-green {
            color: #22c55e;
        }

        .text-red {
            color: #ef4444;
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

        .badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
        }

        .badge-sale {
            background: #dcfce7;
            color: #166534;
        }

        .badge-processing {
            background: #fef3c7;
            color: #92400e;
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
                    <option value="6bulanan" {{ $periode == '6bulanan' ? 'selected' : '' }}>6 Bulan</option>
                    <option value="tahunan" {{ $periode == 'tahunan' ? 'selected' : '' }}>Tahunan</option>
                </select>
                <select name="bank_sampah_id" class="filter-select" onchange="this.form.submit()">
                    <option value="">Semua Bank Sampah</option>
                    @foreach($bankSampahList as $bs)
                        <option value="{{ $bs->id }}" {{ $selectedBankSampah == $bs->id ? 'selected' : '' }}>{{ $bs->nama_bank_sampah }}</option>
                    @endforeach
                </select>
                <div class="toggle-group">
                    <button type="submit" name="group_by" value="offtaker" class="btn btn-outline {{ $groupBy == 'offtaker' ? 'active' : '' }}">
                        <i class="fa-solid fa-building"></i> Per Offtaker
                    </button>
                    <button type="submit" name="group_by" value="sampah" class="btn btn-outline {{ $groupBy == 'sampah' ? 'active' : '' }}">
                        <i class="fa-solid fa-trash"></i> Per Jenis Sampah
                    </button>
                </div>
            </form>

            <div class="btn-group">
                <form method="POST" action="{{ route('reports.penjualan.export.excel') }}" style="display:inline;">
                    @csrf
                    <input type="hidden" name="periode" value="{{ $periode }}">
                    <input type="hidden" name="bank_sampah_id" value="{{ $selectedBankSampah }}">
                    <input type="hidden" name="group_by" value="{{ $groupBy }}">
                    <button type="submit" class="btn btn-success">
                        <i class="fa-solid fa-file-excel"></i> Export Excel
                    </button>
                </form>
                <form method="POST" action="{{ route('reports.penjualan.export.pdf') }}" style="display:inline;">
                    @csrf
                    <input type="hidden" name="periode" value="{{ $periode }}">
                    <input type="hidden" name="bank_sampah_id" value="{{ $selectedBankSampah }}">
                    <input type="hidden" name="group_by" value="{{ $groupBy }}">
                    <button type="submit" class="btn btn-danger">
                        <i class="fa-solid fa-file-pdf"></i> Export PDF
                    </button>
                </form>
            </div>
        </div>

        <h3 class="section-title">
            Ringkasan Penjualan per {{ $groupBy == 'offtaker' ? 'Offtaker' : 'Jenis Sampah' }}
        </h3>

        @if(count($data) > 0)
            <table class="summary-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>{{ $groupBy == 'offtaker' ? 'Offtaker' : 'Jenis Sampah' }}</th>
                        <th class="text-right">Jumlah Transaksi</th>
                        <th class="text-right">Total Qty (Kg)</th>
                        <th class="text-right">Total Penjualan (Rp)</th>
                        <th class="text-right">Laba (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    @php $totalQty = 0; $totalValue = 0; $totalProfit = 0; @endphp
                    @foreach($data as $index => $item)
                        @php
                            $totalQty += $item['total_quantity'] ?? 0;
                            $totalValue += $item['total_value'] ?? 0;
                            $totalProfit += $item['profit'] ?? 0;
                        @endphp
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item['name'] ?? '-' }}</td>
                            <td class="text-right">{{ $item['count'] ?? 0 }}</td>
                            <td class="text-right">{{ number_format($item['total_quantity'] ?? 0, 2, ',', '.') }}</td>
                            <td class="text-right">{{ number_format($item['total_value'] ?? 0, 0, ',', '.') }}</td>
                            <td class="text-right {{ ($item['profit'] ?? 0) >= 0 ? 'text-green' : 'text-red' }}">
                                {{ number_format($item['profit'] ?? 0, 0, ',', '.') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr style="background: #f3f4f6; font-weight: 600;">
                        <td colspan="3">Total</td>
                        <td class="text-right">{{ number_format($totalQty, 2, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($totalValue, 0, ',', '.') }}</td>
                        <td class="text-right {{ $totalProfit >= 0 ? 'text-green' : 'text-red' }}">
                            {{ number_format($totalProfit, 0, ',', '.') }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        @else
            <div class="empty-state">
                <i class="fa-solid fa-chart-line"></i>
                <p>Tidak ada data penjualan untuk periode ini</p>
            </div>
        @endif

        <h3 class="section-title">Transaksi Terbaru</h3>

        @if(count($recentTransactions) > 0)
            <table class="detail-table">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Tanggal</th>
                        <th>Tipe</th>
                        <th>Offtaker</th>
                        <th>Bank Sampah</th>
                        <th class="text-right">Total (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentTransactions as $trx)
                        <tr>
                            <td>{{ $trx->kode_transaksi }}</td>
                            <td>{{ $trx->tanggal_transaksi ? \Carbon\Carbon::parse($trx->tanggal_transaksi)->format('d M Y') : '-' }}</td>
                            <td>
                                <span class="badge {{ $trx->tipe == 'sale' ? 'badge-sale' : 'badge-processing' }}">
                                    {{ $trx->tipe == 'sale' ? 'Penjualan' : 'Pengolahan' }}
                                </span>
                            </td>
                            <td>{{ $trx->offtaker->nama ?? '-' }}</td>
                            <td>{{ $trx->bankSampah->nama_bank_sampah ?? '-' }}</td>
                            <td class="text-right">{{ number_format($trx->total_value, 0, ',', '.') }}</td>
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
</body>
</html>
@endsection
