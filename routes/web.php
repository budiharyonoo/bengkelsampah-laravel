<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ArtikelController;
use App\Http\Controllers\Auth\AdminAuthController;
use App\Http\Controllers\BankSampahController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardRedeemController;
use App\Http\Controllers\DashboardTransaksiController;
use App\Http\Controllers\DashboardUserController;
use App\Http\Controllers\DeleteAccountController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\SampahController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

Route::get('/optimize', function () {
    Artisan::call('optimize');
    return 'php artisan optimize!';
});

Route::get('/', function () {
    return view('welcome');
});

// Privacy Policy and Delete Account Routes
Route::get('/privacy-policy', function () {
    return view('privacy-policy');
})->name('privacy.policy');

Route::get('/delete-account', function () {
    return view('delete-account');
})->name('delete.account');

Route::post('/delete-account', [DeleteAccountController::class, 'submit'])->name('delete.account.submit');

// Dashboard Authentication Routes
Route::prefix('dashboard')->group(function () {
    Route::get('login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
    Route::post('login', [AdminAuthController::class, 'login']);
    Route::post('logout', [AdminAuthController::class, 'logout'])->name('admin.logout');
});

// Dashboard Routes (Protected)
Route::prefix('dashboard')->middleware(['admin'])->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('dashboard');

    // Transaksi routes
    Route::resource('transaksi', DashboardTransaksiController::class)->names([
        'index' => 'dashboard.transaksi',
        'show' => 'dashboard.transaksi.show',
    ]);
    Route::put('/transaksi/{id}/status', [DashboardTransaksiController::class, 'updateStatus'])->name('dashboard.transaksi.update-status');
    Route::get('/transaksi/export', [DashboardTransaksiController::class, 'export'])->name('dashboard.transaksi.export');
    Route::get('/transaksi/{id}/print-struk', [DashboardTransaksiController::class, 'printStruk'])->name('dashboard.transaksi.print-struk');
    Route::post('/dashboard/transaksi/export/excel', [DashboardTransaksiController::class, 'exportExcel'])->name('dashboard.transaksi.export.excel');
    Route::post('/dashboard/transaksi/export/csv', [DashboardTransaksiController::class, 'exportCsv'])->name('dashboard.transaksi.export.csv');
    Route::post('/dashboard/transaksi/export/pdf', [DashboardTransaksiController::class, 'exportPdf'])->name('dashboard.transaksi.export.pdf');

    // Sampah routes
    Route::resource('sampah', SampahController::class)->names([
        'index' => 'dashboard.sampah',
        'create' => 'dashboard.sampah.create',
        'store' => 'dashboard.sampah.store',
        'show' => 'dashboard.sampah.show',
        'edit' => 'dashboard.sampah.edit',
        'update' => 'dashboard.sampah.update',
        'destroy' => 'dashboard.sampah.destroy',
    ]);
    Route::post('/dashboard/sampah/export/excel', [SampahController::class, 'exportExcel'])->name('dashboard.sampah.export.excel');
    Route::post('/dashboard/sampah/export/csv', [SampahController::class, 'exportCsv'])->name('dashboard.sampah.export.csv');
    Route::post('/dashboard/sampah/export/pdf', [SampahController::class, 'exportPdf'])->name('dashboard.sampah.export.pdf');
    Route::post('/sampah/{id}/update-harga', [App\Http\Controllers\SampahController::class, 'updateHargaCabang'])->name('dashboard.sampah.update-harga');

    // Category routes
    Route::resource('category', CategoryController::class)->names([
        'index' => 'dashboard.category',
        'create' => 'dashboard.category.create',
        'store' => 'dashboard.category.store',
        'show' => 'dashboard.category.show',
        'edit' => 'dashboard.category.edit',
        'update' => 'dashboard.category.update',
        'destroy' => 'dashboard.category.destroy',
    ]);
    Route::post('category/export/excel', [CategoryController::class, 'exportExcel'])->name('dashboard.category.export.excel');
    Route::post('category/export/csv', [CategoryController::class, 'exportCsv'])->name('dashboard.category.export.csv');
    Route::post('category/export/pdf', [CategoryController::class, 'exportPdf'])->name('dashboard.category.export.pdf');

    // Kategori routes (using sidebar kategori)
    Route::resource('kategori', KategoriController::class)->names([
        'index' => 'dashboard.kategori',
        'create' => 'dashboard.kategori.create',
        'store' => 'dashboard.kategori.store',
        'show' => 'dashboard.kategori.show',
        'edit' => 'dashboard.kategori.edit',
        'update' => 'dashboard.kategori.update',
        'destroy' => 'dashboard.kategori.destroy',
    ]);

    // Force delete kategori beserta semua artikel di dalamnya
    Route::delete('kategori/{id}/force', [KategoriController::class, 'forceDestroy'])->name('dashboard.kategori.force-destroy');

    // Bank Sampah routes
    Route::resource('bank-sampah', BankSampahController::class)->names([
        'index' => 'dashboard.bank',
        'create' => 'dashboard.bank.create',
        'store' => 'dashboard.bank.store',
        'show' => 'dashboard.bank.show',
        'edit' => 'dashboard.bank.edit',
        'update' => 'dashboard.bank.update',
        'destroy' => 'dashboard.bank.destroy',
    ]);
    Route::post('bank-sampah/bulk-delete', [BankSampahController::class, 'bulkDestroy'])->name('dashboard.bank.bulk-destroy');
    Route::post('bank-sampah/export/excel', [BankSampahController::class, 'exportExcel'])->name('dashboard.bank.export.excel');
    Route::post('bank-sampah/export/csv', [BankSampahController::class, 'exportCsv'])->name('dashboard.bank.export.csv');
    Route::post('bank-sampah/export/pdf', [BankSampahController::class, 'exportPdf'])->name('dashboard.bank.export.pdf');

    // User routes
    Route::resource('user', DashboardUserController::class)->names([
        'index' => 'dashboard.user',
        'show' => 'dashboard.user.show',
        'edit' => 'dashboard.user.edit',
        'update' => 'dashboard.user.update',
        'destroy' => 'dashboard.user.destroy',
    ])->except(['create', 'store']);
    Route::post('user/bulk-destroy', [DashboardUserController::class, 'bulkDestroy'])->name('dashboard.user.bulk-destroy');
    Route::post('user/export/excel', [DashboardUserController::class, 'exportExcel'])->name('dashboard.user.export.excel');
    Route::post('user/export/csv', [DashboardUserController::class, 'exportCsv'])->name('dashboard.user.export.csv');
    Route::post('user/export/pdf', [DashboardUserController::class, 'exportPdf'])->name('dashboard.user.export.pdf');
    Route::post('user/{id}/export/excel', [DashboardUserController::class, 'exportUserDetailExcel'])->name('dashboard.user.export-detail.excel');
    Route::post('user/{id}/export/csv', [DashboardUserController::class, 'exportUserDetailCsv'])->name('dashboard.user.export-detail.csv');
    Route::post('user/{id}/export/pdf', [DashboardUserController::class, 'exportUserDetailPdf'])->name('dashboard.user.export-detail.pdf');

    // Poin routes (for redeem functionality)
    Route::get('/poin', [DashboardRedeemController::class, 'index'])->name('dashboard.poin');
    Route::get('/poin/create', [DashboardRedeemController::class, 'create'])->name('dashboard.poin.create');
    Route::post('/poin/search-user', [DashboardRedeemController::class, 'searchUser'])->name('dashboard.poin.search-user');
    Route::get('/poin/user/{id}', [DashboardRedeemController::class, 'getUserInfo'])->name('dashboard.poin.user-info');
    Route::post('/poin/process', [DashboardRedeemController::class, 'redeem'])->name('dashboard.poin.process');
    Route::get('/poin/export/{type}', [DashboardRedeemController::class, 'export'])->name('dashboard.poin.export');

    // Artikel routes
    Route::resource('artikel', ArtikelController::class)->names([
        'index' => 'dashboard.artikel',
        'create' => 'dashboard.artikel.create',
        'store' => 'dashboard.artikel.store',
        'show' => 'dashboard.artikel.show',
        'edit' => 'dashboard.artikel.edit',
        'update' => 'dashboard.artikel.update',
        'destroy' => 'dashboard.artikel.destroy',
    ]);
    Route::post('artikel/export/excel', [ArtikelController::class, 'exportExcel'])->name('dashboard.artikel.export.excel');
    Route::post('artikel/export/csv', [ArtikelController::class, 'exportCsv'])->name('dashboard.artikel.export.csv');
    Route::post('artikel/export/pdf', [ArtikelController::class, 'exportPdf'])->name('dashboard.artikel.export.pdf');

    // Additional routes
    Route::get('artikel/{id}/edit', [ArtikelController::class, 'edit'])->name('artikel.edit');
    Route::put('artikel/{id}', [ArtikelController::class, 'update'])->name('artikel.update');

    // Event routes
    Route::resource('event', EventController::class)->names([
        'index' => 'dashboard.event',
        'create' => 'dashboard.event.create',
        'store' => 'dashboard.event.store',
        'show' => 'dashboard.event.show',
        'edit' => 'dashboard.event.edit',
        'update' => 'dashboard.event.update',
        'destroy' => 'dashboard.event.destroy',
    ]);

    // Event result routes
    Route::post('event/{id}/submit-result', [EventController::class, 'submitResult'])->name('dashboard.event.submit-result');
    Route::put('event/{id}/update-result', [EventController::class, 'updateResult'])->name('dashboard.event.update-result');
    Route::get('event/{id}/generate-report', [EventController::class, 'generateReport'])->name('dashboard.event.generate-report');
    Route::post('event/{id}/complete', [EventController::class, 'completeEvent'])->name('dashboard.event.complete');

    // Event export routes
    Route::post('event/export/excel', [EventController::class, 'exportExcel'])->name('dashboard.event.export.excel');
    Route::post('event/export/csv', [EventController::class, 'exportCsv'])->name('dashboard.event.export.csv');
    Route::post('event/export/pdf', [EventController::class, 'exportPdf'])->name('dashboard.event.export.pdf');

    // Admin routes
    Route::post('/admin', [AdminController::class, 'store'])->name('dashboard.admin.store');
    Route::get('/admin/{id}', [AdminController::class, 'show'])->name('dashboard.admin.show');
    Route::put('/admin/{id}', [AdminController::class, 'update'])->name('dashboard.admin.update');
    Route::delete('/admin/{id}', [AdminController::class, 'destroy'])->name('dashboard.admin.destroy');

    Route::get('/dashboard/profile', function () {
        $admin = auth('admin')->user();

        return view('viewprofile', compact('admin'));
    })->name('admin.profile');

    Route::post('/dashboard/profile', function (\Illuminate\Http\Request $request) {
        $admin = auth('admin')->user();
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:admins,email,' . $admin->id,
            'password' => 'nullable|string|min:6',
        ]);
        $admin->name = $validated['name'];
        $admin->email = $validated['email'];
        if (! empty($validated['password'])) {
            $admin->password = bcrypt($validated['password']);
        }
        $admin->save();

        return redirect()->route('admin.profile')->with('success', 'Profil berhasil diperbarui!');
    })->name('admin.profile.update');
});
