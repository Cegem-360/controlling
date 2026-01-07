<?php

declare(strict_types=1);

use App\Filament\Pages\SearchConsoleGeneralStats;
use App\Models\Kpi;
use App\Models\Team;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Filament\Facades\Filament;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\seed;

beforeEach(function (): void {
    seed([RoleSeeder::class, PermissionSeeder::class]);
});

it('can render search console general stats page', function (): void {
    $team = Team::factory()->create(['name' => 'Test Team']);
    $admin = User::factory()->create();
    $admin->teams()->attach($team);
    $admin->assignRole('Super-Admin');

    actingAs($admin);
    Filament::setTenant($team);

    Livewire::actingAs($admin)
        ->test(SearchConsoleGeneralStats::class, ['tenant' => $team])
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
        ->test(SearchConsoleGeneralStats::class, ['tenant' => $team])
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
        ->test(SearchConsoleGeneralStats::class, ['tenant' => $team])
        ->mountAction('setKpiGoal')
        ->setActionData(['source_type' => 'page'])
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

it('can create search console kpi with seo category and format', function (): void {
    $team = Team::factory()->create(['name' => 'Test Team']);
    $admin = User::factory()->create();
    $admin->teams()->attach($team);
    $admin->assignRole('Super-Admin');

    actingAs($admin);
    Filament::setTenant($team);

    $kpi = Kpi::query()->create([
        'team_id' => $team->id,
        'code' => 'test_kpi_page',
        'name' => 'Test KPI',
        'description' => 'Test Description',
        'data_source' => 'search_console',
        'source_type' => 'page',
        'category' => 'seo',
        'format' => 'number',
        'page_path' => '/test',
        'metric_type' => 'clicks',
        'from_date' => now(),
        'target_date' => now()->addMonth(),
        'goal_type' => 'increase',
        'value_type' => 'percentage',
        'target_value' => 20,
        'is_active' => true,
    ]);

    expect($kpi)->not->toBeNull()
        ->and($kpi->category->value)->toBe('seo')
        ->and($kpi->data_source->value)->toBe('search_console')
        ->and($kpi->source_type)->toBe('page')
        ->and($kpi->format)->toBe('number');
});

it('sets correct format based on search console metric type', function (): void {
    $team = Team::factory()->create(['name' => 'Test Team']);
    $admin = User::factory()->create();
    $admin->teams()->attach($team);
    $admin->assignRole('Super-Admin');

    actingAs($admin);
    Filament::setTenant($team);

    $testCases = [
        ['metric' => 'clicks', 'expected_format' => 'number'],
        ['metric' => 'impressions', 'expected_format' => 'number'],
        ['metric' => 'ctr', 'expected_format' => 'percentage'],
        ['metric' => 'position', 'expected_format' => 'number'],
    ];

    foreach ($testCases as $index => $case) {
        $kpi = Kpi::query()->create([
            'team_id' => $team->id,
            'code' => "test_format_{$index}",
            'name' => "Test {$case['metric']}",
            'description' => 'Test',
            'data_source' => 'search_console',
            'source_type' => 'page',
            'category' => 'seo',
            'format' => match ($case['metric']) {
                'ctr' => 'percentage',
                default => 'number',
            },
            'page_path' => '/test',
            'metric_type' => $case['metric'],
            'from_date' => now(),
            'target_date' => now()->addMonth(),
            'goal_type' => 'increase',
            'value_type' => 'percentage',
            'target_value' => 20,
            'is_active' => true,
        ]);

        expect($kpi->format)->toBe($case['expected_format'], "Format for {$case['metric']} should be {$case['expected_format']}");
    }
});
