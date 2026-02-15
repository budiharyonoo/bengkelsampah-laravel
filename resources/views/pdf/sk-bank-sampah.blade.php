<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SK Bank Sampah - {{ $sk->nama_bank_sampah ?? 'Bank Sampah' }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 0;
        }

        * {
            margin: 0;
            padding: 0;
        }

        body {
            font-family: serif;
            font-size: 9pt;
            line-height: 1.4;
            color: #000;
            background: #fff;
        }

        .page {
            padding: 1.5cm 2cm 1.5cm 2cm;
            page-break-after: always;
        }

        .page:last-child {
            page-break-after: auto;
        }

        .header {
            text-align: center;
        }

        .header-logo img {
            width: 180px;
            margin-top: -0.5cm;
            height: auto;
        }

        .document-title {
            text-align: center;
        }

        .document-title h1 {
            font-size: 9pt;
            font-weight: bold;
        }

        .document-number {
            font-size: 9pt;
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

        .content {
            text-align: justify;
            margin: 15px 0;
        }

        .memutuskan {
            text-align: center;
            margin: 15px 0;
        }

        .memutuskan h2 {
            font-size: 9pt;
            font-weight: bold;
        }

        .signature-section {
            margin-top: 25px;
            page-break-inside: avoid;
        }

        .signature-title {
            margin-bottom: 70px;
        }

        .signature-name {
            font-weight: bold;
            text-decoration: underline;
            white-space: nowrap;
        }

        .image-section {
            text-align: center;
            margin: 20px 0;
            page-break-inside: avoid;
        }

        .image-container img {
            max-width: 100%;
            max-height: 450px;
        }

        .image-placeholder {
            border: 1px dashed #999;
            padding: 30px;
            color: #666;
            font-style: italic;
        }

        .lampiran-title {
            text-align: center;
            font-weight: bold;
            margin: 20px 0 15px 0;
        }

        .data-section {
            margin: 15px 0;
        }

        .data-section-title {
            font-weight: bold;
            margin-bottom: 8px;
        }

        /* Preview extra styling */
        @if (isset($isPreview) && $isPreview)
            body {
                padding: 15px;
                background: #f5f5f5;
            }

            .page {
                border: 1px solid #ccc;
                margin-bottom: 15px;
                background: #fff;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
                max-width: 210mm;
            }
        @endif
    </style>
</head>

<body>
    @php
        $garudaBase64 = null;
        $garudaPath = public_path('assets/garuda.png');
        if (!file_exists($garudaPath)) {
            $garudaPath = asset('assets/garuda.png');
        }
        $garudaBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($garudaPath));

        // Determine jabatan type labels based on tipe_jabatan
        $tipeJabatan = $sk->tipe_jabatan ?? 'desa';
        $isLurah = $tipeJabatan === 'lurah';
        $jabatanTitle = $sk->jabatan_title ?? ($isLurah ? 'Kepala Lurah' : 'Kepala Desa');
        $areaTypeLabel = $sk->area_type_label ?? ($isLurah ? 'Kelurahan' : 'Desa');
        $areaTypeLabelUpper = $sk->area_type_label_upper ?? ($isLurah ? 'KELURAHAN' : 'DESA');
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
            <h1>KEPUTUSAN {{ strtoupper($jabatanTitle) }} {{ strtoupper($sk->nama_desa_kelurahan ?? $areaTypeLabelUpper) }}</h1>
            <p class="document-number">Nomor: {{ $sk->nomor_surat ?? '____/____/____' }}</p>
        </div>

        <div class="tentang-section">
            <div class="tentang-title">TENTANG</div>
            <div class="tentang-content">
                PEMBENTUKAN PENGURUS BANK SAMPAH {{ strtoupper($sk->nama_bank_sampah ?? 'BANK SAMPAH') }}<br>
                {{ strtoupper($sk->kecamatan ?? 'KECAMATAN') }}
                {{ strtoupper($sk->kabupaten ?? 'KABUPATEN') }} MASA BAKTI {{ $sk->masa_bakti_mulai ?? date('Y') }} -
                {{ $sk->masa_bakti_selesai ?? date('Y') + 4 }}
            </div>
            <div class="tentang-content-desa">
                {{ strtoupper($jabatanTitle) }} {{ strtoupper($sk->nama_desa_kelurahan ?? $areaTypeLabelUpper) }},
            </div>
        </div>

        <div class="content">
            {{-- Menimbang --}}
            <table cellpadding="0" cellspacing="0" border="0" style="width: 100%; margin-bottom: 8px;">
                <tr>
                    <td style="width: 15%; vertical-align: top;" rowspan="3">Menimbang</td>
                    <td style="width: 2%; vertical-align: top;" rowspan="3">:</td>
                    <td style="width: 4%; vertical-align: top;">a.</td>
                    <td style="width: 79%; vertical-align: top; text-align: justify;">
                        bahwa sampah selama ini belum dikelola dengan baik sehingga menimbulkan dampak negatif terhadap kesehatan masyarakat dan lingkungan
                    </td>
                </tr>
                <tr>
                    <td style="vertical-align: top;">b.</td>
                    <td style="vertical-align: top; text-align: justify;">
                        bahwa pengelolaan sampah perlu dilakukan secara komprehensif dan terpadu agar dapat mengubah perilaku hidup sehat masyarakat dan sekaligus dapat memberikan manfaat secara ekonomi dan kesehatan lingkungan;
                    </td>
                </tr>
                <tr>
                    <td style="vertical-align: top;">c.</td>
                    <td style="vertical-align: top; text-align: justify;">
                        bahwa berdasarkan pertimbangan sebagaimana dimaksud dalam huruf a dan b maka perlu menetapkan Keputusan {{ $jabatanTitle }} {{ $sk->nama_desa_kelurahan ?? $areaTypeLabel }} tentang Pembentukan Bank Sampah {{ $areaTypeLabel }} {{ $sk->nama_desa_kelurahan ?? '' }}.
                    </td>
                </tr>
            </table>

            {{-- Mengingat --}}
            <table cellpadding="0" cellspacing="0" border="0" style="width: 100%; margin-bottom: 8px;">
                <tr>
                    <td style="width: 15%; vertical-align: top;" rowspan="5">Mengingat</td>
                    <td style="width: 2%; vertical-align: top;" rowspan="5">:</td>
                    <td style="width: 4%; vertical-align: top;">1.</td>
                    <td style="width: 79%; vertical-align: top; text-align: justify;">
                        Undang-Undang Nomor 18 Tahun 2008 tentang Pengelolaan Sampah (Lembaran Negara Republik Indonesia Tahun 2008 Nomor 690);
                    </td>
                </tr>
                <tr>
                    <td style="vertical-align: top;">2.</td>
                    <td style="vertical-align: top; text-align: justify;">
                        Undang-Undang Nomor 32 Tahun 2009 tentang Perlindungan dan Pengelolaan Lingkungan Hidup (Lembaran Negara Republik Indonesia Tahun 2009 Nomor 14);
                    </td>
                </tr>
                <tr>
                    <td style="vertical-align: top;">3.</td>
                    <td style="vertical-align: top; text-align: justify;">
                        Peraturan Presiden Nomor 81 Tahun 2012 tentang Pengelolaan Sampah Rumah Tangga dan Sampah Sejenis Sampah Rumah Tangga;
                    </td>
                </tr>
                <tr>
                    <td style="vertical-align: top;">4.</td>
                    <td style="vertical-align: top; text-align: justify;">
                        Peraturan Menteri Lingkungan Hidup Republik Indonesia Nomor 13 Tahun 2012 tentang Pedoman Pelaksanaan Reduce, Reuse, dan Recycle melalui Bank Sampah;
                    </td>
                </tr>
                <tr>
                    <td style="vertical-align: top;">5.</td>
                    <td style="vertical-align: top; text-align: justify;">
                        Peraturan Daerah {{ $sk->kabupaten ?? 'Kabupaten' }} Nomor 5 Tahun 2020 Tentang Pengelolaan Sampah (Lembaran Daerah {{ $sk->kabupaten ?? 'Kabupaten' }} Tahun 2020 Nomor 5);
                    </td>
                </tr>
            </table>

            {{-- Memperhatikan --}}
            <table cellpadding="0" cellspacing="0" border="0" style="width: 100%; margin-bottom: 8px;">
                <tr>
                    <td style="width: 15%; vertical-align: top;">Memperhatikan</td>
                    <td style="width: 2%; vertical-align: top;">:</td>
                    <td style="width: 83%; vertical-align: top; text-align: justify;">
                        Rapat Musyawarah Kelompok Masyarakat {{ $areaTypeLabel }} {{ $sk->nama_desa_kelurahan ?? '' }} tanggal {{ $sk->tanggal_rapat_musyawarah ? $sk->tanggal_rapat_musyawarah->translatedFormat('d F Y') : '____' }}.
                    </td>
                </tr>
            </table>

            <div class="memutuskan">
                <h2>MEMUTUSKAN</h2>
            </div>

            {{-- Menetapkan --}}
            <table cellpadding="0" cellspacing="0" border="0" style="width: 100%; margin-bottom: 5px;">
                <tr>
                    <td style="width: 15%; vertical-align: top;">Menetapkan</td>
                    <td style="width: 2%; vertical-align: top;">:</td>
                    <td style="width: 83%; vertical-align: top; text-align: justify;">
                        <strong>
                            KEPUTUSAN {{ strtoupper($jabatanTitle) }} {{ strtoupper($sk->nama_desa_kelurahan ?? $areaTypeLabelUpper) }} TENTANG PEMBENTUKAN BANK SAMPAH {{ $areaTypeLabelUpper }} {{ strtoupper($sk->nama_desa_kelurahan ?? '') }} {{ strtoupper($sk->kabupaten ?? 'KABUPATEN') }} KECAMATAN {{ strtoupper($sk->kecamatan ?? 'KECAMATAN') }} PROVINSI {{ strtoupper($sk->provinsi ?? 'PROVINSI') }} MASA BAKTI {{ $sk->masa_bakti_mulai ?? date('Y') }}-{{ $sk->masa_bakti_selesai ?? date('Y') + 4 }}
                        </strong>
                    </td>
                </tr>
            </table>

            <table cellpadding="0" cellspacing="0" border="0" style="width: 100%; margin-bottom: 5px;">
                <tr>
                    <td style="width: 15%; vertical-align: top;">KESATU</td>
                    <td style="width: 2%; vertical-align: top;">:</td>
                    <td style="width: 83%; vertical-align: top; text-align: justify;">
                        Mengesahkan berdirinya Bank Sampah {{ $areaTypeLabel }} {{ $sk->nama_desa_kelurahan ?? '' }} dengan nama "{{ $sk->nama_bank_sampah ?? '' }}" {{ $areaTypeLabel }} {{ $sk->nama_desa_kelurahan ?? '' }};
                    </td>
                </tr>
            </table>

            <table cellpadding="0" cellspacing="0" border="0" style="width: 100%; margin-bottom: 5px;">
                <tr>
                    <td style="width: 15%; vertical-align: top;">KEDUA</td>
                    <td style="width: 2%; vertical-align: top;">:</td>
                    <td style="width: 83%; vertical-align: top; text-align: justify;">
                        Menetapkan saudara-saudara yang namanya dan jabatannya tercantum dalam lampiran keputusan ini sebagai pengurus kelompok Pengelola Bank Sampah "{{ $sk->nama_bank_sampah ?? '' }}" {{ $areaTypeLabel }} {{ $sk->nama_desa_kelurahan ?? '' }} sebagaimana lampiran yang tidak terpisahkan dengan keputusan ini;
                    </td>
                </tr>
            </table>

            @php
                $masaBaktiYears = ($sk->masa_bakti_selesai ?? date('Y') + 4) - ($sk->masa_bakti_mulai ?? date('Y'));
                $masaBaktiTerbilang =
                    [
                        1 => 'satu',
                        2 => 'dua',
                        3 => 'tiga',
                        4 => 'empat',
                        5 => 'lima',
                        6 => 'enam',
                        7 => 'tujuh',
                        8 => 'delapan',
                        9 => 'sembilan',
                        10 => 'sepuluh',
                    ][$masaBaktiYears] ?? (string) $masaBaktiYears;
            @endphp

            <table cellpadding="0" cellspacing="0" border="0" style="width: 100%; margin-bottom: 5px;">
                <tr>
                    <td style="width: 15%; vertical-align: top;">KETIGA</td>
                    <td style="width: 2%; vertical-align: top;">:</td>
                    <td style="width: 83%; vertical-align: top; text-align: justify;">
                        Masa bakti kepengurusan adalah selama {{ $masaBaktiYears }} ({{ $masaBaktiTerbilang }}) tahun sejak keputusan ini ditetapkan;
                    </td>
                </tr>
            </table>

            <table cellpadding="0" cellspacing="0" border="0" style="width: 100%; margin-bottom: 5px;">
                <tr>
                    <td style="width: 15%; vertical-align: top;">KEEMPAT</td>
                    <td style="width: 2%; vertical-align: top;">:</td>
                    <td style="width: 83%; vertical-align: top; text-align: justify;">
                        Surat keputusan ini mulai berlaku sejak tanggal ditetapkan dan apabila dikemudian hari terdapat kekeliruan dalam keputusan ini maka akan diadakan perbaikan sebagaimana mestinya;
                    </td>
                </tr>
            </table>

            {{-- Signature --}}
            <div class="signature-section" style="margin-top: 15px !important;">
                <table cellpadding="0" cellspacing="0" border="0" style="width: 100%;">
                    <tr>
                        <td style="width: 55%;"></td>
                        <td style="width: 45%; vertical-align: top;">
                            Ditetapkan di : {{ $sk->nama_desa_kelurahan ?? '' }}<br>
                            Pada tanggal : {{ $sk->tanggal_ditetapkan ? $sk->tanggal_ditetapkan->translatedFormat('d F Y') : '____' }}
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td style="text-align: center; padding-top: 10px;">
                            <div class="signature-title">{{ $jabatanTitle }}</div>
                            <div class="signature-name">{{ strtoupper($sk->nama_kepala_desa ?? '') }}</div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <!-- Page 2: Lampiran I - Susunan Pengurus -->
    <div class="page">
        <table cellpadding="0" cellspacing="0" border="0" style="width: 100%; margin-bottom: 15px;">
            <tr>
                <td style="width: 13%; vertical-align: top;">Lampiran 1</td>
                <td style="width: 2%; vertical-align: top;">:</td>
                <td style="width: 85%; vertical-align: top;">Keputusan {{ $jabatanTitle }} {{ ucwords(strtolower($sk->nama_desa_kelurahan ?? $areaTypeLabel)) }}</td>
            </tr>
            <tr>
                <td style="vertical-align: top;">Nomor</td>
                <td style="vertical-align: top;">:</td>
                <td style="vertical-align: top;">{{ $sk->nomor_surat ?? '____/____/____' }}</td>
            </tr>
            <tr>
                <td style="vertical-align: top;">Tanggal</td>
                <td style="vertical-align: top;">:</td>
                <td style="vertical-align: top;">{{ $sk->tanggal_ditetapkan ? $sk->tanggal_ditetapkan->translatedFormat('d F Y') : '____' }}</td>
            </tr>
            <tr>
                <td style="vertical-align: top;">Tentang</td>
                <td style="vertical-align: top;">:</td>
                <td style="vertical-align: top;">Keputusan {{ $jabatanTitle }} {{ ucwords(strtolower($sk->nama_desa_kelurahan ?? $areaTypeLabel)) }} Tentang Pembentukan Bank Sampah {{ ucwords(strtolower($sk->nama_bank_sampah ?? '')) }} {{ ucwords(strtolower($sk->kecamatan ?? '')) }} {{ ucwords(strtolower($sk->kabupaten ?? '')) }}</td>
            </tr>
        </table>

        <div class="lampiran-title">
            SUSUNAN ORGANISASI BANK SAMPAH {{ strtoupper($sk->nama_bank_sampah ?? 'BANK SAMPAH') }}<br>
            {{ $areaTypeLabelUpper }} {{ strtoupper($sk->nama_desa_kelurahan ?? '') }} MASA BAKTI {{ $sk->masa_bakti_mulai ?? date('Y') }}-{{ $sk->masa_bakti_selesai ?? date('Y') + 4 }}
        </div>

        <div class="data-section">
            <div class="data-section-title">I. DATA ORGANISASI</div>
            <table cellpadding="0" cellspacing="0" border="0" style="width: 95%; margin-left: 20px;">
                <tr>
                    <td style="width: 5%; vertical-align: top;">1.</td>
                    <td style="width: 30%; vertical-align: top;">NAMA BANK SAMPAH</td>
                    <td style="width: 3%; vertical-align: top;">:</td>
                    <td style="width: 62%; vertical-align: top;">{{ strtoupper($sk->nama_bank_sampah ?? '') }}</td>
                </tr>
                <tr>
                    <td style="vertical-align: top;">2.</td>
                    <td style="vertical-align: top;">ALAMAT</td>
                    <td style="vertical-align: top;">:</td>
                    <td style="vertical-align: top;">{{ strtoupper($sk->alamat_bank_sampah ?? '') }}</td>
                </tr>
            </table>
        </div>

        <div class="data-section">
            <div class="data-section-title">II. PEMBINA :</div>
            <div style="margin-left: 20px;">
                - {{ strtoupper($jabatanTitle) }} {{ strtoupper($sk->nama_desa_kelurahan ?? $areaTypeLabelUpper) }}
            </div>
        </div>

        <div class="data-section">
            <div class="data-section-title">III. PENGELOLA :</div>
        </div>

        @if (isset($pengurusImageBase64) && $pengurusImageBase64)
            <div class="image-section" style="margin-top: 10px;">
                <div class="image-container">
                    <img src="{{ $pengurusImageBase64 }}" alt="Susunan Pengurus">
                </div>
            </div>
        @elseif(isset($isPreview) && $isPreview)
            <div class="image-section" style="margin-top: 10px;">
                <div class="image-placeholder">
                    <p>Gambar susunan pengurus dengan nama-nama yang terisi akan ditampilkan di sini</p>
                    <p style="font-size: 8pt; margin-top: 8px;">(Upload gambar pengurus pada form)</p>
                </div>
            </div>
        @endif

        <div class="signature-section" style="margin-top: 40px;">
            <table cellpadding="0" cellspacing="0" border="0" style="width: 100%;">
                <tr>
                    <td style="width: 55%;"></td>
                    <td style="width: 45%; vertical-align: top;">
                        Ditetapkan di : {{ $sk->nama_desa_kelurahan ?? '' }}<br>
                        Pada tanggal : {{ $sk->tanggal_ditetapkan ? $sk->tanggal_ditetapkan->translatedFormat('d F Y') : '____' }}
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td style="text-align: center; padding-top: 10px;">
                        <div class="signature-title">{{ $jabatanTitle }}</div>
                        <div class="signature-name">{{ strtoupper($sk->nama_kepala_desa ?? '') }}</div>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <!-- Page 3: Lampiran II - Struktur Organisasi (Optional) -->
    @if (isset($strukturOrganisasiBase64) && $strukturOrganisasiBase64)
        <div class="page">
            <table cellpadding="0" cellspacing="0" border="0" style="width: 100%; margin-bottom: 15px;">
                <tr>
                    <td style="width: 13%; vertical-align: top;">Lampiran 2</td>
                    <td style="width: 2%; vertical-align: top;">:</td>
                    <td style="width: 85%; vertical-align: top;">Keputusan {{ $jabatanTitle }} {{ ucwords(strtolower($sk->nama_desa_kelurahan ?? $areaTypeLabel)) }}</td>
                </tr>
                <tr>
                    <td style="vertical-align: top;">Nomor</td>
                    <td style="vertical-align: top;">:</td>
                    <td style="vertical-align: top;">{{ $sk->nomor_surat ?? '____/____/____' }}</td>
                </tr>
                <tr>
                    <td style="vertical-align: top;">Tanggal</td>
                    <td style="vertical-align: top;">:</td>
                    <td style="vertical-align: top;">{{ $sk->tanggal_ditetapkan ? $sk->tanggal_ditetapkan->translatedFormat('d F Y') : '____' }}</td>
                </tr>
                <tr>
                    <td style="vertical-align: top;">Tentang</td>
                    <td style="vertical-align: top;">:</td>
                    <td style="vertical-align: top;">Keputusan {{ $jabatanTitle }} {{ ucwords(strtolower($sk->nama_desa_kelurahan ?? $areaTypeLabel)) }} Tentang Pembentukan Bank Sampah {{ ucwords(strtolower($sk->nama_bank_sampah ?? '')) }} {{ ucwords(strtolower($sk->kecamatan ?? '')) }} {{ ucwords(strtolower($sk->kabupaten ?? '')) }}</td>
                </tr>
            </table>

            <div class="lampiran-title">
                STRUKTUR ORGANISASI BANK SAMPAH {{ strtoupper($sk->nama_bank_sampah ?? 'BANK SAMPAH') }}<br>
                {{ $areaTypeLabelUpper }} {{ strtoupper($sk->nama_desa_kelurahan ?? '') }} MASA BAKTI {{ $sk->masa_bakti_mulai ?? date('Y') }}-{{ $sk->masa_bakti_selesai ?? date('Y') + 4 }}
            </div>

            <div class="image-section" style="margin-top: 25px;">
                <div class="image-container">
                    <img src="{{ $strukturOrganisasiBase64 }}" alt="Struktur Organisasi">
                </div>
            </div>

            <div class="signature-section" style="margin-top: 40px;">
                <table cellpadding="0" cellspacing="0" border="0" style="width: 100%;">
                    <tr>
                        <td style="width: 55%;"></td>
                        <td style="width: 45%; vertical-align: top;">
                            Ditetapkan di : {{ $sk->nama_desa_kelurahan ?? '' }}<br>
                            Pada tanggal : {{ $sk->tanggal_ditetapkan ? $sk->tanggal_ditetapkan->translatedFormat('d F Y') : '____' }}
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td style="text-align: center; padding-top: 10px;">
                            <div class="signature-title">{{ $jabatanTitle }}</div>
                            <div class="signature-name">{{ strtoupper($sk->nama_kepala_desa ?? '') }}</div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    @elseif(isset($isPreview) && $isPreview)
        <div class="page">
            <table cellpadding="0" cellspacing="0" border="0" style="width: 100%; margin-bottom: 15px;">
                <tr>
                    <td style="width: 13%; vertical-align: top;">Lampiran 2</td>
                    <td style="width: 2%; vertical-align: top;">:</td>
                    <td style="width: 85%; vertical-align: top;">Keputusan {{ $jabatanTitle }} {{ $sk->nama_desa_kelurahan ?? $areaTypeLabel }}</td>
                </tr>
                <tr>
                    <td style="vertical-align: top;">Nomor</td>
                    <td style="vertical-align: top;">:</td>
                    <td style="vertical-align: top;">{{ $sk->nomor_surat ?? '____/____/____' }}</td>
                </tr>
                <tr>
                    <td style="vertical-align: top;">Tanggal</td>
                    <td style="vertical-align: top;">:</td>
                    <td style="vertical-align: top;">{{ $sk->tanggal_ditetapkan ? $sk->tanggal_ditetapkan->translatedFormat('d F Y') : '____' }}</td>
                </tr>
                <tr>
                    <td style="vertical-align: top;">Tentang</td>
                    <td style="vertical-align: top;">:</td>
                    <td style="vertical-align: top;">Keputusan {{ $jabatanTitle }} {{ ucwords(strtolower($sk->nama_desa_kelurahan ?? $areaTypeLabel)) }} Tentang Pembentukan Bank Sampah {{ ucwords(strtolower($sk->nama_bank_sampah ?? '')) }} Kecamatan {{ ucwords(strtolower($sk->kecamatan ?? '')) }} Kabupaten {{ ucwords(strtolower($sk->kabupaten ?? '')) }}</td>
                </tr>
            </table>

            <div class="lampiran-title">
                STRUKTUR ORGANISASI BANK SAMPAH {{ strtoupper($sk->nama_bank_sampah ?? 'BANK SAMPAH') }}<br>
                {{ $areaTypeLabelUpper }} {{ strtoupper($sk->nama_desa_kelurahan ?? '') }} MASA BAKTI {{ $sk->masa_bakti_mulai ?? date('Y') }}-{{ $sk->masa_bakti_selesai ?? date('Y') + 4 }}
            </div>

            <div class="image-section" style="margin-top: 25px;">
                <div class="image-placeholder">
                    <p>Gambar struktur organisasi akan ditampilkan di sini</p>
                    <p style="font-size: 8pt; margin-top: 8px;">(Upload gambar struktur organisasi pada form - opsional)</p>
                </div>
            </div>

            <div class="signature-section" style="margin-top: 40px;">
                <table cellpadding="0" cellspacing="0" border="0" style="width: 100%;">
                    <tr>
                        <td style="width: 55%;"></td>
                        <td style="width: 45%; vertical-align: top;">
                            Ditetapkan di : {{ $sk->nama_desa_kelurahan ?? '' }}<br>
                            Pada tanggal : {{ $sk->tanggal_ditetapkan ? $sk->tanggal_ditetapkan->translatedFormat('d F Y') : '____' }}
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td style="text-align: center; padding-top: 10px;">
                            <div class="signature-title">{{ $jabatanTitle }}</div>
                            <div class="signature-name">{{ strtoupper($sk->nama_kepala_desa ?? '') }}</div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    @endif
</body>

</html>
