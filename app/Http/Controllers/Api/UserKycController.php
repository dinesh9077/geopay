<?php
	namespace App\Http\Controllers\Api;

	use App\Http\Controllers\Controller;
	use Illuminate\Http\Request;
	use App\Models\{User, UserKyc};
	use Illuminate\Support\Facades\{Http, Storage, DB, Log};
	use App\Http\Traits\ApiResponseTrait;
	use Validator;
 
	class UserKycController extends Controller
	{ 
		use ApiResponseTrait;  
		public function metamapWebhook(Request $request)
		{  
			$data = $request->all();  
			if (empty($data['flowId']) || $data['flowId'] != config('setting.meta_verification_flow_id')) {
				return;
			}
			
			if (!in_array($data['eventName'], ['verification_started', 'verification_updated', 'verification_completed'])) {
				return;
			}
			 
			$verificationId = basename($data['resource']);
			
			if (in_array($data['eventName'], ["verification_started"]) && isset($data['metadata']['user_id'])) 
			{  
				$user_id = $data['metadata']['user_id'];
				$user_email = isset($data['metadata']['user_email']) ? $data['metadata']['user_email'] : null; 
				$step_id = 'pending';
				 
				UserKyc::updateOrCreate(
					['user_id' => $user_id], 
					[
						'email' => $user_email,
						'verification_status' => $step_id,
						'verification_id' => $verificationId,
						'meta_response' => json_encode($data),
					]
				);
				return;
			}
			 
			$metaKycDetail = UserKyc::where('verification_id', $verificationId)->first();
			if (!$metaKycDetail) {
				return;
			}
			
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
			
			$authToken = $authResponse->json()['access_token'] ?? null;
			
			// Fetch verification details
			$verificationResponse = Http::withOptions(['verify' => false])
			->withToken($authToken)
			->get(config('setting.meta_host') . '/v2/verifications/' . $verificationId);
			
			if ($verificationResponse->failed()) {
				return;
			}
			
			$response = $verificationResponse->json();
			
			// Check if the status is 'rejected' or 'deleted'
			if (in_array($response['identity']['status'], ['rejected', 'deleted'])) 
			{ 
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
			DB::transaction(function () use ($response, $documentImages, $storedVideoUrl, $data, $userId) 
			{
				UserKyc::where('verification_id', $response['id']) 
				->update([
				'verification_status' => in_array($response['identity']['status'] ?? 'reviewNeeded', ['reviewNeeded', 'verified']) 
                                ? $response['identity']['status'] 
                                : 'reviewNeeded',
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
	}
