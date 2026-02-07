<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail User - {{ $user->name }}</title>
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
            margin-bottom: 20px;
            border-bottom: 3px solid #39746E;
            padding-bottom: 15px;
        }

        .header h1 {
            color: #39746E;
            font-size: 20px;
            font-weight: bold;
            margin: 0 0 5px 0;
        }

        .header h2 {
            color: #333;
            font-size: 16px;
            font-weight: normal;
            margin: 0 0 10px 0;
        }

        .header p {
            margin: 3px 0;
            font-size: 10px;
            color: #666;
        }

        .section {
            margin-bottom: 20px;
        }

        .section-title {
            background-color: #39746E;
            color: white;
            padding: 8px 12px;
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 10px;
            border-radius: 4px;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        .info-table td {
            padding: 6px 10px;
            border: 1px solid #ddd;
            vertical-align: top;
        }

        .info-table .label {
            background-color: #f8f9fa;
            font-weight: bold;
            width: 35%;
            color: #39746E;
        }

        .info-table .value {
            width: 65%;
        }

        .stats-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        .stats-table td {
            text-align: center;
            padding: 12px 8px;
            border: 1px solid #ddd;
            background-color: #f8f9fa;
        }

        .stats-table .stat-number {
            font-size: 18px;
            font-weight: bold;
            color: #39746E;
            display: block;
            margin-bottom: 4px;
        }

        .stats-table .stat-label {
            font-size: 9px;
            color: #666;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9px;
        }

        .data-table th {
            background-color: #39746E;
            color: white;
            padding: 8px 6px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #ddd;
        }

        .data-table td {
            padding: 6px;
            border: 1px solid #ddd;
            vertical-align: top;
        }

        .data-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .empty-message {
            text-align: center;
            padding: 15px;
            color: #666;
            font-style: italic;
            background-color: #f8f9fa;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: bold;
        }

        .badge-success {
            background-color: #d4edda;
            color: #155724;
        }

        .badge-warning {
            background-color: #fff3cd;
            color: #856404;
        }

        .badge-danger {
            background-color: #f8d7da;
            color: #721c24;
        }

        .badge-info {
            background-color: #d1ecf1;
            color: #0c5460;
        }

        .badge-primary {
            background-color: #cce5ff;
            color: #004085;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 9px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>DETAIL USER</h1>
        <h2>{{ $user->name }}</h2>
        <p>Tanggal Export: {{ now()->format('d F Y H:i:s') }}</p>
    </div>

    <!-- Informasi User -->
    <div class="section">
        <div class="section-title">INFORMASI USER</div>
        <table class="info-table">
            <tr>
                <td class="label">Identifier</td>
                <td class="value">{{ $user->identifier }}</td>
            </tr>
            <tr>
                <td class="label">Nama Lengkap</td>
                <td class="value">{{ $user->name }}</td>
            </tr>
            <tr>
                <td class="label">Jenis Nasabah</td>
                <td class="value">{{ $user->jenis_nasabah }}</td>
            </tr>
            <tr>
                <td class="label">No. Telepon</td>
                <td class="value">{{ $user->phone ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Tanggal Registrasi</td>
                <td class="value">{{ $user->created_at->format('d F Y H:i') }}</td>
            </tr>
            <tr>
                <td class="label">Bank Sampah</td>
                <td class="value">{{ $user->bankSampah->nama_bank_sampah ?? '-' }}</td>
            </tr>
        </table>
    </div>

    <!-- Statistik -->
    <div class="section">
        <div class="section-title">STATISTIK</div>
        <table class="stats-table">
            <tr>
                <td>
                    <span class="stat-number">{{ number_format($user->setor ?? 0) }}</span>
                    <span class="stat-label">Total Setoran</span>
                </td>
                <td>
                    <span class="stat-number">{{ number_format($user->poin ?? 0, 2, ',', '.') }}</span>
                    <span class="stat-label">Total Poin</span>
                </td>
                <td>
                    <span class="stat-number">{{ number_format($user->xp ?? 0) }}</span>
                    <span class="stat-label">Total XP</span>
                </td>
                <td>
                    <span class="stat-number">{{ number_format($user->sampah ?? 0, 1) }} kg</span>
                    <span class="stat-label">Total Sampah</span>
                </td>
            </tr>
        </table>
    </div>

    <!-- Riwayat Setoran -->
    <div class="section">
        <div class="section-title">RIWAYAT SETORAN ({{ $user->setorans ? $user->setorans->count() : 0 }} transaksi)</div>
        @if($user->setorans && $user->setorans->count() > 0)
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 5%;">No</th>
                        <th style="width: 30%;">Bank Sampah</th>
                        <th style="width: 25%;">Tanggal</th>
                        <th style="width: 20%;">Jumlah</th>
                        <th style="width: 20%;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($user->setorans as $index => $setoran)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $setoran->bankSampah->nama_bank_sampah ?? '-' }}</td>
                        <td>{{ $setoran->created_at->format('d/m/Y H:i') }}</td>
                        <td class="text-right">Rp {{ number_format($setoran->total_harga ?? 0, 0, ',', '.') }}</td>
                        <td class="text-center">
                            @if($setoran->status == 'selesai')
                                <span class="badge badge-success">Selesai</span>
                            @elseif($setoran->status == 'diproses')
                                <span class="badge badge-warning">Diproses</span>
                            @elseif($setoran->status == 'batal')
                                <span class="badge badge-danger">Batal</span>
                            @elseif($setoran->status == 'konfirmasi')
                                <span class="badge badge-info">Konfirmasi</span>
                            @else
                                <span class="badge badge-primary">{{ ucfirst($setoran->status) }}</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="empty-message">Tidak ada data setoran</div>
        @endif
    </div>

    <!-- Riwayat Poin -->
    <div class="section">
        <div class="section-title">RIWAYAT POIN ({{ $user->points ? $user->points->count() : 0 }} transaksi)</div>
        @if($user->points && $user->points->count() > 0)
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 5%;">No</th>
                        <th style="width: 40%;">Keterangan</th>
                        <th style="width: 25%;">Tanggal</th>
                        <th style="width: 15%;">Jumlah</th>
                        <th style="width: 15%;">Tipe</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($user->points as $index => $point)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $point->keterangan ?? '-' }}</td>
                        <td>{{ $point->created_at->format('d/m/Y H:i') }}</td>
                        <td class="text-right" style="color: {{ $point->tipe === 'setor' ? '#28a745' : '#dc3545' }};">
                            {{ $point->tipe === 'setor' ? '+' : '-' }}{{ number_format($point->jumlah ?? 0, 2, ',', '.') }}
                        </td>
                        <td class="text-center">
                            @if($point->tipe === 'setor')
                                <span class="badge badge-success">Setor</span>
                            @else
                                <span class="badge badge-danger">Redeem</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="empty-message">Tidak ada riwayat poin</div>
        @endif
    </div>

    <!-- Daftar Alamat -->
    <div class="section">
        <div class="section-title">DAFTAR ALAMAT ({{ $user->addresses ? $user->addresses->count() : 0 }} alamat)</div>
        @if($user->addresses && $user->addresses->count() > 0)
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 5%;">No</th>
                        <th style="width: 15%;">Label</th>
                        <th style="width: 50%;">Alamat Lengkap</th>
                        <th style="width: 18%;">No. Telepon</th>
                        <th style="width: 12%;">Default</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($user->addresses as $index => $address)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $address->label_alamat }}</td>
                        <td>{{ $address->detail_lain }}, {{ $address->kecamatan }}, {{ $address->kota_kabupaten }}, {{ $address->provinsi }} {{ $address->kode_pos }}</td>
                        <td>{{ $address->nomor_handphone ?? '-' }}</td>
                        <td class="text-center">
                            @if($address->is_default)
                                <span class="badge badge-success">Ya</span>
                            @else
                                <span class="badge badge-primary">Tidak</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="empty-message">Tidak ada data alamat</div>
        @endif
    </div>

    <div class="footer">
        <p>Laporan ini dibuat secara otomatis oleh sistem Bengkel Sampah</p>
        <p>&copy; {{ date('Y') }} Bengkel Sampah. Semua hak cipta dilindungi.</p>
    </div>
</body>
</html>
