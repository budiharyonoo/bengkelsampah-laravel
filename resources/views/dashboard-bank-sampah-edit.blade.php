@extends('dashboard')
@section('content')
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Bank Sampah - Admin Panel</title>
    <link href="https://fonts.googleapis.com/css2?family=Urbanist:wght@400;500;600;700;900&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" rel="stylesheet"/>
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
        }

        .btn-cancel:hover {
            background: #FDCED1;
        }

        .btn-save {
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

        .btn-save:hover {
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
            resize: vertical;
            min-height: 100px;
        }

        .form-textarea:focus {
            outline: none;
            border-color: #39746E;
        }

        .form-textarea::placeholder {
            color: #6B7271;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .readonly-field {
            background-color: #f8f9fa;
            color: #6c757d;
            cursor: not-allowed;
        }

        .error-message {
            color: #dc2626;
            font-size: 12px;
            margin-top: 4px;
            font-family: 'Urbanist', sans-serif;
        }

        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }
        }

        /* Loading Overlay Styles */
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

        /* File input styling */
        .file-input-container {
            position: relative;
            margin-bottom: 8px;
        }

        .file-input {
            position: absolute;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }

        .file-input-label {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 12px;
            border: 2px dashed #E5E6E6;
            border-radius: 8px;
            background: #f9fafb;
            cursor: pointer;
            transition: all 0.2s;
            min-height: 60px;
        }

        .file-input-label:hover {
            border-color: #39746E;
            background: #f0f9ff;
        }

        .file-input-label.has-file {
            border-color: #39746E;
            color: #39746E;
            background: #E3F4F1;
        }

        .file-input-label.dragover {
            background-color: #f0f9ff;
            border-color: #39746E;
            transform: scale(1.02);
        }

        .preview-image {
            max-width: 200px;
            max-height: 200px;
            border-radius: 8px;
            margin-top: 12px;
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

    <!-- Modal Crop -->
    <div id="cropModal" style="display:none;position:fixed;z-index:9999;top:0;left:0;width:100vw;height:100vh;background:rgba(0,0,0,0.7);align-items:center;justify-content:center;">
      <div style="background:#fff;padding:20px;border-radius:8px;max-width:90vw;max-height:90vh;">
        <img id="cropImage" style="max-width:80vw;max-height:70vh;">
        <div style="margin-top:10px;text-align:right;display:flex;gap:8px;justify-content:flex-end;">
          <button type="button" id="cropCancelBtn" style="padding:8px 16px;background:transparent;border:1px solid #FDCED1;border-radius:8px;font-family:'Urbanist',sans-serif;font-size:14px;font-weight:600;color:#F73541;cursor:pointer;transition:all 0.2s;">Batal</button>
          <button type="button" id="cropOkBtn" style="padding:8px 16px;background:#39746E;border:none;border-radius:8px;font-family:'Urbanist',sans-serif;font-size:14px;font-weight:600;color:#DFF0EE;cursor:pointer;transition:all 0.2s;">OK</button>
        </div>
      </div>
    </div>

    <header class="header">
        <div class="header-left">
            <h1>Bank Sampah</h1>
            <span class="header-separator">/</span>
            <span class="header-subtitle">Edit Bank Sampah</span>
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
                <h2 class="form-title">Form Edit Bank Sampah</h2>
                <div class="form-actions">
                    <button class="btn-cancel" onclick="goBack()">Batal</button>
                    <button class="btn-save" onclick="submitForm()">Update</button>
                </div>
            </div>

            <form id="bankForm">
                <div class="form-group">
                    <label class="form-label">Kode Bank Sampah</label>
                    <input type="text" class="form-input readonly-field" value="{{ $bankSampah->kode_bank_sampah }}" readonly>
                </div>

                <div class="form-group">
                    <label class="form-label">Nama Bank Sampah *</label>
                    <input type="text" class="form-input" name="nama_bank_sampah" value="{{ $bankSampah->nama_bank_sampah }}" placeholder="Masukkan nama bank sampah" required>
                    <div class="error-message" id="nama_error"></div>
                </div>

                <div class="form-group">
                    <label class="form-label">Alamat Bank Sampah *</label>
                    <textarea class="form-textarea" name="alamat_bank_sampah" placeholder="Masukkan alamat lengkap bank sampah" required>{{ $bankSampah->alamat_bank_sampah }}</textarea>
                    <div class="error-message" id="alamat_error"></div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Nama Penanggung Jawab *</label>
                        <input type="text" class="form-input" name="nama_penanggung_jawab" value="{{ $bankSampah->nama_penanggung_jawab }}" placeholder="Masukkan nama penanggung jawab" required>
                        <div class="error-message" id="penanggung_jawab_error"></div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Kontak Penanggung Jawab *</label>
                        <input type="text" class="form-input" name="kontak_penanggung_jawab" value="{{ $bankSampah->kontak_penanggung_jawab }}" placeholder="Masukkan nomor telepon/email" required>
                        <div class="error-message" id="kontak_error"></div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Foto Bank Sampah</label>
                    @if($bankSampah->foto)
                        <div style="margin-bottom: 12px;">
                            <label class="form-label">Foto Saat Ini:</label>
                            <img src="{{ $bankSampah->foto }}" alt="Current photo" class="current-image" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <div style="width:200px;height:150px;background:#f3f4f6;border-radius:8px;display:none;align-items:center;justify-content:center;color:#9ca3af;font-size:14px;text-align:center;">No Image</div>
                        </div>
                    @else
                        <div style="margin-bottom: 12px;">
                            <label class="form-label">Foto Saat Ini:</label>
                            <div style="width:200px;height:150px;background:#f3f4f6;border-radius:8px;display:flex;align-items:center;justify-content:center;color:#9ca3af;font-size:14px;text-align:center;">No Image</div>
                        </div>
                    @endif
                    <div class="file-input-container">
                        <input type="file" class="file-input" id="foto" name="foto" accept="image/*">
                        <label for="foto" class="file-input-label" id="fileLabel">
                            <span id="fileText">Klik untuk memilih gambar baru atau drag & drop</span>
                        </label>
                    </div>
                    <div class="error-message" id="foto_error"></div>
                    <small style="color: #6B7271; font-size: 12px;">Format: JPEG, PNG, JPG, atau WebP. Maksimal: 2MB. Kosongkan jika tidak ingin mengubah foto.</small>
                    <img id="preview" class="preview-image" style="display: none;">
                </div>

                <div class="form-group">
                    <label class="form-label">Link Google Maps</label>
                    <input type="url" class="form-input" name="gmaps_link" placeholder="https://maps.google.com/..." value="{{ $bankSampah->gmaps_link ?? '' }}">
                    <div class="error-message" id="gmaps_link_error"></div>
                    <small style="color: #6B7271; font-size: 12px;">Masukkan link Google Maps lokasi bank sampah (opsional)</small>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Latitude</label>
                        <input type="text" class="form-input" name="latitude" placeholder="Contoh: -6.2088" value="{{ $bankSampah->latitude ?? '' }}">
                        <div class="error-message" id="latitude_error"></div>
                        <small style="color: #6B7271; font-size: 12px;">Koordinat latitude (-90 hingga 90)</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Longitude</label>
                        <input type="text" class="form-input" name="longitude" placeholder="Contoh: 106.8456" value="{{ $bankSampah->longitude ?? '' }}">
                        <div class="error-message" id="longitude_error"></div>
                        <small style="color: #6B7271; font-size: 12px;">Koordinat longitude (-180 hingga 180)</small>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Tipe Layanan *</label>
                    <select class="form-input" name="tipe_layanan" required>
                        <option value="">Pilih tipe layanan</option>
                        <option value="jemput" {{ $bankSampah->tipe_layanan == 'jemput' ? 'selected' : '' }}>Jemput Saja</option>
                        <option value="tempat" {{ $bankSampah->tipe_layanan == 'tempat' ? 'selected' : '' }}>Tempat Saja</option>
                        <option value="keduanya" {{ $bankSampah->tipe_layanan == 'keduanya' ? 'selected' : '' }}>Keduanya</option>
                    </select>
                    <div class="error-message" id="tipe_layanan_error"></div>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
    <script>
        let cropper;
        const input = document.getElementById('foto');
        const modal = document.getElementById('cropModal');
        const cropImg = document.getElementById('cropImage');
        const preview = document.getElementById('preview');

        // Loading helper functions
        function showLoading(message = 'Memuat...') {
            document.getElementById('loadingMessage').textContent = message;
            document.getElementById('pageLoadingOverlay').style.display = 'flex';
        }

        function hideLoading() {
            document.getElementById('pageLoadingOverlay').style.display = 'none';
        }

        function submitForm() {
            const form = document.getElementById('bankForm');
            const formData = new FormData(form);

            // Clear previous error messages
            clearErrors();

            // Show loading
            showLoading('Menyimpan perubahan...');

            fetch('{{ route("dashboard.bank.update", $bankSampah->id) }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-HTTP-Method-Override': 'PUT'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showLoading('Mengalihkan ke halaman bank sampah...');
                    window.location.href = '{{ route("dashboard.bank") }}';
                } else {
                    // Handle validation errors
                    if (data.errors) {
                        Object.keys(data.errors).forEach(field => {
                            const errorElement = document.getElementById(field + '_error');
                            if (errorElement) {
                                errorElement.textContent = data.errors[field][0];
                            }
                        });
                    } else {
                        alert('Gagal mengupdate Bank Sampah: ' + data.message);
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat mengupdate Bank Sampah');
            })
            .finally(() => {
                hideLoading();
            });
        }

        function clearErrors() {
            const errorElements = document.querySelectorAll('.error-message');
            errorElements.forEach(element => {
                element.textContent = '';
            });
        }

        function goBack() {
            showLoading('Kembali ke halaman bank sampah...');
            window.location.href = '{{ route("dashboard.bank") }}';
        }

        // File input handling
        document.getElementById('foto').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const url = URL.createObjectURL(file);
                cropImg.src = url;
                modal.style.display = 'flex';
                if (cropper) cropper.destroy();
                setTimeout(() => {
                    cropper = new Cropper(cropImg, { viewMode: 1 });
                }, 100);
            }
        });

        // Crop modal event listeners
        document.getElementById('cropCancelBtn').onclick = function() {
            modal.style.display = 'none';
            if (cropper) cropper.destroy();
            input.value = '';
            preview.src = '';
            preview.style.display = 'none';
            document.getElementById('fileText').textContent = 'Klik untuk memilih gambar baru atau drag & drop';
            document.getElementById('fileLabel').classList.remove('has-file');
        };

        document.getElementById('cropOkBtn').onclick = function() {
            if (cropper) {
                cropper.getCroppedCanvas().toBlob(function(blob) {
                    const file = new File([blob], 'bank-sampah-cropped.jpg', { type: 'image/jpeg' });
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(file);
                    input.files = dataTransfer.files;
                    const url = URL.createObjectURL(blob);
                    preview.src = url;
                    preview.style.display = 'block';
                    document.getElementById('fileText').textContent = file.name;
                    document.getElementById('fileLabel').classList.add('has-file');
                    modal.style.display = 'none';
                    cropper.destroy();
                }, 'image/jpeg');
            }
        };

        // Drag and drop functionality
        const fileLabel = document.getElementById('fileLabel');
        
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            fileLabel.addEventListener(eventName, preventDefaults, false);
        });
        
        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }
        
        ['dragenter', 'dragover'].forEach(eventName => {
            fileLabel.addEventListener(eventName, highlight, false);
        });
        
        ['dragleave', 'drop'].forEach(eventName => {
            fileLabel.addEventListener(eventName, unhighlight, false);
        });
        
        function highlight(e) {
            fileLabel.classList.add('dragover');
        }
        
        function unhighlight(e) {
            fileLabel.classList.remove('dragover');
        }
        
        fileLabel.addEventListener('drop', handleDrop, false);
        
        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            
            if (files.length > 0) {
                document.getElementById('foto').files = files;
                // Trigger the change event
                const event = new Event('change', { bubbles: true });
                document.getElementById('foto').dispatchEvent(event);
            }
        }
    </script>
</body>
</html>
@endsection 