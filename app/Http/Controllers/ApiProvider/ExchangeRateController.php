<?php
	namespace App\Http\Controllers\ApiProvider;
	
	use App\Http\Controllers\Controller;
	use Illuminate\Http\Request;
	use Illuminate\Support\Facades\{
		DB, Auth, Log, Validator
	};
	use App\Models\{
		Country, User, LiveExchangeRate, ExchangeRate, Transaction
	}; 
	use App\Http\Traits\ApiServiceResponseTrait;
	
	class ExchangeRateController extends Controller
	{ 
		use ApiServiceResponseTrait;  
		  
		public function exchangeRateList(Request $request)
		{  
			$validator = Validator::make($request->all(), [ 
				'payoutCurrency' => 'required|string|size:3',
				'service'       => 'required|integer|in:1,2' 
			], [
				'payoutCurrency.required' => 'Payout currency is required.',
				'payoutCurrency.size'     => 'Payout currency must be exactly 3 characters.',
				'service.required'        => 'Service is required.',
				'service.in'              => 'Service must be either 1 or 2.'
			]);

			if ($validator->fails()) {
				return $this->validateResponse($validator->errors()->toArray());
			}
			
			$serviceName = $request->service == 1 ? ['lightnet'] : ['onafric'];
		
			$liveExchangeRate = LiveExchangeRate::select('id', 'currency', 'api_markdown_rate as rate')
			->whereIn('channel', $serviceName)
			->where('currency', $request->payoutCurrency)
			->first(); 
			
			if(!$liveExchangeRate)
			{ 
				$liveExchangeRate = ExchangeRate::select('id', 'currency', 'api_markdown_rate as rate')
				->where('type', 2)
				->whereIn('service_name', $serviceName)
				->where('currency', $request->payoutCurrency)
				->first();
				
				if (!$liveExchangeRate) {
					return $this->errorResponse('A technical issue has occurred. Please try again later.', 'ERR_RATE', 401); 
				}
			} 
			
			return $this->successResponse('Rate fetched Successfully.', 
				$liveExchangeRate
			);
		}    
		
		public function getTransactionStatus(Request $request) 
		{
			$validator = Validator::make($request->all(), [ 
				'thirdPartyId' => 'required|string' 
			]);

			if ($validator->fails()) {
				return $this->validateResponse($validator->errors()->toArray());
			}
			
			$trasaction = Transaction::where('order_id', $request->thirdPartyId)->orderByDesc('id')->first();
			
			if(!$trasaction)
			{
				return $this->errorResponse('transaction not found.', 'ERR_NOT_FOUND');
			}
			
			$data = [
				'thirdPartyId' => $trasaction->order_id,
				 "status" =>  [
					"status" =>  $trasaction->txn_status,
					"message" =>  $trasaction->api_status
				],
				'exchangeRate' => $trasaction->rates,
				'receiveAmount' => [
					'amount' => $trasaction->unit_amount,
					'currencyCode' => $trasaction->unit_convert_currency 
				],
				"txExecutedDate" =>  $trasaction->complete_transaction_at
			];
			
			return $this->successResponse('transaction details fetched Successfully.', 
				$data
			);
		}
	}
