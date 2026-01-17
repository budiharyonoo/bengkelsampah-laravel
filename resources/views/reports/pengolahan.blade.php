@extends('dashboard')
@section('content')
@include('partials.dashboard-header-bar', ['title' => 'Laporan Pengolahan'])
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Pengolahan - Admin Panel</title>
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
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .summary-card {
            background: #fff;
            border: 1px solid #E5E6E6;
            border-radius: 12px;
            padding: 1.5rem;
        }

        .summary-card.total {
            border-left: 4px solid #387478;
        }

        .summary-card.qty {
            border-left: 4px solid #f59e0b;
        }

        .summary-card.output {
            border-left: 4px solid #22c55e;
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

        .method-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 2rem;
        }

        .method-table th,
        .method-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #E5E6E6;
        }

        .method-table th {
            font-weight: 600;
            color: #6b7280;
            font-size: 13px;
            background: #f9fafb;
        }

        .method-table td {
            font-size: 14px;
            color: #374151;
        }

        .method-table tr:hover {
            background: #fafafa;
        }

        .text-right {
            text-align: right !important;
        }

        .period-info {
            font-size: 14px;
            color: #6b7280;
            margin-bottom: 1rem;
        }

        .section-title {
            font-size: 16px;
            font-weight: 600;
            margin: 2rem 0 1rem;
            color: #374151;
        }

        .method-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .method-badge.kompos {
            background: #dcfce7;
            color: #166534;
        }

        .method-badge.daur_ulang {
            background: #dbeafe;
            color: #1e40af;
        }

        .method-badge.rdf {
            background: #fef3c7;
            color: #92400e;
        }

        .method-badge.lainnya {
            background: #f3f4f6;
            color: #4b5563;
        }

        .empty-state {
            text-align: center;
            padding: 3rem;
            color: #9ca3af;
        }

        .empty-state i {
            font-size: 48px;
            margin-bottom: 1rem;
        }

        .efficiency-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
        }

        .efficiency-good {
            background: #dcfce7;
            color: #166534;
        }

        .efficiency-medium {
            background: #fef3c7;
            color: #92400e;
        }

        .efficiency-low {
            background: #fee2e2;
            color: #991b1b;
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
                <form method="POST" action="{{ route('reports.pengolahan.export.excel') }}" style="display:inline;">
                    @csrf
                    <input type="hidden" name="periode" value="{{ $periode }}">
                    <input type="hidden" name="bank_sampah_id" value="{{ $selectedBankSampah }}">
                    <button type="submit" class="btn btn-success">
                        <i class="fa-solid fa-file-excel"></i> Export Excel
                    </button>
                </form>
                <form method="POST" action="{{ route('reports.pengolahan.export.pdf') }}" style="display:inline;">
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
            <div class="summary-card total">
                <div class="label">Total Transaksi Pengolahan</div>
                <div class="value">{{ $processingMetrics['count'] ?? 0 }}</div>
            </div>
            <div class="summary-card qty">
                <div class="label">Total Input (Kg)</div>
                <div class="value">{{ number_format($processingMetrics['total_quantity'] ?? 0, 2, ',', '.') }}</div>
            </div>
            <div class="summary-card output">
                <div class="label">Total Output (Kg)</div>
                <div class="value">{{ number_format($processingMetrics['output_quantity'] ?? 0, 2, ',', '.') }}</div>
            </div>
        </div>

        <h3 class="section-title">Ringkasan per Metode Pengolahan</h3>

        @if(count($processingByMethod) > 0)
            <table class="method-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Metode Pengolahan</th>
                        <th class="text-right">Jumlah Transaksi</th>
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
                            $methodClass = match($item['method'] ?? '') {
                                'kompos' => 'kompos',
                                'daur_ulang' => 'daur_ulang',
                                'rdf' => 'rdf',
                                default => 'lainnya'
                            };
                            $methodLabel = match($item['method'] ?? '') {
                                'kompos' => 'Kompos',
                                'daur_ulang' => 'Daur Ulang',
                                'rdf' => 'RDF (Refuse Derived Fuel)',
                                default => ucfirst($item['method'] ?? 'Lainnya')
                            };
                            $efficiencyClass = $efficiency >= 80 ? 'efficiency-good' : ($efficiency >= 50 ? 'efficiency-medium' : 'efficiency-low');
                        @endphp
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <span class="method-badge {{ $methodClass }}">
                                    <i class="fa-solid fa-recycle"></i>
                                    {{ $methodLabel }}
                                </span>
                            </td>
                            <td class="text-right">{{ $item['count'] ?? 0 }}</td>
                            <td class="text-right">{{ number_format($item['total_quantity'] ?? 0, 2, ',', '.') }}</td>
                            <td class="text-right">{{ number_format($item['output_quantity'] ?? 0, 2, ',', '.') }}</td>
                            <td class="text-right">
                                <span class="efficiency-badge {{ $efficiencyClass }}">
                                    {{ number_format($efficiency, 1) }}%
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    @php
                        $totalEfficiency = $totalInput > 0 ? ($totalOutput / $totalInput) * 100 : 0;
                        $totalEfficiencyClass = $totalEfficiency >= 80 ? 'efficiency-good' : ($totalEfficiency >= 50 ? 'efficiency-medium' : 'efficiency-low');
                    @endphp
                    <tr style="background: #f3f4f6; font-weight: 600;">
                        <td colspan="3">Total</td>
                        <td class="text-right">{{ number_format($totalInput, 2, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($totalOutput, 2, ',', '.') }}</td>
                        <td class="text-right">
                            <span class="efficiency-badge {{ $totalEfficiencyClass }}">
                                {{ number_format($totalEfficiency, 1) }}%
                            </span>
                        </td>
                    </tr>
                </tfoot>
            </table>
        @else
            <div class="empty-state">
                <i class="fa-solid fa-recycle"></i>
                <p>Tidak ada data pengolahan untuk periode ini</p>
            </div>
        @endif
    </div>
</body>
</html>
@endsection
