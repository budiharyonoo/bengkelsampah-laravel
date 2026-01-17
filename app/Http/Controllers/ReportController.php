<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\BankSampah;
use App\Repositories\InventoryRepository;
use App\Repositories\SetoranRepository;
use App\Repositories\WasteTransactionRepository;
use App\Services\Dashboard\PeriodHelper;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Barryvdh\DomPDF\Facade\Pdf;

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

        // Get sales and profit metrics
        $salesMetrics = $this->transactionRepository->getSalesMetrics(
            $params['bankSampahId'],
            $params['startDate'],
            $params['endDate']
        );
        $totalPenjualan = $salesMetrics['total_value'];

        $profitMetrics = $this->transactionRepository->getProfitMetrics(
            $params['bankSampahId'],
            $params['startDate'],
            $params['endDate']
        );
        $labaKotor = $profitMetrics['laba_kotor'];

        // Get inventory summary
        $inventorySummary = $this->inventoryRepository->getInventorySummary($params['bankSampahId']);

        // Get bank sampah list for filter
        $bankSampahList = BankSampah::orderBy('nama_bank_sampah')->get();

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

        // Get recent transactions for detail view
        $recentTransactions = $this->transactionRepository->getRecentTransactions(
            $params['bankSampahId'],
            $params['startDate'],
            $params['endDate'],
            50
        );

        // Get bank sampah list for filter
        $bankSampahList = BankSampah::orderBy('nama_bank_sampah')->get();

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

        // Get processing by method
        $processingByMethod = $this->transactionRepository->getProcessingByMethod(
            $params['bankSampahId'],
            $params['startDate'],
            $params['endDate']
        );

        // Get bank sampah list for filter
        $bankSampahList = BankSampah::orderBy('nama_bank_sampah')->get();

        return view('reports.pengolahan', [
            'processingMetrics' => $processingMetrics,
            'processingByMethod' => $processingByMethod,
            'bankSampahList' => $bankSampahList,
            'periode' => $params['periode'],
            'startDate' => $params['startDate'],
            'endDate' => $params['endDate'],
            'selectedBankSampah' => $params['bankSampahId'],
        ]);
    }

    /**
     * Export Laporan Laba Rugi to Excel.
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
        $profitMetrics = $this->transactionRepository->getProfitMetrics(
            $params['bankSampahId'],
            $params['startDate'],
            $params['endDate']
        );
        $inventorySummary = $this->inventoryRepository->getInventorySummary($params['bankSampahId']);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Laporan Laba Rugi');

        // Header
        $sheet->setCellValue('A1', 'LAPORAN LABA RUGI');
        $sheet->setCellValue('A2', 'Periode: ' . $params['startDate']->format('d/m/Y') . ' - ' . $params['endDate']->format('d/m/Y'));

        // Data
        $row = 4;
        $data = [
            ['Keterangan', 'Jumlah (Rp)'],
            ['Total Pembelian (Setoran)', number_format($purchaseStats['total_value'], 0, ',', '.')],
            ['Total Penjualan', number_format($salesMetrics['total_value'], 0, ',', '.')],
            ['Laba Kotor', number_format($profitMetrics['laba_kotor'], 0, ',', '.')],
            ['', ''],
            ['INVENTORY STATUS', ''],
            ['Sampah Tersimpan (Kg)', number_format($inventorySummary['stored_kg'], 2, ',', '.')],
            ['Sampah Terjual (Kg)', number_format($inventorySummary['sold_kg'], 2, ',', '.')],
            ['Sampah Diolah (Kg)', number_format($inventorySummary['processed_kg'], 2, ',', '.')],
        ];

        foreach ($data as $rowData) {
            $sheet->setCellValue('A' . $row, $rowData[0]);
            $sheet->setCellValue('B' . $row, $rowData[1]);
            $row++;
        }

        // Auto-size columns
        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);

        $writer = new Xlsx($spreadsheet);
        $filename = 'laporan_laba_rugi_' . date('Y-m-d_H-i-s') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
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
        $profitMetrics = $this->transactionRepository->getProfitMetrics(
            $params['bankSampahId'],
            $params['startDate'],
            $params['endDate']
        );
        $inventorySummary = $this->inventoryRepository->getInventorySummary($params['bankSampahId']);

        $pdf = Pdf::loadView('reports.pdf.laba-rugi', [
            'totalPembelian' => $purchaseStats['total_value'],
            'totalPenjualan' => $salesMetrics['total_value'],
            'labaKotor' => $profitMetrics['laba_kotor'],
            'inventorySummary' => $inventorySummary,
            'startDate' => $params['startDate'],
            'endDate' => $params['endDate'],
        ]);

        return $pdf->download('laporan_laba_rugi_' . date('Y-m-d') . '.pdf');
    }

    /**
     * Export Laporan Penjualan to Excel.
     */
    public function exportPenjualanExcel(Request $request)
    {
        $params = $this->getPeriodParams($request);
        $groupBy = $request->get('group_by', 'offtaker');

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

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Laporan Penjualan');

        // Header
        $sheet->setCellValue('A1', 'LAPORAN PENJUALAN');
        $sheet->setCellValue('A2', 'Periode: ' . $params['startDate']->format('d/m/Y') . ' - ' . $params['endDate']->format('d/m/Y'));
        $sheet->setCellValue('A3', 'Group By: ' . ucfirst($groupBy));

        // Column headers
        $row = 5;
        if ($groupBy === 'offtaker') {
            $sheet->setCellValue('A' . $row, 'Offtaker');
            $sheet->setCellValue('B' . $row, 'Jumlah Transaksi');
            $sheet->setCellValue('C' . $row, 'Total Quantity (Kg)');
            $sheet->setCellValue('D' . $row, 'Total Penjualan (Rp)');
            $sheet->setCellValue('E' . $row, 'Laba (Rp)');
        } else {
            $sheet->setCellValue('A' . $row, 'Jenis Sampah');
            $sheet->setCellValue('B' . $row, 'Jumlah Transaksi');
            $sheet->setCellValue('C' . $row, 'Total Quantity (Kg)');
            $sheet->setCellValue('D' . $row, 'Total Penjualan (Rp)');
            $sheet->setCellValue('E' . $row, 'Laba (Rp)');
        }

        $row++;
        foreach ($data as $item) {
            $sheet->setCellValue('A' . $row, $item['name'] ?? '-');
            $sheet->setCellValue('B' . $row, $item['count'] ?? 0);
            $sheet->setCellValue('C' . $row, number_format($item['total_quantity'] ?? 0, 2, ',', '.'));
            $sheet->setCellValue('D' . $row, number_format($item['total_value'] ?? 0, 0, ',', '.'));
            $sheet->setCellValue('E' . $row, number_format($item['profit'] ?? 0, 0, ',', '.'));
            $row++;
        }

        // Auto-size columns
        foreach (range('A', 'E') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'laporan_penjualan_' . date('Y-m-d_H-i-s') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    /**
     * Export Laporan Penjualan to PDF.
     */
    public function exportPenjualanPdf(Request $request)
    {
        $params = $this->getPeriodParams($request);
        $groupBy = $request->get('group_by', 'offtaker');

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

        $pdf = Pdf::loadView('reports.pdf.penjualan', [
            'data' => $data,
            'groupBy' => $groupBy,
            'startDate' => $params['startDate'],
            'endDate' => $params['endDate'],
        ]);

        return $pdf->download('laporan_penjualan_' . date('Y-m-d') . '.pdf');
    }

    /**
     * Export Laporan Pengolahan to Excel.
     */
    public function exportPengolahanExcel(Request $request)
    {
        $params = $this->getPeriodParams($request);

        $processingByMethod = $this->transactionRepository->getProcessingByMethod(
            $params['bankSampahId'],
            $params['startDate'],
            $params['endDate']
        );

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Laporan Pengolahan');

        // Header
        $sheet->setCellValue('A1', 'LAPORAN PENGOLAHAN SAMPAH');
        $sheet->setCellValue('A2', 'Periode: ' . $params['startDate']->format('d/m/Y') . ' - ' . $params['endDate']->format('d/m/Y'));

        // Column headers
        $row = 4;
        $sheet->setCellValue('A' . $row, 'Metode Pengolahan');
        $sheet->setCellValue('B' . $row, 'Jumlah Transaksi');
        $sheet->setCellValue('C' . $row, 'Total Quantity (Kg)');
        $sheet->setCellValue('D' . $row, 'Output Hasil (Kg)');

        $row++;
        foreach ($processingByMethod as $item) {
            $sheet->setCellValue('A' . $row, $item['method'] ?? '-');
            $sheet->setCellValue('B' . $row, $item['count'] ?? 0);
            $sheet->setCellValue('C' . $row, number_format($item['total_quantity'] ?? 0, 2, ',', '.'));
            $sheet->setCellValue('D' . $row, number_format($item['output_quantity'] ?? 0, 2, ',', '.'));
            $row++;
        }

        // Auto-size columns
        foreach (range('A', 'D') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'laporan_pengolahan_' . date('Y-m-d_H-i-s') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
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

        $processingByMethod = $this->transactionRepository->getProcessingByMethod(
            $params['bankSampahId'],
            $params['startDate'],
            $params['endDate']
        );

        $pdf = Pdf::loadView('reports.pdf.pengolahan', [
            'processingMetrics' => $processingMetrics,
            'processingByMethod' => $processingByMethod,
            'startDate' => $params['startDate'],
            'endDate' => $params['endDate'],
        ]);

        return $pdf->download('laporan_pengolahan_' . date('Y-m-d') . '.pdf');
    }
}
