<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Http\Traits\WebResponseTrait;
use Helper;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */
	
    use AuthenticatesUsers, WebResponseTrait;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
	 public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    } 
	
    public function showLoginForm()
    {
        return view('user.auth.login');  
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
			session(['login_time' => now()]);
			$user = User::where('email', $request->email)->first();
			if(!$user)
			{ 
				return $this->errorResponse('User not found.');
			}
			
			// Check user status and verification
			$messages = [
				'status' => 'This user account is inactive. Please reach out to the administrator for further details.',
				'is_email_verify' => 'This email was not verified. Please reach out to the administrator for further details.',
				'is_mobile_verify' => 'This mobile number was not verified. Please reach out to the administrator for further details.',
			];
			
			foreach ($messages as $key => $message) { 
				if ($user->$key == 0) {
					return $this->errorResponse($message);
				}
			} 
			
			$credentials = $request->only('email', 'password');
			$remember = $request->filled('remember'); // Check if "remember" checkbox is selected

			if (Auth::attempt($credentials, $remember)) 
			{ 
				Helper::loginLog('login', $user);
				Auth::logoutOtherDevices($request->password); 
				$url = $user->is_kyc_verify == 0 ? route('metamap.kyc') : route('home');
				return $this->successResponse('user logged in successfully.', ['url' => $url]);
			}
			
			return $this->errorResponse('Invalid credentials.');
		}
		catch (\Throwable $e)
		{
			return $this->errorResponse($e->getMessage());
		}
	}

    // Handle logout
    public function logout(Request $request)
    {
		$user = Auth::user();
		Helper::loginLog('logout', $user);
		
		// Clear the remember token
        Auth::logout();
		$request->session()->invalidate();
		$request->session()->regenerateToken();

        return redirect('/login')->with('success', 'Logged out successfully');
    }
}
