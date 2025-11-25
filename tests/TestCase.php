<?php

declare(strict_types=1);

namespace Tests;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

/**
 * @property Team $team
 * @property User $user
 */
abstract class TestCase extends BaseTestCase
{
    protected Team $team;

    protected User $user;
}
