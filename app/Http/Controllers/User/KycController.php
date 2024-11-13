<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserKyc;
use App\Models\User;
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
}
