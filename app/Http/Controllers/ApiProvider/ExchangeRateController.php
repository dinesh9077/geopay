<?php
	namespace App\Http\Controllers\ApiProvider;
	
	use App\Http\Controllers\Controller;
	use Illuminate\Http\Request;
	use Illuminate\Support\Facades\{
		DB, Auth, Log, Validator
	};
	use App\Models\{
		Country, User, LiveExchangeRate, ExchangeRate
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
			
			$serviceName = $request->service == 1 ? ['lightnet'] : ['onafric mobile collection', 'onafric'];
			$liveExchangeRate = LiveExchangeRate::query()
			->whereIn('channel', $serviceName)
			->where('currency', $request->payoutCurrency)
			->first(); 
			
			if(!$liveExchangeRate)
			{ 
				$liveExchangeRate = ExchangeRate::select('exchange_rate as markdown_rate', 'aggregator_rate')
				->where('type', 2)
				->whereIn('service_name', $serviceName)
				->where('currency', $request->payoutCurrency)
				->first();
				
				if (!$liveExchangeRate) {
					return $this->errorResponse('A technical issue has occurred. Please try again later.'); 
				}
			} 
			
			return $this->successResponse('Rate fetched Successfully.', 
				$liveExchangeRate
			);
		}    
	}
