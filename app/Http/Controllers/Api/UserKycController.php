<?php
	namespace App\Http\Controllers\Api;

	use App\Http\Controllers\Controller;
	use Illuminate\Http\Request;
	use App\Models\{
		User, UserKyc, BusinessType, DocumentType, CompanyDetail, CompanyDirector
	};
	use Illuminate\Support\Facades\{Http, Storage, DB, Log};
	use App\Http\Traits\ApiResponseTrait;
	use Validator, Auth;
 
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
		
		//Company KYC
		public function companyKycDetails()
		{
			error_reporting(0);
			// Fetch the authenticated user and their company details with the related documents
			$user = Auth::user();
			$companyDetail = $user->companyDetail()->with(['companyDocuments', 'companyDirectors'])->first();

			// Calculate the step number based on the company's step number, defaulting to 1 if not found
			$stepNumber = $companyDetail ? ($companyDetail->step_number + 1 ?? 1) : 1;
			
			// Get company documents, group them by document_type
			$companyDocument = $companyDetail 
			? $companyDetail->companyDocuments->groupBy('document_type')->map(function ($documents) {
				return $documents->first(); // Get the first document in each group
			})->toArray()
			: [];
			
			$businessTypes = BusinessType::whereStatus(1)->get();
			$documentTypes = DocumentType::whereStatus(1)->get();
			
			$data = [];
			$data['companyDetail'] = $companyDetail;
			$data['stepNumber'] = $stepNumber;
			$data['businessTypes'] = $businessTypes;
			$data['documentTypes'] = $documentTypes;
			$data['companyDocument'] = $companyDocument;
			return $this->successResponse('Kyc details fetched.', $data); 
		}
		
		public function companyKycStepStore(Request $request, $step)
		{   
			$user = Auth::user();
			
			// Common validation rules for each step
			$validationRules = [
				1 => [
					'business_type_id' => 'required|integer',
					'no_of_director' => 'required|integer|gt:0',
					'business_licence' => 'required|string',
					'postcode' => 'required|string',
					'company_address' => 'required|string',
					'director_name' => 'required|array|min:1', // Ensure it's an array with at least one element
					'director_name.*' => 'required|string|min:1', // Each item must be a non-empty string
				],
				2 => [
					'bank_name' => 'required|string',
					'bank_code' => 'required|string',
					'account_number' => 'required|string',
				],
			];
			
			// Check if the step is valid
			if (!array_key_exists($step, $validationRules)) {
				return $this->errorResponse('Invalid step');
			}
			
			try {
				// Start a transaction
				DB::beginTransaction();

				// Validate the request data based on the step
				$validator = Validator::make($request->all(), $validationRules[$step]);
				
				if ($validator->fails()) {
					return $this->validateResponse($validator->errors());
				}

				// Common fields for company details
				$companyData = [
					'user_id' => $user->id,
					'step_number' => $step,
					'updated_at' => now(),
				];

				// Step 1: Store company details
				if ($step == 1) {
					$companyData = array_merge($companyData, [
						'business_type_id' => $request->business_type_id,
						'no_of_director' => $request->no_of_director,
						'business_licence' => $request->business_licence,
						'postcode' => $request->postcode,
						'company_address' => $request->company_address,
					]); 
				}

				// Step 2: Update company details
				if ($step == 2) {
					$companyData = array_merge($companyData, [
						'bank_name' => $request->bank_name,
						'bank_code' => $request->bank_code,
						'account_number' => $request->account_number,
					]);
				}

				// Update or create company detail record
				$companyDetail = CompanyDetail::updateOrCreate(
					['user_id' => $user->id],
					$companyData
				);
				
			  
				if ($step == 1) {
					// Ensure `director_name` exists and is an array
					if ($request->has('director_name') && is_array($request->director_name)) {
						$submittedDirectorNames = array_filter($request->director_name); // Remove empty values
					 
						// Delete unmatched directors
						if ($request->has('director_id') && is_array($request->director_id)) {
							CompanyDirector::where('company_details_id', $companyDetail->id)
								->whereNotIn('id', $request->director_id)
								->delete();
						}

						// Loop through submitted director names and update or insert them
						foreach ($submittedDirectorNames as $ky => $directorName) {
							CompanyDirector::updateOrCreate(
								[
									'id' => $request->director_id[$ky] ?? '', 
								],
								[ 
									'company_details_id' => $companyDetail->id,
									'name' => $directorName,
									'updated_at' => now() 
								]
							);
						}
					}
				}

	 
				// Commit the transaction
				DB::commit();  
				// Return a success response with the view and company details
				return $this->successResponse("Step {$step} has been completed and stored successfully.", [
					'company_detail' => $companyDetail
				]);
				
			} catch (\Throwable $e) {
				// Rollback the transaction in case of an error
				DB::rollBack();
				return $this->errorResponse($e->getMessage());
			}
		}
	}
