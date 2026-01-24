@extends('dashboard')
@section('content')
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>User Redeem Request Detail - Admin Panel</title>
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
            display: flex;
            gap: 1rem;
            margin: 0 2rem 2rem 2rem;
        }
        .detail-container {
            flex: 1;
            background: #fff;
            border: 1px solid #E5E6E6;
            border-radius: 16px;
            padding: 24px;
        }
        .detail-header {
            margin-bottom: 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .detail-title {
            font-family: 'Urbanist', sans-serif;
            font-size: 18px;
            font-weight: 700;
            color: #242E2C;
        }
        .detail-actions {
            display: flex;
            gap: 8px;
        }
        .btn-back {
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
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .btn-back:hover {
            background: #FDCED1;
        }
        .info-section {
            margin-bottom: 32px;
        }
        .info-title {
            font-family: 'Urbanist', sans-serif;
            font-size: 16px;
            font-weight: 600;
            color: #39746E;
            margin-bottom: 16px;
            padding-bottom: 8px;
            border-bottom: 2px solid #E3F4F1;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
        }
        .info-item {
            display: flex;
            flex-direction: column;
        }
        .info-label {
            font-family: 'Urbanist', sans-serif;
            font-size: 14px;
            font-weight: 500;
            color: #6B7271;
            margin-bottom: 8px;
        }
        .info-value {
            font-family: 'Urbanist', sans-serif;
            font-size: 16px;
            font-weight: 600;
            color: #242E2C;
            padding: 12px;
            background: #F8F9FA;
            border-radius: 8px;
            border: 1px solid #E5E6E6;
        }
        .info-value.full-width {
            grid-column: 1 / -1;
        }
        .identifier-badge {
            display: inline-block;
            padding: 8px 16px;
            background: #E3F4F1;
            color: #39746E;
            border-radius: 20px;
            font-family: 'Urbanist', sans-serif;
            font-size: 14px;
            font-weight: 600;
        }
        .status-badge {
            display: inline-block;
            padding: 6px 16px;
            border-radius: 12px;
            font-size: 13px;
            font-weight: 700;
            border: 1.5px solid;
            background: transparent;
        }
        .status-waiting {
            color: #D97706;
            border-color: #D97706;
            background: #FEF3C7;
        }
        .status-approved {
            color: #10B981;
            border-color: #10B981;
            background: #D1FAE5;
        }
        .status-rejected {
            color: #EF4444;
            border-color: #EF4444;
            background: #FEE2E2;
        }
        .status-cancelled {
            color: #6B7280;
            border-color: #9CA3AF;
            background: #F3F4F6;
        }
        .form-group {
            margin-bottom: 24px;
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
        .form-textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #E5E6E6;
            border-radius: 8px;
            font-family: 'Urbanist', sans-serif;
            font-size: 14px;
            color: #1e293b;
            background: #fff;
            transition: all 0.2s;
            min-height: 100px;
            resize: vertical;
        }
        .form-textarea:focus {
            outline: none;
            border-color: #39746E;
        }
        .form-file {
            width: 100%;
            padding: 12px;
            border: 1px solid #E5E6E6;
            border-radius: 8px;
            font-family: 'Urbanist', sans-serif;
            font-size: 14px;
            color: #1e293b;
            background: #fff;
            cursor: pointer;
        }
        .form-text {
            font-family: 'Urbanist', sans-serif;
            font-size: 12px;
            color: #6B7271;
            margin-top: 4px;
        }
        .form-info {
            background: #E3F4F1;
            border: 1px solid #D1F2EB;
            border-radius: 8px;
            padding: 12px;
            margin-bottom: 1.5rem;
            color: #0FB7A6;
            font-size: 14px;
        }
        .form-warning {
            background: #FFF3CD;
            border: 1px solid #FFEAA7;
            border-radius: 8px;
            padding: 12px;
            margin-bottom: 1.5rem;
            color: #856404;
            font-size: 14px;
        }
        .form-success {
            background: #D1FAE5;
            border: 1px solid #A7F3D0;
            border-radius: 8px;
            padding: 12px;
            margin-bottom: 1.5rem;
            color: #065F46;
            font-size: 14px;
        }
        .form-error {
            background: #FEE2E2;
            border: 1px solid #FECACA;
            border-radius: 8px;
            padding: 12px;
            margin-bottom: 1.5rem;
            color: #991B1B;
            font-size: 14px;
        }
        .file-preview {
            margin-top: 8px;
            padding: 12px;
            background: #F8F9FA;
            border-radius: 8px;
            border: 1px solid #E5E6E6;
        }
        .file-link {
            color: #39746E;
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .file-link:hover {
            text-decoration: underline;
        }
        .action-buttons {
            display: flex;
            gap: 12px;
            margin-top: 32px;
            padding-top: 24px;
            border-top: 2px solid #E5E6E6;
        }
        .btn-approve {
            flex: 1;
            padding: 12px 24px;
            background: #10B981;
            border: none;
            border-radius: 8px;
            font-family: 'Urbanist', sans-serif;
            font-size: 14px;
            font-weight: 600;
            color: white;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .btn-approve:hover {
            background: #059669;
        }
        .btn-reject {
            flex: 1;
            padding: 12px 24px;
            background: #EF4444;
            border: none;
            border-radius: 8px;
            font-family: 'Urbanist', sans-serif;
            font-size: 14px;
            font-weight: 600;
            color: white;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .btn-reject:hover {
            background: #DC2626;
        }
        .processed-info {
            background: #F8F9FA;
            border: 1px solid #E5E6E6;
            border-radius: 8px;
            padding: 16px;
            margin-top: 24px;
        }
        .processed-info-title {
            font-family: 'Urbanist', sans-serif;
            font-size: 14px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 8px;
        }
        .processed-info-text {
            font-family: 'Urbanist', sans-serif;
            font-size: 14px;
            color: #6B7271;
        }
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        }
        .loading-content {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }
        .loading-spinner-large {
            width: 40px;
            height: 40px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #00B6A0;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 1rem;
        }
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        @media (max-width: 768px) {
            .info-grid {
                grid-template-columns: 1fr;
            }
            .action-buttons {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <!-- Loading Overlay -->
    <div class="loading-overlay" id="pageLoadingOverlay" style="display: none;">
        <div class="loading-content">
            <div class="loading-spinner-large"></div>
            <p id="loadingMessage">Memuat...</p>
        </div>
    </div>

    <header class="header">
        <div class="header-left">
            <h1>User Redeem Request</h1>
            <span class="header-separator">/</span>
            <span class="header-subtitle">Detail</span>
        </div>
        <div class="user-info">
            <div class="notification"></div>
            <span class="user-name">{{ Auth::guard('admin')->user()->role ?? 'Admin' }}</span>
            <div class="user-avatar">{{ strtoupper(substr(Auth::guard('admin')->user()->name ?? 'A', 0, 2)) }}</div>
        </div>
    </header>

    <div class="main-container">
        <div class="detail-container">
            <div class="detail-header">
                <div class="detail-title">Detail Redeem Request</div>
                <div class="detail-actions">
                    <a href="{{ route('dashboard.user-redeem.index') }}" class="btn-back">Kembali</a>
                </div>
            </div>

            @if(session('success'))
            <div class="form-success">
                <strong>Sukses!</strong> {{ session('success') }}
            </div>
            @endif

            @if(session('error'))
            <div class="form-error">
                <strong>Error!</strong> {{ session('error') }}
            </div>
            @endif

            @if($errors->any())
            <div class="form-error">
                <strong>Terdapat kesalahan:</strong>
                <ul style="margin: 8px 0 0 20px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <!-- Request Information -->
            <div class="info-section">
                <h3 class="info-title">Informasi Request</h3>
                <div class="info-grid">
                    <div class="info-item">
                        <span class="info-label">Request ID</span>
                        <div class="info-value">#{{ $redeem->id }}</div>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Tanggal Request</span>
                        <div class="info-value">{{ $redeem->created_at->format('d M Y H:i') }}</div>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Status</span>
                        <div class="info-value">
                            @php
                                $statusClass = 'status-' . $redeem->status;
                                $statusText = [
                                    'waiting' => 'Pending',
                                    'approved' => 'Approved',
                                    'rejected' => 'Rejected',
                                    'cancelled' => 'Cancelled'
                                ][$redeem->status] ?? ucfirst($redeem->status);
                            @endphp
                            <span class="status-badge {{ $statusClass }}">{{ $statusText }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- User Information -->
            <div class="info-section">
                <h3 class="info-title">Informasi User</h3>
                <div class="info-grid">
                    <div class="info-item">
                        <span class="info-label">Nama User</span>
                        <div class="info-value">{{ $redeem->user_name }}</div>
                    </div>
                    <div class="info-item">
                        <span class="info-label">User Identifier</span>
                        <div class="info-value">
                            <span class="identifier-badge">{{ $redeem->user_identifier }}</span>
                        </div>
                    </div>
                    @if($redeem->user)
                    <div class="info-item">
                        <span class="info-label">Poin User Saat Ini</span>
                        <div class="info-value">{{ number_format($redeem->user->poin, 2, ',', '.') }} Poin</div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Item Information -->
            <div class="info-section">
                <h3 class="info-title">Informasi Item</h3>
                <div class="info-grid">
                    <div class="info-item">
                        <span class="info-label">Nama Item</span>
                        <div class="info-value">{{ $redeem->redeem_item_name }}</div>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Poin yang Di-redeem</span>
                        <div class="info-value" style="color: #EF4444;">{{ number_format($redeem->point_used, 0, ',', '.') }} Poin</div>
                    </div>
                    @if($redeem->redeem_item_description)
                    <div class="info-item" style="grid-column: 1 / -1;">
                        <span class="info-label">Deskripsi Item</span>
                        <div class="info-value">{{ $redeem->redeem_item_description }}</div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Form for Editing -->
            <form id="redeemForm" method="POST" action="{{ route('dashboard.user-redeem.update', $redeem->id) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" name="action" id="actionInput" value="">

                <!-- Info JSON Fields -->
                @if($redeem->info_json && is_array($redeem->info_json) && count($redeem->info_json) > 0)
                <div class="info-section">
                    <h3 class="info-title">Informasi Tambahan</h3>
                    <div class="form-info">
                        <strong>Info:</strong> Data berikut dapat diedit sesuai kebutuhan pengiriman reward.
                    </div>
                    <div class="info-grid">
                        @foreach($redeem->info_json as $index => $item)
                        <div class="info-item">
                            <label class="form-label" for="info_{{ $index }}">{{ $item['label'] ?? 'Info ' . ($index + 1) }}</label>
                            <input type="text" class="form-input" id="info_{{ $index }}" name="info_json[{{ $index }}][value]" value="{{ old('info_json.' . $index . '.value', $item['value'] ?? '') }}" placeholder="Masukkan {{ strtolower($item['label'] ?? 'info') }}" {{ $redeem->status !== 'waiting' ? 'readonly' : '' }}>
                            <input type="hidden" name="info_json[{{ $index }}][label]" value="{{ $item['label'] ?? '' }}">
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Reward Proof Upload -->
                <div class="info-section">
                    <h3 class="info-title">Bukti Reward</h3>
                    <div class="form-group">
                        <label class="form-label" for="reward_proof">Upload Bukti Pengiriman Reward</label>
                        <input type="file" class="form-file" id="reward_proof" name="reward_proof" accept=".pdf,.jpg,.jpeg,.png" {{ $redeem->status !== 'waiting' ? 'disabled' : '' }}>
                        <small class="form-text">Format: PDF, JPG, JPEG, PNG. Maksimal ukuran: 5MB</small>

                        @if($redeem->reward_proof_url)
                        <div class="file-preview">
                            <span class="form-label" style="margin-bottom: 8px; display: block;">Bukti Saat Ini:</span>
                            <a href="{{ asset($redeem->reward_proof_url) }}" target="_blank" class="file-link">
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M14 10V12.6667C14 13.0203 13.8595 13.3594 13.6095 13.6095C13.3594 13.8595 13.0203 14 12.6667 14H3.33333C2.97971 14 2.64057 13.8595 2.39052 13.6095C2.14048 13.3594 2 13.0203 2 12.6667V10M11.3333 5.33333L8 2M8 2L4.66667 5.33333M8 2V10" stroke="#39746E" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                Lihat File
                            </a>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Notes -->
                <div class="info-section">
                    <h3 class="info-title">Catatan Admin</h3>
                    <div class="form-group">
                        <label class="form-label" for="notes">Catatan</label>
                        <textarea class="form-textarea" id="notes" name="notes" placeholder="Tambahkan catatan untuk request ini (opsional)" {{ $redeem->status !== 'waiting' ? 'readonly' : '' }}>{{ old('notes', $redeem->notes) }}</textarea>
                        <small class="form-text">Catatan ini akan tersimpan untuk referensi internal.</small>
                    </div>
                </div>

                <!-- Action Buttons or Processed Info -->
                @if($redeem->isWaiting())
                <div class="form-warning">
                    <strong>Perhatian!</strong> Setelah approve atau reject, status tidak dapat diubah kembali. Pastikan semua data sudah benar.
                </div>
                <div class="action-buttons">
                    <button type="button" class="btn-approve" onclick="submitForm('approve')">
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M16.6668 5L7.50016 14.1667L3.3335 10" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        Approve Request
                    </button>
                    <button type="button" class="btn-reject" onclick="submitForm('reject')">
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M15 5L5 15M5 5L15 15" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        Reject Request
                    </button>
                </div>
                @else
                <div class="processed-info">
                    <div class="processed-info-title">Request Telah Diproses</div>
                    <div class="processed-info-text">
                        Request ini sudah diproses dengan status <strong>{{ $statusText }}</strong>
                        @if($redeem->status_changed_by_name)
                            oleh <strong>{{ $redeem->status_changed_by_name }}</strong>
                        @endif
                        @if($redeem->status_changed_at)
                            pada {{ $redeem->status_changed_at->format('d M Y H:i') }}.
                        @endif
                    </div>
                </div>
                @endif
            </form>
        </div>
    </div>

    <script>
        // Loading helper functions
        function showLoading(message = 'Memuat...') {
            document.getElementById('loadingMessage').textContent = message;
            document.getElementById('pageLoadingOverlay').style.display = 'flex';
        }

        function hideLoading() {
            document.getElementById('pageLoadingOverlay').style.display = 'none';
        }

        // Show loading on page load
        document.addEventListener('DOMContentLoaded', function() {
            showLoading('Memuat data redeem request...');
            setTimeout(() => {
                hideLoading();
            }, 500);
        });

        // Add loading for navigation
        document.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', function() {
                showLoading('Memuat halaman...');
            });
        });

        // Submit form with action
        function submitForm(action) {
            const actionText = action === 'approve' ? 'menyetujui' : 'menolak';
            const confirmMessage = `Apakah Anda yakin ingin ${actionText} redeem request ini?`;

            if (confirm(confirmMessage)) {
                showLoading(action === 'approve' ? 'Memproses approval...' : 'Memproses rejection...');
                document.getElementById('actionInput').value = action;
                document.getElementById('redeemForm').submit();
            }
        }

        // File upload validation
        document.getElementById('reward_proof').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                // Check file size (5MB = 5 * 1024 * 1024 bytes)
                const maxSize = 5 * 1024 * 1024;
                if (file.size > maxSize) {
                    alert('Ukuran file terlalu besar. Maksimal 5MB.');
                    e.target.value = '';
                    return;
                }

                // Check file type
                const allowedTypes = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'];
                if (!allowedTypes.includes(file.type)) {
                    alert('Format file tidak didukung. Gunakan PDF, JPG, JPEG, atau PNG.');
                    e.target.value = '';
                    return;
                }
            }
        });
    </script>
</body>
</html>
@endsection
