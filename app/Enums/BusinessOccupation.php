<?php

namespace App\Enums;

enum BusinessOccupation: string
{
    case AGRICULTURE_FORESTRY_FISHERIES = '07';
    case CONSTRUCTION_MANUFACTURING_MARINE = '08';
    case GOVERNMENT_OFFICIALS_SPECIAL_INTEREST = '03';
    case OTHER = '99';
    case PROFESSIONAL_RELATED_WORKERS = '23';
    case RETIRED = '15';
    case SELF_EMPLOYED = '05';
    case STUDENT = '13';
    case UNEMPLOYED = '14';

    public function label(): string
    {
        return match ($this) {
            self::AGRICULTURE_FORESTRY_FISHERIES => 'Agriculture forestry fisheries',
            self::CONSTRUCTION_MANUFACTURING_MARINE => 'Construction, manufacturing, marine',
            self::GOVERNMENT_OFFICIALS_SPECIAL_INTEREST => 'Government officials and Special Interest Organizations',
            self::OTHER => 'Other',
            self::PROFESSIONAL_RELATED_WORKERS => 'Professional, and related workers',
            self::RETIRED => 'Retired',
            self::SELF_EMPLOYED => 'Self-employed',
            self::STUDENT => 'Student',
            self::UNEMPLOYED => 'Unemployed',
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
