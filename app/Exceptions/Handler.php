<?php
	
	namespace App\Exceptions;
	
	use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
	use Throwable;
	use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
	use Illuminate\Auth\AuthenticationException;
	use Illuminate\Session\TokenMismatchException; 
	
	class Handler extends ExceptionHandler
	{ 
		/**
			* The list of the inputs that are never flashed to the session on validation exceptions.
			*
			* @var array<int, string>
		*/
		protected $dontFlash = [
			'current_password',
			'password',
			'password_confirmation',
		];
		
		
		public function render($request, Throwable $exception)
		{
			if ($exception instanceof TokenMismatchException) {
				// Handle the CSRF token mismatch 
				return response()->json([
					'status' => 'error', 
					'message' => 'CSRF token mismatch. Please refresh the page and try again.'
				]); 
			}
			
			// Handle NotFoundHttpException for API requests
			if ($exception instanceof NotFoundHttpException) 
			{
			    // If the request is not an API request, abort with a 404 response
			    if ($request->expectsJson()) { 
    				return response()->json([
					'success' => false,
					'message' => 'URL not found. Please check the endpoint and try again.',
					], 404);
				} 
				return abort(404);
			}
			
			// Handle AuthenticationException for API requests
			if ($exception instanceof AuthenticationException) {
				return $this->unauthenticated($request, $exception);
			}
			
			// Use parent render method for all other exceptions
			return parent::render($request, $exception);
		}
		
		protected function unauthenticated($request, AuthenticationException $exception)
		{ 
			// Check if the request expects JSON
			if ($request->expectsJson()) {
				// Return JSON response for API requests
				return response()->json([
					'success' => false,
					'message' => 'Unauthorized. Please check your token and try again.',
				], 401);
			}

			// Otherwise, redirect to the login page for web requests
			return redirect()->route('login');
		}
 
		public function register(): void
		{
			$this->reportable(function (Throwable $e) {
				//
			});
		}
	}
