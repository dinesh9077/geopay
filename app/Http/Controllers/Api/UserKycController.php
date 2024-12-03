<?php
	
	namespace App\Http\Controllers\Api;
	
	use App\Http\Controllers\Controller;
	use Illuminate\Http\Request;
	use App\Models\UserKyc;
	use App\Models\User;
	use Illuminate\Support\Facades\Http;
	use Illuminate\Support\Facades\Storage;
	use App\Http\Traits\ApiResponseTrait; 
	use Validator, DB, Log;
	
	class UserKycController extends Controller
	{ 
		use ApiResponseTrait;
		
		public function verify(Request $request)
		{
			// Validate incoming request data
			$validator = Validator::make($request->all(), [
			'email' => 'required|email',
			'verification_id' => 'required|string',
			'identification_id' => 'required|string', 
			'meta_response' => 'nullable|array' 
			]);
			
			// Check for validation failures
			if ($validator->fails()) {
				return $this->validateResponse($validator->errors());
			}
			
			// Start database transaction
			DB::beginTransaction();
			
			try {
				// Retrieve the user based on the provided email
				$user = User::where('email', $request->email)->first();
				
				// Check if the user exists
				if (!$user) {
					return $this->errorResponse('The email not found.');
				}
				
				// Insert or update the KYC data
				$userKyc = UserKyc::updateOrCreate(
					['email' => $user->email], // Conditions to find existing record
					[
						'user_id' => $user->id,
						'verification_status' => 'pending',
						'verification_id' => $request->verification_id, // Laravel handles null automatically
						'identification_id' => $request->identification_id,
						'meta_response' => json_encode($request->meta_response), // Assuming meta_response is of JSON type
					]
				);
				
				// Commit the transaction
				DB::commit();
				
				return $this->successResponse('KYC verification successful.', $userKyc);
				} catch (\Throwable $e) {
				// Rollback the transaction in case of error
				DB::rollBack(); 
				return $this->errorResponse('Something went wrong while processing your request. Please try again later.');
			}
		} 
		
		public function getKYCVerification(Request $request)
		{  
			$data = $request->all();
			Log::info($data);
			if (empty($data['flowId']) || $data['flowId'] != config('setting.meta_verification_flow_id')) {
				return; // Exit if flowId is missing or does not match
			}

			// Ensure event type is correct before proceeding
			if (!in_array($data['eventName'], ['verification_started', 'verification_updated', 'verification_completed', 'step_completed'])) {
				return;
			}
			
			// Get verification ID from resource URL
			$verificationId = basename($data['resource']);
			
			if (in_array($data['eventName'], ["step_completed", "verification_started"]) && isset($data['metadata']['user_id'])) 
			{  
				$user_id = $data['metadata']['user_id'];
				$user_email = isset($data['metadata']['user_email']) ? $data['metadata']['user_email'] : null; // Check if 'user_email' exists
				$step_id = $data['step']['id'] ?? 'pending'; // Default to 'pending' if 'step_id' is not present
	
				// Update or create the KYC record
				UserKyc::updateOrCreate(
					['user_id' => $user_id], // Conditions to find existing record
					[
						'user_email' => $user_email,
						'verification_status' => $step_id, // Set the verification status
						'verification_id' => $verificationId, // Laravel handles null automatically 
						'meta_response' => json_encode($data), // Assuming meta_response is of JSON type 
					]
				);
				return;
			}
 
			// Fetch KYC detail from database
			$metaKycDetail = UserKyc::where('verification_id', $verificationId)->first();
			if (!$metaKycDetail) {
				return;
			}

			// Retrieve user ID associated with KYC
			$userId = $metaKycDetail->user_id;

			// Obtain access token
			$authResponse = Http::withOptions(['verify' => false])
				->withHeaders([
					'Content-Type' => 'application/x-www-form-urlencoded',
					'Authorization' => 'Basic ' . config('setting.meta_bearer')
				])
				->asForm()
				->post(config('setting.meta_host') . '/oauth', ['grant_type' => 'client_credentials']);

			if ($authResponse->failed()) {
				return;
			}

			$authToken = $authResponse->json()['access_token'];

			// Fetch verification details
			$verificationResponse = Http::withOptions(['verify' => false])
				->withToken($authToken)
				->get(config('setting.meta_host') . '/v2/verifications/' . $verificationId);

			if ($verificationResponse->failed()) {
				return;
			}

			$response = $verificationResponse->json();
			
			// Check if the status is 'rejected' or 'deleted'
			if (in_array($response['identity']['status'], ['rejected', 'deleted'])) {
				// Delete stored files (videos and documents)
				$this->deleteKYCFiles($userId); 
				User::whereId($userId)->update(['is_kyc_verify' => 0]);
				$metaKycDetail->delete();
				return;
			}
	
			// Process and store video
			$storedVideoUrl = $this->storeKYCVideo($response, $userId);

			// Process and store document images
			$documentImages = $this->storeKYCImages($response, $userId);

			// Update the KYC record in the database
			DB::transaction(function () use ($response, $documentImages, $storedVideoUrl, $data, $userId) {
				UserKyc::where('verification_id', $response['id']) 
				->update([
					'verification_status' => $response['identity']['status'],
					'identification_id' => $response['identity']['id'],
					'document' => json_encode($documentImages),
					'video' => $storedVideoUrl,
					'meta_response' => json_encode($data),
					'updated_at' => now()
				]);
					
				// Determine KYC verification status
				$isKycVerified = $response['identity']['status'] == "verified" ? 1 : 0;

				// Update the user record with KYC verification status
				User::whereId($userId)->update(['is_kyc_verify' => $isKycVerified]);
			});

			return;
		}

		/**
		 * Store KYC video and return its URL.
		 */
		private function storeKYCVideo($response, $userId)
		{
			$videoUrl = $response['steps'][0]['data']['videoUrl'] ?? null;
			if (!$videoUrl) {
				return null;
			}

			$videoContents = @file_get_contents($videoUrl);
			if ($videoContents) {
				$videoName = uniqid() . '.mp4';
				Storage::disk('public')->put("kyc-videos/{$userId}/{$videoName}", $videoContents);
				return Storage::url("kyc-videos/{$userId}/{$videoName}");
			}

			return null;
		}

		/**
		 * Store KYC document images and return their URLs.
		 */
		private function storeKYCImages($response, $userId)
		{
			$documentImages = [];

			foreach ($response['documents'] ?? [] as $document) {
				foreach ($document['photos'] ?? [] as $photoUrl) {
					$imageContents = @file_get_contents($photoUrl);
					if ($imageContents) {
						$imageName = uniqid() . '.jpg';
						Storage::disk('public')->put("kyc-documents/{$userId}/{$imageName}", $imageContents);
						$documentImages[] = Storage::url("kyc-documents/{$userId}/{$imageName}");
					}
				}
			}

			return $documentImages;
		}

		/**
		* Delete KYC video and document images associated with a user ID.
		*/
		private function deleteKYCFiles($userId)
		{
			Storage::disk('public')->deleteDirectory("kyc-videos/{$userId}");
			Storage::disk('public')->deleteDirectory("kyc-documents/{$userId}");
		}

		/* public function getKYCVerification(Request $request)
			{  
			Log::error($request->all());	
			die;
			// Obtain the access token
			$authResponse = Http::withOptions(['verify' => false])->withHeaders([
			'Content-Type' => 'application/x-www-form-urlencoded',
			'Authorization' => 'Basic '.env('META_BEARER')
			])->asForm()->post('https://api.getmati.com/oauth', [
			'grant_type' => 'client_credentials'
			]);
			
			if ($authResponse->failed()) {
			return;
			}
			
			$authToken = $authResponse->json()['access_token'];
			
			// Process KYC in chunks to improve memory usage
			UserKyc::whereIn('verification_status', ['pending', 'reviewNeeded'])->chunk(50, function ($pendingKycs) use ($authToken) {
			
		foreach ($pendingKycs as $value) {
		$verificationId = $value->verification_id;
		$userId = $value->user_id;
		
		// Fetch verification details
		$verificationResponse = Http::withOptions(['verify' => false])
		->withToken($authToken)
		->get('https://api.getmati.com/v2/verifications/' . $verificationId);
		
		if ($verificationResponse->failed()) {
		continue; // Skip to the next KYC if the request fails
		}
		
		$response = $verificationResponse->json();
		Log::error($response);
		die;
		// Process and store the video
		$storedVideoUrl = null;
		if (isset($response['steps'][0]['data']['videoUrl'])) {
		$videoUrl = $response['steps'][0]['data']['videoUrl'];
		$videoContents = @file_get_contents($videoUrl);
		if ($videoContents) {
		$videoName = uniqid() . '.mp4';
		Storage::disk('public')->put('kyc-videos/'.$userId.'/'. $videoName, $videoContents);
		$storedVideoUrl = Storage::url('kyc-videos/'.$userId.'/'. $videoName);
		}
		}
		
		// Process and store document images
		$documentImages = [];
		foreach ($response['documents'] as $documents) {
		foreach ($documents['photos'] as $photoUrl) {
		$imageContents = @file_get_contents($photoUrl);
		if ($imageContents) {
		$imageName = uniqid() . '.jpg';
		Storage::disk('public')->put('kyc-documents/'.$userId.'/'. $imageName, $imageContents);
		$documentImages[] = Storage::url('kyc-documents/'.$userId.'/'. $imageName);
		}
		}
		}
		
		// Update the KYC record within a transaction
		DB::transaction(function () use ($response, $documentImages, $storedVideoUrl) {
		UserKyc::where('verification_id', $response['id'])
		->where('identification_id', $response['identity']['id'])
		->update([
		'verification_status' => $response['identity']['status'],
		'document' => json_encode($documentImages),
		'video' => $storedVideoUrl
		]);
		});
		}
		});
		
		return response()->json(['message' => 'KYC verification status updated successfully'], 200);
		} */
		
	}
