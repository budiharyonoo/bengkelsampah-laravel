@extends('dashboard')
@section('content')
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Transaksi - Admin Panel</title>
    <link href="https://fonts.googleapis.com/css2?family=Urbanist:wght@400;500;600;700;900&display=swap" rel="stylesheet">
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

        .header {
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .header-left h1 {
            font-size: 22px;
            font-weight: 400;
            color: #39746E;
        }

        .header-separator {
            font-size: 22px;
            color: #39746E;
        }

        .header-subtitle {
            font-size: 22px;
            font-weight: 700;
            color: #39746E;
        }

        .main-container {
            display: grid;
            grid-template-columns: 1fr 350px;
            gap: 1rem;
            margin: 0 2rem 2rem 2rem;
        }

        .info-container {
            background: #fff;
            border: 1px solid #E5E6E6;
            border-radius: 16px;
            padding: 24px;
        }

        .summary-container {
            background: #fff;
            border: 1px solid #E5E6E6;
            border-radius: 16px;
            padding: 24px;
            height: fit-content;
        }

        .info-header {
            margin-bottom: 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .info-title {
            font-size: 18px;
            font-weight: 700;
            color: #242E2C;
        }

        .info-actions {
            display: flex;
            gap: 8px;
        }

        .btn-back {
            padding: 8px 16px;
            background: transparent;
            border: 1px solid #E5E6E6;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            color: #374151;
            cursor: pointer;
            text-decoration: none;
        }

        .btn-print {
            padding: 8px 16px;
            background: #39746E;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            color: #DFF0EE;
            cursor: pointer;
            text-decoration: none;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 32px;
        }

        .info-item {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .info-label {
            font-size: 12px;
            font-weight: 500;
            color: #6B7271;
            text-transform: uppercase;
        }

        .info-value {
            font-size: 15px;
            font-weight: 600;
            color: #1e293b;
        }

        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }

        .badge-sale {
            background: #D1FAE5;
            color: #065F46;
        }

        .badge-processing {
            background: #DBEAFE;
            color: #1E40AF;
        }

        .section-title {
            font-size: 16px;
            font-weight: 700;
            color: #242E2C;
            margin-bottom: 16px;
            padding-bottom: 8px;
            border-bottom: 1px solid #E5E6E6;
        }

        .table-container {
            overflow-x: auto;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .data-table th,
        .data-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #E5E6E6;
        }

        .data-table th {
            font-weight: 600;
            font-size: 12px;
            color: #6B7271;
            text-transform: uppercase;
        }

        .data-table td {
            font-size: 14px;
            color: #1e293b;
        }

        .summary-title {
            font-size: 16px;
            font-weight: 700;
            color: #242E2C;
            margin-bottom: 16px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #E5E6E6;
            font-size: 14px;
        }

        .summary-row:last-child {
            border-bottom: none;
        }

        .summary-label {
            color: #6B7271;
        }

        .summary-value {
            font-weight: 600;
            color: #1e293b;
        }

        .summary-total {
            margin-top: 16px;
            padding-top: 16px;
            border-top: 2px solid #E5E6E6;
        }

        .summary-total .summary-value {
            font-size: 20px;
            color: #39746E;
        }

        .profit-display {
            margin-top: 16px;
            padding: 16px;
            background: #D1FAE5;
            border-radius: 8px;
            text-align: center;
        }

        .profit-display.loss {
            background: #FEE2E2;
        }

        .profit-label {
            font-size: 12px;
            color: #065F46;
        }

        .profit-display.loss .profit-label {
            color: #991B1B;
        }

        .profit-value {
            font-size: 24px;
            font-weight: 700;
            color: #065F46;
        }

        .profit-display.loss .profit-value {
            color: #991B1B;
        }

        .profit-margin {
            font-size: 12px;
            color: #065F46;
            margin-top: 4px;
        }

        .profit-display.loss .profit-margin {
            color: #991B1B;
        }

        .struk-image {
            margin-top: 16px;
            border-radius: 8px;
            overflow: hidden;
        }

        .struk-image img {
            max-width: 100%;
            height: auto;
        }

        .notes-box {
            margin-top: 24px;
            padding: 16px;
            background: #F9FAFB;
            border-radius: 8px;
        }

        .notes-label {
            font-size: 12px;
            font-weight: 600;
            color: #6B7271;
            margin-bottom: 8px;
        }

        .notes-content {
            font-size: 14px;
            color: #1e293b;
        }

        @media (max-width: 1024px) {
            .main-container {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .info-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-left">
            <h1>Transaksi Sampah</h1>
            <span class="header-separator">/</span>
            <span class="header-subtitle">{{ $wasteTransaction->kode_transaksi }}</span>
        </div>
    </div>

    <div class="main-container">
        <div class="info-container">
            <div class="info-header">
                <h2 class="info-title">Detail Transaksi</h2>
                <div class="info-actions">
                    <a href="{{ $wasteTransaction->isSale() ? route('waste-transactions.sales.index') : route('waste-transactions.processing.index') }}" class="btn-back">Kembali</a>
                    {{-- <a href="#" class="btn-print" onclick="window.print()">Cetak</a> --}}
                </div>
            </div>

            <div class="info-grid">
                <div class="info-item">
                    <span class="info-label">Kode Transaksi</span>
                    <span class="info-value">{{ $wasteTransaction->kode_transaksi }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Tipe</span>
                    <span class="info-value">
                        @if($wasteTransaction->isSale())
                            <span class="badge badge-sale">Penjualan</span>
                        @else
                            <span class="badge badge-processing">Pengolahan</span>
                        @endif
                    </span>
                </div>
                <div class="info-item">
                    <span class="info-label">Bank Sampah</span>
                    <span class="info-value">
                        {{ $wasteTransaction->bankSampah->nama_bank_sampah ?? '-' }}
                        <br><small style="color: #6B7271;">{{ $wasteTransaction->bankSampah->kode_bank_sampah ?? '' }}</small>
                    </span>
                </div>
                <div class="info-item">
                    <span class="info-label">{{ $wasteTransaction->isSale() ? 'Pembeli' : 'Pengolah' }}</span>
                    <span class="info-value">
                        {{ $wasteTransaction->offtaker->nama ?? '-' }}
                        <br><small style="color: #6B7271;">{{ $wasteTransaction->offtaker->kode_offtaker ?? '' }}</small>
                    </span>
                </div>
                <div class="info-item">
                    <span class="info-label">Tanggal Transaksi</span>
                    <span class="info-value">{{ $wasteTransaction->tanggal_transaksi->format('d F Y') }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Dicatat Oleh</span>
                    <span class="info-value">{{ $wasteTransaction->admin_name }}</span>
                </div>
                @if($wasteTransaction->isProcessing())
                    <div class="info-item" style="grid-column: span 2;">
                        <span class="info-label">Metode Pengolahan</span>
                        <span class="info-value">{{ $wasteTransaction->metode_pengolahan ?? '-' }}</span>
                    </div>
                    @if($wasteTransaction->hasil_pengolahan)
                        <div class="info-item" style="grid-column: span 2;">
                            <span class="info-label">Hasil Pengolahan</span>
                            <span class="info-value">{{ $wasteTransaction->hasil_pengolahan }}</span>
                        </div>
                    @endif
                @endif
            </div>

            <h3 class="section-title">Item Transaksi</h3>
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Jenis Sampah</th>
                            <th>Qty</th>
                            @if($wasteTransaction->isSale())
                                <th>Harga Jual/kg</th>
                                <th>Subtotal</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($wasteTransaction->items_json as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $sampahList[$item['sampah_id']] ?? 'Sampah #'.$item['sampah_id'] }}</td>
                                <td>{{ number_format($item['quantity'], 2) }} kg</td>
                                @if($wasteTransaction->isSale())
                                    <td>Rp {{ number_format($item['harga_jual'] ?? 0, 0, ',', '.') }}</td>
                                    <td>Rp {{ number_format(($item['quantity'] ?? 0) * ($item['harga_jual'] ?? 0), 0, ',', '.') }}</td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($wasteTransaction->notes)
                <div class="notes-box">
                    <div class="notes-label">Catatan</div>
                    <div class="notes-content">{{ $wasteTransaction->notes }}</div>
                </div>
            @endif
        </div>

        <div class="summary-container">
            <h3 class="summary-title">Ringkasan</h3>
            <div class="summary-row">
                <span class="summary-label">Total Qty</span>
                <span class="summary-value">{{ number_format($wasteTransaction->total_quantity, 2) }} kg</span>
            </div>
            @if($wasteTransaction->isSale())
                <div class="summary-row">
                    <span class="summary-label">Total Harga Beli</span>
                    <span class="summary-value">Rp {{ number_format($wasteTransaction->harga_beli_total, 0, ',', '.') }}</span>
                </div>
                <div class="summary-row summary-total">
                    <span class="summary-label">Total Penjualan</span>
                    <span class="summary-value">Rp {{ number_format($wasteTransaction->total_value, 0, ',', '.') }}</span>
                </div>

                @php
                    $profit = $wasteTransaction->getProfit();
                    $margin = $wasteTransaction->getProfitMargin();
                    $isLoss = $profit < 0;
                @endphp
                <div class="profit-display {{ $isLoss ? 'loss' : '' }}">
                    <div class="profit-label">{{ $isLoss ? 'Rugi' : 'Laba' }}</div>
                    <div class="profit-value">Rp {{ number_format(abs($profit), 0, ',', '.') }}</div>
                    <div class="profit-margin">Margin: {{ number_format($margin, 1) }}%</div>
                </div>
            @else
                <div class="summary-row">
                    <span class="summary-label">Nilai Beli Sampah</span>
                    <span class="summary-value">Rp {{ number_format($wasteTransaction->harga_beli_total, 0, ',', '.') }}</span>
                </div>
            @endif

            @if($wasteTransaction->struk)
                <div class="struk-image">
                    <div class="notes-label" style="margin-bottom: 8px;">Foto Struk</div>
                    <img src="{{ Storage::url($wasteTransaction->struk) }}" alt="Struk">
                </div>
            @endif
        </div>
    </div>
</body>
</html>
@endsection
