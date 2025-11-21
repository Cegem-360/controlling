<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\UserRole;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

final class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions for Team management
        $permissions = [
            // Team permissions
            'view teams',
            'create teams',
            'update teams',
            'delete teams',
            'manage team users',

            // User permissions
            'view users',
            'create users',
            'update users',
            'delete users',

            // Role permissions
            'view roles',
            'create roles',
            'update roles',
            'delete roles',

            // Permission permissions
            'view permissions',
            'create permissions',
            'update permissions',
            'delete permissions',

            // KPI permissions
            'view kpis',
            'create kpis',
            'update kpis',
            'delete kpis',

            // Analytics permissions
            'view analytics',
            'manage analytics',

            // Search Console permissions
            'view search data',
            'manage search data',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        // Assign all permissions to super_admin and admin roles
        $superAdminRole = Role::findByName(UserRole::SuperAdmin->value, 'web');
        $superAdminRole->givePermissionTo(Permission::all());

        $adminRole = Role::findByName(UserRole::Admin->value, 'web');
        $adminRole->givePermissionTo(Permission::all());

        // Assign management permissions to manager role
        $managerRole = Role::findByName(UserRole::Manager->value, 'web');
        $managerRole->givePermissionTo([
            'view teams',
            'manage team users',
            'view users',
            'view kpis',
            'create kpis',
            'update kpis',
            'delete kpis',
            'view analytics',
            'manage analytics',
            'view search data',
            'manage search data',
        ]);

        // Assign limited permissions to subscriber role
        $subscriberRole = Role::findByName(UserRole::Subscriber->value, 'web');
        $subscriberRole->givePermissionTo([
            'view teams',
            'view users',
            'view kpis',
            'view analytics',
            'view search data',
        ]);
    }
}
