<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiProvider\{
   TokenController, TransferBankController, ExchangeRateController, TransferMobileController, WebhookController
};
 
Route::post('/api-service/auth/token', [TokenController::class, 'issue']);
Route::middleware(['client.bearer', 'ip.whitelist'])->prefix('api-service')->group(function () 
{ 
	Route::post('/auth/token/revoke', [TokenController::class, 'revoke']); 
	Route::get('/profile', fn() => auth()->user());
	
	Route::post('exchange-rate', [ExchangeRateController::class, 'exchangeRateList']);
	Route::post('get-transaction-status', [ExchangeRateController::class, 'getTransactionStatus']);
	
	// Transfer Bank
	Route::middleware(['check.service'])->prefix('transfer-bank')->group(function () 
	{  
		Route::get('country-list', [TransferBankController::class, 'countryList']);   
		Route::post('bank-list', [TransferBankController::class, 'bankList']);  
		Route::post('get-fields', [TransferBankController::class, 'getFields']);   
		Route::post('create-trasaction', [TransferBankController::class, 'createTransaction']);  
	}); 
	
	//Transfer Money
	Route::middleware(['check.service'])->prefix('transfer-money')->group(function () 
	{  
		Route::get('country-list', [TransferMobileController::class, 'countryList']);   
		Route::get('get-fields', [TransferMobileController::class, 'getFields']);  
		Route::post('create-transaction', [TransferMobileController::class, 'createTransaction']);     
	});  
	
	//Webhook Register
	Route::prefix('webhook')->group(function () 
	{   
		Route::post('register', [WebhookController::class, 'webhookRegister']);     
		Route::post('delete', [WebhookController::class, 'webhookDelete']);     
	});  
});