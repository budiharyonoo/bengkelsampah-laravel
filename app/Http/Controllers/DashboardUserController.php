<?php

namespace App\Http\Controllers;

use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class DashboardUserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('identifier', 'like', "%{$search}%");
            });
        }

        // Sort functionality
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $users = $query->paginate(10);

        return view('dashboard-user', compact('users'));
    }

    public function show($id)
    {
        $user = User::with(['points' => function ($query) {
            $query->orderBy('created_at', 'desc')->limit(10);
        }, 'setorans' => function ($query) {
            $query->orderBy('created_at', 'desc')->limit(10);
        }, 'addresses'])->findOrFail($id);

        return view('dashboard-user-detail', compact('user'));
    }

    public function searchUser(Request $request)
    {
        $query = $request->get('query');

        $users = User::where('name', 'like', "%{$query}%")
            ->orWhere('identifier', 'like', "%{$query}%")
            ->select('id', 'name', 'identifier', 'poin', 'xp', 'setor', 'sampah')
            ->get();

        return response()->json($users);
    }

    public function getUserInfo($id)
    {
        $user = User::select('id', 'name', 'identifier', 'poin', 'xp', 'setor', 'sampah')
            ->find($id);

        if (! $user) {
            return response()->json(['error' => 'User tidak ditemukan'], 404);
        }

        return response()->json($user);
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);

        return view('dashboard-user-edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'identifier' => ['required', 'string', Rule::unique('users')->ignore($id), 'max:255'],
            'poin' => 'required',
            'xp' => 'nullable|numeric|min:0',
            'setor' => 'nullable|numeric|min:0',
            'sampah' => 'required',
        ]);

        // Process poin input
        $poinInput = str_replace([' ', '.', ','], ['', '', '.'], $request->poin); // Hapus spasi, titik ribuan, koma jadi titik
        if (! is_numeric($poinInput)) {
            return back()->withErrors(['poin' => 'Format poin tidak valid'])->withInput();
        }
        $poin = floatval($poinInput);

        // Process xp input (integer only)
        $xp = null;
        if ($request->filled('xp')) {
            $xpInput = str_replace([' ', '.', ','], ['', '', ''], $request->xp);
            if (! is_numeric($xpInput) || strpos($xpInput, '.') !== false) {
                return back()->withErrors(['xp' => 'Format XP tidak valid (harus angka bulat)'])->withInput();
            }
            $xp = intval($xpInput);
        }

        // Process setor input (integer only)
        $setor = null;
        if ($request->filled('setor')) {
            $setorInput = str_replace([' ', '.', ','], ['', '', ''], $request->setor);
            if (! is_numeric($setorInput) || strpos($setorInput, '.') !== false) {
                return back()->withErrors(['setor' => 'Format total setoran tidak valid (harus angka bulat)'])->withInput();
            }
            $setor = intval($setorInput);
        }

        // Process sampah input (follows poin format - can be decimal)
        $sampah = null;
        if ($request->filled('sampah')) {
            $sampahInput = str_replace([' ', '.', ','], ['', '', '.'], $request->sampah);
            if (! is_numeric($sampahInput)) {
                return back()->withErrors(['sampah' => 'Format total sampah tidak valid'])->withInput();
            }
            $sampah = floatval($sampahInput);
        }

        $updateData = [
            'name' => $request->name,
            'identifier' => $request->identifier,
            'poin' => $poin,
        ];

        // Only update if values are provided
        if ($xp !== null) {
            $updateData['xp'] = $xp;
        }
        if ($setor !== null) {
            $updateData['setor'] = $setor;
        }
        if ($sampah !== null) {
            $updateData['sampah'] = $sampah;
        }

        $user->update($updateData);

        return redirect()->route('dashboard.user')->with('success', 'Data user berhasil diperbarui');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('dashboard.user')->with('success', 'User berhasil dihapus');
    }

    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        $deletedCount = 0;
        $errorCount = 0;
        $errorMessages = [];

        try {
            foreach ($request->user_ids as $userId) {
                $user = User::find($userId);
                if (! $user) {
                    $errorCount++;

                    continue;
                }
                $user->delete();
                $deletedCount++;
            }

            $message = "Berhasil menghapus {$deletedCount} user";
            if ($errorCount > 0) {
                $message .= ". {$errorCount} user gagal dihapus (tidak ditemukan)";
            }

            // Always return JSON for AJAX requests
            return response()->json([
                'success' => true,
                'message' => $message,
                'deleted_count' => $deletedCount,
                'error_count' => $errorCount,
                'error_messages' => $errorMessages,
            ]);

        } catch (\Exception $e) {
            \Log::error('Bulk delete error: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus user: '.$e->getMessage(),
            ], 500);
        }
    }

    public function exportExcel(Request $request)
    {
        try {
            $users = User::with('addresses')->orderBy('created_at', 'desc')->get();

            // Generate Excel file
            return $this->generateExcelFile($users);

        } catch (\Exception $e) {
            \Log::error('Error in DashboardUserController@exportExcel: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal export Excel: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Generate Excel file from user data
     */
    private function generateExcelFile($users)
    {
        // Create new Spreadsheet
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();

        // Set document properties
        $spreadsheet->getProperties()
            ->setCreator('Bengkel Sampah Admin')
            ->setLastModifiedBy('Bengkel Sampah Admin')
            ->setTitle('Laporan Data User')
            ->setSubject('Laporan Data User')
            ->setDescription('Laporan data user Bengkel Sampah')
            ->setKeywords('user, laporan, bengkel sampah')
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
        $sheet->setCellValue('A1', 'LAPORAN DATA USER BENGKEL SAMPAH');
        $sheet->mergeCells('A1:H1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Set subtitle
        $sheet->setCellValue('A2', 'Tanggal Export: '.now()->format('d F Y H:i:s'));
        $sheet->mergeCells('A2:H2');
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Set headers
        $headers = [
            'No', 'ID', 'Nama Lengkap', 'Identifier', 'Point', 'XP',
            'Jumlah Setoran', 'Alamat',
        ];

        $col = 'A';
        $row = 4;
        foreach ($headers as $header) {
            $sheet->setCellValue($col.$row, $header);
            $sheet->getStyle($col.$row)->applyFromArray($headerStyle);
            $col++;
        }

        // Set data
        $row = 5;
        foreach ($users as $index => $user) {
            // Get user address
            $userAddress = '-';
            if ($user->addresses && $user->addresses->count() > 0) {
                // Try to find default address first
                $defaultAddress = $user->addresses->where('is_default', true)->first();
                if ($defaultAddress) {
                    $userAddress = $defaultAddress->label_alamat.' ('.$defaultAddress->nomor_handphone.') '.
                                  $defaultAddress->detail_lain.', '.$defaultAddress->kecamatan.', '.
                                  $defaultAddress->kota_kabupaten.', '.$defaultAddress->provinsi.' '.
                                  $defaultAddress->kode_pos;
                } else {
                    // Use first address if no default
                    $firstAddress = $user->addresses->first();
                    $userAddress = $firstAddress->label_alamat.' ('.$firstAddress->nomor_handphone.') '.
                                  $firstAddress->detail_lain.', '.$firstAddress->kecamatan.', '.
                                  $firstAddress->kota_kabupaten.', '.$firstAddress->provinsi.' '.
                                  $firstAddress->kode_pos;
                }
            }

            $sheet->setCellValue('A'.$row, $index + 1);
            $sheet->setCellValue('B'.$row, $user->id);
            $sheet->setCellValue('C'.$row, $user->name);
            $sheet->setCellValue('D'.$row, $user->identifier);
            $sheet->setCellValue('E'.$row, number_format($user->poin ?? 0, 0));
            $sheet->setCellValue('F'.$row, number_format($user->xp ?? 0, 0));
            $sheet->setCellValue('G'.$row, $user->setor ?? 0);
            $sheet->setCellValue('H'.$row, $userAddress);

            // Center align numeric columns
            $sheet->getStyle('A'.$row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('B'.$row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('E'.$row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('F'.$row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('G'.$row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $row++;
        }

        // Auto-size columns
        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Add borders to data
        $dataRange = 'A4:H'.($row - 1);
        $sheet->getStyle($dataRange)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        // Create the Excel file
        $writer = new Xlsx($spreadsheet);

        // Set filename
        $filename = 'user_export_all_'.now()->format('Y-m-d_H-i-s').'.xlsx';

        // Save to temporary file
        $tempFile = storage_path('app/temp/'.$filename);
        if (! file_exists(dirname($tempFile))) {
            mkdir(dirname($tempFile), 0755, true);
        }
        $writer->save($tempFile);

        // Return file for download
        return response()->download($tempFile, $filename)->deleteFileAfterSend();
    }

    public function exportCsv(Request $request)
    {
        try {
            $users = User::with('addresses')->orderBy('created_at', 'desc')->get();

            // Generate CSV file
            return $this->generateCsvFile($users);

        } catch (\Exception $e) {
            \Log::error('Error in DashboardUserController@exportCsv: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal export CSV: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Generate CSV file from user data
     */
    private function generateCsvFile($users)
    {
        // Set headers for CSV download
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="user_export_all_'.now()->format('Y-m-d_H-i-s').'.csv"',
        ];

        // Create CSV content
        $callback = function () use ($users) {
            $file = fopen('php://output', 'w');

            // Add BOM for UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // Headers
            fputcsv($file, [
                'No', 'ID', 'Nama Lengkap', 'Identifier', 'Point', 'XP',
                'Jumlah Setoran', 'Alamat',
            ]);

            // Data
            foreach ($users as $index => $user) {
                // Get user address
                $userAddress = '-';
                if ($user->addresses && $user->addresses->count() > 0) {
                    // Try to find default address first
                    $defaultAddress = $user->addresses->where('is_default', true)->first();
                    if ($defaultAddress) {
                        $userAddress = $defaultAddress->label_alamat.' ('.$defaultAddress->nomor_handphone.') '.
                                      $defaultAddress->detail_lain.', '.$defaultAddress->kecamatan.', '.
                                      $defaultAddress->kota_kabupaten.', '.$defaultAddress->provinsi.' '.
                                      $defaultAddress->kode_pos;
                    } else {
                        // Use first address if no default
                        $firstAddress = $user->addresses->first();
                        $userAddress = $firstAddress->label_alamat.' ('.$firstAddress->nomor_handphone.') '.
                                      $firstAddress->detail_lain.', '.$firstAddress->kecamatan.', '.
                                      $firstAddress->kota_kabupaten.', '.$firstAddress->provinsi.' '.
                                      $firstAddress->kode_pos;
                    }
                }

                fputcsv($file, [
                    $index + 1,
                    $user->id,
                    $user->name,
                    $user->identifier,
                    number_format($user->poin ?? 0, 0),
                    number_format($user->xp ?? 0, 0),
                    $user->setor ?? 0,
                    $userAddress,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportPdf(Request $request)
    {
        try {
            $users = User::with('addresses')->orderBy('created_at', 'desc')->get();

            // Generate PDF
            return $this->generatePdfFile($users);

        } catch (\Exception $e) {
            \Log::error('Error in DashboardUserController@exportPdf: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal export PDF: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Generate PDF file from user data
     */
    private function generatePdfFile($users)
    {
        // Get comprehensive statistics
        $stats = $this->getComprehensiveUserStats($users);

        // Generate PDF using DomPDF
        $pdf = \PDF::loadView('pdf.user-report', [
            'users' => $users,
            'totalUsers' => $stats['totalUsers'],
            'totalSetoran' => $stats['totalSetoran'],
            'totalSampah' => $stats['totalSampah'],
            'totalPoint' => $stats['totalPoint'],
        ]);

        // Set paper to A4 landscape
        $pdf->setPaper('A4', 'landscape');

        // Set filename
        $filename = 'user_export_all_'.now()->format('Y-m-d_H-i-s').'.pdf';

        // Return PDF for download
        return $pdf->download($filename);
    }

    /**
     * Get comprehensive statistics for users
     */
    private function getComprehensiveUserStats($users)
    {
        $userIds = $users->pluck('id')->toArray();

        // Get setoran data (only completed)
        $setorans = \App\Models\Setoran::whereIn('user_id', $userIds)
            ->where('status', 'selesai')
            ->get();

        // Calculate statistics
        $totalSetoran = $setorans->count();
        $totalSampah = 0;
        $totalPoint = 0;

        foreach ($users as $user) {
            // Calculate from user's sampah field
            if ($user->sampah) {
                $sampahData = json_decode($user->sampah, true);
                if (is_array($sampahData)) {
                    foreach ($sampahData as $item) {
                        $berat = $item['aktual_berat'] ?? $item['estimasi_berat'] ?? 0;
                        $totalSampah += $berat;
                    }
                }
            }

            // Get points from user's poin field
            $totalPoint += ($user->poin ?? 0);
        }

        return [
            'totalUsers' => $users->count(),
            'totalSetoran' => $totalSetoran,
            'totalSampah' => $totalSampah,
            'totalPoint' => $totalPoint,
        ];
    }
}
