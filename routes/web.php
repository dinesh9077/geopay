<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\User\HomeController;
use App\Http\Controllers\User\SettingController;
use App\Http\Controllers\User\TransactionController;
use App\Http\Controllers\User\KycController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
//\Artisan::call('storage:link');

Route::get('/', function () {
    return view('welcome');
});

//Auth::routes();

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.submit')->middleware('webdecrypt.request'); 
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
 
Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('individual/register', [RegisterController::class, 'individualRegister'])->name('register.individual')->middleware('webdecrypt.request');
Route::post('company/register', [RegisterController::class, 'companyRegister'])->name('register.company')->middleware('webdecrypt.request');

Route::post('email/send', [RegisterController::class, 'sendEmailOtp'])->name('email.send')->middleware('webdecrypt.request');
Route::post('/email/resend', [RegisterController::class, 'resendEmailOtp'])->name('email.resend')->middleware('webdecrypt.request');
Route::post('/verify/email-otp', [RegisterController::class, 'verifyEmailOtp'])->name('verify.email-otp')->middleware('webdecrypt.request');
 
Route::post('/mobile/send', [RegisterController::class, 'sendMobileOtp'])->name('mobile.send')->middleware('webdecrypt.request');
Route::post('/mobile/resend', [RegisterController::class, 'resendMobileOtp'])->name('mobile.resend')->middleware('webdecrypt.request');
Route::post('/verify/mobile-otp', [RegisterController::class, 'verifyMobileOtp'])->name('verify.mobile-otp')->middleware('webdecrypt.request');

// Reset Password
Route::get('/password/reset', [ResetPasswordController::class, 'showOtpRequestForm'])->name('password.request');  
Route::post('/password/send-otp', [ResetPasswordController::class, 'sendOtp'])->name('password.sendOtp')->middleware('webdecrypt.request');  
Route::post('/password/resend-otp', [ResetPasswordController::class, 'resendOtp'])->name('password.resendOtp')->middleware('webdecrypt.request');
Route::post('/password/verify-otp', [ResetPasswordController::class, 'verifyEmailOtp'])->name('password.verifyOtp')->middleware('webdecrypt.request');
Route::post('/password/reset', [ResetPasswordController::class, 'resetPassword'])->name('password.reset')->middleware('webdecrypt.request');

Route::middleware(['webdecrypt.request', 'kycStatus'])->group(function ()
{     
	// Meta Kyc
	Route::get('/metamap/kyc', [KycController::class, 'metaMapKyc'])->name('metamap.kyc');
	Route::get('/metamap/kyc-check-status', [KycController::class, 'metaMapKycStatus'])->name('metamap.kyc-check-status');
	Route::post('/metamap/kyc-finished', [KycController::class, 'metaMapKycFinished'])->name('metamap.kyc-finished');
	
	// Company/Corporate Kyc
	Route::get('/corporate/kyc', [KycController::class, 'corporateKyc'])->name('corporate.kyc'); 
	Route::get('/corporate/director/{companyDetailId}', [KycController::class, 'getRemainingDirector'])->name('corporate.kyc.director'); 
	Route::get('/corporate/document-type/{directorId}', [KycController::class, 'getRemainingDocuments'])->name('corporate.kyc.document-type'); 
	Route::post('corporate/kyc/step/{step}', [KycController::class, 'corporateKycStep'])->name('corporate.kyc.submit-step');
	Route::post('corporate/kyc/document-store', [KycController::class, 'corporateKycDocumntStore'])->name('corporate.kyc.document-store');
	Route::post('corporate/kyc/final', [KycController::class, 'corporateKycFinal'])->name('corporate.kyc.submit-final');
	 
	// Dashboard
	Route::get('/home', [HomeController::class, 'index'])->name('home');   
	
	Route::get('/notifications', [HomeController::class, 'notifications'])->name('notifications');   
	  
	Route::get('/wallet-to-wallet', [TransactionController::class, 'walletToWallet'])->name('wallet-to-wallet');  
	Route::post('/wallet-to-wallet/store', [TransactionController::class, 'walletToWalletStore'])->name('wallet-to-wallet.store');  
	 
	Route::get('/add-money', function () {
		return view('user.transaction.add-money.index');
	})->name('add-money');  
  
	Route::get('/transfer-to-mobile-money', function () {
		return view('user.transaction.transfer-to-mobile-money');
	})->name('transfer-to-mobile-money');
	
	Route::get('/international-airtime', function () {
		return view('user.transaction.international-airtime');
	})->name('international-airtime');
	
	Route::get('/transaction-list', function () {
		return view('user.transaction.transaction-list-page');
	})->name('transaction-list');
	
	Route::get('/transfer-to-bank', function () {
		return view('user.transaction.transfer-bank');
	})->name('transfer-to-bank');
	 
	//Setting
	Route::get('/setting', [SettingController::class, 'index'])->name('setting');  
	Route::post('/password-change', [SettingController::class, 'changePassword'])->name('password-change');  
	Route::post('/profile-update', [SettingController::class, 'profileUpdate'])->name('profile-update');  
});

