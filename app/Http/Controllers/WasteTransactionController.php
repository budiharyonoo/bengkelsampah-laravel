<?php

namespace App\Http\Controllers;

use App\Models\BankSampah;
use App\Models\Offtaker;
use App\Models\Sampah;
use App\Models\WasteTracking;
use App\Models\WasteTransaction;
use App\Repositories\WasteTransactionRepository;
use App\Services\Inventory\InventoryService;
use App\Services\Inventory\WasteTransactionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WasteTransactionController extends Controller
{
    public function __construct(
        private WasteTransactionService $transactionService,
        private WasteTransactionRepository $transactionRepository,
        private InventoryService $inventoryService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $admin = Auth::guard('admin')->user();
        $bankSampahId = null;

        // If admin is branch-specific, filter by their bank sampah
        if ($admin->role !== 'admin' && $admin->id_bank_sampah) {
            $bankSampahId = $admin->id_bank_sampah;
        } elseif ($request->filled('bank_sampah_id')) {
            $bankSampahId = $request->bank_sampah_id;
        }

        $query = WasteTransaction::query()
            ->with(['bankSampah:id,nama_bank_sampah,kode_bank_sampah', 'offtaker:id,nama,kode_offtaker']);

        if ($bankSampahId) {
            $query->where('bank_sampah_id', $bankSampahId);
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by offtaker
        if ($request->filled('offtaker_id')) {
            $query->where('offtaker_id', $request->offtaker_id);
        }

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->whereDate('tanggal_transaksi', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('tanggal_transaksi', '<=', $request->end_date);
        }

        // Search by code
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('kode_transaksi', 'like', "%{$search}%");
        }

        $transactions = $query->orderByDesc('tanggal_transaksi')
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        $bankSampahList = BankSampah::select('id', 'nama_bank_sampah')->get();
        $offtakerList = Offtaker::active()->select('id', 'nama', 'kode_offtaker')->get();

        return view('waste-transactions.index', compact('transactions', 'bankSampahList', 'offtakerList'));
    }

    /**
     * Display a listing of sales and processing transactions (merged view).
     */
    public function salesIndex(Request $request)
    {
        $admin = Auth::guard('admin')->user();
        $bankSampahId = null;

        if ($admin->role !== 'admin' && $admin->id_bank_sampah) {
            $bankSampahId = $admin->id_bank_sampah;
        } elseif ($request->filled('bank_sampah_id')) {
            $bankSampahId = $request->bank_sampah_id;
        }

        $query = WasteTransaction::query()
            ->with(['bankSampah:id,nama_bank_sampah,kode_bank_sampah', 'offtaker:id,nama,kode_offtaker']);

        // Filter by type if specified
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($bankSampahId) {
            $query->where('bank_sampah_id', $bankSampahId);
        }

        if ($request->filled('offtaker_id')) {
            $query->where('offtaker_id', $request->offtaker_id);
        }

        if ($request->filled('start_date')) {
            $query->whereDate('tanggal_transaksi', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('tanggal_transaksi', '<=', $request->end_date);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('kode_transaksi', 'like', "%{$search}%");
        }

        $transactions = $query->orderByDesc('tanggal_transaksi')
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        $bankSampahList = BankSampah::select('id', 'nama_bank_sampah')->get();
        $offtakerList = Offtaker::active()->buyers()->select('id', 'nama', 'kode_offtaker')->get();

        $pageTitle = 'Penjualan & Pengolahan';
        $transactionType = 'all'; // Changed from 'sale' to 'all'
        $createRoute = 'waste-transactions.sales.create';
        $createLabel = 'Tambah Transaksi Sampah';
        $indexRoute = 'waste-transactions.sales.index';

        return view('waste-transactions.index', compact(
            'transactions',
            'bankSampahList',
            'offtakerList',
            'pageTitle',
            'transactionType',
            'createRoute',
            'createLabel',
            'indexRoute'
        ));
    }

    /**
     * Display a listing of processing transactions (type = processing).
     */
    public function processingIndex(Request $request)
    {
        $admin = Auth::guard('admin')->user();
        $bankSampahId = null;

        if ($admin->role !== 'admin' && $admin->id_bank_sampah) {
            $bankSampahId = $admin->id_bank_sampah;
        } elseif ($request->filled('bank_sampah_id')) {
            $bankSampahId = $request->bank_sampah_id;
        }

        $query = WasteTransaction::query()
            ->with(['bankSampah:id,nama_bank_sampah,kode_bank_sampah', 'offtaker:id,nama,kode_offtaker'])
            ->where('type', WasteTransaction::TYPE_PROCESSING);

        if ($bankSampahId) {
            $query->where('bank_sampah_id', $bankSampahId);
        }

        if ($request->filled('offtaker_id')) {
            $query->where('offtaker_id', $request->offtaker_id);
        }

        if ($request->filled('start_date')) {
            $query->whereDate('tanggal_transaksi', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('tanggal_transaksi', '<=', $request->end_date);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('kode_transaksi', 'like', "%{$search}%");
        }

        $transactions = $query->orderByDesc('tanggal_transaksi')
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        $bankSampahList = BankSampah::select('id', 'nama_bank_sampah')->get();
        $offtakerList = Offtaker::active()->processors()->select('id', 'nama', 'kode_offtaker')->get();

        $pageTitle = 'Pengolahan Sampah';
        $transactionType = 'processing';
        $createRoute = 'waste-transactions.processing.create';
        $createLabel = 'Tambah Pengolahan Sampah';
        $indexRoute = 'waste-transactions.processing.index';

        return view('waste-transactions.index', compact(
            'transactions',
            'bankSampahList',
            'offtakerList',
            'pageTitle',
            'transactionType',
            'createRoute',
            'createLabel',
            'indexRoute'
        ));
    }

    /**
     * Show the form for creating a new sale.
     */
    public function createSale(Request $request)
    {
        $admin = Auth::guard('admin')->user();
        $bankSampahId = $admin->id_bank_sampah ?? $request->bank_sampah_id;

        $bankSampahList = BankSampah::select('id', 'nama_bank_sampah', 'kode_bank_sampah')->get();
        // Load processors by default (for 'sale' type) - will be dynamically filtered by JS
        $offtakerList = Offtaker::active()->processors()->select('id', 'nama', 'kode_offtaker')->get();
        $sampahList = Sampah::select('id', 'nama', 'satuan', 'gambar')->get();

        // Get available inventory if bank sampah is selected
        $inventory = [];
        if ($bankSampahId) {
            $inventory = $this->inventoryService->getAvailableStock((int) $bankSampahId);
        }

        return view('waste-transactions.sales-create', compact(
            'bankSampahList',
            'offtakerList',
            'sampahList',
            'inventory',
            'bankSampahId'
        ));
    }

    /**
     * Get offtakers filtered by transaction type.
     */
    public function getOfftakersByType(Request $request)
    {
        $type = $request->get('type', 'sale');

        $query = Offtaker::active()->select('id', 'nama', 'kode_offtaker', 'tipe');

        if ($type === 'processing') {
            // For processing: show buyers and both
            $query->processors();
        } else {
            // For sale: show processors and both
            $query->buyers();
        }

        $offtakers = $query->get();

        return response()->json($offtakers);
    }

    /**
     * Store a newly created sale or processing transaction.
     */
    public function storeSale(Request $request)
    {
        $validated = $request->validate([
            'bank_sampah_id' => 'required|int|exists:bank_sampah,id',
            'offtaker_id' => 'required|int|exists:offtakers,id',
            'type' => 'required|in:sale,processing',
            'metode_pengolahan' => 'required_if:type,processing|nullable|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.sampah_id' => 'required|int|exists:sampah,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.harga_jual' => 'required|numeric|min:0',
            'tanggal_transaksi' => 'required|date',
            'struk' => 'nullable|image|max:2048',
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            $admin = Auth::guard('admin')->user();

            // Handle file upload
            if ($request->hasFile('struk')) {
                $validated['struk'] = $request->file('struk')->store('uploads/struk', 'public');
            }

            $validated['bank_sampah_id'] = (int) $validated['bank_sampah_id'];
            $validated['offtaker_id'] = (int) $validated['offtaker_id'];

            $transaction = $this->transactionService->createTransaction($validated, $admin);

            $typeLabel = $validated['type'] === 'sale' ? 'Penjualan' : 'Pengolahan';

            return redirect()
                ->route('waste-transactions.show', $transaction)
                ->with('success', "{$typeLabel} berhasil dicatat dengan kode {$transaction->kode_transaksi}");
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Gagal mencatat transaksi: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new processing transaction.
     */
    public function createProcessing(Request $request)
    {
        $admin = Auth::guard('admin')->user();
        $bankSampahId = $admin->id_bank_sampah ?? $request->bank_sampah_id;

        $bankSampahList = BankSampah::select('id', 'nama_bank_sampah', 'kode_bank_sampah')->get();
        $offtakerList = Offtaker::active()->processors()->select('id', 'nama', 'kode_offtaker')->get();
        $sampahList = Sampah::select('id', 'nama', 'satuan', 'gambar')->get();

        // Get available inventory if bank sampah is selected
        $inventory = [];
        if ($bankSampahId) {
            $inventory = $this->inventoryService->getAvailableStock((int) $bankSampahId);
        }

        return view('waste-transactions.processing-create', compact(
            'bankSampahList',
            'offtakerList',
            'sampahList',
            'inventory',
            'bankSampahId'
        ));
    }

    /**
     * Store a newly created processing transaction.
     */
    public function storeProcessing(Request $request)
    {
        $validated = $request->validate([
            'bank_sampah_id' => 'required|int|exists:bank_sampah,id',
            'offtaker_id' => 'required|int|exists:offtakers,id',
            'items' => 'required|array|min:1',
            'items.*.sampah_id' => 'required|int|exists:sampah,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'metode_pengolahan' => 'required|string|max:255',
            'hasil_pengolahan' => 'nullable|string',
            'tanggal_transaksi' => 'required|date',
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            $admin = Auth::guard('admin')->user();

            $validated['bank_sampah_id'] = (int) $validated['bank_sampah_id'];
            $validated['offtaker_id'] = (int) $validated['offtaker_id'];

            $transaction = $this->transactionService->createProcessing($validated, $admin);

            return redirect()
                ->route('waste-transactions.show', $transaction)
                ->with('success', "Pengolahan berhasil dicatat dengan kode {$transaction->kode_transaksi}");
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Gagal mencatat pengolahan: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(WasteTransaction $wasteTransaction)
    {
        $wasteTransaction->load([
            'bankSampah:id,nama_bank_sampah,kode_bank_sampah,alamat_bank_sampah',
            'offtaker:id,nama,kode_offtaker,kontak_pic',
            'trackings.sampah:id,nama,satuan',
        ]);

        // Enrich items with sampah names
        $sampahIds = collect($wasteTransaction->items_json)->pluck('sampah_id')->unique();
        $sampahList = Sampah::whereIn('id', $sampahIds)->pluck('nama', 'id');

        return view('waste-transactions.show', compact('wasteTransaction', 'sampahList'));
    }

    /**
     * Get available inventory for a bank sampah (AJAX).
     */
    public function getInventory(Request $request)
    {
        $bankSampahId = $request->bank_sampah_id;

        if (! $bankSampahId) {
            return response()->json([]);
        }

        $inventory = $this->inventoryService->getAvailableStock((int) $bankSampahId);

        return response()->json($inventory->map(function ($item) {
            // Calculate weighted average purchase price from tracking data
            $trackingData = WasteTracking::where('bank_sampah_id', $item->bank_sampah_id)
                ->where('sampah_id', $item->sampah_id)
                ->where('status', WasteTracking::STATUS_STORED)
                ->selectRaw('SUM(quantity) as total_qty, SUM(harga_beli) as total_harga_beli')
                ->first();

            $avgHargaBeli = 0;
            if ($trackingData && $trackingData->total_qty > 0) {
                $avgHargaBeli = $trackingData->total_harga_beli / $trackingData->total_qty;
            }

            return [
                'sampah_id' => $item->sampah_id,
                'sampah_nama' => $item->sampah->nama,
                'sampah_satuan' => $item->unit,
                'quantity' => $item->quantity,
                'avg_harga_beli' => round($avgHargaBeli, 2),
            ];
        }));
    }

    /**
     * Export waste transactions to Excel.
     */
    public function exportExcel(Request $request)
    {
        $transactions = $this->getFilteredTransactions($request);

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();

        $type = $request->type;
        $typeLabel = $type === 'sale' ? 'Penjualan' : ($type === 'processing' ? 'Pengolahan' : 'Transaksi');
        $isProcessing = $type === 'processing';

        // Set headers - always include Tipe column
        if ($isProcessing) {
            $headers = ['No', 'Kode Trx / Tanggal', 'Tipe', 'Bank Sampah', 'Item Sampah', 'Total Qty (kg)', 'Metode Daur Ulang', 'Catatan'];
        } elseif ($type === 'sale') {
            $headers = ['No', 'Kode Trx / Tanggal', 'Tipe', 'Bank Sampah', 'Item Sampah', 'Total Qty (kg)', 'Total Harga (Rp)', 'Catatan'];
        } else {
            // When exporting all types, include both columns
            $headers = ['No', 'Kode Trx / Tanggal', 'Tipe', 'Bank Sampah', 'Item Sampah', 'Total Qty (kg)', 'Total Harga (Rp)', 'Metode Daur Ulang', 'Catatan'];
        }

        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '1', $header);
            $sheet->getStyle($col . '1')->getFont()->setBold(true);
            $sheet->getStyle($col . '1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
            $sheet->getStyle($col . '1')->getFill()->getStartColor()->setARGB('FF39746E');
            $sheet->getStyle($col . '1')->getFont()->getColor()->setARGB('FFFFFFFF');
            $col++;
        }

        // Add data - one row per transaction
        $row = 2;
        $no = 1;
        foreach ($transactions as $transaction) {
            $sampahModels = $transaction->sampahModels ?? collect();
            $col = 'A';

            // No
            $sheet->setCellValue($col++ . $row, $no++);

            // Kode Trx / Tanggal
            $kodeTrxDate = $transaction->kode_transaksi . "\n" .
                $transaction->tanggal_transaksi->format('d/m/Y');
            $sheet->setCellValue($col++ . $row, $kodeTrxDate);
            $sheet->getStyle(($col - 1) . $row)->getAlignment()->setWrapText(true);

            // Tipe
            $tipeLabel = $transaction->type === 'sale' ? 'Penjualan' : 'Pengolahan';
            $sheet->setCellValue($col++ . $row, $tipeLabel);

            // Bank Sampah - show actual bank name
            $bankSampahName = $transaction->bankSampah->nama_bank_sampah ?? '-';
            $sheet->setCellValue($col++ . $row, $bankSampahName);

            // Item Sampah - merge all items with line breaks
            $itemsText = [];
            $totalQuantity = 0;
            $totalHarga = 0;

            if (! empty($transaction->items_json) && is_array($transaction->items_json)) {
                foreach ($transaction->items_json as $item) {
                    $sampahId = $item['sampah_id'] ?? null;
                    $sampahName = '-';
                    if ($sampahId && isset($sampahModels[$sampahId])) {
                        $sampahName = $sampahModels[$sampahId]->nama;
                    }

                    $quantity = $item['quantity'] ?? 0;
                    $totalQuantity += $quantity;

                    if ($isProcessing) {
                        // For processing: only show name and quantity, no price
                        $itemsText[] = $sampahName . ' (' . number_format($quantity, 2, ',', '.') . ' kg)';
                    } else {
                        // For sale: show name, quantity, and price
                        $harga = $item['harga_jual'] ?? 0;
                        $totalHarga += $harga;
                        $itemsText[] = $sampahName . ' (' . number_format($quantity, 2, ',', '.') . ' kg @ Rp ' . number_format($harga, 0, ',', '.') . ')';
                    }
                }
            }

            $itemsSampahText = ! empty($itemsText) ? implode("\n", $itemsText) : '-';
            $sheet->setCellValue($col++ . $row, $itemsSampahText);
            $sheet->getStyle(($col - 1) . $row)->getAlignment()->setWrapText(true);

            // Total Quantity
            $sheet->setCellValue($col++ . $row, $totalQuantity);

            // Total Harga / Metode Daur Ulang based on type filter
            if ($isProcessing) {
                // Processing only export
                $metodeDaurUlang = $transaction->metode_pengolahan ?? '-';
                $sheet->setCellValue($col++ . $row, $metodeDaurUlang);
            } elseif ($type === 'sale') {
                // Sale only export
                $sheet->setCellValue($col++ . $row, $totalHarga);
            } else {
                // All types export - show both columns
                $sheet->setCellValue($col++ . $row, $totalHarga);
                $metodeDaurUlang = $transaction->metode_pengolahan ?? '-';
                $sheet->setCellValue($col++ . $row, $metodeDaurUlang);
            }

            // Catatan
            $notes = $transaction->notes ?? '-';
            $sheet->setCellValue($col++ . $row, $notes);

            $row++;
        }

        // Auto-size columns
        foreach (range('A', 'I') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        $filename = 'laporan_' . strtolower($typeLabel) . '_sampah_' . date('Y-m-d') . '.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    /**
     * Export waste transactions to PDF.
     */
    public function exportPdf(Request $request)
    {
        $transactions = $this->getFilteredTransactions($request);
        $type = $request->type;
        $typeLabel = $type === 'sale' ? 'Penjualan Sampah' : ($type === 'processing' ? 'Pengolahan Sampah' : 'Transaksi Sampah');

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.waste-transactions-report', [
            'transactions' => $transactions,
            'type' => $type,
            'typeLabel' => $typeLabel,
            'generatedAt' => now()->format('d/m/Y H:i'),
        ]);

        $pdf->setPaper('A4', 'landscape');

        $filename = 'laporan_' . strtolower(str_replace(' ', '_', $typeLabel)) . '_' . date('Y-m-d') . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Export waste transactions to CSV.
     */
    public function exportCsv(Request $request)
    {
        $transactions = $this->getFilteredTransactions($request);

        $type = $request->type;
        $typeLabel = $type === 'sale' ? 'penjualan' : ($type === 'processing' ? 'pengolahan' : 'transaksi');
        $isProcessing = $type === 'processing';

        $filename = 'laporan_' . strtolower($typeLabel) . '_sampah_' . date('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response()->stream(function () use ($transactions, $isProcessing) {
            $handle = fopen('php://output', 'w');

            // Add BOM for UTF-8
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Write headers - always include Tipe column
            if ($isProcessing) {
                fputcsv($handle, ['No', 'Kode Trx / Tanggal', 'Tipe', 'Bank Sampah', 'Item Sampah', 'Total Qty (kg)', 'Metode Daur Ulang', 'Catatan']);
            } elseif ($type === 'sale') {
                fputcsv($handle, ['No', 'Kode Trx / Tanggal', 'Tipe', 'Bank Sampah', 'Item Sampah', 'Total Qty (kg)', 'Total Harga (Rp)', 'Catatan']);
            } else {
                // When exporting all types, include both columns
                fputcsv($handle, ['No', 'Kode Trx / Tanggal', 'Tipe', 'Bank Sampah', 'Item Sampah', 'Total Qty (kg)', 'Total Harga (Rp)', 'Metode Daur Ulang', 'Catatan']);
            }

            // Write data - one row per transaction
            $no = 1;
            foreach ($transactions as $transaction) {
                $sampahModels = $transaction->sampahModels ?? collect();

                $kodeTrxDate = $transaction->kode_transaksi . ' / ' . $transaction->tanggal_transaksi->format('d/m/Y');

                // Tipe
                $tipeLabel = $transaction->type === 'sale' ? 'Penjualan' : 'Pengolahan';

                // Bank Sampah - show actual bank name
                $bankSampahName = $transaction->bankSampah->nama_bank_sampah ?? '-';

                // Item Sampah - merge all items
                $itemsText = [];
                $totalQuantity = 0;
                $totalHarga = 0;

                if (! empty($transaction->items_json) && is_array($transaction->items_json)) {
                    foreach ($transaction->items_json as $item) {
                        $sampahId = $item['sampah_id'] ?? null;
                        $sampahName = '-';
                        if ($sampahId && isset($sampahModels[$sampahId])) {
                            $sampahName = $sampahModels[$sampahId]->nama;
                        }

                        $quantity = $item['quantity'] ?? 0;
                        $totalQuantity += $quantity;

                        if ($isProcessing) {
                            // For processing: only show name and quantity, no price
                            $itemsText[] = $sampahName . ' (' . number_format($quantity, 2, ',', '.') . ' kg)';
                        } else {
                            // For sale: show name, quantity, and price
                            $harga = $item['harga_jual'] ?? 0;
                            $totalHarga += $harga;
                            $itemsText[] = $sampahName . ' (' . number_format($quantity, 2, ',', '.') . ' kg @ Rp ' . number_format($harga, 0, ',', '.') . ')';
                        }
                    }
                }

                $itemsSampahText = ! empty($itemsText) ? implode('; ', $itemsText) : '-';

                $notes = $transaction->notes ?? '-';

                // Different columns based on type filter
                if ($isProcessing) {
                    // Processing only export
                    $metodeDaurUlang = $transaction->metode_pengolahan ?? '-';
                    fputcsv($handle, [
                        $no++,
                        $kodeTrxDate,
                        $tipeLabel,
                        $bankSampahName,
                        $itemsSampahText,
                        $totalQuantity,
                        $metodeDaurUlang,
                        $notes,
                    ]);
                } elseif ($type === 'sale') {
                    // Sale only export
                    fputcsv($handle, [
                        $no++,
                        $kodeTrxDate,
                        $tipeLabel,
                        $bankSampahName,
                        $itemsSampahText,
                        $totalQuantity,
                        $totalHarga,
                        $notes,
                    ]);
                } else {
                    // All types export - include both columns
                    $metodeDaurUlang = $transaction->metode_pengolahan ?? '-';
                    fputcsv($handle, [
                        $no++,
                        $kodeTrxDate,
                        $tipeLabel,
                        $bankSampahName,
                        $itemsSampahText,
                        $totalQuantity,
                        $totalHarga,
                        $metodeDaurUlang,
                        $notes,
                    ]);
                }
            }

            fclose($handle);
        }, 200, $headers);
    }

    /**
     * Get filtered transactions for export.
     */
    private function getFilteredTransactions(Request $request)
    {
        $admin = Auth::guard('admin')->user();
        $query = WasteTransaction::query()
            ->with(['bankSampah:id,nama_bank_sampah,kode_bank_sampah', 'offtaker:id,nama,kode_offtaker']);

        // Filter by admin's bank sampah if branch-specific
        if ($admin->role !== 'admin' && $admin->id_bank_sampah) {
            $query->where('bank_sampah_id', $admin->id_bank_sampah);
        } elseif ($request->filled('bank_sampah_id')) {
            $query->where('bank_sampah_id', $request->bank_sampah_id);
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by offtaker
        if ($request->filled('offtaker_id')) {
            $query->where('offtaker_id', $request->offtaker_id);
        }

        // Filter by time period
        if ($request->filled('time_filter')) {
            $timeFilter = $request->time_filter;
            $now = now();

            switch ($timeFilter) {
                case 'harian':
                    $query->whereDate('tanggal_transaksi', $now->toDateString());
                    break;
                case 'mingguan':
                    $query->whereBetween('tanggal_transaksi', [$now->startOfWeek()->toDateString(), $now->endOfWeek()->toDateString()]);
                    break;
                case 'bulanan':
                    $query->whereMonth('tanggal_transaksi', $now->month)
                        ->whereYear('tanggal_transaksi', $now->year);
                    break;
                case 'range':
                    if ($request->filled('start_date')) {
                        $query->whereDate('tanggal_transaksi', '>=', $request->start_date);
                    }
                    if ($request->filled('end_date')) {
                        $query->whereDate('tanggal_transaksi', '<=', $request->end_date);
                    }
                    break;
            }
        }

        $transactions = $query->orderByDesc('tanggal_transaksi')->get();

        // Load all sampah models referenced in items_json
        $sampahIds = [];
        foreach ($transactions as $transaction) {
            if (! empty($transaction->items_json)) {
                foreach ($transaction->items_json as $item) {
                    if (isset($item['sampah_id'])) {
                        $sampahIds[] = $item['sampah_id'];
                    }
                }
            }
        }

        $sampahIds = array_unique($sampahIds);
        $sampahModels = \App\Models\Sampah::whereIn('id', $sampahIds)->get()->keyBy('id');

        // Attach sampah models to each transaction for easy access
        foreach ($transactions as $transaction) {
            $transaction->sampahModels = $sampahModels;
        }

        return $transactions;
    }
}
