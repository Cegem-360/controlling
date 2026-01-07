<?php

declare(strict_types=1);

use App\Models\Team;
use App\Models\User;
use Filament\Facades\Filament;

use function Pest\Laravel\actingAs;

it('can create a team', function (): void {
    $team = Team::factory()->create([
        'name' => 'Test Team',
        'slug' => 'test-team',
    ]);

    expect($team)->toBeInstanceOf(Team::class)
        ->and($team->name)->toBe('Test Team')
        ->and($team->slug)->toBe('test-team');
});

it('can associate users with teams', function (): void {
    $team = Team::factory()->create();
    $user = User::factory()->create();
    $user->teams()->attach($team);

    expect($user->teams)->toHaveCount(1)
        ->and($user->teams->first()->id)->toBe($team->id);
});

it('user can access tenant they belong to', function (): void {
    $team = Team::factory()->create();
    $user = User::factory()->create();
    $user->teams()->attach($team);

    actingAs($user);

    expect($user->canAccessTenant($team))->toBeTrue();
});

it('user cannot access tenant they do not belong to', function (): void {
    $team = Team::factory()->create();
    $user = User::factory()->create();
    $user->teams()->attach($team);

    actingAs($user);

    $otherTeam = Team::factory()->create();

    expect($user->canAccessTenant($otherTeam))->toBeFalse();
});

it('user can get their tenants', function (): void {
    $team = Team::factory()->create();
    $user = User::factory()->create();
    $user->teams()->attach($team);

    actingAs($user);

    $tenants = $user->getTenants(Filament::getPanel('admin'));

    expect($tenants)->toHaveCount(1)
        ->and($tenants->first()->id)->toBe($team->id);
});

it('user can belong to multiple teams', function (): void {
    $team1 = Team::factory()->create();
    $team2 = Team::factory()->create();
    $team3 = Team::factory()->create();
    $user = User::factory()->create();

    $user->teams()->attach([$team1->id, $team2->id, $team3->id]);

    expect($user->teams)->toHaveCount(3);
});

it('team can have multiple users', function (): void {
    $team = Team::factory()->create();
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $user3 = User::factory()->create();

    $team->users()->attach([$user1->id, $user2->id, $user3->id]);

    expect($team->users)->toHaveCount(3);
});

it('deleting team deletes pivot records', function (): void {
    $team = Team::factory()->create();
    $user = User::factory()->create();
    $user->teams()->attach($team);

    $teamId = $team->id;

    $team->delete();

    expect(Team::query()->find($teamId))->toBeNull()
        ->and($user->fresh()->teams)->toHaveCount(0);
});
