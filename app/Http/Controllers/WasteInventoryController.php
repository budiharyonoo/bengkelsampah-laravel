<?php

namespace App\Http\Controllers;

use App\Models\BankSampah;
use App\Models\WasteInventory;
use App\Repositories\InventoryRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WasteInventoryController extends Controller
{
    public function __construct(
        private InventoryRepository $inventoryRepository
    ) {}

    /**
     * Display inventory summary.
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

        // Get summary metrics
        $summary = $this->inventoryRepository->getInventorySummary($bankSampahId);

        // Get inventory data grouped by category
        if ($bankSampahId) {
            // Single bank sampah view
            $bankSampah = BankSampah::find($bankSampahId);
            $categoryGrouped = $this->inventoryRepository->getInventoryGroupedByCategory($bankSampahId);
            $inventoryGrouped = collect([$bankSampahId => $categoryGrouped]);
        } else {
            // All bank sampah view
            $bankSampah = null;
            $inventoryGrouped = $this->inventoryRepository->getAllInventoryGroupedByCategory();
        }

        // Get low stock alerts
        $lowStockAlerts = $this->inventoryRepository->getLowStockAlerts(10.0);
        if ($bankSampahId) {
            $lowStockAlerts = $lowStockAlerts->where('bank_sampah_id', $bankSampahId);
        }

        // Get top waste types
        $topWasteTypes = $this->inventoryRepository->getTopWasteTypes($bankSampahId, 10);

        // Get bank sampah list for filter
        $bankSampahList = BankSampah::select('id', 'nama_bank_sampah')->get();

        return view('waste-inventory.index', compact(
            'summary',
            'inventoryGrouped',
            'bankSampah',
            'lowStockAlerts',
            'topWasteTypes',
            'bankSampahList',
            'bankSampahId'
        ));
    }

    /**
     * Display inventory for a specific bank sampah.
     */
    public function show(BankSampah $bankSampah)
    {
        $admin = Auth::guard('admin')->user();

        // Check if admin has access to this bank sampah
        if ($admin->role !== 'admin' && $admin->id_bank_sampah && $admin->id_bank_sampah !== $bankSampah->id) {
            abort(403, 'Anda tidak memiliki akses ke inventori bank sampah ini.');
        }

        // Get summary for this bank sampah
        $summary = $this->inventoryRepository->getInventorySummary($bankSampah->id);

        // Get inventory items
        $inventory = $this->inventoryRepository->getInventoryByBankSampah($bankSampah->id);

        // Get inventory value by type
        $inventoryByType = $this->inventoryRepository->getInventoryValueByType($bankSampah->id);

        // Get trend for last 30 days
        $trend = $this->inventoryRepository->getInventoryTrend(
            $bankSampah->id,
            Carbon::now()->subDays(30),
            Carbon::now()
        );

        // Get low stock items
        $lowStockItems = $inventory->where('quantity', '<', 10);

        return view('waste-inventory.show', compact(
            'bankSampah',
            'summary',
            'inventory',
            'inventoryByType',
            'trend',
            'lowStockItems'
        ));
    }

    /**
     * Get inventory data for API/AJAX requests.
     */
    public function getInventoryData(Request $request)
    {
        $bankSampahId = $request->bank_sampah_id;

        if (! $bankSampahId) {
            return response()->json([
                'success' => false,
                'message' => 'Bank Sampah ID required',
            ], 400);
        }

        $inventory = WasteInventory::query()
            ->with('sampah:id,nama,satuan,gambar')
            ->where('bank_sampah_id', $bankSampahId)
            ->where('quantity', '>', 0)
            ->get()
            ->map(function ($item) {
                return [
                    'sampah_id' => $item->sampah_id,
                    'sampah_nama' => $item->sampah->nama,
                    'sampah_satuan' => $item->unit,
                    'sampah_gambar' => $item->sampah->gambar,
                    'quantity' => $item->quantity,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $inventory,
        ]);
    }
}
