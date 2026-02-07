<?php

namespace App\Http\Controllers;

use App\Models\BankSampah;
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
        $query = User::query()->with('bankSampah');

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
        $user = User::with(['bankSampah', 'points' => function ($query) {
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
        $user = User::with('bankSampah')->findOrFail($id);
        $bankSampahList = BankSampah::orderBy('nama_bank_sampah')->get();

        return view('dashboard-user-edit', compact('user', 'bankSampahList'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'identifier' => ['required', 'string', Rule::unique('users')->ignore($id), 'max:255'],
            'user_type' => ['required', 'integer', 'min:-1'],
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
            'user_type' => $request->user_type,
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
            $users = User::with(['addresses', 'bankSampah'])->orderBy('created_at', 'desc')->get();

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
        $sheet->mergeCells('A1:I1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Set subtitle
        $sheet->setCellValue('A2', 'Tanggal Export: '.now()->format('d F Y H:i:s'));
        $sheet->mergeCells('A2:I2');
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Set headers
        $headers = [
            'No', 'ID', 'Nama Lengkap', 'Identifier', 'Jenis Nasabah', 'Point', 'XP',
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
            $sheet->setCellValue('E'.$row, $user->jenis_nasabah);
            $sheet->setCellValue('F'.$row, number_format($user->poin ?? 0, 0));
            $sheet->setCellValue('G'.$row, number_format($user->xp ?? 0, 0));
            $sheet->setCellValue('H'.$row, $user->setor ?? 0);
            $sheet->setCellValue('I'.$row, $userAddress);

            // Center align numeric columns
            $sheet->getStyle('A'.$row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('B'.$row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('F'.$row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('G'.$row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('H'.$row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $row++;
        }

        // Auto-size columns
        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Add borders to data
        $dataRange = 'A4:I'.($row - 1);
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
            $users = User::with(['addresses', 'bankSampah'])->orderBy('created_at', 'desc')->get();

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
                'No', 'ID', 'Nama Lengkap', 'Identifier', 'Jenis Nasabah', 'Point', 'XP',
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
                    $user->jenis_nasabah,
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

    /**
     * Export single user detail to Excel
     */
    public function exportUserDetailExcel($id)
    {
        try {
            $user = User::with(['bankSampah', 'addresses', 'points' => function ($query) {
                $query->orderBy('created_at', 'desc');
            }, 'setorans' => function ($query) {
                $query->with('bankSampah')->orderBy('created_at', 'desc');
            }])->findOrFail($id);

            return $this->generateUserDetailExcelFile($user);

        } catch (\Exception $e) {
            \Log::error('Error in DashboardUserController@exportUserDetailExcel: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal export Excel: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Generate Excel file for single user detail
     */
    private function generateUserDetailExcelFile($user)
    {
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();

        // Set document properties
        $spreadsheet->getProperties()
            ->setCreator('Bengkel Sampah Admin')
            ->setLastModifiedBy('Bengkel Sampah Admin')
            ->setTitle('Detail User - '.$user->name)
            ->setSubject('Detail User')
            ->setDescription('Detail data user Bengkel Sampah')
            ->setKeywords('user, detail, bengkel sampah')
            ->setCategory('Laporan');

        // Styles
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '39746E']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]],
        ];

        $sectionHeaderStyle = [
            'font' => ['bold' => true, 'size' => 12],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E8F5F3']],
        ];

        $row = 1;

        // Title
        $sheet->setCellValue('A'.$row, 'DETAIL USER - '.strtoupper($user->name));
        $sheet->mergeCells('A'.$row.':F'.$row);
        $sheet->getStyle('A'.$row)->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A'.$row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $row++;

        // Export date
        $sheet->setCellValue('A'.$row, 'Tanggal Export: '.now()->format('d F Y H:i:s'));
        $sheet->mergeCells('A'.$row.':F'.$row);
        $sheet->getStyle('A'.$row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $row += 2;

        // Section 1: User Information
        $sheet->setCellValue('A'.$row, 'INFORMASI USER');
        $sheet->mergeCells('A'.$row.':F'.$row);
        $sheet->getStyle('A'.$row)->applyFromArray($sectionHeaderStyle);
        $row++;

        $userInfo = [
            ['Identifier', $user->identifier],
            ['Nama', $user->name],
            ['Jenis Nasabah', $user->jenis_nasabah],
            ['No. Telepon', $user->phone ?? '-'],
            ['Tanggal Registrasi', $user->created_at->format('d F Y H:i')],
            ['Bank Sampah', $user->bankSampah->nama_bank_sampah ?? '-'],
        ];

        foreach ($userInfo as $info) {
            $sheet->setCellValue('A'.$row, $info[0]);
            $sheet->setCellValue('B'.$row, $info[1]);
            $sheet->getStyle('A'.$row)->getFont()->setBold(true);
            $row++;
        }
        $row++;

        // Section 2: Statistics
        $sheet->setCellValue('A'.$row, 'STATISTIK');
        $sheet->mergeCells('A'.$row.':F'.$row);
        $sheet->getStyle('A'.$row)->applyFromArray($sectionHeaderStyle);
        $row++;

        $statsHeaders = ['Total Setoran', 'Total Poin', 'Total XP', 'Total Sampah (kg)', 'Total Sampah (unit)'];
        $statsValues = [
            number_format($user->setor ?? 0),
            number_format($user->poin ?? 0, 2, ',', '.'),
            number_format($user->xp ?? 0),
            number_format($user->sampah ?? 0, 1),
            number_format($user->sampah_unit ?? 0),
        ];

        $col = 'A';
        foreach ($statsHeaders as $header) {
            $sheet->setCellValue($col.$row, $header);
            $sheet->getStyle($col.$row)->applyFromArray($headerStyle);
            $col++;
        }
        $row++;

        $col = 'A';
        foreach ($statsValues as $value) {
            $sheet->setCellValue($col.$row, $value);
            $sheet->getStyle($col.$row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $col++;
        }
        $row += 2;

        // Section 3: Setorans
        $sheet->setCellValue('A'.$row, 'RIWAYAT SETORAN');
        $sheet->mergeCells('A'.$row.':F'.$row);
        $sheet->getStyle('A'.$row)->applyFromArray($sectionHeaderStyle);
        $row++;

        if ($user->setorans && $user->setorans->count() > 0) {
            $setoranHeaders = ['No', 'Bank Sampah', 'Tanggal', 'Jumlah (Rp)', 'Status'];
            $col = 'A';
            foreach ($setoranHeaders as $header) {
                $sheet->setCellValue($col.$row, $header);
                $sheet->getStyle($col.$row)->applyFromArray($headerStyle);
                $col++;
            }
            $row++;

            foreach ($user->setorans as $index => $setoran) {
                $sheet->setCellValue('A'.$row, $index + 1);
                $sheet->setCellValue('B'.$row, $setoran->bankSampah->nama_bank_sampah ?? '-');
                $sheet->setCellValue('C'.$row, $setoran->created_at->format('d/m/Y H:i'));
                $sheet->setCellValue('D'.$row, 'Rp '.number_format($setoran->total_harga ?? 0, 0, ',', '.'));
                $sheet->setCellValue('E'.$row, ucfirst($setoran->status));
                $sheet->getStyle('A'.$row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $row++;
            }
        } else {
            $sheet->setCellValue('A'.$row, 'Tidak ada data setoran');
            $sheet->mergeCells('A'.$row.':F'.$row);
            $row++;
        }
        $row++;

        // Section 4: Points History
        $sheet->setCellValue('A'.$row, 'RIWAYAT POIN');
        $sheet->mergeCells('A'.$row.':F'.$row);
        $sheet->getStyle('A'.$row)->applyFromArray($sectionHeaderStyle);
        $row++;

        if ($user->points && $user->points->count() > 0) {
            $pointHeaders = ['No', 'Keterangan', 'Tanggal', 'Jumlah', 'Tipe'];
            $col = 'A';
            foreach ($pointHeaders as $header) {
                $sheet->setCellValue($col.$row, $header);
                $sheet->getStyle($col.$row)->applyFromArray($headerStyle);
                $col++;
            }
            $row++;

            foreach ($user->points as $index => $point) {
                $prefix = $point->tipe === 'setor' ? '+' : '-';
                $sheet->setCellValue('A'.$row, $index + 1);
                $sheet->setCellValue('B'.$row, $point->keterangan ?? '-');
                $sheet->setCellValue('C'.$row, $point->created_at->format('d/m/Y H:i'));
                $sheet->setCellValue('D'.$row, $prefix.number_format($point->jumlah ?? 0, 2, ',', '.'));
                $sheet->setCellValue('E'.$row, ucfirst($point->tipe));
                $sheet->getStyle('A'.$row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $row++;
            }
        } else {
            $sheet->setCellValue('A'.$row, 'Tidak ada riwayat poin');
            $sheet->mergeCells('A'.$row.':F'.$row);
            $row++;
        }
        $row++;

        // Section 5: Addresses
        $sheet->setCellValue('A'.$row, 'DAFTAR ALAMAT');
        $sheet->mergeCells('A'.$row.':F'.$row);
        $sheet->getStyle('A'.$row)->applyFromArray($sectionHeaderStyle);
        $row++;

        if ($user->addresses && $user->addresses->count() > 0) {
            $addressHeaders = ['No', 'Label', 'Alamat Lengkap', 'No. Telepon', 'Default'];
            $col = 'A';
            foreach ($addressHeaders as $header) {
                $sheet->setCellValue($col.$row, $header);
                $sheet->getStyle($col.$row)->applyFromArray($headerStyle);
                $col++;
            }
            $row++;

            foreach ($user->addresses as $index => $address) {
                $fullAddress = $address->detail_lain.', '.$address->kecamatan.', '.
                              $address->kota_kabupaten.', '.$address->provinsi.' '.$address->kode_pos;
                $sheet->setCellValue('A'.$row, $index + 1);
                $sheet->setCellValue('B'.$row, $address->label_alamat);
                $sheet->setCellValue('C'.$row, $fullAddress);
                $sheet->setCellValue('D'.$row, $address->nomor_handphone ?? '-');
                $sheet->setCellValue('E'.$row, $address->is_default ? 'Ya' : 'Tidak');
                $sheet->getStyle('A'.$row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $row++;
            }
        } else {
            $sheet->setCellValue('A'.$row, 'Tidak ada data alamat');
            $sheet->mergeCells('A'.$row.':F'.$row);
            $row++;
        }

        // Auto-size columns
        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Create the Excel file
        $writer = new Xlsx($spreadsheet);
        $filename = 'user_detail_'.$user->identifier.'_'.now()->format('Y-m-d_H-i-s').'.xlsx';

        $tempFile = storage_path('app/temp/'.$filename);
        if (! file_exists(dirname($tempFile))) {
            mkdir(dirname($tempFile), 0755, true);
        }
        $writer->save($tempFile);

        return response()->download($tempFile, $filename)->deleteFileAfterSend();
    }

    /**
     * Export single user detail to CSV
     */
    public function exportUserDetailCsv($id)
    {
        try {
            $user = User::with(['bankSampah', 'addresses', 'points' => function ($query) {
                $query->orderBy('created_at', 'desc');
            }, 'setorans' => function ($query) {
                $query->with('bankSampah')->orderBy('created_at', 'desc');
            }])->findOrFail($id);

            return $this->generateUserDetailCsvFile($user);

        } catch (\Exception $e) {
            \Log::error('Error in DashboardUserController@exportUserDetailCsv: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal export CSV: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Generate CSV file for single user detail
     */
    private function generateUserDetailCsvFile($user)
    {
        $filename = 'user_detail_'.$user->identifier.'_'.now()->format('Y-m-d_H-i-s').'.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ];

        $callback = function () use ($user) {
            $file = fopen('php://output', 'w');

            // Add BOM for UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // Title
            fputcsv($file, ['DETAIL USER - '.$user->name]);
            fputcsv($file, ['Tanggal Export: '.now()->format('d F Y H:i:s')]);
            fputcsv($file, []);

            // User Information
            fputcsv($file, ['INFORMASI USER']);
            fputcsv($file, ['Identifier', $user->identifier]);
            fputcsv($file, ['Nama', $user->name]);
            fputcsv($file, ['Jenis Nasabah', $user->jenis_nasabah]);
            fputcsv($file, ['No. Telepon', $user->phone ?? '-']);
            fputcsv($file, ['Tanggal Registrasi', $user->created_at->format('d F Y H:i')]);
            fputcsv($file, ['Bank Sampah', $user->bankSampah->nama_bank_sampah ?? '-']);
            fputcsv($file, []);

            // Statistics
            fputcsv($file, ['STATISTIK']);
            fputcsv($file, ['Total Setoran', 'Total Poin', 'Total XP', 'Total Sampah (kg)', 'Total Sampah (unit)']);
            fputcsv($file, [
                number_format($user->setor ?? 0),
                number_format($user->poin ?? 0, 2, ',', '.'),
                number_format($user->xp ?? 0),
                number_format($user->sampah ?? 0, 1),
                number_format($user->sampah_unit ?? 0),
            ]);
            fputcsv($file, []);

            // Setorans
            fputcsv($file, ['RIWAYAT SETORAN']);
            if ($user->setorans && $user->setorans->count() > 0) {
                fputcsv($file, ['No', 'Bank Sampah', 'Tanggal', 'Jumlah (Rp)', 'Status']);
                foreach ($user->setorans as $index => $setoran) {
                    fputcsv($file, [
                        $index + 1,
                        $setoran->bankSampah->nama_bank_sampah ?? '-',
                        $setoran->created_at->format('d/m/Y H:i'),
                        'Rp '.number_format($setoran->total_harga ?? 0, 0, ',', '.'),
                        ucfirst($setoran->status),
                    ]);
                }
            } else {
                fputcsv($file, ['Tidak ada data setoran']);
            }
            fputcsv($file, []);

            // Points History
            fputcsv($file, ['RIWAYAT POIN']);
            if ($user->points && $user->points->count() > 0) {
                fputcsv($file, ['No', 'Keterangan', 'Tanggal', 'Jumlah', 'Tipe']);
                foreach ($user->points as $index => $point) {
                    $prefix = $point->tipe === 'setor' ? '+' : '-';
                    fputcsv($file, [
                        $index + 1,
                        $point->keterangan ?? '-',
                        $point->created_at->format('d/m/Y H:i'),
                        $prefix.number_format($point->jumlah ?? 0, 2, ',', '.'),
                        ucfirst($point->tipe),
                    ]);
                }
            } else {
                fputcsv($file, ['Tidak ada riwayat poin']);
            }
            fputcsv($file, []);

            // Addresses
            fputcsv($file, ['DAFTAR ALAMAT']);
            if ($user->addresses && $user->addresses->count() > 0) {
                fputcsv($file, ['No', 'Label', 'Alamat Lengkap', 'No. Telepon', 'Default']);
                foreach ($user->addresses as $index => $address) {
                    $fullAddress = $address->detail_lain.', '.$address->kecamatan.', '.
                                  $address->kota_kabupaten.', '.$address->provinsi.' '.$address->kode_pos;
                    fputcsv($file, [
                        $index + 1,
                        $address->label_alamat,
                        $fullAddress,
                        $address->nomor_handphone ?? '-',
                        $address->is_default ? 'Ya' : 'Tidak',
                    ]);
                }
            } else {
                fputcsv($file, ['Tidak ada data alamat']);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export single user detail to PDF
     */
    public function exportUserDetailPdf($id)
    {
        try {
            $user = User::with(['bankSampah', 'addresses', 'points' => function ($query) {
                $query->orderBy('created_at', 'desc');
            }, 'setorans' => function ($query) {
                $query->with('bankSampah')->orderBy('created_at', 'desc');
            }])->findOrFail($id);

            $pdf = Pdf::loadView('pdf.user-detail-report', [
                'user' => $user,
            ]);

            $pdf->setPaper('A4', 'portrait');

            $filename = 'user_detail_'.$user->identifier.'_'.now()->format('Y-m-d_H-i-s').'.pdf';

            return $pdf->download($filename);

        } catch (\Exception $e) {
            \Log::error('Error in DashboardUserController@exportUserDetailPdf: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal export PDF: '.$e->getMessage(),
            ], 500);
        }
    }
}
