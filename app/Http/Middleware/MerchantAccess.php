<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MerchantAccess
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
	{
		$user = Auth::user();

		if ($user && $user->is_merchant == 1) 
		{
			// Allowed routes for merchants
			$allowedRoutes = [
				'home', 
				'transaction-list', 
				'password-change', 
				'profile-update', 
				'basic-info-update', 
				'transaction-ajax',
				'transaction-receipt/*',
				'transaction-receipt-pdf/*',
				'setting',
				'api-credentials',
				'api-documentation',
			];

			foreach ($allowedRoutes as $route) {
				if ($request->is($route)) {
					return $next($request);
				}
			}
 
			return redirect()->route('home')->with('error', 'Access denied.');
		}
		return $next($request);
	}

}
