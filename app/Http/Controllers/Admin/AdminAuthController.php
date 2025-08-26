<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin; // Assuming you have an Admin model
use Validator;
use App\Http\Traits\WebResponseTrait;

class AdminAuthController extends Controller
{
	use WebResponseTrait;
	
    // Show the admin login form
    public function showLoginForm()
    {
		// Redirect to dashboard if already logged in
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.auth.login'); // Create this view file
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
			'email' => 'required|string|email|max:255',
			'password' => 'required|string',
		]);
		
		if ($validator->fails()) {
			return $this->validateResponse($validator->errors());
		} 
		 
		try 
		{   
			if (Auth::guard('admin')->attempt($request->only('email', 'password')))
			{ 
				$url = route('admin.dashboard'); 
				return $this->successResponse('logged in successfully.', ['url' => $url]);
			}

			return $this->errorResponse('Invalid credentials.');
		}
		catch (\Throwable $e)
		{
			return $this->errorResponse($e->getMessage());
		}
    }

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
		$request->session()->invalidate();
		$request->session()->regenerateToken();
        return redirect()->route('admin.login');
    }

}
