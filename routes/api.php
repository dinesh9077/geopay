<?php
	
	use Illuminate\Http\Request;
	use Illuminate\Support\Facades\Route;
	use App\Http\Controllers\Api\Auth\RegisterController;
	use App\Http\Controllers\Api\Auth\LoginController;
	use App\Http\Controllers\Api\CountryController;
	use App\Http\Controllers\Api\UserController;
	use App\Http\Controllers\Api\TransactionLimitController;
	use App\Http\Controllers\Api\TransactionController;
	use App\Http\Controllers\Api\BannerController;
	use App\Http\Controllers\Api\UserKycController;
	
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
		Route::post('login', [LoginController::class, 'login']);  
		
		Route::post('forgot-password', [LoginController::class, 'forgotPassword']);
		Route::post('forgot-resend-otp', [LoginController::class, 'forgotResendOtp']);
		Route::post('verify-email-otp', [LoginController::class, 'verifyEmailOtp']);
		Route::post('reset-password', [LoginController::class, 'resetPassword']);
		
		Route::post('register', [RegisterController::class, 'register']);
		
		Route::post('email/send', [RegisterController::class, 'sendEmailOtp']);
		Route::post('/email/resend', [RegisterController::class, 'resendEmailOtp']);
		Route::post('/verify/email-otp', [RegisterController::class, 'verifyEmailOtp']);
		 
		Route::post('/mobile/send', [RegisterController::class, 'sendMobileOtp']);
		Route::post('/mobile/resend', [RegisterController::class, 'resendMobileOtp']);
		Route::post('/verify/otp', [RegisterController::class, 'verifyMobileOtp']);
		  
		Route::post('/user-kyc/verify', [UserKycController::class, 'verify'])->withoutMiddleware('webdecrypt.request');
		Route::post('/user-kyc-cron', [UserKYCController::class, 'getKYCVerification'])->withoutMiddleware('webdecrypt.request');
	});
	
	Route::post('logout', [LoginController::class, 'logout'])->middleware(['auth:api', 'ensure.token']);
	
	Route::get('countries', [CountryController::class, 'index']); 
	Route::post('liquidnet', [CountryController::class, 'liquidnet']); 
	
	Route::get('user-roles', [UserController::class, 'userRoles']); 
	 
	
	Route::middleware(['auth:api', 'ensure.token', 'decrypt.request'])->group(function () 
	{ 
		Route::post('user-details', [LoginController::class, 'userDetails']);
		Route::post('user-profile', [RegisterController::class, 'updateProfile']);
		
		// ---------------------------
		// Transaction Limit Module
		// ---------------------------
		Route::get('transaction-limits', [TransactionLimitController::class, 'index']);
		Route::post('update-transaction-limits', [TransactionLimitController::class, 'updateTransactionLimits']);
		
		// ---------------------------
		// Transaction Module
		// ---------------------------
		Route::post('transactions', [TransactionController::class, 'store']);
		Route::post('verify-add-money', [TransactionController::class, 'verifyLimits']);
		Route::post('verify-wallet-lmits', [TransactionController::class, 'verifyWalletLimits']);
		Route::post('benificiary-add', [TransactionController::class, 'benificaryAdd']);
		Route::get('get-benificiary-list', [TransactionController::class, 'getBenificiaryList']);
		Route::post('user-transaction', [TransactionController::class, 'userTransaction']);
		Route::get('pending-transaction', [TransactionController::class, 'pendingTransaction']);
		Route::post('search-transaction/{search}', [TransactionController::class, 'searchTransactionList']);
		Route::post('wallet-transactions', [TransactionController::class, 'walletTransaction']);
		Route::get('get-user-transaction', [TransactionController::class, 'getuserTransaction']);
		
		// ---------------------------
		// Banner Module
		// ---------------------------
		Route::post('banner-add', [BannerController::class, 'create']);
		Route::get('get-banner-list', [BannerController::class, 'getBannerData']);
		
		// ---------------------------
		// User Module
		// ---------------------------
		Route::get('user-transaction-limit', [UserController::class, 'userData']);
		Route::get('company-details-list', [UserController::class, 'companyList'])->middleware('checkAdmin');
		Route::get('user-list', [UserController::class, 'userList'])->middleware('checkAdmin');
		Route::post('update', [UserController::class, 'update']);
		Route::post('user-status', [UserController::class, 'userStatus']);
		Route::post('login-logs', [UserController::class, 'getLoginLogs']);
		Route::get('test', [UserController::class, 'test']);
	});
	
	
