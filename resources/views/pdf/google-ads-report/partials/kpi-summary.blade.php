@php
    $changes = $data->getKpiChanges();
    $kpi = $data->kpiSummary;
@endphp

<div class="kpi-grid">
    <div class="kpi-row">
        <div class="kpi-card">
            <div class="kpi-label">{{ __('Megjelenítések') }}</div>
            <div class="kpi-value">{{ number_format($kpi['impressions'], 0, ',', ' ') }}</div>
            <div class="kpi-change {{ $changes['impressions'] >= 0 ? 'positive' : 'negative' }}">
                {{ $changes['impressions'] >= 0 ? '▲' : '▼' }} {{ $changes['impressions'] >= 0 ? '+' : '' }}{{ number_format($changes['impressions'], 1, ',', ' ') }}%
            </div>
            <div class="kpi-change-label">előző hónaphoz képest</div>
        </div>
        <div class="kpi-spacer"></div>
        <div class="kpi-card">
            <div class="kpi-label">{{ __('Kattintások') }}</div>
            <div class="kpi-value">{{ number_format($kpi['clicks'], 0, ',', ' ') }}</div>
            <div class="kpi-change {{ $changes['clicks'] >= 0 ? 'positive' : 'negative' }}">
                {{ $changes['clicks'] >= 0 ? '▲' : '▼' }} {{ $changes['clicks'] >= 0 ? '+' : '' }}{{ number_format($changes['clicks'], 1, ',', ' ') }}%
            </div>
            <div class="kpi-change-label">előző hónaphoz képest</div>
        </div>
        <div class="kpi-spacer"></div>
        <div class="kpi-card cost">
            <div class="kpi-label">{{ __('Átlagos CPC') }}</div>
            <div class="kpi-value">{{ number_format($kpi['avg_cpc'], 0, ',', ' ') }} Ft</div>
            <div class="kpi-change {{ $changes['avg_cpc'] >= 0 ? 'negative' : 'positive' }}">
                {{ $changes['avg_cpc'] >= 0 ? '▲' : '▼' }} {{ $changes['avg_cpc'] >= 0 ? '+' : '' }}{{ number_format($changes['avg_cpc'], 1, ',', ' ') }}%
            </div>
            <div class="kpi-change-label">előző hónaphoz képest</div>
        </div>
    </div>
    <div class="kpi-row">
        <div class="kpi-card cost">
            <div class="kpi-label">{{ __('Összes Költség') }}</div>
            <div class="kpi-value">{{ number_format($kpi['cost'], 0, ',', ' ') }} Ft</div>
            <div class="kpi-change {{ $changes['cost'] >= 0 ? 'negative' : 'positive' }}">
                {{ $changes['cost'] >= 0 ? '▲' : '▼' }} {{ $changes['cost'] >= 0 ? '+' : '' }}{{ number_format($changes['cost'], 1, ',', ' ') }}%
            </div>
            <div class="kpi-change-label">előző hónaphoz képest</div>
        </div>
        <div class="kpi-spacer"></div>
        <div class="kpi-card highlight">
            <div class="kpi-label">{{ __('Konverziók') }}</div>
            <div class="kpi-value">{{ number_format($kpi['conversions'], 0, ',', ' ') }}</div>
            <div class="kpi-change {{ $changes['conversions'] >= 0 ? 'positive' : 'negative' }}">
                {{ $changes['conversions'] >= 0 ? '▲' : '▼' }} {{ $changes['conversions'] >= 0 ? '+' : '' }}{{ number_format($changes['conversions'], 1, ',', ' ') }}%
            </div>
            <div class="kpi-change-label">előző hónaphoz képest</div>
        </div>
        <div class="kpi-spacer"></div>
        <div class="kpi-card cost">
            <div class="kpi-label">{{ __('Költség / Konverzió') }}</div>
            <div class="kpi-value">{{ number_format($kpi['cost_per_conversion'], 0, ',', ' ') }} Ft</div>
            <div class="kpi-change {{ $changes['cost_per_conversion'] >= 0 ? 'negative' : 'positive' }}">
                {{ $changes['cost_per_conversion'] >= 0 ? '▲' : '▼' }} {{ $changes['cost_per_conversion'] >= 0 ? '+' : '' }}{{ number_format($changes['cost_per_conversion'], 1, ',', ' ') }}%
            </div>
            <div class="kpi-change-label">előző hónaphoz képest</div>
        </div>
    </div>
</div>
