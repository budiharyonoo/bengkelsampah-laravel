<?php

namespace App\Http\Controllers;

use App\Models\Artikel;
use App\Models\KategoriArtikel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ArtikelController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Artikel::with('kategori');

        // Search
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%'.$request->search.'%')
                    ->orWhere('content', 'like', '%'.$request->search.'%');
            });
        }

        // Filter kategori
        if ($request->filled('kategori')) {
            $query->whereHas('kategori', function ($q) use ($request) {
                $q->where('nama', $request->kategori);
            });
        }

        $artikels = $query->orderByDesc('created_at')->paginate(10)->withQueryString();
        $kategoris = KategoriArtikel::all();

        if ($request->ajax()) {
            return response()->json([
                'artikels' => $artikels,
                'html' => view('partials.artikel-table', compact('artikels'))->render(),
                'pagination' => view('partials.pagination', compact('artikels'))->render(),
            ]);
        }

        return view('dashboard-artikel', [
            'artikels' => $artikels,
            'kategoris' => $kategoris,
            'search' => $request->search,
            'selectedKategori' => $request->kategori,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $kategoris = KategoriArtikel::all();

        return view('dashboard-artikel-create', compact('kategoris'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'content' => 'required|string',
                'cover' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
                'kategori_id' => 'required|exists:kategori_artikels,id',
            ]);

            if ($request->hasFile('cover')) {
                $file = $request->file('cover');
                $filename = time().'_'.Str::random(10).'_'.Str::slug($request->title).'.'.$file->getClientOriginalExtension();

                // Create directory if it doesn't exist
                $uploadPath = base_path('../uploads/artikel_cover');
                if (! file_exists($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }

                // Store the file
                $path = $file->storeAs('artikel_cover', $filename, 'public');

                if (! $path) {
                    throw new \Exception('Failed to upload file');
                }

                // Get the full URL for the image
                $coverUrl = env('APP_URL').'/uploads/'.$path;

                $artikel = Artikel::create([
                    'title' => $request->title,
                    'content' => $request->content,
                    'cover' => $coverUrl,
                    'kategori_id' => $request->kategori_id,
                    'creator' => auth()->guard('admin')->user()->name,
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Artikel berhasil ditambahkan',
                    'data' => $artikel,
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupload gambar',
            ], 400);

        } catch (\Exception $e) {
            \Log::error('Error in ArtikelController@store: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan artikel: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $artikel = Artikel::with('kategori')->findOrFail($id);
        $kategoris = KategoriArtikel::all();

        return view('dashboard-artikel-edit', compact('artikel', 'kategoris'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $artikel = Artikel::findOrFail($id);

            $request->validate([
                'title' => 'required|string|max:255',
                'content' => 'required|string',
                'cover' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
                'kategori_id' => 'required|exists:kategori_artikels,id',
            ]);

            $data = [
                'title' => $request->title,
                'content' => $request->content,
                'kategori_id' => $request->kategori_id,
            ];

            if ($request->hasFile('cover')) {
                // Delete old cover if exists
                if ($artikel->cover) {
                    $oldPath = str_replace(env('APP_URL').'/uploads/', '', $artikel->cover);
                    $fullOldPath = base_path('../api.bengkelsampah.com/uploads/'.$oldPath);
                    if (file_exists($fullOldPath)) {
                        unlink($fullOldPath);
                    }
                }

                $file = $request->file('cover');
                // Generate unique filename using timestamp and random string
                $filename = time().'_'.Str::random(10).'_'.Str::slug($request->title).'.'.$file->getClientOriginalExtension();

                // Create directory if it doesn't exist
                $uploadPath = base_path('../uploads/artikel_cover');
                if (! file_exists($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }

                // Store the file
                $path = $file->storeAs('artikel_cover', $filename, 'public');

                if (! $path) {
                    throw new \Exception('Failed to upload file');
                }

                // Get the full URL for the image
                $data['cover'] = env('APP_URL').'/uploads/'.$path;
            }

            $artikel->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Artikel berhasil diubah',
                'data' => $artikel,
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in ArtikelController@update: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengubah artikel: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        // Jika request berisi array ids, hapus multiple
        if ($request->has('ids') && is_array($request->ids)) {
            Artikel::whereIn('id', $request->ids)->delete();

            return response()->json(['success' => true, 'message' => 'Artikel berhasil dihapus.']);
        }
        // Hapus satu artikel
        $artikel = Artikel::find($id);
        if ($artikel) {
            $artikel->delete();

            return response()->json(['success' => true, 'message' => 'Artikel berhasil dihapus.']);
        }

        return response()->json(['success' => false, 'message' => 'Artikel tidak ditemukan.'], 404);
    }

    /**
     * Export artikel to Excel
     */
    public function exportExcel(Request $request)
    {
        try {
            $request->validate([
                'period' => 'required|string',
                'category' => 'nullable|string',
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
            ]);

            $query = Artikel::with('kategori');

            // Apply period filter
            switch ($request->period) {
                case 'today':
                    $query->whereDate('created_at', today());
                    break;
                case 'yesterday':
                    $query->whereDate('created_at', today()->subDay());
                    break;
                case 'this_week':
                    $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'last_week':
                    $query->whereBetween('created_at', [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()]);
                    break;
                case 'this_month':
                    $query->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year);
                    break;
                case 'last_month':
                    $query->whereMonth('created_at', now()->subMonth()->month)->whereYear('created_at', now()->subMonth()->year);
                    break;
                case 'this_year':
                    $query->whereYear('created_at', now()->year);
                    break;
                case 'last_year':
                    $query->whereYear('created_at', now()->subYear()->year);
                    break;
                case 'range':
                    if ($request->start_date && $request->end_date) {
                        $query->whereBetween('created_at', [$request->start_date.' 00:00:00', $request->end_date.' 23:59:59']);
                    }
                    break;
                case 'all':
                default:
                    // No date filter
                    break;
            }

            // Apply category filter
            if ($request->category) {
                $query->where('kategori_id', $request->category);
            }

            $artikels = $query->orderBy('created_at', 'desc')->get();

            // Generate Excel file
            return $this->generateExcelFile($artikels, $request->period);

        } catch (\Exception $e) {
            \Log::error('Error in ArtikelController@exportExcel: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal export Excel: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Generate Excel file from artikel data
     */
    private function generateExcelFile($artikels, $period)
    {
        // Create new Spreadsheet
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();

        // Set document properties
        $spreadsheet->getProperties()
            ->setCreator('Bengkel Sampah Admin')
            ->setLastModifiedBy('Bengkel Sampah Admin')
            ->setTitle('Laporan Artikel - '.ucfirst($period))
            ->setSubject('Laporan Data Artikel')
            ->setDescription('Laporan data artikel Bengkel Sampah')
            ->setKeywords('artikel, laporan, bengkel sampah')
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
        $sheet->setCellValue('A1', 'LAPORAN DATA ARTIKEL BENGKEL SAMPAH');
        $sheet->mergeCells('A1:H1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Set subtitle
        $sheet->setCellValue('A2', 'Periode: '.ucfirst(str_replace('_', ' ', $period)).' | Total Data: '.$artikels->count().' artikel');
        $sheet->mergeCells('A2:H2');
        $sheet->getStyle('A2')->getFont()->setSize(12);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Set export date
        $sheet->setCellValue('A3', 'Tanggal Export: '.now()->format('d F Y H:i:s'));
        $sheet->mergeCells('A3:H3');
        $sheet->getStyle('A3')->getFont()->setSize(10);
        $sheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Set headers
        $headers = [
            'A5' => 'No',
            'B5' => 'ID Artikel',
            'C5' => 'Judul Artikel',
            'D5' => 'Kategori',
            'E5' => 'Creator',
            'F5' => 'Tanggal Dibuat',
            'G5' => 'URL Cover',
            'H5' => 'Content',
        ];

        foreach ($headers as $cell => $header) {
            $sheet->setCellValue($cell, $header);
        }

        // Apply header style
        $sheet->getStyle('A5:H5')->applyFromArray($headerStyle);

        // Set data
        $row = 6;
        foreach ($artikels as $index => $artikel) {
            $sheet->setCellValue('A'.$row, $index + 1);
            $sheet->setCellValue('B'.$row, $artikel->id);
            $sheet->setCellValue('C'.$row, $artikel->title);
            $sheet->setCellValue('D'.$row, $artikel->kategori->nama ?? '-');
            $sheet->setCellValue('E'.$row, $artikel->creator);
            $sheet->setCellValue('F'.$row, $artikel->created_at->format('d/m/Y H:i'));
            $sheet->setCellValue('G'.$row, $artikel->cover ?? '-');

            // Set full content without character limit
            $fullContent = strip_tags($artikel->content);
            $sheet->setCellValue('H'.$row, $fullContent);

            // Enable word wrap for content column
            $sheet->getStyle('H'.$row)->getAlignment()->setWrapText(true);
            $sheet->getStyle('H'.$row)->getAlignment()->setVertical(Alignment::VERTICAL_TOP);

            // Set row height for content column to accommodate longer text
            $contentLength = strlen($fullContent);
            if ($contentLength > 500) {
                $sheet->getRowDimension($row)->setRowHeight(60); // Higher row for longer content
            } elseif ($contentLength > 200) {
                $sheet->getRowDimension($row)->setRowHeight(40); // Medium row for medium content
            } else {
                $sheet->getRowDimension($row)->setRowHeight(25); // Default row height
            }

            // Set border for data row
            $sheet->getStyle('A'.$row.':H'.$row)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

            $row++;
        }

        // Auto-size columns
        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Set column widths for better readability
        $sheet->getColumnDimension('A')->setWidth(5);  // No
        $sheet->getColumnDimension('B')->setWidth(10); // ID
        $sheet->getColumnDimension('C')->setWidth(40); // Title
        $sheet->getColumnDimension('D')->setWidth(20); // Category
        $sheet->getColumnDimension('E')->setWidth(20); // Creator
        $sheet->getColumnDimension('F')->setWidth(20); // Date
        $sheet->getColumnDimension('G')->setWidth(50); // URL
        $sheet->getColumnDimension('H')->setWidth(60); // Content

        // Create Excel file
        $writer = new Xlsx($spreadsheet);

        // Set headers for download
        $filename = 'artikel_export_'.$period.'_'.now()->format('Y-m-d_H-i-s').'.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$filename.'"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    /**
     * Export artikel to CSV
     */
    public function exportCsv(Request $request)
    {
        try {
            $request->validate([
                'period' => 'required|string',
                'category' => 'nullable|string',
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
            ]);

            $query = Artikel::with('kategori');

            // Apply period filter
            switch ($request->period) {
                case 'today':
                    $query->whereDate('created_at', today());
                    break;
                case 'yesterday':
                    $query->whereDate('created_at', today()->subDay());
                    break;
                case 'this_week':
                    $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'last_week':
                    $query->whereBetween('created_at', [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()]);
                    break;
                case 'this_month':
                    $query->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year);
                    break;
                case 'last_month':
                    $query->whereMonth('created_at', now()->subMonth()->month)->whereYear('created_at', now()->subMonth()->year);
                    break;
                case 'this_year':
                    $query->whereYear('created_at', now()->year);
                    break;
                case 'last_year':
                    $query->whereYear('created_at', now()->subYear()->year);
                    break;
                case 'range':
                    if ($request->start_date && $request->end_date) {
                        $query->whereBetween('created_at', [$request->start_date.' 00:00:00', $request->end_date.' 23:59:59']);
                    }
                    break;
                case 'all':
                default:
                    // No date filter
                    break;
            }

            // Apply category filter
            if ($request->category) {
                $query->where('kategori_id', $request->category);
            }

            $artikels = $query->orderBy('created_at', 'desc')->get();

            // Generate CSV file
            return $this->generateCsvFile($artikels, $request->period);

        } catch (\Exception $e) {
            \Log::error('Error in ArtikelController@exportCsv: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal export CSV: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Generate CSV file from artikel data
     */
    private function generateCsvFile($artikels, $period)
    {
        // Set headers for download
        $filename = 'artikel_export_'.$period.'_'.now()->format('Y-m-d_H-i-s').'.csv';

        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="'.$filename.'"');
        header('Cache-Control: max-age=0');

        // Add BOM for UTF-8 to ensure proper encoding in Excel
        echo "\xEF\xBB\xBF";

        // Create output stream
        $output = fopen('php://output', 'w');

        // Write header row
        $headers = [
            'No',
            'ID Artikel',
            'Judul Artikel',
            'Kategori',
            'Creator',
            'Tanggal Dibuat',
            'URL Cover',
            'Content',
        ];
        fputcsv($output, $headers);

        // Write data rows
        foreach ($artikels as $index => $artikel) {
            $row = [
                $index + 1,
                $artikel->id,
                $artikel->title,
                $artikel->kategori->nama ?? '-',
                $artikel->creator,
                $artikel->created_at->format('d/m/Y H:i'),
                $artikel->cover ?? '-',
                strip_tags($artikel->content), // Full content without HTML tags
            ];
            fputcsv($output, $row);
        }

        fclose($output);
        exit;
    }

    /**
     * Export artikel to PDF
     */
    public function exportPdf(Request $request)
    {
        try {
            $request->validate([
                'period' => 'required|string',
                'category' => 'nullable|string',
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
            ]);

            $query = Artikel::with('kategori');

            // Apply period filter
            switch ($request->period) {
                case 'today':
                    $query->whereDate('created_at', today());
                    break;
                case 'yesterday':
                    $query->whereDate('created_at', today()->subDay());
                    break;
                case 'this_week':
                    $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'last_week':
                    $query->whereBetween('created_at', [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()]);
                    break;
                case 'this_month':
                    $query->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year);
                    break;
                case 'last_month':
                    $query->whereMonth('created_at', now()->subMonth()->month)->whereYear('created_at', now()->subMonth()->year);
                    break;
                case 'this_year':
                    $query->whereYear('created_at', now()->year);
                    break;
                case 'last_year':
                    $query->whereYear('created_at', now()->subYear()->year);
                    break;
                case 'range':
                    if ($request->start_date && $request->end_date) {
                        $query->whereBetween('created_at', [$request->start_date.' 00:00:00', $request->end_date.' 23:59:59']);
                    }
                    break;
                case 'all':
                default:
                    // No date filter
                    break;
            }

            // Apply category filter
            $categoryFilter = null;
            if ($request->category) {
                $query->where('kategori_id', $request->category);
                $category = KategoriArtikel::find($request->category);
                $categoryFilter = $category ? $category->nama : 'Kategori tidak ditemukan';
            }

            $artikels = $query->orderBy('created_at', 'desc')->get();

            // Generate PDF
            return $this->generatePdfFile($artikels, $request->period, $categoryFilter);

        } catch (\Exception $e) {
            \Log::error('Error in ArtikelController@exportPdf: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal export PDF: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Generate PDF file from artikel data
     */
    private function generatePdfFile($artikels, $period, $categoryFilter = null)
    {
        // Load the view
        $html = view('pdf.artikel-report', compact('artikels', 'period', 'categoryFilter'))->render();

        // Create PDF using DomPDF
        $pdf = Pdf::loadHTML($html);

        // Set paper size and orientation
        $pdf->setPaper('A4', 'portrait');

        // Set filename
        $filename = 'artikel_export_'.$period.'_'.now()->format('Y-m-d_H-i-s').'.pdf';

        // Return PDF for download
        return $pdf->download($filename);
    }
}
