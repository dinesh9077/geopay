<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Traits\ApiServiceResponseTrait;

class IpWhitelist
{
	use ApiServiceResponseTrait;	
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if (!$user) {
            return $this->errorResponse('Unauthorized', 'ERR_UNAUTHORIZED', 401);
        }

        $ip = $request->ip();
		 
        $whitelists = $user->ipWhitelists; 
		if ($whitelists->isNotEmpty() && !$whitelists->pluck('ip_address')->contains($ip)) { 
			return $this->errorResponse(
				'Access denied. Your IP address is not whitelisted.',
				'ERR_IP_NOT_ALLOWED',
				403
			); 
        }

        return $next($request);
    } 
}
