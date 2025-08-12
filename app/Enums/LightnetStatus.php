<?php

namespace App\Enums;

enum LightnetStatus: string
{
    case UN_COMMIT_HOLD       = 'UN-COMMIT-HOLD';
    case UN_COMMIT_COMPLIANCE = 'UN-COMMIT-COMPLIANCE';
    case HOLD                 = 'HOLD';
    case COMPLIANCE           = 'COMPLIANCE';
    case SANCTION             = 'SANCTION';
    case UN_PAID              = 'UN-PAID';
    case POST                 = 'POST';
    case PAID                 = 'PAID';
    case CANCEL               = 'CANCEL';
    case CANCELHOLD           = 'CANCELHOLD';
    case API_PROCESSING       = 'API PROCESSING';
    case BLOCK                = 'BLOCK';

    public function label(): string
    {
        return match($this) {
            self::POST => 'inprocess',
            self::PAID => 'paid',
            self::CANCEL,
            self::CANCELHOLD => 'cancelled and refunded',
            default => 'processing', // default fallback
        };
    }
}
