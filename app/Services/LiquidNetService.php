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
		
		$payoutCurrencyAmount = (int) $request->payoutCurrencyAmount;
		 
		$requestBody = [
			"agentSessionId" => (string) $requestTimestamp,
			"agentTxnId" => $orderId,
			"locationId" => $beneficiary['bankId'],
			"remitterType" => "B",
			"senderFirstName" => $user->first_name,
			"senderMiddleName" => "",
			"senderLastName" => $user->last_name,
			"senderGender" => "",
			"senderAddress" => $beneficiary['beneficiaryAddress'],
			"senderCity" => "",
			"senderState" => "",
			"senderZipCode" => "",
			"senderCountry" => $beneficiary['payoutCountry'],
			"senderMobile" => ltrim(trim($user->formatted_number), '+'),
			"SenderNationality" => $beneficiary['payoutCountry'],
			"senderIdType" => $beneficiary['receiverIdType'],
			"senderIdTypeRemarks" => $beneficiary['receiverIdTypeRemarks'],
			"senderIdNumber" => "",
			"senderIdIssueCountry" => $beneficiary['payoutCountry'],
			"senderIdIssueDate" => "",
			"senderIdExpireDate" => "",
			"senderDateOfBirth" => "",
			"senderOccupation" => "",
			"senderOccupationRemarks" => "",
			"senderSourceOfFund" => $beneficiary['senderSourceOfFund'],
			"senderSourceOfFundRemarks" => $beneficiary['senderSourceOfFundRemarks'],
			"senderEmail" =>  $user->email,
			"senderNativeFirstname" => "",
			"senderBeneficiaryRelationship" => $beneficiary['senderBeneficiaryRelationship'],
			"senderBeneficiaryRelationshipRemarks" => $beneficiary['senderBeneficiaryRelationshipRemarks'],
			"purposeOfRemittance" => $beneficiary['purposeOfRemittance'],
			"purposeOfRemittanceRemark" => $beneficiary['purposeOfRemittanceRemark'],
			"beneficiaryType" => $beneficiary['beneficiaryType'],
			"receiverFirstName" => $beneficiary['beneficiaryFirstName'],
			"receiverMiddleName" => "",
			"receiverLastName" => $beneficiary['beneficiaryLastName'],
			"receiverAddress" => $beneficiary['beneficiaryAddress'],
			"receiverContactNumber" => $beneficiary['beneficiaryMobile'],
			"receiverState" => $beneficiary['beneficiaryState'],
			"receiverAreaTown" => "",
			"receiverCity" => "",
			"receiverCountry" => $beneficiary['payoutCountry'],
			"receiverIdType" => $beneficiary['receiverIdType'],
			"receiverIdTypeRemarks" => $beneficiary['receiverIdTypeRemarks'],
			"receiverOccupation" => "",
			"receiverOccupationRemark" => "",
			"receiverIdNumber" => "",
			"receiverEmail" => $beneficiary['beneficiaryEmail'],
			"receiverNativeFirstname" => "",
			"receiverNativeMiddleName" => "",
			"receiverNativeLastname" => "",
			"senderSecondaryIdType" => "",
			"senderSecondaryIdNumber" => "",
			"senderNativeLastname" => "",
			"calcBy" => "P",
			"transferAmount" => (string) $payoutCurrencyAmount,
			"remitCurrency" => Config('setting.default_currency'),
			"payoutCurrency" => $beneficiary['payoutCurrency'],
			"paymentMode" => "B",
			"bankName" => $beneficiary['bankName'],
			"bankBranchName" => "",
			"bankBranchCode" => "",
			"bankAccountNumber" => $beneficiary['bankAccountNumber'],
			"swiftCode" => "",
			"promotionCode" => "",
			"SenderNativeAddress" => "",
			"ReceiverNationality" => $beneficiary['payoutCountry'],
			"receiverIdIssueDate" => "",
			"receiverIdExpireDate" => $beneficiary['receiverIdExpireDate'],
			"receiverDistrict" => "",
			"receiptCpf" => "",
			"remarks" => $request->notes,
			"receiverDateOfBirth" => $beneficiary['receiverDateOfBirth'],
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

}