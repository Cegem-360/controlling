<?php

declare(strict_types=1);

namespace App\Livewire;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;

final class DatabaseNotificationsTrigger extends Component
{
    #[Computed]
    public function unreadNotificationsCount(): int
    {
        return Auth::user()?->unreadNotifications()->count() ?? 0;
    }

    public function render(): View
    {
        return view('livewire.database-notifications-trigger');
    }
}
