<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FrontController extends Controller
{
    public function index(Request $request)
    {
        return view('welcome');
    }    
	
    public function termAndCondition()
    {
        return view('terms_condition');
    } 
	
    public function handleDepositCallback(Request $request)
    {
        // Verify signature / securehash if required
        \Log::info('Payment Callback Received', $request->all());
  
        return response()->json(['status' => 'ok']);
    }
	
    public function depositPaymentReturn(Request $request)
    {
        // Verify signature / securehash if required
        \Log::info('Return Payment Callback Received', $request->all()); 
        return response()->json(['status' => 'ok']);
    }
}
