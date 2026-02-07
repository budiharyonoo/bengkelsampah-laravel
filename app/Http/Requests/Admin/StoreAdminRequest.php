<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Form request for creating a new admin.
 *
 * Validates admin creation data:
 * - name: Required string
 * - email: Required unique email
 * - password: Required minimum 6 characters
 * - id_bank_sampah: Required valid bank sampah ID
 */
class StoreAdminRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:admins,email'],
            'password' => ['required', 'string', 'min:6'],
            'id_bank_sampah' => ['required', 'exists:bank_sampah,id'],
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
            'name.required' => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 6 karakter.',
            'id_bank_sampah.required' => 'Bank sampah wajib dipilih.',
            'id_bank_sampah.exists' => 'Bank sampah tidak ditemukan.',
        ];
    }

    /**
     * Get validated data for creating admin.
     *
     * @return array{name: string, email: string, password: string, id_bank_sampah: int}
     */
    public function getAdminData(): array
    {
        return [
            'name' => $this->validated('name'),
            'email' => $this->validated('email'),
            'password' => $this->validated('password'),
            'id_bank_sampah' => (int) $this->validated('id_bank_sampah'),
        ];
    }
}
