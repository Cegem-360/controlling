<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

final class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Super-Admin role
        Role::findOrCreate('Super-Admin', 'web');

        // Create admin role
        Role::findOrCreate('admin', 'web');

        // Create user role (optional, for future use)
        Role::findOrCreate('user', 'web');
    }
}
