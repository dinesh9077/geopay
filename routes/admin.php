<?php

use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\Admin\CompaniesController;
use App\Http\Controllers\Admin\ExchangeRateController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\TransactionController;
use Illuminate\Support\Facades\Route;

// Admin authentication routes 
Route::get('login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
Route::post('login', [AdminAuthController::class, 'login'])->name('admin.login.submit')->middleware(['webdecrypt.request']);
Route::post('logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

// Protected admin routes
 
Route::middleware(['auth:admin', 'webdecrypt.request'])->as('admin.')->group(function ()
{
    Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');

    // Permission
    Route::get('/permission', [PermissionController::class, 'index'])->name('permission.index'); 
    Route::post('/permission/store', [PermissionController::class, 'store'])->name('permission.store')->withoutMiddleware('webdecrypt.request'); 
    Route::post('/permission/update/{id}', [PermissionController::class, 'update'])->name('permission.update')->withoutMiddleware('webdecrypt.request'); 
    Route::post('/permission/position', [PermissionController::class, 'positionUpdate'])->withoutMiddleware('webdecrypt.request'); 
    Route::get('/permission/delete/{id}', [PermissionController::class, 'delete']); 
  
	// Settings
    Route::get('/general-setting', [SettingController::class, 'generalSetting'])->name('general-setting');
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
    Route::get('/third-party-key', [SettingController::class, 'thirdPartyKey'])->name('third-party-key');
    Route::post('/third-party-key/update', [SettingController::class, 'thirdPartyKeyUpdate'])->name('third-party-key.update');
    Route::post('/third-party-key/lightnet-update', [SettingController::class, 'thirdPartyKeyLightnetUpdate'])->name('third-party-key.lightnet-update');
    Route::get('/third-party-key/lightnet-view', [SettingController::class, 'thirdPartyKeyLightnetView'])->name('third-party-key.lightnet-view'); 
    Route::get('/third-party-key/sync-catalogue', [SettingController::class, 'thirdPartyKeySyncCatalogue'])->name('third-party-key.sync-catalogue');
    Route::get('/third-party-key/sync-countries', [SettingController::class, 'thirdPartyKeySyncCountries'])->name('third-party-key.sync-countries');
    Route::post('/third-party-key/lightnet-country-update', [SettingController::class, 'thirdPartyKeyCountryUpdate'])->name('third-party-key.lightnet-country-update');
	
	Route::get('/third-party-key/onafric-mobile-view', [SettingController::class, 'thirdPartyKeyOnafricMobileView'])->name('third-party-key.onafric-mobile-view');
	Route::get('/third-party-key/onafric-collection-view', [SettingController::class, 'thirdPartyKeyOnafricCollectionView'])->name('third-party-key.onafric-collection-view');
	Route::post('/third-party-key/onafric-mobile-update', [SettingController::class, 'thirdPartyKeyOnafricMobileUpdate'])->name('third-party-key.onafric-mobile-update')->withoutMiddleware('webdecrypt.request');
	
	Route::post('/third-party-key/onafric-bank-transfer-update', [SettingController::class, 'thirdPartyKeyOnafricBankTransferUpdate'])->name('third-party-key.onafric-bank-transfer-update')->withoutMiddleware('webdecrypt.request');
	
	Route::post('/third-party-key/onafric-collection-update', [SettingController::class, 'thirdPartyKeyOnafricCollectionUpdate'])->name('third-party-key.onafric-collection-update')->withoutMiddleware('webdecrypt.request');
	
	Route::post('third-party-key/onafric-mobile-webhook', [SettingController::class, 'thirdPartyKeyOnafricBankLists'])->name('third-party-key.onafric-mobile-webhook')->withoutMiddleware('webdecrypt.request');
    Route::post('third-party-key/onafric-bank-list', [SettingController::class, 'thirdPartyKeyOnafricBankLists'])->name('third-party-key.onafric-bank-list')->withoutMiddleware('webdecrypt.request');

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
        ->name('companies.active')->middleware('permission:active_company.view');
    Route::get('/companies/pending', [CompaniesController::class, 'companiesPending'])
        ->name('companies.pending')->middleware('permission:pending_company.view');
    Route::get('/companies/block', [CompaniesController::class, 'companiesBlock'])
        ->name('companies.block')->middleware('permission:block_company.view');

    Route::post('/companies/ajax', [CompaniesController::class, 'companiesAjax'])->withoutMiddleware('webdecrypt.request')->name('companies.ajax');
    Route::get('/companies/edit/{id}', [CompaniesController::class, 'companiesEdit'])->name('companies.edit');
    Route::post('/companies/update/{id}', [CompaniesController::class, 'companiesUpdate'])->name('companies.update');
    Route::get('/companies/increment-balance/{id}', [CompaniesController::class, 'companiesIncrementBalance'])->name('companies.increment-balance');
    Route::post('/companies/store-increment-balance', [CompaniesController::class, 'storeIncrementBalance'])->name('companies.store-increment-balance');
    Route::get('/companies/decrement-balance/{id}', [CompaniesController::class, 'companiesDecrementBalance'])->name('companies.decrement-balance');
    Route::post('/companies/store-decrement-balance', [CompaniesController::class, 'storeDecrementBalance'])->name('companies.store-decrement-balance');
    Route::post('/companies/update-status', [CompaniesController::class, 'companiesUpdateStatus'])->name('companies.update-status');
    Route::get('/companies/login-history/{id}', [CompaniesController::class, 'companiesloginHistory'])->name('companies.login-history');
    Route::post('/companies/login-history-ajax', [CompaniesController::class, 'companiesloginHistoryAjax'])->withoutMiddleware('webdecrypt.request')->name('companies.login-history-ajax');

    Route::get('/companies/view-kyc/{id}', [CompaniesController::class, 'companiesViewKyc'])->name('companies.view-kyc');
    Route::post('/companies/kyc-update', [CompaniesController::class, 'companiesKycUpdate'])->name('companies.kyc-update')->withoutMiddleware('webdecrypt.request');

    Route::post('/transaction-ajax', [CompaniesController::class, 'transactionAjax'])->name('transaction-ajax')->withoutMiddleware('webdecrypt.request');
    Route::get('/transaction-receipt/{id}', [CompaniesController::class, 'transactionReceipt'])->name('transaction.receipt');
    Route::get('/transaction-receipt-pdf/{id}', [CompaniesController::class, 'transactionReceiptPdf'])->name('transaction.receipt-pdf');
    Route::get('/commit-transaction/{id}', [CompaniesController::class, 'transferToBankCommitTransaction'])->name('transaction.commit-transaction');

    //Manage Users
    Route::get('/user/active', [UserController::class, 'userActive'])->name('user.active')->middleware('permission:active_user.view');
    Route::get('/user/pending', [UserController::class, 'userPending'])->name('user.pending')->middleware('permission:pending_user.view');
    Route::get('/user/block', [UserController::class, 'userBlock'])->name('user.block')->middleware('permission:block_user.view');
    Route::post('/user/ajax', [UserController::class, 'userAjax'])->withoutMiddleware('webdecrypt.request')->name('user.ajax');
    Route::get('/user/edit/{id}', [UserController::class, 'userEdit'])->name('user.edit');
    Route::post('/user/update/{id}', [UserController::class, 'userUpdate'])->name('user.update');
    Route::post('/user/update-status', [UserController::class, 'userUpdateStatus'])->name('user.update-status');
    Route::get('/user/view-kyc/{id}', [UserController::class, 'userViewKyc'])->name('user.view-kyc');
    Route::get('/user/login-history/{id}', [UserController::class, 'userLoginHistory'])->name('user.login-history');
 
    //Manual Exchange Rate
    Route::get('/manual-exchange-rate', [ExchangeRateController::class, 'manualExchangeRate'])->name('manual.exchange-rate')->middleware('permission:manual_exchange_rate.view');
    Route::post('/manual-exchange-rate/ajax', [ExchangeRateController::class, 'manualExchangeRateAjax'])->withoutMiddleware('webdecrypt.request')->name('manual.exchange-rate.ajax');
    Route::get('/manual-exchange-rate/import', [ExchangeRateController::class, 'manualExchangeRateImport'])->name('manual.exchange-rate.import');
    Route::post('/manual-exchange-rate/store', [ExchangeRateController::class, 'manualExchangeRateStore'])->name('manual.exchange-rate.store');
	Route::get('/manual-exchange-rate/edit/{id}', [ExchangeRateController::class, 'manualExchangeRateEdit'])->name('manual.exchange-rate.edit');
	Route::post('/manual-exchange-rate/update/{id}', [ExchangeRateController::class, 'manualExchangeRateUpdate'])->name('manual.exchange-rate.update');
    Route::post('/manual-exchange-rate/delete/{id}', [ExchangeRateController::class, 'manualExchangeRateDelete'])->withoutMiddleware('webdecrypt.request')->name('manual.exchange-rate.delete');
	
    //Live Exchange Rate
    Route::get('/live-exchange-rate', [ExchangeRateController::class, 'liveExchangeRate'])->name('live.exchange-rate')->middleware('permission:live_exchange_rate.view');
    Route::post('/live-exchange-rate/ajax', [ExchangeRateController::class, 'liveExchangeRateAjax'])->withoutMiddleware('webdecrypt.request')->name('live.exchange-rate.ajax'); 
    Route::post('/live-exchange-rate/fetch', [ExchangeRateController::class, 'liveExchangeRateFetch'])->name('live.exchange-rate.fetch'); 
	Route::get('/live-exchange-rate/edit/{id}', [ExchangeRateController::class, 'liveExchangeRateEdit'])->name('live.exchange-rate.edit');
	Route::post('/live-exchange-rate/update/{id}', [ExchangeRateController::class, 'liveExchangeRateUpdate'])->name('live.exchange-rate.update');
	Route::post('/live-exchange-rate/bulk-update', [ExchangeRateController::class, 'liveExchangeRateBulkUpdate'])->name('live.exchange-rate.bulk-update');
	  
    // Reports
    Route::prefix('reports')->as('report.')->group(function () 
	{
        Route::get('/transaction-history', [ReportController::class, 'transactionHistory'])->name('transaction-history')
		->middleware('permission:transaction_history.view');
        Route::post('/transaction-history-ajax', [ReportController::class, 'transactionReportAjax'])
		->name('transaction-history-ajax')->withoutMiddleware('webdecrypt.request'); 
		
		//Admin/User Log History
		Route::get('/admin-log-history', [ReportController::class, 'adminLogHistory'])->name('admin-log-history')
		->middleware('permission:admin_log_history.view');
		Route::get('/user-log-history', [ReportController::class, 'userLogHistory'])->name('user-log-history')
		->middleware('permission:user_log_history.view'); 
		Route::get('/log-view/{id}', [ReportController::class, 'adminUserLogView'])->name('log-view'); 
        Route::post('/log-history-ajax', [ReportController::class, 'adminUserLogAjax'])
		->name('log-history-ajax')->withoutMiddleware('webdecrypt.request'); 
    }); 
	
    // Reports
    Route::prefix('transaction')->as('transaction.')->group(function () 
	{
        Route::get('/mobile-money-onafric', [TransactionController::class, 'mobileMoneyOnafric'])->name('mobile-money-onafric')
		->middleware('permission:transaction_mobile_money_onafric.view');
		
		Route::get('/bank-onafric', [TransactionController::class, 'bankOnafric'])->name('bank-onafric')
		->middleware('permission:transaction_bank_onafric.view');
		
		Route::get('/bank-lightnet', [TransactionController::class, 'bankLightnet'])->name('bank-lightnet')
		->middleware('permission:transaction_bank_lightnet.view');
		
        Route::post('/ajax', [TransactionController::class, 'transactionAjax'])
		->name('ajax')->withoutMiddleware('webdecrypt.request'); 
		
		Route::post('/refund', [TransactionController::class, 'transactionRefund'])
		->name('refund')->withoutMiddleware('webdecrypt.request'); 
    }); 
});
