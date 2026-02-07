<?php

declare(strict_types=1);

namespace App\Http\Requests\UserRedeem;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Update User Redeem Request
 *
 * Validates admin actions on user redeem requests (approve/reject).
 */
class UpdateUserRedeemRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user('admin') !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'action' => ['required', 'string', 'in:approve,reject'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'reward_proof' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'], // 5MB max
            'info_json' => ['nullable', 'array'],
            'info_json.*.label' => ['nullable', 'string', 'max:500'],
            'info_json.*.value' => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'action.required' => 'Aksi harus dipilih (approve atau reject).',
            'action.in' => 'Aksi yang dipilih tidak valid.',
            'reward_proof.file' => 'Bukti reward harus berupa file.',
            'reward_proof.mimes' => 'Bukti reward harus berformat PDF, JPG, JPEG, atau PNG.',
            'reward_proof.max' => 'Ukuran file bukti reward maksimal 5MB.',
            'notes.max' => 'Catatan maksimal 1000 karakter.',
        ];
    }
}
