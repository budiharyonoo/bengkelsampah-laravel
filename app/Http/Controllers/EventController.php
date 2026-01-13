<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Event::withCount('participants');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%'.$search.'%')
                    ->orWhere('description', 'like', '%'.$search.'%')
                    ->orWhere('location', 'like', '%'.$search.'%');
            });
        }

        // Filter by status
        if ($request->filled('status') && $request->status !== 'Semua Status') {
            $status = strtolower($request->status);
            if (in_array($status, ['active', 'completed', 'cancelled'])) {
                $query->where('status', $status);
            }
        }

        $events = $query->orderByDesc('created_at')->paginate(10);

        // Transform data untuk response
        $events->getCollection()->transform(function ($event) {
            $event->cover_url = $event->cover ? $event->cover : null;
            $event->start_datetime = $event->start_datetime->format('d M Y H:i');
            $event->end_datetime = $event->end_datetime->format('d M Y H:i');
            $event->participants_count = $event->participants->count();
            $event->has_result = $event->hasResult();
            $event->is_expired = $event->isExpired();

            return $event;
        });

        // Return JSON for AJAX requests
        if ($request->ajax()) {
            return response()->json([
                'events' => $events,
            ]);
        }

        return view('dashboard-event', compact('events'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('dashboard-event-create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'cover' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
                'start_datetime' => 'required|date|after:now',
                'end_datetime' => 'required|date|after:start_datetime',
                'location' => 'required|string|max:255',
                'url' => 'nullable|url|max:500',
                'max_participants' => 'nullable|integer|min:1',
                'admin_name' => 'required|string|max:255',
                'status' => 'required|in:active,completed,cancelled',
            ]);

            $data = $request->all();

            // Handle cover upload
            if ($request->hasFile('cover')) {
                $file = $request->file('cover');
                $filename = time().'_'.Str::random(10).'_'.Str::slug($request->title).'.'.$file->getClientOriginalExtension();

                // Create directory if it doesn't exist
                $uploadPath = base_path('../uploads/event_cover');
                if (! file_exists($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }

                // Store the file
                $path = $file->storeAs('event_cover', $filename, 'public');

                if (! $path) {
                    throw new \Exception('Failed to upload file');
                }

                // Get the full URL for the image
                $data['cover'] = env('APP_URL').'/uploads/'.$path;
            }

            $event = \App\Models\Event::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Event berhasil dibuat',
                'data' => $event,
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in EventController@store: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat event: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $event = \App\Models\Event::with(['participants'])->findOrFail($id);

        return view('dashboard-event-show', compact('event'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $event = \App\Models\Event::findOrFail($id);

        return view('dashboard-event-edit', compact('event'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $event = \App\Models\Event::findOrFail($id);

            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'cover' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
                'start_datetime' => 'required|date',
                'end_datetime' => 'required|date|after:start_datetime',
                'location' => 'required|string|max:255',
                'url' => 'nullable|url|max:500',
                'max_participants' => 'nullable|integer|min:1',
                'status' => 'required|in:active,completed,cancelled',
            ]);

            $data = $request->except(['cover']);

            // Handle cover upload
            if ($request->hasFile('cover')) {
                // Delete old cover if exists
                if ($event->cover) {
                    $oldPath = str_replace(env('APP_URL').'/uploads/', '', $event->cover);
                    $fullOldPath = base_path('../uploads/'.$oldPath);
                    if (file_exists($fullOldPath)) {
                        unlink($fullOldPath);
                    }
                }

                $file = $request->file('cover');
                $filename = time().'_'.Str::random(10).'_'.Str::slug($request->title).'.'.$file->getClientOriginalExtension();

                // Create directory if it doesn't exist
                $uploadPath = base_path('../uploads/event_cover');
                if (! file_exists($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }

                // Store the file
                $path = $file->storeAs('event_cover', $filename, 'public');

                if (! $path) {
                    throw new \Exception('Failed to upload file');
                }

                // Get the full URL for the image
                $data['cover'] = env('APP_URL').'/uploads/'.$path;
            }

            $event->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Event berhasil diupdate',
                'data' => $event,
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in EventController@update: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate event: '.$e->getMessage(),
            ], 500);
        }
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
                $events = Event::whereIn('id', $ids)->get();

                foreach ($events as $event) {
                    // Delete cover file if exists
                    if ($event->cover) {
                        $oldPath = str_replace(env('APP_URL').'/uploads/', '', $event->cover);
                        $fullOldPath = base_path('../uploads/'.$oldPath);
                        if (file_exists($fullOldPath)) {
                            unlink($fullOldPath);
                        }
                    }
                }

                Event::whereIn('id', $ids)->delete();

                return response()->json([
                    'success' => true,
                    'message' => count($ids).' event berhasil dihapus',
                ]);
            }

            // Handle single delete
            $event = Event::findOrFail($id);

            // Delete cover file if exists
            if ($event->cover) {
                $oldPath = str_replace(env('APP_URL').'/uploads/', '', $event->cover);
                $fullOldPath = base_path('../uploads/'.$oldPath);
                if (file_exists($fullOldPath)) {
                    unlink($fullOldPath);
                }
            }

            $event->delete();

            return response()->json([
                'success' => true,
                'message' => 'Event berhasil dihapus',
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in EventController@destroy: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus event: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Submit event result.
     */
    public function submitResult(Request $request, $id)
    {
        try {
            $event = Event::findOrFail($id);

            // Check if event can have result submitted
            if (! $event->canSubmitResult()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Hasil event hanya bisa diinput untuk event yang sudah selesai (completed)',
                ], 400);
            }

            $request->validate([
                'result_description' => 'required|string',
                'saved_waste_amount' => 'required|numeric|min:0',
                'actual_participants' => 'required|integer|min:0',
                'result_photos.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
                'result_photos' => 'nullable|array|max:5', // Maksimal 5 foto
            ]);

            $data = [
                'result_description' => $request->result_description,
                'saved_waste_amount' => $request->saved_waste_amount,
                'actual_participants' => $request->actual_participants,
                'result_submitted_at' => now(),
                'result_submitted_by_name' => Auth::guard('admin')->user()->name,
            ];

            // Handle result photos upload
            if ($request->hasFile('result_photos')) {
                $photoUrls = [];
                foreach ($request->file('result_photos') as $photo) {
                    $filename = time().'_'.Str::random(10).'_result_'.Str::slug($event->title).'.'.$photo->getClientOriginalExtension();

                    // Create directory if it doesn't exist
                    $uploadPath = base_path('../uploads/event_results');
                    if (! file_exists($uploadPath)) {
                        mkdir($uploadPath, 0755, true);
                    }

                    // Store the file
                    $path = $photo->storeAs('event_results', $filename, 'public');

                    if ($path) {
                        $photoUrls[] = env('APP_URL').'/uploads/'.$path;
                    }
                }
                $data['result_photos'] = $photoUrls;
            }

            $event->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Hasil event berhasil disimpan',
                'data' => $event,
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in EventController@submitResult: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan hasil event: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update event result.
     */
    public function updateResult(Request $request, $id)
    {
        try {
            $event = Event::findOrFail($id);

            // Check if event has result
            if (! $event->hasResult()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Event belum memiliki hasil yang bisa diupdate',
                ], 400);
            }

            $request->validate([
                'result_description' => 'required|string',
                'saved_waste_amount' => 'required|numeric|min:0',
                'actual_participants' => 'required|integer|min:0',
                'result_photos.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
                'result_photos' => 'nullable|array|max:5', // Maksimal 5 foto
                'existing_photos' => 'nullable|array',
                'deleted_photos' => 'nullable|array',
            ]);

            $data = [
                'result_description' => $request->result_description,
                'saved_waste_amount' => $request->saved_waste_amount,
                'actual_participants' => $request->actual_participants,
                'result_submitted_by_name' => Auth::guard('admin')->user()->name,
            ];

            // Handle existing photos (keep the ones not deleted)
            $existingPhotos = $request->input('existing_photos', []);
            $deletedPhotoIndexes = $request->input('deleted_photos', []);

            // Filter out deleted photos
            $keptPhotos = [];
            if ($event->result_photos) {
                foreach ($event->result_photos as $index => $photo) {
                    if (! in_array($index, $deletedPhotoIndexes)) {
                        $keptPhotos[] = $photo;
                    } else {
                        // Delete the file from storage
                        $oldPath = str_replace(env('APP_URL').'/uploads/', '', $photo);
                        $fullOldPath = base_path('../uploads/'.$oldPath);
                        if (file_exists($fullOldPath)) {
                            unlink($fullOldPath);
                        }
                    }
                }
            }

            // Handle new photos upload
            $newPhotoUrls = [];
            if ($request->hasFile('result_photos')) {
                foreach ($request->file('result_photos') as $photo) {
                    $filename = time().'_'.Str::random(10).'_result_'.Str::slug($event->title).'.'.$photo->getClientOriginalExtension();

                    // Create directory if it doesn't exist
                    $uploadPath = base_path('../uploads/event_results');
                    if (! file_exists($uploadPath)) {
                        mkdir($uploadPath, 0755, true);
                    }

                    // Store the file
                    $path = $photo->storeAs('event_results', $filename, 'public');

                    if ($path) {
                        $newPhotoUrls[] = env('APP_URL').'/uploads/'.$path;
                    }
                }
            }

            // Combine kept photos and new photos
            $allPhotos = array_merge($keptPhotos, $newPhotoUrls);
            $data['result_photos'] = $allPhotos;

            $event->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Hasil event berhasil diupdate',
                'data' => $event,
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in EventController@updateResult: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate hasil event: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Convert image URL/path to base64 for PDF
     */
    private function convertImageToBase64($imageUrl)
    {
        try {
            // Handle both URL and storage path formats
            if (strpos($imageUrl, 'http') === 0) {
                // If it's a full URL, extract the path
                $relativePath = str_replace(env('APP_URL').'/uploads/', '', $imageUrl);
            } else {
                // If it's already a relative path
                $relativePath = str_replace('uploads/', '', $imageUrl);
            }

            // Try different possible locations
            $possiblePaths = [
                base_path('../uploads/'.$relativePath),
                public_path('uploads/'.$relativePath),
                storage_path('app/public/'.$relativePath),
                base_path('uploads/'.$relativePath),
            ];

            foreach ($possiblePaths as $localPath) {
                if (file_exists($localPath)) {
                    $imageData = file_get_contents($localPath);
                    $imageType = pathinfo($localPath, PATHINFO_EXTENSION);

                    // Ensure proper MIME type
                    $mimeType = match (strtolower($imageType)) {
                        'jpg', 'jpeg' => 'image/jpeg',
                        'png' => 'image/png',
                        'gif' => 'image/gif',
                        'webp' => 'image/webp',
                        default => 'image/jpeg'
                    };

                    return 'data:'.$mimeType.';base64,'.base64_encode($imageData);
                }
            }

            \Log::warning('Image not found in any location: '.$imageUrl);
            \Log::warning('Tried paths: '.implode(', ', $possiblePaths));

            return null;

        } catch (\Exception $e) {
            \Log::error('Error converting image to base64: '.$e->getMessage());

            return null;
        }
    }

    /**
     * Generate PDF report for event.
     */
    public function generateReport($id)
    {
        try {
            $event = Event::with([
                'participants',
            ])->findOrFail($id);

            // Check if event has result
            if (! $event->hasResult()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Laporan PDF hanya bisa di-generate setelah hasil event diinput',
                ], 400);
            }

            // Convert cover image to base64
            if ($event->cover) {
                $event->cover_local = $this->convertImageToBase64($event->cover);
                if (! $event->cover_local) {
                    \Log::warning('Cover image could not be converted: '.$event->cover);
                }
            }

            // Convert result photos to base64
            if ($event->result_photos && is_array($event->result_photos)) {
                $localPhotos = [];
                foreach ($event->result_photos as $photoUrl) {
                    $base64Image = $this->convertImageToBase64($photoUrl);
                    if ($base64Image) {
                        $localPhotos[] = $base64Image;
                    } else {
                        \Log::warning('Result photo could not be converted: '.$photoUrl);
                    }
                }
                $event->result_photos_local = $localPhotos;
            }

            // Set DomPDF options
            $options = new \Dompdf\Options;
            $options->set('isRemoteEnabled', true);
            $options->set('isHtml5ParserEnabled', true);
            $options->set('defaultFont', 'Arial');
            $options->set('chroot', base_path());
            $options->set('debugKeepTemp', true);

            // Generate PDF using DomPDF
            $dompdf = new \Dompdf\Dompdf($options);

            $period = $event->start_datetime->translatedFormat('d F Y H:i').' - '.$event->end_datetime->translatedFormat('d F Y H:i');

            // Get HTML content
            $html = view('pdf.event-detail-report', compact('event', 'period'))->render();

            // Log the HTML to debug
            \Log::debug('PDF HTML content length: '.strlen($html));
            if ($event->cover_local) {
                \Log::debug('Cover image base64 length: '.strlen($event->cover_local));
            }
            if (! empty($event->result_photos_local)) {
                \Log::debug('Result photos count: '.count($event->result_photos_local));
            }

            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();

            $filename = 'event_report_'.$event->id.'_'.now()->format('Y-m-d_H-i-s').'.pdf';

            return $dompdf->stream($filename);

        } catch (\Exception $e) {
            \Log::error('Error in EventController@generateReport: '.$e->getMessage());
            \Log::error('Stack trace: '.$e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Gagal generate laporan PDF: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Complete event (change status to completed).
     */
    public function completeEvent($id)
    {
        try {
            $event = Event::findOrFail($id);
            $event->update(['status' => 'completed']);

            return response()->json([
                'success' => true,
                'message' => 'Event berhasil diselesaikan',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyelesaikan event: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Export events to Excel
     */
    public function exportExcel(Request $request)
    {
        try {
            $request->validate([
                'period' => 'required|string',
                'status' => 'nullable|string',
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
                'time_type' => 'nullable|string|in:created,start,end',
            ]);

            $query = Event::withCount('participants');

            // Apply period filter based on time type
            $timeType = $request->time_type ?? 'created';
            $dateField = $timeType === 'created' ? 'created_at' : ($timeType === 'start' ? 'start_datetime' : 'end_datetime');

            switch ($request->period) {
                case 'today':
                    $query->whereDate($dateField, today());
                    break;
                case 'yesterday':
                    $query->whereDate($dateField, today()->subDay());
                    break;
                case 'this_week':
                    $query->whereBetween($dateField, [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'last_week':
                    $query->whereBetween($dateField, [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()]);
                    break;
                case 'this_month':
                    $query->whereMonth($dateField, now()->month)->whereYear($dateField, now()->year);
                    break;
                case 'last_month':
                    $query->whereMonth($dateField, now()->subMonth()->month)->whereYear($dateField, now()->subMonth()->year);
                    break;
                case 'this_year':
                    $query->whereYear($dateField, now()->year);
                    break;
                case 'last_year':
                    $query->whereYear($dateField, now()->subYear()->year);
                    break;
                case 'range':
                    if ($request->start_date && $request->end_date) {
                        $query->whereBetween($dateField, [$request->start_date.' 00:00:00', $request->end_date.' 23:59:59']);
                    }
                    break;
                case 'all':
                default:
                    // No date filter
                    break;
            }

            // Apply status filter
            if ($request->status) {
                $query->where('status', $request->status);
            }

            $events = $query->orderBy('created_at', 'desc')->get();

            // Generate Excel file
            return $this->generateExcelFile($events, $request->period, $timeType);

        } catch (\Exception $e) {
            \Log::error('Error in EventController@exportExcel: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal export Excel: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Generate Excel file from event data
     */
    private function generateExcelFile($events, $period, $timeType = 'created')
    {
        // Create new Spreadsheet
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();

        // Set document properties
        $spreadsheet->getProperties()
            ->setCreator('Bengkel Sampah Admin')
            ->setLastModifiedBy('Bengkel Sampah Admin')
            ->setTitle('Laporan Event - '.ucfirst($period))
            ->setSubject('Laporan Data Event')
            ->setDescription('Laporan data event Bengkel Sampah')
            ->setKeywords('event, laporan, bengkel sampah')
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
        $sheet->setCellValue('A1', 'LAPORAN DATA EVENT BENGKEL SAMPAH');
        $sheet->mergeCells('A1:J1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Set subtitle
        $timeTypeText = $timeType === 'created' ? 'Dibuat' : ($timeType === 'start' ? 'Mulai' : 'Berakhir');
        $sheet->setCellValue('A2', 'Periode: '.ucfirst(str_replace('_', ' ', $period)).' (Berdasarkan waktu '.$timeTypeText.') | Total Data: '.$events->count().' event');
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
            'B5' => 'ID Event',
            'C5' => 'Judul Event',
            'D5' => 'Lokasi',
            'E5' => 'Waktu Mulai',
            'F5' => 'Waktu Berakhir',
            'G5' => 'Status',
            'H5' => 'Jumlah Peserta',
            'I5' => 'URL Cover',
            'J5' => 'Foto Cover',
        ];

        foreach ($headers as $cell => $header) {
            $sheet->setCellValue($cell, $header);
        }

        // Apply header style
        $sheet->getStyle('A5:J5')->applyFromArray($headerStyle);

        // Set data
        $row = 6;
        foreach ($events as $index => $event) {
            $sheet->setCellValue('A'.$row, $index + 1);
            $sheet->setCellValue('B'.$row, $event->id);
            $sheet->setCellValue('C'.$row, $event->title);
            $sheet->setCellValue('D'.$row, $event->location);
            $sheet->setCellValue('E'.$row, $event->start_datetime->format('d/m/Y H:i'));
            $sheet->setCellValue('F'.$row, $event->end_datetime->format('d/m/Y H:i'));
            $sheet->setCellValue('G'.$row, ucfirst($event->status));
            $sheet->setCellValue('H'.$row, $event->participants_count);
            $sheet->setCellValue('I'.$row, $event->cover ?? '-');
            $sheet->setCellValue('J'.$row, $event->cover ? 'Lihat Cover' : '-');

            // Set border for data row
            $sheet->getStyle('A'.$row.':J'.$row)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

            $row++;
        }

        // Auto-size columns
        foreach (range('A', 'J') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Set column widths for better readability
        $sheet->getColumnDimension('A')->setWidth(5);  // No
        $sheet->getColumnDimension('B')->setWidth(10); // ID
        $sheet->getColumnDimension('C')->setWidth(40); // Title
        $sheet->getColumnDimension('D')->setWidth(30); // Location
        $sheet->getColumnDimension('E')->setWidth(20); // Start Time
        $sheet->getColumnDimension('F')->setWidth(20); // End Time
        $sheet->getColumnDimension('G')->setWidth(15); // Status
        $sheet->getColumnDimension('H')->setWidth(15); // Participants
        $sheet->getColumnDimension('I')->setWidth(50); // URL
        $sheet->getColumnDimension('J')->setWidth(15); // Foto Cover

        // Create Excel file
        $writer = new Xlsx($spreadsheet);

        // Set headers for download
        $filename = 'laporan_event_'.$period.'_'.now()->format('Y-m-d_H-i-s').'.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$filename.'"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    /**
     * Export events to CSV
     */
    public function exportCsv(Request $request)
    {
        try {
            $request->validate([
                'period' => 'required|string',
                'status' => 'nullable|string',
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
                'time_type' => 'nullable|string|in:created,start,end',
            ]);

            $query = Event::withCount('participants');

            // Apply period filter based on time type
            $timeType = $request->time_type ?? 'created';
            $dateField = $timeType === 'created' ? 'created_at' : ($timeType === 'start' ? 'start_datetime' : 'end_datetime');

            switch ($request->period) {
                case 'today':
                    $query->whereDate($dateField, today());
                    break;
                case 'yesterday':
                    $query->whereDate($dateField, today()->subDay());
                    break;
                case 'this_week':
                    $query->whereBetween($dateField, [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'last_week':
                    $query->whereBetween($dateField, [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()]);
                    break;
                case 'this_month':
                    $query->whereMonth($dateField, now()->month)->whereYear($dateField, now()->year);
                    break;
                case 'last_month':
                    $query->whereMonth($dateField, now()->subMonth()->month)->whereYear($dateField, now()->subMonth()->year);
                    break;
                case 'this_year':
                    $query->whereYear($dateField, now()->year);
                    break;
                case 'last_year':
                    $query->whereYear($dateField, now()->subYear()->year);
                    break;
                case 'range':
                    if ($request->start_date && $request->end_date) {
                        $query->whereBetween($dateField, [$request->start_date.' 00:00:00', $request->end_date.' 23:59:59']);
                    }
                    break;
                case 'all':
                default:
                    // No date filter
                    break;
            }

            // Apply status filter
            if ($request->status) {
                $query->where('status', $request->status);
            }

            $events = $query->orderBy('created_at', 'desc')->get();

            // Generate CSV file
            return $this->generateCsvFile($events, $request->period, $timeType);

        } catch (\Exception $e) {
            \Log::error('Error in EventController@exportCsv: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal export CSV: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Generate CSV file from event data
     */
    private function generateCsvFile($events, $period, $timeType = 'created')
    {
        $filename = 'laporan_event_'.$period.'_'.now()->format('Y-m-d_H-i-s').'.csv';

        // Set headers for download
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="'.$filename.'"');
        header('Cache-Control: max-age=0');

        // Create output stream
        $output = fopen('php://output', 'w');

        // Add BOM for UTF-8
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

        // Write title
        fputcsv($output, ['LAPORAN DATA EVENT BENGKEL SAMPAH']);
        fputcsv($output, ['']);

        // Write subtitle
        $timeTypeText = $timeType === 'created' ? 'Dibuat' : ($timeType === 'start' ? 'Mulai' : 'Berakhir');
        fputcsv($output, ['Periode: '.ucfirst(str_replace('_', ' ', $period)).' (Berdasarkan waktu '.$timeTypeText.') | Total Data: '.$events->count().' event']);
        fputcsv($output, ['']);

        // Write export date
        fputcsv($output, ['Tanggal Export: '.now()->format('d F Y H:i:s')]);
        fputcsv($output, ['']);

        // Write headers
        $headers = [
            'No',
            'ID Event',
            'Judul Event',
            'Lokasi',
            'Waktu Mulai',
            'Waktu Berakhir',
            'Status',
            'Jumlah Peserta',
            'URL Cover',
            'Foto Cover',
        ];
        fputcsv($output, $headers);

        // Write data
        foreach ($events as $index => $event) {
            $row = [
                $index + 1,
                $event->id,
                $event->title,
                $event->location,
                $event->start_datetime->format('d/m/Y H:i'),
                $event->end_datetime->format('d/m/Y H:i'),
                ucfirst($event->status),
                $event->participants_count,
                $event->cover ?? '-',
                $event->cover ? 'Lihat Cover' : '-',
            ];
            fputcsv($output, $row);
        }

        fclose($output);
        exit;
    }

    /**
     * Export events to PDF
     */
    public function exportPdf(Request $request)
    {
        try {
            $request->validate([
                'period' => 'required|string',
                'status' => 'nullable|string',
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
                'time_type' => 'nullable|string|in:created,start,end',
            ]);

            $query = Event::withCount('participants');

            // Apply period filter based on time type
            $timeType = $request->time_type ?? 'created';
            $dateField = $timeType === 'created' ? 'created_at' : ($timeType === 'start' ? 'start_datetime' : 'end_datetime');

            switch ($request->period) {
                case 'today':
                    $query->whereDate($dateField, today());
                    break;
                case 'yesterday':
                    $query->whereDate($dateField, today()->subDay());
                    break;
                case 'this_week':
                    $query->whereBetween($dateField, [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'last_week':
                    $query->whereBetween($dateField, [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()]);
                    break;
                case 'this_month':
                    $query->whereMonth($dateField, now()->month)->whereYear($dateField, now()->year);
                    break;
                case 'last_month':
                    $query->whereMonth($dateField, now()->subMonth()->month)->whereYear($dateField, now()->subMonth()->year);
                    break;
                case 'this_year':
                    $query->whereYear($dateField, now()->year);
                    break;
                case 'last_year':
                    $query->whereYear($dateField, now()->subYear()->year);
                    break;
                case 'range':
                    if ($request->start_date && $request->end_date) {
                        $query->whereBetween($dateField, [$request->start_date.' 00:00:00', $request->end_date.' 23:59:59']);
                    }
                    break;
                case 'all':
                default:
                    // No date filter
                    break;
            }

            // Apply status filter
            if ($request->status) {
                $query->where('status', $request->status);
            }

            $events = $query->orderBy('created_at', 'desc')->get();

            // Generate PDF file
            return $this->generatePdfFile($events, $request->period, $timeType, $request->status);

        } catch (\Exception $e) {
            \Log::error('Error in EventController@exportPdf: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal export PDF: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Generate PDF file from event data
     */
    private function generatePdfFile($events, $period, $timeType = 'created', $statusFilter = null)
    {
        $timeTypeText = $timeType === 'created' ? 'Dibuat' : ($timeType === 'start' ? 'Mulai' : 'Berakhir');
        $statusText = $statusFilter ? ' | Status: '.ucfirst($statusFilter) : '';

        $data = [
            'events' => $events,
            'period' => ucfirst(str_replace('_', ' ', $period)),
            'timeType' => $timeTypeText,
            'statusFilter' => $statusText,
            'exportDate' => now()->format('d F Y H:i:s'),
            'totalData' => $events->count(),
        ];

        $pdf = Pdf::loadView('pdf.event-report', $data);

        // Enable HTML rendering for links
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'isPhpEnabled' => true,
        ]);

        $filename = 'laporan_event_'.$period.'_'.now()->format('Y-m-d_H-i-s').'.pdf';

        return $pdf->download($filename);
    }
}
