<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Icons\Heroicon;

enum NavigationGroup implements HasIcon, HasLabel
{
    case Analytics;
    case SearchConsole;
    case GoogleAds;
    case Kpis;
    case Reports;
    case Configuration;
    case Settings;

    public function getLabel(): string
    {
        return match ($this) {
            self::Analytics => __('Analytics'),
            self::SearchConsole => __('Search Console'),
            self::GoogleAds => __('Google Ads'),
            self::Kpis => __('KPIs'),
            self::Reports => __('Reports'),
            self::Configuration => __('Configuration'),
            self::Settings => __('Settings'),
        };
    }

    public function getIcon(): string|Heroicon|null
    {
        return match ($this) {
            self::Analytics => Heroicon::OutlinedChartBar,
            self::SearchConsole => Heroicon::OutlinedMagnifyingGlass,
            self::GoogleAds => Heroicon::OutlinedCurrencyDollar,
            self::Kpis => Heroicon::OutlinedChartPie,
            self::Reports => Heroicon::OutlinedDocumentChartBar,
            self::Configuration => Heroicon::OutlinedCog6Tooth,
            self::Settings => Heroicon::OutlinedCog,
        };
    }
}
