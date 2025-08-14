<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Crypt; // Remove Crypt facade if not used
use Auth;
use App\Models\Country;
use App\Models\OnafriqCountry;
use App\Models\OnafricBank;
use App\Enums\BusinessOccupation;
use App\Enums\SourceOfFunds;
use App\Enums\IdType;

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
		$this->onafricCollectionToken = Config('setting.onafric_collection_token') ?? '';
		$this->onafricCollectionApiUrl = Config('setting.onafric_collection_api_url') ?? '';
		$this->onafricRateCollectionApiUrl = Config('setting.onafric_rate_api_url') ?? '';
		$this->onafricRateCollectionPartnerCode = Config('setting.onafric_rate_partner_code') ?? '';
		$this->onafricRateCollectionAuthKey = Config('setting.onafric_rate_auth_key') ?? '';
		$this->onafricRateCollectionAccountId = Config('setting.onafric_collection_account_id') ?? '';
    }
	
	public function bankAvailableCountry()
	{
		return OnafriqCountry::where('service_name', 'bank-transfer')->pluck('country_name')->toArray();   
	}
	
	public function availableCountry()
	{
		return [
			"Ivory Coast", "Senegal", "South Sudan", "Burkina Faso", "Niger", "Benin", "Guinea-Bissau", "Gambia", "Guinea", "Congo",
			"Gabon", "Rwanda", "Sierra Leone", "Tanzania", "Ghana", "Botswana", "Burundi", "Ethiopia", "Liberia", "Madagascar", "Malawi", "Morocco", "Mozambique", "Zimbabwe", "Cameroon", "Uganda", "Zambia", "Kenya", "Chad", "Central African Republic", "Nigeria","Togo", "Democratic Republic of the Congo" , "South Africa"  
		];   
	}
	
	public function collectionAvailableCountry()
	{
		return OnafriqCountry::where('service_name', 'collection')->pluck('channels', 'country_name')->toArray(); 
	}
	
	public function collectionCountry()
	{
		$africanCountries = $this->collectionAvailableCountry();
		 
		$countries = Country::whereIn('nicename', array_keys($africanCountries)) // fix here: use keys
			->get();

		$countriesWithFlags = $countries->transform(function ($country) use ($africanCountries) {
			// Add full flag URL
			if ($country->country_flag) {
				$country->country_flag = asset('country/' . $country->country_flag);
			}

			// Add available channels from the hardcoded list
			$countryName = $country->nicename;
			$country->available_channels = $africanCountries[$countryName] ?? [];

			return $country;
		});

		return $countriesWithFlags;
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
		
		//Log::info('get_rate request', ['request' => $xmlRequest]);
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
		//Log::info('get_rate response', ['response' => $xmlResponse]);
		
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
	
	
	public function getAccountRequest($recipient_country_code, $recipient_mobile)
    {
        $xmlRequest = <<<XML
        <?xml version="1.0" encoding="utf-8"?>
        <soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
            <soap:Body>
                <ns:account_request xmlns:ns="http://ws.mfsafrica.com">
                    <ns:login>
                        <ns:corporate_code>{$this->onafricCorporate}</ns:corporate_code>
                        <ns:password>{$this->onafricPassword}</ns:password>
                    </ns:login>
                    <ns:to_country>{$recipient_country_code}</ns:to_country>
                    <ns:msisdn>{$recipient_mobile}</ns:msisdn> 
                </ns:account_request>
            </soap:Body>
        </soap:Envelope> 
        XML;
  
		//Log::info('get_rate request', ['request' => $xmlRequest]);
		// Send the request
		$response = Http::withHeaders([
			'Content-Type' => 'text/xml; charset=utf-8',
			'SOAPAction' => 'urn:account_request', // Fixed typo
			'User-Agent' => 'GEOPAYOUTBOUND'
		]) 
		->withBody($xmlRequest, 'text/xml') // Ensure XML is sent as raw body
		->post($this->onafricSyncUrl);
		
		// Debug the raw XML response
		$xmlResponse = $response->body(); 
		//Log::info('get_rate response', ['response' => $xmlResponse]);
	 
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
			$statusCode = $xpath->evaluate("string(//ax21:status_code)");
			$msisdn = $xpath->evaluate("string(//ax21:msisdn)");
			$partnerCode = $xpath->evaluate("string(//ax21:partner_code)"); 

			$arrayResponse = [
				'status_code' => $statusCode,
				'msisdn' => $msisdn,
				'partner_code' => $partnerCode,
			];
			     
		} catch (\Exception $e) {
			return [
				'success' => false, 
				'response' => 'Provided country and mobile number are not active'
			];
		}

		// Return structured response
		return [
			'success' => true,
			'response' => $arrayResponse
		];	 
	}
	
    public function getValidateBankRequest($payoutIso, $bankId, $bankaccountnumber)
    {
        $xmlRequest = <<<XML
        <?xml version="1.0" encoding="utf-8"?>
        <soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
          <soap:Body>
            <ns:validate_bank_account xmlns:ns="http://ws.mfsafrica.com">
              <ns:login>
                <ns:corporate_code>{$this->onafricCorporate}</ns:corporate_code>
                <ns:password>{$this->onafricPassword}</ns:password>
              </ns:login>
              <ns:payee>
                <ns:msisdn></ns:msisdn>
                <ns:name></ns:name>
              </ns:payee>
              <ns:account>
                <ns:account_number>{$bankaccountnumber}</ns:account_number>
                <ns:mfs_bank_code>{$bankId}</ns:mfs_bank_code>
              </ns:account>
              <ns:to_country>{$payoutIso}</ns:to_country>
            </ns:validate_bank_account>
          </soap:Body>
        </soap:Envelope> 
        XML; 
		
		Log::info('bank validate request', ['request' => $xmlRequest]);
		// Send the request
		$response = Http::withHeaders([
			'Content-Type' => 'text/xml; charset=utf-8',
			'SOAPAction' => 'urn:validate_bank_account', // Fixed typo
			'User-Agent' => 'GEOPAYOUTBOUND'
		]) 
		->withBody($xmlRequest, 'text/xml') // Ensure XML is sent as raw body
		->post($this->onafricSyncUrl);
		
		// Debug the raw XML response
		$xmlResponse = $response->body();
	    
		Log::info('bank validate request', ['response' => $xmlResponse]);
	 
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
			$statusCode = $xpath->evaluate("string(//ax21:status_code)");
			$account_holder_name = $xpath->evaluate("string(//ax21:account_holder_name)");
			$partnerCode = $xpath->evaluate("string(//ax21:partner_code)"); 

			$arrayResponse = [
				'status_code' => $statusCode,
				'account_holder_name' => $account_holder_name,
				'partner_code' => $partnerCode,
			];
			     
		} catch (\Exception $e) {
			return [
				'success' => false, 
				'response' => 'Provided bank or account number are not active'
			];
		}

		// Return structured response
		return [
			'success' => true,
			'response' => $arrayResponse
		];	 
	}
	
	public function getOnafricBankFetch($payoutIso)
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
			  <ns:to_country>{$payoutIso}</ns:to_country>
			</ns:get_banks>
		  </soap:Body>
		</soap:Envelope>
		XML;
		
		//Log::info('get_banks request', ['request' => $xmlRequest]);
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
		//Log::info('get_banks response', ['response' => $xmlResponse]);
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
			$bankMfsDataArr = [];
			$bankLogArr = [];
			
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
					
				OnafricBank::updateOrCreate(
					[
						'payout_iso' => $payoutIso,
						'mfs_bank_code' => $bankData['mfs_bank_code'],
					],
					[
						'bank_name' => $bankData['bank_name'], 
						'response' => $bankData, 
						'updated_at' => now()
					]
				);
				$bankMfsDataArr[] = $bankData['mfs_bank_code'];
				$bankLogArr[] = [
					'bank_name' => $bankData['bank_name'],
					'mfs_bank_code' => $bankData['mfs_bank_code'],
					'country_code' => $bankData['country_code'],
				]; 
			}
			
			Log::info('Final Bank Log Array:', $bankLogArr);
			if(count($bankMfsDataArr) > 0)
			{
				OnafricBank::where('payout_iso', $payoutIso)->whereNotIn('mfs_bank_code', $bankMfsDataArr)->delete();
			} 
			return true;
		} catch (\Exception $e) {
			return false;
		} 
	}

	public function getOnafricBank($request)
    {   
		try 
		{
			$onafricBanks = OnafricBank::where('payout_iso', $request['payoutIso'])->where('status', 1)->get();  
			$output = '<option value="">Select Bank Name</option>';
			foreach ($onafricBanks as $onafricBank)  
			{
				$selected = ($request['bankId'] ?? '') == $onafricBank->mfs_bank_code ? 'selected' : '';
				$output .= sprintf(
					'<option value="%s" data-bank-name="%s" %s>%s</option>',
					htmlspecialchars($onafricBank->mfs_bank_code ?? '', ENT_QUOTES, 'UTF-8'),
					htmlspecialchars($onafricBank->bank_name ?? '', ENT_QUOTES, 'UTF-8'),
					$selected,
					htmlspecialchars(
						($onafricBank->bank_name ?? '') . ' (' . ($onafricBank->mfs_bank_code ?? '') . ')',
						ENT_QUOTES,
						'UTF-8'
					)
				);
			}
			return $output; 
		} catch (\Exception $e) {
			return '<option value="">No banks available</option>';
		}  
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
		//dd($request->all());
		$uuid = Str::uuid()->toString(); 
		$timestamp = now()->format('YmdHis'); 
		$batchId = "BATCH-" . $uuid . "-" . $timestamp;  
		$requestTimestamp = $request->timestamp;
		$thirdPartyTransId = $request->order_id;   
		$sendFee = $request->sendFee ?? 0;

		$txnAmount = $request->payoutCurrencyAmount; 
		$payoutCurrency = $beneficiary['payoutCurrency'] ?? '';
		$mobileNumber = ltrim(($beneficiary['mobile_code'] ?? ''), '+').($beneficiary['recipient_mobile'] ?? '');
		
		$user = Auth::user(); 
		$purposeOfTransfer = BusinessOccupation::from($user->business_activity_occupation)->label();
		$sourceOfFunds = SourceOfFunds::from($user->source_of_fund)->label();
		
		$requestBody = [
			"corporateCode" => $this->onafricCorporate,
			"password" => $this->onafricPassword, 
			"batchId" => $batchId,
			"requestBody" => [
				[
					"instructionType" => [
						"destAcctType" => 1,
						"amountType" => 2
					],
					"amount" => [
						"amount" => (string) $txnAmount,
						"currencyCode" => $payoutCurrency
					],
					"sendFee" => null,
					// "sendFee" => [
					// 	"amount" => (string) $sendFee,
					// 	"currencyCode" => $this->defaultCurrency
					// ],
					"sender" => [
						"msisdn" => $beneficiary['sender_mobile'] ?? '',
						"fromCountry" => $beneficiary['sender_country_code'] ?? '',
						"name" => $user->first_name ?? '',
						"surname" => $user->last_name ?? '',
						"address" => $user->address ?? '',
						"city" => $user->city ?? '',
						"state" => $user->state ?? '',
						"postalCode" => $user->zip_code ?? '',
						"email" => $user->email ?? '',
						"dateOfBirth" => null,
						"document" => null,
						"placeOfBirth" => $user->date_of_birth ?? '',
					],
					"recipient" => [
						"msisdn" => $mobileNumber ?? '',
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
					"purposeOfTransfer" => $purposeOfTransfer ?? '',
					"sourceOfFunds" => $sourceOfFunds ?? '',
				]
			]
		]; 
		
		//Log::info('send mobile request', ['request' => $requestBody]);
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
	  
		//Log::info('send mobile response', ['response' => $response->json()]);
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
	
	public function apiSendMobileTransaction($request, $user)
	{     
		$uuid = Str::uuid()->toString(); 
		$timestamp = now()->format('YmdHis'); 
		$batchId = "BATCH-" . $uuid . "-" . $timestamp;  
		$requestTimestamp = $request->timestamp;
		$thirdPartyTransId = $request->order_id;   
		  
		$txnAmount = $request->converted_amount; 
		$payoutCurrency = $request['payoutCurrency'] ?? '';
		$mobileNumber = ltrim(($request['recipient_mobile'] ?? ''), '+');
	   
		$requestBody = [
			"corporateCode" => $this->onafricCorporate,
			"password" => $this->onafricPassword, 
			"batchId" => $batchId,
			"requestBody" => [
				[
					"instructionType" => [
						"destAcctType" => 1,
						"amountType" => 2
					],
					"amount" => [
						"amount" => (string) $txnAmount,
						"currencyCode" => $payoutCurrency
					],
					"sendFee" => null, 
					"sender" => [
						"msisdn" => $request['sender_mobile'] ?? '',
						"fromCountry" => $request['sender_country_code'] ?? '',
						"name" => $request['sender_name'] ?? '',
						"surname" => $request['sender_surname'] ?? '',
						"address" => $request['sender_address'] ?? '',
						"city" => $request['sender_city'] ?? '',
						"state" => $request['sender_state'] ?? '',
						"postalCode" => $request['sender_postalCode'] ?? '',
						"email" => $request['sender_email'] ?? '',
						"dateOfBirth" => $request['sender_dateOfBirth'] ?? null,
						"document" => $request['sender_document'] ?? null,
						"placeOfBirth" => $request['sender_placeofbirth'] ?? null,
					],
					"recipient" => [
						"msisdn" => $mobileNumber ?? '',
						"toCountry" => $request['recipient_country_code'] ?? '',
						"name" => $request['recipient_name'] ?? '',
						"surname" => $request['recipient_surname'] ?? '',
						"address" => $request['recipient_address'] ?? '',
						"city" => $request['recipient_city'] ?? '',
						"state" => $request['recipient_state'] ?? '',
						"postalCode" => $request['recipient_postalcode'] ?? '',
						"email" => $request['recipient_email'] ?? null,
						"dateOfBirth" => $request['recipient_dateofbirth'] ?? null,
						"document" => $request['recipient_document'] ?? null,
						"destinationAccount" => $request['recipient_destinationAccount'] ?? null,
					],
					"thirdPartyTransId" => $thirdPartyTransId,
					"reference" => null,
					"purposeOfTransfer" => $request['purposeOfTransfer'] ?? null,
					"sourceOfFunds" => $request['sourceOfFunds'] ?? null,
				]
			]
		]; 
		
		//Log::info('send mobile request', ['request' => $requestBody]);
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
	  
		//Log::info('send mobile response', ['response' => $response->json()]);
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
		//Log::info('webhook register request', ['request' => $requestBody]);
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
		//Log::info('webhook register response', ['response' => $response->json()]);
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
	
	public function getTransactionStatus($thirdPartyTransId)
	{  
		$requestTimestamp = now()->format('Y-m-d H:i:s');  
		$requestBody = [
			"corporateCode" => $this->onafricCorporate,
			"password" => $this->onafricPassword, 
			"thirdPartyTransId" => $thirdPartyTransId 
		];
		
		//Log::info('query status request', ['request' => $requestBody]);
		
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
		->post($this->onafricAsyncCallService.'/status', $requestBody); // Send requestBody instead of $data
		
		Log::info('query status response', ['response' => $response->json()]);
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
		$txnAmount = $request->payoutCurrencyAmount;
		$sendFee = $this->sendFees; 
		$payoutCurrency = $beneficiary['payoutCurrency'] ?? '';
		
		$mobileNumber = ltrim(($beneficiary['mobile_code'] ?? ''), '+').($beneficiary['receivercontactnumber'] ?? '');
		$user = Auth::user(); 
		$purposeOfTransfer = BusinessOccupation::from($user->business_activity_occupation)->label();
		$sourceOfFunds = SourceOfFunds::from($user->source_of_fund)->label();
		
		$requestBody = [
			"corporateCode" => $this->onafricCorporate,
			"password" => $this->onafricPassword, 
			"batchId" => $batchId,
			"requestBody" => [
				[
					"instructionType" => [
						"destAcctType" => 2,
						"amountType" => 2
					],
					"amount" => [
						"amount" => (string) $txnAmount,
						"currencyCode" => $payoutCurrency
					],
					"sendFee" => null,
					"sender" => [
						"msisdn" => $beneficiary['sender_mobile'] ?? '',
						"fromCountry" => $beneficiary['sender_country_code'] ?? '',
						"name" => $user->first_name ?? '',
						"surname" => $user->last_name ?? '',
						"address" => $user->address ?? '',
						"city" => $user->city ?? '',
						"state" => $user->state ?? '',
						"postalCode" => $user->zip_code ?? '',
						"email" => $user->email ?? '',
						"dateOfBirth" => null,
						"document" => null,
						"placeOfBirth" => $user->date_of_birth ?? '',
					], 
					"recipient" => [
						"msisdn" => $mobileNumber ?? '',
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
					"purposeOfTransfer" => $purposeOfTransfer ?? '',
					"sourceOfFunds" => $sourceOfFunds ?? '',
				]
			]
		];
		
		//Log::info('send bank request', ['request' => $requestBody]);
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
	  
		//Log::info('send bank response', ['response' => $response->json()]);
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
	
	public function sendMobileCollectionTransaction($request)
	{   
		$thirdPartyTransId = $request->order_id;
		$txnAmount = (int)$request->payoutCurrencyAmount; 
		$mobileNumber = ltrim($request->mobile_code . $request->mobile_no, '+'); 
		$payoutCurrency = $request->payoutCurrency;
		$requestCurrency = $request->request_currency;
		$account = $this->onafricRateCollectionAccountId ?? '';
		  
		// Base payload
		$requestBody = [
			"phonenumber" => "+" . $mobileNumber,
			"amount" => $txnAmount,
			"currency" => $payoutCurrency,
			//"callback_url" => route('mobile-collection.callback'),
			"metadata" => [
				"order_id" => $thirdPartyTransId,
			],
		];
		
		// Determine payload type based on currency and country
		if ($payoutCurrency === 'BXC') { 
			$requestBody['reason'] = $request->notes ?? 'BXC Collection';
		} elseif ($request->country_code == 240) {
			// DRC collection
			$requestBody["currency"] = $this->defaultCurrency;
			$requestBody["amount"] = $requestCurrency === "USD" ? (int) $request->txnAmount : (int) $request->payoutCurrencyAmount;
			$requestBody = array_merge($requestBody, [
				"account" => $account,
				//"request_currency" => $this->defaultCurrency ?? "USD",   
				"request_currency" => $requestCurrency,
				"reason" => $request->notes ?? "DRC Multi-currency Collection",
				"send_instructions" => true,
			]);
		} elseif ($payoutCurrency === 'NGN') {
			// Nigerian Baxi collection 
			$requestBody = array_merge($requestBody, [
				"account" => $account,
				"description" => $request->notes ?? '',
				"send_instructions" => true,
				"enable_email_bill" => true,
				"enable_merchant_pull" => false,
				"customer_email" => $request->beneficiary_email ?? "",
				"first_name" => $request->beneficiary_name ?? "",
				"last_name" => $request->beneficiary_last_name ?? "",
				"instructions" => $request->notes ?? "",
				"expiry_date" => $request->expired_date ?? "",
				"max_attempts" => 0
			]);
		} else {
			// General cross-border collection
			$requestBody = array_merge($requestBody, [
				"account" => $account,
				"reason" => $request->notes ?? '',
				"send_instructions" => true,
			]);
		}
		 
		// Send the API request using Laravel's HTTP client
		$response = Http::withHeaders([
			'Authorization' => 'Token ' . $this->onafricCollectionToken, 
			'Content-Type' => 'application/json',
		])
		->withOptions([
			'verify' => false, 
		])
		->post($this->onafricCollectionApiUrl.'/collectionrequests', $requestBody); // Send requestBody instead of $data
	  
		 
		// Handle the response
		if ($response->successful()) {
			return [
				'success' => true,
				'request' => $requestBody, 
				'response' => $response->json(),  
			];
		}

		// If the response was unsuccessful, return an error response
		return [
			'success' => false,
			'request' => $requestBody, // Return the request sent
			'response' => json_decode($response->body(), true), // Return the error response body
		];
	}
	
	public function getCollectionStatus($requestId)
	{        
		$bearerToken = $this->onafricCollectionToken; 
		 
		// Send the API request using Laravel's HTTP client
		$response = Http::withHeaders([
			'Authorization' => 'Token ' . $bearerToken, 
			'Content-Type' => 'application/json',
		])
		->withOptions([
			'verify' => false, // Disable SSL verification if needed
		])
		->get($this->onafricCollectionApiUrl.'/collectionrequests/'.$requestId); // Send requestBody instead of $data
	  
		//Log::info('Collection Response', ['response' => $response->json()]);
		// Handle the response
		if ($response->successful()) {
			return [
				'success' => true, 
				'response' => $response->json(),  
			];
		}

		// If the response was unsuccessful, return an error response
		return [
			'success' => false, 
			'response' => json_decode($response->body(), true), // Return the error response body
		];
	}
	
	public function getCollectionRates()
	{        
		$partnerCode = $this->onafricRateCollectionPartnerCode; 
		$authKey = $this->onafricRateCollectionAuthKey; 
		 
		// Send the API request using Laravel's HTTP client
		$response = Http::withHeaders([
			'AuthKey' => $authKey,  
		]) 
		->get($this->onafricRateCollectionApiUrl.'?pCode='.$partnerCode); 
	   
		// Handle the response
		if ($response->successful()) {
			return [
				'success' => true, 
				'response' => $response->json(),  
			];
		}

		// If the response was unsuccessful, return an error response
		return [
			'success' => false, 
			'response' => json_decode($response->body(), true), // Return the error response body
		];
	}
	 
}