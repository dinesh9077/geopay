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
    Route::post('/banner/ajax', [SettingController::class, 'bannerAjax'])->withoutMiddleware('webdecrypt.request')->name('banner.ajax');
	Route::get('/banner/create', [SettingController::class, 'bannerCreate'])->name('banner.create');
	Route::post('/banner/store', [SettingController::class, 'bannerStore'])->name('banner.store');
	Route::get('/banner/edit/{id}', [SettingController::class, 'bannerEdit'])->name('banner.edit');
    Route::post('/banner/update/{id}', [SettingController::class, 'bannerUpdate'])->name('banner.update');
    Route::post('/banner/delete/{id}', [SettingController::class, 'bannerDelete'])->withoutMiddleware('webdecrypt.request')->name('banner.delete');

	// Faqs
    Route::get('/faqs', [SettingController::class, 'faqs'])->name('faqs');
    Route::post('/faqs/ajax', [SettingController::class, 'faqsAjax'])->withoutMiddleware('webdecrypt.request')->name('faqs.ajax');
	Route::get('/faqs/create', [SettingController::class, 'faqsCreate'])->name('faqs.create');
	Route::post('/faqs/store', [SettingController::class, 'faqsStore'])->name('faqs.store');
	Route::get('/faqs/edit/{id}', [SettingController::class, 'faqsEdit'])->name('faqs.edit');
    Route::post('/faqs/update/{id}', [SettingController::class, 'faqsUpdate'])->name('faqs.update');
    Route::post('/faqs/delete/{id}', [SettingController::class, 'faqsDelete'])->withoutMiddleware('webdecrypt.request')->name('faqs.delete');
	
	// Third Party Key
    Route::get('/third-party-key', [SettingController::class, 'ThirdPartyKey'])->name('third-party-key'); 
    Route::post('/third-party-key/update', [SettingController::class, 'ThirdPartyKeyUpdate'])->name('third-party-key.update'); 
     
    /* 
		Route::get('/profile', [DashboardController::class, 'profile'])->name('profile');
		Route::post('/profile/update', [DashboardController::class, 'profileUpdate'])->name('profile-update'); 
    */
});

