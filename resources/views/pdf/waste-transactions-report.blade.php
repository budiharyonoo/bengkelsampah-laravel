<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan {{ $typeLabel }}</title>
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
            background: linear-gradient(135deg, #39746E 0%, #2c5530 100%);
            color: white;
            padding: 10px 6px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #ddd;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
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

        tr:hover {
            background-color: #e8f5e8;
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

        .no-data {
            text-align: center;
            padding: 30px;
            color: #666;
            font-style: italic;
        }

        .bank-info {
            font-size: 9px;
        }

        .bank-name {
            font-weight: bold;
            color: #39746E;
        }

        .bank-details {
            font-size: 8px;
            color: #999;
        }

        .offtaker-info {
            font-size: 9px;
        }

        .offtaker-name {
            font-weight: bold;
            color: #39746E;
        }

        .offtaker-details {
            font-size: 8px;
            color: #666;
        }

        .sampah-info {
            font-size: 9px;
        }

        .sampah-name {
            font-weight: bold;
            color: #333;
        }

        .sampah-category {
            font-size: 8px;
            color: #666;
            font-style: italic;
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

        th {
            background: #39746E;
            color: white;
            font-weight: bold;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN {{ strtoupper($typeLabel) }}</h1>
        <h2>Bank Sampah Management System</h2>
        <div class="header-info">
            <span><strong>Dicetak:</strong> {{ $generatedAt }}</span>
            <span><strong>Total Data:</strong> {{ $transactions->count() }} transaksi</span>
        </div>
    </div>

    @php
        // Calculate totals from items_json
        $isProcessing = $type === 'processing';
        $totalQuantity = 0;
        $totalValue = 0;
        $totalTransactions = 0;

        $sampahStats = [];
        $bankStats = [];

        foreach ($transactions as $transaction) {
            $sampahModels = $transaction->sampahModels ?? collect();

            // Initialize bank stats for this transaction
            $bankName = $transaction->bankSampah->nama_bank_sampah ?? 'Unknown';
            if (!isset($bankStats[$bankName])) {
                $bankStats[$bankName] = [
                    'count' => 0,
                    'total_qty' => 0,
                    'total_value' => 0,
                ];
            }

            $bankStats[$bankName]['count']++;

            if (!empty($transaction->items_json) && is_array($transaction->items_json)) {
                foreach ($transaction->items_json as $item) {
                    $quantity = $item['quantity'] ?? 0;
                    $sampahId = $item['sampah_id'] ?? null;

                    $totalQuantity += $quantity;
                    $totalTransactions++;

                    // Accumulate to bank stats from items
                    $bankStats[$bankName]['total_qty'] += $quantity;

                    // Only process prices for sale type
                    if (!$isProcessing) {
                        $harga = $item['harga_jual'] ?? 0;
                        $totalValue += $harga;
                        $bankStats[$bankName]['total_value'] += $harga;
                    }

                    // Group by sampah
                    if ($sampahId && isset($sampahModels[$sampahId])) {
                        $sampahName = $sampahModels[$sampahId]->nama;

                        if (!isset($sampahStats[$sampahName])) {
                            $sampahStats[$sampahName] = [
                                'count' => 0,
                                'total_qty' => 0,
                                'total_value' => 0,
                            ];
                        }

                        $sampahStats[$sampahName]['count']++;
                        $sampahStats[$sampahName]['total_qty'] += $quantity;

                        // Only process prices for sale type
                        if (!$isProcessing) {
                            $harga = $item['harga_jual'] ?? 0;
                            $sampahStats[$sampahName]['total_value'] += $harga;
                        }
                    }
                }
            }
        }

        $avgPrice = $totalTransactions > 0 && !$isProcessing ? $totalValue / $totalTransactions : 0;

        // Sort by total quantity for processing, by total value for sale
        if ($isProcessing) {
            uasort($sampahStats, function($a, $b) {
                return $b['total_qty'] <=> $a['total_qty'];
            });

            uasort($bankStats, function($a, $b) {
                return $b['total_qty'] <=> $a['total_qty'];
            });
        } else {
            uasort($sampahStats, function($a, $b) {
                return $b['total_value'] <=> $a['total_value'];
            });

            uasort($bankStats, function($a, $b) {
                return $b['total_value'] <=> $a['total_value'];
            });
        }
    @endphp

    <!-- Summary Statistics -->
    <table class="summary-table" style="width:100%; margin: 20px 0 25px 0; border-collapse: separate; border-spacing: 10px 0; background: #f8f9fa; border-radius: 8px;">
        <tr>
            <td class="stat-td">
                <div class="stat-number">{{ number_format($totalTransactions) }}</div>
                <div class="stat-label">Total Item</div>
            </td>
            <td class="stat-td">
                <div class="stat-number">{{ number_format($totalQuantity, 2) }} kg</div>
                <div class="stat-label">Total Quantity</div>
            </td>
            @if(!$isProcessing)
            <td class="stat-td">
                <div class="stat-number">Rp {{ number_format($totalValue) }}</div>
                <div class="stat-label">Total Nilai</div>
            </td>
            <td class="stat-td">
                <div class="stat-number">Rp {{ number_format($avgPrice) }}</div>
                <div class="stat-label">Rata-rata per Item</div>
            </td>
            @endif
            <td class="stat-td">
                <div class="stat-number">{{ count($sampahStats) }}</div>
                <div class="stat-label">Jenis Sampah</div>
            </td>
            <td class="stat-td">
                <div class="stat-number">{{ count($bankStats) }}</div>
                <div class="stat-label">Bank Sampah</div>
            </td>
        </tr>
    </table>

    <!-- Detailed Transaction Table -->
    <div class="table-container">
        @if($transactions->count() > 0)
            <table>
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th width="12%">Kode Trx / Tanggal</th>
                        <th width="15%">Bank Sampah</th>
                        <th width="{{ $isProcessing ? '35%' : '30%' }}">Item Sampah</th>
                        <th width="12%">Total Qty (kg)</th>
                        @if($isProcessing)
                        <th width="15%">Metode Daur Ulang</th>
                        @else
                        <th width="15%">Total Harga (Rp)</th>
                        @endif
                        <th width="11%">Catatan</th>
                    </tr>
                </thead>
                <tbody>
                    @php $rowNumber = 1; @endphp
                    @foreach($transactions as $transaction)
                        @php
                            $sampahModels = $transaction->sampahModels ?? collect();
                            $itemsText = [];
                            $totalQuantity = 0;
                            $totalHarga = 0;

                            if (!empty($transaction->items_json) && is_array($transaction->items_json)) {
                                foreach ($transaction->items_json as $item) {
                                    $sampahId = $item['sampah_id'] ?? null;
                                    $sampahName = '-';
                                    if ($sampahId && isset($sampahModels[$sampahId])) {
                                        $sampahName = $sampahModels[$sampahId]->nama;
                                    }

                                    $quantity = $item['quantity'] ?? 0;
                                    $totalQuantity += $quantity;

                                    if ($isProcessing) {
                                        // For processing: only show name and quantity, no price
                                        $itemsText[] = $sampahName . ' (' . number_format($quantity, 2) . ' kg)';
                                    } else {
                                        // For sale: show name, quantity, and price
                                        $harga = $item['harga_jual'] ?? 0;
                                        $totalHarga += $harga;
                                        $itemsText[] = $sampahName . ' (' . number_format($quantity, 2) . ' kg @ Rp ' . number_format($harga) . ')';
                                    }
                                }
                            }
                        @endphp
                        <tr>
                            <td class="text-center">{{ $rowNumber++ }}</td>
                            <td>
                                <div class="font-bold">{{ $transaction->kode_transaksi }}</div>
                                <div style="font-size: 8px; color: #666;">
                                    {{ $transaction->tanggal_transaksi->format('d/m/Y') }}
                                </div>
                            </td>
                            <td>
                                <div class="font-bold" style="font-size: 9px;">
                                    {{ $transaction->bankSampah->nama_bank_sampah ?? '-' }}
                                </div>
                            </td>
                            <td>
                                <div style="font-size: 8px; line-height: 1.4;">
                                    @if(!empty($itemsText))
                                        @foreach($itemsText as $itemLine)
                                            <div style="margin-bottom: 2px;">{{ $itemLine }}</div>
                                        @endforeach
                                    @else
                                        -
                                    @endif
                                </div>
                            </td>
                            <td class="text-right">
                                {{ number_format($totalQuantity, 2) }}
                            </td>
                            @if($isProcessing)
                            <td style="font-size: 9px;">
                                {{ $transaction->metode_pengolahan ?? '-' }}
                            </td>
                            @else
                            <td class="currency">
                                Rp {{ number_format($totalHarga) }}
                            </td>
                            @endif
                            <td style="font-size: 8px;">
                                {{ $transaction->notes ?? '-' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Summary by Sampah -->
            <div style="margin-top: 30px;">
                <h3 style="color: #39746E; font-size: 12px; margin-bottom: 10px; text-transform: uppercase; letter-spacing: 1px;">
                    Ringkasan per Jenis Sampah
                </h3>
                <table>
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th width="{{ $isProcessing ? '40%' : '30%' }}">Jenis Sampah</th>
                            <th width="{{ $isProcessing ? '25%' : '20%' }}">Jumlah Item</th>
                            <th width="{{ $isProcessing ? '30%' : '20%' }}">Total Quantity (kg)</th>
                            @if(!$isProcessing)
                            <th width="25%">Total Nilai (Rp)</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sampahStats as $sampahName => $stats)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td class="font-bold">{{ $sampahName }}</td>
                                <td class="text-center">{{ $stats['count'] }}</td>
                                <td class="text-right">{{ number_format($stats['total_qty'], 2) }}</td>
                                @if(!$isProcessing)
                                <td class="currency">Rp {{ number_format($stats['total_value']) }}</td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Summary by Bank Sampah -->
            <div style="margin-top: 30px;">
                <h3 style="color: #39746E; font-size: 12px; margin-bottom: 10px; text-transform: uppercase; letter-spacing: 1px;">
                    Ringkasan per Bank Sampah
                </h3>
                <table>
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th width="{{ $isProcessing ? '40%' : '30%' }}">Bank Sampah</th>
                            <th width="{{ $isProcessing ? '25%' : '20%' }}">Jumlah Transaksi</th>
                            <th width="{{ $isProcessing ? '30%' : '20%' }}">Total Quantity (kg)</th>
                            @if(!$isProcessing)
                            <th width="25%">Total Nilai (Rp)</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bankStats as $bankName => $stats)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td class="font-bold">{{ $bankName }}</td>
                                <td class="text-center">{{ $stats['count'] }}</td>
                                <td class="text-right">{{ number_format($stats['total_qty'], 2) }}</td>
                                @if(!$isProcessing)
                                <td class="currency">Rp {{ number_format($stats['total_value']) }}</td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="no-data">
                <h3>Tidak ada data transaksi yang ditemukan</h3>
                <p>Silakan periksa filter atau rentang tanggal yang dipilih</p>
            </div>
        @endif
    </div>

    <div class="footer">
        <p><strong>Laporan {{ $typeLabel }} - Bank Sampah Management System</strong></p>
        <p>Dokumen ini digenerate secara otomatis pada {{ now()->format('d F Y, H:i:s') }} WIB</p>
        <p>&copy; {{ date('Y') }} Bengkel Sampah. All rights reserved.</p>
    </div>
</body>
</html>
