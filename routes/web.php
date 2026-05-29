<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminDashboardController;

Route::get('/', function () {
    return view('welcome');
});

// Admin Web Routes
Route::prefix('admin')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLogin'])->name('admin.login');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('admin.login.submit');
    
    Route::middleware('auth')->group(function () {
        Route::post('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');
        Route::get('/dashboard', [AdminDashboardController::class, 'dashboard'])->name('admin.dashboard');
        
        Route::get('/activations', [AdminDashboardController::class, 'activations'])->name('admin.activations');
        Route::post('/activations/activate/{id}', [AdminDashboardController::class, 'activateSalon'])->name('admin.activations.activate');
        Route::post('/activations/reject/{id}', [AdminDashboardController::class, 'rejectSalon'])->name('admin.activations.reject');
        
        Route::get('/salons', [AdminDashboardController::class, 'salons'])->name('admin.salons');
        Route::post('/salons/activate/{id}', [AdminDashboardController::class, 'activateSalonDirect'])->name('admin.salons.activate');
        Route::post('/salons/suspend/{id}', [AdminDashboardController::class, 'suspendSalon'])->name('admin.salons.suspend');
        Route::post('/salons/delete/{id}', [AdminDashboardController::class, 'deleteSalon'])->name('admin.salons.delete');

        Route::get('/payments', [AdminDashboardController::class, 'payments'])->name('admin.payments');
        
        Route::get('/customers', [AdminDashboardController::class, 'customers'])->name('admin.customers');
        
        Route::get('/bookings', [AdminDashboardController::class, 'bookings'])->name('admin.bookings');
        
        Route::get('/notifications', [AdminDashboardController::class, 'notifications'])->name('admin.notifications');
        
        Route::get('/settings', [AdminDashboardController::class, 'settings'])->name('admin.settings');
        Route::post('/settings', [AdminDashboardController::class, 'updateSettings'])->name('admin.settings.update');
    });
});
