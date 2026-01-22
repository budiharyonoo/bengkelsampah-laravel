<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Helpers\ResponseHelper as R;
use App\Http\Controllers\Controller;
use App\Http\Requests\RedeemRequest;
use App\Models\RedeemItem;
use App\Models\User;
use App\Models\UserRedeem;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class RedeemItemController extends Controller
{
    /**
     * Display a listing of reward items.
     */
    public function index(): JsonResponse
    {
        $items = RedeemItem::query()
            ->active()
            ->select(['id', 'name', 'description', 'points_required', 'created_at'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'description' => $item->description,
                    'points_required' => $item->points_required,
                    'created_at' => $item->created_at->format('Y-m-d H:i:s'),
                ];
            });

        return R::success('Daftar item berhasil diambil', $items);
    }

    /**
     * Store a new redemption request.
     *
     * This method handles the redemption of reward items with points.
     * Uses database transactions to ensure atomicity and prevent race conditions.
     *
     * Business Flow:
     * 1. Validate request (points, item availability, no duplicate waiting)
     * 2. Start database transaction
     * 3. Lock user row (FOR UPDATE) to prevent concurrent modifications
     * 4. Deduct points from user
     * 5. Create redemption record with status 'waiting'
     * 6. Commit transaction
     *
     * @param  RedeemRequest  $request  Validated request with redeem_item_id and optional delivery_info
     */
    public function store(RedeemRequest $request): JsonResponse
    {
        try {
            $user = auth()->user();
            $redeemItem = $request->getRedeemItem();

            // Use database transaction for atomicity
            $userRedeem = DB::transaction(function () use ($user, $redeemItem, $request) {
                // Lock user row to prevent race conditions (concurrent redemptions)
                $user = User::where('id', $user->id)->lockForUpdate()->first();

                if (! $user) {
                    throw new Exception('User tidak ditemukan.');
                }

                // Double-check points within transaction (race condition safety)
                if ($user->poin < $redeemItem->points_required) {
                    throw new Exception(
                        sprintf(
                            'Poin tidak cukup. Diperlukan %s poin, tersedia %s poin.',
                            number_format($redeemItem->points_required, 0, ',', '.'),
                            number_format($user->poin, 0, ',', '.')
                        )
                    );
                }

                // Create redemption record
                $userRedeem = UserRedeem::create([
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'user_identifier' => $user->identifier,
                    'redeem_item_id' => $redeemItem->id,
                    'redeem_item_name' => $redeemItem->name,
                    'redeem_item_description' => $redeemItem->description,
                    'point_used' => $redeemItem->points_required,
                    'status' => 'waiting',
                    'info_json' => $request->getInfoJson(),
                ]);

                return $userRedeem;
            });

            return R::success(
                'Permintaan penukaran berhasil dibuat. Silakan tunggu konfirmasi admin.',
                [
                    'redeem_id' => $userRedeem->id,
                    'item_name' => $userRedeem->redeem_item_name,
                    'points_used' => (int) $userRedeem->point_used,
                    'status' => $userRedeem->status,
                    'created_at' => $userRedeem->created_at->format('Y-m-d H:i:s'),
                ],
                201
            );
        } catch (Exception $e) {
            return R::error($e->getMessage(), 400);
        }
    }

    /**
     * Display the authenticated user's redeem history.
     */
    public function history(): JsonResponse
    {
        $user = auth()->user();

        $redeems = UserRedeem::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($redeem) {
                return [
                    'redeem_id' => $redeem->id,
                    'redeem_item_id' => $redeem->redeem_item_id,
                    'redeem_item_name' => $redeem->redeem_item_name,
                    'redeem_item_description' => $redeem->redeem_item_description,
                    'point_used' => (int) $redeem->point_used,
                    'status' => $redeem->status,
                    'created_at' => $redeem->created_at->format('Y-m-d H:i:s'),
                    'info_json' => $redeem->info_json,
                    'reward_proof_url' => $redeem->reward_proof_url,
                ];
            });

        return R::success('Riwayat penukaran berhasil diambil', $redeems);
    }

    /**
     * Cancel a redeem request.
     *
     * Users can only cancel their own redeem requests that are in 'waiting' status.
     */
    public function cancel(int $id): JsonResponse
    {
        try {
            $user = auth()->user();

            $redeem = UserRedeem::find($id);

            // Check if redeem exists
            if (! $redeem) {
                return R::error('Permintaan penukaran tidak ditemukan.', 404);
            }

            // Check if redeem belongs to the authenticated user
            if ($redeem->user_id !== $user->id) {
                return R::error('Anda tidak memiliki akses untuk membatalkan permintaan penukaran ini.', 403);
            }

            // Check if redeem can be cancelled (only waiting status)
            if ($redeem->status !== 'waiting') {
                return R::error(
                    sprintf(
                        'Permintaan penukaran dengan status "%s" tidak dapat dibatalkan.',
                        $redeem->status
                    ),
                    400
                );
            }

            // Update redeem status to cancelled
            $redeem->update([
                'status' => 'cancelled',
                'status_changed_by' => $user->id,
                'status_changed_by_name' => $user->name,
                'status_changed_at' => now(),
            ]);

            return R::success('Permintaan penukaran berhasil dibatalkan.', [
                'redeem_id' => $redeem->id,
                'status' => $redeem->status,
                'cancelled_at' => $redeem->status_changed_at->format('Y-m-d H:i:s'),
            ]);
        } catch (Exception $e) {
            return R::error($e->getMessage() ?: 'Terjadi kesalahan saat membatalkan permintaan penukaran.', 500);
        }
    }
}
