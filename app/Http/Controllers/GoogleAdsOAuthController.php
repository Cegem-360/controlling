<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Team;
use App\Services\GoogleAdsOAuthService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

final class GoogleAdsOAuthController extends Controller
{
    public function __construct(
        private readonly GoogleAdsOAuthService $oauthService,
    ) {}

    /**
     * Redirect to Google OAuth consent screen.
     */
    public function redirect(Team $team): RedirectResponse
    {
        try {
            $authUrl = $this->oauthService->getAuthorizationUrl($team);

            return redirect()->away($authUrl);
        } catch (Exception $e) {
            Log::error('Google Ads OAuth redirect failed', ['error' => $e->getMessage()]);

            return to_route('filament.admin.pages.settings', ['tenant' => $team])
                ->with('error', __('Failed to initiate Google Ads connection: :message', ['message' => $e->getMessage()]));
        }
    }

    /**
     * Handle OAuth callback from Google.
     */
    public function callback(Request $request): RedirectResponse
    {
        $teamId = (int) $request->get('state');
        $team = Team::query()->find($teamId);

        if ($request->has('error')) {
            Log::error('Google Ads OAuth callback error', ['error' => $request->get('error')]);

            return to_route('filament.admin.pages.settings', ['tenant' => $team])
                ->with('error', __('Google Ads connection was denied or failed.'));
        }

        $code = $request->get('code');

        if (! $code || ! $team) {
            return to_route('filament.admin.pages.dashboard')
                ->with('error', __('Invalid OAuth callback parameters.'));
        }

        try {
            $this->oauthService->handleCallback($code, $teamId);

            return to_route('filament.admin.pages.settings', ['tenant' => $team])
                ->with('success', __('Google Ads account connected successfully!'));
        } catch (Exception $e) {
            Log::error('Google Ads OAuth callback failed', ['error' => $e->getMessage()]);

            return to_route('filament.admin.pages.settings', ['tenant' => $team])
                ->with('error', __('Failed to connect Google Ads: :message', ['message' => $e->getMessage()]));
        }
    }

    /**
     * Disconnect Google Ads account.
     */
    public function disconnect(Team $team): RedirectResponse
    {
        try {
            $this->oauthService->disconnect($team);

            return to_route('filament.admin.pages.settings', ['tenant' => $team])
                ->with('success', __('Google Ads account disconnected successfully.'));
        } catch (Exception $e) {
            Log::error('Google Ads disconnect failed', ['error' => $e->getMessage()]);

            return to_route('filament.admin.pages.settings', ['tenant' => $team])
                ->with('error', __('Failed to disconnect Google Ads: :message', ['message' => $e->getMessage()]));
        }
    }
}
