<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\QrController;
use App\Http\Controllers\BookingController;

// Public Routes
Route::post('/register/customer', [AuthController::class, 'registerCustomer']);
Route::post('/register/owner', [AuthController::class, 'registerOwner']);
Route::post('/login', [AuthController::class, 'login']);

// Protected Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::get('/check-status', [AuthController::class, 'checkStatus']);

    // Owner Payment Routes
    Route::post('/payments/upload-proof', [PaymentController::class, 'uploadProof']);
    Route::get('/payments/status', [PaymentController::class, 'getPaymentStatus']);

    // Admin Payment Routes
    Route::get('/admin/payments/pending', [PaymentController::class, 'getPendingPayments']);
    Route::post('/admin/payments/activate/{id}', [PaymentController::class, 'activatePayment']);
    Route::post('/admin/payments/reject/{id}', [PaymentController::class, 'rejectPayment']);

    // QR Owner Routes
    Route::get('/owner/qr', [QrController::class, 'getOwnerQr']);

    // QR Customer Routes
    Route::post('/customer/link-salon', [QrController::class, 'linkSalon']);
    Route::get('/customer/linked-salon', [QrController::class, 'getLinkedSalon']);
    Route::delete('/customer/unlink-salon', [QrController::class, 'unlinkSalon']);

    // Booking Customer Routes
    Route::post('/bookings/create', [BookingController::class, 'createBooking']);
    Route::get('/bookings/history', [BookingController::class, 'getHistory']);
    Route::post('/bookings/cancel/{id}', [BookingController::class, 'cancelBooking']);

    // Booking Owner Routes
    Route::get('/owner/bookings/today', [BookingController::class, 'getTodayBookings']);
    Route::get('/owner/bookings', [BookingController::class, 'getAllBookings']);
    Route::post('/owner/bookings/confirm/{id}', [BookingController::class, 'confirmBooking']);
    Route::post('/owner/bookings/reject/{id}', [BookingController::class, 'rejectBooking']);
    Route::post('/owner/bookings/complete/{id}', [BookingController::class, 'completeBooking']);
    Route::post('/owner/bookings/reschedule/{id}', [BookingController::class, 'rescheduleBooking']);
});
