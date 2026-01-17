<?php

declare(strict_types=1);

namespace App\Http\Requests\WasteTransaction;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Form request for creating a new waste sale transaction.
 *
 * Validates sale data:
 * - bank_sampah_id: Required valid bank sampah
 * - offtaker_id: Required valid offtaker
 * - items: Required array of sale items
 * - tanggal_transaksi: Required transaction date
 * - struk: Optional receipt image
 * - notes: Optional notes
 */
class StoreSaleRequest extends FormRequest
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
            'items.*.harga_jual' => ['required', 'numeric', 'min:0'],
            'tanggal_transaksi' => ['required', 'date'],
            'struk' => ['nullable', 'image', 'max:2048'],
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
            'offtaker_id.required' => 'Offtaker/pembeli wajib dipilih.',
            'offtaker_id.exists' => 'Offtaker tidak ditemukan.',
            'items.required' => 'Minimal satu item sampah harus diisi.',
            'items.min' => 'Minimal satu item sampah harus diisi.',
            'items.*.sampah_id.required' => 'Jenis sampah wajib dipilih.',
            'items.*.sampah_id.exists' => 'Jenis sampah tidak ditemukan.',
            'items.*.quantity.required' => 'Jumlah wajib diisi.',
            'items.*.quantity.numeric' => 'Jumlah harus berupa angka.',
            'items.*.quantity.min' => 'Jumlah minimal 0.01.',
            'items.*.harga_jual.required' => 'Harga jual wajib diisi.',
            'items.*.harga_jual.numeric' => 'Harga jual harus berupa angka.',
            'items.*.harga_jual.min' => 'Harga jual tidak boleh negatif.',
            'tanggal_transaksi.required' => 'Tanggal transaksi wajib diisi.',
            'tanggal_transaksi.date' => 'Format tanggal tidak valid.',
            'struk.image' => 'File struk harus berupa gambar.',
            'struk.max' => 'Ukuran struk maksimal 2MB.',
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
            'offtaker_id' => 'offtaker',
            'items.*.sampah_id' => 'jenis sampah',
            'items.*.quantity' => 'jumlah',
            'items.*.harga_jual' => 'harga jual',
            'tanggal_transaksi' => 'tanggal transaksi',
        ];
    }
}
