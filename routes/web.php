<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\HomeController;
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
//\Artisan::call('migrate');

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

Route::middleware(['webdecrypt.request'])->group(function ()
{  
	// Reset Password
	Route::get('/password/reset', [ResetPasswordController::class, 'showOtpRequestForm'])->name('password.request');  
	Route::post('/password/send-otp', [ResetPasswordController::class, 'sendOtp'])->name('password.sendOtp');  
	Route::post('/password/resend-otp', [ResetPasswordController::class, 'resendOtp'])->name('password.resendOtp');
	Route::post('/password/verify-otp', [ResetPasswordController::class, 'verifyEmailOtp'])->name('password.verifyOtp');  
	Route::post('/password/reset', [ResetPasswordController::class, 'resetPassword'])->name('password.reset'); 
	 
	// Meta Kyc
	Route::get('/metamap/kyc', [KycController::class, 'metaMapKyc'])->name('metamap.kyc');
	Route::get('/metamap/kyc-check-status', [KycController::class, 'metaMapKycStatus'])->name('metamap.kyc-check-status');
	Route::post('/metamap/kyc-finished', [KycController::class, 'metaMapKycFinished'])->name('metamap.kyc-finished');
	
	// Company/Corporate Kyc
	Route::get('/corporate/kyc', [KycController::class, 'corporateKyc'])->name('corporate.kyc'); 
	Route::post('corporate/kyc/step/{step}', [KycController::class, 'corporateKycStep'])->name('corporate.kyc.submit-step');
	Route::post('corporate/kyc/final', [KycController::class, 'corporateKycFinal'])->name('corporate.kyc.submit-final');

	Route::get('/home', [HomeController::class, 'index'])->name('home');
	
});

