<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Sampah;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Category::query();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('nama', 'like', '%'.$search.'%');
        }

        $categories = $query->orderBy('created_at', 'desc')->paginate(10);

        // Transform data untuk response
        $categories->getCollection()->transform(function ($category) {
            $category->sampah_count = $category->sampah_count;

            return $category;
        });

        // Return JSON for AJAX requests
        if ($request->ajax()) {
            return response()->json([
                'categories' => $categories,
            ]);
        }

        return view('dashboard-category', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $sampahList = Sampah::orderBy('nama')->get();

        return view('dashboard-category-create', compact('sampahList'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'nama' => 'required|string|max:255',
                'sampah_ids' => 'required|array|min:1',
                'sampah_ids.*' => 'exists:sampah,id',
            ]);

            $category = Category::create([
                'nama' => $request->nama,
                'sampah' => $request->sampah_ids,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Kategori berhasil dibuat',
                'data' => $category,
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in CategoryController@store: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat kategori: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $category = Category::findOrFail($id);

        return view('dashboard-category-show', compact('category'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $category = Category::findOrFail($id);
        $sampahList = Sampah::orderBy('nama')->get();

        return view('dashboard-category-edit', compact('category', 'sampahList'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $category = Category::findOrFail($id);

            $request->validate([
                'nama' => 'required|string|max:255',
                'sampah_ids' => 'required|array|min:1',
                'sampah_ids.*' => 'exists:sampah,id',
            ]);

            $category->update([
                'nama' => $request->nama,
                'sampah' => $request->sampah_ids,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Kategori berhasil diupdate',
                'data' => $category,
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in CategoryController@update: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate kategori: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
    {
        try {
            // Handle bulk delete
            if ($id == 0 && $request->has('ids')) {
                $ids = $request->ids;
                Category::whereIn('id', $ids)->delete();

                return response()->json([
                    'success' => true,
                    'message' => count($ids).' kategori berhasil dihapus',
                ]);
            }

            // Handle single delete
            $category = Category::findOrFail($id);
            $category->delete();

            return response()->json([
                'success' => true,
                'message' => 'Kategori berhasil dihapus',
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in CategoryController@destroy: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus kategori: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Export category data to Excel
     */
    public function exportExcel(Request $request)
    {
        try {
            $categories = Category::orderBy('created_at', 'desc')->get();

            // Generate Excel file
            return $this->generateExcelFile($categories);

        } catch (\Exception $e) {
            \Log::error('Error in CategoryController@exportExcel: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal export Excel: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Generate Excel file from category data
     */
    private function generateExcelFile($categories)
    {
        // Create new Spreadsheet
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();

        // Set document properties
        $spreadsheet->getProperties()
            ->setCreator('Bengkel Sampah Admin')
            ->setLastModifiedBy('Bengkel Sampah Admin')
            ->setTitle('Laporan Kategori')
            ->setSubject('Laporan Data Kategori')
            ->setDescription('Laporan data kategori sampah Bengkel Sampah')
            ->setKeywords('kategori, laporan, bengkel sampah')
            ->setCategory('Laporan');

        // Set header style
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

        // Set title
        $sheet->setCellValue('A1', 'LAPORAN DATA KATEGORI SAMPAH BENGKEL SAMPAH');
        $sheet->mergeCells('A1:F1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Set subtitle
        $sheet->setCellValue('A2', 'Total Data: '.$categories->count().' kategori');
        $sheet->mergeCells('A2:F2');
        $sheet->getStyle('A2')->getFont()->setSize(12);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Set export date
        $sheet->setCellValue('A3', 'Tanggal Export: '.now()->format('d F Y H:i:s'));
        $sheet->mergeCells('A3:F3');
        $sheet->getStyle('A3')->getFont()->setSize(10);
        $sheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Set headers
        $headers = [
            'A5' => 'No',
            'B5' => 'ID Kategori',
            'C5' => 'Nama Kategori',
            'D5' => 'Jumlah Sampah',
            'E5' => 'Daftar Sampah',
            'F5' => 'Tanggal Dibuat',
        ];

        foreach ($headers as $cell => $header) {
            $sheet->setCellValue($cell, $header);
        }

        // Apply header style
        $sheet->getStyle('A5:F5')->applyFromArray($headerStyle);

        // Add data rows
        $row = 6;
        foreach ($categories as $index => $category) {
            // Get sampah items for this category
            $sampahItems = $category->sampah_items;
            $sampahList = '';

            if ($sampahItems && count($sampahItems) > 0) {
                $sampahNames = [];
                foreach ($sampahItems as $sampah) {
                    $sampahNames[] = $sampah->nama.' ('.strtoupper($sampah->satuan).')';
                }
                $sampahList = implode(', ', $sampahNames);
            } else {
                $sampahList = 'Tidak ada data';
            }

            $sheet->setCellValue('A'.$row, $index + 1);
            $sheet->setCellValue('B'.$row, $category->id);
            $sheet->setCellValue('C'.$row, $category->nama);
            $sheet->setCellValue('D'.$row, $category->sampah_count);
            $sheet->setCellValue('E'.$row, $sampahList);
            $sheet->setCellValue('F'.$row, $category->created_at->format('d/m/Y H:i'));
            $row++;
        }

        // Auto-size columns
        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Set column widths for better readability
        $sheet->getColumnDimension('A')->setWidth(5);   // No
        $sheet->getColumnDimension('B')->setWidth(10);  // ID
        $sheet->getColumnDimension('C')->setWidth(30);  // Nama Kategori
        $sheet->getColumnDimension('D')->setWidth(15);  // Jumlah Sampah
        $sheet->getColumnDimension('E')->setWidth(50);  // Daftar Sampah
        $sheet->getColumnDimension('F')->setWidth(20);  // Tanggal

        // Create Excel file
        $writer = new Xlsx($spreadsheet);

        // Set headers for download
        $filename = 'kategori_export_'.now()->format('Y-m-d_H-i-s').'.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$filename.'"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    /**
     * Export category data to CSV
     */
    public function exportCsv(Request $request)
    {
        try {
            $categories = Category::orderBy('created_at', 'desc')->get();

            // Generate CSV file
            return $this->generateCsvFile($categories);

        } catch (\Exception $e) {
            \Log::error('Error in CategoryController@exportCsv: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal export CSV: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Generate CSV file from category data
     */
    private function generateCsvFile($categories)
    {
        // Set headers for download
        $filename = 'kategori_export_'.now()->format('Y-m-d_H-i-s').'.csv';

        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="'.$filename.'"');
        header('Cache-Control: max-age=0');

        // Add BOM for UTF-8 to ensure proper encoding in Excel
        echo "\xEF\xBB\xBF";

        // Create output stream
        $output = fopen('php://output', 'w');

        // Add headers
        fputcsv($output, [
            'No',
            'ID Kategori',
            'Nama Kategori',
            'Jumlah Sampah',
            'Daftar Sampah',
            'Tanggal Dibuat',
        ]);

        // Add data rows
        foreach ($categories as $index => $category) {
            // Get sampah items for this category
            $sampahItems = $category->sampah_items;
            $sampahList = '';

            if ($sampahItems && count($sampahItems) > 0) {
                $sampahNames = [];
                foreach ($sampahItems as $sampah) {
                    $sampahNames[] = $sampah->nama.' ('.strtoupper($sampah->satuan).')';
                }
                $sampahList = implode(', ', $sampahNames);
            } else {
                $sampahList = 'Tidak ada data';
            }

            fputcsv($output, [
                $index + 1,
                $category->id,
                $category->nama,
                $category->sampah_count,
                $sampahList,
                $category->created_at->format('d/m/Y H:i'),
            ]);
        }

        fclose($output);
        exit;
    }

    /**
     * Export category data to PDF
     */
    public function exportPdf(Request $request)
    {
        try {
            $categories = Category::orderBy('created_at', 'desc')->get();

            // Generate PDF file
            return $this->generatePdfFile($categories);

        } catch (\Exception $e) {
            \Log::error('Error in CategoryController@exportPdf: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal export PDF: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Generate PDF file from category data
     */
    private function generatePdfFile($categories)
    {
        // Load categories with sampah items for PDF
        $categoriesWithSampah = $categories->map(function ($category) {
            $category->sampahItems = $category->sampah_items;
            $category->sampah_count = $category->sampah_count;

            return $category;
        });

        $data = [
            'categories' => $categoriesWithSampah,
            'period' => 'all',
            'exportDate' => now()->format('d F Y H:i:s'),
            'totalData' => $categories->count(),
            'startDate' => null,
            'endDate' => null,
        ];

        $pdf = Pdf::loadView('exports.category', $data);

        $filename = 'kategori_export_'.now()->format('Y-m-d_H-i-s').'.pdf';

        return $pdf->download($filename);
    }
}
