<?php

namespace App\Http\Controllers;

use App\Models\BankSampah;
use App\Models\Point;
use App\Models\Setoran;
use App\Models\User;
use App\Services\Inventory\InventoryService;
use App\Services\NotificationService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class DashboardTransaksiController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function index(Request $request)
    {
        try {
            $query = Setoran::query();
            $admin = Auth::guard('admin')->user();
            $isCabang = $admin->role !== 'admin' && $admin->id_bank_sampah;
            if ($isCabang) {
                $query->where('bank_sampah_id', $admin->id_bank_sampah);
            } elseif ($request->has('bank_sampah_id') && $request->bank_sampah_id) {
                $query->where('bank_sampah_id', $request->bank_sampah_id);
            }

            // Filter by tipe setor
            if ($request->has('tipe_setor') && $request->tipe_setor) {
                $query->where('tipe_setor', $request->tipe_setor);
            }

            // Filter by status
            if ($request->has('status') && $request->status) {
                $query->where('status', $request->status);
            }

            // Filter by date range
            if ($request->has('start_date') && $request->start_date) {
                $query->whereDate('created_at', '>=', $request->start_date);
            }
            if ($request->has('end_date') && $request->end_date) {
                $query->whereDate('created_at', '<=', $request->end_date);
            }

            // Search by user name or transaction ID
            if ($request->has('search') && $request->search) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('id', 'LIKE', "%{$search}%")
                        ->orWhere('user_name', 'LIKE', "%{$search}%")
                        ->orWhere('user_identifier', 'LIKE', "%{$search}%");
                });
            }

            // Get paginated results
            $transaksi = $query->orderBy('created_at', 'desc')
                ->paginate(10);

            // Get all bank sampah for filter dropdown
            $bankSampahList = \App\Models\BankSampah::select('id', 'nama_bank_sampah')->get();

            return view('dashboard-transaksi', compact(
                'transaksi',
                'bankSampahList'
            ));

        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: '.$e->getMessage());
        }
    }

    public function show($id)
    {
        try {
            $transaction = Setoran::findOrFail($id);

            return view('dashboard-transaksi-detail', compact('transaction'));

        } catch (\Exception $e) {
            return back()->with('error', 'Transaksi tidak ditemukan');
        }
    }

    public function updateStatus(Request $request, $id)
    {
        try {
            $request->validate([
                'status' => 'required|in:dikonfirmasi,diproses,dijemput,selesai,batal',
                'alasan_pembatalan' => 'required_if:status,batal|nullable|string|max:500',
                'petugas_nama' => 'required_if:status,dijemput|nullable|string|max:255',
                'petugas_contact' => 'required_if:status,dijemput|nullable|string|max:255',
                'items_json' => 'nullable|string',
                'aktual_total' => 'nullable|numeric|min:0',
            ]);

            $transaction = Setoran::findOrFail($id);
            $oldStatus = $transaction->status;

            $transaction->status = $request->status;

            if ($request->status === 'batal' && $request->alasan_pembatalan) {
                $transaction->alasan_pembatalan = $request->alasan_pembatalan;
            }

            if ($request->status === 'dijemput') {
                $transaction->petugas_nama = $request->petugas_nama;
                $transaction->petugas_contact = $request->petugas_contact;
            }

            // Handle items and actual total for selesai status
            if ($request->status === 'selesai') {
                if ($request->items_json) {
                    $transaction->items_json = $request->items_json;

                    // Log the items_json for debugging
                    \Log::info("Items JSON received for transaction {$id}:", [
                        'items_json' => $request->items_json,
                        'decoded_items' => json_decode($request->items_json, true),
                    ]);
                }
                if ($request->aktual_total) {
                    $transaction->aktual_total = $request->aktual_total;
                }

                // Set tanggal_selesai when status is completed
                $transaction->tanggal_selesai = now();

                // Calculate and add points + XP
                $this->addPointsAndXP($transaction, $request->aktual_total);

                // Add waste to inventory tracking when setoran is completed
                app(InventoryService::class)->addFromSetoran($transaction);
            }

            $transaction->save();

            // Send notification to user based on status change
            $this->sendStatusChangeNotification($transaction, $oldStatus, $request->status);

            // Log status change
            \Log::info("Transaction {$id} status changed from {$oldStatus} to {$request->status}");

            return back()->with('success', 'Status transaksi berhasil diperbarui');

        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: '.$e->getMessage());
        }
    }

    /**
     * Add points and XP to user when setoran is completed
     */
    private function addPointsAndXP($setoran, $aktualTotal)
    {
        try {
            // Calculate points based on tipe_setor
            $points = 0;
            if ($setoran->tipe_setor === 'tabung') {
                // Points = total aktual (1 point per 1 rupiah) for tabung
                $points = $aktualTotal;
            }
            // For 'jual' and 'sedekah', points = 0

            // XP = total aktual / 1000 (regardless of tipe_setor)
            $xp = $aktualTotal / 1000;

            // Calculate total actual weight from items
            $items = json_decode($setoran->items_json, true);
            $totalWeight = 0;
            if (is_array($items)) {
                foreach ($items as $item) {
                    if (isset($item['aktual_berat']) && $item['status'] !== 'dihapus') {
                        $totalWeight += $item['aktual_berat'];
                    }
                }
            }

            // Create point record
            \App\Models\Point::create([
                'user_id' => $setoran->user_id,
                'user_name' => $setoran->user_name,
                'user_identifier' => $setoran->user_identifier,
                'type' => \App\Models\Point::TYPE_SETOR,
                'tanggal' => now()->toDateString(),
                'jumlah_point' => $points,
                'xp' => $xp,
                'setoran_id' => $setoran->id,
                'keterangan' => "Setoran sampah #{$setoran->id} - {$setoran->tipe_setor} - Total: Rp ".number_format($aktualTotal),
            ]);

            // Update user's stats
            $user = \App\Models\User::find($setoran->user_id);
            if ($user) {
                $user->increment('poin', $points);
                $user->increment('xp', $xp);
                $user->increment('setor', 1); // Increment setor count by 1
                $user->increment('sampah', $totalWeight); // Add actual total weight
            }

        } catch (\Exception $e) {
            \Log::error('Error adding points and XP: '.$e->getMessage());
            throw $e;
        }
    }

    /**
     * Send notification to user when setoran status changes
     */
    private function sendStatusChangeNotification($transaction, $oldStatus, $newStatus)
    {
        try {
            $user = \App\Models\User::find($transaction->user_id);
            if (! $user) {
                \Log::warning('User not found for notification', ['user_id' => $transaction->user_id]);

                return;
            }

            $title = '';
            $body = '';
            $type = 'setoran_status';
            $data = [
                'setoran_id' => $transaction->id,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'bank_sampah_name' => $transaction->bank_sampah_name,
            ];

            switch ($newStatus) {
                case 'dikonfirmasi':
                    $title = 'Setoran Dikonfirmasi';
                    $body = "Setoran sampah Anda telah dikonfirmasi oleh {$transaction->bank_sampah_name}. Tim akan segera memproses setoran Anda.";
                    break;

                case 'diproses':
                    $title = 'Setoran Sedang Diproses';
                    $body = "Setoran sampah Anda sedang diproses oleh {$transaction->bank_sampah_name}. Mohon tunggu hingga selesai.";
                    break;

                case 'dijemput':
                    $title = 'Petugas Akan Menjemput';
                    $body = "Petugas {$transaction->petugas_nama} akan menjemput setoran sampah Anda. Kontak: {$transaction->petugas_contact}";
                    $data['petugas_nama'] = $transaction->petugas_nama;
                    $data['petugas_contact'] = $transaction->petugas_contact;
                    break;

                case 'selesai':
                    $title = 'Setoran Selesai';
                    $body = "Setoran sampah Anda telah selesai diproses oleh {$transaction->bank_sampah_name}.";

                    // Add points and XP info if available
                    if ($transaction->aktual_total) {
                        $points = 0;
                        if ($transaction->tipe_setor === 'tabung') {
                            $points = $transaction->aktual_total;
                        }
                        $xp = $transaction->aktual_total / 1000;

                        $body .= " Anda mendapatkan {$points} poin dan ".number_format($xp, 1).' XP.';

                        $data['points_earned'] = $points;
                        $data['xp_earned'] = $xp;
                        $data['aktual_total'] = $transaction->aktual_total;
                    }
                    break;

                case 'batal':
                    $title = 'Setoran Dibatalkan';
                    $body = "Setoran sampah Anda telah dibatalkan oleh {$transaction->bank_sampah_name}.";
                    if ($transaction->alasan_pembatalan) {
                        $body .= " Alasan: {$transaction->alasan_pembatalan}";
                        $data['alasan_pembatalan'] = $transaction->alasan_pembatalan;
                    }
                    break;

                default:
                    $title = 'Status Setoran Diperbarui';
                    $body = "Status setoran sampah Anda telah diubah dari {$oldStatus} menjadi {$newStatus}.";
                    break;
            }

            // Send notification
            $this->notificationService->sendToUser(
                $user->id,
                $title,
                $body,
                $type,
                $data
            );

            \Log::info('Status change notification sent', [
                'user_id' => $user->id,
                'setoran_id' => $transaction->id,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'title' => $title,
            ]);

        } catch (\Exception $e) {
            \Log::error('Error sending status change notification: '.$e->getMessage(), [
                'setoran_id' => $transaction->id,
                'user_id' => $transaction->user_id,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
            ]);
        }
    }

    public function export(Request $request)
    {
        try {
            $query = Setoran::query();

            // Apply same filters as index
            if ($request->has('bank_sampah_id') && $request->bank_sampah_id) {
                $query->where('bank_sampah_id', $request->bank_sampah_id);
            }

            if ($request->has('tipe_setor') && $request->tipe_setor) {
                $query->where('tipe_setor', $request->tipe_setor);
            }

            if ($request->has('status') && $request->status) {
                $query->where('status', $request->status);
            }

            // Filter by date range
            if ($request->has('time_filter') && $request->time_filter) {
                $timeFilter = $request->time_filter;
                $today = now();

                switch ($timeFilter) {
                    case 'harian':
                        $query->whereDate('created_at', $today->toDateString());
                        break;
                    case 'mingguan':
                        $query->whereBetween('created_at', [
                            $today->startOfWeek()->toDateTimeString(),
                            $today->endOfWeek()->toDateTimeString(),
                        ]);
                        break;
                    case 'bulanan':
                        $query->whereBetween('created_at', [
                            $today->startOfMonth()->toDateTimeString(),
                            $today->endOfMonth()->toDateTimeString(),
                        ]);
                        break;
                    case 'range':
                        if ($request->has('start_date') && $request->start_date) {
                            $query->whereDate('created_at', '>=', $request->start_date);
                        }
                        if ($request->has('end_date') && $request->end_date) {
                            $query->whereDate('created_at', '<=', $request->end_date);
                        }
                        break;
                }
            }

            $transactions = $query->orderBy('created_at', 'desc')->get();

            // Generate CSV
            $filename = 'transaksi_'.date('Y-m-d_H-i-s').'.csv';
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            ];

            $callback = function () use ($transactions) {
                $file = fopen('php://output', 'w');

                // CSV headers
                fputcsv($file, [
                    'ID Transaksi',
                    'Tanggal',
                    'Nama User',
                    'Identifier User',
                    'Bank Sampah',
                    'Alamat Bank Sampah',
                    'Alamat User',
                    'Tipe Setor',
                    'Status',
                    'Total Estimasi',
                    'Catatan',
                    'Alasan Pembatalan',
                ]);

                foreach ($transactions as $transaction) {
                    fputcsv($file, [
                        $transaction->id,
                        $transaction->created_at->format('d/m/Y H:i'),
                        $transaction->user_name,
                        $transaction->user_identifier,
                        $transaction->bank_sampah_name,
                        $transaction->bank_sampah_address,
                        $transaction->address_full_address,
                        $transaction->tipe_setor_text,
                        $transaction->status_text,
                        number_format($transaction->estimasi_total),
                        $transaction->notes,
                        $transaction->alasan_pembatalan ?? '-',
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);

        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat export: '.$e->getMessage());
        }
    }

    public function printStruk($id)
    {
        try {
            $transaction = Setoran::findOrFail($id);

            // Only allow printing for completed transactions
            if ($transaction->status !== 'selesai') {
                return back()->with('error', 'Struk hanya dapat dicetak untuk transaksi yang sudah selesai');
            }

            // Get bank sampah info
            $bankSampah = BankSampah::find($transaction->bank_sampah_id);

            // Parse items JSON
            $items = json_decode($transaction->items_json, true) ?: [];

            // Calculate totals
            $totalEstimasiBerat = array_sum(array_column($items, 'estimasi_berat'));
            $totalAktualBerat = array_sum(array_map(function ($item) {
                return isset($item['aktual_berat']) && $item['aktual_berat'] !== null ? $item['aktual_berat'] : 0;
            }, $items));

            $data = [
                'transaction' => $transaction,
                'bankSampah' => $bankSampah,
                'items' => $items,
                'totalEstimasiBerat' => $totalEstimasiBerat,
                'totalAktualBerat' => $totalAktualBerat,
            ];

            // Generate PDF
            $pdf = PDF::loadView('pdf.transaksi-struk', $data);

            // PERBAIKAN: Set paper size yang lebih tepat untuk thermal printer
            // 80mm = 226.77 points, tinggi auto dengan max height
            $pdf->setPaper([0, 0, 226.77, 600], 'portrait'); // Reduced max height

            // Set options untuk rendering yang lebih baik
            $pdf->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'defaultFont' => 'DejaVu Sans',
                'chroot' => public_path(),
                'dpi' => 96, // Reduced DPI
                'defaultPaperSize' => 'custom',
                'isPhpEnabled' => true,
            ]);

            return $pdf->download("struk_transaksi_{$transaction->id}.pdf");

        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat mencetak struk: '.$e->getMessage());
        }
    }

    public function exportExcel(Request $request)
    {
        try {
            $query = Setoran::query();

            // Apply filters (sama seperti exportPdf)
            if ($request->has('bank_sampah_id') && $request->bank_sampah_id) {
                $query->where('bank_sampah_id', $request->bank_sampah_id);
            }
            if ($request->has('tipe_setor') && $request->tipe_setor) {
                $query->where('tipe_setor', $request->tipe_setor);
            }
            if ($request->has('status') && $request->status) {
                $query->where('status', $request->status);
            }
            if ($request->has('time_filter') && $request->time_filter) {
                $timeFilter = $request->time_filter;
                $today = now();
                switch ($timeFilter) {
                    case 'harian':
                        $query->whereDate('created_at', $today->toDateString());
                        break;
                    case 'mingguan':
                        $query->whereBetween('created_at', [
                            $today->startOfWeek()->toDateTimeString(),
                            $today->endOfWeek()->toDateTimeString(),
                        ]);
                        break;
                    case 'bulanan':
                        $query->whereBetween('created_at', [
                            $today->startOfMonth()->toDateTimeString(),
                            $today->endOfMonth()->toDateTimeString(),
                        ]);
                        break;
                    case 'range':
                        if ($request->has('start_date') && $request->start_date) {
                            $query->whereDate('created_at', '>=', $request->start_date);
                        }
                        if ($request->has('end_date') && $request->end_date) {
                            $query->whereDate('created_at', '<=', $request->end_date);
                        }
                        break;
                }
            }

            $transactions = $query->with(['user', 'bankSampah', 'address'])->orderBy('created_at', 'desc')->get();

            // Generate Excel using PhpSpreadsheet
            $spreadsheet = new Spreadsheet;

            // Sheet 1: Detail Transaksi (sesuai dengan tabel di PDF)
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Detail Transaksi');

            // Headers untuk detail transaksi (sesuai dengan PDF)
            $headers = [
                'No', 'ID Transaksi', 'Tanggal', 'Nama User', 'Identifier User', 'Bank Sampah',
                'Alamat Pickup', 'Tipe Setor', 'Status', 'Estimasi Total (Rp)', 'Aktual Total (Rp)',
                'Estimasi Total (KG)', 'Aktual Total (KG)', 'Estimasi Total (UNIT)', 'Aktual Total (UNIT)',
                'Petugas', 'Kontak Petugas', 'Jadwal Penjemputan', 'Tanggal Selesai', 'Items Sampah',
            ];

            $columns = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T'];
            foreach ($headers as $col => $header) {
                $sheet->setCellValue($columns[$col].'1', $header);
            }

            // Add main transaction data
            $row = 2;
            foreach ($transactions as $i => $transaction) {
                $items = json_decode($transaction->items_json, true) ?: [];

                // Hitung total estimasi dan aktual per satuan
                $estimasiTotalKg = collect($items)->where('sampah_satuan', 'KG')->sum('estimasi_berat');
                $aktualTotalKg = collect($items)->where('sampah_satuan', 'KG')->where('aktual_berat', '!=', null)->sum('aktual_berat');
                $estimasiTotalUnit = collect($items)->where('sampah_satuan', 'UNIT')->sum('estimasi_berat');
                $aktualTotalUnit = collect($items)->where('sampah_satuan', 'UNIT')->where('aktual_berat', '!=', null)->sum('aktual_berat');

                // Perbaiki perhitungan estimasi dan aktual total Rp
                $estimasiTotalRp = array_sum(array_map(function ($item) {
                    return (isset($item['estimasi_berat']) ? $item['estimasi_berat'] : 0) * (isset($item['harga_per_satuan']) ? $item['harga_per_satuan'] : 0);
                }, $items));
                $aktualTotalRp = array_sum(array_map(function ($item) {
                    return (isset($item['aktual_berat']) && $item['aktual_berat'] !== null ? $item['aktual_berat'] : 0) * (isset($item['harga_per_satuan']) ? $item['harga_per_satuan'] : 0);
                }, $items));

                // Format items sampah untuk kolom
                $itemsText = '';
                foreach ($items as $item) {
                    $itemName = $item['sampah_nama'] ?? $item['nama_sampah'] ?? 'N/A';
                    $itemStatus = $item['status'] ?? 'normal';
                    $statusText = '';

                    if ($itemStatus === 'dihapus') {
                        $statusText = ' (DIHAPUS)';
                    } elseif ($itemStatus === 'ditambah') {
                        $statusText = ' (DITAMBAH)';
                    } elseif ($itemStatus === 'dimodifikasi') {
                        $statusText = ' (DIMODIFIKASI)';
                    }

                    $itemsText .= $itemName.$statusText.' - Est: '.($item['estimasi_berat'] ?? 0).' '.($item['sampah_satuan'] ?? 'KG');
                    if (isset($item['aktual_berat']) && $item['aktual_berat']) {
                        $itemsText .= ' | Aktual: '.$item['aktual_berat'].' '.($item['sampah_satuan'] ?? 'KG');
                    }
                    $itemsText .= "\n";
                }

                $sheet->setCellValue('A'.$row, $i + 1);
                $sheet->setCellValue('B'.$row, $transaction->id);
                $sheet->setCellValue('C'.$row, $transaction->created_at->format('d/m/Y'));
                $sheet->setCellValue('D'.$row, $transaction->user_name);
                $sheet->setCellValue('E'.$row, $transaction->user_identifier);
                $sheet->setCellValue('F'.$row, $transaction->bank_sampah_name);
                $sheet->setCellValue('G'.$row, $transaction->address_full_address ?? '-');
                $sheet->setCellValue('H'.$row, ucfirst($transaction->tipe_setor));
                $sheet->setCellValue('I'.$row, ucfirst($transaction->status));
                $sheet->setCellValue('J'.$row, number_format($estimasiTotalRp, 0));
                $sheet->setCellValue('K'.$row, number_format($aktualTotalRp, 0));
                $sheet->setCellValue('L'.$row, number_format($estimasiTotalKg, 2));
                $sheet->setCellValue('M'.$row, number_format($aktualTotalKg, 2));
                $sheet->setCellValue('N'.$row, number_format($estimasiTotalUnit, 2));
                $sheet->setCellValue('O'.$row, number_format($aktualTotalUnit, 2));
                $sheet->setCellValue('P'.$row, $transaction->petugas_nama ?? '-');
                $sheet->setCellValue('Q'.$row, $transaction->petugas_contact ?? '-');
                $sheet->setCellValue('R'.$row, $transaction->getJadwalAttribute() ?? '-');
                $sheet->setCellValue('S'.$row, $transaction->tanggal_selesai ? $transaction->tanggal_selesai->format('d/m/Y H:i') : '-');
                $sheet->setCellValue('T'.$row, trim($itemsText));
                $row++;
            }

            // Sheet 2: Bank Performa (sesuai dengan PDF)
            $sheet2 = $spreadsheet->createSheet();
            $sheet2->setTitle('Bank Performa');

            // Headers untuk bank performa
            $bankHeaders = [
                'Bank', 'Total Transaksi', 'Selesai', 'Completion Rate (%)', 'Estimasi Total (Rp)',
                'Realisasi Total (Rp)', 'Estimasi Total (KG)', 'Realisasi Total (KG)',
                'Estimasi Total (UNIT)', 'Realisasi Total (UNIT)',
            ];

            $bankColumns = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J'];
            foreach ($bankHeaders as $col => $header) {
                $sheet2->setCellValue($bankColumns[$col].'1', $header);
            }

            // Hitung bank performance
            $bankPerformance = $transactions->groupBy('bank_sampah_name')
                ->map(function ($group) {
                    $completed = $group->where('status', 'selesai')->count();
                    $total = $group->count();
                    $rate = $total > 0 ? ($completed / $total) * 100 : 0;

                    return [
                        'total' => $total,
                        'completed' => $completed,
                        'rate' => $rate,
                        'total_value' => $group->sum('estimasi_total'),
                    ];
                });

            // Add bank performance data
            $bankRow = 2;
            foreach ($bankPerformance as $bankName => $performance) {
                // Hitung total estimasi dan realisasi per satuan untuk bank ini
                $bankTransactions = $transactions->where('bank_sampah_name', $bankName);
                $allItems = collect();
                foreach ($bankTransactions as $transaction) {
                    $items = json_decode($transaction->items_json, true) ?? [];
                    foreach ($items as $item) {
                        $allItems->push($item);
                    }
                }

                $estimasiTotalKg = $allItems->where('sampah_satuan', 'KG')->sum('estimasi_berat');
                $aktualTotalKg = $allItems->where('sampah_satuan', 'KG')->where('aktual_berat', '!=', null)->sum('aktual_berat');
                $estimasiTotalUnit = $allItems->where('sampah_satuan', 'UNIT')->sum('estimasi_berat');
                $aktualTotalUnit = $allItems->where('sampah_satuan', 'UNIT')->where('aktual_berat', '!=', null)->sum('aktual_berat');

                $sheet2->setCellValue('A'.$bankRow, $bankName);
                $sheet2->setCellValue('B'.$bankRow, $performance['total']);
                $sheet2->setCellValue('C'.$bankRow, $performance['completed']);
                $sheet2->setCellValue('D'.$bankRow, number_format($performance['rate'], 1));
                $sheet2->setCellValue('E'.$bankRow, number_format($performance['total_value'], 0));
                $sheet2->setCellValue('F'.$bankRow, number_format($bankTransactions->where('aktual_total', '!=', null)->sum('aktual_total'), 0));
                $sheet2->setCellValue('G'.$bankRow, number_format($estimasiTotalKg, 2));
                $sheet2->setCellValue('H'.$bankRow, number_format($aktualTotalKg, 2));
                $sheet2->setCellValue('I'.$bankRow, number_format($estimasiTotalUnit, 2));
                $sheet2->setCellValue('J'.$bankRow, number_format($aktualTotalUnit, 2));
                $bankRow++;
            }

            // Sheet 3: Items by Volume (sesuai dengan PDF)
            $sheet3 = $spreadsheet->createSheet();
            $sheet3->setTitle('Items by Volume');

            // Headers untuk items by volume
            $itemHeaders = [
                'Item', 'Jumlah Transaksi', 'Estimasi Total (KG)', 'Realisasi Total (KG)',
                'Estimasi Total (UNIT)', 'Realisasi Total (UNIT)', 'Rata-rata Harga (Rp)',
            ];

            $itemColumns = ['A', 'B', 'C', 'D', 'E', 'F', 'G'];
            foreach ($itemHeaders as $col => $header) {
                $sheet3->setCellValue($itemColumns[$col].'1', $header);
            }

            // Hitung item statistics
            $allItems = collect();
            foreach ($transactions as $transaction) {
                $items = json_decode($transaction->items_json, true) ?? [];
                foreach ($items as $item) {
                    $allItems->push($item);
                }
            }

            $itemStats = $allItems->groupBy('sampah_nama')
                ->map(function ($group) {
                    return [
                        'count' => $group->count(),
                        'avg_price' => $group->avg('harga_per_satuan'),
                    ];
                });

            // Add items data
            $itemRow = 2;
            foreach ($itemStats as $itemName => $stats) {
                $estimasiKg = $allItems->where('sampah_nama', $itemName)->where('sampah_satuan', 'KG')->sum('estimasi_berat');
                $aktualKg = $allItems->where('sampah_nama', $itemName)->where('sampah_satuan', 'KG')->where('aktual_berat', '!=', null)->sum('aktual_berat');
                $estimasiUnit = $allItems->where('sampah_nama', $itemName)->where('sampah_satuan', 'UNIT')->sum('estimasi_berat');
                $aktualUnit = $allItems->where('sampah_nama', $itemName)->where('sampah_satuan', 'UNIT')->where('aktual_berat', '!=', null)->sum('aktual_berat');

                $sheet3->setCellValue('A'.$itemRow, $itemName);
                $sheet3->setCellValue('B'.$itemRow, $stats['count']);
                $sheet3->setCellValue('C'.$itemRow, number_format($estimasiKg, 2));
                $sheet3->setCellValue('D'.$itemRow, number_format($aktualKg, 2));
                $sheet3->setCellValue('E'.$itemRow, number_format($estimasiUnit, 2));
                $sheet3->setCellValue('F'.$itemRow, number_format($aktualUnit, 2));
                $sheet3->setCellValue('G'.$itemRow, number_format($stats['avg_price'], 0));
                $itemRow++;
            }

            // Sheet 4: Summary Statistics
            $sheet4 = $spreadsheet->createSheet();
            $sheet4->setTitle('Summary Statistics');

            // Hitung summary statistics
            $totalEstimasi = $transactions->sum('estimasi_total');
            $totalAktual = $transactions->where('aktual_total', '!=', null)->sum('aktual_total');
            $transaksiSelesai = $transactions->where('status', 'selesai')->count();
            $transaksiProses = $transactions->whereIn('status', ['diproses', 'dijemput', 'dikonfirmasi'])->count();
            $transaksiBatal = $transactions->where('status', 'batal')->count();

            $completionRate = $transactions->count() > 0 ? ($transaksiSelesai / $transactions->count()) * 100 : 0;
            $cancellationRate = $transactions->count() > 0 ? ($transaksiBatal / $transactions->count()) * 100 : 0;

            // Hitung total estimasi dan realisasi per satuan
            $allItemsForSummary = collect();
            foreach ($transactions as $transaction) {
                $items = json_decode($transaction->items_json, true) ?? [];
                foreach ($items as $item) {
                    $allItemsForSummary->push($item);
                }
            }

            $totalEstimasiKg = $allItemsForSummary->where('sampah_satuan', 'KG')->sum('estimasi_berat');
            $totalAktualKg = $allItemsForSummary->where('sampah_satuan', 'KG')->where('aktual_berat', '!=', null)->sum('aktual_berat');
            $totalEstimasiUnit = $allItemsForSummary->where('sampah_satuan', 'UNIT')->sum('estimasi_berat');
            $totalAktualUnit = $allItemsForSummary->where('sampah_satuan', 'UNIT')->where('aktual_berat', '!=', null)->sum('aktual_berat');

            // Add summary data
            $summaryData = [
                ['Metric', 'Value'],
                ['Total Transaksi', $transactions->count()],
                ['Estimasi Total (Rp)', number_format($totalEstimasi, 0)],
                ['Realisasi Total (Rp)', number_format($totalAktual, 0)],
                ['Estimasi Sampah (KG)', number_format($totalEstimasiKg, 2)],
                ['Realisasi Sampah (KG)', number_format($totalAktualKg, 2)],
                ['Estimasi Sampah (UNIT)', number_format($totalEstimasiUnit, 2)],
                ['Realisasi Sampah (UNIT)', number_format($totalAktualUnit, 2)],
                ['Completion Rate (%)', number_format($completionRate, 1)],
                ['Cancellation Rate (%)', number_format($cancellationRate, 1)],
                ['Transaksi Selesai', $transaksiSelesai],
                ['Transaksi Dalam Proses', $transaksiProses],
                ['Transaksi Dibatalkan', $transaksiBatal],
            ];

            foreach ($summaryData as $rowIndex => $rowData) {
                foreach ($rowData as $colIndex => $value) {
                    $col = $colIndex === 0 ? 'A' : 'B';
                    $sheet4->setCellValue($col.($rowIndex + 1), $value);
                }
            }

            // Auto-size columns for all sheets
            foreach (range(1, 20) as $col) {
                $sheet->getColumnDimensionByColumn($col)->setAutoSize(true);
            }
            foreach (range(1, 10) as $col) {
                $sheet2->getColumnDimensionByColumn($col)->setAutoSize(true);
            }
            foreach (range(1, 7) as $col) {
                $sheet3->getColumnDimensionByColumn($col)->setAutoSize(true);
            }
            foreach (range(1, 2) as $col) {
                $sheet4->getColumnDimensionByColumn($col)->setAutoSize(true);
            }

            // Set first sheet as active
            $spreadsheet->setActiveSheetIndex(0);

            // Create Excel file
            $writer = new Xlsx($spreadsheet);
            $filename = 'laporan_transaksi_lengkap_'.date('Y-m-d_H-i-s').'.xlsx';

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="'.$filename.'"');
            header('Cache-Control: max-age=0');

            $writer->save('php://output');
            exit;

        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan saat export Excel: '.$e->getMessage()], 500);
        }
    }

    public function exportCsv(Request $request)
    {
        try {
            $query = Setoran::query();

            // Apply filters (sama seperti exportPdf)
            if ($request->has('bank_sampah_id') && $request->bank_sampah_id) {
                $query->where('bank_sampah_id', $request->bank_sampah_id);
            }
            if ($request->has('tipe_setor') && $request->tipe_setor) {
                $query->where('tipe_setor', $request->tipe_setor);
            }
            if ($request->has('status') && $request->status) {
                $query->where('status', $request->status);
            }
            if ($request->has('time_filter') && $request->time_filter) {
                $timeFilter = $request->time_filter;
                $today = now();
                switch ($timeFilter) {
                    case 'harian':
                        $query->whereDate('created_at', $today->toDateString());
                        break;
                    case 'mingguan':
                        $query->whereBetween('created_at', [
                            $today->startOfWeek()->toDateTimeString(),
                            $today->endOfWeek()->toDateTimeString(),
                        ]);
                        break;
                    case 'bulanan':
                        $query->whereBetween('created_at', [
                            $today->startOfMonth()->toDateTimeString(),
                            $today->endOfMonth()->toDateTimeString(),
                        ]);
                        break;
                    case 'range':
                        if ($request->has('start_date') && $request->start_date) {
                            $query->whereDate('created_at', '>=', $request->start_date);
                        }
                        if ($request->has('end_date') && $request->end_date) {
                            $query->whereDate('created_at', '<=', $request->end_date);
                        }
                        break;
                }
            }

            $transactions = $query->with(['user', 'bankSampah', 'address'])->orderBy('created_at', 'desc')->get();

            // Generate CSV
            $filename = 'laporan_transaksi_lengkap_'.date('Y-m-d_H-i-s').'.csv';
            $headers = [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            ];

            $callback = function () use ($transactions) {
                $file = fopen('php://output', 'w');

                // Add BOM for UTF-8
                fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

                // Headers untuk detail transaksi (sama seperti Excel)
                fputcsv($file, [
                    'No', 'ID Transaksi', 'Tanggal', 'Nama User', 'Identifier User', 'Bank Sampah',
                    'Alamat Pickup', 'Tipe Setor', 'Status', 'Estimasi Total (Rp)', 'Aktual Total (Rp)',
                    'Estimasi Total (KG)', 'Aktual Total (KG)', 'Estimasi Total (UNIT)', 'Aktual Total (UNIT)',
                    'Petugas', 'Kontak Petugas', 'Jadwal Penjemputan', 'Tanggal Selesai', 'Items Sampah',
                ]);

                foreach ($transactions as $i => $transaction) {
                    $items = json_decode($transaction->items_json, true) ?: [];

                    // Hitung total estimasi dan aktual per satuan
                    $estimasiTotalKg = collect($items)->where('sampah_satuan', 'KG')->sum('estimasi_berat');
                    $aktualTotalKg = collect($items)->where('sampah_satuan', 'KG')->where('aktual_berat', '!=', null)->sum('aktual_berat');
                    $estimasiTotalUnit = collect($items)->where('sampah_satuan', 'UNIT')->sum('estimasi_berat');
                    $aktualTotalUnit = collect($items)->where('sampah_satuan', 'UNIT')->where('aktual_berat', '!=', null)->sum('aktual_berat');

                    // Perbaiki perhitungan estimasi dan aktual total Rp (sama seperti Excel)
                    $estimasiTotalRp = array_sum(array_map(function ($item) {
                        return (isset($item['estimasi_berat']) ? $item['estimasi_berat'] : 0) * (isset($item['harga_per_satuan']) ? $item['harga_per_satuan'] : 0);
                    }, $items));

                    $aktualTotalRp = array_sum(array_map(function ($item) {
                        return (isset($item['aktual_berat']) && $item['aktual_berat'] !== null ? $item['aktual_berat'] : 0) * (isset($item['harga_per_satuan']) ? $item['harga_per_satuan'] : 0);
                    }, $items));

                    // Format items sampah untuk kolom
                    $itemsText = '';
                    foreach ($items as $item) {
                        $itemName = $item['sampah_nama'] ?? $item['nama_sampah'] ?? 'N/A';
                        $itemStatus = $item['status'] ?? 'normal';
                        $statusText = '';

                        if ($itemStatus === 'dihapus') {
                            $statusText = ' (DIHAPUS)';
                        } elseif ($itemStatus === 'ditambah') {
                            $statusText = ' (DITAMBAH)';
                        } elseif ($itemStatus === 'dimodifikasi') {
                            $statusText = ' (DIMODIFIKASI)';
                        }

                        $itemsText .= $itemName.$statusText.' - Est: '.($item['estimasi_berat'] ?? 0).' '.($item['sampah_satuan'] ?? 'KG');
                        if (isset($item['aktual_berat']) && $item['aktual_berat']) {
                            $itemsText .= ' | Aktual: '.$item['aktual_berat'].' '.($item['sampah_satuan'] ?? 'KG');
                        }
                        $itemsText .= "\n";
                    }

                    fputcsv($file, [
                        $i + 1,
                        $transaction->id,
                        $transaction->created_at->format('d/m/Y'),
                        $transaction->user_name,
                        $transaction->user_identifier,
                        $transaction->bank_sampah_name,
                        $transaction->address_full_address ?? '-',
                        ucfirst($transaction->tipe_setor),
                        ucfirst($transaction->status),
                        number_format($estimasiTotalRp, 0),
                        number_format($aktualTotalRp, 0),
                        number_format($estimasiTotalKg, 2),
                        number_format($aktualTotalKg, 2),
                        number_format($estimasiTotalUnit, 2),
                        number_format($aktualTotalUnit, 2),
                        $transaction->petugas_nama ?? '-',
                        $transaction->petugas_contact ?? '-',
                        $transaction->getJadwalAttribute() ?? '-',
                        $transaction->tanggal_selesai ? $transaction->tanggal_selesai->format('d/m/Y H:i') : '-',
                        trim($itemsText),
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan saat export CSV: '.$e->getMessage()], 500);
        }
    }

    public function exportPdf(Request $request)
    {
        try {
            $query = Setoran::query();

            // Apply filters (sama seperti exportPdf)
            if ($request->has('bank_sampah_id') && $request->bank_sampah_id) {
                $query->where('bank_sampah_id', $request->bank_sampah_id);
            }
            if ($request->has('tipe_setor') && $request->tipe_setor) {
                $query->where('tipe_setor', $request->tipe_setor);
            }
            if ($request->has('status') && $request->status) {
                $query->where('status', $request->status);
            }
            if ($request->has('time_filter') && $request->time_filter) {
                $timeFilter = $request->time_filter;
                $today = now();
                switch ($timeFilter) {
                    case 'harian':
                        $query->whereDate('created_at', $today->toDateString());
                        break;
                    case 'mingguan':
                        $query->whereBetween('created_at', [
                            $today->startOfWeek()->toDateTimeString(),
                            $today->endOfWeek()->toDateTimeString(),
                        ]);
                        break;
                    case 'bulanan':
                        $query->whereBetween('created_at', [
                            $today->startOfMonth()->toDateTimeString(),
                            $today->endOfMonth()->toDateTimeString(),
                        ]);
                        break;
                    case 'range':
                        if ($request->has('start_date') && $request->start_date) {
                            $query->whereDate('created_at', '>=', $request->start_date);
                        }
                        if ($request->has('end_date') && $request->end_date) {
                            $query->whereDate('created_at', '<=', $request->end_date);
                        }
                        break;
                }
            }

            $transactions = $query->with(['user', 'bankSampah', 'address'])->orderBy('created_at', 'desc')->get();

            // Calculate comprehensive statistics
            $stats = $this->calculateTransactionStats($transactions);

            $pdf = PDF::loadView('pdf.transaksi-report', [
                'transactions' => $transactions,
                'filters' => $request->all(),
                'stats' => $stats,
            ]);
            $pdf->setPaper('A4', 'landscape');

            return $pdf->download('laporan_transaksi_lengkap_'.date('Y-m-d_H-i-s').'.pdf');

        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan saat export PDF: '.$e->getMessage()], 500);
        }
    }

    private function calculateTransactionStats($transactions)
    {
        $stats = [
            'total_transactions' => $transactions->count(),
            'total_estimasi_kg' => 0,
            'total_aktual_kg' => 0,
            'total_estimasi_rp' => 0,
            'total_aktual_rp' => 0,
            'status_counts' => [],
            'tipe_counts' => [],
            'bank_counts' => [],
            'items_added' => 0,
            'items_removed' => 0,
            'items_modified' => 0,
            'total_items' => 0,
            'avg_completion_time' => 0,
            'completion_times' => [],
        ];

        foreach ($transactions as $transaction) {
            $items = json_decode($transaction->items_json, true) ?: [];

            // Calculate totals
            $estimasiTotalKg = array_sum(array_column($items, 'estimasi_berat'));
            $aktualTotalKg = array_sum(array_map(function ($item) {
                return isset($item['aktual_berat']) && $item['aktual_berat'] !== null ? $item['aktual_berat'] : 0;
            }, $items));
            $estimasiTotalRp = array_sum(array_column($items, 'estimasi_harga'));
            $aktualTotalRp = array_sum(array_map(function ($item) {
                return isset($item['aktual_harga']) && $item['aktual_harga'] !== null ? $item['aktual_harga'] : 0;
            }, $items));

            $stats['total_estimasi_kg'] += $estimasiTotalKg;
            $stats['total_aktual_kg'] += $aktualTotalKg;
            $stats['total_estimasi_rp'] += $estimasiTotalRp;
            $stats['total_aktual_rp'] += $aktualTotalRp;

            // Count status
            $status = $transaction->status;
            $stats['status_counts'][$status] = ($stats['status_counts'][$status] ?? 0) + 1;

            // Count tipe setor
            $tipe = $transaction->tipe_setor;
            $stats['tipe_counts'][$tipe] = ($stats['tipe_counts'][$tipe] ?? 0) + 1;

            // Count bank sampah
            $bank = $transaction->bank_sampah_name;
            $stats['bank_counts'][$bank] = ($stats['bank_counts'][$bank] ?? 0) + 1;

            // Count items
            $stats['total_items'] += count($items);
            foreach ($items as $item) {
                if (isset($item['status'])) {
                    switch ($item['status']) {
                        case 'ditambah':
                            $stats['items_added']++;
                            break;
                        case 'dihapus':
                            $stats['items_removed']++;
                            break;
                        case 'dimodifikasi':
                            $stats['items_modified']++;
                            break;
                    }
                }
            }

            // Calculate completion time
            if ($transaction->status === 'selesai' && $transaction->tanggal_selesai) {
                $completionTime = $transaction->created_at->diffInHours($transaction->tanggal_selesai);
                $stats['completion_times'][] = $completionTime;
            }
        }

        // Calculate average completion time
        if (! empty($stats['completion_times'])) {
            $stats['avg_completion_time'] = array_sum($stats['completion_times']) / count($stats['completion_times']);
        }

        return $stats;
    }
}
