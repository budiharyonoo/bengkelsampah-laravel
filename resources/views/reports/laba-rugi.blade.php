@extends('dashboard')
@section('content')
@include('partials.dashboard-header-bar', ['title' => 'Laporan Laba Rugi'])
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Laba Rugi - Admin Panel</title>
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

        .summary-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .summary-card {
            background: #fff;
            border: 1px solid #E5E6E6;
            border-radius: 12px;
            padding: 1.5rem;
        }

        .summary-card.pembelian {
            border-left: 4px solid #ef4444;
        }

        .summary-card.penjualan {
            border-left: 4px solid #22c55e;
        }

        .summary-card.laba {
            border-left: 4px solid #387478;
        }

        .summary-card .label {
            font-size: 14px;
            color: #6b7280;
            margin-bottom: 8px;
        }

        .summary-card .value {
            font-size: 28px;
            font-weight: 700;
            color: #1e293b;
        }

        .summary-card .sub-info {
            font-size: 13px;
            color: #9ca3af;
            margin-top: 8px;
        }

        .summary-card.laba .value.positive {
            color: #22c55e;
        }

        .summary-card.laba .value.negative {
            color: #ef4444;
        }

        .detail-section {
            margin-top: 2rem;
        }

        .detail-section h3 {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 1rem;
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

        .inventory-section {
            margin-top: 2rem;
            padding: 1.5rem;
            background: #f9fafb;
            border-radius: 12px;
        }

        .inventory-section h3 {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 1rem;
            color: #374151;
        }

        .inventory-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 1rem;
        }

        .inventory-item {
            background: #fff;
            padding: 1rem;
            border-radius: 8px;
            text-align: center;
        }

        .inventory-item .icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 8px;
        }

        .inventory-item.stored .icon {
            background: #e0f2fe;
            color: #0891b2;
        }

        .inventory-item.sold .icon {
            background: #dcfce7;
            color: #22c55e;
        }

        .inventory-item.processed .icon {
            background: #fef3c7;
            color: #f59e0b;
        }

        .inventory-item .amount {
            font-size: 20px;
            font-weight: 700;
            color: #1e293b;
        }

        .inventory-item .label {
            font-size: 13px;
            color: #6b7280;
        }

        .period-info {
            font-size: 14px;
            color: #6b7280;
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
            </form>

            <div class="btn-group">
                <form method="POST" action="{{ route('reports.laba-rugi.export.excel') }}" style="display:inline;">
                    @csrf
                    <input type="hidden" name="periode" value="{{ $periode }}">
                    <input type="hidden" name="bank_sampah_id" value="{{ $selectedBankSampah }}">
                    <button type="submit" class="btn btn-success">
                        <i class="fa-solid fa-file-excel"></i> Export Excel
                    </button>
                </form>
                <form method="POST" action="{{ route('reports.laba-rugi.export.pdf') }}" style="display:inline;">
                    @csrf
                    <input type="hidden" name="periode" value="{{ $periode }}">
                    <input type="hidden" name="bank_sampah_id" value="{{ $selectedBankSampah }}">
                    <button type="submit" class="btn btn-danger">
                        <i class="fa-solid fa-file-pdf"></i> Export PDF
                    </button>
                </form>
            </div>
        </div>

        <div class="summary-cards">
            <div class="summary-card pembelian">
                <div class="label">Total Pembelian (Setoran)</div>
                <div class="value">Rp {{ number_format($totalPembelian, 0, ',', '.') }}</div>
                <div class="sub-info">{{ $jumlahSetoran }} transaksi setoran</div>
            </div>
            <div class="summary-card penjualan">
                <div class="label">Total Penjualan</div>
                <div class="value">Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</div>
                <div class="sub-info">{{ $jumlahTransaksiJual }} transaksi penjualan</div>
            </div>
            <div class="summary-card laba">
                <div class="label">Laba Kotor</div>
                <div class="value {{ $labaKotor >= 0 ? 'positive' : 'negative' }}">
                    Rp {{ number_format($labaKotor, 0, ',', '.') }}
                </div>
                <div class="sub-info">
                    @if($totalPenjualan > 0)
                        Margin: {{ number_format($labaKotor / $totalPenjualan * 100, 1) }}%
                    @else
                        Margin: 0%
                    @endif
                </div>
            </div>
        </div>

        <div class="detail-section">
            <h3>Ringkasan Laba Rugi</h3>
            <table class="detail-table">
                <thead>
                    <tr>
                        <th>Keterangan</th>
                        <th style="text-align: right;">Jumlah (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Total Pembelian Sampah (dari Nasabah)</td>
                        <td style="text-align: right; color: #ef4444;">- {{ number_format($totalPembelian, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td>Total Penjualan Sampah (ke Offtaker)</td>
                        <td style="text-align: right; color: #22c55e;">+ {{ number_format($totalPenjualan, 0, ',', '.') }}</td>
                    </tr>
                    <tr style="background: #f9fafb; font-weight: 600;">
                        <td>Laba Kotor</td>
                        <td style="text-align: right; color: {{ $labaKotor >= 0 ? '#22c55e' : '#ef4444' }};">
                            {{ number_format($labaKotor, 0, ',', '.') }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="inventory-section">
            <h3>Status Inventori Sampah</h3>
            <div class="inventory-grid">
                <div class="inventory-item stored">
                    <div class="icon">
                        <i class="fa-solid fa-box"></i>
                    </div>
                    <div class="amount">{{ number_format($inventorySummary['stored_kg'], 2, ',', '.') }} Kg</div>
                    <div class="label">Tersimpan</div>
                </div>
                <div class="inventory-item sold">
                    <div class="icon">
                        <i class="fa-solid fa-money-bill-trend-up"></i>
                    </div>
                    <div class="amount">{{ number_format($inventorySummary['sold_kg'], 2, ',', '.') }} Kg</div>
                    <div class="label">Terjual</div>
                </div>
                <div class="inventory-item processed">
                    <div class="icon">
                        <i class="fa-solid fa-recycle"></i>
                    </div>
                    <div class="amount">{{ number_format($inventorySummary['processed_kg'], 2, ',', '.') }} Kg</div>
                    <div class="label">Diolah</div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
@endsection
