<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\App;

class EnsureTokenIsPresent
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
        // Check if the Authorization header with Bearer token is present
        if (!$request->hasHeader('Authorization')) {

            return response()->json([
				'success' => false, 
				'message' => 'Authorization Bearer token is required',
            ], 401);
        }
		
        $user = Auth::user(); 
        App::singleton('authUser', function () use ($user) {
            return $user;
        });
		
        return $next($request);
    }
}
