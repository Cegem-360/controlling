<?php

declare(strict_types=1);

use App\Http\Controllers\GoogleAdsOAuthController;
use App\Livewire\Dashboard;
use App\Livewire\Pages\Analytics\Dashboard as AnalyticsDashboard;
use App\Livewire\Pages\Analytics\GeneralStats as AnalyticsGeneralStats;
use App\Livewire\Pages\Analytics\Statistics as AnalyticsStatistics;
use App\Livewire\Pages\GoogleAds\Dashboard as GoogleAdsDashboard;
use App\Livewire\Pages\Kpis\Index as KpisIndex;
use App\Livewire\Pages\SearchConsole\GeneralStats as SearchConsoleGeneralStats;
use App\Livewire\Pages\SearchConsole\Pages as SearchConsolePages;
use App\Livewire\Pages\SearchConsole\Queries as SearchConsoleQueries;
use App\Livewire\Pages\Settings;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Route;

Route::get('/', fn (): View|Factory => view('home'))->name('home');

// User Dashboard
Route::middleware(['auth', 'verified'])->group(function (): void {
    Route::get('/dashboard', Dashboard::class)->name('dashboard');

    // Analytics
    Route::get('/analytics/general', AnalyticsGeneralStats::class)->name('analytics.general');
    Route::get('/analytics/statistics', AnalyticsStatistics::class)->name('analytics.statistics');
    Route::get('/analytics/dashboard', AnalyticsDashboard::class)->name('analytics.dashboard');

    // Search Console
    Route::get('/search-console/general', SearchConsoleGeneralStats::class)->name('search-console.general');
    Route::get('/search-console/pages', SearchConsolePages::class)->name('search-console.pages');
    Route::get('/search-console/queries', SearchConsoleQueries::class)->name('search-console.queries');

    // Google Ads
    Route::get('/google-ads/dashboard', GoogleAdsDashboard::class)->name('google-ads.dashboard');

    // KPIs
    Route::get('/kpis', KpisIndex::class)->name('kpis.index');

    // Settings
    Route::get('/settings', Settings::class)->name('settings');
});

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
