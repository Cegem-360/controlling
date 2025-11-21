<?php

declare(strict_types=1);

use App\Models\Team;
use App\Models\User;

beforeEach(function () {
    config(['services.subscriber_api_key' => 'test-api-key']);
});

describe('create', function () {
    it('creates a team successfully', function () {
        $response = $this->postJson('/api/create-team', [
            'name' => 'Test Team',
            'slug' => 'test-team',
        ], ['Authorization' => 'Bearer test-api-key']);

        $response->assertCreated()
            ->assertJsonStructure(['message', 'team_id']);

        $this->assertDatabaseHas('teams', [
            'name' => 'Test Team',
            'slug' => 'test-team',
        ]);
    });

    it('creates a team and attaches user when email provided', function () {
        $user = User::factory()->create(['email' => 'user@example.com']);

        $response = $this->postJson('/api/create-team', [
            'name' => 'Test Team',
            'slug' => 'test-team',
            'user_email' => 'user@example.com',
        ], ['Authorization' => 'Bearer test-api-key']);

        $response->assertCreated();

        $team = Team::where('slug', 'test-team')->first();
        expect($user->teams->contains($team))->toBeTrue();
    });

    it('returns unauthorized without api key', function () {
        $response = $this->postJson('/api/create-team', [
            'name' => 'Test Team',
            'slug' => 'test-team',
        ]);

        $response->assertUnauthorized();
    });

    it('validates required fields', function (string $field) {
        $data = [
            'name' => 'Test Team',
            'slug' => 'test-team',
        ];

        unset($data[$field]);

        $response = $this->postJson('/api/create-team', $data, [
            'Authorization' => 'Bearer test-api-key',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors($field);
    })->with(['name', 'slug']);

    it('validates slug uniqueness', function () {
        Team::factory()->create(['slug' => 'existing-slug']);

        $response = $this->postJson('/api/create-team', [
            'name' => 'Test Team',
            'slug' => 'existing-slug',
        ], ['Authorization' => 'Bearer test-api-key']);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors('slug');
    });

    it('validates user_email exists', function () {
        $response = $this->postJson('/api/create-team', [
            'name' => 'Test Team',
            'slug' => 'test-team',
            'user_email' => 'nonexistent@example.com',
        ], ['Authorization' => 'Bearer test-api-key']);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors('user_email');
    });
});
