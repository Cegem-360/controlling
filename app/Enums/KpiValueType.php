<?php

declare(strict_types=1);

namespace App\Enums;

enum KpiValueType: string
{
    case Percentage = 'percentage';
    case Fixed = 'fixed';

    public function getLabel(): string
    {
        return match ($this) {
            self::Percentage => __('Percentage'),
            self::Fixed => __('Fixed'),
        };
    }
}
