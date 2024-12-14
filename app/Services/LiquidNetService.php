<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class LiquidNetService
{
    protected $appId;
    protected $apiKey;

    public function __construct()
    {
        // Set the APP ID and API Key (You can also use environment variables)
        $this->appId = 'geoapi'; // or env('LIQUIDNET_APP_ID')
        $this->apiKey = 'ZjM5ZmQzYjUzY2FmNDk4YThlYWJlOTBhOGQ2NWQwYTQ='; // or env('LIQUIDNET_API_KEY')
		$this->baseUrl = 'https://staging-api-lqn.lightnet.io';
    }
 
    public function hmacAuthGenerate(string $method, string $url, string $timestamp, array $body = [])
    {
		$url = $this->baseUrl . $url;
		
        // Step 1: HTTP Method (Uppercase)
        $httpMethod = strtoupper($method);
		
        // Step 2: Nonce (Unique identifier for each request)
        $nonce = Str::uuid()->toString();

        // Step 3: Request URI (Encoded and Lowercase)
        $requestUri = urlencode(strtolower($url));
  
        // Step 5: Base64 Representation of the Request Payload (Body)
        $payloadBase64 = '';
        if (!empty($body)) {
            $bodyJson = json_encode($body); // Convert body to JSON
            $hashedPayload = md5($bodyJson); // MD5 hash the JSON payload
            $payloadBase64 = base64_encode($hashedPayload); // Convert hash to Base64
        }
	
        // Step 6: Build the Signature Raw Data
        $signatureRawData = $this->appId . $httpMethod . $requestUri . $timestamp . $nonce . $payloadBase64;
	
        // Step 7: Generate the HMAC Signature
        $hmacSignature = base64_encode(hash_hmac('sha256', $signatureRawData, base64_decode($this->apiKey), true));

        // Step 8: Set the Authorization Header
        $authorizationHeader = "hmacauth {$this->appId}:{$hmacSignature}:{$nonce}:{$timestamp}";
		 
		// Step 9: Send the HTTP Request
        $response = Http::withHeaders([
            'Authorization' => $authorizationHeader,
            'Content-Type' => 'application/json',
        ])->$method($url, $body);

        // Step 10: Return the Response
        return $response->json();
    }
}
