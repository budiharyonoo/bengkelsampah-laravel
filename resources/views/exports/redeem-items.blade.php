<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Export Data Item Redeem</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            color: #333;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #39746E;
        }

        .header h1 {
            font-size: 18px;
            color: #39746E;
            margin-bottom: 5px;
        }

        .header p {
            font-size: 10px;
            color: #666;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th {
            background-color: #39746E;
            color: white;
            padding: 8px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #2d5a55;
        }

        td {
            padding: 6px 8px;
            border: 1px solid #ddd;
        }

        tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 9px;
            color: #999;
        }

        .status-badge {
            padding: 2px 8px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
        }

        .status-active {
            background-color: #E3F4F1;
            color: #39746E;
        }

        .status-inactive {
            background-color: #FEE2E2;
            color: #DC2626;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Data Item Redeem</h1>
        <p>Tanggal Export: {{ $exportDate }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center" width="5%">No</th>
                <th width="25%">Nama Item</th>
                <th width="35%">Deskripsi</th>
                <th class="text-right" width="15%">Point Required</th>
                <th class="text-center" width="10%">Status</th>
                <th width="10%">Created At</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($redeemItems as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $item->name }}</td>
                    <td>{{ $item->description ?? '-' }}</td>
                    <td class="text-right">{{ number_format($item->points_required, 0, ',', '.') }}</td>
                    <td class="text-center">
                        <span class="status-badge {{ $item->is_active ? 'status-active' : 'status-inactive' }}">
                            {{ $item->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td>{{ $item->created_at->format('d M Y') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">Tidak ada data</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Dokumen ini digenerate secara otomatis oleh Sistem Bengkel Sampah</p>
    </div>
</body>
</html>
