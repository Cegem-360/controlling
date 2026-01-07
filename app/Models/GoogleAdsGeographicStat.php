<?php

declare(strict_types=1);

namespace App\Models;

use App\Observers\TenantAwareObserver;
use Database\Factories\GoogleAdsGeographicStatFactory;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[ObservedBy(TenantAwareObserver::class)]
final class GoogleAdsGeographicStat extends Model
{
    /** @use HasFactory<GoogleAdsGeographicStatFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'team_id',
        'date',
        'geo_target_constant',
        'location_name',
        'country_code',
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
