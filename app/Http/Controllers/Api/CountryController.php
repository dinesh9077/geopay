<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller; 
use Illuminate\Http\Request;
use App\Models\Country;
use App\Http\Traits\ApiResponseTrait;
use App\Services\LiquidNetService;

class CountryController extends Controller
{
    use ApiResponseTrait;
    /**
     * Get all countries with URL for country_flag images.
     *
     * @return \Illuminate\Http\JsonResponse
     */
	protected $liquidNetService;

    public function __construct(LiquidNetService $liquidNetService)
    {
        $this->liquidNetService = $liquidNetService;
    }
	
	public function liquidnet()
    {
        $url = '/api/webservice/GetEcho';
        $method = 'post';
        $body = ['agentSessionId' => '67898987979']; // Optional payload

        $response = $this->liquidNetService->hmacAuthGenerate($method, $url, $body);

        return response()->json($response);
    }
	
    public function index()
    {
        $countries = Country::select('id', 'name', 'isdcode', 'country_flag')->get();

        $countriesWithFlags = $countries->transform(function ($country) {
            if ($country->country_flag) {
                $country->country_flag = asset('country/' . $country->country_flag);
            } 
            return $country;
        });
		
        return $this->successResponse("you've successfully fetched the country data!", $countriesWithFlags); 
    }
}
