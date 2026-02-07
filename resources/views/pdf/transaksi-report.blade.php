<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Transaksi Bank Sampah - Advanced Analytics</title>
    <style>
        @page {
            margin: 1.5cm;
            size: A4 landscape;
        }
        
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 0;
        }
        
        .header {
            text-align: center;
            margin-bottom: 25px;
            border-bottom: 4px solid #39746E;
            padding-bottom: 15px;
            position: relative;
        }
        
        .header::before {
            content: '';
            position: absolute;
            bottom: -4px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: #e74c3c;
        }
        
        .header h1 {
            color: #39746E;
            font-size: 28px;
            font-weight: 900;
            margin: 0 0 8px 0;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        
        .header h2 {
            font-size: 16px;
            color: #666;
            font-weight: 600;
            margin: 0 0 5px 0;
        }
        
        .header-info {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 18px;
            font-size: 10px;
            color: #666;
            margin-top: 10px;
        }
        
        .header-info span {
            white-space: nowrap;
        }
        
        .summary-table {
            background: #f8f9fa;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .summary-table .stat-td {
            padding: 15px 0 8px 0;
            vertical-align: middle;
            text-align: center;
            background: none;
            border: none;
            border-right: 1.5px solid #e0e0e0;
        }
        .summary-table tr td:last-child {
            border-right: none !important;
        }
        .summary-table .stat-number {
            font-size: 2.2em;
            font-weight: bold;
            color: #39746E;
            margin-bottom: 4px;
            display: block;
            line-height: 1.1;
        }
        .summary-table .stat-label {
            font-size: 0.9em;
            color: #666;
            font-weight: 500;
            line-height: 1.2;
            margin-bottom: 0;
        }
        
        .analytics-table {
            border-collapse: separate;
            border-spacing: 15px 10px;
            width: 100%;
            margin-bottom: 20px;
        }
        .analytics-card {
            background: #fff;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 15px;
            vertical-align: top;
            box-shadow: 0 2px 8px rgba(0,0,0,0.07);
        }
        
        .analytics-card h3 {
            color: #39746E;
            font-size: 10px;
            font-weight: bold;
            margin: 0 0 12px 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #39746E;
            padding-bottom: 5px;
        }
        
        .analytics-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 6px 0;
            border-bottom: 1px solid #f0f0f0;
            font-size: 10px;
        }
        
        .analytics-item:last-child {
            border-bottom: none;
        }
        
        .analytics-label {
            font-weight: 600;
            color: #555;
        }
        
        .analytics-value {
            font-weight: bold;
            color: #39746E;
        }
        
        .progress-bar {
            width: 100%;
            height: 8px;
            background: #e9ecef;
            border-radius: 4px;
            overflow: hidden;
            margin-top: 4px;
        }
        
        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #39746E, #28a745);
            border-radius: 4px;
            transition: width 0.3s ease;
        }
        
        .table-container {
            margin-top: 20px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            font-size: 8px;
            background: white;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            border-radius: 8px;
            overflow: hidden;
        }
        
        th {
            background-color: #39746E !important;
            color: #ffffff !important;
            padding: 8px 4px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #ddd;
            font-size: 8px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        
        td {
            padding: 6px 4px;
            border: 1px solid #ddd;
            vertical-align: top;
            font-size: 7px;
        }
        
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        tr:hover {
            background-color: #e8f5e8;
        }
        
        .status {
            padding: 2px 6px;
            border-radius: 10px;
            font-size: 6px;
            font-weight: bold;
            text-transform: uppercase;
            text-align: center;
            color: white;
            display: inline-block;
            min-width: 50px;
        }
        
        .status-selesai { background: #28a745; }
        .status-dijemput { background: #17a2b8; }
        .status-diproses { background: #ffc107; color: #333; }
        .status-dikonfirmasi { background: #6f42c1; }
        .status-batal { background: #dc3545; }
        
        .tipe-setor {
            padding: 2px 4px;
            border-radius: 3px;
            font-size: 6px;
            font-weight: bold;
            text-transform: uppercase;
            display: inline-block;
            min-width: 40px;
            text-align: center;
        }
        
        .tipe-jual { background: #d4edda; color: #155724; }
        .tipe-tabung { background: #cce5f0; color: #0c5460; }
        .tipe-sedekah { background: #f8d7da; color: #721c24; }
        
        .currency {
            text-align: right;
            font-weight: bold;
            color: #39746E;
        }
        
        .items-detail {
            font-size: 6px;
            color: #666;
            max-width: 100px;
        }
        
        .items-detail .item {
            margin-bottom: 2px;
            padding: 1px 3px;
            background: #f1f3f4;
            border-radius: 2px;
            border-left: 2px solid #39746E;
        }
        
        .item-deleted {
            background: #f8d7da !important;
            border-left-color: #dc3545 !important;
            color: #721c24;
        }
        
        .item-added {
            background: #d4edda !important;
            border-left-color: #28a745 !important;
            color: #155724;
        }
        
        .item-modified {
            background: #fff3cd !important;
            border-left-color: #ffc107 !important;
            color: #856404;
        }
        
        .footer {
            margin-top: 25px;
            text-align: center;
            font-size: 9px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 12px;
        }
        
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        
        .no-data {
            text-align: center;
            padding: 30px;
            color: #666;
            font-style: italic;
        }
        
        .user-info {
            font-size: 7px;
        }
        
        .user-name {
            font-weight: bold;
            color: #39746E;
        }
        
        .user-identifier {
            font-size: 6px;
            color: #666;
        }
        
        .bank-info {
            font-size: 7px;
        }
        
        .bank-name {
            font-weight: bold;
            color: #39746E;
        }
        
        .bank-details {
            font-size: 5px;
            color: #999;
        }
        
        .address-info {
            font-size: 7px;
        }
        
        .address-name {
            font-weight: bold;
            color: #39746E;
        }
        
        .address-phone {
            font-size: 5px;
            color: #666;
        }
        
        .schedule-info {
            font-size: 5px;
            color: #666;
        }
        
        .completion-date {
            font-size: 5px;
            color: #28a745;
            font-weight: bold;
        }
        
        .performance-indicator {
            display: inline-block;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            margin-right: 4px;
        }
        
        .performance-excellent { background: #28a745; }
        .performance-good { background: #17a2b8; }
        .performance-average { background: #ffc107; }
        .performance-poor { background: #dc3545; }
        
        .trend-indicator {
            font-size: 6px;
            font-weight: bold;
        }
        
        .trend-up { color: #28a745; }
        .trend-down { color: #dc3545; }
        .trend-stable { color: #6c757d; }
        
        thead { display: table-header-group; }
        th {
            background-color: #39746E !important;
            color: #ffffff !important;
            font-weight: bold;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN TRANSAKSI BANK SAMPAH</h1>
        <div class="header-info">
            <span>
                <strong>Periode:</strong>
                @if(isset($filters['time_filter']) && $filters['time_filter'])
                    @if($filters['time_filter'] === 'harian')
                        Hari ini ({{ now()->format('d M Y') }})
                    @elseif($filters['time_filter'] === 'mingguan')
                        Minggu ini ({{ now()->startOfWeek()->format('d M Y') }} - {{ now()->endOfWeek()->format('d M Y') }})
                    @elseif($filters['time_filter'] === 'bulanan')
                        Bulan ini ({{ now()->format('F Y') }})
                    @elseif($filters['time_filter'] === 'range')
                        {{ $filters['start_date'] ?? '-' }} s/d {{ $filters['end_date'] ?? '-' }}
                    @endif
                @else
                    Semua Periode
                @endif
            </span>
            <span><strong>Dicetak:</strong> {{ now()->format('d/m/Y H:i:s') }}</span>
            <span><strong>Total Data:</strong> {{ $transactions->count() }} transaksi</span>
        </div>
    </div>

    @php
        // Advanced Analytics Calculations
        $totalEstimasi = $transactions->sum('estimasi_total');
        $totalAktual = $transactions->where('aktual_total', '!=', null)->sum('aktual_total');
        $transaksiSelesai = $transactions->where('status', 'selesai')->count();
        $transaksiProses = $transactions->whereIn('status', ['diproses', 'dijemput', 'dikonfirmasi'])->count();
        $transaksiBatal = $transactions->where('status', 'batal')->count();
        
        // Performance Metrics
        $completionRate = $transactions->count() > 0 ? ($transaksiSelesai / $transactions->count()) * 100 : 0;
        $cancellationRate = $transactions->count() > 0 ? ($transaksiBatal / $transactions->count()) * 100 : 0;
        $accuracyRate = $totalEstimasi > 0 ? (($totalAktual - $totalEstimasi) / $totalEstimasi) * 100 : 0;
        
        // Service Type Analysis
        $serviceTypes = $transactions->groupBy('tipe_layanan')->map->count();
        $setorTypes = $transactions->groupBy('tipe_setor')->map->count();
        
        // Bank Performance
        $bankPerformance = $transactions->groupBy('bank_sampah_name')
            ->map(function($group) {
                $completed = $group->where('status', 'selesai')->count();
                $total = $group->count();
                $rate = $total > 0 ? ($completed / $total) * 100 : 0;
                return [
                    'total' => $total,
                    'completed' => $completed,
                    'rate' => $rate,
                    'total_value' => $group->sum('estimasi_total')
                ];
            });
        
        // Top Performing Banks
        $topBanks = $bankPerformance->sortByDesc('rate')->take(3);
        
        // Item Analysis
        $allItems = collect();
        foreach($transactions as $transaction) {
            $items = json_decode($transaction->items_json, true) ?? [];
            foreach($items as $item) {
                $allItems->push($item);
            }
        }
        
        $itemStats = $allItems->groupBy('sampah_nama')
            ->map(function($group) {
                return [
                    'total_estimasi' => $group->sum('estimasi_berat'),
                    'total_aktual' => $group->where('aktual_berat', '!=', null)->sum('aktual_berat'),
                    'count' => $group->count(),
                    'avg_price' => $group->avg('harga_per_satuan')
                ];
            });
        
        // Time Analysis
        $avgCompletionTime = $transactions->where('status', 'selesai')
            ->where('tanggal_selesai', '!=', null)
            ->map(function($transaction) {
                return \Carbon\Carbon::parse($transaction->created_at)
                    ->diffInHours(\Carbon\Carbon::parse($transaction->tanggal_selesai));
            })->avg();

        // Hitung total estimasi dan realisasi per satuan
        $totalEstimasiKg = $allItems->where('sampah_satuan', 'KG')->sum('estimasi_berat');
        $totalAktualKg = $allItems->where('sampah_satuan', 'KG')->where('aktual_berat', '!=', null)->sum('aktual_berat');
        $totalEstimasiUnit = $allItems->where('sampah_satuan', 'UNIT')->sum('estimasi_berat');
        $totalAktualUnit = $allItems->where('sampah_satuan', 'UNIT')->where('aktual_berat', '!=', null)->sum('aktual_berat');
    @endphp

    <!-- Advanced Summary Statistics -->
    <table class="summary-table" style="width:100%; margin: 20px 0 25px 0; border-collapse: separate; border-spacing: 10px 0; background: #f8f9fa; border-radius: 8px;">
        <tr>
            <td class="stat-td">
                <div class="stat-number">{{ number_format($transactions->count()) }}</div>
                <div class="stat-label">Total Transaksi</div>
            </td>
            <td class="stat-td">
                <div class="stat-number">Rp {{ number_format($totalEstimasi) }}</div>
                <div class="stat-label">Estimasi Total (Rp)</div>
            </td>
            <td class="stat-td">
                <div class="stat-number">Rp {{ number_format($totalAktual) }}</div>
                <div class="stat-label">Realisasi Total (Rp)</div>
            </td>
            <td class="stat-td">
                <div class="stat-number">{{ number_format($totalEstimasiKg, 2) }} kg</div>
                <div class="stat-label">Estimasi Sampah (KG)</div>
            </td>
            <td class="stat-td">
                <div class="stat-number">{{ number_format($totalAktualKg, 2) }} kg</div>
                <div class="stat-label">Realisasi Sampah (KG)</div>
            </td>
            <td class="stat-td">
                <div class="stat-number">{{ number_format($totalEstimasiUnit, 2) }} unit</div>
                <div class="stat-label">Estimasi Sampah (UNIT)</div>
            </td>
            <td class="stat-td">
                <div class="stat-number">{{ number_format($totalAktualUnit, 2) }} unit</div>
                <div class="stat-label">Realisasi Sampah (UNIT)</div>
            </td>
        </tr>
    </table>

    <!-- Analytics Table 5 Columns, 1 Row -->
    <table class="analytics-table">
        <tr>
            <td class="analytics-card" style="width:20%">
                <h3>Performance Metrics</h3>
                <div class="analytics-item">
                    <span class="analytics-label">Completion Rate</span>
                    <span class="analytics-value">{{ number_format($completionRate, 1) }}%</span>
                </div>
                <div class="analytics-item">
                    <span class="analytics-label">Cancellation Rate</span>
                    <span class="analytics-value">{{ number_format($cancellationRate, 1) }}%</span>
                </div>
                <div class="analytics-item">
                    <span class="analytics-label">Accuracy Rate</span>
                    <span class="analytics-value {{ $accuracyRate > 0 ? 'trend-up' : ($accuracyRate < 0 ? 'trend-down' : 'trend-stable') }}">
                        {{ number_format($accuracyRate, 1) }}%
                        @if($accuracyRate > 0)
                            <span class="trend-indicator">↗</span>
                        @elseif($accuracyRate < 0)
                            <span class="trend-indicator">↘</span>
                        @else
                            <span class="trend-indicator">→</span>
                        @endif
                    </span>
                </div>
                <div class="analytics-item">
                    <span class="analytics-label">Avg Completion Time</span>
                    <span class="analytics-value">{{ number_format($avgCompletionTime ?? 0, 1) }} hours</span>
                </div>
            </td>
            <td class="analytics-card" style="width:20%">
                <h3>Service Type Analysis</h3>
                @foreach($serviceTypes as $type => $count)
                    <div class="analytics-item">
                        <span class="analytics-label">{{ ucfirst($type ?? 'Unknown') }}</span>
                        <span class="analytics-value">{{ $count }} ({{ number_format(($count / $transactions->count()) * 100, 1) }}%)</span>
                    </div>
                @endforeach
            </td>
            <td class="analytics-card" style="width:20%">
                <h3>Type Distribution</h3>
                @foreach($setorTypes as $type => $count)
                    <div class="analytics-item">
                        <span class="analytics-label">{{ ucfirst($type) }}</span>
                        <span class="analytics-value">{{ $count }} ({{ number_format(($count / $transactions->count()) * 100, 1) }}%)</span>
                    </div>
                @endforeach
            </td>
            <td class="analytics-card" style="width:20%">
                <h3>Transaction Status</h3>
                <div class="analytics-item">
                    <span class="analytics-label">Selesai</span>
                    <span class="analytics-value">{{ $transaksiSelesai }} ({{ number_format(($transaksiSelesai / $transactions->count()) * 100, 1) }}%)</span>
                </div>
                <div class="analytics-item">
                    <span class="analytics-label">Dalam Proses</span>
                    <span class="analytics-value">{{ $transaksiProses }} ({{ number_format(($transaksiProses / $transactions->count()) * 100, 1) }}%)</span>
                </div>
                <div class="analytics-item">
                    <span class="analytics-label">Dibatalkan</span>
                    <span class="analytics-value">{{ $transaksiBatal }} ({{ number_format(($transaksiBatal / $transactions->count()) * 100, 1) }}%)</span>
                </div>
                <div class="analytics-item">
                    <span class="analytics-label">Total Transaksi</span>
                    <span class="analytics-value">{{ $transactions->count() }}</span>
                </div>
            </td>
            <td class="analytics-card" style="width:20%">
                <h3>Financial Overview</h3>
                <div class="analytics-item">
                    <span class="analytics-label">Total Estimasi</span>
                    <span class="analytics-value">Rp {{ number_format($totalEstimasi) }}</span>
                </div>
                <div class="analytics-item">
                    <span class="analytics-label">Total Aktual</span>
                    <span class="analytics-value">Rp {{ number_format($totalAktual) }}</span>
                </div>
                <div class="analytics-item">
                    <span class="analytics-label">Selisih</span>
                    <span class="analytics-value {{ ($totalAktual - $totalEstimasi) > 0 ? 'trend-up' : 'trend-down' }}">
                        Rp {{ number_format($totalAktual - $totalEstimasi) }}
                        @if(($totalAktual - $totalEstimasi) > 0) ↗ @else ↘ @endif
                    </span>
                </div>
                <div class="analytics-item">
                    <span class="analytics-label">Rata-rata per Transaksi</span>
                    <span class="analytics-value">Rp {{ number_format($transactions->count() > 0 ? $totalAktual / $transactions->count() : 0) }}</span>
                </div>
            </td>
        </tr>
    </table>
    <!-- Bank Performa (full width) -->
    <div class="analytics-card" style="margin-bottom:15px;">
        <h3>Bank Performa</h3>
        <table style="width:100%;font-size:7px;margin-top:5px;">
            <thead>
                <tr>
                    <th>Bank</th>
                    <th>Transaksi</th>
                    <th>Selesai</th>
                    <th>Rate</th>
                    <th>Est. (Rp)</th>
                    <th>Real. (Rp)</th>
                    <th>Est. (KG)</th>
                    <th>Real. (KG)</th>
                    <th>Est. (UNIT)</th>
                    <th>Real. (UNIT)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($bankPerformance as $bankName => $performance)
                    @php
                        $bankItems = $allItems->filter(function($item) use ($bankName, $transactions) {
                            $trx = $transactions->firstWhere('bank_sampah_name', $bankName);
                            return $trx && $trx->bank_sampah_name === $bankName;
                        });
                    @endphp
                    <tr>
                        <td>{{ $bankName }}</td>
                        <td>{{ $performance['total'] }}</td>
                        <td>{{ $performance['completed'] }}</td>
                        <td>{{ number_format($performance['rate'], 1) }}%</td>
                        <td>Rp {{ number_format($performance['total_value']) }}</td>
                        <td>Rp {{ number_format($transactions->where('bank_sampah_name', $bankName)->where('aktual_total', '!=', null)->sum('aktual_total')) }}</td>
                        <td>{{ number_format($bankItems->where('sampah_satuan', 'KG')->sum('estimasi_berat'),2) }}</td>
                        <td>{{ number_format($bankItems->where('sampah_satuan', 'KG')->where('aktual_berat', '!=', null)->sum('aktual_berat'),2) }}</td>
                        <td>{{ number_format($bankItems->where('sampah_satuan', 'UNIT')->sum('estimasi_berat'),2) }}</td>
                        <td>{{ number_format($bankItems->where('sampah_satuan', 'UNIT')->where('aktual_berat', '!=', null)->sum('aktual_berat'),2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <!-- Items by Volume (full width) -->
    <div class="analytics-card" style="margin-bottom:20px;">
        <h3>Items by Volume</h3>
        <table style="width:100%;font-size:7px;margin-top:5px;">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Transaksi</th>
                    <th>Est. (KG)</th>
                    <th>Real. (KG)</th>
                    <th>Est. (UNIT)</th>
                    <th>Real. (UNIT)</th>
                    <th>Avg Harga</th>
                </tr>
            </thead>
            <tbody>
                @foreach($itemStats as $itemName => $stats)
                    <tr>
                        <td>{{ $itemName }}</td>
                        <td>{{ $stats['count'] }}</td>
                        <td>{{ number_format($allItems->where('sampah_nama', $itemName)->where('sampah_satuan', 'KG')->sum('estimasi_berat'),2) }}</td>
                        <td>{{ number_format($allItems->where('sampah_nama', $itemName)->where('sampah_satuan', 'KG')->where('aktual_berat', '!=', null)->sum('aktual_berat'),2) }}</td>
                        <td>{{ number_format($allItems->where('sampah_nama', $itemName)->where('sampah_satuan', 'UNIT')->sum('estimasi_berat'),2) }}</td>
                        <td>{{ number_format($allItems->where('sampah_nama', $itemName)->where('sampah_satuan', 'UNIT')->where('aktual_berat', '!=', null)->sum('aktual_berat'),2) }}</td>
                        <td>Rp {{ number_format($stats['avg_price']) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Detailed Transaction Table -->
    <div class="table-container">
        @if($transactions->count() > 0)
            <table>
                <thead>
                    <tr>
                        <th width="2%">No</th>
                        <th width="6%">ID / Tanggal</th>
                        <th width="10%">Pengguna</th>
                        <th width="10%">Bank Sampah</th>
                        <th width="14%">Alamat Pickup</th>
                        <th width="5%">Tipe</th>
                        <th width="6%">Status</th>
                        <th width="6%">Estimasi</th>
                        <th width="6%">Aktual</th>
                        <th width="11%">Petugas & Jadwal</th>
                        <th width="14%">Items Sampah</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transactions as $index => $transaction)
                        @php
                            $items = json_decode($transaction->items_json, true) ?? [];
                            $estimasiTotalKg = array_sum(array_column($items, 'estimasi_berat'));
                            $aktualTotalKg = array_sum(array_map(function($item) {
                                return isset($item['aktual_berat']) && $item['aktual_berat'] !== null ? $item['aktual_berat'] : 0;
                            }, $items));
                            $estimasiTotalRp = array_sum(array_column($items, 'estimasi_harga'));
                            $aktualTotalRp = array_sum(array_map(function($item) {
                                return isset($item['aktual_harga']) && $item['aktual_harga'] !== null ? $item['aktual_harga'] : 0;
                            }, $items));
                        @endphp
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>
                                <div class="font-bold">#{{ $transaction->id }}</div>
                                <div style="font-size: 6px; color: #666;">
                                    {{ \Carbon\Carbon::parse($transaction->created_at)->format('d/m/Y') }}
                                </div>
                            </td>
                            <td>
                                <div class="user-info">
                                    <div class="user-name">{{ $transaction->user_name }}</div>
                                    <div class="user-identifier">{{ $transaction->user_identifier }}</div>
                                </div>
                            </td>
                            <td>
                                <div class="bank-info">
                                    <div class="bank-name">{{ $transaction->bank_sampah_name }}</div>
                                    <div class="bank-details">{{ $transaction->bank_sampah_code ?? '-' }}</div>
                                    <div class="bank-details">{{ $transaction->bank_sampah_phone ?? '-' }}</div>
                                </div>
                            </td>
                            <td>
                                <div class="address-info">
                                    {{ $transaction->address_full_address ?? '-' }}
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="tipe-setor tipe-{{ $transaction->tipe_setor }}">
                                    {{ ucfirst($transaction->tipe_setor) }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="status status-{{ $transaction->status }}">
                                    {{ ucfirst($transaction->status) }}
                                </span>
                            </td>
                            <td class="currency">
                                Rp {{ number_format($transaction->estimasi_total ?? 0) }}
                            </td>
                            <td class="currency">
                                @if($transaction->aktual_total)
                                    Rp {{ number_format($transaction->aktual_total) }}
                                @else
                                    <span style="color: #999; font-style: italic;">Belum ada</span>
                                @endif
                            </td>
                            <td>
                                @if($transaction->petugas_nama)
                                    <div class="font-bold" style="font-size: 6px;">{{ $transaction->petugas_nama }}</div>
                                    <div style="font-size: 5px; color: #666;">{{ $transaction->petugas_contact }}</div>
                                @endif
                                @if($transaction->tanggal_penjemputan)
                                    <div class="schedule-info">
                                        Jemput: {{ \Carbon\Carbon::parse($transaction->tanggal_penjemputan)->format('d/m/Y') }}
                                    </div>
                                    @if($transaction->waktu_penjemputan)
                                        <div class="schedule-info">
                                            Jam: {{ \Carbon\Carbon::parse($transaction->waktu_penjemputan)->format('H:i') }}
                                        </div>
                                    @endif
                                @endif
                                @if($transaction->tanggal_selesai)
                                    <div class="completion-date">
                                        Selesai: {{ \Carbon\Carbon::parse($transaction->tanggal_selesai)->format('d/m/Y') }}
                                    </div>
                                @endif
                            </td>
                            <td>
                                <div class="items-detail">
                                    @foreach($items as $item)
                                        @php
                                            $itemName = $item['sampah_nama'] ?? $item['nama_sampah'] ?? 'N/A';
                                            $itemStatus = $item['status'] ?? 'normal';
                                            $statusText = '';
                                            $statusClass = '';
                                            
                                            if ($itemStatus === 'dihapus') {
                                                $statusText = ' (DIHAPUS)';
                                                $statusClass = 'item-deleted';
                                            } elseif ($itemStatus === 'ditambah') {
                                                $statusText = ' (DITAMBAH)';
                                                $statusClass = 'item-added';
                                            } elseif ($itemStatus === 'dimodifikasi') {
                                                $statusText = ' (DIMODIFIKASI)';
                                                $statusClass = 'item-modified';
                                            }
                                        @endphp
                                        <div class="item {{ $statusClass }}">
                                            <strong>{{ $itemName }}{{ $statusText }}</strong><br>
                                            Est: {{ $item['estimasi_berat'] ?? 0 }} {{ $item['sampah_satuan'] ?? $item['satuan'] ?? 'KG' }}
                                            @if(isset($item['aktual_berat']) && $item['aktual_berat'])
                                                | Aktual: {{ $item['aktual_berat'] }} {{ $item['sampah_satuan'] ?? $item['satuan'] ?? 'KG' }}
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="no-data">
                <h3>Tidak ada data transaksi yang ditemukan</h3>
                <p>Silakan periksa filter atau rentang tanggal yang dipilih</p>
            </div>
        @endif
    </div>

    <div class="footer">
        <p><strong>Advanced Analytics Report - Bank Sampah Transaction System</strong></p>
        <p>Dokumen ini digenerate secara otomatis pada {{ now()->format('d F Y, H:i:s') }} WIB</p>
        <p>© {{ date('Y') }} Bengkel Sampah. All rights reserved.</p>
    </div>
</body>
</html>