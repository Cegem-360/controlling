<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\SavedQueryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class SavedQuery extends Model
{
    /** @use HasFactory<SavedQueryFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'query_type',
        'parameters',
        'query_definition',
        'is_parameterized',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected function casts(): array
    {
        return [
            'parameters' => 'array',
            'query_definition' => 'array',
            'is_parameterized' => 'boolean',
        ];
    }
}
