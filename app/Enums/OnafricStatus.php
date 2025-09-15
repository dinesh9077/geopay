<?php

namespace App\Enums;

enum OnafricStatus: string
{
    case SUCCESS                                  = 'Success';
    case PENDING                                  = 'Pending';
    case LOG_SUCCESS                              = 'Log Success';
    case ACCEPTED                                 = 'Accepted';
    case PARTNER_CORRIDOR_NOT_ACTIVE              = 'Partner corridor not active';
    case SUBSCRIBER_NOT_FOUND                     = 'Subscriber not found';
    case SUBSCRIBER_NOT_AUTHORIZED                = 'Subscriber not authorized to receive amount';
    case INSUFFICIENT_FUNDS_MERCHANT              = 'Insufficient fund in merchant account';
    case TRANSACTION_NOT_EXECUTED                 = 'Transaction could not be executed';
    case E_WALLET_SYSTEM_ERROR                    = 'E-Wallet System error';
    case MFS_SYSTEM_ERROR                         = 'MFS System error';
    case BLACKLIST_ERROR                          = 'Blacklist error';
    case DAILY_SENDER_VELOCITY_LIMIT              = 'Daily Sender Velocity Limit Exceeded';
    case DAILY_RECIPIENT_VELOCITY_LIMIT           = 'Daily Recipient Velocity Limit Exceeded';
    case WEEKLY_SENDER_VELOCITY_LIMIT             = 'Weekly Sender velocity Limit Exceeded';
    case WEEKLY_RECIPIENT_VELOCITY_LIMIT          = 'Weekly Recipient velocity Limit Exceeded';
    case MONTHLY_SENDER_VELOCITY_LIMIT            = 'Monthly Sender velocity Limit Exceeded';
    case MONTHLY_RECIPIENT_VELOCITY_LIMIT         = 'Monthly Recipient velocity Limit Exceeded for Recipient';
    case TRANSACTION_MAX_AMOUNT_EXCEEDED          = 'Transaction Max Amount exceeded';
    case INVALID_BANK_ACCOUNT_NUMBER              = 'Invalid bank account number';
    case INVALID_MFS_BANK_CODE                    = 'Invalid MFS Africa bank code';
    case INTERNAL_SERVER_ERROR                    = 'Internal server error';
    case REQUESTED_TRASACTION_CANNOT_EXECUTE      = 'The requested transaction cannot be executed';
    case RECEIVE_PARTNER_CORRIDOR_IS_UNAVAILABLE  = 'Receive partner corridor is unavailable';
    case INVALID_MSISDN_FORMAT  				  = 'Invalid MSISDN Format';

    public function label(): string
    {
        return match($this) {
            self::SUCCESS => 'paid',
            self::ACCEPTED => 'inprocess', 
            self::PENDING => 'inprocess', 
            self::LOG_SUCCESS => 'inprocess', 
            default => 'cancelled and refunded',
        };
    }
}
