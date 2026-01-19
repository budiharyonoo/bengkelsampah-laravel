<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Penjualan & Pengolahan</title>
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
            font-size: 2em;
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

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            font-size: 9px;
            background: white;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            border-radius: 8px;
            overflow: hidden;
        }

        th {
            background-color: #39746E !important;
            color: #ffffff !important;
            padding: 10px 6px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #ddd;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        td {
            padding: 8px 6px;
            border: 1px solid #ddd;
            vertical-align: middle;
            font-size: 9px;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .type-badge {
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
            text-align: center;
            color: white;
            display: inline-block;
            min-width: 60px;
        }

        .type-sale {
            background: #28a745;
        }

        .type-processing {
            background: #17a2b8;
        }

        .currency {
            text-align: right;
            font-weight: bold;
            color: #39746E;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .font-bold {
            font-weight: bold;
        }

        .section-title {
            color: #39746E;
            font-size: 12px;
            margin-top: 30px;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: bold;
        }

        .total-row {
            background: #f0f9ff;
            font-weight: bold;
        }

        .footer {
            margin-top: 25px;
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
        <h1>LAPORAN PENJUALAN & PENGOLAHAN</h1>
        <h2>Bank Sampah Management System</h2>
        <div class="header-info">
            <span><strong>Periode:</strong> {{ $startDate->format('d M Y') }} - {{ $endDate->format('d M Y') }}</span>
            <span><strong>Bank Sampah:</strong> {{ $bankSampahName }}</span>
        </div>
    </div>

    @php
        $totalTransaksi = $summaryData['total_transaksi'] ?? 0;
        $totalQuantity = $summaryData['total_quantity'] ?? 0;
        $totalNilai = $summaryData['total_nilai'] ?? 0;
        $jenisWasteCount = $summaryData['jenis_waste_count'] ?? 0;
        $totalDijual = $summaryData['total_dijual'] ?? 0;
        $totalDiolah = $summaryData['total_diolah'] ?? 0;
    @endphp

    <!-- Summary Statistics -->
    <table class="summary-table" style="width:100%; margin: 20px 0 25px 0; border-collapse: separate; border-spacing: 10px 0; background: #f8f9fa; border-radius: 8px;">
        <tr>
            <td class="stat-td">
                <div class="stat-number">{{ number_format($totalTransaksi) }}</div>
                <div class="stat-label">Total Transaksi</div>
            </td>
            <td class="stat-td">
                <div class="stat-number">{{ number_format($totalQuantity, 2) }} kg</div>
                <div class="stat-label">Total Quantity</div>
            </td>
            <td class="stat-td">
                <div class="stat-number">Rp {{ number_format($totalNilai) }}</div>
                <div class="stat-label">Total Nilai</div>
            </td>
            <td class="stat-td">
                <div class="stat-number">{{ number_format($jenisWasteCount) }}</div>
                <div class="stat-label">Jenis Sampah</div>
            </td>
            <td class="stat-td">
                <div class="stat-number">{{ number_format($totalDijual) }}</div>
                <div class="stat-label">Total Dijual</div>
            </td>
            <td class="stat-td">
                <div class="stat-number">{{ number_format($totalDiolah) }}</div>
                <div class="stat-label">Total Diolah</div>
            </td>
        </tr>
    </table>

    <!-- Section 1: Per Offtaker -->
    <h3 class="section-title">Ringkasan per Offtaker</h3>

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="30%">Offtaker</th>
                <th width="15%">Transaksi</th>
                <th width="15%">Qty (Kg)</th>
                <th width="20%">Total (Rp)</th>
                <th width="15%">Laba (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @php $totalQty = 0; $totalValue = 0; $totalProfit = 0; @endphp
            @foreach($dataByOfftaker as $index => $item)
                @php
                    $totalQty += $item['total_quantity'] ?? 0;
                    $totalValue += $item['total_value'] ?? 0;
                    $totalProfit += $item['profit'] ?? 0;
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="font-bold">{{ $item['name'] ?? '-' }}</td>
                    <td class="text-center">{{ $item['count'] ?? 0 }}</td>
                    <td class="text-right">{{ number_format($item['total_quantity'] ?? 0, 2) }}</td>
                    <td class="currency">Rp {{ number_format($item['total_value'] ?? 0) }}</td>
                    <td class="currency" style="color: {{ ($item['profit'] ?? 0) >= 0 ? '#28a745' : '#e74c3c' }}">
                        Rp {{ number_format($item['profit'] ?? 0) }}
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="3" class="font-bold">TOTAL</td>
                <td class="text-right font-bold">{{ number_format($totalQty, 2) }}</td>
                <td class="currency font-bold">Rp {{ number_format($totalValue) }}</td>
                <td class="currency font-bold" style="color: {{ $totalProfit >= 0 ? '#28a745' : '#e74c3c' }}">
                    Rp {{ number_format($totalProfit) }}
                </td>
            </tr>
        </tfoot>
    </table>

    <!-- Section 2: Per Jenis Sampah -->
    <h3 class="section-title">Ringkasan per Jenis Sampah</h3>

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="30%">Jenis Sampah</th>
                <th width="15%">Transaksi</th>
                <th width="15%">Qty (Kg)</th>
                <th width="20%">Total (Rp)</th>
                <th width="15%">Laba (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @php $totalQtyWaste = 0; $totalValueWaste = 0; $totalProfitWaste = 0; @endphp
            @foreach($dataByWasteType as $index => $item)
                @php
                    $totalQtyWaste += $item['total_quantity'] ?? 0;
                    $totalValueWaste += $item['total_value'] ?? 0;
                    $totalProfitWaste += $item['profit'] ?? 0;
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="font-bold">{{ $item['name'] ?? '-' }}</td>
                    <td class="text-center">{{ $item['count'] ?? 0 }}</td>
                    <td class="text-right">{{ number_format($item['total_quantity'] ?? 0, 2) }}</td>
                    <td class="currency">Rp {{ number_format($item['total_value'] ?? 0) }}</td>
                    <td class="currency" style="color: {{ ($item['profit'] ?? 0) >= 0 ? '#28a745' : '#e74c3c' }}">
                        Rp {{ number_format($item['profit'] ?? 0) }}
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="3" class="font-bold">TOTAL</td>
                <td class="text-right font-bold">{{ number_format($totalQtyWaste, 2) }}</td>
                <td class="currency font-bold">Rp {{ number_format($totalValueWaste) }}</td>
                <td class="currency font-bold" style="color: {{ $totalProfitWaste >= 0 ? '#28a745' : '#e74c3c' }}">
                    Rp {{ number_format($totalProfitWaste) }}
                </td>
            </tr>
        </tfoot>
    </table>

    <!-- Section 3: Detail Transaksi -->
    @if(isset($recentTransactions) && count($recentTransactions) > 0)
        <h3 class="section-title">Detail Transaksi</h3>

        <table>
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th width="12%">Kode</th>
                    <th width="10%">Tipe</th>
                    <th width="12%">Tanggal</th>
                    <th width="25%">Offtaker</th>
                    <th width="20%">Bank Sampah</th>
                    <th width="16%">Total (Rp)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recentTransactions as $index => $trx)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td class="font-bold">{{ $trx->kode_transaksi }}</td>
                        <td class="text-center">
                            @if ($trx->type === 'sale')
                                <span class="type-badge type-sale">Penjualan</span>
                            @else
                                <span class="type-badge type-processing">Pengolahan</span>
                            @endif
                        </td>
                        <td>{{ $trx->tanggal_transaksi ? \Carbon\Carbon::parse($trx->tanggal_transaksi)->format('d/m/Y') : '-' }}</td>
                        <td>{{ $trx->offtaker->nama ?? '-' }}</td>
                        <td>{{ $trx->bankSampah->nama_bank_sampah ?? '-' }}</td>
                        <td class="currency">Rp {{ number_format($trx->total_value ?? 0) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <div class="footer">
        <p><strong>Laporan Penjualan & Pengolahan - Bank Sampah Management System</strong></p>
        <p>Dokumen ini digenerate secara otomatis pada {{ now()->format('d F Y, H:i:s') }} WIB</p>
        <p>&copy; {{ date('Y') }} Bengkel Sampah. All rights reserved.</p>
    </div>
</body>
</html>
