<?php

namespace App\Http\Controllers;

use App\Models\Offtaker;
use Illuminate\Http\Request;

class OfftakerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Offtaker::query();

        // Search by name or code
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('kode_offtaker', 'like', "%{$search}%")
                    ->orWhere('nama_pic', 'like', "%{$search}%");
            });
        }

        // Filter by type
        if ($request->filled('tipe')) {
            $query->where('tipe', $request->tipe);
        }

        // Filter by status
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active === 'true');
        }

        $offtakers = $query->withCount('wasteTransactions')
            ->orderBy('nama')
            ->paginate(15)
            ->withQueryString();

        return view('offtakers.index', compact('offtakers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('offtakers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'tipe' => 'required|in:buyer,processor,both',
            'nama_pic' => 'required|string|max:255',
            'kontak_pic' => 'required|string|max:50',
            'alamat' => 'nullable|string',
        ]);

        $validated['is_active'] = true;

        $offtaker = Offtaker::create($validated);

        return redirect()
            ->route('offtakers.index')
            ->with('success', "Offtaker {$offtaker->nama} berhasil ditambahkan dengan kode {$offtaker->kode_offtaker}");
    }

    /**
     * Display the specified resource.
     */
    public function show(Offtaker $offtaker)
    {
        $offtaker->loadCount('wasteTransactions');
        $offtaker->load(['wasteTransactions' => function ($query) {
            $query->with('bankSampah:id,nama_bank_sampah')
                ->orderByDesc('tanggal_transaksi')
                ->limit(10);
        }]);

        return view('offtakers.show', compact('offtaker'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Offtaker $offtaker)
    {
        return view('offtakers.edit', compact('offtaker'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Offtaker $offtaker)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'tipe' => 'required|in:buyer,processor,both',
            'nama_pic' => 'required|string|max:255',
            'kontak_pic' => 'required|string|max:50',
            'alamat' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $offtaker->update($validated);

        return redirect()
            ->route('offtakers.index')
            ->with('success', "Offtaker {$offtaker->nama} berhasil diperbarui");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Offtaker $offtaker)
    {
        // Check if offtaker has transactions
        if ($offtaker->wasteTransactions()->exists()) {
            return back()->with('error', 'Tidak dapat menghapus offtaker yang memiliki transaksi');
        }

        $nama = $offtaker->nama;
        $offtaker->delete();

        return redirect()
            ->route('offtakers.index')
            ->with('success', "Offtaker {$nama} berhasil dihapus");
    }

    /**
     * Toggle active status.
     */
    public function toggleStatus(Offtaker $offtaker)
    {
        $offtaker->update(['is_active' => ! $offtaker->is_active]);

        $status = $offtaker->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', "Offtaker {$offtaker->nama} berhasil {$status}");
    }
}
