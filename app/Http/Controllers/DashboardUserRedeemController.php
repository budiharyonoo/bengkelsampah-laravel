<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\UserRedeem\UpdateUserRedeemRequest;
use App\Models\Point;
use App\Models\User;
use App\Models\UserRedeem;
use App\Services\FirebaseService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class DashboardUserRedeemController extends Controller
{
    public function __construct(private FirebaseService $firebaseService) {}

    /**
     * Display a listing of user redeem requests.
     */
    public function index(Request $request): View
    {
        $query = UserRedeem::query()->with(['user', 'redeemItem']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->filled('tgl_awal') && $request->filled('tgl_akhir')) {
            $query->whereBetween('created_at', [
                $request->tgl_awal . ' 00:00:00',
                $request->tgl_akhir . ' 23:59:59',
            ]);
        }

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('user_name', 'like', "%{$search}%")
                    ->orWhere('user_identifier', 'like', "%{$search}%")
                    ->orWhere('redeem_item_name', 'like', "%{$search}%");
            });
        }

        // Sort by created_at descending by default
        $query->orderBy('created_at', 'desc');

        $redeems = $query->paginate(10);

        return view('dashboard-user-redeem', compact('redeems'));
    }

    /**
     * Show the form for editing the specified user redeem request.
     */
    public function edit(int $id): View
    {
        $redeem = UserRedeem::with(['user', 'redeemItem', 'statusChangedBy'])
            ->findOrFail($id);

        return view('dashboard-user-redeem-edit', compact('redeem'));
    }

    /**
     * Update the specified user redeem request (approve/reject).
     */
    public function update(UpdateUserRedeemRequest $request, int $id): RedirectResponse
    {
        $redeem = UserRedeem::with('user')->findOrFail($id);

        // Check if already processed
        if ($redeem->status !== 'waiting') {
            return redirect()
                ->back()
                ->with('error', 'Redeem request sudah diproses sebelumnya.');
        }

        $admin = Auth::guard('admin')->user();

        try {
            DB::beginTransaction();

            $action = $request->input('action');

            $user = $redeem->user;

            if ($action === 'approve') {
                // Check user's current point balance
                if ($user->poin < $redeem->point_used) {
                    DB::rollBack();

                    return redirect()
                        ->back()
                        ->with('error', 'Poin user tidak mencukupi. Poin saat ini: ' . number_format($user->poin, 2) . ', Poin diperlukan: ' . number_format($redeem->point_used, 2));
                }

                // Deduct points from user
                $user->poin -= $redeem->point_used;
                $user->save();

                // Update redeem status
                $redeem->status = 'approved';
            } else {
                // Reject
                $redeem->status = 'rejected';
            }

            // Handle reward proof upload
            if ($request->hasFile('reward_proof')) {
                // Delete old file if exists
                if ($redeem->reward_proof_url) {
                    Storage::disk('public')->delete($redeem->reward_proof_url);
                }

                $file = $request->file('reward_proof');
                $filename = 'redeem_' . $redeem->id . '_' . time() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('redeem', $filename, 'public');
                $redeem->reward_proof_url = 'uploads/' . $path;
            }

            // Update info_json if provided
            if ($request->filled('info_json')) {
                $redeem->info_json = $request->input('info_json');
            }

            // Update notes
            $redeem->notes = $request->input('notes');

            // Update status change metadata
            $redeem->status_changed_by = $admin->id;
            $redeem->status_changed_by_name = $admin->name;
            $redeem->status_changed_at = now();

            $redeem->save();

            DB::commit();

            // Send FCM notification to user
            if ($user->fcm_token) {
                $notificationTitle = $action === 'approve'
                    ? 'Redeem Disetujui'
                    : 'Redeem Ditolak';

                $notificationBody = $action === 'approve'
                    ? "Permintaan redeem {$redeem->redeem_item_name} Anda telah disetujui. Poin anda telah berkurang sebesar " . number_format($redeem->point_used, 0, ',', '.')
                    : "Permintaan redeem {$redeem->redeem_item_name} Anda telah ditolak.";

                $notificationData = [
                    'type' => 'redeem_status',
                    'redeem_id' => (string) $redeem->id,
                    'status' => $redeem->status,
                    'item_name' => $redeem->redeem_item_name,
                    'points_used' => (string) $redeem->point_used,
                ];

                $this->firebaseService->sendToUser(
                    $user->fcm_token,
                    $notificationTitle,
                    $notificationBody,
                    $notificationData
                );
            }

            $message = $action === 'approve'
                ? 'Redeem request berhasil disetujui dan poin telah dideduct.'
                : 'Redeem request berhasil ditolak.';

            return redirect()
                ->route('dashboard.user-redeem.index')
                ->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Export user redeem requests to Excel.
     */
    public function exportExcel(Request $request)
    {
        try {
            $query = UserRedeem::query()->with(['user', 'redeemItem', 'statusChangedBy']);

            // Apply same filters as index
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('tgl_awal') && $request->filled('tgl_akhir')) {
                $query->whereBetween('created_at', [
                    $request->tgl_awal . ' 00:00:00',
                    $request->tgl_akhir . ' 23:59:59',
                ]);
            }

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('user_name', 'like', "%{$search}%")
                        ->orWhere('user_identifier', 'like', "%{$search}%")
                        ->orWhere('redeem_item_name', 'like', "%{$search}%");
                });
            }

            $redeems = $query->orderBy('created_at', 'desc')->get();

            return $this->generateExcelFile($redeems);
        } catch (\Exception $e) {
            Log::error('Error in DashboardUserRedeemController@exportExcel: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal export Excel: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Export user redeem requests to CSV.
     */
    public function exportCsv(Request $request)
    {
        try {
            $query = UserRedeem::query()->with(['user', 'redeemItem', 'statusChangedBy']);

            // Apply same filters as index
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('tgl_awal') && $request->filled('tgl_akhir')) {
                $query->whereBetween('created_at', [
                    $request->tgl_awal . ' 00:00:00',
                    $request->tgl_akhir . ' 23:59:59',
                ]);
            }

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('user_name', 'like', "%{$search}%")
                        ->orWhere('user_identifier', 'like', "%{$search}%")
                        ->orWhere('redeem_item_name', 'like', "%{$search}%");
                });
            }

            $redeems = $query->orderBy('created_at', 'desc')->get();

            return $this->generateCsvFile($redeems);
        } catch (\Exception $e) {
            Log::error('Error in DashboardUserRedeemController@exportCsv: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal export CSV: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Export user redeem requests to PDF.
     */
    public function exportPdf(Request $request)
    {
        try {
            $query = UserRedeem::query()->with(['user', 'redeemItem', 'statusChangedBy']);

            // Apply same filters as index
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('tgl_awal') && $request->filled('tgl_akhir')) {
                $query->whereBetween('created_at', [
                    $request->tgl_awal . ' 00:00:00',
                    $request->tgl_akhir . ' 23:59:59',
                ]);
            }

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('user_name', 'like', "%{$search}%")
                        ->orWhere('user_identifier', 'like', "%{$search}%")
                        ->orWhere('redeem_item_name', 'like', "%{$search}%");
                });
            }

            $redeems = $query->orderBy('created_at', 'desc')->get();

            return $this->generatePdfFile($redeems);
        } catch (\Exception $e) {
            Log::error('Error in DashboardUserRedeemController@exportPdf: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal export PDF: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Generate Excel file from redeem data.
     */
    private function generateExcelFile($redeems)
    {
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();

        // Set document properties
        $spreadsheet->getProperties()
            ->setCreator('Bengkel Sampah Admin')
            ->setTitle('Laporan User Redeem Requests')
            ->setSubject('Laporan User Redeem Requests')
            ->setDescription('Laporan data user redeem requests');

        // Header style
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '39746E'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ];

        // Title
        $sheet->setCellValue('A1', 'LAPORAN USER REDEEM REQUESTS');
        $sheet->mergeCells('A1:H1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Subtitle
        $sheet->setCellValue('A2', 'Tanggal Export: ' . now()->format('d F Y H:i:s'));
        $sheet->mergeCells('A2:H2');
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Headers
        $headers = [
            'No',
            'Tanggal Request',
            'Nama User',
            'Email User',
            'Nama Item',
            'Point Digunakan',
            'Status',
            'Diproses Oleh',
        ];

        $col = 'A';
        $row = 4;
        foreach ($headers as $header) {
            $sheet->setCellValue($col . $row, $header);
            $sheet->getStyle($col . $row)->applyFromArray($headerStyle);
            $col++;
        }

        // Data
        $row = 5;
        foreach ($redeems as $index => $redeem) {
            $statusLabel = match ($redeem->status) {
                'waiting' => 'Pending',
                'approved' => 'Approved',
                'rejected' => 'Rejected',
                'cancelled' => 'Cancelled',
                default => ucfirst($redeem->status),
            };

            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $redeem->created_at->format('d/m/Y H:i'));
            $sheet->setCellValue('C' . $row, $redeem->user_name);
            $sheet->setCellValue('D' . $row, $redeem->user->identifier ?? '-');
            $sheet->setCellValue('E' . $row, $redeem->redeemItem->name ?? $redeem->redeem_item_name);
            $sheet->setCellValue('F' . $row, number_format($redeem->point_used, 2));
            $sheet->setCellValue('G' . $row, $statusLabel);
            $sheet->setCellValue('H' . $row, $redeem->status_changed_by_name ?? '-');

            // Center align
            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('F' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('G' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $row++;
        }

        // Auto-size columns
        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Borders
        $dataRange = 'A4:H' . ($row - 1);
        $sheet->getStyle($dataRange)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        // Create file
        $writer = new Xlsx($spreadsheet);
        $filename = 'user_redeem_requests_' . now()->format('Y-m-d_H-i-s') . '.xlsx';
        $tempFile = storage_path('app/temp/' . $filename);

        if (! file_exists(dirname($tempFile))) {
            mkdir(dirname($tempFile), 0755, true);
        }

        $writer->save($tempFile);

        return response()->download($tempFile, $filename)->deleteFileAfterSend();
    }

    /**
     * Generate CSV file from redeem data.
     */
    private function generateCsvFile($redeems)
    {
        $filename = 'user_redeem_requests_' . now()->format('Y-m-d_H-i-s') . '.csv';
        $tempFile = storage_path('app/temp/' . $filename);

        if (! file_exists(dirname($tempFile))) {
            mkdir(dirname($tempFile), 0755, true);
        }

        $output = fopen($tempFile, 'w');

        // UTF-8 BOM for Excel compatibility
        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

        // Headers
        fputcsv($output, [
            'No',
            'Tanggal Request',
            'Nama User',
            'Email User',
            'Nama Item',
            'Point Digunakan',
            'Status',
            'Diproses Oleh',
        ]);

        // Data
        foreach ($redeems as $index => $redeem) {
            $statusLabel = match ($redeem->status) {
                'waiting' => 'Pending',
                'approved' => 'Approved',
                'rejected' => 'Rejected',
                'cancelled' => 'Cancelled',
                default => ucfirst($redeem->status),
            };

            fputcsv($output, [
                $index + 1,
                $redeem->created_at->format('d/m/Y H:i'),
                $redeem->user_name,
                $redeem->user->identifier ?? '-',
                $redeem->redeemItem->name ?? $redeem->redeem_item_name,
                number_format($redeem->point_used, 2),
                $statusLabel,
                $redeem->status_changed_by_name ?? '-',
            ]);
        }

        fclose($output);

        return response()->download($tempFile, $filename)->deleteFileAfterSend();
    }

    /**
     * Generate PDF file from redeem data.
     */
    private function generatePdfFile($redeems)
    {
        $pdf = app('dompdf.wrapper');
        $pdf->loadView('exports.user-redeem', compact('redeems'));
        $pdf->setPaper('a4', 'landscape');

        $filename = 'user_redeem_requests_' . now()->format('Y-m-d_H-i-s') . '.pdf';

        return $pdf->download($filename);
    }
}
