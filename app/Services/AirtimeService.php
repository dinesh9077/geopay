<?php
	
	namespace App\Services;
	
	use Illuminate\Support\Facades\Http;
	use Illuminate\Support\Str; 
	use App\Http\Traits\ApiResponseTrait; 
	use App\Models\ExchangeRate;
	use Carbon\Carbon;
	use Illuminate\Support\Facades\{
		Log, Mail
	};
	
	class AirtimeService
	{
		use ApiResponseTrait; 
		
		protected $baseUrl;
		protected $apiKey;
		protected $secretKey;
		protected $serviceId;
		protected $subServiceId;

		public function __construct()
		{ 
			$this->baseUrl = config('setting.dtone_url');
			$this->apiKey = config('setting.dtone_apikey');
			$this->secretKey = config('setting.dtone_secretkey');
			$this->serviceId = config('setting.dtone_serviceid');
			$this->subServiceId = config('setting.dtone_subserviceid');
		} 
		
		public function getCountries($isWeb = false)
		{ 
			try {
				// Create Basic Authorization header
				$basicAuth = base64_encode("{$this->apiKey}:{$this->secretKey}");

				$headers = [
					'Authorization' => 'Basic ' . $basicAuth,
					'Content-Type' => 'application/json',
				];

				// Make the GET request
				$response = Http::withHeaders($headers)->get("{$this->baseUrl}/countries", [
					'service_id' => $this->serviceId,
					'subservice_id' => $this->subServiceId,
				    'per_page' => 100
				]);

				// Check for success and return data 
				if ($response->successful()) 
				{  
					return $response->json();
				}
				
				return [];
			} 
			catch (\Throwable $e) 
			{  
				return [];
			}
		}
		
		public function getOperators($countryCode, $isWeb = false)
		{
			try {
				// Basic Authorization Header
				$basicAuth = base64_encode("{$this->apiKey}:{$this->secretKey}");
				$headers = [
					'Authorization' => 'Basic ' . $basicAuth,
					'Content-Type' => 'application/json',
				];

				// API Request Parameters
				$queryParams = [
					'country_iso_code' => $countryCode,
					//'country_iso_code' => 'IND',
					'service_id' => $this->serviceId,
					'subservice_id' => $this->subServiceId,
				];

				// API Request
				$response = Http::withHeaders($headers)->get("{$this->baseUrl}/operators", $queryParams);

				// Handle Successful Response
				if ($response->successful()) {
					$responseType = $isWeb ? 'webSuccessResponse' : 'successResponse';
					return $this->{$responseType}('Operator fetched successfully.', $response->json());
				}

				// Handle API Errors
				$errorMessage = 'Operator not found.'; 
				$responseType = $isWeb ? 'webErrorResponse' : 'errorResponse';
				return $this->{$responseType}($errorMessage);
			} 
			catch (\Throwable $e) 
			{
				// Log Exception
				Log::error('Operator API failed with exception:', [
					'operator_code' => $countryCode,
					'exception' => $e->getMessage(),
				]);

				// Handle Exception
				$errorMessage = 'Operator API failed due to an exception.'. $e->getMessage();
				$responseType = $isWeb ? 'webErrorResponse' : 'errorResponse';
				return $this->{$responseType}($errorMessage);
			}
		}
		
		public function getValidatePhoneByOperator($mobileNumber, $operatorId, $isWeb = false)
		{
			try {
				// Basic Authorization Header
				$basicAuth = base64_encode("{$this->apiKey}:{$this->secretKey}");
				$headers = [
					'Authorization' => 'Basic ' . $basicAuth,
					'Content-Type' => 'application/json',
				];

				// API Request Parameters
				$postParams = [
					'mobile_number' => $mobileNumber,
				];

				// API Request
				$response = Http::withHeaders($headers)->post("{$this->baseUrl}/lookup/mobile-number", $postParams);

				// Handle Successful Response
				if ($response->successful()) {
					$operators = $response->json();
					
					// Check if the operator exists and is identified
					$matchedOperator = collect($operators)->firstWhere('id', $operatorId);
				 
					if ($matchedOperator && $matchedOperator['identified']) {
						$responseType = $isWeb ? 'webSuccessResponse' : 'successResponse';
						return $this->{$responseType}('Operator matched successfully.', $matchedOperator);
					}

					// Operator ID not found or not identified
					$errorMessage = 'The operator is not identified for this mobile number.';
					$responseType = $isWeb ? 'webErrorResponse' : 'errorResponse';
					return $this->{$responseType}($errorMessage);
				}
				
				$errors = json_decode($response->body(), true);  
				$responseType = $isWeb ? 'webErrorResponse' : 'errorResponse';
				return $this->{$responseType}($errors['errors'][0]['message']);
			} catch (\Throwable $e) {
				// Log Exception
				Log::error('Operator API failed with exception:', [
					'mobile_number' => $mobileNumber,
					'operator_id' => $operatorId,
					'exception' => $e->getMessage(),
				]);

				// Handle Exception
				$errorMessage = 'Operator API failed due to an exception: ' . $e->getMessage();
				$responseType = $isWeb ? 'webErrorResponse' : 'errorResponse';
				return $this->{$responseType}($errorMessage);
			}
		}

		
		public function getProducts($countryCode, $operatorId, $isWeb = false)
		{
			try {
				// Basic Authorization Header
				$basicAuth = base64_encode("{$this->apiKey}:{$this->secretKey}");
				$headers = [
					'Authorization' => 'Basic ' . $basicAuth,
					'Content-Type' => 'application/json',
				];

				// API Request Parameters
				$queryParams = [
					'type' => 'FIXED_VALUE_RECHARGE',
					'country_iso_code' => $countryCode,
					'operator_id' => $operatorId,
					'service_id' => $this->serviceId,
					'subservice_id' => $this->subServiceId,
				];

				// API Request
				$response = Http::withHeaders($headers)->get("{$this->baseUrl}/products", $queryParams);

				// Handle Successful Response
				if ($response->successful()) {
					$products = $response->json();
					$productData = [];

					if (!empty($products)) {
						$exchangeRates = ExchangeRate::whereType(2)->get()->keyBy('currency');
						
						foreach ($products as $product) {
							$unitAmount = $product['prices']['retail']['amount'] ?? 0;
							$unit = $product['prices']['retail']['unit'] ?? '';
							$rates = $product['rates']['retail'] ?? [];
							$exchangeRate = $exchangeRates[$unit]['exchange_rate'] ?? null;

							if ($exchangeRate) {
								$productData[] = [
									'id' => $product['id'] ?? null,
									'name' => $product['name'] ?? 'Unknown Product',
									'unit' => $unit,
									'rates' => $rates,
									'unit_amount' => $unitAmount,
									'unit_convert_currency' => config('setting.default_currency'),
									'unit_convert_exchange' => $exchangeRate,
									'unit_convert_amount' => $unitAmount * $exchangeRate
								];
							}
						}
					}

					$responseType = $isWeb ? 'webSuccessResponse' : 'successResponse';
					return $this->{$responseType}('Products fetched successfully.', $productData);
				}

				// Handle API Errors
				$errorMessage = 'Operator not found.'; 
				$responseType = $isWeb ? 'webErrorResponse' : 'errorResponse';
				return $this->{$responseType}($errorMessage);
			} 
			catch (\Throwable $e) 
			{
				// Log Exception
				Log::error('Operator API failed with exception:', [
					'operator_code' => $operatorCode,
					'exception' => $e->getMessage(),
				]);

				// Handle Exception
				$errorMessage = 'Operator API failed due to an exception.'. $e->getMessage();
				$responseType = $isWeb ? 'webErrorResponse' : 'errorResponse';
				return $this->{$responseType}($errorMessage);
			}
		} 
		
		public function transactionRecord($request, $user, $isWeb = false)
		{
			// Basic Authorization Header
			$basicAuth = base64_encode("{$this->apiKey}:{$this->secretKey}");
			$headers = [
				'Authorization' => 'Basic ' . $basicAuth,
				'Content-Type' => 'application/json',
			];
			
			$mobile_number = '+' . ltrim($request->mobile_number, '+');
			
			$txnId = $request->order_id;
			// API Request Parameters 
			$transactionRequest = [
				"external_id" => $txnId,
				"product_id" => $request->product_id, 
				"auto_confirm" => true,
				"callback_url" => url('international-airtime/callback', $txnId),
			];
			
			$transactionRequest['credit_party_identifier'] = [
				"mobile_number" => $mobile_number 
			];
    
			// API Request
			$response = Http::withHeaders($headers)->post("{$this->baseUrl}/async/transactions", $transactionRequest);
            
			// Handle Successful Response
			if ($response->successful()) {
				return [
					'success' => true,
					'request' => $transactionRequest,
					'response' => $response->json()
				];
			}
			return [
				'success' => false,
				'request' => $transactionRequest,
				'response' => json_decode($response->body(), true)
			];	
		}
	}	