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
			$this->commissionType = config('setting.dtone_commission_type') ?? 'flat';
			$this->commissionCharge = config('setting.dtone_commission_charge') ?? 0;
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
		
		public function getOperators($countryCode)
		{ 
			// Basic Authorization Header
			$basicAuth = base64_encode("{$this->apiKey}:{$this->secretKey}");
			$headers = [
				'Authorization' => 'Basic ' . $basicAuth,
				'Content-Type' => 'application/json',
			];

			// API Request Parameters
			$queryParams = [
				'country_iso_code' => $countryCode, 
				'service_id' => $this->serviceId,
				'subservice_id' => $this->subServiceId,
			];

			// API Request
			$response = Http::withHeaders($headers)->get("{$this->baseUrl}/operators", $queryParams);
			
			if ($response->successful()) {
				return [
					'success' => true, 
					'response' => $response->json()
				];
			}
			return [
				'success' => false, 
				'response' => json_decode($response->body(), true)
			];	  
		}
		
		public function getValidatePhoneByOperator($mobileNumber, $operatorId, $isWeb = false)
		{  
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
					return [
						'success' => true, 
						'response' => $matchedOperator
					]; 
				}
 
				return [
					'success' => false, 
					'response' => 'The operator is not identified for this mobile number.'
				]; 
			}
			
			return [
				'success' => false, 
				'response' => json_decode($response->body(), true)
			]; 
		}
 
		public function getProducts($countryCode, $operatorId)
		{ 
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

				if (!empty($products)) 
				{
					//$exchangeRates = ExchangeRate::whereType(2)->get()->keyBy('currency');
					
					foreach ($products as $product) {
						$retailUnitCurrency = $product['prices']['retail']['unit'] ?? '';
						$retailUnitAmount = $product['prices']['retail']['amount'] ?? 0;
						$wholesaleUnitCurrency = $product['prices']['wholesale']['unit'] ?? '';
						$wholesaleUnitAmount = $product['prices']['wholesale']['amount'] ?? 0;
						$destinationAmount = $product['destination']['amount'] ?? 0;
						$retailRates = $product['rates']['retail'] ?? 0;
						$wholesaleRates = $product['rates']['wholesale'] ?? 0;
						$validity = $product['validity']['quantity'] ?? 0;
						$validityUnit = $product['validity']['unit'] ?? 'DAY';
						$destinationCurrency = $product['operator']['country']['iso_code'] ?? '';
						
						//$exchangeRate = $exchangeRates[$unit]['exchange_rate'] ?? null;
						  
						$platformFees = $this->commissionType === "flat"
						? max($this->commissionCharge, 0) // Ensure flat fee is not negative
						: max(($retailUnitAmount * $this->commissionCharge / 100), 0); // Ensure percentage fee is not negative



						$productData[] = [
							'id' => $product['id'] ?? null,
							'name' => $product['name'] ?? 'Unknown Product',
							'retail_unit_currency' => $retailUnitCurrency,
							'retail_unit_amount' => $retailUnitAmount,
							'wholesale_unit_currency' => $wholesaleUnitCurrency,
							'wholesale_unit_amount' => $wholesaleUnitAmount,
							'retail_rates' => $retailRates,
							'wholesale_rates' => $wholesaleRates,
							'destination_rates' => $destinationAmount,
							'destination_currency' => $destinationCurrency,
							'platform_fees' => $platformFees,
							'validity' => $validity,
							'validity_unit' => $validityUnit, 
							'remit_currency' => config('setting.default_currency'),
						];
					}
				}

				return [
					'success' => true, 
					'response' => $productData
				];
			}
			
			return [
				'success' => false, 
				'response' => json_decode($response->body(), true)
			];	   
		} 
		
		public function getProductById($productId)
		{  
			if (empty($productId)) {
				return [
					'success' => false,
					'response' => ['message' => 'Product ID is required.']
				];
			}
			$basicAuth = base64_encode("{$this->apiKey}:{$this->secretKey}");
			$headers = [
				'Authorization' => 'Basic ' . $basicAuth,
				'Content-Type' => 'application/json',
			];
 
			// API Request
			$response = Http::withHeaders($headers)->get("{$this->baseUrl}/products/{$productId}");

			// Handle Successful Response
			if ($response->successful()) {
				return [
					'success' => true,
					'response' => $response->json()
				];
			}
			
			// Handle Client or Server Errors
			return [
				'success' => false,
				'response' => [
					'status' => $response->status(),
					'message' => $response->json()['message'] ?? 'An error occurred.'
				]
			]; 
		} 
		
		public function transactionRecord($request, $user)
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