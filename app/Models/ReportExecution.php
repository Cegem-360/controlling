<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\ReportExecutionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class ReportExecution extends Model
{
    /** @use HasFactory<ReportExecutionFactory> */
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'report_id',
        'user_id',
        'parameters',
        'executed_at',
        'execution_time_ms',
        'row_count',
    ];

    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected function casts(): array
    {
        return [
            'parameters' => 'array',
            'executed_at' => 'datetime',
            'execution_time_ms' => 'integer',
            'row_count' => 'integer',
        ];
    }
}
