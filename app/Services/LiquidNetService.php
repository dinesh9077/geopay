<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Crypt; // Remove Crypt facade if not used
use Auth;
class LiquidNetService
{
    protected $appId;
    protected $apiKey;
    protected $baseUrl;

    public function __construct()
    {
        $this->appId = config('setting.lightnet_apikey');
        $this->apiKey = config('setting.lightnet_secretkey');
        $this->baseUrl = config('setting.lightnet_url');
        $this->defaultCurrency = Config('setting.default_currency') ?? 'USD';
    }

    public function hmacAuthGenerate(string $method, string $apiUrl, string $requestTimeStamp, array $requestBody = [])
    { 
		$jsonRequestBody = json_encode($requestBody);
        $requestUri = strtolower(urlencode($apiUrl));

        $requestHttpMethod = strtoupper($method);

        $nonce = Str::uuid()->toString();

        $requestContentBase64String = '';
        if (!empty($jsonRequestBody)) {
            $requestContentHash = md5($jsonRequestBody, true);
            $requestContentBase64String = base64_encode($requestContentHash);
        }

        $signatureRawData = sprintf('%s%s%s%s%s%s', $this->appId, $requestHttpMethod, $requestUri, $requestTimeStamp, $nonce, $requestContentBase64String);
        $secretKeyByteArray = base64_decode($this->apiKey);

        $signatureBytes = hash_hmac('sha256', $signatureRawData, $secretKeyByteArray, true);

        $requestSignatureBase64String = base64_encode($signatureBytes);

        $signatureString = sprintf('%s:%s:%s:%s', $this->appId, $requestSignatureBase64String, $nonce, $requestTimeStamp);
		  
        return $signatureString;
	} 
	
	public function serviceApi(string $method, string $url, string $requestTimeStamp, array $requestBody = [])
    {
        $apiUrl = $this->baseUrl . $url; 
		$signatureString = $this->hmacAuthGenerate($method, $apiUrl, $requestTimeStamp, $requestBody);
        $response = Http::withHeaders([
            'Authorization' => "hmacauth {$signatureString}",
            'Content-Type' => 'application/json',
        ])
		->withOptions([
			'verify' => false,
		])
		->{$method}($apiUrl, $requestBody);

        // Handle Successful Response
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
	
	public function sendTransaction($request, $beneficiary)
    {
        $apiUrl = $this->baseUrl . '/SendTransaction'; 
		$method = 'post'; 
		$requestTimestamp = $request->timestamp;
		$orderId = $request->order_id;
		$user = Auth::user();
		
		$senderFirstname = $user->first_name ?? '';
		$senderLastname = $user->last_name ?? '';
		$senderMobile = $user->formatted_number ? ltrim(trim($user->formatted_number), '+') : '';
		$senderCountry = $user->country->iso3 ?? '';
		
		$aggregatorCurrencyAmount = (int) round($request->aggregatorCurrencyAmount); 
		
		$requestBody = [
			"agentSessionId" => (string) $requestTimestamp,
			"agentTxnId" => $orderId,
			"locationId" => $beneficiary['bankId'],
			"remitterType" => $beneficiary['remittertype'] ?? '',
			"senderFirstName" => $senderFirstname,
			"senderMiddleName" => "",
			"senderLastName" => $senderLastname,
			"senderGender" => $beneficiary['sendergender'] ?? '',
			"senderAddress" => $beneficiary['senderaddress'] ?? '',
			"senderCity" => $beneficiary['sendercity'] ?? '',
			"senderState" => $beneficiary['senderstate'] ?? '',
			"senderZipCode" => $beneficiary['senderzipcode'] ?? '',
			"senderCountry" => $senderCountry,
			"senderMobile" => $senderMobile,
			"SenderNationality" => $senderCountry,
			"senderIdType" => $beneficiary['senderidtype'] ?? '',
			"senderIdTypeRemarks" => $beneficiary['senderidtyperemarks'] ?? '',
			"senderIdNumber" => $beneficiary['senderidnumber'] ?? '',
			"senderIdIssueCountry" => $beneficiary['senderidissuecountry'] ?? '',
			"senderIdIssueDate" => $beneficiary['senderidissuedate'] ?? '',
			"senderIdExpireDate" => $beneficiary['senderidexpiredate'] ?? '',
			"senderDateOfBirth" => $beneficiary['senderdateofbirth'] ?? '',
			"senderOccupation" => $beneficiary['senderoccupation'] ?? '',
			"senderOccupationRemarks" => $beneficiary['senderoccupationremarks'] ?? '',
			"senderSourceOfFund" => $beneficiary['sendersourceoffund'] ?? '',
			"senderSourceOfFundRemarks" => $beneficiary['sendersourceoffundremarks'] ?? '',
			"senderEmail" =>  $beneficiary['senderemail'] ?? '',
			"senderNativeFirstname" => $beneficiary['senderNativeFirstname'] ?? '',
			"senderBeneficiaryRelationship" => $beneficiary['senderbeneficiaryrelationship'] ?? '',
			"senderBeneficiaryRelationshipRemarks" => $beneficiary['senderbeneficiaryrelationshipremarks'] ?? '',
			"purposeOfRemittance" => $beneficiary['purposeofremittance'] ?? '',
			"purposeOfRemittanceRemark" => $beneficiary['purposeofremittanceremark'] ?? '',
			"beneficiaryType" => $beneficiary['beneficiarytype'] ?? '',
			"receiverFirstName" => $beneficiary['receiverfirstname'] ?? '',
			"receiverMiddleName" => "",
			"receiverLastName" => $beneficiary['receiverlastname'] ?? '',
			"receiverAddress" => $beneficiary['receiveraddress'] ?? '',
			"receiverContactNumber" => $beneficiary['receivercontactnumber'] ?? '',
			"receiverState" => $beneficiary['receiverstate'] ?? '',
			"receiverAreaTown" => $beneficiary['receiverareatown'] ?? '',
			"receiverCity" => $beneficiary['receivercity'] ?? '',
			"receiverCountry" => $beneficiary['payoutCountry'] ?? '',
			"receiverIdType" => $beneficiary['receiveridtype'] ?? '',
			"receiverIdTypeRemarks" => $beneficiary['receiveridtyperemarks'] ?? '',
			"receiverOccupation" => $beneficiary['receiveroccupation'] ?? '',
			"receiverOccupationRemark" => $beneficiary['receiveroccupationremark'] ?? '',
			"receiverIdNumber" => $beneficiary['receiveridnumber'] ?? '',
			"receiverEmail" => $beneficiary['receiveremail'] ?? '',
			"receiverNativeFirstname" => "",
			"receiverNativeMiddleName" => "",
			"receiverNativeLastname" => "",
			"senderSecondaryIdType" => "",
			"senderSecondaryIdNumber" => "",
			"senderNativeLastname" => "",
			"calcBy" => "P",
			"transferAmount" => (string) $aggregatorCurrencyAmount,
			"remitCurrency" => $this->defaultCurrency,
			"payoutCurrency" => $beneficiary['payoutCurrency'] ?? '',
			"paymentMode" => "B",
			"bankName" => $beneficiary['bankName'] ?? '',
			"bankBranchName" => $beneficiary['bankbranchname'] ?? '',
			"bankBranchCode" => $beneficiary['bankbranchcode'] ?? '',
			"bankAccountNumber" => $beneficiary['bankaccountnumber'] ?? '',
			"swiftCode" => $beneficiary['swiftcode'] ?? '',
			"promotionCode" => "",
			"SenderNativeAddress" => "",
			"ReceiverNationality" => $beneficiary['payoutCountry'] ?? '',
			"receiverIdIssueDate" => $beneficiary['receiveridissuedate'] ?? '',
			"receiverIdExpireDate" => $beneficiary['receiveridexpiredate'] ?? '',
			"receiverDistrict" => $beneficiary['receiverdistrict'] ?? '',
			"receiptCpf" => $beneficiary['receiptcpf'] ?? '',
			"remarks" => $request->notes,
			"receiverDateOfBirth" => $beneficiary['receiverdateofbirth'] ?? '',
			"receiverAccountType" => ""
		]; 
		$signatureString = $this->hmacAuthGenerate($method, $apiUrl, $requestTimestamp, $requestBody);
        $response = Http::withHeaders([
            'Authorization' => "hmacauth {$signatureString}",
            'Content-Type' => 'application/json',
        ])
		->withOptions([
			'verify' => false,
		])
		->{$method}($apiUrl, $requestBody);
		 
        // Handle Successful Response
		if ($response->successful()) {
			return [
				'success' => true,  
				'request' => $requestBody,
				'response' => $response->json()
			];
		}
		return [
			'success' => false, 
			'request' => $requestBody,
			'response' => json_decode($response->body(), true)
		];	
	}
	
	public function commitTransaction($confirmationId, $remitCurrency)
    {
        $apiUrl = $this->baseUrl . '/CommitTransaction'; 
		$method = 'post'; 
		$requestTimestamp = time();  
		 
		$requestBody = [
			"agentSessionId" => (string) $requestTimestamp,
			"confirmationId" => $confirmationId,
			"remitCurrency" => $remitCurrency
		]; 
		
		$signatureString = $this->hmacAuthGenerate($method, $apiUrl, $requestTimestamp, $requestBody);
        $response = Http::withHeaders([
            'Authorization' => "hmacauth {$signatureString}",
            'Content-Type' => 'application/json',
        ])
		->withOptions([
			'verify' => false,
		])
		->{$method}($apiUrl, $requestBody);
		 
        // Handle Successful Response
		if ($response->successful()) {
			return [
				'success' => true,  
				'request' => $requestBody,
				'response' => $response->json()
			];
		}
		return [
			'success' => false, 
			'request' => $requestBody,
			'response' => json_decode($response->body(), true)
		];	
	}
	
	public function getTXNStatus($orderId)
    {
		$apiUrl = $this->baseUrl . '/QueryTXNStatus'; 
		$method = 'post'; 
		$requestTimestamp = time();  
		 
		$requestBody = [
			"agentSessionId" => (string) $requestTimestamp,
			"agentTxnId" => $orderId
		]; 
		
		$signatureString = $this->hmacAuthGenerate($method, $apiUrl, $requestTimestamp, $requestBody);
        $response = Http::withHeaders([
            'Authorization' => "hmacauth {$signatureString}",
            'Content-Type' => 'application/json',
        ])
		->withOptions([
			'verify' => false,
		])
		->{$method}($apiUrl, $requestBody);
		 
        // Handle Successful Response
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
	
	public function getRateHistory($payoutCountry, $payoutCurrency, $currentDate)
    {
		$apiUrl = $this->baseUrl . '/getratehistory'; 
		$method = 'post'; 
		$requestTimestamp = time();  
		 
		$requestBody = [
			"agentSessionId" => (string) $requestTimestamp,
			"payoutCountry" => (string) $payoutCountry,
			"payoutCurrency" => (string) $payoutCurrency,
			"date" => (string) $currentDate
		]; 
		
		$signatureString = $this->hmacAuthGenerate($method, $apiUrl, $requestTimestamp, $requestBody);
        $response = Http::withHeaders([
            'Authorization' => "hmacauth {$signatureString}",
            'Content-Type' => 'application/json',
        ])
		->withOptions([
			'verify' => false,
		])
		->{$method}($apiUrl, $requestBody);
		 
        // Handle Successful Response
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
	
	public function getEcho($data)
	{
		$apiUrl = $data['lightnet_url'] . '/GetEcho';
		$method = 'post';
		$requestTimestamp = time(); // Use this for consistent timestamp
		 
		$requestBody = [
			"agentSessionId" => (string) $requestTimestamp
		];

		$jsonRequestBody = json_encode($requestBody);
		$requestUri = strtolower(urlencode($apiUrl));

		$requestHttpMethod = strtoupper($method);

		$nonce = Str::uuid()->toString();

		$requestContentBase64String = '';
		if (!empty($jsonRequestBody)) {
			$requestContentHash = md5($jsonRequestBody, true);
			$requestContentBase64String = base64_encode($requestContentHash);
		}

		// Corrected $requestTimestamp usage
		$signatureRawData = sprintf(
			'%s%s%s%s%s%s',
			$data['lightnet_apikey'],
			$requestHttpMethod,
			$requestUri,
			$requestTimestamp, // Corrected here
			$nonce,
			$requestContentBase64String
		);

		$secretKeyByteArray = base64_decode($data['lightnet_secretkey']);
		$signatureBytes = hash_hmac('sha256', $signatureRawData, $secretKeyByteArray, true);
		$requestSignatureBase64String = base64_encode($signatureBytes);

		$signatureString = sprintf(
			'%s:%s:%s:%s',
			$data['lightnet_apikey'],
			$requestSignatureBase64String,
			$nonce,
			$requestTimestamp // Corrected here
		);

		$response = Http::withHeaders([
			'Authorization' => "hmacauth {$signatureString}",
			'Content-Type' => 'application/json',
		])
		->withOptions([
			'verify' => false, // Avoid SSL verification issues
		])
		->{$method}($apiUrl, $requestBody);

		// Handle Successful Response
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
	
	public function getAgentList($request)
	{
		$timestamp = time();
		$body = [
			'agentSessionId' => (string) $timestamp,
			'paymentMode' => 'B',
			'payoutCountry' => (string) $request->payoutCountry,
		];

		// Call the service API
		$response = $this->serviceApi('post', '/GetAgentList', $timestamp, $body);
		
		// Initialize the output
		if (!isset($response['success']) || !$response['success'] && $response['response']['code'] !== 0) {
			return '<option value="">No banks available</option>';
		} 
		// Process bank list
		$banks = $response['response']['locationDetail'] ?? [];
		
		$output = '<option value="">Select Bank Name</option>';
		foreach ($banks as $bank) {
			$output .= sprintf(
				'<option value="%s" data-bank-name="%s">%s</option>',
				htmlspecialchars($bank['locationId'] ?? '', ENT_QUOTES, 'UTF-8'),
				htmlspecialchars($bank['locationName'] ?? '', ENT_QUOTES, 'UTF-8'),
				htmlspecialchars($bank['locationName'] ?? '', ENT_QUOTES, 'UTF-8')
			);
		}
		return $output;

	}
	
	public function getAgentLists($request)
	{
		$timestamp = time();
		$body = [
			'agentSessionId' => (string) $timestamp,
			'paymentMode' => 'B',
			'payoutCountry' => (string) $request->payoutCountry,
		];

		// Call the service API
		$response = $this->serviceApi('post', '/GetAgentList', $timestamp, $body);
		
		// Initialize the output
		if (!isset($response['success']) || !$response['success'] && $response['response']['code'] !== 0) {
			return [];
		} 
		// Process bank list
		$banks = $response['response']['locationDetail'] ?? []; 
		return $banks; 
	} 
}