<?php

use App\Http\Controllers\Admin\XPResetController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ArtikelController;
use App\Http\Controllers\Auth\AdminAuthController;
use App\Http\Controllers\BankSampahController;
use App\Http\Controllers\BlastNotificationController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardRedeemController;
use App\Http\Controllers\DashboardTransaksiController;
use App\Http\Controllers\DashboardUserController;
use App\Http\Controllers\DashboardUserRedeemController;
use App\Http\Controllers\DeleteAccountController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\OfftakerController;
use App\Http\Controllers\RedeemItemController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SampahController;
use App\Http\Controllers\SkBankSampahController;
use App\Http\Controllers\WasteInventoryController;
use App\Http\Controllers\WasteTransactionController;
use App\Http\Controllers\WilayahController;
use Illuminate\Support\Facades\Route;

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

    // Redeem Item routes (Master Data)
    Route::resource('redeem-item', RedeemItemController::class)->names([
        'index' => 'dashboard.redeem-item.index',
        'create' => 'dashboard.redeem-item.create',
        'store' => 'dashboard.redeem-item.store',
        'edit' => 'dashboard.redeem-item.edit',
        'update' => 'dashboard.redeem-item.update',
        'destroy' => 'dashboard.redeem-item.destroy',
    ]);
    Route::delete('redeem-item/{id}/bulk', [RedeemItemController::class, 'bulkDestroy'])->name('dashboard.redeem-item.bulk-destroy');
    Route::post('redeem-item/export/excel', [RedeemItemController::class, 'exportExcel'])->name('dashboard.redeem-item.export.excel');
    Route::post('redeem-item/export/csv', [RedeemItemController::class, 'exportCsv'])->name('dashboard.redeem-item.export.csv');
    Route::post('redeem-item/export/pdf', [RedeemItemController::class, 'exportPdf'])->name('dashboard.redeem-item.export.pdf');

    // Blast Notification routes
    Route::get('blast-notification', [BlastNotificationController::class, 'index'])->name('dashboard.blast-notification.index');
    Route::get('blast-notification/create', [BlastNotificationController::class, 'create'])->name('dashboard.blast-notification.create');
    Route::post('blast-notification', [BlastNotificationController::class, 'store'])->name('dashboard.blast-notification.store');

    // XP Reset routes
    Route::get('xp-reset', [XPResetController::class, 'index'])->name('admin.xp-reset.index');
    Route::post('xp-reset/execute', [XPResetController::class, 'executeReset'])->name('admin.xp-reset.execute');
    Route::get('xp-reset/history', [XPResetController::class, 'history'])->name('admin.xp-reset.history');

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

    // Wilayah (region cascade) routes
    Route::get('wilayah/provinces', [WilayahController::class, 'provinces'])->name('wilayah.provinces');
    Route::get('wilayah/cities', [WilayahController::class, 'cities'])->name('wilayah.cities');
    Route::get('wilayah/districts', [WilayahController::class, 'districts'])->name('wilayah.districts');
    Route::get('wilayah/villages', [WilayahController::class, 'villages'])->name('wilayah.villages');

    // SK Bank Sampah routes
    Route::post('sk-bank-sampah/preview', [SkBankSampahController::class, 'preview'])->name('sk-bank-sampah.preview');
    Route::post('bank-sampah/{bankSampah}/sk', [SkBankSampahController::class, 'store'])->name('sk-bank-sampah.store');
    Route::get('bank-sampah/{bankSampah}/sk', [SkBankSampahController::class, 'show'])->name('sk-bank-sampah.show');
    Route::get('sk-bank-sampah/{sk}/download', [SkBankSampahController::class, 'download'])->name('sk-bank-sampah.download');

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

    // User Redeem Request routes (for managing user redemption requests)
    Route::get('user-redeem', [DashboardUserRedeemController::class, 'index'])->name('dashboard.user-redeem.index');
    Route::get('user-redeem/{id}/edit', [DashboardUserRedeemController::class, 'edit'])->name('dashboard.user-redeem.edit');
    Route::put('user-redeem/{id}', [DashboardUserRedeemController::class, 'update'])->name('dashboard.user-redeem.update');
    Route::post('user-redeem/export/excel', [DashboardUserRedeemController::class, 'exportExcel'])->name('dashboard.user-redeem.export.excel');
    Route::post('user-redeem/export/csv', [DashboardUserRedeemController::class, 'exportCsv'])->name('dashboard.user-redeem.export.csv');
    Route::post('user-redeem/export/pdf', [DashboardUserRedeemController::class, 'exportPdf'])->name('dashboard.user-redeem.export.pdf');

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

    // Offtaker routes
    Route::resource('offtakers', OfftakerController::class)->names([
        'index' => 'offtakers.index',
        'create' => 'offtakers.create',
        'store' => 'offtakers.store',
        'show' => 'offtakers.show',
        'edit' => 'offtakers.edit',
        'update' => 'offtakers.update',
        'destroy' => 'offtakers.destroy',
    ]);
    Route::patch('offtakers/{offtaker}/toggle-status', [OfftakerController::class, 'toggleStatus'])->name('offtakers.toggle-status');

    // Waste Transactions routes
    Route::prefix('waste-transactions')->group(function () {
        Route::get('/', [WasteTransactionController::class, 'index'])->name('waste-transactions.index');
        Route::get('/sales', [WasteTransactionController::class, 'salesIndex'])->name('waste-transactions.sales.index');
        Route::get('/sales/create', [WasteTransactionController::class, 'createSale'])->name('waste-transactions.sales.create');
        Route::post('/sales', [WasteTransactionController::class, 'storeSale'])->name('waste-transactions.sales.store');
        // Processing routes now redirect to sales routes (merged functionality)
        Route::get('/processing', fn () => redirect()->route('waste-transactions.sales.index'))->name('waste-transactions.processing.index');
        Route::get('/processing/create', fn () => redirect()->route('waste-transactions.sales.create'))->name('waste-transactions.processing.create');
        Route::post('/processing', [WasteTransactionController::class, 'storeSale'])->name('waste-transactions.processing.store');
        Route::get('/get-inventory', [WasteTransactionController::class, 'getInventory'])->name('waste-transactions.get-inventory');
        Route::get('/get-offtakers-by-type', [WasteTransactionController::class, 'getOfftakersByType'])->name('waste-transactions.get-offtakers-by-type');
        Route::post('/export/excel', [WasteTransactionController::class, 'exportExcel'])->name('waste-transactions.export.excel');
        Route::post('/export/pdf', [WasteTransactionController::class, 'exportPdf'])->name('waste-transactions.export.pdf');
        Route::post('/export/csv', [WasteTransactionController::class, 'exportCsv'])->name('waste-transactions.export.csv');
        Route::get('/{wasteTransaction}', [WasteTransactionController::class, 'show'])->name('waste-transactions.show');
    });

    // Waste Inventory routes
    Route::get('waste-inventory', [WasteInventoryController::class, 'index'])->name('waste-inventory.index');
    Route::get('waste-inventory/{bankSampah}', [WasteInventoryController::class, 'show'])->name('waste-inventory.show');

    // Laporan (Reports) routes
    Route::prefix('reports')->group(function () {
        Route::get('/laba-rugi', [ReportController::class, 'labaRugi'])->name('reports.laba-rugi');
        Route::get('/penjualan', [ReportController::class, 'penjualan'])->name('reports.penjualan');
        Route::get('/pengolahan', [ReportController::class, 'pengolahan'])->name('reports.pengolahan');
        Route::post('/laba-rugi/export/excel', [ReportController::class, 'exportLabaRugiExcel'])->name('reports.laba-rugi.export.excel');
        Route::post('/laba-rugi/export/pdf', [ReportController::class, 'exportLabaRugiPdf'])->name('reports.laba-rugi.export.pdf');
        Route::post('/laba-rugi/export/csv', [ReportController::class, 'exportLabaRugiCsv'])->name('reports.laba-rugi.export.csv');
        Route::post('/penjualan/export/excel', [ReportController::class, 'exportPenjualanExcel'])->name('reports.penjualan.export.excel');
        Route::post('/penjualan/export/pdf', [ReportController::class, 'exportPenjualanPdf'])->name('reports.penjualan.export.pdf');
        Route::post('/penjualan/export/csv', [ReportController::class, 'exportPenjualanCsv'])->name('reports.penjualan.export.csv');
        Route::post('/pengolahan/export/excel', [ReportController::class, 'exportPengolahanExcel'])->name('reports.pengolahan.export.excel');
        Route::post('/pengolahan/export/pdf', [ReportController::class, 'exportPengolahanPdf'])->name('reports.pengolahan.export.pdf');
        Route::post('/pengolahan/export/csv', [ReportController::class, 'exportPengolahanCsv'])->name('reports.pengolahan.export.csv');
    });

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
            'email' => 'required|email|max:255|unique:admins,email,'.$admin->id,
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
