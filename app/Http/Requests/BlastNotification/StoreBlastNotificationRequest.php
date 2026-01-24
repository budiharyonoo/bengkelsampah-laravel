<?php

declare(strict_types=1);

namespace App\Http\Requests\BlastNotification;

use Illuminate\Foundation\Http\FormRequest;

class StoreBlastNotificationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization is handled by middleware
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string', 'max:1000'],
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
            'title.required' => 'Judul notifikasi harus diisi',
            'title.max' => 'Judul notifikasi maksimal 255 karakter',
            'body.required' => 'Isi pesan harus diisi',
            'body.max' => 'Isi pesan maksimal 1000 karakter',
        ];
    }
}
