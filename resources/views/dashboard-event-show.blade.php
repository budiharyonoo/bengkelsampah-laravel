@extends('dashboard')
@section('content')
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Event - Admin Panel</title>
    <link href="https://fonts.googleapis.com/css2?family=Urbanist:wght@400;500;600;700;900&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Urbanist', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        body {
            font-family: 'Urbanist', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
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
            font-family: 'Urbanist', sans-serif;
            font-size: 22px;
            font-weight: 400;
            color: #39746E;
        }

        .header-separator {
            font-family: 'Urbanist', sans-serif;
            font-size: 22px;
            font-weight: 400;
            color: #39746E;
        }

        .header-subtitle {
            font-family: 'Urbanist', sans-serif;
            font-size: 22px;
            font-weight: 700;
            color: #39746E;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .notification {
            position: relative;
            width: 24px;
            height: 24px;
            cursor: pointer;
        }

        .notification::before {
            content: "🔔";
            font-size: 18px;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #0FB7A6;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 14px;
            font-family: 'Urbanist', sans-serif;
        }

        .user-name {
            font-family: 'Urbanist', sans-serif;
            font-weight: 700;
            font-size: 16px;
            color: #39746E;
        }

        .main-container {
            margin: 0 2rem 2rem 2rem;
        }

        .content-container {
            background: #fff;
            border: 1px solid #E5E6E6;
            border-radius: 16px;
            padding: 24px;
        }

        .content-header {
            margin-bottom: 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .content-title {
            font-family: 'Urbanist', sans-serif;
            font-size: 18px;
            font-weight: 700;
            color: #242E2C;
        }

        .form-actions {
            display: flex;
            gap: 8px;
        }

        .btn-cancel {
            padding: 8px 16px;
            background: transparent;
            border: 1px solid #FDCED1;
            border-radius: 8px;
            font-family: 'Urbanist', sans-serif;
            font-size: 14px;
            font-weight: 600;
            color: #F73541;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-block;
        }

        .btn-cancel:hover {
            background: #FDCED1;
        }

        .btn-edit {
            padding: 8px 16px;
            background: #39746E;
            border: none;
            border-radius: 8px;
            font-family: 'Urbanist', sans-serif;
            font-size: 14px;
            font-weight: 600;
            color: #DFF0EE;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-block;
        }

        .btn-edit:hover {
            background: #2d5a55;
        }

        .card {
            background: #fff;
            border: 1px solid #E5E6E6;
            border-radius: 16px;
            padding: 24px;
            margin-bottom: 1rem;
        }

        .card-header {
            padding: 0 0 16px 0;
            margin-bottom: 16px;
            border-bottom: 1px solid #E5E6E6;
            font-family: 'Urbanist', sans-serif;
            font-size: 18px;
            font-weight: 700;
            color: #242E2C;
        }

        .card-body {
            padding: 0;
        }

        .table {
            width: 100%;
            margin-bottom: 1rem;
            color: #212529;
            border-collapse: collapse;
        }

        .table th,
        .table td {
            padding: 0.75rem;
            vertical-align: top;
            border-top: 1px solid #dee2e6;
        }

        .table thead th {
            vertical-align: bottom;
            border-bottom: 2px solid #dee2e6;
            background-color: #f8f9fa;
            font-weight: 600;
            font-family: 'Urbanist', sans-serif;
        }

        .table-hover tbody tr:hover {
            color: #212529;
            background-color: rgba(0,0,0,.075);
        }

        .badge {
            display: inline-block;
            padding: 0.25em 0.4em;
            font-size: 75%;
            font-weight: 700;
            line-height: 1;
            text-align: center;
            white-space: nowrap;
            vertical-align: baseline;
            border-radius: 0.25rem;
            font-family: 'Urbanist', sans-serif;
        }

        .bg-success {
            background-color: #28a745 !important;
            color: white;
        }

        .bg-secondary {
            background-color: #6c757d !important;
            color: white;
        }

        .bg-danger {
            background-color: #dc3545 !important;
            color: white;
        }

        .event-cover {
            width: 100%;
            max-width: 400px;
            height: 250px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 1rem;
        }

        .info-row {
            display: flex;
            margin-bottom: 0.5rem;
        }

        .info-label {
            font-weight: 600;
            width: 150px;
            color: #6c757d;
            font-family: 'Urbanist', sans-serif;
        }

        .info-value {
            flex: 1;
            font-family: 'Urbanist', sans-serif;
        }

        .description-box {
            background-color: #f8f9fa;
            padding: 1rem;
            border-radius: 8px;
            border-left: 4px solid #007bff;
            margin-bottom: 1rem;
        }

        .description-box h6 {
            font-family: 'Urbanist', sans-serif;
            font-size: 14px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 8px;
        }

        .description-box p {
            font-family: 'Urbanist', sans-serif;
            font-size: 14px;
            color: #374151;
            line-height: 1.5;
            margin: 0;
        }

        .row {
            display: flex;
            flex-wrap: wrap;
            margin-right: -15px;
            margin-left: -15px;
        }

        .col-md-8 {
            flex: 0 0 66.666667%;
            max-width: 66.666667%;
            padding-right: 15px;
            padding-left: 15px;
        }

        .col-md-4 {
            flex: 0 0 33.333333%;
            max-width: 33.333333%;
            padding-right: 15px;
            padding-left: 15px;
        }

        .text-center {
            text-align: center !important;
        }

        .text-muted {
            color: #6c757d !important;
        }

        .py-4 {
            padding-top: 1.5rem !important;
            padding-bottom: 1.5rem !important;
        }

        .table-responsive {
            display: block;
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-family: 'Urbanist', sans-serif;
            font-size: 14px;
            font-weight: 500;
            color: #374151;
            margin-bottom: 8px;
        }

        .form-input {
            width: 100%;
            padding: 12px;
            border: 1px solid #E5E6E6;
            border-radius: 8px;
            font-family: 'Urbanist', sans-serif;
            font-size: 14px;
            color: #1e293b;
            background: #fff;
            transition: all 0.2s;
        }

        .form-input:focus {
            outline: none;
            border-color: #39746E;
        }

        .form-input::placeholder {
            color: #6B7271;
        }

        .form-row {
            display: flex;
            gap: 16px;
        }

        .form-row .form-group {
            flex: 1;
        }

        .form-help {
            font-family: 'Urbanist', sans-serif;
            font-size: 12px;
            color: #6B7271;
            margin-top: 4px;
        }

        .result-photos {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            margin-top: 8px;
        }

        .result-photo {
            width: 120px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid #E5E6E6;
        }

        /* Current Photos Grid Styling */
        .current-photos-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 16px;
            margin-top: 12px;
        }

        .photo-item {
            position: relative;
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid #E5E6E6;
            background: #f8f9fa;
        }

        .current-photo {
            width: 100%;
            height: auto;
            max-height: 120px;
            object-fit: contain;
            display: block;
        }

        .photo-delete-btn {
            position: absolute;
            top: 8px;
            right: 8px;
            width: 24px;
            height: 24px;
            background: rgba(220, 53, 69, 0.9);
            border: none;
            border-radius: 50%;
            color: white;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            font-weight: bold;
            transition: all 0.2s;
        }

        .photo-delete-btn:hover {
            background: rgba(220, 53, 69, 1);
            transform: scale(1.1);
        }

        .photo-item.deleted {
            opacity: 0.5;
            filter: grayscale(1);
        }

        .photo-item.deleted .photo-delete-btn {
            background: rgba(108, 117, 125, 0.9);
        }

        /* Alert Styling */
        .alert {
            padding: 12px 16px;
            margin-bottom: 20px;
            border: 1px solid transparent;
            border-radius: 8px;
            font-family: 'Urbanist', sans-serif;
            font-size: 14px;
        }

        .alert-success {
            color: #155724;
            background-color: #d4edda;
            border-color: #c3e6cb;
        }

        .alert-error {
            color: #721c24;
            background-color: #f8d7da;
            border-color: #f5c6cb;
        }

        @media (max-width: 767.98px) {
            .col-md-8, .col-md-4 {
                flex: 0 0 100%;
                max-width: 100%;
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-left">
            <h1>Event</h1>
            <span class="header-separator">/</span>
            <span class="header-subtitle">Detail Event</span>
        </div>
        <div class="user-info">
            <div class="notification"></div>
            <span class="user-name">{{ Auth::guard('admin')->user()->role ?? 'Admin' }}</span>
            <div class="user-avatar">{{ strtoupper(substr(Auth::guard('admin')->user()->name ?? 'AD', 0, 2)) }}</div>
        </div>
    </header>

    <div class="main-container">
        <div class="content-container">
            <div class="content-header">
                <h2 class="content-title">Detail Event</h2>
                <div class="form-actions">
                    <a href="{{ route('dashboard.event') }}" class="btn-cancel">Kembali ke Daftar</a>
                    <a href="{{ route('dashboard.event.edit', $event->id) }}" class="btn-edit">Edit Event</a>
                    @if($event->status === 'active' && $event->isExpired())
                    <button type="button" class="btn-edit" onclick="completeEvent()">Mark as Completed</button>
                    @endif
                </div>
            </div>

            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            Informasi Event
                        </div>
                        <div class="card-body">
                            @if($event->cover)
                            <img src="{{ $event->cover }}" alt="Event Cover" class="event-cover" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <div style="width:100%;height:200px;background:#f3f4f6;border-radius:8px;display:none;align-items:center;justify-content:center;color:#9ca3af;font-size:14px;text-align:center;">No Image</div>
                            @else
                            <div style="width:100%;height:200px;background:#f3f4f6;border-radius:8px;display:flex;align-items:center;justify-content:center;color:#9ca3af;font-size:14px;text-align:center;">No Image</div>
                            @endif

                            <div class="info-row">
                                <span class="info-label">Judul:</span>
                                <span class="info-value">{{ $event->title }}</span>
                            </div>

                            <div class="info-row">
                                <span class="info-label">Status:</span>
                                <span class="info-value">
                                    <span class="badge bg-{{ $event->status == 'active' ? 'success' : ($event->status == 'completed' ? 'secondary' : 'danger') }}">
                                        {{ ucfirst($event->status) }}
                                    </span>
                                </span>
                            </div>

                            <div class="info-row">
                                <span class="info-label">Waktu Mulai:</span>
                                <span class="info-value">{{ $event->start_datetime->format('d M Y H:i') }}</span>
                            </div>

                            <div class="info-row">
                                <span class="info-label">Waktu Selesai:</span>
                                <span class="info-value">{{ $event->end_datetime->format('d M Y H:i') }}</span>
                            </div>

                            <div class="info-row">
                                <span class="info-label">Lokasi:</span>
                                <span class="info-value">{{ $event->location }}</span>
                            </div>

                            @if($event->url)
                            <div class="info-row">
                                <span class="info-label">URL Event:</span>
                                <span class="info-value">
                                    <a href="{{ $event->url }}" target="_blank" style="color: #39746E; text-decoration: underline;">
                                        {{ $event->url }}
                                    </a>
                                </span>
                            </div>
                            @endif

                            @if($event->max_participants)
                            <div class="info-row">
                                <span class="info-label">Maksimal Peserta:</span>
                                <span class="info-value">{{ $event->max_participants }} orang</span>
                            </div>
                            @endif

                            <div class="info-row">
                                <span class="info-label">Pembuat:</span>
                                <span class="info-value">{{ $event->admin_name ?? 'Admin' }}</span>
                            </div>

                            <div class="description-box">
                                <h6>Deskripsi Event:</h6>
                                <p>{{ $event->description }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            Statistik Event
                        </div>
                        <div class="card-body">
                            <div class="info-row">
                                <span class="info-label">Total Peserta:</span>
                                <span class="info-value" style="font-weight: 700;">{{ $event->participants->count() }} orang</span>
                            </div>

                            @if($event->max_participants)
                            <div class="info-row">
                                <span class="info-label">Sisa Kuota:</span>
                                <span class="info-value" style="font-weight: 700;">{{ $event->max_participants - $event->participants->count() }} orang</span>
                            </div>
                            @endif

                            <div class="info-row">
                                <span class="info-label">Status Event:</span>
                                <span class="info-value">
                                    @if($event->isExpired())
                                        <span class="badge bg-danger">Expired</span>
                                    @elseif($event->isActive())
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    Daftar Peserta ({{ $event->participants->count() }} orang)
                </div>
                <div class="card-body">
                    @if($event->participants->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Peserta</th>
                                    <th>Identifier</th>
                                    <th>Waktu Join</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($event->participants as $index => $participant)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $participant->user_name ?? 'User tidak ditemukan' }}</td>
                                    <td>{{ $participant->user_identifier ?? '-' }}</td>
                                    <td>{{ $participant->join_datetime->format('d M Y H:i') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-4">
                        <p class="text-muted">Belum ada peserta yang join event ini.</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Hasil Event Section -->
            @if($event->status === 'completed')
            <div class="card">
                <div class="card-header">
                    Hasil Event
                </div>
                <div class="card-body">
                    <!-- Alert Container -->
                    <div id="alertContainer"></div>

                    @if($event->hasResult())
                        <!-- Show Result -->
                        <div class="info-row">
                            <span class="info-label">Deskripsi Hasil:</span>
                            <span class="info-value">{{ $event->result_description }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Jumlah Sampah Terkumpul:</span>
                            <span class="info-value">{{ $event->saved_waste_amount }} kg</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Peserta yang Hadir:</span>
                            <span class="info-value">{{ $event->actual_participants }} orang</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Dilaporkan oleh:</span>
                            <span class="info-value">{{ $event->result_submitted_by_name ?? 'Admin' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Tanggal Laporan:</span>
                            <span class="info-value">{{ $event->result_submitted_at->format('d M Y H:i') }}</span>
                        </div>

                        @if($event->result_photos)
                        <div class="info-row">
                            <span class="info-label">Foto Kegiatan:</span>
                            <div class="info-value">
                                <div class="result-photos">
                                    @foreach($event->result_photos as $photo)
                                    <div style="position:relative;display:inline-block;margin:5px;">
                                        <img src="{{ $photo }}" alt="Foto Kegiatan" class="result-photo" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                        <div style="width:120px;height:120px;background:#f3f4f6;border-radius:8px;display:none;align-items:center;justify-content:center;color:#9ca3af;font-size:12px;text-align:center;">No Image</div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @endif

                        <div class="form-actions" style="margin-top: 20px;">
                            <a href="{{ route('dashboard.event.generate-report', $event->id) }}" class="btn-edit">Download Laporan PDF</a>
                            <button type="button" class="btn-edit" onclick="showEditResultForm()">Edit Hasil Event</button>
                        </div>
                    @else
                        <!-- Submit Result Form -->
                        <form id="resultForm" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group">
                                <label class="form-label">Deskripsi Hasil Kegiatan</label>
                                <textarea class="form-input" name="result_description" placeholder="Jelaskan hasil kegiatan yang telah dilakukan" rows="4" required></textarea>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label">Jumlah Sampah Terkumpul (kg)</label>
                                    <input type="number" class="form-input" name="saved_waste_amount" placeholder="0.00" step="0.01" min="0" required>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Peserta yang Hadir</label>
                                    <input type="number" class="form-input" name="actual_participants" placeholder="0" min="0" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Foto Kegiatan (Opsional, Maksimal 5 foto)</label>
                                <input type="file" class="form-input" name="result_photos[]" id="resultPhotosInput" accept="image/*" multiple onchange="validatePhotoCount(this)">
                                <div class="form-help">Upload foto kegiatan untuk dokumentasi (maksimal 5 foto)</div>
                                <div id="photoCounter" style="margin-top: 5px; font-size: 12px; color: #666;">Foto dipilih: 0/5</div>
                                <div id="photoPreviewContainer" class="current-photos-grid" style="margin-top: 10px;"></div>
                                <button type="button" class="btn-cancel" style="margin-top: 10px;" onclick="resetPhotoSelection()">Reset Pilihan Foto</button>
                            </div>
                            <div class="form-actions">
                                <button type="submit" class="btn-edit">Submit Hasil Event</button>
                            </div>
                        </form>
                    @endif

                    <!-- Edit Result Form (Hidden by default) -->
                    @if($event->hasResult())
                    <form id="editResultForm" enctype="multipart/form-data" style="display: none; margin-top: 40px;">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label class="form-label">Deskripsi Hasil Kegiatan</label>
                            <textarea class="form-input" name="result_description" placeholder="Jelaskan hasil kegiatan yang telah dilakukan" rows="4" required>{{ $event->result_description }}</textarea>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Jumlah Sampah Terkumpul (kg)</label>
                                <input type="number" class="form-input" name="saved_waste_amount" placeholder="0.00" step="0.01" min="0" value="{{ $event->saved_waste_amount }}" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Peserta yang Hadir</label>
                                <input type="number" class="form-input" name="actual_participants" placeholder="0" min="0" value="{{ $event->actual_participants }}" required>
                            </div>
                        </div>

                        <!-- Current Photos Section -->
                        @if($event->result_photos && count($event->result_photos) > 0)
                        <div class="form-group">
                            <label class="form-label">Foto Kegiatan Saat Ini</label>
                            <div class="current-photos-grid">
                                @foreach($event->result_photos as $index => $photo)
                                <div class="photo-item" data-photo-index="{{ $index }}">
                                    <img src="{{ $photo }}" alt="Foto Kegiatan {{ $index + 1 }}" class="current-photo" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    <div style="width:100%;height:120px;background:#f3f4f6;border-radius:8px;display:none;align-items:center;justify-content:center;color:#9ca3af;font-size:12px;text-align:center;">No Image</div>
                                    <button type="button" class="photo-delete-btn" onclick="deletePhoto({{ $index }})">×</button>
                                    <input type="hidden" name="existing_photos[]" value="{{ $photo }}">
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <div class="form-group">
                            <label class="form-label">Foto Kegiatan Baru (Opsional, Maksimal 5 foto)</label>
                            <input type="file" class="form-input" name="result_photos[]" id="editResultPhotosInput" accept="image/*" multiple onchange="validatePhotoCount(this)">
                            <div class="form-help">Upload foto kegiatan baru untuk dokumentasi (maksimal 5 foto). Jika tidak memilih foto baru, foto lama akan tetap digunakan.</div>
                            <div id="editPhotoCounter" style="margin-top: 5px; font-size: 12px; color: #666;">Foto baru dipilih: 0/5</div>
                            <div id="editPhotoPreviewContainer" class="current-photos-grid" style="margin-top: 10px;"></div>
                            <button type="button" class="btn-cancel" style="margin-top: 10px;" onclick="resetEditPhotoSelection()">Reset Pilihan Foto Baru</button>
                        </div>
                        <div class="form-actions">
                            <button type="submit" class="btn-edit">Update Hasil Event</button>
                            <button type="button" class="btn-cancel" onclick="hideEditResultForm()">Batal</button>
                        </div>
                    </form>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>

    <script>
        // Alert function
        function showAlert(type, message) {
            const alertContainer = document.getElementById('alertContainer');
            if (alertContainer) {
                const alertClass = type === 'success' ? 'alert-success' : 'alert-error';
                alertContainer.innerHTML = `<div class="alert ${alertClass}">${message}</div>`;

                // Auto hide after 5 seconds
                setTimeout(() => {
                    alertContainer.innerHTML = '';
                }, 5000);
            } else {
                // Fallback to regular alert if container not found
                alert(message);
            }
        }

        // Handle result form submission
        document.addEventListener('DOMContentLoaded', function() {
            const resultForm = document.getElementById('resultForm');
            if (resultForm) {
                resultForm.addEventListener('submit', function(e) {
                    e.preventDefault();

                    console.log('Form submitted'); // Debug log

                    const formData = new FormData(this);
                    const submitBtn = this.querySelector('button[type="submit"]');
                    const originalText = submitBtn.textContent;

                    // Validate required fields
                    const resultDescription = formData.get('result_description');
                    const savedWasteAmount = formData.get('saved_waste_amount');
                    const actualParticipants = formData.get('actual_participants');

                    if (!resultDescription || !savedWasteAmount || !actualParticipants) {
                        showAlert('error', 'Mohon lengkapi semua field yang wajib diisi');
                        return;
                    }

                    submitBtn.textContent = 'Menyimpan...';
                    submitBtn.disabled = true;

                    // Add method field for Laravel
                    formData.append('_method', 'POST');

                    fetch('{{ route("dashboard.event.submit-result", $event->id) }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(response => {
                        console.log('Response status:', response.status); // Debug log
                        return response.json();
                    })
                    .then(data => {
                        console.log('Response data:', data); // Debug log
                        if (data.success) {
                            showAlert('success', data.message);
                            setTimeout(() => {
                                window.location.reload();
                            }, 1500);
                        } else {
                            showAlert('error', data.message || 'Terjadi kesalahan saat menyimpan hasil event');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showAlert('error', 'Terjadi kesalahan saat menyimpan hasil event');
                    })
                    .finally(() => {
                        submitBtn.textContent = originalText;
                        submitBtn.disabled = false;
                    });
                });
            }
        });

        // Handle complete event
        function completeEvent() {
            if (confirm('Apakah Anda yakin ingin mengubah status event ini menjadi completed?')) {
                // Show loading
                showLoading('Mengubah status event...');

                fetch('{{ route("dashboard.event.complete", $event->id) }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showAlert('success', data.message);
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    } else {
                        showAlert('error', data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('error', 'Terjadi kesalahan saat mengubah status event');
                })
                .finally(() => {
                    // Hide loading
                    hideLoading();
                });
            }
        }

        // Handle edit result form submission
        const editResultForm = document.getElementById('editResultForm');
        if (editResultForm) {
            editResultForm.addEventListener('submit', function(e) {
                e.preventDefault();

                console.log('Edit form submitted'); // Debug log

                const formData = new FormData(this);
                const submitBtn = this.querySelector('button[type="submit"]');
                const originalText = submitBtn.textContent;

                // Validate required fields
                const resultDescription = formData.get('result_description');
                const savedWasteAmount = formData.get('saved_waste_amount');
                const actualParticipants = formData.get('actual_participants');

                if (!resultDescription || !savedWasteAmount || !actualParticipants) {
                    showAlert('error', 'Mohon lengkapi semua field yang wajib diisi');
                    return;
                }

                // Show loading on button and overlay
                submitBtn.textContent = 'Mengupdate...';
                submitBtn.disabled = true;
                showLoading('Mengupdate hasil event...');

                fetch('{{ route("dashboard.event.update-result", $event->id) }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => {
                    console.log('Response status:', response.status); // Debug log
                    return response.json();
                })
                .then(data => {
                    console.log('Server response:', data); // Debug log

                    if (data.success) {
                        showAlert('success', data.message);
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    } else {
                        showAlert('error', data.message || 'Gagal mengupdate hasil event');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('error', 'Terjadi kesalahan saat mengupdate hasil event');
                })
                .finally(() => {
                    submitBtn.textContent = originalText;
                    submitBtn.disabled = false;
                    hideLoading();
                });
            });
        }

        // Show Edit Result Form
        function showEditResultForm() {
            const editResultForm = document.getElementById('editResultForm');
            if (editResultForm) {
                editResultForm.style.display = 'block';
            }
        }

        // Hide Edit Result Form
        function hideEditResultForm() {
            const editResultForm = document.getElementById('editResultForm');
            if (editResultForm) {
                editResultForm.style.display = 'none';
            }
        }

        // Validate photo count
        function validatePhotoCount(input) {
            const maxPhotos = 5;
            const files = input.files;

            // For single selection validation (when user selects files in one go)
            if (files.length > maxPhotos) {
                alert(`Maksimal ${maxPhotos} foto dapat diunggah dalam satu kali pilihan. Silakan pilih ${maxPhotos} foto atau kurang.`);
                input.value = '';
                return false;
            }

            // For accumulated selection validation (check total with existing files)
            let currentTotal = 0;
            if (input.id === 'resultPhotosInput') {
                currentTotal = selectedPhotos.length + files.length;
            } else if (input.id === 'editResultPhotosInput') {
                currentTotal = editSelectedPhotos.length + files.length;
            }

            if (currentTotal > maxPhotos) {
                alert(`Total foto tidak boleh lebih dari ${maxPhotos}. Anda sudah memiliki ${currentTotal - files.length} foto dan mencoba menambahkan ${files.length} foto lagi.`);
                input.value = '';
                return false;
            }

            return true;
        }

        // Track deleted photos
        let deletedPhotos = [];

        // Delete photo function
        function deletePhoto(photoIndex) {
            const photoItem = document.querySelector(`[data-photo-index="${photoIndex}"]`);
            const photoUrl = photoItem.querySelector('input[name="existing_photos[]"]').value;

            if (confirm('Apakah Anda yakin ingin menghapus foto ini?')) {
                // Show loading
                showLoading('Menghapus foto...');

                photoItem.classList.add('deleted');
                deletedPhotos.push(photoIndex);

                // Disable the hidden input so it won't be submitted
                photoItem.querySelector('input[name="existing_photos[]"]').disabled = true;

                // Add to deleted photos array for submission
                const deletedInput = document.createElement('input');
                deletedInput.type = 'hidden';
                deletedInput.name = 'deleted_photos[]';
                deletedInput.value = photoIndex;
                document.getElementById('editResultForm').appendChild(deletedInput);

                // Hide loading after a short delay to show the visual feedback
                setTimeout(() => {
                    hideLoading();
                }, 500);
            }
        }

        // Preview and remove photos before submit (for create result)
        let selectedPhotos = [];
        let editSelectedPhotos = [];

        // Initialize photo preview for create form
        const input = document.getElementById('resultPhotosInput');
        const previewContainer = document.getElementById('photoPreviewContainer');

        if (input) {
            input.addEventListener('change', function(e) {
                if (validatePhotoCount(this)) {
                    // Add new files to existing selection instead of replacing
                    const newFiles = Array.from(input.files);
                    selectedPhotos = selectedPhotos.concat(newFiles);

                    // Check total count after adding
                    if (selectedPhotos.length > 5) {
                        alert('Maksimal 5 foto dapat diunggah.');
                        selectedPhotos = selectedPhotos.slice(0, 5);
                    }

                    // Update input files to reflect the combined selection
                    const dataTransfer = new DataTransfer();
                    selectedPhotos.forEach(file => dataTransfer.items.add(file));
                    input.files = dataTransfer.files;

                    renderPhotoPreview();
                }
            });
        }

        // Initialize photo preview for edit form
        const editInput = document.getElementById('editResultPhotosInput');
        const editPreviewContainer = document.getElementById('editPhotoPreviewContainer');

        if (editInput) {
            editInput.addEventListener('change', function(e) {
                if (validatePhotoCount(this)) {
                    // Add new files to existing selection instead of replacing
                    const newFiles = Array.from(editInput.files);
                    editSelectedPhotos = editSelectedPhotos.concat(newFiles);

                    // Check total count after adding
                    if (editSelectedPhotos.length > 5) {
                        alert('Maksimal 5 foto dapat diunggah.');
                        editSelectedPhotos = editSelectedPhotos.slice(0, 5);
                    }

                    // Update input files to reflect the combined selection
                    const dataTransfer = new DataTransfer();
                    editSelectedPhotos.forEach(file => dataTransfer.items.add(file));
                    editInput.files = dataTransfer.files;

                    renderEditPhotoPreview();
                }
            });
        }

        function renderPhotoPreview() {
            if (!previewContainer) return;
            previewContainer.innerHTML = '';
            if (!selectedPhotos.length) return;
            selectedPhotos.forEach((file, idx) => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const photoItem = document.createElement('div');
                    photoItem.className = 'photo-item';
                    photoItem.innerHTML = `
                        <img src="${e.target.result}" alt="Preview Foto" class="current-photo">
                        <button type="button" class="photo-delete-btn" onclick="removeSelectedPhoto(${idx})">×</button>
                    `;
                    previewContainer.appendChild(photoItem);
                };
                reader.readAsDataURL(file);
            });

            // Update counter
            const counter = document.getElementById('photoCounter');
            if (counter) {
                counter.textContent = `Foto dipilih: ${selectedPhotos.length}/5`;
            }
        }

        function renderEditPhotoPreview() {
            if (!editPreviewContainer) return;
            editPreviewContainer.innerHTML = '';
            if (!editSelectedPhotos.length) return;
            editSelectedPhotos.forEach((file, idx) => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const photoItem = document.createElement('div');
                    photoItem.className = 'photo-item';
                    photoItem.innerHTML = `
                        <img src="${e.target.result}" alt="Preview Foto" class="current-photo">
                        <button type="button" class="photo-delete-btn" onclick="removeEditSelectedPhoto(${idx})">×</button>
                    `;
                    editPreviewContainer.appendChild(photoItem);
                };
                reader.readAsDataURL(file);
            });

            // Update counter
            const counter = document.getElementById('editPhotoCounter');
            if (counter) {
                counter.textContent = `Foto baru dipilih: ${editSelectedPhotos.length}/5`;
            }
        }

        window.removeSelectedPhoto = function(idx) {
            selectedPhotos.splice(idx, 1);
            // Update input files
            const dataTransfer = new DataTransfer();
            selectedPhotos.forEach(file => dataTransfer.items.add(file));
            input.files = dataTransfer.files;
            renderPhotoPreview();
        }

        window.removeEditSelectedPhoto = function(idx) {
            editSelectedPhotos.splice(idx, 1);
            // Update input files
            const dataTransfer = new DataTransfer();
            editSelectedPhotos.forEach(file => dataTransfer.items.add(file));
            editInput.files = dataTransfer.files;
            renderEditPhotoPreview();
        }

        function resetPhotoSelection() {
            selectedPhotos = [];
            input.value = '';
            renderPhotoPreview();
        }

        function resetEditPhotoSelection() {
            editSelectedPhotos = [];
            editInput.value = '';
            renderEditPhotoPreview();
        }
    </script>
</body>
</html>
@endsection
