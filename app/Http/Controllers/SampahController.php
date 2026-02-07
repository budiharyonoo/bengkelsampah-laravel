<?php

namespace App\Http\Controllers;

use App\Models\BankSampah;
use App\Models\Price;
use App\Models\Sampah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SampahController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Sampah::with('prices.bankSampah');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('nama', 'like', '%'.$search.'%');
        }

        $sampah = $query->orderBy('created_at', 'desc')->paginate(10);

        // Return JSON for AJAX requests
        if ($request->ajax()) {
            return response()->json([
                'sampah' => $sampah,
            ]);
        }

        return view('dashboard-sampah', compact('sampah'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $bankSampah = BankSampah::all();

        return view('dashboard-sampah-create', compact('bankSampah'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'nama' => 'required|string|max:255',
                'deskripsi' => 'nullable|string',
                'gambar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
                'satuan' => 'required|in:kg,unit',
                'first_price' => 'required|numeric|min:0',
            ]);

            $data = [
                'nama' => $request->nama,
                'deskripsi' => $request->deskripsi,
                'satuan' => $request->satuan,
            ];

            // Handle image upload
            if ($request->hasFile('gambar')) {
                $file = $request->file('gambar');
                $filename = time().'_'.Str::random(10).'_'.Str::slug($request->nama).'.'.$file->getClientOriginalExtension();

                // Create upload directory if it doesn't exist
                $uploadPath = base_path('../uploads/sampah');
                if (! file_exists($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }

                // Move file to the uploads directory
                if ($file->move($uploadPath, $filename)) {
                    $data['gambar'] = env('APP_URL').'/uploads/sampah/'.$filename;

                    // Debug log
                    \Log::info('Image uploaded successfully:', [
                        'filename' => $filename,
                        'path' => $uploadPath.'/'.$filename,
                        'url' => $data['gambar'],
                        'file_exists' => file_exists($uploadPath.'/'.$filename),
                    ]);
                } else {
                    throw new \Exception('Failed to upload file');
                }
            }

            $sampah = Sampah::create($data);

            // Create price entries for all bank sampah
            $bankSampah = BankSampah::all();
            foreach ($bankSampah as $bank) {
                Price::create([
                    'sampah_id' => $sampah->id,
                    'bank_sampah_id' => $bank->id,
                    'harga' => $request->first_price,
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Sampah berhasil dibuat',
                'data' => $sampah,
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in SampahController@store: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat sampah: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $sampah = Sampah::with(['prices.bankSampah'])->findOrFail($id);

        return view('dashboard-sampah-detail', compact('sampah'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $sampah = Sampah::with('prices.bankSampah')->findOrFail($id);

        return view('dashboard-sampah-edit', compact('sampah'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $sampah = Sampah::findOrFail($id);

            $request->validate([
                'nama' => 'required|string|max:255',
                'deskripsi' => 'nullable|string',
                'gambar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
                'satuan' => 'required|in:kg,unit',
            ]);

            $data = [
                'nama' => $request->nama,
                'deskripsi' => $request->deskripsi,
                'satuan' => $request->satuan,
            ];

            // Handle image upload
            if ($request->hasFile('gambar')) {
                // Delete old image if exists
                if ($sampah->gambar) {
                    $oldPath = str_replace(env('APP_URL').'/uploads/', '', $sampah->gambar);
                    $fullOldPath = base_path('../uploads/'.$oldPath);
                    if (file_exists($fullOldPath)) {
                        unlink($fullOldPath);
                    }
                }

                $file = $request->file('gambar');
                $filename = time().'_'.Str::random(10).'_'.Str::slug($request->nama).'.'.$file->getClientOriginalExtension();

                // Create upload directory if it doesn't exist
                $uploadPath = base_path('../uploads/sampah');
                if (! file_exists($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }

                // Move file to the uploads directory
                if ($file->move($uploadPath, $filename)) {
                    $data['gambar'] = env('APP_URL').'/uploads/sampah/'.$filename;

                    // Debug log
                    \Log::info('Image updated successfully:', [
                        'filename' => $filename,
                        'path' => $uploadPath.'/'.$filename,
                        'url' => $data['gambar'],
                        'file_exists' => file_exists($uploadPath.'/'.$filename),
                    ]);
                } else {
                    throw new \Exception('Failed to upload file');
                }
            }

            $sampah->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Sampah berhasil diupdate',
                'data' => $sampah,
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in SampahController@update: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate sampah: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update harga cabang for a specific sampah.
     */
    public function updateHargaCabang(Request $request, $id)
    {
        $admin = auth('admin')->user();
        if (! $admin || $admin->role === 'admin' || ! $admin->id_bank_sampah) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
        }
        $request->validate([
            'harga' => 'required|numeric|min:0',
        ]);
        $price = \App\Models\Price::where('sampah_id', $id)
            ->where('bank_sampah_id', $admin->id_bank_sampah)
            ->first();
        if (! $price) {
            return response()->json(['success' => false, 'message' => 'Data harga tidak ditemukan'], 404);
        }
        $price->harga = $request->harga;
        $price->save();

        return response()->json(['success' => true, 'message' => 'Harga berhasil diupdate']);
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
                $sampahItems = Sampah::whereIn('id', $ids)->get();

                foreach ($sampahItems as $sampah) {
                    // Delete image if exists
                    if ($sampah->gambar) {
                        $oldPath = str_replace(env('APP_URL').'/uploads/', '', $sampah->gambar);
                        $fullOldPath = base_path('../uploads/'.$oldPath);
                        if (file_exists($fullOldPath)) {
                            unlink($fullOldPath);
                        }
                    }
                }

                // Delete all related prices first
                Price::whereIn('sampah_id', $ids)->delete();

                Sampah::whereIn('id', $ids)->delete();

                return response()->json([
                    'success' => true,
                    'message' => count($ids).' sampah berhasil dihapus',
                ]);
            }

            // Handle single delete
            $sampah = Sampah::findOrFail($id);

            // Delete image if exists
            if ($sampah->gambar) {
                $oldPath = str_replace(env('APP_URL').'/uploads/', '', $sampah->gambar);
                $fullOldPath = base_path('../uploads/'.$oldPath);
                if (file_exists($fullOldPath)) {
                    unlink($fullOldPath);
                }
            }

            // Delete all related prices first
            Price::where('sampah_id', $id)->delete();

            $sampah->delete();

            return response()->json([
                'success' => true,
                'message' => 'Sampah berhasil dihapus',
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in SampahController@destroy: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus sampah: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Export sampah data to Excel
     */
    public function exportExcel(Request $request)
    {
        try {
            $sampah = Sampah::with('prices')->orderBy('created_at', 'desc')->get();
            $banks = BankSampah::orderBy('nama_bank_sampah')->get();

            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet;
            $sheet = $spreadsheet->getActiveSheet();

            $spreadsheet->getProperties()
                ->setCreator('Bengkel Sampah Admin')
                ->setLastModifiedBy('Bengkel Sampah Admin')
                ->setTitle('Laporan Data Sampah')
                ->setSubject('Laporan Data Sampah')
                ->setDescription('Laporan data sampah Bengkel Sampah')
                ->setKeywords('sampah, laporan, bengkel sampah')
                ->setCategory('Laporan');

            $headerStyle = [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '39746E'],
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['rgb' => '000000'],
                    ],
                ],
            ];

            // Calculate total columns
            $totalCols = 5 + $banks->count() + 1; // 5 base columns + bank columns + 1 for date
            $lastCol = chr(ord('A') + $totalCols - 1);

            $sheet->setCellValue('A1', 'LAPORAN DATA SAMPAH BENGKEL SAMPAH');
            $sheet->mergeCells('A1:'.$lastCol.'1');
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
            $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            $sheet->setCellValue('A2', 'Tanggal Export: '.now()->format('d F Y H:i:s'));
            $sheet->mergeCells('A2:'.$lastCol.'2');
            $sheet->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            // Set headers
            $colIndex = 0;
            $headers = ['No', 'ID Sampah', 'Nama Sampah', 'Deskripsi', 'Satuan'];
            foreach ($headers as $header) {
                $col = chr(ord('A') + $colIndex);
                $sheet->setCellValue($col.'4', $header);
                $colIndex++;
            }

            // Dynamic bank columns
            foreach ($banks as $bank) {
                $col = chr(ord('A') + $colIndex);
                $sheet->setCellValue($col.'4', $bank->nama_bank_sampah);
                $colIndex++;
            }

            $col = chr(ord('A') + $colIndex);
            $sheet->setCellValue($col.'4', 'Tanggal Dibuat');
            $sheet->getStyle('A4:'.$col.'4')->applyFromArray($headerStyle);

            // Set data
            $row = 5;
            foreach ($sampah as $index => $item) {
                $colIndex = 0;

                // No
                $col = chr(ord('A') + $colIndex);
                $sheet->setCellValue($col.$row, $index + 1);
                $colIndex++;

                // ID Sampah
                $col = chr(ord('A') + $colIndex);
                $sheet->setCellValue($col.$row, $item->id);
                $colIndex++;

                // Nama Sampah
                $col = chr(ord('A') + $colIndex);
                $sheet->setCellValue($col.$row, $item->nama);
                $colIndex++;

                // Deskripsi
                $col = chr(ord('A') + $colIndex);
                $sheet->setCellValue($col.$row, $item->deskripsi ?? '-');
                $colIndex++;

                // Satuan
                $col = chr(ord('A') + $colIndex);
                $sheet->setCellValue($col.$row, strtoupper($item->satuan));
                $colIndex++;

                // Harga per bank
                foreach ($banks as $bank) {
                    $price = $item->prices->where('bank_sampah_id', $bank->id)->first();
                    $col = chr(ord('A') + $colIndex);
                    $sheet->setCellValue($col.$row, $price ? $price->harga : '-');
                    $colIndex++;
                }

                // Tanggal Dibuat
                $col = chr(ord('A') + $colIndex);
                $sheet->setCellValue($col.$row, $item->created_at->format('d/m/Y H:i'));

                $row++;
            }

            // Auto-size columns
            for ($i = 0; $i < $totalCols; $i++) {
                $col = chr(ord('A') + $i);
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $filename = 'sampah_export_'.now()->format('Y-m-d_H-i-s').'.xlsx';
            $tempFile = storage_path('app/temp/'.$filename);
            if (! file_exists(dirname($tempFile))) {
                mkdir(dirname($tempFile), 0755, true);
            }
            $writer->save($tempFile);

            return response()->download($tempFile, $filename)->deleteFileAfterSend();
        } catch (\Exception $e) {
            \Log::error('Error in SampahController@exportExcel: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal export Excel: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Export sampah data to CSV
     */
    public function exportCsv(Request $request)
    {
        try {
            $sampah = Sampah::with('prices')->orderBy('created_at', 'desc')->get();
            $banks = BankSampah::orderBy('nama_bank_sampah')->get();
            $filename = 'sampah_export_'.now()->format('Y-m-d_H-i-s').'.csv';
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            ];
            $callback = function () use ($sampah, $banks) {
                $file = fopen('php://output', 'w');
                // Header row
                $headerRow = ['No', 'ID Sampah', 'Nama Sampah', 'Deskripsi', 'Satuan'];
                foreach ($banks as $bank) {
                    $headerRow[] = $bank->nama_bank_sampah;
                }
                $headerRow[] = 'Tanggal Dibuat';
                fputcsv($file, $headerRow);
                // Data rows
                foreach ($sampah as $index => $item) {
                    $row = [
                        $index + 1,
                        $item->id,
                        $item->nama,
                        $item->deskripsi ?? '-',
                        strtoupper($item->satuan),
                    ];
                    foreach ($banks as $bank) {
                        $price = $item->prices->where('bank_sampah_id', $bank->id)->first();
                        $row[] = $price ? $price->harga : '-';
                    }
                    $row[] = $item->created_at->format('d/m/Y H:i');
                    fputcsv($file, $row);
                }
                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        } catch (\Exception $e) {
            \Log::error('Error in SampahController@exportCsv: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal export CSV: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Export sampah data to PDF
     */
    public function exportPdf(Request $request)
    {
        try {
            $sampah = Sampah::with('prices')->orderBy('created_at', 'desc')->get();
            $banks = BankSampah::orderBy('nama_bank_sampah')->get();
            $data = [
                'sampah' => $sampah,
                'banks' => $banks,
                'exportDate' => now()->format('d F Y H:i:s'),
                'totalData' => $sampah->count(),
            ];
            $pdf = \PDF::loadView('pdf.sampah-report', $data);
            $pdf->setPaper('A4', 'landscape');
            $filename = 'sampah_export_'.now()->format('Y-m-d_H-i-s').'.pdf';

            return $pdf->download($filename);
        } catch (\Exception $e) {
            \Log::error('Error in SampahController@exportPdf: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal export PDF: '.$e->getMessage(),
            ], 500);
        }
    }
}
