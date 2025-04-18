<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\{
    RegisterController, LoginController, 
};
use App\Http\Controllers\Api\{
   SettingController, UserKycController, TransactionController, AirtimeController,
   ReceiveMoneyController, TransferBankController, TransferMobileController
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
	Route::get('notification-list', [SettingController::class, 'notificationList']);
	 
	Route::middleware(['decrypt.request'])->group(function () 
	{ 
		//Company Kyc
		Route::get('/company-kyc-details', [UserKycController::class, 'companyKycDetails']);
		Route::post('/company-kyc/step/{number}', [UserKycController::class, 'companyKycStepStore']);
	
		Route::post('user-profile-update', [SettingController::class, 'userProfileUpdate']);   
		Route::post('user-reset-password', [SettingController::class, 'userResetPassword']);   
		Route::get('common-details', [SettingController::class, 'commonDetails']);   
		
		//Transaction list
		Route::post('transaction-list', [TransactionController::class, 'transactionList']); 
		
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
		
		//Transfer Bank
		Route::prefix('transfer-bank')->group(function () 
		{  
			Route::get('country-list', [TransferBankController::class, 'countryList']);  
			Route::post('beneficiary-list', [TransferBankController::class, 'beneficiaryList']);  
			Route::post('beneficiary-delete/{id}', [TransferBankController::class, 'beneficiaryDelete'])->withoutMiddleware('decrypt.request'); 
			Route::post('beneficiary-store', [TransferBankController::class, 'beneficiaryStore']); 
			Route::post('beneficiary-update/{id}', [TransferBankController::class, 'beneficiaryUpdate']);  
			Route::post('commission', [TransferBankController::class, 'commission']);  
			Route::post('store-transaction', [TransferBankController::class, 'storeTransaction']);  
			Route::post('bank-list', [TransferBankController::class, 'bankList']);  
			Route::post('get-fields-by-bank', [TransferBankController::class, 'getFieldByBank']);  
		});
		
		//Transfer Money
		Route::prefix('transfer-mobile-money')->group(function () 
		{  
			Route::get('country-list', [TransferMobileController::class, 'countryList']);  
			Route::post('beneficiary-list', [TransferMobileController::class, 'beneficiaryList']);  
			Route::post('beneficiary-delete/{id}', [TransferMobileController::class, 'beneficiaryDelete'])->withoutMiddleware('decrypt.request'); 
			Route::post('fields-view', [TransferMobileController::class, 'getOnafricFieldView'])->withoutMiddleware('decrypt.request');  
			Route::post('beneficiary-store', [TransferMobileController::class, 'beneficiaryStore']);  
			Route::post('beneficiary-update/{id}', [TransferMobileController::class, 'beneficiaryUpdate']);  
			Route::post('commission', [TransferMobileController::class, 'commission']);  
			Route::post('store-transaction', [TransferMobileController::class, 'storeTransaction']);     
		}); 
	});
});
