<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\GoogleAdsSettingsFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class GoogleAdsSettings extends Model
{
    /** @use HasFactory<GoogleAdsSettingsFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'team_id',
        'customer_id',
        'access_token',
        'refresh_token',
        'token_expires_at',
        'is_connected',
        'last_sync_at',
    ];

    /**
     * @var list<string>
     */
    protected $hidden = [
        'access_token',
        'refresh_token',
    ];

    /**
     * @return BelongsTo<Team, $this>
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * Check if the OAuth token is expired or about to expire.
     */
    public function isTokenExpired(): bool
    {
        if (! $this->token_expires_at) {
            return true;
        }

        return $this->token_expires_at->subMinutes(5)->isPast();
    }

    /**
     * Check if the settings have valid credentials for API calls.
     */
    public function hasValidCredentials(): bool
    {
        return $this->is_connected
            && $this->refresh_token !== null
            && $this->customer_id !== null;
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'access_token' => 'encrypted',
            'refresh_token' => 'encrypted',
            'token_expires_at' => 'datetime',
            'is_connected' => 'boolean',
            'last_sync_at' => 'datetime',
        ];
    }
}
