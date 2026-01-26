@php
    $maxImpressions = $data->adGroups->max('impressions') ?: 1;
@endphp

<table>
    <thead>
        <tr>
            <th>{{ __('Kampány') }}</th>
            <th>{{ __('Hirdetéscsoport') }}</th>
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
        @foreach($data->adGroups as $adGroup)
        <tr>
            <td>{{ Str::limit($adGroup['campaign_name'], 25) }}</td>
            <td>{{ Str::limit($adGroup['ad_group_name'], 30) }}</td>
            <td class="number bar-cell">
                <div class="bar-bg" style="width: {{ ($adGroup['impressions'] / $maxImpressions) * 100 }}%"></div>
                <span class="bar-value">{{ number_format($adGroup['impressions'], 0, ',', ' ') }}</span>
            </td>
            <td class="number">{{ number_format($adGroup['clicks'], 0, ',', ' ') }}</td>
            <td class="number">{{ number_format($adGroup['avg_cpc'], 2, ',', ' ') }} Ft</td>
            <td class="number">{{ number_format($adGroup['ctr'], 2, ',', ' ') }}%</td>
            <td class="number {{ $adGroup['conversions'] > 0 ? 'highlight-cell' : '' }}">{{ number_format($adGroup['conversions'], 0, ',', ' ') }}</td>
            <td class="number">{{ number_format($adGroup['cost_per_conversion'], 2, ',', ' ') }} Ft</td>
            <td class="number {{ $adGroup['conversion_rate'] > 5 ? 'highlight-cell' : '' }}">{{ number_format($adGroup['conversion_rate'], 2, ',', ' ') }}%</td>
            <td class="number">{{ number_format($adGroup['cost'], 2, ',', ' ') }} Ft</td>
        </tr>
        @endforeach
    </tbody>
</table>
