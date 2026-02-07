<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Admin\StoreSkBankSampahRequest;
use App\Models\BankSampah;
use App\Models\SkBankSampah;
use App\Services\SkBankSampahService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Controller for SK Bank Sampah operations.
 *
 * Handles SK creation, preview, and PDF download.
 */
class SkBankSampahController extends Controller
{
    public function __construct(
        private readonly SkBankSampahService $skService
    ) {}

    /**
     * Generate preview HTML for SK Bank Sampah.
     */
    public function preview(Request $request): Response
    {
        $bankSampahId = $request->input('bank_sampah_id');
        $bankSampah = BankSampah::findOrFail($bankSampahId);

        $html = $this->skService->generatePreviewHtml($bankSampah, $request->all());

        return response($html, 200, ['Content-Type' => 'text/html']);
    }

    /**
     * Store or update SK Bank Sampah.
     */
    public function store(StoreSkBankSampahRequest $request, BankSampah $bankSampah): JsonResponse
    {
        $sk = $this->skService->createOrUpdateSk(
            $bankSampah,
            $request->getSkData(),
            $request->file('pengurus_image'),
            $request->file('struktur_organisasi')
        );

        return response()->json([
            'success' => true,
            'message' => 'SK Bank Sampah berhasil disimpan.',
            'data' => [
                'sk_id' => $sk->id,
                'download_url' => route('sk-bank-sampah.download', $sk),
            ],
        ]);
    }

    /**
     * Get existing SK data for a Bank Sampah.
     */
    public function show(BankSampah $bankSampah): JsonResponse
    {
        $data = $this->skService->getSkData($bankSampah);

        if (! $data['sk']) {
            return response()->json([
                'success' => true,
                'data' => null,
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'sk' => $data['sk'],
                'pengurus_image_url' => $data['pengurus_image_url'],
                'struktur_organisasi_url' => $data['struktur_organisasi_url'],
            ],
        ]);
    }

    /**
     * Download SK Bank Sampah as PDF.
     */
    public function download(SkBankSampah $sk): Response
    {
        return $this->skService->downloadPdf($sk);
    }
}
