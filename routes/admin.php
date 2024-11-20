<?php

use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\Admin\CompaniesController;
use App\Http\Controllers\Admin\ExchangeRateController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route; 
 
// Admin authentication routes
Route::get('login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
Route::post('login', [AdminAuthController::class, 'login'])->name('admin.login.submit')->middleware(['webdecrypt.request']);
Route::post('logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

// Protected admin routes

Route::middleware(['auth:admin', 'webdecrypt.request'])->as('admin.')->group(function () 
{ 
    Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');
	
	// Settings
    Route::get('/general-setting', [SettingController::class, 'generalSetting'])->name('general-setting')->middleware('permission:general_setting.view');
    Route::post('/general-setting/update', [SettingController::class, 'generalSettingUpdate'])->name('general-setting.update');
    Route::post('/user-limit/update', [SettingController::class, 'UserLimitUpdate'])->name('user-limit.update');
	
	// Banners
    Route::get('/banner', [SettingController::class, 'banner'])->name('banner')->middleware('permission:banner.view');
    Route::post('/banner/ajax', [SettingController::class, 'bannerAjax'])->withoutMiddleware('webdecrypt.request')->name('banner.ajax');
	Route::get('/banner/create', [SettingController::class, 'bannerCreate'])->name('banner.create');
	Route::post('/banner/store', [SettingController::class, 'bannerStore'])->name('banner.store');
	Route::get('/banner/edit/{id}', [SettingController::class, 'bannerEdit'])->name('banner.edit');
    Route::post('/banner/update/{id}', [SettingController::class, 'bannerUpdate'])->name('banner.update');
    Route::post('/banner/delete/{id}', [SettingController::class, 'bannerDelete'])->withoutMiddleware('webdecrypt.request')->name('banner.delete');

	// Faqs
    Route::get('/faqs', [SettingController::class, 'faqs'])->name('faqs')->middleware('permission:faqs.view');
    Route::post('/faqs/ajax', [SettingController::class, 'faqsAjax'])->withoutMiddleware('webdecrypt.request')->name('faqs.ajax');
	Route::get('/faqs/create', [SettingController::class, 'faqsCreate'])->name('faqs.create');
	Route::post('/faqs/store', [SettingController::class, 'faqsStore'])->name('faqs.store');
	Route::get('/faqs/edit/{id}', [SettingController::class, 'faqsEdit'])->name('faqs.edit');
    Route::post('/faqs/update/{id}', [SettingController::class, 'faqsUpdate'])->name('faqs.update');
    Route::post('/faqs/delete/{id}', [SettingController::class, 'faqsDelete'])->withoutMiddleware('webdecrypt.request')->name('faqs.delete');
	
	// Third Party Key
    Route::get('/third-party-key', [SettingController::class, 'ThirdPartyKey'])->name('third-party-key')
	->middleware('permission:third_party_api.view');
    Route::post('/third-party-key/update', [SettingController::class, 'ThirdPartyKeyUpdate'])->name('third-party-key.update'); 
    
	// Profile
	Route::get('/profile', [SettingController::class, 'profile'])->name('profile');
	Route::post('/profile/update', [SettingController::class, 'profileUpdate'])->name('profile-update'); 
  
	//Roles
	Route::get('/roles', [StaffController::class, 'roles'])->name('roles')->middleware('permission:role.view');
    Route::post('/roles/ajax', [StaffController::class, 'rolesAjax'])->withoutMiddleware('webdecrypt.request')->name('roles.ajax');
	Route::get('/roles/create', [StaffController::class, 'rolesCreate'])->name('roles.create');
	Route::post('/roles/store', [StaffController::class, 'rolesStore'])->name('roles.store');
	Route::get('/roles/edit/{id}', [StaffController::class, 'rolesEdit'])->name('roles.edit');
    Route::post('/roles/update/{id}', [StaffController::class, 'rolesUpdate'])->name('roles.update');
    Route::post('/roles/delete/{id}', [StaffController::class, 'rolesDelete'])->withoutMiddleware('webdecrypt.request')->name('roles.delete'); 
	Route::get('roles/groups/{id}', [StaffController::class, 'rolesGroups']);
	
	//staffs
	Route::get('/staff', [StaffController::class, 'staff'])->name('staff')->middleware('permission:staff.view');
    Route::post('/staff/ajax', [StaffController::class, 'staffAjax'])->withoutMiddleware('webdecrypt.request')->name('staff.ajax');
	Route::get('/staff/create', [StaffController::class, 'staffCreate'])->name('staff.create');
	Route::post('/staff/store', [StaffController::class, 'staffStore'])->name('staff.store');
	Route::get('/staff/edit/{id}', [StaffController::class, 'staffEdit'])->name('staff.edit');
    Route::post('/staff/update/{id}', [StaffController::class, 'staffUpdate'])->name('staff.update');
    Route::post('/staff/delete/{id}', [StaffController::class, 'staffDelete'])->withoutMiddleware('webdecrypt.request')->name('staff.delete'); 
	Route::get('/staff/permission/{id}', [StaffController::class, 'staffPermission'])->name('staff.permission');
	Route::post('/staff/permission/{id}', [StaffController::class, 'staffPermissionUpdate'])->name('staff.permission-update');
	
	//Manage Companies 
	Route::get('/companies/active', [CompaniesController::class, 'companiesActive'])
	->name('company.active')->middleware('permission:active_company.view'); 
	
	Route::get('/companies/pending', [CompaniesController::class, 'companiesPending'])
	->name('company.pending')->middleware('permission:pending_company.view'); 
	
	Route::get('/companies/block', [CompaniesController::class, 'companiesBlock'])
	->name('company.block')->middleware('permission:block_company.view'); 
	
	Route::post('/companies/ajax', [CompaniesController::class, 'companiesAjax'])->withoutMiddleware('webdecrypt.request')->name('companies.ajax');
	Route::get('/companies/edit/{id}', [CompaniesController::class, 'companiesEdit'])->name('companies.edit');
    Route::post('/companies/update/{id}', [CompaniesController::class, 'companiesUpdate'])->name('companies.update');
	Route::post('/companies/update-status', [CompaniesController::class, 'companiesUpdateStatus'])->name('companies.update-status');
	
	//Manage Exchnage Rate
	Route::get('/exchange-rate', [ExchangeRateController::class, 'exchangeRate'])
	->name('exchange-rate')->middleware('permission:exchange_rate.view');
	Route::post('/exchange-rate/ajax', [ExchangeRateController::class, 'exchangeRateAjax'])->withoutMiddleware('webdecrypt.request')->name('exchange-rate.ajax');
	Route::get('/exchange-rate/import', [ExchangeRateController::class, 'exchangeRateImport'])->name('exchange-rate.import');
	Route::post('/exchange-rate/store', [ExchangeRateController::class, 'exchangeRateStore'])->name('exchange-rate.store');
	Route::post('/exchange-rate/delete/{id}', [ExchangeRateController::class, 'exchangeRateDelete'])->withoutMiddleware('webdecrypt.request')->name('exchange-rate.delete'); 
	
	//Manage Users 
	Route::get('/user/active', [UserController::class, 'userActive'])->name('user.active')->middleware('permission:active_user.view');  
	Route::get('/user/pending', [UserController::class, 'userPending'])->name('user.pending')->middleware('permission:pending_user.view');
	Route::get('/user/block', [UserController::class, 'userBlock'])->name('user.block')->middleware('permission:block_user.view');  
	Route::post('/user/ajax', [UserController::class, 'userAjax'])->withoutMiddleware('webdecrypt.request')->name('user.ajax');
	Route::get('/user/edit/{id}', [UserController::class, 'userEdit'])->name('user.edit');
    Route::post('/user/update/{id}', [UserController::class, 'userUpdate'])->name('user.update');
	Route::post('/user/update-status', [UserController::class, 'userUpdateStatus'])->name('user.update-status');
	
});

