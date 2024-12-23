<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Auth; 
use Carbon\Carbon;

class CheckPasswordChange
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
		$user = Auth::user();

        if ($user && $user->password_changed_at) {
            // Check if the session was created before the password was last changed
            $lastPasswordChange = $user->password_changed_at;
			if (!$lastPasswordChange instanceof Carbon) {
                $lastPasswordChange = Carbon::parse($lastPasswordChange);
            }  
            $sessionCreationTime = session('login_time');
            if (!$sessionCreationTime || $lastPasswordChange->greaterThan($sessionCreationTime)) {
                Auth::logout();
                session()->flush();
                return redirect()->route('login')->with('error', 'Your session has expired due to a password change. Please log in again.');
            }
        } 
		
        return $next($request);
    }
}
