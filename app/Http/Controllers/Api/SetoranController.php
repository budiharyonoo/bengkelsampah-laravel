<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\BankSampah;
use App\Models\Point;
use App\Models\Setoran;
use App\Models\User;
use App\Services\NotificationService;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\PersonalAccessToken;

class SetoranController extends Controller
{
    protected $notificationService;

    protected $whatsappService;

    public function __construct(NotificationService $notificationService, WhatsAppService $whatsappService)
    {
        $this->notificationService = $notificationService;
        $this->whatsappService = $whatsappService;
    }

    /**
     * Create a new deposit
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // Get authenticated user
        $user = $this->getAuthenticatedUser($request);
        if (! $user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tidak terautentikasi',
            ], 401);
        }

        // Validate request
        $validator = Validator::make($request->all(), [
            'bank_sampah_id' => 'required|integer|exists:bank_sampah,id',
            'address_id' => 'required|integer|exists:addresses,id',
            'tipe_setor' => 'required|in:jual,sedekah,tabung',
            'items' => 'required',
            'estimasi_total' => 'required|numeric|min:0',
            'tanggal_penjemputan' => 'nullable|date|after_or_equal:today',
            'waktu_penjemputan' => 'nullable|date_format:H:i',
            'foto_sampah' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'tipe_layanan' => 'required|in:jemput,tempat,keduanya',
        ]);

        $message = '';
        $validationErrors = $validator->errors()->messages();
        foreach ($validationErrors as $key => $value) {
            $message = $value[0];
        }

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $message,
                'errors' => $validator->errors(),
            ], 400);
        }

        // Decode items JSON
        $items = json_decode($request->items, true);
        if (! is_array($items)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Format items tidak valid',
            ], 400);
        }

        try {
            // Get bank sampah data
            $bankSampah = BankSampah::findOrFail($request->bank_sampah_id);

            // Get address data
            $address = Address::where('id', $request->address_id)
                ->where('user_id', $user->id)
                ->firstOrFail();

            // Handle photo upload
            $fotoUrl = null;
            if ($request->hasFile('foto_sampah')) {
                $file = $request->file('foto_sampah');
                $filename = time().'_'.Str::random(10).'_setoran_'.uniqid().'.'.$file->getClientOriginalExtension();

                // Create directory if it doesn't exist
                $uploadPath = base_path('../uploads/setoran');
                if (! file_exists($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }

                // Store the file using public disk
                $path = $file->storeAs('setoran', $filename, 'public');

                if (! $path) {
                    throw new \Exception('Failed to upload file');
                }

                // Get the full URL for the image
                $fotoUrl = env('APP_URL').'/uploads/'.$path;
            }

            // Create setoran
            $setoran = Setoran::create([
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_identifier' => $user->identifier,
                'bank_sampah_id' => $bankSampah->id,
                'bank_sampah_name' => $bankSampah->nama_bank_sampah,
                'bank_sampah_code' => $bankSampah->kode_bank_sampah,
                'bank_sampah_address' => $bankSampah->alamat_bank_sampah,
                'bank_sampah_phone' => $bankSampah->kontak_penanggung_jawab,
                'address_id' => $address->id,
                'address_name' => $address->nama,
                'address_phone' => $address->nomor_handphone,
                'address_full_address' => $address->label_alamat.', '.$address->detail_lain.', '.$address->kecamatan.', '.$address->kota_kabupaten.', '.$address->provinsi.', '.$address->kode_pos,
                'address_is_default' => $address->is_default,
                'tipe_setor' => $request->tipe_setor,
                'status' => Setoran::STATUS_DIKONFIRMASI,
                'items_json' => $request->items,
                'estimasi_total' => $request->estimasi_total,
                'tanggal_penjemputan' => $request->tanggal_penjemputan,
                'waktu_penjemputan' => $request->waktu_penjemputan,
                'foto_sampah' => $fotoUrl,
                'tipe_layanan' => $request->tipe_layanan,
            ]);

            // Send WhatsApp notification to bank sampah penanggung jawab (async)
            $this->sendWhatsAppNotificationAsync($setoran);

            return response()->json([
                'status' => 'success',
                'message' => 'Setoran berhasil dibuat',
                'data' => [
                    'id' => $setoran->id,
                    'status' => $setoran->status,
                    'tipe_setor' => $setoran->tipe_setor,
                    'estimasi_total' => $setoran->estimasi_total,
                    'created_at' => $setoran->created_at,
                ],
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat membuat setoran: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get user's deposit history
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        // Get authenticated user
        $user = $this->getAuthenticatedUser($request);
        if (! $user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tidak terautentikasi',
            ], 401);
        }

        try {
            $query = Setoran::where('user_id', $user->id)
                ->orderBy('created_at', 'desc');

            // Filter by status if provided
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            // Filter by tipe_setor if provided
            if ($request->has('tipe_setor')) {
                $query->where('tipe_setor', $request->tipe_setor);
            }

            $setorans = $query->paginate(10);

            return response()->json([
                'status' => 'success',
                'data' => $setorans,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat mengambil data setoran: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get specific deposit detail
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $id)
    {
        // Get authenticated user
        $user = $this->getAuthenticatedUser($request);
        if (! $user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tidak terautentikasi',
            ], 401);
        }

        try {
            $setoran = Setoran::where('id', $id)
                ->where('user_id', $user->id)
                ->first();

            if (! $setoran) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Setoran tidak ditemukan',
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'data' => $setoran,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat mengambil detail setoran: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Cancel a setoran
     */
    public function cancelSetoran(Request $request, $id)
    {
        try {
            $user = auth()->user();
            if (! $user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized',
                ], 401);
            }

            $setoran = Setoran::where('id', $id)
                ->where('user_id', $user->id)
                ->first();

            if (! $setoran) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Setoran tidak ditemukan',
                ], 404);
            }

            // Only allow cancellation for dikonfirmasi status
            if ($setoran->status !== Setoran::STATUS_DIKONFIRMASI) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Setoran tidak dapat dibatalkan. Hanya setoran dengan status dikonfirmasi yang dapat dibatalkan.',
                ], 400);
            }

            $request->validate([
                'alasan_pembatalan' => 'required|string|max:500',
            ]);

            $setoran->update([
                'status' => Setoran::STATUS_BATAL,
                'alasan_pembatalan' => $request->alasan_pembatalan,
                'updated_at' => now(),
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Setoran berhasil dibatalkan',
                'data' => [
                    'id' => $setoran->id,
                    'status' => $setoran->status,
                    'alasan_pembatalan' => $setoran->alasan_pembatalan,
                    'updated_at' => $setoran->updated_at,
                ],
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat membatalkan setoran: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update setoran status
     */
    public function updateStatus(Request $request, $id)
    {
        try {
            $setoran = Setoran::findOrFail($id);

            $request->validate([
                'status' => 'required|in:dikonfirmasi,diproses,dijemput,selesai,batal',
                'alasan_pembatalan' => 'nullable|string|max:500',
                'petugas_nama' => 'nullable|string|max:255',
                'petugas_contact' => 'nullable|string|max:255',
                'items_json' => 'nullable|string',
                'aktual_total' => 'nullable|numeric|min:0',
            ]);

            $oldStatus = $setoran->status;
            $newStatus = $request->status;

            // Update setoran data
            $updateData = [
                'status' => $newStatus,
                'updated_at' => now(),
            ];

            // Add conditional fields
            if ($newStatus === 'batal' && $request->alasan_pembatalan) {
                $updateData['alasan_pembatalan'] = $request->alasan_pembatalan;
            }

            if ($newStatus === 'dijemput') {
                if ($request->petugas_nama) {
                    $updateData['petugas_nama'] = $request->petugas_nama;
                }
                if ($request->petugas_contact) {
                    $updateData['petugas_contact'] = $request->petugas_contact;
                }
            }

            // Handle items and actual total for selesai status
            if ($newStatus === 'selesai') {
                if ($request->items_json) {
                    $updateData['items_json'] = $request->items_json;
                }
                if ($request->aktual_total) {
                    $updateData['aktual_total'] = $request->aktual_total;
                }

                // Calculate and add points + XP
                $this->addPointsAndXP($setoran, $request->aktual_total);
            }

            $setoran->update($updateData);

            return response()->json([
                'status' => 'success',
                'message' => 'Status setoran berhasil diperbarui',
                'data' => [
                    'id' => $setoran->id,
                    'status' => $setoran->status,
                    'old_status' => $oldStatus,
                    'updated_at' => $setoran->updated_at,
                ],
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat memperbarui status: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Add points and XP to user when setoran is completed
     */
    private function addPointsAndXP($setoran, $aktualTotal)
    {
        try {
            // Calculate points based on tipe_setor
            $points = 0;
            if ($setoran->tipe_setor === 'tabung') {
                // Points = total aktual (1 point per 1 rupiah) for tabung
                $points = $aktualTotal;
            }
            // For 'jual' and 'sedekah', points = 0

            // XP = total aktual / 1000 (regardless of tipe_setor)
            $xp = $aktualTotal / 1000;

            // Calculate total actual weight from items
            $items = json_decode($setoran->items_json, true);
            $totalWeight = 0;
            if (is_array($items)) {
                foreach ($items as $item) {
                    if (isset($item['aktual_berat']) && $item['status'] !== 'dihapus') {
                        $totalWeight += $item['aktual_berat'];
                    }
                }
            }

            // Create point record
            Point::create([
                'user_id' => $setoran->user_id,
                'user_name' => $setoran->user_name,
                'user_identifier' => $setoran->user_identifier,
                'type' => Point::TYPE_SETOR,
                'tanggal' => now()->toDateString(),
                'jumlah_point' => $points,
                'xp' => $xp,
                'setoran_id' => $setoran->id,
                'keterangan' => "Setoran sampah #{$setoran->id} - {$setoran->tipe_setor} - Total: Rp ".number_format($aktualTotal),
            ]);

            // Update user points and XP
            $user = User::find($setoran->user_id);
            if ($user) {
                $user->increment('poin', $points);
                $user->increment('xp', $xp);
                $user->increment('setor');
                $user->increment('sampah', $totalWeight);

                // Send notification to user about completed setoran
                $this->notificationService->sendSetoranCompletedNotification(
                    $user->id,
                    $setoran->id,
                    $points,
                    $xp
                );
            }

        } catch (\Exception $e) {
            \Log::error('Error adding points and XP: '.$e->getMessage());
            throw $e;
        }
    }

    /**
     * Send WhatsApp notification to bank sampah penanggung jawab asynchronously
     *
     * @param  Setoran  $setoran
     * @return void
     */
    private function sendWhatsAppNotificationAsync($setoran)
    {
        // Get bank sampah penanggung jawab phone number
        $bankSampah = BankSampah::find($setoran->bank_sampah_id);
        if (! $bankSampah || ! $bankSampah->kontak_penanggung_jawab) {
            \Log::warning("Bank sampah or contact not found for setoran #{$setoran->id}");

            return;
        }

        // Prepare setoran data for notification
        $setoranData = [
            'id' => $setoran->id,
            'user_name' => $setoran->user_name,
            'user_identifier' => $setoran->user_identifier,
            'tipe_setor' => $setoran->tipe_setor,
            'estimasi_total' => $setoran->estimasi_total,
            'bank_sampah_name' => $setoran->bank_sampah_name,
            'address_phone' => $setoran->address_phone,
            'address_full_address' => $setoran->address_full_address,
            'tanggal_penjemputan' => $setoran->tanggal_penjemputan,
            'waktu_penjemputan' => $setoran->waktu_penjemputan,
        ];

        // Send notification asynchronously (don't wait for response)
        try {
            $this->whatsappService->sendSetoranNotification(
                $bankSampah->kontak_penanggung_jawab,
                $setoranData
            );
        } catch (\Exception $e) {
            \Log::error("Failed to send WhatsApp notification for setoran #{$setoran->id}: ".$e->getMessage());
        }
    }

    /**
     * Get authenticated user from token
     */
    private function getAuthenticatedUser(Request $request)
    {
        $token = $request->bearerToken();
        if (! $token) {
            return null;
        }

        $accessToken = PersonalAccessToken::findToken($token);
        if (! $accessToken) {
            return null;
        }

        return User::find($accessToken->tokenable_id);
    }
}
