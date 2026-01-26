@php
    $currentYearTotals = [
        'impressions' => $data->currentYearMonthly->sum('impressions'),
        'clicks' => $data->currentYearMonthly->sum('clicks'),
        'cost' => $data->currentYearMonthly->sum('cost'),
        'conversions' => $data->currentYearMonthly->sum('conversions'),
    ];
    $currentYearTotals['ctr'] = $currentYearTotals['impressions'] > 0 ? round(($currentYearTotals['clicks'] / $currentYearTotals['impressions']) * 100, 2) : 0;
    $currentYearTotals['avg_cpc'] = $currentYearTotals['clicks'] > 0 ? round($currentYearTotals['cost'] / $currentYearTotals['clicks'], 2) : 0;
    $currentYearTotals['cost_per_conversion'] = $currentYearTotals['conversions'] > 0 ? round($currentYearTotals['cost'] / $currentYearTotals['conversions'], 2) : 0;
    $currentYearTotals['conversion_rate'] = $currentYearTotals['clicks'] > 0 ? round(($currentYearTotals['conversions'] / $currentYearTotals['clicks']) * 100, 2) : 0;

    $maxCurrentImpressions = $data->currentYearMonthly->max('impressions') ?: 1;
@endphp

<h2 class="section-title">{{ __('Idei év / hónapok szerinti eredmények') }}</h2>

<table>
    <thead>
        <tr>
            <th>{{ __('Hónap') }}</th>
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
        @foreach($data->currentYearMonthly as $month)
        <tr>
            <td>{{ $month['month']->format('Y.') }} {{ $month['month_name'] }}</td>
            <td class="number bar-cell">
                <div class="bar-bg" style="width: {{ ($month['impressions'] / $maxCurrentImpressions) * 100 }}%"></div>
                <span class="bar-value">{{ number_format($month['impressions'], 0, ',', ' ') }}</span>
            </td>
            <td class="number">{{ number_format($month['clicks'], 0, ',', ' ') }}</td>
            <td class="number">{{ number_format($month['avg_cpc'], 2, ',', ' ') }} Ft</td>
            <td class="number">{{ number_format($month['ctr'], 2, ',', ' ') }}%</td>
            <td class="number {{ $month['conversions'] > 0 ? 'highlight-cell' : '' }}">{{ number_format($month['conversions'], 0, ',', ' ') }}</td>
            <td class="number">{{ number_format($month['cost_per_conversion'], 2, ',', ' ') }} Ft</td>
            <td class="number {{ $month['conversion_rate'] > 5 ? 'highlight-cell' : '' }}">{{ number_format($month['conversion_rate'], 2, ',', ' ') }}%</td>
            <td class="number">{{ number_format($month['cost'], 2, ',', ' ') }} Ft</td>
        </tr>
        @endforeach
        <tr class="total-row">
            <td>{{ __('Mindösszesen') }}</td>
            <td class="number">{{ number_format($currentYearTotals['impressions'], 0, ',', ' ') }}</td>
            <td class="number">{{ number_format($currentYearTotals['clicks'], 0, ',', ' ') }}</td>
            <td class="number">{{ number_format($currentYearTotals['avg_cpc'], 2, ',', ' ') }} Ft</td>
            <td class="number">{{ number_format($currentYearTotals['ctr'], 2, ',', ' ') }}%</td>
            <td class="number">{{ number_format($currentYearTotals['conversions'], 0, ',', ' ') }}</td>
            <td class="number">{{ number_format($currentYearTotals['cost_per_conversion'], 2, ',', ' ') }} Ft</td>
            <td class="number">{{ number_format($currentYearTotals['conversion_rate'], 2, ',', ' ') }}%</td>
            <td class="number">{{ number_format($currentYearTotals['cost'], 2, ',', ' ') }} Ft</td>
        </tr>
    </tbody>
</table>

@if($data->previousYearMonthly->isNotEmpty())
@php
    $previousYearTotals = [
        'impressions' => $data->previousYearMonthly->sum('impressions'),
        'clicks' => $data->previousYearMonthly->sum('clicks'),
        'cost' => $data->previousYearMonthly->sum('cost'),
        'conversions' => $data->previousYearMonthly->sum('conversions'),
    ];
    $previousYearTotals['ctr'] = $previousYearTotals['impressions'] > 0 ? round(($previousYearTotals['clicks'] / $previousYearTotals['impressions']) * 100, 2) : 0;
    $previousYearTotals['avg_cpc'] = $previousYearTotals['clicks'] > 0 ? round($previousYearTotals['cost'] / $previousYearTotals['clicks'], 2) : 0;
    $previousYearTotals['cost_per_conversion'] = $previousYearTotals['conversions'] > 0 ? round($previousYearTotals['cost'] / $previousYearTotals['conversions'], 2) : 0;
    $previousYearTotals['conversion_rate'] = $previousYearTotals['clicks'] > 0 ? round(($previousYearTotals['conversions'] / $previousYearTotals['clicks']) * 100, 2) : 0;

    $maxPreviousImpressions = $data->previousYearMonthly->max('impressions') ?: 1;
@endphp

<h2 class="section-title" style="margin-top: 30px;">{{ __('Előző év / hónapok szerinti eredmények') }}</h2>

<table>
    <thead>
        <tr>
            <th>{{ __('Hónap') }}</th>
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
        @foreach($data->previousYearMonthly as $month)
        <tr>
            <td>{{ $month['month']->format('Y.') }} {{ $month['month_name'] }}</td>
            <td class="number bar-cell">
                <div class="bar-bg" style="width: {{ ($month['impressions'] / $maxPreviousImpressions) * 100 }}%"></div>
                <span class="bar-value">{{ number_format($month['impressions'], 0, ',', ' ') }}</span>
            </td>
            <td class="number">{{ number_format($month['clicks'], 0, ',', ' ') }}</td>
            <td class="number">{{ number_format($month['avg_cpc'], 2, ',', ' ') }} Ft</td>
            <td class="number">{{ number_format($month['ctr'], 2, ',', ' ') }}%</td>
            <td class="number {{ $month['conversions'] > 0 ? 'highlight-cell' : '' }}">{{ number_format($month['conversions'], 0, ',', ' ') }}</td>
            <td class="number">{{ number_format($month['cost_per_conversion'], 2, ',', ' ') }} Ft</td>
            <td class="number {{ $month['conversion_rate'] > 5 ? 'highlight-cell' : '' }}">{{ number_format($month['conversion_rate'], 2, ',', ' ') }}%</td>
            <td class="number">{{ number_format($month['cost'], 2, ',', ' ') }} Ft</td>
        </tr>
        @endforeach
        <tr class="total-row">
            <td>{{ __('Mindösszesen') }}</td>
            <td class="number">{{ number_format($previousYearTotals['impressions'], 0, ',', ' ') }}</td>
            <td class="number">{{ number_format($previousYearTotals['clicks'], 0, ',', ' ') }}</td>
            <td class="number">{{ number_format($previousYearTotals['avg_cpc'], 2, ',', ' ') }} Ft</td>
            <td class="number">{{ number_format($previousYearTotals['ctr'], 2, ',', ' ') }}%</td>
            <td class="number">{{ number_format($previousYearTotals['conversions'], 0, ',', ' ') }}</td>
            <td class="number">{{ number_format($previousYearTotals['cost_per_conversion'], 2, ',', ' ') }} Ft</td>
            <td class="number">{{ number_format($previousYearTotals['conversion_rate'], 2, ',', ' ') }}%</td>
            <td class="number">{{ number_format($previousYearTotals['cost'], 2, ',', ' ') }} Ft</td>
        </tr>
    </tbody>
</table>
@endif
