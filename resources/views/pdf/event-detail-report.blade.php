<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Event - {{ $event->title }}</title>
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
            width: 30%;
            color: #39746E;
        }

        .info-table .value {
            width: 70%;
        }

        .cover-image {
            text-align: center;
            margin: 15px 0;
        }

        .cover-image img {
            max-width: 100%;
            max-height: 250px;
            border-radius: 8px;
            border: 1px solid #ddd;
        }

        .description-box {
            background-color: #f8f9fa;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 11px;
            line-height: 1.6;
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

        .result-photos {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 10px;
        }

        .result-photo {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 4px;
            border: 1px solid #ddd;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 9px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }

        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN DETAIL EVENT</h1>
        <h2>{{ $event->title }}</h2>
        <p>Tanggal Export: {{ now()->translatedFormat('d F Y H:i:s') }}</p>
    </div>

    <!-- Cover Image -->
    @if($event->cover_local)
    <div class="cover-image">
        <img src="{{ $event->cover_local }}" alt="Cover Event">
    </div>
    @endif

    <!-- Informasi Event -->
    <div class="section">
        <div class="section-title">INFORMASI EVENT</div>
        <table class="info-table">
            <tr>
                <td class="label">ID Event</td>
                <td class="value">{{ $event->id }}</td>
            </tr>
            <tr>
                <td class="label">Judul Event</td>
                <td class="value">{{ $event->title }}</td>
            </tr>
            <tr>
                <td class="label">Periode</td>
                <td class="value">{{ $period }}</td>
            </tr>
            <tr>
                <td class="label">Lokasi</td>
                <td class="value">{{ $event->location }}</td>
            </tr>
            @if($event->url)
            <tr>
                <td class="label">URL Event</td>
                <td class="value">{{ $event->url }}</td>
            </tr>
            @endif
            <tr>
                <td class="label">Status</td>
                <td class="value">
                    @if($event->status === 'active')
                        <span class="badge badge-success">Active</span>
                    @elseif($event->status === 'completed')
                        <span class="badge badge-info">Completed</span>
                    @elseif($event->status === 'cancelled')
                        <span class="badge badge-danger">Cancelled</span>
                    @else
                        <span class="badge badge-primary">{{ ucfirst($event->status) }}</span>
                    @endif
                </td>
            </tr>
            <tr>
                <td class="label">Max Peserta</td>
                <td class="value">{{ $event->max_participants ?? 'Tidak dibatasi' }}</td>
            </tr>
            <tr>
                <td class="label">Admin</td>
                <td class="value">{{ $event->admin_name ?? '-' }}</td>
            </tr>
        </table>
    </div>

    <!-- Deskripsi -->
    <div class="section">
        <div class="section-title">DESKRIPSI EVENT</div>
        <div class="description-box">
            {!! nl2br(e($event->description)) !!}
        </div>
    </div>

    <!-- Statistik -->
    <div class="section">
        <div class="section-title">STATISTIK</div>
        <table class="stats-table">
            <tr>
                <td>
                    <span class="stat-number">{{ $event->participants->count() }}</span>
                    <span class="stat-label">Peserta Terdaftar</span>
                </td>
                <td>
                    <span class="stat-number">{{ $event->actual_participants ?? '-' }}</span>
                    <span class="stat-label">Peserta Hadir</span>
                </td>
                <td>
                    <span class="stat-number">{{ $event->saved_waste_amount ? number_format($event->saved_waste_amount, 1) . ' kg' : '-' }}</span>
                    <span class="stat-label">Sampah Terselamatkan</span>
                </td>
            </tr>
        </table>
    </div>

    <!-- Daftar Peserta -->
    <div class="section">
        <div class="section-title">DAFTAR PESERTA ({{ $event->participants->count() }} orang)</div>
        @if($event->participants->count() > 0)
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 5%;">No</th>
                        <th style="width: 30%;">Nama</th>
                        <th style="width: 25%;">No. Telepon</th>
                        <th style="width: 25%;">Tanggal Daftar</th>
                        <th style="width: 15%;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($event->participants as $index => $participant)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $participant->user->name ?? $participant->name ?? '-' }}</td>
                        <td>{{ $participant->user->phone ?? $participant->phone ?? '-' }}</td>
                        <td>{{ $participant->created_at->translatedFormat('d/m/Y H:i') }}</td>
                        <td class="text-center">
                            @if($participant->status === 'registered')
                                <span class="badge badge-success">Terdaftar</span>
                            @elseif($participant->status === 'attended')
                                <span class="badge badge-info">Hadir</span>
                            @elseif($participant->status === 'cancelled')
                                <span class="badge badge-danger">Batal</span>
                            @else
                                <span class="badge badge-primary">{{ ucfirst($participant->status ?? 'Terdaftar') }}</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="empty-message">Belum ada peserta terdaftar</div>
        @endif
    </div>

    <!-- Hasil Event -->
    @if($event->hasResult())
    <div class="section">
        <div class="section-title">HASIL EVENT</div>
        <table class="info-table">
            <tr>
                <td class="label">Tanggal Submit</td>
                <td class="value">{{ $event->result_submitted_at->translatedFormat('d F Y H:i') }}</td>
            </tr>
            <tr>
                <td class="label">Disubmit Oleh</td>
                <td class="value">{{ $event->result_submitted_by_name ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Peserta Hadir</td>
                <td class="value">{{ $event->actual_participants ?? '-' }} orang</td>
            </tr>
            <tr>
                <td class="label">Sampah Terselamatkan</td>
                <td class="value">{{ $event->saved_waste_amount ? number_format($event->saved_waste_amount, 1) . ' kg' : '-' }}</td>
            </tr>
        </table>

        @if($event->result_description)
        <div style="margin-top: 10px;">
            <strong style="color: #39746E;">Deskripsi Hasil:</strong>
            <div class="description-box" style="margin-top: 5px;">
                {!! nl2br(e($event->result_description)) !!}
            </div>
        </div>
        @endif

        @if(!empty($event->result_photos_local))
        <div style="margin-top: 15px;">
            <strong style="color: #39746E;">Foto Dokumentasi:</strong>
            <div class="result-photos">
                @foreach($event->result_photos_local as $photo)
                    <img src="{{ $photo }}" alt="Foto Hasil" class="result-photo">
                @endforeach
            </div>
        </div>
        @endif
    </div>
    @endif

    <div class="footer">
        <p>Laporan ini dibuat secara otomatis oleh sistem Bengkel Sampah</p>
        <p>&copy; {{ date('Y') }} Bengkel Sampah. Semua hak cipta dilindungi.</p>
    </div>
</body>
</html>
