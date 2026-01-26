@php
    $totals = [
        'impressions' => $data->geographicStats->sum('impressions'),
        'clicks' => $data->geographicStats->sum('clicks'),
        'cost' => $data->geographicStats->sum('cost'),
        'conversions' => $data->geographicStats->sum('conversions'),
    ];
    $totals['ctr'] = $totals['impressions'] > 0 ? round(($totals['clicks'] / $totals['impressions']) * 100, 2) : 0;
    $totals['avg_cpc'] = $totals['clicks'] > 0 ? round($totals['cost'] / $totals['clicks'], 2) : 0;
    $totals['cost_per_conversion'] = $totals['conversions'] > 0 ? round($totals['cost'] / $totals['conversions'], 2) : 0;
    $totals['conversion_rate'] = $totals['clicks'] > 0 ? round(($totals['conversions'] / $totals['clicks']) * 100, 2) : 0;

    $maxImpressions = $data->geographicStats->max('impressions') ?: 1;
@endphp

<table>
    <thead>
        <tr>
            <th>{{ __('Terület') }}</th>
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
        @foreach($data->geographicStats as $location)
        <tr>
            <td>{{ $location['location_name'] }}</td>
            <td class="number bar-cell" style="background: linear-gradient(to right, rgba(26, 115, 232, 0.15) {{ ($location['impressions'] / $maxImpressions) * 100 }}%, transparent {{ ($location['impressions'] / $maxImpressions) * 100 }}%);">
                {{ number_format($location['impressions'], 0, ',', ' ') }}
            </td>
            <td class="number">{{ number_format($location['clicks'], 0, ',', ' ') }}</td>
            <td class="number">{{ number_format($location['avg_cpc'], 2, ',', ' ') }} Ft</td>
            <td class="number">{{ number_format($location['ctr'], 2, ',', ' ') }}%</td>
            <td class="number {{ $location['conversions'] > 0 ? 'highlight-cell' : '' }}">{{ number_format($location['conversions'], 0, ',', ' ') }}</td>
            <td class="number">{{ number_format($location['cost_per_conversion'], 2, ',', ' ') }} Ft</td>
            <td class="number {{ $location['conversion_rate'] > 5 ? 'highlight-cell' : '' }}">{{ number_format($location['conversion_rate'], 2, ',', ' ') }}%</td>
            <td class="number">{{ number_format($location['cost'], 2, ',', ' ') }} Ft</td>
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
