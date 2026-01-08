<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use App\Services\Dashboard\PeriodHelper;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Form request for dashboard filter parameters.
 *
 * Validates and sanitizes dashboard filter inputs:
 * - bank_sampah_id: Optional bank sampah filter
 * - periode: Period type (harian, mingguan, etc.)
 * - range_date: Custom date range string
 */
class DashboardFilterRequest extends FormRequest
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
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'bank_sampah_id' => ['nullable', 'integer', 'exists:bank_sampah,id'],
            'periode' => [
                'nullable',
                'string',
                Rule::in([
                    PeriodHelper::PERIOD_HARIAN,
                    PeriodHelper::PERIOD_MINGGUAN,
                    PeriodHelper::PERIOD_BULANAN,
                    PeriodHelper::PERIOD_ENAM_BULANAN,
                    PeriodHelper::PERIOD_TAHUNAN,
                    PeriodHelper::PERIOD_RANGE,
                ]),
            ],
            'range_date' => ['nullable', 'string', 'max:50'],
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
            'bank_sampah_id.exists' => 'Bank sampah tidak ditemukan.',
            'periode.in' => 'Periode tidak valid.',
        ];
    }

    /**
     * Get validated bank sampah ID.
     *
     * @return int|null
     */
    public function getBankSampahId(): ?int
    {
        $validated = $this->validated();
        $id = $validated['bank_sampah_id'] ?? null;

        return $id ? (int) $id : null;
    }

    /**
     * Get validated periode.
     *
     * @return string
     */
    public function getPeriode(): string
    {
        $validated = $this->validated();

        return $validated['periode'] ?? PeriodHelper::PERIOD_HARIAN;
    }

    /**
     * Get validated range date.
     *
     * @return string|null
     */
    public function getRangeDate(): ?string
    {
        $validated = $this->validated();

        return $validated['range_date'] ?? null;
    }
}
