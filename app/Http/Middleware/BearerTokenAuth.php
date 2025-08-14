<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response; 
use App\Services\OpaqueToken; 
use App\Http\Traits\ApiServiceResponseTrait;
class BearerTokenAuth
{
	use ApiServiceResponseTrait;  	
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $header = $request->header('Authorization', '');
        $bearer = str_starts_with($header, 'Bearer ') ? substr($header, 7) : null;

        $token = OpaqueToken::validate($bearer);
        if (!$token) {
			$errorCode = request()->attributes->get('auth_error', 'ERR_TOKEN_UNKNOWN'); 
			 
			return $this->errorResponse(match ($errorCode) {
				'ERR_TOKEN_MISSING' => 'Token is missing from the request.',
				'ERR_TOKEN_FORMAT'  => 'Token format is invalid.',
				'ERR_TOKEN_NOT_FOUND' => 'Token not found.',
				'ERR_TOKEN_EXPIRED' => 'Your session has expired. Please log in again.',
				'ERR_TOKEN_INVALID' => 'Token verification failed.',
				default => 'Unauthorized access.'
			}, $errorCode, 401); 
        }

        auth()->setUser($token->user);
        $request->attributes->set('access_token', $token);

        return $next($request);
    }
}
