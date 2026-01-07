<?php

declare(strict_types=1);

namespace App\Models;

use App\Observers\TenantAwareObserver;
use Database\Factories\GoogleAdsAdGroupFactory;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[ObservedBy(TenantAwareObserver::class)]
final class GoogleAdsAdGroup extends Model
{
    /** @use HasFactory<GoogleAdsAdGroupFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'team_id',
        'date',
        'campaign_id',
        'campaign_name',
        'ad_group_id',
        'ad_group_name',
        'ad_group_status',
        'impressions',
        'clicks',
        'cost',
        'avg_cpc',
        'ctr',
        'conversions',
        'conversion_value',
        'cost_per_conversion',
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
            'avg_cpc' => 'decimal:2',
            'ctr' => 'decimal:4',
            'conversions' => 'decimal:2',
            'conversion_value' => 'decimal:2',
            'cost_per_conversion' => 'decimal:2',
            'conversion_rate' => 'decimal:4',
        ];
    }
}
