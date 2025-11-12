<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\SearchPageFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class SearchPage extends Model
{
    /** @use HasFactory<SearchPageFactory> */
    use HasFactory;

    protected $fillable = [
        'date',
        'page_url',
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
