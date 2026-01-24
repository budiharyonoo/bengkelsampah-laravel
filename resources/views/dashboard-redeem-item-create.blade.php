@extends('dashboard')
@section('content')
@include('partials.dashboard-header-bar', ['title' => 'Item Redeem > Tambah'])
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Item Redeem - Admin Panel</title>
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

        .form-input, .form-textarea, .form-select {
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

        .form-input:focus, .form-textarea:focus, .form-select:focus {
            outline: none;
            border-color: #39746E;
        }

        .form-input::placeholder, .form-textarea::placeholder {
            color: #6B7271;
        }

        .form-textarea {
            min-height: 100px;
            resize: vertical;
        }

        .error-message {
            color: #F73541;
            font-size: 12px;
            margin-top: 4px;
            display: none;
        }

        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }

        .loading-content {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            text-align: center;
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
    </style>
</head>
<body>
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-content">
            <div class="loading-spinner-large"></div>
            <p>Menyimpan data...</p>
        </div>
    </div>

    <div class="form-container">
        <div class="form-header">
            <h2 class="form-title">Tambah Item Redeem</h2>
            <div class="form-actions">
                <a href="{{ route('dashboard.redeem-item.index') }}" class="btn-cancel">Batal</a>
                <button type="submit" form="redeemItemForm" class="btn-submit">Simpan</button>
            </div>
        </div>

        <form id="redeemItemForm">
            <div class="form-group">
                <label for="name" class="form-label">Nama Item *</label>
                <input type="text" id="name" name="name" class="form-input" placeholder="Masukkan nama item" required>
                <span class="error-message" id="nameError"></span>
            </div>

            <div class="form-group">
                <label for="description" class="form-label">Deskripsi</label>
                <textarea id="description" name="description" class="form-textarea" placeholder="Masukkan deskripsi item"></textarea>
                <span class="error-message" id="descriptionError"></span>
            </div>

            <div class="form-group">
                <label for="points_required" class="form-label">Point Required *</label>
                <input type="number" id="points_required" name="points_required" class="form-input" placeholder="Masukkan jumlah point" min="1" required>
                <span class="error-message" id="pointsError"></span>
            </div>

            <div class="form-group">
                <label for="is_active" class="form-label">Status *</label>
                <select id="is_active" name="is_active" class="form-select" required>
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>
                <span class="error-message" id="statusError"></span>
            </div>
        </form>
    </div>

    <script>
        document.getElementById('redeemItemForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = {
                name: document.getElementById('name').value,
                description: document.getElementById('description').value,
                points_required: parseInt(document.getElementById('points_required').value),
                is_active: parseInt(document.getElementById('is_active').value) === 1
            };

            document.getElementById('loadingOverlay').style.display = 'flex';

            fetch('{{ route("dashboard.redeem-item.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify(formData)
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById('loadingOverlay').style.display = 'none';
                if (data.success) {
                    alert(data.message || 'Item redeem berhasil ditambahkan');
                    window.location.href = '{{ route("dashboard.redeem-item.index") }}';
                } else {
                    alert('Gagal menambahkan item: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('loadingOverlay').style.display = 'none';
                alert('Terjadi kesalahan saat menyimpan data');
            });
        });
    </script>
</body>
</html>
@endsection
