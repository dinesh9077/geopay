<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckKycStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if the user is authenticated and KYC is incomplete
        if (Auth::check() && (Auth::user()->is_kyc_verify !== 1)) {
            $is_company = Auth::user()->is_company;
            
            // Define allowed routes for accessing KYC pages
            $routesArr = $is_company == 1 ? ['corporate.kyc', 'corporate.kyc.submit-step', 'corporate.kyc.submit-final', 'corporate.kyc.document-store'] : ['metamap.kyc', 'metamap.kyc-check-status'];
            $redirectUrl = $is_company == 1
                ? redirect()->route('corporate.kyc')->with('message', 'Please complete your KYC verification.')
                : redirect()->route('metamap.kyc')->with('message', 'Please complete your KYC verification.');

            // Redirect if the current route is not in the allowed KYC routes
            if (!in_array($request->route()->getName(), $routesArr)) {
                return $redirectUrl;
            }
        } 

        return $next($request);
    }
}
