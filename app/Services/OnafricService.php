<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Crypt; // Remove Crypt facade if not used
use Auth;
use App\Models\Country;
class OnafricService
{
    protected $onafricCorporate;
    protected $onafricPassword;
    protected $onafricSyncUrl;

    public function __construct()
    {
        $this->onafricCorporate = config('setting.onafric_corporate');
        $this->onafricPassword = config('setting.onafric_password');
        $this->onafricSyncUrl = config('setting.onafric_sync_url');
        $this->onafricUniqueKey = config('setting.onafric_unique_key');
        $this->onafricAsyncCallService = rtrim(config('setting.onafric_async_callservice') ?? '', '/');
		$this->defaultCurrency = Config('setting.default_currency') ?? 'USD';
		$this->sendFees = Config('setting.onafric_bank_send_fees') ?? '1.5';
    }
	
	public function bankAvailableCountry()
	{
		return [
			"Uganda", "Kenya", "Nigeria", "South Africa" 
		];   
	}
	
	public function availableCountry()
	{
		return [
			"Ivory Coast", "Senegal", "South Sudan", "Burkina Faso", "Niger", "Benin", "Guinea-Bissau", "Gambia", "Guinea", "Congo",
			"Gabon", "Rwanda", "Sierra Leone", "Tanzania", "Ghana", "Botswana", "Burundi", "Ethiopia", "Liberia", "Madagascar", "Malawi", "Morocco", "Mozambique", "Zimbabwe", "Cameroon", "Uganda", "Zambia", "Kenya", "Chad", "Central African Republic", "Nigeria","Togo", "Democratic Republic of the Congo" , "South Africa"  
		];   
	}
	
	public function country()
	{
		$africanCountries = $this->availableCountry();
		$countries = Country::with('channels')
		->whereHas('channels')
		->whereIn('nicename', $africanCountries)
		->get(); 
		
		$countriesWithFlags = $countries->transform(function ($country) {
			if ($country->country_flag) {
				$country->country_flag = asset('country/' . $country->country_flag);
			} 
			return $country;
		});
		return $countriesWithFlags;
	}
	
	public function getRates($defaultCurrency, $country)
    {
        $xmlRequest = <<<XML
		<?xml version="1.0" encoding="utf-8"?>
		<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
			<soap:Body>
				<ns:get_rate xmlns:ns="http://ws.mfsafrica.com">
					<ns:login>
						<ns:corporate_code>{$this->onafricCorporate}</ns:corporate_code>
						<ns:password>{$this->onafricPassword}</ns:password>
					</ns:login>
					<ns:to_country>{$country['iso']}</ns:to_country>
					<ns:from_currency>{$defaultCurrency}</ns:from_currency>
					<ns:to_currency>{$country['currency_code']}</ns:to_currency>
				</ns:get_rate>
			</soap:Body>
		</soap:Envelope>
		XML; 
	 
		// Send the request
		$response = Http::withHeaders([
			'Content-Type' => 'text/xml; charset=utf-8',
			'SOAPAction' => 'urn:get_rate', // Fixed typo
			'User-Agent' => 'GEOPAYOUTBOUND'
		]) 
		->withBody($xmlRequest, 'text/xml') // Ensure XML is sent as raw body
		->post($this->onafricSyncUrl);
		
		// Debug the raw XML response
		$xmlResponse = $response->body(); 
		
		
		try 
		{    
			libxml_use_internal_errors(true); // Prevent XML parsing errors from displaying
			$dom = new \DOMDocument();
				if (!$dom->loadXML($xmlResponse)) { 
					return [
					'success' => false, 
					'response' => 'Invalid XML response'
				];
			}

			$xpath = new \DOMXPath($dom);
			$xpath->registerNamespace('soapenv', 'http://schemas.xmlsoap.org/soap/envelope/');
			$xpath->registerNamespace('ns', 'http://ws.mfsafrica.com');
			$xpath->registerNamespace('ax21', 'http://mfs/xsd');

			// Extract values using XPath queries
			$fromCurrency = $xpath->evaluate("string(//ax21:from_currency)");
			$fxRate = $xpath->evaluate("string(//ax21:fx_rate)");
			$partnerCode = $xpath->evaluate("string(//ax21:partner_code)");
			$timeStamp = $xpath->evaluate("string(//ax21:time_stamp)");
			$toCurrency = $xpath->evaluate("string(//ax21:to_currency)");

			$arrayResponse = [
				'from_currency' => $fromCurrency,
				'fx_rate' => $fxRate,
				'partner_code' => $partnerCode,
				'time_stamp' => $timeStamp,
				'to_currency' => $toCurrency,
			];
			     
		} catch (\Exception $e) {
			return [
				'success' => false, 
				'response' => 'Invalid XML response'
			];
		}

		// Return structured response
		return [
			'success' => true,
			'response' => $arrayResponse
		];	 
	}  
	
	public function getOnafricBank($request)
    {  
		$xmlRequest = <<<XML
		<?xml version="1.0" encoding="utf-8"?>
		<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
		  <soap:Body>
			<ns:get_banks xmlns:ns="http://ws.mfsafrica.com">
			  <ns:login>
				<ns:corporate_code>{$this->onafricCorporate}</ns:corporate_code>
				<ns:password>{$this->onafricPassword}</ns:password>
			  </ns:login>
			  <ns:to_country>{$request['payoutIso']}</ns:to_country>
			</ns:get_banks>
		  </soap:Body>
		</soap:Envelope>
		XML;
		
		// Send the request
		$response = Http::withHeaders([
			'Content-Type' => 'text/xml; charset=utf-8',
			'SOAPAction' => 'urn:get_banks', // Fixed typo
			'User-Agent' => 'GEOPAYOUTBOUND'
		]) 
		->withBody($xmlRequest, 'text/xml') // Ensure XML is sent as raw body
		->post($this->onafricSyncUrl);
		
		// Debug the raw XML response
		$xmlResponse = $response->body(); 
		
		try 
		{    
			 // Disable XML errors to avoid display during parsing
			libxml_use_internal_errors(true);

			// Create DOMDocument instance
			$dom = new \DOMDocument();

			// Load the XML string
			if (!$dom->loadXML($xmlResponse)) {
				return [
					'success' => false,
					'response' => 'Invalid XML response'
				];
			}

			// Initialize DOMXPath to query the XML
			$xpath = new \DOMXPath($dom);

			// Register namespaces for XPath queries
			$xpath->registerNamespace('soapenv', 'http://schemas.xmlsoap.org/soap/envelope/');
			$xpath->registerNamespace('ns', 'http://ws.mfsafrica.com');
			$xpath->registerNamespace('ax21', 'http://mfs/xsd');

			// Extract all the <return> nodes
			$entries = $xpath->query('//ns:return');

			// Prepare an array to store the results
			$results = [];

			// Iterate over each <return> node
			foreach ($entries as $entry) {
				$bankData = [];

				// Extract specific elements from each <return>
				$bankData['bank_name'] = $xpath->query('ax21:bank_name', $entry)->item(0)->nodeValue ?? '';
				$bankData['bic'] = $xpath->query('ax21:bic', $entry)->item(0)->nodeValue ?? '';
				$bankData['country_code'] = $xpath->query('ax21:country_code', $entry)->item(0)->nodeValue ?? '';
				$bankData['currency_code'] = $xpath->query('ax21:currency_code', $entry)->item(0)->nodeValue ?? '';
				$bankData['dom_bank_code'] = $xpath->query('ax21:dom_bank_code', $entry)->item(0)->nodeValue ?? '';
				$bankData['iban'] = $xpath->query('ax21:iban', $entry)->item(0)->nodeValue ?? '';
				$bankData['mfs_bank_code'] = $xpath->query('ax21:mfs_bank_code', $entry)->item(0)->nodeValue ?? '';

				// Extract the bank_limit data
				$bankLimitData = [];
				$bankLimitData['max_daily_value'] = $xpath->query('ax21:bank_limit/ax21:max_daily_value', $entry)->item(0)->nodeValue ?? '';
				$bankLimitData['max_monthly_value'] = $xpath->query('ax21:bank_limit/ax21:max_monthly_value', $entry)->item(0)->nodeValue ?? '';
				$bankLimitData['max_per_tx_limit'] = $xpath->query('ax21:bank_limit/ax21:max_per_tx_limit', $entry)->item(0)->nodeValue ?? '';
				$bankLimitData['max_weekly_value'] = $xpath->query('ax21:bank_limit/ax21:max_weekly_value', $entry)->item(0)->nodeValue ?? '';
				$bankLimitData['min_per_tx_limit'] = $xpath->query('ax21:bank_limit/ax21:min_per_tx_limit', $entry)->item(0)->nodeValue ?? '';

				// Add bank_limit to the bank data
				$bankData['bank_limit'] = $bankLimitData;

				// Push the bank data to the results array
				$results[] = $bankData; 
			}
			
			$output = '<option value="">Select Bank Name</option>';
			foreach($results as $result) 
			{
				$selected = ($request['bankId'] ?? '') == $result['mfs_bank_code'] ? 'selected' : '';
				$output .= sprintf(
					'<option value="%s" data-bank-name="%s" %s>%s</option>',
					htmlspecialchars($result['mfs_bank_code'] ?? '', ENT_QUOTES, 'UTF-8'),
					htmlspecialchars($result['bank_name'] ?? '', ENT_QUOTES, 'UTF-8'),
					htmlspecialchars($selected, ENT_QUOTES, 'UTF-8'),
					htmlspecialchars($result['bank_name'] ?? '', ENT_QUOTES, 'UTF-8')
				);
			}
			return $output; 
		} catch (\Exception $e) {
			return '<option value="">No banks available</option>';
		}

		return '<option value="">No banks available</option>';
	}  
	
	public function generateBearerToken($timestamp)
	{
		$data = $this->onafricCorporate . $this->onafricPassword . $timestamp;  
		return hash('sha256', $data);
	}
	
	public function generateMfsSign($batchId)
	{
		$data = $this->onafricPassword . $batchId . $this->onafricUniqueKey;  
		return hash('sha256', $data); 
	}

	public function sendMobileTransaction($request, $beneficiary)
	{    
		$uuid = Str::uuid()->toString(); 
		$timestamp = now()->format('YmdHis'); 
		$batchId = "BATCH-" . $uuid . "-" . $timestamp;  
		$requestTimestamp = $request->timestamp;
		$thirdPartyTransId = $request->order_id;  
		$txnAmount = $request->txnAmount;
		$sendFee = $request->sendFee ?? 0;
		 
		$requestBody = [
			"corporateCode" => $this->onafricCorporate,
			"password" => $this->onafricPassword, 
			"batchId" => $batchId,
			"requestBody" => [
				[
					"instructionType" => [
						"destAcctType" => 1,
						"amountType" => 1
					],
					"amount" => [
						"amount" => (string) $txnAmount,
						"currencyCode" => /* $country->currency_code ?? */ $this->defaultCurrency
					],
					"sendFee" => [
						"amount" => (string) $sendFee,
						"currencyCode" => /* $country->currency_code ?? */ $this->defaultCurrency
					],
					"sender" => [
						"msisdn" => $beneficiary['sender_mobile'] ?? '',
						"fromCountry" => $beneficiary['sender_country_code'] ?? '',
						"name" => $beneficiary['sender_name'] ?? '',
						"surname" => $beneficiary['sender_surname'] ?? '',
						"address" => $beneficiary['sender_address'] ?? '',
						"city" => $beneficiary['sender_city'] ?? '',
						"state" => $beneficiary['sender_state'] ?? '',
						"postalCode" => $beneficiary['sender_postalcode'] ?? '',
						"email" => null,
						"dateOfBirth" => null,
						"document" => null,
						"placeOfBirth" => $beneficiary['sender_placeofbirth'] ?? '',
					],
					"recipient" => [
						"msisdn" => $beneficiary['recipient_mobile'] ?? '',
						"toCountry" => $beneficiary['recipient_country_code'] ?? '',
						"name" => $beneficiary['recipient_name'] ?? '',
						"surname" => $beneficiary['recipient_surname'] ?? '',
						"address" => $beneficiary['recipient_address'] ?? '',
						"city" => $beneficiary['recipient_city'] ?? '',
						"state" => $beneficiary['recipient_state'] ?? '',
						"postalCode" => $beneficiary['recipient_postalcode'] ?? '',
						"email" => null,
						"dateOfBirth" => $beneficiary['recipient_dateofbirth'] ?? '',
						"document" => null,
						"destinationAccount" => null
					],
					"thirdPartyTransId" => $thirdPartyTransId,
					"reference" => null,
					"purposeOfTransfer" => $beneficiary['purposeOfTransfer'] ?? '',
					"sourceOfFunds" => $beneficiary['sourceOfFunds'] ?? '',
				]
			]
		];
	  
		// Generate the mfsSign
		$mfsSign = $this->generateMfsSign($batchId);
	  
		// Add mfsSign to requestBody
		$requestBody['mfsSign'] = $mfsSign;
 
		// Generate the bearer token 
		$bearerToken = $this->generateBearerToken($requestTimestamp); 
		 
		// Send the API request using Laravel's HTTP client
		$response = Http::withHeaders([
			'Authorization' => 'Bearer ' . $bearerToken, // Add Bearer Token to the header
			'timestamp' => $requestTimestamp,
			'Content-Type' => 'application/json',
		])
		->withOptions([
			'verify' => false, // Disable SSL verification if needed
		])
		->post($this->onafricAsyncCallService.'/callService', $requestBody); // Send requestBody instead of $data
	  
		// Handle the response
		if ($response->successful()) {
			return [
				'success' => true,
				'request' => $requestBody, // Return the request sent
				'response' => $response->json(), // Return the API response
			];
		}

		// If the response was unsuccessful, return an error response
		return [
			'success' => false,
			'request' => $requestBody, // Return the request sent
			'response' => json_decode($response->body(), true), // Return the error response body
		];
	}
	
	public function webhookRegister()
	{ 
		$webhookUniqueId = "nW8h9vQ8MgRQTbqTUcy5HcjBLmbRB9";
		$requestBody = [
			"corporateCode" => $this->onafricCorporate, 
			"callbackUrl" => url('onafric/webhook', $webhookUniqueId)
		];
		 
		// Generate the bearer token 
		$requestTimestamp = now()->format('Y-m-d H:i:s'); 
		$bearerToken = $this->generateBearerToken($requestTimestamp); 
		
		// Send the API request using Laravel's HTTP client
		$response = Http::withHeaders([
			'Authorization' => 'Bearer ' . $bearerToken, // Add Bearer Token to the header
			'password' => $this->onafricPassword,
			'Content-Type' => 'application/json',
		])
		->withOptions([
			'verify' => false, // Disable SSL verification if needed
		])
		->post($this->onafricAsyncCallService.'/api/webhook/subscribe', $requestBody); // Send requestBody instead of $data
	  
		// Handle the response
		if ($response->successful()) {
			return [
				'success' => true,
				'request' => $requestBody, // Return the request sent
				'response' => $response->json(), // Return the API response
			];
		}

		// If the response was unsuccessful, return an error response
		return [
			'success' => false,
			'request' => $requestBody, // Return the request sent
			'response' => json_decode($response->body(), true), // Return the error response body
		];
	}
	public function getWebhookRegister()
	{  
		// Generate the bearer token 
		$requestTimestamp = now()->format('Y-m-d H:i:s'); 
		$bearerToken = $this->generateBearerToken($requestTimestamp); 
		
		// Send the API request using Laravel's HTTP client
		$response = Http::withHeaders([
			'Authorization' => 'Bearer ' . $bearerToken, // Add Bearer Token to the header
			'password' => $this->onafricPassword,
			'Content-Type' => 'application/json',
		])
		->withOptions([
			'verify' => false, // Disable SSL verification if needed
		])
		->get($this->onafricAsyncCallService.'/api/webhook/'.$this->onafricCorporate); // Send requestBody instead of $data
	  
		// Handle the response
		if ($response->successful()) {
			return [
				'success' => true, 
				'response' => $response->json(), // Return the API response
			];
		}

		// If the response was unsuccessful, return an error response
		return [
			'success' => false, 
			'response' => json_decode($response->body(), true), // Return the error response body
		];
	}
	
	public function getTransactionStatus()
	{  
		$requestTimestamp = now()->format('Y-m-d H:i:s'); 
		$thirdPartyTransId = "GPTM-12-1738053070";
		$requestBody = [
			"corporateCode" => $this->onafricCorporate,
			"password" => $this->onafricPassword, 
			"thirdPartyTransId" => $thirdPartyTransId 
		];
	  
		// Generate the mfsSign
		$mfsSign = $this->generateMfsSign($thirdPartyTransId);
	  
		// Add mfsSign to requestBody
		$requestBody['mfsSign'] = $mfsSign;
 
		// Generate the bearer token 
		$bearerToken = $this->generateBearerToken($requestTimestamp); 
		
		// Send the API request using Laravel's HTTP client
		$response = Http::withHeaders([
			'Authorization' => 'Bearer ' . $bearerToken, // Add Bearer Token to the header
			'timestamp' => $requestTimestamp,
			'Content-Type' => 'application/json',
		])
		->withOptions([
			'verify' => false, // Disable SSL verification if needed
		])
		->post('https://async-v2.dev.apionafriq.com/hub/async/status', $requestBody); // Send requestBody instead of $data
	  
		// Handle the response
		if ($response->successful()) {
			return [
				'success' => true,
				'request' => $requestBody, // Return the request sent
				'response' => $response->json(), // Return the API response
			];
		}

		// If the response was unsuccessful, return an error response
		return [
			'success' => false,
			'request' => $requestBody, // Return the request sent
			'response' => json_decode($response->body(), true), // Return the error response body
		];
	}
	
	public function sendBankTransaction($request, $beneficiary)
	{    
		$uuid = Str::uuid()->toString(); 
		$timestamp = now()->format('YmdHis'); 
		$batchId = "BATCH-" . $uuid . "-" . $timestamp;  
		$requestTimestamp = $request->timestamp;
		$thirdPartyTransId = $request->order_id;  
		$txnAmount = $request->txnAmount;
		$sendFee = $this->sendFees;
		 
		$requestBody = [
			"corporateCode" => $this->onafricCorporate,
			"password" => $this->onafricPassword, 
			"batchId" => $batchId,
			"requestBody" => [
				[
					"instructionType" => [
						"destAcctType" => 2,
						"amountType" => 1
					],
					"amount" => [
						"amount" => (string) $txnAmount,
						"currencyCode" => $this->defaultCurrency
					],
					"sendFee" => [
						"amount" => (string) $sendFee,
						"currencyCode" => $this->defaultCurrency
					],
					"sender" => [
						"msisdn" => $beneficiary['sender_mobile'] ?? '',
						"fromCountry" => $beneficiary['sender_country_code'] ?? '',
						"name" => $beneficiary['sender_name'] ?? '',
						"surname" => $beneficiary['sender_surname'] ?? '',
						"address" => $beneficiary['sender_address'] ?? '',
						"city" => null,
						"state" => null,
						"postalCode" => null,
						"email" => null,
						"dateOfBirth" => null,
						"document" => null,
						"placeOfBirth" => null,
					],
					"recipient" => [
						"msisdn" => $beneficiary['receivercontactnumber'] ?? '',
						"toCountry" => $beneficiary['payoutIso'] ?? '',
						"name" => $beneficiary['receiverfirstname'] ?? '',
						"surname" => $beneficiary['receiverlastname'] ?? '',
						"address" => $beneficiary['receiveraddress'] ?? '',
						"city" => null,
						"state" => null,
						"postalCode" => null,
						"email" => null,
						"dateOfBirth" => null,
						"document" => [
							'idNumber' => $beneficiary['idNumber'] ?? '',
							'idType' => $beneficiary['idType'] ?? '',
							'idExpiry' => $beneficiary['idExpiry'] ?? '',
						],
						"destinationAccount" => [
							'accountNumber' => $beneficiary['bankaccountnumber'] ?? '',
							'mfsBankCode' => $beneficiary['bankId'] ?? '',
						]
					],
					"thirdPartyTransId" => $thirdPartyTransId,
					"reference" => null,
					"purposeOfTransfer" => $beneficiary['purposeOfTransfer'] ?? '',
					"sourceOfFunds" => $beneficiary['sourceOfFunds'] ?? '',
				]
			]
		];
	  
		// Generate the mfsSign
		$mfsSign = $this->generateMfsSign($batchId);
	  
		// Add mfsSign to requestBody
		$requestBody['mfsSign'] = $mfsSign;
 
		// Generate the bearer token 
		$bearerToken = $this->generateBearerToken($requestTimestamp); 
		 
		// Send the API request using Laravel's HTTP client
		$response = Http::withHeaders([
			'Authorization' => 'Bearer ' . $bearerToken, // Add Bearer Token to the header
			'timestamp' => $requestTimestamp,
			'Content-Type' => 'application/json',
		])
		->withOptions([
			'verify' => false, // Disable SSL verification if needed
		])
		->post($this->onafricAsyncCallService.'/callService', $requestBody); // Send requestBody instead of $data
	  
		// Handle the response
		if ($response->successful()) {
			return [
				'success' => true,
				'request' => $requestBody, // Return the request sent
				'response' => $response->json(), // Return the API response
			];
		}

		// If the response was unsuccessful, return an error response
		return [
			'success' => false,
			'request' => $requestBody, // Return the request sent
			'response' => json_decode($response->body(), true), // Return the error response body
		];
	}
}