<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\{
    RegisterController, LoginController, 
};
use App\Http\Controllers\Api\{
   SettingController, UserKycController, TransactionControllerAirtimeController,
   ReceiveMoneyController, AirtimeController
};

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware(['decrypt.request'])->group(function ()
{
    // Register Routes
    Route::post('individual-register', [RegisterController::class, 'individualRegister']); 
    Route::post('company-register', [RegisterController::class, 'companyRegister']);
	
	// Email Verification Routes
    Route::prefix('email')->group(function () {
        Route::post('/send', [RegisterController::class, 'sendEmailOtp']);
        Route::post('/resend', [RegisterController::class, 'resendEmailOtp']);
        Route::post('/verify-otp', [RegisterController::class, 'verifyEmailOtp']);
    });
	
	// Mobile Verification Routes
    Route::prefix('mobile')->group(function () {
        Route::post('/send', [RegisterController::class, 'sendMobileOtp']);
        Route::post('/resend', [RegisterController::class, 'resendMobileOtp']);
        Route::post('/verify-otp', [RegisterController::class, 'verifyMobileOtp']);
    });
	
    Route::post('login', [LoginController::class, 'login']);  
    
    // Password Reset Routes
    Route::post('forgot-password', [LoginController::class, 'forgotPassword']);
    Route::post('forgot-resend-otp', [LoginController::class, 'forgotResendOtp']);
    Route::post('verify-email-otp', [LoginController::class, 'verifyEmailOtp']);
    Route::post('reset-password', [LoginController::class, 'resetPassword']);
      
    // User Metamap Webhook
    Route::post('/metamap-webhook', [UserKycController::class, 'metamapWebhook'])->withoutMiddleware('decrypt.request');
});

Route::get('country-list', [SettingController::class, 'countryList']);   

// Authenticated Routes
Route::middleware(['auth:api', 'ensure.token'])->group(function () 
{
    Route::post('logout', [LoginController::class, 'logout']); 
	Route::post('user-details', [LoginController::class, 'userDetails']); 
	
	Route::middleware(['decrypt.request'])->group(function () 
	{ 
		Route::post('user-profile-update', [SettingController::class, 'userProfileUpdate']);   
		Route::post('user-reset-password', [SettingController::class, 'userResetPassword']);  
		
		Route::get('common-details', [SettingController::class, 'commonDetails']);   
		
		//Wallet To Wallet 
		Route::post('wallet-to-wallet', [TransactionController::class, 'walletToWalletStore']); 
		
		//Add Money
		Route::prefix('collection')->group(function () 
		{ 
			Route::get('country-list', [ReceiveMoneyController::class, 'collectionCountryList']);
			Route::post('commission', [ReceiveMoneyController::class, 'collectionCommission']);
			Route::post('store', [ReceiveMoneyController::class, 'storeTransaction']);
		});
		
		//Internation Airtime 
		Route::prefix('international-airtime')->group(function () 
		{  
			Route::post('operator', [AirtimeController::class, 'operator']); 
			Route::post('products', [AirtimeController::class, 'products']); 
			Route::post('mobile-validate', [AirtimeController::class, 'mobileValidate']); 
			Route::post('store-transaction', [AirtimeController::class, 'storeTransaction']); 
		});
    });
});
