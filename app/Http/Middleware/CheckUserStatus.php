<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckUserStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
		// Check if user is authenticated and status is 0
        if (Auth::check() && Auth::user()->status == 0) {
            Auth::logout(); // Logout the user
            return redirect()->route('login')->with('error', 'This user account is inactive. Please reach out to the administrator for further details.');
        }
		
        return $next($request);
    }
}
