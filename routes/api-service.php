<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiProvider\{
   TokenController, TransferBankController, ExchangeRateController
};
 
Route::post('/api-service/auth/token', [TokenController::class, 'issue']);
Route::middleware(['client.bearer'])->prefix('api-service')->group(function () 
{ 
	Route::post('/auth/token/revoke', [TokenController::class, 'revoke']); 
	Route::get('/profile', fn() => auth()->user());
	
	Route::post('exchange-rate', [ExchangeRateController::class, 'exchangeRateList']);
	
	// Transfer Bank
	Route::prefix('transfer-bank')->group(function () 
	{  
		Route::get('country-list', [TransferBankController::class, 'countryList']);   
		Route::post('bank-list', [TransferBankController::class, 'bankList']);  
		Route::post('get-fields', [TransferBankController::class, 'getFields']);   
		Route::post('store-transaction', [TransferBankController::class, 'storeTransaction']);  
	}); 
});