@extends('dashboard')
@section('content')
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User - Admin Panel</title>
    <link href="https://fonts.googleapis.com/css2?family=Urbanist:wght@400;500;600;700;900&display=swap" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Urbanist', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; }
        body { font-family: 'Urbanist', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #fff; color: #1e293b; }
        .header { padding: 1rem 2rem; display: flex; justify-content: space-between; align-items: center; }
        .header-left { display: flex; align-items: center; gap: 0.5rem; }
        .header-left h1 { font-family: 'Urbanist', sans-serif; font-size: 22px; font-weight: 400; color: #39746E; }
        .header-separator { font-family: 'Urbanist', sans-serif; font-size: 22px; font-weight: 400; color: #39746E; }
        .header-subtitle { font-family: 'Urbanist', sans-serif; font-size: 22px; font-weight: 700; color: #39746E; }
        .user-info { display: flex; align-items: center; gap: 1rem; }
        .notification { position: relative; width: 24px; height: 24px; cursor: pointer; }
        .notification::before { content: "🔔"; font-size: 18px; }
        .user-avatar { width: 40px; height: 40px; border-radius: 50%; background: #0FB7A6; color: white; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 14px; font-family: 'Urbanist', sans-serif; }
        .user-name { font-family: 'Urbanist', sans-serif; font-weight: 700; font-size: 16px; color: #39746E; }
        .main-container { display: flex; gap: 1rem; margin: 0 2rem 2rem 2rem; }
        .form-container { flex: 1; background: #fff; border: 1px solid #E5E6E6; border-radius: 16px; padding: 24px; }
        .form-header { margin-bottom: 24px; display: flex; justify-content: space-between; align-items: center; }
        .form-title { font-family: 'Urbanist', sans-serif; font-size: 18px; font-weight: 700; color: #242E2C; }
        .form-actions { display: flex; gap: 8px; }
        .btn-cancel { padding: 8px 16px; background: transparent; border: 1px solid #FDCED1; border-radius: 8px; font-family: 'Urbanist', sans-serif; font-size: 14px; font-weight: 600; color: #F73541; cursor: pointer; transition: all 0.2s; }
        .btn-cancel:hover { background: #FDCED1; }
        .btn-save { padding: 8px 16px; background: #39746E; border: none; border-radius: 8px; font-family: 'Urbanist', sans-serif; font-size: 14px; font-weight: 600; color: #DFF0EE; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; gap: 8px; }
        .btn-save:hover { background: #2d5a55; }
        .form-group { margin-bottom: 24px; }
        .form-label { display: block; font-family: 'Urbanist', sans-serif; font-size: 14px; font-weight: 500; color: #374151; margin-bottom: 8px; }
        .form-input { width: 100%; padding: 12px; border: 1px solid #E5E6E6; border-radius: 8px; font-family: 'Urbanist', sans-serif; font-size: 14px; color: #1e293b; background: #fff; transition: all 0.2s; }
        .form-input:focus { outline: none; border-color: #39746E; }
        .form-input::placeholder { color: #6B7271; }
        .form-select { width: 100%; padding: 12px; border: 1px solid #E5E6E6; border-radius: 8px; font-family: 'Urbanist', sans-serif; font-size: 14px; color: #1e293b; background: #fff; transition: all 0.2s; }
        .form-select:focus { outline: none; border-color: #39746E; }
        .form-info { background: #E3F4F1; border: 1px solid #D1F2EB; border-radius: 8px; padding: 12px; margin-bottom: 1.5rem; color: #0FB7A6; font-size: 14px; }
        .form-text { font-family: 'Urbanist', sans-serif; font-size: 12px; color: #6B7271; margin-top: 4px; }
        .row { display: flex; gap: 1rem; }
        .col { flex: 1; }
        @media (max-width: 900px) { .main-container { flex-direction: column; } }
        @media (max-width: 600px) { .form-header { flex-direction: column; align-items: flex-start; gap: 1rem; } }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const poinInput = document.getElementById('poin');
            const xpInput = document.getElementById('xp');

            // Function to parse formatted number (1.234,56 -> 1234.56)
            function parseFormattedNumber(value) {
                if (!value) return 0;
                // Remove thousands separator (.) and replace comma with dot
                return parseFloat(value.replace(/\./g, '').replace(',', '.')) || 0;
            }

            // Function to format number as integer with thousands separator (1234 -> 1.234)
            function formatInteger(value) {
                return Math.floor(value).toLocaleString('id-ID');
            }

            // Auto-calculate XP when Poin changes
            poinInput.addEventListener('input', function() {
                const poinValue = parseFormattedNumber(this.value);
                const xpValue = poinValue / 1000;
                xpInput.value = formatInteger(xpValue);
            });
        });
    </script>
</head>
<body>
    <header class="header">
        <div class="header-left">
            <h1>User</h1>
            <span class="header-separator">/</span>
            <span class="header-subtitle">Edit User</span>
        </div>
        <div class="user-info">
            <div class="notification"></div>
            <span class="user-name">{{ Auth::guard('admin')->user()->role ?? 'Admin' }}</span>
            <div class="user-avatar">{{ strtoupper(substr(Auth::guard('admin')->user()->name ?? 'A', 0, 2)) }}</div>
        </div>
    </header>
    <div class="main-container">
        <div class="form-container">
            <div class="form-header">
                <div class="form-title">Edit User</div>
                <div class="form-actions">
                    <a href="{{ route('dashboard.user') }}" class="btn-cancel">Kembali</a>
                    <button type="submit" form="userEditForm" class="btn-save">
                        <img src="{{ asset('icon/ic_edit.svg') }}" alt="Save" width="16" height="16" style="filter:brightness(0) invert(1);">
                        Simpan
                    </button>
                </div>
            </div>
            <div class="form-info">
                <strong>ID:</strong> {{ $user->id }} &nbsp; | &nbsp; <strong>Registrasi:</strong> {{ $user->created_at->format('d/m/Y H:i') }}
            </div>
            <div class="form-info" style="background:#FFF3CD; border:1px solid #FFEAA7; color:#856404; margin-bottom:24px;">
                <strong>Perhatian:</strong> Perubahan data user harus disetujui oleh user. Tindakan ini dapat menyebabkan kebingungan atau ketidakcocokan dengan data-data yang ada pada sistem.
            </div>
            <div class="form-info" style="background:#E3F4F1; border:1px solid #D1F2EB; color:#0FB7A6; margin-bottom:24px;">
                <strong>Informasi:</strong> Anda dapat mengedit XP dan total setoran user. Kosongkan field jika tidak ingin mengubah nilai tersebut. XP dan total setoran harus angka bulat. Poin dan total sampah wajib diisi.
            </div>
            <form id="userEditForm" method="POST" action="{{ route('dashboard.user.update', $user->id) }}">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col">
                        <div class="form-group">
                            <label class="form-label" for="name">Nama Lengkap *</label>
                            <input type="text" class="form-input" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            <label class="form-label" for="identifier">Identifier *</label>
                            <input type="text" class="form-input" id="identifier" name="identifier" value="{{ old('identifier', $user->identifier) }}" required>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="form-group">
                            <label class="form-label" for="user_type">Jenis Nasabah *</label>
                            <select class="form-select" id="user_type" name="user_type" required>
                                <option value="-1" {{ old('user_type', $user->user_type) == -1 ? 'selected' : '' }}>Instansi</option>
                                <option value="0" {{ old('user_type', $user->user_type) == 0 ? 'selected' : '' }}>Umum</option>
                                @foreach($bankSampahList as $bankSampah)
                                    <option value="{{ $bankSampah->id }}" {{ old('user_type', $user->user_type) == $bankSampah->id ? 'selected' : '' }}>
                                        {{ $bankSampah->nama_bank_sampah }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="form-text">Pilih jenis nasabah: Instansi, Umum, atau Bank Sampah tertentu</small>
                            @error('user_type')
                                <div class="form-text" style="color:#F73541;">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="form-group">
                            <label class="form-label" for="password">Password Baru</label>
                            <input type="password" class="form-input" id="password" name="password" placeholder="Kosongkan jika tidak ingin mengubah password">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="form-group">
                            <label class="form-label" for="poin">Poin *</label>
                            <input type="text" class="form-input" id="poin" name="poin" value="{{ old('poin', number_format($user->poin, 2, ',', '.')) }}" required>
                            <small class="form-text">Masukkan nilai poin baru (gunakan koma untuk desimal, contoh: 1234,56)</small>
                            @error('poin')
                                <div class="form-text" style="color:#F73541;">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            <label class="form-label" for="xp">XP</label>
                            <input type="text" class="form-input" id="xp" name="xp" value="{{ old('xp', number_format($user->xp, 0, ',', '.')) }}" placeholder="Kosongkan jika tidak ingin mengubah XP">
                            <small class="form-text">Masukkan nilai XP baru (harus angka bulat, contoh: 1234)</small>
                            @error('xp')
                                <div class="form-text" style="color:#F73541;">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="form-group">
                            <label class="form-label" for="setor">Total Setoran</label>
                            <input type="text" class="form-input" id="setor" name="setor" value="{{ old('setor', number_format($user->setor, 0, ',', '.')) }}" placeholder="Kosongkan jika tidak ingin mengubah total setoran">
                            <small class="form-text">Masukkan jumlah total setoran baru (harus angka bulat, contoh: 50)</small>
                            @error('setor')
                                <div class="form-text" style="color:#F73541;">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            <label class="form-label" for="sampah">Total Sampah (kg) *</label>
                            <input type="text" class="form-input" id="sampah" name="sampah" value="{{ old('sampah', number_format($user->sampah, 1, ',', '.')) }}" required>
                            <small class="form-text">Masukkan total sampah dalam kg (gunakan koma untuk desimal, contoh: 25,5)</small>
                            @error('sampah')
                                <div class="form-text" style="color:#F73541;">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
@endsection 