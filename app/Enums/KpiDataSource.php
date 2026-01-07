<?php

declare(strict_types=1);

namespace App\Enums;

use function in_array;

enum KpiDataSource: string
{
    case Analytics = 'analytics';
    case SearchConsole = 'search_console';
    case GoogleAds = 'google_ads';
    case Manual = 'manual';
    case Calculated = 'calculated';

    public static function isIntegrationSource(string|self|null $value): bool
    {
        if ($value instanceof self) {
            return in_array($value, [self::Analytics, self::SearchConsole, self::GoogleAds], true);
        }

        return in_array($value, [self::Analytics->value, self::SearchConsole->value, self::GoogleAds->value], true);
    }
}
