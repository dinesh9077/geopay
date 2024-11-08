<?php 
	
	namespace App\Services; 
	use App\Models\{
		UserRole, User
	};
	use Illuminate\Support\Facades\Log;
	use Auth;
	class MasterService
	{ 
		public function getUserRoles($status = null)
		{ 
			$userRoles = UserRole::orderBy('role_name');
			
			if ($status !== null) {
				$userRoles->where('status', $status);
			}
			 
			return $userRoles->get(['id', 'role_name']);
		}	
		
		public function getCompanies($status = null)
		{ 
			$userCompanies = User::with('companyDetail')->where('is_company', 1)->orderBy('first_name');
			
			if ($status !== null) {
				$userCompanies->where('status', $status);
			} 
			return $userCompanies->get();
		}	
		
		public function getUsers($status = null)
		{ 
			$users = User::whereNot('role', 'admin')->orderBy('first_name');
			
			if ($status !== null) {
				$users->where('status', $status);
			} 
			return $users->get();
		}	
	}