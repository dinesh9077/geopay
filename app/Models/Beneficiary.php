<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Beneficiary extends Model
{
    use HasFactory, LogsActivity;
    protected $fillable = [
        'user_id',
        'type',
        'country_id',
        'bank_name',
        'account_number',
        'b_first_name',
        'b_middle_name',
        'b_last_name',
        'b_address',
        'b_state',
        'b_mobile',
        'b_email',
        'relations',
        'other_remarks',
        'remittance_purpose',
        'beneficiary_id',
        'receiver_id_expiry',
        'receiver_dob',
    ];
    /**
     * The name of the log.
     *
     * @var string
     */
    protected static $logName = 'benificiary';

    /**
     * Whether to log only dirty attributes.
     *
     * @var bool
     */
    protected static $logOnlyDirty = true;

    /**
     * Custom description for the log.
     *
     * @param string $eventName
     * @return string
     */
    public function getDescriptionForEvent(string $eventName): string
    {
        return "Banificiary model has been {$eventName}";
    }

    /**
     * Get the activity log options for the model.
     *
     * @return LogOptions
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->useLogName('benificiary')
            ->logOnlyDirty();
    }
}
