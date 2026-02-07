@extends('dashboard')
@section('content')
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Offtaker - Admin Panel</title>
    <link href="https://fonts.googleapis.com/css2?family=Urbanist:wght@400;500;600;700;900&display=swap" rel="stylesheet">
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
            font-weight: 400;
            color: #39746E;
        }

        .header-subtitle {
            font-size: 22px;
            font-weight: 700;
            color: #39746E;
        }

        .main-container {
            display: flex;
            gap: 1rem;
            margin: 0 2rem 2rem 2rem;
        }

        .info-container {
            flex: 1;
            background: #fff;
            border: 1px solid #E5E6E6;
            border-radius: 16px;
            padding: 24px;
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

        .btn-edit {
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

        .btn-edit:hover {
            background: #2d5a55;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 24px;
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
            font-size: 16px;
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

        .badge-buyer {
            background: #DBEAFE;
            color: #1E40AF;
        }

        .badge-processor {
            background: #FCE7F3;
            color: #9D174D;
        }

        .badge-both {
            background: #E0E7FF;
            color: #3730A3;
        }

        .badge-active {
            background: #D1FAE5;
            color: #065F46;
        }

        .badge-inactive {
            background: #FEE2E2;
            color: #991B1B;
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
            font-size: 13px;
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

        .badge-sale {
            background: #D1FAE5;
            color: #065F46;
        }

        .badge-processing {
            background: #DBEAFE;
            color: #1E40AF;
        }

        .empty-state {
            text-align: center;
            padding: 40px;
            color: #6B7271;
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
            <h1>Offtaker</h1>
            <span class="header-separator">/</span>
            <span class="header-subtitle">{{ $offtaker->kode_offtaker }}</span>
        </div>
    </div>

    <div class="main-container">
        <div class="info-container">
            <div class="info-header">
                <h2 class="info-title">Informasi Offtaker</h2>
                <div class="info-actions">
                    <a href="{{ route('offtakers.index') }}" class="btn-back">Kembali</a>
                    <a href="{{ route('offtakers.edit', $offtaker) }}" class="btn-edit">Edit</a>
                </div>
            </div>

            <div class="info-grid">
                <div class="info-item">
                    <span class="info-label">Kode Offtaker</span>
                    <span class="info-value">{{ $offtaker->kode_offtaker }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Nama</span>
                    <span class="info-value">{{ $offtaker->nama }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Tipe</span>
                    <span class="info-value">
                        @if($offtaker->tipe === 'buyer')
                            <span class="badge badge-buyer">Pembeli</span>
                        @elseif($offtaker->tipe === 'processor')
                            <span class="badge badge-processor">Pengolah</span>
                        @else
                            <span class="badge badge-both">Keduanya</span>
                        @endif
                    </span>
                </div>
                <div class="info-item">
                    <span class="info-label">Status</span>
                    <span class="info-value">
                        @if($offtaker->is_active)
                            <span class="badge badge-active">Aktif</span>
                        @else
                            <span class="badge badge-inactive">Nonaktif</span>
                        @endif
                    </span>
                </div>
                <div class="info-item">
                    <span class="info-label">Nama PIC</span>
                    <span class="info-value">{{ $offtaker->nama_pic }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Kontak PIC</span>
                    <span class="info-value">{{ $offtaker->kontak_pic }}</span>
                </div>
                <div class="info-item" style="grid-column: span 2;">
                    <span class="info-label">Alamat</span>
                    <span class="info-value">{{ $offtaker->alamat ?: '-' }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Total Transaksi</span>
                    <span class="info-value">{{ $offtaker->waste_transactions_count }} transaksi</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Dibuat</span>
                    <span class="info-value">{{ $offtaker->created_at->format('d M Y H:i') }}</span>
                </div>
            </div>

            <h3 class="section-title">Transaksi Terakhir</h3>
            <div class="table-container">
                @if($offtaker->wasteTransactions->count() > 0)
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Tipe</th>
                                <th>Bank Sampah</th>
                                <th>Tanggal</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($offtaker->wasteTransactions as $transaction)
                                <tr>
                                    <td><strong>{{ $transaction->kode_transaksi }}</strong></td>
                                    <td>
                                        @if($transaction->type === 'sale')
                                            <span class="badge badge-sale">Penjualan</span>
                                        @else
                                            <span class="badge badge-processing">Pengolahan</span>
                                        @endif
                                    </td>
                                    <td>{{ $transaction->bankSampah->nama_bank_sampah ?? '-' }}</td>
                                    <td>{{ $transaction->tanggal_transaksi->format('d M Y') }}</td>
                                    <td>
                                        @if($transaction->type === 'sale')
                                            Rp {{ number_format($transaction->total_value, 0, ',', '.') }}
                                        @else
                                            {{ number_format($transaction->total_quantity, 2) }} kg
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="empty-state">
                        <p>Belum ada transaksi dengan offtaker ini.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</body>
</html>
@endsection
