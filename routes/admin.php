<?php

use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\DashboardController;
use Illuminate\Support\Facades\Route;
 
// Admin authentication routes
Route::get('login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
Route::post('login', [AdminAuthController::class, 'login'])->name('admin.login.submit');
Route::post('logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

// Protected admin routes
Route::middleware('auth:admin')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('admin.dashboard');
	
    Route::get('/profile', [DashboardController::class, 'profile'])->name('admin.profile');
    Route::post('/profile/update', [DashboardController::class, 'profileUpdate'])->name('admin.profile-update');
	 
});
