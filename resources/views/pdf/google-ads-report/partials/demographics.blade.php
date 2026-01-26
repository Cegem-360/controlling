@php
    $deviceTotals = [
        'impressions' => $data->deviceStats->sum('impressions'),
        'clicks' => $data->deviceStats->sum('clicks'),
        'cost' => $data->deviceStats->sum('cost'),
        'conversions' => $data->deviceStats->sum('conversions'),
    ];
    $deviceTotals['ctr'] = $deviceTotals['impressions'] > 0 ? round(($deviceTotals['clicks'] / $deviceTotals['impressions']) * 100, 2) : 0;
    $deviceTotals['avg_cpc'] = $deviceTotals['clicks'] > 0 ? round($deviceTotals['cost'] / $deviceTotals['clicks'], 2) : 0;
    $deviceTotals['cost_per_conversion'] = $deviceTotals['conversions'] > 0 ? round($deviceTotals['cost'] / $deviceTotals['conversions'], 2) : 0;
    $deviceTotals['conversion_rate'] = $deviceTotals['clicks'] > 0 ? round(($deviceTotals['conversions'] / $deviceTotals['clicks']) * 100, 2) : 0;

    $maxDeviceImpressions = $data->deviceStats->max('impressions') ?: 1;
@endphp

{{-- Device Stats --}}
<table>
    <thead>
        <tr>
            <th>{{ __('Eszköz') }}</th>
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
        @foreach($data->deviceStats as $device)
        <tr>
            <td>{{ $device['device'] }}</td>
            <td class="number bar-cell">
                <div class="bar-bg" style="width: {{ ($device['impressions'] / $maxDeviceImpressions) * 100 }}%"></div>
                <span class="bar-value">{{ number_format($device['impressions'], 0, ',', ' ') }}</span>
            </td>
            <td class="number">{{ number_format($device['clicks'], 0, ',', ' ') }}</td>
            <td class="number">{{ number_format($device['avg_cpc'], 2, ',', ' ') }} Ft</td>
            <td class="number">{{ number_format($device['ctr'], 2, ',', ' ') }}%</td>
            <td class="number {{ $device['conversions'] > 0 ? 'highlight-cell' : '' }}">{{ number_format($device['conversions'], 0, ',', ' ') }}</td>
            <td class="number">{{ number_format($device['cost_per_conversion'], 2, ',', ' ') }} Ft</td>
            <td class="number {{ $device['conversion_rate'] > 5 ? 'highlight-cell' : '' }}">{{ number_format($device['conversion_rate'], 2, ',', ' ') }}%</td>
            <td class="number">{{ number_format($device['cost'], 2, ',', ' ') }} Ft</td>
        </tr>
        @endforeach
        <tr class="total-row">
            <td>{{ __('Mindösszesen') }}</td>
            <td class="number">{{ number_format($deviceTotals['impressions'], 0, ',', ' ') }}</td>
            <td class="number">{{ number_format($deviceTotals['clicks'], 0, ',', ' ') }}</td>
            <td class="number">{{ number_format($deviceTotals['avg_cpc'], 2, ',', ' ') }} Ft</td>
            <td class="number">{{ number_format($deviceTotals['ctr'], 2, ',', ' ') }}%</td>
            <td class="number">{{ number_format($deviceTotals['conversions'], 0, ',', ' ') }}</td>
            <td class="number">{{ number_format($deviceTotals['cost_per_conversion'], 2, ',', ' ') }} Ft</td>
            <td class="number">{{ number_format($deviceTotals['conversion_rate'], 2, ',', ' ') }}%</td>
            <td class="number">{{ number_format($deviceTotals['cost'], 2, ',', ' ') }} Ft</td>
        </tr>
    </tbody>
</table>

@php
    $genderTotals = [
        'impressions' => $data->genderStats->sum('impressions'),
        'clicks' => $data->genderStats->sum('clicks'),
        'cost' => $data->genderStats->sum('cost'),
        'conversions' => $data->genderStats->sum('conversions'),
    ];
    $genderTotals['ctr'] = $genderTotals['impressions'] > 0 ? round(($genderTotals['clicks'] / $genderTotals['impressions']) * 100, 2) : 0;
    $genderTotals['avg_cpc'] = $genderTotals['clicks'] > 0 ? round($genderTotals['cost'] / $genderTotals['clicks'], 2) : 0;
    $genderTotals['cost_per_conversion'] = $genderTotals['conversions'] > 0 ? round($genderTotals['cost'] / $genderTotals['conversions'], 2) : 0;
    $genderTotals['conversion_rate'] = $genderTotals['clicks'] > 0 ? round(($genderTotals['conversions'] / $genderTotals['clicks']) * 100, 2) : 0;

    $maxGenderImpressions = $data->genderStats->max('impressions') ?: 1;
@endphp

{{-- Gender Stats --}}
<table style="margin-top: 20px;">
    <thead>
        <tr>
            <th>{{ __('Nem') }}</th>
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
        @foreach($data->genderStats as $gender)
        <tr>
            <td>{{ $gender['gender'] }}</td>
            <td class="number bar-cell">
                <div class="bar-bg" style="width: {{ ($gender['impressions'] / $maxGenderImpressions) * 100 }}%"></div>
                <span class="bar-value">{{ number_format($gender['impressions'], 0, ',', ' ') }}</span>
            </td>
            <td class="number">{{ number_format($gender['clicks'], 0, ',', ' ') }}</td>
            <td class="number">{{ number_format($gender['avg_cpc'], 2, ',', ' ') }} Ft</td>
            <td class="number">{{ number_format($gender['ctr'], 2, ',', ' ') }}%</td>
            <td class="number {{ $gender['conversions'] > 0 ? 'highlight-cell' : '' }}">{{ number_format($gender['conversions'], 0, ',', ' ') }}</td>
            <td class="number">{{ number_format($gender['cost_per_conversion'], 2, ',', ' ') }} Ft</td>
            <td class="number {{ $gender['conversion_rate'] > 5 ? 'highlight-cell' : '' }}">{{ number_format($gender['conversion_rate'], 2, ',', ' ') }}%</td>
            <td class="number">{{ number_format($gender['cost'], 2, ',', ' ') }} Ft</td>
        </tr>
        @endforeach
        <tr class="total-row">
            <td>{{ __('Mindösszesen') }}</td>
            <td class="number">{{ number_format($genderTotals['impressions'], 0, ',', ' ') }}</td>
            <td class="number">{{ number_format($genderTotals['clicks'], 0, ',', ' ') }}</td>
            <td class="number">{{ number_format($genderTotals['avg_cpc'], 2, ',', ' ') }} Ft</td>
            <td class="number">{{ number_format($genderTotals['ctr'], 2, ',', ' ') }}%</td>
            <td class="number">{{ number_format($genderTotals['conversions'], 0, ',', ' ') }}</td>
            <td class="number">{{ number_format($genderTotals['cost_per_conversion'], 2, ',', ' ') }} Ft</td>
            <td class="number">{{ number_format($genderTotals['conversion_rate'], 2, ',', ' ') }}%</td>
            <td class="number">{{ number_format($genderTotals['cost'], 2, ',', ' ') }} Ft</td>
        </tr>
    </tbody>
</table>

@php
    $ageTotals = [
        'impressions' => $data->ageStats->sum('impressions'),
        'clicks' => $data->ageStats->sum('clicks'),
        'cost' => $data->ageStats->sum('cost'),
        'conversions' => $data->ageStats->sum('conversions'),
    ];
    $ageTotals['ctr'] = $ageTotals['impressions'] > 0 ? round(($ageTotals['clicks'] / $ageTotals['impressions']) * 100, 2) : 0;
    $ageTotals['avg_cpc'] = $ageTotals['clicks'] > 0 ? round($ageTotals['cost'] / $ageTotals['clicks'], 2) : 0;
    $ageTotals['cost_per_conversion'] = $ageTotals['conversions'] > 0 ? round($ageTotals['cost'] / $ageTotals['conversions'], 2) : 0;
    $ageTotals['conversion_rate'] = $ageTotals['clicks'] > 0 ? round(($ageTotals['conversions'] / $ageTotals['clicks']) * 100, 2) : 0;

    $maxAgeImpressions = $data->ageStats->max('impressions') ?: 1;
@endphp

{{-- Age Stats --}}
<table style="margin-top: 20px;">
    <thead>
        <tr>
            <th>{{ __('Kor') }}</th>
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
        @foreach($data->ageStats as $age)
        <tr>
            <td>{{ $age['age_range'] }}</td>
            <td class="number bar-cell">
                <div class="bar-bg" style="width: {{ ($age['impressions'] / $maxAgeImpressions) * 100 }}%"></div>
                <span class="bar-value">{{ number_format($age['impressions'], 0, ',', ' ') }}</span>
            </td>
            <td class="number">{{ number_format($age['clicks'], 0, ',', ' ') }}</td>
            <td class="number">{{ number_format($age['avg_cpc'], 2, ',', ' ') }} Ft</td>
            <td class="number">{{ number_format($age['ctr'], 2, ',', ' ') }}%</td>
            <td class="number {{ $age['conversions'] > 0 ? 'highlight-cell' : '' }}">{{ number_format($age['conversions'], 0, ',', ' ') }}</td>
            <td class="number">{{ number_format($age['cost_per_conversion'], 2, ',', ' ') }} Ft</td>
            <td class="number {{ $age['conversion_rate'] > 5 ? 'highlight-cell' : '' }}">{{ number_format($age['conversion_rate'], 2, ',', ' ') }}%</td>
            <td class="number">{{ number_format($age['cost'], 2, ',', ' ') }} Ft</td>
        </tr>
        @endforeach
        <tr class="total-row">
            <td>{{ __('Mindösszesen') }}</td>
            <td class="number">{{ number_format($ageTotals['impressions'], 0, ',', ' ') }}</td>
            <td class="number">{{ number_format($ageTotals['clicks'], 0, ',', ' ') }}</td>
            <td class="number">{{ number_format($ageTotals['avg_cpc'], 2, ',', ' ') }} Ft</td>
            <td class="number">{{ number_format($ageTotals['ctr'], 2, ',', ' ') }}%</td>
            <td class="number">{{ number_format($ageTotals['conversions'], 0, ',', ' ') }}</td>
            <td class="number">{{ number_format($ageTotals['cost_per_conversion'], 2, ',', ' ') }} Ft</td>
            <td class="number">{{ number_format($ageTotals['conversion_rate'], 2, ',', ' ') }}%</td>
            <td class="number">{{ number_format($ageTotals['cost'], 2, ',', ' ') }} Ft</td>
        </tr>
    </tbody>
</table>
