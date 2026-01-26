<?php

declare(strict_types=1);

namespace App\Models;

use App\Observers\TenantAwareObserver;
use Database\Factories\GoogleAdsDeviceStatFactory;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[ObservedBy(TenantAwareObserver::class)]
final class GoogleAdsDeviceStat extends Model
{
    /** @use HasFactory<GoogleAdsDeviceStatFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'team_id',
        'date',
        'device',
        'impressions',
        'clicks',
        'cost',
        'conversions',
        'ctr',
        'avg_cpc',
        'conversion_rate',
    ];

    /**
     * @return BelongsTo<Team, $this>
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * Get a human-readable device name.
     */
    protected function deviceName(): Attribute
    {
        return Attribute::make(get: fn () => match ($this->device) {
            'MOBILE' => __('Mobile'),
            'DESKTOP' => __('Desktop'),
            'TABLET' => __('Tablet'),
            'CONNECTED_TV' => __('Connected TV'),
            default => $this->device,
        });
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'date' => 'date',
            'impressions' => 'integer',
            'clicks' => 'integer',
            'cost' => 'decimal:2',
            'conversions' => 'decimal:2',
            'ctr' => 'decimal:4',
            'avg_cpc' => 'decimal:2',
            'conversion_rate' => 'decimal:4',
        ];
    }
}
