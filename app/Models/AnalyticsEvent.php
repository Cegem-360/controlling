<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\AnalyticsEventFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class AnalyticsEvent extends Model
{
    /** @use HasFactory<AnalyticsEventFactory> */
    use HasFactory;

    protected $fillable = [
        'date',
        'event_name',
        'event_category',
        'event_action',
        'event_label',
        'event_count',
        'event_value',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'event_count' => 'integer',
            'event_value' => 'decimal:2',
        ];
    }
}
