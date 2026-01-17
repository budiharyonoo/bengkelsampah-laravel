@extends('dashboard')
@section('content')
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Offtaker - Admin Panel</title>
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
            font-size: 22px;
            font-weight: 400;
            color: #39746E;
        }

        .header-separator {
            font-size: 22px;
            font-weight: 400;
            color: #39746E;
        }

        .header-subtitle {
            font-size: 22px;
            font-weight: 700;
            color: #39746E;
        }

        .main-container {
            display: flex;
            gap: 1rem;
            margin: 0 2rem 2rem 2rem;
        }

        .form-container {
            flex: 1;
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
            font-size: 14px;
            font-weight: 600;
            color: #F73541;
            cursor: pointer;
            text-decoration: none;
        }

        .btn-cancel:hover {
            background: #FDCED1;
        }

        .btn-save {
            padding: 8px 16px;
            background: #39746E;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            color: #DFF0EE;
            cursor: pointer;
        }

        .btn-save:hover {
            background: #2d5a55;
        }

        .form-group {
            margin-bottom: 24px;
        }

        .form-label {
            display: block;
            font-size: 14px;
            font-weight: 500;
            color: #374151;
            margin-bottom: 8px;
        }

        .form-label span {
            color: #EF4444;
        }

        .form-input {
            width: 100%;
            padding: 12px;
            border: 1px solid #E5E6E6;
            border-radius: 8px;
            font-size: 14px;
            color: #1e293b;
            background: #fff;
        }

        .form-input:focus {
            outline: none;
            border-color: #39746E;
        }

        .form-input::placeholder {
            color: #6B7271;
        }

        .form-textarea {
            min-height: 100px;
            resize: vertical;
        }

        .form-select {
            width: 100%;
            padding: 12px;
            border: 1px solid #E5E6E6;
            border-radius: 8px;
            font-size: 14px;
            color: #1e293b;
            background: #fff;
        }

        .form-select:focus {
            outline: none;
            border-color: #39746E;
        }

        .form-error {
            color: #EF4444;
            font-size: 12px;
            margin-top: 4px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-left">
            <h1>Offtaker</h1>
            <span class="header-separator">/</span>
            <span class="header-subtitle">Tambah Offtaker</span>
        </div>
    </div>

    <div class="main-container">
        <div class="form-container">
            <form action="{{ route('offtakers.store') }}" method="POST">
                @csrf
                <div class="form-header">
                    <h2 class="form-title">Informasi Offtaker</h2>
                    <div class="form-actions">
                        <a href="{{ route('offtakers.index') }}" class="btn-cancel">Batal</a>
                        <button type="submit" class="btn-save">Simpan</button>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Nama Offtaker <span>*</span></label>
                    <input type="text" name="nama" class="form-input" placeholder="Masukkan nama offtaker" value="{{ old('nama') }}" required>
                    @error('nama')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Tipe <span>*</span></label>
                    <select name="tipe" class="form-select" required>
                        <option value="">Pilih tipe offtaker</option>
                        <option value="buyer" {{ old('tipe') === 'buyer' ? 'selected' : '' }}>Pembeli</option>
                        <option value="processor" {{ old('tipe') === 'processor' ? 'selected' : '' }}>Pengolah</option>
                        <option value="both" {{ old('tipe') === 'both' ? 'selected' : '' }}>Keduanya</option>
                    </select>
                    @error('tipe')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Nama PIC <span>*</span></label>
                        <input type="text" name="nama_pic" class="form-input" placeholder="Masukkan nama PIC" value="{{ old('nama_pic') }}" required>
                        @error('nama_pic')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Kontak PIC <span>*</span></label>
                        <input type="text" name="kontak_pic" class="form-input" placeholder="Masukkan nomor telepon" value="{{ old('kontak_pic') }}" required>
                        @error('kontak_pic')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Alamat</label>
                    <textarea name="alamat" class="form-input form-textarea" placeholder="Masukkan alamat lengkap">{{ old('alamat') }}</textarea>
                    @error('alamat')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>
            </form>
        </div>
    </div>
</body>
</html>
@endsection
