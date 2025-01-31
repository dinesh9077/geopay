<?php
	
	namespace App\Models;
	
	use Illuminate\Database\Eloquent\Factories\HasFactory;
	use Illuminate\Database\Eloquent\Model;
	use Spatie\Activitylog\Traits\LogsActivity;
	use Spatie\Activitylog\LogOptions;
	
	class Setting extends Model
	{
		use HasFactory, LogsActivity;
		
		protected $fillable = [
			'name',
			'value', 
		];
		
		protected static $recordEvents = ['created', 'deleted', 'updated'];
	 
		public function getActivitylogOptions(string $logName = 'Settings'): LogOptions
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
	}
