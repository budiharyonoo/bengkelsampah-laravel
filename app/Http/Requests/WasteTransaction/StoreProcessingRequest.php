<?php

declare(strict_types=1);

namespace App\Http\Requests\WasteTransaction;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Form request for creating a new waste processing transaction.
 *
 * Validates processing data:
 * - bank_sampah_id: Required valid bank sampah
 * - offtaker_id: Required valid offtaker (processor)
 * - items: Required array of processing items
 * - metode_pengolahan: Required processing method
 * - hasil_pengolahan: Optional processing result
 * - tanggal_transaksi: Required transaction date
 * - notes: Optional notes
 */
class StoreProcessingRequest extends FormRequest
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
            'bank_sampah_id' => ['required', 'exists:bank_sampah,id'],
            'offtaker_id' => ['required', 'exists:offtakers,id'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.sampah_id' => ['required', 'exists:sampah,id'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.01'],
            'metode_pengolahan' => ['required', 'string', 'max:255'],
            'hasil_pengolahan' => ['nullable', 'string'],
            'tanggal_transaksi' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:500'],
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
            'bank_sampah_id.required' => 'Bank sampah wajib dipilih.',
            'bank_sampah_id.exists' => 'Bank sampah tidak ditemukan.',
            'offtaker_id.required' => 'Pengolah wajib dipilih.',
            'offtaker_id.exists' => 'Pengolah tidak ditemukan.',
            'items.required' => 'Minimal satu item sampah harus diisi.',
            'items.min' => 'Minimal satu item sampah harus diisi.',
            'items.*.sampah_id.required' => 'Jenis sampah wajib dipilih.',
            'items.*.sampah_id.exists' => 'Jenis sampah tidak ditemukan.',
            'items.*.quantity.required' => 'Jumlah wajib diisi.',
            'items.*.quantity.numeric' => 'Jumlah harus berupa angka.',
            'items.*.quantity.min' => 'Jumlah minimal 0.01.',
            'metode_pengolahan.required' => 'Metode pengolahan wajib diisi.',
            'metode_pengolahan.max' => 'Metode pengolahan maksimal 255 karakter.',
            'tanggal_transaksi.required' => 'Tanggal transaksi wajib diisi.',
            'tanggal_transaksi.date' => 'Format tanggal tidak valid.',
            'notes.max' => 'Catatan maksimal 500 karakter.',
        ];
    }

    /**
     * Get custom attribute names.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'bank_sampah_id' => 'bank sampah',
            'offtaker_id' => 'pengolah',
            'items.*.sampah_id' => 'jenis sampah',
            'items.*.quantity' => 'jumlah',
            'metode_pengolahan' => 'metode pengolahan',
            'hasil_pengolahan' => 'hasil pengolahan',
            'tanggal_transaksi' => 'tanggal transaksi',
        ];
    }
}
