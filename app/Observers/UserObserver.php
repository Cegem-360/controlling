<?php

declare(strict_types=1);

namespace App\Observers;

use App\Jobs\SyncPasswordToSecondaryApp;
use App\Models\User;

final class UserObserver
{
    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        if ($user->wasChanged('password')) {
            dispatch(new SyncPasswordToSecondaryApp(email: $user->email, hashedPassword: $user->password));
        }
    }
}
