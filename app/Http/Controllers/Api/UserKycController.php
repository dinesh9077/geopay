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
			Log::error($request->all());	
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
		}

}
