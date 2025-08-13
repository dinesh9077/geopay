<?php

namespace App\Enums;

enum IdType: string
{
    case PASSPORT = '01';
    case ID_CARD = '02';
    case DRIVERS_LICENSE = '03';
    case RESIDENCE = '04';
    case COMPANY_REGISTRATION = '05';
    case WORK_PERMIT = '06';

    public function label(): string
    {
        return match ($this) {
            self::PASSPORT => 'Passport',
            self::ID_CARD => 'ID card (government issued)',
            self::DRIVERS_LICENSE => 'Drivers License',
            self::RESIDENCE => 'Residence',
            self::COMPANY_REGISTRATION => 'Company Registration',
            self::WORK_PERMIT => 'Work Permit',
        };
    }

    public static function options(): array
    {
        return array_map(fn($case) => [
            'value' => $case->value,
            'label' => $case->label()
        ], self::cases());
    }
}
