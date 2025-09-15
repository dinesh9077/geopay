<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class DepositPaymentService
{
    protected string $endPoint;
    protected string $merchantSiteKey;
    protected string $merchantSiteSecret;
    protected string $currency;

    public function __construct()
    {
        $this->endPoint          = config('services.deposit.endpoint', 'https://ggapi.ibanera.com/v1/payment/deposit');
        $this->merchantSiteKey   = config('services.deposit.merchant_key', 'geo-payments-3d-test-29-sa6x1yyb');
        $this->merchantSiteSecret= config('services.deposit.merchant_secret', 'a2327435d3763214d30c97edb189866c8283b8486717c667595b569b99aae6b8');
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
            "redirecturl"       => route('deposit.payment-return'),
            "callbackurl"       => route('deposit.payment-callback'),
            "ipaddress"         => request()->ip(),
            "browseragent"      => request()->header('User-Agent'),
        ];
	 
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post($this->endPoint, $payload);

        return $response->json();
    }
}
