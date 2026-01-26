@php
    $maxImpressions = $data->hourlyStats->max('impressions') ?: 1;
    $maxClicks = $data->hourlyStats->max('clicks') ?: 1;
    $maxConversions = $data->hourlyStats->max('conversions') ?: 1;

    $totals = [
        'impressions' => $data->hourlyStats->sum('impressions'),
        'clicks' => $data->hourlyStats->sum('clicks'),
        'cost' => $data->hourlyStats->sum('cost'),
        'conversions' => $data->hourlyStats->sum('conversions'),
    ];
    $totals['ctr'] = $totals['impressions'] > 0 ? round(($totals['clicks'] / $totals['impressions']) * 100, 2) : 0;
    $totals['avg_cpc'] = $totals['clicks'] > 0 ? round($totals['cost'] / $totals['clicks'], 2) : 0;
    $totals['cost_per_conversion'] = $totals['conversions'] > 0 ? round($totals['cost'] / $totals['conversions'], 2) : 0;
    $totals['conversion_rate'] = $totals['clicks'] > 0 ? round(($totals['conversions'] / $totals['clicks']) * 100, 2) : 0;
@endphp

{{-- Bar Charts --}}
<div class="chart-container">
    <div style="font-size: 8px; font-weight: bold; margin-bottom: 5px;">{{ __('Megjelenítések') }}:</div>
    <div class="bar-chart">
        @foreach($data->hourlyStats as $hour)
        <div class="bar-item">
            <div class="bar" style="height: {{ ($hour['impressions'] / $maxImpressions) * 60 }}px;"></div>
            <div class="bar-label">{{ $hour['hour'] }}</div>
        </div>
        @endforeach
    </div>
</div>

<div class="chart-container">
    <div style="font-size: 8px; font-weight: bold; margin-bottom: 5px;">{{ __('Kattintások') }}</div>
    <div class="bar-chart">
        @foreach($data->hourlyStats as $hour)
        <div class="bar-item">
            <div class="bar" style="height: {{ ($hour['clicks'] / $maxClicks) * 60 }}px;"></div>
            <div class="bar-label">{{ $hour['hour'] }}</div>
        </div>
        @endforeach
    </div>
</div>

<div class="chart-container">
    <div style="font-size: 8px; font-weight: bold; margin-bottom: 5px;">{{ __('Konverziók') }}</div>
    <div class="bar-chart">
        @foreach($data->hourlyStats as $hour)
        <div class="bar-item">
            <div class="bar" style="height: {{ $maxConversions > 0 ? ($hour['conversions'] / $maxConversions) * 60 : 0 }}px; background: #4caf50;"></div>
            <div class="bar-label">{{ $hour['hour'] }}</div>
        </div>
        @endforeach
    </div>
</div>

{{-- Table --}}
<table>
    <thead>
        <tr>
            <th>{{ __('A nap órája') }}</th>
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
        @foreach($data->hourlyStats as $hour)
        <tr>
            <td>{{ $hour['hour'] }}</td>
            <td class="number">{{ number_format($hour['impressions'], 0, ',', ' ') }}</td>
            <td class="number">{{ number_format($hour['clicks'], 0, ',', ' ') }}</td>
            <td class="number">{{ number_format($hour['avg_cpc'], 2, ',', ' ') }} Ft</td>
            <td class="number">{{ number_format($hour['ctr'], 2, ',', ' ') }}%</td>
            <td class="number {{ $hour['conversions'] > 0 ? 'highlight-cell' : '' }}">{{ number_format($hour['conversions'], 0, ',', ' ') }}</td>
            <td class="number">{{ number_format($hour['cost_per_conversion'], 2, ',', ' ') }} Ft</td>
            <td class="number {{ $hour['conversion_rate'] > 5 ? 'highlight-cell' : '' }}">{{ number_format($hour['conversion_rate'], 2, ',', ' ') }}%</td>
            <td class="number">{{ number_format($hour['cost'], 2, ',', ' ') }} Ft</td>
        </tr>
        @endforeach
        <tr class="total-row">
            <td>{{ __('Mindösszesen') }}</td>
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
