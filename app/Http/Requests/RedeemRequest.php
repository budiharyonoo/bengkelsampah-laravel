<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\RedeemItem;
use App\Models\UserRedeem;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

/**
 * Form request for redeeming an item with points.
 *
 * Validates:
 * - redeem_item_id: Required, exists, and is active
 * - User has sufficient points
 * - No duplicate waiting redemptions for the same item
 */
class RedeemRequest extends FormRequest
{
    /**
     * Cached redeem item to avoid duplicate queries.
     */
    private ?RedeemItem $cachedRedeemItem = null;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'redeem_item_id' => ['required', 'integer', 'exists:redeem_items,id'],
            'info_json' => ['required', 'array'],
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
            'redeem_item_id.required' => 'Item yang akan ditukar wajib dipilih.',
            'redeem_item_id.integer' => 'ID item tidak valid.',
            'redeem_item_id.exists' => 'Item yang dipilih tidak ditemukan.',
            'info_json.required' => 'Informasi pengiriman wajib diisi.',
            'info_json.array' => 'Format informasi pengiriman tidak valid.',
        ];
    }

    /**
     * Configure the validator instance with custom validation.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            $user = auth()->user();
            $redeemItemId = $this->input('redeem_item_id');

            if (! $user || ! $redeemItemId) {
                return;
            }

            // Check if item is active and cache it
            $this->cachedRedeemItem = RedeemItem::find($redeemItemId);
            if ($this->cachedRedeemItem && ! $this->cachedRedeemItem->is_active) {
                $validator->errors()->add(
                    'redeem_item_id',
                    'Item ini sedang tidak tersedia untuk penukaran.'
                );

                return;
            }

            // Check if user has sufficient points
            if ($this->cachedRedeemItem && $user->poin < $this->cachedRedeemItem->points_required) {
                $validator->errors()->add(
                    'insufficient_points',
                    sprintf(
                        'Poin Anda tidak cukup. Diperlukan %s poin, Anda memiliki %s poin.',
                        number_format($this->cachedRedeemItem->points_required, 0, ',', '.'),
                        number_format((int) $user->poin, 0, ',', '.')
                    )
                );

                return;
            }

            // Check for duplicate waiting redemption
            $hasWaitingRedeem = UserRedeem::where('user_id', $user->id)
                ->where('redeem_item_id', $redeemItemId)
                ->where('status', 'waiting')
                ->exists();

            if ($hasWaitingRedeem) {
                $validator->errors()
                    ->add(
                        'duplicate_redeem',
                        'Anda sudah memiliki permintaan penukaran untuk item ini yang sedang diproses.'
                    );
            }
        });
    }

    /**
     * Get the redeem item from validated data (cached to avoid duplicate query).
     */
    public function getRedeemItem(): ?RedeemItem
    {
        return $this->cachedRedeemItem;
    }

    /**
     * Get delivery info from validated data.
     *
     * @return array<string, mixed>|null
     */
    public function getInfoJson(): ?array
    {
        return $this->validated('info_json');
    }
}
