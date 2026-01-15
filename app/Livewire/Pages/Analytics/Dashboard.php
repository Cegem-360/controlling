<?php

declare(strict_types=1);

namespace App\Livewire\Pages\Analytics;

use App\Models\Team;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

final class Dashboard extends Component
{
    public ?Team $team = null;

    public bool $analyticsConfigured = false;

    public function mount(): void
    {
        $this->team = Auth::user()->teams()->first();
        $this->analyticsConfigured = $this->checkAnalyticsConfiguration();
    }

    public function render(): View
    {
        return view('livewire.pages.analytics.dashboard')
            ->layout('components.layouts.dashboard');
    }

    private function checkAnalyticsConfiguration(): bool
    {
        $propertyId = config('analytics.property_id');

        return ! empty($propertyId);
    }
}
