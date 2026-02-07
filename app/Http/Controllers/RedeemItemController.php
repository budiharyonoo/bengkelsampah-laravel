<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\RedeemItem;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RedeemItemController extends Controller
{
    /**
     * Display a listing of redeem items.
     */
    public function index(Request $request): View|JsonResponse
    {
        $query = RedeemItem::query()->orderBy('created_at', 'desc');

        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Apply status filter
        if ($request->filled('status')) {
            $status = $request->input('status');
            if ($status === 'active') {
                $query->where('is_active', true);
            } elseif ($status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $redeemItems = $query->paginate(10);

        // Return JSON for AJAX requests
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'redeem_items' => $redeemItems,
            ]);
        }

        return view('dashboard-redeem-item', compact('redeemItems'));
    }

    /**
     * Show the form for creating a new redeem item.
     */
    public function create(): View
    {
        return view('dashboard-redeem-item-create');
    }

    /**
     * Store a newly created redeem item in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'points_required' => 'required|integer|min:1',
            'is_active' => 'required|boolean',
        ], [
            'name.required' => 'Nama item harus diisi',
            'name.max' => 'Nama item maksimal 255 karakter',
            'points_required.required' => 'Point yang dibutuhkan harus diisi',
            'points_required.integer' => 'Point yang dibutuhkan harus berupa angka',
            'points_required.min' => 'Point yang dibutuhkan minimal 1',
            'is_active.required' => 'Status harus dipilih',
            'is_active.boolean' => 'Status tidak valid',
        ]);

        $validated['created_by'] = Auth::guard('admin')->id();

        $redeemItem = RedeemItem::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Item redeem berhasil ditambahkan',
            'data' => $redeemItem,
        ], 201);
    }

    /**
     * Show the form for editing the specified redeem item.
     */
    public function edit(string $id): View
    {
        $redeemItem = RedeemItem::findOrFail($id);

        return view('dashboard-redeem-item-edit', compact('redeemItem'));
    }

    /**
     * Update the specified redeem item in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $redeemItem = RedeemItem::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'points_required' => 'required|integer|min:1',
            'is_active' => 'required|boolean',
        ], [
            'name.required' => 'Nama item harus diisi',
            'name.max' => 'Nama item maksimal 255 karakter',
            'points_required.required' => 'Point yang dibutuhkan harus diisi',
            'points_required.integer' => 'Point yang dibutuhkan harus berupa angka',
            'points_required.min' => 'Point yang dibutuhkan minimal 1',
            'is_active.required' => 'Status harus dipilih',
            'is_active.boolean' => 'Status tidak valid',
        ]);

        $redeemItem->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Item redeem berhasil diperbarui',
            'data' => $redeemItem,
        ]);
    }

    /**
     * Remove the specified redeem item from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $redeemItem = RedeemItem::findOrFail($id);

        // Soft delete - just set is_active to false
        $redeemItem->delete();

        return response()->json([
            'success' => true,
            'message' => 'Item redeem berhasil dihapus',
        ]);
    }

    /**
     * Remove multiple redeem items from storage.
     */
    public function bulkDestroy(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'required|integer|exists:redeem_items,id',
        ]);

        $count = RedeemItem::whereIn('id', $validated['ids'])
            ->update(['is_active' => false]);

        return response()->json([
            'success' => true,
            'message' => "{$count} item redeem berhasil dinonaktifkan",
        ]);
    }

    /**
     * Export redeem items to Excel.
     */
    public function exportExcel(): StreamedResponse
    {
        $redeemItems = RedeemItem::orderBy('created_at', 'desc')->get();

        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Nama Item');
        $sheet->setCellValue('C1', 'Deskripsi');
        $sheet->setCellValue('D1', 'Point Required');
        $sheet->setCellValue('E1', 'Status');
        $sheet->setCellValue('F1', 'Tanggal Dibuat');

        // Style headers
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '39746E']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ];
        $sheet->getStyle('A1:F1')->applyFromArray($headerStyle);

        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(8);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(40);
        $sheet->getColumnDimension('D')->setWidth(15);
        $sheet->getColumnDimension('E')->setWidth(12);
        $sheet->getColumnDimension('F')->setWidth(20);

        // Fill data
        $row = 2;
        foreach ($redeemItems as $index => $item) {
            $sheet->setCellValue("A{$row}", $index + 1);
            $sheet->setCellValue("B{$row}", $item->name);
            $sheet->setCellValue("C{$row}", $item->description ?? '-');
            $sheet->setCellValue("D{$row}", number_format($item->points_required, 0, ',', '.'));
            $sheet->setCellValue("E{$row}", $item->is_active ? 'Active' : 'Inactive');
            $sheet->setCellValue("F{$row}", $item->created_at->format('d M Y H:i'));

            // Style data rows
            $sheet->getStyle("A{$row}:F{$row}")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
            ]);
            $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("D{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle("E{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $row++;
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'redeem_items_export_'.date('Y-m-d_His').'.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    /**
     * Export redeem items to CSV.
     */
    public function exportCsv(): StreamedResponse
    {
        $redeemItems = RedeemItem::orderBy('created_at', 'desc')->get();

        $filename = 'redeem_items_export_'.date('Y-m-d_His').'.csv';

        return response()->streamDownload(function () use ($redeemItems) {
            $output = fopen('php://output', 'w');

            // Add UTF-8 BOM for Excel compatibility
            fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

            // Headers
            fputcsv($output, ['No', 'Nama Item', 'Deskripsi', 'Point Required', 'Status', 'Tanggal Dibuat']);

            // Data rows
            foreach ($redeemItems as $index => $item) {
                fputcsv($output, [
                    $index + 1,
                    $item->name,
                    $item->description ?? '-',
                    number_format($item->points_required, 0, ',', '.'),
                    $item->is_active ? 'Active' : 'Inactive',
                    $item->created_at->format('d M Y H:i'),
                ]);
            }

            fclose($output);
        }, $filename, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }

    /**
     * Export redeem items to PDF.
     */
    public function exportPdf()
    {
        $redeemItems = RedeemItem::orderBy('created_at', 'desc')->get();

        $pdf = Pdf::loadView('exports.redeem-items', [
            'redeemItems' => $redeemItems,
            'exportDate' => now()->format('d M Y H:i'),
        ]);

        $pdf->setPaper('a4', 'landscape');

        return $pdf->download('redeem_items_export_'.date('Y-m-d_His').'.pdf');
    }
}
