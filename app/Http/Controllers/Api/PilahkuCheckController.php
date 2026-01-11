<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BankSampah;
use App\Models\Price;
use App\Models\Sampah;
use Illuminate\Http\Request;

class PilahkuCheckController extends Controller
{
    /**
     * Mengecek status bank sampah, sampah, tipe layanan, detail, dan harga.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * Request body:
     * {
     *   "items": [
     *     {
     *       "bank_sampah_id": 1,
     *       "sampah_id": 2,
     *       "tipe_layanan": "jemput",
     *       "detail_sampah": { ... },
     *       "harga": 2000
     *     }, ...
     *   ]
     * }
     */
    public function check(Request $request)
    {
        $data = $request->validate([
            'items' => 'required|array',
            'items.*.bank_sampah_id' => 'required|integer',
            'items.*.sampah_id' => 'required|integer',
            'items.*.tipe_layanan' => 'required|string',
            'items.*.detail_sampah' => 'required|array',
            'items.*.harga' => 'required|numeric',
        ]);

        $results = [];
        foreach ($data['items'] as $item) {
            $result = [
                'bank_sampah_id' => $item['bank_sampah_id'],
                'sampah_id' => $item['sampah_id'],
                'status' => 'ok',
                'messages' => [],
                'changes' => [],
            ];

            // 1. Cek bank sampah
            $bank = BankSampah::find($item['bank_sampah_id']);
            if (! $bank) {
                $result['status'] = 'bank_sampah_not_found';
                $result['messages'][] = 'Bank sampah sudah tidak tersedia.';
                $results[] = $result;

                continue;
            }

            // 2. Cek sampah
            $sampah = Sampah::find($item['sampah_id']);
            if (! $sampah) {
                $result['status'] = 'sampah_not_found';
                $result['messages'][] = 'Sampah sudah tidak tersedia.';
                $results[] = $result;

                continue;
            }

            // 3. Cek tipe layanan
            if ($bank->tipe_layanan !== $item['tipe_layanan']) {
                $result['status'] = 'tipe_layanan_changed';
                $result['messages'][] = 'Tipe layanan bank sampah telah berubah.';
                $result['changes']['tipe_layanan'] = $bank->tipe_layanan;
            }

            // 4. Cek detail sampah (misal: nama, satuan, deskripsi)
            $detailChanged = false;
            $detailDiff = [];
            foreach ([
                'nama', 'satuan', 'deskripsi',
            ] as $field) {
                if (isset($item['detail_sampah'][$field]) && $item['detail_sampah'][$field] !== $sampah->$field) {
                    $detailChanged = true;
                    $detailDiff[$field] = [
                        'old' => $item['detail_sampah'][$field],
                        'new' => $sampah->$field,
                    ];
                }
            }
            if ($detailChanged) {
                $result['status'] = 'detail_sampah_changed';
                $result['messages'][] = 'Detail sampah telah berubah.';
                $result['changes']['detail_sampah'] = $detailDiff;
            }

            // 5. Cek harga
            $price = Price::where('bank_sampah_id', $item['bank_sampah_id'])
                ->where('sampah_id', $item['sampah_id'])
                ->first();
            if (! $price || $price->harga != $item['harga']) {
                $result['status'] = 'harga_changed';
                $result['messages'][] = 'Harga sampah di cabang ini telah berubah.';
                $result['changes']['harga'] = $price ? $price->harga : null;
            }

            $results[] = $result;
        }

        return response()->json([
            'status' => 'success',
            'results' => $results,
        ]);
    }
}
