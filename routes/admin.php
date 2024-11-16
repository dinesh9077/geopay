<?php

use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\SettingController;
use Illuminate\Support\Facades\Route; 
 
// Admin authentication routes
Route::get('login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
Route::post('login', [AdminAuthController::class, 'login'])->name('admin.login.submit')->middleware(['webdecrypt.request']);
Route::post('logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

// Protected admin routes

Route::middleware(['auth:admin', 'webdecrypt.request'])->as('admin.')->group(function () {
    
    Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');
	
	// Settings
    Route::get('/general-setting', [SettingController::class, 'generalSetting'])->name('general-setting');
    Route::post('/general-setting/update', [SettingController::class, 'generalSettingUpdate'])->name('general-setting.update');
	
	// Banners
    Route::get('/banner', [SettingController::class, 'banner'])->name('banner');
    Route::post('/banner/ajax', [SettingController::class, 'bannerAjax'])
        ->withoutMiddleware('webdecrypt.request') // Exclude this middleware
        ->name('banner.ajax');
	Route::get('/banner/create', [SettingController::class, 'bannerCreate'])->name('banner.create');
    Route::post('/banner/update', [SettingController::class, 'bannerUpdate'])->name('banner.update');
     
    /* 
		Route::get('/profile', [DashboardController::class, 'profile'])->name('profile');
		Route::post('/profile/update', [DashboardController::class, 'profileUpdate'])->name('profile-update'); 
    */
});

