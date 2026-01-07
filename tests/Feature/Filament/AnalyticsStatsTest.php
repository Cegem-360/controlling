<?php

declare(strict_types=1);

use App\Filament\Pages\AnalyticsStats;
use App\Models\Team;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\seed;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    seed([RoleSeeder::class, PermissionSeeder::class]);
});

it('can render analytics stats page', function (): void {
    $team = Team::factory()->create(['name' => 'Test Team']);
    $admin = User::factory()->create();
    $admin->teams()->attach($team);
    $admin->assignRole('Super-Admin');

    actingAs($admin);
    Filament::setTenant($team);

    Livewire::actingAs($admin)
        ->test(AnalyticsStats::class, ['tenant' => $team])
        ->assertSuccessful();
});

it('has set kpi goal action', function (): void {
    $team = Team::factory()->create(['name' => 'Test Team']);
    $admin = User::factory()->create();
    $admin->teams()->attach($team);
    $admin->assignRole('Super-Admin');

    actingAs($admin);
    Filament::setTenant($team);

    Livewire::actingAs($admin)
        ->test(AnalyticsStats::class, ['tenant' => $team])
        ->assertActionExists('setKpiGoal');
});

it('validates required fields when setting kpi goal', function (): void {
    $team = Team::factory()->create(['name' => 'Test Team']);
    $admin = User::factory()->create();
    $admin->teams()->attach($team);
    $admin->assignRole('Super-Admin');

    actingAs($admin);
    Filament::setTenant($team);

    Livewire::actingAs($admin)
        ->test(AnalyticsStats::class, ['tenant' => $team])
        ->mountAction('setKpiGoal')
        ->setActionData([])
        ->callMountedAction()
        ->assertHasActionErrors([
            'page_path' => 'required',
            'metric_type' => 'required',
            'target_date' => 'required',
            'goal_type' => 'required',
            'value_type' => 'required',
            'target_value' => 'required',
        ]);
});
