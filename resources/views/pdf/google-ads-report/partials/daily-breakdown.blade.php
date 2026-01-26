@php
    $maxImpressions = $data->dailyStats->max('impressions') ?: 1;
    $maxClicks = $data->dailyStats->max('clicks') ?: 1;
    $maxConversions = $data->dailyStats->max('conversions') ?: 1;

    $totals = [
        'impressions' => $data->dailyStats->sum('impressions'),
        'clicks' => $data->dailyStats->sum('clicks'),
        'cost' => $data->dailyStats->sum('cost'),
        'conversions' => $data->dailyStats->sum('conversions'),
    ];
    $totals['ctr'] = $totals['impressions'] > 0 ? round(($totals['clicks'] / $totals['impressions']) * 100, 2) : 0;
    $totals['avg_cpc'] = $totals['clicks'] > 0 ? round($totals['cost'] / $totals['clicks'], 2) : 0;
    $totals['cost_per_conversion'] = $totals['conversions'] > 0 ? round($totals['cost'] / $totals['conversions'], 2) : 0;
    $totals['conversion_rate'] = $totals['clicks'] > 0 ? round(($totals['conversions'] / $totals['clicks']) * 100, 2) : 0;

    $chartData = $data->dailyStats->sortBy(fn($item) => $item['date'])->values();
@endphp

{{-- Bar Charts --}}
<div class="chart-container">
    <div style="font-size: 8px; font-weight: bold; margin-bottom: 5px;">{{ __('Megjelenítések') }}:</div>
    <div class="bar-chart">
        @foreach($chartData as $day)
        <div class="bar-item">
            <div class="bar" style="height: {{ ($day['impressions'] / $maxImpressions) * 60 }}px;"></div>
            <div class="bar-label">{{ $day['date']->format('M j.') }}</div>
        </div>
        @endforeach
    </div>
</div>

<div class="chart-container">
    <div style="font-size: 8px; font-weight: bold; margin-bottom: 5px;">{{ __('Kattintások') }}</div>
    <div class="bar-chart">
        @foreach($chartData as $day)
        <div class="bar-item">
            <div class="bar" style="height: {{ ($day['clicks'] / $maxClicks) * 60 }}px;"></div>
            <div class="bar-label">{{ $day['date']->format('M j.') }}</div>
        </div>
        @endforeach
    </div>
</div>

<div class="chart-container">
    <div style="font-size: 8px; font-weight: bold; margin-bottom: 5px;">{{ __('Konverziók') }}</div>
    <div class="bar-chart">
        @foreach($chartData as $day)
        <div class="bar-item">
            <div class="bar" style="height: {{ $maxConversions > 0 ? ($day['conversions'] / $maxConversions) * 60 : 0 }}px; background: #4caf50;"></div>
            <div class="bar-label">{{ $day['date']->format('M j.') }}</div>
        </div>
        @endforeach
    </div>
</div>

{{-- Table --}}
<table>
    <thead>
        <tr>
            <th>{{ __('Nap') }}</th>
            <th>{{ __('a hét napja') }}</th>
            <th class="number">{{ __('Megjelenítések') }}:</th>
            <th class="number">{{ __('Kattintások') }}</th>
            <th class="number">{{ __('Átl. CPC') }}</th>
            <th class="number">{{ __('CTR') }}</th>
            <th class="number">{{ __('Konverziók') }}</th>
            <th class="number">{{ __('Költség/konv.') }}</th>
            <th class="number">{{ __('Konv. arány') }}</th>
            <th class="number">{{ __('Költség') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data->dailyStats as $day)
        <tr>
            <td>{{ $day['date']->format('Y. M j.') }}</td>
            <td>{{ ucfirst($day['day_name']) }}</td>
            <td class="number">{{ number_format($day['impressions'], 0, ',', ' ') }}</td>
            <td class="number {{ $day['clicks'] > ($totals['clicks'] / max(1, $data->dailyStats->count())) ? 'highlight-cell' : '' }}">{{ number_format($day['clicks'], 0, ',', ' ') }}</td>
            <td class="number">{{ number_format($day['avg_cpc'], 2, ',', ' ') }} Ft</td>
            <td class="number">{{ number_format($day['ctr'], 2, ',', ' ') }}%</td>
            <td class="number {{ $day['conversions'] > 0 ? 'highlight-cell' : '' }}">{{ number_format($day['conversions'], 0, ',', ' ') }}</td>
            <td class="number">{{ number_format($day['cost_per_conversion'], 2, ',', ' ') }} Ft</td>
            <td class="number">{{ number_format($day['conversion_rate'], 2, ',', ' ') }}%</td>
            <td class="number">{{ number_format($day['cost'], 2, ',', ' ') }} Ft</td>
        </tr>
        @endforeach
        <tr class="total-row">
            <td colspan="2">{{ __('Mindösszesen') }}</td>
            <td class="number">{{ number_format($totals['impressions'], 0, ',', ' ') }}</td>
            <td class="number">{{ number_format($totals['clicks'], 0, ',', ' ') }}</td>
            <td class="number">{{ number_format($totals['avg_cpc'], 2, ',', ' ') }} Ft</td>
            <td class="number">{{ number_format($totals['ctr'], 2, ',', ' ') }}%</td>
            <td class="number">{{ number_format($totals['conversions'], 0, ',', ' ') }}</td>
            <td class="number">{{ number_format($totals['cost_per_conversion'], 2, ',', ' ') }} Ft</td>
            <td class="number">{{ number_format($totals['conversion_rate'], 2, ',', ' ') }}%</td>
            <td class="number">{{ number_format($totals['cost'], 2, ',', ' ') }} Ft</td>
        </tr>
    </tbody>
</table>
