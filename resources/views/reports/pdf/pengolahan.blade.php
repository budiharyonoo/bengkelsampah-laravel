<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Pengolahan</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            color: #333;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #387478;
            padding-bottom: 15px;
        }
        .header h1 {
            font-size: 20px;
            color: #387478;
            margin-bottom: 5px;
        }
        .header p {
            font-size: 11px;
            color: #666;
        }
        .summary-box {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        .summary-item {
            display: table-cell;
            width: 33.33%;
            text-align: center;
            padding: 10px;
            border: 1px solid #ddd;
        }
        .summary-item .label {
            font-size: 10px;
            color: #666;
        }
        .summary-item .value {
            font-size: 16px;
            font-weight: bold;
            color: #387478;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .data-table th,
        .data-table td {
            padding: 8px;
            text-align: left;
            border: 1px solid #ddd;
        }
        .data-table th {
            background: #f5f5f5;
            font-weight: bold;
            font-size: 10px;
        }
        .text-right {
            text-align: right;
        }
        .total-row {
            background: #f0f9ff;
            font-weight: bold;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #999;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN PENGOLAHAN SAMPAH</h1>
        <p>Periode: {{ $startDate->format('d M Y') }} - {{ $endDate->format('d M Y') }}</p>
    </div>

    <div class="summary-box">
        <div class="summary-item">
            <div class="label">Total Transaksi</div>
            <div class="value">{{ $processingMetrics['count'] ?? 0 }}</div>
        </div>
        <div class="summary-item">
            <div class="label">Total Input (Kg)</div>
            <div class="value">{{ number_format($processingMetrics['total_quantity'] ?? 0, 2, ',', '.') }}</div>
        </div>
    </div>

    <h3 style="font-size: 14px; margin-top: 20px; margin-bottom: 10px;">Ringkasan Pengolahan per Offtaker</h3>

    <table class="data-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Offtaker</th>
                <th class="text-right">Transaksi</th>
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
            <tr class="total-row">
                <td colspan="3">Total</td>
                <td class="text-right">{{ number_format($totalQty, 2, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <h3 style="font-size: 14px; margin-top: 20px; margin-bottom: 10px;">Transaksi Terbaru</h3>

    <table class="data-table">
        <thead>
            <tr>
                <th>Kode</th>
                <th>Tanggal</th>
                <th>Offtaker</th>
                <th>Bank Sampah</th>
                <th>Metode</th>
                <th class="text-right">Qty (Kg)</th>
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

    <div class="footer">
        <p>Dicetak pada: {{ now()->format('d M Y H:i') }} | Bengkel Sampah</p>
    </div>
</body>
</html>
