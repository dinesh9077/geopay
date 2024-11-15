<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{
	Folder, User, File
};
use Validator, DB, Hash, Storage, Auth;
use Illuminate\Validation\Rule;

class DashboardController extends Controller
{ 
    public function dashboard()
    { 
        return view('admin.dashboard');
    } 
}
