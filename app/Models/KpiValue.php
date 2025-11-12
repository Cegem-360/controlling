<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\KpiValueFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class KpiValue extends Model
{
    /** @use HasFactory<KpiValueFactory> */
    use HasFactory;

    protected $fillable = [
        'kpi_id',
        'period',
        'planned_value',
        'actual_value',
        'variance',
        'variance_percentage',
    ];

    public function kpi(): BelongsTo
    {
        return $this->belongsTo(Kpi::class);
    }

    /**
     * Calculate variance between planned and actual values.
     */
    public function calculateVariance(): void
    {
        if ($this->planned_value !== null) {
            $this->variance = $this->actual_value - $this->planned_value;
            $this->variance_percentage = $this->planned_value !== 0
                ? ($this->variance / $this->planned_value) * 100
                : 0;
        }
    }

    protected function casts(): array
    {
        return [
            'period' => 'date',
            'planned_value' => 'decimal:2',
            'actual_value' => 'decimal:2',
            'variance' => 'decimal:2',
            'variance_percentage' => 'decimal:2',
        ];
    }
}
