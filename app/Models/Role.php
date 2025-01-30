<?php
	
	namespace App\Models;
	
	use Illuminate\Database\Eloquent\Factories\HasFactory;
	use Illuminate\Database\Eloquent\Model;
	use Spatie\Activitylog\Traits\LogsActivity;
	use Spatie\Activitylog\LogOptions;

	class Role extends Model
	{
		use HasFactory, LogsActivity;
		 
		protected $fillable = [
			'id',
			'admin_id',
			'name',
			'status',
			'created_at',
			'updated_at'
		];  
		
		protected static $recordEvents = ['created', 'deleted', 'updated'];
	 
		public function getActivitylogOptions(string $logName = 'roles'): LogOptions
		{  
			$user_name = auth()->check() 
			? (auth()->guard('admin')->check() 
				? auth()->guard('admin')->user()->name 
				: auth()->user()->name) 
			: 'Unknown User';

			return LogOptions::defaults()
			->logOnly(['*', 'admin.name'])
			->logOnlyDirty()
			->dontSubmitEmptyLogs()
			->useLogName($logName)
			->setDescriptionForEvent(function (string $eventName) use ($logName, $user_name) {
				return "The {$logName} has been {$eventName} by {$user_name}";
			});
		}
		
		public function admin()
		{
			return $this->belongsTo(Admin::class, 'admin_id');
		}
	
		public function roleGroups()
		{
			return $this->hasMany(RoleGroup::class, 'role_id');
		}
	}
