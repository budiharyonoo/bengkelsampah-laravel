<?php

use App\Http\Controllers\Api\AddressController;
use App\Http\Controllers\Api\ArtikelController;
use App\Http\Controllers\Api\BankSampahController;
use App\Http\Controllers\Api\DeleteUserController;
use App\Http\Controllers\Api\DetailProfileController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\ForgotController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\KatalogController;
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\OtpController;
use App\Http\Controllers\Api\PilahkuCheckController;
use App\Http\Controllers\Api\PointController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\RedeemItemController;
use App\Http\Controllers\Api\RegisterController;
use App\Http\Controllers\Api\SetoranController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Auth routes
Route::post('/login', [LoginController::class, 'login']);
Route::post('/register', [RegisterController::class, 'register']);
Route::post('/send-otp', [OtpController::class, 'send']);
Route::post('/forgot', [ForgotController::class, 'reset']);

// Public routes
Route::get('/bank-sampah', [BankSampahController::class, 'index']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/home', [HomeController::class, 'index']);
    Route::get('/profile', [ProfileController::class, 'index']);
    Route::get('/point', [PointController::class, 'index']);
    Route::get('/detail-profile', [DetailProfileController::class, 'index']);
    Route::put('/edit-profile', [DetailProfileController::class, 'update']);
    Route::get('/artikels', [ArtikelController::class, 'index']);
    Route::get('/artikels/{id}', [ArtikelController::class, 'show']);
    Route::get('/events', [EventController::class, 'index']);
    Route::get('/events/{id}', [EventController::class, 'show']);
    Route::post('/events/{id}/toggle-join', [EventController::class, 'toggleJoin']);
    Route::get('/addresses', [AddressController::class, 'index']);
    Route::post('/addresses', [AddressController::class, 'store']);
    Route::put('/addresses/{id}', [AddressController::class, 'update']);
    Route::delete('/addresses/{id}', [AddressController::class, 'destroy']);
    Route::post('/delete-account', [DeleteUserController::class, 'delete']);
    Route::get('/katalog', [KatalogController::class, 'index']);
    Route::get('/katalog/{id}', [KatalogController::class, 'show']);
    Route::post('/pilahku/check', [PilahkuCheckController::class, 'check']);

    // Setoran routes
    Route::get('/setorans', [SetoranController::class, 'index']);
    Route::post('/setorans', [SetoranController::class, 'store']);
    Route::get('/setorans/{id}', [SetoranController::class, 'show']);
    Route::post('/setorans/{id}/cancel', [SetoranController::class, 'cancelSetoran']);
    Route::put('/setorans/{id}/status', [SetoranController::class, 'updateStatus']);

    // Notification routes
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::post('/notifications/mark-as-read', [NotificationController::class, 'markAsRead']);
    Route::post('/notifications/mark-all-as-read', [NotificationController::class, 'markAllAsRead']);
    Route::delete('/notifications', [NotificationController::class, 'destroy']);
    Route::post('/fcm-token', [NotificationController::class, 'updateFcmToken']);

    // Test notification route
    Route::post('/test-notification', [NotificationController::class, 'testNotification']);

    // Redeem Item route
    Route::get('/redeem-items', [RedeemItemController::class, 'index']);
});
