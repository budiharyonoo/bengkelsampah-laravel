<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    public function sendSetoranNotification($phone, $setoranData): bool
    {
        return $this->sendMessage($phone, $setoranData);
    }

    public function sendMessage($phone, $setoranData): bool
    {
        try {
            $url = 'https://api.tcastsms.net/api/whatsapp/v1/Send';

            $apiKey = env('TCAST_API_KEY');
            $clientId = env('TCAST_CLIENT_ID');
            $senderNumber = env('TCAST_SENDER_NUMBER');
            $metaTemplateId = env('TCAST_META_TEMPLATE_ID');

            $phone = preg_replace('/\D/', '', $phone);

            if (preg_match('/^08/', $phone)) {
                $phone = '62'.substr($phone, 1);
            } elseif (preg_match('/^8/', $phone)) {
                $phone = '62'.$phone;
            } elseif (preg_match('/^620/', $phone)) {
                $phone = '62'.substr($phone, 3);
            }

            $userName = $setoranData['user_name'] ?? 'User';
            $setoranId = $setoranData['id'] ?? 'N/A';
            $tipeSetor = ucfirst($setoranData['tipe_setor'] ?? 'N/A');
            $estimasiTotal = number_format($setoranData['estimasi_total'] ?? 0);
            $bankName = $setoranData['bank_sampah_name'] ?? 'N/A';
            $addressPhone = $setoranData['address_phone'] ?? 'N/A';
            $address = $setoranData['address_full_address'] ?? 'N/A';
            $tanggalPenjemputan = $setoranData['tanggal_penjemputan'] ?? 'N/A';
            $waktuPenjemputan = $setoranData['waktu_penjemputan'] ?? 'N/A';

            $tanggalFormatted = $tanggalPenjemputan
                ? date('d/m/Y', strtotime($tanggalPenjemputan))
                : 'N/A';

            $payload = [
                'SenderNumber' => $senderNumber,
                'MetaTemplateId' => $metaTemplateId,
                'BulkPayload' => [
                    'RecieverNumber' => $phone,
                    'HeaderVariable' => 'NOTIFIKASI SETORAN BARU',
                    'BodyVariables' => [
                        "#{$setoranId}",
                        "{$userName}",
                        "{$addressPhone}",
                        "{$tipeSetor}",
                        "Rp {$estimasiTotal}",
                        "{$bankName}",
                        "{$address}",
                        "{$tanggalFormatted}",
                        "{$waktuPenjemputan}",
                    ],
                ],
            ];

            $response = Http::withBasicAuth($apiKey, $clientId)
                ->withHeaders([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ])
                ->post($url, $payload)
                ->throw();

            return $response->successful();
        } catch (\Exception $e) {
            Log::error("Error sending WhatsApp notification to {$phone}: ".$e->getMessage(), [
                'payload' => $setoranData,
                'exception' => $e,
            ]);

            return false;
        }
    }
}
