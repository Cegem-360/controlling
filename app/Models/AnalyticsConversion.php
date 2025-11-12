<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\AnalyticsConversionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class AnalyticsConversion extends Model
{
    /** @use HasFactory<AnalyticsConversionFactory> */
    use HasFactory;

    protected $fillable = [
        'date',
        'goal_name',
        'goal_completions',
        'goal_value',
        'conversion_rate',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'goal_completions' => 'integer',
            'goal_value' => 'decimal:2',
            'conversion_rate' => 'decimal:2',
        ];
    }
}
