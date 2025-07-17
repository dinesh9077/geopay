<?php
	
	namespace App\Models;
	
	use Illuminate\Database\Eloquent\Factories\HasFactory;
	use Illuminate\Database\Eloquent\Model;
	use Spatie\Activitylog\Traits\LogsActivity;
	use Spatie\Activitylog\LogOptions;
	class LightnetCountry extends Model
	{
		use HasFactory, LogsActivity;
		
		// Define fillable fields to allow mass assignment
		protected $fillable = [
        'data', 
        'value', 
        'label', 
        'service_name', 
        'status',
        'markdown_type',
        'markdown_charge',
        'updated_at'
		];
		
		// If you want to allow automatic timestamps, make sure this is set to true
		public $timestamps = true;
		
		protected static $recordEvents = ['created', 'deleted', 'updated']; 
		public function getActivitylogOptions(string $logName = 'lightnet country setting'): LogOptions
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
		
		public function country()
		{
		return $this->belongsTo(Country::class, 'data', 'iso3');
		}
	}
