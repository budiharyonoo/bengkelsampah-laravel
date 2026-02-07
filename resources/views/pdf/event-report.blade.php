<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Event Bengkel Sampah</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #39746E;
            padding-bottom: 20px;
        }
        .header h1 {
            color: #39746E;
            margin: 0;
            font-size: 24px;
            font-weight: bold;
        }
        .header p {
            margin: 5px 0;
            font-size: 12px;
            color: #666;
        }
        .info-section {
            margin-bottom: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }
        .info-section h3 {
            margin: 0 0 10px 0;
            color: #39746E;
            font-size: 16px;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            font-size: 12px;
        }
        .info-item {
            display: flex;
            justify-content: space-between;
        }
        .info-label {
            font-weight: bold;
            color: #555;
        }
        .info-value {
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 10px;
        }
        th {
            background-color: #39746E;
            color: white;
            padding: 8px;
            text-align: left;
            font-weight: bold;
        }
        td {
            padding: 6px 8px;
            border-bottom: 1px solid #ddd;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .status-active {
            color: #28a745;
            font-weight: bold;
        }
        .status-completed {
            color: #007bff;
            font-weight: bold;
        }
        .status-cancelled {
            color: #dc3545;
            font-weight: bold;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        .page-break {
            page-break-before: always;
        }
        .cover-link {
            color: #007bff;
            text-decoration: underline;
            font-size: 10px;
            font-weight: 500;
        }
        .no-cover {
            color: #999;
            font-style: italic;
            font-size: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN DATA EVENT BENGKEL SAMPAH</h1>
        <p>Periode: {{ $period }} (Berdasarkan waktu {{ $timeType }}){{ $statusFilter }}</p>
        <p>Total Data: {{ $totalData }} event</p>
        <p>Tanggal Export: {{ $exportDate }}</p>
    </div>

    <div class="info-section">
        <h3>Informasi Laporan</h3>
        <div class="info-grid">
            <div class="info-item">
                <span class="info-label">Periode:</span>
                <span class="info-value">{{ $period }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Berdasarkan Waktu:</span>
                <span class="info-value">{{ $timeType }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Total Event:</span>
                <span class="info-value">{{ $totalData }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Tanggal Export:</span>
                <span class="info-value">{{ $exportDate }}</span>
            </div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>ID Event</th>
                <th>Judul Event</th>
                <th>Lokasi</th>
                <th>URL Event</th>
                <th>Waktu Mulai</th>
                <th>Waktu Berakhir</th>
                <th>Status</th>
                <th>Jumlah Peserta</th>
                <th>Foto Cover</th>
            </tr>
        </thead>
        <tbody>
            @foreach($events as $index => $event)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $event->id }}</td>
                <td>{{ $event->title }}</td>
                <td>{{ $event->location }}</td>
                <td>{{ $event->url ?? '-' }}</td>
                <td>{{ $event->start_datetime->format('d/m/Y H:i') }}</td>
                <td>{{ $event->end_datetime->format('d/m/Y H:i') }}</td>
                <td class="status-{{ $event->status }}">{{ ucfirst($event->status) }}</td>
                <td>{{ $event->participants_count }}</td>
                <td>{!! $event->cover ? '<a href="' . $event->cover . '" class="cover-link">Lihat Cover</a>' : '<span class="no-cover">-</span>' !!}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Laporan ini dibuat secara otomatis oleh sistem Bengkel Sampah</p>
        <p>© {{ date('Y') }} Bengkel Sampah. All rights reserved.</p>
    </div>
</body>
</html>