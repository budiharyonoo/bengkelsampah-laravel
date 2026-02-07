<?php

declare(strict_types=1);

namespace App\Http\Requests\Offtaker;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Form request for updating an offtaker.
 *
 * Validates offtaker update data:
 * - nama: Required name
 * - tipe: Required type (buyer, processor, both)
 * - nama_pic: Required PIC name
 * - kontak_pic: Required PIC contact
 * - alamat: Optional address
 * - is_active: Optional active status
 */
class UpdateOfftakerRequest extends FormRequest
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
            'nama' => ['required', 'string', 'max:255'],
            'tipe' => ['required', 'in:buyer,processor,both'],
            'nama_pic' => ['required', 'string', 'max:255'],
            'kontak_pic' => ['required', 'string', 'max:50'],
            'alamat' => ['nullable', 'string'],
            'is_active' => ['sometimes', 'boolean'],
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
            'nama.required' => 'Nama offtaker wajib diisi.',
            'nama.max' => 'Nama offtaker maksimal 255 karakter.',
            'tipe.required' => 'Tipe offtaker wajib dipilih.',
            'tipe.in' => 'Tipe offtaker harus salah satu dari: pembeli, pengolah, atau keduanya.',
            'nama_pic.required' => 'Nama PIC wajib diisi.',
            'nama_pic.max' => 'Nama PIC maksimal 255 karakter.',
            'kontak_pic.required' => 'Kontak PIC wajib diisi.',
            'kontak_pic.max' => 'Kontak PIC maksimal 50 karakter.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('is_active')) {
            $this->merge([
                'is_active' => $this->boolean('is_active'),
            ]);
        }
    }
}
