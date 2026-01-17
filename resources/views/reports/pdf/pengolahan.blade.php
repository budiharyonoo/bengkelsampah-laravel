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
        <div class="summary-item">
            <div class="label">Total Output (Kg)</div>
            <div class="value">{{ number_format($processingMetrics['output_quantity'] ?? 0, 2, ',', '.') }}</div>
        </div>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Metode Pengolahan</th>
                <th class="text-right">Transaksi</th>
                <th class="text-right">Input (Kg)</th>
                <th class="text-right">Output (Kg)</th>
                <th class="text-right">Efisiensi</th>
            </tr>
        </thead>
        <tbody>
            @php $totalInput = 0; $totalOutput = 0; @endphp
            @foreach($processingByMethod as $index => $item)
                @php
                    $totalInput += $item['total_quantity'] ?? 0;
                    $totalOutput += $item['output_quantity'] ?? 0;
                    $efficiency = ($item['total_quantity'] ?? 0) > 0 ? (($item['output_quantity'] ?? 0) / ($item['total_quantity'] ?? 1)) * 100 : 0;
                    $methodLabel = match($item['method'] ?? '') {
                        'kompos' => 'Kompos',
                        'daur_ulang' => 'Daur Ulang',
                        'rdf' => 'RDF',
                        default => ucfirst($item['method'] ?? 'Lainnya')
                    };
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $methodLabel }}</td>
                    <td class="text-right">{{ $item['count'] ?? 0 }}</td>
                    <td class="text-right">{{ number_format($item['total_quantity'] ?? 0, 2, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($item['output_quantity'] ?? 0, 2, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($efficiency, 1) }}%</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            @php $totalEfficiency = $totalInput > 0 ? ($totalOutput / $totalInput) * 100 : 0; @endphp
            <tr class="total-row">
                <td colspan="3">Total</td>
                <td class="text-right">{{ number_format($totalInput, 2, ',', '.') }}</td>
                <td class="text-right">{{ number_format($totalOutput, 2, ',', '.') }}</td>
                <td class="text-right">{{ number_format($totalEfficiency, 1) }}%</td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <p>Dicetak pada: {{ now()->format('d M Y H:i') }} | Bengkel Sampah</p>
    </div>
</body>
</html>
