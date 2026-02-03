<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Form request for creating/updating SK Bank Sampah.
 *
 * Validates SK Bank Sampah data:
 * - nama_bank_sampah: Required string (stored separately)
 * - alamat_bank_sampah: Required string (stored separately)
 * - nomor_surat: Required string
 * - tanggal_ditetapkan: Required date
 * - tanggal_rapat_musyawarah: Required date
 * - Location fields: Required strings
 * - masa_bakti: Required years
 * - nama_kepala_desa: Required string
 * - pengurus_image: Optional image file (pengurus as image)
 * - struktur_organisasi: Optional image file
 */
class StoreSkBankSampahRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            // Data Bank Sampah (stored separately)
            'nama_bank_sampah' => ['required', 'string', 'max:255'],
            'alamat_bank_sampah' => ['required', 'string'],

            // Data SK
            'nomor_surat' => ['required', 'string', 'max:100'],
            'tanggal_ditetapkan' => ['required', 'date'],
            'tanggal_rapat_musyawarah' => ['required', 'date'],

            // Data Wilayah
            'nama_desa_kelurahan' => ['required', 'string', 'max:100'],
            'kecamatan' => ['required', 'string', 'max:100'],
            'kabupaten' => ['required', 'string', 'max:100'],
            'provinsi' => ['required', 'string', 'max:100'],
            'kode_pos' => ['nullable', 'string', 'max:10'],

            // Masa Bakti
            'masa_bakti_mulai' => ['required', 'integer', 'min:2000', 'max:2100'],
            'masa_bakti_selesai' => ['required', 'integer', 'min:2000', 'max:2100', 'gte:masa_bakti_mulai'],

            // Pengesahan
            'nama_kepala_desa' => ['required', 'string', 'max:255'],

            // Images
            'pengurus_image' => ['nullable', 'image', 'mimes:jpeg,jpg,png', 'max:5120'],
            'struktur_organisasi' => ['nullable', 'image', 'mimes:jpeg,jpg,png', 'max:5120'],
        ];
    }

    /**
     * Get custom error messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nama_bank_sampah.required' => 'Nama bank sampah wajib diisi.',
            'alamat_bank_sampah.required' => 'Alamat bank sampah wajib diisi.',
            'nomor_surat.required' => 'Nomor surat wajib diisi.',
            'tanggal_ditetapkan.required' => 'Tanggal ditetapkan wajib diisi.',
            'tanggal_ditetapkan.date' => 'Format tanggal ditetapkan tidak valid.',
            'tanggal_rapat_musyawarah.required' => 'Tanggal rapat musyawarah wajib diisi.',
            'tanggal_rapat_musyawarah.date' => 'Format tanggal rapat tidak valid.',
            'nama_desa_kelurahan.required' => 'Nama desa/kelurahan wajib diisi.',
            'kecamatan.required' => 'Kecamatan wajib diisi.',
            'kabupaten.required' => 'Kabupaten wajib diisi.',
            'provinsi.required' => 'Provinsi wajib diisi.',
            'masa_bakti_mulai.required' => 'Tahun mulai masa bakti wajib diisi.',
            'masa_bakti_mulai.min' => 'Tahun mulai tidak valid.',
            'masa_bakti_selesai.required' => 'Tahun selesai masa bakti wajib diisi.',
            'masa_bakti_selesai.gte' => 'Tahun selesai harus lebih besar atau sama dengan tahun mulai.',
            'nama_kepala_desa.required' => 'Nama kepala desa/lurah wajib diisi.',
            'pengurus_image.image' => 'File pengurus harus berupa gambar.',
            'pengurus_image.mimes' => 'Format gambar pengurus harus JPG, JPEG, atau PNG.',
            'pengurus_image.max' => 'Ukuran gambar pengurus maksimal 5MB.',
            'struktur_organisasi.image' => 'File struktur organisasi harus berupa gambar.',
            'struktur_organisasi.mimes' => 'Format gambar struktur organisasi harus JPG, JPEG, atau PNG.',
            'struktur_organisasi.max' => 'Ukuran gambar struktur organisasi maksimal 5MB.',
        ];
    }

    /**
     * Get validated SK data.
     *
     * @return array{
     *     nama_bank_sampah: string,
     *     alamat_bank_sampah: string,
     *     nomor_surat: string,
     *     tanggal_ditetapkan: string,
     *     tanggal_rapat_musyawarah: string,
     *     nama_desa_kelurahan: string,
     *     kecamatan: string,
     *     kabupaten: string,
     *     provinsi: string,
     *     kode_pos: string|null,
     *     masa_bakti_mulai: int,
     *     masa_bakti_selesai: int,
     *     nama_kepala_desa: string
     * }
     */
    public function getSkData(): array
    {
        return [
            'nama_bank_sampah' => $this->validated('nama_bank_sampah'),
            'alamat_bank_sampah' => $this->validated('alamat_bank_sampah'),
            'nomor_surat' => $this->validated('nomor_surat'),
            'tanggal_ditetapkan' => $this->validated('tanggal_ditetapkan'),
            'tanggal_rapat_musyawarah' => $this->validated('tanggal_rapat_musyawarah'),
            'nama_desa_kelurahan' => $this->validated('nama_desa_kelurahan'),
            'kecamatan' => $this->validated('kecamatan'),
            'kabupaten' => $this->validated('kabupaten'),
            'provinsi' => $this->validated('provinsi'),
            'kode_pos' => $this->validated('kode_pos'),
            'masa_bakti_mulai' => (int) $this->validated('masa_bakti_mulai'),
            'masa_bakti_selesai' => (int) $this->validated('masa_bakti_selesai'),
            'nama_kepala_desa' => $this->validated('nama_kepala_desa'),
        ];
    }
}
