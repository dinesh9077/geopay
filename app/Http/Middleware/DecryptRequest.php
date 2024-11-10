<?php
	
	namespace App\Http\Middleware;
	
	use Closure;
	use Illuminate\Http\Request;
	use Symfony\Component\HttpFoundation\Response;
	use Illuminate\Support\Facades\Crypt; 
	use Illuminate\Support\Facades\Log;
	class DecryptRequest
	{
		/**
			* Handle an incoming request.
			*
			* @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
		*/
		public function handle(Request $request, Closure $next): Response
		{ 
			try { 
				// Check if the request has the encrypted fields
				if ($request->isMethod('post')) 
				{ 
					if(!$request->has('encrypted_data'))
					{
						return response()->json(['status' => 'error', 'message' => 'encrypted data is invalid.']); 
					}
					
					$secretKey = env('ENCRYPTION_SECRET_KEY'); 
					// Decrypt the encrypted_data field 
					$decryptedData = openssl_decrypt(
						base64_decode($request->input('encrypted_data')),      // Decode the base64 encoded data
						'AES-256-ECB',                      // Encryption method
						base64_decode($secretKey),           // The key needs to be base64 decoded
						OPENSSL_RAW_DATA                    // Use raw data
					);
					
					// Optionally, decode the decrypted JSON data
					$decryptedArray = json_decode($decryptedData, true);
					 
					// Check if the decryption was successful and merge the decrypted array 
					if ($decryptedArray === null || !is_array($decryptedArray)) { 
						return response()->json(['success' => false, 'message' => 'encrypted data is invalid.']); 
					}
					
					$request->merge($decryptedArray);
					
					// Remove the 'encrypted_data' key from the request
					$request->request->remove('encrypted_data');
				} 
			} catch (\Throwable $e) {
				// Log any decryption errors
				Log::error('Decryption failed: ' . $e->getMessage());
				return response()->json(['success' => false, 'message' => $e->getMessage()]); 
			}
			return $next($request);
		} 
	}
