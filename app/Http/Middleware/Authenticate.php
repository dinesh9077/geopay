<?php
	
	namespace App\Http\Middleware;
	
	use Illuminate\Auth\Middleware\Authenticate as Middleware;
	use Illuminate\Http\Request;
	use Illuminate\Auth\AuthenticationException;
	class Authenticate extends Middleware
	{
		/**
			* Get the path the user should be redirected to when they are not authenticated.
		*/
		protected function redirectTo(Request $request)
		{  
			if ($request->expectsJson()) {
				// Return null to bypass redirection
				return response()->json([
				'success' => false,
				'message' => 'Unauthorized. Please check your token and try again.',
				], 401);
			}
		}
		
		/**
			* Handle an unauthenticated user.
			*
			* @param  \Illuminate\Http\Request  $request
			* @param  array  $guards
			* @throws \Illuminate\Auth\AuthenticationException
		*/
		protected function unauthenticated($request, array $guards)
		{
			// If the request expects JSON, throw an AuthenticationException
			if ($request->expectsJson()) {
				throw new AuthenticationException(
                'Unauthorized. Please check your token and try again.', $guards
				);
			}
			
			// Otherwise, redirect to login (web request)
			parent::unauthenticated($request, $guards);
		}
	}
