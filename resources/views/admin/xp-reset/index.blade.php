@extends('dashboard')
@section('content')
    @include('partials.dashboard-header-bar', ['title' => 'Reset User XP'])
    <!DOCTYPE html>
    <html lang="id">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Reset User XP - Admin Panel</title>
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
                font-family: 'Urbanist', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
                background: #fff;
                color: #1e293b;
            }

            .container {
                max-width: 1000px;
                margin: 0 auto;
            }

            .card {
                background: #fff;
                border: 1px solid #E5E6E6;
                border-radius: 16px;
                padding: 24px;
                margin-bottom: 24px;
            }

            .card-title {
                font-family: 'Urbanist', sans-serif;
                font-size: 20px;
                font-weight: 700;
                color: #242E2C;
                margin-bottom: 16px;
            }

            .warning-box {
                background: #FFF3CD;
                border: 1px solid #FFEAA7;
                border-radius: 12px;
                padding: 16px;
                margin-bottom: 24px;
                color: #856404;
            }

            .warning-box strong {
                display: block;
                margin-bottom: 8px;
                font-weight: 700;
            }

            .info-box {
                background: #E3F4F1;
                border: 1px solid #D1F2EB;
                border-radius: 12px;
                padding: 16px;
                margin-bottom: 24px;
                color: #0FB7A6;
            }

            .top-users-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
                gap: 16px;
                margin-bottom: 24px;
            }

            .top-user-card {
                background: linear-gradient(135deg, #0FB7A6 0%, #39746E 100%);
                border-radius: 12px;
                padding: 20px;
                color: white;
                position: relative;
                overflow: hidden;
            }

            .top-user-card.rank-1 {
                background: linear-gradient(135deg, #FFD700 0%, #FFA500 100%);
                color: #000;
            }

            .top-user-card.rank-2 {
                background: linear-gradient(135deg, #C0C0C0 0%, #808080 100%);
            }

            .top-user-card.rank-3 {
                background: linear-gradient(135deg, #CD7F32 0%, #8B4513 100%);
            }

            .rank-badge {
                font-size: 48px;
                font-weight: 900;
                opacity: 0.2;
                position: absolute;
                right: 16px;
                top: 8px;
            }

            .user-rank {
                font-size: 14px;
                font-weight: 600;
                margin-bottom: 8px;
                text-transform: uppercase;
            }

            .user-name {
                font-size: 18px;
                font-weight: 700;
                margin-bottom: 8px;
            }

            .user-xp {
                font-size: 24px;
                font-weight: 900;
            }

            .user-xp-label {
                font-size: 12px;
                opacity: 0.8;
            }

            .no-data {
                text-align: center;
                padding: 40px;
                color: #6B7271;
                font-style: italic;
            }

            .btn-container {
                display: flex;
                gap: 12px;
                justify-content: flex-end;
                margin-top: 24px;
            }

            .btn {
                padding: 12px 24px;
                border-radius: 8px;
                font-family: 'Urbanist', sans-serif;
                font-size: 14px;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.2s;
                border: none;
                display: inline-flex;
                align-items: center;
                gap: 8px;
            }

            .btn-danger {
                background: #F73541;
                color: white;
            }

            .btn-danger:hover:not(:disabled) {
                background: #d32028;
            }

            .btn-danger:disabled {
                background: #ccc;
                cursor: not-allowed;
                opacity: 0.6;
            }

            .btn-secondary {
                background: transparent;
                border: 1px solid #E5E6E6;
                color: #6B7271;
            }

            .btn-secondary:hover {
                background: #6B7271;
                color: white;
            }

            /* Modal Styles */
            .modal-overlay {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0, 0, 0, 0.5);
                z-index: 9999;
                align-items: center;
                justify-content: center;
            }

            .modal-overlay.active {
                display: flex;
            }

            .modal-content {
                background: white;
                border-radius: 16px;
                padding: 24px;
                max-width: 500px;
                width: 90%;
                box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            }

            .modal-header {
                display: flex;
                align-items: center;
                gap: 12px;
                margin-bottom: 16px;
            }

            .modal-icon {
                width: 48px;
                height: 48px;
                background: #FEE2E2;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 24px;
            }

            .modal-title {
                font-size: 20px;
                font-weight: 700;
                color: #242E2C;
            }

            .modal-body {
                margin-bottom: 24px;
                color: #6B7271;
                line-height: 1.6;
            }

            .modal-actions {
                display: flex;
                gap: 12px;
                justify-content: flex-end;
            }

            .checkbox-container {
                display: flex;
                align-items: start;
                gap: 12px;
                margin-bottom: 16px;
                padding: 12px;
                background: #F8FAFC;
                border-radius: 8px;
            }

            .checkbox-container input[type="checkbox"] {
                margin-top: 2px;
                width: 18px;
                height: 18px;
                cursor: pointer;
            }

            .checkbox-container label {
                cursor: pointer;
                user-select: none;
                font-size: 14px;
                color: #242E2C;
            }
        </style>
    </head>

    <body>
        <div class="container">
            <div class="card">
                <h1 class="card-title">Reset User XP</h1>

                <div class="warning-box">
                    <strong>⚠️ PERHATIAN</strong>
                    Reset XP adalah tindakan yang tidak dapat dibatalkan. Pastikan Anda benar-benar yakin sebelum
                    melanjutkan. Notifikasi akan dikirim ke semua device user.
                </div>

                <div class="info-box">
                    <strong>ℹ️ Informasi</strong>
                    Setelah reset, semua user akan menerima notifikasi yang berisi nama top 3 user dengan XP tertinggi pada
                    periode ini.
                </div>

                <h2 class="card-title">Preview Top 3 User XP</h2>

                @if ($topUsers->isEmpty())
                    <div class="no-data">
                        Tidak ada data user dengan XP
                    </div>
                @else
                    <div class="top-users-grid">
                        @foreach ($topUsers as $index => $user)
                            <div class="top-user-card rank-{{ $index + 1 }}">
                                <div class="rank-badge">{{ $index + 1 }}</div>
                                <div class="user-rank">Top {{ $index + 1 }}</div>
                                <div class="user-name">{{ $user->name }}</div>
                                <div class="user-xp">
                                    {{ number_format($user->xp, 0, ',', '.') }}
                                    <span class="user-xp-label">XP</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                <div class="btn-container">
                    <a href="{{ route('admin.xp-reset.history') }}" class="btn btn-secondary">
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path d="M6 12L2 8L6 4" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" />
                            <path d="M2 8H14" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                        Lihat History
                    </a>
                    <button type="button" class="btn btn-danger" id="btnResetXP"
                        {{ $topUsers->isEmpty() ? 'disabled' : '' }}>
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path d="M14 2L2 14M2 2L14 14" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                        </svg>
                        Reset Semua XP
                    </button>
                </div>
            </div>
        </div>

        <!-- Confirmation Modal -->
        <div class="modal-overlay" id="confirmModal">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="modal-icon">⚠️</div>
                    <h3 class="modal-title">Konfirmasi Reset XP</h3>
                </div>
                <div class="modal-body">
                    <p>Anda akan mereset XP semua user menjadi 0. Tindakan ini:</p>
                    <ul style="margin-left: 20px; margin-top: 12px; margin-bottom: 12px;">
                        <li>Tidak dapat dibatalkan</li>
                        <li>Akan mengirim notifikasi ke semua user via FCM</li>
                        <li>Akan menyimpan data top 3 user ke history</li>
                    </ul>
                    <div class="checkbox-container">
                        <input type="checkbox" id="confirmCheck">
                        <label for="confirmCheck">
                            Saya memahami bahwa tindakan ini tidak dapat dibatalkan dan akan mengirim notifikasi ke semua
                            user
                        </label>
                    </div>
                </div>
                <div class="modal-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeModal()">Batal</button>
                    <button type="button" class="btn btn-danger" id="btnConfirmReset" disabled>
                        Ya, Reset XP
                    </button>
                </div>
            </div>
        </div>

        <script>
            const btnResetXP = document.getElementById('btnResetXP');
            const confirmModal = document.getElementById('confirmModal');
            const confirmCheck = document.getElementById('confirmCheck');
            const btnConfirmReset = document.getElementById('btnConfirmReset');

            // Show modal
            btnResetXP.addEventListener('click', function() {
                confirmModal.classList.add('active');
            });

            // Close modal
            function closeModal() {
                confirmModal.classList.remove('active');
                confirmCheck.checked = false;
                btnConfirmReset.disabled = true;
            }

            // Enable confirm button when checkbox is checked
            confirmCheck.addEventListener('change', function() {
                btnConfirmReset.disabled = !this.checked;
            });

            // Execute reset
            btnConfirmReset.addEventListener('click', function() {
                if (!confirmCheck.checked) {
                    alert('Silakan centang konfirmasi terlebih dahulu');
                    return;
                }

                btnConfirmReset.disabled = true;
                btnConfirmReset.textContent = 'Memproses...';

                fetch('{{ route('admin.xp-reset.execute') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({})
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('✅ ' + data.message);
                            window.location.reload();
                        } else {
                            alert('❌ ' + data.message);
                            btnConfirmReset.disabled = false;
                            btnConfirmReset.textContent = 'Ya, Reset XP';
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('❌ Terjadi kesalahan saat mereset XP');
                        btnConfirmReset.disabled = false;
                        btnConfirmReset.textContent = 'Ya, Reset XP';
                    });
            });

            // Close modal when clicking outside
            confirmModal.addEventListener('click', function(e) {
                if (e.target === confirmModal) {
                    closeModal();
                }
            });
        </script>
    </body>

    </html>
@endsection
