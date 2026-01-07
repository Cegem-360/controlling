<?php

declare(strict_types=1);

use App\Filament\Pages\EditTeamProfile;
use App\Filament\Pages\RegisterTeam;
use App\Models\Team;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Filament\Facades\Filament;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\seed;

beforeEach(function (): void {
    seed([RoleSeeder::class, PermissionSeeder::class]);
});

it('can render register team page', function (): void {
    $team = Team::factory()->create(['name' => 'Test Team']);
    $admin = User::factory()->create();
    $admin->teams()->attach($team);
    $admin->assignRole('Super-Admin');

    actingAs($admin);
    Filament::setTenant($team);

    Livewire::actingAs($admin)
        ->test(RegisterTeam::class)
        ->assertSuccessful();
});

it('can create new team', function (): void {
    $team = Team::factory()->create(['name' => 'Test Team']);
    $admin = User::factory()->create();
    $admin->teams()->attach($team);
    $admin->assignRole('Super-Admin');

    actingAs($admin);
    Filament::setTenant($team);

    Livewire::actingAs($admin)
        ->test(RegisterTeam::class)
        ->set('data.name', 'New Test Team')
        ->set('data.slug', 'new-test-team')
        ->call('register')
        ->assertHasNoFormErrors();

    assertDatabaseHas(Team::class, [
        'name' => 'New Test Team',
        'slug' => 'new-test-team',
    ]);
});

it('automatically generates slug from name when creating team', function (): void {
    $team = Team::factory()->create(['name' => 'Test Team']);
    $admin = User::factory()->create();
    $admin->teams()->attach($team);
    $admin->assignRole('Super-Admin');

    actingAs($admin);
    Filament::setTenant($team);

    Livewire::actingAs($admin)
        ->test(RegisterTeam::class)
        ->set('data.name', 'Amazing New Team')
        ->assertSet('data.slug', 'amazing-new-team');
});

it('automatically attaches creator to new team', function (): void {
    $team = Team::factory()->create(['name' => 'Test Team']);
    $admin = User::factory()->create();
    $admin->teams()->attach($team);
    $admin->assignRole('Super-Admin');

    actingAs($admin);
    Filament::setTenant($team);

    Livewire::actingAs($admin)
        ->test(RegisterTeam::class)
        ->set('data.name', 'Creator Test Team')
        ->set('data.slug', 'creator-test-team')
        ->call('register');

    $createdTeam = Team::query()->where('slug', 'creator-test-team')->first();
    expect($createdTeam->users->contains($admin))->toBeTrue();
});

it('validates required fields when creating team', function (): void {
    $team = Team::factory()->create(['name' => 'Test Team']);
    $admin = User::factory()->create();
    $admin->teams()->attach($team);
    $admin->assignRole('Super-Admin');

    actingAs($admin);
    Filament::setTenant($team);

    Livewire::actingAs($admin)
        ->test(RegisterTeam::class)
        ->set('data.name', '')
        ->set('data.slug', '')
        ->call('register')
        ->assertHasFormErrors(['name' => 'required', 'slug' => 'required']);
});

it('validates unique slug when creating team', function (): void {
    $team = Team::factory()->create(['name' => 'Test Team']);
    $admin = User::factory()->create();
    $admin->teams()->attach($team);
    $admin->assignRole('Super-Admin');

    actingAs($admin);
    Filament::setTenant($team);

    Team::factory()->create(['slug' => 'existing-slug']);

    Livewire::actingAs($admin)
        ->test(RegisterTeam::class)
        ->set('data.name', 'Test Team')
        ->set('data.slug', 'existing-slug')
        ->call('register')
        ->assertHasFormErrors(['slug']);
});

it('validates slug format (alpha-dash)', function (): void {
    $team = Team::factory()->create(['name' => 'Test Team']);
    $admin = User::factory()->create();
    $admin->teams()->attach($team);
    $admin->assignRole('Super-Admin');

    actingAs($admin);
    Filament::setTenant($team);

    Livewire::actingAs($admin)
        ->test(RegisterTeam::class)
        ->set('data.name', 'Test Team')
        ->set('data.slug', 'invalid slug!')
        ->call('register')
        ->assertHasFormErrors(['slug']);
});

it('non-admin users cannot access register team page', function (): void {
    $team = Team::factory()->create(['name' => 'Test Team']);
    $user = User::factory()->create();
    $user->teams()->attach($team);
    $user->assignRole('subscriber');

    actingAs($user);
    Filament::setTenant($team);

    Livewire::actingAs($user)
        ->test(RegisterTeam::class)
        ->assertStatus(404);
});

it('can render edit team profile page', function (): void {
    $team = Team::factory()->create(['name' => 'Test Team']);
    $admin = User::factory()->create();
    $admin->teams()->attach($team);
    $admin->assignRole('Super-Admin');

    actingAs($admin);
    Filament::setTenant($team);

    Livewire::actingAs($admin)
        ->test(EditTeamProfile::class, ['tenant' => $team->getRouteKey()])
        ->assertSuccessful();
});

it('can retrieve team data for editing', function (): void {
    $team = Team::factory()->create(['name' => 'Test Team']);
    $admin = User::factory()->create();
    $admin->teams()->attach($team);
    $admin->assignRole('Super-Admin');

    actingAs($admin);
    Filament::setTenant($team);

    Livewire::actingAs($admin)
        ->test(EditTeamProfile::class, ['tenant' => $team->getRouteKey()])
        ->assertSet('data.name', $team->name)
        ->assertSet('data.slug', $team->slug);
});

it('can update team profile', function (): void {
    $team = Team::factory()->create(['name' => 'Test Team']);
    $admin = User::factory()->create();
    $admin->teams()->attach($team);
    $admin->assignRole('Super-Admin');

    actingAs($admin);
    Filament::setTenant($team);

    Livewire::actingAs($admin)
        ->test(EditTeamProfile::class, ['tenant' => $team->getRouteKey()])
        ->set('data.name', 'Updated Team Name')
        ->set('data.slug', 'updated-team-slug')
        ->call('save')
        ->assertHasNoFormErrors();

    expect($team->refresh()->name)->toBe('Updated Team Name');
    expect($team->slug)->toBe('updated-team-slug');
});

it('validates unique slug when updating team (ignores current record)', function (): void {
    $team = Team::factory()->create(['name' => 'Test Team']);
    $admin = User::factory()->create();
    $admin->teams()->attach($team);
    $admin->assignRole('Super-Admin');

    actingAs($admin);
    Filament::setTenant($team);

    Team::factory()->create(['slug' => 'other-team-slug']);

    Livewire::actingAs($admin)
        ->test(EditTeamProfile::class, ['tenant' => $team->getRouteKey()])
        ->set('data.name', $team->name)
        ->set('data.slug', $team->slug)
        ->call('save')
        ->assertHasNoFormErrors();

    Livewire::actingAs($admin)
        ->test(EditTeamProfile::class, ['tenant' => $team->getRouteKey()])
        ->set('data.name', 'Test')
        ->set('data.slug', 'other-team-slug')
        ->call('save')
        ->assertHasFormErrors(['slug']);
});

it('non-admin users cannot access edit team profile page', function (): void {
    $team = Team::factory()->create(['name' => 'Test Team']);
    $user = User::factory()->create();
    $user->teams()->attach($team);
    $user->assignRole('subscriber');

    actingAs($user);
    Filament::setTenant($team);

    Livewire::actingAs($user)
        ->test(EditTeamProfile::class, ['tenant' => $team->getRouteKey()])
        ->assertStatus(404);
});

it('validates required fields when updating team', function (): void {
    $team = Team::factory()->create(['name' => 'Test Team']);
    $admin = User::factory()->create();
    $admin->teams()->attach($team);
    $admin->assignRole('Super-Admin');

    actingAs($admin);
    Filament::setTenant($team);

    Livewire::actingAs($admin)
        ->test(EditTeamProfile::class, ['tenant' => $team->getRouteKey()])
        ->set('data.name', '')
        ->set('data.slug', '')
        ->call('save')
        ->assertHasFormErrors(['name' => 'required', 'slug' => 'required']);
});
