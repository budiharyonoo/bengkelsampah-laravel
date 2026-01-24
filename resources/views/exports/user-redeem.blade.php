<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan User Redeem Requests</title>
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

        .status-badge {
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

        .status-pending {
            background: #FCD34D;
            color: #92400E;
        }

        .status-approved {
            background: #28a745;
        }

        .status-rejected {
            background: #dc3545;
        }

        .status-cancelled {
            background: #9CA3AF;
            color: #1F2937;
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

        .footer {
            margin-top: 25px;
            text-align: center;
            font-size: 9px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 12px;
        }

        .no-data {
            text-align: center;
            padding: 40px;
            color: #666;
            font-style: italic;
            font-size: 12px;
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
        <h1>LAPORAN USER REDEEM REQUESTS</h1>
        <h2>Bengkel Sampah - Sistem Penukaran Poin</h2>
        <div class="header-info">
            <span><strong>Total Requests:</strong> {{ $redeems->count() }} requests</span>
            <span><strong>Tanggal Export:</strong> {{ now()->format('d F Y H:i:s') }}</span>
        </div>
    </div>

    @php
        $totalRequests = $redeems->count();
        $totalPoints = $redeems->sum('point');
        $pendingCount = $redeems->where('status', 'waiting')->count();
        $approvedCount = $redeems->where('status', 'approved')->count();
        $rejectedCount = $redeems->where('status', 'rejected')->count();
    @endphp

    <!-- Summary Statistics -->
    <table class="summary-table" style="width:100%; margin: 20px 0 25px 0; border-collapse: separate; border-spacing: 10px 0; background: #f8f9fa; border-radius: 8px;">
        <tr>
            <td class="stat-td">
                <div class="stat-number">{{ number_format($totalRequests) }}</div>
                <div class="stat-label">Total Requests</div>
            </td>
            <td class="stat-td">
                <div class="stat-number">{{ number_format($totalPoints) }}</div>
                <div class="stat-label">Total Poin</div>
            </td>
            <td class="stat-td">
                <div class="stat-number">{{ number_format($pendingCount) }}</div>
                <div class="stat-label">Pending</div>
            </td>
            <td class="stat-td">
                <div class="stat-number">{{ number_format($approvedCount) }}</div>
                <div class="stat-label">Approved</div>
            </td>
            <td class="stat-td">
                <div class="stat-number">{{ number_format($rejectedCount) }}</div>
                <div class="stat-label">Rejected</div>
            </td>
        </tr>
    </table>

    @if($redeems->count() > 0)
        <!-- Detail Table -->
        <table>
            <thead>
                <tr>
                    <th width="3%">No</th>
                    <th width="10%">Tanggal Request</th>
                    <th width="15%">Nama User</th>
                    <th width="15%">Email User</th>
                    <th width="18%">Nama Item</th>
                    <th width="10%">Point Digunakan</th>
                    <th width="10%">Status</th>
                    <th width="19%">Diproses Oleh</th>
                </tr>
            </thead>
            <tbody>
                @foreach($redeems as $index => $redeem)
                    @php
                        // Map status
                        $statusText = 'Pending';
                        $statusClass = 'status-pending';

                        if ($redeem->status === 'approved') {
                            $statusText = 'Approved';
                            $statusClass = 'status-approved';
                        } elseif ($redeem->status === 'rejected') {
                            $statusText = 'Rejected';
                            $statusClass = 'status-rejected';
                        } elseif ($redeem->status === 'cancelled') {
                            $statusText = 'Cancelled';
                            $statusClass = 'status-cancelled';
                        }
                    @endphp
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ \Carbon\Carbon::parse($redeem->created_at)->format('d/m/Y H:i') }}</td>
                        <td class="font-bold">{{ $redeem->user->name ?? '-' }}</td>
                        <td>{{ $redeem->user->identifier ?? '-' }}</td>
                        <td>{{ $redeem->redeemItem->name ?? $redeem->redeem_item_name }}</td>
                        <td class="text-right font-bold" style="color: #e74c3c;">{{ number_format($redeem->point_used) }}</td>
                        <td class="text-center">
                            <span class="status-badge {{ $statusClass }}">{{ $statusText }}</span>
                        </td>
                        <td>
                            @if($redeem->statusChangedBy)
                                {{ $redeem->statusChangedBy->name ?? '-' }}
                                @if($redeem->updated_at && $redeem->status !== 'waiting')
                                    <br><span style="font-size: 8px; color: #999;">{{ \Carbon\Carbon::parse($redeem->updated_at)->format('d/m/Y H:i') }}</span>
                                @endif
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="no-data">
            <h3>Tidak ada data redeem request</h3>
            <p>Belum ada permintaan penukaran poin yang tercatat dalam sistem.</p>
        </div>
    @endif

    <div class="footer">
        <p><strong>Laporan User Redeem Requests - Bengkel Sampah Management System</strong></p>
        <p>Dokumen ini digenerate secara otomatis pada {{ now()->format('d F Y, H:i:s') }} WIB</p>
        <p>&copy; {{ date('Y') }} Bengkel Sampah. All rights reserved.</p>
    </div>
</body>
</html>
