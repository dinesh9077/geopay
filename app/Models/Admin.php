<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Admin extends Authenticatable
{
    use HasFactory, LogsActivity;
	
	protected $fillable = [
		'id', 
		'name', 
		'mobile', 
		'email', 
		'email_verified_at', 
		'password', 
		'profile', 
		'remember_token', 
		'created_at', 
		'updated_at', 
		'dob', 
		'office_mobile', 
		'role_id', 
		'role', 
		'status', 
		'xps', 
		'assign_by',
	]; 
	
	protected static $recordEvents = ['created', 'deleted', 'updated'];

	public function getActivitylogOptions(string $logName = 'staff'): LogOptions
	{  
		$user_name = auth()->check() 
		? (auth()->guard('admin')->check() 
			? auth()->guard('admin')->user()->name 
			: auth()->user()->name) 
		: 'Unknown User';
		
		return LogOptions::defaults()
			->logOnly(['*'])
			->logOnlyDirty()
			->dontSubmitEmptyLogs()
			->useLogName($logName)
			->setDescriptionForEvent(function (string $eventName) use ($logName, $user_name) {
				return "The {$logName} has been {$eventName} by {$user_name}";
			});
	}
	
	public function roles()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }
	
	public function rolePermissions()
	{
		return $this->hasMany(RolePermission::class, 'admin_id');
	}
}