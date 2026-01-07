<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\GlobalSettingFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

final class GlobalSetting extends Model
{
    /** @use HasFactory<GlobalSettingFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'google_service_account',
        'google_ads_client_id',
        'google_ads_client_secret',
        'google_ads_developer_token',
    ];

    /**
     * @var list<string>
     */
    protected $hidden = [
        'google_ads_client_secret',
        'google_ads_developer_token',
    ];

    /**
     * Get the singleton instance of global settings.
     */
    public static function instance(): self
    {
        return self::query()->firstOrCreate([]);
    }

    /**
     * Check if Google Ads OAuth is configured.
     */
    public function hasGoogleAdsCredentials(): bool
    {
        return $this->google_ads_client_id !== null
            && $this->google_ads_client_secret !== null
            && $this->google_ads_developer_token !== null;
    }

    /**
     * Get the service account credentials.
     *
     * @return array<string, mixed>|null
     */
    public function getServiceAccount(): ?array
    {
        if (! $this->google_service_account) {
            return null;
        }

        /** @var array<string, mixed>|null */
        return Storage::json($this->google_service_account);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'google_ads_client_secret' => 'encrypted',
            'google_ads_developer_token' => 'encrypted',
        ];
    }
}
