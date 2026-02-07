<?php

namespace App\Http\Controllers;

use App\Models\Point;
use App\Models\User;
use App\Services\NotificationService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class DashboardRedeemController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function index(Request $request)
    {
        // Get query parameters
        $search = $request->get('search');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        // Build query
        $query = Point::where('type', 'redeem');

        // Apply search filter
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('user_name', 'like', "%{$search}%")
                    ->orWhere('user_identifier', 'like', "%{$search}%");
            });
        }

        // Apply date filter
        if ($startDate && $endDate) {
            $query->whereBetween('tanggal', [$startDate, $endDate]);
        }

        // Get redeem history with pagination
        $redeems = $query->orderBy('created_at', 'desc')->paginate(10);

        // Append query parameters to pagination links
        $redeems->appends($request->query());

        return view('dashboard-redeem', compact('redeems'));
    }

    public function create()
    {
        return view('dashboard-redeem-create');
    }

    public function searchUser(Request $request)
    {
        $query = $request->get('query');

        $users = User::where('name', 'like', "%{$query}%")
            ->orWhere('identifier', 'like', "%{$query}%")
            ->select('id', 'name', 'identifier', 'poin', 'xp')
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

    public function redeem(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'jumlah_point' => 'required|numeric|min:1',
            'alasan_redeem' => 'required|string|max:500',
            'bukti_redeem' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        try {
            DB::beginTransaction();

            $user = User::find($request->user_id);

            // Check if user has enough points
            if ($user->poin < $request->jumlah_point) {
                return response()->json([
                    'error' => 'Poin tidak mencukupi. Poin tersedia: '.$user->poin,
                ], 400);
            }

            // Handle file upload
            $file = $request->file('bukti_redeem');
            $fileName = 'redeem_'.time().'_'.$file->getClientOriginalName();

            // Create upload directory if it doesn't exist
            $uploadPath = base_path('../uploads/redeem');
            if (! file_exists($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            // Store file outside Laravel project
            $file->move($uploadPath, $fileName);
            $fileUrl = env('APP_URL').'/uploads/redeem/'.$fileName;

            // Create point record for redeem
            Point::create([
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_identifier' => $user->identifier,
                'type' => 'redeem',
                'tanggal' => now(),
                'jumlah_point' => -$request->jumlah_point, // Negative for redeem
                'xp' => 0, // XP set to 0 for redeem
                'setoran_id' => null,
                'keterangan' => $request->alasan_redeem,
                'bukti_redeem' => $fileUrl,
            ]);

            // Update user points (only reduce points, not XP)
            $user->decrement('poin', $request->jumlah_point);
            // XP tidak dikurangi saat redeem

            // Send notification to user
            $this->notificationService->sendRedeemNotification(
                $user->id,
                $request->jumlah_point,
                $request->alasan_redeem
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Redeem poin berhasil diproses',
                'data' => [
                    'user_id' => $user->id,
                    'jumlah_point' => $request->jumlah_point,
                    'sisa_poin' => $user->poin - $request->jumlah_point,
                ],
            ]);

        } catch (\Exception $e) {
            DB::rollback();

            // Delete uploaded file if exists
            if (isset($fileUrl) && file_exists(base_path('../uploads/redeem/'.$fileName))) {
                unlink(base_path('../uploads/redeem/'.$fileName));
            }

            return response()->json([
                'error' => 'Terjadi kesalahan: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Export redeem data to Excel
     */
    public function export($type)
    {
        try {
            if ($type === 'excel') {
                return $this->exportExcel();
            } elseif ($type === 'csv') {
                return $this->exportCsv();
            } elseif ($type === 'pdf') {
                return $this->exportPdf();
            }

            return response()->json([
                'error' => 'Format export tidak didukung',
            ], 400);

        } catch (\Exception $e) {
            \Log::error('Error in DashboardRedeemController@export: '.$e->getMessage());

            return response()->json([
                'error' => 'Gagal export data: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Export redeem data to Excel
     */
    private function exportExcel()
    {
        // Get query parameters from request
        $request = request();
        $period = $request->get('period', 'all');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        // Build query
        $query = Point::where('type', 'redeem');

        // Apply period filter
        switch ($period) {
            case 'today':
                $query->whereDate('tanggal', today());
                break;
            case 'yesterday':
                $query->whereDate('tanggal', today()->subDay());
                break;
            case 'this_week':
                $query->whereBetween('tanggal', [now()->startOfWeek(), now()->endOfWeek()]);
                break;
            case 'last_week':
                $query->whereBetween('tanggal', [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()]);
                break;
            case 'this_month':
                $query->whereMonth('tanggal', now()->month)->whereYear('tanggal', now()->year);
                break;
            case 'last_month':
                $query->whereMonth('tanggal', now()->subMonth()->month)->whereYear('tanggal', now()->subMonth()->year);
                break;
            case 'this_year':
                $query->whereYear('tanggal', now()->year);
                break;
            case 'last_year':
                $query->whereYear('tanggal', now()->subYear()->year);
                break;
            case 'range':
                if ($startDate && $endDate) {
                    $query->whereBetween('tanggal', [$startDate, $endDate]);
                }
                break;
            case 'all':
            default:
                // No date filter
                break;
        }

        // Get redeem data
        $redeems = $query->orderBy('created_at', 'desc')->get();

        // Create new Spreadsheet
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();

        // Set document properties
        $spreadsheet->getProperties()
            ->setCreator('Bengkel Sampah Admin')
            ->setLastModifiedBy('Bengkel Sampah Admin')
            ->setTitle('Laporan Redeem Poin')
            ->setSubject('Laporan Data Redeem Poin')
            ->setDescription('Laporan data redeem poin Bengkel Sampah')
            ->setKeywords('redeem, poin, laporan, bengkel sampah')
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
        $sheet->setCellValue('A1', 'LAPORAN DATA REDEEM POIN BENGKEL SAMPAH');
        $sheet->mergeCells('A1:G1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Set subtitle
        $periodText = 'Semua Data';
        switch ($period) {
            case 'today':
                $periodText = 'Hari Ini';
                break;
            case 'yesterday':
                $periodText = 'Kemarin';
                break;
            case 'this_week':
                $periodText = 'Minggu Ini';
                break;
            case 'last_week':
                $periodText = 'Minggu Lalu';
                break;
            case 'this_month':
                $periodText = 'Bulan Ini';
                break;
            case 'last_month':
                $periodText = 'Bulan Lalu';
                break;
            case 'this_year':
                $periodText = 'Tahun Ini';
                break;
            case 'last_year':
                $periodText = 'Tahun Lalu';
                break;
            case 'range':
                if ($startDate && $endDate) {
                    $periodText = 'Periode: '.date('d/m/Y', strtotime($startDate)).' - '.date('d/m/Y', strtotime($endDate));
                }
                break;
        }

        $sheet->setCellValue('A2', $periodText.' | Total Data: '.$redeems->count().' redeem');
        $sheet->mergeCells('A2:G2');
        $sheet->getStyle('A2')->getFont()->setSize(12);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Set export date
        $sheet->setCellValue('A3', 'Tanggal Export: '.now()->format('d F Y H:i:s'));
        $sheet->mergeCells('A3:G3');
        $sheet->getStyle('A3')->getFont()->setSize(10);
        $sheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Set headers
        $headers = [
            'A5' => 'No',
            'B5' => 'Tanggal',
            'C5' => 'User',
            'D5' => 'Identifier',
            'E5' => 'Jumlah Poin',
            'F5' => 'Alasan',
            'G5' => 'URL Bukti Redeem',
        ];

        foreach ($headers as $cell => $header) {
            $sheet->setCellValue($cell, $header);
        }

        // Apply header style
        $sheet->getStyle('A5:G5')->applyFromArray($headerStyle);

        // Set data
        $row = 6;
        foreach ($redeems as $index => $redeem) {
            $sheet->setCellValue('A'.$row, $index + 1);
            $sheet->setCellValue('B'.$row, \Carbon\Carbon::parse($redeem->tanggal)->format('d/m/Y'));
            $sheet->setCellValue('C'.$row, $redeem->user_name);
            $sheet->setCellValue('D'.$row, $redeem->user_identifier);
            $sheet->setCellValue('E'.$row, number_format(abs($redeem->jumlah_point)).' Poin');
            $sheet->setCellValue('F'.$row, $redeem->keterangan);

            // Set bukti redeem URL
            $buktiUrl = $redeem->bukti_redeem ?: '-';
            $sheet->setCellValue('G'.$row, $buktiUrl);

            // Set border for data row
            $sheet->getStyle('A'.$row.':G'.$row)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

            $row++;
        }

        // Auto-size columns
        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Set column widths for better readability
        $sheet->getColumnDimension('A')->setWidth(5);   // No
        $sheet->getColumnDimension('B')->setWidth(15);  // Tanggal
        $sheet->getColumnDimension('C')->setWidth(25);  // User
        $sheet->getColumnDimension('D')->setWidth(20);  // Identifier
        $sheet->getColumnDimension('E')->setWidth(15);  // Jumlah Poin
        $sheet->getColumnDimension('F')->setWidth(40);  // Alasan
        $sheet->getColumnDimension('G')->setWidth(60);  // Bukti Redeem URL

        // Create Excel file
        $writer = new Xlsx($spreadsheet);

        // Set headers for download
        $filename = 'redeem_export_'.$period.'_'.now()->format('Y-m-d_H-i-s').'.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$filename.'"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    /**
     * Export redeem data to CSV
     */
    private function exportCsv()
    {
        // Get query parameters from request
        $request = request();
        $period = $request->get('period', 'all');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        // Build query
        $query = Point::where('type', 'redeem');

        // Apply period filter
        switch ($period) {
            case 'today':
                $query->whereDate('tanggal', today());
                break;
            case 'yesterday':
                $query->whereDate('tanggal', today()->subDay());
                break;
            case 'this_week':
                $query->whereBetween('tanggal', [now()->startOfWeek(), now()->endOfWeek()]);
                break;
            case 'last_week':
                $query->whereBetween('tanggal', [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()]);
                break;
            case 'this_month':
                $query->whereMonth('tanggal', now()->month)->whereYear('tanggal', now()->year);
                break;
            case 'last_month':
                $query->whereMonth('tanggal', now()->subMonth()->month)->whereYear('tanggal', now()->subMonth()->year);
                break;
            case 'this_year':
                $query->whereYear('tanggal', now()->year);
                break;
            case 'last_year':
                $query->whereYear('tanggal', now()->subYear()->year);
                break;
            case 'range':
                if ($startDate && $endDate) {
                    $query->whereBetween('tanggal', [$startDate, $endDate]);
                }
                break;
            case 'all':
            default:
                // No date filter
                break;
        }

        // Get redeem data
        $redeems = $query->orderBy('created_at', 'desc')->get();

        // Set headers for download
        $filename = 'redeem_export_'.$period.'_'.now()->format('Y-m-d_H-i-s').'.csv';

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
            'Tanggal',
            'User',
            'Identifier',
            'Jumlah Poin',
            'Alasan',
            'URL Bukti Redeem',
        ];
        fputcsv($output, $headers);

        // Write data rows
        foreach ($redeems as $index => $redeem) {
            $buktiUrl = $redeem->bukti_redeem ?: '-';

            $row = [
                $index + 1,
                \Carbon\Carbon::parse($redeem->tanggal)->format('d/m/Y'),
                $redeem->user_name,
                $redeem->user_identifier,
                number_format(abs($redeem->jumlah_point)).' Poin',
                $redeem->keterangan,
                $buktiUrl,
            ];
            fputcsv($output, $row);
        }

        fclose($output);
        exit;
    }

    /**
     * Export redeem data to PDF
     */
    private function exportPdf()
    {
        // Get query parameters from request
        $request = request();
        $period = $request->get('period', 'all');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        // Build query
        $query = Point::where('type', 'redeem');

        // Apply period filter
        switch ($period) {
            case 'today':
                $query->whereDate('tanggal', today());
                break;
            case 'yesterday':
                $query->whereDate('tanggal', today()->subDay());
                break;
            case 'this_week':
                $query->whereBetween('tanggal', [now()->startOfWeek(), now()->endOfWeek()]);
                break;
            case 'last_week':
                $query->whereBetween('tanggal', [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()]);
                break;
            case 'this_month':
                $query->whereMonth('tanggal', now()->month)->whereYear('tanggal', now()->year);
                break;
            case 'last_month':
                $query->whereMonth('tanggal', now()->subMonth()->month)->whereYear('tanggal', now()->subMonth()->year);
                break;
            case 'this_year':
                $query->whereYear('tanggal', now()->year);
                break;
            case 'last_year':
                $query->whereYear('tanggal', now()->subYear()->year);
                break;
            case 'range':
                if ($startDate && $endDate) {
                    $query->whereBetween('tanggal', [$startDate, $endDate]);
                }
                break;
            case 'all':
            default:
                // No date filter
                break;
        }

        // Get redeem data
        $redeems = $query->orderBy('created_at', 'desc')->get();

        // Create PDF
        $pdf = Pdf::loadView('exports.redeem', compact('redeems', 'period'));

        // Set headers for download
        $filename = 'redeem_export_'.$period.'_'.now()->format('Y-m-d_H-i-s').'.pdf';

        return $pdf->download($filename);
    }
}
