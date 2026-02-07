<?php

namespace App\Http\Controllers;

use App\Models\DeleteAccountRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DeleteAccountController extends Controller
{
    /**
     * Show the delete account form
     */
    public function show()
    {
        return view('delete-account');
    }

    /**
     * Handle delete account request submission
     */
    public function submit(Request $request)
    {
        // Validate the request
        $request->validate([
            'email' => 'required|email',
            'phone' => 'required|string',
            'fullName' => 'required|string|max:255',
            'reason' => 'required|string',
            'explanation' => 'nullable|string|max:1000',
            'confirmation' => 'required|in:yes,no',
        ]);

        // Check if user confirmed deletion
        if ($request->confirmation !== 'yes') {
            return back()->withErrors(['confirmation' => 'Anda harus mengkonfirmasi penghapusan akun.']);
        }

        try {
            // Save request to database
            $deleteRequest = DeleteAccountRequest::create([
                'email' => $request->email,
                'phone' => $request->phone,
                'full_name' => $request->fullName,
                'reason' => $request->reason,
                'explanation' => $request->explanation,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'status' => 'pending',
            ]);

            // Log the delete account request
            Log::info('Delete account request submitted and saved to database', [
                'id' => $deleteRequest->id,
                'email' => $request->email,
                'phone' => $request->phone,
                'full_name' => $request->fullName,
                'reason' => $request->reason,
                'explanation' => $request->explanation,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'timestamp' => now(),
            ]);

            return back()->with('success', 'Request penghapusan akun telah berhasil dikirim. Tim kami akan menghubungi Anda dalam waktu 24-48 jam untuk verifikasi.');

        } catch (\Exception $e) {
            Log::error('Error processing delete account request', [
                'error' => $e->getMessage(),
                'email' => $request->email,
            ]);

            return back()->withErrors(['general' => 'Terjadi kesalahan saat memproses request. Silakan coba lagi atau hubungi tim dukungan kami.']);
        }
    }
}
