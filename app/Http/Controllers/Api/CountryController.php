<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller; 
use Illuminate\Http\Request;
use App\Models\Country;
use App\Http\Traits\ApiResponseTrait;

class CountryController extends Controller
{
    use ApiResponseTrait;
    /**
     * Get all countries with URL for country_flag images.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $countries = Country::select('id', 'name', 'isdcode', 'country_flag')->get();

        $countriesWithFlags = $countries->transform(function ($country) {
            if ($country->country_flag) {
                $country->country_flag = asset('country/' . $country->country_flag);
            } 
            return $country;
        });
		
        return $this->successResponse("you've successfully fetched the country data!", "countries", $countriesWithFlags); 
    }
}
