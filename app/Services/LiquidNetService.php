<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Crypt; // Remove Crypt facade if not used

class LiquidNetService
{
    protected $appId;
    protected $apiKey;
    protected $baseUrl;

    public function __construct()
    {
        $this->appId = 'geoapi';
        $this->apiKey = 'ZjM5ZmQzYjUzY2FmNDk4YThlYWJlOTBhOGQ2NWQwYTQ='; // Assuming API key is stored in environment variable
        $this->baseUrl = 'https://staging-api-lqn.lightnet.io';
    }

    public function hmacAuthGenerate(string $method, string $url, string $timestamp, array $body = [])
    {
        $url = $this->baseUrl . $url;

        // Step 1: HTTP Method (Uppercase)
        $httpMethod = strtoupper($method);

        // Step 2: Nonce (Unique identifier for each request)
        $nonce = $timestamp;

        // Step 3: Request URI (Encoded and Lowercase)
        $requestUri = urlencode(strtolower($url));

        // Step 5: Base64 Representation of the Request Payload (Body)
        $payloadBase64 = '';
        if (!empty($body)) { 
            $hashedPayload = md5(json_encode($body), true); // Hash JSON payload
            $payloadBase64 = base64_encode($hashedPayload);
        }

        // Step 6: Build the Signature Raw Data
        $signatureRawData = $this->appId . $httpMethod . $requestUri . $timestamp . $nonce . $payloadBase64;
		/* echo json_encode($body);
		echo '<br>';
		echo $signatureRawData;
		die; */
        // Step 7: Generate the HMAC Signature
        $apiSecret = base64_decode($this->apiKey);
        $hmacSignature = base64_encode(hash_hmac('sha256', $signatureRawData, $apiSecret, true));

        // Step 8: Set the Authorization Header
        $authorizationHeader = "hmacauth {$this->appId}:{$hmacSignature}:{$nonce}:{$timestamp}";

        // Step 9: Send the HTTP Request
        try { 
            $response = Http::withHeaders([
                'Authorization' => $authorizationHeader,
                'Content-Type' => 'application/json',
            ])->{$method}($url, $body);

            return $response->json();
        } catch (\Exception $e) {
            // Handle request error
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}