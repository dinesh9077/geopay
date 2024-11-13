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
			$user = User::where('email', $request->email)->first();
			if(!$user)
			{
				return $this->errorResponse('The user was not found.'); 
			}
			
			if($user->status == 0)
			{
				return $this->errorResponse('This user account is inactive. Please reach out to the administrator for further details.'); 
			}
				
			// Attempt to authenticate the user
			if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) 
			{ 
				Helper::loginLog('login', $user);
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
    public function logout()
    {
		$user = Auth::user();
		Helper::loginLog('login', $user);
        Auth::logout();
        return redirect('/login')->with('error', 'Logged out successfully');
    }
}
