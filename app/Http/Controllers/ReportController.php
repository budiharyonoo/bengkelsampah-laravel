<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\BankSampah;
use App\Models\WasteTransaction;
use App\Repositories\InventoryRepository;
use App\Repositories\SetoranRepository;
use App\Repositories\WasteTransactionRepository;
use App\Services\Dashboard\PeriodHelper;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ReportController extends Controller
{
    public function __construct(
        private WasteTransactionRepository $transactionRepository,
        private InventoryRepository $inventoryRepository,
        private SetoranRepository $setoranRepository
    ) {}

    /**
     * Get period parameters from request.
     *
     * @return array{bankSampahId: int|null, startDate: Carbon, endDate: Carbon, periode: string}
     */
    private function getPeriodParams(Request $request): array
    {
        $periode = $request->get('periode', 'bulanan');
        $bankSampahId = $request->filled('bank_sampah_id') ? (int) $request->get('bank_sampah_id') : null;

        // Parse date range if provided
        $rangeDate = $request->get('range_date');
        $parsed = PeriodHelper::parseDateRange($rangeDate);

        [$startDate, $endDate] = PeriodHelper::getPeriodRange(
            $periode,
            $parsed['startDate'],
            $parsed['endDate']
        );

        return [
            'bankSampahId' => $bankSampahId,
            'startDate' => Carbon::parse($startDate),
            'endDate' => Carbon::parse($endDate),
            'periode' => $periode,
        ];
    }

    /**
     * Laporan Laba Rugi (Profit & Loss Report).
     */
    public function labaRugi(Request $request): View
    {
        $params = $this->getPeriodParams($request);

        // Get total pembelian (dari setorans)
        $purchaseStats = $this->setoranRepository->getCompletedTransactionStats(
            $params['bankSampahId'],
            $params['startDate'],
            $params['endDate']
        );
        $totalPembelian = $purchaseStats['total_value'];

        // Get sales and processing metrics (combined revenue)
        $salesMetrics = $this->transactionRepository->getSalesMetrics(
            $params['bankSampahId'],
            $params['startDate'],
            $params['endDate']
        );
        $totalPenjualan = $salesMetrics['total_value'];

        // Calculate Laba Kotor correctly using consistent data sources
        // Laba Kotor = Total Penjualan (sales + processing) - Total Pembelian
        $labaKotor = $totalPenjualan - $totalPembelian;

        // Get inventory summary
        $inventorySummary = $this->inventoryRepository->getInventorySummary($params['bankSampahId']);

        // Get bank sampah list for filter
        $bankSampahList = BankSampah::select(['id', 'kode_bank_sampah', 'nama_bank_sampah'])->get();

        return view('reports.laba-rugi', [
            'totalPembelian' => $totalPembelian,
            'totalPenjualan' => $totalPenjualan,
            'labaKotor' => $labaKotor,
            'inventorySummary' => $inventorySummary,
            'bankSampahList' => $bankSampahList,
            'periode' => $params['periode'],
            'startDate' => $params['startDate'],
            'endDate' => $params['endDate'],
            'selectedBankSampah' => $params['bankSampahId'],
            'jumlahTransaksiJual' => $salesMetrics['count'],
            'jumlahSetoran' => $purchaseStats['count'],
        ]);
    }

    /**
     * Laporan Penjualan (Sales Report).
     */
    public function penjualan(Request $request): View
    {
        $params = $this->getPeriodParams($request);
        $groupBy = $request->get('group_by', 'offtaker'); // 'offtaker' or 'sampah'

        if ($groupBy === 'offtaker') {
            $data = $this->transactionRepository->getSalesByOfftaker(
                $params['bankSampahId'],
                $params['startDate'],
                $params['endDate']
            );
        } else {
            $data = $this->transactionRepository->getSalesByWasteType(
                $params['bankSampahId'],
                $params['startDate'],
                $params['endDate']
            );
        }

        // Get recent transactions with pagination (10 per page)
        $recentTransactions = $this->transactionRepository->getRecentTransactionsPaginated(
            $params['bankSampahId'],
            $params['startDate'],
            $params['endDate'],
            10
        );

        // Get bank sampah list for filter
        $bankSampahList = BankSampah::select(['id', 'kode_bank_sampah', 'nama_bank_sampah'])->get();

        return view('reports.penjualan', [
            'data' => $data,
            'recentTransactions' => $recentTransactions,
            'groupBy' => $groupBy,
            'bankSampahList' => $bankSampahList,
            'periode' => $params['periode'],
            'startDate' => $params['startDate'],
            'endDate' => $params['endDate'],
            'selectedBankSampah' => $params['bankSampahId'],
        ]);
    }

    /**
     * Laporan Pengolahan (Processing Report).
     */
    public function pengolahan(Request $request): View
    {
        $params = $this->getPeriodParams($request);

        // Get processing metrics
        $processingMetrics = $this->transactionRepository->getProcessingMetrics(
            $params['bankSampahId'],
            $params['startDate'],
            $params['endDate']
        );

        // Get processing by offtaker
        $processingByOfftaker = $this->transactionRepository->getProcessingByOfftaker(
            $params['bankSampahId'],
            $params['startDate'],
            $params['endDate']
        );

        // Get recent processing transactions
        $recentTransactions = $this->transactionRepository->getRecentProcessingTransactions(
            $params['bankSampahId'],
            $params['startDate'],
            $params['endDate'],
            10
        );

        // Get bank sampah list for filter
        $bankSampahList = BankSampah::select(['id', 'kode_bank_sampah', 'nama_bank_sampah'])->get();

        return view('reports.pengolahan', [
            'processingMetrics' => $processingMetrics,
            'processingByOfftaker' => $processingByOfftaker,
            'recentTransactions' => $recentTransactions,
            'bankSampahList' => $bankSampahList,
            'periode' => $params['periode'],
            'startDate' => $params['startDate'],
            'endDate' => $params['endDate'],
            'selectedBankSampah' => $params['bankSampahId'],
        ]);
    }

    /**
     * Export Laporan Laba Rugi to Excel with professional styling.
     */
    public function exportLabaRugiExcel(Request $request)
    {
        $params = $this->getPeriodParams($request);

        // Get data
        $purchaseStats = $this->setoranRepository->getCompletedTransactionStats(
            $params['bankSampahId'],
            $params['startDate'],
            $params['endDate']
        );
        $salesMetrics = $this->transactionRepository->getSalesMetrics(
            $params['bankSampahId'],
            $params['startDate'],
            $params['endDate']
        );
        $inventorySummary = $this->inventoryRepository->getInventorySummary($params['bankSampahId']);

        // Get bank sampah name if filtered
        $bankSampahName = 'Semua Bank Sampah';
        if ($params['bankSampahId']) {
            $bankSampah = BankSampah::find($params['bankSampahId']);
            $bankSampahName = $bankSampah ? $bankSampah->nama_bank_sampah : 'Unknown';
        }

        // Calculate Laba Kotor correctly
        $labaKotor = $salesMetrics['total_value'] - $purchaseStats['total_value'];

        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Laporan Laba Rugi');

        // Header
        $sheet->setCellValue('A1', 'LAPORAN LABA RUGI');
        $sheet->mergeCells('A1:D1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('A2', 'Periode: '.$params['startDate']->format('d/m/Y').' - '.$params['endDate']->format('d/m/Y'));
        $sheet->mergeCells('A2:D2');
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('A3', 'Bank Sampah: '.$bankSampahName);
        $sheet->mergeCells('A3:D3');
        $sheet->getStyle('A3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $row = 5;

        // Summary Section
        $sheet->setCellValue('A'.$row, 'RINGKASAN');
        $sheet->mergeCells('A'.$row.':B'.$row);
        $sheet->getStyle('A'.$row)->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A'.$row)->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FF39746E');
        $sheet->getStyle('A'.$row)->getFont()->getColor()->setARGB('FFFFFFFF');
        $row++;

        // Summary metrics
        $summaryData = [
            ['Total Pembelian (Rp)', number_format($purchaseStats['total_value'], 0, ',', '.')],
            ['Total Penjualan & Pengolahan (Rp)', number_format($salesMetrics['total_value'], 0, ',', '.')],
            ['Laba Kotor (Rp)', number_format($labaKotor, 0, ',', '.')],
            ['', ''],
            ['Jumlah Setoran', $purchaseStats['count']],
            ['Jumlah Transaksi Penjualan', $salesMetrics['count']],
        ];

        foreach ($summaryData as $data) {
            $sheet->setCellValue('A'.$row, $data[0]);
            $sheet->setCellValue('B'.$row, $data[1]);
            if (! empty($data[0])) {
                $sheet->getStyle('A'.$row)->getFont()->setBold(true);
                $sheet->getStyle('A'.$row.':B'.$row)->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFF3F4F6');
            }
            $row++;
        }

        $row += 2;

        // Rincian Laba Rugi Section
        $sheet->setCellValue('A'.$row, 'RINCIAN LABA RUGI');
        $sheet->mergeCells('A'.$row.':B'.$row);
        $sheet->getStyle('A'.$row)->getFont()->setBold(true);
        $row++;

        $sheet->setCellValue('A'.$row, 'Keterangan');
        $sheet->setCellValue('B'.$row, 'Jumlah (Rp)');
        $sheet->getStyle('A'.$row.':B'.$row)->getFont()->setBold(true);
        $sheet->getStyle('A'.$row.':B'.$row)->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFE5E7EB');

        $row++;

        // Pembelian row
        $sheet->setCellValue('A'.$row, 'Total Pembelian Sampah (dari Nasabah)');
        $sheet->setCellValue('B'.$row, $purchaseStats['total_value']);
        $sheet->getStyle('B'.$row)->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle('B'.$row)->getFont()->getColor()->setARGB('FFEF4444');
        $row++;

        // Penjualan row
        $sheet->setCellValue('A'.$row, 'Total Penjualan & Pengolahan Sampah');
        $sheet->setCellValue('B'.$row, $salesMetrics['total_value']);
        $sheet->getStyle('B'.$row)->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle('B'.$row)->getFont()->getColor()->setARGB('FF22C55E');
        $row++;

        // Laba Kotor row (total)
        $sheet->setCellValue('A'.$row, 'Laba Kotor');
        $sheet->setCellValue('B'.$row, $labaKotor);
        $sheet->getStyle('A'.$row.':B'.$row)->getFont()->setBold(true);
        $sheet->getStyle('A'.$row.':B'.$row)->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFF3F4F6');
        $sheet->getStyle('B'.$row)->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle('B'.$row)->getFont()->getColor()->setARGB($labaKotor >= 0 ? 'FF22C55E' : 'FFEF4444');

        $row += 3;

        // Inventory Section
        $sheet->setCellValue('A'.$row, 'STATUS INVENTORI');
        $sheet->mergeCells('A'.$row.':B'.$row);
        $sheet->getStyle('A'.$row)->getFont()->setBold(true);
        $row++;

        $sheet->setCellValue('A'.$row, 'Keterangan');
        $sheet->setCellValue('B'.$row, 'Jumlah (Kg)');
        $sheet->getStyle('A'.$row.':B'.$row)->getFont()->setBold(true);
        $sheet->getStyle('A'.$row.':B'.$row)->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFE5E7EB');

        $row++;

        $inventoryData = [
            ['Sampah Tersimpan', number_format($inventorySummary['stored_kg'], 2, ',', '.')],
            ['Sampah Terjual', number_format($inventorySummary['sold_kg'], 2, ',', '.')],
            ['Sampah Diolah', number_format($inventorySummary['processed_kg'], 2, ',', '.')],
        ];

        foreach ($inventoryData as $data) {
            $sheet->setCellValue('A'.$row, $data[0]);
            $sheet->setCellValue('B'.$row, $data[1]);
            $row++;
        }

        // Auto-size columns
        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);

        $writer = new Xlsx($spreadsheet);
        $filename = 'laporan_laba_rugi_'.date('Y-m-d_H-i-s').'.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$filename.'"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    /**
     * Export Laporan Laba Rugi to PDF.
     */
    public function exportLabaRugiPdf(Request $request)
    {
        $params = $this->getPeriodParams($request);

        // Get data
        $purchaseStats = $this->setoranRepository->getCompletedTransactionStats(
            $params['bankSampahId'],
            $params['startDate'],
            $params['endDate']
        );
        $salesMetrics = $this->transactionRepository->getSalesMetrics(
            $params['bankSampahId'],
            $params['startDate'],
            $params['endDate']
        );
        $inventorySummary = $this->inventoryRepository->getInventorySummary($params['bankSampahId']);

        // Calculate Laba Kotor correctly
        $labaKotor = $salesMetrics['total_value'] - $purchaseStats['total_value'];

        $pdf = Pdf::loadView('reports.pdf.laba-rugi', [
            'totalPembelian' => $purchaseStats['total_value'],
            'totalPenjualan' => $salesMetrics['total_value'],
            'labaKotor' => $labaKotor,
            'inventorySummary' => $inventorySummary,
            'startDate' => $params['startDate'],
            'endDate' => $params['endDate'],
        ]);

        return $pdf->download('laporan_laba_rugi_'.date('Y-m-d').'.pdf');
    }

    /**
     * Export Laporan Laba Rugi to CSV with detailed breakdown.
     */
    public function exportLabaRugiCsv(Request $request)
    {
        $params = $this->getPeriodParams($request);

        // Get data
        $purchaseStats = $this->setoranRepository->getCompletedTransactionStats(
            $params['bankSampahId'],
            $params['startDate'],
            $params['endDate']
        );
        $salesMetrics = $this->transactionRepository->getSalesMetrics(
            $params['bankSampahId'],
            $params['startDate'],
            $params['endDate']
        );
        $inventorySummary = $this->inventoryRepository->getInventorySummary($params['bankSampahId']);

        // Get bank sampah name if filtered
        $bankSampahName = 'Semua Bank Sampah';
        if ($params['bankSampahId']) {
            $bankSampah = BankSampah::find($params['bankSampahId']);
            $bankSampahName = $bankSampah ? $bankSampah->nama_bank_sampah : 'Unknown';
        }

        // Calculate Laba Kotor correctly
        $labaKotor = $salesMetrics['total_value'] - $purchaseStats['total_value'];

        $filename = 'laporan_laba_rugi_'.date('Y-m-d_H-i-s').'.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="'.$filename.'"');
        header('Cache-Control: max-age=0');

        $output = fopen('php://output', 'w');

        // Add BOM for UTF-8 Excel compatibility
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

        // Header information
        fputcsv($output, ['LAPORAN LABA RUGI']);
        fputcsv($output, ['Periode', $params['startDate']->format('d/m/Y').' - '.$params['endDate']->format('d/m/Y')]);
        fputcsv($output, ['Bank Sampah', $bankSampahName]);
        fputcsv($output, []);

        // Summary Section
        fputcsv($output, ['RINGKASAN']);
        fputcsv($output, ['Total Pembelian (Rp)', number_format($purchaseStats['total_value'], 0, '.', '')]);
        fputcsv($output, ['Total Penjualan & Pengolahan (Rp)', number_format($salesMetrics['total_value'], 0, '.', '')]);
        fputcsv($output, ['Laba Kotor (Rp)', number_format($labaKotor, 0, '.', '')]);
        fputcsv($output, []);
        fputcsv($output, ['Jumlah Setoran', number_format($purchaseStats['count'], 0, '.', '')]);
        fputcsv($output, ['Jumlah Transaksi Penjualan', number_format($salesMetrics['count'], 0, '.', '')]);
        fputcsv($output, []);

        // Rincian Laba Rugi Section
        fputcsv($output, ['RINCIAN LABA RUGI']);
        fputcsv($output, ['Keterangan', 'Jumlah (Rp)']);
        fputcsv($output, [
            'Total Pembelian Sampah (dari Nasabah)',
            number_format($purchaseStats['total_value'], 0, '.', ''),
        ]);
        fputcsv($output, [
            'Total Penjualan & Pengolahan Sampah',
            number_format($salesMetrics['total_value'], 0, '.', ''),
        ]);
        fputcsv($output, [
            'Laba Kotor',
            number_format($labaKotor, 0, '.', ''),
        ]);
        fputcsv($output, []);

        // Inventory Section
        fputcsv($output, ['STATUS INVENTORI']);
        fputcsv($output, ['Keterangan', 'Jumlah (Kg)']);
        fputcsv($output, ['Sampah Tersimpan', number_format($inventorySummary['stored_kg'], 2, '.', '')]);
        fputcsv($output, ['Sampah Terjual', number_format($inventorySummary['sold_kg'], 2, '.', '')]);
        fputcsv($output, ['Sampah Diolah', number_format($inventorySummary['processed_kg'], 2, '.', '')]);

        fclose($output);
        exit;
    }

    /**
     * Export Laporan Penjualan to Excel with detailed breakdown.
     */
    public function exportPenjualanExcel(Request $request)
    {
        $params = $this->getPeriodParams($request);
        $groupBy = $request->get('group_by', 'offtaker');

        // Get both offtaker and waste type data for comprehensive export
        $dataByOfftaker = $this->transactionRepository->getSalesByOfftaker(
            $params['bankSampahId'],
            $params['startDate'],
            $params['endDate']
        );

        $dataByWasteType = $this->transactionRepository->getSalesByWasteType(
            $params['bankSampahId'],
            $params['startDate'],
            $params['endDate']
        );

        // Get bank sampah name if filtered
        $bankSampahName = 'Semua Bank Sampah';
        if ($params['bankSampahId']) {
            $bankSampah = BankSampah::find($params['bankSampahId']);
            $bankSampahName = $bankSampah ? $bankSampah->nama_bank_sampah : 'Unknown';
        }

        // Calculate summary metrics
        $allTransactions = WasteTransaction::query()
            ->salesAndProcessing()
            ->when($params['bankSampahId'], fn ($q) => $q->where('bank_sampah_id', $params['bankSampahId']))
            ->whereBetween('tanggal_transaksi', [$params['startDate']->toDateString(), $params['endDate']->toDateString()])
            ->get();

        $totalTransaksi = $allTransactions->count();
        $totalQuantity = $allTransactions->sum('total_quantity');
        $totalNilai = $allTransactions->sum('total_value');
        $totalDijual = $allTransactions->where('type', 'sale')->count();
        $totalDiolah = $allTransactions->where('type', 'processing')->count();
        $jenisWasteCount = count($dataByWasteType);

        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Laporan Penjualan & Pengolahan');

        // Header
        $sheet->setCellValue('A1', 'LAPORAN PENJUALAN & PENGOLAHAN');
        $sheet->mergeCells('A1:E1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('A2', 'Periode: '.$params['startDate']->format('d/m/Y').' - '.$params['endDate']->format('d/m/Y'));
        $sheet->mergeCells('A2:E2');
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('A3', 'Bank Sampah: '.$bankSampahName);
        $sheet->mergeCells('A3:E3');
        $sheet->getStyle('A3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $row = 5;

        // Summary Section
        $sheet->setCellValue('A'.$row, 'RINGKASAN');
        $sheet->mergeCells('A'.$row.':B'.$row);
        $sheet->getStyle('A'.$row)->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A'.$row)->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FF39746E');
        $sheet->getStyle('A'.$row)->getFont()->getColor()->setARGB('FFFFFFFF');
        $row++;

        // Summary metrics
        $summaryData = [
            ['Total Transaksi', $totalTransaksi],
            ['Total Quantity (Kg)', number_format($totalQuantity, 2, ',', '.')],
            ['Total Nilai (Rp)', number_format($totalNilai, 0, ',', '.')],
            ['Jenis Sampah', $jenisWasteCount],
            ['Total Dijual', $totalDijual],
            ['Total Diolah', $totalDiolah],
        ];

        foreach ($summaryData as $data) {
            $sheet->setCellValue('A'.$row, $data[0]);
            $sheet->setCellValue('B'.$row, $data[1]);
            $sheet->getStyle('A'.$row)->getFont()->setBold(true);
            $sheet->getStyle('A'.$row.':B'.$row)->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setARGB('FFF3F4F6');
            $row++;
        }

        $row += 2;

        // Section 1: Per Offtaker
        $sheet->setCellValue('A'.$row, 'RINGKASAN PER OFFTAKER');
        $sheet->mergeCells('A'.$row.':E'.$row);
        $sheet->getStyle('A'.$row)->getFont()->setBold(true);
        $row++;

        $sheet->setCellValue('A'.$row, 'Offtaker');
        $sheet->setCellValue('B'.$row, 'Jumlah Transaksi');
        $sheet->setCellValue('C'.$row, 'Total Qty (Kg)');
        $sheet->setCellValue('D'.$row, 'Total Penjualan (Rp)');
        $sheet->setCellValue('E'.$row, 'Laba (Rp)');
        $sheet->getStyle('A'.$row.':E'.$row)->getFont()->setBold(true);
        $sheet->getStyle('A'.$row.':E'.$row)->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFE5E7EB');

        $row++;
        $totalQtyOfftaker = 0;
        $totalValueOfftaker = 0;
        $totalProfitOfftaker = 0;

        foreach ($dataByOfftaker as $item) {
            $totalQtyOfftaker += $item['total_quantity'] ?? 0;
            $totalValueOfftaker += $item['total_value'] ?? 0;
            $totalProfitOfftaker += $item['profit'] ?? 0;

            $sheet->setCellValue('A'.$row, $item['name'] ?? '-');
            $sheet->setCellValue('B'.$row, $item['count'] ?? 0);
            $sheet->setCellValue('C'.$row, $item['total_quantity'] ?? 0);
            $sheet->setCellValue('D'.$row, $item['total_value'] ?? 0);
            $sheet->setCellValue('E'.$row, $item['profit'] ?? 0);

            $sheet->getStyle('C'.$row)->getNumberFormat()->setFormatCode('#,##0.00');
            $sheet->getStyle('D'.$row)->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getStyle('E'.$row)->getNumberFormat()->setFormatCode('#,##0');

            $row++;
        }

        // Total row for offtaker
        $sheet->setCellValue('A'.$row, 'TOTAL');
        $sheet->mergeCells('A'.$row.':B'.$row);
        $sheet->setCellValue('C'.$row, $totalQtyOfftaker);
        $sheet->setCellValue('D'.$row, $totalValueOfftaker);
        $sheet->setCellValue('E'.$row, $totalProfitOfftaker);
        $sheet->getStyle('A'.$row.':E'.$row)->getFont()->setBold(true);
        $sheet->getStyle('A'.$row.':E'.$row)->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFF3F4F6');
        $sheet->getStyle('C'.$row)->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle('D'.$row)->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle('E'.$row)->getNumberFormat()->setFormatCode('#,##0');

        $row += 3;

        // Section 2: Per Jenis Sampah
        $sheet->setCellValue('A'.$row, 'RINGKASAN PER JENIS SAMPAH');
        $sheet->mergeCells('A'.$row.':E'.$row);
        $sheet->getStyle('A'.$row)->getFont()->setBold(true);
        $row++;

        $sheet->setCellValue('A'.$row, 'Jenis Sampah');
        $sheet->setCellValue('B'.$row, 'Jumlah Transaksi');
        $sheet->setCellValue('C'.$row, 'Total Qty (Kg)');
        $sheet->setCellValue('D'.$row, 'Total Penjualan (Rp)');
        $sheet->setCellValue('E'.$row, 'Laba (Rp)');
        $sheet->getStyle('A'.$row.':E'.$row)->getFont()->setBold(true);
        $sheet->getStyle('A'.$row.':E'.$row)->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFE5E7EB');

        $row++;
        $totalQtyWaste = 0;
        $totalValueWaste = 0;
        $totalProfitWaste = 0;

        foreach ($dataByWasteType as $item) {
            $totalQtyWaste += $item['total_quantity'] ?? 0;
            $totalValueWaste += $item['total_value'] ?? 0;
            $totalProfitWaste += $item['profit'] ?? 0;

            $sheet->setCellValue('A'.$row, $item['name'] ?? '-');
            $sheet->setCellValue('B'.$row, $item['count'] ?? 0);
            $sheet->setCellValue('C'.$row, $item['total_quantity'] ?? 0);
            $sheet->setCellValue('D'.$row, $item['total_value'] ?? 0);
            $sheet->setCellValue('E'.$row, $item['profit'] ?? 0);

            $sheet->getStyle('C'.$row)->getNumberFormat()->setFormatCode('#,##0.00');
            $sheet->getStyle('D'.$row)->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getStyle('E'.$row)->getNumberFormat()->setFormatCode('#,##0');

            $row++;
        }

        // Total row for waste type
        $sheet->setCellValue('A'.$row, 'TOTAL');
        $sheet->mergeCells('A'.$row.':B'.$row);
        $sheet->setCellValue('C'.$row, $totalQtyWaste);
        $sheet->setCellValue('D'.$row, $totalValueWaste);
        $sheet->setCellValue('E'.$row, $totalProfitWaste);
        $sheet->getStyle('A'.$row.':E'.$row)->getFont()->setBold(true);
        $sheet->getStyle('A'.$row.':E'.$row)->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFF3F4F6');
        $sheet->getStyle('C'.$row)->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle('D'.$row)->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle('E'.$row)->getNumberFormat()->setFormatCode('#,##0');

        $row += 3;

        // Section 3: Detail Transaksi
        $recentTransactions = $this->transactionRepository->getRecentTransactions(
            $params['bankSampahId'],
            $params['startDate'],
            $params['endDate'],
            100 // Get more transactions for export
        );

        $sheet->setCellValue('A'.$row, 'DETAIL TRANSAKSI');
        $sheet->mergeCells('A'.$row.':F'.$row);
        $sheet->getStyle('A'.$row)->getFont()->setBold(true);
        $row++;

        $sheet->setCellValue('A'.$row, 'Kode');
        $sheet->setCellValue('B'.$row, 'Tipe');
        $sheet->setCellValue('C'.$row, 'Tanggal');
        $sheet->setCellValue('D'.$row, 'Offtaker');
        $sheet->setCellValue('E'.$row, 'Bank Sampah');
        $sheet->setCellValue('F'.$row, 'Total (Rp)');
        $sheet->getStyle('A'.$row.':F'.$row)->getFont()->setBold(true);
        $sheet->getStyle('A'.$row.':F'.$row)->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFE5E7EB');

        $row++;

        foreach ($recentTransactions as $trx) {
            $sheet->setCellValue('A'.$row, $trx->kode_transaksi);
            $sheet->setCellValue('B'.$row, $trx->type === 'sale' ? 'Penjualan' : 'Pengolahan');
            $sheet->setCellValue('C'.$row, $trx->tanggal_transaksi ? \Carbon\Carbon::parse($trx->tanggal_transaksi)->format('d/m/Y') : '-');
            $sheet->setCellValue('D'.$row, $trx->offtaker->nama ?? '-');
            $sheet->setCellValue('E'.$row, $trx->bankSampah->nama_bank_sampah ?? '-');
            $sheet->setCellValue('F'.$row, $trx->total_value ?? 0);
            $sheet->getStyle('F'.$row)->getNumberFormat()->setFormatCode('#,##0');

            $row++;
        }

        // Auto-size columns
        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'laporan_penjualan_pengolahan_'.date('Y-m-d_H-i-s').'.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$filename.'"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    /**
     * Export Laporan Penjualan to PDF with detailed breakdown.
     */
    public function exportPenjualanPdf(Request $request)
    {
        $params = $this->getPeriodParams($request);
        $groupBy = $request->get('group_by', 'offtaker');

        // Get both offtaker and waste type data for comprehensive export
        $dataByOfftaker = $this->transactionRepository->getSalesByOfftaker(
            $params['bankSampahId'],
            $params['startDate'],
            $params['endDate']
        );

        $dataByWasteType = $this->transactionRepository->getSalesByWasteType(
            $params['bankSampahId'],
            $params['startDate'],
            $params['endDate']
        );

        // Get bank sampah name if filtered
        $bankSampahName = 'Semua Bank Sampah';
        if ($params['bankSampahId']) {
            $bankSampah = BankSampah::find($params['bankSampahId']);
            $bankSampahName = $bankSampah ? $bankSampah->nama_bank_sampah : 'Unknown';
        }

        // Calculate summary metrics
        $allTransactions = WasteTransaction::query()
            ->salesAndProcessing()
            ->when($params['bankSampahId'], fn ($q) => $q->where('bank_sampah_id', $params['bankSampahId']))
            ->whereBetween('tanggal_transaksi', [$params['startDate']->toDateString(), $params['endDate']->toDateString()])
            ->get();

        $summaryData = [
            'total_transaksi' => $allTransactions->count(),
            'total_quantity' => $allTransactions->sum('total_quantity'),
            'total_nilai' => $allTransactions->sum('total_value'),
            'total_dijual' => $allTransactions->where('type', 'sale')->count(),
            'total_diolah' => $allTransactions->where('type', 'processing')->count(),
            'jenis_waste_count' => count($dataByWasteType),
        ];

        // Get recent transactions for detail view
        $recentTransactions = $this->transactionRepository->getRecentTransactions(
            $params['bankSampahId'],
            $params['startDate'],
            $params['endDate'],
            50
        );

        $pdf = Pdf::loadView('reports.pdf.penjualan', [
            'dataByOfftaker' => $dataByOfftaker,
            'dataByWasteType' => $dataByWasteType,
            'recentTransactions' => $recentTransactions,
            'summaryData' => $summaryData,
            'groupBy' => $groupBy,
            'startDate' => $params['startDate'],
            'endDate' => $params['endDate'],
            'bankSampahName' => $bankSampahName,
        ]);

        return $pdf->download('laporan_penjualan_pengolahan_'.date('Y-m-d').'.pdf');
    }

    /**
     * Export Laporan Penjualan to CSV with detailed breakdown.
     */
    public function exportPenjualanCsv(Request $request)
    {
        $params = $this->getPeriodParams($request);
        $groupBy = $request->get('group_by', 'offtaker');

        // Get both offtaker and waste type data for comprehensive export
        $dataByOfftaker = $this->transactionRepository->getSalesByOfftaker(
            $params['bankSampahId'],
            $params['startDate'],
            $params['endDate']
        );

        $dataByWasteType = $this->transactionRepository->getSalesByWasteType(
            $params['bankSampahId'],
            $params['startDate'],
            $params['endDate']
        );

        // Get bank sampah name if filtered
        $bankSampahName = 'Semua Bank Sampah';
        if ($params['bankSampahId']) {
            $bankSampah = BankSampah::find($params['bankSampahId']);
            $bankSampahName = $bankSampah ? $bankSampah->nama_bank_sampah : 'Unknown';
        }

        // Calculate summary metrics
        $allTransactions = WasteTransaction::query()
            ->salesAndProcessing()
            ->when($params['bankSampahId'], fn ($q) => $q->where('bank_sampah_id', $params['bankSampahId']))
            ->whereBetween('tanggal_transaksi', [$params['startDate']->toDateString(), $params['endDate']->toDateString()])
            ->get();

        $totalTransaksi = $allTransactions->count();
        $totalQuantity = $allTransactions->sum('total_quantity');
        $totalNilai = $allTransactions->sum('total_value');
        $totalDijual = $allTransactions->where('type', 'sale')->count();
        $totalDiolah = $allTransactions->where('type', 'processing')->count();
        $jenisWasteCount = count($dataByWasteType);

        $filename = 'laporan_penjualan_pengolahan_'.date('Y-m-d_H-i-s').'.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="'.$filename.'"');
        header('Cache-Control: max-age=0');

        $output = fopen('php://output', 'w');

        // Add BOM for UTF-8 Excel compatibility
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

        // Header information
        fputcsv($output, ['LAPORAN PENJUALAN & PENGOLAHAN']);
        fputcsv($output, ['Periode', $params['startDate']->format('d/m/Y').' - '.$params['endDate']->format('d/m/Y')]);
        fputcsv($output, ['Bank Sampah', $bankSampahName]);
        fputcsv($output, []);

        // Summary Section
        fputcsv($output, ['RINGKASAN']);
        fputcsv($output, ['Total Transaksi', number_format($totalTransaksi, 0, '.', '')]);
        fputcsv($output, ['Total Quantity (Kg)', number_format($totalQuantity, 2, '.', '')]);
        fputcsv($output, ['Total Nilai (Rp)', number_format($totalNilai, 0, '.', '')]);
        fputcsv($output, ['Jenis Sampah', number_format($jenisWasteCount, 0, '.', '')]);
        fputcsv($output, ['Total Dijual', number_format($totalDijual, 0, '.', '')]);
        fputcsv($output, ['Total Diolah', number_format($totalDiolah, 0, '.', '')]);
        fputcsv($output, []);

        // Section 1: Data per Offtaker
        fputcsv($output, ['RINGKASAN PER OFFTAKER']);
        fputcsv($output, ['Offtaker', 'Jumlah Transaksi', 'Total Qty (Kg)', 'Total Penjualan (Rp)', 'Laba (Rp)']);

        $totalQtyOfftaker = 0;
        $totalValueOfftaker = 0;
        $totalProfitOfftaker = 0;

        foreach ($dataByOfftaker as $item) {
            $totalQtyOfftaker += $item['total_quantity'] ?? 0;
            $totalValueOfftaker += $item['total_value'] ?? 0;
            $totalProfitOfftaker += $item['profit'] ?? 0;

            fputcsv($output, [
                $item['name'] ?? '-',
                $item['count'] ?? 0,
                number_format($item['total_quantity'] ?? 0, 2, '.', ''),
                number_format($item['total_value'] ?? 0, 0, '.', ''),
                number_format($item['profit'] ?? 0, 0, '.', ''),
            ]);
        }

        // Total for offtaker
        fputcsv($output, [
            'TOTAL',
            '',
            number_format($totalQtyOfftaker, 2, '.', ''),
            number_format($totalValueOfftaker, 0, '.', ''),
            number_format($totalProfitOfftaker, 0, '.', ''),
        ]);

        fputcsv($output, []);
        fputcsv($output, []);

        // Section 2: Data per Jenis Sampah
        fputcsv($output, ['RINGKASAN PER JENIS SAMPAH']);
        fputcsv($output, ['Jenis Sampah', 'Jumlah Transaksi', 'Total Qty (Kg)', 'Total Penjualan (Rp)', 'Laba (Rp)']);

        $totalQtyWaste = 0;
        $totalValueWaste = 0;
        $totalProfitWaste = 0;

        foreach ($dataByWasteType as $item) {
            $totalQtyWaste += $item['total_quantity'] ?? 0;
            $totalValueWaste += $item['total_value'] ?? 0;
            $totalProfitWaste += $item['profit'] ?? 0;

            fputcsv($output, [
                $item['name'] ?? '-',
                $item['count'] ?? 0,
                number_format($item['total_quantity'] ?? 0, 2, '.', ''),
                number_format($item['total_value'] ?? 0, 0, '.', ''),
                number_format($item['profit'] ?? 0, 0, '.', ''),
            ]);
        }

        // Total for waste type
        fputcsv($output, [
            'TOTAL',
            '',
            number_format($totalQtyWaste, 2, '.', ''),
            number_format($totalValueWaste, 0, '.', ''),
            number_format($totalProfitWaste, 0, '.', ''),
        ]);

        fputcsv($output, []);
        fputcsv($output, []);

        // Section 3: Detail Transaksi
        $recentTransactions = $this->transactionRepository->getRecentTransactions(
            $params['bankSampahId'],
            $params['startDate'],
            $params['endDate'],
            100
        );

        fputcsv($output, ['DETAIL TRANSAKSI']);
        fputcsv($output, ['Kode', 'Tipe', 'Tanggal', 'Offtaker', 'Bank Sampah', 'Total (Rp)']);

        foreach ($recentTransactions as $trx) {
            fputcsv($output, [
                $trx->kode_transaksi,
                $trx->type === 'sale' ? 'Penjualan' : 'Pengolahan',
                $trx->tanggal_transaksi ? \Carbon\Carbon::parse($trx->tanggal_transaksi)->format('d/m/Y') : '-',
                $trx->offtaker->nama ?? '-',
                $trx->bankSampah->nama_bank_sampah ?? '-',
                number_format($trx->total_value ?? 0, 0, '.', ''),
            ]);
        }

        fclose($output);
        exit;
    }

    /**
     * Export Laporan Pengolahan to Excel.
     */
    public function exportPengolahanExcel(Request $request)
    {
        $params = $this->getPeriodParams($request);

        $processingMetrics = $this->transactionRepository->getProcessingMetrics(
            $params['bankSampahId'],
            $params['startDate'],
            $params['endDate']
        );

        $processingByOfftaker = $this->transactionRepository->getProcessingByOfftaker(
            $params['bankSampahId'],
            $params['startDate'],
            $params['endDate']
        );

        $processingByWasteType = $this->transactionRepository->getProcessingByWasteType(
            $params['bankSampahId'],
            $params['startDate'],
            $params['endDate']
        );

        $recentTransactions = $this->transactionRepository->getRecentProcessingTransactions(
            $params['bankSampahId'],
            $params['startDate'],
            $params['endDate'],
            100
        );

        // Get bank sampah name if filtered
        $bankSampahName = 'Semua Bank Sampah';
        if ($params['bankSampahId']) {
            $bankSampah = BankSampah::find($params['bankSampahId']);
            $bankSampahName = $bankSampah ? $bankSampah->nama_bank_sampah : 'Unknown';
        }

        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Laporan Pengolahan');

        // Header
        $sheet->setCellValue('A1', 'LAPORAN PENGOLAHAN SAMPAH');
        $sheet->mergeCells('A1:D1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('A2', 'Periode: '.$params['startDate']->format('d/m/Y').' - '.$params['endDate']->format('d/m/Y'));
        $sheet->mergeCells('A2:D2');
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('A3', 'Bank Sampah: '.$bankSampahName);
        $sheet->mergeCells('A3:D3');
        $sheet->getStyle('A3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $row = 5;

        // Summary Section
        $sheet->setCellValue('A'.$row, 'RINGKASAN');
        $sheet->mergeCells('A'.$row.':B'.$row);
        $sheet->getStyle('A'.$row)->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A'.$row)->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FF39746E');
        $sheet->getStyle('A'.$row)->getFont()->getColor()->setARGB('FFFFFFFF');
        $row++;

        // Summary metrics
        $summaryData = [
            ['Total Transaksi', $processingMetrics['count'] ?? 0],
            ['Total Input (Kg)', number_format($processingMetrics['total_quantity'] ?? 0, 2, ',', '.')],
            ['Jenis Sampah', count($processingByWasteType)],
        ];

        foreach ($summaryData as $data) {
            $sheet->setCellValue('A'.$row, $data[0]);
            $sheet->setCellValue('B'.$row, $data[1]);
            $sheet->getStyle('A'.$row)->getFont()->setBold(true);
            $sheet->getStyle('A'.$row.':B'.$row)->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setARGB('FFF3F4F6');
            $row++;
        }

        $row += 2;

        // Section 1: Ringkasan per Offtaker
        $sheet->setCellValue('A'.$row, 'RINGKASAN PENGOLAHAN PER OFFTAKER');
        $sheet->mergeCells('A'.$row.':C'.$row);
        $sheet->getStyle('A'.$row)->getFont()->setBold(true);
        $row++;

        $sheet->setCellValue('A'.$row, 'Offtaker');
        $sheet->setCellValue('B'.$row, 'Jumlah Transaksi');
        $sheet->setCellValue('C'.$row, 'Total Qty (Kg)');
        $sheet->getStyle('A'.$row.':C'.$row)->getFont()->setBold(true);
        $sheet->getStyle('A'.$row.':C'.$row)->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFE5E7EB');

        $row++;
        $totalQty = 0;

        foreach ($processingByOfftaker as $item) {
            $totalQty += $item['total_quantity'] ?? 0;

            $sheet->setCellValue('A'.$row, $item['name'] ?? '-');
            $sheet->setCellValue('B'.$row, $item['count'] ?? 0);
            $sheet->setCellValue('C'.$row, $item['total_quantity'] ?? 0);

            $sheet->getStyle('C'.$row)->getNumberFormat()->setFormatCode('#,##0.00');

            $row++;
        }

        // Total row
        $sheet->setCellValue('A'.$row, 'TOTAL');
        $sheet->mergeCells('A'.$row.':B'.$row);
        $sheet->setCellValue('C'.$row, $totalQty);
        $sheet->getStyle('A'.$row.':C'.$row)->getFont()->setBold(true);
        $sheet->getStyle('A'.$row.':C'.$row)->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFF3F4F6');
        $sheet->getStyle('C'.$row)->getNumberFormat()->setFormatCode('#,##0.00');

        $row += 3;

        // Section 2: Ringkasan per Jenis Sampah
        $sheet->setCellValue('A'.$row, 'RINGKASAN PENGOLAHAN PER JENIS SAMPAH');
        $sheet->mergeCells('A'.$row.':C'.$row);
        $sheet->getStyle('A'.$row)->getFont()->setBold(true);
        $row++;

        $sheet->setCellValue('A'.$row, 'Jenis Sampah');
        $sheet->setCellValue('B'.$row, 'Jumlah Transaksi');
        $sheet->setCellValue('C'.$row, 'Total Qty (Kg)');
        $sheet->getStyle('A'.$row.':C'.$row)->getFont()->setBold(true);
        $sheet->getStyle('A'.$row.':C'.$row)->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFE5E7EB');

        $row++;
        $totalQtyWaste = 0;

        foreach ($processingByWasteType as $item) {
            $totalQtyWaste += $item['total_quantity'] ?? 0;

            $sheet->setCellValue('A'.$row, $item['name'] ?? '-');
            $sheet->setCellValue('B'.$row, $item['count'] ?? 0);
            $sheet->setCellValue('C'.$row, $item['total_quantity'] ?? 0);

            $sheet->getStyle('C'.$row)->getNumberFormat()->setFormatCode('#,##0.00');

            $row++;
        }

        // Total row for waste type
        $sheet->setCellValue('A'.$row, 'TOTAL');
        $sheet->mergeCells('A'.$row.':B'.$row);
        $sheet->setCellValue('C'.$row, $totalQtyWaste);
        $sheet->getStyle('A'.$row.':C'.$row)->getFont()->setBold(true);
        $sheet->getStyle('A'.$row.':C'.$row)->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFF3F4F6');
        $sheet->getStyle('C'.$row)->getNumberFormat()->setFormatCode('#,##0.00');

        $row += 3;

        // Section 3: Transaksi Terbaru
        $sheet->setCellValue('A'.$row, 'TRANSAKSI TERBARU');
        $sheet->mergeCells('A'.$row.':F'.$row);
        $sheet->getStyle('A'.$row)->getFont()->setBold(true);
        $row++;

        $sheet->setCellValue('A'.$row, 'Kode');
        $sheet->setCellValue('B'.$row, 'Tanggal');
        $sheet->setCellValue('C'.$row, 'Offtaker');
        $sheet->setCellValue('D'.$row, 'Bank Sampah');
        $sheet->setCellValue('E'.$row, 'Metode Pengolahan');
        $sheet->setCellValue('F'.$row, 'Total Qty (Kg)');
        $sheet->getStyle('A'.$row.':F'.$row)->getFont()->setBold(true);
        $sheet->getStyle('A'.$row.':F'.$row)->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFE5E7EB');

        $row++;

        foreach ($recentTransactions as $trx) {
            $sheet->setCellValue('A'.$row, $trx->kode_transaksi);
            $sheet->setCellValue('B'.$row, $trx->tanggal_transaksi ? \Carbon\Carbon::parse($trx->tanggal_transaksi)->format('d/m/Y') : '-');
            $sheet->setCellValue('C'.$row, $trx->offtaker->nama ?? '-');
            $sheet->setCellValue('D'.$row, $trx->bankSampah->nama_bank_sampah ?? '-');
            $sheet->setCellValue('E'.$row, ucfirst($trx->metode_pengolahan ?? '-'));
            $sheet->setCellValue('F'.$row, $trx->total_quantity ?? 0);

            $sheet->getStyle('F'.$row)->getNumberFormat()->setFormatCode('#,##0.00');

            $row++;
        }

        // Auto-size columns
        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'laporan_pengolahan_'.date('Y-m-d_H-i-s').'.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$filename.'"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    /**
     * Export Laporan Pengolahan to PDF.
     */
    public function exportPengolahanPdf(Request $request)
    {
        $params = $this->getPeriodParams($request);

        $processingMetrics = $this->transactionRepository->getProcessingMetrics(
            $params['bankSampahId'],
            $params['startDate'],
            $params['endDate']
        );

        $processingByOfftaker = $this->transactionRepository->getProcessingByOfftaker(
            $params['bankSampahId'],
            $params['startDate'],
            $params['endDate']
        );

        $recentTransactions = $this->transactionRepository->getRecentProcessingTransactions(
            $params['bankSampahId'],
            $params['startDate'],
            $params['endDate'],
            10
        );

        $pdf = Pdf::loadView('reports.pdf.pengolahan', [
            'processingMetrics' => $processingMetrics,
            'processingByOfftaker' => $processingByOfftaker,
            'recentTransactions' => $recentTransactions,
            'startDate' => $params['startDate'],
            'endDate' => $params['endDate'],
        ]);

        return $pdf->download('laporan_pengolahan_'.date('Y-m-d').'.pdf');
    }

    /**
     * Export Laporan Pengolahan to CSV.
     */
    public function exportPengolahanCsv(Request $request)
    {
        $params = $this->getPeriodParams($request);

        $processingMetrics = $this->transactionRepository->getProcessingMetrics(
            $params['bankSampahId'],
            $params['startDate'],
            $params['endDate']
        );

        $processingByOfftaker = $this->transactionRepository->getProcessingByOfftaker(
            $params['bankSampahId'],
            $params['startDate'],
            $params['endDate']
        );

        $processingByWasteType = $this->transactionRepository->getProcessingByWasteType(
            $params['bankSampahId'],
            $params['startDate'],
            $params['endDate']
        );

        $recentTransactions = $this->transactionRepository->getRecentProcessingTransactions(
            $params['bankSampahId'],
            $params['startDate'],
            $params['endDate'],
            100
        );

        // Get bank sampah name
        $bankSampahName = 'Semua Bank Sampah';
        if ($params['bankSampahId']) {
            $bankSampah = BankSampah::find($params['bankSampahId']);
            $bankSampahName = $bankSampah ? $bankSampah->nama_bank_sampah : 'Unknown';
        }

        $filename = 'laporan_pengolahan_'.date('Y-m-d_H-i-s').'.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="'.$filename.'"');
        header('Cache-Control: max-age=0');

        $output = fopen('php://output', 'w');

        // UTF-8 BOM for Excel compatibility
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

        // Headers
        fputcsv($output, ['LAPORAN PENGOLAHAN SAMPAH']);
        fputcsv($output, ['Periode', $params['startDate']->format('d/m/Y').' - '.$params['endDate']->format('d/m/Y')]);
        fputcsv($output, ['Bank Sampah', $bankSampahName]);
        fputcsv($output, []);

        // Summary Section
        fputcsv($output, ['RINGKASAN']);
        fputcsv($output, ['Total Transaksi', number_format($processingMetrics['count'] ?? 0, 0, '.', '')]);
        fputcsv($output, ['Total Input (Kg)', number_format($processingMetrics['total_quantity'] ?? 0, 2, '.', '')]);
        fputcsv($output, ['Jenis Sampah', number_format(count($processingByWasteType), 0, '.', '')]);
        fputcsv($output, []);

        // Section 1: Ringkasan per Offtaker
        fputcsv($output, ['RINGKASAN PENGOLAHAN PER OFFTAKER']);
        fputcsv($output, ['Offtaker', 'Jumlah Transaksi', 'Total Qty (Kg)']);

        $totalQty = 0;
        foreach ($processingByOfftaker as $item) {
            $totalQty += $item['total_quantity'] ?? 0;
            fputcsv($output, [
                $item['name'] ?? '-',
                $item['count'] ?? 0,
                number_format($item['total_quantity'] ?? 0, 2, '.', ''),
            ]);
        }

        // Total row
        fputcsv($output, ['TOTAL', '', number_format($totalQty, 2, '.', '')]);
        fputcsv($output, []);
        fputcsv($output, []);

        // Section 2: Ringkasan per Jenis Sampah
        fputcsv($output, ['RINGKASAN PENGOLAHAN PER JENIS SAMPAH']);
        fputcsv($output, ['Jenis Sampah', 'Jumlah Transaksi', 'Total Qty (Kg)']);

        $totalQtyWaste = 0;
        foreach ($processingByWasteType as $item) {
            $totalQtyWaste += $item['total_quantity'] ?? 0;
            fputcsv($output, [
                $item['name'] ?? '-',
                $item['count'] ?? 0,
                number_format($item['total_quantity'] ?? 0, 2, '.', ''),
            ]);
        }

        // Total row for waste type
        fputcsv($output, ['TOTAL', '', number_format($totalQtyWaste, 2, '.', '')]);
        fputcsv($output, []);
        fputcsv($output, []);

        // Section 3: Transaksi Terbaru
        fputcsv($output, ['TRANSAKSI TERBARU']);
        fputcsv($output, ['Kode', 'Tanggal', 'Offtaker', 'Bank Sampah', 'Metode Pengolahan', 'Total Qty (Kg)']);

        foreach ($recentTransactions as $trx) {
            fputcsv($output, [
                $trx->kode_transaksi,
                $trx->tanggal_transaksi ? \Carbon\Carbon::parse($trx->tanggal_transaksi)->format('d/m/Y') : '-',
                $trx->offtaker->nama ?? '-',
                $trx->bankSampah->nama_bank_sampah ?? '-',
                ucfirst($trx->metode_pengolahan ?? '-'),
                number_format($trx->total_quantity ?? 0, 2, '.', ''),
            ]);
        }

        fclose($output);
        exit;
    }
}
