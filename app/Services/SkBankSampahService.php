<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\BankSampah;
use App\Models\SkBankSampah;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

/**
 * Service class for SK Bank Sampah operations.
 *
 * Handles SK creation, updates, preview generation, and PDF export.
 * All data is stored independently (not referenced from bank_sampah table).
 */
class SkBankSampahService
{
    private const UPLOAD_PATH = 'uploads/sk_bank_sampah';

    /**
     * Generate preview HTML for SK Bank Sampah.
     *
     * @param array{
     *     nama_bank_sampah?: string,
     *     alamat_bank_sampah?: string,
     *     nomor_surat?: string,
     *     tanggal_ditetapkan?: string,
     *     tanggal_rapat_musyawarah?: string,
     *     nama_desa_kelurahan?: string,
     *     kecamatan?: string,
     *     kabupaten?: string,
     *     provinsi?: string,
     *     kode_pos?: string|null,
     *     masa_bakti_mulai?: int,
     *     masa_bakti_selesai?: int,
     *     nama_kepala_desa?: string
     * } $data
     */
    public function generatePreviewHtml(BankSampah $bankSampah, array $data): string
    {
        // Create a mock SK object for preview
        $sk = (object) [
            'nama_bank_sampah' => $data['nama_bank_sampah'] ?? $bankSampah->nama_bank_sampah,
            'alamat_bank_sampah' => $data['alamat_bank_sampah'] ?? $bankSampah->alamat_bank_sampah,
            'nomor_surat' => $data['nomor_surat'] ?? '',
            'tanggal_ditetapkan' => ! empty($data['tanggal_ditetapkan']) ? \Carbon\Carbon::parse($data['tanggal_ditetapkan']) : null,
            'tanggal_rapat_musyawarah' => ! empty($data['tanggal_rapat_musyawarah']) ? \Carbon\Carbon::parse($data['tanggal_rapat_musyawarah']) : null,
            'nama_desa_kelurahan' => $data['nama_desa_kelurahan'] ?? '',
            'kecamatan' => $data['kecamatan'] ?? '',
            'kabupaten' => $data['kabupaten'] ?? '',
            'provinsi' => $data['provinsi'] ?? '',
            'kode_pos' => $data['kode_pos'] ?? '',
            'masa_bakti_mulai' => $data['masa_bakti_mulai'] ?? date('Y'),
            'masa_bakti_selesai' => $data['masa_bakti_selesai'] ?? (date('Y') + 4),
            'nama_kepala_desa' => $data['nama_kepala_desa'] ?? '',
        ];

        return view('pdf.sk-bank-sampah', [
            'sk' => $sk,
            'pengurusImageBase64' => null,
            'strukturOrganisasiBase64' => null,
            'isPreview' => true,
        ])->render();
    }

    /**
     * Create or update SK Bank Sampah.
     *
     * @param array{
     *     nama_bank_sampah: string,
     *     alamat_bank_sampah: string,
     *     nomor_surat: string,
     *     tanggal_ditetapkan: string,
     *     tanggal_rapat_musyawarah: string,
     *     nama_desa_kelurahan: string,
     *     kecamatan: string,
     *     kabupaten: string,
     *     provinsi: string,
     *     kode_pos?: string|null,
     *     masa_bakti_mulai: int,
     *     masa_bakti_selesai: int,
     *     nama_kepala_desa: string
     * } $skData
     */
    public function createOrUpdateSk(
        BankSampah $bankSampah,
        array $skData,
        ?UploadedFile $pengurusImage = null,
        ?UploadedFile $strukturImage = null
    ): SkBankSampah {
        return DB::transaction(function () use ($bankSampah, $skData, $pengurusImage, $strukturImage) {
            // Get existing SK or create new
            $sk = SkBankSampah::firstOrNew(['bank_sampah_id' => $bankSampah->id]);

            // Handle pengurus image upload
            if ($pengurusImage) {
                $pengurusPath = $this->uploadImage($bankSampah->id, $pengurusImage, 'pengurus');

                // Delete old image if exists
                if ($sk->exists && $sk->pengurus_image_path) {
                    $this->deleteImage($sk->pengurus_image_path);
                }

                $sk->pengurus_image_path = $pengurusPath;
            }

            // Handle struktur organisasi image upload
            if ($strukturImage) {
                $strukturPath = $this->uploadImage($bankSampah->id, $strukturImage, 'struktur');

                // Delete old image if exists
                if ($sk->exists && $sk->struktur_organisasi_path) {
                    $this->deleteImage($sk->struktur_organisasi_path);
                }

                $sk->struktur_organisasi_path = $strukturPath;
            }

            // Update SK data
            $sk->fill($skData);
            $sk->bank_sampah_id = $bankSampah->id;
            $sk->save();

            return $sk;
        });
    }

    /**
     * Download SK Bank Sampah as PDF.
     */
    public function downloadPdf(SkBankSampah $sk): \Illuminate\Http\Response
    {
        // Convert pengurus image to base64
        $pengurusBase64 = null;
        if ($sk->pengurus_image_path) {
            $pengurusBase64 = $this->getImageBase64($sk->pengurus_image_path);
        }

        // Convert struktur organisasi image to base64
        $strukturBase64 = null;
        if ($sk->struktur_organisasi_path) {
            $strukturBase64 = $this->getImageBase64($sk->struktur_organisasi_path);
        }

        $pdf = Pdf::loadView('pdf.sk-bank-sampah', [
            'sk' => $sk,
            'pengurusImageBase64' => $pengurusBase64,
            'strukturOrganisasiBase64' => $strukturBase64,
            'isPreview' => false,
        ]);

        $pdf->setPaper('A4', 'portrait');

        $filename = 'SK_' . preg_replace('/[^a-zA-Z0-9]/', '_', $sk->nama_bank_sampah) . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Get SK Bank Sampah data for editing.
     *
     * @return array{
     *     sk: SkBankSampah|null,
     *     pengurus_image_url: string|null,
     *     struktur_organisasi_url: string|null
     * }
     */
    public function getSkData(BankSampah $bankSampah): array
    {
        $sk = SkBankSampah::where('bank_sampah_id', $bankSampah->id)->first();

        return [
            'sk' => $sk,
            'pengurus_image_url' => $sk?->pengurus_image_path
                ? asset(self::UPLOAD_PATH . '/' . $sk->pengurus_image_path)
                : null,
            'struktur_organisasi_url' => $sk?->struktur_organisasi_path
                ? asset(self::UPLOAD_PATH . '/' . $sk->struktur_organisasi_path)
                : null,
        ];
    }

    /**
     * Upload image file.
     */
    private function uploadImage(int $bankSampahId, UploadedFile $file, string $type): string
    {
        $uploadPath = public_path(self::UPLOAD_PATH);

        // Create directory if not exists
        if (! File::isDirectory($uploadPath)) {
            File::makeDirectory($uploadPath, 0755, true);
        }

        $filename = $type . '_' . $bankSampahId . '_' . time() . '.' . $file->getClientOriginalExtension();
        $file->move($uploadPath, $filename);

        return $filename;
    }

    /**
     * Get image as base64 string.
     */
    private function getImageBase64(string $filename): ?string
    {
        $filePath = public_path(self::UPLOAD_PATH . '/' . $filename);

        if (! File::exists($filePath)) {
            return null;
        }

        $mimeType = mime_content_type($filePath);

        return 'data:' . $mimeType . ';base64,' . base64_encode(File::get($filePath));
    }

    /**
     * Delete image file.
     */
    private function deleteImage(string $filename): void
    {
        $filePath = public_path(self::UPLOAD_PATH . '/' . $filename);

        if (File::exists($filePath)) {
            File::delete($filePath);
        }
    }
}
