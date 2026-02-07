<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Laba Rugi</title>
    <style>
        @page {
            margin: 1.5cm;
            size: A4 portrait;
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
            font-size: 24px;
            font-weight: 900;
            margin: 0 0 8px 0;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .header h2 {
            font-size: 14px;
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

        .summary-section {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin: 25px 0;
        }

        .summary-grid {
            display: table;
            width: 100%;
            border-collapse: separate;
            border-spacing: 10px 0;
        }

        .summary-item {
            display: table-cell;
            text-align: center;
            padding: 15px 10px;
            background: white;
            border-radius: 6px;
            border-right: 1.5px solid #e0e0e0;
        }

        .summary-item:last-child {
            border-right: none;
        }

        .summary-number {
            font-size: 20px;
            font-weight: bold;
            color: #39746E;
            margin-bottom: 5px;
            display: block;
            line-height: 1.1;
        }

        .summary-number.positive {
            color: #28a745;
        }

        .summary-number.negative {
            color: #e74c3c;
        }

        .summary-label {
            font-size: 10px;
            color: #666;
            font-weight: 500;
            line-height: 1.2;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 11px;
            background: white;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            border-radius: 8px;
            overflow: hidden;
        }

        th {
            background-color: #39746E !important;
            color: #ffffff !important;
            padding: 12px 10px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #ddd;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        td {
            padding: 10px;
            border: 1px solid #ddd;
            vertical-align: middle;
            font-size: 11px;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .font-bold {
            font-weight: bold;
        }

        .text-green {
            color: #28a745;
            font-weight: bold;
        }

        .text-red {
            color: #e74c3c;
            font-weight: bold;
        }

        .total-row {
            background: #f0f9ff !important;
            font-weight: bold;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        .section-title {
            color: #39746E;
            font-size: 14px;
            margin-top: 30px;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: bold;
            border-bottom: 2px solid #39746E;
            padding-bottom: 5px;
        }

        .inventory-section {
            margin-top: 25px;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 9px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 12px;
        }

        thead {
            display: table-header-group;
        }

        thead th {
            background-color: #39746E !important;
            color: #ffffff !important;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN LABA RUGI</h1>
        <h2>Bank Sampah Management System</h2>
        <div class="header-info">
            <span><strong>Periode:</strong> {{ $startDate->format('d M Y') }} - {{ $endDate->format('d M Y') }}</span>
        </div>
    </div>

    <!-- Summary Statistics -->
    <div class="summary-section">
        <div class="summary-grid">
            <div class="summary-item">
                <span class="summary-number text-red">Rp {{ number_format($totalPembelian) }}</span>
                <span class="summary-label">Total Pembelian</span>
            </div>
            <div class="summary-item">
                <span class="summary-number text-green">Rp {{ number_format($totalPenjualan) }}</span>
                <span class="summary-label">Total Penjualan & Pengolahan</span>
            </div>
            <div class="summary-item">
                <span class="summary-number {{ $labaKotor >= 0 ? 'positive' : 'negative' }}">
                    Rp {{ number_format($labaKotor) }}
                </span>
                <span class="summary-label">Laba Kotor</span>
            </div>
        </div>
    </div>

    <!-- Profit & Loss Details -->
    <h3 class="section-title">Rincian Laba Rugi</h3>
    <table>
        <thead>
            <tr>
                <th width="60%">Keterangan</th>
                <th width="40%" class="text-right">Jumlah (Rp)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="font-bold">Total Pembelian Sampah (dari Nasabah)</td>
                <td class="text-right text-red">- {{ number_format($totalPembelian, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="font-bold">Total Penjualan & Pengolahan Sampah</td>
                <td class="text-right text-green">+ {{ number_format($totalPenjualan, 0, ',', '.') }}</td>
            </tr>
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td class="font-bold">Laba Kotor</td>
                <td class="text-right font-bold {{ $labaKotor >= 0 ? 'text-green' : 'text-red' }}">
                    {{ number_format($labaKotor, 0, ',', '.') }}
                </td>
            </tr>
        </tfoot>
    </table>

    <!-- Inventory Status -->
    <div class="inventory-section">
        <h3 class="section-title">Status Inventori</h3>
        <table>
            <thead>
                <tr>
                    <th width="60%">Keterangan</th>
                    <th width="40%" class="text-right">Jumlah (Kg)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Sampah Tersimpan</td>
                    <td class="text-right font-bold">{{ number_format($inventorySummary['stored_kg'], 2, ',', '.') }}</td>
                </tr>
                <tr>
                    <td>Sampah Terjual</td>
                    <td class="text-right font-bold">{{ number_format($inventorySummary['sold_kg'], 2, ',', '.') }}</td>
                </tr>
                <tr>
                    <td>Sampah Diolah</td>
                    <td class="text-right font-bold">{{ number_format($inventorySummary['processed_kg'], 2, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="footer">
        <p><strong>Laporan Laba Rugi - Bank Sampah Management System</strong></p>
        <p>Dokumen ini digenerate secara otomatis pada {{ now()->format('d F Y, H:i:s') }} WIB</p>
        <p>&copy; {{ date('Y') }} Bengkel Sampah. All rights reserved.</p>
    </div>
</body>
</html>
