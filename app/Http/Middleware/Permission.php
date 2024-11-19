<?php
	
	namespace App\Http\Middleware;
	
	use Closure;
	use Illuminate\Http\Request;
	use App\Models\RolePermission;  
	use Auth, Session, Config, Cache, DB; 
	
	class Permission
	{
		/**
			* Handle an incoming request.
			*
			* @param  \Illuminate\Http\Request  $request
			* @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
			* @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
		*/ 
		public function handle(Request $request, Closure $next, $per = null)
		{
			if (Auth::guard('admin')->check()) 
			{   	 
				$admin = Auth::guard('admin')->user();
				 
				$id = $admin->id;
				$role = $admin->role; 
				
				$permissions = [];
				   
				// Handle specific permission check
				if (!empty($per))
				{  
					$permissions = $admin->rolePermissions()->where('name', $per)->exists();
					if ($role !== 'admin' && !$permissions) 
					{
						return abort(403);
					} 
				}
				   
				if ($role != 'admin')
				{
					$permissions = $admin->rolePermissions()->get(['value', 'name']); 
					foreach ($permissions as $setting) 
					{  
						Config::set('permission.' . $setting->name, $setting->value);
					} 
				}
				else
				{   
					$permissions = DB::table('permissions')->select('name')->where('status', 1)->get(); 
					 
					$arrayper = ['view', 'add', 'edit', 'delete'];
					foreach ($permissions as $permission) 
					{  
						foreach ($arrayper as $arrayp)
						{
							Config::set('permission.' . $permission->name . '.' . $arrayp, $arrayp);
						}
					}
				}   
			}
			return $next($request);
		}   
	}
