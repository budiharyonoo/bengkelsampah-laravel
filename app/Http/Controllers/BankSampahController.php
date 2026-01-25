<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\BankSampah;
use App\Models\Price;
use App\Models\Sampah;
use App\Models\Setoran;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class BankSampahController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = BankSampah::query();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('kode_bank_sampah', 'like', '%'.$search.'%')
                    ->orWhere('nama_bank_sampah', 'like', '%'.$search.'%')
                    ->orWhere('alamat_bank_sampah', 'like', '%'.$search.'%')
                    ->orWhere('nama_penanggung_jawab', 'like', '%'.$search.'%')
                    ->orWhere('kontak_penanggung_jawab', 'like', '%'.$search.'%');
            });
        }

        $bankSampah = $query->orderBy('created_at', 'desc')->paginate(10);

        // Always return view for consistent browser behavior
        return view('dashboard-bank-sampah', compact('bankSampah'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('dashboard-bank-sampah-create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'nama_bank_sampah' => 'required|string|max:255',
                'alamat_bank_sampah' => 'required|string',
                'nama_penanggung_jawab' => 'required|string|max:255',
                'kontak_penanggung_jawab' => 'required|string|max:255',
                'foto' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
                'latitude' => 'nullable|numeric|min:-90|max:90',
                'longitude' => 'nullable|numeric|min:-180|max:180',
                'tipe_layanan' => 'required|in:jemput,tempat,keduanya',
            ]);

            $data = $request->all();

            if ($request->hasFile('foto')) {
                $file = $request->file('foto');
                $filename = time().'_'.Str::random(10).'_'.Str::slug($request->nama_bank_sampah).'.'.$file->getClientOriginalExtension();

                // Create directory if it doesn't exist
                $uploadPath = base_path('../uploads/bank_sampah');
                if (! file_exists($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }

                // Store the file
                $path = $file->storeAs('bank_sampah', $filename, 'public');

                if (! $path) {
                    throw new \Exception('Failed to upload file');
                }

                // Get the full URL for the image
                $data['foto'] = env('APP_URL').'/uploads/'.$path;
            }

            $bankSampah = BankSampah::create($data);

            // Auto-insert price entries for all existing sampah
            $this->createPriceEntriesForNewBank($bankSampah->id);

            return response()->json([
                'success' => true,
                'message' => 'Bank Sampah berhasil dibuat',
                'data' => $bankSampah,
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in BankSampahController@store: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat Bank Sampah: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Create price entries for all existing sampah for a new bank sampah
     */
    private function createPriceEntriesForNewBank($bankSampahId)
    {
        $sampahList = Sampah::all();

        foreach ($sampahList as $sampah) {
            // Get the lowest price for this sampah from all existing bank sampah
            $lowestPrice = Price::where('sampah_id', $sampah->id)
                ->min('harga');

            // If no existing price, use default price of 1000
            $defaultPrice = $lowestPrice ?: 1000;

            // Create price entry for the new bank sampah
            Price::create([
                'sampah_id' => $sampah->id,
                'bank_sampah_id' => $bankSampahId,
                'harga' => $defaultPrice,
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $bankSampah = BankSampah::with(['admin'])->findOrFail($id);

        // Get statistics
        $stats = $this->getBankSampahStats($id);

        // Get recent setoran
        $recentSetoran = \App\Models\Setoran::where('bank_sampah_id', $id)
            ->with(['user', 'points'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('dashboard-bank-sampah-show', compact('bankSampah', 'stats', 'recentSetoran'));
    }

    /**
     * Get bank sampah statistics
     */
    private function getBankSampahStats($bankSampahId)
    {
        $totalSetoran = \App\Models\Setoran::where('bank_sampah_id', $bankSampahId)->count();
        $completedSetoran = \App\Models\Setoran::where('bank_sampah_id', $bankSampahId)
            ->where('status', 'selesai')
            ->count();
        $pendingSetoran = \App\Models\Setoran::where('bank_sampah_id', $bankSampahId)
            ->whereIn('status', ['dikonfirmasi', 'diproses', 'dijemput'])
            ->count();
        $cancelledSetoran = \App\Models\Setoran::where('bank_sampah_id', $bankSampahId)
            ->where('status', 'batal')
            ->count();

        $totalRevenue = \App\Models\Setoran::where('bank_sampah_id', $bankSampahId)
            ->where('status', 'selesai')
            ->sum('aktual_total');

        $totalPoints = \App\Models\Point::whereHas('setoran', function ($query) use ($bankSampahId) {
            $query->where('bank_sampah_id', $bankSampahId);
        })->sum('jumlah_point');

        // Registered customers from this bank sampah (based on users table)
        $registeredCustomers = \App\Models\User::query()->whereUserType($bankSampahId)->count();

        // Unique customers who have setoran transactions in this bank sampah
        $uniqueTransactionUsers = \App\Models\Setoran::where('bank_sampah_id', $bankSampahId)
            ->distinct('user_id')
            ->count('user_id');

        $avgSetoranValue = $completedSetoran > 0 ? $totalRevenue / $completedSetoran : 0;

        return [
            'total_setoran' => $totalSetoran,
            'completed_setoran' => $completedSetoran,
            'pending_setoran' => $pendingSetoran,
            'cancelled_setoran' => $cancelledSetoran,
            'total_revenue' => $totalRevenue,
            'total_points' => $totalPoints,
            'registered_customers' => $registeredCustomers,
            'unique_transaction_users' => $uniqueTransactionUsers,
            'avg_setoran_value' => $avgSetoranValue,
            'completion_rate' => $totalSetoran > 0 ? ($completedSetoran / $totalSetoran) * 100 : 0,
        ];
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $bankSampah = BankSampah::findOrFail($id);

        return view('dashboard-bank-sampah-edit', compact('bankSampah'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $bankSampah = BankSampah::findOrFail($id);

            $request->validate([
                'nama_bank_sampah' => 'required|string|max:255',
                'alamat_bank_sampah' => 'required|string',
                'nama_penanggung_jawab' => 'required|string|max:255',
                'kontak_penanggung_jawab' => 'required|string|max:255',
                'foto' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
                'latitude' => 'nullable|numeric|min:-90|max:90',
                'longitude' => 'nullable|numeric|min:-180|max:180',
                'tipe_layanan' => 'required|in:jemput,tempat,keduanya',
            ]);

            $data = $request->all();

            if ($request->hasFile('foto')) {
                // Delete old foto if exists
                if ($bankSampah->foto) {
                    $oldPath = str_replace(env('APP_URL').'/uploads/', '', $bankSampah->foto);
                    $fullOldPath = base_path('../api.bengkelsampah.com/uploads/'.$oldPath);
                    if (file_exists($fullOldPath)) {
                        unlink($fullOldPath);
                    }
                }

                $file = $request->file('foto');
                $filename = time().'_'.Str::random(10).'_'.Str::slug($request->nama_bank_sampah).'.'.$file->getClientOriginalExtension();

                // Create directory if it doesn't exist
                $uploadPath = base_path('../uploads/bank_sampah');
                if (! file_exists($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }

                // Store the file
                $path = $file->storeAs('bank_sampah', $filename, 'public');

                if (! $path) {
                    throw new \Exception('Failed to upload file');
                }

                // Get the full URL for the image
                $data['foto'] = env('APP_URL').'/uploads/'.$path;
            }

            $bankSampah->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Bank Sampah berhasil diupdate',
                'data' => $bankSampah,
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in BankSampahController@update: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate Bank Sampah: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
    {
        try {
            $bankSampah = BankSampah::findOrFail($id);

            // Delete foto if exists
            if ($bankSampah->foto) {
                $oldPath = str_replace(env('APP_URL').'/uploads/', '', $bankSampah->foto);
                $fullOldPath = base_path('../api.bengkelsampah.com/uploads/'.$oldPath);
                if (file_exists($fullOldPath)) {
                    unlink($fullOldPath);
                }
            }

            // Delete all related prices first
            Price::where('bank_sampah_id', $bankSampah->id)->delete();

            // Delete all related admin accounts
            Admin::where('id_bank_sampah', $bankSampah->id)->delete();

            $bankSampah->delete();

            return response()->json([
                'success' => true,
                'message' => 'Bank Sampah berhasil dihapus',
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in BankSampahController@destroy: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus Bank Sampah: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Bulk delete multiple bank sampah
     */
    public function bulkDestroy(Request $request)
    {
        try {
            $ids = $request->ids;

            if (empty($ids)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada data yang dipilih',
                ], 400);
            }

            // Delete all related prices first
            Price::whereIn('bank_sampah_id', $ids)->delete();

            // Delete all related admin accounts
            Admin::whereIn('id_bank_sampah', $ids)->delete();

            BankSampah::whereIn('id', $ids)->delete();

            return response()->json([
                'success' => true,
                'message' => 'Bank Sampah berhasil dihapus',
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in BankSampahController@bulkDestroy: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus Bank Sampah: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Export bank sampah data to Excel
     */
    public function exportExcel(Request $request)
    {
        try {
            $request->validate([
                'period' => 'required|string',
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
            ]);

            $query = BankSampah::query();

            // Apply filters based on request
            $this->applyExportFilters($query, $request);

            $bankSampah = $query->orderBy('created_at', 'desc')->get();

            // Generate Excel file
            return $this->generateExcelFile($bankSampah, $request->period);

        } catch (\Exception $e) {
            \Log::error('Error in BankSampahController@exportExcel: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal export Excel: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Generate Excel file from bank sampah data
     */
    private function generateExcelFile($bankSampah, $period)
    {
        // Create new Spreadsheet
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();

        // Set document properties
        $spreadsheet->getProperties()
            ->setCreator('Bengkel Sampah Admin')
            ->setLastModifiedBy('Bengkel Sampah Admin')
            ->setTitle('Laporan Bank Sampah - '.ucfirst($period))
            ->setSubject('Laporan Data Bank Sampah')
            ->setDescription('Laporan data bank sampah Bengkel Sampah')
            ->setKeywords('bank sampah, laporan, bengkel sampah')
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
        $sheet->setCellValue('A1', 'LAPORAN DATA BANK SAMPAH BENGKEL SAMPAH');
        $sheet->mergeCells('A1:J1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Set subtitle
        $sheet->setCellValue('A2', 'Periode: '.ucfirst(str_replace('_', ' ', $period)).' | Total Data: '.$bankSampah->count().' bank sampah');
        $sheet->mergeCells('A2:J2');
        $sheet->getStyle('A2')->getFont()->setSize(12);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Set export date
        $sheet->setCellValue('A3', 'Tanggal Export: '.now()->format('d F Y H:i:s'));
        $sheet->mergeCells('A3:J3');
        $sheet->getStyle('A3')->getFont()->setSize(10);
        $sheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Set headers
        $headers = [
            'A5' => 'No',
            'B5' => 'ID Bank Sampah',
            'C5' => 'Kode Bank Sampah',
            'D5' => 'Nama Bank Sampah',
            'E5' => 'Alamat Bank Sampah',
            'F5' => 'Nama Penanggung Jawab',
            'G5' => 'Kontak Penanggung Jawab',
            'H5' => 'Tipe Layanan',
            'I5' => 'Gambar',
            'J5' => 'Tanggal Dibuat',
        ];

        foreach ($headers as $cell => $header) {
            $sheet->setCellValue($cell, $header);
        }

        // Apply header style
        $sheet->getStyle('A5:J5')->applyFromArray($headerStyle);

        // Add data rows
        $row = 6;
        foreach ($bankSampah as $index => $bank) {
            $sheet->setCellValue('A'.$row, $index + 1);
            $sheet->setCellValue('B'.$row, $bank->id);
            $sheet->setCellValue('C'.$row, $bank->kode_bank_sampah);
            $sheet->setCellValue('D'.$row, $bank->nama_bank_sampah);
            $sheet->setCellValue('E'.$row, $bank->alamat_bank_sampah);
            $sheet->setCellValue('F'.$row, $bank->nama_penanggung_jawab);
            $sheet->setCellValue('G'.$row, $bank->kontak_penanggung_jawab);
            $sheet->setCellValue('H'.$row, ucfirst($bank->tipe_layanan));

            // Add image link
            if ($bank->foto) {
                $sheet->setCellValue('I'.$row, 'Lihat Gambar');
                $sheet->getCell('I'.$row)->getHyperlink()->setUrl($bank->foto);
                $sheet->getCell('I'.$row)->getHyperlink()->setTooltip('Klik untuk melihat gambar');
            } else {
                $sheet->setCellValue('I'.$row, 'Tidak ada gambar');
            }

            $sheet->setCellValue('J'.$row, $bank->created_at->format('d/m/Y H:i'));
            $row++;
        }

        // Auto-size columns
        foreach (range('A', 'J') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Set column widths for better readability
        $sheet->getColumnDimension('A')->setWidth(5);  // No
        $sheet->getColumnDimension('B')->setWidth(10); // ID
        $sheet->getColumnDimension('C')->setWidth(20); // Kode
        $sheet->getColumnDimension('D')->setWidth(30); // Nama
        $sheet->getColumnDimension('E')->setWidth(40); // Alamat
        $sheet->getColumnDimension('F')->setWidth(25); // Penanggung Jawab
        $sheet->getColumnDimension('G')->setWidth(20); // Kontak
        $sheet->getColumnDimension('H')->setWidth(15); // Tipe Layanan
        $sheet->getColumnDimension('I')->setWidth(50); // URL Foto
        $sheet->getColumnDimension('J')->setWidth(20); // Tanggal

        // Create Excel file
        $writer = new Xlsx($spreadsheet);

        // Set headers for download
        $filename = 'bank_sampah_export_'.$period.'_'.now()->format('Y-m-d_H-i-s').'.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$filename.'"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    /**
     * Export bank sampah data to CSV
     */
    public function exportCsv(Request $request)
    {
        try {
            $request->validate([
                'period' => 'required|string',
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
            ]);

            $query = BankSampah::query();

            // Apply filters based on request
            $this->applyExportFilters($query, $request);

            $bankSampah = $query->orderBy('created_at', 'desc')->get();

            // Generate CSV file
            return $this->generateCsvFile($bankSampah, $request->period);

        } catch (\Exception $e) {
            \Log::error('Error in BankSampahController@exportCsv: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal export CSV: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Generate CSV file from bank sampah data
     */
    private function generateCsvFile($bankSampah, $period)
    {
        // Set headers for download
        $filename = 'bank_sampah_export_'.$period.'_'.now()->format('Y-m-d_H-i-s').'.csv';

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
            'ID Bank Sampah',
            'Kode Bank Sampah',
            'Nama Bank Sampah',
            'Alamat Bank Sampah',
            'Nama Penanggung Jawab',
            'Kontak Penanggung Jawab',
            'Tipe Layanan',
            'Gambar',
            'Tanggal Dibuat',
        ]);

        // Add data rows
        foreach ($bankSampah as $index => $bank) {
            fputcsv($output, [
                $index + 1,
                $bank->id,
                $bank->kode_bank_sampah,
                $bank->nama_bank_sampah,
                $bank->alamat_bank_sampah,
                $bank->nama_penanggung_jawab,
                $bank->kontak_penanggung_jawab,
                ucfirst($bank->tipe_layanan),
                $bank->foto ? $bank->foto : 'Tidak ada gambar',
                $bank->created_at->format('d/m/Y H:i'),
            ]);
        }

        fclose($output);
        exit;
    }

    /**
     * Export bank sampah data to PDF
     */
    public function exportPdf(Request $request)
    {
        try {
            $request->validate([
                'period' => 'required|string',
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
            ]);

            $query = BankSampah::query();

            // Apply filters based on request
            $this->applyExportFilters($query, $request);

            $bankSampah = $query->orderBy('created_at', 'desc')->get();

            // Generate PDF
            return $this->generatePdfFile($bankSampah, $request->period, $request->start_date, $request->end_date);

        } catch (\Exception $e) {
            \Log::error('Error in BankSampahController@exportPdf: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal export PDF: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Generate PDF file from bank sampah data
     */
    private function generatePdfFile($bankSampah, $period, $startDate = null, $endDate = null)
    {
        // Get comprehensive statistics
        $stats = $this->getComprehensiveBankSampahStats($bankSampah, $period, $startDate, $endDate);

        // Generate PDF using DomPDF
        $pdf = \PDF::loadView('pdf.bank-sampah-report', [
            'bankSampah' => $bankSampah,
            'period' => $period,
            'totalBankSampah' => $stats['totalBankSampah'],
            'totalSetoran' => $stats['totalSetoran'],
            'totalSampahKg' => $stats['totalSampahKg'],
            'totalSampahUnit' => $stats['totalSampahUnit'],
            'totalPoint' => $stats['totalPoint'],
            'totalPelangganUnik' => $stats['totalPelangganUnik'],
            'totalPembelian' => $stats['totalPembelian'],
            'totalAdmin' => $stats['totalAdmin'],
            'bankSampahSummary' => $stats['bankSampahSummary'],
            'bankStats' => $stats['bankStats'],
        ]);

        // Set paper to A4 landscape
        $pdf->setPaper('A4', 'landscape');

        // Set filename
        $filename = 'bank_sampah_export_'.$period.'_'.now()->format('Y-m-d_H-i-s').'.pdf';

        // Return PDF for download
        return $pdf->download($filename);
    }

    /**
     * Get comprehensive statistics for bank sampah
     */
    private function getComprehensiveBankSampahStats($bankSampah, $period = null, $startDate = null, $endDate = null)
    {
        $bankSampahIds = $bankSampah->pluck('id')->toArray();

        // Build setoran query with period filters - ONLY COMPLETED SETORAN
        $setoranQuery = Setoran::whereIn('bank_sampah_id', $bankSampahIds)
            ->where('status', 'selesai');

        // Apply period filters
        if ($period && $startDate && $endDate) {
            $setoranQuery->whereBetween('created_at', [$startDate, $endDate]);
        } elseif ($period) {
            $this->applyDateFilter($setoranQuery, $period);
        }

        // Get setoran data
        $setorans = $setoranQuery->get();

        // Calculate statistics from items_json
        $bankStats = [];
        $totalSetoran = $setorans->count();
        $totalSampahKg = 0;
        $totalSampahUnit = 0;
        $totalPoint = 0;
        $totalPelangganUnik = $setorans->unique('user_id')->count();

        foreach ($setorans as $setoran) {
            $bankId = $setoran->bank_sampah_id;

            if (! isset($bankStats[$bankId])) {
                $bankStats[$bankId] = [
                    'setoran' => 0,
                    'sampah_kg' => 0,
                    'sampah_unit' => 0,
                    'point' => 0,
                    'pelanggan' => 0,
                ];
            }

            $bankStats[$bankId]['setoran']++;

            // Calculate from items_json
            $items = [];
            if ($setoran->items_json) {
                $rawItemsJson = $setoran->getRawOriginal('items_json');
                if ($rawItemsJson) {
                    $cleanJson = trim(stripslashes($rawItemsJson), '"');
                    $items = json_decode($cleanJson, true) ?? [];
                }
            }
            foreach ($items as $item) {
                $berat = $item['aktual_berat'] ?? $item['estimasi_berat'] ?? 0;
                $satuan = isset($item['sampah_satuan']) ? strtoupper($item['sampah_satuan']) : 'KG';
                if ($satuan === 'KG') {
                    $totalSampahKg += $berat;
                    $bankStats[$bankId]['sampah_kg'] += $berat;
                } elseif ($satuan === 'UNIT') {
                    $totalSampahUnit += $berat;
                    $bankStats[$bankId]['sampah_unit'] += $berat;
                }
            }

            // Get points from related points table
            $setoranPoints = \App\Models\Point::where('setoran_id', $setoran->id)->sum('jumlah_point');
            $totalPoint += $setoranPoints;
            $bankStats[$bankId]['point'] += $setoranPoints;
        }

        // Count unique customers per bank
        foreach ($setorans->groupBy('bank_sampah_id') as $bankId => $bankSetorans) {
            $bankStats[$bankId]['pelanggan'] = $bankSetorans->unique('user_id')->count();
        }

        // Get admin statistics (not affected by period)
        $adminStats = Admin::whereIn('id_bank_sampah', $bankSampahIds)
            ->selectRaw('id_bank_sampah, COUNT(*) as total_admin')
            ->groupBy('id_bank_sampah')
            ->get()
            ->keyBy('id_bank_sampah');

        $totalAdmin = $adminStats->sum('total_admin');

        // Calculate total pembelian from completed setoran with period filter
        $pembelianQuery = Setoran::whereIn('bank_sampah_id', $bankSampahIds)
            ->where('status', 'selesai');

        if ($period && $startDate && $endDate) {
            $pembelianQuery->whereBetween('created_at', [$startDate, $endDate]);
        } elseif ($period) {
            $this->applyDateFilter($pembelianQuery, $period);
        }

        $totalPembelian = $pembelianQuery->sum('aktual_total');

        // Prepare bank sampah summary
        $bankSampahSummary = [];
        foreach ($bankSampah as $bank) {
            $bankSampahSummary[] = [
                'nama' => $bank->nama_bank_sampah,
                'setoran' => $bankStats[$bank->id]['setoran'] ?? 0,
                'sampah_kg' => $bankStats[$bank->id]['sampah_kg'] ?? 0,
                'sampah_unit' => $bankStats[$bank->id]['sampah_unit'] ?? 0,
                'point' => $bankStats[$bank->id]['point'] ?? 0,
                'pelanggan' => $bankStats[$bank->id]['pelanggan'] ?? 0,
            ];
        }

        return [
            'totalBankSampah' => $bankSampah->count(),
            'totalSetoran' => $totalSetoran,
            'totalSampahKg' => $totalSampahKg,
            'totalSampahUnit' => $totalSampahUnit,
            'totalPoint' => $totalPoint,
            'totalPelangganUnik' => $totalPelangganUnik,
            'totalPembelian' => $totalPembelian,
            'totalAdmin' => $totalAdmin,
            'bankSampahSummary' => $bankSampahSummary,
            'bankStats' => $bankStats,
        ];
    }

    /**
     * Apply date filter based on period
     */
    private function applyDateFilter($query, $period)
    {
        switch ($period) {
            case 'hari_ini':
                $query->whereDate('created_at', today());
                break;
            case 'kemarin':
                $query->whereDate('created_at', today()->subDay());
                break;
            case 'minggu_ini':
                $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                break;
            case 'bulan_ini':
                $query->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()]);
                break;
            case 'tahun_ini':
                $query->whereBetween('created_at', [now()->startOfYear(), now()->endOfYear()]);
                break;
            case 'semua':
                // No filter applied
                break;
        }
    }

    /**
     * Apply export filters to query
     */
    private function applyExportFilters($query, $request)
    {
        $period = $request->input('period', 'all');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Apply period filter
        if ($period !== 'all') {
            switch ($period) {
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
                    if ($startDate && $endDate) {
                        $query->whereBetween('created_at', [$startDate.' 00:00:00', $endDate.' 23:59:59']);
                    }
                    break;
            }
        }
    }
}
