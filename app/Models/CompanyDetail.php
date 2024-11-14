<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
	
class CompanyDetail extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'company_details';

    protected $fillable = [
        'user_id', 
        'business_licence', 
        'tin', 
        'vat', 
        'company_address', 
        'postcode', 
        'bank_name', 
        'account_number', 
        'bank_code',
        'step_number'
    ];
	
	protected static $recordEvents = ['created', 'deleted', 'updated'];
		
	public function getActivitylogOptions(string $logName = 'company details'): LogOptions
	{  
		$user_name = auth()->check() ? auth()->user()->name : 'Unknown User'; // Fixed ternary operator
		return LogOptions::defaults()
			->logOnly(['*', 'user.name'])
			->logOnlyDirty()
			->dontSubmitEmptyLogs()
			->useLogName($logName)
			->setDescriptionForEvent(function (string $eventName) use ($logName, $user_name) {
				return "The {$logName} has been {$eventName} by {$user_name}";
			});
	}
		
    public function user()
    {
        return $this->belongsTo(User::class);
    }
	
	public function companyDocuments()
	{
		return $this->hasMany(CompanyDocument::class, 'company_details_id');
	}
}
