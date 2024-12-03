<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class UserKyc extends Model
{
    use HasFactory, LogsActivity;

    // Specify the table name if it's different from the default plural form
    protected $table = 'user_kycs';

    // Specify the fillable attributes
    protected $fillable = [
        'user_id',
        'email',
        'video',
        'document',
        'verification_status',
        'identification_id',
        'verification_id',
        'meta_response', 
        'updated_at', 
    ];

    // Disable timestamps if you do not want to use created_at and updated_at
    public $timestamps = true;
	
	protected static $recordEvents = ['created', 'deleted', 'updated']; 
	public function getActivitylogOptions(string $logName = 'user meta kyc'): LogOptions
	{  
		$user_name = auth()->check() ? auth()->user()->name : 'Unknown User'; // Fixed ternary operator
		return LogOptions::defaults()
			->logOnly(['*', 'user.first_name', 'user.last_name'])
			->logOnlyDirty()
			->dontSubmitEmptyLogs()
			->useLogName($logName)
			->setDescriptionForEvent(function (string $eventName) use ($logName, $user_name) {
				return "The {$logName} has been {$eventName} by {$user_name}";
			});
	}
	
	public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
