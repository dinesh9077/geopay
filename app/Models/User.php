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
		protected $fillable = ['id','user_role_id','first_name','last_name','email','password','country_id','mobile_number','formatted_number','referalcode','fcm_token','is_company','verification_token','is_email_verify','is_mobile_verify','is_kyc_verify','status','role','balance','remember_token','profile_image','deleted_at','created_at','updated_at','terms','xps','company_name','user_limit_id','is_upload_document','password_changed_at','address','id_type','id_number','expiry_id_date','city','state','zip_code','date_of_birth','gender','business_activity_occupation','source_of_fund','issue_id_date','developer_option','is_merchant']; 
		
		/**
			* The attributes that should be hidden for serialization.
			*
			* @var array<int, string>
		*/
		protected $hidden = [
			'password',
			'remember_token',
			'xps', 
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
			'password_changed_at' => 'datetime',
		];
		
		protected static $recordEvents = ['created', 'deleted', 'updated'];
		
		public function getActivitylogOptions(string $logName = 'Individual User'): LogOptions
		{  
			$user_name = auth()->check() ? auth()->user()->name : 'Unknown User'; // Fixed ternary operator
			return LogOptions::defaults()
			->logOnly(['*', 'country.name', 'user_status_text'])
			->logOnlyDirty()
			->dontSubmitEmptyLogs()
			->useLogName($logName)
			->setDescriptionForEvent(function (string $eventName) use ($logName, $user_name) {
				return "The {$logName} has been {$eventName} by {$user_name}";
			});
		}
		
		public function getUserStatusTextAttribute()
		{
			$statusText = [
				0 => 'In-active',
				1 => 'Active'
			];

			return $statusText[$this->status] ?? 'Unknown';
		}
		
		public function getFullNameAttribute()
		{   
			return "{$this->first_name} {$this->last_name}";
		}
		
		public function unreadNotifications()
		{
			return $this->notifications()->whereNull('read_at');
		}

		public function companyDetail()
		{
			return $this->hasOne(CompanyDetail::class, 'user_id');
		}
		 
		public function country()
		{
			return $this->belongsTo(Country::class, 'country_id');
		}
		
		public function loginLogs()
		{
			return $this->hasMany(LoginLog::class);
		} 
		
		public function userKyc()
		{
			return $this->hasOne(UserKyc::class, 'user_id');
		} 
		
		public function userLimit()
		{
			return $this->belongsTo(UserLimit::class, 'user_limit_id');
		}
		
		public function calculateTransactionSum($transactionType)
		{
			return Transaction::where('receiver_id', $this->id)
							  ->where('transaction_type', $transactionType)
							  ->sum('txn_amount');
		}

		public function depositAmount()
		{
			return $this->calculateTransactionSum('credit');
		}

		public function withdrawAmount()
		{
			return $this->calculateTransactionSum('debit');
		}
		
		public function totalTransaction()
		{
			return Transaction::where('receiver_id', $this->id)->count();
		} 
		
		public function webhook()
		{
			return $this->hasOne(WebhookRegister::class, 'user_id');
		}   
	}
