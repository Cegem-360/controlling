<?php

declare(strict_types=1);

namespace App\Enums;

enum KpiGoalType: string
{
    case Increase = 'increase';
    case Decrease = 'decrease';

    public function getLabel(): string
    {
        return match ($this) {
            self::Increase => __('Increase'),
            self::Decrease => __('Decrease'),
        };
    }
}
