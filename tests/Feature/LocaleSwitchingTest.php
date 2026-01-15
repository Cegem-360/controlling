<?php

declare(strict_types=1);

use Illuminate\Support\Facades\App;

it('can switch locale to hungarian', function (): void {
    $this->get(route('language.switch', 'hu'))
        ->assertRedirect()
        ->assertCookie('locale', 'hu');
});

it('can switch locale to english', function (): void {
    $this->get(route('language.switch', 'en'))
        ->assertRedirect()
        ->assertCookie('locale', 'en');
});

it('rejects invalid locale', function (): void {
    $this->get(route('language.switch', 'invalid'))
        ->assertStatus(400);
});

it('sets app locale from cookie', function (): void {
    $this->withCookie('locale', 'hu')
        ->get(route('home'))
        ->assertOk();

    expect(App::getLocale())->toBe('hu');
});

it('uses default locale when no cookie is set', function (): void {
    $this->get(route('home'))
        ->assertOk();

    expect(App::getLocale())->toBe(config('app.locale'));
});

it('shows language switcher on home page', function (): void {
    $this->get(route('home'))
        ->assertOk()
        ->assertSee('English')
        ->assertSee('Magyar');
});

it('shows translated content when locale is hungarian', function (): void {
    $this->withCookie('locale', 'hu')
        ->get(route('home'))
        ->assertOk()
        ->assertSee('Funkciók');
});

it('shows english content when locale is english', function (): void {
    $this->withCookie('locale', 'en')
        ->get(route('home'))
        ->assertOk()
        ->assertSee('Features');
});
