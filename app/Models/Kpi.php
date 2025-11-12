<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\KpiCategory;
use App\Enums\KpiDataSource;
use App\Enums\KpiFormat;
use Database\Factories\KpiFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Kpi extends Model
{
    /** @use HasFactory<KpiFactory> */
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
        'data_source',
        'formula',
        'category',
        'format',
        'target_value',
        'is_active',
    ];

    public function kpiValues(): HasMany
    {
        return $this->hasMany(KpiValue::class);
    }

    protected function casts(): array
    {
        return [
            'data_source' => KpiDataSource::class,
            'category' => KpiCategory::class,
            'format' => KpiFormat::class,
            'target_value' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }
}
