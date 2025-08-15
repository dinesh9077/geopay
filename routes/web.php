<?php
	
	use Illuminate\Support\Facades\Route;
	use App\Http\Controllers\Auth\LoginController;
	use App\Http\Controllers\Auth\RegisterController;
	use App\Http\Controllers\Auth\ResetPasswordController;
	use App\Http\Controllers\User\{
		HomeController, SettingController, TransactionController, KycController, 
		TransferBankController, AirtimeController, TransferMobileController, ReceiveMoneyController, ApiCredentialController
	};
	use App\Http\Controllers\FrontController;
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
	
	//Route::get('/', [FrontController::class, 'index']);
 
	Route::get('/', function () {
		return redirect()->route('login');
	});
 
	Route::get('/terms-and-condition', [FrontController::class, 'termAndCondition'])->name('terms-and-condition');
	
	//Auth::routes(); 
	Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
	Route::post('/login', [LoginController::class, 'login'])->name('login.submit')->middleware('webdecrypt.request'); 
	Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
	
	Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
	Route::post('individual/register', [RegisterController::class, 'individualRegister'])->name('register.individual')->middleware('webdecrypt.request');
	Route::post('temp-individual/register', [RegisterController::class, 'individualTempRegister'])->name('register.temp-individual')->middleware('webdecrypt.request');
	Route::post('company/register', [RegisterController::class, 'companyRegister'])->name('register.company')->middleware('webdecrypt.request');
	Route::post('temp-company/register', [RegisterController::class, 'companyTempRegister'])->name('register.temp-company')->middleware('webdecrypt.request');
	
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
	 
	Route::match(['get', 'post'], '/onafric/webhook/{webhookIds}', [TransferMobileController::class, 'transferToMobileWebhook']);
	Route::match(['get', 'post'], '/mobile-collection-callback', [ReceiveMoneyController::class, 'storeMobileCollectionCallback'])->name('mobile-collection.callback');
	
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
		Route::get('/api-documentation', [HomeController::class, 'apiDocumentation'])->name('api-documentation');
		
		// Notification
		Route::get('/notifications', [HomeController::class, 'notifications'])->name('notifications');   
		
		// Wallet To Wallet
		Route::get('/wallet-to-wallet', [TransactionController::class, 'walletToWallet'])->name('wallet-to-wallet');  
		Route::post('/wallet-to-wallet/store', [TransactionController::class, 'walletToWalletStore'])->name('wallet-to-wallet.store');  
		
		// International Airtime
		Route::get('/international-airtime', [AirtimeController::class, 'internationalAirtime'])->name('international-airtime');  
		Route::post('/international-airtime/operator', [AirtimeController::class, 'internationalAirtimeOperator'])->name('international-airtime.operator');  
		Route::post('/international-airtime/product', [AirtimeController::class, 'internationalAirtimeProduct'])->name('international-airtime.product');  
		Route::post('/international-airtime/validate-phone', [AirtimeController::class, 'internationalAirtimeValidatePhone'])->name('international-airtime.validate-phone');  
		Route::post('/international-airtime/store', [AirtimeController::class, 'internationalAirtimeStore'])->name('international-airtime.store');  
		Route::post('/international-airtime/callback/{txnId}', [AirtimeController::class, 'internationalAirtimeCallback'])->withoutMiddleware('webdecrypt.request');
		
		// Transfer To Bank 
		Route::get('/transfer-to-bank', [TransferBankController::class, 'transferToBank'])->name('transfer-to-bank'); 
		Route::post('/transfer-to-bank/store', [TransferBankController::class, 'transferToBankStore'])->name('transfer-to-bank.store');  
		
		Route::get('/transfer-to-bank/beneficiary', [TransferBankController::class, 'transferToBankBeneficiary'])->name('transfer-to-bank.beneficiary');  
		Route::post('/transfer-to-bank/beneficiary-store', [TransferBankController::class, 'transferToBankBeneficiaryStore'])->name('transfer-to-bank.beneficiary-store');  
		Route::post('/transfer-to-bank/bank-list', [TransferBankController::class, 'transferToBankList'])->name('transfer-to-bank.bank-list');  
		Route::post('/transfer-to-bank/get-fields', [TransferBankController::class, 'transferToBankFields'])->name('transfer-to-bank.get-fields');  
		
		Route::post('/transfer-to-bank/beneficiary-list', [TransferBankController::class, 'transferToBeneficiaryList'])->name('transfer-to-bank.beneficiary-list');  
		Route::post('/transfer-to-bank/beneficiary-detail', [TransferBankController::class, 'transferToBeneficiaryDetail'])->name('transfer-to-bank.beneficiary-detail');  
		Route::get('/transfer-to-bank/beneficiary-edit/{id}', [TransferBankController::class, 'transferToBankBeneficiaryEdit']);  
		Route::post('/transfer-to-bank/beneficiary-update/{id}', [TransferBankController::class, 'transferToBankBeneficiaryUpdate'])->name('transfer-to-bank.beneficiary-update');  
		Route::get('/transfer-to-bank/beneficiary-delete/{id}', [TransferBankController::class, 'transferToBeneficiaryDelete'])->name('transfer-to-bank.beneficiary-delete');  
		Route::post('/transfer-to-bank/commission', [TransferBankController::class, 'transferToBankCommission'])->name('transfer-to-bank.commission');  
		Route::get('/transfer-to-bank/commit-transaction/{id}', [TransferBankController::class, 'transferToBankCommitTransaction'])->name('transfer-to-bank.commit-transaction');
		
		// Transfer To Mobile Money
		Route::get('/transfer-to-mobile-money', [TransferMobileController::class, 'transferToMobileMoney'])->name('transfer-to-mobile-money'); 
		Route::post('/transfer-to-mobile/store', [TransferMobileController::class, 'transferToMobileStore'])->name('transfer-to-mobile.store');  
		Route::get('/transfer-to-mobile/beneficiary', [TransferMobileController::class, 'transferToMobileBeneficiary'])->name('transfer-to-mobile.beneficiary');  
		Route::post('/transfer-to-mobile/beneficiary-store', [TransferMobileController::class, 'transferToMobileBeneficiaryStore'])->name('transfer-to-mobile.beneficiary-store');
		Route::post('/transfer-to-mobile/beneficiary-list', [TransferMobileController::class, 'transferToBeneficiaryList'])->name('transfer-to-mobile.beneficiary-list'); 
		Route::post('/transfer-to-mobile/beneficiary-detail', [TransferMobileController::class, 'transferToBeneficiaryDetail'])->name('transfer-to-mobile.beneficiary-detail'); 
		Route::get('/transfer-to-mobile/beneficiary-edit/{id}', [TransferMobileController::class, 'transferToMobileBeneficiaryEdit']);  
		Route::post('/transfer-to-mobile/beneficiary-update/{id}', [TransferMobileController::class, 'transferToMobileBeneficiaryUpdate'])->name('transfer-to-mobile.beneficiary-update');  
		Route::get('/transfer-to-mobile/beneficiary-delete/{id}', [TransferMobileController::class, 'transferToBeneficiaryDelete'])->name('transfer-to-mobile.beneficiary-delete');  
		Route::post('/transfer-to-mobile/commission', [TransferMobileController::class, 'transferToMobileCommission'])->name('transfer-to-mobile.commission');
		 
		//Add Mobile Money  
		Route::get('/add-money', [ReceiveMoneyController::class, 'addMoney'])->name('add-money'); 
		Route::post('/mobile-collection-store', [ReceiveMoneyController::class, 'storeMobileCollection'])->name('mobile-collection.store');Route::post('/mobile-collection-commission', [ReceiveMoneyController::class, 'storeMobileCollectionCommission'])->name('mobile-collection.commission'); 
		 
		//Transaction List 
		Route::get('/transaction-list', [TransactionController::class, 'index'])->name('transaction-list');
		Route::post('/transaction-ajax', [TransactionController::class, 'transactionAjax'])->name('transaction-ajax')->withoutMiddleware('webdecrypt.request');
		Route::get('/transaction-receipt/{id}', [TransactionController::class, 'transactionReceipt'])->name('transaction.receipt');
		Route::get('/transaction-receipt-pdf/{id}', [TransactionController::class, 'transactionReceiptPdf'])->name('transaction.receipt-pdf');
		
		//Setting
		Route::get('/setting', [SettingController::class, 'index'])->name('setting');  
		Route::post('/password-change', [SettingController::class, 'changePassword'])->name('password-change');  
		Route::post('/profile-update', [SettingController::class, 'profileUpdate'])->name('profile-update');  
		Route::post('/basic-info-update', [SettingController::class, 'basicInfoUpdate'])->name('basic-info-update'); 
		
		Route::get('/api-credentials', [ApiCredentialController::class, 'index'])->name('api.credentials.index');
		Route::post('/api-credentials', [ApiCredentialController::class, 'store'])->name('api.credentials.store')->withoutMiddleware('webdecrypt.request');
	}); 