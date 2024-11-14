<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserKyc;
use App\Models\User;
use App\Models\CompanyDetail;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use App\Http\Traits\WebResponseTrait; 
use Validator, DB, Auth;
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
		 
		$metaClientId = env('META_VERIFICATION_API_KEY');
        $metaFlowId = env('META_VERIFICATION_FLOW_ID');
        
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
		$user = Auth::user();  
		$companyDetail = $user->companyDetail; 
		
		$stepNumber = $companyDetail ? (($companyDetail->step_number ?? 1) + 1) : 1;
		return view('user.kyc.corporatekyc', compact('user', 'companyDetail', 'stepNumber'));
	} 
	
	public function corporateKycStep(Request $request, $step)
	{
		$user = Auth::user();
		
		// Common validation rules for each step
		$validationRules = [
			1 => [
				'business_licence' => 'required|string',
				'postcode' => 'required|string',
				'company_address' => 'required|string',
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

			// Commit the transaction
			DB::commit();

			return $this->successResponse("Step {$step} has been completed and stored successfully.", [
				'company_details_id' => $companyDetail->id
			]);
			
		} catch (\Throwable $e) {
			// Rollback the transaction in case of an error
			DB::rollBack();
			return $this->errorResponse($e->getMessage());
		}
	}


}
