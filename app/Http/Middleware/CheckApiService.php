<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Traits\ApiServiceResponseTrait;

class CheckApiService
{
	use ApiServiceResponseTrait;  	
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
	{
		$user = auth()->user();

		if (!$user) {
			return $this->errorResponse('Unauthorized', 'ERR_UNAUTHORIZED', 401);
		}

		// Detect service type from route prefix
		$service = null;
		if ($request->is('api-service/transfer-bank/*')) {
			$service = 'bank_transfer';
		} elseif ($request->is('api-service/transfer-money/*')) {
			$service = 'mobile_money';
		}

		if (!$service) {
			return $this->errorResponse('Unknown service', 'ERR_UNKNOWN_SERVICE', 401);
		}

		// Check if user has API credential and service enabled
		if (!$user->apicredential || !in_array($service, (array) $user->apicredential->services)) {
			return $this->errorResponse(
				ucfirst(str_replace('_', ' ', $service)) . ' service is disabled for this client.',
				'ERR_SERVICE_DISABLED',
				403
			);
		}

		return $next($request);
	}

}
