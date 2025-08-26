<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAdminRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user(); // Get the authenticated user 
        // Check if the user is not an admin
        if ($user && $user->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Access denied: You do not have the necessary permissions to access this resource.'
            ], 403);
        }

        return $next($request); // Allow the request to proceed if authorized
    }
}
