<?php

declare(strict_types=1);

use App\Http\Controllers\GoogleAdsOAuthController;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Route;

Route::get('/', fn (): View|Factory => view('home'))->name('home');

Route::get('/language/{locale}', function (string $locale) {
    if (! in_array($locale, ['en', 'hu'], true)) {
        abort(400);
    }

    $cookie = cookie('locale', $locale, 60 * 24 * 365);

    $referer = request()->headers->get('referer');
    $redirectUrl = $referer ?: url()->previous();

    return redirect($redirectUrl)->withCookie($cookie);
})->name('language.switch');

// Google Ads OAuth Routes
Route::middleware(['auth', 'verified'])->group(function (): void {
    Route::get('/google-ads/auth/redirect/{team}', [GoogleAdsOAuthController::class, 'redirect'])
        ->name('google-ads.oauth.redirect');
    Route::get('/google-ads/auth/callback', [GoogleAdsOAuthController::class, 'callback'])
        ->name('google-ads.oauth.callback');
    Route::post('/google-ads/auth/disconnect/{team}', [GoogleAdsOAuthController::class, 'disconnect'])
        ->name('google-ads.oauth.disconnect');
});
