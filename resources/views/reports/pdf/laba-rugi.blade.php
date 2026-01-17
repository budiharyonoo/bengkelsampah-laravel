<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Laba Rugi</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
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
        .summary-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        .summary-table th,
        .summary-table td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
        }
        .summary-table th {
            background: #f5f5f5;
            font-weight: bold;
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
        .inventory-section {
            margin-top: 20px;
        }
        .inventory-section h3 {
            font-size: 14px;
            margin-bottom: 10px;
            color: #387478;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN LABA RUGI</h1>
        <p>Periode: {{ $startDate->format('d M Y') }} - {{ $endDate->format('d M Y') }}</p>
    </div>

    <table class="summary-table">
        <thead>
            <tr>
                <th>Keterangan</th>
                <th class="text-right">Jumlah (Rp)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Total Pembelian Sampah (dari Nasabah)</td>
                <td class="text-right text-red">- {{ number_format($totalPembelian, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Total Penjualan Sampah (ke Offtaker)</td>
                <td class="text-right text-green">+ {{ number_format($totalPenjualan, 0, ',', '.') }}</td>
            </tr>
            <tr class="total-row">
                <td>Laba Kotor</td>
                <td class="text-right {{ $labaKotor >= 0 ? 'text-green' : 'text-red' }}">
                    {{ number_format($labaKotor, 0, ',', '.') }}
                </td>
            </tr>
        </tbody>
    </table>

    <div class="inventory-section">
        <h3>Status Inventori</h3>
        <table class="summary-table">
            <tr>
                <td>Sampah Tersimpan</td>
                <td class="text-right">{{ number_format($inventorySummary['stored_kg'], 2, ',', '.') }} Kg</td>
            </tr>
            <tr>
                <td>Sampah Terjual</td>
                <td class="text-right">{{ number_format($inventorySummary['sold_kg'], 2, ',', '.') }} Kg</td>
            </tr>
            <tr>
                <td>Sampah Diolah</td>
                <td class="text-right">{{ number_format($inventorySummary['processed_kg'], 2, ',', '.') }} Kg</td>
            </tr>
        </table>
    </div>

    <div class="footer">
        <p>Dicetak pada: {{ now()->format('d M Y H:i') }} | Bengkel Sampah</p>
    </div>
</body>
</html>
