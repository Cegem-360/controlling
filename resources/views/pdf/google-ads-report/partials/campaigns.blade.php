@php
    $maxImpressions = $data->campaigns->max('impressions') ?: 1;
    $totals = [
        'impressions' => $data->campaigns->sum('impressions'),
        'clicks' => $data->campaigns->sum('clicks'),
        'cost' => $data->campaigns->sum('cost'),
        'conversions' => $data->campaigns->sum('conversions'),
    ];
    $totals['ctr'] = $totals['impressions'] > 0 ? round(($totals['clicks'] / $totals['impressions']) * 100, 2) : 0;
    $totals['avg_cpc'] = $totals['clicks'] > 0 ? round($totals['cost'] / $totals['clicks'], 2) : 0;
    $totals['cost_per_conversion'] = $totals['conversions'] > 0 ? round($totals['cost'] / $totals['conversions'], 2) : 0;
    $totals['conversion_rate'] = $totals['clicks'] > 0 ? round(($totals['conversions'] / $totals['clicks']) * 100, 2) : 0;
@endphp

<table>
    <thead>
        <tr>
            <th>{{ __('Kampány') }}</th>
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
        @foreach($data->campaigns as $campaign)
        <tr>
            <td>{{ Str::limit($campaign['campaign_name'], 40) }}</td>
            <td class="number bar-cell" style="background: linear-gradient(to right, rgba(26, 115, 232, 0.15) {{ ($campaign['impressions'] / $maxImpressions) * 100 }}%, transparent {{ ($campaign['impressions'] / $maxImpressions) * 100 }}%);">
                {{ number_format($campaign['impressions'], 0, ',', ' ') }}
            </td>
            <td class="number">{{ number_format($campaign['clicks'], 0, ',', ' ') }}</td>
            <td class="number">{{ number_format($campaign['avg_cpc'], 2, ',', ' ') }} Ft</td>
            <td class="number">{{ number_format($campaign['ctr'], 2, ',', ' ') }}%</td>
            <td class="number {{ $campaign['conversions'] > 0 ? 'highlight-cell' : '' }}">{{ number_format($campaign['conversions'], 0, ',', ' ') }}</td>
            <td class="number">{{ number_format($campaign['cost_per_conversion'], 2, ',', ' ') }} Ft</td>
            <td class="number {{ $campaign['conversion_rate'] > 5 ? 'highlight-cell' : '' }}">{{ number_format($campaign['conversion_rate'], 2, ',', ' ') }}%</td>
            <td class="number">{{ number_format($campaign['cost'], 2, ',', ' ') }} Ft</td>
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
