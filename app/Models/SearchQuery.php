<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\SearchQueryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class SearchQuery extends Model
{
    /** @use HasFactory<SearchQueryFactory> */
    use HasFactory;

    protected $fillable = [
        'date',
        'query',
        'country',
        'device',
        'impressions',
        'clicks',
        'ctr',
        'position',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'impressions' => 'integer',
            'clicks' => 'integer',
            'ctr' => 'decimal:2',
            'position' => 'decimal:2',
        ];
    }
}
