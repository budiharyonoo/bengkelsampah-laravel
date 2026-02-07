@extends('dashboard')
@section('content')
@include('partials.dashboard-header-bar', ['title' => 'Blast Notification > Kirim'])
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kirim Blast Notification - Admin Panel</title>
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

        .form-container {
            max-width: 800px;
            background: #fff;
            border: 1px solid #E5E6E6;
            border-radius: 16px;
            padding: 24px;
        }

        .form-header {
            margin-bottom: 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .form-title {
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

        .btn-submit {
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
        }

        .btn-submit:hover {
            background: #2d5a55;
        }

        .btn-submit:disabled {
            background: #9CA3AF;
            cursor: not-allowed;
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

        .form-label.required::after {
            content: ' *';
            color: #F73541;
        }

        .form-input,
        .form-textarea {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid #E5E6E6;
            border-radius: 8px;
            font-family: 'Urbanist', sans-serif;
            font-size: 14px;
            color: #1e293b;
            transition: border-color 0.2s;
        }

        .form-input:focus,
        .form-textarea:focus {
            outline: none;
            border-color: #39746E;
        }

        .form-textarea {
            min-height: 120px;
            resize: vertical;
        }

        .form-help {
            font-family: 'Urbanist', sans-serif;
            font-size: 12px;
            color: #6B7271;
            margin-top: 4px;
        }

        .info-box {
            padding: 16px;
            background: #E3F4F1;
            border-left: 4px solid #39746E;
            border-radius: 8px;
            margin-bottom: 24px;
        }

        .info-box-title {
            font-family: 'Urbanist', sans-serif;
            font-size: 14px;
            font-weight: 600;
            color: #39746E;
            margin-bottom: 8px;
        }

        .info-box-text {
            font-family: 'Urbanist', sans-serif;
            font-size: 13px;
            color: #374151;
            line-height: 1.6;
        }

        .topic-display {
            display: inline-block;
            padding: 6px 12px;
            background: #F3F4F6;
            border-radius: 8px;
            font-family: 'Urbanist', sans-serif;
            font-size: 14px;
            font-weight: 600;
            color: #39746E;
        }

        .error-message {
            color: #F73541;
            font-size: 12px;
            margin-top: 4px;
            display: none;
        }

        .error-message.show {
            display: block;
        }

        .form-input.error,
        .form-textarea.error {
            border-color: #F73541;
        }

        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: none;
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
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .loading-spinner-large {
            width: 40px;
            height: 40px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #00B6A0;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 1rem auto;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @media (max-width: 768px) {
            .form-container {
                padding: 16px;
            }

            .form-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 12px;
            }
        }
    </style>
</head>
<body>
    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-content">
            <div class="loading-spinner-large"></div>
            <p style="margin-top: 1rem; font-family: 'Urbanist', sans-serif; color: #374151;">Mengirim notifikasi...</p>
        </div>
    </div>

    <div class="form-container">
        <div class="form-header">
            <h1 class="form-title">Kirim Blast Notification</h1>
            <div class="form-actions">
                <a href="{{ route('dashboard.blast-notification.index') }}" class="btn-cancel">Batal</a>
                <button type="submit" form="blastNotificationForm" class="btn-submit" id="submitButton">Kirim Notifikasi</button>
            </div>
        </div>

        <div class="info-box">
            <div class="info-box-title">Topic Tujuan</div>
            <div class="info-box-text">
                Notifikasi akan dikirim ke topic: <span class="topic-display">{{ $topicLabel }}</span>
            </div>
        </div>

        <form id="blastNotificationForm">
            @csrf

            <div class="form-group">
                <label class="form-label required" for="title">Judul Notifikasi</label>
                <input
                    type="text"
                    id="title"
                    name="title"
                    class="form-input"
                    placeholder="Masukkan judul notifikasi"
                    maxlength="255"
                    required>
                <div class="form-help">Maksimal 255 karakter</div>
                <div class="error-message" id="titleError"></div>
            </div>

            <div class="form-group">
                <label class="form-label required" for="body">Isi Pesan</label>
                <textarea
                    id="body"
                    name="body"
                    class="form-textarea"
                    placeholder="Masukkan isi pesan notifikasi"
                    maxlength="1000"
                    required></textarea>
                <div class="form-help">Maksimal 1000 karakter</div>
                <div class="error-message" id="bodyError"></div>
            </div>
        </form>
    </div>

    <script>
        document.getElementById('blastNotificationForm').addEventListener('submit', function(e) {
            e.preventDefault();

            // Clear previous errors
            clearErrors();

            // Get form data
            const formData = {
                title: document.getElementById('title').value.trim(),
                body: document.getElementById('body').value.trim(),
            };

            // Basic validation
            let hasError = false;

            if (!formData.title) {
                showError('title', 'Judul notifikasi harus diisi');
                hasError = true;
            }

            if (!formData.body) {
                showError('body', 'Isi pesan harus diisi');
                hasError = true;
            }

            if (hasError) {
                return;
            }

            // Show loading
            document.getElementById('loadingOverlay').style.display = 'flex';
            document.getElementById('submitButton').disabled = true;

            // Send request
            fetch('{{ route("dashboard.blast-notification.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                },
                body: JSON.stringify(formData)
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById('loadingOverlay').style.display = 'none';
                document.getElementById('submitButton').disabled = false;

                if (data.success) {
                    alert(data.message || 'Blast notification berhasil dikirim!');
                    window.location.href = '{{ route("dashboard.blast-notification.index") }}';
                } else {
                    // Handle validation errors
                    if (data.errors) {
                        Object.keys(data.errors).forEach(field => {
                            showError(field, data.errors[field][0]);
                        });
                    } else {
                        alert(data.message || 'Gagal mengirim blast notification');
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('loadingOverlay').style.display = 'none';
                document.getElementById('submitButton').disabled = false;
                alert('Terjadi kesalahan saat mengirim notifikasi');
            });
        });

        function showError(field, message) {
            const input = document.getElementById(field);
            const errorDiv = document.getElementById(field + 'Error');

            if (input && errorDiv) {
                input.classList.add('error');
                errorDiv.textContent = message;
                errorDiv.classList.add('show');
            }
        }

        function clearErrors() {
            document.querySelectorAll('.error-message').forEach(el => {
                el.classList.remove('show');
                el.textContent = '';
            });
            document.querySelectorAll('.form-input, .form-textarea').forEach(el => {
                el.classList.remove('error');
            });
        }

        // Clear error on input
        document.querySelectorAll('.form-input, .form-textarea').forEach(el => {
            el.addEventListener('input', function() {
                this.classList.remove('error');
                const errorDiv = document.getElementById(this.id + 'Error');
                if (errorDiv) {
                    errorDiv.classList.remove('show');
                }
            });
        });
    </script>
</body>
</html>
@endsection
