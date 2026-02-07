<?php

declare(strict_types=1);

namespace App\Http\Requests\UserRedeem;

use Illuminate\Foundation\Http\FormRequest;

/**
 * User Redeem Filter Request
 *
 * Validates filter parameters for user redeem requests datatable.
 */
class UserRedeemFilterRequest extends FormRequest
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
            'status' => ['nullable', 'string', 'in:waiting,approved,rejected'],
            'tgl_awal' => ['nullable', 'date', 'before_or_equal:tgl_akhir'],
            'tgl_akhir' => ['nullable', 'date', 'after_or_equal:tgl_awal'],
            'search' => ['nullable', 'string', 'max:255'],
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
            'status.in' => 'Status yang dipilih tidak valid.',
            'tgl_awal.before_or_equal' => 'Tanggal awal harus sebelum atau sama dengan tanggal akhir.',
            'tgl_akhir.after_or_equal' => 'Tanggal akhir harus setelah atau sama dengan tanggal awal.',
        ];
    }
}
