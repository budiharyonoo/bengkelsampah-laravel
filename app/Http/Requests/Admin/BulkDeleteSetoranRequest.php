<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Form request for bulk deleting setoran records.
 *
 * Validates that:
 * - ids: Required array with at least one element
 * - Each id: Required integer that exists in the setorans table
 */
class BulkDeleteSetoranRequest extends FormRequest
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
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['required', 'integer', 'exists:setorans,id'],
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
            'ids.required' => 'Tidak ada data yang dipilih.',
            'ids.array' => 'Format data tidak valid.',
            'ids.min' => 'Minimal pilih satu data untuk dihapus.',
            'ids.*.required' => 'ID setoran wajib diisi.',
            'ids.*.integer' => 'ID setoran harus berupa angka.',
            'ids.*.exists' => 'Setoran tidak ditemukan.',
        ];
    }
}
