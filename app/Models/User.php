<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, LogsActivity;

    /**
	 * The attributes that are mass assignable.
	 *
	 * @var array<int, string>
	 */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'country_id',
        'mobile_number',
        'formatted_number',
        'referalcode',
        'company_name',
        'fcm_token',
        'is_email_verify',
        'is_company',
        'is_mobile_verify',
        'is_kyc_verify',
        'status',
        'profile_image',
        'role',
        'xps',
        'terms',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_company' => 'boolean',
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
 
	protected static $recordEvents = ['created', 'deleted', 'updated'];

	public function getActivitylogOptions(string $logName = 'user'): LogOptions
	{  
		$user_name = auth()->check() ? auth()->user()->name : 'Unknown User'; // Fixed ternary operator
		return LogOptions::defaults()
			->logOnly(['*', 'country.name', 'userRole.role_name'])
			->logOnlyDirty()
			->dontSubmitEmptyLogs()
			->useLogName($logName)
			->setDescriptionForEvent(function (string $eventName) use ($logName, $user_name) {
				return "The {$logName} has been {$eventName} by {$user_name}";
			});
	}
 
    public function companyDetail()
    {
        return $this->hasOne(CompanyDetail::class, 'user_id');
    }

    public function userRole()
    {
        return $this->belongsTo(UserRole::class, 'user_role_id');
    }
	
    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function loginLogs()
    {
        return $this->hasMany(LoginLog::class);
    } 
}
