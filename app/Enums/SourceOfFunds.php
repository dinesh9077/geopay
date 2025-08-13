<?php

namespace App\Enums;

enum SourceOfFunds: string
{
    case SALARY = '01';
    case BUSINESS_INVESTMENT = '02';
    case DONATION = '06';
    case FRIENDS_FAMILY = '04';

    public function label(): string
    {
        return match ($this) {
            self::SALARY => 'Salary to include any work related compensation and pensions',
            self::BUSINESS_INVESTMENT => 'Business and investment',
            self::DONATION => 'Donation',
            self::FRIENDS_FAMILY => 'Friends and family',
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
