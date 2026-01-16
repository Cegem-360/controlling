<?php

declare(strict_types=1);

namespace App\Livewire\Concerns;

use Carbon\CarbonInterface;

/**
 * Provides common date range functionality for Search Console components.
 */
trait WithSearchConsoleDateRange
{
    public string $dateRangeType = '28_days';

    /**
     * Get the start date based on the selected date range type.
     */
    public function getStartDate(): CarbonInterface
    {
        return match ($this->dateRangeType) {
            '24_hours' => now()->subHours(24),
            '7_days' => now()->subDays(7),
            '28_days' => now()->subDays(28),
            '3_months' => now()->subMonths(3),
            default => now()->subDays(28),
        };
    }

    /**
     * Set the date range and persist to session.
     */
    protected function setDateRangeWithSession(string $type, string $sessionKey): void
    {
        $this->dateRangeType = $type;
        session([$sessionKey => $type]);
    }
}
