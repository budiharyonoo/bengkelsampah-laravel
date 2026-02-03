<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SK Bank Sampah - {{ $sk->nama_bank_sampah ?? 'Bank Sampah' }}</title>
    <style>
        @page {
            margin: 2cm 2cm 2cm 2cm;
            size: A4 portrait;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Bookman Old Style', 'Palatino Linotype', 'Book Antiqua', Palatino, Georgia, serif;
            /* font-family: 'Times New Roman', Palatino, Georgia, serif; */
            font-size: 9pt;
            line-height: 1.35;
            color: #000;
            background: #fff;
        }

        .page {
            page-break-after: always;
        }

        .page:last-child {
            page-break-after: auto;
        }

        /* Header Styles - No separator */
        .header {
            margin-top: -25px;
            text-align: center;
            /* margin-bottom: 10px; */
        }

        .header-logo {
            /* margin-bottom: 8px; */
        }

        .header-logo img {
            width: 200px;
            height: auto;
        }

        /* Document Title */
        .document-title {
            text-align: center;
            margin: 0;
        }

        .document-title h1 {
            font-size: 9pt;
            font-weight: bold;
            /* text-decoration: underline; */
            /* letter-spacing: 2px; */
            /* margin-bottom: 3px; */
        }

        .document-number {
            font-size: 9pt;
            /* margin-top: 3px; */
        }

        /* Content Styles */
        .content {
            text-align: justify;
            margin: 15px 0;
        }

        .content p {
            margin-bottom: 8px;
            text-indent: 1.27cm;
        }

        .content p.no-indent {
            text-indent: 0;
        }

        .tentang-section {
            text-align: center;
            margin: 15px 0;
        }

        .tentang-title {
            font-weight: bold;
            margin-bottom: 15px;
        }

        .tentang-content {
            font-weight: bold;
        }

        .tentang-content-desa {
            font-weight: bold;
            margin: 10px 0;
        }

        /* Menimbang/Mengingat Lists */
        .consideration-list {
            margin: 10px 0;
        }

        .consideration-item {
            display: table;
            width: 100%;
            margin-bottom: 5px;
        }

        .consideration-label {
            display: table-cell;
            width: 70px;
            vertical-align: top;
        }

        .consideration-colon {
            display: table-cell;
            width: 10px;
            vertical-align: top;
        }

        .consideration-content {
            display: table-cell;
            vertical-align: top;
            text-align: justify;
        }

        /* Lettered List (a, b, c) */
        .lettered-list {
            padding-left: 0;
        }

        .lettered-item {
            display: table;
            width: 100%;
            margin-bottom: 4px;
        }

        .lettered-label {
            display: table-cell;
            width: 20px;
            vertical-align: top;
        }

        .lettered-content {
            display: table-cell;
            vertical-align: top;
            text-align: justify;
        }

        /* MEMUTUSKAN Section */
        .memutuskan {
            text-align: center;
            margin: 15px 0;
        }

        .memutuskan h2 {
            font-size: 9pt;
            font-weight: bold;
            /* letter-spacing: 3px; */
        }

        .menetapkan {
            display: table;
            width: 100%;
            margin: 10px 0;
        }

        .menetapkan-label {
            display: table-cell;
            width: 70px;
            vertical-align: top;
        }

        .menetapkan-colon {
            display: table-cell;
            width: 10px;
            vertical-align: top;
        }

        .menetapkan-content {
            display: table-cell;
            vertical-align: top;
        }

        /* Pasal Section */
        .pasal {
            margin: 12px 0;
        }

        .pasal-title {
            text-align: center;
            font-weight: bold;
            margin-bottom: 6px;
        }

        .pasal-content {
            text-align: justify;
        }

        .pasal-content p {
            margin-bottom: 6px;
            text-indent: 1.27cm;
        }

        /* Signature Section */
        .signature-section {
            margin-top: 25px;
            page-break-inside: avoid;
        }

        .signature-date {
            text-align: right;
            margin-bottom: 10px;
        }

        .signature-block {
            float: right;
            width: 200px;
            text-align: center;
        }

        .signature-title {
            margin-bottom: 50px;
        }

        .signature-name {
            font-weight: bold;
            text-decoration: underline;
        }

        /* Image Sections */
        .image-section {
            text-align: center;
            margin: 20px 0;
            page-break-inside: avoid;
        }

        .image-title {
            font-size: 9pt;
            font-weight: bold;
            margin-bottom: 15px;
            text-decoration: underline;
        }

        .image-container img {
            max-width: 100%;
            max-height: 450px;
            margin: 0 auto;
        }

        .image-placeholder {
            border: 1px dashed #999;
            padding: 30px;
            color: #666;
            font-style: italic;
        }

        /* Preview Mode Styles */
        @if (isset($isPreview) && $isPreview)
            body {
                padding: 15px;
                background: #f5f5f5;
            }

            .page {
                border: 1px solid #ccc;
                padding: 2cm;
                margin-bottom: 15px;
                background: #fff;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            }
        @endif

        /* Clear float */
        .clearfix::after {
            content: "";
            display: table;
            clear: both;
        }
    </style>
</head>

<body>
    @php
        // Convert Garuda logo to base64 for PDF
        $garudaBase64 = null;
        $garudaPath = public_path('assets/garuda.png');
        if (file_exists($garudaPath)) {
            $garudaBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($garudaPath));
        }
    @endphp

    <!-- Page 1: SK Document -->
    <div class="page">
        <div class="header">
            @if ($garudaBase64)
                <div class="header-logo">
                    <img src="{{ $garudaBase64 }}" alt="Garuda">
                </div>
            @endif
        </div>

        <div class="document-title">
            <h1>KEPUTUSAN KEPALA {{ strtoupper($sk->nama_desa_kelurahan ?? 'DESA') }}</h1>
            <p class="document-number">Nomor: {{ $sk->nomor_surat ?? '____/____/____' }}</p>
        </div>

        <div class="tentang-section">
            <div class="tentang-title">TENTANG</div>
            <div class="tentang-content">
                PEMBENTUKAN PENGURUS BANK SAMPAH {{ strtoupper($sk->nama_bank_sampah ?? 'BANK SAMPAH') }}<br>
                KECAMATAN {{ strtoupper($sk->kecamatan ?? 'KECAMATAN') }}
                {{ strtoupper($sk->kabupaten ?? 'KABUPATEN') }} MASA BAKTI {{ $sk->masa_bakti_mulai ?? date('Y') }} -
                {{ $sk->masa_bakti_selesai ?? date('Y') + 4 }}
            </div>

            <div class="tentang-content-desa">
                KEPALA {{ strtoupper($sk->nama_desa_kelurahan ?? 'DESA') }},
            </div>
        </div>

        <div class="content">
            <div class="consideration-list">
                <div class="consideration-item">
                    <div class="consideration-label">Menimbang</div>
                    <div class="consideration-colon">:</div>
                    <div class="consideration-content">
                        <div class="lettered-list">
                            <div class="lettered-item">
                                <div class="lettered-label">a.</div>
                                <div class="lettered-content">
                                    bahwa dalam rangka mengurangi volume sampah dan meningkatkan kesadaran masyarakat
                                    dalam pengelolaan sampah, dipandang perlu membentuk Bank Sampah di tingkat
                                    desa/kelurahan;
                                </div>
                            </div>
                            <div class="lettered-item">
                                <div class="lettered-label">b.</div>
                                <div class="lettered-content">
                                    bahwa berdasarkan pertimbangan sebagaimana dimaksud pada huruf a, perlu menetapkan
                                    Keputusan Kepala Desa/Lurah tentang Pembentukan Pengurus Bank Sampah;
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="consideration-list">
                <div class="consideration-item">
                    <div class="consideration-label">Mengingat</div>
                    <div class="consideration-colon">:</div>
                    <div class="consideration-content">
                        <div class="lettered-list">
                            <div class="lettered-item">
                                <div class="lettered-label">1.</div>
                                <div class="lettered-content">
                                    Undang-Undang Nomor 18 Tahun 2008 tentang Pengelolaan Sampah;
                                </div>
                            </div>
                            <div class="lettered-item">
                                <div class="lettered-label">2.</div>
                                <div class="lettered-content">
                                    Peraturan Pemerintah Nomor 81 Tahun 2012 tentang Pengelolaan Sampah Rumah Tangga dan
                                    Sampah Sejenis Sampah Rumah Tangga;
                                </div>
                            </div>
                            <div class="lettered-item">
                                <div class="lettered-label">3.</div>
                                <div class="lettered-content">
                                    Peraturan Menteri Lingkungan Hidup dan Kehutanan Nomor
                                    P.97/MENLHK/SETJEN/KUM.1/11/2017 tentang Pedoman Pengembangan Sistem Bank Sampah;
                                </div>
                            </div>
                            <div class="lettered-item">
                                <div class="lettered-label">4.</div>
                                <div class="lettered-content">
                                    Hasil Rapat Musyawarah Pembentukan Bank Sampah tanggal
                                    {{ $sk->tanggal_rapat_musyawarah ? $sk->tanggal_rapat_musyawarah->translatedFormat('d F Y') : '____' }};
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="memutuskan">
                <h2>MEMUTUSKAN</h2>
            </div>

            <div class="menetapkan">
                <div class="menetapkan-label">Menetapkan</div>
                <div class="menetapkan-colon">:</div>
                <div class="menetapkan-content">
                    <strong>KEPUTUSAN KEPALA DESA/LURAH TENTANG PEMBENTUKAN PENGURUS BANK SAMPAH
                        "{{ strtoupper($sk->nama_bank_sampah ?? 'BANK SAMPAH') }}"</strong>
                </div>
            </div>
        </div>
    </div>

    <!-- Page 2: Pasal-pasal -->
    <div class="page">
        <div class="pasal">
            <div class="pasal-title">KESATU</div>
            <div class="pasal-content">
                <p>Membentuk Pengurus Bank Sampah "{{ $sk->nama_bank_sampah ?? '' }}" Desa/Kelurahan
                    {{ $sk->nama_desa_kelurahan ?? '' }}, Kecamatan {{ $sk->kecamatan ?? '' }},
                    {{ $sk->kabupaten ?? '' }}, {{ $sk->provinsi ?? '' }} dengan susunan pengurus sebagaimana
                    terlampir.</p>
            </div>
        </div>

        <div class="pasal">
            <div class="pasal-title">KEDUA</div>
            <div class="pasal-content">
                <p class="no-indent">Pengurus sebagaimana dimaksud pada diktum KESATU bertugas:</p>
                <div class="lettered-list" style="margin-left: 15px; margin-top: 5px;">
                    <div class="lettered-item">
                        <div class="lettered-label">a.</div>
                        <div class="lettered-content">Mengelola operasional Bank Sampah secara profesional dan
                            transparan;</div>
                    </div>
                    <div class="lettered-item">
                        <div class="lettered-label">b.</div>
                        <div class="lettered-content">Melakukan penimbangan, pencatatan, dan pembayaran kepada nasabah;
                        </div>
                    </div>
                    <div class="lettered-item">
                        <div class="lettered-label">c.</div>
                        <div class="lettered-content">Menyusun laporan kegiatan dan keuangan Bank Sampah secara berkala;
                        </div>
                    </div>
                    <div class="lettered-item">
                        <div class="lettered-label">d.</div>
                        <div class="lettered-content">Melakukan sosialisasi dan edukasi tentang pengelolaan sampah
                            kepada masyarakat;</div>
                    </div>
                    <div class="lettered-item">
                        <div class="lettered-label">e.</div>
                        <div class="lettered-content">Menjalin kerjasama dengan pihak terkait dalam pengembangan Bank
                            Sampah.</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="pasal">
            <div class="pasal-title">KETIGA</div>
            <div class="pasal-content">
                <p>Masa bakti pengurus Bank Sampah sebagaimana dimaksud pada diktum KESATU adalah
                    {{ $sk->masa_bakti_mulai ?? date('Y') }} sampai dengan
                    {{ $sk->masa_bakti_selesai ?? date('Y') + 4 }}.</p>
            </div>
        </div>

        <div class="pasal">
            <div class="pasal-title">KEEMPAT</div>
            <div class="pasal-content">
                <p>Segala biaya yang timbul akibat ditetapkannya keputusan ini dibebankan pada anggaran yang sesuai.</p>
            </div>
        </div>

        <div class="pasal">
            <div class="pasal-title">KELIMA</div>
            <div class="pasal-content">
                <p>Keputusan ini mulai berlaku pada tanggal ditetapkan dengan ketentuan apabila di kemudian hari
                    ternyata terdapat kekeliruan dalam penetapannya akan diadakan perbaikan sebagaimana mestinya.</p>
            </div>
        </div>

        <div class="signature-section clearfix">
            <div class="signature-date">
                Ditetapkan di: {{ $sk->nama_desa_kelurahan ?? '' }}<br>
                Pada tanggal: {{ $sk->tanggal_ditetapkan ? $sk->tanggal_ditetapkan->translatedFormat('d F Y') : '____' }}
            </div>

            <div class="signature-block">
                <div class="signature-title">
                    KEPALA DESA/LURAH {{ strtoupper($sk->nama_desa_kelurahan ?? '') }}
                </div>
                <div class="signature-name">
                    {{ strtoupper($sk->nama_kepala_desa ?? '') }}
                </div>
            </div>
        </div>
    </div>

    <!-- Page 3: Lampiran Pengurus (Image) -->
    @if (isset($pengurusImageBase64) && $pengurusImageBase64)
        <div class="page">
            <div class="image-section">
                <div class="image-title">
                    LAMPIRAN SUSUNAN PENGURUS<br>
                    BANK SAMPAH "{{ strtoupper($sk->nama_bank_sampah ?? 'BANK SAMPAH') }}"
                </div>
                <div class="image-container">
                    <img src="{{ $pengurusImageBase64 }}" alt="Susunan Pengurus">
                </div>
            </div>

            <div class="signature-section clearfix" style="margin-top: 40px;">
                <div class="signature-block">
                    <div class="signature-title">
                        Mengetahui,<br>
                        KEPALA DESA/LURAH {{ strtoupper($sk->nama_desa_kelurahan ?? '') }}
                    </div>
                    <div class="signature-name">
                        {{ strtoupper($sk->nama_kepala_desa ?? '') }}
                    </div>
                </div>
            </div>
        </div>
    @elseif(isset($isPreview) && $isPreview)
        <div class="page">
            <div class="image-section">
                <div class="image-title">
                    LAMPIRAN SUSUNAN PENGURUS<br>
                    BANK SAMPAH "{{ strtoupper($sk->nama_bank_sampah ?? 'BANK SAMPAH') }}"
                </div>
                <div class="image-placeholder">
                    <p>Gambar susunan pengurus akan ditampilkan di sini</p>
                    <p style="font-size: 8pt; margin-top: 8px;">(Upload gambar pengurus pada form)</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Page 4: Struktur Organisasi (Optional Image) -->
    @if (isset($strukturOrganisasiBase64) && $strukturOrganisasiBase64)
        <div class="page">
            <div class="image-section">
                <div class="image-title">
                    STRUKTUR ORGANISASI<br>
                    BANK SAMPAH "{{ strtoupper($sk->nama_bank_sampah ?? 'BANK SAMPAH') }}"
                </div>
                <div class="image-container">
                    <img src="{{ $strukturOrganisasiBase64 }}" alt="Struktur Organisasi">
                </div>
            </div>

            <div class="signature-section clearfix" style="margin-top: 40px;">
                <div class="signature-block">
                    <div class="signature-title">
                        Mengetahui,<br>
                        KEPALA DESA/LURAH {{ strtoupper($sk->nama_desa_kelurahan ?? '') }}
                    </div>
                    <div class="signature-name">
                        {{ strtoupper($sk->nama_kepala_desa ?? '') }}
                    </div>
                </div>
            </div>
        </div>
    @elseif(isset($isPreview) && $isPreview)
        <div class="page">
            <div class="image-section">
                <div class="image-title">
                    STRUKTUR ORGANISASI<br>
                    BANK SAMPAH "{{ strtoupper($sk->nama_bank_sampah ?? 'BANK SAMPAH') }}"
                </div>
                <div class="image-placeholder">
                    <p>Gambar struktur organisasi akan ditampilkan di sini</p>
                    <p style="font-size: 8pt; margin-top: 8px;">(Upload gambar struktur organisasi pada form -
                        opsional)</p>
                </div>
            </div>
        </div>
    @endif
</body>

</html>
