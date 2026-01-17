@extends('dashboard')
@section('content')
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Inventori - Admin Panel</title>
    <link href="https://fonts.googleapis.com/css2?family=Urbanist:wght@400;500;600;700;900&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

        .page-container {
            padding: 0 2rem 2rem 2rem;
        }

        .metrics-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
            margin-bottom: 24px;
        }

        .metric-card {
            background: #fff;
            border: 1px solid #E5E6E6;
            border-radius: 12px;
            padding: 20px;
        }

        .metric-label {
            font-size: 13px;
            color: #6B7271;
            margin-bottom: 8px;
        }

        .metric-value {
            font-size: 24px;
            font-weight: 700;
            color: #39746E;
        }

        .metric-value.secondary {
            color: #1E40AF;
        }

        .metric-value.tertiary {
            color: #9D174D;
        }

        .metric-subtext {
            font-size: 12px;
            color: #6B7271;
            margin-top: 4px;
        }

        .grid-2col {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 24px;
            margin-bottom: 24px;
        }

        .container {
            background: #fff;
            border: 1px solid #E5E6E6;
            border-radius: 16px;
            padding: 24px;
        }

        .container-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
        }

        .container-title {
            font-size: 18px;
            font-weight: 700;
            color: #242E2C;
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

        .data-table tbody tr:hover {
            background: #F9FAFB;
        }

        .low-stock {
            color: #DC2626;
        }

        .badge-warning {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: 600;
            background: #FEF3C7;
            color: #92400E;
            margin-left: 4px;
        }

        .chart-container {
            height: 300px;
            position: relative;
        }

        .low-stock-list {
            margin-top: 16px;
        }

        .low-stock-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #E5E6E6;
            font-size: 13px;
        }

        .low-stock-item:last-child {
            border-bottom: none;
        }

        .low-stock-name {
            color: #1e293b;
        }

        .low-stock-qty {
            color: #DC2626;
            font-weight: 600;
        }

        .bank-info {
            margin-bottom: 24px;
            padding: 16px;
            background: #F9FAFB;
            border-radius: 12px;
        }

        .bank-info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
        }

        .bank-info-item {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .bank-info-label {
            font-size: 12px;
            color: #6B7271;
        }

        .bank-info-value {
            font-size: 14px;
            font-weight: 600;
            color: #1e293b;
        }

        @media (max-width: 1024px) {
            .grid-2col {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .metrics-grid {
                grid-template-columns: 1fr;
            }
            .bank-info-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-left">
            <h1>Inventori</h1>
            <span class="header-separator">/</span>
            <span class="header-subtitle">{{ $bankSampah->nama_bank_sampah }}</span>
        </div>
        <a href="{{ route('waste-inventory.index') }}" class="btn-back">Kembali</a>
    </div>

    <div class="page-container">
        <!-- Bank Sampah Info -->
        <div class="bank-info">
            <div class="bank-info-grid">
                <div class="bank-info-item">
                    <span class="bank-info-label">Kode</span>
                    <span class="bank-info-value">{{ $bankSampah->kode_bank_sampah }}</span>
                </div>
                <div class="bank-info-item">
                    <span class="bank-info-label">Nama</span>
                    <span class="bank-info-value">{{ $bankSampah->nama_bank_sampah }}</span>
                </div>
                <div class="bank-info-item" style="grid-column: span 2;">
                    <span class="bank-info-label">Alamat</span>
                    <span class="bank-info-value">{{ $bankSampah->alamat_bank_sampah ?? '-' }}</span>
                </div>
            </div>
        </div>

        <!-- Metrics -->
        <div class="metrics-grid">
            <div class="metric-card">
                <div class="metric-label">Tersimpan</div>
                <div class="metric-value">{{ number_format($summary['stored_kg'], 2) }} kg</div>
                <div class="metric-subtext">Nilai: Rp {{ number_format($summary['stored_value'], 0, ',', '.') }}</div>
            </div>
            <div class="metric-card">
                <div class="metric-label">Terjual</div>
                <div class="metric-value secondary">{{ number_format($summary['sold_kg'], 2) }} kg</div>
                <div class="metric-subtext">Nilai: Rp {{ number_format($summary['sold_value'], 0, ',', '.') }}</div>
            </div>
            <div class="metric-card">
                <div class="metric-label">Diolah</div>
                <div class="metric-value tertiary">{{ number_format($summary['processed_kg'], 2) }} kg</div>
                <div class="metric-subtext">Nilai: Rp {{ number_format($summary['processed_value'], 0, ',', '.') }}</div>
            </div>
        </div>

        <!-- Charts and Low Stock -->
        <div class="grid-2col">
            <div class="container">
                <div class="container-header">
                    <h3 class="container-title">Trend 30 Hari Terakhir</h3>
                </div>
                <div class="chart-container">
                    <canvas id="trendChart"></canvas>
                </div>
            </div>

            <div class="container">
                <div class="container-header">
                    <h3 class="container-title">Stok Rendah</h3>
                </div>
                @if($lowStockItems->count() > 0)
                    <div class="low-stock-list">
                        @foreach($lowStockItems as $item)
                            <div class="low-stock-item">
                                <span class="low-stock-name">{{ $item->sampah->nama ?? 'Unknown' }}</span>
                                <span class="low-stock-qty">{{ number_format($item->quantity, 2) }} {{ $item->unit }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p style="color: #6B7271; text-align: center; padding: 20px;">Tidak ada item dengan stok rendah.</p>
                @endif
            </div>
        </div>

        <!-- Inventory Table -->
        <div class="container">
            <div class="container-header">
                <h3 class="container-title">Daftar Inventori</h3>
            </div>
            <div class="table-container">
                @if($inventory->count() > 0)
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Jenis Sampah</th>
                                <th>Stok Tersedia</th>
                                <th>Satuan</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($inventory as $index => $item)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $item->sampah->nama ?? 'Unknown' }}</td>
                                    <td class="{{ $item->quantity < 10 ? 'low-stock' : '' }}">
                                        {{ number_format($item->quantity, 2) }}
                                    </td>
                                    <td>{{ $item->unit }}</td>
                                    <td>
                                        @if($item->quantity < 10)
                                            <span class="badge-warning">Stok Rendah</span>
                                        @else
                                            <span style="color: #059669; font-size: 12px;">Normal</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p style="color: #6B7271; text-align: center; padding: 40px;">Belum ada data inventori.</p>
                @endif
            </div>
        </div>
    </div>

    <script>
        const trendData = @json($trend);

        new Chart(document.getElementById('trendChart'), {
            type: 'line',
            data: {
                labels: trendData.labels,
                datasets: [
                    {
                        label: 'Masuk',
                        data: trendData.stored,
                        borderColor: '#39746E',
                        backgroundColor: 'rgba(57, 116, 110, 0.1)',
                        tension: 0.3,
                        fill: true
                    },
                    {
                        label: 'Terjual',
                        data: trendData.sold,
                        borderColor: '#1E40AF',
                        backgroundColor: 'rgba(30, 64, 175, 0.1)',
                        tension: 0.3,
                        fill: true
                    },
                    {
                        label: 'Diolah',
                        data: trendData.processed,
                        borderColor: '#9D174D',
                        backgroundColor: 'rgba(157, 23, 77, 0.1)',
                        tension: 0.3,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>
</html>
@endsection
