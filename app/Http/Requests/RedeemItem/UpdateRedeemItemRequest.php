<?php

declare(strict_types=1);

namespace App\Http\Requests\RedeemItem;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRedeemItemRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'points_required' => 'required|integer|min:1',
            'is_active' => 'required|boolean',
        ];
    }

    /**
     * Get custom error messages for validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Nama item harus diisi',
            'name.max' => 'Nama item maksimal 255 karakter',
            'points_required.required' => 'Point yang dibutuhkan harus diisi',
            'points_required.integer' => 'Point yang dibutuhkan harus berupa angka',
            'points_required.min' => 'Point yang dibutuhkan minimal 1',
            'is_active.required' => 'Status harus dipilih',
            'is_active.boolean' => 'Status tidak valid',
        ];
    }
}
