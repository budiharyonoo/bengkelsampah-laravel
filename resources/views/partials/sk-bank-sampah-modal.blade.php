{{-- SK Bank Sampah Modal --}}
<div class="modal" id="skBankSampahModal">
    <div class="sk-modal-wrapper">
        <div class="sk-modal-header">
            <h2 class="sk-modal-title">Generate SK Bank Sampah</h2>
            <button type="button" class="sk-modal-close" onclick="closeSkModal()">
                <img src="{{ asset('icon/ic_close.svg') }}" alt="Close" width="20" height="20">
            </button>
        </div>

        <div class="sk-modal-body">
            {{-- Left Panel: Form --}}
            <div class="sk-form-panel">
                <form id="skBankSampahForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="bank_sampah_id" value="{{ $bankSampah->id }}">

                    {{-- Section 1: Data Bank Sampah --}}
                    <div class="sk-form-section">
                        <h3 class="sk-form-section-title">Data Bank Sampah</h3>

                        <div class="sk-form-group">
                            <label class="sk-form-label">Nama Bank Sampah <span class="required">*</span></label>
                            <input type="text" name="nama_bank_sampah" class="sk-form-input" value="{{ $bankSampah->nama_bank_sampah }}" required>
                        </div>

                        <div class="sk-form-group">
                            <label class="sk-form-label">Alamat Bank Sampah <span class="required">*</span></label>
                            <textarea name="alamat_bank_sampah" class="sk-form-input" rows="2" required>{{ $bankSampah->alamat_bank_sampah }}</textarea>
                        </div>
                    </div>

                    {{-- Section 2: Data Surat Keputusan --}}
                    <div class="sk-form-section">
                        <h3 class="sk-form-section-title">Data Surat Keputusan</h3>

                        <div class="sk-form-group">
                            <label class="sk-form-label">Nomor Surat <span class="required">*</span></label>
                            <input type="text" name="nomor_surat" class="sk-form-input" placeholder="Contoh: 001/SK-BS/I/2026" required>
                        </div>

                        <div class="sk-form-row">
                            <div class="sk-form-group">
                                <label class="sk-form-label">Tanggal Ditetapkan <span class="required">*</span></label>
                                <input type="date" name="tanggal_ditetapkan" class="sk-form-input" required>
                            </div>
                            <div class="sk-form-group">
                                <label class="sk-form-label">Tanggal Rapat Musyawarah <span class="required">*</span></label>
                                <input type="date" name="tanggal_rapat_musyawarah" class="sk-form-input" required>
                            </div>
                        </div>
                    </div>

                    {{-- Section 3: Data Wilayah --}}
                    <div class="sk-form-section">
                        <h3 class="sk-form-section-title">Data Wilayah</h3>

                        <div class="sk-form-group">
                            <label class="sk-form-label">Nama Desa/Kelurahan <span class="required">*</span></label>
                            <input type="text" name="nama_desa_kelurahan" class="sk-form-input" placeholder="Masukkan nama desa/kelurahan" required>
                        </div>

                        <div class="sk-form-row">
                            <div class="sk-form-group">
                                <label class="sk-form-label">Kecamatan <span class="required">*</span></label>
                                <input type="text" name="kecamatan" class="sk-form-input" placeholder="Masukkan kecamatan" required>
                            </div>
                            <div class="sk-form-group">
                                <label class="sk-form-label">Kabupaten/Kota <span class="required">*</span></label>
                                <input type="text" name="kabupaten" class="sk-form-input" placeholder="Masukkan kabupaten/kota" required>
                            </div>
                        </div>

                        <div class="sk-form-row">
                            <div class="sk-form-group">
                                <label class="sk-form-label">Provinsi <span class="required">*</span></label>
                                <input type="text" name="provinsi" class="sk-form-input" placeholder="Masukkan provinsi" required>
                            </div>
                            <div class="sk-form-group">
                                <label class="sk-form-label">Kode Pos</label>
                                <input type="text" name="kode_pos" class="sk-form-input" placeholder="Masukkan kode pos" maxlength="10">
                            </div>
                        </div>
                    </div>

                    {{-- Section 4: Masa Bakti & Pengesahan --}}
                    <div class="sk-form-section">
                        <h3 class="sk-form-section-title">Masa Bakti & Pengesahan</h3>

                        <div class="sk-form-row">
                            <div class="sk-form-group">
                                <label class="sk-form-label">Tahun Mulai <span class="required">*</span></label>
                                <input type="number" name="masa_bakti_mulai" class="sk-form-input" min="2000" max="2100" value="{{ date('Y') }}" required>
                            </div>
                            <div class="sk-form-group">
                                <label class="sk-form-label">Tahun Selesai <span class="required">*</span></label>
                                <input type="number" name="masa_bakti_selesai" class="sk-form-input" min="2000" max="2100" value="{{ date('Y') + 4 }}" required>
                            </div>
                        </div>

                        <div class="sk-form-group">
                            <label class="sk-form-label">Nama Kepala Desa/Lurah <span class="required">*</span></label>
                            <input type="text" name="nama_kepala_desa" class="sk-form-input" placeholder="Masukkan nama kepala desa/lurah" required>
                        </div>
                    </div>

                    {{-- Section 5: Upload Gambar Pengurus --}}
                    <div class="sk-form-section">
                        <h3 class="sk-form-section-title">Lampiran Susunan Pengurus</h3>

                        <div class="sk-form-group">
                            <label class="sk-form-label">Upload Gambar Susunan Pengurus</label>
                            <p class="sk-form-hint" style="margin-bottom: 8px;">Upload gambar yang berisi daftar nama dan jabatan pengurus bank sampah.</p>
                            <div class="sk-file-upload">
                                <input type="file" name="pengurus_image" id="pengurusImageInput" accept="image/jpeg,image/jpg,image/png" style="display: none;">
                                <button type="button" class="sk-btn-upload" onclick="document.getElementById('pengurusImageInput').click()">
                                    Pilih Gambar
                                </button>
                                <span id="pengurusFileName" class="sk-file-name">Belum ada file dipilih</span>
                            </div>
                            <div id="pengurusPreview" class="sk-image-preview" style="display: none;">
                                <img id="pengurusPreviewImg" src="" alt="Preview Pengurus">
                                <button type="button" class="sk-btn-remove-image" onclick="removePengurusImage()">Hapus</button>
                            </div>
                            <p class="sk-form-hint">Format: JPG, JPEG, PNG. Maksimal 5MB.</p>
                        </div>
                    </div>

                    {{-- Section 6: Upload Struktur Organisasi --}}
                    <div class="sk-form-section">
                        <h3 class="sk-form-section-title">Struktur Organisasi (Opsional)</h3>

                        <div class="sk-form-group">
                            <label class="sk-form-label">Upload Gambar Bagan Organisasi</label>
                            <div class="sk-file-upload">
                                <input type="file" name="struktur_organisasi" id="strukturOrganisasiInput" accept="image/jpeg,image/jpg,image/png" style="display: none;">
                                <button type="button" class="sk-btn-upload" onclick="document.getElementById('strukturOrganisasiInput').click()">
                                    Pilih Gambar
                                </button>
                                <span id="strukturFileName" class="sk-file-name">Belum ada file dipilih</span>
                            </div>
                            <div id="strukturPreview" class="sk-image-preview" style="display: none;">
                                <img id="strukturPreviewImg" src="" alt="Preview Struktur">
                                <button type="button" class="sk-btn-remove-image" onclick="removeStrukturImage()">Hapus</button>
                            </div>
                            <p class="sk-form-hint">Format: JPG, JPEG, PNG. Maksimal 5MB.</p>
                        </div>
                    </div>

                    {{-- Form Actions --}}
                    <div class="sk-form-actions">
                        <button type="button" class="sk-btn-secondary" onclick="closeSkModal()">Batal</button>
                        <button type="submit" class="sk-btn-primary">
                            <span class="sk-btn-text">Generate PDF</span>
                            <span class="sk-btn-loading" style="display: none;">Memproses...</span>
                        </button>
                    </div>
                </form>
            </div>

            {{-- Right Panel: Preview --}}
            <div class="sk-preview-panel">
                <div class="sk-preview-header">
                    <h3>Preview Dokumen</h3>
                    <button type="button" class="sk-btn-refresh" onclick="updatePreview()">
                        Refresh Preview
                    </button>
                </div>
                <div class="sk-preview-content">
                    <iframe id="skPreviewIframe" src="about:blank"></iframe>
                    <div id="skPreviewLoading" class="sk-preview-loading" style="display: none;">
                        <div class="sk-spinner"></div>
                        <p>Memuat preview...</p>
                    </div>
                    <div id="skPreviewPlaceholder" class="sk-preview-placeholder">
                        <p>Isi form di sebelah kiri untuk melihat preview dokumen SK</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* SK Modal Styles */
    #skBankSampahModal {
        display: none;
        position: fixed;
        z-index: 2000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.6);
        animation: fadeIn 0.3s ease;
    }

    #skBankSampahModal.show {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .sk-modal-wrapper {
        background: #fff;
        width: 95vw;
        height: 90vh;
        max-width: 1600px;
        border-radius: 16px;
        display: flex;
        flex-direction: column;
        overflow: hidden;
        animation: slideIn 0.3s ease;
    }

    .sk-modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 16px 24px;
        border-bottom: 1px solid #E5E6E6;
        background: #F8F9FA;
    }

    .sk-modal-title {
        font-family: 'Urbanist', sans-serif;
        font-size: 18px;
        font-weight: 700;
        color: #39746E;
        margin: 0;
    }

    .sk-modal-close {
        background: none;
        border: none;
        cursor: pointer;
        padding: 8px;
        border-radius: 8px;
        transition: background 0.2s;
    }

    .sk-modal-close:hover {
        background: #E5E6E6;
    }

    .sk-modal-body {
        display: flex;
        flex: 1;
        overflow: hidden;
    }

    /* Form Panel */
    .sk-form-panel {
        width: 40%;
        padding: 24px;
        overflow-y: auto;
        border-right: 1px solid #E5E6E6;
    }

    .sk-form-section {
        margin-bottom: 24px;
        padding-bottom: 20px;
        border-bottom: 1px solid #F0F0F0;
    }

    .sk-form-section:last-of-type {
        border-bottom: none;
    }

    .sk-form-section-title {
        font-family: 'Urbanist', sans-serif;
        font-size: 14px;
        font-weight: 700;
        color: #39746E;
        margin-bottom: 16px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .sk-form-group {
        margin-bottom: 12px;
    }

    .sk-form-row {
        display: flex;
        gap: 12px;
    }

    .sk-form-row .sk-form-group {
        flex: 1;
    }

    .sk-form-label {
        display: block;
        font-family: 'Urbanist', sans-serif;
        font-size: 13px;
        font-weight: 500;
        color: #6B7271;
        margin-bottom: 6px;
    }

    .sk-form-label .required {
        color: #DC2626;
    }

    .sk-form-input {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #E5E6E6;
        border-radius: 8px;
        font-family: 'Urbanist', sans-serif;
        font-size: 14px;
        color: #1e293b;
        transition: border-color 0.2s;
    }

    .sk-form-input:focus {
        outline: none;
        border-color: #39746E;
    }

    textarea.sk-form-input {
        resize: vertical;
        min-height: 60px;
    }

    .sk-form-hint {
        font-size: 12px;
        color: #9CA3AF;
        margin-top: 4px;
    }

    /* File Upload */
    .sk-file-upload {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .sk-btn-upload {
        padding: 8px 16px;
        background: #E5E6E6;
        border: none;
        border-radius: 8px;
        font-family: 'Urbanist', sans-serif;
        font-size: 13px;
        font-weight: 600;
        color: #374151;
        cursor: pointer;
        transition: background 0.2s;
    }

    .sk-btn-upload:hover {
        background: #D1D5DB;
    }

    .sk-file-name {
        font-size: 13px;
        color: #6B7271;
    }

    .sk-image-preview {
        margin-top: 12px;
        position: relative;
        display: inline-block;
    }

    .sk-image-preview img {
        max-width: 200px;
        max-height: 150px;
        border-radius: 8px;
        border: 1px solid #E5E6E6;
    }

    .sk-btn-remove-image {
        position: absolute;
        top: -8px;
        right: -8px;
        padding: 4px 8px;
        background: #DC2626;
        border: none;
        border-radius: 4px;
        font-size: 11px;
        color: #fff;
        cursor: pointer;
    }

    /* Form Actions */
    .sk-form-actions {
        display: flex;
        gap: 12px;
        justify-content: flex-end;
        padding-top: 20px;
        border-top: 1px solid #E5E6E6;
        margin-top: 20px;
    }

    .sk-btn-secondary {
        padding: 10px 24px;
        background: transparent;
        border: 1px solid #E5E6E6;
        border-radius: 8px;
        font-family: 'Urbanist', sans-serif;
        font-size: 14px;
        font-weight: 600;
        color: #6B7271;
        cursor: pointer;
        transition: all 0.2s;
    }

    .sk-btn-secondary:hover {
        background: #F8F9FA;
    }

    .sk-btn-primary {
        padding: 10px 24px;
        background: #39746E;
        border: none;
        border-radius: 8px;
        font-family: 'Urbanist', sans-serif;
        font-size: 14px;
        font-weight: 600;
        color: #fff;
        cursor: pointer;
        transition: all 0.2s;
    }

    .sk-btn-primary:hover {
        background: #2d5a55;
    }

    .sk-btn-primary:disabled {
        background: #9CA3AF;
        cursor: not-allowed;
    }

    /* Preview Panel */
    .sk-preview-panel {
        width: 60%;
        display: flex;
        flex-direction: column;
        background: #F8F9FA;
    }

    .sk-preview-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 24px;
        border-bottom: 1px solid #E5E6E6;
        background: #fff;
    }

    .sk-preview-header h3 {
        font-family: 'Urbanist', sans-serif;
        font-size: 14px;
        font-weight: 600;
        color: #374151;
        margin: 0;
    }

    .sk-btn-refresh {
        padding: 6px 12px;
        background: #E3F4F1;
        border: none;
        border-radius: 6px;
        font-family: 'Urbanist', sans-serif;
        font-size: 12px;
        font-weight: 600;
        color: #39746E;
        cursor: pointer;
        transition: background 0.2s;
    }

    .sk-btn-refresh:hover {
        background: #B6E2DB;
    }

    .sk-preview-content {
        flex: 1;
        position: relative;
        padding: 16px;
    }

    #skPreviewIframe {
        width: 100%;
        height: 100%;
        border: 1px solid #E5E6E6;
        border-radius: 8px;
        background: #fff;
    }

    .sk-preview-loading,
    .sk-preview-placeholder {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        text-align: center;
        color: #6B7271;
    }

    .sk-spinner {
        width: 32px;
        height: 32px;
        border: 3px solid #E5E6E6;
        border-top-color: #39746E;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        margin: 0 auto 12px;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    /* Responsive */
    @media (max-width: 1200px) {
        .sk-form-panel {
            width: 45%;
        }
        .sk-preview-panel {
            width: 55%;
        }
    }

    @media (max-width: 900px) {
        .sk-modal-body {
            flex-direction: column;
        }
        .sk-form-panel,
        .sk-preview-panel {
            width: 100%;
        }
        .sk-form-panel {
            max-height: 50vh;
        }
        .sk-preview-panel {
            max-height: 50vh;
        }
    }
</style>

<script>
    let previewTimeout = null;
    let existingSkData = null;

    // Open SK Modal
    function openSkModal() {
        document.getElementById('skBankSampahModal').classList.add('show');
        document.body.style.overflow = 'hidden';

        // Load existing data if available
        loadExistingSkData();

        // Add event listeners for live preview
        setupLivePreview();
    }

    // Close SK Modal
    function closeSkModal() {
        document.getElementById('skBankSampahModal').classList.remove('show');
        document.body.style.overflow = '';
    }

    // Load existing SK data
    function loadExistingSkData() {
        fetch('{{ route("sk-bank-sampah.show", $bankSampah) }}', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data) {
                existingSkData = data.data;
                populateForm(data.data);
            }
        })
        .catch(error => {
            console.log('No existing SK data:', error);
        });
    }

    // Populate form with existing data
    function populateForm(data) {
        const form = document.getElementById('skBankSampahForm');
        const sk = data.sk;

        if (!sk) return;

        // Populate basic fields
        form.querySelector('[name="nama_bank_sampah"]').value = sk.nama_bank_sampah || '';
        form.querySelector('[name="alamat_bank_sampah"]').value = sk.alamat_bank_sampah || '';
        form.querySelector('[name="nomor_surat"]').value = sk.nomor_surat || '';
        form.querySelector('[name="tanggal_ditetapkan"]').value = sk.tanggal_ditetapkan ? sk.tanggal_ditetapkan.split('T')[0] : '';
        form.querySelector('[name="tanggal_rapat_musyawarah"]').value = sk.tanggal_rapat_musyawarah ? sk.tanggal_rapat_musyawarah.split('T')[0] : '';
        form.querySelector('[name="nama_desa_kelurahan"]').value = sk.nama_desa_kelurahan || '';
        form.querySelector('[name="kecamatan"]').value = sk.kecamatan || '';
        form.querySelector('[name="kabupaten"]').value = sk.kabupaten || '';
        form.querySelector('[name="provinsi"]').value = sk.provinsi || '';
        form.querySelector('[name="kode_pos"]').value = sk.kode_pos || '';
        form.querySelector('[name="masa_bakti_mulai"]').value = sk.masa_bakti_mulai || new Date().getFullYear();
        form.querySelector('[name="masa_bakti_selesai"]').value = sk.masa_bakti_selesai || (new Date().getFullYear() + 4);
        form.querySelector('[name="nama_kepala_desa"]').value = sk.nama_kepala_desa || '';

        // Show existing pengurus image preview
        if (data.pengurus_image_url) {
            const preview = document.getElementById('pengurusPreview');
            const img = document.getElementById('pengurusPreviewImg');
            img.src = data.pengurus_image_url;
            preview.style.display = 'block';
            document.getElementById('pengurusFileName').textContent = 'Gambar tersimpan';
        }

        // Show existing struktur image preview
        if (data.struktur_organisasi_url) {
            const preview = document.getElementById('strukturPreview');
            const img = document.getElementById('strukturPreviewImg');
            img.src = data.struktur_organisasi_url;
            preview.style.display = 'block';
            document.getElementById('strukturFileName').textContent = 'Gambar tersimpan';
        }

        // Update preview
        setTimeout(updatePreview, 500);
    }

    // Handle pengurus image
    document.getElementById('pengurusImageInput').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            document.getElementById('pengurusFileName').textContent = file.name;

            // Show preview
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('pengurusPreviewImg').src = e.target.result;
                document.getElementById('pengurusPreview').style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
    });

    function removePengurusImage() {
        document.getElementById('pengurusImageInput').value = '';
        document.getElementById('pengurusFileName').textContent = 'Belum ada file dipilih';
        document.getElementById('pengurusPreview').style.display = 'none';
    }

    // Handle struktur organisasi image
    document.getElementById('strukturOrganisasiInput').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            document.getElementById('strukturFileName').textContent = file.name;

            // Show preview
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('strukturPreviewImg').src = e.target.result;
                document.getElementById('strukturPreview').style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
    });

    function removeStrukturImage() {
        document.getElementById('strukturOrganisasiInput').value = '';
        document.getElementById('strukturFileName').textContent = 'Belum ada file dipilih';
        document.getElementById('strukturPreview').style.display = 'none';
    }

    // Setup live preview
    function setupLivePreview() {
        const form = document.getElementById('skBankSampahForm');
        form.querySelectorAll('input, select, textarea').forEach(input => {
            input.addEventListener('input', debouncePreview);
            input.addEventListener('change', debouncePreview);
        });
    }

    // Debounce preview updates
    function debouncePreview() {
        clearTimeout(previewTimeout);
        previewTimeout = setTimeout(updatePreview, 500);
    }

    // Update preview
    function updatePreview() {
        const form = document.getElementById('skBankSampahForm');
        const formData = new FormData(form);

        // Convert FormData to JSON object (excluding files)
        const data = {};
        formData.forEach((value, key) => {
            if (key !== '_token' && key !== 'pengurus_image' && key !== 'struktur_organisasi') {
                data[key] = value;
            }
        });

        // Show loading
        document.getElementById('skPreviewLoading').style.display = 'block';
        document.getElementById('skPreviewPlaceholder').style.display = 'none';

        fetch('{{ route("sk-bank-sampah.preview") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'text/html'
            },
            body: JSON.stringify(data)
        })
        .then(response => response.text())
        .then(html => {
            const iframe = document.getElementById('skPreviewIframe');
            iframe.srcdoc = html;
            document.getElementById('skPreviewLoading').style.display = 'none';
        })
        .catch(error => {
            console.error('Preview error:', error);
            document.getElementById('skPreviewLoading').style.display = 'none';
        });
    }

    // Form submission
    document.getElementById('skBankSampahForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const form = this;
        const formData = new FormData(form);
        const submitBtn = form.querySelector('button[type="submit"]');
        const btnText = submitBtn.querySelector('.sk-btn-text');
        const btnLoading = submitBtn.querySelector('.sk-btn-loading');

        // Show loading
        submitBtn.disabled = true;
        btnText.style.display = 'none';
        btnLoading.style.display = 'inline';

        fetch('{{ route("sk-bank-sampah.store", $bankSampah) }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Trigger PDF download
                window.location.href = data.data.download_url;

                // Close modal after a short delay
                setTimeout(() => {
                    closeSkModal();
                    alert('SK Bank Sampah berhasil disimpan dan di-download!');
                }, 1000);
            } else {
                if (data.errors) {
                    const messages = Object.values(data.errors).flat().join('\n');
                    alert('Validasi gagal:\n' + messages);
                } else {
                    alert(data.message || 'Terjadi kesalahan');
                }
            }
        })
        .catch(error => {
            console.error('Submit error:', error);
            alert('Terjadi kesalahan saat menyimpan SK');
        })
        .finally(() => {
            submitBtn.disabled = false;
            btnText.style.display = 'inline';
            btnLoading.style.display = 'none';
        });
    });
</script>
