<?php

declare(strict_types=1);

namespace App\Models;

use App\Observers\TenantAwareObserver;
use Database\Factories\GoogleAdsHourlyStatFactory;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[ObservedBy(TenantAwareObserver::class)]
final class GoogleAdsHourlyStat extends Model
{
    /** @use HasFactory<GoogleAdsHourlyStatFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'team_id',
        'date',
        'hour',
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
            'hour' => 'integer',
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
