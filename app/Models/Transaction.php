<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Transaction extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'receiver_id',
        'wallet_id',
        'invoice_id',
        'transaction_id',
        'platform_name',
        'platform_provider',
        'country_id',
        'transaction_type',
        'image',
        'previous_amount',
        'current_amount',
        'total_amount',
        'requested_amount',
        'commission_amount',
        'transaction_status',
        'comments',
        'remarks'
    ];
    /**
     * The name of the log.
     *
     * @var string
     */
    protected static $logName = 'transaction';

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
        return "Transaction model has been {$eventName}";
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
            ->useLogName('transaction')
            ->logOnlyDirty();
    }
}
