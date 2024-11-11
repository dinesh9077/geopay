<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\HomeController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

//Auth::routes();

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.submit')->middleware('webdecrypt.request'); 
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');


Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register')->middleware('webdecrypt.request');
Route::post('individual/register', [RegisterController::class, 'individualRegister'])->name('register.submit');

Route::middleware(['webdecrypt.request'])->group(function ()
{  
	// Reset Password
	Route::get('/password/reset', [ResetPasswordController::class, 'showOtpRequestForm'])->name('password.request');  
	Route::post('/password/send-otp', [ResetPasswordController::class, 'sendOtp'])->name('password.sendOtp');  
	Route::post('/password/resend-otp', [ResetPasswordController::class, 'resendOtp'])->name('password.resendOtp');
	Route::post('/password/verify-otp', [ResetPasswordController::class, 'verifyEmailOtp'])->name('password.verifyOtp');  
	Route::post('/password/reset', [ResetPasswordController::class, 'resetPassword'])->name('password.reset'); 
});

Route::get('/home', [HomeController::class, 'index'])->name('home');