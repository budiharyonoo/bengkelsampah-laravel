@extends('dashboard')
@section('content')
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Event - Admin Panel</title>
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

        .btn-upload {
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

        .btn-upload:hover {
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

        .form-select {
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

        .form-select:focus {
            outline: none;
            border-color: #39746E;
        }

        .upload-area {
            border: 2px dashed #E5E6E6;
            border-radius: 8px;
            padding: 60px 20px;
            text-align: center;
            background: #fafafa;
            cursor: pointer;
            transition: all 0.2s;
        }

        .upload-area:hover {
            border-color: #39746E;
            background: #f0f9ff;
        }

        .upload-area.dragover {
            border-color: #39746E;
            background: #f0f9ff;
        }

        .upload-icon {
            width: 48px;
            height: 48px;
            margin: 0 auto 16px;
            opacity: 0.5;
        }

        .upload-text {
            font-family: 'Urbanist', sans-serif;
            font-size: 14px;
            font-weight: 500;
            color: #6B7271;
            margin-bottom: 4px;
        }

        .upload-subtext {
            font-family: 'Urbanist', sans-serif;
            font-size: 12px;
            color: #9CA3AF;
        }

        .file-input {
            display: none;
        }

        .image-preview {
            display: none;
            position: relative;
            max-width: 300px;
            margin: 16px auto 0;
        }

        .image-preview img {
            width: 100%;
            height: auto;
            max-height: 300px;
            object-fit: contain;
            border-radius: 8px;
        }

        .image-remove {
            position: absolute;
            top: 8px;
            right: 8px;
            width: 24px;
            height: 24px;
            background: rgba(0, 0, 0, 0.5);
            border: none;
            border-radius: 50%;
            color: white;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
        }

        .content-editor {
            min-height: 200px;
            border: 1px solid #E5E6E6;
            border-radius: 8px;
            padding: 12px;
            font-family: 'Urbanist', sans-serif;
            font-size: 14px;
            resize: vertical;
        }

        .content-editor:focus {
            outline: none;
            border-color: #39746E;
        }

        .char-count {
            font-family: 'Urbanist', sans-serif;
            font-size: 12px;
            color: #6B7271;
            margin-top: 4px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .status-sidebar {
            background: #fff;
            border: 1px solid #E5E6E6;
            border-radius: 16px;
            padding: 24px;
            width: 300px;
            height: fit-content;
        }

        .sidebar-title {
            font-family: 'Urbanist', sans-serif;
            font-size: 18px;
            font-weight: 700;
            color: #242E2C;
            margin-bottom: 16px;
        }

        .status-info {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 16px;
        }

        .status-info h4 {
            font-family: 'Urbanist', sans-serif;
            font-size: 14px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 8px;
        }

        .status-info p {
            font-family: 'Urbanist', sans-serif;
            font-size: 12px;
            color: #6B7271;
            line-height: 1.4;
        }

        .error-message {
            color: #F73541;
            font-size: 12px;
            margin-top: 4px;
        }

        .current-image {
            margin-bottom: 16px;
        }

        .current-image img {
            width: 100%;
            max-width: 300px;
            height: 200px;
            object-fit: cover;
            border-radius: 8px;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-left">
            <h1>Event</h1>
            <span class="header-separator">/</span>
            <span class="header-subtitle">Edit Event</span>
        </div>
        <div class="user-info">
            <div class="notification"></div>
            <span class="user-name">{{ Auth::guard('admin')->user()->role ?? 'Admin' }}</span>
            <div class="user-avatar">{{ strtoupper(substr(Auth::guard('admin')->user()->name ?? 'AD', 0, 2)) }}</div>
        </div>
    </header>

    <div class="main-container">
        <div class="form-container">
            <div class="form-header">
                <h2 class="form-title">Edit Event</h2>
                <div class="form-actions">
                    <button type="button" class="btn-cancel" onclick="window.history.back()">Kembali</button>
                    <button type="submit" class="btn-upload" onclick="submitForm()">Update Event</button>
                </div>
            </div>

            <form id="eventForm" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label class="form-label">Judul Event</label>
                    <input type="text" class="form-input" name="title" value="{{ $event->title }}" placeholder="Masukkan judul event" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Deskripsi Event</label>
                    <textarea class="form-input content-editor" name="description" placeholder="Masukkan deskripsi event" rows="6" oninput="updateCharCount(this)" required>{{ $event->description }}</textarea>
                    <div class="char-count" id="charCount">{{ strlen($event->description) }} karakter</div>
                </div>

                <div class="form-group">
                    <label class="form-label">Cover Event</label>
                    @if($event->cover)
                    <div class="current-image">
                        <img src="{{ $event->cover }}" alt="Current cover" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <div style="width:100%;height:200px;background:#f3f4f6;border-radius:8px;display:none;align-items:center;justify-content:center;color:#9ca3af;font-size:14px;text-align:center;">No Image</div>
                    </div>
                    @else
                    <div style="width:100%;height:200px;background:#f3f4f6;border-radius:8px;display:flex;align-items:center;justify-content:center;color:#9ca3af;font-size:14px;text-align:center;">No Image</div>
                    @endif
                    <div class="upload-area" onclick="triggerFileInput()">
                        <div class="upload-icon">
                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <polyline points="7,10 12,15 17,10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <line x1="12" y1="15" x2="12" y2="3" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                        </div>
                        <div class="upload-text">Pilih Gambar baru untuk event</div>
                        <div class="upload-subtext">Format: JPG, PNG, WebP (Max: 2MB)</div>
                    </div>
                    <input type="file" id="coverInput" name="cover" class="file-input" accept="image/*">
                    <div class="image-preview" id="imagePreview">
                        <img id="previewImg" src="" alt="Preview">
                        <button type="button" class="image-remove" onclick="removeImage()">&times;</button>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Waktu Mulai</label>
                        <input type="datetime-local" class="form-input" name="start_datetime" value="{{ $event->start_datetime->format('Y-m-d\TH:i') }}" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Waktu Selesai</label>
                        <input type="datetime-local" class="form-input" name="end_datetime" value="{{ $event->end_datetime->format('Y-m-d\TH:i') }}" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Lokasi Event</label>
                    <input type="text" class="form-input" name="location" value="{{ $event->location }}" placeholder="Masukkan lokasi event" required>
                </div>

                <div class="form-group">
                    <label class="form-label">URL Event (Opsional)</label>
                    <input type="url" class="form-input" name="url" value="{{ $event->url }}" placeholder="Masukkan URL event (contoh: https://example.com/event)">
                </div>

                <div class="form-group">
                    <label class="form-label">Maksimal Peserta (Opsional)</label>
                    <input type="number" class="form-input" name="max_participants" value="{{ $event->max_participants }}" placeholder="Masukkan jumlah maksimal peserta" min="1">
                </div>

                <div class="form-group">
                    <label class="form-label">Status Event</label>
                    <select class="form-select" name="status" required>
                        <option value="active" {{ $event->status == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="completed" {{ $event->status == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ $event->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
            </form>
        </div>

        <div class="status-sidebar">
            <div class="sidebar-title">
                <h3>Informasi Event</h3>
            </div>
            <div class="status-info">
                <h4>Status Event</h4>
                <p>Ubah status event sesuai kondisi saat ini. Event yang sudah selesai sebaiknya diubah ke "Completed".</p>
            </div>
            <div class="status-info">
                <h4>Waktu Event</h4>
                <p>Pastikan waktu mulai dan selesai sudah benar. Event yang sudah lewat waktu selesai akan otomatis expired.</p>
            </div>
            <div class="status-info">
                <h4>Peserta</h4>
                <p>Jika tidak diisi, jumlah peserta tidak dibatasi. User dapat join/unjoin event sesuai waktu yang ditentukan.</p>
            </div>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div id="pageLoadingOverlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); z-index: 9999; justify-content: center; align-items: center;">
        <div style="background: white; padding: 20px; border-radius: 8px; text-align: center;">
            <div style="width: 40px; height: 40px; border: 4px solid #f3f3f3; border-top: 4px solid #39746E; border-radius: 50%; animation: spin 1s linear infinite; margin: 0 auto 10px;"></div>
            <p style="margin: 0; color: #333; font-family: 'Urbanist', sans-serif;">Memproses...</p>
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

    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
    <script>
        let cropper;
        const input = document.getElementById('coverInput');
        const modal = document.getElementById('cropModal');
        const cropImg = document.getElementById('cropImage');
        const previewDiv = document.getElementById('imagePreview');
        const previewImg = document.getElementById('previewImg');

        // Loading utility functions
        function showLoading(message = 'Memproses...') {
            const overlay = document.getElementById('pageLoadingOverlay');
            const loadingText = overlay.querySelector('p');
            loadingText.textContent = message;
            overlay.style.display = 'flex';
        }

        function hideLoading() {
            document.getElementById('pageLoadingOverlay').style.display = 'none';
        }

        // Alert utility function
        function showAlert(type, message) {
            // Create alert element
            const alertDiv = document.createElement('div');
            alertDiv.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                padding: 15px 20px;
                border-radius: 8px;
                color: white;
                font-family: 'Urbanist', sans-serif;
                font-weight: 500;
                z-index: 10000;
                max-width: 400px;
                word-wrap: break-word;
                animation: slideIn 0.3s ease-out;
            `;
            
            if (type === 'success') {
                alertDiv.style.backgroundColor = '#10B981';
            } else if (type === 'error') {
                alertDiv.style.backgroundColor = '#EF4444';
            } else {
                alertDiv.style.backgroundColor = '#3B82F6';
            }
            
            alertDiv.textContent = message;
            
            // Add to page
            document.body.appendChild(alertDiv);
            
            // Remove after 3 seconds
            setTimeout(() => {
                alertDiv.style.animation = 'slideOut 0.3s ease-in';
                setTimeout(() => {
                    if (alertDiv.parentNode) {
                        alertDiv.parentNode.removeChild(alertDiv);
                    }
                }, 300);
            }, 3000);
        }

        // Add CSS animations
        const style = document.createElement('style');
        style.textContent = `
            @keyframes spin {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }
            @keyframes slideIn {
                from { transform: translateX(100%); opacity: 0; }
                to { transform: translateX(0); opacity: 1; }
            }
            @keyframes slideOut {
                from { transform: translateX(0); opacity: 1; }
                to { transform: translateX(100%); opacity: 0; }
            }
        `;
        document.head.appendChild(style);

        function triggerFileInput() {
            document.getElementById('coverInput').click();
        }



        function removeImage() {
            document.getElementById('coverInput').value = '';
            document.getElementById('imagePreview').style.display = 'none';
            document.querySelector('.upload-area').style.display = 'block';
        }

        function updateCharCount(textarea) {
            const count = textarea.value.length;
            document.getElementById('charCount').textContent = count + ' karakter';
        }

        function submitForm() {
            const form = document.getElementById('eventForm');
            const formData = new FormData(form);
            const submitBtn = document.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;

            // Show loading
            submitBtn.textContent = 'Mengupdate...';
            submitBtn.disabled = true;
            showLoading('Mengupdate event...');

            fetch('{{ route("dashboard.event.update", $event->id) }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('success', 'Event berhasil diupdate!');
                    setTimeout(() => {
                        window.location.href = '{{ route("dashboard.event") }}';
                    }, 1500);
                } else {
                    showAlert('error', 'Gagal mengupdate event: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('error', 'Terjadi kesalahan saat mengupdate event');
            })
            .finally(() => {
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
                hideLoading();
            });
        }

        // Ensure end datetime is after start datetime
        document.querySelector('input[name="start_datetime"]').addEventListener('change', function() {
            document.querySelector('input[name="end_datetime"]').min = this.value;
        });

        // Crop modal event listeners
        document.getElementById('cropCancelBtn').onclick = function() {
            modal.style.display = 'none';
            if (cropper) cropper.destroy();
            input.value = '';
            previewImg.src = '';
            previewDiv.style.display = 'none';
            document.querySelector('.upload-area').style.display = 'block';
        };

        document.getElementById('cropOkBtn').onclick = function() {
            if (cropper) {
                cropper.getCroppedCanvas().toBlob(function(blob) {
                    const file = new File([blob], 'cover-cropped.jpg', { type: 'image/jpeg' });
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(file);
                    input.files = dataTransfer.files;
                    const url = URL.createObjectURL(blob);
                    previewImg.src = url;
                    previewDiv.style.display = 'block';
                    document.querySelector('.upload-area').style.display = 'none';
                    modal.style.display = 'none';
                    cropper.destroy();
                }, 'image/jpeg');
            }
        };

        // Drag and drop functionality
        const uploadArea = document.querySelector('.upload-area');
        
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            uploadArea.addEventListener(eventName, preventDefaults, false);
        });
        
        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }
        
        ['dragenter', 'dragover'].forEach(eventName => {
            uploadArea.addEventListener(eventName, highlight, false);
        });
        
        ['dragleave', 'drop'].forEach(eventName => {
            uploadArea.addEventListener(eventName, unhighlight, false);
        });
        
        function highlight(e) {
            uploadArea.classList.add('dragover');
        }
        
        function unhighlight(e) {
            uploadArea.classList.remove('dragover');
        }
        
        uploadArea.addEventListener('drop', handleDrop, false);
        
        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            
            if (files.length > 0) {
                document.getElementById('coverInput').files = files;
                // previewImage(document.getElementById('coverInput')); // Hapus/disable fungsi previewImage dari onchange input file
            }
        }

        input.addEventListener('change', function(e) {
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
    </script>
</body>
</html>
@endsection 