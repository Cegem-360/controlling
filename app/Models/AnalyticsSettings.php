<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\GoogleAnalitycs\OrderByType;
use Database\Factories\AnalyticsSettingsFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class AnalyticsSettings extends Model
{
    /** @use HasFactory<AnalyticsSettingsFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'dimensions',
        'metrics',
        'order_by',
        'order_by_type',
        'order_by_direction',
    ];

    protected function casts(): array
    {
        return [
            'dimensions' => 'array',
            'metrics' => 'array',
            'order_by' => 'array',
            'order_by_type' => OrderByType::class,
        ];
    }
}
