<?php

namespace App\Http\Controllers\ApiProvider;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\AccessToken;
use App\Models\ApiCredential;
use App\Services\OpaqueToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Http\Traits\ApiServiceResponseTrait;

class TokenController extends Controller
{
	use ApiServiceResponseTrait;  
	
    public function issue(Request $request)
    {
        $data = $request->validate([
            'client_id'    => 'required|string',
            'secret_id' => 'required|string',
            'name'     => 'nullable|string|max:100',
            'ttl' => [
				'nullable',
				function ($attribute, $value, $fail) {
					if (!is_bool($value) && !is_numeric($value)) {
						$fail("The {$attribute} must be a boolean or a number.");
					}
				},
			], 
        ]);

        $apiCredential = ApiCredential::where('client_id', $data['client_id'])
		->where('client_secret', $data['secret_id'])
		->where('status', 'active')
		->first();
		
        if (!$apiCredential) {
			return $this->errorResponse('Invalid credentials', 'ERR_INVALID_CREDENTIALS', 401);  
        }
		 
		AccessToken::where('user_id', $apiCredential->user_id)
        ->whereNull('revoked_at')
        ->update(['revoked_at' => now()]);
		
        $token = OpaqueToken::issue(
            $apiCredential->user_id,
            $data['name'] ?? null,
            $data['ttl'] ?? true
        );
		
		$expiredIn = isset($data['ttl']) 
		? (is_bool($data['ttl']) ? 'never expired' : $data['ttl'] * 60)
		: 'never expired';
 
		return $this->successResponse('Token generate successfully.', [
            'token_type'   => 'Bearer',
            'access_token' => $token,
            'expires_in'   => $expiredIn,
        ], 200);  
    }

    public function revoke(Request $request)
    {
        $token = $request->attributes->get('access_token');
        if ($token) {
            OpaqueToken::revoke($token);
        }
		return $this->successResponse('Token revoked successfully.', null, 200);  
		return response()->json(['status' => true, 'message' => 'Token revoked successfully.', 'revoked' => true], 200); 
    }
}
