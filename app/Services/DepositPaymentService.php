<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Log;
class DepositPaymentService
{
    protected string $endPoint;
    protected string $merchantSiteKey;
    protected string $merchantSiteSecret;
    protected string $currency;

    public function __construct()
    {
        $this->baseUrl          = config('setting.guardian_endpoint');
        $this->merchantSiteKey   = config('setting.guardian_merchant_key');
        $this->merchantSiteSecret= config('setting.guardian_merchant_secret');
        $this->currency          = config('setting.default_currency', 'USD');
    }

    /**
     * Generate Secure Hash
     */
    protected function generateSecureHash($timestamp, $amount): string
    {  
        $dataToHash = $this->merchantSiteKey . '|' . $timestamp . '|' . $amount . '|' . $this->currency . '|' . $this->merchantSiteSecret;  
        return hash('sha256', $dataToHash);
    }

    /**
     * Make deposit payment request
     */
    public function deposit($user, array $card, float $amount, string $orderId)
    {
        $timestamp = now()->timestamp * 1000; // milliseconds 
        $secureHash = $this->generateSecureHash($timestamp, $amount);
		
		$baseUrl = $this->baseUrl.'/v1/payment/deposit';
		
        $payload = [
            "merchant_site_key" => $this->merchantSiteKey,
            "securehash"        => $secureHash,
            "first_name"        => $user['first_name'],
            "last_name"         => $user['last_name'],
            "email"             => $user['email'],
            "phone"             => $user['phone'],
            "address"           => $user['address'],
            "city"              => $user['city'],
            "state"             => $user['state'],
            "postalcode"        => $user['postalcode'],
            "country"           => $user['country'],
            "timestamp"         => $timestamp,
            "cardtype"          => $card['cardtype'],
            "cardname"          => $card['cardname'],
            "cardnumber"        => $card['cardnumber'],
            "month"             => $card['month'],
            "year"              => $card['year'],
            "cvv"               => $card['cvv'],
            "currency"          => $this->currency,
            "amount"            => $amount,
            "merchant_orderid"  => $orderId, 
            "redirecturl"       => route('deposit.payment'),
            "callbackurl"       => route('deposit.payment-callback'),
            "ipaddress"         => request()->ip(),
            "browseragent"      => request()->header('User-Agent'),
        ];

        Log::info('Payment payload:', $payload);

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post($baseUrl, $payload);

        // Handle Successful Response
		if ($response->successful()) {
			return [
				'success' => true,
				'request' => $payload,
				'response' => $response->json()
			];
		}
		
		return [
			'success' => false,
			'request' => $payload,
			'response' => json_decode($response->body(), true)
		];	
    }
}
