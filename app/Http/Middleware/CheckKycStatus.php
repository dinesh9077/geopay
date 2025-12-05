<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckKycStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // If user is not logged in, let the request pass
        if (!Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();
        $currentRoute = $request->route() ? $request->route()->getName() : null;
        $currentPath = $request->path(); // fallback to path checks if route name is not set

        // Determine allowed KYC routes based on user type
        if ($user->is_company == 1) {
            $allowedKycRoutes = [
                'corporate.kyc',
                'corporate.kyc.submit-step',
                'corporate.kyc.submit-final',
                'corporate.kyc.document-store',
                'corporate.kyc.document-type',
                'corporate.kyc.director',
            ];
            $kycRedirect = redirect()->route('corporate.kyc')->with('message', 'Please complete your KYC verification.');
        } else {
            $allowedKycRoutes = [
                'metamap.kyc',
                'metamap.kyc-finished',
                'metamap.kyc-check-status',
            ];
            $kycRedirect = redirect()->route('metamap.kyc')->with('message', 'Please complete your KYC verification.');
        }

        // Always allow the KYC routes themselves and the basic-details page (to avoid loop)
        $allowedKycPaths = [
            'user/basic-details', 
        ];

        // 1) If KYC incomplete -> redirect to KYC unless the current route/path is allowed
        if ($user->is_kyc_verify !== 1) {
            $isAllowedRoute = $currentRoute && in_array($currentRoute, $allowedKycRoutes);
            $isAllowedPath = in_array($currentPath, $allowedKycPaths);

            if (!$isAllowedRoute && !$isAllowedPath) {
                return $kycRedirect;
            }
        }
       
        // 2) If KYC done but both email and mobile not verified -> redirect to basic-details
        if ($user->is_kyc_verify == 1 && $user->is_email_verify == 1 && $user->is_mobile_verify == 0)
        { 
            // avoid redirect loop: allow if we're already on basic-details
            if (!($currentRoute === 'user.basic-details' || $currentPath === 'user/basic-details')) { 
                return redirect()->to('/user/basic-details'); 
            }
        }

        return $next($request);
    }
}
