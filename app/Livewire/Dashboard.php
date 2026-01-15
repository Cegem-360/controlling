<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Team;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

final class Dashboard extends Component
{
    /** @var Collection<int, Team> */
    public $teams;

    public function mount(): void
    {
        $this->teams = Auth::user()->teams()->with(['settings', 'googleAdsSettings'])->get();
    }

    public function render(): View
    {
        return view('livewire.dashboard')
            ->layout('components.layouts.dashboard');
    }
}
