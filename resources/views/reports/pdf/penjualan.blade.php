<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Penjualan</title>
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
        .text-green {
            color: #22c55e;
        }
        .text-red {
            color: #ef4444;
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
        .group-label {
            font-size: 12px;
            margin-bottom: 10px;
            color: #387478;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN PENJUALAN</h1>
        <p>Periode: {{ $startDate->format('d M Y') }} - {{ $endDate->format('d M Y') }}</p>
    </div>

    <p class="group-label">Ringkasan per {{ $groupBy == 'offtaker' ? 'Offtaker' : 'Jenis Sampah' }}</p>

    <table class="data-table">
        <thead>
            <tr>
                <th>No</th>
                <th>{{ $groupBy == 'offtaker' ? 'Offtaker' : 'Jenis Sampah' }}</th>
                <th class="text-right">Transaksi</th>
                <th class="text-right">Qty (Kg)</th>
                <th class="text-right">Total (Rp)</th>
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
            <tr class="total-row">
                <td colspan="3">Total</td>
                <td class="text-right">{{ number_format($totalQty, 2, ',', '.') }}</td>
                <td class="text-right">{{ number_format($totalValue, 0, ',', '.') }}</td>
                <td class="text-right {{ $totalProfit >= 0 ? 'text-green' : 'text-red' }}">
                    {{ number_format($totalProfit, 0, ',', '.') }}
                </td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <p>Dicetak pada: {{ now()->format('d M Y H:i') }} | Bengkel Sampah</p>
    </div>
</body>
</html>
