<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\AnalyticsPageviewFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class AnalyticsPageview extends Model
{
    /** @use HasFactory<AnalyticsPageviewFactory> */
    use HasFactory;

    protected $fillable = [
        'date',
        'page_path',
        'page_title',
        'pageviews',
        'unique_pageviews',
        'avg_time_on_page',
        'entrances',
        'bounce_rate',
        'exit_rate',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'pageviews' => 'integer',
            'unique_pageviews' => 'integer',
            'avg_time_on_page' => 'integer',
            'entrances' => 'integer',
            'bounce_rate' => 'decimal:2',
            'exit_rate' => 'decimal:2',
        ];
    }
}
