<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserKyc;
use App\Models\User;
use App\Models\CompanyDetail;
use App\Models\CompanyDocument;
use App\Models\BusinessType;
use App\Models\DocumentType;
use App\Models\CompanyDirector;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use App\Http\Traits\WebResponseTrait; 
use Validator, DB, Auth;
use Helper, ImageManager;
class KycController extends Controller
{
	use WebResponseTrait;
    public function __construct()
    {
        $this->middleware('auth');
    }
	
	public function metaMapKyc()
	{
		$user = Auth::user();
		 
		$metaClientId = config('setting.meta_verification_api_key');
        $metaFlowId = config('setting.meta_verification_flow_id');
        
        // Encrypt the data (client id, flow id)
		$metaKey = [
            'metaClientId' => $metaClientId,
            'metaFlowId' => $metaFlowId,
        ];
        
		$encryptedData = $this->encryptData($metaKey);
		
		$userKyc = UserKyc::where('user_id', $user->id)->first();
		 
		return view('user.kyc.metamapkyc', compact('encryptedData', 'userKyc', 'user'));
	}
	
	function metaMapKycStatus()
	{
		$user = Auth::user();
		$userKyc = UserKyc::where('user_id', $user->id)->first();
		if(!$userKyc) 
		{
			return $this->errorResponse('User kyc not found.');
		}
		
		$output = "";
		if(!in_array($userKyc->verification_status, ["verified", "rejected"]))
		{
			$output .= '<h6 class="fw-semibold text-black text-center mb-4">Your Meta KYC Is Completed.</h6>';
			$output .= '<p style="color: gray; font-size: 0.8rem; text-align: center;" class="caption">Thank you for completing your KYC submission! Your documents have been reviewed and approved. You can now continue using our services.</p>';
			$output .= '<div class="text-center"><a href="' . route('home') . '" class="btn btn-primary btn-sm">Continue to use</a></div>';

			return $this->successResponse('The KYC is verified successfully.', ['output' => $output]);
		}

		return $this->errorResponse('User kyc not found.');
	}
	
	public function metaMapKycFinished(Request $request)
	{  
		DB::beginTransaction();

		try {
			// Retrieve the user based on the provided email
			$user = Auth::user();

			// Check if the user exists and has a valid email
			if (!$user) {
				return $this->errorResponse('User not found.');
			}

			if (empty($user->email)) {
				return $this->errorResponse('The email address is missing or invalid.');
			}
			
			if(!$request->verification_id || !$request->identification_id)
			{
				return $this->errorResponse('Missing required KYC data: identityId or verificationId.');
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
	
	// Corporate / Company Kyc
	public function corporateKyc()
	{
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
		$isSelect = true; 
		// Pass the necessary data to the view
		return view('user.kyc.corporatekyc', compact('user', 'companyDetail', 'stepNumber', 'companyDocument', 'businessTypes', 'documentTypes', 'isSelect'));
	}
 
	public function corporateKycStep(Request $request, $step)
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
					foreach ($submittedDirectorNames as $directorName) {
						CompanyDirector::updateOrCreate(
							[
								'company_details_id' => $companyDetail->id,
								'name' => $directorName
							],
							[
								'name' => $directorName, // Ensure updated timestamp
								'updated_at' => now(), // Ensure updated timestamp
							]
						);
					}
				}
			}

 
			// Commit the transaction
			DB::commit();
			$documentTypes = DocumentType::whereStatus(1)->get();
			// Render the next step's view based on the current step
			$view = view($step == 1 ? 'user.kyc.step-2' : 'user.kyc.step-3', compact('companyDetail', 'documentTypes'))->render();

			// Return a success response with the view and company details
			return $this->successResponse("Step {$step} has been completed and stored successfully.", [
				'company_details_id' => $companyDetail->id,
				'view' => $view,
			]);
			
		} catch (\Throwable $e) {
			// Rollback the transaction in case of an error
			DB::rollBack();
			return $this->errorResponse($e->getMessage());
		}
	}
	
	public function corporateKycDocumntStore(Request $request)
	{
	
		try {
			DB::beginTransaction();

			// Validate the incoming request
			$validator = Validator::make($request->all(), [
				'company_director_id' => 'required|integer',
				'company_details_id' => 'required|integer|exists:company_details,id',
				'document_type_id' => 'required|integer',
				'documents' => 'required|array|max:2', 
				'documents.*' => 'mimes:jpg,jpeg,png,pdf|max:2048',  
			]);
			
			if ($validator->fails()) {
				return $this->validateResponse($validator->errors()->first());
			}
			
			$user = Auth::user();
			
			$companyDetail = CompanyDetail::where('id', $request->company_details_id)->first();
			$documentType = DocumentType::where('id', $request->document_type_id)->first();
			// Array to hold file paths
			$storedFiles = [];
			  
			// Validate and process each file in the request
			foreach ($request->documents as $key => $file) {
					 
				// Get the file extension
				$extension = $file->getClientOriginalExtension();
				// If company details exist, update document (optional files)
				if ($companyDetail) {
					
					// Check if the document already exists for this company
					$existingDocs = DB::table('company_documents')
						->where('company_details_id', $companyDetail->id)
						->where('document_type_id', $request->document_type_id)
						->where('company_director_id', $request->company_director_id) 
						->get();

					if ($existingDocs->isNotEmpty()) {
						foreach ($existingDocs as $existingDoc) {
							// Construct the full path for the old document
							$fullPath = 'company_documents/'.$companyDetail->user_id.'/'.$existingDoc->document;
							
							// Delete the old image using ImageManager
							ImageManager::imgDelete($fullPath);
							
							// Delete the record from the database
							DB::table('company_documents')->where('id', $existingDoc->id)->delete();
						}
					}

					// If no existing document, move the new file
					$storedFile = ImageManager::move('company_documents/'.$companyDetail->user_id, $file, $extension);
					$storedFiles[] = [
						'company_details_id' => $companyDetail->id,
						'company_director_id' => $request->company_director_id,
						'document_type_id' => $request->document_type_id,
						'document_type' => $documentType->name,
						'document' => $storedFile,  // Store the file name returned by move()
						'status' => 0,  
						'created_at' => now(),
						'updated_at' => now(),
					];
					
				} else {
					// If no company details, move the new file (required for new company details)
					$storedFile = ImageManager::move('company_documents/'.$companyDetail->user_id, $file, $extension);
					$storedFiles[] = [
						'company_details_id' => $companyDetail->id,
						'company_director_id' => $request->company_director_id,
						'document_type_id' => $request->document_type_id,
						'document_type' => $documentType->name,
						'document' => $storedFile,  // Store the file name returned by move()
						'status' => 0,
						'created_at' => now(),
						'updated_at' => now(),
					];
				}
			 
			}
		
			// Insert new documents into the database if needed
			if (count($storedFiles) > 0) {
				DB::table('company_documents')->insert($storedFiles);
			}
			
			DB::commit();
			return $this->successResponse('KYC documents processed successfully.', ['data' => $request->only('company_details_id', 'company_director_id', 'document_type_id')]);
			
		} catch (\Throwable $e) { 
			DB::rollBack();
			return $this->errorResponse($e->getMessage());
		}
	}
	public function corporateKycFinal(Request $request)
	{     
        try {
			DB::beginTransaction();

			$user = Auth::user();
			
			$companyDetail = CompanyDetail::where('user_id', $user->id)->first();
			if(!$companyDetail)
			{
				return $this->errorResponse('Company details not found.');
			}
			
			$companyDocuments = CompanyDocument::where('company_details_id', $companyDetail->id)->get(); 
			 
			if (!$request->has('company_document') || empty($request->company_document)) {
				return $this->errorResponse('The document field is required. Please provide the company documents.');
			}
			 
			// Validation rules
			$rules = $this->validateRules();
			$updateRules = $this->validateUpdateRules(); // Removed extra ()
			$messages = $this->validateMessages();

			if ($companyDocuments->isEmpty()) { 
				$validator = Validator::make($request->all(), $rules, $messages); 
			} else {
				$validator = Validator::make($request->all(), $updateRules, $messages); 
			}

			if ($validator->fails()) {
				return $this->validateResponse($validator->errors());
			}


			// Array to hold file paths
			$storedFiles = [];
			  
			// Process each file input in the validated request
			foreach ($request->company_document as $key => $files) {
				foreach ($files as $file) {
					// Determine the file extension
					$extension = $file->getClientOriginalExtension();

					// If company details exist, update document (optional files)
					if ($companyDetail) {
						
						// Check if the document already exists for this company
						$existingDocs = DB::table('company_documents')
							->where('company_details_id', $companyDetail->id)
							->where('document_type', $key) 
							->get();

						if ($existingDocs->isNotEmpty()) {
							foreach ($existingDocs as $existingDoc) {
								// Construct the full path for the old document
								$fullPath = 'company_documents/'.$companyDetail->user_id.'/'.$existingDoc->document;
								
								// Delete the old image using ImageManager
								ImageManager::imgDelete($fullPath);
								
								// Delete the record from the database
								DB::table('company_documents')->where('id', $existingDoc->id)->delete();
							}
						}
 
						// If no existing document, move the new file
						$storedFile = ImageManager::move('company_documents/'.$companyDetail->user_id, $file, $extension);
						$storedFiles[] = [
							'company_details_id' => $companyDetail->id,
							'document_type' => $key,
							'document' => $storedFile,  // Store the file name returned by move()
							'status' => 0,  
							'created_at' => now(),
							'updated_at' => now(),
						];
						
					} else {
						// If no company details, move the new file (required for new company details)
						$storedFile = ImageManager::move('company_documents/'.$companyDetail->user_id, $file, $extension);
						$storedFiles[] = [
							'company_details_id' => $companyDetail->id,
							'document_type' => $key,
							'document' => $storedFile,  // Store the file name returned by move()
							'status' => 0,
							'created_at' => now(),
							'updated_at' => now(),
						];
					}
				}
			}

			// Insert new documents into the database if needed
			if (count($storedFiles) > 0) {
				DB::table('company_documents')->insert($storedFiles);
			}

			// Commit the transaction if all goes well
			DB::commit();
			return $this->successResponse('KYC documents processed successfully.');
			
		} catch (\Throwable $e) { 
			DB::rollBack();
			return $this->errorResponse($e->getMessage());
		}
	} 
	
	protected function validateRules()
	{
		  // Validation rules for the uploaded files
		$rules = [
			'company_document.memorandum_articles_of_association' => 'required|array|min:1|max:2',
			'company_document.memorandum_articles_of_association.*' => 'file|mimes:pdf,jpeg,png,jpg|max:5120',
			
			'company_document.registration_of_shareholders' => 'required|array|min:1|max:2',
			'company_document.registration_of_shareholders.*' => 'file|mimes:pdf,jpeg,png,jpg|max:5120',
			
			'company_document.registration_of_directors' => 'required|array|min:1|max:2',
			'company_document.registration_of_directors.*' => 'file|mimes:pdf,jpeg,png,jpg|max:5120',
			
			'company_document.proof_of_address_shareholders' => 'required|array|min:1|max:2',
			'company_document.proof_of_address_shareholders.*' => 'file|mimes:pdf,jpeg,png,jpg|max:5120',
			
			'company_document.proof_of_address_directors' => 'required|array|min:1|max:2',
			'company_document.proof_of_address_directors.*' => 'file|mimes:pdf,jpeg,png,jpg|max:5120',
			
			'company_document.govt_id_shareholders' => 'required|array|min:1|max:2',
			'company_document.govt_id_shareholders.*' => 'file|mimes:pdf,jpeg,png,jpg|max:5120',
			
			'company_document.govt_id_directors' => 'required|array|min:1|max:2',
			'company_document.govt_id_directors.*' => 'file|mimes:pdf,jpeg,png,jpg|max:5120',
		];
		return $rules;
	}
	
	protected function validateUpdateRules()
	{
		  // Validation rules for the uploaded files
		$rules = [
			'company_document.memorandum_articles_of_association' => 'nullable|array|min:1|max:2',
			'company_document.memorandum_articles_of_association.*' => 'nullable|file|mimes:pdf,jpeg,png,jpg|max:5120',
			
			'company_document.registration_of_shareholders' => 'nullable|array|min:1|max:2',
			'company_document.registration_of_shareholders.*' => 'nullable|file|mimes:pdf,jpeg,png,jpg|max:5120',
			
			'company_document.registration_of_directors' => 'nullable|array|min:1|max:2',
			'company_document.registration_of_directors.*' => 'nullable|file|mimes:pdf,jpeg,png,jpg|max:5120',
			
			'company_document.proof_of_address_shareholders' => 'nullable|array|min:1|max:2',
			'company_document.proof_of_address_shareholders.*' => 'nullable|file|mimes:pdf,jpeg,png,jpg|max:5120',
			
			'company_document.proof_of_address_directors' => 'nullable|array|min:1|max:2',
			'company_document.proof_of_address_directors.*' => 'nullable|file|mimes:pdf,jpeg,png,jpg|max:5120',
			
			'company_document.govt_id_shareholders' => 'nullable|array|min:1|max:2',
			'company_document.govt_id_shareholders.*' => 'nullable|file|mimes:pdf,jpeg,png,jpg|max:5120',
			
			'company_document.govt_id_directors' => 'nullable|array|min:1|max:2',
			'company_document.govt_id_directors.*' => 'nullable|file|mimes:pdf,jpeg,png,jpg|max:5120',
		];
		return $rules;
	}
	
	protected function validateMessages()
	{
		$messages = [
			'company_document.memorandum_articles_of_association.required' => 'The memorandum articles of association is required.',
			'company_document.memorandum_articles_of_association.array' => 'The memorandum articles of association must be an array.', 
			'company_document.memorandum_articles_of_association.max' => 'The memorandum articles of association field must not have more than 2 items.',
			'company_document.memorandum_articles_of_association.*.file' => 'Each memorandum article must be a file.',
			'company_document.memorandum_articles_of_association.*.mimes' => 'Each memorandum article must be a PDF, JPEG, PNG, or JPG file.',
			'company_document.memorandum_articles_of_association.*.max' => 'Each memorandum article must be no larger than 2MB.',
			
			'company_document.registration_of_shareholders.required' => 'The registration of shareholders is required.',
			'company_document.registration_of_shareholders.array' => 'The registration of shareholders must be an array.',
			'company_document.registration_of_shareholders.max' => 'The registration of shareholders field must not have more than 2 items.',
			'company_document.registration_of_shareholders.*.file' => 'Each registration of shareholders document must be a file.',
			'company_document.registration_of_shareholders.*.mimes' => 'Each registration of shareholders document must be a PDF, JPEG, PNG, or JPG file.',
			'company_document.registration_of_shareholders.*.max' => 'Each registration of shareholders document must be no larger than 2MB.',
			
			'company_document.registration_of_directors.required' => 'The registration of directors is required.',
			'company_document.registration_of_directors.array' => 'The registration of directors must be an array.',
			'company_document.registration_of_directors.max' => 'The registration of directors field must not have more than 2 items.',
			'company_document.registration_of_directors.*.file' => 'Each registration of directors document must be a file.',
			'company_document.registration_of_directors.*.mimes' => 'Each registration of directors document must be a PDF, JPEG, PNG, or JPG file.',
			'company_document.registration_of_directors.*.max' => 'Each registration of directors document must be no larger than 2MB.',
			
			'company_document.proof_of_address_shareholders.required' => 'The proof of address for shareholders is required.',
			'company_document.proof_of_address_shareholders.array' => 'The proof of address for shareholders must be an array.',
			'company_document.proof_of_address_shareholders.max' => 'The proof of address for shareholders field must not have more than 2 items.',
			'company_document.proof_of_address_shareholders.*.file' => 'Each proof of address document for shareholders must be a file.',
			'company_document.proof_of_address_shareholders.*.mimes' => 'Each proof of address document for shareholders must be a PDF, JPEG, PNG, or JPG file.',
			'company_document.proof_of_address_shareholders.*.max' => 'Each proof of address document for shareholders must be no larger than 2MB.',
			
			'company_document.proof_of_address_directors.required' => 'The proof of address for directors is required.',
			'company_document.proof_of_address_directors.array' => 'The proof of address for directors must be an array.',
			'company_document.proof_of_address_directors.max' => 'The proof of address for directors field must not have more than 2 items.',
			'company_document.proof_of_address_directors.*.file' => 'Each proof of address document for directors must be a file.',
			'company_document.proof_of_address_directors.*.mimes' => 'Each proof of address document for directors must be a PDF, JPEG, PNG, or JPG file.',
			'company_document.proof_of_address_directors.*.max' => 'Each proof of address document for directors must be no larger than 2MB.',
			
			'company_document.govt_id_shareholders.required' => 'The government ID for shareholders is required.',
			'company_document.govt_id_shareholders.array' => 'The government ID for shareholders must be an array.',
			'company_document.govt_id_shareholders.max' => 'The government ID for shareholders field must not have more than 2 items.',
			'company_document.govt_id_shareholders.*.file' => 'Each government ID document for shareholders must be a file.',
			'company_document.govt_id_shareholders.*.mimes' => 'Each government ID document for shareholders must be a PDF, JPEG, PNG, or JPG file.',
			'company_document.govt_id_shareholders.*.max' => 'Each government ID document for shareholders must be no larger than 2MB.',
			
			'company_document.govt_id_directors.required' => 'The government ID for directors is required.',
			'company_document.govt_id_directors.array' => 'The government ID for directors must be an array.',
			'company_document.govt_id_directors.max' => 'The government ID for directors field must not have more than 2 items.',
			'company_document.govt_id_directors.*.file' => 'Each government ID document for directors must be a file.',
			'company_document.govt_id_directors.*.mimes' => 'Each government ID document for directors must be a PDF, JPEG, PNG, or JPG file.',
			'company_document.govt_id_directors.*.max' => 'Each government ID document for directors must be no larger than 2MB.',
		];
		return $messages;
	}
}
