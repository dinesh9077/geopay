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
		if(in_array($userKyc->verification_status, ["verified"]))
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
					'verification_status' => 'reviewNeeded',
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
	
	public function getRemainingDirector($companyDetailId)
    {
        // Get all active document types
		$allDocuments = DocumentType::whereStatus(1)->pluck('id')->toArray(); 

		// Fetch all directors
		$directors = CompanyDirector::where('company_details_id', $companyDetailId)->with('documents')->get();

		$remainingDirectors = $directors->filter(function ($director) use ($allDocuments) {
			// Get uploaded document IDs for this director
			$uploadedDocuments = $director->documents->pluck('document_type_id')->toArray();

			// Check if this director is missing any document
			$missingDocuments = array_diff($allDocuments, $uploadedDocuments);

			return !empty($missingDocuments); // Return true if there are missing documents
		});
		
	 
		$response = [];
		foreach($remainingDirectors as $director)
		{
			$response[] = [
					'id' => $director->id,
					'name' => $director->name,
				];
		} 
		// Return response
		return response()->json([
			'remainingDirectors' => $response
		]);
    }
	
	public function getRemainingDocuments($director_id)
    {
        // Fetch all documents for the director
		$allDocuments = DocumentType::whereStatus(1)->get(['id', 'label'])->toArray(); // Convert to array

		// Fetch already added document IDs for the director
		$uploadedDocuments = CompanyDocument::query()
			->where('company_director_id', $director_id)
			->pluck('document_type_id') // Get document type IDs
			->toArray(); // Convert to array

		// Filter out the documents that are already uploaded
		$remainingDocuments = array_filter($allDocuments, function ($doc) use ($uploadedDocuments) {
			return !in_array($doc['id'], $uploadedDocuments); // Check if document ID is not uploaded
		});

		// Return remaining documents as JSON response
		return response()->json([
			'remainingDocuments' => array_values($remainingDocuments) // Re-index the array
		]);
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
			$documentTypes = DocumentType::whereStatus(1)->get();
			// Render the next step's view based on the current step
			$view = view($step == 1 ? 'user.kyc.step-2' : 'user.kyc.step-3', compact('companyDetail', 'documentTypes', 'user'))->render();

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
			
			if(!CompanyDocument::where('company_details_id', $companyDetail->id)->where('status', 2)->exists())
			{ 
				CompanyDetail::where('id', $request->company_details_id)->update(['is_update_kyc' => 1]);
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
			$user->update(['is_upload_document' => 1]); 
			   
			DB::commit();
			return $this->successResponse('KYC documents processed successfully.');
			
		} catch (\Throwable $e) { 
			DB::rollBack();
			return $this->errorResponse($e->getMessage());
		}
	}  
}
