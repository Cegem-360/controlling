<?php

declare(strict_types=1);

use App\Models\AnalyticsPageview;
use App\Models\Kpi;
use App\Models\SearchPage;
use App\Models\Team;
use App\Models\User;
use Filament\Facades\Filament;

use function Pest\Laravel\actingAs;

it('scopes kpis to current tenant', function (): void {
    $team1 = Team::factory()->create(['name' => 'Team 1']);
    $team2 = Team::factory()->create(['name' => 'Team 2']);
    $user = User::factory()->create();
    $user->teams()->attach([$team1->id, $team2->id]);
    actingAs($user);

    $kpi1 = Kpi::factory()->create(['team_id' => $team1->id, 'name' => 'Team 1 KPI']);
    Kpi::factory()->create(['team_id' => $team2->id, 'name' => 'Team 2 KPI']);

    Filament::setTenant($team1);
    Kpi::addGlobalScope('team', fn ($query) => $query->where('team_id', $team1->id));

    $kpis = Kpi::all();

    expect($kpis)->toHaveCount(1)
        ->and($kpis->first()->id)->toBe($kpi1->id)
        ->and($kpis->first()->name)->toBe('Team 1 KPI');
});

it('scopes search pages to current tenant', function (): void {
    $team1 = Team::factory()->create(['name' => 'Team 1']);
    $team2 = Team::factory()->create(['name' => 'Team 2']);
    $user = User::factory()->create();
    $user->teams()->attach([$team1->id, $team2->id]);
    actingAs($user);

    SearchPage::factory()->create(['team_id' => $team1->id, 'page_url' => 'team1.com']);
    $page2 = SearchPage::factory()->create(['team_id' => $team2->id, 'page_url' => 'team2.com']);

    Filament::setTenant($team2);
    SearchPage::addGlobalScope('team', fn ($query) => $query->where('team_id', $team2->id));

    $pages = SearchPage::all();

    expect($pages)->toHaveCount(1)
        ->and($pages->first()->id)->toBe($page2->id)
        ->and($pages->first()->page_url)->toBe('team2.com');
});

it('scopes analytics pageviews to current tenant', function (): void {
    $team1 = Team::factory()->create(['name' => 'Team 1']);
    $team2 = Team::factory()->create(['name' => 'Team 2']);
    $user = User::factory()->create();
    $user->teams()->attach([$team1->id, $team2->id]);
    actingAs($user);

    $analytics1 = AnalyticsPageview::factory()->create(['team_id' => $team1->id, 'page_path' => '/team1']);
    AnalyticsPageview::factory()->create(['team_id' => $team2->id, 'page_path' => '/team2']);

    Filament::setTenant($team1);
    AnalyticsPageview::addGlobalScope('team', fn ($query) => $query->where('team_id', $team1->id));

    $pageviews = AnalyticsPageview::all();

    expect($pageviews)->toHaveCount(1)
        ->and($pageviews->first()->id)->toBe($analytics1->id)
        ->and($pageviews->first()->page_path)->toBe('/team1');
});

it('creates records with team_id', function (): void {
    $team = Team::factory()->create(['name' => 'Team 1']);
    $user = User::factory()->create();
    $user->teams()->attach($team);
    actingAs($user);

    $kpi = Kpi::factory()->create([
        'team_id' => $team->id,
        'name' => 'Team 1 KPI',
    ]);

    expect($kpi->team_id)->toBe($team->id)
        ->and($kpi->team)->toBeInstanceOf(Team::class)
        ->and($kpi->team->id)->toBe($team->id);
});

it('automatically assigns team_id when creating records with tenant set', function (): void {
    $team1 = Team::factory()->create(['name' => 'Team 1']);
    $team2 = Team::factory()->create(['name' => 'Team 2']);
    $user = User::factory()->create();
    $user->teams()->attach([$team1->id, $team2->id]);
    actingAs($user);

    Filament::setTenant($team1);

    $kpi = Kpi::query()->create([
        'code' => 'TEST_KPI',
        'name' => 'Test KPI',
        'data_source' => 'manual',
        'category' => 'traffic',
        'is_active' => true,
    ]);

    expect($kpi->team_id)->toBe($team1->id)
        ->and($kpi->team)->toBeInstanceOf(Team::class);

    Filament::setTenant($team2);

    $kpi2 = Kpi::query()->create([
        'code' => 'TEST_KPI_2',
        'name' => 'Test KPI 2',
        'data_source' => 'manual',
        'category' => 'conversion',
        'is_active' => true,
    ]);

    expect($kpi2->team_id)->toBe($team2->id)
        ->and($kpi2->team->id)->toBe($team2->id);
});

it('automatically assigns team_id to all tenant-scoped models', function (): void {
    $team = Team::factory()->create(['name' => 'Team 1']);
    $user = User::factory()->create();
    $user->teams()->attach($team);
    actingAs($user);

    Filament::setTenant($team);

    $searchPage = SearchPage::query()->create([
        'date' => now(),
        'page_url' => 'example.com/test',
        'country' => 'HU',
        'device' => 'desktop',
        'impressions' => 100,
        'clicks' => 10,
        'ctr' => 10.0,
        'position' => 5.5,
    ]);

    expect($searchPage->team_id)->toBe($team->id);

    $pageview = AnalyticsPageview::query()->create([
        'date' => now(),
        'page_path' => '/test',
        'page_title' => 'Test Page',
        'pageviews' => 50,
        'unique_pageviews' => 40,
        'avg_time_on_page' => 120,
        'entrances' => 30,
        'bounce_rate' => 25.0,
        'exit_rate' => 20.0,
    ]);

    expect($pageview->team_id)->toBe($team->id);
});
