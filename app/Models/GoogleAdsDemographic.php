<?php

declare(strict_types=1);

namespace App\Models;

use App\Observers\TenantAwareObserver;
use Database\Factories\GoogleAdsDemographicFactory;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[ObservedBy(TenantAwareObserver::class)]
final class GoogleAdsDemographic extends Model
{
    /** @use HasFactory<GoogleAdsDemographicFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'team_id',
        'date',
        'gender',
        'age_range',
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
     * Get a human-readable gender name.
     */
    public function getGenderNameAttribute(): string
    {
        return match ($this->gender) {
            'MALE' => __('Male'),
            'FEMALE' => __('Female'),
            'UNDETERMINED' => __('Undetermined'),
            default => $this->gender ?? __('Unknown'),
        };
    }

    /**
     * Get a human-readable age range name.
     */
    public function getAgeRangeNameAttribute(): string
    {
        return match ($this->age_range) {
            'AGE_RANGE_18_24' => '18-24',
            'AGE_RANGE_25_34' => '25-34',
            'AGE_RANGE_35_44' => '35-44',
            'AGE_RANGE_45_54' => '45-54',
            'AGE_RANGE_55_64' => '55-64',
            'AGE_RANGE_65_UP' => '65+',
            'AGE_RANGE_UNDETERMINED' => __('Undetermined'),
            default => $this->age_range ?? __('Unknown'),
        };
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
